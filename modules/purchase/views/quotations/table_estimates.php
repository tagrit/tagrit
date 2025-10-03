<?php

defined('BASEPATH') or exit('No direct script access allowed');


$aColumns = [
    db_prefix() . 'pur_estimates.number',
    db_prefix() . 'pur_estimates.total',
    db_prefix() . 'pur_estimates.total_tax',
    'YEAR(date) as year',
    'vendor',
    'pur_request',
    
    'date',
    'expirydate',

    db_prefix() . 'pur_estimates.status',
    ];

$join = [
    'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'pur_estimates.currency',
    'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_estimates.vendor',
    'LEFT JOIN ' . db_prefix() . 'pur_request ON ' . db_prefix() . 'pur_request.id = ' . db_prefix() . 'pur_estimates.pur_request',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'pur_estimates';


$where  = [];

$pur_request = $this->ci->input->post('pur_request');
if (isset($pur_request)) {
    $where_pur_request = '';
    foreach ($pur_request as $request) {
        if ($request != '') {
            if ($where_pur_request == '') {
                $where_pur_request .= ' AND (pur_request = "' . $request . '"';
            } else {
                $where_pur_request .= ' or pur_request = "' . $request . '"';
            }
        }
    }
    if ($where_pur_request != '') {
        $where_pur_request .= ')';
        array_push($where, $where_pur_request);
    }
}

$vendors = $this->ci->input->post('vendor');
if (isset($vendors)) {
    $where_vendor = '';
    foreach ($vendors as $ven) {
        if ($ven != '') {
            if ($where_vendor == '') {
                $where_vendor .= ' AND (vendor = ' . $ven . '';
            } else {
                $where_vendor .= ' or vendor = ' . $ven . '';
            }
        }
    }
    if ($where_vendor != '') {
        $where_vendor .= ')';
        array_push($where, $where_vendor);
    }
}

if(isset($vendor)){
    array_push($where, ' AND '.db_prefix().'pur_estimates.vendor = '.$vendor);
}

if(!has_permission('purchase_quotations', '', 'view')){
    array_push($where, 'AND (' . db_prefix() . 'pur_estimates.addedfrom = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_estimates.buyer = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_estimates.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . ') OR '.get_staff_user_id().' IN (SELECT staffid FROM ' . db_prefix() . 'pur_approval_details WHERE ' . db_prefix() . 'pur_approval_details.rel_type = "pur_quotation" AND ' . db_prefix() . 'pur_approval_details.rel_id = '.db_prefix().'pur_estimates.id))');
}

$filter = [];


$aColumns = hooks()->apply_filters('estimates_table_sql_columns', $aColumns);


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix() . 'pur_estimates.id',
    db_prefix() . 'pur_estimates.vendor',
    db_prefix() . 'pur_estimates.invoiceid',
    db_prefix() . 'currencies.name as currency_name',
    'pur_request',
    'deleted_vendor_name',
    db_prefix() . 'pur_estimates.currency',
    'company',
    'pur_rq_name',
    'pur_rq_code'
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $base_currency = get_base_currency_pur();

    if($aRow['currency'] != 0){
        $base_currency = pur_get_currency_by_id($aRow['currency']);
    }

    $numberOutput = '';
    // If is from client area table or projects area request
    
    $numberOutput = '<a href="' . admin_url('purchase/quotations/' . $aRow['id']) . '" onclick="init_pur_estimate(' . $aRow['id'] . '); return false;">' . format_pur_estimate_number($aRow['id']) . '</a>';

    

    $numberOutput .= '<div class="row-options">';

    if (has_permission('purchase_quotations', '', 'view') || has_permission('purchase_quotations', '', 'view_own')) {
        $numberOutput .= ' <a href="' . admin_url('purchase/quotations/' . $aRow['id']) . '" onclick="init_pur_estimate(' . $aRow['id'] . '); return false;">' . _l('view') . '</a>';
    }
    if ( (has_permission('purchase_quotations', '', 'edit') || is_admin()) && $aRow[db_prefix() . 'pur_estimates.status'] != 2) {
        $numberOutput .= ' | <a href="' . admin_url('purchase/estimate/' . $aRow['id']) . '">' . _l('edit') . '</a>';
    }
    if (has_permission('purchase_quotations', '', 'delete') || is_admin()) {
        $numberOutput .= ' | <a href="' . admin_url('purchase/delete_estimate/' . $aRow['id']) . '" class="text-danger">' . _l('delete') . '</a>';
    }
    $numberOutput .= '</div>';

    $row[] = $numberOutput;

    $amount = app_format_money($aRow[db_prefix() . 'pur_estimates.total'], $base_currency->symbol);

    if ($aRow['invoiceid']) {
        $amount .= '<br /><span class="hide"> - </span><span class="text-success">' . _l('estimate_invoiced') . '</span>';
    }

    $row[] = $amount;

    $row[] = app_format_money($aRow[db_prefix() . 'pur_estimates.total_tax'], $base_currency->symbol);

    $row[] = $aRow['year'];

    if (empty($aRow['deleted_vendor_name'])) {
        $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor']) . '" >' .  $aRow['company'] . '</a>';
    } else {
        $row[] = $aRow['deleted_vendor_name'];
    }

    $row[] = '<a href="' . admin_url('purchase/view_pur_request/' . $aRow['pur_request']) . '" onclick="init_pur_estimate(' . $aRow['id'] . '); return false;">' . $aRow['pur_rq_code'] .'</a>' ;

   

    $row[] = _d($aRow['date']);

    $row[] = _d($aRow['expirydate']);



    $row[] = get_status_approve($aRow[db_prefix() . 'pur_estimates.status']);


    $row['DT_RowClass'] = 'has-row-options';

    $row = hooks()->apply_filters('estimates_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}

echo json_encode($output);
die();
