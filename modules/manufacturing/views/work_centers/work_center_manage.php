<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s"> 
					<div class="panel-body">

						<div class="row">
							<div class="col-md-12">
								<h4 class="h4-color no-margin"><i class="fa fa-question-circle" aria-hidden="true"></i> <?php echo _l('work_center'); ?></h4>
							</div>
						</div>
						<hr class="hr-color">

						<?php if(has_permission('manufacturing', '', 'create')){ ?>
							<div class="_buttons">
								<a href="<?php echo admin_url('manufacturing/add_edit_work_center'); ?>" class="btn btn-info pull-left display-block mright5"><?php echo _l('add_work_center'); ?></a>

								<a href="<?php echo admin_url('manufacturing/import_xlsx_contract'); ?>" class=" btn mright5 btn-default pull-left hide">
									<?php echo _l('work_center_import'); ?>
								</a>
							</div>
							<br>
							<br>
							<br>
						<?php } ?>

						<?php render_datatable(array(
							_l('id'),
							_l('work_center_code'),
							_l('work_center_name'),
							_l('work_center_working_hours'),
						),'work_center_table'); ?>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<?php 
require('modules/manufacturing/assets/js/work_centers/work_center_manage_js.php');
?>
</body>
</html>
