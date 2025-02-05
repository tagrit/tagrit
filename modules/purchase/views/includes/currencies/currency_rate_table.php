<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
 'from_currency_rate',
 'to_currency_rate',
 'date_updated',
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'currency_rates';
$join = [];
$where = [];
if ($this->ci->input->post('from_currency')) {
    $from_currency = $this->ci->input->post('from_currency');
    array_push($where, 'AND from_currency_id IN (' . implode(', ', $from_currency) . ')');
}

if ($this->ci->input->post('to_currency')) {
    $to_currency = $this->ci->input->post('to_currency');
    array_push($where, 'AND to_currency_id IN (' . implode(', ', $to_currency) . ')');
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['from_currency_id', 'to_currency_id', 'id', 'from_currency_name', 'to_currency_name']);

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];  

    $_data = '';
    $_data .= '<div class="row-options">';
    $_data .= '<a href="javascript:void(0)" onclick="edit_currency_rate_modal('.$aRow['id'].'); return false;" class="text-primary">' . _l('edit') . '</a>';
    $_data .= ' | <a href="'.admin_url('purchase/delete_currency_rate/'.$aRow['id'].'').'" class="text-danger _delete">' . _l('delete') . '</a>';
    $_data .= '</div>'; 

    $row[] = $aRow['from_currency_name'].' '._l('pur_to').' '.$aRow['to_currency_name'].$_data;  

    $row[] = $aRow['to_currency_rate'];  
    $row[] = _dt($aRow['date_updated']);   
 
   $output['aaData'][] = $row;
}