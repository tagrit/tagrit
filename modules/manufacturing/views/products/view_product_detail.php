<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">               
						<div class="clearfix"></div>
						<h4>
							<?php echo new_html_entity_decode($commodity_item->description); ?>
						</h4>


						<hr class="hr-panel-heading" /> 
						<div class="clearfix"></div> 
						<div class="col-md-12">

							<div class="row col-md-12">

								<h4 class="h4-color"><?php echo _l('general_infor'); ?></h4>
								<hr class="hr-color">



								<div class="col-md-7 panel-padding">
									<table class="table border table-striped table-margintop">
										<tbody>

											<tr class="project-overview">
												<td class="bold"><?php echo _l('product_name'); ?></td>
												<td><?php echo new_html_entity_decode($commodity_item->description) ; ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo _l('product_type'); ?></td>
												<td><?php

												if($commodity_item->product_type == 'storable_product'){
													echo _l('mrp_storable_product') ;

												}elseif($commodity_item->product_type == 'mrp_consumable'){
													echo _l('mrp_consumable') ;
												}else{
													echo _l('mrp_service') ;
												}
												?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo _l('product_category'); ?></td>
												<td><?php echo get_wh_group_name(new_html_entity_decode($commodity_item->group_id)) != null ? get_wh_group_name(new_html_entity_decode($commodity_item->group_id))->name : '' ; ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo _l('barcode'); ?></td>
												<td><?php echo new_html_entity_decode($commodity_item->commodity_barcode) ; ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo _l('sku_code'); ?></td>
												<td><?php echo new_html_entity_decode($commodity_item->sku_code) ; ?></td>
											</tr>

										</tbody>
									</table>
								</div>

								<div class="gallery">
									<div class="wrapper-masonry">
										<div id="masonry" class="masonry-layout columns-3">
											<?php if(isset($commodity_file) && count($commodity_file) > 0){ ?>
												<?php foreach ($commodity_file as $key => $value) { ?>

													<?php if(file_exists('modules/warehouse/uploads/item_img/' .$value["rel_id"].'/'.$value["file_name"])){ ?>
														<a  class="images_w_table" href="<?php echo site_url('modules/warehouse/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/warehouse/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo new_html_entity_decode($value['file_name']) ?>"/></a>

													<?php }elseif(file_exists('modules/purchase/uploads/item_img/' . $value["rel_id"] . '/' . $value["file_name"])) { ?>
														<a  class="images_w_table" href="<?php echo site_url('modules/purchase/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/purchase/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo new_html_entity_decode($value['file_name']) ?>"/></a>


													<?php }elseif(file_exists('modules/manufacturing/uploads/products/' . $value["rel_id"] . '/' . $value["file_name"])){ ?>
														<a  class="images_w_table" href="<?php echo site_url('modules/manufacturing/uploads/products/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/manufacturing/uploads/products/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo new_html_entity_decode($value['file_name']) ?>"/></a>

													<?php }else{ ?>
														<a  href="<?php echo site_url('modules/manufacturing/uploads/null_image.jpg'); ?>"><img class="images_w_table" src="<?php echo site_url('modules/manufacturing/uploads/null_image.jpg'); ?>" alt="null_image.jpg"/></a>
													<?php } ?>


												<?php } ?>
											<?php }else{ ?>

												<a  href="<?php echo site_url('modules/warehouse/uploads/nul_image.jpg'); ?>"><img class="images_w_table" src="<?php echo site_url('modules/warehouse/uploads/nul_image.jpg'); ?>" alt="nul_image.jpg"/></a>

											<?php } ?>
											<div class="clear"></div>
										</div>
									</div>
								</div>
								<br>
							</div>


							<h4 class="h4-color"><?php echo _l('infor_detail'); ?></h4>
							<hr class="hr-color">
							<div class="col-md-6 panel-padding" >
								<table class="table border table-striped table-margintop" >
									<tbody>
										
										<tr class="project-overview">
											<td class="bold"><?php echo _l('sales_price'); ?></td>
											<td><?php echo app_format_money((float)$commodity_item->rate,'') ; ?></td>
										</tr>

									</tbody>
								</table>
							</div>

							<div class="col-md-6 panel-padding" >
								<table class="table table-striped table-margintop">
									<tbody>

										<tr class="project-overview">
											<td class="bold"><?php echo _l('unit_id'); ?></td>
											<td><?php echo  $commodity_item->unit_id != '' && get_unit_type($commodity_item->unit_id) != null ? get_unit_type($commodity_item->unit_id)->unit_name : ''; ?></td>
										</tr> 

										<tr class="project-overview">
											<td class="bold"><?php echo _l('mrp_cost'); ?></td>
											<td><?php echo app_format_money((float)$commodity_item->purchase_price,'') ; ?></td>
										</tr>

									</tbody>
								</table>
							</div>
							<div class=" row ">
								<div class="col-md-12">
									<h4 class="h4-color"><?php echo _l('internal_notes'); ?></h4>
									<hr class="hr-color">
									<h5><?php echo new_html_entity_decode($commodity_item->long_description) ; ?></h5>

								</div>

							</div>

							<div class="horizontal-scrollable-tabs preview-tabs-top">
								<div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
								<div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
								<div class="horizontal-tabs">
									<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">

										<li role="presentation" class="active">
											<a href="#child_items" aria-controls="child_items" role="tab" id="tab_child_items" data-toggle="tab">
												<?php echo _l('sub_items') ?>
											</a>
										</li>  

									</ul>
								</div>
							</div>

							<div class="tab-content col-md-12">

								<!-- child item -->
								<div role="tabpanel" class="tab-pane active" id="child_items">
									<div class="row">
										<div class="col-md-12">
											<div class="col-md-4 ">
												<?php if (has_permission('manufacturing', '', 'create') || is_admin() || has_permission('manufacturing', '', 'edit') ) { ?>

													<a href="#" id="dowload_items"  class="btn btn-warning pull-left  mr-4 button-margin-r-b hide"><?php echo _l('dowload_items'); ?></a>

												<?php } ?>
											</div>

										</div>  
										<div class="col-md-12">

											<!-- view/manage -->            
											<div class="modal bulk_actions" id="table_commodity_list_bulk_actions" tabindex="-1" role="dialog">
												<div class="modal-dialog" role="document">
													<div class="modal-content">
														<div class="modal-header">
															<h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
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

											<!-- update multiple item -->

											<div class="modal export_item" id="table_commodity_list_export_item" tabindex="-1" role="dialog">
												<div class="modal-dialog" role="document">
													<div class="modal-content">
														<div class="modal-header">
															<h4 class="modal-title"><?php echo _l('export_item'); ?></h4>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
														</div>
														<div class="modal-body">
															<?php if(has_permission('manufacturing','','create') || is_admin()){ ?>
																<div class="checkbox checkbox-danger">
																	<input type="checkbox" name="mass_delete" id="mass_delete">
																	<label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
																</div>
																
															<?php } ?>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

															<?php if(has_permission('manufacturing','','create') || is_admin()){ ?>
																<a href="#" class="btn btn-info" onclick="warehouse_delete_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
															<?php } ?>
														</div>
													</div>
													
												</div>
												
											</div>

											<!-- print barcode -->      
											<?php echo form_open_multipart(admin_url('warehouse/item_print_barcode'), array('id'=>'item_print_barcode')); ?>      
											<div class="modal bulk_actions" id="table_commodity_list_print_barcode" tabindex="-1" role="dialog">
												<div class="modal-dialog" role="document">
													<div class="modal-content">
														<div class="modal-header">
															<h4 class="modal-title"><?php echo _l('print_barcode'); ?></h4>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
																			<select name="item_select_print_barcode[]" id="item_select_print_barcode" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('select_item_print_barcode'); ?>">

																				<?php foreach($commodity_filter as $commodity) { ?>
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


											<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_commodity_list" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>


											<a href="#"  onclick="print_barcode_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_commodity_list" data-target="#print_barcode_item" class=" hide print_barcode-bulk-actions-btn table-btn"><?php echo _l('print_barcode'); ?></a>


											<?php 
											$table_data = array(
												'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_commodity_list"><label></label></div>',
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

											render_datatable($table_data,'table_commodity_list',
												array('customizable-table'),
												array(
													'proposal_sm' => 'proposal_sm',
													'id'=>'table-table_commodity_list',
													'data-last-order-identifier'=>'table_commodity_list',
													'data-default-order'=>get_table_last_order('table_commodity_list'),
												)); ?>
											</div>
										</div>
									</div>

								</div>                                    

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo form_close(); ?>

	<!-- add one commodity list sibar end -->  


	<?php echo form_hidden('commodity_id'); ?>
	<?php echo form_hidden('parent_item_filter', 'false'); ?>


	<?php init_tail(); ?>
	<?php require 'modules/manufacturing/assets/js/products/sub_commodity_list_js.php';?>

</body>
</html>

