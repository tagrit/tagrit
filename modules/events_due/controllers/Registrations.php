<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Registrations extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Registration_model');
        $this->load->model('Event_model');
        $this->load->model('Registration_model');
        $this->load->model('Event_details_model');
        $this->load->library('form_validation');

    }

    public function validateRegistration()
    {

        // Set validation rules
        $this->form_validation->set_rules('location', 'Location', 'required');
        $this->form_validation->set_rules('venue', 'Venue', 'required');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required');
        $this->form_validation->set_rules('end_date', 'End Date', 'required');
        $this->form_validation->set_rules('organization', 'Organization', 'required');
        $this->form_validation->set_rules('no_of_delegates', 'Number of Delegates', 'required|numeric');
        $this->form_validation->set_rules('charges_per_delegates', 'Charges Per Delegate', 'required|numeric');
        $this->form_validation->set_rules('setup', 'Setup', 'required');
        $this->form_validation->set_rules('division', 'Division', 'required');
        $this->form_validation->set_rules('revenue', 'Charges', 'required|numeric');

        // Validate delegate details dynamically
        if (!empty($_POST['delegates'])) {
            foreach ($_POST['delegates'] as $key => $delegate) {
                $this->form_validation->set_rules("delegates[$key][first_name]", 'First Name', 'trim|required');
                $this->form_validation->set_rules("delegates[$key][last_name]", 'Last Name', 'trim|required');
                $this->form_validation->set_rules("delegates[$key][email]", 'Email', 'trim|required|valid_email');
                $this->form_validation->set_rules("delegates[$key][phone]", 'Phone', 'trim|required');
            }
        }

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


    public function store()
    {

        $this->validateRegistration();

        if ($this->form_validation->run() == FALSE) {
            redirect('admin/events_due/registrations/create');
        } else {
            // Start database transaction
            $this->db->trans_begin();

            try {
                $data = [
                    'venue' => $this->input->post('venue') ?? '',
                    'location' => $this->input->post('location') ?? '',
                    'event_id' => $this->input->post('event_id') ?? '',
                    'organization' => $this->input->post('organization') ?? '',
                    'start_date' => $this->input->post('start_date') ?? '',
                    'end_date' => $this->input->post('end_date') ?? '',
                    'no_of_delegates' => $this->input->post('no_of_delegates') ?? '',
                    'charges_per_delegate' => $this->input->post('charges_per_delegate') ?? '',
                    'division' => $this->input->post('division') ?? '',
                    'trainers' => serialize($this->input->post('trainers') ?? ['capabuil']),
                    'facilitator' => $this->input->post('facilitator') ?? 'capabuil',
                    'revenue' => $this->input->post('revenue') ?? '',
                    'setup' => $this->input->post('setup') ?? '',
                    'type' => $this->input->post('type') ?? '',
                ];

                $event_detail_id = $this->Event_details_model->add($data);

                // Register event
                $insert_data = [
                    'event_detail_id' => $event_detail_id,
                    'clients' => serialize($this->input->post('delegates') ?? []),
                ];

                $this->db->insert(db_prefix() . 'events_due_registrations', $insert_data);

                // Commit transaction if everything is fine
                if ($this->db->trans_status() === FALSE) {
                    throw new Exception('Transaction failed.');
                }

                $this->db->trans_commit();

                // Set success message and redirect
                set_alert('success', 'Registration successfully completed!');
                redirect('admin/events_due/registrations/create');

            } catch (Exception $exception) {
                // Rollback transaction on failure
                $this->db->trans_rollback();

                set_alert('danger', 'An error occurred: ' . $exception->getMessage());
                log_message('error', $exception->getMessage());

                redirect('admin/events_due/registrations/create');
            }
        }
    }

}