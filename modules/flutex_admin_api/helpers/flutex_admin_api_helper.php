<?php

defined('BASEPATH') || exit('No direct script access allowed');

if (!function_exists('convertSerializeDataToObject')) {
    function convertSerializeDataToObject($data)
    {
        return json_decode(json_encode(unserialize($data)));
    }
}

if (!function_exists('isAuthorized')) {
    function isAuthorized()
    {
        if (checkModuleStatus()) {
            return [
                'response' => [
                    'message' => checkModuleStatus()['response']['message'],
                ],
                'response_code' => 404,
            ];
        }

        $loggedInStaff = get_instance()->authorization_token->validateToken();
        if (!$loggedInStaff['status']) {
            return [
                'response' => [
                    'message' => $loggedInStaff['message'],
                ],
                'response_code' => 401,
            ];
        }

        get_instance()->db->where('staffid', $loggedInStaff['data']->staff_id);
        $staff = get_instance()->db->get(db_prefix() . 'staff');

        $token = $staff->row()->flutex_api_key;
        if (empty($token)) {
            return [
                'response' => [
                    'message' => _l('login_to_continue'),
                ],
                'response_code' => 401,
            ];
        }

        $authToken = get_instance()->input->request_headers()['Authorization'];

        if (trim($token) !== trim($authToken)) {
            return [
                'response' => [
                    'message' => _l('login_to_continue'),
                ],
                'response_code' => 401,
            ];
        }

        $isStaffActive = get_staff($staff->row()->staffid)->active;

        if ($isStaffActive == 0) {
            return [
                'response' => [
                    'message' => _l('admin_auth_inactive_account'),
                ],
                'response_code' => 401,
            ];
        }

        return $loggedInStaff;
    }
}

if (!function_exists('checkModuleStatus')) {
    function checkModuleStatus()
    {
        get_instance()->load->library('app_modules');
        if (get_instance()->app_modules->is_inactive('flutex_admin_api')) {
            return [
                'response' => [
                    'message' => 'Flutex Admin/Staff API module is deactivated. Please reactivate or contact support',
                ],
                'response_code' => 404,
            ];
        }
    }
}

function get_invoices_percent_by_status_api($status, $staff_id)
{
    $has_permission_view = staff_can('view',  'invoices', $staff_id);
    $total_invoices      = total_rows(db_prefix() . 'invoices', 'status NOT IN(5)' . (!$has_permission_view ? ' AND (' . get_invoices_where_sql_for_staff($staff_id) . ')' : ''));

    $data            = [];
    $total_by_status = 0;
    if (!is_numeric($status)) {
        if ($status == 'not_sent') {
            $total_by_status = total_rows(db_prefix() . 'invoices', 'sent=0 AND status NOT IN(2,5)' . (!$has_permission_view ? ' AND (' . get_invoices_where_sql_for_staff($staff_id) . ')' : ''));
        }
    } else {
        $total_by_status = total_rows(db_prefix() . 'invoices', 'status = ' . $status . ' AND status NOT IN(5)' . (!$has_permission_view ? ' AND (' . get_invoices_where_sql_for_staff($staff_id) . ')' : ''));
    }
    $percent                 = ($total_invoices > 0 ? number_format(($total_by_status * 100) / $total_invoices, 2) : 0);
    $data['total_by_status'] = $total_by_status;
    $data['percent']         = $percent;
    $data['total']           = $total_invoices;

    return $data;
}

function get_proposals_percent_by_status_api($status,$staff_id, $total_proposals = '')
{
    $has_permission_view                 = staff_can('view',  'proposals',$staff_id);
    $has_permission_view_own             = staff_can('view_own',  'proposals',$staff_id);
    $allow_staff_view_proposals_assigned = get_option('allow_staff_view_proposals_assigned');
    $staffId                             = $staff_id;

    $whereUser = '';
    if (!$has_permission_view) {
        if ($has_permission_view_own) {
            $whereUser = '(addedfrom=' . $staffId;
            if ($allow_staff_view_proposals_assigned == 1) {
                $whereUser .= ' OR assigned=' . $staffId;
            }
            $whereUser .= ')';
        } else {
            $whereUser .= 'assigned=' . $staffId;
        }
    }

    if (!is_numeric($total_proposals)) {
        $total_proposals = total_rows(db_prefix() . 'proposals', $whereUser);
    }

    $data            = [];
    $total_by_status = 0;
    $where           = 'status=' . get_instance()->db->escape_str($status);
    if (!$has_permission_view) {
        $where .= ' AND (' . $whereUser . ')';
    }

    $total_by_status = total_rows(db_prefix() . 'proposals', $where);
    $percent         = ($total_proposals > 0 ? number_format(($total_by_status * 100) / $total_proposals, 2) : 0);

    $data['total_by_status'] = $total_by_status;
    $data['percent']         = $percent;
    $data['total']           = $total_proposals;

    return $data;
}

function get_estimates_percent_by_status_api($status, $staff_id, $project_id = null)
{
    $has_permission_view = staff_can('view',  'estimates',$staff_id);
    $where               = '';

    if (isset($project_id)) {
        $where .= 'project_id=' . get_instance()->db->escape_str($project_id) . ' AND ';
    }
    if (!$has_permission_view) {
        $where .= get_estimates_where_sql_for_staff($staff_id);
    }

    $where = trim($where);

    if (endsWith($where, ' AND')) {
        $where = substr_replace($where, '', -3);
    }

    $total_estimates = total_rows(db_prefix() . 'estimates', $where);

    $data            = [];
    $total_by_status = 0;

    if (!is_numeric($status)) {
        if ($status == 'not_sent') {
            $total_by_status = total_rows(db_prefix() . 'estimates', 'sent=0 AND status NOT IN(2,3,4)' . ($where != '' ? ' AND (' . $where . ')' : ''));
        }
    } else {
        $whereByStatus = 'status=' . $status;
        if ($where != '') {
            $whereByStatus .= ' AND (' . $where . ')';
        }
        $total_by_status = total_rows(db_prefix() . 'estimates', $whereByStatus);
    }

    $percent                 = ($total_estimates > 0 ? number_format(($total_by_status * 100) / $total_estimates, 2) : 0);
    $data['total_by_status'] = $total_by_status;
    $data['percent']         = $percent;
    $data['total']           = $total_estimates;

    return $data;
}