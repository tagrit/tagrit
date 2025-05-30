<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
  <div class="row">
   <?php
   echo form_open($this->uri->uri_string(),array('id'=>'debit-note-form','class'=>'_transaction_form debit-note-form'));
   if(isset($debit_note)){
    echo form_hidden('isedit');
  }
  ?>
  <div class="col-md-12">
    <div class="panel_s debit_note accounting-template">
     <div class="additional"></div>
     <div class="panel-body">
      <?php if(isset($debit_note)){ ?>
      <?php echo format_debit_note_status($debit_note->status); ?>
      <hr class="hr-panel-heading" />
      <?php } ?>
      <div class="row">
       <div class="col-md-6">
       <div class="form-group">
       <label for="vendorid"><?php echo _l('vendor'); ?></label>
        <select name="vendorid" id="vendorid" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
            <option value=""></option>
            <?php foreach($vendors as $s) { ?>
            <option value="<?php echo pur_html_entity_decode($s['userid']); ?>" <?php if(isset($debit_note) && $debit_note->vendorid == $s['userid']){ echo 'selected'; }else{ if(isset($ven) && $ven == $s['userid']){ echo 'selected';} } ?>><?php echo pur_html_entity_decode($s['company']); ?></option>
              <?php } ?>
        </select> 
      </div>
 <div class="row">
   <div class="col-md-12">
    <hr class="hr-10" />
    <a href="#" class="edit_shipping_billing_info" data-toggle="modal" data-target="#billing_and_shipping_details"><i class="fa fa-pencil-square"></i></a>
    <?php 
     include_once(module_views_path('purchase', 'debit_notes/billing_and_shipping_template.php')); ?>

  </div>
  <div class="col-md-6">
    <p class="bold"><?php echo _l('credit_note_bill_to'); ?></p>
    <address>
     <span class="billing_street">
       <?php $billing_street = (isset($debit_note) ? $debit_note->billing_street : '--'); ?>
       <?php $billing_street = ($billing_street == '' ? '--' :$billing_street); ?>
       <?php echo $billing_street; ?></span><br>
       <span class="billing_city">
         <?php $billing_city = (isset($debit_note) ? $debit_note->billing_city : '--'); ?>
         <?php $billing_city = ($billing_city == '' ? '--' :$billing_city); ?>
         <?php echo $billing_city; ?></span>,
         <span class="billing_state">
           <?php $billing_state = (isset($debit_note) ? $debit_note->billing_state : '--'); ?>
           <?php $billing_state = ($billing_state == '' ? '--' :$billing_state); ?>
           <?php echo $billing_state; ?></span>
           <br/>
           <span class="billing_country">
             <?php $billing_country = (isset($debit_note) ? get_country_short_name($debit_note->billing_country) : '--'); ?>
             <?php $billing_country = ($billing_country == '' ? '--' :$billing_country); ?>
             <?php echo $billing_country; ?></span>,
             <span class="billing_zip">
               <?php $billing_zip = (isset($debit_note) ? $debit_note->billing_zip : '--'); ?>
               <?php $billing_zip = ($billing_zip == '' ? '--' :$billing_zip); ?>
               <?php echo $billing_zip; ?></span>
             </address>
           </div>
           <div class="col-md-6">
            <p class="bold"><?php echo _l('ship_to'); ?></p>
            <address>
             <span class="shipping_street">
               <?php $shipping_street = (isset($debit_note) ? $debit_note->shipping_street : '--'); ?>
               <?php $shipping_street = ($shipping_street == '' ? '--' :$shipping_street); ?>
               <?php echo $shipping_street; ?></span><br>
               <span class="shipping_city">
                 <?php $shipping_city = (isset($debit_note) ? $debit_note->shipping_city : '--'); ?>
                 <?php $shipping_city = ($shipping_city == '' ? '--' :$shipping_city); ?>
                 <?php echo $shipping_city; ?></span>,
                 <span class="shipping_state">
                   <?php $shipping_state = (isset($debit_note) ? $debit_note->shipping_state : '--'); ?>
                   <?php $shipping_state = ($shipping_state == '' ? '--' :$shipping_state); ?>
                   <?php echo $shipping_state; ?></span>
                   <br/>
                   <span class="shipping_country">
                     <?php $shipping_country = (isset($debit_note) ? get_country_short_name($debit_note->shipping_country) : '--'); ?>
                     <?php $shipping_country = ($shipping_country == '' ? '--' :$shipping_country); ?>
                     <?php echo $shipping_country; ?></span>,
                     <span class="shipping_zip">
                       <?php $shipping_zip = (isset($debit_note) ? $debit_note->shipping_zip : '--'); ?>
                       <?php $shipping_zip = ($shipping_zip == '' ? '--' :$shipping_zip); ?>
                       <?php echo $shipping_zip; ?></span>
                     </address>
                   </div>
                 </div>
                 <div class="row">
                   <div class="col-md-6">
                    <?php $value = (isset($debit_note) ? _d($debit_note->date) : _d(date('Y-m-d'))); ?>
                    <?php echo render_date_input('date','debit_note_date',$value); ?>
                  </div>
                   <div class="col-md-6">
                    <?php
                    $next_debit_note_number = get_option('next_debit_note_number');
                    $format = get_option('debit_note_number_format');
                    $prefix = get_option('debit_note_prefix');

                    if(isset($debit_note)){
                     $format = $debit_note->number_format;
                   }

                   $__number = '';
                   if ($format == 1) {
                    $__number = $next_debit_note_number;
                    if(isset($debit_note)){
                      $__number = $debit_note->number;
                      $prefix = '<span id="prefix">' . $debit_note->prefix . '</span>';
                    }
                  } else if($format == 2) {
                    if(isset($debit_note)){
                      $__number = $debit_note->number;
                      $prefix = $debit_note->prefix;
                      $prefix = '<span id="prefix">'. $prefix . '</span><span id="prefix_year">' .date('Y',strtotime($debit_note->date)).'</span>/';
                    } else {
                     $__number = $next_debit_note_number;
                     $prefix = $prefix.'<span id="prefix_year">'.date('Y').'</span>/';
                   }
                 } else if($format == 3) {
                   if(isset($debit_note)){
                    $yy = date('y',strtotime($debit_note->date));
                    $__number = $debit_note->number;
                    $prefix = '<span id="prefix">'. $debit_note->prefix . '</span>';
                  } else {
                   $yy = date('y');
                   $__number = $next_debit_note_number;
                 }
               } else if($format == 4) {
                if(isset($debit_note)){
                  $yyyy = date('Y',strtotime($debit_note->date));
                  $mm = date('m',strtotime($debit_note->date));
                  $__number = $debit_note->number;
                  $prefix = '<span id="prefix">'. $debit_note->prefix . '</span>';
               } else {
                $yyyy = date('Y');
                $mm = date('m');
                $__number = $next_debit_note_number;
              }
            }
            $_debit_note_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
            $isedit = isset($debit_note) ? 'true' : 'false';
            $data_original_number = isset($debit_note) ? $debit_note->number : 'false';
            ?>
            <div class="form-group">
             <label for="number"><?php echo _l('debit_note_number'); ?></label>
             <div class="input-group">
              <span class="input-group-addon">
                <?php if(isset($debit_note)){ ?>
                <a href="#" onclick="return false;" data-toggle="popover" data-container='._transaction_form' data-html="true" data-content="<label class='control-label'><?php echo _l('debit_note_prefix'); ?></label><div class='input-group'><input name='s_prefix' type='text' class='form-control' value='<?php echo $debit_note->prefix; ?>'></div><button type='button' onclick='save_sales_number_settings(this); return false;' data-url='<?php echo admin_url('debit_notes/update_number_settings/'.$debit_note->id); ?>' class='btn btn-info btn-block mtop15'><?php echo _l('submit'); ?></button>"><i class="fa fa-cog"></i></a>
                <?php } ?>
                <?php echo $prefix; ?></span>
                <input type="text" name="number" class="form-control" value="<?php echo $_debit_note_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>">
                <?php if($format == 3) { ?>
                <span class="input-group-addon">
                  <span id="prefix_year" class="format-n-yy"><?php echo $yy; ?></span>
                </span>
                <?php } else if($format == 4) { ?>
                <span class="input-group-addon">
                 <span id="prefix_month" class="format-mm-yyyy"><?php echo $mm; ?></span>
                 /
                 <span id="prefix_year" class="format-mm-yyyy"><?php echo $yyyy; ?></span>
               </span>
               <?php } ?>
             </div>
           </div>
         </div>

      </div>
    </div>
    <div class="col-md-6">
      <div class="">
       <div class="row">
        <div class="col-md-6">
         <?php

         $debit_note_currency_attr = array('disabled'=>true,'data-show-subtext'=>true);
         $debit_note_currency_attr = apply_filters_deprecated('debit_note_currency_disabled', [$debit_note_currency_attr], '2.3.0', 'debit_note_currency_attributes');

         foreach($currencies as $currency){
          if($currency['isdefault'] == 1){
           $debit_note_currency_attr['data-base'] = $currency['id'];
         }
         if(isset($debit_note)){
           if($currency['id'] == $debit_note->currency){
            $selected = $currency['id'];
          }
        } else {
          if($currency['isdefault'] == 1){
            $selected = $currency['id'];
          }
        }
      }
      $debit_note_currency_attr = hooks()->apply_filters('debit_note_currency_attributes',$debit_note_currency_attr);
      ?>
      <?php echo render_select('currency', $currencies, array('id','name','symbol'), 'currency', $selected, $debit_note_currency_attr); ?>
    </div>
    <div class="col-md-6">
     <div class="form-group select-placeholder">
      <label for="discount_type" class="control-label"><?php echo _l('discount_type'); ?></label>
      <select name="discount_type" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
       <option value="" selected><?php echo _l('no_discount'); ?></option>
       <option value="before_tax" <?php
       if(isset($debit_note)){ if($debit_note->discount_type == 'before_tax'){ echo 'selected'; }} ?>><?php echo _l('discount_type_before_tax'); ?></option>
       <option value="after_tax" <?php if(isset($debit_note)){if($debit_note->discount_type == 'after_tax'){echo 'selected';}} ?>><?php echo _l('discount_type_after_tax'); ?></option>
     </select>
   </div>
 </div>
</div>
<?php $value = (isset($debit_note) ? $debit_note->reference_no : ''); ?>
<?php echo render_input('reference_no','reference_no',$value); ?>
<?php $value = (isset($debit_note) ? $debit_note->adminnote : ''); ?>
<?php echo render_textarea('adminnote','debit_note_admin_note',$value); ?>
<?php $rel_id = (isset($debit_note) ? $debit_note->id : false); ?>
<?php echo render_custom_fields('debit_note',$rel_id); ?>
</div>
</div>
</div>
</div>
<div class="panel-body mtop10">
<div class="row">
  <div class="col-md-4">
    <?php $this->load->view('purchase/item_include/main_item_select'); ?>
  </div>
</div> 
<div class="table-responsive s_table">
 <table class="table credite-note-items-table items table-main-credit-note-edit has-calculations no-mtop">
  <thead>
   <tr>
    <th></th>
    <th width="20%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('debit_note_table_item_heading'); ?></th>
    <th width="25%" align="left"><?php echo _l('debit_note_table_item_description'); ?></th>
    <?php
   
   $qty_heading = _l('debit_note_table_quantity_heading');
   if(isset($debit_note) && $debit_note->show_quantity_as == 2 || isset($hours_quantity)){
    $qty_heading = _l('debit_note_table_hours_heading');
  } else if(isset($debit_note) && $debit_note->show_quantity_as == 3){
    $qty_heading = _l('debit_note_table_quantity_heading') .'/'._l('debit_note_table_hours_heading');
  }
  ?>
  <th width="10%" class="qty" align="right"><?php echo $qty_heading; ?></th>
  <th width="15%" align="right"><?php echo _l('debit_note_table_rate_heading'); ?></th>
  <th width="20%" align="right"><?php echo _l('debit_note_table_tax_heading'); ?></th>
  <th width="10%" align="right"><?php echo _l('debit_note_table_amount_heading'); ?></th>
  <th align="center"><i class="fa fa-cog"></i></th>
</tr>
</thead>
<tbody>
 <tr class="main">
  <td></td>
  <td>
   <textarea name="description" class="form-control" rows="4" placeholder="<?php echo _l('item_description_placeholder'); ?>"></textarea>
 </td>
 <td>
   <textarea name="long_description" rows="4" class="form-control" placeholder="<?php echo _l('item_long_description_placeholder'); ?>"></textarea>
 </td>

 <td>
   <input type="number" name="quantity" min="0" value="1" class="form-control" placeholder="<?php echo _l('item_quantity_placeholder'); ?>">
   <input type="text" placeholder="<?php echo _l('unit'); ?>" name="unit" class="form-control input-transparent text-right">
 </td>
 <td>
   <input type="number" name="rate" class="form-control" placeholder="<?php echo _l('item_rate_placeholder'); ?>">
 </td>
 <td>
   <?php
   $default_tax = unserialize(get_option('default_tax'));
   $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="taxname" multiple data-none-selected-text="'._l('no_tax').'">';
   foreach($taxes as $tax){
     $selected = '';
     if(is_array($default_tax)){
      if(in_array($tax['name'] . '|' . $tax['taxrate'],$default_tax)){
       $selected = ' selected ';
     }
   }
   $select .= '<option value="'.$tax['name'].'|'.$tax['taxrate'].'"'.$selected.'data-taxrate="'.$tax['taxrate'].'" data-taxname="'.$tax['name'].'" data-subtext="'.$tax['name'].'">'.$tax['taxrate'].'%</option>';
 }
 $select .= '</select>';
 echo $select;
 ?>
</td>
<td></td>
<td>
 <?php
 $new_item = 'undefined';
 if(isset($debit_note)){
  $new_item = true;
}
?>
<button type="button" onclick="add_item_to_table('undefined','undefined',<?php echo $new_item; ?>); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button>
</td>
</tr>
<?php if (isset($debit_note) || isset($add_items)) {
  $i               = 1;
  $items_indicator = 'newitems';
  if (isset($debit_note)) {
    $add_items       = $debit_note->items;
    $items_indicator = 'items';
  }
  foreach ($add_items as $item) {
    $manual    = false;
    $table_row = '<tr class="sortable item">';
    $table_row .= '<td class="dragger">';
    if (!is_numeric($item['qty'])) {
      $item['qty'] = 1;
    }
    $debit_note_item_taxes = get_debit_note_item_taxes($item['id']);
                              // passed like string
    if ($item['id'] == 0) {
      $debit_note_item_taxes = $item['taxname'];
      $manual             = true;
    }
    $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][itemid]', $item['id']);
    $amount = $item['rate'] * $item['qty'];
    $amount = app_format_number($amount);
                              // order input
    $table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]">';
    $table_row .= '</td>';
    $table_row .= '<td class="bold description"><textarea name="' . $items_indicator . '[' . $i . '][description]" class="form-control" rows="5">' . clear_textarea_breaks($item['description']) . '</textarea></td>';
    $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][long_description]" class="form-control" rows="5">' . clear_textarea_breaks($item['long_description']) . '</textarea></td>';

    $table_row .= '<td><input type="number" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][qty]" value="' . $item['qty'] . '" class="form-control">';
    $unit_placeholder = '';
    if(!$item['unit']){
      $unit_placeholder = _l('unit');
      $item['unit'] = '';
    }
    $table_row .= '<input type="text" placeholder="'.$unit_placeholder.'" name="'.$items_indicator.'['.$i.'][unit]" class="form-control input-transparent text-right" value="'.$item['unit'].'">';
    $table_row .= '</td>';
    $table_row .= '<td class="rate"><input type="number" data-toggle="tooltip" title="' . _l('numbers_not_formatted_while_editing') . '" onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['rate'] . '" class="form-control"></td>';
    $table_row .= '<td class="taxrate">' . $this->misc_model->get_taxes_dropdown_template('' . $items_indicator . '[' . $i . '][taxname][]', $debit_note_item_taxes, 'debit_note', $item['id'], true, $manual) . '</td>';
    $table_row .= '<td class="amount" align="right">' . $amount . '</td>';
    $table_row .= '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_item(this,' . $item['id'] . '); return false;"><i class="fa fa-times"></i></a></td>';
    $table_row .= '</tr>';
    echo $table_row;
    $i++;
  }
}
?>
</tbody>
</table>
</div>
<div class="col-md-8 col-md-offset-4">
 <table class="table text-right">
  <tbody>
   <tr id="subtotal">
    <td><span class="bold"><?php echo _l('debit_note_subtotal'); ?> :</span>
    </td>
    <td class="subtotal">
    </td>
  </tr>
  <tr id="discount_area">
    <td>
     <div class="row">
      <div class="col-md-7">
       <span class="bold"><?php echo _l('debit_note_discount'); ?></span>
     </div>
     <div class="col-md-5">
      <div class="input-group" id="discount-total">

       <input type="number" value="<?php echo (isset($debit_note) ? $debit_note->discount_percent : 0); ?>" class="form-control pull-left input-discount-percent<?php if(isset($debit_note) && !is_sale_discount($debit_note,'percent') && is_sale_discount_applied($debit_note)){echo ' hide';} ?>" min="0" max="100" name="discount_percent">

       <input type="number" data-toggle="tooltip" data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>" value="<?php echo (isset($debit_note) ? $debit_note->discount_total : 0); ?>" class="form-control pull-left input-discount-fixed<?php if(!isset($debit_note) || (isset($debit_note) && !is_sale_discount($debit_note,'fixed'))){echo ' hide';} ?>" min="0" name="discount_total">

       <div class="input-group-addon">
        <div class="dropdown">
         <a class="dropdown-toggle" href="#" id="dropdown_menu_tax_total_type" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
           <span class="discount-total-type-selected">
            <?php if(!isset($debit_note) || isset($debit_note) && (is_sale_discount($debit_note,'percent') || !is_sale_discount_applied($debit_note))) {
              echo '%';
            } else {
              echo _l('discount_fixed_amount');
            }
            ?>
          </span>
          <span class="caret"></span>
        </a>
        <ul class="dropdown-menu" id="discount-total-type-dropdown" aria-labelledby="dropdown_menu_tax_total_type">
          <li>
            <a href="#" class="discount-total-type discount-type-percent<?php if(!isset($debit_note) || (isset($debit_note) && is_sale_discount($debit_note,'percent')) || (isset($debit_note) && !is_sale_discount_applied($debit_note))){echo ' selected';} ?>">%</a>
          </li>
          <li>
            <a href="#" class="discount-total-type discount-type-fixed<?php if(isset($debit_note) && is_sale_discount($debit_note,'fixed')){echo ' selected';} ?>">
              <?php echo _l('discount_fixed_amount'); ?>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
</div>
</td>
<td class="discount-total"></td>
</tr>
<tr>
  <td>
   <div class="row">
    <div class="col-md-7">
     <span class="bold"><?php echo _l('debit_note_adjustment'); ?></span>
   </div>
   <div class="col-md-5">
     <input type="number" data-toggle="tooltip" data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>" value="<?php if(isset($debit_note)){echo $debit_note->adjustment; } else { echo 0; } ?>" class="form-control pull-left" name="adjustment">
   </div>
 </div>
</td>
<td class="adjustment"></td>
</tr>
<tr>
  <td><span class="bold"><?php echo _l('debit_note_total'); ?> :</span>
  </td>
  <td class="total">
  </td>
</tr>
</tbody>
</table>
</div>
<div id="removed-items"></div>
</div>
<div class="row">
  <div class="col-md-12 mtop15">
   <div class="panel-body bottom-transaction">
    <?php $value = (isset($debit_note) ? $debit_note->vendornote : get_purchase_option('vendor_note')); ?>
    <?php echo render_textarea('vendornote','debit_note_add_edit_vendor_note',$value,array(),array(),'mtop15'); ?>
    <?php $value = (isset($debit_note) ? $debit_note->terms : get_purchase_option('terms_and_conditions')); ?>
    <?php echo render_textarea('terms','terms_and_conditions',$value,array(),array(),'mtop15','tinymce'); ?>
    <div class="btn-bottom-toolbar text-right">

     <button class="btn-tr btn btn-info mleft10 text-right credit-note-form-submit transaction-submit">
       <?php echo _l('submit'); ?>
     </button>
   </div>
 </div>
 <div class="btn-bottom-pusher"></div>
</div>
</div>
</div>
</div>
<?php echo form_close(); ?>
<?php $this->load->view('admin/invoice_items/item'); ?>
</div>
</div>
</div>
<?php init_tail(); ?>
 <?php require 'modules/purchase/assets/js/debit_note_js.php';?>  
 </body>
 </html>
