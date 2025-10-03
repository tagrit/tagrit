<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<?php echo form_open_multipart(admin_url('purchase/update_order_return_setting'),array('id'=>'pur_order_setting-form')); ?>


<div class="row">
	<div class="col-md-6">
		<?php echo render_input('pur_order_return_number_prefix','pur_order_return_number_prefix',get_option('pur_order_return_number_prefix'), 'text'); ?>
	</div>

	<div class="col-md-6">
		<?php echo render_input('next_pur_order_return_number','next_pur_order_return_number',get_option('next_pur_order_return_number'), 'number'); ?>
	</div>
</div>

<button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
<?php echo form_close(); ?>