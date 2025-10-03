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
        if (!empty($location_id)) {
            return $this->db->where('location_id', $location_id)
                ->get(db_prefix() . 'events_due_venues')
                ->result();
        }

        return false;
    }


    public function getOrCreateVenueId($venueName)
    {
        $venue = $this->db->where('name', $venueName)->get($this->table)->row();

        if ($venue) {
            return $venue->id;
        }

        // Insert new venue and return its ID
        $this->db->insert($this->table, ['name' => $venueName, 'location_id' => 1, 'created_at' => date('Y-m-d H:i:s')]);
        return $this->db->insert_id();
    }


}
