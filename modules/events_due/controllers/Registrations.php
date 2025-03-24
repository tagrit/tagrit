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


    }

    public function validateRegistration()
    {

        // Set validation rules
        $this->form_validation->set_rules('location_id', 'Location', 'required');
        $this->form_validation->set_rules('venue_id', 'Venue', 'required');
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
            dd(validation_errors());
            redirect('admin/events_due/registrations/create');
        } else {
            // Start database transaction
            $this->db->trans_begin();

            try {
                $data = [
                    'venue' => $this->Event_venue_model->get($this->input->post('venue_id'))->name ?? '',
                    'location' => $this->Event_location_model->get($this->input->post('location_id'))->name ?? '',
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

                // Fill template and send email
                $templates = [
                    'training_invitation_letter.docx' => [
                        'date' => '2025-04-15',
                        'period' => '2025-04-15',
                        'venue' => 'Hilton Hotel',
                        'cost_per_delegate' => '$200',
                        'organization' => 'Tech Corp',
                        'event' => 'AI Conference',
                        'delegates' => 'John Doe, Jane Smith'
                    ],
                    'profoma_invoice.docx' => [
                        'date' => '2025-04-15',
                        'period' => '2025-04-15',
                        'invoice_number' => 'INV250128-15D',
                        'total_fee' => '5000',
                        'cost_per_delegate' => '1000',
                        'organization' => 'Tech Corp',
                        'total_chargeable' => 1.16 * 5000,
                        'delegates' => 'John Doe, Jane Smith',
                        'event' => 'AI Conference',
                        'total_tax' => '500',
                    ],
                    'course_content.docx' => [
                        'event' => 'AI Conference',
                    ]
                ];

                $generatedFiles = $this->fillWordTemplates($templates);

                $this->send_email_with_attachments(
                    'kevinamayi20@gmail.com',
                    $generatedFiles
                );

                // Commit transaction if everything is fine
                if ($this->db->trans_status() === FALSE) {
                    throw new Exception('Transaction failed.');
                }

                $this->db->trans_commit();

                // Set success message and redirect
                set_alert('success', 'Registration successfully completed');
                redirect('admin/events_due/registrations/create');

            } catch (Exception $exception) {

                dd($exception->getMessage());
                // Rollback transaction on failure
                $this->db->trans_rollback();

                set_alert('danger', 'An error occurred: ' . $exception->getMessage());
                log_message('error', $exception->getMessage());

                redirect('admin/events_due/registrations/create');
            }
        }
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

    public function send_email_with_attachments($to, $generatedFiles)
    {
        $template_slug = 'event-due-registration';
        $merge_fields = [
            'client' => 'Kevin Amayi',
            'event_name' => 'Test Event Name'
        ];

        if (is_array($generatedFiles)) {
            foreach ($generatedFiles as $filePath) {
                $this->emails_model->add_attachment($filePath);
            }
        } else {
            $this->emails_model->add_attachment(FCPATH . 'uploads/events_due_files/training_invitation_letter_1742750826.docx');
        }

        return $this->emails_model->send_email_template($template_slug, $to, $merge_fields);
    }

}