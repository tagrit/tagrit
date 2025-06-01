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

        // Attempt to fetch event data
        $event_data = null;

        // Fetch event data based on the stored session data
        if ($event_id && $location && $venue && $start_date && $end_date) {
            $event_data = $this->Event_model->event_details($event_id, $location, $venue, $start_date, $end_date);

            // Store event data in session if not already available
            if (!empty($event_data)) {
                $this->session->set_userdata('event_data', $event_data);
            }
        }

        // Use newly fetched data OR fallback to session
        $data['event_data'] = $event_data ?: $this->session->userdata('event_data');

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


    public function get_location_and_venue_names($location_id, $venue_id)
    {
        $result = [];

        // Get Location Name
        $location = $this->db->get_where(db_prefix() . 'events_due_locations', ['id' => $location_id])->row();
        if ($location) {
            $result['location_name'] = $location->name;
        } else {
            $result['location_name'] = null;
        }

        // Get Venue Name
        $venue = $this->db->get_where(db_prefix() . 'events_due_venues', ['id' => $venue_id])->row();
        if ($venue) {
            $result['venue_name'] = $venue->name;
        } else {
            $result['venue_name'] = null;
        }

        return $result;
    }


    public function edit()
    {
        $this->load->model('Event_model');

        $identifiers = [
            'event_id' => $this->input->post('event_id'),
            'location' => $this->input->post('location'),
            'venue' => $this->input->post('venue'),
            'start_date' => $this->input->post('startDate'),
            'end_date' => $this->input->post('endDate'),
        ];


        $location_venue = $this->get_location_and_venue_names($this->input->post('edit_location_id'), $this->input->post('edit_venue_id'));

        $updateData = [
            'event_id' => $this->input->post('edit_event_id'),
            'location' => $location_venue['location_name'],
            'venue' => $location_venue['venue_name'],
            'start_date' => $this->input->post('edit_start_date'),
            'end_date' => $this->input->post('edit_end_date'),
        ];


        try {
            $this->db->trans_begin();

            $updated = $this->Event_model->update_event_by_details($identifiers, $updateData);

            if (!$updated || $this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'Failed to update event with: ' . json_encode($identifiers));
                set_alert('danger', 'Failed to update event.');
            } else {

                $this->update_event_unique_code($identifiers, $updateData);

                $this->db->trans_commit();
                set_alert('success', 'Event updated successfully!');
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Exception during event update: ' . $e->getMessage());
            set_alert('danger', 'An error occurred while updating the event.' . $e->getMessage());
        }

        redirect('admin/events_due/events/index');
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


    public function update_event_unique_code($data, $updateData)
    {

        $updateData = [
            'event_id' => $updateData['event_id'],
            'event_unique_code' => $this->generateEventUniqueCode($updateData['event_id'], $updateData['venue'], $updateData['location'], $updateData['start_date']),
            'location' => $updateData['location'],
            'venue' => $updateData['venue'],
            'start_date' => $updateData['start_date'],
            'end_date' => $updateData['end_date'],
        ];

        // Check if the event already exists
        $this->db->where('event_id', $data['event_id']);
        $this->db->where('location', $data['location']);
        $this->db->where('venue', $data['venue']);
        $this->db->where('start_date', $data['start_date']);
        $this->db->where('end_date', $data['end_date']);
        $query = $this->db->get(db_prefix() . 'event_unique_codes');

        if ($query->num_rows() > 0) {
            $this->db->where('event_id', $data['event_id']);
            $this->db->where('location', $data['location']);
            $this->db->where('venue', $data['venue']);
            $this->db->where('start_date', $data['start_date']);
            $this->db->where('end_date', $data['end_date']);
            $this->db->update(db_prefix() . 'event_unique_codes', $updateData);
        } else {
            $updateData['event_unique_code'] = $this->generateEventUniqueCode($updateData['event_id'], $updateData['venue'], $updateData['location'], $updateData['start_date']);
            $this->db->insert(db_prefix() . 'event_unique_codes', $updateData);
        }
    }


    function generateEventUniqueCode($event_id, $venue, $location, $start_date)
    {
        $event = $this->db->get_where(db_prefix() . '_events', ['id' => $event_id])->row();

        if (!$event) {
            return null;
        }

        // Ensure all values are non-null and default to empty strings if necessary
        $eventName = isset($event->name) ? $event->name : '';
        $venue = isset($venue) ? $venue : '';
        $location = isset($location) ? $location : '';

        // Clean and format inputs
        $eventPart = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $eventName), 0, 4));
        $venuePart = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $venue), 0, 3));
        $locationPart = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $location), 0, 3));
        $startDatePart = date('dmy', strtotime($start_date));

        // Combine to create the code
        return "{$eventPart}-{$venuePart}-{$locationPart}-{$startDatePart}";
    }


    function upload_welcome_email_files()
    {
        $base_upload_path = FCPATH . 'modules/events_due/assets/welcome_email_documents/';

        if (!is_dir($base_upload_path)) {
            mkdir($base_upload_path, 0755, true);
        }

        $uploaded_file_urls = [];

        $file_input_names = [
            'program_outline',
            'accommodation_sites',
            'delegate_information'
        ];

        foreach ($file_input_names as $input_name) {

            if (!isset($_FILES[$input_name])) {
                continue;
            }

            $file_data = $_FILES[$input_name];

            if ($file_data['error'] === UPLOAD_ERR_OK) {
                $file_name = time() . '_' . $file_data['name'];
                $file_path = $base_upload_path . $file_name;

                if (move_uploaded_file($file_data['tmp_name'], $file_path)) {
                    $file_url = base_url('modules/events_due/assets/welcome_email_documents/' . $file_name);
                    $uploaded_file_urls[$input_name] = $file_url;
                } else {
                    throw new Exception('Failed to move uploaded file for ' . $input_name . '. Error code: ' . $file_data['error']);
                }
            } elseif ($file_data['error'] !== UPLOAD_ERR_NO_FILE) {
                $error_message = 'Upload error for ' . $input_name . ': ';

                switch ($file_data['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                        $error_message .= 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $error_message .= 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $error_message .= 'The uploaded file was only partially uploaded.';
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $error_message .= 'Missing a temporary folder.';
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $error_message .= 'Failed to write file to disk.';
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $error_message .= 'A PHP extension stopped the file upload.';
                        break;
                    default:
                        $error_message .= 'Unknown upload error.';
                        break;
                }
                throw new Exception($error_message);
            }
        }

        return $uploaded_file_urls;
    }

    function send_welcome_email()
    {

        $this->db->trans_begin();
        try {

            $uploaded_file_urls = $this->upload_welcome_email_files();
            $table = db_prefix() . '_notification_queue';
            $event_id = $this->input->post('event_id');
            $event_name = $this->input->post('eventName');
            $event_location = $this->input->post('event_location');
            $event_venue = $this->input->post('event_venue');
            $startDate = $this->input->post('startDate');
            $endDate = $this->input->post('endDate');
            $clients_data = $this->input->post('clients')[0];

            $clients_data = json_decode($clients_data, true);

            $serialized_clients = serialize($clients_data);

            foreach ($clients_data as $client) {

                //store confirmed clients
                if (isset($client['attendance_confirmed']) && $client['attendance_confirmed']) {

                    //check if client and event exists
                    $this->db->where('email', $client['email']);
                    $this->db->where('event_name', $this->input->post('eventName'));
                    $this->db->where('client_name', $client['first_name'] . ' ' . $client['last_name']);
                    $this->db->where('event_date', $startDate);
                    $this->db->where('event_location', $event_location);
                    $this->db->where('type', 'welcome_email');
                    $existing_welcome = $this->db->get($table)->row();

                    if ($existing_welcome) {
                        continue;
                    }


                    $data = array(
                        'type' => 'welcome_email',
                        'email' => $client['email'],
                        'client_name' => $client['first_name'] . ' ' . $client['last_name'],
                        'client_list' => $serialized_clients,
                        'event_name' => $event_name,
                        'event_date' => $startDate,
                        'event_location' => $event_location,
                        'status' => 'pending',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'program_outline' => $uploaded_file_urls['program_outline'] ?? null,
                        'accommodation_sites' => $uploaded_file_urls['accommodation_sites'] ?? null,
                        'delegate_information' => $uploaded_file_urls['delegate_information'] ?? null,
                    );

                    $this->db->insert($table, $data);

                }

            }

            $event_id = $this->input->post('event_id') ?? $this->session->userdata('event_id');
            $location = $this->input->post('event_location') ?? $this->session->userdata('location');
            $venue = $this->input->post('event_venue') ?? $this->session->userdata('venue');
            $start_date = $this->input->post('startDate') ?? $this->session->userdata('start_date');
            $end_date = $this->input->post('endDate') ?? $this->session->userdata('end_date');

            if ($event_id && $location && $venue && $start_date && $end_date) {
                $event_data = $this->Event_model->event_details($event_id, $location, $venue, $start_date, $end_date);

                if (!empty($event_data)) {
                    $this->session->set_userdata('event_data', $event_data);
                }
            }

            $data['event_data'] = $event_data ?: $this->session->userdata('event_data');

            if (empty($data['event_data'])) {
                show_error('No event data available.', 404);
            }

            $this->db->trans_commit();
            set_alert('success', 'Welcome Email scheduled to be sent!');

            redirect('admin/events_due/events/view');


        } catch (Exception $e) {

            $event_id = $this->input->post('event_id') ?? $this->session->userdata('event_id');
            $location = $this->input->post('event_location') ?? $this->session->userdata('location');
            $venue = $this->input->post('event_venue') ?? $this->session->userdata('venue');
            $start_date = $this->input->post('startDate') ?? $this->session->userdata('start_date');
            $end_date = $this->input->post('endDate') ?? $this->session->userdata('end_date');

            if ($event_id && $location && $venue && $start_date && $end_date) {
                $event_data = $this->Event_model->event_details($event_id, $location, $venue, $start_date, $end_date);

                // Store event data in session if not already available
                if (!empty($event_data)) {
                    $this->session->set_userdata('event_data', $event_data);
                }
            }

            $data['event_data'] = $event_data ?: $this->session->userdata('event_data');

            if (empty($data['event_data'])) {
                show_error('No event data available.', 404);
            }

            $this->db->trans_rollback();
            log_message('error', 'File Upload Error: ' . $e->getMessage());
            set_alert('danger', 'Error: ' . $e->getMessage());

            redirect('admin/events_due/events/view');

        }

    }


}
