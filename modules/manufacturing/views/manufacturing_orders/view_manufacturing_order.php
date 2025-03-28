<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php 
			$id = '';
			$title = '';
			$title .= _l('view_manufacturing_order_lable');

			?>

			<div class="col-md-12" >
				<div class="panel_s">
					
					<div class="panel-body">
						<!-- action related work order -->
						<div class="row">
							<div class="col-md-12">
								<?php if(has_permission('manufacturing', '', 'create') || has_permission('manufacturing', '', 'edit') ){ ?>
									<?php 
									$check_availability_status = true;
									 ?>
									<?php if($check_availability && $manufacturing_order->status != 'draft'){ ?>
										<button type="button" class="label-planned btn btn-success pull-left mark_check_availability mright5"><?php echo _l('mark_as_check_availability'); ?></button>
										<?php 
										$check_availability_status = false;
										 ?>
									<?php } ?>

									<?php if($manufacturing_order->status == 'draft'){ ?>
										<button type="button" class="label-confirmed  btn btn-info pull-left mark_as_todo mright5"><?php echo _l('mark_as_todo'); ?></button>
									<?php } ?>
										
									<?php if($manufacturing_order->status == 'confirmed' && $check_planned){ ?>
										<button type="button" class="label-planned btn btn-success pull-left mark_as_planned mright5"><?php echo _l('mark_as_planned'); ?></button>
									<?php } ?>

									<?php if($manufacturing_order->status == 'confirmed'){ ?>
										<button type="button" class="label-warning btn btn-success pull-left mark_as_unreserved mright5"><?php echo _l('mark_as_unreserved'); ?></button>
									<?php } ?>
										
									<?php if($check_mark_done && $manufacturing_order->status == 'in_progress' && $check_availability_status ){ ?>
										<button type="button" class="btn btn-success pull-left mark_as_done mright5"><?php echo _l('mark_as_done'); ?></button>
									<?php } ?>

									<?php if(($check_create_purchase_request && $manufacturing_order->status != 'draft') || (!$pur_order_exist) ){ ?>
										<button type="button" class="btn btn-success pull-left mo_create_purchase_request mright5" data-toggle="tooltip" title="" data-original-title="<?php echo _l('create_purchase_request_title'); ?>"><?php echo _l('mo_create_purchase_request'); ?> <i class="fa fa-question-circle i_tooltip" ></i></button>
									<?php } ?>
									
									<?php if($manufacturing_order->status != 'cancelled' && $manufacturing_order->status != 'done'){ ?>
										<button type="button" class="btn btn-default pull-left mark_as_cancel mright5"><?php echo _l('mrp_cancel'); ?></button>
									<?php } ?>

									<?php if($manufacturing_order->status == 'planned' || $manufacturing_order->status == 'in_progress' || $manufacturing_order->status == 'done' ){ ?>
										
										<a href="<?php echo admin_url('manufacturing/mo_work_order_manage/'.$manufacturing_order->id); ?>" class="btn btn-warning pull-right display-block mright5"><i class="fa fa-play-circle-o"></i> <?php echo _l('mrp_work_orders'); ?></a>

									<?php } ?>


									<?php } ?>
							</div>
						</div>
						<br>
						<!-- action related work order -->

						<div class="row mb-5">
							<div class="col-md-5">
								<h4 class="no-margin"><?php echo new_html_entity_decode($manufacturing_order->manufacturing_order_code); ?> 
							</div>
						</div>
						<hr class="hr-color no-margin">

						<!-- start tab -->
						<div class="modal-body">
							<div class="tab-content">
								<!-- start general infor -->
								<?php 

								$id = isset($manufacturing_order) ? $manufacturing_order->id : '';
								$product_id = isset($manufacturing_order) ? $manufacturing_order->product_id : '';
								$product_qty = isset($manufacturing_order) ? $manufacturing_order->product_qty : 1;
								$unit_id = isset($manufacturing_order) ? $manufacturing_order->unit_id : '';
								$manufacturing_order_code = isset($manufacturing_order) ? $manufacturing_order->manufacturing_order_code : '';
								$staff_id = isset($manufacturing_order) ? $manufacturing_order->staff_id : '';
								$bom_id = isset($manufacturing_order) ? $manufacturing_order->bom_id : '';
								$routing_id = isset($manufacturing_order) ? $manufacturing_order->routing_id : '';
								$components_warehouse_id = isset($manufacturing_order) ? $manufacturing_order->components_warehouse_id : '';
								$finished_products_warehouse_id = isset($manufacturing_order) ? $manufacturing_order->finished_products_warehouse_id : '';
								$date_deadline = isset($manufacturing_order) ? _dt($manufacturing_order->date_deadline) : '';
								$date_plan_from = isset($manufacturing_order) ? _dt($manufacturing_order->date_plan_from) : '';
								$routing_id_view = isset($manufacturing_order) ? mrp_get_routing_name($manufacturing_order->routing_id) : '';
								$routing_id = isset($manufacturing_order) ? ($manufacturing_order->routing_id) : '';
								$status = isset($manufacturing_order) ? ($manufacturing_order->status) : '';
								$reference_purchase_request = isset($manufacturing_order) ? ($manufacturing_order->purchase_request_id) : '';

								$components_warehouse_name='';
								$finished_products_warehouse_name= mrp_get_warehouse_name($finished_products_warehouse_id);
								if($components_warehouse_id != ''){
									$components_warehouse_name .= mrp_get_warehouse_name($components_warehouse_id);
								}else{
									$components_warehouse_name .= _l('mrp_all');
								}

								$date_planned_start = '';
								if(isset($manufacturing_order) && $manufacturing_order->date_planned_start != null && $manufacturing_order->date_planned_start != ''){

									$date_planned_start = _dt($manufacturing_order->date_planned_start).' '._l('mrp_to').' '. _dt($manufacturing_order->date_planned_finished);
								};
								?>
								<div class="row">
									<div class="col-md-6 panel-padding" >
										<input type="hidden" name="id" value="<?php echo new_html_entity_decode($id) ?>">

										<table class="table border table-striped table-margintop" >
											<tbody>
												<tr class="project-overview">
													<td class="bold td-width"><?php echo _l('product_label'); ?></td>
													<td><?php echo mrp_get_product_name($product_id) ; ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('unit_of_measure'); ?></td>
													<td><?php echo mrp_get_unit_name($unit_id) ; ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('product_qty'); ?></td>
													<td><?php echo new_html_entity_decode($product_qty)  ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('bill_of_material_label'); ?></td>
													<td><?php echo mrp_get_product_name(mrp_get_bill_of_material($bom_id))  ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('routing_label'); ?></td>
													<td><?php echo mrp_get_routing_name($routing_id)  ?></td>
												</tr>
												

											</tbody>
										</table>
									</div>

									<div class="col-md-6 panel-padding" >
										<table class="table table-striped table-margintop">
											<tbody>
												<tr class="project-overview">
													<td class="bold" width="40%"><?php echo _l('date_deadline'); ?></td>
													<td><?php echo new_html_entity_decode($date_deadline)  ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('date_plan_from'); ?></td>
													<td><?php echo new_html_entity_decode($date_plan_from)  ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('planned_date'); ?></td>
													<td><?php echo new_html_entity_decode($date_planned_start)  ?></td>
												</tr>
												

												<tr class="project-overview">
													<td class="bold"><?php echo _l('responsible'); ?></td>
													<td><?php echo new_html_entity_decode(get_staff_full_name($staff_id))  ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('status'); ?></td>
													<td><span class="label label-<?php echo  new_html_entity_decode($status) ?>" ><?php echo _l($status); ?></span></td>
												</tr>

												<?php if($reference_purchase_request != ''){ ?>
													<tr class="project-overview">
														<td class="bold"><?php echo _l('reference_purchase_request'); ?></td>
														<td><a href="<?php echo admin_url('purchase/view_pur_request/'.$reference_purchase_request) ?>" ><?php echo mrp_purchase_request_code($reference_purchase_request) ?></a></td>
													</tr>
												<?php } ?>
												 

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
												<li role="presentation" class="active">
													<a href="#component_tab" aria-controls="component_tab" role="tab" data-toggle="tab">
														<span class="glyphicon glyphicon-align-justify"></span>&nbsp;<?php echo _l('tab_component_tab'); ?>
													</a>
												</li>
												<li role="presentation" class="">
													<a href="#finished_product_tab" aria-controls="finished_product_tab" role="tab" data-toggle="tab">
														<span class="fa fa-cogs menu-icon"></span>&nbsp;<?php echo _l('finished_product_tab'); ?>
													</a>
												</li>
												<li role="presentation" class="">
													<a href="#miscellaneous_tab" aria-controls="miscellaneous_tab" role="tab" data-toggle="tab">
														<span class="fa fa-balance-scale menu-icon"></span>&nbsp;<?php echo _l('miscellaneous_tab'); ?>
													</a>
												</li>
												<li role="presentation" class="">
													<a href="#costing" aria-controls="costing" role="tab" data-toggle="tab">
														<span class="fa-solid fa-coins"></span>&nbsp;<?php echo _l('costing'); ?>
													</a>
												</li>
												<li role="presentation" class="">
													<a href="#bom_changes_logs_tab" aria-controls="bom_changes_logs_tab" role="tab" data-toggle="tab">
														<span class="fa-regular fa-clock"></span>&nbsp;<?php echo _l('mrp_bom_changes_logs'); ?>
													</a>
												</li>

											</ul>
										</div>
									</div>
									<br>


									<div class="tab-content active">
										<div role="tabpanel" class="tab-pane active" id="component_tab">
											<div class="form"> 
												<div id="product_tab_hs" class="product_tab handsontable htColumnHeaders">
												</div>
												<?php echo form_hidden('product_tab_hs'); ?>
											</div>

										</div>
										<div role="tabpanel" class="tab-pane " id="finished_product_tab">
											<?php echo _l('Use_the_Produce_button_or_process_the_work_orders_to_create_some_finished_products'); ?>
										</div>
										<div role="tabpanel" class="tab-pane " id="miscellaneous_tab">
											<div class="row">
												<div class="col-md-6 panel-padding" >
													<table class="table table-striped table-margintop">
														<tbody>
															<tr class="project-overview">
																<td class="bold" width="40%"><?php echo _l('components_warehouse'); ?></td>
																<td><?php echo new_html_entity_decode($components_warehouse_name)  ?></td>
															</tr>
															<tr class="project-overview">
																<td class="bold"><?php echo _l('finished_products_warehouse'); ?></td>
																<td><?php echo new_html_entity_decode($finished_products_warehouse_name)  ?></td>
															</tr>

														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane" id="costing">
											<div class="row">
												<div class="col-md-6 panel-padding" >
													<table class="table table-striped table-margintop">
														<tbody>
															<tr class="project-overview">
																<td class="bold" width="40%"><?php echo _l('total_material_cost'); ?></td>
																<td><?php echo app_format_money($manufacturing_order_costing['total_material_cost'], $currency->name)  ?></td>
															</tr>
															<tr class="project-overview">
																<td class="bold"><?php echo _l('total_labour_cost'); ?></td>
																<td>
																	<?php echo app_format_money($manufacturing_order_costing['total_labour_cost'], $currency->name)  ?>
																	<br>
																</td>
															</tr>
															<tr class="project-overview">
																<td class="" width="40%">    +   <?php echo _l('total_work_center_cost'); ?></td>
																<td><?php echo app_format_money($manufacturing_order_costing['total_work_center_cost'], $currency->name)  ?></td>
															</tr>
															<tr class="project-overview">
																<td class="" width="40%">    +   <?php echo _l('total_employee_working_cost'); ?></td>
																<td><?php echo app_format_money($manufacturing_order_costing['total_employee_working_cost'], $currency->name)  ?></td>
															</tr>

														</tbody>
													</table>
												</div>
											</div>
										</div>

										<div role="tabpanel" class="tab-pane" id="bom_changes_logs_tab">
											<?php if(has_permission('manufacturing', '', 'create')){ ?>
												<div class="_buttons hide">
													<a href="#" onclick="add_component(<?php echo new_html_entity_decode($manufacturing_order->id) ?>,0, 'add'); return false;" class="btn btn-info mbot10 pull-right"><?php echo _l('mrp_add_change_log_manual'); ?></a>
												</div>
											<?php } ?>

											<?php render_datatable(array(

												_l('id'),
												_l('component'),
												// _l('mrp_parent'),
												_l('mrp_change_type'),
												_l('mrp_change_quantity'),
												_l('mrp_date_and_time'),
												_l('mrp_user'),
												_l('description'),
												_l('mrp_related'),
											),'bom_change_log_table',
											array('customizable-table'),
											array(
												'id'=>'table-bom_change_log_table',
												'data-last-order-identifier'=>'bom_change_log_table',
												'data-default-order'=>get_table_last_order('bom_change_log_table'),
											)); ?>

										</div>
									</div>
								</div>

							</div>

							<div class="modal-footer">
								<a href="<?php echo admin_url('manufacturing/manufacturing_order_manage'); ?>"  class="btn btn-default mr-2 "><?php echo _l('close'); ?></a>

									<?php if(has_permission('manufacturing', '', 'create') ){ ?>
										<a href="<?php echo admin_url('manufacturing/add_edit_manufacturing_order'); ?>" class="btn btn-info pull-right display-block mright5"><?php echo _l('add_manufacturing_order'); ?></a>
									<?php } ?>

									<?php if( has_permission('manufacturing', '', 'edit')){ ?>
										<a href="<?php echo admin_url('manufacturing/add_edit_manufacturing_order/'.$manufacturing_order->id); ?>" class="btn btn-primary pull-right display-block mright5"><?php echo _l('edit_manufacturing'); ?></a>
									<?php } ?>

							</div>

						</div>
					</div>
				</div>

			</div>
		</div>

		<div class="modal fade" id="show_detail" tabindex="-1" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">
							<span class="add-title"><?php echo _l('quantity_produced'); ?></span>
						</h4>
					</div>
					<div class="modal-body">
						<div class="row">

							<div class="col-md-12">
								<label class="text-danger "><?php echo _l('If_the_actual_quantity_of_products_produced_note'); ?></label>
								<?php echo render_input('change_product_qty', 'quantity_produced', $product_qty, 'number', ['min' => 0, 'step' => 'any']); ?>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary btn_mark_as_done" ><?php echo _l('mark_as_done'); ?></button>
					</div>
				</div>
			</div>
		</div>

		<?php echo form_hidden('manufacturing_order_id',$manufacturing_order->id); ?>
		<?php init_tail(); ?>
		<?php 
		require('modules/manufacturing/assets/js/manufacturing_orders/view_manufacturing_order_js.php');
		?>
	</body>
	</html>
