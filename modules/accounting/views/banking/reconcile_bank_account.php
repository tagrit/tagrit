<?php $arrAtt = array();
$arrAtt['data-type']='currency';

      $arrAtt2 = array();
      $arrAtt2['data-type']='currency';
      $arrAtt2['readonly']='true';
      ?>
<?php if($bank_account == ''){ ?>
  <?php echo form_open_multipart(admin_url('accounting/banking?group=reconcile_bank_account'),array('id'=>'reconcile-account-form','autocomplete'=>'off')); ?>
  <?php echo form_hidden('resume', $resume); ?>
  <?php echo form_hidden('reconcile_id', 0); ?>
  <p ><?php echo _l('open_your_statement_and_we_will_get_started'); ?></p>
  <hr />
    <h5><?php echo _l('which_account_do_you_want_to_reconcile'); ?></h5>
    <div class="row">
     
      <div class="col-md-12">
        <?php echo render_select('account',$bank_accounts,array('id','name', 'account_type_name'),'acc_account','',array(),array(),'','',false); ?>
        
      </div>
    </div>
    <div id="divInfo" class="<?php if($resume == 1){echo 'hide';} ?>">
    <br>
    <h5><?php echo _l('add_the_following_information'); ?></h5>
    <div class="row">
      <div class="col-md-4">
        <?php echo render_input('beginning_balance','beginning_balance', number_format($beginning_balance,2),'text', $arrAtt2); ?>
      </div>
      <div class="col-md-4">
        <?php echo render_input('debits_for_period','debits_for_period','','text', $arrAtt); ?>
      </div>
      <div class="col-md-4">
        <?php echo render_input('credits_for_period','credits_for_period','','text', $arrAtt); ?>
      </div>
      
    </div>
    <div class="row">
      <div class="col-md-4">
        <?php echo render_input('ending_balance','ending_balance','','text', $arrAtt2); ?>
      </div>
      <div class="col-md-4">
        <?php echo render_date_input('ending_date','ending_date'); ?>
      </div>
    </div>
      <div class="row hide_start_reconciling">
        <div class="col-md-12">
            <hr>
            <button type="submit" class="btn btn-info pull-right <?php if(!has_permission('accounting_reconcile', '', 'create')){echo 'hide';} ?>"><?php echo _l('start_reconciling'); ?></button>
            <button type="button" class="btn btn-warning pull-right mright5 hide_restored <?php echo new_html_entity_decode($hide_restored); ?> <?php if(!has_permission('accounting_reconcile', '', 'edit')){echo 'hide';} ?>"><?php echo _l('acc_restored_last'); ?></button>

        </div>
      </div>
    </div>
    <div id="divResume" class="<?php if($resume == 0){echo 'hide';} ?>">
      <div class="row">
        <div class="col-md-12">
            <hr>

            <button type="submit" class="btn btn-info pull-right btnResume <?php if(!has_permission('accounting_reconcile', '', 'edit')){echo 'hide';} ?>"><?php echo _l('resume_reconciling'); ?></button>

            <button type="button" class="btn btn-warning pull-right mright5 hide_restored <?php echo new_html_entity_decode($hide_restored); ?> <?php if(!has_permission('accounting_reconcile', '', 'edit')){echo 'hide';} ?>"><?php echo _l('acc_restored_last'); ?></button>

            <button type="button" class="btn btn-info pull-right edit_reconcile  mright5 <?php if(!has_permission('accounting_reconcile', '', 'edit')){echo 'hide';} ?>"><?php echo _l('edit'); ?></button>

            <button type="button" class="btn btn-info pull-right update_reconcile hide  mright5 <?php if(!has_permission('accounting_reconcile', '', 'edit')){echo 'hide';} ?>"><?php echo _l('account_update'); ?></button>
        </div>
      </div>
    </div>
  </div>
  <?php echo form_close(); ?>
<?php }else{ ?> 


          <?php 

          echo form_hidden('posted_bank_withdrawals', $reconcile_difference_info['posted_bank_withdrawals']);
          echo form_hidden('posted_bank_deposits', $reconcile_difference_info['posted_bank_deposits']);
          echo form_hidden('banking_register_withdrawals', $reconcile_difference_info['banking_register_withdrawals']);
          echo form_hidden('banking_register_deposits', $reconcile_difference_info['banking_register_deposits']);

          echo form_open(admin_url('accounting/finish_reconcile_bank_account'),array('id'=>'reconcile-account-form','autocomplete'=>'off')); ?>
            <?php echo form_hidden('account', $account->id); ?>
            <?php echo form_hidden('reconcile', $reconcile->id); ?>
            <?php echo form_hidden('finish', 0); ?>
          <?php echo form_close(); ?>

          <div class="row">
          <div class="col-md-6">
            <h4 class="no-margin font-bold"><?php echo _l($title).' '. ($account->name != '' ? $account->name : _l($account->key_name)); ?></h4>
          </div>
          <div class="col-md-6">
            <i class="fa fa-lg fa-question-circle pull-right mright15" data-toggle="tooltip" data-title="Please execute match transactions function before making transaction adjustment." data-original-title="" title=""></i>
          </div>
          </div>
          <hr />
          <div class="integrations-banking-reconcile-ui">
            <div class="reconcile-stage row">
              <div class="col-md-5">
                <?php echo _l('reconciliation_period'); ?>: <?php echo _d($reconcile->ending_date); ?><br>
                <?php echo _l('reconciled_by'); ?>: <?php echo get_staff_full_name($reconcile->addedfrom); ?><br>
              </div>
              <div class="col-md-5">
                <?php echo _l('beginning_balance'); ?>: <?php echo app_format_money($reconcile->beginning_balance, $currency->name); ?><br>
                <?php echo form_hidden('beginning_balance', $reconcile->beginning_balance); ?>
                <?php echo _l('withdrawals'); ?>: <span class="ha-numeral" id="payment_amount"><?php echo app_format_money($reconcile->debits_for_period, $currency->name); ?></span><br>
                <?php echo _l('deposits'); ?>: <span class="ha-numeral" id="deposit_amount"><?php echo app_format_money($reconcile->credits_for_period, $currency->name); ?></span><br>
                <?php echo _l('ending_balance'); ?>: <?php echo app_format_money($reconcile->ending_balance, $currency->name); ?><br>
                <?php echo _l('difference'); ?>: <span class="ha-numeral medium" id="difference_amount"><?php echo app_format_money(abs($reconcile->beginning_balance - $reconcile->ending_balance), $currency->name); ?></span>
                <?php echo form_hidden('beginning_balance', $reconcile->beginning_balance); ?>
              </div>
              <div class="col-md-2 text-center">
                <div class="row">
                  <button type="button" class="btn btn-success mright15 pull-right mtop10 w-100" onclick="match_transactions(); return false;"><?php echo _l('acc_match_transactions'); ?></button>
                </div>
                <div class="row">
                  
                  <a href="<?php echo admin_url('accounting/banking?group=reconcile_bank_account'); ?>" id="set_up_your_bank_account" class="btn btn-default mtop10 mright15 pull-right"><?php echo _l('save_for_later'); ?></a>
                </div>
                <div class="row">
                  <button type="button" class="btn btn-warning mright15 pull-right mtop10 w-100" onclick="unmatch_transactions(); return false;"><?php echo _l('delete_start_process_again'); ?></button>
                </div>
                <div class="row mtop10">
                <?php 
                  $approval_class = 'hide';
                  $finish_class = 'hide';

                if($reconcile->finish == 0){ 
                  $finish_class = '';
                } ?>
                  <?php echo form_open(admin_url('accounting/approve_reconcile'),array('id'=>'approve-form','autocomplete'=>'off')); ?>
                  <?php echo form_hidden('reconcile', $reconcile->id); ?>
                  <button type="submit" class="btn btn-info mright15 pull-right approval_btn <?php echo $approval_class; ?> w-100" ><?php echo _l('acc_approve'); ?></button>
                  <?php echo form_close(); ?>
                  <button type="button" class="btn btn-info mright15 pull-right finish_btn <?php echo $finish_class; ?> w-100" onclick="complete_reconcile(); return false;"><?php echo _l('adjust_and_finish'); ?></button>
                </div>
              </div>
            </div>
          </div>
          <br>
          <h4><?php echo _l('acc_transactions_in_banking_register'); ?></h4>
          <table class="table table-reconcile-transactions scroll-responsive">
           <thead>
              <tr>
                 <th width="15%"><?php echo _l('expense_dt_table_heading_date'); ?></th>
                 <!-- <th width="10%"><?php echo _l('check').' #'; ?></th> -->
                 <th width="25%"><?php echo _l('payee'); ?></th>
                 <th width="25%"><?php echo _l('description'); ?></th>
                 <th width="15%"><?php echo _l('acc_withdrawals'); ?></th>
                 <th width="15%"><?php echo _l('acc_deposits'); ?></th>
                 <th width="5%"><?php echo _l('acc_match'); ?></th>
              </tr>
           </thead>
          </table>
          <br>
          <h4><?php echo _l('acc_posted_bank_transaction_from_bank_account'); ?></h4>
          <table class="table table-reconcile-posted-bank scroll-responsive">
           <thead>
              <tr>
                 <th width="15%"><?php echo _l('expense_dt_table_heading_date'); ?></th>
                 <!-- <th width="10%"><?php echo _l('check').' #'; ?></th> -->
                 <th width="25%"><?php echo _l('payee'); ?></th>
                 <th width="25%"><?php echo _l('description'); ?></th>
                 <th width="15%"><?php echo _l('acc_withdrawals'); ?></th>
                 <th width="15%"><?php echo _l('acc_deposits'); ?></th>
                 <th width="5%"><?php echo _l('acc_match'); ?></th>
              </tr>
           </thead>
          </table>
     
<div class="modal fade" id="adjustment-modal">
  <div class="modal-dialog">
    <div class="modal-content">
        <?php echo form_open(admin_url('accounting/adjustment'),array('id'=>'adjustment-form','autocomplete'=>'off')); ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="finish_difference_header"><?php echo _l('manager_reconciliation_approval')?></h4>
      </div>
      <div class="modal-body">
        <div id="finish_difference">
          <?php echo form_hidden('account', $account->id); ?>
          <?php echo form_hidden('reconcile', $reconcile->id); ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <!-- <button type="submit" class="btn btn-info intext-btn" id="btn-add-adjustment-and-finish"><?php echo _l('add_adjustment_and_finish'); ?></button> -->
        <button type="button" class="btn btn-info intext-btn" id="btn-finish"><?php echo _l('save'); ?></button>
        <!-- <button type="button" class="btn btn-info intext-btn hide" id="btn-finish"><?php echo _l('finish'); ?></button> -->
      </div>
        <?php echo form_close(); ?>
    </div>
  </div>
</div>

<div class="modal fade" id="edit-info-modal">
  <div class="modal-dialog">
    <div class="modal-content">
        <?php echo form_open(admin_url('accounting/edit_reconcile'),array('id'=>'edit-reconcile-form','autocomplete'=>'off')); ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l($title).' '. ($account->name != '' ? $account->name : _l($account->key_name)); ?></h4>
      </div>
      <div class="modal-body">
        <h5><?php echo _l('add_the_following_information'); ?></h5>
        <div class="row">
          <div class="col-md-4">
            <?php echo render_input('beginning_balance','beginning_balance',$reconcile->beginning_balance,'text', $arrAtt2); ?>
          </div>
          <div class="col-md-4">
            <?php echo render_input('ending_balance','ending_balance',$reconcile->ending_balance,'text', $arrAtt); ?>
          </div>
          <div class="col-md-4">
            <?php echo render_date_input('ending_date','ending_date',_d($reconcile->ending_date)); ?>
          </div>
        </div>
        <?php echo form_hidden('reconcile_id', $reconcile->id); ?>
        <?php echo form_hidden('account', $account->id); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info intext-btn"><?php echo _l('submit'); ?></button>
      </div>
        <?php echo form_close(); ?>
    </div>
  </div>
</div>
<div class="modal fade" id="transaction-uncleared-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title add-title"><?php echo _l('transactions_do_not_match')?></h4>
         </div>
         <div class="modal-body">
            <table class="table table-checks-to-print scroll-responsive dataTable">
                 <thead>
                    <tr>
                       <th><?php echo _l('acc_date'); ?></th>
                       <!-- <th><?php echo _l('check_#'); ?></th> -->
                       <th><?php echo _l('payee'); ?></th>
                       <th><?php echo _l('description'); ?></th>
                       <th><?php echo _l('withdrawals'); ?></th>
                       <th><?php echo _l('deposits'); ?></th>
                       <th></th>
                    </tr>
                 </thead>
                 <tbody id="transaction-uncleared-tbody">
                </tbody>
            </table>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="make-adjusting-entry-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title add-title"><?php echo _l('make_adjusting_entry_header')?></h4>
         </div>
         <div class="modal-body">
            <div class="row">
                <div class="col-md-4 no-padding"><h4 class="pull-right no-margin"><?php echo _l('transaction'); ?></h4></div>
                <div class="col-md-8" id="make-adjusting-entry-transaction"></div>
            </div>
            <?php echo form_hidden('transaction_bank_id'); ?>
            <!-- Default unchecked -->
            <div class="custom-control custom-radio">
              <input type="radio" class="custom-control-input" id="add_transaction" name="make_adjusting_type" value="add_transaction" checked>
              <label class="custom-control-label" for="add_transaction">Add this transaction to the banking register.</label>
            </div>
            <!-- Default checked -->
            <div class="custom-control custom-radio">
              <input type="radio" class="custom-control-input" id="update_transaction" name="make_adjusting_type" value="update_transaction">
              <label class="custom-control-label" for="update_transaction">Adjust the transaction.</label>
            </div>
            <div class="div-add-transaction">
              <table class="table table-checks-to-print scroll-responsive dataTable">
                   <thead>
                      <tr>
                         <th><?php echo _l('acc_date'); ?></th>
                         <th><?php echo _l('payee'); ?></th>
                         <th><?php echo _l('bank_account'); ?></th>
                         <th><?php echo _l('account'); ?></th>
                         <th><?php echo _l('amount'); ?></th>
                      </tr>
                   </thead>
                   <tbody>
                    <tr>
                         <td id="make-adjusting-entry-date"><?php echo _l('acc_date'); ?></td>
                         <td id="make-adjusting-entry-vendor"></td>
                         <td class="max-width-180"><?php echo render_select('make_adjusting_bank_account',$bank_accounts,array('id','name', 'account_type_name'),'',$account->id,array('disabled' => true),array(),'','',false); ?></td>
                         <td class="max-width-180"><?php echo render_select('make_adjusting_account',$accounts,array('id','name', 'account_type_name'),'',$account_adjust,array(),array(),'','',false); ?></td>
                         <td id="make-adjusting-entry-amount"><?php echo _l('acc_date'); ?></td>
                      </tr>
                  </tbody>
              </table>
            </div>
            <div class="div-update-transaction hide">
              <?php echo render_select('make_adjusting_transaction',[],array('id','name'), 'transaction'); ?>
              <?php echo render_date_input('make_adjusting_date','acc_date', '', array('required' => true)); ?>
              <div class="row">
              <div class="col-md-6">
                <?php echo render_input('make_adjusting_withdrawal','withdrawal', '','text', array('required' => true, 'data-type' => 'currency')); ?>
              </div>
              <div class="col-md-6">
                <?php echo render_input('make_adjusting_deposit','deposit', '','text', array('required' => true, 'data-type' => 'currency')); ?>
              </div>
              </div>
              <span class="text-danger"><?php echo _l('bank_reconcile_update_transaction_note'); ?></span>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" id="btn-make-adjusting-entry-submit" class="btn btn-info"><?php echo _l('save'); ?></button>
        </div>
      </div>
   </div>
</div>
<div class="modal fade" id="complete-reconcile-modal">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title add-title"><?php echo _l('reconciliation_complete')?></h4>
         </div>
         <div class="modal-body">
            <div id="complete_reconcile_transactions">
            </div>
            <table class="table table-checks-to-print scroll-responsive dataTable">
                 <tbody>
                  <tr>
                    <td><?php echo _l('reconciled_by'); ?>:</td>
                    <td><?php echo get_staff_full_name($reconcile->addedfrom); ?></td>
                  </tr>
                  <tr>
                    <td><?php echo _l('acc_date'); ?>:</td>
                    <td><?php echo _d($reconcile->ending_date); ?></td>
                  </tr>
                </tbody>
            </table>
         </div>
         <div class="modal-footer">
            <div class="col-md-3">
            </div>
            <div class="col-md-3">
              <button type="button" id="btn-complete-reconcile-submit" class="btn btn-info pull-left"><?php echo _l('finish'); ?></button>
            </div>
            <div class="col-md-3">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
            <div class="col-md-3">
            </div>
        </div>
      </div>
   </div>
</div>
<div class="modal fade" id="manager-reconciliation-approval-modal">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title add-title"><?php echo _l('manager_reconciliation_approval')?></h4>
         </div>
         
      </div>
   </div>
</div>

<div class="adjust_transaction_params">
<?php echo form_hidden('adjust_transaction_date'); ?>
<?php echo form_hidden('adjust_transaction_payee'); ?>
</div>

<div class="modal fade" id="adjust-transaction-confirm-modal">
   <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" onclick="cancel_confirm_modal(); return false;">&times;</button>
            <h4 class="modal-title add-title"><?php echo _l('confirm')?></h4>
         </div>
         <div class="modal-body">
            <div id="adjust-transaction-confirm-message">
            </div>
         </div>
         <div class="modal-footer">
            <div class="col-md-3">
            </div>
            <div class="col-md-3">
              <button type="button" class="btn btn-info pull-left w-100" onclick="make_adjusting_entry_save(); return false;"><?php echo _l('OK'); ?></button>
            </div>
            <div class="col-md-3">
            <button type="button" class="btn btn-default w-100" onclick="cancel_confirm_modal(); return false;"><?php echo _l('cancel'); ?></button>
            </div>
            <div class="col-md-3">
            </div>
        </div>
      </div>
   </div>
</div>
<?php } ?>      