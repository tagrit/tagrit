<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/RestController.php';

use FlutexAdminApi\RestController;

class Dashboard extends RestController
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
    }
    
    public function dashboard_get()
    {
        // Perfex CRM Logo
        $perfex_logo = get_option('company_logo');
        $perfex_logo_dark = get_option('company_logo_dark');
        
        // Staff Information
        $staffID = $this->staffInfo['data']->staff_id;
        $staff = $this->db->where('staffid', $staffID)->get(db_prefix() . 'staff')->row();
        $staff_data = array(
            'id'=> $staff->staffid,
            'email' => $staff->email,
            'firstname' => $staff->firstname,
            'lastname' => $staff->lastname,
            'phonenumber' => $staff->phonenumber,
            'profile_image' => staff_profile_image_url($staff->staffid),
        );
        
        // Dashboard Overview Data << START >>
        $total_invoices = total_rows(db_prefix() . 'invoices', 'status NOT IN (5,6)' . (!staff_can('view', 'invoices', $staffID) ? ' AND ' . get_invoices_where_sql_for_staff($staffID) : ''));
        $total_invoices_awaiting_payment = total_rows(db_prefix() . 'invoices', 'status NOT IN (2,5,6)' . (!staff_can('view', 'invoices', $staffID) ? ' AND ' . get_invoices_where_sql_for_staff($staffID) : ''));
        $percent_total_invoices_awaiting_payment = ($total_invoices > 0 ? number_format(($total_invoices_awaiting_payment * 100) / $total_invoices, 2) : 0);

        $where = '';
        if (!is_admin()) {
            $where .= '(addedfrom = ' . $staffID . ' OR assigned = ' . $staffID . ')';
        }
        
        $total_leads = total_rows(db_prefix() . 'leads', ($where == '' ? 'junk=0' : $where .= ' AND junk =0'));
        if ($where == '') {
            $where .= 'status=1';
        } else {
            $where .= ' AND status =1';
        }
        $total_leads_converted = total_rows(db_prefix() . 'leads', $where);
        $percent_total_leads_converted = ($total_leads > 0 ? number_format(($total_leads_converted * 100) / $total_leads, 2) : 0);

        $_where = '';
        $project_status = get_project_status_by_id(2);
        if (!staff_can('view', 'projects', $staffID)) {
            $_where = 'id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . $staffID . ')';
        }
        $total_projects = total_rows(db_prefix() . 'projects', $_where);
        $where = ($_where == '' ? '' : $_where . ' AND ') . 'status = 2';
        $total_projects_in_progress = total_rows(db_prefix() . 'projects', $where);
        $percent_in_progress_projects = ($total_projects > 0 ? number_format(($total_projects_in_progress * 100) / $total_projects, 2) : 0);

        $_where = '';
        if (!staff_can('view', 'tasks', $staffID)) {
            $_where = db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . $staffID . ')';
        }
        $total_tasks = total_rows(db_prefix() . 'tasks', $_where);
        $where = ($_where == '' ? '' : $_where . ' AND ') . 'status != ' . Tasks_model::STATUS_COMPLETE;
        $total_not_finished_tasks = total_rows(db_prefix() . 'tasks', $where);
        $percent_not_finished_tasks = ($total_tasks > 0 ? number_format(($total_not_finished_tasks * 100) / $total_tasks, 2) : 0);
        // Dashboard Overview Data << END >>
        
        //Menu Items
        $menu_items = [
            'customers' => staff_can('view', 'customers', $staffID) || (have_assigned_customers() || (!have_assigned_customers() && staff_can('create', 'customers', $staffID))),
            'proposals' => (staff_can('view', 'proposals', $staffID) || staff_can('view_own', 'proposals', $staffID)) || (staff_has_assigned_proposals() && get_option('allow_staff_view_proposals_assigned') == 1),
            'estimates' => (staff_can('view', 'estimates', $staffID) || staff_can('view_own', 'estimates', $staffID)) || (staff_has_assigned_estimates() && get_option('allow_staff_view_estimates_assigned') == 1),
            'invoices' => (staff_can('view', 'invoices', $staffID) || staff_can('view_own', 'invoices', $staffID)) || (staff_has_assigned_invoices() && get_option('allow_staff_view_invoices_assigned') == 1),
            'payments' => staff_can('view', 'payments', $staffID) || staff_can('view_own', 'invoices', $staffID) || (get_option('allow_staff_view_invoices_assigned') == 1 && staff_has_assigned_invoices()),
            'credit_notes' => staff_can('view', 'credit_notes', $staffID) || staff_can('view_own', 'credit_notes', $staffID),
            'items' => staff_can('view', 'items', $staffID),
            'subscriptions' => staff_can('view', 'subscriptions', $staffID) || staff_can('view_own', 'subscriptions', $staffID),
            'expenses' => staff_can('view', 'expenses', $staffID) || staff_can('view_own', 'expenses', $staffID),
            'contracts' => staff_can('view', 'contracts', $staffID) || staff_can('view_own', 'contracts', $staffID),
            'projects' => staff_can('view', 'projects', $staffID) || staff_can('view_own', 'projects', $staffID),
            'tasks' => staff_can('view', 'tasks', $staffID),
            'tickets' => (!is_staff_member($staffID) && get_option('access_tickets_to_none_staff_members') == 1) || is_staff_member($staffID),
            'leads' => is_staff_member($staffID),
            'staff' => staff_can('view', 'staff', $staffID)
        ];

        $this->response([
            'message' => _l('data_retrieved_successfully'),
            'overview' => [
                'perfex_logo' => ($perfex_logo != '' ? base_url('uploads/company/' . $perfex_logo) : ''),
                'perfex_logo_dark' => ($perfex_logo_dark != '' ? base_url('uploads/company/' . $perfex_logo_dark) : ''),
                'total_invoices' => strval($total_invoices),
                'invoices_awaiting_payment_total' => strval($total_invoices_awaiting_payment),
                'invoices_awaiting_payment_percent' => strval($percent_total_invoices_awaiting_payment),
                'total_leads' => strval($total_leads),
                'leads_converted_total' => strval($total_leads_converted),
                'leads_converted_percent' => strval($percent_total_leads_converted),
                'total_projects' => strval($total_projects),
                'projects_in_progress_total' => strval($total_projects_in_progress),
                'projects_in_progress_percent' => strval($percent_in_progress_projects),
                'total_tasks' => strval($total_tasks),
                'tasks_not_finished_total' => strval($total_not_finished_tasks),
                'tasks_not_finished_percent' => strval($percent_not_finished_tasks)
            ],
            'data' => [
                'invoices'  => $this->invoices_summary(),
                'estimates' => $this->estimates_summary(),
                'proposals' => $this->proposals_summary(),
                'projects'  => $this->projects_summary(),
                'tasks'     => $this->tasks_summary(),
                'customers' => $this->customers_summary(),
                'leads'     => $this->leads_summary(),
                'tickets'   => $this->tickets_summary(),
            ],
            'staff' => $staff_data,
            'menu_items' => $menu_items
        ], RestController::HTTP_OK);
    }
    
    public function overview_get()
    {
        // Perfex CRM Logo / Name
        $perfex_logo = get_option('company_logo');
        $perfex_logo_dark = get_option('company_logo_dark');
        $perfex_company_name = get_option('companyname');
        
        $this->response([
            'message' => _l('data_retrieved_successfully'),
            'data' => [
                'perfex_logo' => ($perfex_logo != '' ? base_url('uploads/company/' . $perfex_logo) : ''),
                'perfex_logo_dark' => ($perfex_logo_dark != '' ? base_url('uploads/company/' . $perfex_logo_dark) : ''),
                'perfex_company_name' => $perfex_company_name,
            ],
        ], RestController::HTTP_OK);
    }
    
    public function notifications_get($read = false)
    {
        $read     = $read == false ? 0 : 1;
        $total    = 15;
        $staff_id = $this->staffInfo['data']->staff_id;

        $sql = 'SELECT COUNT(*) as total FROM ' . db_prefix() . 'notifications WHERE isread=' . $read . ' AND touserid=' . $staff_id;
        $sql .= ' UNION ALL ';
        $sql .= 'SELECT COUNT(*) as total FROM ' . db_prefix() . 'notifications WHERE isread_inline=' . $read . ' AND touserid=' . $staff_id;

        $res = $this->db->query($sql)->result();

        $total_unread        = $res[0]->total;
        $total_unread_inline = $res[1]->total;

        if ($total_unread > $total) {
            $total = ($total_unread - $total) + $total;
        } elseif ($total_unread_inline > $total) {
            $total = ($total_unread_inline - $total) + $total;
        }

        // In case user is not marking the notifications are read this process may be long because the script will always fetch the total from the not read notifications.
        // In this case we are limiting to 30
        $total = $total > 30 ? 30 : $total;

        $this->db->where('touserid', $staff_id);
        $this->db->limit($total);
        $this->db->order_by('date', 'desc');

        $notifications = $this->db->get(db_prefix() . 'notifications')->result_array();
        
        $this->response(['message' => _l('data_retrieved_successfully'), 'data' => $notifications], RestController::HTTP_OK);
    }
    
    public function invoices_summary()
    {
        $staffID = $this->staffInfo['data']->staff_id;
        if (staff_can('view', 'invoices', $staffID)) {
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

        return null;
    }

    public function estimates_summary()
    {
        $staffID = $this->staffInfo['data']->staff_id;
        if (staff_can('view', 'estimates', $staffID)) {
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

        return null;
    }

    public function proposals_summary()
    {
        $staffID = $this->staffInfo['data']->staff_id;
        if (staff_can('view', 'proposals', $staffID)) {
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

        return null;
    }

    public function projects_summary()
    {
        $staffID = $this->staffInfo['data']->staff_id;
        if (staff_can('view', 'projects', $staffID)) {
            // Projects Overview
            $projects = [];
            $this->load->model('projects_model');
            $project_statuses = $this->projects_model->get_project_statuses();
    
            $_where = '';
            $staffID = $this->staffInfo['data']->staff_id;
            if (staff_cant('view', 'projects', $staffID)) {
                $_where = 'id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . $staffID . ')';
            }
    
            foreach ($project_statuses as $key => $status) {
                $where = ($_where == '' ? '' : $_where . ' AND ') . 'status = ' . $status['id'];
                array_push($projects, [
                    'status' => $status['name'],
                    'total' => strval(total_rows(db_prefix() . 'projects', $where)),
                    'percent' => total_rows(db_prefix() . 'projects', $where) == 0 ? '0' : strval(total_rows(db_prefix() . 'projects', $where) / total_rows(db_prefix() . 'projects') * 100)
                ]);
            }
            return $projects;
        }

        return null;
    }

    public function tasks_summary()
    {
        $staffID = $this->staffInfo['data']->staff_id;
        if (staff_can('view', 'tasks', $staffID)) {
            // Tasks Overview
            $tasks = [];
            $this->load->model('tasks_model');
            $tasks_statuses = $this->tasks_model->get_statuses();
    
            $_where = '';
            $staffID = $this->staffInfo['data']->staff_id;
            if (staff_cant('view', 'tasks', $staffID)) {
                $_where = db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . $staffID . ')';
            }
    
            foreach ($tasks_statuses as $key => $status) {
                $where = ($_where == '' ? '' : $_where . ' AND ') . 'status = ' . $status['id'];
                array_push($tasks, [
                    'status' => $status['name'],
                    'total' => strval(total_rows(db_prefix() . 'tasks', $where)),
                    'percent' => total_rows(db_prefix() . 'tasks', $where) == 0 ? '0' : strval(total_rows(db_prefix() . 'tasks', $where) / total_rows(db_prefix() . 'tasks') * 100)
                ]);
            }
            return $tasks;
        }

        return null;
    }

    public function customers_summary()
    {
        $staffID = $this->staffInfo['data']->staff_id;
        if (staff_can('view', 'customers', $staffID) || have_assigned_customers()) {
            $where_summary = '';
            $staffID = $this->staffInfo['data']->staff_id;
            if (staff_cant('view', 'customers', $staffID)) {
                $where_summary = ' AND userid IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id=' . $staffID . ')';
            }
            return [
                'customers_total' => strval(total_rows(db_prefix() . 'clients', ($where_summary != '' ? substr($where_summary, 5) : ''))),
                'customers_active' => strval(total_rows(db_prefix() . 'clients', 'active=1' . $where_summary)),
                'customers_inactive' => strval(total_rows(db_prefix() . 'clients', 'active=0' . $where_summary)),
                'contacts_active' => strval(total_rows(db_prefix() . 'contacts', 'active=1' . $where_summary)),
                'contacts_inactive' => strval(total_rows(db_prefix() . 'contacts', 'active=0' . $where_summary)),
                'contacts_last_login' => strval(total_rows(db_prefix() . 'contacts', 'last_login LIKE "' . date('Y-m-d') . '%"' . $where_summary))
            ];
        }

        return null;
    }

    public function leads_summary()
    {
        $staffID = $this->staffInfo['data']->staff_id;
        if (staff_can('view', 'leads', $staffID)) {
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

        return null;
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
}