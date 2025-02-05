<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ThirdPartyShipment_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . '_third_party_shipments';

    }

    public function get($shipment_id = null)
    {
        if ($shipment_id) {
            $this->db->where('shipment_id', $shipment_id);
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
            log_message('error', 'Insert failed for contact person: ' . $this->db->last_query());
            return false;
        }
    }

    public function update($shipment_id, $data): bool
    {
        $this->db->where('shipment_id', $shipment_id);
        return $this->db->update($this->table, $data);
    }

    public function delete($shipment_id)
    {
        $this->db->where('shipment_id', $shipment_id);
        return $this->db->delete($this->table);
    }
}
