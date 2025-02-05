<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manifest_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . '_manifests';

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

    public function get_records($manifest_number = null)
    {
        $this->db->select(db_prefix().'_manifests.*, do.company_name, do.location,do.street_address,do.landmark,do.phone_number'); // Adjust the selected columns as needed
        $this->db->from($this->table);
        $this->db->join(db_prefix().'_destination_offices do', db_prefix().'_manifests.destination_id = do.id', 'left'); // Use 'left' for optional matching

        if ($manifest_number) {
            $this->db->where(db_prefix().'_manifests.manifest_number', $manifest_number);
        }

        return $this->db->get()->result();
    }

    public function get_latest_manifest_number() {
        $this->db->select_max('manifest_number');
        $query = $this->db->get($this->table);
        return $query->row()->manifest_number;
    }

    public function get_latest_flight_number() {
        $this->db->select_max('flight_number');
        $query = $this->db->get($this->table);
        return $query->row()->flight_number;
    }

    public function manifestExists($manifest_number)
    {
        $this->db->where('manifest_number', $manifest_number);
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    public function deleteByManifestNumber($manifest_number)
    {
        $this->db->where('manifest_number', $manifest_number);
        $this->db->delete($this->table);
    }


    public function get_manifests()
    {
        $this->db->select('manifest_number, flight_number, SUM(usd) AS total, MAX(created_at) AS created_at');
        $this->db->from($this->table);
        $this->db->group_by('manifest_number, flight_number');
        $this->db->order_by('created_at', 'DESC'); // Order by created_at if needed

        $query = $this->db->get();
        return $query->result();
    }


    public function get_manifest_period(){
        $this->db->where('manifest_number', $manifest_number);
        $query = $this->db->get(db_prefix().'_manifest_period');
        return $query->num_rows() > 0;
    }


    public function add_manifest_period($data): bool|int
    {
        if ($this->db->insert(db_prefix().'_manifest_period', $data)) {
            $insert_id = $this->db->insert_id();
            log_message('debug', 'Insert successful. Insert ID: ' . $insert_id);
            return $insert_id;
        } else {
            log_message('error', 'Insert failed for '.db_prefix().'_manifest_period');
            return false;
        }
    }
}
