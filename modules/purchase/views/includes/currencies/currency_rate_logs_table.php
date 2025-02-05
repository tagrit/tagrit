<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
 'from_currency_rate',
 'to_currency_rate',
 'date',
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'currency_rate_logs';
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

if ($this->ci->input->post('date')) {
    $date = to_sql_date($this->ci->input->post('date'));
    array_push($where, 'AND date = "'. $date . '"');

}
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['from_currency_id', 'to_currency_id', 'id', 'from_currency_name', 'to_currency_name']);

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];  


    $row[] = $aRow['from_currency_name'].' '._l('pur_to').' '.$aRow['to_currency_name'];  

    $row[] = $aRow['to_currency_rate'];  
    $row[] = _d($aRow['date']);   
 
   $output['aaData'][] = $row;
}