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
        $this->load->model('Event_details_model');
        $this->load->model('Registration_model');
        $this->load->model('Client_model');

    }

    public function main()
    {
        $group = $this->input->get('group', true) ?? 'import_events_registrations';
        $data['group'] = $group;

        switch ($group) {
            case 'import_events_registrations':
                $data['group_content'] = $this->load->view('settings/import_events_registrations', $data, true);
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
        $events_clients = []; // Stores all clients grouped by event_id

        $this->db->trans_begin(); // Start transaction

        try {
            if ($file_ext == 'csv') {
                // Handle CSV file
                $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
                fgetcsv($file); // Skip headers

                while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {

                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $event_data = [
                        'name' => $row[0] ?? ''
                    ];

                    $eventId = $this->Event_model->getOrCreateEventId($event_data);

                    $eventData = [
                        'event_id' => $eventId,
                        'setup' => $row[1] ?? '',
                        'type' => $row[2] ?? '',
                        'division' => $row[3] ?? '',
                        'start_date' => date('Y-m-d', strtotime($row[4] ?? '')),
                        'end_date' => date('Y-m-d', strtotime($row[5] ?? '')),
                        'location' => $row[6],
                        'venue' => $row[6],
                        'organization' => $row[11] ?? '',
                        'revenue' => '0',
                        'facilitator' => 'capabuil',
                        'no_of_delegates' => '1',
                        'charges_per_delegate' => '1',
                        'trainers' => serialize(['capabuil']),
                    ];

                    $existingEvent = $this->db->where($eventData)->get(db_prefix() . '_events_details')->row();
                    $event_detail_id = $existingEvent ? $existingEvent->id : $this->Event_details_model->add($eventData);

                    $nameParts = explode(' ', $row[7] ?? '', 2);

                    $customer_data = [
                        'first_name' => $nameParts[0] ?? '',
                        'last_name' => $nameParts[1] ?? '',
                        'email' => $row[10] ?? '',
                        'phone' => $row[9] ?? '',
                    ];

                    if (!isset($events_clients[$event_detail_id])) {
                        $events_clients[$event_detail_id] = [];
                    }
                    $events_clients[$event_detail_id][] = $customer_data;
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

                    // Skip row if all key fields are empty
                    if (empty($cells[0]) && empty($cells[7]) && empty($cells[9]) && empty($cells[10])) {
                        continue;
                    }

                    $event_data = [
                        'name' => $cells[0] ?? ''
                    ];

                    $eventId = $this->Event_model->getOrCreateEventId($event_data);

                    $eventData = [
                        'event_id' => $eventId,
                        'setup' => $cells[1] ?? '',
                        'type' => $cells[2] ?? '',
                        'division' => $cells[3] ?? '',
                        'start_date' => date('Y-m-d', strtotime($cells[4] ?? '')),
                        'end_date' => date('Y-m-d', strtotime($cells[5] ?? '')),
                        'location' => $cells[6],
                        'venue' => $cells[6],
                        'organization' => $cells[11] ?? '',
                        'revenue' => '0',
                        'facilitator' => 'capabuil',
                        'no_of_delegates' => '1',
                        'charges_per_delegate' => '1',
                        'trainers' => serialize(['capabuil']),
                    ];

                    $existingEvent = $this->db->where($eventData)->get(db_prefix() . '_events_details')->row();
                    $event_detail_id = $existingEvent ? $existingEvent->id : $this->Event_details_model->add($eventData);

                    $nameParts = explode(' ', $cells[7] ?? '', 2);

                    $customer_data = [
                        'first_name' => $nameParts[0] ?? '',
                        'last_name' => $nameParts[1] ?? '',
                        'email' => $cells[10] ?? '',
                        'phone' => $cells[9] ?? '',
                    ];

                    if (!isset($events_clients[$event_detail_id])) {
                        $events_clients[$event_detail_id] = [];
                    }
                    $events_clients[$event_detail_id][] = $customer_data;
                }
            } else {
                set_alert('danger', 'Invalid file format. Upload a CSV or Excel file.');
                redirect(admin_url('events_due/settings'));
                return;
            }


            foreach ($events_clients as $event_detail_id => $clients) {
                $data[] = [
                    'event_detail_id' => $event_detail_id,
                    'clients' => serialize($clients)
                ];
            }

            // Batch insert all records at once
            if (!empty($data)) {
                $this->db->insert_batch(db_prefix() . 'events_due_registrations', $data);
            } else {
                throw new Exception('No valid data found in the file.');
            }

            $this->db->trans_commit(); // Commit transaction
            set_alert('success', 'Events Registration imported successfully.');
        } catch (Exception $e) {
            $this->db->trans_rollback(); // Rollback transaction
            log_message('error', 'Upload failed: ' . $e->getMessage());
            set_alert('danger', 'Failed to import registrations. Please try again.');
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