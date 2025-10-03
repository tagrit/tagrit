<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Companies extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('courier/courier'); // Load the helper specific to the courier module
        $this->load->model('CourierCompany_model');
        $this->load->model('ContactPerson_model');
        $this->load->library('form_validation');

    }

    public function main()
    {

        $group = $this->input->get('group', true) ?? 'dashboard';

        $types = ['internal', 'third_party'];
        $company_counts = [];

        foreach ($types as $type) {
            $company_counts[$type] = $this->CourierCompany_model->get_company_count_by_type($type);
        }

        $data['type_counts'] = $company_counts;
        $data['group'] = $group;

        switch ($group) {
            case 'dashboard':
                $data['title'] = _l('Dashboard');
                $data['group_content'] = $this->load->view('companies/dashboard', $data, true);
                break;

            case 'create_company':
                $data['title'] = _l('Create Company');
                $data['group_content'] = $this->load->view('companies/create', $data, true);
                break;

            case 'list_companies':
                $data['companies'] = $this->CourierCompany_model->get();
                $data['title'] = _l('List Companies');
                $data['group_content'] = $this->load->view('companies/index', $data, true);
                break;

            default:
                $data['group_content'] = $this->load->view('dashboard', [], true);
                break;
        }

        if ($this->router->fetch_method() == 'main' && !$this->input->is_ajax_request()) {
            $this->load->view('companies/main', $data);
        }

    }

    public function dashboard()
    {
        $this->load->view('companies/dashboard');
    }

    public function index()
    {
        $data['companies'] = $this->CourierCompany_model->get();
        $this->load->view('companies/index', $data);
    }

    public function create()
    {
        $this->load->view('companies/create');
    }

    private function set_validation_rules()
    {
        // Set validation rules for company data
        $this->form_validation->set_rules('name', 'Company Name', 'required');
        $this->form_validation->set_rules('type', 'Company Type', 'required');

        // Set validation rules for contact person data
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required|numeric');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
    }

    public function store()
    {
        $this->set_validation_rules(); // Set validation rules

        if ($this->form_validation->run() == FALSE) {

            // Validation failed; set flashdata and redirect
            foreach ($this->input->post() as $key => $value) {
                $this->session->set_flashdata($key . '_error', form_error($key));
            }

            redirect('admin/courier/companies/main?group=create_company');
        } else {
            // Validation passed; proceed with data processing

            //store company data
            $company_data = [
                'company_name' => $this->input->post('name'),
                'prefix' => strtoupper(substr($this->input->post('name'), 0, 4)),
                'type' => $this->input->post('type'),
            ];

            $company_id = $this->CourierCompany_model->add($company_data);

            if ($company_id === false) {
                set_alert('danger', 'Failed to add company.');
                redirect('admin/courier/companies/main?group=create_company');
            }

            //store contact person data
            $contact_person_data = [
                'company_id' => $company_id,
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'phone_number' => $this->input->post('phone_number'),
                'email' => $this->input->post('email')
            ];
            $contact_person_id = $this->ContactPerson_model->add($contact_person_data);

            set_alert('success', 'Company added successfully.');
            redirect('admin/courier/companies/main?group=list_companies');
        }
    }

    public function edit()
    {
        $this->load->view('companies/edit');
    }

    public function delete($id)
    {
        if (!$id) {
            set_alert('danger', 'Invalid company ID.');
            redirect('admin/courier/companies/main?group=list_companies');
        }

        // Start a transaction
        $this->db->trans_begin();

        // Get the company and its contact person
        $company = $this->CourierCompany_model->get_by_id($id);

        if (!$company) {
            set_alert('danger', 'Company not found.');
            $this->db->trans_rollback();
            redirect('admin/courier/companies/main?group=list_companies');
        }

        // Delete the company
        $deleted_company = $this->CourierCompany_model->delete($id);

        if ($deleted_company) {
            // Delete the associated contact person
            $deleted_contact = $this->ContactPerson_model->delete_by_company_id($id);

            if ($deleted_contact) {
                $this->db->trans_commit();
                set_alert('success', 'Company deleted successfully.');
            } else {
                $this->db->trans_rollback();
                set_alert('danger', 'Failed to delete the company contact person.');
            }
        } else {
            $this->db->trans_rollback();
            set_alert('danger', 'Failed to delete the company.');
        }

        redirect('admin/courier/companies/main?group=list_companies');
    }


}