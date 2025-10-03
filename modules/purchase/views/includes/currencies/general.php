<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
	$asm_automatically_get_currency_rate = get_option('cr_automatically_get_currency_rate');
	$asm_global_amount_expiration = get_option('cr_global_amount_expiration');
 ?>
<div  class="row">
<?php echo form_open(admin_url('purchase/update_setting_currency_rate'),array('id'=>'general-settings-form')); ?>
    <div class="col-md-5">
		<?php echo render_input('cr_global_amount_expiration', _l('maximum_number_of_storage_days') .' <i class="fa fa-question-circle" data-toggle="tooltip" data-title="'. _l('maximum_number_of_storage_days_note').'"></i> ', $asm_global_amount_expiration, 'number'); ?>
	</div>
		<div class="col-md-5">
          <div class="row">
              <div class="col-md-6 mtop25 border-right">
                  <span><?php echo _l('automatically_get_currency_rates'); ?></span>
              </div>
              <div class="col-md-6 mtop25">
                  <div class="onoffswitch">
                      <input type="checkbox" id="cr_automatically_get_currency_rate" data-perm-id="3" class="onoffswitch-checkbox" <?php if($asm_automatically_get_currency_rate == '1'){echo 'checked';} ?>  value="1" name="cr_automatically_get_currency_rate">
                      <label class="onoffswitch-label" for="cr_automatically_get_currency_rate"></label>
                  </div>
              </div>
          </div>
      </div>

	  <div class="col-md-2 mtop25">

    <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
  </div>
<?php echo form_close(); ?>
	<div class="row">    
      <div class="_buttons col-md-12">
			<hr>
        <div class="col-md-3">
        	<?php echo render_select('from_currency',$currencies,array('id','name'),'from_currency', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
        </div>
        <div class="col-md-3">
        	<?php echo render_select('to_currency',$currencies,array('id','name'),'to_currency', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
        </div>
        <div class="col-md-6">
        	<a href="<?php echo admin_url('purchase/get_all_currency_rate_online'); ?>" class="btn btn-info pull-right mtop25" ><?php echo _l('get_online_currency_rates'); ?></a>
        </div>
    </div>
  </div>
	<div class="clearfix"></div>
	<br>
	<div class="clearfix"></div>
	<div  class="col-md-12">
		<table class="table table-currency_rates scroll-responsive">
			<thead>
				<tr>
					<th><?php echo _l('pur_type'); ?></th>
					<th><?php echo _l('pur_currency_rate'); ?></th>
					<th><?php echo _l('pur_updated_at'); ?></th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<td></td>
				<td></td>
				<td></td>
			</tfoot>
		</table>
	</div>
</div>


<div id="modal_wrapper"></div>

