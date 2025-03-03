<div class="modal fade" id="appointmentModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<?php 
				$title='';
				$id='';
				$compute_based_on_real_time='';
				$set_duration_manually='';

				$once_all_products_are_processed='';
				$once_some_products_are_processed='';

				$based_on_hide='';
				$default_duration_hide='';
				$quantity_process_hide='';

				if(isset($operation)){
					$title =_l('update_operation');
					$id= $operation->id;

					if($operation->duration_computation == 'compute_based_on_real_time'){
						$compute_based_on_real_time = "checked";
						$default_duration_hide = ' hide';

					}elseif($operation->duration_computation == 'set_duration_manually'){
						$set_duration_manually = "checked";
						$based_on_hide = ' hide';
					}

					if($operation->start_next_operation == 'once_all_products_are_processed'){
						$once_all_products_are_processed = "checked";
						$quantity_process_hide = ' hide';
					}elseif($operation->start_next_operation == 'once_some_products_are_processed'){
						$once_some_products_are_processed = "checked";
					}

					$based_on = $operation->based_on;
					$default_duration = $operation->default_duration;
					$quantity_process = $operation->quantity_process;
					$display_order = $operation->display_order;
					$operation_value = $operation->operation;
					$work_center_selected = $operation->work_center_id;

				}else{
					$title =_l('add_operation');
					$set_duration_manually = "checked";
					$once_all_products_are_processed = "checked";

					$based_on = 10;
					$default_duration = 60;
					$quantity_process = 1;
					$display_order = 0;
					$operation_value ='';
					$work_center_selected='';

					$based_on_hide = ' hide';
					$quantity_process_hide = ' hide';

				}

				$routing_id = isset($routing_id) ? $routing_id : '';

				?>
				<h4 class="modal-title"><?php echo new_html_entity_decode($title); ?></h4>
			</div>
			<?php echo form_open_multipart(admin_url('manufacturing/add_edit_operation/'.$id), array('id' => 'add_edit_operation')); ?>
			<div class="modal-body">
				<div class="tab-content">
					<div class="row">
						<input type="hidden" value="<?php echo new_html_entity_decode($routing_id); ?>" name="routing_id">

						<div class="row">
							<div class="col-md-12">
								<div class="col-md-6">
									<?php echo render_input('operation','operation', $operation_value,'text'); ?>   

									<?php echo render_select('work_center_id',$work_centers,array('id','work_center_name'),'work_center_name',$work_center_selected); ?>
								</div>
								<div class="col-md-6">

									<div class="form-group">
										<label for="profit_rate" class="control-label clearfix"><?php echo _l('duration_computation'); ?></label>
										<div class="radio radio-primary radio-inline" >
											<input type="radio" id="compute_based_on_real_time" name="duration_computation" value="compute_based_on_real_time" <?php  echo  new_html_entity_decode($compute_based_on_real_time); ?>>
											<label for="compute_based_on_real_time"><?php echo _l('duration_computation_label1'); ?></label>

										</div>
										<br>
										<div class="radio radio-primary radio-inline" >
											<input type="radio" id="set_duration_manually" name="duration_computation" value="set_duration_manually" <?php echo new_html_entity_decode($set_duration_manually) ; ?>>
											<label for="set_duration_manually"><?php echo _l('duration_computation_label2'); ?></label>

										</div>
									</div>

									<div class='based_on_hide <?php echo new_html_entity_decode($based_on_hide); ?>'>
										<?php echo render_input('based_on','based_on', $based_on,'number'); ?>   
									</div>

									<div class='default_duration_hide <?php echo new_html_entity_decode($default_duration_hide); ?>'>
										<?php echo render_input('default_duration','default_duration', $default_duration,'number'); ?> 
									</div>  


								</div>
							</div>
						</div>


						<div class="row">
							<div class="col-md-12">
								<div class="col-md-6">

									<div class="form-group">
										<label for="profit_rate" class="control-label clearfix"><?php echo _l('start_next_operation'); ?></label>
										<div class="radio radio-primary radio-inline" >
											<input  type="radio" id="once_all_products_are_processed" name="start_next_operation" value="once_all_products_are_processed" <?php echo new_html_entity_decode($once_all_products_are_processed) ; ?>>
											<label for="once_all_products_are_processed"><?php echo _l('start_next_operation1'); ?></label>

										</div>
										<br>
										<div class="radio radio-primary radio-inline" >
											<input  type="radio" id="once_some_products_are_processed" name="start_next_operation" value="once_some_products_are_processed" <?php  echo new_html_entity_decode($once_some_products_are_processed); ?>>
											<label for="once_some_products_are_processed"><?php echo _l('start_next_operation2'); ?></label>

										</div>
									</div>
									<div class="quantity_process_hide <?php echo new_html_entity_decode($quantity_process_hide); ?>">
										<?php echo render_input('quantity_process','quantity_process', $quantity_process,'number'); ?>   
									</div>


								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="col-md-6">
									<?php echo render_input('display_order','display_order', $display_order,'number'); ?>   
								</div>
							</div>
						</div>


						<div class="col-md-12">

							<p class="bold"><?php echo _l('operation_description'); ?></p>
							<?php
               					// onclick and onfocus used for convert ticket to task too
							echo render_textarea('description','',(isset($operation) ? $operation->description : ''),array('rows'=>6,'placeholder'=>_l('task_add_description'),'data-task-ae-editor'=>true, !is_mobile() ? 'onclick' : 'onfocus'=>(!isset($operation) || isset($operation) && $operation->description == '' ? 'routing_init_editor(\'.tinymce-operation\', {height:200, auto_focus: true});' : 'routing_init_editor(\'.tinymce-operation\', {height:200, auto_focus: true});' )),array(),'','tinymce-operation'); ?>
						</div>

						<div class="col-md-12">
							<div class=" attachments">
								<div class="attachment">
									<div class="col-md-6">
										<div class="form-group">
											<label for="attachment" class="control-label"><?php echo _l('add_task_attachments'); ?></label>
											<div class="input-group">
												<input type="file" extension="<?php echo new_str_replace('.','',get_option('allowed_files')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="file[0]">
												<span class="input-group-btn">
													<button class="btn btn-success add_more_attachments_file p8" type="button"><i class="fa fa-plus"></i></button>
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div id="contract_attachments" class="mtop30 ">
									<?php if(isset($operation_attachment)){ ?>

										<?php
										$data = '<div id="attachment_file">';
										foreach($operation_attachment as $attachment) {
											$path = MANUFACTURING_OPERATION_ATTACHMENTS_UPLOAD_FOLDER.'/' .$attachment['rel_id'].'/'.$attachment['file_name'];
											
											if(!file_exists($path)){
												continue;
											}
											$href_url = site_url('modules/hr_profile/uploads/contracts/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
											if(!empty($attachment['external'])){
												$href_url = $attachment['external_link'];
											}
											$data .= '<div class="display-block contract-attachment-wrapper">';
											$data .= '<div class="col-md-10">';
											$data .= '<div class="col-md-1 mr-5">';
											$data .= '<a name="preview-btn" onclick="preview_file(this); return false;" rel_id = "'.$attachment['rel_id'].'" id = "'.$attachment['id'].'" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'._l("preview_file").'">';
											$data .= '<i class="fa fa-eye"></i>'; 
											$data .= '</a>';
											$data .= '</div>';
											$data .= '<div class=col-md-9>';
											$data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
											$data .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
											$data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
											$data .= '</div>';
											$data .= '</div>';
											$data .= '<div class="col-md-2 text-right">';
											if( has_permission('manufacturing', '', 'delete')){
												$data .= '<a href="#" class="text-danger" onclick="delete_operation_attachment(this,'.$attachment['id'].'); return false;"><i class="fa fa fa-times"></i></a>';
											}
											$data .= '</div>';
											$data .= '<div class="clearfix"></div><hr/>';
											$data .= '</div>';
										}
										$data .= '</div>';
										echo new_html_entity_decode($data);
										?>
									<?php } ?>
									<!-- check if edit contract => display attachment file end-->

								</div>
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
<?php require('modules/manufacturing/assets/js/routings/routing_details/add_edit_operation_js.php'); ?>