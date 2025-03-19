<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'id',
	'manufacturing_order_code',
	'product_id',
	'bom_id',
	'product_qty',
	'unit_id',
	'routing_id',
	'status',

];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'mrp_manufacturing_orders';

$where = [];
$join= [];

$products_filter = $this->ci->input->post('products_filter');
$routing_filter = $this->ci->input->post('routing_filter');
$status_filter = $this->ci->input->post('status_filter');

if (isset($products_filter)) {
	$where_products_ft = '';
	foreach ($products_filter as $product_id) {
		if ($product_id != '') {
			if ($where_products_ft == '') {
				$where_products_ft .= 'AND ('.db_prefix().'mrp_manufacturing_orders.product_id = "' . $product_id . '"';
			} else {
				$where_products_ft .= ' or '.db_prefix().'mrp_manufacturing_orders.product_id = "' . $product_id . '"';
			}
		}
	}
	if ($where_products_ft != '') {
		$where_products_ft .= ')';
		array_push($where, $where_products_ft);
	}
}

if (isset($routing_filter)) {
	$where_routing_ft = '';
	foreach ($routing_filter as $routing_id) {
		if ($routing_id != '') {
			if ($where_routing_ft == '') {
				$where_routing_ft .= 'AND ('.db_prefix().'mrp_manufacturing_orders.routing_id = "' . $routing_id . '"';
			} else {
				$where_routing_ft .= ' or '.db_prefix().'mrp_manufacturing_orders.routing_id = "' . $routing_id . '"';
			}
		}
	}
	if ($where_routing_ft != '') {
		$where_routing_ft .= ')';
		array_push($where, $where_routing_ft);
	}
}

if (isset($status_filter)) {
	$where_status_ft = '';
	foreach ($status_filter as $status) {
		if ($status != '') {
			if ($where_status_ft == '') {
				$where_status_ft .= 'AND ('.db_prefix().'mrp_manufacturing_orders.status = "' . $status . '"';
			} else {
				$where_status_ft .= ' or '.db_prefix().'mrp_manufacturing_orders.status = "' . $status . '"';
			}
		}
	}
	if ($where_status_ft != '') {
		$where_status_ft .= ')';
		array_push($where, $where_status_ft);
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

		}elseif ($aColumns[$i] == 'manufacturing_order_code') {
			$code = '<a href="' . admin_url('manufacturing/view_manufacturing_order/' . $aRow['id']) . '">' . $aRow['manufacturing_order_code'] . '</a>';
			$code .= '<div class="row-options">';

			$code .= '<a href="' . admin_url('manufacturing/view_manufacturing_order/' . $aRow['id']) . '" >' . _l('view') . '</a>';

			if (has_permission('manufacturing', '', 'edit') || is_admin()) {

				$code .= ' | <a href="' . admin_url('manufacturing/add_edit_manufacturing_order/' . $aRow['id']) . '" >' . _l('edit') . '</a>';
			}
			if (has_permission('manufacturing', '', 'delete') || is_admin()) {
				$code .= ' | <a href="' . admin_url('manufacturing/delete_manufacturing_order/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
			}
			$code .= '</div>';

			$_data = $code;


		}elseif($aColumns[$i] == 'product_id'){
			$_data =  mrp_get_product_name($aRow['product_id']);

		}elseif($aColumns[$i] == 'bom_id'){

			$_data =  mrp_get_bill_of_material_code($aRow['bom_id']).' '.mrp_get_product_name(mrp_get_bill_of_material($aRow['bom_id']));

		}elseif($aColumns[$i] == 'product_qty'){

			$_data =  app_format_money($aRow['product_qty'], '');

		}elseif($aColumns[$i] == 'unit_id'){

			$_data =  mrp_get_unit_name($aRow['unit_id']);

		}elseif($aColumns[$i] == 'routing_id'){

			$_data =  mrp_get_routing_name($aRow['routing_id']);

		}elseif($aColumns[$i] == 'status'){

			$_data = ' <span class="label label-'.$aRow['status'].'" > '._l($aRow['status']).' </span>';

		}


		$row[] = $_data;
	}

	$output['aaData'][] = $row;
}

