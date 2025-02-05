<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s accounting-template estimate">
   <div class="panel-body">
      
      <div class="row">
         <div class="col-md-6 ">
          <div class="row">
            <?php $additional_discount = 0; ?>
            <input type="hidden" name="additional_discount" value="<?php echo pur_html_entity_decode($additional_discount); ?>">
            <div class="col-md-6 form-group">
              <label for="vendor"><?php echo _l('vendor'); ?></label>
              <select name="vendor" id="vendor" class="selectpicker" onchange="estimate_by_vendor(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                  <option value=""></option>
                  <?php foreach($vendors as $s) { ?>
                  <option value="<?php echo pur_html_entity_decode($s['userid']); ?>" <?php if(isset($estimate) && $estimate->vendor->userid == $s['userid']){ echo 'selected'; } ?>><?php echo pur_html_entity_decode($s['company']); ?></option>
                    <?php } ?>
              </select>
     
            </div>
            <div class="col-md-6 form-group">
              <label for="pur_request"><?php echo _l('pur_request'); ?></label>
              <select name="pur_request" id="pur_request" onchange="coppy_pur_request(); return false;" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                <option value=""></option>
                  <?php foreach($pur_request as $s) { ?>
                  <option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if(isset($estimate) && $estimate->pur_request != '' && $estimate->pur_request->id == $s['id']){ echo 'selected'; } ?> ><?php echo pur_html_entity_decode($s['pur_rq_code'].' - '.$s['pur_rq_name']); ?></option>
                    <?php } ?>
              </select>
             </div>
           </div>
            <?php
               $next_estimate_number = max_number_estimates()+1;
               $format = get_option('estimate_number_format');

                if(isset($estimate)){
                  $format = $estimate->number_format;
                }

               $prefix = get_option('estimate_prefix');

               if ($format == 1) {
                 $__number = $next_estimate_number;
                 if(isset($estimate)){
                   $__number = $estimate->number;
                   $prefix = '<span id="prefix">' . $estimate->prefix . '</span>';
                 }
               } else if($format == 2) {
                 if(isset($estimate)){
                   $__number = $estimate->number;
                   $prefix = $estimate->prefix;
                   $prefix = '<span id="prefix">'. $prefix . '</span><span id="prefix_year">' . date('Y',strtotime($estimate->date)).'</span>/';
                 } else {
                   $__number = $next_estimate_number;
                   $prefix = $prefix.'<span id="prefix_year">'.date('Y').'</span>/';
                 }
               } else if($format == 3) {
                  if(isset($estimate)){
                   $yy = date('y',strtotime($estimate->date));
                   $__number = $estimate->number;
                   $prefix = '<span id="prefix">'. $estimate->prefix . '</span>';
                 } else {
                  $yy = date('y');
                  $__number = $next_estimate_number;
                }
               } else if($format == 4) {
                  if(isset($estimate)){
                   $yyyy = date('Y',strtotime($estimate->date));
                   $mm = date('m',strtotime($estimate->date));
                   $__number = $estimate->number;
                   $prefix = '<span id="prefix">'. $estimate->prefix . '</span>';
                 } else {
                  $yyyy = date('Y');
                  $mm = date('m');
                  $__number = $next_estimate_number;
                }
               }

               $_estimate_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
               $isedit = isset($estimate) ? 'true' : 'false';
               $data_original_number = isset($estimate) ? $estimate->number : 'false';
               ?>
               <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                 <label for="number"><?php echo _l('estimate_add_edit_number'); ?></label>
                 <div class="input-group">
                    <span class="input-group-addon">
                    <?php if(isset($estimate)){ ?>
                    <a href="#" onclick="return false;" data-toggle="popover" data-container='._transaction_form' data-html="true" data-content="<label class='control-label'><?php echo _l('settings_sales_estimate_prefix'); ?></label><div class='input-group'><input name='s_prefix' type='text' class='form-control' value='<?php echo pur_html_entity_decode($estimate->prefix); ?>'></div><button type='button' onclick='save_sales_number_settings(this); return false;' data-url='<?php echo admin_url('estimates/update_number_settings/'.$estimate->id); ?>' class='btn btn-info btn-block mtop15'><?php echo _l('submit'); ?></button>"><i class="fa fa-cog"></i></a>
                     <?php }
                      echo pur_html_entity_decode($prefix);
                    ?>
                   </span>
                    <input type="text" name="number" class="form-control" value="<?php echo pur_html_entity_decode($_estimate_number); ?>" data-isedit="<?php echo pur_html_entity_decode($isedit); ?>" data-original-number="<?php echo pur_html_entity_decode($data_original_number); ?>">
                    <?php if($format == 3) { ?>
                    <span class="input-group-addon">
                       <span id="prefix_year" class="format-n-yy"><?php echo pur_html_entity_decode($yy); ?></span>
                    </span>
                    <?php } else if($format == 4) { ?>
                     <span class="input-group-addon">
                       <span id="prefix_month" class="format-mm-yyyy"><?php echo pur_html_entity_decode($mm); ?></span>
                       /
                       <span id="prefix_year" class="format-mm-yyyy"><?php echo pur_html_entity_decode($yyyy); ?></span>
                    </span>
                    <?php } ?>
                 </div>
              </div>
            </div>
            <div class="col-md-6">
                         <?php
                        $selected = '';
                        foreach($staff as $member){
                         if(isset($estimate)){
                           if($estimate->buyer == $member['staffid']) {
                             $selected = $member['staffid'];
                           }
                         }elseif($member['staffid'] == get_staff_user_id()){
                          $selected = $member['staffid'];
                         }
                        }
                        echo render_select('buyer',$staff,array('staffid',array('firstname','lastname')),'buyer',$selected);
                        ?>
            </div>
          </div>
            
            <div class="clearfix mbot15"></div>
            <?php $rel_id = (isset($estimate) ? $estimate->id : false); ?>
            
         </div>
         <div class="col-md-6">
            <div class=" no-shadow">
              
               <div class="row">
                  <div class="col-md-6">
                     <?php

                        $currency_attr = array();
                        
                        foreach($currencies as $currency){
                          if($currency['isdefault'] == 1){
                            $currency_attr['data-base'] = $currency['id'];
                          }
                          if(isset($estimate) && $estimate->currency != 0){
                            if($currency['id'] == $estimate->currency){
                              $selected = $currency['id'];
                            }
                          } else{
                             if($currency['isdefault'] == 1){
                              $selected = $currency['id'];
                            }
                          }
                        }
                        
                        ?>
                     <?php echo render_select('currency', $currencies, array('id','name','symbol'), 'estimate_add_edit_currency', $selected, $currency_attr); ?>
                  </div>
                  <div class="col-md-6">
                  <?php $value = (isset($estimate) ? _d($estimate->date) : _d(date('Y-m-d'))); ?>
                  <?php echo render_date_input('date','estimate_add_edit_date',$value); ?>
               </div>
               <div class="col-md-6">
                  <?php
                  $value = '';
                  if(isset($estimate)){
                    $value = _d($estimate->expirydate);
                  } else {
                      if(get_option('estimate_due_after') != 0){
                          $value = _d(date('Y-m-d', strtotime('+' . get_option('estimate_due_after') . ' DAY', strtotime(date('Y-m-d')))));
                      }
                  }
                  echo render_date_input('expirydate','estimate_add_edit_expirydate',$value); ?>
               </div>

               <div class="col-md-6">
                  <div class="form-group select-placeholder">
                      <label for="discount_type"
                          class="control-label"><?php echo _l('discount_type'); ?></label>
                      <select name="discount_type" class="selectpicker" data-width="100%"
                          data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                         
                          <option value="before_tax" <?php
                    if (isset($estimate)) {
                        if ($estimate->discount_type == 'before_tax') {
                            echo 'selected';
                        }
                    } ?>><?php echo _l('discount_type_before_tax'); ?></option>
                          <option value="after_tax" <?php if (isset($estimate)) {
                        if ($estimate->discount_type == 'after_tax' || $estimate->discount_type == null) {
                            echo 'selected';
                        }
                    }else {
                      echo 'selected';
                    } ?>><?php echo _l('discount_type_after_tax'); ?></option>
                      </select>
                  </div>
               </div>
                 
               </div>
            </div>
         </div>

               
           
      </div>
   </div>
   <div class="panel-body mtop10 invoice-item">
  <div class="row">
    <div class="col-md-4">
      <?php $this->load->view('purchase/item_include/main_item_select'); ?>
    </div>
    <?php
          $estimate_currency = $base_currency;
          if(isset($estimate) && $estimate->currency != 0){
            $estimate_currency = pur_get_currency_by_id($estimate->currency);
          } 

          $from_currency = (isset($estimate) && $estimate->from_currency != null) ? $estimate->from_currency : $base_currency->id;
          echo form_hidden('from_currency', $from_currency);

        ?>
    <div class="col-md-8 <?php if($estimate_currency->id == $base_currency->id){ echo 'hide'; } ?>" id="currency_rate_div">
      <div class="col-md-10 text-right">
        
        <p class="mtop10"><?php echo _l('currency_rate'); ?><span id="convert_str"><?php echo ' ('.$base_currency->name.' => '.$estimate_currency->name.'): ';  ?></span></p>
      </div>
      <div class="col-md-2 pull-right">
        <?php $currency_rate = 1;
          if(isset($estimate) && $estimate->currency != 0){
            $currency_rate = pur_get_currency_rate($base_currency->name, $estimate_currency->name);
          }
        echo render_input('currency_rate', '', $currency_rate, 'number', [], [], '', 'text-right'); 
        ?>
      </div>
    </div>
  </div>

  <div class="row">
   <div class="col-md-12">
    <div class="table-responsive s_table ">
        <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
          <thead>
            <tr>
              <th></th>
              <th width="20%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
              <th width="10%" align="right"><?php echo _l('unit_price'); ?><span class="th_currency"><?php echo '('.$estimate_currency->name.')'; ?></span></th>
              <th width="10%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
              <th width="10%" align="right"><?php echo _l('subtotal_before_tax'); ?><span class="th_currency"><?php echo '('.$estimate_currency->name.')'; ?></span></th>
              <th width="12%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
              <th width="10%" align="right"><?php echo _l('tax_value'); ?><span class="th_currency"><?php echo '('.$estimate_currency->name.')'; ?></span></th>
              <th width="10%" align="right"><?php echo _l('pur_subtotal_after_tax'); ?><span class="th_currency"><?php echo '('.$estimate_currency->name.')'; ?></span></th>
              <th width="7%" align="right"><?php echo _l('discount').'(%)'; ?></th>
              <th width="10%" align="right"><?php echo _l('discount(money)'); ?><span class="th_currency"><?php echo '('.$estimate_currency->name.')'; ?></span></th>
              <th width="10%" align="right"><?php echo _l('total'); ?><span class="th_currency"><?php echo '('.$estimate_currency->name.')'; ?></span></th>
              <th align="center"><i class="fa fa-cog"></i></th>
            </tr>
          </thead>
          <tbody>
            <?php echo $pur_quotation_row_template; ?>
          </tbody>
        </table>
      </div>
     <div class="col-md-8 col-md-offset-4">
      <table class="table text-right">
        <tbody>
          <tr id="subtotal">
            <td><span class="bold"><?php echo _l('subtotal'); ?> :</span>
              <?php echo form_hidden('total_mn', ''); ?>
            </td>
            <td class="wh-subtotal">
            </td>
          </tr>
          <tr id="total_discount">
            <td><span class="bold"><?php echo _l('total_discount'); ?> :</span>
              <?php echo form_hidden('dc_total', ''); ?>
            </td>
            <td class="wh-total_discount">
            </td>
          </tr>

          <tr>
            <td>
             <div class="row">
              <div class="col-md-9">
               <span class="bold"><?php echo _l('pur_shipping_fee'); ?></span>
             </div>
             <div class="col-md-3">
              <input type="number" onchange="pur_calculate_total()" data-toggle="tooltip" value="<?php if(isset($estimate)){ echo $estimate->shipping_fee; }else{ echo '0';} ?>" class="form-control pull-left text-right" name="shipping_fee">
            </div>
          </div>
          </td>
          <td class="shiping_fee">
          </td>
          </tr>
          
          <tr id="totalmoney">
            <td><span class="bold"><?php echo _l('grand_total'); ?> :</span>
              <?php echo form_hidden('grand_total', ''); ?>
            </td>
            <td class="wh-total">
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div id="removed-items"></div>
    </div>
    </div>
   </div>
   <div class="row">
      <div class="col-md-12 mtop15">
         <div class="panel-body bottom-transaction">
            <?php $value = (isset($estimate) ? $estimate->vendornote : get_purchase_option('vendor_note')); ?>
            <?php echo render_textarea('vendornote','estimate_add_edit_vendor_note',$value,array(),array(),'mtop15'); ?>
            <?php $value = (isset($estimate) ? $estimate->terms : get_purchase_option('terms_and_conditions')); ?>
            <?php echo render_textarea('terms','terms_and_conditions',$value,array(),array(),'mtop15','tinymce'); ?>
            <div class="btn-bottom-toolbar text-right">
              
              <button type="button" class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
              <?php echo _l('submit'); ?>
              </button>
            </div>
         </div>
           <div class="btn-bottom-pusher"></div>
      </div>
   </div>
</div>
