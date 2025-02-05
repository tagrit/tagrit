<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Manifests extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('courier/courier'); // Load the helper specific to the courier module
        $this->load->model('Manifest_model');
    }

    public function index()
    {
        $data['manifests'] = $this->Manifest_model->get_manifests();
        $this->load->view('manifests/index', $data);
    }

    public function store()
    {
        // Get the POST data from the request
        $manifest_data = $this->input->post('manifests');
        $manifest_number = $this->input->post('manifest_number');
        $flight_number = $this->input->post('flight_number');
        $destination_id = $this->input->post('destination_id');

        // delete manifest records that exists the Manifest_model
        $this->load->model('Manifest_model');

        if ($this->Manifest_model->manifestExists($manifest_number)) {
            $this->Manifest_model->deleteByManifestNumber($manifest_number);
        }

        // Loop through each manifest entry
        foreach ($manifest_data as $data) {
            $insert_data = [
                'date' => date('Y-m-d', strtotime($data['date'])),
                'sender' => $data['sender'],
                'rcvr' => $data['rcvr'],
                'phone' => $data['phone'],
                'awb_number' => $data['awb_number'],
                'description' => $data['description'],
                'pcs' => (int)$data['pcs'],
                'kgs' => (float)$data['kgs'],
                'rate' => (float)$data['rate'],
                'aed' => (float)0,
                'usd' => (float)$data['usd'],
                'pack' => $data['pack'],
                'dest' => $data['dest'],
                'rmks' => $data['rmks'],
                'manifest_number' => $manifest_number,
                'flight_number' => $flight_number,
                'status' => $data['status'],
                'destination_id' => $destination_id,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Call the add method from the Manifest_model to insert the data
            $this->Manifest_model->add($insert_data);
        }


        if (!empty($this->input->post('start_date')) && !empty($this->input->post('end_date'))) {
            $data = [
                'manifest_number' => $manifest_number,
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date')
            ];

            $this->Manifest_model->add_manifest_period($data);
        }

        // You can also output the data directly (optional)
        echo json_encode([
            'success' => true,
            'message' => 'Manifest data added successfully!',
            'data' => $manifest_data,
        ]);
    }

    public function view($manifest_number)
    {
        $data['manifest_records'] = $this->Manifest_model->get_records($manifest_number);
        $data['manifest_number'] = $manifest_number;
        $this->load->view('manifests/view', $data);

    }


}