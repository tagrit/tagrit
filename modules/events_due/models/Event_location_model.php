<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Event_location_model extends App_Model
{
    private $table = 'events_due_locations';

    public function __construct()
    {
        parent::__construct();
    }

    public function get($id=null)
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

    public function get_event_locations($event_name_id) {

        // Get all location IDs related to the event
        $this->db->select('location_id');
        $this->db->from(db_prefix().'events_due_events');
        $this->db->where('event_name_id', $event_name_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $location_ids = array_column($query->result_array(), 'location_id');

            // Get all locations from the locations table
            $this->db->select('*'); // Select all location details
            $this->db->from(db_prefix().'events_due_locations'); // Assuming table name is 'locations'
            $this->db->where_in('id', $location_ids);
            $location_query = $this->db->get();

            return $location_query->result(); // Return all matching locations
        } else {
            return false; // Return false if no event is found
        }
    }

    public function getOrCreateLocationId($locationName)
    {
        $location = $this->db->where('name', $locationName)->get($this->table)->row();

        if ($location) {
            return $location->id; // Return existing location ID
        }

        // Insert new location and return its ID
        $this->db->insert($this->table, ['name' => $locationName, 'created_at' => date('Y-m-d H:i:s')]);
        return $this->db->insert_id();
    }

}
