<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Client_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    // Method to insert a new client
    public function insert_client($data)
    {
        if ($this->db->insert(db_prefix().'clients', $data)) {
            return $this->db->insert_id();
        } else {
            // Log the error for debugging purposes
            log_message('error', 'Insert failed for client: ' . $this->db->last_query());
            return false;
        }
    }

    // Method to get all clients
    public function get_all_clients()
    {
        $query = $this->db->get(db_prefix().'clients');
        return $query->result_array();
    }
}

?>
