<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/RestController.php';

use FlutexAdminApi\RestController;

class Payments extends RestController
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

        if (staff_cant('view', 'payments', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
    }
    
    public function payments_get()
    {
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
        
        $paymentID = $this->get('id');
        
        $this->db->select('*,' . db_prefix() . 'invoicepaymentrecords.id as paymentid');
        $this->db->join(db_prefix() . 'payment_modes', db_prefix() . 'payment_modes.id = ' . db_prefix() . 'invoicepaymentrecords.paymentmode', 'left');
        $this->db->order_by(db_prefix() . 'invoicepaymentrecords.id', 'asc');
        
        if (!empty($paymentID)) {
			$this->db->where(db_prefix() . 'invoicepaymentrecords.id', $paymentID);
			$paymentData = $this->db->get(db_prefix() . 'invoicepaymentrecords')->row();
		} else {
			$paymentData = $this->db->get(db_prefix() . 'invoicepaymentrecords')->result();
		}
		
        // Since version 1.0.1
        $this->load->model('payment_modes_model');
        $payment_gateways = $this->payment_modes_model->get_payment_gateways(true);
        if (is_null($paymentData->id)) {
            foreach ($payment_gateways as $gateway) {
                if ($paymentData->paymentmode == $gateway['id']) {
                    $paymentData->name = $gateway['name'];
                }
            }
        }
        
        if (!empty($paymentData)) {
            $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $paymentData], RestController::HTTP_OK);
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
                $where .= '(' . db_prefix() . 'invoicepaymentrecords.id LIKE "' . $keySearch . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'invoicepaymentrecords.invoiceid LIKE "%' . $keySearch . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'invoicepaymentrecords.transactionid LIKE "%' . $keySearch . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'invoicepaymentrecords.paymentmode LIKE "%' . $keySearch . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'invoicepaymentrecords.amount LIKE "%' . $keySearch . '%" ESCAPE \'!\')';
            }
            $this->db->where($where);
            $paymentData = $this->db->get(db_prefix() . 'invoicepaymentrecords')->result_array();
            
            if (!empty($paymentData)) {
                $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $paymentData], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function payments_post()
    {
        if (staff_cant('create', 'payments', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
            $this->form_validation->set_rules('invoiceid', 'Invoice Id', 'required|greater_than[0]');
            $this->form_validation->set_rules('date', 'Payment Date', 'required|max_length[255]');
            $this->form_validation->set_rules('paymentmode', 'Payment Mode', 'trim|required');
            $this->form_validation->set_rules('amount', 'Amount Received', 'required|max_length[255]');
            
            if (!$this->form_validation->run()) {
                $this->response(['message' => strip_tags(validation_errors()),'error' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
            } else {
                $data = [
                    'invoiceid' =>$this->input->post('invoiceid'),
                    'date' => $this->input->post('date'),
                    'amount' => $this->input->post('amount'),
                    'paymentmode' => $this->input->post('paymentmode'),
                    'note' => $this->input->post('note') ?? '',
                ];
                
                $this->load->model('payments_model');
                $success = $this->payments_model->add($data);
                if ($success) {
                    $this->response(['message' => _l('payment_added_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('payment_add_failed')], RestController::HTTP_NOT_FOUND);
                }
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function payments_put()
    {
        if (staff_cant('edit', 'payments', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
            
            if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_BAD_REQUEST);
            }
            
            $paymentID = $this->get('id');
            $this->load->model('payments_model');
            $payment = $this->payments_model->get($paymentID);
            
            if (is_object($payment)) {
                $data = array();
                parse_str(file_get_contents('php://input'), $data);
                $success = $this->payments_model->update($data, $paymentID);
                if ($success) {
                    $this->response(['message' => _l('payment_updated_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('payment_update_failed')], RestController::HTTP_NOT_FOUND);
                }
            } else {
                $this->response(['message' => _l('invalid_payment_id')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function payments_delete()
    {
        $paymentID = $this->get('id');
        
        if (staff_cant('delete', 'payments', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        $this->load->model('payments_model');
        $payment = $this->payments_model->get($paymentID);
        if (is_object($payment)) {
            $output = $this->payments_model->delete($paymentID);
            if ($output === TRUE) {
                $this->response(['message' => _l('payment_deleted_successfully')], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('payment_delete_failed')], RestController::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['message' => _l('invalid_payment_id')], RestController::HTTP_NOT_FOUND);
        }
    }
}