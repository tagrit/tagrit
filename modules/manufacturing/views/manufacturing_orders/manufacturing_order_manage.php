<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s"> 
					<div class="panel-body">

						<div class="row">
							<div class="col-md-12">
								<h4 class="h4-color no-margin"><i class="fa-brands fa-first-order" aria-hidden="true"></i> <?php echo _l('manufacturing_orders'); ?></h4>
							</div>
						</div>
						<hr class="hr-color">

						<?php if(has_permission('manufacturing', '', 'create')){ ?>
							<div class="row">
								<div  class="col-md-4 leads-filter-column">
									<div class="_buttons">
										<a href="<?php echo admin_url('manufacturing/add_edit_manufacturing_order'); ?>" class="btn btn-info pull-left display-block mright5"><?php echo _l('add_manufacturing_order'); ?></a>

										<a href="<?php echo admin_url('manufacturing/import_xlsx_contract'); ?>" class=" btn mright5 btn-default pull-left hide">
											<?php echo _l('work_center_import'); ?>
										</a>
									</div>
								</div>
							</div>
							<br>
						<?php } ?>

						<div class="row">
							<div  class="col-md-4 leads-filter-column">
								<div class="form-group">
									<select name="products_filter[]" id="products_filter" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('product_label'); ?>">
										<?php foreach($products as $product) { ?>
											<option value="<?php echo new_html_entity_decode($product['id']); ?>"><?php echo new_html_entity_decode($product['description']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div> 
							<div  class="col-md-4 leads-filter-column">
								<div class="form-group">
									<select name="routing_filter[]" id="routing_filter" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('routing_label'); ?>">
										<?php foreach($routings as $routing) { ?>
											<option value="<?php echo new_html_entity_decode($routing['id']); ?>"><?php echo new_html_entity_decode($routing['routing_name']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div> 
							<div  class="col-md-4 leads-filter-column">
								<div class="form-group">
									<select name="status_filter[]" id="status_filter" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('status'); ?>">
										<?php foreach($status_data as $status) { ?>
											<option value="<?php echo new_html_entity_decode($status['name']); ?>"><?php echo new_html_entity_decode($status['label']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div> 
						</div>
						<br>

						<div class="modal bulk_actions" id="manufacturing_order_table_bulk_actions" tabindex="-1" role="dialog">
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
											<a href="#" class="btn btn-info" onclick="mo_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

						<?php if (has_permission('manufacturing','','delete')) { ?>
							<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-manufacturing_order_table" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
						<?php } ?>


						<?php render_datatable(array(
							'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="manufacturing_order_table"><label></label></div>',

							_l('id'),
							_l('manufacturing_order_code'),
							_l('product_label'),
							_l('bill_of_material_label'),
							_l('product_qty'),
							_l('unit_id'),
							_l('routing_label'),
							_l('status'),
						),'manufacturing_order_table',

						array('customizable-table'),
						array(
							'id'=>'table-manufacturing_order_table',
							'data-last-order-identifier'=>'manufacturing_order_table',
							'data-default-order'=>get_table_last_order('manufacturing_order_table'),
						)); ?>
					</div>

				</div>
			</div>

<div id="modal_wrapper"></div>

		</div>
	</div>
</div>
<?php init_tail(); ?>
<?php 
require('modules/manufacturing/assets/js/manufacturing_orders/manufacturing_order_manage_js.php');
?>
</body>
</html>
