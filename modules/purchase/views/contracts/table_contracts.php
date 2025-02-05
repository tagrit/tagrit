<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix().'pur_contracts.department',
    db_prefix().'pur_contracts.project',
    'service_category',
    db_prefix().'pur_contracts.vendor',
    'contract_name',
    'contract_value',
    'payment_amount',
    'payment_cycle',
    'payment_terms',
    db_prefix().'pur_contracts.start_date',
    db_prefix().'pur_contracts.end_date', 
    'add_from',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'pur_contracts';
$join         = [
                    'LEFT JOIN '.db_prefix().'pur_orders ON '.db_prefix().'pur_orders.id = '.db_prefix().'pur_contracts.pur_order',
                    'LEFT JOIN '.db_prefix().'departments ON '.db_prefix().'departments.departmentid = '.db_prefix().'pur_contracts.department',
                    'LEFT JOIN '.db_prefix().'projects ON '.db_prefix().'projects.id = '.db_prefix().'pur_contracts.project',
                ];
$where = [];

$base_currency = get_base_currency_pur();

if(isset($vendor)){
    array_push($where, ' AND '.db_prefix().'pur_contracts.vendor = '.$vendor);
}

if(isset($project)){
    array_push($where, ' AND '.db_prefix().'pur_contracts.project = '.$project);
}

if($this->ci->input->post('vendor')){
    $vendors = $this->ci->input->post('vendor');
    $where_vendors = '';
    foreach ($vendors as $ven) {
        if ($ven != '') {
            if ($where_vendors == '') {
                $where_vendors .= ' AND (tblpur_contracts.vendor = "' . $ven . '"';
            } else {
                $where_vendors .= ' or tblpur_contracts.vendor = "' . $ven . '"';
            }
        }
    }
    if ($where_vendors != '') {
        $where_vendors .= ')';
        array_push($where, $where_vendors);
    }
}

if($this->ci->input->post('department')){
    $departments = $this->ci->input->post('department');
    $where_departments = '';
    foreach ($departments as $ven) {
        if ($ven != '') {
            if ($where_departments == '') {
                $where_departments .= ' AND (tblpur_contracts.department = "' . $ven . '"';
            } else {
                $where_departments .= ' or tblpur_contracts.department = "' . $ven . '"';
            }
        }
    }
    if ($where_departments != '') {
        $where_departments .= ')';
        array_push($where, $where_departments);
    }
}

if($this->ci->input->post('project')){
    $projects = $this->ci->input->post('project');
    $where_projects = '';
    foreach ($projects as $ven) {
        if ($ven != '') {
            if ($where_projects == '') {
                $where_projects .= ' AND (tblpur_contracts.project = "' . $ven . '"';
            } else {
                $where_projects .= ' or tblpur_contracts.project = "' . $ven . '"';
            }
        }
    }
    if ($where_projects != '') {
        $where_projects .= ')';
        array_push($where, $where_projects);
    }
}

if(!has_permission('purchase_contracts', '', 'view')){
    array_push($where, 'AND (' . db_prefix() . 'pur_contracts.add_from = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_contracts.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'pur_contracts.id as contract_id','contract_number','pur_order_number','pur_order_name', db_prefix().'departments.name as department_name', db_prefix().'projects.name as project_name']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

   for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == db_prefix().'pur_contracts.vendor'){
            $ven = get_vendor_company_name($aRow[db_prefix().'pur_contracts.vendor']);
            $_data = '<a href="' . admin_url('purchase/vendor/' . $aRow[db_prefix().'pur_contracts.vendor']) . '" >' .  $ven . '</a>';
        }elseif($aColumns[$i] == 'contract_name'){
            $numberOutput = '';
            $numberOutput = '<a href="' . admin_url('purchase/contract/' . $aRow['contract_id']) . '">' . $aRow['contract_number'].' - '. $aRow['contract_name'] . '</a>';
    
            $numberOutput .= '<div class="row-options">';

            if (has_permission('purchase_contracts', '', 'view') || has_permission('purchase_contracts', '', 'view_own')) {
                $numberOutput .= ' <a href="' . admin_url('purchase/contract/' . $aRow['contract_id']) . '" >' . _l('view') . '</a>';
            }
            if (has_permission('purchase_contracts', '', 'edit')) {
                $numberOutput .= ' | <a href="' . admin_url('purchase/contract/' . $aRow['contract_id']) . '">' . _l('edit') . '</a>';
            }
            if (has_permission('purchase_contracts', '', 'delete')) {
                $numberOutput .= ' | <a href="' . admin_url('purchase/delete_contract/' . $aRow['contract_id']) . '" class="text-danger">' . _l('delete') . '</a>';
            }
            $numberOutput .= '</div>';

            $_data = $numberOutput;

        }elseif($aColumns[$i] == db_prefix().'pur_contracts.start_date'){
            $_data = _d($aRow[db_prefix().'pur_contracts.start_date']);
        }elseif($aColumns[$i] == db_prefix().'pur_contracts.end_date'){
            $_data = _d($aRow[db_prefix().'pur_contracts.end_date']);
        }elseif($aColumns[$i] == 'add_from'){
            $status = '';
            if($aRow[db_prefix().'pur_contracts.end_date'] >= date('Y-m-d')){
                $status = '<span class="label label-success">'._l('valid').'</span>';
            }else{
                $status = '<span class="label label-danger">'._l('expired').'</span>';
            }
            $_data = $status;
        }elseif($aColumns[$i] == 'contract_value'){
            $_data = app_format_money($aRow['contract_value'],$base_currency->symbol);
        }elseif($aColumns[$i] == 'payment_amount'){
            $_data = app_format_money($aRow['payment_amount'],$base_currency->symbol);
        }elseif($aColumns[$i] == 'payment_cycle'){
            $_data = _l($aRow['payment_cycle'],'');
        }elseif($aColumns[$i] == db_prefix().'pur_contracts.department'){
            $_data = $aRow['department_name'];
        }elseif($aColumns[$i] == db_prefix().'pur_contracts.project'){
            $_data = $aRow['project_name'];
        }
    
        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
