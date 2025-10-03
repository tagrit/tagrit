<div class="row">
  <?php if($organizations || $region == 'central_european'){ ?>
  <a href="#" class="btn btn-info pull-right" onclick="sync_transaction(); return false;"><?php echo _l('sa_synchronize_data_from_accounting_system'); ?></a>
  <?php } ?>

</div>
<div class="row">
<hr>
  <div class="col-md-3">
    <?php echo render_select('organization',$organizations,array('ID','Name'),'company', '', array(), array(), '', '', false); ?>
  </div>
  <div class="col-md-3">
    <?php $status = [ 
          1 => ['id' => 'synchronized', 'name' => _l('synchronized')],
          2 => ['id' => 'not_synchronized_yet', 'name' => _l('not_synchronized_yet')],
        ]; 
        ?>
        <?php echo render_select('status',$status,array('id','name'),'status', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
        <?php echo form_hidden('software','sage_accounting'); ?>
  </div>
</div>
<table class="table table-customers">
  <thead>
    <th><?php echo _l('clients_list_company'); ?></th>
    <th><?php echo _l('contact_primary'); ?></th>
    <th><?php echo _l('company_primary_email'); ?></th>
    <th><?php echo _l('clients_list_phone'); ?></th>
    <th><?php echo _l('customer_active'); ?></th>
    <th><?php echo _l('customer_groups'); ?></th>
    <th><?php echo _l('status'); ?></th>
    <th><?php echo _l('error_message'); ?></th>
    <th><?php echo _l('options'); ?></th>
  </thead>
  <tbody>
    
  </tbody>
</table>