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
