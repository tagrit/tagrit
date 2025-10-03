<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div> 

	<div class="row">
		<div class="col-md-12">
			<h4 class="h4-color"><i class="fa fa-area-chart" aria-hidden="true"></i> <?php echo _l('working_hour'); ?></h4>
		</div>
	</div>
	<hr class="hr-color">

	<?php if(has_permission('manufacturing', '', 'create')){ ?>
		<a href="<?php echo admin_url('manufacturing/add_edit_working_hour'); ?>" class="btn btn-info pull-left display-block mright5"><?php echo _l('add_working_hour'); ?></a>
	<?php } ?>
	<br>
	<br>

	<?php render_datatable(array(
		_l('id'),
		_l('working_hour_name'),
		_l('hours_per_day'),
		_l('options'),
	),'working_hour_table'); ?>

</body>
</html>
