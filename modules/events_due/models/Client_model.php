<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Client_model extends App_Model
{
    
    private $table = 'events_due_clients';

    public function __construct()
    {
        parent::__construct();
    }


    public function get($id)
    {
        if ($id) {
            return $this->db->where('id', $id)->get(db_prefix() . $this->table)->row_array();
        }

        return $this->db->get(db_prefix() . $this->table)->result_array();
    }


    public function get_client_by_email($email)
    {
        return $this->db->select('id')
            ->from(db_prefix() . 'events_due_clients')
            ->where('email', $email)
            ->get()
            ->row('id');
    }


    public function create($data)
    {
        $this->db->insert(db_prefix() . $this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update(db_prefix() . $this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete(db_prefix() . $this->table);
    }
}