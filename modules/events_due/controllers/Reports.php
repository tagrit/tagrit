<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reports extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Registration_model');

    }

    public function get_unique_organizations()
    {
        $this->db->distinct();
        $this->db->select('organization');
        $this->db->from(db_prefix() . '_events_details');
        $query = $this->db->get();
        return $query->result();
    }

    public function main()
    {
        $data['registrations'] = $this->Registration_model->get();
        $data['organizations'] = $this->get_unique_organizations();
        $this->load->view('reports/main', $data);
    }


    public function save_filters()
    {
        $filters = [
            'status' => $this->input->post('status'),
            'start_date' => $this->input->post('start_date'),
            'end_date' => $this->input->post('end_date'),
            'organization' => $this->input->post('organization'),
            'query' => $this->input->post('query')
        ];
        $this->session->set_userdata('event_filters', $filters);
        echo json_encode(['success' => true]);
    }

    public function get_filters()
    {
        $filters = $this->session->userdata('event_filters');
        echo json_encode($filters ? $filters : []);
    }

    public function clear_filters()
    {
        $this->session->unset_userdata('event_filters');
        echo json_encode(['success' => true]);
    }

    public function get_filtered_data()
    {
        // Get session filters
        $filters = $this->session->userdata('event_filters');

        // Always prioritize request data
        $status = $this->input->post('status') ?? ($filters['status'] ?? null);
        $start_date = $this->input->post('start_date') ?? ($filters['start_date'] ?? null);
        $end_date = $this->input->post('end_date') ?? ($filters['end_date'] ?? null);
        $organization = $this->input->post('organization') ?? ($filters['organization'] ?? null);
        $query = $this->input->post('query') ?? ($filters['query'] ?? null);

        // Update session with new filter values
        $new_filters = [
            'status' => $status,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'organization' => $organization,
            'query' => $query
        ];

        $this->session->set_userdata('event_filters', $new_filters);

        return $this->Registration_model->get_filtered_data($status, $start_date, $end_date, $organization, $query);
    }


    public function fetch_filtered_data()
    {

        $registrations = $this->get_filtered_data();

        foreach ($registrations as $registration) {
            echo '<tr>
            <td>
                <div class="d-flex flex-column justify-content-center">
                    <p style="font-weight: bold; font-size: 14px;">' . $registration->event_name . '</p>
                    <p style="color:#007BFF; font-weight: bold;" class="text-secondary mb-0">' .
                $registration->location . ' - ' . $registration->venue .
                '</p>
                </div>
            </td>
            <td>
                <p class="text-secondary mb-0">' . $registration->client_first_name . ' ' . $registration->client_last_name . '</p>
                <p class="text-secondary mb-0">' . $registration->client_phone . '</p>
                <p class="text-secondary mb-0">' . $registration->client_email . '</p>
            </td>
            <td>' . $registration->organization . '</td>
            <td>' . $registration->start_date . '</td>
            <td>' . $registration->end_date . '</td>
        </tr>';
        }
    }

    public function export_filtered_report()
    {
        $registrations = $this->get_filtered_data();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'Event Name', 'Setup', 'Type', 'Division', 'Start Date', 'End Date', 'Location',
            'Venue', 'Name of Delegate', 'Date & Month of Birth', 'Mobile No', 'Email Address', 'Organization'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true); // Make header bold
            $col++;
        }

        // Populate data
        $row = 2;
        foreach ($registrations as $registration) {
            $col = 'A';
            $data = [
                $registration->event_name,
                $registration->setup,
                $registration->type,
                $registration->division,
                $registration->start_date,
                $registration->end_date,
                $registration->location,
                $registration->venue,
                $registration->client_first_name . ' ' . $registration->client_last_name,
                'N/A',
                $registration->client_phone,
                $registration->client_email,
                $registration->organization
            ];

            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }

            $row++;
        }

        // Generate and download file
        $filename = 'event_registration_report.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

}