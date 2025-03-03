<div class="modal fade" id="appointmentModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<?php 
				$title='';
				$id='';
				
				$bigger_ratio_hide='';
				$smaller_ratio_hide='';
				$checked='';

				if(isset($unit_of_measure)){
					$title =_l('update_unit_of_measure');
					$id= $unit_of_measure->unit_type_id;
					$bigger_ratio = $unit_of_measure->bigger_ratio;
					$smaller_ratio = $unit_of_measure->smaller_ratio;

					if($unit_of_measure->unit_measure_type == 'smaller'){
						$bigger_ratio_hide = ' hide';
						$bigger_ratio = 1;

					}elseif($unit_of_measure->unit_measure_type == 'bigger'){
						$smaller_ratio_hide = ' hide';
						$smaller_ratio = 1;

					}else{
						$bigger_ratio_hide = ' hide';
						$smaller_ratio_hide = ' hide';
						$bigger_ratio = 1;
						$smaller_ratio = 1;
						
					}

					if($unit_of_measure->display == 1){
						$checked =' checked';
					}

					$unit_name = $unit_of_measure->unit_name;
					$rounding = $unit_of_measure->rounding;
					$unit_measure_type = $unit_of_measure->unit_measure_type;
					$category_id = $unit_of_measure->category_id;


				}else{
					$title =_l('add_unit_of_measure');
					$unit_name ='';
					$category_id ='';
					$unit_measure_type ='reference';
					$rounding = 0.01;
					$bigger_ratio_hide=' hide';
					$smaller_ratio_hide=' hide';
					$checked = ' checked';

					$bigger_ratio = 1;
					$smaller_ratio = 1;
				}

				$routing_id = isset($routing_id) ? $routing_id : '';

				?>
				<h4 class="modal-title"><?php echo new_html_entity_decode($title); ?></h4>
			</div>
			<?php echo form_open_multipart(admin_url('manufacturing/add_edit_unit_of_measure/'.$id), array('id' => 'add_edit_unit_of_measure')); ?>
			<div class="modal-body">
				<div class="tab-content">
					<div class="row">

						<div class="row">
							<div class="col-md-12">
								<div class="col-md-6">
									<?php echo render_input('unit_name','unit_of_measure', $unit_name,'text'); ?>   

									<?php echo render_select('unit_measure_type',$unit_types,array('id','value'),'mrp_type',$unit_measure_type); ?>

									<div class="bigger_ratio_hide <?php echo new_html_entity_decode($bigger_ratio_hide); ?>">
										<div class="form-group" app-field-wrapper="bigger_ratio">
											<label for="bigger_ratio" class="control-label"><?php echo _l('bigger_ratio'); ?><small><?php echo _l('ratio_eg_bigger') ?></small></label>
											<input type="number" id="bigger_ratio" name="bigger_ratio" class="form-control" value="<?php echo new_html_entity_decode($bigger_ratio); ?>">
										</div>
									</div>

									<div class="smaller_ratio_hide <?php echo new_html_entity_decode($smaller_ratio_hide); ?>">
										<div class="form-group" app-field-wrapper="smaller_ratio">
											<label for="smaller_ratio" class="control-label"><?php echo _l('smaller_ratio'); ?><small><?php echo _l('ratio_eg_smaller') ?></small></label>
											<input type="number" id="smaller_ratio" name="smaller_ratio" class="form-control" value="<?php echo new_html_entity_decode($smaller_ratio); ?>">
										</div>
									</div>
									
								</div>
								<div class="col-md-6">
									<?php echo render_select('category_id',$categories,array('id','category_name'),'category',$category_id); ?>
									<?php echo render_input('rounding','rounding', $rounding,'number'); ?> 
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="col-md-6">
									<div class="form-group">
										<label>   </label>
										<div class="checkbox checkbox-primary">
											<input type="checkbox" id="display" name="display" value="1" <?php echo new_html_entity_decode($checked) ?>>
											<label for="display"><?php echo _l('mrp_active'); ?></label>
										</div>
									</div>
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
<?php require('modules/manufacturing/assets/js/settings/add_edit_unit_of_measure_js.php'); ?>