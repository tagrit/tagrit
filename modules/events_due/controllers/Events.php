<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Events extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Event_model');
        $this->load->model('Attendance_model');
        $this->load->library('form_validation');

    }

    public function validate()
    {
        $this->form_validation->set_rules('event_name', 'Event Name', 'required');
    }

    public function index()
    {
        $data['events'] = $this->Event_model->event_details();
        $this->load->view('events/index', $data);
    }

    public function view($event_id)
    {
        $data['event'] = $this->Event_model->event_details($event_id);
        $this->load->view('events/view', $data);
    }

    public function store()
    {

        if ($this->input->post()) {

            $this->validate();

            if ($this->form_validation->run() === false) {
                set_alert('danger', validation_errors());
            } else {
                // Save Data
                $data = [
                    'name' => $this->input->post('event_name')
                ];

                $insert_id = $this->Event_model->add($data);

                if ($insert_id) {
                    set_alert('success', 'Event created successfully!');

                    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

                    if (strpos($referer, 'admin/imprest/fund_requests/create') !== false) {
                        redirect(admin_url('imprest/fund_requests/create'));
                    } else {
                        redirect(admin_url('events_due/registrations/create'));
                    }
                } else {
                    set_alert('danger', 'Failed to create event.');
                }

            }
        }

        $this->load->view('events/create');
    }

    // Optional: Custom validation for dates
    public function validate_date($date)
    {
        if (strtotime($date) === false) {
            $this->form_validation->set_message('validate_date', 'The {field} field must be a valid date.');
            return false;
        }
        return true;
    }

    public function edit($event_id)
    {
        $data['event'] = $this->Event_model->get($event_id);
        $this->load->view('events/edit', $data);
    }

    public function update()
    {
        // Get the event ID from the POST request
        $event_id = $this->input->post('event_id');

        if (empty($event_id)) {
            set_alert('danger', 'Event ID is missing.');
            redirect('events_due/events/index');
            return; // Stop further execution if no event ID is provided
        }

        // Fetch the event from the database
        $event = $this->Event_model->get($event_id);

        // If event not found, show 404 error
        if (empty($event)) {
            show_404();
        }

        // Run form validation
        $this->validate(); // Assume the validate function handles the validation rules

        // Check if form validation passed
        if ($this->form_validation->run() === FALSE) {
            set_alert('danger', validation_errors());
        } else {
            // Prepare data to be updated
            $data = [
                'name' => $this->input->post('name'),
            ];

            // Attempt to update the event
            if ($this->Event_model->update($event['id'], $data)) {
                set_alert('success', 'Event updated successfully!');
                redirect('events_due/events/index');
            } else {
                set_alert('danger', 'Failed to update event.');
                redirect('events/edit/' . $event['id']);
            }
        }

        $data['event'] = $event;
        $this->load->view('events/edit', $data);

    }

    public function upload_attendance_sheet()
    {
        // Fetch form data
        $event_id = $this->input->post('event_id');
        $location = $this->input->post('location');
        $venue = $this->input->post('venue');

        // Check if the file exists in the request
        if (!isset($_FILES['attendance_sheet']) || $_FILES['attendance_sheet']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('No file uploaded or file upload error occurred.');
        }

        $file_data = $_FILES['attendance_sheet'];

        // Define upload directory
        $upload_path = FCPATH . 'modules/events_due/assets/event_attendance_sheets/';

        // Ensure upload directory exists
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        // Generate a unique file name
        $file_name = time() . '_' . $file_data['name'];
        $file_path = $upload_path . $file_name;

        // Move uploaded file to the destination folder
        if (!move_uploaded_file($file_data['tmp_name'], $file_path)) {
            throw new Exception('Failed to move the uploaded file. Error code: ' . $file_data['error']);
        }

        // Build file URL
        $attendance_sheet_url = base_url('modules/events_due/assets/event_attendance_sheets/' . $file_name);

        // Prepare data for insertion
        $data = [
            'event_id' => $event_id,
            'location' => $location,
            'venue' => $venue,
            'attendance_url' => $attendance_sheet_url,
        ];

        // Insert into database inside a transaction
        $this->db->trans_begin();
        try {
            if (!$this->Attendance_model->create($data)) {
                throw new Exception('Failed to save attendance record to the database.');
            }

            $this->db->trans_commit();
            set_alert('success', 'Attendance sheet uploaded successfully!');
            redirect('admin/events_due/events/index');

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'File Upload Error: ' . $e->getMessage());
            set_alert('danger', 'Error: ' . $e->getMessage());
            redirect('admin/events_due/events/index');
        }
    }

}
