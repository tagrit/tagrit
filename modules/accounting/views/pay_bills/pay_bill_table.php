<?php

defined('BASEPATH') or exit('No direct script access allowed');
	$currency = get_base_currency();

$aColumns = [
	'id',
	'pay_number',
	'reference_no',
	db_prefix() . 'pur_vendor.company as vendor_name',
	'date',
	'amount',
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'acc_pay_bills';
$join         = [ ];
$join = [
    'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'acc_pay_bills.vendor',
];

$where = [];

if ($this->ci->input->post('vendor_id') && $this->ci->input->post('vendor_id') != '') {
    $vendor_id = $this->ci->input->post('vendor_id');
   
    array_push($where, 'AND '.db_prefix().'pur_vendor.userid IN (' . implode(', ', $vendor_id) . ')');
}

$from_date = '';
$to_date = '';
if ($this->ci->input->post('from_date')) {
    $from_date = $this->ci->input->post('from_date');
    if (!$this->ci->accounting_model->check_format_date($from_date)) {
        $from_date = to_sql_date($from_date);
    }
}

if ($this->ci->input->post('to_date')) {
    $to_date = $this->ci->input->post('to_date');
    if (!$this->ci->accounting_model->check_format_date($to_date)) {
        $to_date = to_sql_date($to_date);
    }
}
if ($from_date != '' && $to_date != '') {
    array_push($where, 'AND (' . db_prefix() . 'acc_pay_bills.date >= "' . $from_date . '" and ' . db_prefix() . 'acc_pay_bills.date <= "' . $to_date . '")');
} elseif ($from_date != '') {
    array_push($where, 'AND (' . db_prefix() . 'acc_pay_bills.date >= "' . $from_date . '")');
} elseif ($to_date != '') {
    array_push($where, 'AND (' . db_prefix() . 'acc_pay_bills.date <= "' . $to_date . '")');
}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	$row[] = $aRow['id'];

	$row[] = $aRow['pay_number'];
	$row[] = $aRow['reference_no'];
	$row[] = $aRow['vendor_name'];
	$row[] = _d($aRow['date']);
	$row[] = app_format_money($aRow['amount'], $currency->name);

	if(has_permission('accounting_bills','','edit')){
		$name = '<a href="' . admin_url('accounting/pay_bill/' . $aRow['id'] ).'" class="btn btn-default btn-icon"><i class="fa fa-pencil-square"></i></a>';
	}
	if(has_permission('accounting_bills','','delete')){
		$name .= '<a href="' . admin_url('accounting/delete_pay_bill/0/' . $aRow['id'] ).'" class="btn btn-default btn-icon _delete"><i class="fa fa-remove"></i></a>';
	}

	$row[] = $name ;

	
	$output['aaData'][] = $row;

}
