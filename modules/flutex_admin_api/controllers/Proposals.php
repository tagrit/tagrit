<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/RestController.php';

use FlutexAdminApi\RestController;

class Proposals extends RestController
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

        if (staff_cant('view', 'proposals', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
    }
    
    public function proposals_get()
    {
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
        
        $proposalID = $this->get('id');
        
        $this->load->model('proposals_model');
        
        $proposalData = $this->proposals_model->get($proposalID);
        
        if (!empty($proposalData) && !empty($proposalID)) {
            $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $proposalData], RestController::HTTP_OK);
        }
        
        $proposals_summary = $this->proposals_summary();
        
        if (!empty($proposalData)) {
            $this->response(['message' => _l('data_retrieved_successfully'), 'overview' => $proposals_summary, 'data' => $proposalData], RestController::HTTP_OK);
        } else {
            $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
        }
    }

    public function proposals_summary()
    {
        $staffID = $this->staffInfo['data']->staff_id;
        // Proposals Overview
        $proposals = [];
        $this->load->model('proposals_model');
        $proposal_statuses = $this->proposals_model->get_statuses();
        
        foreach ($proposal_statuses as $status) {
            $percent_data = get_proposals_percent_by_status_api($status,$staffID);
            array_push($proposals, [
                'status' => format_proposal_status($status, '', false),
                'total' => strval($percent_data['total_by_status']),
                'percent' => strval($percent_data['percent'])
            ]);
        }
        return $proposals;
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
                $where .= '(' . db_prefix() . 'proposals.id LIKE "' . $keySearch . '%"
                    OR ' . db_prefix() . 'proposals.subject LIKE "%' . $keySearch . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'proposals.content LIKE "%' . $keySearch . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'proposals.proposal_to LIKE "%' . $keySearch . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'proposals.zip LIKE "%' . $keySearch . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'proposals.state LIKE "%' . $keySearch . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'proposals.city LIKE "%' . $keySearch . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'proposals.address LIKE "%' . $keySearch . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'proposals.email LIKE "%' . $keySearch . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'proposals.phone LIKE "%' . $keySearch . '%" ESCAPE \'!\')';
            }
            
            $this->load->model('proposals_model');
            
            $proposalData = $this->proposals_model->get('', $where);
            
            if (!empty($proposalData)) {
                $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $proposalData], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function proposals_post()
    {
        if (staff_cant('create', 'proposals', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
    		$this->form_validation->set_rules('subject', 'Subject', 'required|max_length[191]');
            $this->form_validation->set_rules('rel_type', 'Rel Type', 'required|in_list[lead,customer]');
            $this->form_validation->set_rules('rel_id', 'Rel Id', 'required|greater_than[0]');
            $this->form_validation->set_rules('date', 'date', 'required|max_length[255]');
            $this->form_validation->set_rules('currency', 'Currency', 'required|max_length[255]');
            $this->form_validation->set_rules('status', 'Status', 'required|max_length[255]');
            $this->form_validation->set_rules('proposal_to', 'Proposal to', 'required|max_length[191]');
            $this->form_validation->set_rules('email', 'Email', 'valid_email|required|max_length[150]');
            $this->form_validation->set_rules('newitems[]', 'Items', 'required');
            $this->form_validation->set_rules('subtotal', 'Sub Total', 'required|decimal|greater_than[0]');
            $this->form_validation->set_rules('total', 'Total', 'required|decimal|greater_than[0]');
    		
            if (!$this->form_validation->run()) {
                $this->response(['message' => strip_tags(validation_errors()),'error' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
            } else {
                $data = $this->input->post();
                
                $this->load->model('proposals_model');
                $success = $this->proposals_model->add($data);
                if ($success) {
                    $this->response(['message' => _l('proposal_added_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('proposal_add_failed')], RestController::HTTP_NOT_FOUND);
                }
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function proposals_put()
    {
        if (staff_cant('edit', 'proposals', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
            
            if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_BAD_REQUEST);
            }
            
            $proposalID = $this->get('id');
            $this->load->model('proposals_model');
            $proposal = $this->proposals_model->get($proposalID);
            
            if (is_object($proposal)) {
                $data = array();
                parse_str(file_get_contents('php://input'), $data);
                $success = $this->proposals_model->update($data, $proposalID);
                if ($success) {
                    $this->response(['message' => _l('proposal_updated_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('proposal_update_failed')], RestController::HTTP_NOT_FOUND);
                }
            } else {
                $this->response(['message' => _l('invalid_proposal_id')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function proposals_delete()
    {
        $proposalID = $this->get('id');
        
        if (staff_cant('delete', 'proposals', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        $this->load->model('proposals_model');
        $proposal = $this->proposals_model->get($proposalID);
        if (is_object($proposal)) {
            $output = $this->proposals_model->delete($proposalID);
            if ($output === TRUE) {
                $this->response(['message' => _l('proposal_deleted_successfully')], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('proposal_delete_failed')], RestController::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['message' => _l('invalid_proposal_id')], RestController::HTTP_NOT_FOUND);
        }
    }
}