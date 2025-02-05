<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'number',
    'date',
    get_sql_select_vendor_company(),
    db_prefix() . 'pur_debit_notes.status as status',
    'reference_no',
    'total',
    '(SELECT ' . db_prefix() . 'pur_debit_notes.total - (
      (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'pur_debits WHERE ' . db_prefix() . 'pur_debits.debit_id=' . db_prefix() . 'pur_debit_notes.id)
      +
      (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'pur_debits_refunds WHERE ' . db_prefix() . 'pur_debits_refunds.debit_note_id=' . db_prefix() . 'pur_debit_notes.id)
      )
    ) as remaining_amount',
    ];

$join = [
    'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_debit_notes.vendorid',
    'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'pur_debit_notes.currency',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'pur_debit_notes';


$where  = [];
$filter = [];

/*if ($vendorid != '') {
    array_push($where, 'AND ' . db_prefix() . 'pur_debit_notes.vendorid=' . $this->ci->db->escape_str($vendorid));
}
*/
if (!has_permission('purchase_debit_notes', '', 'view')) {
    array_push($where, 'AND (' . db_prefix() . 'pur_debit_notes.addedfrom = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_debit_notes.vendorid IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
}

$this->ci->load->model('purchase/purchase_model');
$statuses  = $this->ci->purchase_model->get_debit_note_statuses();
$statusIds = [];

foreach ($statuses as $status) {
    if ($this->ci->input->post('debit_notes_status_' . $status['id'])) {
        array_push($statusIds, $status['id']);
    }
}

if (count($statusIds) > 0) {
    array_push($filter, 'AND ' . db_prefix() . 'pur_debit_notes.status IN (' . implode(', ', $statusIds) . ')');
}

$years      = $this->ci->purchase_model->get_debits_years();
$yearsArray = [];

foreach ($years as $year) {
    if ($this->ci->input->post('year_' . $year['year'])) {
        array_push($yearsArray, $year['year']);
    }
}

if (count($yearsArray) > 0) {
    array_push($filter, 'AND YEAR(date) IN (' . implode(', ', $yearsArray) . ')');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

if(isset($vendor)){
    array_push($where, ' AND '.db_prefix().'pur_debit_notes.vendorid = '.$vendor);
}



$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix() . 'pur_debit_notes.id',
    db_prefix() . 'pur_debit_notes.vendorid',
    db_prefix(). 'currencies.name as currency_name',
    'deleted_vendor_name',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $numberOutput = '';
    // If is from client area table
  
    $numberOutput = '<a href="' . admin_url('purchase/debit_notes/' . $aRow['id']) . '" onclick="init_debit_note(' . $aRow['id'] . '); return false;">' . format_debit_note_number($aRow['id']) . '</a>';
    

    $numberOutput .= '<div class="row-options">';

    if (has_permission('purchase_debit_notes', '', 'edit')) {
        $numberOutput .= '<a href="' . admin_url('purchase/debit_note/' . $aRow['id']) . '">' . _l('edit') . '</a>';
    }
    $numberOutput .= '</div>';

    $row[] = $numberOutput;

    $row[] = _d($aRow['date']);

    if (empty($aRow['deleted_customer_name'])) {
        $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendorid']) . '">' . $aRow['company'] . '</a>';
    } else {
        $row[] = $aRow['deleted_customer_name'];
    }

    $row[] = format_credit_note_status($aRow['status']);


    $row[] = $aRow['reference_no'];

    $row[] = app_format_money($aRow['total'], $aRow['currency_name']);

    $row[] = app_format_money($aRow['remaining_amount'], $aRow['currency_name']);


    $output['aaData'][] = $row;
}

echo json_encode($output);
die();
