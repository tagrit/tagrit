<?php

defined('BASEPATH') or exit('No direct script access allowed');
$arr_inventory_min_data = $this->ci->warehouse_model->arr_inventory_min(false);
$filter_arr_inventory_min_max = $this->ci->warehouse_model->filter_arr_inventory_min_max();
$arr_inventory_min_id = $filter_arr_inventory_min_max['inventory_min'];
$arr_inventory_max_id = $filter_arr_inventory_min_max['inventory_max'];

$aColumns = [
	'1',
	db_prefix() . 'items.id',
	'commodity_code',
	'description',
	'sku_code',
	db_prefix() . 'items_groups.name as group_name',
	db_prefix() . 'items.warehouse_id',
	'(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'items.id and rel_type="item_tags" ORDER by tag_order ASC) as tags',
	'commodity_barcode',
	'unit_id',
	'rate',
	'purchase_price',
	't1.taxrate as taxrate_1',
    't2.taxrate as taxrate_2',
	'origin',
	'2',	//minimum stock
	'3',	//maximum stock
	'4',	//maximum stock
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'items';


$where = [];

$where[] = 'AND '.db_prefix().'items.active = 1';
$warehouse_ft = $this->ci->input->post('warehouse_ft');
$commodity_ft = $this->ci->input->post('commodity_ft');
$alert_filter = $this->ci->input->post('alert_filter');
$filter_all_simple_variation = $this->ci->input->post('filter_all_simple_variation');
$show_items_filter = $this->ci->input->post('show_items_filter');


$join = [
	'LEFT JOIN ' . db_prefix() . 'taxes t1 ON t1.id = ' . db_prefix() . 'items.tax',
	'LEFT JOIN ' . db_prefix() . 'taxes t2 ON t2.id = ' . db_prefix() . 'items.tax2',
	'LEFT JOIN ' . db_prefix() . 'items_groups ON ' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id',
];



if($filter_all_simple_variation == 'true'){
	$filter_all_simple_variation_flag = ' OR true';
}elseif($filter_all_simple_variation == 'false'){
	$filter_all_simple_variation_flag = '';
}else{
	$filter_all_simple_variation_flag = '';
}

$where[] = 'AND ('  .db_prefix().'items.parent_id is null OR  '.db_prefix().'items.parent_id = 0 OR  '.db_prefix().'items.parent_id = "" '.$filter_all_simple_variation_flag.' )  ';


if (isset($warehouse_ft)) {
	$arr_commodity_id = $this->ci->warehouse_model->get_commodity_in_warehouse($warehouse_ft);

	$where[] = 'AND '.db_prefix().'items.id IN (' . implode(', ', $arr_commodity_id) . ')';
}

if (isset($commodity_ft)) {
	$where_commodity_ft = '';
	foreach ($commodity_ft as $commodity_id) {
		if ($commodity_id != '') {
			if ($where_commodity_ft == '') {
				$where_commodity_ft .= 'AND ('.db_prefix().'items.id = "' . $commodity_id . '"';
			} else {
				$where_commodity_ft .= ' or '.db_prefix().'items.id = "' . $commodity_id . '"';
			}
		}
	}
	if ($where_commodity_ft != '') {
		$where_commodity_ft .= ')';
		array_push($where, $where_commodity_ft);
	}
}

if (isset($show_items_filter)) {
	if(count($show_items_filter) == 1 ){
		if((int)$show_items_filter[0] == 1){
			//add serial numbers for items
			// get item need add: parent item and variation item
			$where[] = "AND (
			select sum(inventory_number) from ".db_prefix()."inventory_manage as iv
			where iv.commodity_id IN ( select id from ".db_prefix()."items as tem_items where tem_items.parent_id = ".db_prefix()."items.id OR tem_items.id = ".db_prefix()."items.id  )
			) > (
			select count(id) from ".db_prefix()."wh_inventory_serial_numbers as snm
			where snm.is_used = 'no' AND snm.commodity_id IN ( select id from ".db_prefix()."items as tem_items where tem_items.parent_id = ".db_prefix()."items.id OR tem_items.id = ".db_prefix()."items.id )
			) ";
			
		}elseif((int)$show_items_filter[0] == 2){
			//update serial numbers for items
			// get item need update: parent item and variation item
			$where[] = "AND (
			select sum(inventory_number) from ".db_prefix()."inventory_manage as iv
			where iv.commodity_id IN ( select id from ".db_prefix()."items as tem_items where tem_items.parent_id = ".db_prefix()."items.id OR tem_items.id = ".db_prefix()."items.id  )
			) = (
			select count(id) from ".db_prefix()."wh_inventory_serial_numbers as snm
			where snm.is_used = 'no' AND snm.commodity_id IN ( select id from ".db_prefix()."items as tem_items where tem_items.parent_id = ".db_prefix()."items.id OR tem_items.id = ".db_prefix()."items.id )
			) ";

		}
	}else{
		$where[] = "AND ((
			select sum(inventory_number) from ".db_prefix()."inventory_manage as iv
			where iv.commodity_id IN ( select id from ".db_prefix()."items as tem_items where tem_items.parent_id = ".db_prefix()."items.id OR tem_items.id = ".db_prefix()."items.id   )
			) > (
			select count(id) from ".db_prefix()."wh_inventory_serial_numbers as snm
			where snm.is_used = 'no' AND snm.commodity_id IN ( select id from ".db_prefix()."items as tem_items where tem_items.parent_id = ".db_prefix()."items.id OR tem_items.id = ".db_prefix()."items.id )
			) OR (
			select sum(inventory_number) from ".db_prefix()."inventory_manage as iv
			where iv.commodity_id IN ( select id from ".db_prefix()."items as tem_items where tem_items.parent_id = ".db_prefix()."items.id OR tem_items.id = ".db_prefix()."items.id  )
			) = (
			select count(id) from ".db_prefix()."wh_inventory_serial_numbers as snm
			where snm.is_used = 'no' AND snm.commodity_id IN ( select id from ".db_prefix()."items as tem_items where tem_items.parent_id = ".db_prefix()."items.id OR tem_items.id = ".db_prefix()."items.id )
			) ) ";
	}
}

//only show item can be inventory
$where_can_be_ft = '';
if ($where_can_be_ft == '') {
	$where_can_be_ft .= 'AND ('.db_prefix().'items.can_be_inventory = "can_be_inventory"';
} else {
	$where_can_be_ft .= ' or '.db_prefix().'items.can_be_inventory = "can_be_inventory"';
}
if ($where_can_be_ft != '') {
	$where_can_be_ft .= ')';
	array_push($where, $where_can_be_ft);
}

$custom_fields = get_custom_fields('items', [
    'show_on_table' => 1,
    ]);
    

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);

    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'items.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="items_pr" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'items.id', db_prefix() . 'items.description', db_prefix() . 'items.unit_id', db_prefix() . 'items.commodity_code', db_prefix() . 'items.commodity_barcode', db_prefix() . 'items.commodity_type', db_prefix() . 'items.warehouse_id', db_prefix() . 'items.origin', db_prefix() . 'items.color_id', db_prefix() . 'items.style_id', db_prefix() . 'items.model_id', db_prefix() . 'items.size_id', db_prefix() . 'items.rate', db_prefix() . 'items.tax', db_prefix() . 'items.group_id', db_prefix() . 'items.long_description', db_prefix() . 'items.sku_code', db_prefix() . 'items.sku_name', db_prefix() . 'items.sub_group', db_prefix() . 'items.color', db_prefix() . 'items.guarantee', db_prefix().'items.profif_ratio', db_prefix().'items.without_checking_warehouse', db_prefix().'items.parent_id', db_prefix().'items.tax2', 
	db_prefix().'items.can_be_sold', db_prefix().'items.can_be_purchased', db_prefix().'items.can_be_manufacturing', db_prefix().'items.can_be_inventory' ]);

$output = $result['output'];
$rResult = $result['rResult'];

$arr_images = $this->ci->warehouse_model->item_attachments();
$arr_inventory_min = $arr_inventory_min_data;
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
		$product_inventory_quantity = 0;
		$row = [];
		for ($i = 0; $i < count($aColumns); $i++) {

			 if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
	            $_data = $aRow[strafter($aColumns[$i], 'as ')];
	        } else {
				$_data = $aRow[$aColumns[$i]];
	        }


			/*get commodity file*/
			if($aColumns[$i] == db_prefix() . 'items.id'){
				if (isset($arr_images[$aRow['id']]) && isset($arr_images[$aRow['id']][0])) {

					if (file_exists(WAREHOUSE_ITEM_UPLOAD . $arr_images[$aRow['id']][0]['rel_id'] . '/' . $arr_images[$aRow['id']][0]['file_name'])) {
						$_data = '<img class="images_w_table" src="' . site_url('modules/warehouse/uploads/item_img/' . $arr_images[$aRow['id']][0]['rel_id'] . '/' . $arr_images[$aRow['id']][0]['file_name']) . '" alt="' . $arr_images[$aRow['id']][0]['file_name'] . '" >';
					} elseif(file_exists('modules/purchase/uploads/item_img/' . $arr_images[$aRow['id']][0]['rel_id'] . '/' . $arr_images[$aRow['id']][0]['file_name'])) {
						$_data = '<img class="images_w_table" src="' . site_url('modules/purchase/uploads/item_img/' . $arr_images[$aRow['id']][0]['rel_id'] . '/' . $arr_images[$aRow['id']][0]['file_name']) . '" alt="' . $arr_images[$aRow['id']][0]['file_name'] . '" >';
					}elseif(file_exists('modules/manufacturing/uploads/products/' . $arr_images[$aRow['id']][0]['rel_id'] . '/' . $arr_images[$aRow['id']][0]['file_name'])) {
						$_data = '<img class="images_w_table" src="' . site_url('modules/manufacturing/uploads/products/' . $arr_images[$aRow['id']][0]['rel_id'] . '/' . $arr_images[$aRow['id']][0]['file_name']) . '" alt="' . $arr_images[$aRow['id']][0]['file_name'] . '" >';
					}else{
						$_data = '<img class="images_w_table" src="' . site_url('modules/warehouse/uploads/nul_image.jpg') . '" alt="nul_image.jpg">';
					}

				} else {

					$_data = '<img class="images_w_table" src="' . site_url('modules/warehouse/uploads/nul_image.jpg') . '" alt="nul_image.jpg">';
				}
			}

			if ($aColumns[$i] == 'commodity_code') {
				$code = '<a href="' . admin_url('warehouse/view_commodity_detail/' . $aRow['id']) . '">' . $aRow['commodity_code'] . '</a>';
				$_data = $code;

			}elseif($aColumns[$i] == '1'){
				$_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
			} elseif ($aColumns[$i] == 'description') {

				if (isset($arr_inventory_min[$aRow['id']]) && $arr_inventory_min[$aRow['id']] == true) {
					$_data = '<a href="javascript:void(0)" class="text-danger"   data-name="' . $aRow['description'] . '" data-warehouse_id="' . $aRow['warehouse_id'] . '" data-commodity_id="' . $aRow['id'] . '"  >' . $aRow['description'] . '</a>';
				} else {

					$_data = '<a href="javascript:void(0)"  data-name="' . $aRow['description'] . '"  data-commodity_id="' . $aRow['id'] . '"  >' . $aRow['description'] . '</a>';
				}

			}elseif($aColumns[$i] == 'sku_code'){
				$_data = '<span class="label label-tag tag-id-1"><span class="tag">' . $aRow['sku_code'] . '</span><span class="hide">, </span></span>&nbsp';
			} elseif ($aColumns[$i] == 'group_name') {
				$_data = $aRow['group_name'];

			} elseif ($aColumns[$i] == db_prefix() . 'items.warehouse_id') {
				$_data ='';

				if(isset($item_have_variation[$aRow['id']]) && (float)$item_have_variation[$aRow['id']]['total_child'] > 0 ){

					$arr_warehouse = get_inventory_by_warehouse_variation($aRow['id']);

					$str = '';
					if(count($arr_warehouse) > 0){
						foreach ($arr_warehouse as $wh_key => $warehouseid) {
							$str = '';
							if ($warehouseid['warehouse_id'] != '' && $warehouseid['warehouse_id'] != '0') {
								//get inventory quantity
								$quantity_by_warehouse = $warehouseid['inventory_number'];
								$product_inventory_quantity += $quantity_by_warehouse;
								$team = get_warehouse_name($warehouseid['warehouse_id']);
								if($team){
									$value = $team != null ? get_object_vars($team)['warehouse_name'] : '';

									$str .= '<span class="label label-tag tag-id-1"><span class="tag">' . $value . ': ( '.$quantity_by_warehouse.' )</span><span class="hide">, </span></span>&nbsp';

									$_data .= $str;
									if($wh_key%3 ==0){
										$_data .='<br/>';
									}
								}

							}
						}

					} else {
						$_data = '';
					}


				}else{


					$str = '';
					if(isset($arr_warehouse_by_item[$aRow['id']]) > 0){
						foreach ($arr_warehouse_by_item[$aRow['id']] as $wh_key => $warehouse_value) {
							$str = '';
							if ($warehouse_value['warehouse_id'] != '' && $warehouse_value['warehouse_id'] != '0') {
								//get inventory quantity
								$quantity_by_warehouse = $warehouse_value['inventory_number'];
								$product_inventory_quantity += $quantity_by_warehouse;
								if(isset($arr_warehouse_id[$warehouse_value['warehouse_id']])){

									$str .= '<span class="label label-tag tag-id-1"><span class="tag">' . $arr_warehouse_id[$warehouse_value['warehouse_id']]['warehouse_name'] . ': ( '.$quantity_by_warehouse.' )</span><span class="hide">, </span></span>&nbsp';

									$_data .= $str;
									if($wh_key%3 ==0){
										$_data .='<br/>';
									}
								}

							}
						}

					} else {
						$_data = '';
					}
				}


			}elseif($aColumns[$i] == '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'items.id and rel_type="item_tags" ORDER by tag_order ASC) as tags'){
				
				$_data = render_tags($aRow['tags']);

			} elseif ($aColumns[$i] == 'unit_id') {
				if ($aRow['unit_id'] != null) {
					if(isset($arr_unit_id[$aRow['unit_id']])){
						$_data = $arr_unit_id[$aRow['unit_id']]['unit_name'];
					}else{
						$_data = '';
					}
				} else {
					$_data = '';
				}
			} elseif ($aColumns[$i] == 'rate') {
				$_data = app_format_money((float) $aRow['rate'], '');
			} elseif ($aColumns[$i] == 'purchase_price') {
				$_data = app_format_money((float) $aRow['purchase_price'], '');

			} elseif ($aColumns[$i] == 'taxrate_1') {

				$aRow['taxrate_1'] = $aRow['taxrate_1'] ?? 0;
				$_data             = '<span data-toggle="tooltip" title="' . $aRow['taxname_1'] . '" data-taxid="' . $aRow['tax_id_1'] . '">' . app_format_number($aRow['taxrate_1']) . '%' . '</span>';

			} elseif ($aColumns[$i] == 'taxrate_2') {
				$aRow['taxrate_2'] = $aRow['taxrate_2'] ?? 0;
				$_data             = '<span data-toggle="tooltip" title="' . $aRow['taxname_2'] . '" data-taxid="' . $aRow['tax_id_2'] . '">' . app_format_number($aRow['taxrate_2']) . '%' . '</span>';

			} elseif ($aColumns[$i] == 'commodity_barcode') {
				/*inventory number*/
				$inventory_number = 0;

        		if(isset($arr_inventory_number[$aRow['id']])){
        			$inventory_number =  $arr_inventory_number[$aRow['id']]['inventory_number'];
        		}
				$_data = $product_inventory_quantity;

			} elseif ($aColumns[$i] == 'origin') {

        		if (isset($arr_inventory_min[$aRow['id']]) && $arr_inventory_min[$aRow['id']]) {
					$_data = '<span class="label label-tag tag-id-1 label-tabus "><span class="tag text-danger">' . _l('unsafe_inventory') . '</span><span class="hide">, </span></span>&nbsp';
				} else {
					$_data = '';
				}

			} elseif ($aColumns[$i] == '2') {
				/*3: minmumstock, maximum stock*/
				$minmumstock = '';

				if(isset($inventory_min[$aRow['id']])){
					$minmumstock .= $inventory_min[$aRow['id']]['inventory_number_min'] ;
				}

				$_data =  $minmumstock;

			}elseif ($aColumns[$i] == '3') {
				/*3: minmumstock, maximum stock*/
				$maxmumstock = '';

				if(isset($inventory_min[$aRow['id']])){
					$maxmumstock .= $inventory_min[$aRow['id']]['inventory_number_max'] ;
				}

				$_data = $maxmumstock;

			}elseif($aColumns[$i] == '4') {
				//final price: price*Vat
				$tax_value=0;
				if($aRow['tax'] != 0 && $aRow['tax'] != ''){
					if(isset($arr_tax_rate[$aRow['tax']])){
						$tax_value = $arr_tax_rate[$aRow['tax']]['taxrate'];
					}
				}

				if($aRow['tax2'] != 0 && $aRow['tax2'] != ''){
					if(isset($arr_tax_rate[$aRow['tax2']])){
						$tax_value += (float)$arr_tax_rate[$aRow['tax2']]['taxrate'];
					}
				}

				$_data = app_format_money((float)$aRow['rate'] + (float)$aRow['rate']*$tax_value/100, '');
				
			}


			$row[] = $_data;

		}
		$output['aaData'][] = $row;
	}

