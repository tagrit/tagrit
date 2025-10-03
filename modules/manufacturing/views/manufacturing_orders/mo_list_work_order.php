<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s"> 
					<div class="panel-body">

						<div class="row">
							<div class="col-md-10">
								<h4 class="h4-color no-margin"><i class="fa fa-address-card" aria-hidden="true"></i> <?php echo _l('work_orders').' / '.mrp_get_manufacturing_code($mo_id); ?></h4>
							</div>
							<div class="col-md-2">
								<a href="#" class="btn btn-default hidden-xs toggle-articles-list pull-right" onclick="change_work_order_view(); return false;">
									<i class="fa fa-th-list article_change_icon"></i>
								</a>
							</div>
						</div>
						<hr class="hr-color no-margin">


						<div class="row hide">
							<div  class="col-md-3 leads-filter-column pull-right">
								<select name="status_filter[]" id="status_filter" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('status'); ?>">
									<?php foreach($status_data as $status) { ?>
										<option value="<?php echo new_html_entity_decode($status['name']); ?>"><?php echo new_html_entity_decode($status['label']); ?></option>
									<?php } ?>
								</select>
							</div> 

							<div  class="col-md-3 leads-filter-column pull-right">
								<select name="routing_filter[]" id="routing_filter" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('routing_label'); ?>">
									<?php foreach($routings as $routing) { ?>
										<option value="<?php echo new_html_entity_decode($routing['id']); ?>"><?php echo new_html_entity_decode($routing['routing_name']); ?></option>
									<?php } ?>
								</select>
							</div> 
							
							<div  class="col-md-3 leads-filter-column pull-right">
								<select name="products_filter[]" id="products_filter" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('product_label'); ?>">
									<?php foreach($products as $product) { ?>
										<option value="<?php echo new_html_entity_decode($product['id']); ?>"><?php echo new_html_entity_decode($product['description']); ?></option>
									<?php } ?>
								</select>
							</div>
							
							<div  class="col-md-3 leads-filter-column pull-right">
								<select name="manufacturing_order_filter[]" id="manufacturing_order_filter" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('manufacturing_order'); ?>">
									<?php foreach($manufacturing_orders as $manufacturing_order) { ?>
										<option value="<?php echo new_html_entity_decode($manufacturing_order['id']); ?>"><?php echo new_html_entity_decode($manufacturing_order['manufacturing_order_code']); ?></option>
									<?php } ?>
								</select>
							</div>


							
						</div>
						<br>

						<div class="col-md-12 tab-content">
							<div role="tabpanel" class="tab-pane kb-kan-ban kan-ban-tab active" id="kan-ban">

								<div class="mx-auto mt-3 btn-group fc" role="group">
									<button type="button" class=" button-text-transform fc-quarter-day-button btn btn-sm btn-default active"><?php echo _l('quarter_day') ?></button>
									<button type="button" class="button-text-transform fc-half-day-button btn btn-sm btn-default"><?php echo _l('half_day') ?></button>
									<button type="button" class="button-text-transform fc-day-button btn btn-sm btn-default"><?php echo _l('mrp_day') ?></button>
									<button type="button" class="button-text-transform fc-week-button btn btn-sm btn-default"><?php echo _l('mrp_week') ?></button>
									<button type="button" class="button-text-transform fc-month-button btn btn-sm btn-default"><?php echo _l('mrp_month') ?></button>
								</div>
								<br>
								<br>
								<svg id="timeline"></svg>
							</div>

							<div role="tabpanel" class="tab-pane " id="list_tab">
								<div class="modal bulk_actions" id="mo_work_order_table_bulk_actions" tabindex="-1" role="dialog">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title"><?php echo _l('hr_bulk_actions'); ?></h4>
											</div>
											<div class="modal-body">
												<?php if(has_permission('manufacturing','','delete') || is_admin()){ ?>
													<div class="checkbox checkbox-danger">
														<input type="checkbox" name="mass_delete" id="mass_delete">
														<label for="mass_delete"><?php echo _l('hr_mass_delete'); ?></label>
													</div>
												<?php } ?>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>

												<?php if(has_permission('manufacturing','','delete') || is_admin()){ ?>
													<a href="#" class="btn btn-info" onclick="staff_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>



								<?php render_datatable(array(

									_l('id'),
									_l('work_order_label'),
									_l('scheduled_date_start'),
									_l('work_center_label'),
									_l('manufacturing_order'),
									_l('product_label'),
									_l('product_qty'),
									_l('unit_id'),
									_l('status'),
								),'mo_work_order_table',

								array('customizable-table'),
								array(
									'id'=>'table-mo_work_order_table',
									'data-last-order-identifier'=>'mo_work_order_table',
									'data-default-order'=>get_table_last_order('mo_work_order_table'),
								)); ?>
							</div>

						</div>

					</div>
				</div>

				<div id="modal_wrapper"></div>

			</div>
		</div>
	</div>
	<?php echo form_hidden('manufacturing_order_id',$mo_id); ?>

	<?php init_tail(); ?>
	<?php 
	require('modules/manufacturing/assets/js/manufacturing_orders/mo_list_work_order_js.php');
	?>
</body>
</html>
