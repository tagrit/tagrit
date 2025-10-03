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
								<h4 class="h4-color"><i class="fa fa-cogs" aria-hidden="true"></i> <?php echo _l('routing'); ?></h4>
							</div>
						</div>
						<hr class="hr-color">

						<?php if(has_permission('manufacturing', '', 'create')){ ?>
							<div class="_buttons">
								<a href="#" onclick="add_routing(0,0,' hide'); return false;" class="btn btn-info mbot10"><?php echo _l('add_routing'); ?></a>

								<a href="<?php echo admin_url('manufacturing/import_xlsx_contract'); ?>" class=" btn mright5 btn-default pull-left hide">
									<?php echo _l('work_center_import'); ?>
								</a>
							</div>
							<br>
						<?php } ?>

						<?php render_datatable(array(
							_l('id'),
							_l('routing_code'),
							_l('routing_name'),
							_l('routing_description'),
						),'routing_table'); ?>
					</div>

				</div>
			</div>

<div id="modal_wrapper"></div>

		</div>
	</div>
</div>
<?php init_tail(); ?>
<?php 
require('modules/manufacturing/assets/js/routings/routing_manage_js.php');
?>
</body>
</html>
