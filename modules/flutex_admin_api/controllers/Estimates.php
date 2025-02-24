<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/RestController.php';

use FlutexAdminApi\RestController;

class Estimates extends RestController
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

        if (staff_cant('view', 'estimates', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
    }
    
    public function estimates_get()
    {
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
        
        $estimateID = $this->get('id');
        
        $this->load->model('estimates_model');
        
        $estimateData = $this->estimates_model->get($estimateID);
        
        if (!empty($estimateData) && !empty($estimateID)) {
            $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $estimateData], RestController::HTTP_OK);
        }
        
        $estimates_summary = $this->estimates_summary();
        
        if (!empty($estimateData)) {
            $this->response(['message' => _l('data_retrieved_successfully'), 'overview' => $estimates_summary, 'data' => $estimateData], RestController::HTTP_OK);
        } else {
            $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
        }
    }

    public function estimates_summary()
    {
        $staffID = $this->staffInfo['data']->staff_id;
        // Estimates Overview
        $estimates = [];
        $this->load->model('estimates_model');
        $estimate_statuses = $this->estimates_model->get_statuses();

        array_splice($estimate_statuses, 1, 0, 'not_sent');
        foreach ($estimate_statuses as $status) {
            $percent_data = get_estimates_percent_by_status_api($status,$staffID);
            array_push($estimates, [
                'status' => format_estimate_status($status, '', false),
                'total' => strval($percent_data['total_by_status']),
                'percent' => strval($percent_data['percent'])
            ]);
        }
        return $estimates;
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
                $where .= '(number LIKE "' . $keySearch . '" OR clientnote LIKE "%' . $keySearch . '%" ESCAPE \'!\' OR adminnote LIKE "%' . $keySearch . '%" ESCAPE \'!\')';
            }
            
            $this->load->model('estimates_model');
            
            $estimateData = $this->estimates_model->get('', $where);
            
            if (!empty($estimateData)) {
                $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $estimateData], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function estimates_post()
    {
        if (staff_cant('create', 'estimates', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
    		$this->form_validation->set_rules('clientid', 'Customer', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('number', 'Estimate Number', 'required|numeric');
            $this->form_validation->set_rules('date', 'Estimate date', 'required|max_length[255]');
            $this->form_validation->set_rules('currency', 'Currency', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('status', 'Status', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('newitems[]', 'Items', 'required');
            $this->form_validation->set_rules('subtotal', 'Sub Total', 'required|decimal|greater_than[0]');
            $this->form_validation->set_rules('total', 'Total', 'required|decimal|greater_than[0]');
    		
            if (!$this->form_validation->run()) {
                $this->response(['message' => strip_tags(validation_errors()),'error' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
            } else {
                $data = $this->input->post();
                
                $this->load->model('estimates_model');
                $success = $this->estimates_model->add($data);
                if ($success) {
                    $this->response(['message' => _l('estimate_added_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('estimate_add_failed')], RestController::HTTP_NOT_FOUND);
                }
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function estimates_put()
    {
        if (staff_cant('edit', 'estimates', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
            
            if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_BAD_REQUEST);
            }
            
            $estimateID = $this->get('id');
            $this->load->model('estimates_model');
            $estimate = $this->estimates_model->get($estimateID);
            
            if (is_object($estimate)) {
                $data = array();
                parse_str(file_get_contents('php://input'), $data);
                $success = $this->estimates_model->update($data, $estimateID);
                if ($success) {
                    $this->response(['message' => _l('estimate_updated_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('estimate_update_failed')], RestController::HTTP_NOT_FOUND);
                }
            } else {
                $this->response(['message' => _l('invalid_estimate_id')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function estimates_delete()
    {
        $estimateID = $this->get('id');
        
        if (staff_cant('delete', 'estimates', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        $this->load->model('estimates_model');
        $estimate = $this->estimates_model->get($estimateID);
        if (is_object($estimate)) {
            $success = $this->estimates_model->delete($estimateID);
            if ($success) {
                $this->response(['message' => _l('estimate_deleted_successfully')], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('estimate_delete_failed')], RestController::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['message' => _l('invalid_estimate_id')], RestController::HTTP_NOT_FOUND);
        }
    }
}