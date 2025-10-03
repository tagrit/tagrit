<div class="row">
    <div class="col-md-3">
      <?php echo render_select('bank_account',$bank_accounts,array('id','name', 'account_type_name'),'acc_bank_account'); ?>
    </div>
    <div class="col-md-3">
      <?php $status = [ 
            1 => ['id' => 'converted', 'name' => _l('cleared')],
            2 => ['id' => 'has_not_been_converted', 'name' => _l('uncleared')],
          ]; 
          ?>
          <?php echo render_select('status',$status,array('id','name'),'status', $_status, array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
    </div>
    <div class="col-md-3">
      <?php echo render_date_input('from_date','from_date'); ?>
    </div>
    <div class="col-md-3">
      <?php echo render_date_input('to_date','to_date'); ?>
    </div>
  </div>

  <hr>
  <a href="<?php echo admin_url('accounting/import_xlsx_posted_bank_transactions'); ?>" class="btn btn-success mr-4 button-margin-r-b" title="<?php echo _l('import_excel') ?> ">
    <?php echo _l('import_excel'); ?>
  </a>

  <div class="mbot25 text-center"><h4><?php echo _l('posted_transactions_from_your_bank_account'); ?></h4></div>
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