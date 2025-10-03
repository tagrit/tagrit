
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" >
				<div class="panel_s">
					<div class="panel-body">

						<div class="row">
							<div class="col-md-3">
								<div  class="leads-filter-column">
									<label for="mo_measures"><?php echo _l('measures'); ?></label><br />
									<select name="mo_measures" id="mo_measures" data-live-search="true" class="selectpicker"  data-width="100%" data-none-selected-text="<?php echo _l('status'); ?>">
										<?php foreach($mo_measures_type as $measures) { ?>
											<option value="<?php echo new_html_entity_decode($measures['name']); ?>"><?php echo new_html_entity_decode($measures['label']); ?></option>
										<?php } ?>
									</select>
								</div> 
							</div>

							<div class="col-md-3">
								
								<div class="form-group" id="report-time">
									<label for="mo_months-report"><?php echo _l('period_datepicker'); ?></label><br />
									<select class="selectpicker" name="mo_months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										<option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
										<option value="this_month"><?php echo _l('this_month'); ?></option>
										<option value="1"><?php echo _l('last_month'); ?></option>
										<option value="this_year"><?php echo _l('this_year'); ?></option>
										<option value="last_year"><?php echo _l('last_year'); ?></option>
										<option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
										<option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
										<option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
										<option value="custom"><?php echo _l('period_datepicker'); ?></option>
									</select>
								</div>
							</div>
							<div id="mo_date-range" class="hide ">
								<div class="row">
									<div class="col-md-3">
										<label for="mo_report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" id="mo_report-from" autocomplete="off" name="mo_report-from">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
									<div class="col-md-3">
										<label for="mo_report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" disabled="disabled" autocomplete="off" id="mo_report-to" name="mo_report-to">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>

						<!--report by manufacturing order  -->
						<div class="row">
							<div class="col-md-12">
								<div id="report_by_manufacturing_order">
								</div>
							</div>
						</div>
						<br>
						<br>


						<!--report by work order  -->
						<div class="row">
							<div class="col-md-3">
								<div  class="leads-filter-column">
									<label for="wo_measures"><?php echo _l('measures'); ?></label><br />
									<select name="wo_measures" id="wo_measures" data-live-search="true" class="selectpicker"  data-width="100%" data-none-selected-text="<?php echo _l('status'); ?>">
										<?php foreach($wo_measures_type as $measures) { ?>
											<option value="<?php echo new_html_entity_decode($measures['name']); ?>"><?php echo new_html_entity_decode($measures['label']); ?></option>
										<?php } ?>
									</select>
								</div> 
							</div>

							<div class="col-md-3">
								
								<div class="form-group" id="report-time">
									<label for="wo_months-report"><?php echo _l('period_datepicker'); ?></label><br />
									<select class="selectpicker" name="wo_months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										<option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
										<option value="this_month"><?php echo _l('this_month'); ?></option>
										<option value="1"><?php echo _l('last_month'); ?></option>
										<option value="this_year"><?php echo _l('this_year'); ?></option>
										<option value="last_year"><?php echo _l('last_year'); ?></option>
										<option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
										<option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
										<option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
										<option value="custom"><?php echo _l('period_datepicker'); ?></option>
									</select>
								</div>
							</div>
							<div id="wo_date-range" class="hide ">
								<div class="row">
									<div class="col-md-3">
										<label for="wo_report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" id="wo_report-from" autocomplete="off" name="wo_report-from">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
									<div class="col-md-3">
										<label for="wo_report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" disabled="disabled" autocomplete="off" id="wo_report-to" name="wo_report-to">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>

						<div class="row">
							<div class="col-md-12">
								<div id="report_by_work_order">
								</div>
							</div>
						</div>
						<br>
						<br>

						<h4 class="text-center"><?php echo _l('mrp_work_centers'); ?></h4>
						<div class="row mtop15">
							<?php
							foreach ($work_centers as $key => $work_center) { ?>
								<?php 
								$template_id = 1;
								$status = _l('requested');
								$class = 'draftsts';
								
								?>
								<div class="col-md-4 col-lg-4 col-sm-6 item item-card-hira" data-observation="68">
									<div class="card item-card card-block">
										<div class="card item-card card-block">
											<div class="cardcoliner <?php echo new_html_entity_decode($class); ?>">
												<div class="catimg">

													<div class="cardworkertag">
														<span class="catdesc"><?php echo new_html_entity_decode($work_center['work_center_code']); ?></span><br>
														<span class="pull-left"><?php echo new_html_entity_decode($work_center['work_center_name']); ?></span>
													</div>
													
												</div>

												<div class="cattexts" data-template-id="<?php echo new_html_entity_decode($work_center['id']) ?>"
													data-staff-id="<?php echo new_html_entity_decode($work_center['id']) ?>"
													data-permit-id="<?php echo new_html_entity_decode($work_center['id']) ?>"
													data-worker-id="<?php echo new_html_entity_decode($work_center['id']) ?>">

													<div class="row">
														<div class="col-md-4">
															
															<div class="permitworkerdate">
																<div >
																	<button class="btn btn-sm btn-success"><?php echo _l('work_order_label'); ?></button>
																</div>
															</div>
														</div>

														<div class="col-md-8">
															<div class="col-md-12 panel-padding" >
																<table class="table  table-margintop">
																	<tbody>
																		<tr class="project-overview">
																			<td class="bold" width="70%"><?php echo _l('To Launch'); ?></td>
																			<td class="text-right"><b><?php echo new_html_entity_decode( $work_center['ready']) ?></b></td>
																		</tr>
																		<tr class="project-overview">
																			<td class="bold" width="70%"><?php echo _l('In Progress'); ?></td>
																			<td class="text-right"><b><?php echo new_html_entity_decode( $work_center['in_progress']) ?></b></td>
																		</tr>
																		<tr class="project-overview">
																			<td class="bold" width="70%"><?php echo _l('Late'); ?></td>
																			<td class="text-right"><b><?php echo new_html_entity_decode( $work_center['late']) ?></b></td>
																		</tr>
																		<tr class="project-overview hide">
																			<td class="bold" width="70%"><?php echo _l('OEE'); ?></td>
																			<td class="text-right"><b>1</b></td>
																		</tr>
																		

																	</tbody>
																</table>
															</div>
														</div>
													</div>
													
												</div>

											</div>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<?php require('modules/manufacturing/assets/js/dashboards/dashboard_js.php'); ?>

</body>
</html>
