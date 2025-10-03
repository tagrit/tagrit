<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">               
						<div class="clearfix"></div>
						<h4>
							<?php echo new_html_entity_decode($work_center->work_center_name); ?>
						</h4>

								<hr class="hr-color">
						<div class="col-md-12">

							<div class="row col-md-12">

								<div class="col-md-7 panel-padding">
									<table class="table border table-striped table-margintop">
										<tbody>
											<tr class="project-overview">
												<td class="bold" width="30%"><?php echo _l('work_center_code'); ?></td>
												<td><?php echo new_html_entity_decode($work_center->work_center_code) ; ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo _l('work_center_name'); ?></td>
												<td><?php echo new_html_entity_decode($work_center->work_center_name) ; ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo _l('work_center_working_hours'); ?></td>
												<?php 
												$working_hours_name = '';

												if($work_center->working_hours != '' && $work_center->working_hours != null && $work_center->working_hours != 0){
													$working_hour = $this->manufacturing_model->get_working_hour($work_center->working_hours);
													if($working_hour['working_hour']){
														$working_hours_name .= $working_hour['working_hour']->working_hour_name;
													}
												}

												 ?>
												<td><?php echo new_html_entity_decode($working_hours_name) ?></td>
											</tr>
										</tbody>
									</table>
								</div>

							</div>


							<h4 class="h4-color"><?php echo _l('work_center_info'); ?></h4>
							<hr class="hr-color">
							<div class="col-md-6 panel-padding" >
								<table class="table border table-striped table-margintop" >
									<tbody>
										<tr class="project-overview">
											<td class="bold td-width"><?php echo _l('time_efficiency'); ?></td>
											<td><?php echo new_html_entity_decode($work_center->time_efficiency) ; ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo _l('work_center_capacity'); ?></td>
											<td><?php echo new_html_entity_decode($work_center->capacity) ; ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo _l('work_center_time_start'); ?></td>
											<td><?php echo new_html_entity_decode($work_center->time_start)  ?></td>
										</tr>

									</tbody>
								</table>
							</div>

							<div class="col-md-6 panel-padding" >
								<table class="table table-striped table-margintop">
									<tbody>
										<tr class="project-overview">
											<td class="bold" width="40%"><?php echo _l('costs_hour'); ?></td>
											<td><?php echo new_html_entity_decode($work_center->costs_hour)  ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo _l('oee_target'); ?></td>
											<td><?php echo new_html_entity_decode($work_center->oee_target)  ?></td>
										</tr>

										<tr class="project-overview">
											<td class="bold"><?php echo _l('time_stop'); ?></td>
											<td><?php echo new_html_entity_decode($work_center->time_stop)  ?></td>
										</tr> 

									</tbody>
								</table>
							</div>
							<div class=" row ">
								<div class="col-md-12">
									<h4 class="h4-color"><?php echo _l('work_center_description'); ?></h4>
									<hr class="hr-color">
									<h5><?php echo new_html_entity_decode($work_center->description) ; ?></h5>
								</div>
							</div>

						</div>

						<div class="modal-footer">
							<a href="<?php echo admin_url('manufacturing/work_center_manage'); ?>"  class="btn btn-default mr-2 "><?php echo _l('hr_close'); ?></a>

							<?php if(has_permission('manufacturing', '', 'create') ){ ?>
								<a href="<?php echo admin_url('manufacturing/add_edit_work_center'); ?>" class="btn btn-info pull-right display-block mright5"><?php echo _l('add_work_center'); ?></a>
							<?php } ?>

							<?php if( has_permission('manufacturing', '', 'edit')){ ?>
								<a href="<?php echo admin_url('manufacturing/add_edit_work_center/'.$work_center->id); ?>" class="btn btn-primary pull-right display-block mright5"><?php echo _l('edit_work_center'); ?></a>
							<?php } ?>
							
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<?php init_tail(); ?>

</body>
</html>

