<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/RestController.php';

use FlutexAdminApi\RestController;

class Customers extends RestController
{
    protected $staffInfo;

    public function __construct()
    {
        parent::__construct();
        register_language_files('flutex_admin_api');
        load_admin_language();
        
        $this->load->helper('flutex_admin_api');
        if (!isset(isAuthorized()['status'])) {
            $this->response(isAuthorized()['response'], isAuthorized()['response_code']);
        }

        $this->staffInfo = isAuthorized();

        if (staff_cant('view', 'customers', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
    }
    
    public function customers_get()
    {
        try {
            
            if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_BAD_REQUEST);
            }
            
            $customerID = $this->get('id');
            
            $this->load->model('Clients_model');
            
            $customerData = $this->Clients_model->get($customerID);
            
            
            if(!empty($customerData) && !empty($customerID)){
                $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $customerData], RestController::HTTP_OK);
            }
            
            $customerSummery = $this->customers_summary();
            
            if (!empty($customerData)) {
                $this->response(['message' => _l('data_retrieved_successfully'), 'overview' => $customerSummery, 'data' => $customerData], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
        
    }
    
    public function customers_summary()
    {
        return [
            'customers_total' => total_rows(db_prefix() . 'clients'),
            'customers_active' => total_rows(db_prefix() . 'clients', 'active=1'),
            'customers_inactive' => total_rows(db_prefix() . 'clients', 'active=0'),
            'contacts_active' => total_rows(db_prefix() . 'contacts', 'active=1'),
            'contacts_inactive' => total_rows(db_prefix() . 'contacts', 'active=0'),
            'contacts_last_login' => total_rows(db_prefix() . 'contacts', 'last_login LIKE "' . date('Y-m-d') . '%"')
        ];
    }
    
    public function search_get()
    {
        try {
            
            if (!empty($this->get()) && !in_array('search', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_BAD_REQUEST);
            }
            
            $keySearch = $this->get('search');
            
            $where = '';

            if ($keySearch) {
                $keySearch = trim(urldecode($keySearch));
                $where .= '(company LIKE "%' . $keySearch . '%" OR CONCAT(firstname, " ", lastname) LIKE "%' . $keySearch . '%" OR email LIKE "%' . $keySearch . '%")';
            }
            
            $this->load->model('Clients_model');
            
            $customerData = $this->Clients_model->get('', $where);
            
            if (!empty($customerData)) {
                $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $customerData], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function customers_post()
    {
        if (staff_cant('create', 'customers', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
            $this->form_validation->set_rules('company', 'Company', 'required|max_length[600]');
            if (!$this->form_validation->run()) {
                $this->response(['message' => strip_tags(validation_errors()),'error' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
            } else {
                $data = [
                    'company' =>$this->input->post('company'),
                    'vat' => $this->input->post('vat') ?? '',
                    'phonenumber' => $this->input->post('phonenumber') ?? '',
                    'website' => $this->input->post('website') ?? '',
                    'default_currency' => $this->input->post('default_currency') ?? '',
                    'default_language' => $this->input->post('default_language') ?? '',
                    'address' => $this->input->post('address') ?? '',
                    'city' => $this->input->post('city') ?? '',
                    'state' => $this->input->post('state') ?? '',
                    'zip' => $this->input->post('zip') ?? '',
                    'country' => $this->input->post('country') ?? '',
                    'groups_in' => $this->input->post('groups_in'),
                    'billing_street' => $this->input->post('billing_street') ?? '',
                    'billing_city' => $this->input->post('billing_city') ?? '',
                    'billing_state' => $this->input->post('billing_state') ?? '',
                    'billing_zip' => $this->input->post('billing_zip') ?? '',
                    'billing_country' => $this->input->post('billing_country') ?? '',
                    'shipping_street' => $this->input->post('shipping_street') ?? '',
                    'shipping_city' => $this->input->post('shipping_city') ?? '',
                    'shipping_state' => $this->input->post('shipping_state') ?? '',
                    'shipping_zip' => $this->input->post('shipping_zip') ?? '',
                    'shipping_country' => $this->input->post('shipping_country') ?? '',
                ];
                
                $this->load->model('clients_model');
                $success = $this->clients_model->add($data);
                if ($success) {
                    $this->response(['message' => _l('customer_added_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('customer_add_failed')], RestController::HTTP_NOT_FOUND);
                }
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function customers_put()
    {
        if (staff_cant('edit', 'customers', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
            
            if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_BAD_REQUEST);
            }
            
            $customerID = $this->get('id');
            $this->load->model('clients_model');
            $customer = $this->clients_model->get($customerID);
            
            if (is_object($customer)) {
                $data = array();
                parse_str(file_get_contents('php://input'), $data);
                $success = $this->clients_model->update($data, $customerID);
                if ($success) {
                    $this->response(['message' => _l('customer_updated_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('customer_update_failed')], RestController::HTTP_NOT_FOUND);
                }
            } else {
                $this->response(['message' => _l('invalid_customer_id')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function customers_delete()
    {
        $customerID = $this->get('id');
        
        if (staff_cant('delete', 'customers', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        $this->load->model('Clients_model');
        $customer = $this->Clients_model->get($customerID);
        if (is_object($customer)) {
            $success = $this->Clients_model->delete($customerID);
            if ($success) {
                $this->response(['message' => _l('customer_deleted_successfully')], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('customer_delete_failed')], RestController::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['message' => _l('invalid_customer_id')], RestController::HTTP_NOT_FOUND);
        }
    }
    
    public function contacts_get()
    {
        try {
            
            if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_BAD_REQUEST);
            }
            
            $customerID = $this->get('id');
            $contactID = $this->get('contact');
            
            $this->load->model('Clients_model');
            
            if (!empty($customerID) && empty($contactID)) {
                $data = $this->Clients_model->get_contacts($customerID);
                $contactData = [];
                foreach ($data as $contact) {
                    $contactData[] = array(
                        'id' => $contact['id'],
                        'userid' => $contact['userid'],
                        'is_primary' => $contact['is_primary'],
                        'firstname' => $contact['firstname'],
                        'lastname' => $contact['lastname'],
                        'email' => $contact['email'],
                        'phonenumber' => $contact['phonenumber'],
                        'title' => $contact['title'],
                        'active' => $contact['active'],
                        'profile_image' => contact_profile_image_url($contact['id']));
                }
            }
            
            if (!empty($customerID) && !empty($contactID)) {
                $data = $this->Clients_model->get_contact($contactID);
                $contactData = array(
                    'id' => $data->id,
                    'userid' => $data->userid,
                    'is_primary' => $data->is_primary,
                    'firstname' => $data->firstname,
                    'lastname' => $data->lastname,
                    'email' => $data->email,
                    'phonenumber' => $data->phonenumber,
                    'title' => $data->title,
                    'active' => $data->active,
                    'profile_image' => contact_profile_image_url($data->id));
            }
            
            if (!empty($data)) {
                $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $contactData], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
        
    }
    
    public function contacts_post()
    {
        if (staff_cant('create', 'customers', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_BAD_REQUEST);
        }
            
        $customerID = $this->get('id');
        $this->load->model('Clients_model');
        $customer = $this->Clients_model->get($customerID);
        if (is_object($customer)) {
            try {
                $this->form_validation->set_rules('firstname', 'First Name', 'required|max_length[255]');
                $this->form_validation->set_rules('lastname', 'Last Name', 'required|max_length[255]');
                $this->form_validation->set_rules('email', 'Email', 'required|max_length[255]|is_unique['.db_prefix().'contacts.email]', array('is_unique' => _l('email_already_exist')));
                $this->form_validation->set_rules('password', 'Password', 'required|max_length[255]');
                
                if (!$this->form_validation->run()) {
                    $this->response(['message' => strip_tags(validation_errors()),'error' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
                } else {
                    $data = [
                        'firstname' =>$this->input->post('firstname'),
                        'lastname' => $this->input->post('lastname'),
                        'email' => $this->input->post('email'),
                        'password' => $this->input->post('password'),
                        'phonenumber' => $this->input->post('phonenumber') ?? '',
                        'title' => $this->input->post('title') ?? '',
                    ];
                    
                    $this->load->model('clients_model');
                    $success = $this->clients_model->add_contact($data, $customerID);
                    if ($success) {
                        $this->response(['message' => _l('contact_added_successfully')], RestController::HTTP_OK);
                    } else {
                        $this->response(['message' => _l('contact_add_failed')], RestController::HTTP_NOT_FOUND);
                    }
                }
                
            } catch (\Throwable $th) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
            }
        } else {
            $this->response(['message' => _l('invalid_customer_id')], RestController::HTTP_NOT_FOUND);
        }
    }
    
    public function contacts_delete()
    {
        $contactID = $this->get('id');
        
        if (staff_cant('delete', 'customers', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        $this->load->model('Clients_model');
        $contact = $this->Clients_model->get_contact($contactID);
        if (is_object($contact)) {
            $success = $this->Clients_model->delete_contact($contactID);
            if ($success) {
                $this->response(['message' => _l('contact_deleted_successfully')], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('contact_delete_failed')], RestController::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['message' => _l('invalid_contact_id')], RestController::HTTP_NOT_FOUND);
        }
    }
}