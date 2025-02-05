<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s mbot10">
               <div class="panel-body _buttons">
                  <?php echo form_hidden('type',$type); ?>

                  <div class="horizontal-scrollable-tabs preview-tabs-top">
                   <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                   <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                   <div class="horizontal-tabs">

                     <ul class="nav nav-tabs nav-tabs-horizontal no-margin" role="tablist">
                        <?php if(has_permission('accounting_bills','','create')){ ?>
                            <li class="<?php echo ($type == 'new_bill' ? 'active' : '') ?>">
                              <a href="<?php echo admin_url('accounting/bill'); ?>"><?php echo _l('add_new_bill'); ?></a>
                            </li>
                        <?php } ?>
                         <li class="<?php echo ($type == 'unpaid' ? 'active' : '') ?>">
                           <a href="<?php echo admin_url('accounting/bills?type=unpaid'); ?>"><?php echo _l('unpaid_bills'); ?></a>
                         </li>
                         <li class="<?php echo ($type == 'approved' ? 'active' : '') ?>">
                              <a href="<?php echo admin_url('accounting/bills?type=approved'); ?>"><?php echo _l('approved_bills'); ?></a>
                         </li>
                         <li class="">
                           <a href="<?php echo admin_url('accounting/checks'); ?>"><?php echo _l('write_checks'); ?></a>
                         </li>
                         <li class="<?php echo ($type == 'paid' ? 'active' : '') ?>">
                           <a href="<?php echo admin_url('accounting/bills?type=paid'); ?>"><?php echo _l('paid_bills'); ?></a>
                         </li>
                         <li class="<?php echo ($type == 'check_register' ? 'active' : '') ?>">
                              <a href="<?php echo admin_url('accounting/check_register'); ?>"><?php echo _l('check_register'); ?></a>
                         </li>
                         <li class="<?php echo ($type == 'configure_checks' ? 'active' : '') ?>">
                              <a href="<?php echo admin_url('accounting/configure_checks'); ?>"><?php echo _l('configure_checks'); ?></a>
                         </li>
                     </ul>
                   </div>
                 </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12" id="small-table">
                  <div class="panel_s">
                     <?php
            if(isset($bill)){
             echo form_hidden('is_edit','true');
            }
            ?>
         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'expense-form','class'=>'dropzone dropzone-manual')) ;?>
                     <div class="panel-body">
                        <div class="clearfix"></div>
                        <h4 class="no-margin"><?php echo $title; ?></h4>
            <hr class="hr-panel-heading" />
            <div class="col-md-6">
            <?php
                $option = '';
                $option .= '<div class="row">';
                $option .= '<div class="col-md-12">';
                $option .= '<div class="form-group">';
                $option .= '<lable for="vendor">'._l('vendor').'</label>';
                $option .= '<select name="vendor" id="vendor" data-width="100%" class="selectpicker" data-live-search="true" data-none-selected-text="'. _l('ticket_settings_none_assigned').'">';
                $option .= '<option value=""></option>';
                foreach($list_vendor as $ven){
                    $select = '';
                    if( isset($bill) && $bill->vendor == $ven['userid']){
                        $select = 'selected';
                    }else{
                        $select = ''; 
                    }
                    $option .= '<option value="'.$ven['userid'].'" '.$select.'>'. $ven['company'].'</option>';
                }
                $option .= '</select>';
                $option .= '</div>';
                $option .= '</div>';
                $option .= '</div>';
                echo new_html_entity_decode($option);
             ?>
             <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('expense_name_help'); ?> - <?php echo _l('expense_field_billable_help',_l('expense_name')); ?>"></i>
                  <?php $value = (isset($bill) ? $bill->expense_name : ''); ?>
                  <?php echo render_input('expense_name','expense_name',$value); ?>
            <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('expense_field_billable_help',_l('expense_add_edit_note')); ?>"></i>
            <?php $value = (isset($bill) ? $bill->note : ''); ?>
            <?php echo render_textarea('note','expense_add_edit_note',$value,array('rows'=>4),array()); ?>
            <div class="row">
               <div class="col-md-6">
                  <?php $value = (isset($bill) ? _d($bill->date) : _d(date('Y-m-d')));
                     $date_attrs = array();
                     if(isset($bill) && $bill->recurring > 0 && $bill->last_recurring_date != null) {
                       $date_attrs['disabled'] = true;
                     }
                     ?>
                  <?php echo render_date_input('date','bill_date',$value,$date_attrs);?>
               </div>
               <div class="col-md-6">
                  <?php $due_date = (isset($bill) ? _d($bill->due_date) : _d(date('Y-m-d')));
                  echo render_date_input('due_date','acc_due_date',$due_date,$date_attrs); ?>
               </div>
            </div>
                  <div class="btn-bottom-toolbar text-right">
                     <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                  </div>
               </div>
               <div class="col-md-6">
                  <?php if(isset($bill) && $bill->attachment !== ''){ ?>
                  <div class="row">
                     <div class="col-md-10">
                        <i class="<?php echo get_mime_class($bill->filetype); ?>"></i> <a href="<?php echo admin_url('accounting/download_file/bill/'.$bill->id); ?>"><?php echo $bill->attachment; ?></a>
                     </div>
                     <?php if($bill->attachment_added_from == get_staff_user_id() || is_admin()){ ?>
                     <div class="col-md-2 text-right">
                        <a href="<?php echo admin_url('accounting/delete_bill_attachment/'.$bill->id); ?>" class="text-danger _delete"><i class="fa fa fa-times"></i></a>
                     </div>
                    <?php } ?>
                  </div>
                  
                  <?php } ?>
                  <?php if(!isset($bill) || (isset($bill) && $bill->attachment == '')){ ?>
                  <div id="dropzoneDragArea" class="dz-default dz-message">
                     <span><?php echo _l('acc_attachment'); ?></span>
                  </div>
                  <div class="dropzone-previews"></div>
                  <?php } ?>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group select-placeholder">
                           <label class="control-label" for="tax"><?php echo _l('tax_1'); ?></label>
                           <select class="selectpicker display-block" data-width="100%" name="tax" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                              <option value=""><?php echo _l('no_tax'); ?></option>
                              <?php foreach($taxes as $tax){
                                 $selected = '';
                                 if(isset($bill)){
                                   if($tax['id'] == $bill->tax){
                                     $selected = 'selected';
                                   }
                                 } ?>
                              <option
                                 value="<?php echo $tax['id']; ?>"
                                 <?php echo $selected; ?>
                                 data-percent="<?php echo $tax['taxrate']; ?>"
                                 data-subtext="<?php echo $tax['name']; ?>">
                                 <?php echo $tax['taxrate']; ?>%
                              </option>
                              <?php } ?>
                           </select>

                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group select-placeholder">
                           <label class="control-label" for="tax2"><?php echo _l('tax_2'); ?></label>
                           <select class="selectpicker display-block" data-width="100%" name="tax2" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" <?php if(!isset($bill) || isset($bill) && $bill->tax == 0){echo 'disabled';} ?>>
                              <option value=""><?php echo _l('no_tax'); ?></option>
                              <?php foreach($taxes as $tax){
                                 $selected = '';
                                 if(isset($bill)){
                                   if($tax['id'] == $bill->tax2){
                                     $selected = 'selected';
                                   }
                                 } ?>
                              <option
                                 value="<?php echo $tax['id']; ?>"
                                 <?php echo $selected; ?>
                                 data-percent="<?php echo $tax['taxrate']; ?>"
                                 data-subtext="<?php echo $tax['name']; ?>">
                                 <?php echo $tax['taxrate']; ?>%
                              </option>
                              <?php } ?>
                           </select>
                        </div>
                     </div>
                     <?php if(!isset($bill)) { ?>
                      <div class="col-md-12 hide" id="tax_subtract">
                           <div class="info-block">
                           <div class="checkbox checkbox-primary no-margin">
                            <input type="checkbox" id="tax1_included">
                            <label for="tax1_included">
                              <?php echo _l('subtract_tax_total_from_amount','<span id="tax_subtract_total" class="bold"></span>'); ?>
                            </label>
                          </div>
                          <small class="text-muted">
                            <?php echo _l('expense_subtract_info_text'); ?>
                          </small>
                        </div>
                      </div>
                    <?php } ?>
                  </div>
                  <div class="clearfix mtop15"></div>
                  <div class="row">
                     <div class="col-md-6">
                        <?php $selected = (isset($bill) ? $bill->paymentmode : ''); ?>
                        <?php echo render_select('paymentmode',$payment_modes,array('id','name'),'payment_mode',$selected); ?>
                     </div>
                     <div class="col-md-6">
                        <?php $value = (isset($bill) ? $bill->reference_no : ''); ?>
                        <?php echo render_input('reference_no','expense_add_edit_reference_no',$value); ?>
                     </div>
                  </div>
         </div>
         <div class="col-md-12">
            <table id="bill-debit-account" class="table invoice-mapping-table items">
            <thead>
              
            <tr>
              <th width="50%"><?php echo _l('debit_account'); ?></th>
              <th width="40%"><?php echo _l('amount'); ?></th>
              <th width="10%"></th>
            </tr>
            </thead>
          <?php if(isset($bill)){
            $i               = 0;
            foreach($bill->debit_account as $debit_account){ 
              ?>
              <tr class="bill-debit-account-<?php echo new_html_entity_decode($i); ?> template_children">
               <td>
                  <?php echo render_select('debit_account['.$i.']', $list_debit_account, array('id','name'), '',$debit_account['account'], array('required' => true, 'onchange' => 'debit_account_change(); return false;')); ?>
               </td>
               <td>
                  <?php echo render_input('debit_amount['.$i.']', '',number_format($debit_account['amount'],2),'text', array('required' => true, 'data-type' => 'currency')); ?>
               </td>
               <td>
                  <button name="add_template" class="btn <?php if($i == 0){ echo 'new_debit_template btn-success'; }else{ echo 'remove_debit_template btn-danger';} ?>" data-ticket="true" type="button"><i class="fa <?php if($i == 0){ echo 'fa-plus'; }else{ echo 'fa-minus';} ?>"></i></button>
               </td>
            </tr>
            <?php 
            $i++;
            }
          }else{
            ?>
            <tr class="bill-debit-account-0 bill-item-id-0 template_children">
               <td>
                  <?php echo render_select('debit_account[0]', $list_debit_account, array('id','name'), '',$acc_bill_deposit_to, array('required' => true, 'onchange' => 'debit_account_change(); return false;')); ?>
               </td>
               <td>
                  <?php echo render_input('debit_amount[0]', '','','text', array('required' => true, 'data-type' => 'currency')); ?>
               </td>
               <td>
                  <button name="add_template" class="btn new_debit_template btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
               </td>
            </tr>
          <?php } ?>
          </table>
          <table id="bill-credit-account" class="table invoice-mapping-table items">
            <thead>
            <tr>
              <th width="50%"><?php echo _l('credit_account'); ?></th>
              <th width="40%"><?php echo _l('amount'); ?></th>
              <th width="10%"><i class="fa fa-cog"></i></th>
            </tr>
            </thead>
            <tbody id="body-bill-credit-account">
               
          <?php if(isset($bill)){
            $i               = 0;
            foreach($bill->credit_account as $credit_account){ 
              ?>
              <tr class="bill-credit-account-<?php echo new_html_entity_decode($i); ?> template_children">
               <td>
                  <?php echo render_select('credit_account['.$i.']', $list_credit_account, array('id','name'), '',$credit_account['account'], array('required' => true)); ?>
               </td>
               <td>
                  <?php echo render_input('credit_amount['.$i.']', '',number_format($credit_account['amount'],2),'text', array('required' => true, 'data-type' => 'currency')); ?>
               </td>
               <td>
                  <button name="add_template" class="btn <?php if($i == 0){ echo 'new_credit_template btn-success'; }else{ echo 'remove_template btn-danger';} ?>" data-ticket="true" type="button"><i class="fa <?php if($i == 0){ echo 'fa-plus'; }else{ echo 'fa-minus';} ?>"></i></button>
               </td>
            </tr>
            <?php 
            $i++;
            }
          }else{
            ?>
            <tr class="bill-credit-account-0 bill-item-id-0 template_children">
               <td>
                  <?php echo render_select('credit_account[0]', $list_credit_account, array('id','name'), '',$acc_bill_deposit_to, array('required' => true)); ?>
               </td>
               <td>
                  <?php echo render_input('credit_amount[0]', '','','text', array('required' => true, 'data-type' => 'currency')); ?>
               </td>
               <td>
                  <button name="add_template" class="btn new_credit_template btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
               </td>
            </tr>
          <?php } ?>
            </tbody>
          </table>
         </div>
         <div class="col-md-5 col-md-offset-7">
            <table class="table text-right bold">
               <tbody>
                  <tr id="tr_maximum_amount" class="hide">
                     <td><span class="bold"><?php echo _l('maximum_amount'); ?></span>
                     </td>
                     <td id="bill-maximum-amount" class="text-danger">
                     </td>
                  </tr>
                  <tr>
                     <td><span class="bold"><?php echo _l('invoice_total'); ?></span>
                     </td>
                     <?php $value = (isset($bill) ? $bill->amount : 0); ?>
                     <?php echo form_hidden('amount', $value); ?>
                     <td id="bill-total" class="text-danger">
                        <?php echo app_format_money($value, $currency->name); ?>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-7 small-table-right-col">
                  <div id="bill_div" class="hide">
                  </div>
               </div>
            </div>
         </div>
         <?php echo form_close(); ?>
      <div class="btn-bottom-pusher"></div>

      </div>
   </div>
</div>

<?php $this->load->view('admin/expenses/expense_category'); ?>
<?php init_tail(); ?>
<?php require 'modules/accounting/assets/js/bill/bill_js.php';?>

</body>
</html>
