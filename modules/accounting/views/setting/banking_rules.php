<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
	<a href="<?php echo admin_url('accounting/new_rule'); ?>" class="btn btn-info mbot15"><?php echo _l('add'); ?></a>
</div>
<div class="row">
	<div class="col-md-12">
		<?php 
			$table_data = array(
				_l('name'),
				_l('transaction'),
				);
			render_datatable($table_data,'banking-rules');
		?>
	</div>
</div>
<div class="clearfix"></div>
