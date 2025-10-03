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
	'status',
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
if ($this->ci->input->post('vendors') && $this->ci->input->post('vendors') != '') {
	array_push($where, 'AND company_id IN (' . implode(', ', $this->ci->input->post('vendors')) . ')');
}
if ($this->ci->input->post('status') && $this->ci->input->post('status') != '') {
	array_push($where, 'AND status = "'.$this->ci->input->post('status').'"' );
}

if(!has_permission('purchase_order_return', '', 'view')){
	array_push($where, 'AND (' . db_prefix() . 'wh_order_returns.staff_id = '.get_staff_user_id().' OR ' . db_prefix() . 'wh_order_returns.company_id IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
}

array_push($where, 'AND rel_type = "purchasing_return_order"');

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', 'order_return_name', 'additional_discount', 'approval', 'return_type', 'rel_id', 'rel_type', 'order_return_number', 'receipt_delivery_id']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	$row[] = $aRow['id'];

		$name = '<a href="' . admin_url('purchase/view_order_return/' . $aRow['id'] ).'" onclick="init_order_return('.$aRow['id'].'); return false;">' . $aRow['order_return_number'] .' - '.$aRow['order_return_name']. '</a>';

		$name .= '<div class="row-options">';
		$name .= '<a href="' . admin_url('purchase/view_order_return/' . $aRow['id'] ).'" onclick="init_order_return('.$aRow['id'].'); return false;">' . _l('view') . '</a>';

		/*if((has_permission('purchase_order_return', '', 'edit') || is_admin()) && ($aRow['approval'] == 0)){
			$name .= ' | <a href="' . admin_url('purchase/order_return/'.$aRow['rel_type']. '/' . $aRow['id'] ).'" >' . _l('edit') . '</a>';
		}
*/
		if ((has_permission('purchase_order_return', '', 'delete') || is_admin()) && ($aRow['approval'] == 0)) {
			$name .= ' | <a href="' . admin_url('purchase/delete_order_return/' . $aRow['id'] ).'" class="text-danger _delete" >' . _l('delete') . '</a>';
		}			

		$name .= '</div>';

	$row[] = $name;
	
	
	$row[] = get_vendor_company_name($aRow['company_id']);
	


	$row[] = app_format_money($aRow['total_amount'], '');
	$row[] = app_format_money($aRow['discount_total'], '');
	$row[] = app_format_money($aRow['total_after_discount'], '');
	$row[] = _dt($aRow['datecreated']);

	$status = '<span class="label label-success">'._l('pur_'.$aRow['status']).'</span>';  

	$row[] = $status;

	$option = '';

	if(get_status_modules_pur('warehouse')){
		if($aRow['approval'] == 1 && $aRow['receipt_delivery_id'] == 0){
			if(has_permission('purchase_order_return', '', 'create') || has_permission('purchase_order_return', '', 'edit') || is_admin()){
				$option .= icon_btn('#', 'share', 'btn-success', ['data-original-title' => _l('wh_create_inventory_delivery_vocucher'), 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => 'open_warehouse_modal(this,' . $aRow['id'] . ')']);
			}
		}else{
			if($aRow['receipt_delivery_id'] != 0){
				if($aRow['rel_type'] == 'manual' || $aRow['rel_type'] = 'sales_return_order'){
					$option .= icon_btn('warehouse/manage_purchase#' . $aRow['receipt_delivery_id'], 'eye', 'btn-primary', ['data-original-title' => _l('goods_receipt'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']);

				}elseif($aRow['rel_type'] = 'purchasing_return_order'){
					$option .= icon_btn('warehouse/manage_delivery#' . $aRow['receipt_delivery_id'], 'eye', 'btn-primary', ['data-original-title' => _l('stock_export'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']);
				}
			}
		}
	}

	// $row[] = $option;
	
	$output['aaData'][] = $row;

}
