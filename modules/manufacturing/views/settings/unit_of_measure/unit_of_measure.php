<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div> 

	<div class="row">
		<div class="col-md-12">
			<h4 class="h4-color"><i class="fa fa-list-alt" aria-hidden="true"></i> <?php echo _l('unit_of_measure'); ?></h4>
		</div>
	</div>
	<hr class="hr-color">

	<?php if(has_permission('manufacturing', '', 'create')){ ?>
		<a href="#" onclick="add_edit_unit_measure(0,'add'); return false;" class="btn btn-info mbot10"><?php echo _l('mrp_add'); ?></a>

	<?php } ?>
	<br>
	<br>

	<?php render_datatable(array(
		_l('id'),
		_l('unit_of_measure'),
		_l('category'),
		_l('mrp_type'),
		_l('options'),
	),'unit_of_measure_table'); ?>

	<div id="modal_wrapper"></div>
	
</body>
</html>
