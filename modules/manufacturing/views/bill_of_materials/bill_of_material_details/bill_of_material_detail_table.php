<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	db_prefix().'mrp_bill_of_material_details.id as id',
	db_prefix().'mrp_bill_of_material_details.display_order as display_order',
	'product_id',
	'product_qty',
	db_prefix().'mrp_bill_of_material_details.unit_id as unit_id',
	'apply_on_variants',
	'operation_id',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'mrp_bill_of_material_details';

$where = [];

$join = [
	'LEFT JOIN ' . db_prefix() . 'items ON '.db_prefix().'items.id = ' . db_prefix() . 'mrp_bill_of_material_details.product_id',
	'LEFT JOIN ' . db_prefix() . 'ware_unit_type ON '.db_prefix().'ware_unit_type.unit_type_id = ' . db_prefix() . 'mrp_bill_of_material_details.unit_id',
	'LEFT JOIN ' . db_prefix() . 'mrp_routing_details ON ' . db_prefix() . 'mrp_routing_details.routing_id = ' . db_prefix() . 'mrp_bill_of_material_details.operation_id'
];


$bill_of_material_id = $this->ci->input->post('bill_of_material_id');
$bill_of_material_product_id = $this->ci->input->post('bill_of_material_product_id');
$bill_of_material_routing_id = $this->ci->input->post('bill_of_material_routing_id');

if($this->ci->input->post('bill_of_material_id')){
	$where_bill_of_material_id = '';
	$bill_of_material_id = $this->ci->input->post('bill_of_material_id');
	if($bill_of_material_id != '')
	{
		if($where_bill_of_material_id == ''){
			$where_bill_of_material_id .= 'AND bill_of_material_id = "'.$bill_of_material_id. '"';
		}else{
			$where_bill_of_material_id .= ' or bill_of_material_id = "' .$bill_of_material_id.'"';
		}
	}
	if($where_bill_of_material_id != '')
	{
		array_push($where, $where_bill_of_material_id);
	}
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'mrp_bill_of_material_details.id','bill_of_material_id','product_id','product_qty',db_prefix().'mrp_bill_of_material_details.unit_id','apply_on_variants','operation_id', db_prefix().'mrp_bill_of_material_details.display_order', db_prefix().'items.description', db_prefix().'ware_unit_type.unit_name', db_prefix().'mrp_routing_details.operation' ]);

$output = $result['output'];
$rResult = $result['rResult'];
$bill_of_material_detail_id = [];

foreach ($rResult as $aRow) {
	if(in_array($aRow['id'], $bill_of_material_detail_id)){
		$output['iTotalRecords']--;
		$output['iTotalDisplayRecords']--;
		continue;
	}

	$bill_of_material_detail_id[] = $aRow['id'];
	$row = [];
	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

	for ($i = 0; $i < count($aColumns); $i++) {

		if($aColumns[$i] == db_prefix().'mrp_bill_of_material_details.id as id') {
			$_data = $aRow['id'];

		}elseif($aColumns[$i] == db_prefix().'mrp_bill_of_material_details.display_order as display_order'){
			$_data = round($aRow['display_order'],0);

		}elseif ($aColumns[$i] == 'product_id') {

			$code = mrp_get_product_name($aRow['product_id']) ;
			$code .= '<div class="row-options">';

			if (has_permission('manufacturing', '', 'edit') || is_admin()) {

				$code .= ' <a href="#" onclick="add_component('. $bill_of_material_id .','. $aRow['id'].','. $bill_of_material_product_id.','. $bill_of_material_routing_id .',\'updated\'); return false;" >' . _l('edit') . '</a>';
			}
			if (has_permission('manufacturing', '', 'delete') || is_admin()) {
				$code .= ' | <a href="' . admin_url('manufacturing/delete_bill_of_material_detail/' . $aRow['id']) . '/'.$bill_of_material_id.'" class="text-danger _delete">' . _l('delete') . '</a>';
			}
			$code .= '</div>';

			$_data = $code;


		}elseif($aColumns[$i] == 'product_qty'){
			$_data =  $aRow['product_qty'];

		}elseif($aColumns[$i] == db_prefix().'mrp_bill_of_material_details.unit_id as unit_id'){
			$_data = mrp_get_unit_name($aRow['unit_id']);

		}elseif($aColumns[$i] == 'apply_on_variants'){
			
			$_data =  $aRow['apply_on_variants'];

		}elseif($aColumns[$i] == 'operation_id'){
			$_data =  mrp_get_routing_detail_name($aRow['operation_id']);

		}


		$row[] = $_data;
	}

	$output['aaData'][] = $row;
}

