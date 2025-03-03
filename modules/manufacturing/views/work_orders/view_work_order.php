<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php 
			$id = '';
			$title = '';
			$title .= _l('view_manufacturing_order_lable');

			$start_working_hide='';
			$action_hide='';
			$cancel_hide='';

			$waiting_for_another_wo_active='';
			$ready_active='';
			$progress_active='';
			$finished_active='';


			switch ($work_order->status) {
				case 'waiting_for_another_wo':
					$waiting_for_another_wo_active=' active';
					$start_working_hide = '';
					$start_working_status = 'default';
					$action_hide=' hide';
					$pause_hide=' hide';

					break;
				case 'ready':
					$ready_active=' active';
					$start_working_hide = '';
					$start_working_status = 'success';
					$action_hide=' hide';
					$pause_hide=' hide';

					break;
				case 'in_progress':
					$progress_active=' active';
					$start_working_hide = ' hide';
					$start_working_status = 'default';
					$action_hide=' ';
					$pause_hide=' hide';

					break;
				case 'finished':
					$finished_active=' active';
					$start_working_hide = ' hide';
					$start_working_status = 'default';
					$action_hide=' hide';
					$pause_hide=' hide';
					$cancel_hide=' hide';

					break;
				case 'pause':
					$pause_hide=' ';
					$start_working_hide = ' hide';
					$start_working_status = 'default';
					$action_hide=' hide';
					$progress_active=' active';
					break;
					
			
			}


			?>

			<div class="col-md-12" >
				<div class="panel_s">
					
					<div class="panel-body">
						<div class="row mb-5">
							<div class="col-md-9">
								<h4 class="no-margin"><?php echo new_html_entity_decode($header); ?> 
							</div>
							<div class="col-md-3">

								<a href="<?php echo admin_url('manufacturing/view_work_order/'.$next_id.'/'.$manufacturing_order_id); ?>" class=" btn mright5 btn-info pull-right button-text-transform"><?php echo _l('mrp_next'); ?> <i class="fa fa-chevron-circle-right"></i></a>

								<a href="<?php echo admin_url('manufacturing/view_work_order/'.$prev_id.'/'.$manufacturing_order_id); ?>" class=" btn mright5 btn-default pull-right button-text-transform"><i class="fa fa-chevron-circle-left"></i>
									<?php echo _l('mrp_prev'); ?>
								</a>
								<button type="button" class=" btn mright5 btn-default pull-right button-text-transform"><?php echo new_html_entity_decode($pager_value).'/'.new_html_entity_decode($pager_limit); ?> </button>
								
							</div>
						</div>
						<br>
						<hr class="hr-color no-margin">
						<br>

						<!-- action related work order -->
						<div class="row">
							<div class="col-md-6">

								<?php if(has_permission('manufacturing', '', 'create') || has_permission('manufacturing', '', 'edit') ){ ?>

									<?php if(!$check_mo_cancelled){ ?>
									<div class="<?php echo new_html_entity_decode($start_working_hide) ?>">
										<button type="button" class="btn btn-sm btn-<?php echo new_html_entity_decode($start_working_status); ?> pull-left mark_start_working mright5"><?php echo _l('mrp_start_working'); ?></button>
									</div>

									<div class="<?php echo new_html_entity_decode($action_hide) ?>">
										<button type="button" class="btn btn-warning pull-left mark_pause mright5"><?php echo _l('mrp_pause'); ?></button>
										<button type="button" class="btn btn-success pull-left mark_done mright5"><?php echo _l('mrp_done'); ?></button>
									</div>

									<div class="<?php echo new_html_entity_decode($pause_hide) ?>">
										<button type="button" class="btn btn-sm btn-default pull-left mark_start_working mright5"><?php echo _l('mrp_continute_production'); ?></button>
									</div>

									<!-- <button type="button" class="btn btn-default pull-left mark_cancel mright5 <?php echo new_html_entity_decode($cancel_hide) ?>"><?php echo _l('mrp_cancel'); ?></button> -->
								<?php } ?>
									
								<?php } ?>
							</div>

							<!-- status -->
							<div class="col-md-6">
								<div class="sw-main sw-theme-arrows pull-right">

									<!-- SmartWizard html -->
									<ul class="nav nav-tabs step-anchor">
										<li class="<?php echo new_html_entity_decode($waiting_for_another_wo_active) ?>"><a href="#"><?php echo _l('waiting_for_another_wo'); ?></a></li>
										<li class="<?php echo new_html_entity_decode($ready_active) ?>"><a href="#"><?php echo _l('ready'); ?></a></li>
										<li class="<?php echo new_html_entity_decode($progress_active) ?>"><a href="#"><?php echo _l('mrp_in_progress'); ?></a></li>
										<li class="<?php echo new_html_entity_decode($finished_active) ?>"><a href="#"><?php echo _l('mrp_finished'); ?></a></li>
									</ul>
								</div>

							</div>
							<!-- status -->

						</div>
						<!-- action related work order -->

						<hr class="">
						

						<!-- start tab -->
						<div class="modal-body">
							<div class="tab-content">
								<!-- start general infor -->
								<?php 

								$id = isset($manufacturing_order) ? $manufacturing_order->id : '';
								$product_id = isset($work_order) ? $work_order->product_id : '';
								$quantity_produced = isset($work_order) ? $work_order->qty_produced : '';
								$qty_production = isset($work_order) ? $work_order->qty_production : '';

								$unit_id = isset($work_order) ? $work_order->unit_id : '';
								$work_center = isset($work_order) ? $work_order->work_center_id : '';
								$manufacturing_order = isset($work_order) ? $work_order->manufacturing_order_id : '';
								$qty_producing = isset($work_order) ? $work_order->qty_producing : '';
								$work_order_id = isset($work_order) ? $work_order->id : '';
								$date_planned_start = isset($work_order) ? _dt($work_order->date_planned_start) : '';
								$date_planned_finished = isset($work_order) ? _dt($work_order->date_planned_finished) : '';
								$duration_expected = isset($work_order) ? $work_order->duration_expected : '';
								$real_duration = isset($work_order) ? $work_order->real_duration : '';
								$date_start = isset($work_order) ? _dt($work_order->date_start) : '';
								$date_finished = isset($work_order) ? _dt($work_order->date_finished) : '';

								?>
								<div class="row">
									<div class="col-md-6 panel-padding" >
										<input type="hidden" name="manufacturing_order" value="<?php echo new_html_entity_decode($manufacturing_order) ?>">
										<input type="hidden" name="work_order_id" value="<?php echo new_html_entity_decode($work_order_id) ?>">

										<table class="table border table-striped table-margintop" >
											<tbody>
												<tr class="project-overview">
													<td class="bold td-width"><?php echo _l('to_produce'); ?></td>
													<td><b><?php echo mrp_get_product_name($product_id) ; ?></b></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('quantity_produced'); ?></td>
													<td><?php echo new_html_entity_decode($quantity_produced.'/'.$qty_production.' ') ; ?><b><?php echo mrp_get_unit_name($unit_id); ?></b></td>
												</tr>

											</tbody>
										</table>
									</div>

								</div>


								<div class="row">
									<h5 class="h5-color"><?php echo _l('work_center_info'); ?></h5>
									<hr class="hr-color">
								</div>

								<div class="row">
									<div class="horizontal-scrollable-tabs preview-tabs-top">
										<div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
										<div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
										<div class="horizontal-tabs">
											<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
												<li role="presentation" class="">
													<a href="#work_instruction" aria-controls="work_instruction"  class="work_instruction" role="tab" data-toggle="tab">
														<span class="glyphicon glyphicon-align-justify"></span>&nbsp;<?php echo _l('work_instruction'); ?>
													</a>
												</li>
												<li role="presentation" class="">
													<a href="#current_production" aria-controls="current_production" role="tab" data-toggle="tab">
														<span class="fa fa-cogs menu-icon"></span>&nbsp;<?php echo _l('current_production'); ?>
													</a>
												</li>
												<li role="presentation" class="active">
													<a href="#time_tracking" aria-controls="time_tracking" role="tab" data-toggle="tab">
														<span class="fa fa-balance-scale menu-icon"></span>&nbsp;<?php echo _l('time_tracking'); ?>
													</a>
												</li>
												<li role="presentation" class="">
													<a href="#miscellaneous" aria-controls="miscellaneous" role="tab" data-toggle="tab">
														<span class="fa fa-balance-scale menu-icon"></span>&nbsp;<?php echo _l('miscellaneous'); ?>
													</a>
												</li>
												<li role="presentation" class="">
													<a href="#tasks" onclick="init_rel_tasks_table(<?php echo new_html_entity_decode($work_order->id); ?>,'work_order'); return false;" aria-controls="tasks" role="tab" data-toggle="tab">
														<span class="fa-regular fa-circle-check menu-icon"></span>&nbsp;<?php echo _l('tasks'); ?>
													</a>
												</li>

											</ul>
										</div>
									</div>
									<br>


									<div class="tab-content active">
										<div role="tabpanel" class="tab-pane " id="work_instruction">
											<div class="row">
												<?php if(count($work_order_file) > 0){ ?>
													<div class="col-md-12 border-right work_order_area">

														<?php	foreach ($work_order_file as $file) {

															?>

															<?php if(!empty($file['external']) && $file['external'] == 'dropbox'){ ?>
																<a href="<?php echo new_html_entity_decode($file['external_link']); ?>" target="_blank" class="btn btn-info mbot20"><i class="fa fa-dropbox" aria-hidden="true"></i> <?php echo _l('open_in_dropbox'); ?></a><br />
															<?php } ?>
															<?php
															$path = MANUFACTURING_OPERATION_ATTACHMENTS_UPLOAD_FOLDER.'/' .$file['rel_id'].'/'.$file['file_name'];
															if(!file_exists($path)){
																continue;
															}
															if(is_image($path)){ ?>
																<img src="<?php echo base_url(OPERATION_ATTACHMENTS.$file['rel_id'].'/'.$file['file_name']); ?>" class="img img-responsive" >
															<?php } else if(!empty($file['external']) && !empty($file['thumbnail_link'])){ ?>
																<img src="<?php echo optimize_dropbox_thumbnail($file['thumbnail_link']); ?>" class="img img-responsive">
															<?php } else if(strpos($file['filetype'],'pdf') !== false && empty($file['external'])){ ?>
																<iframe src="<?php echo base_url(OPERATION_ATTACHMENTS.$file['rel_id'].'/'.$file['file_name']); ?>" height="100%" width="100%" frameborder="0"></iframe>
															<?php } else if(strpos($file['filetype'],'xls') !== false && empty($file['external'])){ ?>
																<iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url(OPERATION_ATTACHMENTS.$file['rel_id'].'/'.$file['file_name']); ?>' width='100%' height='100%' frameborder='0'>
																</iframe>
															<?php } else if(strpos($file['filetype'],'xlsx') !== false && empty($file['external'])){ ?>
																<iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url(OPERATION_ATTACHMENTS.$file['rel_id'].'/'.$file['file_name']); ?>' width='100%' height='100%' frameborder='0'>
																</iframe>
															<?php } else if(strpos($file['filetype'],'doc') !== false && empty($file['external'])){ ?>
																<iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url(OPERATION_ATTACHMENTS.$file['rel_id'].'/'.$file['file_name']); ?>' width='100%' height='100%' frameborder='0'>
																</iframe>
															<?php } else if(strpos($file['filetype'],'docx') !== false && empty($file['external'])){ ?>
																<iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url(OPERATION_ATTACHMENTS.$file['rel_id'].'/'.$file['file_name']); ?>' width='100%' height='100%' frameborder='0'>
																</iframe>
															<?php } else if(is_html5_video($path)) { ?>
																<video width="100%" height="100%" src="<?php echo site_url('download/preview_video?path='.protected_file_url_by_path($path).'&type='.$file['filetype']); ?>" controls>
																	Your browser does not support the video tag.
																</video>
															<?php } else if(is_markdown_file($path) && $previewMarkdown = markdown_parse_preview($path)) {
																echo new_html_entity_decode($previewMarkdown);
															} else {
																echo '<a href="'.site_url(OPERATION_ATTACHMENTS.$file['rel_id'].'/'.$file['file_name']).'" download>'.$file['file_name'].'</a>';
																echo '<p class="text-muted">'._l('no_preview_available_for_file').'</p>';
															} ?>

														<?php } ?>
													</div>
												<?php } ?>

											</div>

										</div>

										<div role="tabpanel" class="tab-pane " id="current_production">
											<div class="row">
												<div class="col-md-6 panel-padding" >
													<table class="table table-striped table-margintop">
														<tbody>
															<tr class="project-overview">
																<td class="bold" width="40%"><?php echo _l('quantity_in_production'); ?></td>
																<td><?php echo new_html_entity_decode($qty_producing)  ?></td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane active" id="time_tracking">
											<div class="row">
												<div class="col-md-6 panel-padding" >
													<table class="table table-striped table-margintop">
														<tbody>
															<tr class="project-overview">
																<td class="bold" width="40%"><?php echo _l('planned_date'); ?></td>
																<td><b><?php echo new_html_entity_decode($date_planned_start) ?></b><?php echo' '?><?php echo _l('mrp_to')?><?php echo' '?><b><?php echo new_html_entity_decode($date_planned_finished)  ?></b></td>
															</tr>
															<tr class="project-overview">
																<td class="bold"><?php echo _l('effective_date'); ?></td>
																<td><b><?php echo new_html_entity_decode($date_start) ?></b><?php echo' '?><?php echo _l('mrp_to')?><?php echo' '?><b><?php echo new_html_entity_decode($date_finished)  ?></b></td>
															</tr>

														</tbody>
													</table>
												</div>
											
												<div class="col-md-6 panel-padding" >
													<table class="table table-striped table-margintop">
														<tbody>
															<tr class="project-overview">
																<td class="bold" width="40%"><?php echo _l('expected_duration'); ?></td>
																<td><b><?php echo new_html_entity_decode($duration_expected)?></b> <?php echo _l('mrp_minutes')  ?></td>
															</tr>
															<tr class="project-overview">
																<td class="bold"><?php echo _l('real_duration'); ?></td>
																<td><b><?php echo new_html_entity_decode($real_duration)?></b> <?php echo _l('mrp_minutes')  ?></td>
															</tr>

														</tbody>
													</table>
												</div>
											</div>
											
											<div class="row">
												<div class="col-md-12">
											<div class="form"> 
												<div id="time_tracking_hs" class="product_tab handsontable htColumnHeaders">
												</div>
												<?php echo form_hidden('time_tracking_hs'); ?>
											</div>
													
												</div>
											</div>
											
										</div>

										<div role="tabpanel" class="tab-pane " id="miscellaneous">
											<div class="row">
												<div class="col-md-6 panel-padding" >
													<table class="table table-striped table-margintop">
														<tbody>
															<tr class="project-overview">
																<td class="bold" width="40%"><?php echo _l('work_center'); ?></td>
																<td><?php echo new_html_entity_decode(get_work_center_name($work_center))  ?></td>
															</tr>
															<tr class="project-overview">
																<td class="bold"><?php echo _l('manufacturing_order'); ?></td>
																<td><?php echo new_html_entity_decode(mrp_get_manufacturing_code($manufacturing_order))  ?></td>
															</tr>

														</tbody>
													</table>
												</div>
											</div>
										</div>

										<div role="tabpanel" class="tab-pane " id="tasks">
											<?php init_relation_tasks_table(array('data-new-rel-id'=>$work_order->id,'data-new-rel-type'=>'work_order')); ?>
										</div>
										
									</div>
								</div>

							</div>

							<div class="modal-footer">
								<a href="<?php echo admin_url('manufacturing/work_order_manage'); ?>"  class="btn btn-default mr-2 "><?php echo _l('close'); ?></a>
								<a href="<?php echo admin_url('manufacturing/view_manufacturing_order/'.$manufacturing_order_id); ?>"  class="btn btn-info mr-2 "><?php echo _l('manufacturing_order'); ?></a>

							</div>

						</div>
					</div>
				</div>

			</div>
		</div>
		<?php init_tail(); ?>
		<?php 
		require('modules/manufacturing/assets/js/work_orders/view_work_order_js.php');
		?>
	</body>
	</html>
