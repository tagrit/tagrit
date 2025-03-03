<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Events extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Event_model');
        $this->load->model('Event_name_model');
        $this->load->model('Event_location_model');
        $this->load->model('Event_venue_model');
        $this->load->library('form_validation');

    }

    public function index()
    {
        $data['event_names'] = $this->Event_name_model->get();
        $data['event_locations'] = $this->Event_location_model->get();
        $data['event_venues'] = $this->Event_venue_model->get();
        $this->load->view('events/index', $data);
    }

    public function create()
    {
        $this->load->view('events/create');

    }

    public function store()
    {

    }

    public function edit($event_id)
    {
        $this->load->view('events/edit');
    }

    public function update($event_id)
    {

    }

    public function validateEventName()
    {
        // Set validation rules
        $this->form_validation->set_rules('event_name', 'Event Name', 'required|trim|min_length[3]|is_unique[' . db_prefix() . 'events_due_name.event_name]',
            array(
                'required' => 'The Event Name field is required.',
            )
        );
    }


    public function store_event_name()
    {

        $this->validateEventName();

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('events/index');
        } else {

            // Prepare data for insertion
            $data = array(
                'event_name' => $this->input->post('event_name', TRUE), // Sanitize input
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            // Insert data into the database
            if ($this->Event_name_model->create($data)) {
                set_alert('success', 'Event Name added successfully.');
                redirect('admin/events_due/events');
            } else {
                set_alert('danger', 'An error occurred while adding the event.');
                $this->load->view('events/index');
            }
        }

    }
}