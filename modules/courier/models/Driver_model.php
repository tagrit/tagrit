<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Driver_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'staff';
    }

    public function get()
    {
        $this->db->select($this->table . '.*'); // Use the staff table
        $this->db->from($this->table); // Use $this->table instead of hardcoded 'users'
        $this->db->join('roles', 'roles.roleid = ' . $this->table . '.role'); // Adjust join to use 'staff' table
        $this->db->where('roles.name', 'Fleet: Driver');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_staff_role($staff_id)
    {
        $this->db->select(db_prefix() . 'roles.name');
        $this->db->from(db_prefix() . 'staff');
        $this->db->join(db_prefix() . 'roles', db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role'); // Ensure the join is correct
        $this->db->where(db_prefix() . 'staff.staffid', $staff_id);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->name;
        }

        return null;
    }


}
