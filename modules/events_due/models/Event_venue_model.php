<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Event_venue_model extends App_Model
{
    private $table = 'events_due_venues';

    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = null)
    {
        if ($id) {
            return $this->db->where('id', $id)->get(db_prefix() . $this->table)->row_array();
        }

        return $this->db->get(db_prefix() . $this->table)->result();
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

    public function get_event_venues($location_id)
    {

        // Get all venue IDs related to the event
        $this->db->select('venue_id');
        $this->db->from(db_prefix() . 'events_due_events');
        $this->db->where('location_id', $location_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $venues_ids = array_column($query->result_array(), 'venue_id');

            // Get all locations from the locations table
            $this->db->select('*'); // Select all location details
            $this->db->from(db_prefix() . 'events_due_venues'); // Assuming table name is 'locations'
            $this->db->where_in('id', $venues_ids);
            $location_query = $this->db->get();

            return $location_query->result(); // Return all matching locations
        } else {
            return false; // Return false if no event is found
        }
    }

}
