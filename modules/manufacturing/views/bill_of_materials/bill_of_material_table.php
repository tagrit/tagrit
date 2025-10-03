<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'id',
	'product_id',
	'bom_code',
	'bom_type',
	'product_variant_id',
	'product_qty',
	'unit_id',
	'routing_id',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'mrp_bill_of_materials';

$where = [];
$join= [];

$products_filter = $this->ci->input->post('products_filter');
$bom_type_filter = $this->ci->input->post('bom_type_filter');
$routing_filter = $this->ci->input->post('routing_filter');

if (isset($products_filter)) {

	$products = $this->ci->manufacturing_model->bom_get_product_filter($products_filter);

	$where_products_filter = '';
	foreach ($products as $product) {
		if ($where_products_filter == '') {

			if(isset($product['parent_id']) && $product['parent_id'] != 0){
				$where_products_filter .= "AND ( ( (product_id = ".$product['parent_id']." AND product_variant_id = ".$product['id'].") OR( product_id = ".$product['parent_id']." AND (product_variant_id = 0 OR product_variant_id is null))) "; 
			}else{
				$where_products_filter .= "AND ( product_id = ".$product['id'];
			}

		} else {
			if(isset($product['parent_id']) && $product['parent_id'] != 0){
				$where_products_filter .= " OR  ( (product_id = ".$product['parent_id']." AND product_variant_id = ".$product['id'].") OR( product_id = ".$product['parent_id']." AND (product_variant_id = 0 OR product_variant_id is null))) "; 
			}else{
				$where_products_filter .= " OR  product_id = ".$product['id'];
			}

		}
	}

	if ($where_products_filter != '') {
		$where_products_filter .= ')';

		array_push($where, $where_products_filter);
	}
}

if (isset($bom_type_filter)) {
	$where_bom_type_filter = '';
	foreach ($bom_type_filter as $bom_type) {
		if ($bom_type != '') {
			if ($where_bom_type_filter == '') {
				$where_bom_type_filter .= 'AND ('.db_prefix().'mrp_bill_of_materials.bom_type = "' . $bom_type . '"';
			} else {
				$where_bom_type_filter .= ' or '.db_prefix().'mrp_bill_of_materials.bom_type = "' . $bom_type . '"';
			}
		}
	}
	if ($where_bom_type_filter != '') {
		$where_bom_type_filter .= ')';
		array_push($where, $where_bom_type_filter);
	}
}

if (isset($routing_filter)) {
	$where_routing_filter = '';
	foreach ($routing_filter as $routing_id) {
		if ($routing_id != '') {
			if ($where_routing_filter == '') {
				$where_routing_filter .= 'AND ('.db_prefix().'mrp_bill_of_materials.routing_id = "' . $routing_id . '"';
			} else {
				$where_routing_filter .= ' or '.db_prefix().'mrp_bill_of_materials.routing_id = "' . $routing_id . '"';
			}
		}
	}
	if ($where_routing_filter != '') {
		$where_routing_filter .= ')';
		array_push($where, $where_routing_filter);
	}
}



$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

	for ($i = 0; $i < count($aColumns); $i++) {

		if($aColumns[$i] == 'id') {
			$_data = $aRow['id'];

		}elseif ($aColumns[$i] == 'product_id') {
			$code = '<a href="' . admin_url('manufacturing/bill_of_material_detail_manage/' . $aRow['id']) . '">' . mrp_get_product_name($aRow['product_id']) . '</a>';
			$code .= '<div class="row-options">';

			$code .= '<a href="' . admin_url('manufacturing/bill_of_material_detail_manage/' . $aRow['id']) . '" >' . _l('view') . '</a>';

			if (has_permission('manufacturing', '', 'edit') || is_admin()) {

				$code .= ' | <a href="' . admin_url('manufacturing/bill_of_material_detail_manage/' . $aRow['id']) . '" >' . _l('edit') . '</a>';
			}
			if (has_permission('manufacturing', '', 'delete') || is_admin()) {
				$code .= ' | <a href="' . admin_url('manufacturing/delete_bill_of_material/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
			}
			$code .= '</div>';

			$_data = $code;


		}elseif($aColumns[$i] == 'bom_code'){
			$_data =  $aRow['bom_code'];
		}elseif($aColumns[$i] == 'bom_type'){
			$_data =  _l($aRow['bom_type']);

		}elseif($aColumns[$i] == 'product_variant_id'){

			$_data =  mrp_get_product_name($aRow['product_variant_id']);

		}elseif($aColumns[$i] == 'product_qty'){

			$_data =  app_format_money($aRow['product_qty'], '');

		}elseif($aColumns[$i] == 'unit_id'){

			$_data =  mrp_get_unit_name($aRow['unit_id']);

		}elseif($aColumns[$i] == 'routing_id'){

			$_data =  mrp_get_routing_name($aRow['routing_id']);

		}


		$row[] = $_data;
	}

	$output['aaData'][] = $row;
}

