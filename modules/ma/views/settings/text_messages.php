<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(has_permission('ma_setting', '', 'create')){ ?>
<div>
   <a href="<?php echo admin_url('ma/text_message'); ?>" class="btn btn-primary mbot15"><?php echo _l('ma_add_new', _l('sms_template')); ?></a>
</div>

<?php } ?>

<div class="row">
	<div class="col-md-12">
		<?php 
			$table_data = array(
            _l('id'),
				_l('name'),
				_l('category'),
				_l('dateadded'),
				);
			render_datatable($table_data,'text-messages');
		?>
	</div>
</div>
