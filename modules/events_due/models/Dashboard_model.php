<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Dashboard_model extends App_Model
{

    //get latest 5 events
    public function latest_events()
    {
        $this->db->select('
        tblevents_due_events.event_id,
        tblevents_due_events.start_date,
        tblevents_due_events.end_date,
        tblevents_due_events.setup,
        tblevents_due_events.division,
        tblevents_due_events.type,
        tblevents_due_events.revenue,
        tblevents_due_events.trainers,
        tblevents_due_name.name AS event_name,
        tblevents_due_events.location,
        tblevents_due_events.venue,
        tblevents_due_events.organization,
        tblevents_due_registrations.clients AS serialized_clients
    ');

        $this->db->from(db_prefix() . '_events_details AS tblevents_due_events');
        $this->db->join(db_prefix() . '_events AS tblevents_due_name', 'tblevents_due_events.event_id = tblevents_due_name.id', 'left');
        $this->db->join(db_prefix() . 'events_due_registrations AS tblevents_due_registrations', 'tblevents_due_events.id = tblevents_due_registrations.event_detail_id', 'inner');
        $this->db->order_by('tblevents_due_events.start_date', 'DESC');
        $this->db->limit(5);

        return $this->db->get()->result();
    }

    //get event count
    public function events_count()
    {
        $this->db->from(db_prefix() . '_events_details AS tblevents_due_events');
        return $this->db->count_all_results();
    }


    //get client count
    public function delegates_count()
    {
        $this->db->select('events_due_registrations.clients');
        $this->db->from(db_prefix() . 'events_due_registrations AS events_due_registrations');
        $query = $this->db->get();
        $delegates_count = 0;

        foreach ($query->result() as $row) {
            if (!empty($row->clients)) {
                $clients = unserialize($row->clients);
                if (is_array($clients)) {
                    $delegates_count += count($clients);
                }
            }
        }

        return $delegates_count;
    }


    //registration per month
    public function clients_per_month()
    {
        $this->db->select("MONTH(tblevents_due_events.start_date) AS month, COUNT(tblevents_due_registrations.id) AS total_clients");
        $this->db->from(db_prefix() . 'events_due_registrations AS tblevents_due_registrations');
        $this->db->join(db_prefix() . '_events_details AS tblevents_due_events', 'tblevents_due_events.id = tblevents_due_registrations.event_detail_id', 'inner');
        $this->db->group_by("MONTH(tblevents_due_events.start_date)");
        $this->db->order_by("MONTH(tblevents_due_events.start_date)", "ASC");

        $query = $this->db->get();
        $result = $query->result();

        // Initialize array for all 12 months
        $data = array_fill(1, 12, 0);

        foreach ($result as $row) {
            $data[$row->month] = $row->total_clients;
        }

        return json_encode(array_values($data));
    }

    //event per division
    public function events_per_division()
    {
        $this->db->select("tblevents_due_events.division, COUNT(tblevents_due_events.id) AS total_events");
        $this->db->from(db_prefix() . '_events_details AS tblevents_due_events');
        $this->db->group_by("tblevents_due_events.division");
        $this->db->order_by("total_events", "DESC");

        $query = $this->db->get();
        $result = $query->result();

        // Prepare structured data
        $data = [
            'labels' => [],
            'counts' => []
        ];

        foreach ($result as $row) {
            $data['labels'][] = $row->division;
            $data['counts'][] = $row->total_events;
        }

        return json_encode($data);
    }


    //revenue per division
    public function get_revenue_per_division()
    {
        $this->db->select('division, SUM(revenue) as total_revenue');
        $this->db->from(db_prefix() . '_events_details');
        $this->db->group_by('division');
        $this->db->order_by('total_revenue', 'DESC');

        $query = $this->db->get();
        $result = $query->result();

        $data = [
            'labels' => [],
            'revenues' => []
        ];

        foreach ($result as $row) {
            $data['labels'][] = $row->division;
            $data['revenues'][] = (float)$row->total_revenue;
        }

        return $data;
    }


    //missed and attended counts

}