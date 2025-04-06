<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpWord\TemplateProcessor;

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
        $this->load->model('emails_model');
        $this->load->model('Event_location_model');
        $this->load->model('Event_venue_model');
        $this->load->model('Attendance_model');
    }


    public function validateRegistration()
    {
        // Set validation rules
        $this->form_validation->set_rules('location_id', 'Location', 'required');
        $this->form_validation->set_rules('venue_id', 'Venue', 'required');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required|callback_validate_dates');
        $this->form_validation->set_rules('end_date', 'End Date', 'required');
        $this->form_validation->set_rules('organization', 'Organization', 'required');
        $this->form_validation->set_rules('no_of_delegates', 'Number of Delegates', 'required|numeric');
        $this->form_validation->set_rules('charges_per_delegate', 'Charges Per Delegate', 'required|numeric');
        $this->form_validation->set_rules('setup', 'Setup', 'required');
        $this->form_validation->set_rules('division', 'Division', 'required');
        $this->form_validation->set_rules('revenue', 'Charges', 'required|numeric');

        if (!empty($_POST['delegates'])) {
            foreach ($_POST['delegates'] as $key => $delegate) {
                // Convert null values to empty strings
                $_POST['delegates'][$key]['first_name'] = $delegate['first_name'] ?? '';
                $_POST['delegates'][$key]['last_name'] = $delegate['last_name'] ?? '';
                $_POST['delegates'][$key]['email'] = $delegate['email'] ?? '';
                $_POST['delegates'][$key]['phone'] = $delegate['phone'] ?? '';

                // Set validation rules
                $this->form_validation->set_rules("delegates[$key][first_name]", 'First Name', 'trim|required');
                $this->form_validation->set_rules("delegates[$key][last_name]", 'Last Name', 'trim|required');
                $this->form_validation->set_rules("delegates[$key][email]", 'Email', 'trim|required|valid_email');
                $this->form_validation->set_rules("delegates[$key][phone]", 'Phone', 'trim|required');
            }
        }

    }

    public function validate_dates($start_date)
    {
        $end_date = $this->input->post('end_date');

        if (!empty($end_date) && strtotime($start_date) > strtotime($end_date)) {
            $this->form_validation->set_message('validate_dates', 'Start Date cannot be later than End Date.');
            return false;
        }
        return true;
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

            $data = [
                'events' => $this->Event_model->get(),
                'old_input' => $_POST ?? []
            ];

            $this->load->view('registrations/create', $data);
        } else {


            // Start database transaction
            $this->db->trans_begin();

            try {
                // Fetch input values
                $input = $this->input->post();
                $venue = strtoupper($this->Event_venue_model->get($input['venue_id'])['name'] ?? '');
                $location = strtoupper($this->Event_location_model->get($input['location_id'])['name'] ?? '');
                $eventName = strtoupper($this->Event_model->get($input['event_id'])['name'] ?? '');
                $startDate = date('jS F Y', strtotime($input['start_date'] ?? ''));
                $period = strtoupper(date('jS', strtotime($input['start_date'] ?? ''))) . '-' . strtoupper(date('jS', strtotime($input['end_date'] ?? ''))) . ' ' . strtoupper(date('F Y', strtotime($input['start_date'] ?? '')));
                $organization = strtoupper($input['organization'] ?? '');
                $costPerDelegate = (float)$input['charges_per_delegate'] ?? 0;
                $numDelegates = (int)$input['no_of_delegates'] ?? 0;
                $delegatesList = implode(', ', array_map(function ($delegate) {
                    if (isset($delegate['first_name'], $delegate['last_name'])) {
                        return ucwords(strtolower($delegate['first_name'])) . ' ' . ucwords(strtolower($delegate['last_name']));
                    }
                    return null;
                }, $input['delegates'] ?? []));

                $delegatesList = implode(', ', array_filter(explode(', ', $delegatesList)));


                // Prepare event details data
                $eventData = [
                    'venue' => $venue,
                    'location' => $location,
                    'event_id' => $input['event_id'] ?? '',
                    'organization' => $input['organization'] ?? '',
                    'start_date' => $input['start_date'] ?? '',
                    'end_date' => $input['end_date'] ?? '',
                    'no_of_delegates' => $numDelegates,
                    'charges_per_delegate' => $costPerDelegate,
                    'division' => $input['division'] ?? '',
                    'trainers' => serialize($input['trainers'] ?? ['capabuil']),
                    'facilitator' => $input['facilitator'] ?? 'capabuil',
                    'revenue' => $input['revenue'] ?? '',
                    'setup' => $input['setup'] ?? '',
                    'type' => $input['type'] ?? '',
                ];

                $event_detail_id = $this->Event_details_model->add($eventData);

                //event_code
                $this->Attendance_model->create($eventData);

                // Register event
                $this->db->insert(db_prefix() . 'events_due_registrations', [
                    'event_detail_id' => $event_detail_id,
                    'clients' => serialize($input['delegates'] ?? []),
                ]);

                // Define templates
                $templates = [
                    'training_invitation_letter.docx' => [
                        'date' => $startDate,
                        'period' => $period,
                        'venue' => "$venue $location",
                        'cost_per_delegate' => 'KSHS ' . (1.16 * $numDelegates * $costPerDelegate) / $numDelegates,
                        'organization' => $organization,
                        'event' => $eventName,
                        'delegates' => $delegatesList,
                    ],
                    'profoma_invoice.docx' => [
                        'venue' => "$venue $location",
                        'date' => $startDate,
                        'period' => $period,
                        'invoice_number' => $this->generate_invoice_number(),
                        'total_fee' => $numDelegates * $costPerDelegate,
                        'cost_per_delegate' => $costPerDelegate,
                        'vat_per_delegate' => (0.16 * $numDelegates * $costPerDelegate) / $numDelegates,
                        'organization' => $organization,
                        'total_chargeable' => 1.16 * $numDelegates * $costPerDelegate,
                        'delegates' => $delegatesList,
                        'event' => $eventName,
                        'total_tax' => 0.16 * $numDelegates * $costPerDelegate,
                    ],
                    'course_content.docx' => [
                        'event' => $eventName,
                    ],
                ];

                $generatedFiles = $this->fillWordTemplates($templates);
                $remaining_delegates = array_slice($input['delegates'], 1);
                $cc_emails = array_column($remaining_delegates, 'email');
                $this->send_email_with_attachments($input['delegates'][0]['first_name'] . ' ' . $input['delegates'][0]['last_name'], $input['delegates'][0]['email'], $generatedFiles, $eventName, $cc_emails);

                // Commit transaction if successful
                if ($this->db->trans_status() === FALSE) {
                    throw new Exception('Transaction failed.');
                }

                $this->db->trans_commit();

                // Set success message and redirect
                set_alert('success', 'Registration successfully completed');
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

    private function generate_invoice_number()
    {
        $date_part = date('ymd'); // YYMMDD format (e.g., 250128 for 2025-01-28)
        $unique_part = substr(str_replace('.', '', microtime(true)), -5);
        $identifier = 'D';
        $invoice_number = "INV{$date_part}-{$unique_part}{$identifier}";

        return $invoice_number;
    }


    private function fillWordTemplates($templates)
    {
        $generatedFiles = []; // Should be a flat array
        $templateFolder = FCPATH . 'modules/events_due/assets/templates/';
        $outputFolder = FCPATH . 'uploads/events_due_files/';

        // Ensure the output folder exists
        if (!is_dir($outputFolder)) {
            mkdir($outputFolder, 0777, true); // Create directory with full permissions
        }

        foreach ($templates as $templateFile => $data) {
            $templatePath = $templateFolder . $templateFile;
            $fileName = pathinfo($templateFile, PATHINFO_FILENAME) . '_' . time() . '.docx';
            $outputFile = $outputFolder . $fileName;

            // Load template and replace placeholders dynamically
            $templateProcessor = new TemplateProcessor($templatePath);

            foreach ($data as $key => $value) {
                $templateProcessor->setValue($key, $value);
            }

            $templateProcessor->saveAs($outputFile);

            // Append each file directly to the array (fixes nested array issue)
            $generatedFiles[] = $outputFile;
        }

        return $generatedFiles; // Return a flat array
    }

    public function send_email_with_attachments($client, $to, $generatedFiles, $event_name, $cc_emails)
    {
        $template_slug = 'event-due-registration';
        $merge_fields = [
            'client' => $client,
            'event_name' => $event_name
        ];

        if (is_array($generatedFiles)) {
            foreach ($generatedFiles as $filePath) {
                $this->emails_model->add_attachment($filePath);
            }
        } else {
            $this->emails_model->add_attachment(FCPATH . 'uploads/events_due_files/training_invitation_letter_1742750826.docx');
        }

        return $this->emails_model->send_email_template($template_slug, $to, $merge_fields, '', $cc_emails);
    }

}