<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;

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

                // Set validation rules for only the first delegate
                $this->form_validation->set_rules("delegates[$key][first_name]", 'First Name', 'trim|required');
                $this->form_validation->set_rules("delegates[$key][last_name]", 'Last Name', 'trim|required');
                $this->form_validation->set_rules("delegates[$key][email]", 'Email', 'trim|required|valid_email');
                $this->form_validation->set_rules("delegates[$key][phone]", 'Phone', 'trim|required');

                // Break after the first delegate
                break;
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

                $this->create_unique_code($this->Event_details_model->get_event_detail($event_detail_id)[0]);


                $attendanceData = [
                    'event_id' => $eventData['event_id'],
                    'venue' => $eventData['venue'],
                    'location' => $eventData['location'],
                    'start_date' => $eventData['start_date'],
                    'end_date' => $eventData['end_date'],
                ];
                $this->Attendance_model->create($attendanceData);

                // Register event
                $this->db->insert(db_prefix() . 'events_due_registrations', [
                    'event_detail_id' => $event_detail_id,
                    'clients' => serialize($input['delegates'] ?? []),
                ]);


                // Define templates
                $templates = [
                    'training_invitation_letter.docx' => [
                        'date' =>  date('jS F Y', strtotime(date('Y-m-d'))),
                        'period' => $period,
                        'venue' => "$venue $location",
                        'cost_per_delegate' => 'KSHS ' . number_format((1.16 * $numDelegates * $costPerDelegate) / $numDelegates),
                        'organization' => $organization,
                        'event' => $eventName,
                        'delegates' => $delegatesList,
                    ],
                    'profoma_invoice.docx' => [
                        'venue' => "$venue $location",
                        'date' =>  date('jS F Y', strtotime(date('Y-m-d'))),
                        'period' => $period,
                        'invoice_number' => $this->generate_invoice_number(),
                        'total_fee' => number_format($numDelegates * $costPerDelegate),
                        'cost_per_delegate' => number_format($costPerDelegate),
                        'num_del' => number_format($numDelegates * 1),
                        'vat_per_delegate' => number_format((0.16 * $numDelegates * $costPerDelegate) / $numDelegates),
                        'organization' => $organization,
                        'total_chargeable' => number_format(1.16 * $numDelegates * $costPerDelegate),
                        'delegates' => $delegatesList,
                        'event' => $eventName,
                        'total_tax' => number_format(0.16 * $numDelegates * $costPerDelegate),
                    ],
//                    'course_content.docx' => [
//                        'event' => $eventName,
//                    ],
                ];

                $startDate = strtotime($input['start_date'] ?? '');
                $endDate = strtotime($input['end_date'] ?? '');

                if ($startDate && $endDate && date('F Y', $startDate) === date('F Y', $endDate)) {
                    $formattedDate = date('j', $startDate) . '–' . date('j', $endDate) . ' ' . strtoupper(date('F Y', $endDate));
                } else {
                    $formattedDate = date('j M Y', $startDate) . ' – ' . date('j M Y', $endDate);
                }

                $generatedFiles = $this->fillWordTemplates($templates);
                $remaining_delegates = array_slice($input['delegates'], 1);
                $cc_emails = array_column($remaining_delegates, 'email');

                $host = $_SERVER['HTTP_HOST'];

                if (strpos($host, 'app') !== false) {
                    $cc_emails[] = 'kevinmusungu455@gmail.com';
                } elseif (strpos($host, 'capabuil') !== false) {
                    $cc_emails[] = 'customerservice@capabuil.com';
                }

                $this->send_email_with_attachments(
                    $input['delegates'][0]['first_name'],
                    $input['delegates'][0]['email'],
                    $generatedFiles,
                    $eventName,
                    $cc_emails,
                    $formattedDate,
                    $venue . ',' . $location
                );

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


    public function create_unique_code_manually()
    {
        $event_details = $this->db->get(db_prefix() . '_events_details')->result();
        foreach ($event_details as $event) {

            if (empty($event->location)) {
                $event->location = $event->venue;
            }

            $data = [
                'event_id' => $event->event_id,
                'event_unique_code' => $this->generateEventUniqueCode($event->event_id, $event->venue, $event->location, $event->start_date),
                'location' => $event->location,
                'venue' => $event->venue,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,
            ];

            $this->db->where('event_id', $event->event_id);
            $this->db->where('location', $event->location);
            $this->db->where('venue', $event->venue);
            $this->db->where('start_date', $event->start_date);
            $this->db->where('end_date', $event->end_date);
            $query = $this->db->get(db_prefix() . 'event_unique_codes');

            if ($query->num_rows() > 0) {
                $this->db->where('event_id', $event->event_id);
                $this->db->where('location', $event->location);
                $this->db->where('venue', $event->venue);
                $this->db->where('start_date', $event->start_date);
                $this->db->where('end_date', $event->end_date);
                $this->db->update(db_prefix() . 'event_unique_codes', $data);
            } else {
                $data['event_unique_code'] = $this->generateEventUniqueCode($event->event_id, $event->venue, $event->location, $event->start_date);
                $this->db->insert(db_prefix() . 'event_unique_codes', $data);
            }
        }

    }


    public function create_unique_code($event)
    {

        if (empty($event->location)) {
            $event->location = $event->venue;
        }

        $data = [
            'event_id' => $event->event_id,
            'event_unique_code' => $this->generateEventUniqueCode($event->event_id, $event->venue, $event->location, $event->start_date),
            'location' => $event->location,
            'venue' => $event->venue,
            'start_date' => $event->start_date,
            'end_date' => $event->end_date,
        ];

        // Check if the event already exists
        $this->db->where('event_id', $event->event_id);
        $this->db->where('location', $event->location);
        $this->db->where('venue', $event->venue);
        $this->db->where('start_date', $event->start_date);
        $this->db->where('end_date', $event->end_date);
        $query = $this->db->get(db_prefix() . 'event_unique_codes');

        if ($query->num_rows() > 0) {
            $this->db->where('event_id', $event->event_id);
            $this->db->where('location', $event->location);
            $this->db->where('venue', $event->venue);
            $this->db->where('start_date', $event->start_date);
            $this->db->where('end_date', $event->end_date);
            $this->db->update(db_prefix() . 'event_unique_codes', $data);
        } else {
            $data['event_unique_code'] = $this->generateEventUniqueCode($event->event_id, $event->venue, $event->location, $event->start_date);
            $this->db->insert(db_prefix() . 'event_unique_codes', $data);
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
            try {

                 $templatePath = $templateFolder . $templateFile;

                 $sanitizedName = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($templateFile, PATHINFO_FILENAME));
                 $shortName = substr($sanitizedName, 0, 50); // limit to 50 chars
                 $fileName = $shortName . '_' . time() . '.docx';

                 $outputFile = $outputFolder . $fileName;


                if (!file_exists($templatePath)) {
                    throw new Exception("Template file does not exist: $templatePath");
                }

                $templateProcessor = new TemplateProcessor($templatePath);
 
                 foreach ($data as $key => $value) {
		    if (!is_string($value)) continue;

   		       // Escape for XML
    		       $safeValue = htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');

    		      // Optional: wrap long values to avoid breaking XML
    		      if (strlen($safeValue) > 100) {
        	        	$safeValue = wordwrap($safeValue, 60, "\n", true);
    		       }

   		 $templateProcessor->setValue($key, $safeValue);
		}


               $templateProcessor->saveAs($outputFile);

                $pdfFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.pdf';
                $pdfOutputFile = $outputFolder . $pdfFileName;

                $this->convertDocxToPdf($outputFile, $pdfOutputFile);
                $generatedFiles[] = $pdfOutputFile;

            } catch (Exception $e) {
                error_log("Error processing template $templateFile: " . $e->getMessage());
                $generatedFiles[] = "Error processing $templateFile: " . $e->getMessage();
            }
        }

        return $generatedFiles; // Return a flat array of PDF paths (including errors if any)
    }

  
   private function convertDocxToPdf($docxFile, $pdfOutputFile)
   {
     $libreOfficePath = '/usr/bin/libreoffice';
     $outputDir = dirname($pdfOutputFile);

    // Check LibreOffice exists
    if (!file_exists($libreOfficePath)) {
        die("❌ LibreOffice not found at: $libreOfficePath");
    }

    // Check input DOCX
    if (!file_exists($docxFile)) {
        die("❌ DOCX file missing: $docxFile");
    }

    // Check output folder
    if (!is_dir($outputDir)) {
        die("❌ Output folder missing: $outputDir");
    }

    // Build and run the command
    if (!file_exists('/tmp/lo-profile')) {
      mkdir('/tmp/lo-profile', 0777, true);
    }

   $command = $libreOfficePath 
    . " --headless -env:UserInstallation=file:///tmp/lo-profile" 
    . " --convert-to pdf " 
    . escapeshellarg($docxFile) 
    . " --outdir " 
    . escapeshellarg($outputDir);


     $output = shell_exec($command . ' 2>&1');
     $exitCode = null;
     exec($command . ' 2>&1', $fullOutput, $exitCode);

     // Combine the full output
     $joinedOutput = implode("\n", $fullOutput);

     // Show full command and raw output
     if (!file_exists($pdfOutputFile)) {
         echo "<b>❌ PDF not created</b><br>";
         echo "<b>Command executed:</b><br><pre>$command</pre>";
         echo "<b>Exit Code:</b> $exitCode<br>";
         echo "<b>LibreOffice Output:</b><br><pre>$joinedOutput</pre>";
         exit;
     }

    // Success
    echo "✅ PDF created successfully: $pdfOutputFile<br><pre>$output</pre>";
  
  }


    public function send_email_with_attachments($client, $to, $generatedFiles, $event_name, $cc_emails, $date, $location)
    {
        $template_slug = 'event-due-registration';
        $merge_fields = [
            'client_name' => $client,
            'event_name' => $event_name,
            'date' => $date,
            'location' => $location
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
