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
        $data['events'] = $this->Event_model->get();
        $data['event_names'] = $this->Event_name_model->get();
        $data['event_locations'] = $this->Event_location_model->get();
        $data['event_venues'] = $this->Event_venue_model->get();
        $this->load->view('events/index', $data);
    }

    public function create()
    {
        $this->load->view('events/create');

    }

    private function validateEvent()
    {
        $this->form_validation->set_rules('event_name_id', 'Event Name', 'required');
        $this->form_validation->set_rules('location_id', 'Location', 'required');
        $this->form_validation->set_rules('venue_id', 'Venue', 'required|numeric'); // Venue ID should be numeric
        $this->form_validation->set_rules('event_type', 'Event Type', 'required'); // Adjusted length
        $this->form_validation->set_rules('setup', 'Event Setup', 'required'); // Removed min/max length for numeric
        $this->form_validation->set_rules('start_date', 'Start Date', 'required'); // Use valid_date if handling dates
        $this->form_validation->set_rules('end_date', 'End Date', 'required'); // Custom callback to check range
        $this->form_validation->set_rules('division', 'Event Division', 'required');
    }


    public function store()
    {

        $this->validateEvent();
        if ($this->form_validation->run() == FALSE) {

            $data['show_event_modal'] = true;
            redirect('admin/events_due/events',$data);

        } else {

            try {

                $data = [
                    'event_name_id' => $this->input->post('event_name_id'),
                    'location_id' => $this->input->post('location_id'),
                    'venue_id' => $this->input->post('venue_id'),
                    'type' => $this->input->post('event_type'),
                    'setup' => $this->input->post('setup'),
                    'start_date' => $this->input->post('start_date'),
                    'end_date' => $this->input->post('end_date'),
                    'division' => $this->input->post('division'),
                ];

                // Insert data into the database
                if ($this->Event_model->create($data)) {
                    set_alert('success', 'Event Added successfully.');
                    redirect('admin/events_due/events');
                } else {
                    set_alert('danger', 'An error occurred while adding the event.');
                    $this->load->view('events/index');
                }

            } catch (Exception $exception) {

                set_alert('danger', 'An error occurred: ' . $exception->getMessage());
                redirect('admin/events_due/events');
                log_message('error', $exception->getMessage());

            }

        }

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