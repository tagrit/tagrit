<div class="modal fade" id="appointmentModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

				<h4 class="modal-title"><?php echo new_html_entity_decode(_l('add_routing')); ?></h4>
			</div>
			<?php echo form_open(admin_url('manufacturing/add_routing_modal'), array('id' => 'add_routing')); ?>
			<div class="modal-body">
				<div class="tab-content">
					<div class="row">

						<div class="col-md-12">
							<div class="col-md-4">
								<?php echo render_input('routing_code','routing_code', $routing_code,'text'); ?>   
							</div>
							<div class="col-md-8">
								<?php echo render_input('routing_name','routing_name','','text'); ?>   
							</div>
							
							<div class="col-md-12">
								
								<p class="bold"><?php echo _l('routing_description'); ?></p>
								<?php
               					// onclick and onfocus used for convert ticket to task too
								echo render_textarea('description','',(isset($routing) ? $routing->description : ''),array('rows'=>6,'placeholder'=>_l('task_add_description'),'data-task-ae-editor'=>true, !is_mobile() ? 'onclick' : 'onfocus'=>(!isset($routing) || isset($routing) && $routing->description == '' ? 'routing_init_editor(\'.tinymce-task\', {height:200, auto_focus: true});' : '')),array(),'no-mbot','tinymce-task'); ?>
							</div>	
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
			</div>

		</div>

		<?php echo form_close(); ?>
	</div>
</div>
</div>
<?php require('modules/manufacturing/assets/js/routings/add_edit_routing_js.php'); ?>