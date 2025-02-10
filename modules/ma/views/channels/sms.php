<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(has_permission('ma_channels', '', 'create')){ ?>

<div>
   <a href="<?php echo admin_url('ma/sms'); ?>" class="btn btn-primary mbot15"><?php echo _l('ma_add_new', _l('sms')); ?></a>
</div>
<hr class="hr-panel-heading">
<?php } ?>
<div class="row">
  <div class="col-md-3">
        <?php echo render_select('category',$categories,array('id','name'),'category', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
  </div>
</div>
<hr class="hr-panel-heading">
<div class="row">
	<div class="col-md-12">
		<?php 
			$table_data = array(
            _l('id'),
				_l('name'),
				_l('category'),
				_l('leads_dt_datecreated'),
				);
			render_datatable($table_data,'sms');
		?>
	</div>
</div>
