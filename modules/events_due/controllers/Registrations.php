<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Registrations extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Registration_model');
        $this->load->model('Event_model');
        $this->load->model('Client_model');
        $this->load->model('Registration_model');
        $this->load->library('form_validation');

    }

    public function validateRegistration()
    {

        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|min_length[2]|max_length[50]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|min_length[2]|max_length[50]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[100]');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|min_length[10]|max_length[20]');
        $this->form_validation->set_rules('organization', 'Organization', 'trim|required');
        $this->form_validation->set_rules('event_name_id', 'Event Name ID', 'trim|required|integer');
        $this->form_validation->set_rules('location_id', 'Location ID', 'trim|required|integer');
        $this->form_validation->set_rules('venue_id', 'Venue ID', 'trim|required|integer');
        $this->form_validation->set_rules('duration', 'Duration', 'trim|required');
        $this->form_validation->set_rules('setup', 'Setup', 'trim|required');

    }

    public function create()
    {
        $data['events'] = $this->Event_model->get();
        $this->load->view('registrations/create', $data);
    }

    //get registration event_id
    public function get_registration_event_id($data)
    {
        return $this->db->select('id')
            ->from(db_prefix() . 'events_due_events')
            ->where([
                'event_name_id' => $data['event_name_id'],
                'location_id' => $data['location_id'],
                'venue_id' => $data['venue_id'],
                'setup' => $data['setup']
            ])
            ->get()
            ->row('id'); // Get only the event_id field
    }

    public function get_client_by_email($email)
    {
        return $this->db->select('id')
            ->from(db_prefix() . 'events_due_clients')
            ->where('email', $email)
            ->get()
            ->row('id');
    }


    public function store()
    {
        $this->validateRegistration();

        if ($this->form_validation->run() == FALSE) {

            redirect('admin/events_due/registrations/create');

        } else {

            try {

                //customer data
                $customer_data = [
                    'full_name' => $this->input->post('first_name') . ' ' . $this->input->post('last_name'),
                    'email' => $this->input->post('email'),
                    'phone_number' => $this->input->post('phone_number'),
                    'organization_name' => $this->input->post('organization'),
                ];

                // Check if client already exists
                $existing_client_id = $this->get_client_by_email($customer_data['email']);

                if ($existing_client_id) {
                    $client_id = $existing_client_id; // Use existing client ID
                } else {
                    $client_id = $this->Client_model->create($customer_data); // Create new client
                }

                //insert event registration
                $postData = $this->input->post();
                $event_id = $this->get_registration_event_id($postData);


                if ($this->Registration_model->create([
                    'event_id' => $event_id,
                    'client_id' => $client_id
                ])) {
                    set_alert('success', 'Event Resgistration was successful.');
                    redirect('admin/events_due/registrations/create');
                } else {
                    set_alert('danger', 'An error occurred while registering the client.');
                    $this->load->view('registrations/create');
                }

            } catch (Exception $exception) {

                set_alert('danger', 'An error occurred: ' . $exception->getMessage());
                redirect('admin/events_due/registrations/create');
                log_message('error', $exception->getMessage());

            }

        }
    }


}