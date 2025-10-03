<?php

defined('BASEPATH') or exit('No direct script access allowed');
$warehouseByStaff = $this->ci->warehouse_model->getWarehouseByStaff();


$aColumns = [
    db_prefix().'inventory_manage.id',
    'description',
    'group_id',
    'color_id',
    db_prefix().'items.warehouse_id',
    'style_id',
    'unit_id',
    'rate',
    db_prefix().'inventory_manage.purchase_price',
    'tax',
    'origin',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'items';



$where = [];


$warehouse_ft = $this->ci->input->post('warehouse_ft');
$commodity_ft = $this->ci->input->post('commodity_ft');
$alert_filter = $this->ci->input->post('alert_filter');


if(!isset($warehouse_ft) && !isset($commodity_ft) && !isset($alert_filter) && ($alert_filter == '')){
    $join =['LEFT JOIN '.db_prefix().'inventory_manage ON '.db_prefix().'inventory_manage.commodity_id = '.db_prefix().'items.id',];
}else{

     $join = [

    'LEFT JOIN '.db_prefix().'inventory_manage ON '.db_prefix().'inventory_manage.commodity_id = '.db_prefix().'items.id',
   
    ];

    if(is_array($warehouseByStaff)){
        $where[] = 'AND ( '.db_prefix().'inventory_manage.warehouse_id IN ('.implode(',', $warehouseByStaff).') )';
    }
}


if(isset($warehouse_ft)){
    $where_warehouse_ft = '';
    foreach ($warehouse_ft as $warehouse_id) {
        if($warehouse_id != '')
        {
            if($where_warehouse_ft == ''){
                $where_warehouse_ft .= ' AND ('.db_prefix().'inventory_manage.warehouse_id = "'.$warehouse_id.'"';
            }else{
                $where_warehouse_ft .= ' or '.db_prefix().'inventory_manage.warehouse_id = "'.$warehouse_id.'"';
            }
        }
    }
    if($where_warehouse_ft != '')
    {
        $where_warehouse_ft .= ')';
        array_push($where, $where_warehouse_ft);
    }

}

if(isset($commodity_ft)){
    $where_commodity_ft = ' AND tblitems.id = "'.$commodity_ft.'"';
    array_push($where, $where_commodity_ft);
    
}


$where_alert_filter = ' AND '.db_prefix().'inventory_manage.inventory_number > "0"';
array_push($where, $where_alert_filter);



if(!isset($warehouse_ft) && !isset($commodity_ft) && ($alert_filter == '')){
    $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'items.id',db_prefix().'inventory_manage.commodity_id',db_prefix().'inventory_manage.warehouse_id as warehouse_ids',db_prefix().'inventory_manage.inventory_number',db_prefix().'inventory_manage.date_manufacture',db_prefix().'inventory_manage.expiry_date', db_prefix().'items.description', db_prefix().'items.unit_id', db_prefix().'items.commodity_code',  db_prefix().'items.commodity_barcode', db_prefix().'items.commodity_type',  db_prefix().'items.warehouse_id',  db_prefix().'items.origin',   db_prefix().'items.color_id',   db_prefix().'items.style_id',   db_prefix().'items.model_id', db_prefix().'items.size_id',     db_prefix().'items.rate',  db_prefix().'items.tax',  db_prefix().'items.group_id' ,  db_prefix().'items.long_description' ,  db_prefix().'items.sku_code',  db_prefix().'items.sku_name',  db_prefix().'items.sub_group',db_prefix().'inventory_manage.lot_number']);
}else{
    $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'items.id',db_prefix().'inventory_manage.commodity_id',db_prefix().'inventory_manage.warehouse_id as warehouse_ids',db_prefix().'inventory_manage.inventory_number',db_prefix().'inventory_manage.date_manufacture',db_prefix().'inventory_manage.expiry_date',db_prefix().'items.description',db_prefix().'items.group_id',db_prefix().'items.unit_id',db_prefix().'items.rate',db_prefix().'items.tax', db_prefix().'items.description', db_prefix().'items.unit_id', db_prefix().'items.commodity_code',  db_prefix().'items.commodity_barcode', db_prefix().'items.commodity_type',  db_prefix().'items.warehouse_id',  db_prefix().'items.origin',   db_prefix().'items.color_id',   db_prefix().'items.style_id',   db_prefix().'items.model_id', db_prefix().'items.size_id',    db_prefix().'items.rate',  db_prefix().'items.tax',  db_prefix().'items.group_id' ,  db_prefix().'items.long_description',  db_prefix().'items.sku_code',  db_prefix().'items.sku_name',  db_prefix().'items.sub_group' ,db_prefix().'inventory_manage.lot_number', db_prefix().'inventory_manage.id']);

}

$output  = $result['output'];
$rResult = $result['rResult'];

if(!isset($warehouse_ft) && !isset($commodity_ft)  && ($alert_filter == '')){
    foreach ($rResult as $aRow) {
         $row = [];
        for ($i = 0; $i < count($aColumns); $i++) {
            $_data = $aRow[$aColumns[$i]];
           


             if($aColumns[$i] == 'commodity_code') {
                 

                $_data = $aRow['commodity_code'];

            }elseif ($aColumns[$i] == 'description') {
                
                 if(get_status_inventory($aRow['id'], $aRow['inventory_number'])){
                    $_data = '<a href="#"  data-name="'.$aRow['description'].'"  data-warehouse_id="'.$aRow['warehouse_id'].'" data-commodity_id="'.$aRow['commodity_id'].'" data-expiry_date="'.$aRow['expiry_date'].'" >' . $aRow['commodity_code'] .'_'.$aRow['description']. '</a>';
                }else{
                    
                    $_data = '<a href="#" class="text-danger"   data-name="'.$aRow['description'].'" data-warehouse_id="'.$aRow['warehouse_id'].'" data-commodity_id="'.$aRow['commodity_id'].'" data-expiry_date="'.$aRow['expiry_date'].'" >' . $aRow['commodity_code'] .'_'.$aRow['description']. '</a>';
                }
                


            }elseif ($aColumns[$i] == 'group_id') {
                $_data = get_wh_group_name($aRow['group_id']) != null ? get_wh_group_name($aRow['group_id'])->name : '';
            }elseif ($aColumns[$i] == 'color_id') {
                $_data = $aRow['lot_number'];

            }elseif($aColumns[$i] == db_prefix().'items.warehouse_id'){

                if($aRow['id'] != ''){
                    $team = $this->ci->warehouse_model->get_commodity_warehouse($aRow['id']);

                    $str = '';
                    $j = 0;
                    
                    foreach ($team as $value) {
                        $j++;
                        $str .= '<span class="label label-tag tag-id-1"><span class="tag">'.$value['warehouse_name'].'</span><span class="hide">, </span></span>&nbsp';
                        if($j%2 == 0){
                             $str .= '<br><br/>';
                        }
                       
                    }
                    $_data = $str;
                }
                else{
                    $_data = '';
                }  
              
            }elseif ($aColumns[$i] == 'unit_id') {
                if($aRow['unit_id'] != null){
                    $_data = get_unit_type($aRow['unit_id']) != null ? get_unit_type($aRow['unit_id'])->unit_name : '';
                }else{
                    $data = '';
                }
            }elseif ($aColumns[$i] == 'rate') {
                $_data = app_format_money((float)$aRow['rate'],'');
            }elseif($aColumns[$i] == db_prefix().'items.purchase_price'){
                $_data = app_format_money((float)$aRow[db_prefix().'items.purchase_price'],'');

            }elseif ($aColumns[$i] == 'tax') {
                $_data ='';
                $tax_rate = get_tax_rate($aRow['tax']);

                if($aRow['tax']){
                    if($tax_rate && $tax_rate != null && $tax_rate != 'null'){
                        $_data = $tax_rate->name;
                    }
                }


            }elseif ($aColumns[$i] == 'style_id') {
                 $_data = $aRow['inventory_number'];

            }elseif ($aColumns[$i] == 'origin') {
                $_data = '';
            }
         
        $row[] = $_data;
            
        }
        $output['aaData'][] = $row;
    }
}else{

        foreach ($rResult as $aRow) {
         $row = [];

        for ($i = 0; $i < count($aColumns); $i++) {

           

            $_data = $aRow[$aColumns[$i]];


             if($aColumns[$i] == 'commodity_code') {
                 $_data = $aRow['commodity_code'];

            }elseif($aColumns[$i] ==  db_prefix().'inventory_manage.id'){
                 $_data = $aRow['id'];

            }elseif ($aColumns[$i] == 'description') {
                
                    $_data = '<a href="#"  data-name="'.$aRow['description'].'"  data-warehouse_id="'.$aRow['warehouse_id'].'" data-commodity_id="'.$aRow['commodity_id'].'" data-expiry_date="'.$aRow['expiry_date'].'" >' .$aRow['commodity_code'].'_'.$aRow['description']. '</a>';

            }elseif ($aColumns[$i] == 'group_id') {

                if($aRow['expiry_date'] > date('Y-m-d')){
                    $_data = _d($aRow['expiry_date']);
                }else{
                     $_data = '<a href="#" class="text-danger" >'._d($aRow['expiry_date']). '</a>';
                }

            }elseif ($aColumns[$i] == 'color_id') {
                $_data = $aRow['lot_number'];

            }elseif($aColumns[$i] == db_prefix().'items.warehouse_id'){

                if($aRow['warehouse_ids'] != ''){
                    $team = get_warehouse_name($aRow['warehouse_ids']);

                    $str = '';
                    $value = $team != null ? get_object_vars($team)['warehouse_name'] : '';
                   
                        $str .= '<span class="label label-tag tag-id-1"><span class="tag">'.$value.'</span><span class="hide">, </span></span>&nbsp';
                        
                    $_data = $str;
                }
                else{
                    $_data = '';
                }  
              
            }elseif ($aColumns[$i] == 'unit_id') {
                if($aRow['unit_id'] != null){
                    $_data = get_unit_type($aRow['unit_id']) != null ? get_unit_type($aRow['unit_id'])->unit_name : '';
                }else{
                    $data = '';
                }
            }elseif ($aColumns[$i] == 'rate') {
                $_data = app_format_money((float)$aRow['rate'],'');
            }elseif($aColumns[$i] == db_prefix().'inventory_manage.purchase_price'){
                $_data = app_format_money((float)$aRow[db_prefix().'inventory_manage.purchase_price'],'');

            }elseif ($aColumns[$i] == 'tax') {
                $_data ='';
                $tax_rate = get_tax_rate($aRow['tax']);

                if($aRow['tax']){
                    if($tax_rate && $tax_rate != null && $tax_rate != 'null'){
                        $_data = $tax_rate->name;
                    }
                }

            }elseif ($aColumns[$i] == 'style_id') {
                if(get_status_inventory($aRow['id'], $aRow['inventory_number'])){
                    $_data = $aRow['inventory_number'];
                }else{

                    $_data = '<a href="#" class="text-danger" >'.$aRow['inventory_number']. '</a>';
                }
            }elseif ($aColumns[$i] == 'origin') {
                if(get_status_inventory($aRow['id'], $aRow['inventory_number'])){
                    $_data ='';
                }else{
                    $_data = '<span class="label label-tag tag-id-1 label-tabus"><span class="tag">'._l('unsafe_inventory').'</span><span class="hide">, </span></span>&nbsp';
                }
            }
             
         
        $row[] = $_data;
            
        }
        $output['aaData'][] = $row;
    }       

}
