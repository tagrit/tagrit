<?php

defined('BASEPATH') or exit('No direct script access allowed');
$arr_inventory_min_data = $this->ci->warehouse_model->arr_inventory_min(false);
$filter_arr_inventory_min_max = $this->ci->warehouse_model->filter_arr_inventory_min_max();
$arr_inventory_min_id = $filter_arr_inventory_min_max['inventory_min'];
$arr_inventory_max_id = $filter_arr_inventory_min_max['inventory_max'];

$aColumns = [
	db_prefix().'wh_inventory_serial_numbers.id as id',
	'serial_number',
	db_prefix().'wh_inventory_serial_numbers.commodity_id',
	'purchase_price',
	'lot_number',
	'date_manufacture',
	'expiry_date',
	'2',
	db_prefix().'wh_inventory_serial_numbers.warehouse_id',
	'3',
	'is_used',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'wh_inventory_serial_numbers';


$where = [];

$warehouse_ft = $this->ci->input->post('warehouse_ft');
$commodity_ft = $this->ci->input->post('commodity_ft');
$show_serial_number_status_filter = $this->ci->input->post('show_serial_number_status_filter');


$join = [
	'LEFT JOIN ' . db_prefix() . 'inventory_manage t1 ON t1.id = ' . db_prefix() . 'wh_inventory_serial_numbers.inventory_manage_id'
];

if (isset($warehouse_ft)) {
	$where_warehouse_ft = '';
	foreach ($warehouse_ft as $warehouse_id) {
		if ($warehouse_id != '') {
			if ($where_warehouse_ft == '') {
				$where_warehouse_ft .= 'AND ('.db_prefix().'wh_inventory_serial_numbers.warehouse_id = "' . $warehouse_id . '"';
			} else {
				$where_warehouse_ft .= ' or '.db_prefix().'wh_inventory_serial_numbers.warehouse_id = "' . $warehouse_id . '"';
			}
		}
	}
	if ($where_warehouse_ft != '') {
		$where_warehouse_ft .= ')';
		array_push($where, $where_warehouse_ft);
	}
}

if (isset($commodity_ft)) {
	$where_commodity_ft = '';
	foreach ($commodity_ft as $commodity_id) {
		if ($commodity_id != '') {
			if ($where_commodity_ft == '') {
				$where_commodity_ft .= 'AND ('.db_prefix().'wh_inventory_serial_numbers.commodity_id = "' . $commodity_id . '"';
			} else {
				$where_commodity_ft .= ' or '.db_prefix().'wh_inventory_serial_numbers.commodity_id = "' . $commodity_id . '"';
			}
		}
	}
	if ($where_commodity_ft != '') {
		$where_commodity_ft .= ')';
		array_push($where, $where_commodity_ft);
	}
}

if (isset($show_serial_number_status_filter)) {
	if(count($show_serial_number_status_filter) == 1 ){
		if((int)$show_serial_number_status_filter[0] == 1){
			//add serial numbers for items
			// get item need add: parent item and variation item
			$where[] = "AND ".db_prefix()."wh_inventory_serial_numbers.is_used = 'no'";
			
		}elseif((int)$show_serial_number_status_filter[0] == 2){
			$where[] = "AND ".db_prefix()."wh_inventory_serial_numbers.is_used = 'yes'";

		}
	}
}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

$output = $result['output'];
$rResult = $result['rResult'];

$arr_warehouse_by_item = $this->ci->warehouse_model->arr_warehouse_by_item();
$arr_warehouse_id = $this->ci->warehouse_model->arr_warehouse_id();
$arr_unit_id = [];
$get_unit_type = $this->ci->warehouse_model->get_unit_type();
foreach ($get_unit_type as $key => $value) {
   $arr_unit_id[$value['unit_type_id']] = $value;
}
$inventory_min = $this->ci->warehouse_model->arr_inventory_min(true);
$arr_inventory_number = $this->ci->warehouse_model->arr_inventory_number_by_item();
$arr_tax_rate = [];
$get_tax_rate = get_tax_rate();
foreach ($get_tax_rate as $key => $value) {
    $arr_tax_rate[$value['id']] = $value;
}
$item_have_variation = $this->ci->warehouse_model->arr_item_have_variation();


	foreach ($rResult as $aRow) {
		$row = [];
		for ($i = 0; $i < count($aColumns); $i++) {

			/*get commodity file*/
			if($aColumns[$i] == '1'){
				$_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
			}elseif($aColumns[$i] == 'serial_number'){
				$_data = $aRow['serial_number'];
			}elseif($aColumns[$i] == db_prefix().'wh_inventory_serial_numbers.commodity_id'){
				$get_commodity_name = get_commodity_name($aRow[db_prefix().'wh_inventory_serial_numbers.commodity_id']);
				$item_name = '';
				if($get_commodity_name){
					$item_name = $get_commodity_name->description;
				}else{
					$item_name = '';
				}	

				$_data = '<a href="' . admin_url('warehouse/view_commodity_detail/' . $aRow[db_prefix().'wh_inventory_serial_numbers.commodity_id']) . '" target="_blank">' . $item_name . '</a>';

			}elseif($aColumns[$i] == 'purchase_price'){
				$_data = $aRow['purchase_price'];
			}elseif($aColumns[$i] == 'lot_number'){
				$_data = $aRow['lot_number'];
			}elseif($aColumns[$i] == 'date_manufacture'){
				$_data = ($aRow['date_manufacture'] != null && $aRow['date_manufacture'] != '' ) ? _d($aRow['date_manufacture']) : '';
			}elseif($aColumns[$i] == 'expiry_date'){
				$_data = ($aRow['expiry_date'] != null && $aRow['expiry_date'] != '' ) ? _d($aRow['expiry_date']) : '';
			}elseif($aColumns[$i] == '2'){
				$_data = '<a href="#" onclick="show_serial_number_detail_modal('. $aRow['id'].');return false;" >' . _l('wh_detail'). '</a>';
			}elseif($aColumns[$i] == db_prefix().'wh_inventory_serial_numbers.warehouse_id'){
				$get_warehouse_name = get_warehouse_name($aRow[db_prefix().'wh_inventory_serial_numbers.warehouse_id']);
				if($get_warehouse_name){
					$_data = $get_warehouse_name->warehouse_name;
				}else{
					$_data = '';
				}

			}elseif($aColumns[$i] == '3'){
				$_data = 1;
			}elseif($aColumns[$i] == 'is_used'){
				if($aRow['is_used'] == 'no'){
					$_data = '<span class="label label-tag label-tab1"><span class="tag">'._l('wh_in_stock').'</span><span class="hide">, </span></span>&nbsp';
				}else{
					$_data = '<span class="label label-tag label-tab3"><span class="tag">'._l('wh_out_stock').'</span><span class="hide">, </span></span>&nbsp';
				}

			}else{
				$_data = '';
			}

			$row[] = $_data;

		}
		$output['aaData'][] = $row;
	}

