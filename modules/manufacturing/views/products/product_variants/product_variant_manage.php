<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<h4 class="no-margin h4-color"><i class="fa fa-edit" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
							</div>
						</div>
						<hr class="hr-color">

						<?php 
							$type ='product_variant';
						 ?>
						<div class="row ">
							<div class=" col-md-3 ">
								<div class="form-group">
									<select name="item_filter[]" id="item_filter" class="selectpicker" multiple="true" data-actions-box="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('product_label'); ?>">

										<?php foreach($product_variants as $product_variant) { ?>
											<option value="<?php echo new_html_entity_decode($product_variant['id']); ?>"><?php echo new_html_entity_decode($product_variant['description']); ?></option>
										<?php } ?>

									</select>
								</div>
							</div>
							<div class=" col-md-3 ">
								<div class="form-group">
									<select name="product_type_filter[]" id="product_type_filter" class="selectpicker" multiple="true" data-actions-box="true"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('product_type'); ?>">

										<?php foreach($product_types as $product_type) { ?>
											<option value="<?php echo new_html_entity_decode($product_type['name']); ?>"><?php echo new_html_entity_decode($product_type['label']); ?></option>
										<?php } ?>

									</select>
								</div>
							</div>
							<div class=" col-md-3">
								<div class="form-group">
									<select name="product_category_filter[]" id="product_category_filter" class="selectpicker" multiple="true" data-actions-box="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('product_category'); ?>">

										<?php foreach($product_categories as $product_categorie) { ?>
											<option value="<?php echo new_html_entity_decode($product_categorie['id']); ?>"><?php echo new_html_entity_decode($product_categorie['name']); ?></option>
										<?php } ?>

									</select>
								</div>
							</div>

							<?php 
							$can_be_type = [];
							$can_be_type[] = [
								'id' => 'can_be_sold',
								'label' => _l('can_be_sold'),
							];
							$can_be_type[] = [
								'id' => 'can_be_purchased',
								'label' => _l('can_be_purchased'),
							];
							$can_be_type[] = [
								'id' => 'can_be_manufacturing',
								'label' => _l('can_be_manufacturing'),
							];
							$can_be_type[] = [
								'id' => 'can_be_inventory',
								'label' => _l('can_be_inventory'),
							];
							

							?>
							<div class="col-md-3">
								<?php echo render_select('can_be_value_filter[]', $can_be_type, array('id', array('label')), '', ['can_be_manufacturing'], ['multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker'], array(), '', '', false); ?>
							</div>
							
						</div>
						<br>

						<div class="row">
							<div class="col-md-12">
								<!-- view/manage -->            
								<div class="modal bulk_actions" id="product_variant_table_bulk_actions" tabindex="-1" role="dialog">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
											</div>
											<div class="modal-body">
												<?php if(has_permission('manufacturing','','delete') || is_admin()){ ?>
													<div class="checkbox checkbox-danger">
														<input type="checkbox" name="mass_delete" id="mass_delete">
														<label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
													</div>

												<?php } ?>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
												<?php if(has_permission('manufacturing','','delete') || is_admin()){ ?>
													<a href="#" class="btn btn-info" onclick="warehouse_delete_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>

								<!-- update multiple item -->

								<div class="modal export_item hide" id="product_variant_table_export_item" tabindex="-1" role="dialog">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h4 class="modal-title"><?php echo _l('export_item'); ?></h4>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											</div>
											<div class="modal-body">
												<?php if(has_permission('manufacturing','','delete') || is_admin()){ ?>
													<div class="checkbox checkbox-danger">
														<input type="checkbox" name="mass_delete" id="mass_delete">
														<label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
													</div>

												<?php } ?>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
												<?php if(has_permission('manufacturing','','delete') || is_admin()){ ?>
													<a href="#" class="btn btn-info" onclick="warehouse_delete_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>

								<!-- print barcode -->      
								<?php echo form_open_multipart(admin_url('manufacturing/item_print_barcode'), array('id'=>'item_print_barcode')); ?>      
								<div class="modal bulk_actions" id="table_commodity_list_print_barcode" tabindex="-1" role="dialog">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title"><?php echo _l('print_barcode'); ?></h4>
											</div>
											<div class="modal-body">
												<?php if(has_permission('manufacturing','','create') || is_admin()){ ?>

													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<div class="radio radio-primary radio-inline" >
																	<input onchange="print_barcode_option(this); return false" type="radio" id="y_opt_1_" name="select_item" value="0" checked >
																	<label for="y_opt_1_"><?php echo _l('select_all'); ?></label>
																</div>
															</div>
														</div>

														<div class="col-md-6">
															<div class="form-group">
																<div class="radio radio-primary radio-inline" >
																	<input onchange="print_barcode_option(this); return false" type="radio" id="y_opt_2_" name="select_item" value="1" >
																	<label for="y_opt_2_"><?php echo _l('select_item'); ?></label>
																</div>
															</div>
														</div>
													</div>     

													<div class="row display-select-item hide ">
														<div class=" col-md-12">
															<div class="form-group">
																<select name="item_select_print_barcode[]" id="item_select_print_barcode" class="selectpicker" data-live-search="true" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('select_item_print_barcode'); ?>">

																	<?php foreach($product_variants as $commodity) { ?>
																		<option value="<?php echo new_html_entity_decode($commodity['id']); ?>"><?php echo new_html_entity_decode($commodity['description']); ?></option>
																	<?php } ?>
																</select>
															</div>
														</div>
													</div>

												<?php } ?>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

												<?php if(has_permission('manufacturing','','create') || is_admin()){ ?>

													<button type="submit" class="btn btn-info" ><?php echo _l('confirm'); ?></button>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
								<?php echo form_close(); ?>


								<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-product_variant_table" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>

								<a class="hide" href="#"  onclick="staff_export_item(); return false;" data-toggle="modal" data-table=".table-product_variant_table" data-target="#leads_export_item" class=" hide bulk-actions-btn table-btn"><?php echo _l('export_item'); ?></a>

								<a href="#"  onclick="print_barcode_bulk_actions(); return false;" data-toggle="modal" data-table=".table-product_variant_table" data-target="#print_barcode_item" class=" hide print_barcode-bulk-actions-btn table-btn"><?php echo _l('print_barcode'); ?></a>

								<?php 
								$table_data = array(
									'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="product_variant_table"><label></label></div>',
									_l('_images'),
									_l('product_name'),
									_l('barcode'),
									_l('rate'),
									_l('mrp_cost'),
									_l('product_category'),
									_l('product_type'),
									_l('quantity_on_hand'),
									_l('unit_name'),

								);

								$cf = get_custom_fields('items',array('show_on_table'=>1));
								foreach($cf as $custom_field) {
									array_push($table_data,$custom_field['name']);
								}

								render_datatable($table_data,'product_variant_table',
									array('customizable-table'),
									array(
										'proposal_sm' => 'proposal_sm',
										'id'=>'table-product_variant_table',
										'data-last-order-identifier'=>'product_variant_table',
										'data-default-order'=>get_table_last_order('product_variant_table'),
									)); ?>

								</div>
							</div>

						</div>
					</div>
				</div>
				
			</div>
		</div>

	</div>

	<?php init_tail(); ?>
	<?php require 'modules/manufacturing/assets/js/products/product_variant_management_js.php';?>
</body>
</html>
