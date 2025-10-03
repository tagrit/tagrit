<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pickup_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . '_pickups';

    }

    public function get($return_count = false, $status = null, $staff_id = null, $role = null): array|bool|string
    {

        $this->db->select('p.*, c.first_name AS contact_first_name, c.last_name AS contact_last_name, c.phone_number AS contact_phone_number, c.email AS contact_email,ct.short_name AS country_name');
        $this->db->from(db_prefix() . '_pickups p');
        $this->db->join(db_prefix() . '_pickup_contacts c', 'p.contact_person_id = c.id');
        $this->db->join(db_prefix() . 'countries ct', 'p.country_id = ct.country_id');

        if ($role === 'driver') {
            $this->db->where('p.driver_id', $staff_id);

        } else {
            if (!empty($staff_id)) {
                $this->db->where('p.staff_id', $staff_id);
            }
        }


        if ($status !== null) {
            $this->db->where('p.status', $status);
        }

        if ($return_count) {
            return $this->db->count_all_results();
        } else {
            $query = $this->db->get();
            return $query->num_rows() > 0 ? $query->result() : false;
        }
    }

    public function get_pickup_by_id($pickup_id): array
    {
        $this->db->select('p.*, c.first_name AS contact_first_name, c.last_name AS contact_last_name, c.phone_number AS contact_phone_number, c.email AS contact_email, ct.short_name AS country_name');
        $this->db->from(db_prefix() . '_pickups p');
        $this->db->join(db_prefix() . '_pickup_contacts c', 'p.contact_person_id = c.id');
        $this->db->join(db_prefix() . 'countries ct', 'p.country_id = ct.country_id');
        $this->db->where('p.id', $pickup_id); // Filter by pickup_id

        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->row_array() : [];
    }


    public function get_pickup_count_by_status($status = null, $staff_id = null, $role = null): array|bool|string
    {
        return $role === 'driver' ? $this->get(true, $status, $staff_id, $role) : $this->get(true, $status, $staff_id);
    }

    public function add($data): bool
    {
        if ($this->db->insert($this->table, $data)) {
            $insert_id = $this->db->insert_id();
            log_message('debug', 'Insert successful. Insert ID: ' . $insert_id);
            return $insert_id;
        } else {
            log_message('error', 'Insert failed for ' . $this->table . ': ' . $this->db->last_query());
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

    public function get_countries($id = null)
    {
        $table = db_prefix() . 'countries';

        if ($id) {
            $this->db->where('id', $id);
            return $this->db->get($table)->row();
        }
        return $this->db->get($table)->result();
    }
}
