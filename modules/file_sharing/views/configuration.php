<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
	<a href="#" class="btn btn-info add-new-config mbot15"><?php echo _l('add'); ?></a>
</div>
<div class="row">
	<div class="col-md-12">
		<?php 
			$table_data = array(
				_l('fs_name'),
				_l('fs_group_or_role'),
				_l('fs_type_config'),
				_l('view'),
				_l('upload_and_override'),
				_l('delete'),
				_l('project_setting_upload_files'),
				_l('download'),
				_l('fs_min_size'),
				_l('fs_max_size'),
				_l('fs_created_at'),
				_l('fs_inserted_at'),
				_l('fs_options'),
				);
			render_datatable($table_data,'fs-config-share');
		?>
	</div>
</div>
<div class="clearfix"></div>
<?php $this->load->view('modal/modal_config.php') ?>
