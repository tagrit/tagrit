<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Attendance_model extends App_Model
{

    private $table = 'event_attendance_sheets';

    public function __construct()
    {
        parent::__construct();
    }

    public function create($data)
    {
        $this->db->where('event_id', $data['event_id']);
        $this->db->where('location', $data['location']);
        $this->db->where('venue', $data['venue']);
        $query = $this->db->get(db_prefix() . $this->table);

        if ($query->num_rows() > 0) {
            $this->db->where('event_id', $data['event_id']);
            $this->db->where('location', $data['location']);
            $this->db->where('venue', $data['venue']);
            return $this->db->update(db_prefix() . $this->table, $data);
        } else {
            return $this->db->insert(db_prefix() . $this->table, $data);
        }
    }



}