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
        $data['events'] = $this->Event_model->events();
        $this->load->view('events/index', $data);
    }

    public function view()
    {

        $this->load->library('session');

        // Get input values from POST or fallback to session data if not available
        $event_id = $this->input->post('event_id') ?? $this->session->userdata('event_id');
        $location = $this->input->post('location') ?? $this->session->userdata('location');
        $venue = $this->input->post('venue') ?? $this->session->userdata('venue');
        $start_date = $this->input->post('start_date') ?? $this->session->userdata('start_date');
        $end_date = $this->input->post('end_date') ?? $this->session->userdata('end_date');

        // Store data to session if any of the fields have been posted
        if ($this->input->post()) {
            // Store the input values in the session if they are provided
            $this->session->set_userdata([
                'event_id' => $event_id,
                'location' => $location,
                'venue' => $venue,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ]);
        }

        // Fetch event data based on the stored session data
        if ($event_id && $location && $venue && $start_date && $end_date) {
            $event_data = $this->Event_model->event_details($event_id, $location, $venue, $start_date, $end_date);

            // Store event data in session if not already available
            if (!empty($event_data)) {
                $this->session->set_userdata('event_data', $event_data);
            }
        }

        // Retrieve event data from session if available
        $data['event_data'] = $this->session->userdata('event_data');


        if (empty($data['event_data'])) {
            show_error('No event data available.', 404);
        }

        // Load the view with the event data
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
        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');

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
            'start_date' => $startDate,
            'end_date' => $endDate,
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

    //update event_delegate confirmation
    public function event_confirmation()
    {

        // Step 1: Get delegate data from the post request
        $delegateData = $this->input->post();

        // Step 2: Get event details using the event_unique_code
        $eventDetails = $this->db->get_where(db_prefix() . 'event_unique_codes', [
            'event_unique_code' => $delegateData['event_unique_code']
        ])->row();

        // Check if event details exist
        if ($eventDetails) {

            $event_id = $eventDetails->event_id;
            $start_date = $eventDetails->start_date;
            $end_date = $eventDetails->end_date;
            $location = $eventDetails->location;
            $venue = $eventDetails->venue;

            // Step 3: Get event detail ID from events_details table using the event_id, start_date, end_date, location, venue, and organization
            $eventDetail = $this->db->get_where(db_prefix() . '_events_details', [
                'event_id' => $event_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'location' => $location,
                'venue' => $venue,
                'organization' => $delegateData['delegate_organization']
            ])->row();

            // Check if event detail exists
            if ($eventDetail) {
                $event_detail_id = $eventDetail->id;

                // Step 4: Retrieve serialized clients from event_registration table using event_detail_id
                $registration = $this->db->get_where(db_prefix() . 'events_due_registrations', [
                    'event_detail_id' => $event_detail_id
                ])->row();

                if ($registration && $registration->clients) {
                    $clients = unserialize($registration->clients);

                    // Step 5: Find the specific client based on email and phone
                    foreach ($clients as &$client) {
                        if ($client['email'] == $delegateData['delegate_email'] && $client['phone'] == $delegateData['delegate_phone']) {

                            // Check if attendance_confirmed exists, if not initialize it to 0 (Not Confirmed)
                            if (!isset($client['attendance_confirmed'])) {
                                $client['attendance_confirmed'] = 0; // Not Confirmed
                            }

                            // Toggle the attendance status (confirm or cancel)
                            $client['attendance_confirmed'] = !$client['attendance_confirmed'];

                            // Step 7: Serialize the clients array back
                            $updatedClients = serialize($clients);

                            // Step 8: Update the event_registration table with the new clients data
                            $this->db->update(db_prefix() . 'events_due_registrations', [
                                'clients' => $updatedClients
                            ], [
                                'event_detail_id' => $event_detail_id
                            ]);

                            set_alert('success', 'Attendance confirmation updated successfully!');

                            // Redirect back to the previous page
                            $previous_url = $this->agent->referrer();
                            redirect($previous_url);

                            break;
                        }
                    }


                    // If the client was not found in the array
                    $this->session->set_flashdata('error', 'Client not found.');
                    redirect('admin/events_due/events');

                } else {
                    set_alert('danger', 'Error: ' . "No clients found for this event registration.");
                }
            } else {
                set_alert('danger', 'Error: ' . "Event detail not found for the organization and event.");
            }
        } else {
            set_alert('danger', 'Error: ' . "Event not found for the given unique code.");
        }
    }

}
