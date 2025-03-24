<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Registration_model');

    }

    public function main()
    {
        $data['registrations'] = $this->Registration_model->get();
        $this->load->view('reports/main', $data);
    }


    public function fetch_filtered_data()
    {
        $status = $this->input->post('status');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $organization = $this->input->post('organization');

        // Call get_filtered_data() with filter parameters
        $registrations = $this->Registration_model->get_filtered_data($status, $start_date, $end_date, $organization);

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
            <td>
                <a style="color:white;" href="#" class="btn btn-info">
                    <i class="fa fa-eye"></i> View
                </a>
            </td>
        </tr>';
        }
    }

}