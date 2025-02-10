<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
                  <?php echo form_hidden('type',$type); ?>
            <div class="row">
               
            <div class="clearfix"></div>
            <?php 
            if(isset($pay_bill)){
                echo form_hidden('is_edit','true');
            }

            if(is_numeric($bill_ids) == 1){
                $bill_debit = $this->accounting_model->get_bill_mapping_details($bill_ids, 'debit');
                $bill_item = $this->accounting_model->get_bill_mapping_details($bill_ids, 'item');
                $bill_items = array_merge($bill_debit, $bill_item);
                foreach($bill_items as $key => $item){
                    if($item['type'] == 'item'){
                        $bill_items[$key]['name'] = acc_get_item_name_by_id($item['item_id']);
                    }else{
                        $bill_items[$key]['name'] = isset($account_name[$item['account']]) ? $account_name[$item['account']] : '';
                    }
                }
            }
            ?>

         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'expense-form','class'=>'dropzone dropzone-manual')) ;?>

        
         <div class="col-md-6">
            <div class="panel_s">
               <div class="panel-body">
                  <h4 class="no-margin"><?php echo $title; ?></h4>
                  <hr class="hr-panel-heading" />
                  <div class="row">
                     
                  <div class="col-md-6 form-group <?php if(isset($pay_bill)){ echo 'hide'; }?>">
                     <p class="mbot5"><?php echo _l('payment_method'); ?></p>
                     <label class="radio-inline"><input type="radio" id="check" name="payment_method" value="check" ><?php echo _l('check'); ?></label>
                     <label class="radio-inline"><input type="radio" id="credit_card" name="payment_method" value="credit_card"><?php echo _l('acc_credit_card'); ?></label>
                     <label class="radio-inline"><input type="radio" id="electronic_payment" name="payment_method" value="electronic_payment" checked><?php echo _l('electronic_payment'); ?></label>
                   </div>
                  </div>
                   <hr>
                    <div id="div_check" class="hide">
                      <a href="javascript:void(0)" onclick="create_check(); return false;" class="btn btn-info pull-left new new-check-list mright5"><?php echo _l('create_new_check'); ?></a>
                   </div>
                   <div id="div_credit_card">
                      
                  <?php if(isset($pay_bill) && $pay_bill->attachment !== ''){ ?>
                  <div class="row">
                     <div class="col-md-10">
                        <i class="<?php echo get_mime_class($pay_bill->filetype); ?>"></i> <a href="<?php echo admin_url('accounting/download_file/pay_bill/'.$pay_bill->id); ?>"><?php echo $pay_bill->attachment; ?></a>
                     </div>
                     <?php if($pay_bill->attachment_added_from == get_staff_user_id() || is_admin()){ ?>
                     <div class="col-md-2 text-right">
                        <a href="<?php echo admin_url('accounting/delete_pay_bill_attachment/'.$pay_bill->id); ?>" class="text-danger _delete"><i class="fa fa fa-times"></i></a>
                     </div>
                     <?php } ?>
                  </div>
                  <?php } ?>
                  <?php if(!isset($pay_bill) || (isset($pay_bill) && $pay_bill->attachment == '')){ ?>
                  <div id="dropzoneDragArea" class="dz-default dz-message">
                     <span><?php echo _l('acc_attachment'); ?></span>
                  </div>
                  <div class="dropzone-previews"></div>
                  <?php } ?>
                  <hr class="hr-panel-heading" />
                  <div class="hide">
                      <?php $value = (isset($pay_bill) ? $pay_bill->vendor : $vendor); ?>
                      <?php echo render_select('vendor', $vendors, array('userid', 'company'),'vendor', $value, array(), array(), '', '', false); ?>
                  </div>
                  <?php $value = (isset($pay_bill) ? _d($pay_bill->date) : _d(date('Y-m-d'))); ?>
                  <?php echo render_date_input('date','date_paid',$value); ?>
                  <div class="row"> 
                     <div class="col-md-6">
                  <?php 
                    $value = (isset($pay_bill) ? $pay_bill->account_credit : $acc_pay_bill_payment_account); ?>
                        <?php echo render_select('account_credit', $accounts, array('id','name'),'payment_account',$value); ?>
                     </div>
                     <div class="col-md-6">
                  <?php 
                    $value = (isset($pay_bill) ? $pay_bill->account_debit : $acc_pay_bill_deposit_to); ?>
                        <?php echo render_select('account_debit', $accounts, array('id','name'), 'deposit_to',$value); ?>
                     </div>
                  </div>
                    

                  <?php 
                    if(is_numeric($bill_ids) == 1){
                        $value = (isset($pay_bill) ? explode(',', $pay_bill->bill_items) : '');
                    echo render_select('bill_items[]',$bill_items,array('id','name','amount'),'<small class="req text-danger">* </small> Invoice Item', $value, array('multiple' => true, 'data-actions-box' => true, 'required' => true), array(), '', '', false);
                  ?>

                   <div id="pay-bill-items">
                       <?php if(isset($pay_bill)){ ?>
                        <table class="table invoice-mapping-table">
                            <thead>
                            <tr>
                              <th width="50%"><?php echo _l('acc_item'); ?></th>
                              <th width="25%"><?php echo _l('amount'); ?></th>
                              <th width="25%"><?php echo _l('acc_amount_paid'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                       <?php foreach ($pay_bill->pay_bill_item_paid as $key => $value) {
                            echo '<tr>
                               <td>
                                  '. render_input('pay_bill_item['.$value['item_id'].']', '', $value['item_name'],'text', array('readonly' => true)).'
                               </td>
                               <td>
                                  '. render_input('pay_bill_amount['.$value['item_id'].']', '',number_format($value['item_amount'],2),'text', array('readonly' => true, 'data-type' => 'currency')).'
                               </td>
                               <td>
                                  '. render_input('pay_bill_amount_paid['.$value['item_id'].']', '',number_format($value['amount_paid'],2),'text', array('required' => true, 'data-type' => 'currency', 'max-amount' => $value['item_amount'])).'
                               </td>
                            </tr>';
                        } ?>
                            </tbody>
                        </table>
                        <div class="col-md-5 col-md-offset-7">
                            <table class="table text-right bold">
                               <tbody>
                                  <tr>
                                     <td><span class="bold"><?php echo _l('invoice_total'); ?></span>
                                     </td>
                                     <?php echo form_hidden('amount', $pay_bill->amount); ?>
                                     <td id="pay-bill-total" class="text-danger">
                                        <?php echo number_format($pay_bill->amount, 2); ?>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                         </div>
                       <?php } ?>
                   </div>

                  <?php }else{ ?>
                      <?php $value = (isset($pay_bill) ? $pay_bill->amount : $bill_amount); ?>
                      <?php echo render_input('amount','acc_amount_paid',$value,'number',array('readonly' => true)); ?>
                  <?php } ?>
                  
                  <?php $value = (isset($pay_bill) ? $pay_bill->reference_no : ''); ?>
                  <?php echo render_input('reference_no','ref_number',$value); ?>
                  
                  <div class="btn-bottom-toolbar text-right">
                     <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                  </div>
                   </div>

               </div>
            </div>
         </div>
         <?php $this->load->model('currencies_model');
              $this->load->model('accounting/accounting_model');
              $currency = $this->currencies_model->get_base_currency();
         if(isset($bill_ids)){ ?>
         <div class="col-md-6">
            <div class="panel_s">
               <div class="panel-body">
                  <h4 class="no-margin"><?php echo _l('acc_bills'); ?></h4>
                  <hr class="hr-panel-heading" />
                  <div class="row">
                    <div class="col-md-12">
                        <table class="table dt-table list-bills">
                          <thead>
                              <th><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="list-bills" checked><label></label></div></th>
                              <th><?php echo _l('acc_due_date'); ?></th>
                              <th><?php echo _l('acc_vendor'); ?></th>
                              <th><?php echo _l('ref_number'); ?></th>
                              <th><?php echo _l('acc_amount'); ?></th>
                              <th><?php echo _l('bill_date'); ?></th>
                          </thead>
                          <tbody>
                            <?php $bill_ids_arr = explode(',', $bill_ids); ?>
                            <?php foreach($bill_ids_arr as $key => $bill_id){ ?>
                              <?php $bill = $this->accounting_model->get_bill($bill_id); ?>
                              <tr>
                                <td><div class="checkbox"><input onchange="caculate_amount_check(this); return false;" type="checkbox" id="bill_id_check[<?php echo $bill_id; ?>]" name="bill_id_check[<?php echo $bill_id; ?>]" value="<?php echo new_html_entity_decode($bill_id); ?>" data-amount="<?php echo $bill->amount; ?>" checked><label></label></div></td>
                                <td><?php echo _d($bill->due_date); ?></td>
                                <td><?php echo acc_get_vendor_name($bill->vendor); ?></td>
                                <td><?php echo acc_get_vendor_name($bill->reference_no); ?></td>
                                <td><?php echo app_format_money($bill->amount, $currency->name); ?></td>
                                <td><?php echo _d($bill->date); ?></td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                    </div> 
                  </div>
               </div>
            </div>
          </div>
          <?php } ?>
         <?php echo form_close(); ?>
                    
                     <div class="btn-bottom-pusher"></div>

            </div>
         </div>
      </div>
   </div>
</div>
<?php echo form_open(admin_url('accounting/check'), array('id'=>'check_bill-form')); ?>
<?php echo form_hidden('is_bulk_action', 1); ?>
<?php echo form_hidden('bill_ids'); ?>
<?php echo form_close(); ?>

<?php init_tail(); ?>
<?php require 'modules/accounting/assets/js/pay_bill/pay_bill_js.php';?>
</body>
</html>
