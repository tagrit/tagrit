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
        return $this->db->insert(db_prefix() . $this->table, $data);
    }

}