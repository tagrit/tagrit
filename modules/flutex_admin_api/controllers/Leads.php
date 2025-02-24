<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/RestController.php';

use FlutexAdminApi\RestController;

class Leads extends RestController
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

        if (staff_cant('view', 'leads', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
    }
    
    public function leads_get()
    {
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
        
        $leadID = $this->get('id');
        
        $this->load->model('leads_model');
        
        $leadData = $this->leads_model->get($leadID);
        
        if (!empty($leadData) && !empty($leadID)) {
            $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $leadData], RestController::HTTP_OK);
        }
        
        $leads_summary = $this->leads_summary();
        
        if (!empty($leadData)) {
            $this->response(['message' => _l('data_retrieved_successfully'), 'overview' => $leads_summary, 'data' => $leadData], RestController::HTTP_OK);
        } else {
            $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
        }
    }

    public function leads_summary()
    {
        // Leads Overview
        $leads = [];
        $this->load->model('leads_model');
        $leads_statuses = $this->leads_model->get_status();

        foreach ($leads_statuses as $key => $status) {
            $where = 'status = ' . $status['id'];
            array_push($leads, [
                'status' => $status['name'],
                'total' => strval(total_rows(db_prefix() . 'leads', $where)),
                'percent' => total_rows(db_prefix() . 'leads', $where) == 0 ? '0' : strval(total_rows(db_prefix() . 'leads', $where) / total_rows(db_prefix() . 'leads') * 100)
            ]);
        }
        return $leads;
    }
    
    public function search_get()
    {
            
            if (!empty($this->get()) && !in_array('search', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
            }
            
            $keySearch = $this->get('search');
            
            $where = '';
            
            if ($keySearch) {
                $keySearch = trim(urldecode($keySearch));
                $keySearch = $this->db->escape_like_str($keySearch);
                $where .= '(leads.name LIKE "%' . $keySearch . '%" OR title LIKE "%' . $keySearch . '%" OR company LIKE "%' . $keySearch . '%"
                    OR zip LIKE "%' . $keySearch . '%" OR city LIKE "%' . $keySearch . '%" OR state LIKE "%' . $keySearch . '%" OR leads.address LIKE "%' . $keySearch . '%"
                    OR leads.email LIKE "%' . $keySearch . '%" OR leads.phonenumber LIKE "%' . $keySearch . '%")';
            }
            
            $this->load->model('leads_model');
            
            $leadData = $this->leads_model->get('', $where);
            
            if (!empty($leadData)) {
                $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $leadData], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
    }
    
    public function leads_post()
    {
        try {
            
            $this->form_validation->set_rules('name', 'Lead Name', 'required|max_length[600]');
            $this->form_validation->set_rules('source', 'Source', 'required');
            $this->form_validation->set_rules('status', 'Status', 'required');
            
            if (!$this->form_validation->run()) {
                $this->response(['message' => strip_tags(validation_errors()),'error' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
            } else {
                $data = [
                    'name' => $this->input->post('name'),
                    'source' => $this->input->post('source'),
                    'status' => $this->input->post('status'),
                    'assigned' => $this->input->post('assigned'),
                    'lead_value' => $this->input->post('lead_value'),
                    'tags' => $this->input->post('tags')??'',
                    'title' => $this->input->post('title')??'',
                    'email' => $this->input->post('email')??'',
                    'website' => $this->input->post('website')??'',
                    'phonenumber' => $this->input->post('phonenumber')??'',
                    'company' => $this->input->post('company')??'',
                    'address' => $this->input->post('address')??'',
                    'city' => $this->input->post('city')??'',
                    'zip' => $this->input->post('zip')??'',
                    'state' => $this->input->post('state')??'',
                    'default_language' => $this->input->post('default_language')??'',
                    'description' => $this->input->post('description')??'',
                    'is_public' => $this->input->post('is_public')??''
                ];
                
                $this->load->model('leads_model');
                $success = $this->leads_model->add($data);
                if ($success) {
                    $this->response(['message' => _l('lead_added_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('lead_add_failed')], RestController::HTTP_NOT_FOUND);
                }
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function leads_put()
    {
        //try {
            
            if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
                $this->response(['message' => _l('hhhh')], RestController::HTTP_BAD_REQUEST);
            }
            
            $leadID = $this->get('id');
            $this->load->model('leads_model');
            $lead = $this->leads_model->get($leadID);
            
            if (is_object($lead)) {
                $data = array();
                parse_str(file_get_contents('php://input'), $data);
                $success = $this->leads_model->update($data, $leadID);
                if ($success) {
                    $this->response(['message' => _l('lead_updated_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('lead_update_failed')], RestController::HTTP_NOT_FOUND);
                }
            } else {
                $this->response(['message' => _l('invalid_lead_id')], RestController::HTTP_NOT_FOUND);
            }
            
        //} catch (\Throwable $th) {
        //    $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        //}
    }
    
    public function leads_delete()
    {
        
        $leadID = $this->get('id');
        
        if (staff_cant('delete', 'leads', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        $this->load->model('leads_model');
        $lead = $this->leads_model->get($leadID);
        if (is_object($lead)) {
            $output = $this->leads_model->delete($leadID);
            if ($output === TRUE) {
                $this->response(['message' => _l('lead_deleted_successfully')], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('lead_delete_failed')], RestController::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['message' => _l('invalid_lead_id')], RestController::HTTP_NOT_FOUND);
        }
    }
}