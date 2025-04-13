<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Locations extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Event_location_model');

    }

    public function index()
    {
        try {

            $locations = $this->Event_location_model->get();

            if (!$locations) {
                throw new Exception('No locations found for the selected event.');
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