<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Registration_model extends App_Model
{

    private $table = 'events_due_registrations';

    public function __construct()
    {
        parent::__construct();
    }


    public function get($event_id = null)
    {
        $this->db->select('
        tblevents_due_events.event_id,
        tblevents_due_events.start_date,
        tblevents_due_events.end_date,
        tblevents_due_events.setup,
        tblevents_due_events.division,
        tblevents_due_events.type,
        tblevents_due_events.revenue,
        tblevents_due_name.name AS event_name,
        tblevents_due_events.location,
        tblevents_due_events.venue,
        tblevents_due_events.organization,
        tblevents_due_registrations.clients AS serialized_clients
    ');

        $this->db->from(db_prefix() . '_events_details AS tblevents_due_events');
        $this->db->join(db_prefix() . '_events AS tblevents_due_name', 'tblevents_due_events.event_id = tblevents_due_name.id', 'left');
        $this->db->join(db_prefix() . 'events_due_registrations AS tblevents_due_registrations', 'tblevents_due_events.id = tblevents_due_registrations.event_detail_id', 'inner');

        if ($event_id) {
            $this->db->where('tblevents_due_events.id', $event_id);
        }

        $results = $this->db->get()->result_array();

        // Process the serialized clients
        $final_results = [];

        foreach ($results as $row) {
            $clients = unserialize($row['serialized_clients']);

            if (is_array($clients)) {
                foreach ($clients as $client) {
                    $final_results[] = array_merge($row, [
                        'client_first_name' => $client['first_name'] ?? '',
                        'client_last_name' => $client['last_name'] ?? '',
                        'client_email' => $client['email'] ?? '',
                        'client_phone' => $client['phone'] ?? ''
                    ]);
                }
            } else {
                // If no clients, just return the event details
                $final_results[] = array_merge($row, [
                    'client_first_name' => '',
                    'client_last_name' => '',
                    'client_email' => '',
                    'client_phone' => ''
                ]);
            }
        }

        return json_decode(json_encode($final_results));
    }


    public function get_filtered_data($status = null, $start_date = null, $end_date = null, $organization = null)
    {

        $this->db->select('
        tblevents_due_events.event_id,
        tblevents_due_events.start_date,
        tblevents_due_events.end_date,
        tblevents_due_events.setup,
        tblevents_due_events.division,
        tblevents_due_events.type,
        tblevents_due_events.revenue,
        tblevents_due_name.name AS event_name,
        tblevents_due_events.location,
        tblevents_due_events.venue,
        tblevents_due_events.organization,
        tblevents_due_registrations.clients AS serialized_clients,
        tblevents_due_registrations.status,
        tblevents_due_registrations.payment_status
    ');

        $this->db->from(db_prefix() . '_events_details AS tblevents_due_events');
        $this->db->join(db_prefix() . '_events AS tblevents_due_name', 'tblevents_due_events.event_id = tblevents_due_name.id', 'left');
        $this->db->join(db_prefix() . 'events_due_registrations AS tblevents_due_registrations', 'tblevents_due_events.id = tblevents_due_registrations.event_detail_id', 'inner');


        // Apply filters directly to the query
        if (!empty($status)) {
            $this->db->where('tblevents_due_registrations.status IS NOT NULL');
            $this->db->where('tblevents_due_registrations.status', $status);
        }


        if (!empty($start_date)) {
            $this->db->where('tblevents_due_events.start_date >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->db->where('tblevents_due_events.end_date <=', $end_date);
        }

        if (!empty($organization)) {
            if (is_array($organization)) {
                $this->db->where_in('tblevents_due_events.organization', $organization);
            } else {
                $this->db->where('tblevents_due_events.organization', $organization);
            }
        }

        $results = $this->db->get()->result_array();

        // Process the serialized clients
        $final_results = [];

        foreach ($results as $row) {
            $clients = unserialize($row['serialized_clients']);

            if (is_array($clients)) {
                foreach ($clients as $client) {
                    $final_results[] = array_merge($row, [
                        'client_first_name' => $client['first_name'] ?? '',
                        'client_last_name' => $client['last_name'] ?? '',
                        'client_email' => $client['email'] ?? '',
                        'client_phone' => $client['phone'] ?? ''
                    ]);
                }
            } else {
                // If no clients, just return the event details
                $final_results[] = array_merge($row, [
                    'client_first_name' => '',
                    'client_last_name' => '',
                    'client_email' => '',
                    'client_phone' => ''
                ]);
            }
        }

        return json_decode(json_encode($final_results));
    }


    public function create($data)
    {
        return $this->db->insert(db_prefix() . $this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update(db_prefix() . $this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete(db_prefix() . $this->table);
    }
}