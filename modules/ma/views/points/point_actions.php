<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
        <div class="panel_s">
           <div class="panel-body">
<?php if(has_permission('ma_points', '', 'create')){ ?>

            <div>
					   <a href="<?php echo admin_url('ma/point_action'); ?>" class="btn btn-primary"><?php echo _l('ma_add_new', _l('point_action')); ?></a>
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
									_l('dateadded'),
									);
								render_datatable($table_data,'point-actions');
							?>
						</div>
					</div>
           </div>
        </div>
  </div>
</div>
<?php init_tail(); ?>

