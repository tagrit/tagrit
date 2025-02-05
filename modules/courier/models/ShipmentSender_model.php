<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ShipmentSender_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . '_shipment_senders';

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
            log_message('error', 'Insert failed for Rec ' . $this->table . ': ' . $this->db->last_query());
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
