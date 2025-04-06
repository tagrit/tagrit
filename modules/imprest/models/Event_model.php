<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Event_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . '_events';

    }

    public function get($id = null)
    {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->get($this->table)->row_array();
        }
        return $this->db->get($this->table)->result();
    }

    public function events_codes($event_code)
    {
        if ($event_code) {
            $this->db->where('event_unique_code', $event_code);
        }
        return $this->db->get(db_prefix() . 'event_unique_codes')->result();
    }


    public function event_details($event_id, $location, $venue, $start_date, $end_date)
    {
        // Step 1: Get the event details and sum revenue
        $this->db->select('
        tblevents_due_events.event_id,
        tblevents_due_events.start_date,
        tblevents_due_events.end_date,
        MAX(tblevents_due_events.setup) AS setup,
        MAX(tblevents_due_events.division) AS division,
        MAX(tblevents_due_events.type) AS type,
        MAX(tblevents_due_events.revenue) AS revenue,
        SUM(tblevents_due_events.revenue) AS total_revenue,
        GROUP_CONCAT(DISTINCT tblevents_due_events.trainers) AS trainers,
        MAX(tblevents_due_name.name) AS event_name,
        tblevents_due_events.location,
        tblevents_due_events.venue,
        MAX(tblevents_due_events.division) AS division,
        tblevent_unique_codes.event_unique_code  
    ');

        $this->db->from(db_prefix() . '_events_details AS tblevents_due_events');
        $this->db->join(db_prefix() . '_events AS tblevents_due_name', 'tblevents_due_events.event_id = tblevents_due_name.id', 'left');

        // Join with the tblevent_unique_codes table to get the event_unique_code
        $this->db->join(db_prefix() . 'event_unique_codes AS tblevent_unique_codes',
            'tblevents_due_events.event_id = tblevent_unique_codes.event_id 
         AND tblevents_due_events.location = tblevent_unique_codes.location
         AND tblevents_due_events.venue = tblevent_unique_codes.venue
         AND tblevents_due_events.start_date = tblevent_unique_codes.start_date
         AND tblevents_due_events.end_date = tblevent_unique_codes.end_date', 'left');

        // Filtering based on provided values
        $this->db->where('tblevents_due_events.event_id', $event_id);
        $this->db->where('tblevents_due_events.venue', $venue);
        $this->db->where('tblevents_due_events.start_date', $start_date);
        $this->db->where('tblevents_due_events.end_date', $end_date);
        $this->db->where('tblevents_due_events.location', $location);

        // Grouping by event details
        $this->db->group_by([
            'tblevents_due_events.event_id',
            'tblevents_due_events.location',
            'tblevents_due_events.venue',
            'tblevents_due_events.start_date',
            'tblevents_due_events.end_date'
        ]);

        $event_data = $this->db->get()->row();

        if (!$event_data) {
            return null; // Return null if no event found
        }

        // Step 2: Get Clients and attach organizations
        $this->db->select('
        tblevents_due_events.organization,
        tblevents_due_registrations.clients
    ');

        $this->db->from(db_prefix() . '_events_details AS tblevents_due_events');
        $this->db->join(db_prefix() . 'events_due_registrations AS tblevents_due_registrations', 'tblevents_due_registrations.event_detail_id = tblevents_due_events.id', 'left');

        // Apply the same filters to get the correct clients
        $this->db->where('tblevents_due_events.event_id', $event_data->event_id);
        $this->db->where('tblevents_due_events.location', $event_data->location);
        $this->db->where('tblevents_due_events.venue', $event_data->venue);
        $this->db->where('tblevents_due_events.start_date', $event_data->start_date);
        $this->db->where('tblevents_due_events.end_date', $event_data->end_date);

        $clients_data = $this->db->get()->result();

        $clients = [];
        foreach ($clients_data as $client) {
            if (!empty($client->clients)) {
                $client_info = unserialize($client->clients);
                if ($client_info !== false) {
                    foreach ($client_info as &$c) {
                        $c['organization'] = $client->organization;
                    }
                    $clients = array_merge($clients, $client_info);
                }
            }
        }

        // Step 3: Return the result
        return [
            'event_id' => $event_data->event_id,
            'start_date' => $event_data->start_date,
            'end_date' => $event_data->end_date,
            'setup' => $event_data->setup,
            'division' => $event_data->division,
            'type' => $event_data->type,
            'total_revenue' => $event_data->total_revenue,
            'trainers' => $event_data->trainers,
            'event_name' => $event_data->event_name,
            'location' => $event_data->location,
            'venue' => $event_data->venue,
            'event_unique_code' => $event_data->event_unique_code,  // Add event_unique_code to the result
            'clients' => $clients
        ];
    }


    public function get_event_by_fund_request_id($fund_request_id)
    {
        $this->db->select(db_prefix() . '_events.id as event_id, 
                       ' . db_prefix() . '_events.name as event_name,
                       ed.venue,
                       ed.start_date,
                       ed.end_date,
                       ed.no_of_delegates,
                       ed.facilitator,
                       ed.trainers');
        $this->db->from(db_prefix() . '_fund_requests');
        $this->db->join(db_prefix() . '_events', db_prefix() . '_fund_requests.event_id = ' . db_prefix() . '_events.id');
        $this->db->join(db_prefix() . '_events_details ed', db_prefix() . '_fund_requests.event_detail_id = ed.id', 'left');
        $this->db->where(db_prefix() . '_fund_requests.id', $fund_request_id);

        // Execute the query
        $query = $this->db->get();

        // Return the event details or null if no match found
        if ($query->num_rows() > 0) {
            return $query->row(); // Return the event row
        }

        return null; // No event found
    }

}
