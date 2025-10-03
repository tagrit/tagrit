<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
	<div class="col-md-12">
		<h4 class="h4-color"><i class="fa fa-bars menu-icon" aria-hidden="true"></i> <?php echo _l('mrp_general_setting'); ?></h4>
	</div>
</div>
<hr class="hr-color">

<?php echo form_open_multipart(admin_url('manufacturing/prefix_number'),array('class'=>'prefix_number','autocomplete'=>'off')); ?>

<div class="row">
	<div class="col-md-12">
		<h5 class="no-margin font-bold h5-color"><?php echo _l('BOM_code') ?></h5>
		<hr class="hr-color">
	</div>
</div>

<div class="form-group">
	<label><?php echo _l('mrp_bom_prefix'); ?></label>
	<div  class="form-group" app-field-wrapper="bom_prefix">
		<input type="text" id="bom_prefix" name="bom_prefix" class="form-control" value="<?php echo get_mrp_option('bom_prefix'); ?>"></div>
	</div>

	<div class="form-group">
		<label><?php echo _l('mrp_bom_number'); ?></label>
		<i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('mrp_next_number_tooltip'); ?>"></i>
		<div  class="form-group" app-field-wrapper="bom_number">
			<input type="number" min="0" id="bom_number" name="bom_number" class="form-control" value="<?php echo get_mrp_option('bom_number'); ?>">
		</div>

	</div>

	<div class="row">
		<div class="col-md-12">
			<h5 class="no-margin font-bold h5-color"><?php echo _l('routing_code') ?></h5>
			<hr class="hr-color">
		</div>
	</div>

	<div class="form-group">
		<label><?php echo _l('mrp_routing_prefix'); ?></label>
		<div  class="form-group" app-field-wrapper="routing_prefix">
			<input type="text" id="routing_prefix" name="routing_prefix" class="form-control" value="<?php echo get_mrp_option('routing_prefix'); ?>"></div>
		</div>

		<div class="form-group">
			<label><?php echo _l('mrp_routing_number'); ?></label>
			<i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('mrp_next_number_tooltip'); ?>"></i>
			<div  class="form-group" app-field-wrapper="routing_number">
				<input type="number" min="0" id="routing_number" name="routing_number" class="form-control" value="<?php echo get_mrp_option('routing_number'); ?>">
			</div>

		</div>

		<div class="row">
			<div class="col-md-12">
				<h5 class="no-margin font-bold h5-color"><?php echo _l('mo_code') ?></h5>
				<hr class="hr-color">
			</div>
		</div>

		<div class="form-group">
			<label><?php echo _l('mrp_mo_prefix'); ?></label>
			<div  class="form-group" app-field-wrapper="mo_prefix">
				<input type="text" id="mo_prefix" name="mo_prefix" class="form-control" value="<?php echo get_mrp_option('mo_prefix'); ?>">
			</div>
		</div>

		<div class="form-group">
			<label><?php echo _l('mrp_mo_number'); ?></label>
			<i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('mrp_next_number_tooltip'); ?>"></i>
			<div  class="form-group" app-field-wrapper="mo_number">
				<input type="number" min="0" id="mo_number" name="mo_number" class="form-control" value="<?php echo get_mrp_option('mo_number'); ?>">
			</div>

		</div>

		<div class="row">
			<div class="col-md-12">
				<h5 class="no-margin font-bold h5-color"><?php echo _l('working_hour') ?></h5>
				<hr class="hr-color">
			</div>
		</div>

		<div class="form-group">
			<label><?php echo _l('cost_hour'); ?></label>
			<div  class="form-group" app-field-wrapper="cost_hour">
				<input type="number" id="cost_hour" name="cost_hour" class="form-control" value="<?php echo get_mrp_option('cost_hour'); ?>">
			</div>
		</div>

		<div class="clearfix"></div>

		<div class="modal-footer">
			<?php if(has_permission('manufacturing', '', 'create') || has_permission('manufacturing', '', 'edit') ){ ?>
			<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
		<?php } ?>
		</div>
		<?php echo form_close(); ?>


	</body>
	</html>


