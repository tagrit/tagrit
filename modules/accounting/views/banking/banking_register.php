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
<div id="bank_register_reconcile_info_div" class="mbot25"></div>
<div class="mbot25 text-center"><h4><?php echo _l('cash_transactions_recorded_in_revenue_and_expenses'); ?></h4></div>

<table class="table table-banking-registers">
  <thead>
    <th><?php echo _l('invoice_payments_table_date_heading'); ?></th>
    <!-- <th><?php echo _l('check_#'); ?></th>  -->
    <th><?php echo _l('payee'); ?></th>
    <th><?php echo _l('description'); ?></th>
    <th><?php echo _l('withdrawals'); ?></th>
    <th><?php echo _l('deposits'); ?></th>
    <!-- <th><?php echo _l('balance'); ?></th> -->
    <th><?php echo _l('cleared'); ?></th>
  </thead>
  <tbody>
    
  </tbody>
</table>