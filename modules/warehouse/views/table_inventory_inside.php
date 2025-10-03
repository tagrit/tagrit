<?php

defined('BASEPATH') or exit('No direct script access allowed');


$aColumns = [
	'description',
	'profif_ratio',
	'purchase_price',
	'rate',
	'group_id', //changre for average_price_of_inventory
	'commodity_type', // change for profit_rate_inventory
	'style_id',	//change for trade_discounts
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'items';

$where = [];

$profit_rate_search = $this->ci->input->post('profit_rate_search');
$commodity_ft = $this->ci->input->post('commodity_ft');
$warehouse_filter = $this->ci->input->post('warehouse_filter');


$join= [];

$where[] = 'AND '.db_prefix().'items.active = 1';

$where[] = 'AND '.db_prefix().'items.id not in ( SELECT distinct parent_id from '.db_prefix().'items WHERE parent_id is not null AND parent_id != 0 ) ';

if (isset($commodity_ft)) {
	$where_commodity_ft = '';
	foreach ($commodity_ft as $commodity_id) {
		if ($commodity_id != '') {
			if ($where_commodity_ft == '') {
				$where_commodity_ft .= ' AND (tblitems.id = "' . $commodity_id . '"';
			} else {
				$where_commodity_ft .= ' or tblitems.id = "' . $commodity_id . '"';
			}
		}
	}
	if ($where_commodity_ft != '') {
		$where_commodity_ft .= ')';
		array_push($where, $where_commodity_ft);
	}
}



$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'items.id', db_prefix() . 'items.description', db_prefix() . 'items.unit_id', db_prefix() . 'items.commodity_code', db_prefix() . 'items.commodity_barcode', db_prefix() . 'items.commodity_type', db_prefix() . 'items.warehouse_id', db_prefix() . 'items.origin', db_prefix() . 'items.color_id', db_prefix() . 'items.style_id', db_prefix() . 'items.model_id', db_prefix() . 'items.size_id', db_prefix() . 'items.rate', db_prefix() . 'items.tax', db_prefix() . 'items.group_id', db_prefix() . 'items.long_description', db_prefix() . 'items.sku_code', db_prefix() . 'items.sku_name', db_prefix() . 'items.sub_group', db_prefix() . 'items.color', db_prefix() . 'items.guarantee', db_prefix().'items.profif_ratio']);

$output = $result['output'];
$rResult = $result['rResult'];

	foreach ($rResult as $aRow) {
				/*average price of inventory*/
				/*profit rate actual*/
				/*trade discounts*/
				$average_price_inventory = $this->ci->warehouse_model->get_average_price_inventory($aRow['id'], $aRow['rate'], $aRow['profif_ratio'], $warehouse_filter);

		if(isset($profit_rate_search) && $profit_rate_search != ''){
			if($profit_rate_search > $average_price_inventory['trade_discounts']){
				continue;
			}
		}

		if(is_array($warehouse_filter)){
			if($average_price_inventory['item'] != true){
				continue;
			}
		}
		

		$row = [];
		for ($i = 0; $i < count($aColumns); $i++) {


			
			if ($aColumns[$i] == 'description') {
				$_data = '<a href="' . admin_url('warehouse/view_commodity_detail/' . $aRow['id']) . '" >'. $aRow['commodity_code'].'_'. $aRow['description'].'</a>';

			}elseif ($aColumns[$i] == 'profif_ratio') {
				$_data = $aRow['profif_ratio'];
			}elseif ($aColumns[$i] == 'purchase_price') {
				$_data = app_format_money((float)$aRow['purchase_price'],'');
			}elseif ($aColumns[$i] == 'rate') {
				$_data =  app_format_money((float)$aRow['rate'],'');
			}elseif ($aColumns[$i] == 'group_id') {
				/*get average price of inventory*/

				$_data =  app_format_money((float)$average_price_inventory['average_price_of_inventory'],'');
			}elseif ($aColumns[$i] == 'commodity_type') {
				/*profit rate actual*/
				$_data = app_format_money((float)$average_price_inventory['profit_rate_actual'],'');
			}elseif ($aColumns[$i] == 'style_id') {
				/*trade discounts*/
				if((float)$average_price_inventory['trade_discounts'] > 0){
					$_data = '<a href="#" class="text-success" >'. app_format_money((float)$average_price_inventory['trade_discounts'],'').'</a>';
				}elseif( (float)$average_price_inventory['trade_discounts'] < 0 ){
					$_data = '<a href="#" class="text-danger" >'. app_format_money((float)$average_price_inventory['trade_discounts'],'').'</a>';

				}else{
					
					$_data =  app_format_money((float)$average_price_inventory['trade_discounts'],'');
				}
			}

			$row[] = $_data;

		}
		$output['aaData'][] = $row;
	}

