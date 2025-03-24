<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Event_name_model extends App_Model
{
    private $table = 'events_due_name';

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

    public function getOrCreateEventId($eventName)
    {
        // Check if the event already exists
        $event_name= $this->db->where('event_name', $eventName)->get(db_prefix().$this->table)->row();

        if ($event_name) {
            return $event_name->id; // Return existing event ID
        }

        // Insert new event
        $data = [
            'event_name' => $eventName,
        ];

        $this->db->insert($this->table, $data);

        return $this->db->insert_id(); // Return the newly inserted ID
    }
}
