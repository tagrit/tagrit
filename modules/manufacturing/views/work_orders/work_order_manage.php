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
								<h4 class="h4-color no-margin"><i class="fa fa-address-card" aria-hidden="true"></i> <?php echo _l('work_orders'); ?></h4>
							</div>
						</div>
						<hr class="hr-color">


						<div class="row">
							<div  class="col-md-4 leads-filter-column">
								<div class="form-group">
									<select name="manufacturing_order_filter[]" id="manufacturing_order_filter" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('manufacturing_order'); ?>">
										<?php foreach($manufacturing_orders as $manufacturing_order) { ?>
											<option value="<?php echo new_html_entity_decode($manufacturing_order['id']); ?>"><?php echo new_html_entity_decode($manufacturing_order['manufacturing_order_code']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
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
									<select name="status_filter[]" id="status_filter" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('status'); ?>">
										<?php foreach($status_data as $status) { ?>
											<option value="<?php echo new_html_entity_decode($status['name']); ?>"><?php echo new_html_entity_decode($status['label']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div> 
						</div>
						<br>

						<div class="modal bulk_actions" id="work_order_table_bulk_actions" tabindex="-1" role="dialog">
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
						),'work_order_table',

						array('customizable-table'),
						array(
							'id'=>'table-work_order_table',
							'data-last-order-identifier'=>'work_order_table',
							'data-default-order'=>get_table_last_order('work_order_table'),
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
require('modules/manufacturing/assets/js/work_orders/work_order_manage_js.php');
?>
</body>
</html>
