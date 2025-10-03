<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'id',
	'operation_name',
	'date_planned_start',
	'work_center_id',
	'manufacturing_order_id',
	'product_id',
	'qty_production',
	'unit_id',
	'status',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'mrp_work_orders';

$where = [];
$join= [];

$manufacturing_order_id = $this->ci->input->post('manufacturing_order_id');
if($this->ci->input->post('manufacturing_order_id')){
	$where_manufacturing_order_id = '';
	$manufacturing_order_id = $this->ci->input->post('manufacturing_order_id');
	if($manufacturing_order_id != '')
	{
		if($where_manufacturing_order_id == ''){
			$where_manufacturing_order_id .= 'AND manufacturing_order_id = "'.$manufacturing_order_id. '"';
		}else{
			$where_manufacturing_order_id .= ' or manufacturing_order_id = "' .$manufacturing_order_id.'"';
		}
	}
	if($where_manufacturing_order_id != '')
	{
		array_push($where, $where_manufacturing_order_id);
	}
}

$manufacturing_order_filter = $this->ci->input->post('manufacturing_order_filter');
if (isset($manufacturing_order_filter)) {
	$where_manufacturing_order_ft = '';
	foreach ($manufacturing_order_filter as $manufacturing_order) {
		if ($manufacturing_order != '') {
			if ($where_manufacturing_order_ft == '') {
				$where_manufacturing_order_ft .= 'AND ('.db_prefix().'mrp_work_orders.manufacturing_order_id = "' . $manufacturing_order . '"';
			} else {
				$where_manufacturing_order_ft .= ' or '.db_prefix().'mrp_work_orders.manufacturing_order_id = "' . $manufacturing_order . '"';
			}
		}
	}
	if ($where_manufacturing_order_ft != '') {
		$where_manufacturing_order_ft .= ')';
		array_push($where, $where_manufacturing_order_ft);
	}
}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	for ($i = 0; $i < count($aColumns); $i++) {

		if($aColumns[$i] == 'id') {
			$_data = $aRow['id'];

		}elseif ($aColumns[$i] == 'operation_name') {
			$code = '<a href="' . admin_url('manufacturing/view_work_order/' . $aRow['id'].'/'.$aRow['manufacturing_order_id']) . '">' . $aRow['operation_name'] . '</a>';
			$code .= '<div class="row-options">';

			$code .= '<a href="' . admin_url('manufacturing/view_work_order/' . $aRow['id']).'/'.$aRow['manufacturing_order_id'] . '" >' . _l('view') . '</a>';

			$code .= '</div>';

			$_data = $code;


		}elseif($aColumns[$i] == 'date_planned_start'){
			$_data = _dt($aRow['date_planned_start']);
		}elseif($aColumns[$i] == 'work_center_id'){
			$_data =  get_work_center_name($aRow['work_center_id']);

		}elseif($aColumns[$i] == 'manufacturing_order_id'){

			$_data =  mrp_get_manufacturing_code($aRow['manufacturing_order_id']);

		}elseif($aColumns[$i] == 'product_id'){

			$_data =  mrp_get_product_name($aRow['product_id']);

		}elseif($aColumns[$i] == 'qty_production'){
			$_data =  app_format_money($aRow['qty_production'],'');

		}elseif($aColumns[$i] == 'unit_id'){

			$_data =  mrp_get_unit_name($aRow['unit_id']);

		}elseif($aColumns[$i] == 'status'){

			$_data = ' <span class="label label-'.$aRow['status'].'" > '._l($aRow['status']).' </span>';

		}


		$row[] = $_data;
	}

	$output['aaData'][] = $row;
}

