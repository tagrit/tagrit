<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">

			<div class="col-md-5">
				<div class="row">
					<div class="panel_s">
						<?php 

						$bill_of_material_id = isset($bill_of_material) ? $bill_of_material->id : '';
						$product_id = isset($bill_of_material) ? $bill_of_material->product_id : '';
						$product_variant_id = isset($bill_of_material) ? $bill_of_material->product_variant_id : '';
						$product_qty = isset($bill_of_material) ? $bill_of_material->product_qty : '';
						$unit_id = isset($bill_of_material) ? $bill_of_material->unit_id : '';
						$routing_id = isset($bill_of_material) ? $bill_of_material->routing_id : '';
						$bom_code = isset($bill_of_material) ? $bill_of_material->bom_code : '';

						$bom_type = isset($bill_of_material) ? $bill_of_material->bom_type : '';

						$manufacture_this_product_checked='';
						$kit_checked='';
						$kit_hide ='hide';

						if($bom_type == 'manufacture_this_product'){
							$manufacture_this_product_checked = 'checked';
							$kit_hide ='hide';

						}else{
							$kit_checked = 'checked';
							$kit_hide ='';

						}

						$ready_to_produce = isset($bill_of_material) ? $bill_of_material->ready_to_produce : '';
						$consumption = isset($bill_of_material) ? $bill_of_material->consumption : '';

						$product_variant_name='';
						if($product_variant_id != '' && $product_variant_id != 0){
							$product_variant_name = '( '.mrp_get_product_name($product_variant_id).' )';
						}
						?>
						<?php echo form_open(admin_url('manufacturing/add_bill_of_material_modal/'.$bill_of_material_id), array('id' => 'add_bill_of_material', 'autocomplete'=>'off')); ?>

						<div class="panel-body">
							<h4 class="no-margin">
								<?php echo new_html_entity_decode(mrp_get_product_name($product_id) .$product_variant_name); ?>
							</h4>
							<hr class="hr-panel-heading" />

							<div class="row">
								<div class="col-md-12">
									<?php echo render_input('bom_code','BOM_code', $bom_code,'text'); ?> 
								</div>

								<div class="col-md-12">
									<?php echo render_select('product_id',$parent_product,array('id',array('description')),'product_label', $product_id); ?>
								</div>
								<div class="col-md-12">
									<?php echo render_select('product_variant_id',$product_variant,array('id','description'),'product_variant', $product_variant_id); ?>

								</div>

								<div class="col-md-6">
									<?php echo render_input('product_qty','product_qty', $product_qty ,'number'); ?> 
								</div>

								<div class="col-md-6">
									<?php echo render_select('unit_id',$units,array('unit_type_id', 'unit_name'), 'unit_of_measure', $unit_id,[], [], '', '' , false); ?>
								</div>

								<div class="col-md-6">
									<?php echo render_select('routing_id',$routings,array('id', 'routing_name'), 'routing_label', $routing_id,[], [], '', '' , true); ?>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="profit_rate" class="control-label clearfix"><?php echo _l('bom_type'); ?></label>
										<div class="radio radio-primary radio-inline" >
											<input type="radio" id="manufacture_this_product" name="bom_type" value="manufacture_this_product" <?php echo new_html_entity_decode($manufacture_this_product_checked ) ?>>
											<label for="manufacture_this_product"><?php echo _l('manufacture_this_product'); ?></label>

										</div>
										<br>
										<div class="radio radio-primary radio-inline" >
											<input type="radio" id="kit" name="bom_type" value="kit" <?php echo new_html_entity_decode($kit_checked ) ?>>
											<label for="kit"><?php echo _l('kit'); ?></label>

										</div>
										<div class="kit_hide <?php echo new_html_entity_decode($kit_hide); ?>">
											<?php echo _l('A_BoM_of_type_kit_is_used_to_split_the_product_into_its_components'); ?><br>
											<?php echo _l('At_the_creation_of_a_Manufacturing_Order'); ?><br>
											<?php echo _l('At_the_creation_of_a_Stock_Transfer'); ?><br>
										</div>
									</div>
								</div>

								<h4><?php echo _l('miscellaneous') ?></h4>

								<div class="col-md-12">
									<?php echo render_select('ready_to_produce',$ready_to_produce_type,array('name', 'label'), 'ready_to_produce', $ready_to_produce,[], [], '', '' , false); ?>
								</div>
								<div class="col-md-12">
									<?php echo render_select('consumption',$consumption_type,array('name', 'label'), 'consumption', $consumption,[], [], '', '' , false); ?>
								</div>

							</div>

							<hr />
							<button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
							<a href="<?php echo admin_url('manufacturing/bill_of_material_manage'); ?>"  class="btn btn-default pull-right mright5 "><?php echo _l('close'); ?></a>
						</div>
						<?php echo form_close(); ?>
					</div>

				</div>
			</div>

			<div class="col-md-7">
				<div class="row">

					<div class="panel_s"> 
						<div class="panel-body">

							<div class="row">
								<div class="col-md-12">
									<h4 class="h4-color no-margin"><i class="fa fa-shirtsinbulk" aria-hidden="true"></i> <?php echo _l('component'); ?></h4>
								</div>
							</div>
							<hr class="hr-color">

							<?php if(has_permission('manufacturing', '', 'create')){ ?>
								<div class="_buttons">
									<a href="#" onclick="add_component(<?php echo new_html_entity_decode($bill_of_material_id) ?>,0, <?php echo new_html_entity_decode($product_id) ?>, <?php echo new_html_entity_decode($routing_id) ?>,'add'); return false;" class="btn btn-info mbot10"><?php echo _l('add_component'); ?></a>

									<a href="<?php echo admin_url('manufacturing/import_xlsx_contract'); ?>" class=" btn mright5 btn-default pull-left hide">
										<?php echo _l('work_center_import'); ?>
									</a>
								</div>
								<br>
							<?php } ?>

							<div class="modal bulk_actions" id="bill_of_material_detail_table_bulk_actions" tabindex="-1" role="dialog">
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
											<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

											<?php if(has_permission('manufacturing','','delete') || is_admin()){ ?>
												<a href="#" class="btn btn-info" onclick="staff_delete_bulk_action(this); return false;"><?php echo _l('submit'); ?></a>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>

							<?php if (has_permission('manufacturing','','delete')) { ?>
								<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-bill_of_material_detail_table" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
							<?php } ?>

							<?php render_datatable(array(
								'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="bill_of_material_detail_table"><label></label></div>',
								_l('id'),
								_l('display_order'),
								_l('component'),
								_l('product_qty'),
								_l('unit_id'),
								_l('apply_on_variants'),
								_l('consumed_in_operation'),
								
							),'bill_of_material_detail_table',
							array('customizable-table'),
							array(
								'id'=>'table-bill_of_material_detail_table',
								'data-last-order-identifier'=>'bill_of_material_detail_table',
								'data-default-order'=>get_table_last_order('bill_of_material_detail_table'),
							)); ?>
						</div>

					</div>
				</div>
				<div id="modal_wrapper"></div>
			</div>


		</div>
	</div>
</div>
<div id="contract_file_data"></div>

<?php echo form_hidden('bill_of_material_id',$bill_of_material_id); ?>
<?php echo form_hidden('bill_of_material_product_id',$product_id); ?>
<?php echo form_hidden('bill_of_material_routing_id',$routing_id); ?>
<?php init_tail(); ?>
<?php 
require('modules/manufacturing/assets/js/bill_of_materials/add_edit_bill_of_material_js.php');
require('modules/manufacturing/assets/js/bill_of_materials/bill_of_material_details/bill_of_material_detail_manage_js.php');

?>
</body>
</html>
