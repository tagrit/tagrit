<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/RestController.php';

use FlutexAdminApi\RestController;

class Tasks extends RestController
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

        if (staff_cant('view', 'tasks', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
    }
    
    public function tasks_get()
    {
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
        
        $taskID = $this->get('id');
        
        $this->load->model('tasks_model');
        
        if (isset($taskID)) {
            $taskData = $this->tasks_model->get($taskID);
            $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $taskData], RestController::HTTP_OK);
        } else {
            $taskData = [];
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
            foreach ($tasks as $task) {
                $taskData[] = (array)$this->tasks_model->get($task['id']);
            }
        }
        
        $task_summary = $this->tasks_summary();
        
        if (!empty($taskData)) {
            $this->response(['message' => _l('data_retrieved_successfully'),'overview' => $task_summary, 'data' => $taskData], RestController::HTTP_OK);
        } else {
            $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
        }
    }
    
    public function tasks_summary()
    {
        // Tasks Overview
        $tasks = [];
        $this->load->model('tasks_model');
        $tasks_statuses = $this->tasks_model->get_statuses();

        foreach ($tasks_statuses as $key => $status) {
            $where = 'status = ' . $status['id'];
            array_push($tasks, [
                'status' => $status['name'],
                'total' => strval(total_rows(db_prefix() . 'tasks', $where)),
                'percent' => total_rows(db_prefix() . 'tasks', $where) == 0 ? '0' : strval(total_rows(db_prefix() . 'tasks', $where) / total_rows(db_prefix() . 'tasks') * 100)
            ]);
        }
        return $tasks;
    }
    
    public function search_get()
    {
        try {
            
            if (!empty($this->get()) && !in_array('search', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
            }
            
            $keySearch = $this->get('search');
            
            if ($keySearch) {
                $this->db->select(db_prefix().'tasks.*');
                $this->db->from(db_prefix().'tasks');
                $this->db->like('name', $keySearch);
                $this->db->or_like(db_prefix().'tasks.id', $keySearch);
            }
            
            $taskData = $this->db->get()->result_array();
            
            if (!empty($taskData)) {
                $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $taskData], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function tasks_post()
    {
        if (staff_cant('create', 'tasks', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
            $this->form_validation->set_rules('name', 'Task Name', 'required|max_length[600]');
            $this->form_validation->set_rules('startdate', 'Task Start Date', 'required');
            $this->form_validation->set_rules('priority', 'Task Priority', 'required');
            
            if (!$this->form_validation->run()) {
                $this->response(['message' => strip_tags(validation_errors()),'error' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
            } else {
                $data = [
                    'name' => $this->input->post('name'),
                    'hourly_rate' => $this->input->post('hourly_rate') ?? '',
                    'milestone' => $this->input->post('milestone') ?? '',
                    'startdate' => $this->input->post('startdate'),
                    'duedate' => $this->input->post('duedate') ?? '',
                    'priority' => $this->input->post('priority'),
                    'is_public' => $this->input->post('is_public') ?? '',
                    'billable' => $this->input->post('billable') ?? '',
                    'repeat_every' => $this->input->post('repeat_every') ?? '',
                    'repeat_every_custom' => $this->input->post('repeat_every_custom') ?? '',
                    'repeat_type_custom' => $this->input->post('repeat_type_custom') ?? '',
                    'cycles' => $this->input->post('cycles') ?? '',
                    'rel_type' => $this->input->post('rel_type') ?? '',
                    'rel_id' => $this->input->post('rel_id') ?? '',
                    'tags' => $this->input->post('tags') ?? '',
                    'description' => $this->input->post('description') ?? ''
                ];
                
                $this->load->model('tasks_model');
                $success = $this->tasks_model->add($data);
                if ($success) {
                    $this->response(['message' => _l('task_added_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('task_add_failed')], RestController::HTTP_NOT_FOUND);
                }
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function tasks_put()
    {
        if (staff_cant('edit', 'tasks', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
            
            if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_BAD_REQUEST);
            }
            
            $taskID = $this->get('id');
            $this->load->model('tasks_model');
            $task = $this->tasks_model->get($taskID);
            
            if (is_object($task)) {
                $data = array();
                parse_str(file_get_contents('php://input'), $data);
                $success = $this->tasks_model->update($data, $taskID);
                if ($success) {
                    $this->response(['message' => _l('task_updated_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('task_update_failed')], RestController::HTTP_NOT_FOUND);
                }
            } else {
                $this->response(['message' => _l('invalid_task_id')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function tasks_delete()
    {
        if (staff_cant('delete', 'tasks', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        $taskID = $this->get('id');
        
        $this->load->model('tasks_model');
        $task = $this->tasks_model->get($taskID);
        if (is_object($task)) {
            $success = $this->tasks_model->delete_task($taskID);
            if ($success) {
                $this->response(['message' => _l('task_deleted_successfully')], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('task_delete_failed')], RestController::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['message' => _l('invalid_task_id')], RestController::HTTP_NOT_FOUND);
        }
    }
}