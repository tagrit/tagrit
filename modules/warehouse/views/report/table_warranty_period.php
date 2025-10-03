<?php

defined('BASEPATH') or exit('No direct script access allowed');


$aColumns = [
	'goods_delivery_id',
	'commodity_code',
	db_prefix() . 'goods_delivery.customer_code as customer_code',
	'quantities',
	'unit_price', 
	'expiry_date',
	'lot_number',
	'serial_number',
	'guarantee_period',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'goods_delivery';

$where = [];

$commodity_filter = $this->ci->input->post('commodity_filter');
$customer_name_filter = $this->ci->input->post('customer_name_filter');
$to_date_filter = $this->ci->input->post('to_date_filter');
$status_filter = $this->ci->input->post('status_filter');


$join= [
	'LEFT JOIN ' . db_prefix() . 'goods_delivery_detail as gdd ON gdd.goods_delivery_id = ' . db_prefix() . 'goods_delivery.id'
];

$where[] = 'AND guarantee_period is not null AND guarantee_period != ""';

if (isset($commodity_filter)) {
	$where_commodity_ft = '';
	foreach ($commodity_filter as $commodity_id) {
		if ($commodity_id != '') {
			if ($where_commodity_ft == '') {
				$where_commodity_ft .= ' AND (commodity_code = "' . $commodity_id . '"';
			} else {
				$where_commodity_ft .= ' or commodity_code = "' . $commodity_id . '"';
			}
		}
	}
	if ($where_commodity_ft != '') {
		$where_commodity_ft .= ')';
		array_push($where, $where_commodity_ft);
	}
}

if ($this->ci->input->post('to_date_filter')) {
	array_push($where, "AND date_format(guarantee_period, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime(to_sql_date($this->ci->input->post('to_date_filter')))) . "'");
}

if (isset($customer_name_filter)) {
	$where_customer_ft = '';
	foreach ($customer_name_filter as $client_id) {
		if ($client_id != '') {
			if ($where_customer_ft == '') {
				$where_customer_ft .= ' AND ('.db_prefix().'goods_delivery.customer_code = "' . $client_id . '"';
			} else {
				$where_customer_ft .= ' or '.db_prefix().'goods_delivery.customer_code = "' . $client_id . '"';
			}
		}
	}
	if ($where_customer_ft != '') {
		$where_customer_ft .= ')';
		array_push($where, $where_customer_ft);
	}
}

if ($this->ci->input->post('status_filter') && $this->ci->input->post('status_filter') != '') {
	$status_arr = $this->ci->input->post('status_filter');
	$status_ft = '';

	foreach ($status_arr as $value) {
		if($value == 1){
			if ($status_ft == '') {
				$status_ft .= " AND ( date_format(guarantee_period, '%Y-%m-%d') > '" . date('Y-m-d', strtotime(date('Y-m-d'))) . "'";
			}else{
				$status_ft .= " OR date_format(guarantee_period, '%Y-%m-%d') > '" . date('Y-m-d', strtotime(date('Y-m-d'))) . "'";
			}
		}elseif($value == 2){
			if ($status_ft == '') {
				$status_ft .= " AND ( date_format(guarantee_period, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime(date('Y-m-d'))) . "'";
			}else{
				$status_ft .= " OR date_format(guarantee_period, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime(date('Y-m-d'))) . "'";
			}
		}
	}
	if ($status_ft != '') {
		$status_ft .= ')';
		array_push($where, $status_ft);
	}
}



$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['unit_id', 'commodity_name', 'customer_code']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	$text_color = '';
	if(strtotime($aRow['guarantee_period']) <= strtotime(date('Y-m-d'))){
		$text_color = 'text-danger';
	}

	$value = get_goods_delivery_code($aRow['goods_delivery_id']) != null ? get_goods_delivery_code($aRow['goods_delivery_id'])->goods_delivery_code : '';
	if($value != ''){
		$row[] = '<a href="' . admin_url('warehouse/manage_delivery/' . $aRow['goods_delivery_id']) . '" >'. $value.'</a>';
	}else{
		$row[] = '';
	}
	$row[] = get_company_name($aRow['customer_code']);

	if(new_strlen($aRow['commodity_name'] ?? '') == 0){
		$row[] = '<span class="'.$text_color.'">'. wh_get_item_variatiom($aRow['commodity_code']).'</span>';
	}else{
		$row[] = '<span class="'.$text_color.'">'.$aRow['commodity_name'].'</span>';
	}
	$row[] = '<span class="'.$text_color.'">'.$aRow['quantities'].' '.wh_get_unit_name($aRow['unit_id']).'</span>';
	$row[] = '<span class="'.$text_color.'">'.app_format_money((float)$aRow['unit_price'], '').'</span>';
	$row[] = '<span class="'.$text_color.'">'.$aRow['expiry_date'].'</span>';
	$row[] = '<span class="'.$text_color.'">'.$aRow['lot_number'].'</span>';
	$row[] = '<span class="'.$text_color.'">'.$aRow['serial_number'].'</span>';
	$row[] ='<span class="'.$text_color.'">'. _d($aRow['guarantee_period']).'</span>';

	$output['aaData'][] = $row;
}

