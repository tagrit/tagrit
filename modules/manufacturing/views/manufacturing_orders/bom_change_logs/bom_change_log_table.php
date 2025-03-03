<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'id',
	'product_id',
	// 'parent_product_id',
	'change_type',
	'change_quantity',
	'created_at',
	'staff_id',
	'description',
	'rel_id',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'mrp_bom_changes_logs';

$where = [];

$join = [
];


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

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['manufacturing_order_id', 'unit_id', 'rel_type', 'id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	
	$row = [];

	for ($i = 0; $i < count($aColumns); $i++) {

		if($aColumns[$i] == 'id') {
			$_data = (int)$aRow['id'];

		}elseif ($aColumns[$i] == 'parent_product_id') {

			$code = mrp_get_product_name($aRow['parent_product_id']) ;
			$_data = $code;


		}elseif ($aColumns[$i] == 'product_id') {

			$code = mrp_get_product_name($aRow['product_id']) ;
			$code .= '<div class="row-options">';
			$code .= '<a href="' . admin_url('manufacturing/view_product_detail/' . $aRow['product_id']) . '">' . _l('view') . '</a>';
			$code .= '</div>';

			$_data = $code;


		}elseif($aColumns[$i] == 'change_type'){
			$_data =  _l($aRow['change_type']);

		}elseif($aColumns[$i] == 'change_quantity'){
			$unit_name = mrp_get_unit_name($aRow['unit_id']);
			$_data =  $aRow['change_quantity'].' '.$unit_name;

		}elseif($aColumns[$i] == 'created_at'){
			$_data =  _dt($aRow['created_at']);

		}elseif($aColumns[$i] == 'staff_id'){
			if(is_numeric($aRow['staff_id']) && $aRow['staff_id'] != 0){
				$_data =  get_staff_full_name($aRow['staff_id']);
			}else{
				$_data =  _l('mrp_system');
			}
		}elseif($aColumns[$i] == 'description'){
			$_data =  $aRow['description'];
			
		}elseif($aColumns[$i] == 'rel_id'){
			$rel_type_value = '';
			switch ($aRow['rel_type']) {
				case 'wo_order':
				$rel_type_value = '<a href="' . admin_url('manufacturing/view_work_order/'.$aRow['rel_id'].'/'.$manufacturing_order_id) . '" target="_blank">' . _l('work_order_label').': '.get_work_order_name($aRow['rel_id']) . '</a>';
					break;
				case 'receipt_note':
				$goods_receipt_code = '';
				$get_goods_receipt_code = get_goods_receipt_code($aRow['rel_id']);
				if($get_goods_receipt_code){
					$goods_receipt_code = $get_goods_receipt_code->goods_receipt_code;
				}

				$rel_type_value = '<a href="' . admin_url('warehouse/manage_purchase/'.$aRow['rel_id']) . '" target="_blank">' . _l('mrp_receipt_note').': '.$goods_receipt_code . '</a>';

					break;
				case 'delivery_note':
				$goods_delivery_code = '';
				$get_goods_delivery_code = get_goods_delivery_code($aRow['rel_id']);
				if($get_goods_delivery_code){
					$goods_delivery_code = $get_goods_delivery_code->goods_delivery_code;
				}

				$rel_type_value = '<a href="' . admin_url('warehouse/manage_delivery/'.$aRow['rel_id']) . '" target="_blank">' . _l('mrp_delivery_note').': '.$goods_delivery_code . '</a>';
					break;
				
				default:
					// code...
					break;
			}
			$_data =  $rel_type_value;
		}

		$row[] = $_data;
	}

	$output['aaData'][] = $row;
}

