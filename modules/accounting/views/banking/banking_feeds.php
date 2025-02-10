  <div class="row">

    <div class="col-md-3">

        <select id="bank_account" name="bank_account" class="selectpicker" data-width="100%">

         <option value="">Select Bank Account</option>

         <?php 

         foreach($bank_accounts as $b_acc){?>

            <option value="<?php echo $b_acc['id']; ?>" <?php echo (isset($_GET['id']) && $_GET['id']==$b_acc['id']?"selected":"");  ?> data-subtext="<?php echo $b_acc['account_type_name']; ?>"><?php echo $b_acc['name']; ?></option>

         <?php

         }

         ?>

     </select>
    </div>
    <div class="col-md-9">
        <?php if(isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 1) {?>
        <div class="btn-group btn-with-tooltip-group pull-right" data-toggle="tooltip">
            <button type="button" class="btn btn-default dropdown-toggle sm:tw-max-w-xs tw-truncate" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-cog" aria-hidden="true"></i> </button>
            <ul class="dropdown-menu dropdown-menu-right width-250">
                <li>
                    <a href="#"  class="acc-text-info" data-toggle="modal" data-target="#download-transactions-modal"><i class="fa fa-cloud-download"></i> <?php echo _l('download_transactions'); ?></a>
                </li>
                <li>
                    <a href="<?php echo admin_url('accounting/import_xlsx_posted_bank_transactions?bank_id='.$_GET['id']) ?>"  class="top-timers acc-text-success"><i class="fa fa-download"></i> <?php echo _l('import_transactions'); ?></a>
                </li>
                <li>
                    <a href="#"  id="linkButton"><i class="fa fa-refresh"></i> <?php echo _l('re_verify_bank_account'); ?></a>
                </li>
                <li>
                    <a href="#" class="text-danger delete-text" onclick="updatePlaidStatus()"><i class="fa fa-remove"></i> <?php echo _l('delete_verification'); ?></a>
                </li>
            </ul>
            
        </div>
        <?php } ?>
        <?php if(isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 0) {?>
            <div class="btn-group btn-with-tooltip-group pull-right" data-toggle="tooltip">
                <button type="button" class="btn btn-default dropdown-toggle sm:tw-max-w-xs tw-truncate" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-cog" aria-hidden="true"></i> </button>
                <ul class="dropdown-menu dropdown-menu-right width-250">
                    <li>
                        <a href="<?php echo admin_url('accounting/import_xlsx_posted_bank_transactions?bank_id='.$_GET['id']) ?>"  class="top-timers acc-text-success"><i class="fa fa-download"></i> <?php echo _l('import_transactions'); ?></a>
                    </li>
                    <li>
                        <a href="#"  id="linkButton"><i class="fa fa-refresh"></i> <?php echo _l('verify_bank_account'); ?></a>
                    </li>
                </ul>
                
            </div>
        <?php } ?>

    </div>
</div>
    <?php if(isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 1) {?>
        <hr>
<div class="row mtop15">
    <div class="col-md-12">
         <table class="table table-striped  no-margin">
            <tbody>
                <tr class="project-overview">
                  <td class="bold" width="30%"><?php echo _l('account_name'); ?></td>
                  <td><?php echo html_entity_decode($account_data[0]['account_name']); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('status'); ?></td>
                  <td class=""><span class="text-success"><?php echo _l('Verified') ; ?></span></td>
               </tr>
               
                <?php if(isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 1) {?> 
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('last_updated'); ?></td>
                  <?php 
                        $value = '';
                        if(isset($refresh_data) && $refresh_data != NULL && $refresh_data[0]['refresh_date'] != NULL ){ 
                            $value = _d($refresh_data[0]['refresh_date']); 
                        }
                    ?>
                  <td><?php echo html_entity_decode($value) ; ?></td>
               </tr>
                <?php } ?>
              </tbody>
        </table>
    </div>
</div>
    <?php } ?>
<br>

<div class="row">
    <div class="col-md-3">
      <?php echo render_date_input('fliter_from_date','from_date'); ?>
    </div>
    <div class="col-md-3">
      <?php echo render_date_input('fliter_to_date','to_date'); ?>
    </div>
    <div class="col-md-3">
          <?php 
          $method = [
                  1 => ['id' => 'cleared', 'name' => _l('cleared')],
                  2 => ['id' => 'uncleared', 'name' => _l('uncleared')],
                  3 => ['id' => 'ignore', 'name' => _l('ignore')],
                 ];
          echo render_select('status', $method, array('id', 'name'),'status', 'uncleared');
          ?>
    </div>
</div>     
<table class="table table-banking">
  <thead>
    <th><?php echo _l('invoice_payments_table_date_heading'); ?></th>
  <!-- <th><?php echo _l('check_#'); ?></th> -->
  <th><?php echo _l('payee'); ?></th>
  <th><?php echo _l('description'); ?></th>
  <th><?php echo _l('withdrawals'); ?></th>
  <th><?php echo _l('deposits'); ?></th>
  <th><?php echo _l('banking_rule'); ?></th>
  <th><?php echo _l('cleared'); ?></th>
  <th><?php echo _l('options'); ?></th>
  </thead>
  <tbody>
    
  </tbody>
</table>

<div class="modal fade bulk_actions" id="download-transactions-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('download_transactions'); ?></h4>
            </div>
            <div class="modal-body">
                <?php $value = $last_updated != '' ? _d($last_updated) : ''; ?>
                <?php echo render_date_input('from_date','date_from_which_to_import_transactions',$value); ?>
                <h5 class="heading">Up to 500 transactions can be imported at a time. It may take a few minutes to grab them all from your bank.</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <a href="#" class="btn btn-info" onclick="submitForm(); return false;"><?php echo _l('download'); ?></a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add-transaction-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title add-title"><?php echo _l('add_transaction')?></h4>
         </div>
         <div class="modal-body">
            <?php echo form_hidden('transaction_bank_id'); ?>
            <?php echo form_hidden('add_transaction_withdrawal'); ?>
            <?php echo form_hidden('add_transaction_deposit'); ?>
            <div class="div-add-transaction">
              <table class="table table-checks-to-print scroll-responsive dataTable">
                   <thead>
                      <tr>
                         <th><?php echo _l('acc_date'); ?></th>
                         <th class="add-transaction-vendor"><?php echo _l('payee'); ?></th>
                         <th class="add-transaction-customer"><?php echo _l('customer'); ?></th>
                         <th><?php echo _l('bank_account'); ?></th>
                         <th><?php echo _l('account'); ?></th>
                         <th><?php echo _l('payment'); ?></th>
                         <th><?php echo _l('deposit'); ?></th>
                      </tr>
                   </thead>
                   <tbody>
                    <tr>
                         <td id="add-transaction-date"><?php echo _l('acc_date'); ?></td>
                         <td class="add-transaction-vendor"><?php echo render_select('add_transaction_vendor',$vendors,array('userid','company')); ?></td>
                         <td class="add-transaction-customer"><?php echo render_select('add_transaction_customer',$customers,array('userid','company')); ?></td>
                         <td class="max-width-180"><?php echo render_select('add_transaction_bank_account',$bank_accounts,array('id','name', 'account_type_name'),'',$_GET['id'],array('disabled' => true),array(),'','',false); ?></td>
                         <td class="max-width-180"><?php echo render_select('add_transaction_account',$accounts,array('id','name', 'account_type_name'),'','',array(),array(),'','',false); ?></td>
                         <td id="add-transaction-payment"></td>
                         <td id="add-transaction-deposit"></td>
                      </tr>
                  </tbody>
              </table>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-info" onclick="add_transaction_save(); return false;"><?php echo _l('save'); ?></button>
        </div>
      </div>
   </div>
</div>


<div class="modal fade" id="match-transaction-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title add-title"><?php echo _l('match_transaction')?></h4>
         </div>
         <div class="modal-body">
            <?php echo form_hidden('transaction_bank_id'); ?>
            <div class="div-update-transaction">
              <?php echo render_select('match_transaction_transaction',[],array('id','name'), 'transaction'); ?>
              <?php echo render_date_input('match_transaction_date','acc_date', '', array('required' => true)); ?>
              <div class="row">
              <div class="col-md-6">
                <?php echo render_input('match_transaction_withdrawal','withdrawal', '','text', array('required' => true, 'data-type' => 'currency')); ?>
              </div>
              <div class="col-md-6">
                <?php echo render_input('match_transaction_deposit','deposit', '','text', array('required' => true, 'data-type' => 'currency')); ?>
              </div>
              </div>
              <span class="text-danger"><?php echo _l('bank_reconcile_update_transaction_note'); ?></span>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-info" onclick="match_transaction_save(); return false;"><?php echo _l('save'); ?></button>
        </div>
      </div>
   </div>
</div>


<?php $arrAtt = array();
      $arrAtt['data-type']='currency';
?>
<div class="modal fade" id="edit-transaction-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('transaction')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('accounting/update_bank_transaction'),array('id'=>'edit-transaction-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
              <?php echo render_date_input('date', 'expense_dt_table_heading_date') ?>
              <?php echo render_input('payee', 'payee') ?>
              <div class="row">
                <div class="col-md-6">
                  <?php echo render_input('withdrawals', 'withdrawals', '', 'text', $arrAtt) ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_input('deposits', 'deposits', '', 'text', $arrAtt) ?>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <?php echo render_textarea('description','description'); ?>
                </div>
              </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>
<!-- box loading -->

<div id="box-loading"></div>


