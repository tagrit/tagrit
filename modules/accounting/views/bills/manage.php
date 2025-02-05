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
                     <div class="panel-body">
                        <div class="row">
                          <div class="col-md-4">
                            <?php echo render_date_input('from_date','from_date'); ?>
                          </div>
                          <div class="col-md-4">
                            <?php echo render_date_input('to_date','to_date'); ?>
                          </div>
                          <div class="col-md-4">
                             <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs pull-right" onclick="toggle_small_view('.table-bills','#bill_div'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i> <?php echo _l('expand_table'); ?></a>
                          </div>
                        </div>
                        <div class="clearfix"></div>
                        <!-- if expenseid found in url -->
                        <?php echo form_hidden('billid',$expenseid); ?>
                        <?php $this->load->view('accounting/bills/table_html', ['withBulkActions'=>true]); ?>
                     </div>
                  </div>
               </div>
               <div class="col-md-7 small-table-right-col">
                  <div id="bill_div" class="hide">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php echo form_open(admin_url('accounting/pay_bill'), array('id'=>'pay_bill-form')); ?>
<?php echo form_hidden('is_bulk_action', 1); ?>
<?php echo form_hidden('bill_ids'); ?>
<?php echo form_close(); ?>

<div class="modal fade" id="expense_convert_helper_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('additional_action_required'); ?></h4>
         </div>
         <div class="modal-body">
            <div class="radio radio-primary">
               <input type="radio" checked id="expense_convert_invoice_type_1" value="save_as_draft_false" name="expense_convert_invoice_type">
               <label for="expense_convert_invoice_type_1"><?php echo _l('convert'); ?></label>
            </div>
            <div class="radio radio-primary">
               <input type="radio" id="expense_convert_invoice_type_2" value="save_as_draft_true" name="expense_convert_invoice_type">
               <label for="expense_convert_invoice_type_2"><?php echo _l('convert_and_save_as_draft'); ?></label>
            </div>
            <div id="inc_field_wrapper">
               <hr />
               <p><?php echo _l('expense_include_additional_data_on_convert'); ?></p>
               <p><b><?php echo _l('expense_add_edit_description'); ?> +</b></p>
               <div class="checkbox checkbox-primary inc_note">
                  <input type="checkbox" id="inc_note">
                  <label for="inc_note"><?php echo _l('expense'); ?> <?php echo _l('expense_add_edit_note'); ?></label>
               </div>
               <div class="checkbox checkbox-primary inc_name">
                  <input type="checkbox" id="inc_name">
                  <label for="inc_name"><?php echo _l('expense'); ?> <?php echo _l('expense_name'); ?></label>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-info" id="expense_confirm_convert"><?php echo _l('confirm'); ?></button>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="void-bill-modal">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
           <h4 class="modal-title"><?php echo _l('void')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('accounting/void_bill'),array('id'=>'void-bill-form'));?>
         <?php echo form_hidden('id'); ?>
         <div class="modal-body">
            <?php echo render_textarea('reason_for_void','reason_for_void','',array('rows'=>4),array()); ?>
            <label class="text-danger"><?php echo _l('void_bill_confirmation'); ?></label> 
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info">OK</button>
         </div>
       <?php echo form_close(); ?>  
      </div>
   </div>
</div>
<!-- /.modal -->
<?php init_tail(); ?>
<?php require 'modules/accounting/assets/js/bill/manage_js.php';?>

</body>
</html>
