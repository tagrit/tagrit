<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/RestController.php';

use FlutexAdminApi\RestController;

class Invoices extends RestController
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

        if (staff_cant('view', 'invoices', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
    }
    
    public function invoices_get()
    {
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
        
        $invoiceID = $this->get('id');
        
        $this->load->model('invoices_model');
        
        $invoiceData = $this->invoices_model->get($invoiceID);
        
        if (!empty($invoiceData) && !empty($invoiceID)) {
            $invoiceData->allowed_payment_modes = convertSerializeDataToObject($invoiceData->allowed_payment_modes);
            $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $invoiceData], RestController::HTTP_OK);
        }
        
        $invoices_summary = $this->invoices_summary();
        
        foreach ($invoiceData as $key => $invoice) {
            $invoiceData[$key]['client_name'] = get_client($invoice['clientid'])->company;
        }
        
        if (!empty($invoiceData)) {
            $this->response(['message' => _l('data_retrieved_successfully'), 'overview' => $invoices_summary, 'data' => $invoiceData], RestController::HTTP_OK);
        } else {
            $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
        }
    }
    
    public function invoices_summary()
    {
        $staffID = $this->staffInfo['data']->staff_id;
        // Invoices Overview
        $invoices = [];
        $this->load->model('invoices_model');
        $invoice_statuses = $this->invoices_model->get_statuses();
        foreach ($invoice_statuses as $status) {
            $percent_data = get_invoices_percent_by_status_api($status,$staffID);
            array_push($invoices, [
                'status' => format_invoice_status($status, '', false),
                'total' => strval($percent_data['total_by_status']),
                'percent' => strval($percent_data['percent'])
            ]);
        }
        return $invoices;
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
            
            $this->load->model('invoices_model');
            
            $invoiceData = $this->invoices_model->get('', $where);
            
            if (!empty($invoiceData)) {
                $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $invoiceData], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function invoices_post()
    {
        if (staff_cant('create', 'invoices', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
    		$this->form_validation->set_rules('clientid', 'Customer Id', 'required|greater_than[0]');
            $this->form_validation->set_rules('number', 'Invoice number', 'required|max_length[255]');
            $this->form_validation->set_rules('date', 'Invoice date', 'required|max_length[255]');
            $this->form_validation->set_rules('currency', 'Currency', 'required|max_length[255]');
            $this->form_validation->set_rules('newitems[]', 'Items', 'required');
            $this->form_validation->set_rules('allowed_payment_modes[]', 'Allowed Payment Mode', 'required|max_length[255]');
            $this->form_validation->set_rules('billing_street', 'Billing Street', 'required|max_length[255]');
            $this->form_validation->set_rules('subtotal', 'Subtotal', 'required|decimal|greater_than[0]');
            $this->form_validation->set_rules('total', 'Total', 'required|decimal|greater_than[0]');
    		
            if (!$this->form_validation->run()) {
                $this->response(['message' => strip_tags(validation_errors()),'error' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
            } else {
                $data = $this->input->post();
                
                $this->load->model('invoices_model');
                $success = $this->invoices_model->add($data);
                if ($success) {
                    $this->response(['message' => _l('invoice_added_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('invoice_add_failed')], RestController::HTTP_NOT_FOUND);
                }
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function invoices_put()
    {
        if (staff_cant('edit', 'invoices', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
    
        try {
            
            if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_BAD_REQUEST);
            }
            
            $invoiceID = $this->get('id');
            $this->load->model('invoices_model');
            $invoice = $this->invoices_model->get($invoiceID);
            
            if (is_object($invoice)) {
                $data = array();
                parse_str(file_get_contents('php://input'), $data);
                $success = $this->invoices_model->update($data, $invoiceID);
                if ($success) {
                    $this->response(['message' => _l('invoice_updated_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('invoice_update_failed')], RestController::HTTP_NOT_FOUND);
                }
            } else {
                $this->response(['message' => _l('invalid_invoice_id')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function invoices_delete()
    {
        $invoiceID = $this->get('id');
        
        if (staff_cant('delete', 'invoices', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        $this->load->model('invoices_model');
        $invoice = $this->invoices_model->get($invoiceID);
        if (is_object($invoice)) {
            $output = $this->invoices_model->delete($invoiceID);
            if ($output === TRUE) {
                $this->response(['message' => _l('invoice_deleted_successfully')], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('invoice_delete_failed')], RestController::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['message' => _l('invalid_invoice_id')], RestController::HTTP_NOT_FOUND);
        }
    }
}