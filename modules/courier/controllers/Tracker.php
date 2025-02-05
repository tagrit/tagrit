<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tracker extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Shipment_model');
        $this->load->model('ShipmentStatus_model');

    }

    public function tracking()
    {
        $this->load->view('tracking');
    }

    public function shipment_info()
    {

        $tracking_number = $this->input->post('tracking_number');

        $shipment = $this->Shipment_model->get_shipment_by_tracking_number($tracking_number);
        $statuses = $this->ShipmentStatus_model->get();

        if ($shipment) {
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'shipment_details' => $shipment,
                    'statuses' => $statuses,
                ]
            ]);
        } else {
            echo json_encode([
                'status' => 'error', 'message' => 'Shipment not found.',
                'tracking_number' => $tracking_number
            ]);
        }
    }


}