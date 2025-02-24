<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/RestController.php';

use FlutexAdminApi\RestController;

class Contracts extends RestController
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

        if (staff_cant('view', 'contracts', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
    }
    
    public function contracts_get()
    {
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
        
        $contractID = $this->get('id');
        
        $this->load->model('contracts_model');
        
        $contractData = $this->contracts_model->get($contractID);
        
        if (!empty($contractData)) {
            $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $contractData], RestController::HTTP_OK);
        } else {
            $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
        }
    }
    
    public function search_get()
    {
        try {
            
            if (!empty($this->get()) && !in_array('search', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
            }
            
            $keySearch = $this->get('search');
            
            $where = '';
            
            if ($keySearch) {
                $keySearch = trim(urldecode($keySearch));
                $keySearch = $this->db->escape_like_str($keySearch);
                $where .= '(content LIKE "%' . $keySearch . '%" OR description LIKE "%' . $keySearch . '%" ESCAPE \'!\' OR subject LIKE "%' . $keySearch . '%" ESCAPE \'!\' OR contract_value LIKE "%' . $keySearch . '%" ESCAPE \'!\')';
            }
            
            $this->load->model('contracts_model');
            
            $contractData = $this->contracts_model->get('', $where);
            
            if (!empty($contractData)) {
                $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $contractData], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function contracts_post()
    {
        if (staff_cant('create', 'contracts', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
    		$this->form_validation->set_rules('client', 'Customer Id', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules('subject', 'Subject', 'trim|required|max_length[191]');
    		$this->form_validation->set_rules('datestart', 'Start date', 'trim|required|max_length[255]');
    		
            if (!$this->form_validation->run()) {
                $this->response(['message' => strip_tags(validation_errors()),'error' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
            } else {
                $data = [
                    'client' => $this->input->post('client'),
                    'subject' =>$this->input->post('subject'),
                    'datestart' => $this->input->post('datestart'),
                    'dateend' => $this->input->post('dateend'),
                    'contract_value' => $this->input->post('contract_value'),
                    'contract_type' => $this->input->post('contract_type'),
                    'description' => $this->input->post('description'),
                    'content' => $this->input->post('content'),
                ];
                
                $this->load->model('contracts_model');
                $success = $this->contracts_model->add($data);
                if ($success) {
                    $this->response(['message' => _l('contract_added_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('contract_add_failed')], RestController::HTTP_NOT_FOUND);
                }
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function contracts_put()
    {
        if (staff_cant('edit', 'contracts', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
            
            if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_BAD_REQUEST);
            }
            
            $contractID = $this->get('id');
            $this->load->model('contracts_model');
            $contract = $this->contracts_model->get($contractID);
            
            if (is_object($contract)) {
                $data = array();
                parse_str(file_get_contents('php://input'), $data);
                $success = $this->contracts_model->update($data, $contractID);
                if ($success) {
                    $this->response(['message' => _l('contract_updated_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('contract_update_failed')], RestController::HTTP_NOT_FOUND);
                }
            } else {
                $this->response(['message' => _l('invalid_contract_id')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function contracts_delete()
    {
        $contractID = $this->get('id');
        
        if (staff_cant('delete', 'contracts', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        $this->load->model('contracts_model');
        $contract = $this->contracts_model->get($contractID);
        if (is_object($contract)) {
            $output = $this->contracts_model->delete($contractID);
            if ($output === TRUE) {
                $this->response(['message' => _l('contract_deleted_successfully')], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('contract_delete_failed')], RestController::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['message' => _l('invalid_contract_id')], RestController::HTTP_NOT_FOUND);
        }
    }
}