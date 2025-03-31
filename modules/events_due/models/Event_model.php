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

    public function event_details($event_id = null)
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

        if ($event_id) {
            $this->db->where('tblevents_due_events.event_id', $event_id);
        }

        return $this->db->get()->result();

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
