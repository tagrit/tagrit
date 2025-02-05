<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
    <?php if($view == ''){ ?>
      <?php
      
      if(isset($estimate)){
        echo form_open(site_url('purchase/vendors_portal/quotation_form/'.$estimate->id),array('autocomplete'=>'off', 'class'=>'_transaction_form'));
      }else{
        echo form_open(site_url('purchase/vendors_portal/quotation_form'),array('autocomplete'=>'off', 'class'=>'_transaction_form'));
      }
      ?>
    <?php } ?>
      <div class="col-md-12">
        <?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s accounting-template estimate">
   <div class="panel-body">
    <?php $additional_discount = 0; ?>
            <input type="hidden" name="additional_discount" value="<?php echo pur_html_entity_decode($additional_discount); ?>">

      <div class="horizontal-scrollable-tabs preview-tabs-top">
          <div class="horizontal-tabs">
             <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                <li role="presentation" class="<?php if($this->input->get('tab') != 'discussion'){echo 'active';} ?>">
                   <a href="#general_infor" aria-controls="general_infor" role="tab" data-toggle="tab">
                   <?php echo _l('pur_general_infor'); ?>
                   </a>
                </li>
                
               <?php if($view == 1){ ?> 
                <li role="presentation" class="tab-separator <?php if($this->input->get('tab') === 'discussion'){echo 'active';} ?>">
                  <?php
                            $totalComments = total_rows(db_prefix().'pur_comments',['rel_id' => $estimate->id, 'rel_type' => 'pur_quotation']);
                            ?>
                   <a href="#discuss" aria-controls="discuss" role="tab" data-toggle="tab">
                   <?php echo _l('pur_discuss'); ?>
                    <span class="badge comments-indicator<?php echo $totalComments == 0 ? ' hide' : ''; ?>"><?php echo $totalComments; ?></span>
                   </a>
                </li> 
                <?php } ?>
             </ul>
          </div>
       </div>

       <div class="tab-content">
        <div role="tabpanel" class="tab-pane ptop10 <?php if($this->input->get('tab') != 'discussion'){echo 'active';} ?>" id="general_infor">
          <div class="row">
         <div class="col-md-6">
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
            <div class="col-md-12">
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
            
             <div class="col-md-12">
                  <?php $value = (isset($estimate) ? _d($estimate->date) : _d(date('Y-m-d'))); ?>
                  <?php echo pur_render_date_input('date','estimate_add_edit_date',$value); ?>
               </div>

            <div class="col-md-12">
              <label for="pur_request"><?php echo _l('pur_request'); ?></label>
              <select name="pur_request" id="pur_request" onchange="coppy_pur_request(); return false;" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                <option value=""></option>
                  <?php foreach($pur_request as $s) { ?>
                  <option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if(isset($estimate) && $estimate->pur_request != '' && $estimate->pur_request->id == $s['id']){ echo 'selected'; } ?> ><?php echo pur_html_entity_decode($s['pur_rq_code'].' - '.$s['pur_rq_name']); ?></option>
                    <?php } ?>
              </select>
            </div>
            
            <div class="clearfix mbot15"></div>

         </div>

         <div class="col-md-6 ">
           <?php
              $currency_attr = array('data-show-subtext'=>true);
              $selected = '';
              foreach($currencies as $currency){

                if(isset($estimate) && $estimate->currency != 0){
                  $selected = $estimate->currency;
                }else{
                  if($vendor_currency != 0 && !isset($estimate)){
                    $selected = $vendor_currency;
                  }else{
                    if($currency['isdefault'] == 1){
                      $selected = $currency['id'];
                    }
                  }
                }
              }

              ?>
           <?php echo render_select('currency', $currencies, array('id','name','symbol'), 'invoice_add_edit_currency', $selected, $currency_attr); ?>
        </div>
         <div class="col-md-6">
               <div class="row">
              <div class="col-md-12 form-group">
                   

                <label for="buyer" class="control-label"><?php echo _l('buyer'); ?></label>
                <select name="buyer" class="selectpicker" id="buyer" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                   <option value=""></option>
                   <?php foreach($staff as $st){ ?>
                    <option value="<?php echo pur_html_entity_decode($st['staff_id']); ?>" <?php if(isset($estimate) && $estimate->buyer == $st['staff_id']){ echo 'selected';} ?>><?php echo get_staff_full_name($st['staff_id']); ?></option>
                   <?php } ?>
                 </select>
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
                  echo pur_render_date_input('expirydate','estimate_add_edit_expirydate',$value); ?>
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
    <?php if($view == 1){ ?> 
      <div role="tabpanel" class="tab-pane <?php if($this->input->get('tab') === 'discussion'){echo ' active';} ?>" id="discuss">
        <?php echo form_open($this->uri->uri_string()) ;?>
         <div class="contract-comment">
            <textarea name="content" rows="4" class="form-control"></textarea>
            <button type="submit" class="btn btn-info mtop10 pull-right" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('proposal_add_comment'); ?></button>
            <?php echo form_hidden('action','quo_comment'); ?>
         </div>
         <?php echo form_close(); ?>
         <div class="clearfix"></div>
         <?php
            $comment_html = '';
            foreach ($comments as $comment) {
             $comment_html .= '<div class="contract_comment mtop10 mbot20" data-commentid="' . $comment['id'] . '">';
             if($comment['staffid'] != 0){
              $comment_html .= staff_profile_image($comment['staffid'], array(
               'staff-profile-image-small',
               'media-object img-circle pull-left mright10'
            ));
            }
            $comment_html .= '<div class="media-body valign-middle">';
            $comment_html .= '<div class="mtop5">';
            $comment_html .= '<b>';
            if($comment['staffid'] != 0){
              $comment_html .= get_staff_full_name($comment['staffid']);
            } else {
              $comment_html .= get_vendor_company_name(get_vendor_user_id());
            }
            $comment_html .= '</b>';
            $comment_html .= ' - <small class="mtop10 text-muted">' . time_ago($comment['dateadded']) . '</small>';
            $comment_html .= '</div>';
        
            $comment_html .= check_for_links($comment['content']) . '<br />';
            $comment_html .= '</div>';
            $comment_html .= '</div>';
            $comment_html .= '<hr />';
            }
            echo $comment_html; ?>
      </div>
    <?php } ?>
  </div>
   </div>
   <div class="panel-body mtop10 invoice-item">
      <div class="row">
        <div class="col-md-4" data-toggle="tooltip" data-placement="top" title="<?php echo _l('vendor_item_select_note'); ?>">
          <?php $this->load->view('purchase/item_include/main_item_select'); ?>

        </div>


             <?php
              $estimate_currency = $base_currency;
              if($vendor_currency != 0 && !isset($estimate)){
                $estimate_currency = pur_get_currency_by_id($vendor_currency);
              }

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
              }else if(!isset($estimate) && $vendor_currency != 0){
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
                  <th width="17%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
                  <th width="10%" align="right"><?php echo _l('unit_price'); ?><span class="th_currency"><?php echo '('.$estimate_currency->name.')'; ?></span></th>
                  <th width="7%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
                  <th width="10%" align="right"><?php echo _l('subtotal_before_tax'); ?><span class="th_currency"><?php echo '('.$estimate_currency->name.')'; ?></span></th>
                  <th width="12%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
                  <th width="10%" align="right"><?php echo _l('tax_value'); ?><span class="th_currency"><?php echo '('.$estimate_currency->name.')'; ?></span></th>
                  <th width="10%" align="right"><?php echo _l('pur_subtotal_after_tax'); ?><span class="th_currency"><?php echo '('.$estimate_currency->name.')'; ?></span></th>
                  <th width="7%" align="right"><?php echo _l('discount').'(%)'; ?></th>
                  <th width="10%" align="right"><?php echo _l('discount'); ?><span class="th_currency"><?php echo '('.$estimate_currency->name.')'; ?></span></th>
                  <th width="15%" align="right"><?php echo _l('total'); ?><span class="th_currency"><?php echo '('.$estimate_currency->name.')'; ?></span></th>
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

      <div class="">
        <div class="panel-body bottom-transaction">
          <div class="col-md-12 pad_left_0 pad_right_0">
            <?php $value = (isset($estimate) ? $estimate->vendornote : get_purchase_option('vendor_note')); ?>
            <?php echo render_textarea('vendornote','estimate_add_edit_vendor_note',$value,array(),array(),'mtop15'); ?>
                </div>
                <div class="col-md-12 pad_left_0 pad_right_0">
                   <?php $value = (isset($estimate) ? $estimate->terms : get_purchase_option('terms_and_conditions')); ?>
            <?php echo render_textarea('terms','terms_and_conditions',$value,array(),array(),'mtop15','tinymce'); ?>
                </div>

            <div class=" text-right">
      
              <button type="button" class="btn-tr save_detail btn btn-info mleft10 transaction-submit">
              <?php echo _l('submit'); ?>
              </button>
            </div>

        </div>
    </div>
        <div class="btn-bottom-pusher"></div>
  

</div>

      </div>
       <?php if($view == ''){ ?>
      <?php echo form_close(); ?>
    <?php } ?>
      
    </div>
  </div>
</div>
</div>
<?php hooks()->do_action('app_admin_footer'); ?>
</body>
</html>
<?php require 'modules/purchase/assets/js/estimate_vendor_js.php';?>