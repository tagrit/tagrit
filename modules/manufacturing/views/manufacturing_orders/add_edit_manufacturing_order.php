<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php 
			$id = '';
			$title = '';
			if(isset($manufacturing_order)){
				$title .= _l('update_manufacturing_order_lable');
				$id    = $manufacturing_order->id;
			}else{
				$title .= _l('add_manufacturing_order_lable');
			}

			?>

			<?php echo form_open_multipart(admin_url('manufacturing/add_edit_manufacturing_order/'.$id), array('id' => 'add_update_manufacturing_order','autocomplete'=>'off')); ?>

			<div class="col-md-12" >
				<div class="panel_s">
					
					<div class="panel-body">
						<div class="row mb-5">
							<div class="col-md-5">
								<h4 class="no-margin"><?php echo new_html_entity_decode($title); ?> 
							</div>
						</div>
						<hr class="hr-color no-margin">

						<!-- start tab -->
						<div class="modal-body">
							<div class="tab-content">
								<!-- start general infor -->
								<?php 

								$product_id = isset($manufacturing_order) ? $manufacturing_order->product_id : '';
								$product_qty = isset($manufacturing_order) ? $manufacturing_order->product_qty : 1;
								$unit_id = isset($manufacturing_order) ? $manufacturing_order->unit_id : '';
								$manufacturing_order_code = isset($manufacturing_order) ? $manufacturing_order->manufacturing_order_code : $mo_code;
								$staff_id = isset($manufacturing_order) ? $manufacturing_order->staff_id : '';
								$bom_id = isset($manufacturing_order) ? $manufacturing_order->bom_id : '';
								$routing_id = isset($manufacturing_order) ? $manufacturing_order->routing_id : '';
								$components_warehouse_id = isset($manufacturing_order) ? $manufacturing_order->components_warehouse_id : '';
								$finished_products_warehouse_id = isset($manufacturing_order) ? $manufacturing_order->finished_products_warehouse_id : '';
								$date_deadline = isset($manufacturing_order) ? _dt($manufacturing_order->date_deadline) : '';
								$date_plan_from = isset($manufacturing_order) ? _dt($manufacturing_order->date_plan_from) : '';
								$routing_id_view = isset($manufacturing_order) ? mrp_get_routing_name($manufacturing_order->routing_id) : '';
								$routing_id = isset($manufacturing_order) ? ($manufacturing_order->routing_id) : '';

								$disabled_edit=[];
								if(isset($manufacturing_order) && $manufacturing_order->status != 'draft'){
									$disabled_edit = ['disabled' => true];
								}

								?>
								<div class="row">
									<div class="row">
										<div class="col-md-6"> 
											<?php echo render_select('product_id',$products,array('id','description'),'product_label', $product_id, $disabled_edit); ?>
										</div>
										<div class="col-md-6"> 
											<?php echo render_datetime_input('date_deadline','date_deadline', $date_deadline); ?>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<?php echo render_input('product_qty','product_qty', $product_qty,'number', $disabled_edit); ?> 
										</div>
										<div class="col-md-6"> 
											<?php echo render_datetime_input('date_plan_from','date_plan_from', $date_plan_from); ?>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<?php echo render_select('unit_id',$units,array('unit_type_id', 'unit_name'), 'unit_of_measure', $unit_id, $disabled_edit, [], '', '' , false); ?>
										</div>
										<div class="col-md-6">
											<?php echo render_select('staff_id',$staffs,array('staffid', array('firstname', 'lastname')), 'responsible', $staff_id,[], [], '', '' , false); ?>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<?php echo render_select('bom_id',$bill_of_materials,array('id', 'description'), 'bill_of_material_label', $bom_id, $disabled_edit, [], '', '' , false); ?>
										</div>
										<div class="col-md-6">
											<?php echo render_input('manufacturing_order_code', 'reference_code', $manufacturing_order_code, '', $disabled_edit); ?>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<?php echo render_input('routing_id_view', 'routing_label', $routing_id_view, '', ['disabled' => true]); ?>
											<input type="hidden" name="routing_id" value="<?php echo new_html_entity_decode($routing_id) ?>">
										</div>

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
												<div class="col-md-12">
													<?php echo render_select('components_warehouse_id', $warehouses,array('warehouse_id', 'warehouse_name'), 'components_warehouse', $components_warehouse_id,['data-none-selected-text' => _l('mrp_all')], [], '', '' , true); ?>
												</div>
												<div class="col-md-12">
													<?php echo render_select('finished_products_warehouse_id', $warehouses,array('warehouse_id', 'warehouse_name'), 'finished_products_warehouse', $finished_products_warehouse_id,[], [], '', '' , false); ?>
												</div>
												
											</div>
										</div>
										
									</div>
								</div>

							</div>

							<div class="modal-footer">
								<a href="<?php echo admin_url('manufacturing/manufacturing_order_manage'); ?>"  class="btn btn-default mr-2 "><?php echo _l('close'); ?></a>
								<?php if(has_permission('manufacturing', '', 'create') || has_permission('manufacturing', '', 'edit')){ ?>
									<button type="button" class="btn btn-info pull-right add_manufacturing_order"><?php echo _l('submit'); ?></button>

								<?php } ?>
							</div>

						</div>
					</div>
				</div>

				<?php echo form_close(); ?>
			</div>
		</div>
		<?php init_tail(); ?>
		<?php 
		require('modules/manufacturing/assets/js/manufacturing_orders/add_edit_manufacturing_order_js.php');
		?>
	</body>
	</html>
