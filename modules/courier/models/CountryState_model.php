<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CountryState_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . '_country_states';

    }

    public function get_country_name_by_id($country_id) {

        $this->db->where('country_id', $country_id);
        $query = $this->db->get('tblcountries');

        $result = $query->row();
        return $result ? $result->short_name : null;
    }

    public function get($id = null)
    {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->get($this->table)->row();
        }
        return $this->db->get($this->table)->result();
    }

    public function add($data): bool|int
    {
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        } else {
            // Log the error for debugging purposes
            log_message('error', 'Insert failed for state: ' . $this->db->last_query());
            return false;
        }
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    public function get_country_states($country_id)
    {
        if ($country_id) {
            $this->db->where('country_id', $country_id);
            return $this->db->get($this->table)->row();
        }
        return $this->db->get($this->table)->result();
    }
}
