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

    public function add($data): bool|int
    {
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        } else {
            // Log the error for debugging purposes
            log_message('error', 'Insert failed for shipment stop: ' . $this->db->last_query());
            return false;
        }
    }

    public function events()
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
        tblevents_due_events.organization
    ');

        $this->db->from(db_prefix() . '_events_details AS tblevents_due_events');
        $this->db->join(db_prefix() . '_events AS tblevents_due_name', 'tblevents_due_events.event_id = tblevents_due_name.id', 'left');

        // Group by the distinct combination of location, venue, event_id, start_date, and end_date
        $this->db->group_by([
            'tblevents_due_events.location',
            'tblevents_due_events.venue',
            'tblevents_due_events.event_id',
            'tblevents_due_events.start_date',
            'tblevents_due_events.end_date'
        ]);

        return $this->db->get()->result();
    }

    public function event_details($event_id, $location, $venue, $start_date, $end_date)
    {
        // Step 1: Get the event details and sum revenue
        $this->db->select('
        tblevents_due_events.event_id,
        tblevents_due_events.id,
        tblevents_due_events.start_date,
        tblevents_due_events.end_date,
        tblevents_due_events.setup,
        tblevents_due_events.division,
        tblevents_due_events.type,
        SUM(tblevents_due_events.revenue) AS total_revenue,
        tblevents_due_events.trainers,
        tblevents_due_name.name AS event_name,
        tblevents_due_events.location,
        tblevents_due_events.venue,
        tblevents_due_events.division
    ');

        $this->db->from(db_prefix() . '_events_details AS tblevents_due_events');
        $this->db->join(db_prefix() . '_events AS tblevents_due_name', 'tblevents_due_events.event_id = tblevents_due_name.id', 'left');

        // Filtering based on provided values
        $this->db->where('tblevents_due_events.event_id', $event_id);
        $this->db->where('tblevents_due_events.location', $location);
        $this->db->where('tblevents_due_events.venue', $venue);
        $this->db->where('tblevents_due_events.start_date', $start_date);
        $this->db->where('tblevents_due_events.end_date', $end_date);

        // Grouping by event details
        $this->db->group_by([
            'tblevents_due_events.event_id',
            'tblevents_due_events.location',
            'tblevents_due_events.venue',
            'tblevents_due_events.start_date',
            'tblevents_due_events.end_date'
        ]);

        $event_data = $this->db->get()->row(); // Fetch single event record

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
            'id' => $event_data->id,
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
            'clients' => $clients
        ];
    }


    public function set_reminder_days($days)
    {
        // Check if a record exists
        $query = $this->db->get(db_prefix() . 'email_reminder_period');
        $exists = $query->row();

        if ($exists) {
            // Update the existing record
            $this->db->update(db_prefix() . 'email_reminder_period', ['days' => $days], ['id' => $exists->id]);
        } else {
            // Insert a new record if none exists
            $this->db->insert(db_prefix() . 'email_reminder_period', ['days' => $days]);
        }

        return $this->db->affected_rows() > 0;
    }

    public function get_reminder_days()
    {
        $query = $this->db->get(db_prefix() . 'email_reminder_period');
        $row = $query->row();

        return $row ? (int)$row->days : 7; // Default to 7 days if no record exists
    }


    public function upcoming_event_details($limit = 50, $offset = 0)
    {
        $reminder_days = $this->get_reminder_days();
        $reminder_date = date('Y-m-d', strtotime("+{$reminder_days} days"));

        $this->db->select('
        tblevents_due_events.event_id,
        tblevents_due_events.start_date,
        tblevents_due_name.name AS event_name,
        tblevents_due_events.venue,        
        tblevents_due_events.location,
        tblevents_due_registrations.clients AS serialized_clients
    ');

        $this->db->from(db_prefix() . '_events_details AS tblevents_due_events');
        $this->db->join(db_prefix() . '_events AS tblevents_due_name', 'tblevents_due_events.event_id = tblevents_due_name.id', 'left');
        $this->db->join(db_prefix() . 'events_due_registrations AS tblevents_due_registrations', 'tblevents_due_events.id = tblevents_due_registrations.event_detail_id', 'inner');

        // Filter for events 7 days ahead
        $this->db->where('DATE(tblevents_due_events.start_date)', $reminder_date);

        // Pagination to avoid memory overload
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }


    public function update($id, $data): bool
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }


    /**
     * Get or create an event and return its event_id
     *
     * @param array $data Event details (e.g., ['name' => 'Some Event', 'location' => 'XYZ'])
     * @return int|false The event ID or false on failure
     */
    public function getOrCreateEventId($data)
    {
        if (!isset($data['name']) || empty($data['name'])) {
            return false; // Event name is required
        }

        // Check if the event already exists
        $this->db->where('name', $data['name']);
        $existingEvent = $this->db->get($this->table)->row_array();

        if ($existingEvent) {
            return $existingEvent['id']; // Return existing event_id
        }

        // Insert a new event if it doesn't exist
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id(); // Return new event_id
        }

        return false; // Return false if insertion failed
    }

}
