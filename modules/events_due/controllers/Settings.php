<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Settings extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Event_model');
        $this->load->model('Event_name_model');
        $this->load->model('Event_venue_model');
        $this->load->model('Event_location_model');
        $this->load->model('Registration_model');
        $this->load->model('Client_model');

    }

    public function main()
    {
        $group = $this->input->get('group', true) ?? 'import_event_registrations';
        $data['group'] = $group;

        switch ($group) {
            case 'import_event_registration':
                $data['group_content'] = $this->load->view('settings/import_event_registrations', $data, true);
                break;
            default:
                $data['group_content'] = $this->load->view('settings/import_events_registrations', [], true);
                break;
        }

        if ($this->router->fetch_method() == 'main' && !$this->input->is_ajax_request()) {
            $this->load->view('settings/main', $data);
        }
    }


    public function upload_excel()
    {
        if (!isset($_FILES['csv_file']['name']) || empty($_FILES['csv_file']['name'])) {
            set_alert('danger', 'Please select a file to upload.');
            redirect(admin_url('events_due/settings'));
            return;
        }

        $file_ext = pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION);
        $data = [];

        if ($file_ext == 'csv') {

            // Handle CSV file
            $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
            fgetcsv($file); // Skip headers

            while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
                // Prepare event data
                $eventNameId = $this->Event_name_model->getOrCreateEventId($row[0] ?? '');
                $location_id = $this->Event_location_model->getOrCreateLocationId($row[6] ?? '');
                $venue_id = $this->Event_venue_model->getOrCreateVenueId($row[7] ?? '');

                $eventData = [
                    'event_name_id' => $eventNameId,
                    'setup' => $row[1] ?? '',
                    'type' => $row[2] ?? '',
                    'division' => $row[3] ?? '',
                    'start_date' => date('Y-m-d', strtotime($row[4] ?? '')),
                    'end_date' => date('Y-m-d', strtotime($row[5] ?? '')),
                    'location_id' => $location_id,
                    'venue_id' => $venue_id,
                ];

                // Check if event exists
                $existingEvent = $this->db->where($eventData)->get(db_prefix() . 'events_due_events')->row();
                $event_id = $existingEvent ? $existingEvent->id : $this->Event_model->create($eventData);

                // Prepare client data
                $customer_data = [
                    'full_name' => $row[8] ?? '',
                    'email' => $row[11] ?? '',
                    'phone_number' => $row[10] ?? '',
                    'organization_name' => $row[12] ?? '',
                ];

                // Check if client exists
                $existing_client_id = $this->Client_model->get_client_by_email($row[11]);
                $client_id = $existing_client_id ?: $this->Client_model->create($customer_data);

                // Register client to event
                $data[] = [
                    'event_id' => $event_id,
                    'client_id' => $client_id,
                ];
            }
            fclose($file);

        } elseif (in_array($file_ext, ['xls', 'xlsx'])) {
            // Handle Excel file
            $spreadsheet = IOFactory::load($_FILES['csv_file']['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();

            foreach ($worksheet->getRowIterator(2) as $row) {
                $cells = [];
                foreach ($row->getCellIterator() as $cell) {
                    $cells[] = trim($cell->getValue());
                }

                $eventNameId = $this->Event_name_model->getOrCreateEventId($cells[0] ?? '');
                $location_id = $this->Event_location_model->getOrCreateLocationId($cells[6] ?? '');
                $venue_id = $this->Event_venue_model->getOrCreateVenueId($cells[7] ?? '');

                $eventData = [
                    'event_name_id' => $eventNameId,
                    'setup' => $cells[1] ?? '',
                    'type' => $cells[2] ?? '',
                    'division' => $cells[3] ?? '',
                    'start_date' => date('Y-m-d', strtotime($cells[4] ?? '')),
                    'end_date' => date('Y-m-d', strtotime($cells[5] ?? '')),
                    'location_id' => $location_id,
                    'venue_id' => $venue_id,
                ];

                $existingEvent = $this->db->where($eventData)->get(db_prefix() . 'events_due_events')->row();
                $event_id = $existingEvent ? $existingEvent->id : $this->Event_model->create($eventData);

                $customer_data = [
                    'full_name' => $cells[8] ?? '',
                    'email' => $cells[11] ?? '',
                    'phone_number' => $cells[10] ?? '',
                    'organization_name' => $cells[12] ?? '',
                ];

                $existing_client_id = $this->Client_model->get_client_by_email($cells[11]);
                $client_id = $existing_client_id ?: $this->Client_model->create($customer_data);

                $data[] = [
                    'event_id' => $event_id,
                    'client_id' => $client_id,
                ];
            }
        } else {
            set_alert('danger', 'Invalid file format. Upload a CSV or Excel file.');
            redirect(admin_url('events_due/settings'));
            return;
        }

        // Batch insert after processing all rows
        if (!empty($data)) {

            $this->db->insert_batch(db_prefix() . 'events_due_registrations', $data);

            set_alert('success', 'Events Registration imported successfully.');
        } else {
            set_alert('danger', 'No valid data found in the file.');
        }

        redirect(admin_url('events_due/settings/main'));
    }


    public function download_sample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'Event Name', 'Setup', 'Type', 'Division', 'Start Date', 'End Date', 'Location',
            'Venue', 'Name of Delegate', 'Date & Month of Birth', 'Mobile No', 'Email Address', 'Organization'
        ];

        // Add headers to first row
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Add sample data
        $sampleData = [
            'Sample Event', 'Conference', 'Workshop', 'HR', '2025-01-01', '2025-01-03',
            'Nairobi', 'KICC', 'John Doe', '01 Jan 1990', '1234567890', 'john@example.com', 'XYZ Ltd'
        ];

        $col = 'A';
        foreach ($sampleData as $data) {
            $sheet->setCellValue($col . '2', $data);
            $col++;
        }

        // Generate and download file
        $filename = 'sample_event_registration.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }


}