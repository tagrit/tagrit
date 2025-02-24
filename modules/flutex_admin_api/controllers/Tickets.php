<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/RestController.php';

use FlutexAdminApi\RestController;

class Tickets extends RestController
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

        if (!is_staff_member($this->staffInfo['data']->staff_id) && get_option('access_tickets_to_none_staff_members') == '0') {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
    }
    
    public function tickets_get()
    {
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
        
        try {
            $ticketID = $this->get('id');
        
            $this->load->model('tickets_model');
            
            $ticketData = $this->tickets_model->get($ticketID);
            
            if (!empty($ticketData) && !empty($ticketID)) {
                $ticketData->ticket_replies = $this->tickets_model->get_ticket_replies($ticketID);
                $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $ticketData], RestController::HTTP_OK);
            }
            
            $tickets_summary = $this->tickets_summary();
            
            if (!empty($ticketData)) {
                $this->response(['message' => _l('data_retrieved_successfully'), 'overview' => $tickets_summary, 'data' => $ticketData], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong ')], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    public function tickets_summary()
    {
        // Tickets Overview
        $tickets = [];
        $this->load->model('tickets_model');
        $tickets_statuses = $this->tickets_model->get_ticket_status();

        foreach ($tickets_statuses as $key => $status) {
            $where = 'status = ' . $status['ticketstatusid'];
            array_push($tickets, [
                'status' => $status['name'],
                'total' => strval(total_rows(db_prefix() . 'tickets', $where)),
                'percent' => total_rows(db_prefix() . 'tickets', $where) == 0 ? '0' : strval(total_rows(db_prefix() . 'tickets', $where) / total_rows(db_prefix() . 'tickets') * 100)
            ]);
        }

        return $tickets;
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
                $where .= '(ticketid LIKE "' . $keySearch . '%" OR subject LIKE "%' . $keySearch . '%" OR message LIKE "%' . $keySearch . '%"
                    OR ' . db_prefix() . 'contacts.email LIKE "%' . $keySearch . '%" OR CONCAT(' . db_prefix() . 'contacts.firstname, \' \', ' . db_prefix() . 'contacts.lastname) LIKE "%' . $keySearch . '%"
                    OR company LIKE "%' . $keySearch . '%" OR vat LIKE "%' . $keySearch . '%" OR ' . db_prefix() . 'contacts.phonenumber LIKE "%' . $keySearch . '%"
                    OR city LIKE "%' . $keySearch . '%" OR state LIKE "%' . $keySearch . '%" OR address LIKE "%' . $keySearch . '%" OR ' . db_prefix() . 'departments.name LIKE "%' . $keySearch . '%")';
            }
            
            $this->load->model('tickets_model');
            
            $ticketData = $this->tickets_model->get('', $where);
            
            if (!empty($ticketData)) {
                $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $ticketData], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function tickets_post()
    {
        try {
            
            $this->form_validation->set_rules('subject', 'Ticket Name', 'required');
            $this->form_validation->set_rules('department', 'Department', 'required');
            $this->form_validation->set_rules('contactid', 'Contact', 'required');
            
            if (!$this->form_validation->run()) {
                $this->response(['message' => strip_tags(validation_errors()),'error' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
            } else {
                $data = [
                    'subject' => $this->input->post('subject'),
                    'department' => $this->input->post('department'),
                    'userid' => $this->input->post('userid'),
                    'contactid' => $this->input->post('contactid'),
                    'assigned' => $this->input->post('assigned') ?? '',
                    'priority' => $this->input->post('priority') ?? '',
                    'service' => $this->input->post('service') ?? '',
                    'project_id' => $this->input->post('project_id') ?? '',
                    'message' => $this->input->post('message') ?? '',
                    'cc' => $this->input->post('cc') ?? '',
                    'tags' => $this->input->post('tags') ?? '',
                ];
                
                $this->load->model('tickets_model');
                $success = $this->tickets_model->add($data);
                if ($success) {
                    $this->response(['message' => _l('ticket_added_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('ticket_add_failed')], RestController::HTTP_NOT_FOUND);
                }
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function tickets_put()
    {
        try {
            
            if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_BAD_REQUEST);
            }
            
            $ticketID = $this->get('id');
            $this->load->model('tickets_model');
            $ticket = $this->tickets_model->get($ticketID);
            
            if (is_object($ticket)) {
                $data = array();
                parse_str(file_get_contents('php://input'), $data);
                $data['ticketid'] = $ticketID;
                $success = $this->tickets_model->update_single_ticket_settings($data);
                if ($success) {
                    $this->response(['message' => _l('ticket_updated_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('ticket_update_failed')], RestController::HTTP_NOT_FOUND);
                }
            } else {
                $this->response(['message' => _l('invalid_ticket_id')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function tickets_delete()
    {
        $ticketID = $this->get('id');
        
        if (can_staff_delete_ticket()) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        $this->load->model('tickets_model');
        $ticket = $this->tickets_model->get($ticketID);
        if (is_object($ticket)) {
            $success = $this->tickets_model->delete($ticketID);
            if ($success) {
                $this->response(['message' => _l('ticket_deleted_successfully')], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('ticket_delete_failed')], RestController::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['message' => _l('invalid_ticket_id')], RestController::HTTP_NOT_FOUND);
        }
    }
}