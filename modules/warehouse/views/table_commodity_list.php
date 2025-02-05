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
$can_be_value_filter = $this->ci->input->post('can_be_value_filter');

$tags_ft = $this->ci->input->post('item_filter');
$parent_item = $this->ci->input->post('parent_item');
$sub_commodity_ft = $this->ci->input->post('sub_commodity_ft');
$filter_all_simple_variation = $this->ci->input->post('filter_all_simple_variation');
$barcode_filter = $this->ci->input->post('barcode_filter');
$group_filter = $this->ci->input->post('group_filter');
$sub_group_filter = $this->ci->input->post('sub_group_filter');


$join = [
	'LEFT JOIN ' . db_prefix() . 'taxes t1 ON t1.id = ' . db_prefix() . 'items.tax',
	'LEFT JOIN ' . db_prefix() . 'taxes t2 ON t2.id = ' . db_prefix() . 'items.tax2',
	'LEFT JOIN ' . db_prefix() . 'items_groups ON ' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id',
	'LEFT JOIN ' . db_prefix() . 'ware_style_type ON ' . db_prefix() . 'ware_style_type.style_type_id = ' . db_prefix() . 'items.style_id',
	'LEFT JOIN ' . db_prefix() . 'ware_body_type ON ' . db_prefix() . 'ware_body_type.body_type_id = ' . db_prefix() . 'items.model_id',
	'LEFT JOIN ' . db_prefix() . 'ware_size_type ON ' . db_prefix() . 'ware_size_type.size_type_id = ' . db_prefix() . 'items.size_id',
	'LEFT JOIN ' . db_prefix() . 'ware_color ON ' . db_prefix() . 'ware_color.color_id = ' . db_prefix() . 'items.color',
];



if($filter_all_simple_variation == 'true'){
	$filter_all_simple_variation_flag = ' OR true';
}elseif($filter_all_simple_variation == 'false'){
	$filter_all_simple_variation_flag = '';
}else{
	$filter_all_simple_variation_flag = '';
}

if($parent_item == 'true'){
	$where[] = 'AND ('  .db_prefix().'items.parent_id is null OR  '.db_prefix().'items.parent_id = 0 OR  '.db_prefix().'items.parent_id = "" '.$filter_all_simple_variation_flag.' )  ';
}else{
	$where[] = 'AND ' .db_prefix().'items.parent_id = '.$sub_commodity_ft.'  ';

}

if (isset($warehouse_ft)) {
	$arr_commodity_id = $this->ci->warehouse_model->get_commodity_in_warehouse($warehouse_ft);

	$where[] = 'AND '.db_prefix().'items.id IN (' . implode(', ', $arr_commodity_id) . ')';
	
}

if (isset($commodity_ft)) {
	$where_commodity_ft = '';
	foreach ($commodity_ft as $commodity_id) {
		if ($commodity_id != '') {
			if ($where_commodity_ft == '') {
				$where_commodity_ft .= 'AND (tblitems.id = "' . $commodity_id . '"';
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

if (isset($group_filter)) {
	$where_group_filter = '';
	foreach ($group_filter as $group_id) {
		if ($group_id != '') {
			if ($where_group_filter == '') {
				$where_group_filter .= 'AND ('.db_prefix().'items.group_id = "' . $group_id . '"';
			} else {
				$where_group_filter .= ' or '.db_prefix().'items.group_id = "' . $group_id . '"';
			}
		}
	}
	if ($where_group_filter != '') {
		$where_group_filter .= ')';
		array_push($where, $where_group_filter);
	}
}

if (isset($sub_group_filter)) {
	$where_sub_group_filter = '';
	foreach ($sub_group_filter as $sub_group_id) {
		if ($sub_group_id != '') {
			if ($where_sub_group_filter == '') {
				$where_sub_group_filter .= 'AND ('.db_prefix().'items.sub_group = "' . $sub_group_id . '"';
			} else {
				$where_sub_group_filter .= ' or '.db_prefix().'items.sub_group = "' . $sub_group_id . '"';
			}
		}
	}
	if ($where_sub_group_filter != '') {
		$where_sub_group_filter .= ')';
		array_push($where, $where_sub_group_filter);
	}
}

/*alert_filter*/
if (isset($alert_filter)) {
	if ($alert_filter != '') {
		if ($alert_filter == "1") {
			//out of stock
			$arr_commodity_id = $this->ci->warehouse_model->get_commodity_alert($alert_filter);
			if(count($arr_commodity_id) > 0){
				$where[] = 'AND '.db_prefix().'items.id IN (' . implode(', ', $arr_commodity_id) . ')';

			}


		} elseif((float) $alert_filter == 3) {
			// Minimum stock
			if(count($arr_inventory_min_id) > 0){
				$where[] = 'AND '.db_prefix().'items.id IN (' . implode(', ', $arr_inventory_min_id) . ')';
			}else{
				$where[] = 'AND 1 = 2';
			}
		} elseif((float) $alert_filter == 4) {
			// MAx stock
			if(count($arr_inventory_max_id) > 0) {
				$where[] = 'AND '.db_prefix().'items.id IN (' . implode(', ', $arr_inventory_max_id) . ')';
			}else{
				$where[] = 'AND 1 = 2';
			}

		}else {
			//exprired
			$arr_commodity_id = $this->ci->warehouse_model->get_commodity_alert($alert_filter);

			if(count($arr_commodity_id) > 0){
				$where[] = 'AND '.db_prefix().'items.id IN (' . implode(', ', $arr_commodity_id) . ')';
			}

		}
	}
}


//tags filter
if (isset($tags_ft)) {
	$where_tags_ft = '';

	if(count($tags_ft) > 0){
		$where[] = 'AND '.db_prefix().'items.id IN (SELECT rel_id FROM '.db_prefix().'taggables WHERE '.db_prefix().'taggables.rel_type = "item_tags" AND '.db_prefix().'taggables.tag_id IN (' . implode(',', $tags_ft) . '))';
	}
}

if (isset($can_be_value_filter)) {
	$where_can_be_ft = '';

	foreach ($can_be_value_filter as $can_be_value) {
		if($can_be_value == 'can_be_sold'){
			if ($where_can_be_ft == '') {
				$where_can_be_ft .= 'AND ('.db_prefix().'items.can_be_sold = "can_be_sold"';
			} else {
				$where_can_be_ft .= ' or '.db_prefix().'items.can_be_sold = "can_be_sold"';
			}
		}elseif($can_be_value == 'can_be_purchased'){
			if ($where_can_be_ft == '') {
				$where_can_be_ft .= 'AND ('.db_prefix().'items.can_be_purchased = "can_be_purchased"';
			} else {
				$where_can_be_ft .= ' or '.db_prefix().'items.can_be_purchased = "can_be_purchased"';
			}
		}elseif($can_be_value == 'can_be_manufacturing'){
			if ($where_can_be_ft == '') {
				$where_can_be_ft .= 'AND ('.db_prefix().'items.can_be_manufacturing = "can_be_manufacturing"';
			} else {
				$where_can_be_ft .= ' or '.db_prefix().'items.can_be_manufacturing = "can_be_manufacturing"';
			}
		}elseif($can_be_value == 'can_be_inventory'){
			if ($where_can_be_ft == '') {
				$where_can_be_ft .= 'AND ('.db_prefix().'items.can_be_inventory = "can_be_inventory"';
			} else {
				$where_can_be_ft .= ' or '.db_prefix().'items.can_be_inventory = "can_be_inventory"';
			}
		}
	}

	if ($where_can_be_ft != '') {
		$where_can_be_ft .= ')';
		array_push($where, $where_can_be_ft);
	}
}

if(isset($barcode_filter) && new_strlen($barcode_filter ?? '') > 0){
	$where[] = 'AND ('.db_prefix().'items.commodity_barcode = "'.$barcode_filter.'" OR '.db_prefix().'items.commodity_barcode = "'.substr($barcode_filter, 0, -1).'")' ;
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

if (get_status_modules_wh('purchase')) {
	$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'items.id', db_prefix() . 'items.description', db_prefix() . 'items.unit_id', db_prefix() . 'items.commodity_code', db_prefix() . 'items.commodity_barcode', db_prefix() . 'items.commodity_type', db_prefix() . 'items.warehouse_id', db_prefix() . 'items.origin', db_prefix() . 'items.color_id', db_prefix() . 'items.style_id', db_prefix() . 'items.model_id', db_prefix() . 'items.size_id', db_prefix() . 'items.rate', db_prefix() . 'items.tax', db_prefix() . 'items.group_id', db_prefix() . 'items.long_description', db_prefix() . 'items.sku_code', db_prefix() . 'items.sku_name', db_prefix() . 'items.sub_group', db_prefix() . 'items.color', db_prefix() . 'items.guarantee', db_prefix().'items.profif_ratio', db_prefix().'items.without_checking_warehouse', db_prefix().'items.parent_id', db_prefix().'items.tax2', db_prefix().'items.can_be_sold', db_prefix().'items.can_be_purchased', db_prefix().'items.can_be_manufacturing', db_prefix().'items.can_be_inventory', db_prefix().'ware_style_type.style_code', db_prefix().'ware_style_type.style_barcode', db_prefix().'ware_style_type.style_name', db_prefix().'ware_style_type.note' , db_prefix().'ware_body_type.body_code', db_prefix().'ware_body_type.body_name', db_prefix().'ware_body_type.note'  , db_prefix().'ware_size_type.size_code', db_prefix().'ware_size_type.size_name', db_prefix().'ware_size_type.size_symbol', db_prefix().'ware_size_type.note' , db_prefix().'ware_color.color_code', db_prefix().'ware_color.color_name', db_prefix().'ware_color.color_hex', db_prefix().'ware_color.note', db_prefix().'items.from_vendor_item' ]);
}else{

	$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'items.id', db_prefix() . 'items.description', db_prefix() . 'items.unit_id', db_prefix() . 'items.commodity_code', db_prefix() . 'items.commodity_barcode', db_prefix() . 'items.commodity_type', db_prefix() . 'items.warehouse_id', db_prefix() . 'items.origin', db_prefix() . 'items.color_id', db_prefix() . 'items.style_id', db_prefix() . 'items.model_id', db_prefix() . 'items.size_id', db_prefix() . 'items.rate', db_prefix() . 'items.tax', db_prefix() . 'items.group_id', db_prefix() . 'items.long_description', db_prefix() . 'items.sku_code', db_prefix() . 'items.sku_name', db_prefix() . 'items.sub_group', db_prefix() . 'items.color', db_prefix() . 'items.guarantee', db_prefix().'items.profif_ratio', db_prefix().'items.without_checking_warehouse', db_prefix().'items.parent_id', db_prefix().'items.tax2', db_prefix().'items.can_be_sold', db_prefix().'items.can_be_purchased', db_prefix().'items.can_be_manufacturing', db_prefix().'items.can_be_inventory', db_prefix().'ware_style_type.style_code', db_prefix().'ware_style_type.style_barcode', db_prefix().'ware_style_type.style_name', db_prefix().'ware_style_type.note' , db_prefix().'ware_body_type.body_code', db_prefix().'ware_body_type.body_name', db_prefix().'ware_body_type.note'  , db_prefix().'ware_size_type.size_code', db_prefix().'ware_size_type.size_name', db_prefix().'ware_size_type.size_symbol', db_prefix().'ware_size_type.note' , db_prefix().'ware_color.color_code', db_prefix().'ware_color.color_name', db_prefix().'ware_color.color_hex', db_prefix().'ware_color.note' ]);
}

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
						if (get_status_modules_wh('purchase')) {
							$this->ci->load->model('purchase/purchase_model');
							if(is_numeric($aRow['from_vendor_item'])){
								$vendor_image = $this->ci->purchase_model->get_vendor_item_file($aRow['from_vendor_item']);
								if(count($vendor_image) > 0){
									if(file_exists(PURCHASE_PATH.'vendor_items/' .$aRow['from_vendor_item'] .'/'.$vendor_image[0]['file_name'])){
										$_data = '<img class="images_w_table" src="' . site_url('modules/purchase/uploads/vendor_items/' . $vendor_image[0]['rel_id'] .'/'.$vendor_image[0]['file_name']).'" alt="'.$vendor_image[0]['file_name'] .'" >';
									}else{
										$_data = '<img class="images_w_table" src="' . site_url('modules/warehouse/uploads/nul_image.jpg') . '" alt="nul_image.jpg">';
									}
								}
							}else{
								$_data = '<img class="images_w_table" src="' . site_url('modules/warehouse/uploads/nul_image.jpg') . '" alt="nul_image.jpg">';
							}
						}else{

							$_data = '<img class="images_w_table" src="' . site_url('modules/warehouse/uploads/nul_image.jpg') . '" alt="nul_image.jpg">';
						}
					}

				} else {
					if (get_status_modules_wh('purchase')) {
						$this->ci->load->model('purchase/purchase_model');
						if(is_numeric($aRow['from_vendor_item'])){
							$vendor_image = $this->ci->purchase_model->get_vendor_item_file($aRow['from_vendor_item']);
							if(count($vendor_image) > 0){
								if(file_exists(PURCHASE_PATH.'vendor_items/' .$aRow['from_vendor_item'] .'/'.$vendor_image[0]['file_name'])){
									$_data = '<img class="images_w_table" src="' . site_url('modules/purchase/uploads/vendor_items/' . $vendor_image[0]['rel_id'] .'/'.$vendor_image[0]['file_name']).'" alt="'.$vendor_image[0]['file_name'] .'" >';
								}else{
									$_data = '<img class="images_w_table" src="' . site_url('modules/warehouse/uploads/nul_image.jpg') . '" alt="nul_image.jpg">';
								}
							}
						}else{
							$_data = '<img class="images_w_table" src="' . site_url('modules/warehouse/uploads/nul_image.jpg') . '" alt="nul_image.jpg">';
						}
					}else{
						$_data = '<img class="images_w_table" src="' . site_url('modules/warehouse/uploads/nul_image.jpg') . '" alt="nul_image.jpg">';
					}
				}
			}

			if ($aColumns[$i] == 'commodity_code') {
				$code = '<a href="' . admin_url('warehouse/view_commodity_detail/' . $aRow['id']) . '">' . $aRow['commodity_code'] . '</a>';
				$code .= '<div class="row-options">';

				$code .= '<a href="' . admin_url('warehouse/view_commodity_detail/' . $aRow['id']) . '" >' . _l('view') . '</a>';

				if (has_permission('warehouse_item', '', 'edit') || is_admin()) {
					$code .= ' | <a href="#" onclick="edit_commodity_item(this); return false;"  data-commodity_id="' . $aRow['id'] . '" data-description="' . $aRow['description'] . '" data-unit_id="' . $aRow['unit_id'] . '" data-commodity_code="' . $aRow['commodity_code'] . '" data-commodity_barcode="' . $aRow['commodity_barcode'] . '" data-commodity_type="' . $aRow['commodity_type'] . '" data-origin="' . $aRow['origin'] . '" data-color_id="' . $aRow['color_id'] . '" data-style_id="' . $aRow['style_id'] . '" data-model_id="' . $aRow['model_id'] . '" data-size_id="' . $aRow['size_id'] . '"  data-rate="' . $aRow['rate'] . '" data-group_id="' . $aRow['group_id'] . '" data-tax="' . $aRow['tax'] . '"  data-warehouse_id="' . $aRow['warehouse_id'] . '" data-sku_code="' . $aRow['sku_code'] . '" data-sku_name="' . $aRow['sku_name'] . '" data-sub_group="' . $aRow['sub_group'] . '" data-purchase_price="' . $aRow['purchase_price'] . '" data-color="' . $aRow['color'] . '" data-guarantee="' . $aRow['guarantee'] . '" data-profif_ratio="' . $aRow['profif_ratio'] . '" data-without_checking_warehouse="' . $aRow['without_checking_warehouse'] . '" data-parent_id="' . $aRow['parent_id'] . '" data-tax2="' . $aRow['tax2'] . '" data-can_be_sold="' . $aRow['can_be_sold'] . '" data-can_be_purchased="' . $aRow['can_be_purchased'] . '" data-can_be_manufacturing="' . $aRow['can_be_manufacturing'] . '" data-can_be_inventory="' . $aRow['can_be_inventory'] . '"  >' . _l('edit') . '</a>';
				}

				if ((has_permission('warehouse_item', '', 'edit') || has_permission('warehouse_item', '', 'create') ) && ($aRow['without_checking_warehouse'] == 0) ) {
					$code .= ' | <a href="#" onclick="add_opening_stock_modal('. $aRow['id'].', '.$aRow['parent_id'].'); return false;">' . _l('add_opening_stock') . '</a>';
				}
				
				if (has_permission('warehouse_item', '', 'delete') || is_admin()) {
					$code .= ' | <a href="' . admin_url('warehouse/delete_commodity/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
				}

				$code .= '</div>';

				$_data = $code;

			}elseif($aColumns[$i] == '1'){
				$_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
			} elseif ($aColumns[$i] == 'description') {

				if (isset($arr_inventory_min[$aRow['id']]) && $arr_inventory_min[$aRow['id']] == true) {
					$_data = '<a href="#" class="text-danger"  onclick="show_detail_item(this);return false;" data-name="' . $aRow['description'] . '" data-warehouse_id="' . $aRow['warehouse_id'] . '" data-commodity_id="' . $aRow['id'] . '"  >' . $aRow['description'] . '</a>';
				} else {

					$_data = '<a href="#" onclick="show_detail_item(this);return false;" data-name="' . $aRow['description'] . '"  data-commodity_id="' . $aRow['id'] . '"  >' . $aRow['description'] . '</a>';
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
									$check_hide_warehouse = false;
									if($team->hide_warehouse_when_out_of_stock == 1){
										if((float)$quantity_by_warehouse == 0){
											$check_hide_warehouse = true;
										}
									}

									if(!$check_hide_warehouse){

										$value = $team != null ? get_object_vars($team)['warehouse_name'] : '';

										$str .= '<span class="label label-tag tag-id-1"><span class="tag">' . $value . ': ( '.$quantity_by_warehouse.' )</span><span class="hide">, </span></span>&nbsp';

										$_data .= $str;
										if($wh_key%3 ==0){
											$_data .='<br/>';
										}
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

									$check_hide_warehouse = false;
									if($arr_warehouse_id[$warehouse_value['warehouse_id']]['hide_warehouse_when_out_of_stock'] == 1){
										if((float)$quantity_by_warehouse == 0){
											$check_hide_warehouse = true;
										}
									}

									if(!$check_hide_warehouse){

										$str .= '<span class="label label-tag tag-id-1"><span class="tag">' . $arr_warehouse_id[$warehouse_value['warehouse_id']]['warehouse_name'] . ': ( '.$quantity_by_warehouse.' )</span><span class="hide">, </span></span>&nbsp';

										$_data .= $str;
										if($wh_key%3 ==0){
											$_data .='<br/>';
										}
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

