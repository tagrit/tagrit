<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
 <?php echo form_open(admin_url('accounting/reset_account_detail_types')); ?>
<div class="row mbot10">
    <div class="col-md-12">
      <button type="submit" class="btn btn-info _delete"><?php echo _l('reset_account_detail_types'); ?></button> <label class="text-danger"><?php echo _l('accounting_reset_account_detail_types_button_tooltip'); ?></label>
  </div>
</div>
<hr>
<?php echo form_close(); ?>
<div>
	<a href="#" class="btn btn-info add-new-account-type-detail mbot15"><?php echo _l('add'); ?></a>
</div>
<div class="row">
	<div class="col-md-12">
		<?php 
			$table_data = array(
				_l('name'),
				_l('account_type'),
				);
			render_datatable($table_data,'account-type-details');
		?>
	</div>
</div>
<div class="clearfix"></div>
<div class="modal fade" id="account-type-detail-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l('account_type_detail')?></h4>
      </div>
      <?php echo form_open_multipart(admin_url('accounting/account_type_detail'),array('id'=>'account-type-detail-form'));?>
      <?php echo form_hidden('id'); ?>
      <?php echo form_hidden('note'); ?>
      <div class="modal-body">
        <?php echo render_select('account_type_id',$account_types,array('id','name'),'account_type','',array(),array(),'','',false); ?>
        <?php echo render_input('name','name'); ?>
        <?php 
        	$statement_of_cash_flows = [
                  1 => ['id' => 'cash_flows_from_operating_activities', 'name' => _l('cash_flows_from_operating_activities')],
                  2 => ['id' => 'cash_flows_from_investing_activities', 'name' => _l('cash_flows_from_investing_activities')],
                  3 => ['id' => 'cash_flows_from_financing_activities', 'name' => _l('cash_flows_from_financing_activities')],
                  4 => ['id' => 'cash_and_cash_equivalents_at_beginning_of_year', 'name' => _l('cash_and_cash_equivalents_at_beginning_of_year')],
                 ];
          	echo render_select('statement_of_cash_flows', $statement_of_cash_flows, array('id', 'name'),'statement_of_cash_flows', '', array(), array(), '', '', false);
        ?>
        <div class="row">
          <div class="col-md-12">
            <p class="bold"><?php echo _l('dt_expense_description'); ?></p>
            <?php echo render_textarea('note','','',array(),array(),'','tinymce'); ?>
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