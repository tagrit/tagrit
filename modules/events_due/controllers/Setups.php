<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setups extends AdminController
{
    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        try {

            $event_name_id = $this->input->post('event_name_id');
            $location_id = $this->input->post('location_id');
            $venue_id = $this->input->post('venue_id');

            if (empty($location_id) || empty($venue_id) || empty($event_name_id)) {
                throw new Exception('Fill all the required fields.');
            }

            $this->db->distinct();
            $this->db->select('setup');
            $this->db->from(db_prefix() . 'events_due_events');
            $this->db->where('event_name_id', $event_name_id);
            $this->db->where('location_id', $location_id);
            $this->db->where('venue_id', $venue_id);

            $query = $this->db->get();
            $setups = $query->result();


            echo json_encode([
                'success' => true,
                'data' => $setups
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }


}