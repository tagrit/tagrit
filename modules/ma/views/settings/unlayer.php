<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
  	$project_id = get_option('ma_unlayer_project_id');
?>

<?php echo form_open(admin_url('ma/save_unlayer_setting')); ?>
	<?php echo render_input('ma_unlayer_project_id', 'ma_project_id', $project_id, 'password'); ?>
	<div class="col-md-12">
	  <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
	</div>
<?php echo form_close(); ?>
	           