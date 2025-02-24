<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/RestController.php';

use FlutexAdminApi\RestController;

class Projects extends RestController
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

        if (staff_cant('view', 'projects', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
    }
    
    public function projects_get()
    {
        
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
        
        $projectID = $this->get('id');
        $group     = $this->get('group');
        
        $this->load->model('projects_model');
        
        $projectData = $this->projects_model->get($projectID);
        
        $projectSummary = $this->projects_summary();
        
        if (!empty($projectData) && !empty($projectID)) {
            $projectData->project_members              = $this->projects_model->get_project_members($projectID);
            $projectData->total_logged_time            = $this->projects_model->total_logged_time($projectID);
            $projectData->status_name                  = get_project_status_by_id($projectData->status)['name'];
            $projectData->addedfrom_name               = get_staff_full_name($projectData->addedfrom);
            $projectData->settings->available_features = convertSerializeDataToObject($projectData->settings->available_features);
            
            if (!empty($projectData->project_members)) {
                foreach ($projectData->project_members as $key => $project_member) {
                    $projectData->project_members[$key]['staff_name'] = get_staff_full_name($project_member['staff_id']);
                }
            }
        }
        
        if (!empty($projectData) && empty($projectID)) {
            foreach ($projectData as $key => $project) {
                $projectData[$key]['status_name']    = get_project_status_by_id($project['status'])['name'];
                $projectData[$key]['addedfrom_name'] = get_staff_full_name($project['addedfrom']);
            }
            $this->response(['message' => _l('data_retrieved_successfully'), 'overview' => $projectSummary, 'data' => $projectData], RestController::HTTP_OK);
        }
        
        if (!empty($group) && !empty($projectID)) {
            $projectData = $this->projectGroups($projectID, $group);
            if (!empty($projectData)) {
            $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $projectData,], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
        }
        
        if (!empty($group) && empty($projectID)) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
        
        if (!empty($projectData)) {
            $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $projectData], RestController::HTTP_OK);
        } else {
            $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
        }
    }
    
    public function projectGroups($projectId, $groupName)
    {
        $projectData = $this->projects_model->get($projectId);
        
        if (empty($projectData)) {
            return $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
        }

        $settings       = $projectData->settings;
        $availableFeatures = convertSerializeDataToObject($settings->available_features);

        switch ($groupName) {
            case 'tasks':
                if ('1' !== strval($availableFeatures->project_tasks)) {
                    return $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
                }
                $this->load->model('tasks_model');
                $projectGroup = $this->projects_model->get_tasks($projectId);
                
                if (!empty($projectGroup)) {
                    foreach ($projectGroup as $key => $task) {
                        $projectGroup[$key]['status_name']    = get_task_status_by_id($task['status'])['name'];
                        $projectGroup[$key]['addedfrom_name'] = get_staff_full_name($task['addedfrom']);
                        $projectGroup[$key]['assignees']      = $this->tasks_model->get_task_assignees($task['id']);
                        $projectGroup[$key]['followers_ids']  = $this->tasks_model->get_task_followers($task['id']);
                    }
                }
                break;

            case 'timesheets':
                if ('1' !== strval($availableFeatures->project_timesheets)) {
                    return $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
                }
                $projectGroup = $this->projects_model->get_timesheets($projectId);
                break;

            case 'milestones':
                if ('1' !== strval($availableFeatures->project_milestones)) {
                    return $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
                }
                $projectGroup = $this->projects_model->get_milestones($projectId);
                break;

            case 'discussions':
                if ('1' !== strval($availableFeatures->project_discussions)) {
                    return $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
                }
                $projectGroup = $this->projects_model->get_discussions($projectId);
                break;

            case 'proposals':
                if ('1' !== strval($availableFeatures->project_proposals)) {
                    return $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
                }
                $this->load->model('proposals_model');
                $projectGroup = $this->proposals_model->get('', ['project_id' => $projectId]);
                break;

            case 'estimates':
                if ('1' !== strval($availableFeatures->project_estimates)) {
                    return $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
                }
                $this->load->model('estimates_model');
                $projectGroup = $this->estimates_model->get('', ['project_id' => $projectId]);
                break;

            case 'invoices':
                if ('1' !== strval($availableFeatures->project_invoices)) {
                    return $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
                }
                $this->load->model('invoices_model');
                $projectGroup = $this->invoices_model->get('', ['project_id' => $projectId]);
                break;

            case 'subscriptions':
                if ('1' !== strval($availableFeatures->project_invoices)) {
                    return $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
                }
                $this->load->model('subscriptions_model');
                $projectGroup = $this->subscriptions_model->get(['project_id' => $projectId]);
                break;

            case 'expenses':
                if ('1' !== strval($availableFeatures->project_expenses)) {
                    return $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
                }
                $this->load->model('expenses_model');
                $projectGroup = $this->expenses_model->get('', ['project_id' => $projectId]);
                break;

            case 'credit_notes':
                if ('1' !== strval($availableFeatures->project_credit_notes)) {
                    return $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
                }
                $this->load->model('credit_notes_model');
                $projectGroup = $this->credit_notes_model->get('', ['project_id' => $projectId]);
                break;

            default:
                break;
        }
        return $projectGroup;
    }

    public function projects_summary()
    {
        $projects = [];
        $this->load->model('projects_model');
        $project_statuses = $this->projects_model->get_project_statuses();

        foreach ($project_statuses as $key => $status) {
            $where = 'status = ' . $status['id'];
            array_push($projects, [
                'status' => $status['name'],
                'total' => strval(total_rows(db_prefix() . 'projects', $where)),
                'percent' => total_rows(db_prefix() . 'projects', $where) == 0 ? '0' : strval(total_rows(db_prefix() . 'projects', $where) / total_rows(db_prefix() . 'projects') * 100)
            ]);
        }
        return $projects;
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
                $where .= '(description LIKE "%' . $keySearch . '%" OR name LIKE "%' . $keySearch . '%")';
            }
            
            $this->load->model('projects_model');
            
            $projectData = $this->projects_model->get('', $where);
            
            if (!empty($projectData)) {
                $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $projectData], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('data_not_found')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function projects_post()
    {
        if (staff_cant('create', 'projects', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
            $this->form_validation->set_rules('name', 'Project Name', 'required|max_length[191]');
            $this->form_validation->set_rules('clientid', 'Customer', 'required|max_length[11]');
            $this->form_validation->set_rules('billing_type', 'Billing Type', 'required');
            $this->form_validation->set_rules('start_date', 'Project Start Date', 'required');
            $this->form_validation->set_rules('status', 'Project Status', 'required');
            
            if (!$this->form_validation->run()) {
                $this->response(['message' => strip_tags(validation_errors()),'error' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
            } else {
                $data = [
                    'name' => $this->input->post('name'),
                    'clientid' => $this->input->post('clientid'),
                    'progress_from_tasks' => $this->input->post('progress_from_tasks') ?? '',
                    'progress' => $this->input->post('progress') ?? '',
                    'billing_type' => $this->input->post('billing_type'),
                    'status' => $this->input->post('status'),
                    'project_cost' => $this->input->post('project_cost') ?? '',
                    'project_rate_per_hour' => $this->input->post('project_rate_per_hour') ?? '',
                    'estimated_hours' => $this->input->post('estimated_hours') ?? '',
                    'project_members' => $this->input->post('project_members'),
                    'start_date' => $this->input->post('start_date'),
                    'deadline' => $this->input->post('deadline') ?? '',
                    'tags' => $this->input->post('tags') ?? '',
                    'description' => $this->input->post('description') ?? '',
                    // TODO: get available_features from post request too -> Project Settings tab
                    'settings' => array( 'available_features' => array( 'project_overview', 'project_milestones', 'project_gantt', 'project_tasks', 'project_estimates', 'project_subscriptions', 'project_invoices', 'project_expenses', 'project_credit_notes', 'project_tickets', 'project_timesheets', 'project_files', 'project_discussions', 'project_notes', 'project_activity'))
                ];
                
                $this->load->model('projects_model');
                $success = $this->projects_model->add($data);
                if ($success) {
                    $this->response(['message' => _l('project_added_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('project_add_failed')], RestController::HTTP_NOT_FOUND);
                }
            }
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function projects_put($id = '')
    {
        if (staff_cant('edit', 'projects', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        try {
            
            if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
                $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_BAD_REQUEST);
            }
            
            $projectID = $this->get('id');
            $this->load->model('projects_model');
            $project = $this->projects_model->get($projectID);
            
            if (is_object($project)) {
                $data = array();
                parse_str(file_get_contents('php://input'), $data);
                // TODO: get available_features from put request too -> Project Settings tab
                $data['settings'] = array( 'available_features' => array( 'project_overview', 'project_milestones', 'project_gantt', 'project_tasks', 'project_estimates', 'project_subscriptions', 'project_invoices', 'project_expenses', 'project_credit_notes', 'project_tickets', 'project_timesheets', 'project_files', 'project_discussions', 'project_notes', 'project_activity'));
                $success = $this->projects_model->update($data, $projectID);
                if ($success) {
                    $this->response(['message' => _l('project_updated_successfully')], RestController::HTTP_OK);
                } else {
                    $this->response(['message' => _l('project_update_failed')], RestController::HTTP_NOT_FOUND);
                }
            } else {
                $this->response(['message' => _l('invalid_project_id')], RestController::HTTP_NOT_FOUND);
            }
            
        } catch (\Throwable $th) {
            $this->response(['message' => _l('something_went_wrong')], RestController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function projects_delete()
    {
        $projectID = $this->get('id');
        
        if (staff_cant('delete', 'projects', $this->staffInfo['data']->staff_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], RestController::HTTP_FORBIDDEN);
        }
        
        $this->load->model('projects_model');
        $project = $this->projects_model->get($projectID);
        if (is_object($project)) {
            $success = $this->projects_model->delete($projectID);
            if ($success) {
                $this->response(['message' => _l('project_deleted_successfully')], RestController::HTTP_OK);
            } else {
                $this->response(['message' => _l('project_delete_failed')], RestController::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['message' => _l('invalid_project_id')], RestController::HTTP_NOT_FOUND);
        }
    }
}
