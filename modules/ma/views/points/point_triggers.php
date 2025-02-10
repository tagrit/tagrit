<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
   <a href="<?php echo admin_url('ma/point_trigger'); ?>" class="btn btn-primary mbot15"><?php echo _l('add'); ?></a>
</div>
<hr class="hr-panel-heading">
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
				_l('dateadded'),
				);
			render_datatable($table_data,'point-triggers');
		?>
	</div>
</div>
