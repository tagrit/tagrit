<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Venues extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Event_venue_model');

    }

    public function index()
    {
        try {

            $location_id = $this->input->post('location_id');

            if (empty($location_id)) {
                throw new Exception('Location ID is required.');
            }

            $locations = $this->Event_venue_model->get_event_venues($location_id);

            if (!$locations) {
                throw new Exception('No venues found for the selected event.');
            }

            echo json_encode([
                'success' => true,
                'data' => $locations
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }



}