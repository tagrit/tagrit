<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Agent_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . '_agents';
    }

    public function get($id = null)
    {
        // Select from agents and join with staff table if $id is not provided
        if ($id) {
            $this->db->where(db_prefix().'_agents.id', $id);
            $this->db->join(db_prefix().'staff', db_prefix().'_agents.staff_id = '.db_prefix().'staff.staffid', 'left'); // Adjust join condition as necessary
            $this->db->join(db_prefix().'countries', db_prefix().'_agents.country_id = '.db_prefix().'countries.country_id', 'left'); // Adjust join condition as necessary
            return $this->db->get(db_prefix().'_agents')->row();
        }

        // If $id is null, retrieve all agents with their staff data
        $this->db->select(db_prefix().'_agents.*, '.db_prefix().'staff.*, '.db_prefix().'countries.*'); // Select all columns from all three tables
        $this->db->join(db_prefix().'staff', db_prefix().'_agents.staff_id = '.db_prefix().'staff.staffid', 'left'); // Adjust join condition as necessary
        $this->db->join(db_prefix().'countries', db_prefix().'_agents.country_id = '.db_prefix().'countries.country_id', 'left'); // Adjust join condition as necessary
        return $this->db->get(db_prefix().'_agents')->result();

    }

    public function add($data): bool|int
    {
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        } else {
            // Log the error for debugging purposes
            log_message('error', 'Insert failed for agent stop: ' . $this->db->last_query());
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
}
