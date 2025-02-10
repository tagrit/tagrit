<div class="row">
  <a href="#" class="btn btn-info pull-right" onclick="sync_transaction(); return false;"><?php echo _l('synchronize_data_from_accounting_system'); ?></a>
</div>
<div class="row">
<hr>
  <div class="col-md-3">
    <?php $status = [ 
          1 => ['id' => 'synchronized', 'name' => _l('synchronized')],
          2 => ['id' => 'not_synchronized_yet', 'name' => _l('not_synchronized_yet')],
        ]; 
        ?>
        <?php echo render_select('status',$status,array('id','name'),'status', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
        <?php echo form_hidden('software','sage_accounting'); ?>
  </div>
  <div class="col-md-3">
    <?php echo render_date_input('from_date','from_date'); ?>
  </div>
  <div class="col-md-3">
    <?php echo render_date_input('to_date','to_date'); ?>
  </div>
</div>
<table class="table table-expenses">
  <thead>
    <th><?php echo _l('expense_dt_table_heading_category'); ?></th>
    <th><?php echo _l('acc_amount'); ?></th>
    <th><?php echo _l('name'); ?></th>
    <th><?php echo _l('invoice_payments_table_date_heading'); ?></th>
    <th><?php echo _l('payment_mode'); ?></th>
    <th><?php echo _l('invoice'); ?></th>
    <th><?php echo _l('status'); ?></th>
    <th><?php echo _l('error_message'); ?></th>
    <th><?php echo _l('options'); ?></th>
  </thead>
  <tbody>
  </tbody>
</table>