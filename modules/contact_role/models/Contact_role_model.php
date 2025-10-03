<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Contact_role_model extends CI_Model
{
    /** Available tables flag for the module */
    public $contact_role_table = 'contact_roles';
    public $contact_table = 'contacts';
    public $client_table = 'clients';

    public function add($data)
    {
        return $this->db->insert($this->contact_role_table, $data);
    }

    public function update($id, $data)
    {
        return $this->db->update($this->contact_role_table, $data, ['id' => $id]);
    }

    public function get($id = '', $where = [])
    {
        if (!empty($where))
            $this->db->where($where);

        if (!empty($id)) {
            $this->db->where('id', $id);
        }

        $query = $this->db->get($this->contact_role_table);

        return empty($id) ? $query->result() : $query->row();
    }

    public function get_contacts($id, $where = [])
    {
        if (empty($id)) return [];

        if (!empty($where))
            $this->db->where($where);

        $this->db->where('contact_role_id', $id);
        $this->db->join($this->client_table, $this->client_table . '.userid = ' . $this->contact_table . '.userid', 'LEFT');
        $query = $this->db->get($this->contact_table);

        return $query->result();
    }

    public function delete($id)
    {
        if (empty($id)) return false;

        return $this->db->delete($this->contact_role_table, ['id' => $id]);
    }
}