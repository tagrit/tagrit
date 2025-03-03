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
								<h4 class="h4-color no-margin"><i class="fa fa-align-justify" aria-hidden="true"></i> <?php echo _l('bills_of_materials'); ?></h4>
							</div>
						</div>
						<hr class="hr-color">

						<div class="row">
						<?php if(has_permission('manufacturing', '', 'create')){ ?>
							<div class="col-md-3">
								<div class="_buttons">
									<a href="#" onclick="add_bill_of_material(); return false;" class="btn btn-info mbot10"><?php echo _l('add_bills_of_material'); ?></a>

									<a href="<?php echo admin_url('manufacturing/import_xlsx_contract'); ?>" class=" btn mright5 btn-default pull-left hide">
										<?php echo _l('work_center_import'); ?>
									</a>
								</div>
							</div>
							<br>
						<?php } ?>
						
						</div>

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
									<select name="bom_type_filter[]" id="bom_type_filter" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('bom_type'); ?>">
										<?php foreach($bom_types as $bom_type) { ?>
											<option value="<?php echo new_html_entity_decode($bom_type['name']); ?>"><?php echo new_html_entity_decode($bom_type['label']); ?></option>
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
							
						</div>
						<br>

						<div class="modal bulk_actions" id="bill_of_material_table_bulk_actions" tabindex="-1" role="dialog">
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
											<a href="#" class="btn btn-info" onclick="bom_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

						<?php if (has_permission('manufacturing','','delete')) { ?>
							<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-bill_of_material_table" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
						<?php } ?>


						<?php render_datatable(array(
							'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="bill_of_material_table"><label></label></div>',

							_l('id'),
							_l('product_label'),
							_l('BOM_code'),
							_l('bom_type'),
							_l('product_variant'),
							_l('product_qty'),
							_l('unit_id'),
							_l('routing_label'),
						),'bill_of_material_table',

						array('customizable-table'),
						array(
							'id'=>'table-bill_of_material_table',
							'data-last-order-identifier'=>'bill_of_material_table',
							'data-default-order'=>get_table_last_order('bill_of_material_table'),
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
require('modules/manufacturing/assets/js/bill_of_materials/bill_of_material_manage_js.php');
?>
</body>
</html>
