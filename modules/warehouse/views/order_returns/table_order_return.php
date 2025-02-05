<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'id',
	'order_return_name',
	'company_id',
	'total_amount',
	'discount_total',
	'total_after_discount',
	'datecreated',
	'receipt_delivery_type',
	'approval',
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'wh_order_returns';
$join         = [ ];

$where = [];

if ($this->ci->input->post('from_date')) {
	array_push($where, "AND date_format(datecreated, '%Y-%m-%d') >= '" . date('Y-m-d', strtotime(to_sql_date($this->ci->input->post('from_date')))) . "'");
}
if ($this->ci->input->post('to_date')) {
	array_push($where, "AND date_format(datecreated, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime(to_sql_date($this->ci->input->post('to_date')))) . "'");
}
if ($this->ci->input->post('staff_id') && $this->ci->input->post('staff_id') != '') {
	array_push($where, 'AND staff_id IN (' . implode(', ', $this->ci->input->post('staff_id')) . ')');
}

array_push($where, 'AND rel_type IN ("manual","i_sales_return_order","i_purchasing_return_order")');


if ($this->ci->input->post('delivery_id') && $this->ci->input->post('delivery_id') != '') {
	array_push($where, 'AND delivery_note_id IN (' . implode(', ', $this->ci->input->post('delivery_id')) . ')');
}

if ($this->ci->input->post('status_id') && $this->ci->input->post('status_id') != '') {
	$status_arr = $this->ci->input->post('status_id');
	if(in_array(5, $this->ci->input->post('status_id'))){
		$status_arr[] = 0;
	}
	array_push($where, 'AND approval IN (' . implode(', ', $status_arr) . ')');

}
if ($this->ci->input->post('receipt_delivery_type') && $this->ci->input->post('receipt_delivery_type') != '') {
	$receipt_delivery_arr = $this->ci->input->post('receipt_delivery_type');

	array_push($where, 'AND receipt_delivery_type IN ("' . implode('","', $receipt_delivery_arr) . '")');
}

if (!has_permission('wh_receipt_return_order', '', 'view')) {
    array_push($where, 'AND (' . db_prefix() . 'wh_order_returns.staff_id=' . get_staff_user_id().')');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', 'order_return_name', 'additional_discount', 'approval', 'return_type', 'rel_id', 'rel_type', 'order_return_number', 'receipt_delivery_id']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	$row[] = $aRow['id'];

		$name = '<a href="' . admin_url('warehouse/view_order_return/' . $aRow['id'] ).'" onclick="init_order_return('.$aRow['id'].'); return false;">' . $aRow['order_return_number'] .' - '.$aRow['order_return_name']. '</a>';

		$name .= '<div class="row-options">';
		$name .= '<a href="' . admin_url('warehouse/view_order_return/' . $aRow['id'] ).'" onclick="init_order_return('.$aRow['id'].'); return false;">' . _l('view') . '</a>';

		if((has_permission('wh_receipt_return_order', '', 'edit') || is_admin()) && ($aRow['approval'] == 0)){
			$name .= ' | <a href="' . admin_url('warehouse/order_return/'.$aRow['rel_type']. '/' . $aRow['id'] ).'" >' . _l('edit') . '</a>';
		}

		if ((has_permission('wh_receipt_return_order', '', 'delete') || is_admin()) && ($aRow['approval'] == 0)) {
			$name .= ' | <a href="' . admin_url('warehouse/delete_order_return/' . $aRow['id'] ).'" class="text-danger _delete" >' . _l('delete') . '</a>';
		}			

		$name .= '</div>';

	$row[] = $name;
	
	if($aRow['rel_type'] == 'sales_return_order' || $aRow['rel_type'] == 'manual'){
		$row[] = get_company_name($aRow['company_id']);
	}else{
		$row[] = wh_get_vendor_company_name($aRow['company_id']);
	}

	$row[] = app_format_money($aRow['total_amount'], '');
	$row[] = app_format_money($aRow['discount_total'], '');
	$row[] = app_format_money($aRow['total_after_discount'], '');
	$row[] = _dt($aRow['datecreated']);

	$receipt_delivery_type_data = '';
	if($aRow['receipt_delivery_type'] == 'inventory_receipt_voucher_returned_goods'){
		$receipt_delivery_type_data = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'._l('wh_inventory_receipt_voucher_returned_goods').'</span><span class="hide">, </span></span>&nbsp';
	}elseif($aRow['receipt_delivery_type'] == 'inventory_delivery_voucher_returned_purchasing_goods'){
		$receipt_delivery_type_data = '<span class="label label-tag tag-id-1 label-tab4"><span class="tag">'._l('wh_inventory_delivery_voucher_returned_purchasing_goods').'</span><span class="hide">, </span></span>&nbsp';
	}
	$row[] = $receipt_delivery_type_data;

	$approve_data = '';
	if($aRow['approval'] == 1){
		$approve_data = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'._l('approved').'</span><span class="hide">, </span></span>&nbsp';
	}elseif($aRow['approval'] == 0){
		$approve_data = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'._l('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
	}elseif($aRow['approval'] == -1){
		$approve_data = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'._l('reject').'</span><span class="hide">, </span></span>&nbsp';
	}

	$row[] = $approve_data;

	$option = '';

	
	if($aRow['receipt_delivery_id'] != 0){
		if(($aRow['rel_type'] == 'manual' && $aRow['receipt_delivery_type'] == 'inventory_receipt_voucher_returned_goods') || $aRow['rel_type'] == 'i_sales_return_order'){
			$option .= icon_btn('warehouse/manage_purchase#' . $aRow['receipt_delivery_id'], 'fa-solid fa-eye', 'btn-primary', ['data-original-title' => _l('goods_receipt'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']);

		}elseif(($aRow['rel_type'] == 'manual' && $aRow['receipt_delivery_type'] == 'inventory_delivery_voucher_returned_purchasing_goods') || $aRow['rel_type'] == 'i_purchasing_return_order'){
			$option .= icon_btn('warehouse/manage_delivery#' . $aRow['receipt_delivery_id'], 'fa-solid fa-eye', 'btn-primary', ['data-original-title' => _l('stock_export'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']);
		}
	}

	$row[] = $option;
	
	$output['aaData'][] = $row;

}
