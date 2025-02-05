<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
  <div class="col-md-6 mtop10 border-right">
    <span><?php echo _l('enable_income_statement_modifications'); ?> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('enable_income_statement_modifications_note'); ?>"></i></span>
  </div>
  <div class="col-md-6 mtop10">
    <div class="onoffswitch">
      <input type="checkbox" id="acc_enable_income_statement_modifications" data-perm-id="3" class="onoffswitch-checkbox" data-switch-url="<?php echo admin_url('accounting/apply_income_statement_modification'); ?>" <?php if($acc_enable_income_statement_modifications == '1'){echo 'checked';} ?> value="1" name="acc_enable_income_statement_modifications">
      <label class="onoffswitch-label" for="acc_enable_income_statement_modifications"></label>
    </div>
  </div>
</div>
<hr>
<?php echo form_open(admin_url('accounting/reset_income_statement_modifications')); ?>
<div class="row mbot10">
  <div class="col-md-12">
    <button type="submit" class="btn btn-info _delete"><?php echo _l('reset_income_statement_modifications'); ?></button> <label class="text-danger"><?php echo _l('accounting_reset_income_statement_modifications_button_tooltip'); ?></label>
  </div>
</div>
<hr>
<?php echo form_close(); ?>
<div>
	<a href="#" class="btn btn-info add-new-income-statement-modification mbot15"><?php echo _l('add'); ?></a>
</div>
<div class="row">
	<div class="col-md-12">
		<?php 
			$table_data = array(
				_l('name'),
        _l('type'),
				_l('active'),
				);
			render_datatable($table_data,'income-statement-modifications');
		?>
	</div>
</div>
<div class="clearfix"></div>
<div class="modal fade" id="income-statement-modification-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l('income_statement_modification')?></h4>
      </div>
      <?php echo form_open_multipart(admin_url('accounting/income_statement_modification'),array('id'=>'income-statement-modification-form'));?>
      <?php echo form_hidden('id'); ?>
      <div class="modal-body">
        <?php echo render_input('name','name'); ?>
                  <?php 
                  $type = [
                          1 => ['id' => 'income', 'name' => _l('acc_income')],
                          2 => ['id' => 'net_income', 'name' => _l('acc_net_income')],
                         ];
                  echo render_select('type', $type, array('id', 'name'),'type', 'as_of', array(), array(), '', '', false);
                  ?>
        <div class="row">
          <div class="col-md-10">
            <?php echo render_select('account',$accounts,array('id','name'),'account','',array(),array(),'','',false); ?>
          </div>
          <div class="col-md-2">
            <button name="add" class="btn new_fomula btn-success mtop25" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
          </div>
        </div>
        <div class="fomula-list">
          
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

<div class="row fomula-template hide">
  <div class="col-md-2">
    <?php 
      $fomulas = [
              1 => ['id' => '+', 'name' => '+'],
              2 => ['id' => '-', 'name' => '-'],
              3 => ['id' => '*', 'name' => '*'],
              4 => ['id' => '/', 'name' => '/'],
             ];
        echo render_select('fomula[0]', $fomulas, array('id', 'name'),'fomula', '', array(), array(), '', '', false);
    ?>
  </div>
  <div class="col-md-8">
    <?php echo render_select('account_fomula[0]',$accounts,array('id','name'),'account','',array(),array(),'','',false); ?>
  </div>
  <div class="col-md-2">
    <button name="add" class="btn remove_fomula btn-danger mtop25" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
  </div>
</div>