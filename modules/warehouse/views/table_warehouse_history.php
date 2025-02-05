<?php

defined('BASEPATH') or exit('No direct script access allowed');
$warehouseByStaff = $this->ci->warehouse_model->getWarehouseByStaff();


$aColumns = [
    db_prefix().'goods_transaction_detail.id',
    'goods_receipt_id',
    'commodity_id',
    db_prefix().'goods_transaction_detail.warehouse_id',
    db_prefix().'goods_transaction_detail.date_add',
    'old_quantity',
    'quantity',
    'lot_number',
    db_prefix().'goods_transaction_detail.expiry_date',
    db_prefix().'goods_transaction_detail.serial_number',
    'note',
    db_prefix().'goods_transaction_detail.status',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'goods_transaction_detail';



$where = [];
if(is_array($warehouseByStaff)){
    $where[] = 'AND ( '.db_prefix().'goods_transaction_detail.warehouse_id IN ('.implode(',', $warehouseByStaff).') OR  '.db_prefix().'goods_transaction_detail.from_stock_name IN ('.implode(',', $warehouseByStaff).') OR  '.db_prefix().'goods_transaction_detail.to_stock_name IN ('.implode(',', $warehouseByStaff).')  )';
}

$warehouse_ft = $this->ci->input->post('warehouse_ft');
$commodity_ft = $this->ci->input->post('commodity_ft'); 
$status_ft = $this->ci->input->post('status_ft'); 


$join =[
  'LEFT JOIN '.db_prefix().'goods_receipt ON '.db_prefix().'goods_receipt.id = '.db_prefix().'goods_transaction_detail.goods_receipt_id AND  '.db_prefix().'goods_transaction_detail.status = 1',
  'LEFT JOIN '.db_prefix().'goods_delivery ON '.db_prefix().'goods_delivery.id = '.db_prefix().'goods_transaction_detail.goods_receipt_id AND  '.db_prefix().'goods_transaction_detail.status = 2',
  'LEFT JOIN '.db_prefix().'wh_loss_adjustment ON '.db_prefix().'wh_loss_adjustment.id = '.db_prefix().'goods_transaction_detail.goods_receipt_id AND  '.db_prefix().'goods_transaction_detail.status = 3',
  'LEFT JOIN '.db_prefix().'internal_delivery_note ON '.db_prefix().'internal_delivery_note.id = '.db_prefix().'goods_transaction_detail.goods_receipt_id AND  '.db_prefix().'goods_transaction_detail.status = 4'
];



if(isset($warehouse_ft)){

    $where_warehouse_ft = '';
    foreach ($warehouse_ft as $warehouse_id) {
        if($warehouse_id != '')
        {

            if ($where_warehouse_ft == '') {
              $where_warehouse_ft .= ' AND (find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) or find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.from_stock_name) or find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.to_stock_name) ';

            } else {
              $where_warehouse_ft .= ' or find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) or find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.from_stock_name) or find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.to_stock_name) ';

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
        if(!is_array($commodity_ft)){
            $where_commodity_ft = ' AND tblgoods_transaction_detail.commodity_id = "'.$commodity_ft.'"';
            array_push($where, $where_commodity_ft);
            
        }else{

            $where_commodity_ft = '';
            foreach ($commodity_ft as $commodity_id) {
                if($commodity_id != '')
                {
                    if($where_commodity_ft == ''){
                        $where_commodity_ft .= ' AND (tblgoods_transaction_detail.commodity_id = "'.$commodity_id.'"';
                    }else{
                        $where_commodity_ft .= ' or tblgoods_transaction_detail.commodity_id = "'.$commodity_id.'"';
                    }
                }
            }
            if($where_commodity_ft != '')
            {
                $where_commodity_ft .= ')';

                array_push($where, $where_commodity_ft);
            }
        }

    }

if(isset($status_ft)){

    $where_status_ft = '';
    foreach ($status_ft as $status_id) {
        if($status_id != '')
        {
            if($where_status_ft == ''){
                $where_status_ft .= ' AND (tblgoods_transaction_detail.status = "'.$status_id.'"';
            }else{
                $where_status_ft .= ' or tblgoods_transaction_detail.status = "'.$status_id.'"';
            }
        }
    }
    if($where_status_ft != '')
    {
        $where_status_ft .= ')';

        array_push($where, $where_status_ft);
    }
}

if($this->ci->input->post('validity_start_date')){
        $start_date = to_sql_date($this->ci->input->post('validity_start_date'));
    }

    if($this->ci->input->post('validity_end_date')){
        $end_date = to_sql_date($this->ci->input->post('validity_end_date'));
    }

    if(isset($start_date) && isset($end_date)){


        array_push($where, ' AND ( (date_format('.db_prefix().'goods_receipt.date_add,"%Y-%m-%d") BETWEEN "'.$start_date.'" AND "'.$end_date.'") OR (date_format('.db_prefix().'goods_delivery.date_add,"%Y-%m-%d") BETWEEN "'.$start_date.'" AND "'.$end_date.'") OR (date_format('.db_prefix().'internal_delivery_note.date_add,"%Y-%m-%d") BETWEEN "'.$start_date.'" AND "'.$end_date.'") OR (date_format('.db_prefix().'wh_loss_adjustment.date_create,"%Y-%m-%d") BETWEEN "'.$start_date.'" AND "'.$end_date.'") )');



    }elseif(isset($start_date) && !isset($end_date)){

        array_push($where, ' AND ( (date_format('.db_prefix().'goods_receipt.date_add,"%Y-%m-%d") >= "'.$start_date.'" ) OR (date_format('.db_prefix().'goods_delivery.date_add,"%Y-%m-%d") >= "'.$start_date.'") OR (date_format('.db_prefix().'internal_delivery_note.date_add,"%Y-%m-%d") >= "'.$start_date.'") OR (date_format('.db_prefix().'wh_loss_adjustment.date_create,"%Y-%m-%d") >= "'.$start_date.'"))');


    }elseif(!isset($start_date) && isset($end_date)){

        array_push($where, ' AND ( (date_format('.db_prefix().'goods_receipt.date_add,"%Y-%m-%d") <= "'.$end_date.'" ) OR (date_format('.db_prefix().'goods_delivery.date_add,"%Y-%m-%d") <= "'.$end_date.'") OR (date_format('.db_prefix().'internal_delivery_note.date_add,"%Y-%m-%d") <= "'.$end_date.'") OR (date_format('.db_prefix().'wh_loss_adjustment.date_create,"%Y-%m-%d") <= "'.$end_date.'"))');

    }



$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'goods_transaction_detail.id',db_prefix().'goods_transaction_detail.old_quantity',db_prefix().'goods_transaction_detail.from_stock_name',db_prefix().'goods_transaction_detail.to_stock_name',db_prefix().'goods_receipt.date_add as 1_date_add',db_prefix().'goods_delivery.date_add as 2_date_add',db_prefix().'internal_delivery_note.date_add as 4_date_add',db_prefix().'wh_loss_adjustment.date_create as 3_date_add', db_prefix().'goods_transaction_detail.date_add as opening_stock_date_add']);


$output  = $result['output'];
$rResult = $result['rResult'];



    foreach ($rResult as $aRow) {
        $row = [];


    $row[] = $aRow['id'];

    if($aRow[db_prefix().'goods_transaction_detail.status'] == 1){

         $value = get_goods_receipt_code($aRow['goods_receipt_id']) != null ? get_goods_receipt_code($aRow['goods_receipt_id'])->goods_receipt_code : '';

         if($value != ''){
            $row[] = '<a href="' . admin_url('warehouse/manage_purchase/' . $aRow['goods_receipt_id']) . '" >'. $value.'</a>';
         }else{
            $row[] = '';
         }



    }elseif($aRow[db_prefix().'goods_transaction_detail.status'] == 2){

         $value = get_goods_delivery_code($aRow['goods_receipt_id']) != null ? get_goods_delivery_code($aRow['goods_receipt_id'])->goods_delivery_code : '';

         if($value != ''){
            $row[] = '<a href="' . admin_url('warehouse/manage_delivery/' . $aRow['goods_receipt_id']) . '" >'. $value.'</a>';
         }else{
            $row[] = '';
         }


    }elseif($aRow[db_prefix().'goods_transaction_detail.status'] == 4){

         $value = get_internal_delivery_code($aRow['goods_receipt_id']) != null ? get_internal_delivery_code($aRow['goods_receipt_id'])->internal_delivery_code : '';

         if($value != ''){
            $row[] = '<a href="' . admin_url('warehouse/manage_internal_delivery/' . $aRow['goods_receipt_id']) . '" >'. $value.'</a>';
         }else{
            $row[] = '';
         }


    }else{
      //3 lost adjustment
         $value = "LA#".$aRow['goods_receipt_id'];

         if($value != ''){
            $row[] = '<a href="' . admin_url('warehouse/view_lost_adjustment/' . $aRow['goods_receipt_id']) . '" >'. $value.'</a>';
         }else{
            $row[] = '';
         }
    }    

     $row[] = wh_get_item_variatiom($aRow['commodity_id']);

     $warehouse_name ='';
     $warehouse_code ='';

    if($aRow[db_prefix().'goods_transaction_detail.status'] == 4){
     
        $str_code = '';
        $str = '';
          if ($aRow['from_stock_name'] != '' && $aRow['from_stock_name'] != '0') {

            $team = get_warehouse_name($aRow['from_stock_name']);
            if($team){
              $value = $team != null ? get_object_vars($team)['warehouse_name'] : '';

              $value_code = $team != null ? get_object_vars($team)['warehouse_code'] : '';

              $str .= 'From: <span class="label label-tag tag-id-1"><span class="tag">' . $value . '</span><span class="hide">, </span></span>&nbsp';
              
              $str_code .= 'From: <span class="label label-tag tag-id-1"><span class="tag">' . $value_code . '</span><span class="hide">, </span></span>&nbsp';

              $warehouse_name .= $str;
              $warehouse_code .= $str_code;

                $warehouse_name .='<br/>';
                $warehouse_code .='<br/>';
            }

          }

         $str_code = '';
        $str = '';
          if ($aRow['to_stock_name'] != '' && $aRow['to_stock_name'] != '0') {

            $team1 = get_warehouse_name($aRow['to_stock_name']);
            if($team1){
              $value1 = $team1 != null ? get_object_vars($team1)['warehouse_name'] : '';

              $value_code1 = $team1 != null ? get_object_vars($team1)['warehouse_code'] : '';

              $str .= '- To: <span class="label label-tag tag-id-1"><span class="tag">' . $value1 . '</span><span class="hide">, </span></span>&nbsp';
              
              $str_code .= '- To: <span class="label label-tag tag-id-1"><span class="tag">' . $value_code1 . '</span><span class="hide">, </span></span>&nbsp';

              $warehouse_name .= $str;
              $warehouse_code .= $str_code;

              
            }

          }


    }else{

         $str_code = '';
        $str = '';

        if(isset($aRow[db_prefix().'goods_transaction_detail.warehouse_id']) && ($aRow[db_prefix().'goods_transaction_detail.warehouse_id'] !='')){
          $arr_warehouse = new_explode(',', $aRow[db_prefix().'goods_transaction_detail.warehouse_id']);

          if(count($arr_warehouse) > 0){

            foreach ($arr_warehouse as $wh_key => $warehouseid) {
                $str_code = '';
              $str = '';
              if ($warehouseid != '' && $warehouseid != '0') {

                $team = get_warehouse_name($warehouseid);
                if($team){
                  $value = $team != null ? get_object_vars($team)['warehouse_name'] : '';

                  $value_code = $team != null ? get_object_vars($team)['warehouse_code'] : '';

                  $str .= '<span class="label label-tag tag-id-1"><span class="tag">' . $value . '</span><span class="hide">, </span></span>&nbsp';
                  
                  $str_code .= '<span class="label label-tag tag-id-1"><span class="tag">' . $value_code . '</span><span class="hide">, </span></span>&nbsp';

                  $warehouse_name .= $str;
                  $warehouse_code .= $str_code;

                  if($wh_key%3 ==0){
                    $warehouse_name .='<br/>';
                    $warehouse_code .='<br/>';
                  }
                }

              }
            }

          } else {
            $warehouse_name = '';
            $warehouse_code = '';
          }
        }
    }


     $row[] = $warehouse_code;
     $row[] = $warehouse_name;

     if($aRow['goods_receipt_id'] == 0){

       $row[] = _d(date('Y-m-d', strtotime($aRow['opening_stock_date_add']))); 
     }else{
       $row[] = _d($aRow[$aRow[db_prefix().'goods_transaction_detail.status'].'_date_add']); 
     }


    switch ($aRow[db_prefix().'goods_transaction_detail.status']) {
      case 1:
           //stock_import
         $row[] = $aRow['old_quantity']; 
         break;
      case 2:
           //stock_export
        $row[] = (float)$aRow['old_quantity']+ (float)$aRow['quantity'];
         break;
      case 3:
           //lost adjustment
         $row[] = $aRow['old_quantity']; 
         break;
      case 4:
           //internal_delivery_note
         $row[] = $aRow['old_quantity'];
         break;
       
     } 

    

    //update view old quantity, new quantity
    if($aRow['old_quantity'] != null && $aRow['old_quantity'] != ''){
         switch ($aRow[db_prefix().'goods_transaction_detail.status']) {
           case 1:
           //stock_import
              $row[] = (float)$aRow['old_quantity'] + (float)$aRow['quantity'];

               break;
           case 2:
           //stock_export
               $row[] = (float)$aRow['old_quantity'];
               break;
           case 3:
           //lost adjustment
               $row[] = $aRow['quantity'];
               break;
           case 4:
           //internal_delivery_note
               $row[] = app_format_money((float)$aRow['old_quantity'] - (float)$aRow['quantity'],'');
               break;
       } 

    }else{
       $row[] = $aRow['quantity'];
    }
    


        $lot_number ='';
         if(($aRow['lot_number'] != null) && ( $aRow['lot_number'] != '') ){
            $array_lot_number = new_explode(',', $aRow['lot_number']);
            foreach ($array_lot_number as $key => $lot_value) {
                
                if($key%2 ==0){
                  $lot_number .= $lot_value;
                }else{
                  $lot_number .= ' : '.$lot_value.' ';
                }

            }
         }



     $row[] = $lot_number;

     $expiry_date ='';
         if(($aRow[db_prefix().'goods_transaction_detail.expiry_date'] != null) && ( $aRow[db_prefix().'goods_transaction_detail.expiry_date'] != '') ){
            $array_expiry_date = new_explode(',', $aRow[db_prefix().'goods_transaction_detail.expiry_date']);
            foreach ($array_expiry_date as $key => $expiry_date_value) {
                
                if($key%2 ==0){
                  $expiry_date .= _d($expiry_date_value);
                }else{
                  $expiry_date .= ' : '.$expiry_date_value.' ';
                }

            }
         }

     $row[] = $expiry_date;

        /*get frist 100 character */
        if (new_strlen($aRow[db_prefix().'goods_transaction_detail.serial_number'] ?? '') > 40) {
            $pos = strpos($aRow[db_prefix().'goods_transaction_detail.serial_number'], ' ', 40);
            $description_sub = substr($aRow[db_prefix().'goods_transaction_detail.serial_number'], 0, $pos).'...';
        } else {
            $description_sub = $aRow[db_prefix().'goods_transaction_detail.serial_number'] ?? '';
        }

     $row[] = '<span class="pull-left" data-toggle="tooltip" title="" data-original-title="'. new_str_replace(',', ', ', $aRow[db_prefix().'goods_transaction_detail.serial_number']).'">'.$description_sub.'</span>';

     $row[] = $aRow['note'];
     switch ($aRow[db_prefix().'goods_transaction_detail.status']) {
           case 1:
               $row[] = _l('stock_import');
               break;
           case 2:
               $row[] = _l('stock_export');
               break;
           case 3:
               $row[] = _l('lost_adjustment');
               break;
           case 4:
               $row[] = _l('internal_delivery_note');
               break;
       }  
     
     
    $output['aaData'][] = $row;

    }

