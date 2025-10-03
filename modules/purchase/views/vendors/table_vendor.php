<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('purchase_vendors', '', 'delete');

$custom_fields = get_table_custom_fields('vendors');
$this->ci->db->query("SET sql_mode = ''");

$aColumns = [
    '1',
    db_prefix().'pur_vendor.userid as userid',
    'company',
    'firstname',
    'email',
    db_prefix().'pur_vendor.phonenumber as phonenumber',
    db_prefix().'pur_vendor.active',
    db_prefix().'pur_vendor.category',
    db_prefix().'pur_vendor.datecreated as datecreated',
];

$sIndexColumn = 'userid';
$sTable       = db_prefix().'pur_vendor';
$where        = [];
// Add blank where all filter can be stored
$filter = [];

$join = [
    'LEFT JOIN '.db_prefix().'pur_contacts ON '.db_prefix().'pur_contacts.userid='.db_prefix().'pur_vendor.userid AND '.db_prefix().'pur_contacts.is_primary=1',
];

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN '.db_prefix().'customfieldsvalues as ctable_' . $key . ' ON '.db_prefix().'pur_vendor.userid = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

// Filter by vendor category
$groups   = $this->ci->purchase_model->get_vendor_category();
$groupIds = [];
foreach ($groups as $group) {
    if ($this->ci->input->post('vendor_category_' . $group['id'])) {
        array_push($groupIds, $group['id']);
    }
}
if (count($groupIds) > 0) {
    $where_category = '';
    foreach ($groupIds as $t) {
        if ($t != '') {
            if ($where_category == '') {
                $where_category .= ' AND (find_in_set('.$t.', category)';
            } else {
                $where_category .= ' or find_in_set('.$t.', category)';
            }
        }
    }
    if ($where_category != '') {
        $where_category .= ')';
        array_push($where, $where_category);
    }
}

// Filter by Purchase order
$purorderStatusIds = [];
$purorder_statuses = [1,2,3,4];
foreach ($purorder_statuses as $status) {
    if ($this->ci->input->post('pur_order_status_' . $status)) {
        array_push($purorderStatusIds, $status);
    }
}

if (count($purorderStatusIds) > 0) {
    array_push($where, 'AND '.db_prefix().'pur_vendor.userid IN (SELECT vendor FROM '.db_prefix().'pur_orders WHERE approve_status IN (' . implode(', ', $purorderStatusIds) . '))');
}

// Filter by Purchase estimate
$purestimateStatusIds = [];
$purestimate_statuses = [1,2,3];
foreach ($purestimate_statuses as $status) {
    if ($this->ci->input->post('estimate_status_' . $status)) {
        array_push($purestimateStatusIds, $status);
    }
}

if (count($purestimateStatusIds) > 0) {
    array_push($where, 'AND '.db_prefix().'pur_vendor.userid IN (SELECT vendor FROM '.db_prefix().'pur_estimates WHERE status IN (' . implode(', ', $purestimateStatusIds) . '))');
}


// Filter by my vendors
if ($this->ci->input->post('my_vendors')) {
    array_push($where, 'AND '.db_prefix().'pur_vendor.userid IN (SELECT vendor_id FROM '.db_prefix().'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . ')');
}

if(has_permission('purchase_vendors', '', 'view_own') && !is_admin()){
    array_push($where, 'AND ('.db_prefix().'pur_vendor.userid IN (SELECT vendor_id FROM '.db_prefix().'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . ') OR '.db_prefix().'pur_vendor.addedfrom = '.get_staff_user_id().' )');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix().'pur_contacts.id as contact_id',
    'lastname',
    db_prefix().'pur_vendor.zip as zip',
    'registration_confirmed',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Bulk actions
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['userid'] . '"><label></label></div>';
    // User id
    $row[] = $aRow['userid'];

    // Company
    $company  = $aRow['company'];
    $isPerson = false;

    if ($company == '') {
        $company  = _l('no_company_view_profile');
        $isPerson = true;
    }

    $url = admin_url('purchase/vendor/' . $aRow['userid']);

    if ($isPerson && $aRow['contact_id']) {
        $url .= '?contactid=' . $aRow['contact_id'];
    }

    $company = '<a href="' . $url . '">' . $company . '</a>';

    $company .= '<div class="row-options">';
    $company .= '<a href="' . $url . '">' . _l('view') . '</a>';

    if ($aRow['registration_confirmed'] == 0 && is_admin()) {
        $company .= ' | <a href="' . admin_url('purchase/confirm_registration/' . $aRow['userid']) . '" class="text-success bold">' . _l('confirm_registration') . '</a>';
    }
    if (!$isPerson) {
        $company .= ' | <a href="' . admin_url('purchase/vendor/' . $aRow['userid'] . '?group=contacts') . '">' . _l('customer_contacts') . '</a>';
    }
    if ($hasPermissionDelete) {
        $company .= ' | <a href="' . admin_url('purchase/delete_vendor/' . $aRow['userid']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }

    $company .= '</div>';

    $row[] = $company;

    // Primary contact
    $row[] = ($aRow['contact_id'] ? '<a href="' . admin_url('pur_vendor/client/' . $aRow['userid'] . '?contactid=' . $aRow['contact_id']) . '" target="_blank">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>' : '');

    // Primary contact email
    $row[] = ($aRow['email'] ? '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>' : '');

    // Primary contact phone
    $row[] = ($aRow['phonenumber'] ? '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>' : '');

    $groupsRow = '';
    if ($aRow[db_prefix().'pur_vendor.category']) {
        $groups = explode(',', $aRow[db_prefix().'pur_vendor.category']);
        foreach ($groups as $group) {
            $groupsRow .= '<span class="label label-default mleft5 inline-block">' . get_vendor_cate_name_by_id($group) . '</span>';
        }
    }

    $row[] = $groupsRow;

    // Toggle active/inactive customer
    $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="' . _l('customer_active_inactive_help') . '">
    <input type="checkbox"' . ($aRow['registration_confirmed'] == 0 ? ' disabled' : '') . ' data-switch-url="' . admin_url() . 'purchase/change_vendor_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['userid'] . '" data-id="' . $aRow['userid'] . '" ' . ($aRow[db_prefix().'pur_vendor.active'] == 1 ? 'checked' : '') . '>
    <label class="onoffswitch-label" for="' . $aRow['userid'] . '"></label>
    </div>';

    // For exporting
    $toggleActive .= '<span class="hide">' . ($aRow[db_prefix().'pur_vendor.active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';

    $row[] = $toggleActive;


    $row[] = _dt($aRow['datecreated']);

    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $row['DT_RowClass'] = 'has-row-options';

    if ($aRow['registration_confirmed'] == 0) {
        $row['DT_RowClass'] .= ' alert-info requires-confirmation';
        $row['Data_Title']  = _l('customer_requires_registration_confirmation');
        $row['Data_Toggle'] = 'tooltip';
    }

    $row = hooks()->apply_filters('customers_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
