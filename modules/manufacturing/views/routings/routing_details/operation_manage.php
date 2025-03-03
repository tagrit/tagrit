<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">

			<div class="col-md-5">
				<div class="row">
					<div class="panel_s">
						<?php 

						$routing_id = isset($routing) ? $routing->id : '';
						$routing_code = isset($routing) ? $routing->routing_code : '';
						$routing_name = isset($routing) ? $routing->routing_name : '';
						$description = isset($routing) ? $routing->description : '';
						?>
						<?php echo form_open(admin_url('manufacturing/add_routing_modal/'.$routing_id), array('id' => 'add_routing')); ?>

						<div class="panel-body">
							<h4 class="no-margin">
								<?php echo new_html_entity_decode($routing_code); ?>
							</h4>
							<hr class="hr-panel-heading" />

							<div class="row">
								<div class="col-md-12">
									<?php echo render_input('routing_code','routing_code', $routing_code,'text'); ?>   
								</div>
								<div class="col-md-12">
									<?php echo render_input('routing_name','routing_name', $routing_name,'text'); ?>   
								</div>

								<div class="col-md-12">

									<p class="bold"><?php echo _l('routing_description'); ?></p>
									<?php
               					// onclick and onfocus used for convert ticket to task too
									echo render_textarea('description','',($description),array('rows'=>6,'placeholder'=>_l('task_add_description'),'data-task-ae-editor'=>true, !is_mobile() ? 'onclick' : 'onfocus'=>(!isset($routing) || isset($routing) && $routing->description == '' ? 'routing_init_editor(\'.tinymce-task\', {height:200, auto_focus: true});' : 'routing_init_editor(\'.tinymce-task\', {height:200, auto_focus: true});')),array(),'no-mbot','tinymce-task'); ?>
								</div>	
							</div>

							<hr />
							<button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
							<a href="<?php echo admin_url('manufacturing/routing_manage'); ?>"  class="btn btn-default pull-right mright5 "><?php echo _l('hr_close'); ?></a>
						</div>
						<?php echo form_close(); ?>
					</div>

				</div>
			</div>

			<div class="col-md-7">
				<div class="row">

					<div class="panel_s"> 
						<div class="panel-body">

							<div class="row">
								<div class="col-md-12">
									<h4 class="h4-color no-margin"><i class="fa fa-shirtsinbulk" aria-hidden="true"></i> <?php echo _l('operations'); ?></h4>
								</div>
							</div>
							<hr class="hr-color">

							<?php if(has_permission('manufacturing', '', 'create')){ ?>
								<div class="_buttons">
									<a href="#" onclick="add_operation(<?php echo new_html_entity_decode($routing_id) ?>,0,'add'); return false;" class="btn btn-info mbot10"><?php echo _l('add_operation'); ?></a>

									<a href="<?php echo admin_url('manufacturing/import_xlsx_contract'); ?>" class=" btn mright5 btn-default pull-left hide">
										<?php echo _l('work_center_import'); ?>
									</a>
								</div>
								<br>
							<?php } ?>

							<?php render_datatable(array(
								_l('id'),
								_l('display_order'),
								_l('operation'),
								_l('work_center_name'),
								_l('duration_computation'),
							),'operation_table'); ?>
						</div>

					</div>
				</div>
				<div id="modal_wrapper"></div>
			</div>


		</div>
	</div>
</div>
<div id="contract_file_data"></div>

<?php echo form_hidden('routing_id',$routing_id); ?>
<?php init_tail(); ?>
<?php 
require('modules/manufacturing/assets/js/routings/add_edit_routing_js.php');
require('modules/manufacturing/assets/js/routings/routing_details/operation_manage_js.php');

?>
</body>
</html>
