<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/RestController.php';

use FlutexAdminApi\RestController;

class Items extends RestController
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

        if (staff_cant('view', 'items', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
    }
    
    public function items_get()
    {
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
        
        $itemID = $this->get('id');
        
        $this->load->model('Invoice_items_model');
        
        $itemData = $this->Invoice_items_model->get($itemID);
        
        if (!empty($itemData)) {
            $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $itemData], RestController::HTTP_OK);
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
                $this->db->like('description', $keySearch);
                $this->db->or_like('long_description', $keySearch);
            }
            
            $itemsData = $this->db->get(db_prefix() . 'items')->result_array();
            
            if (!empty($itemsData)) {
                $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $itemsData], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function items_post()
    {
        if (staff_cant('create', 'items', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
            
            $this->form_validation->set_rules('description', 'Description', 'required|max_length[255]');
		    $this->form_validation->set_rules('rate', 'Rate', 'required|greater_than[0]');
            
            if (!$this->form_validation->run()) {
                $this->response(['message' => strip_tags(validation_errors()),'error' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
            } else {
                $data = [
                    'description' => $this->input->post('description'),
                    'rate' => $this->input->post('rate'),
                    'long_description' => $this->input->post('long_description')??'',
                    'unit' => $this->input->post('unit')??'',
                    'tax' => $this->input->post('tax')??'',
                    'tax2' => $this->input->post('tax2')??'',
                ];
                $group_id = $this->input->post('group_id') ?? '';
                if ($group_id != '') {
                    $data['group_id'] = $group_id;
                }
                
                $this->load->model('Invoice_items_model');
                $success = $this->Invoice_items_model->add($data);
                if ($success) {
                    $this->response(['message' => _l('item_added_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('item_add_failed')], RestController::HTTP_NOT_FOUND);
                }
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function items_put()
    {
        if (staff_cant('edit', 'items', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
            
            if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_BAD_REQUEST);
            }
            
            $itemID = $this->get('id');
            $this->load->model('Invoice_items_model');
            $item = $this->Invoice_items_model->get($itemID);
            
            if (is_object($item)) {
                $data = array();
                parse_str(file_get_contents('php://input'), $data);
                $data['itemid'] = $itemID;
                $success = $this->Invoice_items_model->edit($data);
                if ($success) {
                    $this->response(['message' => _l('item_updated_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('item_update_failed')], RestController::HTTP_NOT_FOUND);
                }
            } else {
                $this->response(['message' => _l('invalid_item_id')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function items_delete()
    {
        $itemID = $this->get('id');
        
        if (staff_cant('delete', 'items', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        $this->load->model('Invoice_items_model');
        $item = $this->Invoice_items_model->get($itemID);
        if (is_object($item)) {
            $output = $this->Invoice_items_model->delete($itemID);
            if ($output === TRUE) {
                $this->response(['message' => _l('item_deleted_successfully')], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('item_delete_failed')], RestController::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['message' => _l('invalid_item_id')], RestController::HTTP_NOT_FOUND);
        }
    }
}