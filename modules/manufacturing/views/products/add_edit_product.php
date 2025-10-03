<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php 
			$id = '';
			$title = '';
			$can_be_sold = '';
			$can_be_purchased ='';
			$can_be_manufacturing= '';
			$product_type ='';
			$sku_code ='';
			$unit_id = '';
			$purchase_unit_measure='';
			$long_description='';
			$description_sale='';
			$description='';
			$replenish_on_order='';
			$manufacture='';
			$weight='';
			$volume='';
			$hs_code='';
			$group_id='';
			$tax1='';
			$tax2='';

			$description_delivery_orders='';
			$description_receipts='';
			$description_internal_transfers='';
			$supplier_taxes_id='';
			$ordered_quantities='';
			$delivered_quantities='';
			$tab_sales_hide = '';
			$tab_purchase_hide = '';

			if(isset($product)){
				$tab_sales_hide = '  hide';
				$tab_purchase_hide = '  hide';

				$title .= _l('update_product');
				$id    = $product->id;
				$type    = $type;

				if($product->can_be_sold =='can_be_sold'){
					$can_be_sold = 'checked';
					$tab_sales_hide = '';
				}
				if($product->can_be_purchased =='can_be_purchased'){
					$can_be_purchased = 'checked';
					$tab_purchase_hide = '';

				}
				if($product->can_be_manufacturing =='can_be_manufacturing'){
					$can_be_manufacturing = 'checked';
				}

				if($product->replenish_on_order =='replenish_on_order'){
					$replenish_on_order = 'checked';
				}
				if($product->manufacture =='manufacture'){
					$manufacture = 'checked';
				}
				

				$product_type = $product->product_type;
				$rate = $product->rate;
				$barcode = $product->commodity_barcode;
				$purchase_price = $product->purchase_price;
				$sku_code = $product->sku_code;
				$unit_id = $product->unit_id;
				$long_description = $product->long_description;
				$description_sale = $product->description_sale;
				$description = $product->description;
				$group_id = $product->group_id;
				$tax1 = $product->tax;
				$tax2 = $product->tax2;

				$weight = $product->weight;
				$volume = $product->volume;
				$hs_code = $product->hs_code;

				$purchase_unit_measure = $product->purchase_unit_measure;
				$manufacturing_lead_time = $product->manufacturing_lead_time;
				$customer_lead_time = $product->customer_lead_time;

				if($product->invoice_policy == 'ordered_quantities'){
					$ordered_quantities = 'checked';
				}else{
					$delivered_quantities = 'checked';
				}
			

				$description_delivery_orders= $product->description_delivery_orders;
				$description_receipts= $product->description_receipts;
				$description_internal_transfers= $product->description_internal_transfers;
				if(new_strlen($product->supplier_taxes_id) > 0 ){
					$array_supplier_taxes_id = new_explode(',', $product->supplier_taxes_id);
				}

			}else{
				$title .= _l('add_product');

				$can_be_sold = 'checked';
				$can_be_purchased ='checked';
				$can_be_manufacturing= 'checked';

				$product_type = 'storable_product';
				$rate = 1.0;
				$barcode = mrp_generate_commodity_barcode();
				$purchase_price = 0.0;
				$manufacturing_lead_time = 0.0;
				$customer_lead_time = 0.0;
				$weight = 0.0;
				$volume = 0.0;

				$ordered_quantities = 'checked';
				$delivered_quantities = '';
			}

			?>

			<?php echo form_open_multipart(admin_url('manufacturing/add_edit_product/'.$type.'/'.$id), array('id' => 'add_update_product','autocomplete'=>'off')); ?>

			<div class="col-md-12" >
				<div class="panel_s">
					
					<div class="panel-body">
						<div class="row mb-5">
							<div class="col-md-5">
								<h4 class="no-margin"><?php echo new_html_entity_decode($title); ?> 
							</div>
							<div class="col-md-7">

								<div class="o_not_full oe_button_box"><button type="button" name="240" class="btn oe_stat_button"><i class="fa fa-fw o_button_icon fa-pie-chart"></i><div class="o_field_widget o_stat_info"><span class="o_stat_value"><div name="oee" class="o_field_widget o_stat_info o_readonly_modifier" data-original-title="" title="">
									<span class="o_stat_value">0.00</span>
									<span class="o_stat_text"></span>
								</div>%</span><span class="o_stat_text">OEE</span></div></button><button type="button" name="241" class="btn oe_stat_button"><i class="fa fa-fw o_button_icon fa-bar-chart"></i><div class="o_field_widget o_stat_info"><span class="o_stat_value"><div name="blocked_time" class="o_field_widget o_stat_info o_readonly_modifier" data-original-title="" title="">
									<span class="o_stat_value">0.00</span>
									<span class="o_stat_text"></span>
								</div> Hours</span><span class="o_stat_text">Lost</span></div></button><button type="button" name="237" class="btn oe_stat_button" context="{'search_default_workcenter_id': id}"><i class="fa fa-fw o_button_icon fa-bar-chart"></i><div class="o_field_widget o_stat_info"><span class="o_stat_value"><div name="workcenter_load" class="o_field_widget o_stat_info o_readonly_modifier">
									<span class="o_stat_value">0.00</span>
									<span class="o_stat_text"></span>
								</div> Minutes</span><span class="o_stat_text">Load</span></div></button><button type="button" name="243" class="btn oe_stat_button" context="{'search_default_workcenter_id': id, 'search_default_thisyear': True}"><i class="fa fa-fw o_button_icon fa-bar-chart"></i><div class="o_field_widget o_stat_info"><span class="o_stat_value"><div name="performance" class="o_field_widget o_stat_info o_readonly_modifier" data-original-title="" title="">
									<span class="o_stat_value">0</span>
									<span class="o_stat_text"></span>
								</div>%</span><span class="o_stat_text">Performance</span></div></button>
							</div>

						</div>
					</div>
					<hr class="hr-color">

					<!-- start tab -->
					<div class="modal-body">
						<div class="tab-content">
							<!-- start general infor -->
							<div class="row">
								<div class="row">
									
									<div class="col-md-12">
										<input type="hidden" name="id" value="<?php echo new_html_entity_decode($id) ?>">

										<?php echo render_input('description','product_name',$description,'text'); ?>

										<div class="form-group">
											<div class="checkbox checkbox-primary">
												<input  type="checkbox" id="can_be_sold" name="can_be_sold" value="can_be_sold" <?php echo new_html_entity_decode($can_be_sold); ?>>
												<label for="can_be_sold"><?php echo _l('can_be_sold'); ?></label>
											</div>
											<div class="checkbox checkbox-primary">
												<input  type="checkbox" id="can_be_purchased" name="can_be_purchased" value="can_be_purchased" <?php echo new_html_entity_decode($can_be_purchased); ?>>
												<label for="can_be_purchased"><?php echo _l('can_be_purchased'); ?></label>
											</div>
											<div class="checkbox checkbox-primary">
												<input  type="checkbox" id="can_be_manufacturing" name="can_be_manufacturing" value="can_be_manufacturing" <?php echo new_html_entity_decode($can_be_manufacturing); ?>>
												<label for="can_be_manufacturing"><?php echo _l('can_be_manufacturing'); ?></label>
											</div>
										</div>
									</div>

								</div>

								<div class="row">
									<div class="col-md-12">
								<?php if(isset($product) && $type == 'product_variant'){ ?>
									<?php if($product->attributes != null) {
										$array_attributes = json_decode($product->attributes);
										foreach ($array_attributes as $att_key => $att_value) {
										?>
										<button type="button" class="btn btn-sm btn-primary btn_text_tr"><?php echo new_html_entity_decode($att_value->name.' : '.$att_value->option); ?></button>
									<?php }} ?>
								<?php } ?>
								</div>
								</div>

							</div>
							<br>
							<div class="row">

								<div class="horizontal-scrollable-tabs preview-tabs-top">
									<div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
									<div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
									<div class="horizontal-tabs">
										<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
											<li role="presentation" class="active">
												<a href="#general_information" aria-controls="general_information" role="tab" data-toggle="tab">
													<span class="glyphicon glyphicon-align-justify"></span>&nbsp;<?php echo _l('tab_general_information'); ?>
												</a>
											</li>
											<li role="presentation" class="<?php if($type == 'product_variant'){ echo 'hide';} ?>">
												<a href="#tab_variants" aria-controls="tab_variants" role="tab" data-toggle="tab">
													<span class="fa fa-cogs menu-icon"></span>&nbsp;<?php echo _l('tab_variants'); ?>
												</a>
											</li>
											<li role="presentation" class="tab_sales_hide <?php echo new_html_entity_decode($tab_sales_hide); ?>">
												<a href="#tab_sales" aria-controls="tab_sales" role="tab" data-toggle="tab">
													<span class="fa fa-balance-scale menu-icon"></span>&nbsp;<?php echo _l('tab_sales'); ?>
												</a>
											</li>
											<li role="presentation" class="tab_purchase_hide <?php echo new_html_entity_decode($tab_purchase_hide); ?>">
												<a href="#tab_purchase" aria-controls="tab_purchase" role="tab" data-toggle="tab">
													<span class="fa fa-shopping-cart menu-icon"></span>&nbsp;<?php echo _l('tab_purchase'); ?>
												</a>
											</li>
											<li role="presentation" class="">
												<a href="#tab_inventory" aria-controls="tab_inventory" role="tab" data-toggle="tab">
													<span class="fa fa-snowflake-o menu-icon"></span>&nbsp;<?php echo _l('tab_inventory'); ?>
												</a>
											</li>

										</ul>
									</div>
								</div>
								<br>


								<div class="tab-content active">
									<div role="tabpanel" class="tab-pane active" id="general_information">
										<div class="row">
											<div class="col-md-6">
												<?php echo render_select('product_type',$array_product_type,array('name', 'label'), 'product_type', $product_type,[], [], '', '' , false); ?>   
											</div>
											<div class="col-md-6">
												<?php echo render_input('rate','sales_price',$rate,'number'); ?> 
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<?php echo render_select('group_id',$product_group,array('id', 'name'), 'product_category','',[], [], $group_id, '' , false); ?>   
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label class="control-label" for="tax"><?php echo _l('tax_1'); ?></label>
													<select class="selectpicker display-block" data-width="100%" name="tax" data-none-selected-text="<?php echo _l('no_tax'); ?>">
														<option value=""></option>
														<?php foreach($taxes as $tax){ ?>
															<?php 
																$tax1_select='';
																if($tax['id'] == $tax1){
																	$tax1_select .='selected';
																}
															 ?>
															<option value="<?php echo new_html_entity_decode($tax['id']); ?>" data-subtext="<?php echo new_html_entity_decode($tax['name']); ?>" <?php echo new_html_entity_decode($tax1_select) ?>><?php echo new_html_entity_decode($tax['taxrate']); ?>%</option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label class="control-label" for="tax2"><?php echo _l('tax_2'); ?></label>
													<select class="selectpicker display-block" data-width="100%" name="tax2" data-none-selected-text="<?php echo _l('no_tax'); ?>">
														<option value=""></option>
														<?php foreach($taxes as $tax){ ?>
															<?php 
																$tax2_select='';
																if($tax['id'] == $tax2){
																	$tax2_select .='selected';
																}
															 ?>
															<option value="<?php echo new_html_entity_decode($tax['id']); ?>" data-subtext="<?php echo new_html_entity_decode($tax['name']); ?>" <?php echo new_html_entity_decode($tax2_select) ?>><?php echo new_html_entity_decode($tax['taxrate']); ?>%</option>
														<?php } ?>
													</select>
												</div>
											</div>

										</div>	
										<div class="row">
											<div class="col-md-6">
												<?php echo render_input('commodity_barcode','barcode',$barcode,'text'); ?> 
											</div>
											<div class="col-md-6">
												<?php echo render_input('purchase_price','mrp_cost', $purchase_price,'number'); ?> 
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<?php echo render_input('sku_code','sku_code', $sku_code,'text'); ?> 
											</div>
											<div class="col-md-6">
												<?php echo render_select('unit_id',$units,array('unit_type_id', 'unit_name'), 'unit_of_measure', $unit_id,[], [], '', '' , false); ?>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6"></div>
											<div class="col-md-6">
												<?php echo render_select('purchase_unit_measure',$units,array('unit_type_id', 'unit_name'), 'purchase_unit_measure', $purchase_unit_measure,[], [], '', '' , false); ?>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<?php echo render_textarea('long_description', 'internal_notes', $long_description); ?>
											</div>
										</div>


										<div class="row">
											<div class="col-md-12">
												<div id="dropzoneDragArea" class="dz-default dz-message">
													<span><?php echo _l('attach_images'); ?></span>
												</div>
												<div class="dropzone-previews"></div>

												<div id="images_old_preview">

													<?php if( isset($product_attachments) && count($product_attachments) > 0){ ?>
														<?php foreach ($product_attachments as $product_attachment) { ?>
															<?php $rel_type = '' ;?>

															<?php if(file_exists(MANUFACTURING_PRODUCT_UPLOAD . $product_attachment['rel_id'] . '/' . $product_attachment['file_name'])){ ?>
																<?php $rel_type = 'manufacturing' ;?>

															<?php }elseif(file_exists(WAREHOUSE_ITEM_UPLOAD . $product_attachment["rel_id"] . '/' . $product_attachment["file_name"])){ ?>
																<?php $rel_type = 'warehouse' ;?>

															<?php }elseif(file_exists('modules/purchase/uploads/item_img/'. $product_attachment["rel_id"] . '/' . $product_attachment["file_name"])){ ?>

																<?php $rel_type = 'purchase' ;?>
															<?php } ?>

															<?php if($rel_type != ''){ ?>
																<div class="dz-preview dz-image-preview image_old <?php echo new_html_entity_decode($product_attachment['id']) ?>">
																	<div class="dz-image">
																		<?php if(file_exists(MANUFACTURING_PRODUCT_UPLOAD . $product_attachment['rel_id'] . '/' . $product_attachment['file_name'])){ ?>

																			<img class="images_w_table" src="<?php echo site_url('modules/manufacturing/uploads/products/' . $product_attachment['rel_id'] . '/' . $product_attachment['file_name']) . '" alt="' . $product_attachment['file_name'] ?>" >

																		<?php }elseif(file_exists(WAREHOUSE_ITEM_UPLOAD . $product_attachment["rel_id"] . '/' . $product_attachment["file_name"])){ ?>

																			<img class="images_w_table" src="<?php echo site_url('modules/warehouse/uploads/item_img/' . $product_attachment['rel_id'] . '/' . $product_attachment['file_name']) . '" alt="' . $product_attachment['file_name'] ?>" >

																		<?php }elseif(file_exists('modules/purchase/uploads/item_img/'. $product_attachment["rel_id"] . '/' . $product_attachment["file_name"])){ ?>

																			<img class="images_w_table" src="<?php echo site_url('modules/purchase/uploads/item_img/' . $product_attachment['rel_id'] . '/' . $product_attachment['file_name']) . '" alt="' . $product_attachment['file_name'] ?>" >

																		<?php } ?>
																	</div>

																	<div class="dz-error-mark">
																		<a class="dz-remove" data-dz-remove>Remove file</a>
																	</div>
																	<div class="remove_file">
																		<a href="#" class="text-danger" onclick="delete_product_attachment(this,<?php echo new_html_entity_decode($product_attachment['id']); ?>, <?php echo '\''.$rel_type.'\'' ; ?>); return false;"><i class="fa fa fa-times"></i></a>
																	</div>
																</div>
															<?php } ?>

														<?php } ?>
													<?php } ?>
												</div>
											</div>
										</div>

									</div>

									<div role="tabpanel" class="tab-pane <?php if($type == 'product_variant'){ echo 'hide';} ?>" id="tab_variants">
										<label class="variant_note"><?php echo _l('variant_note'); ?></label>
										<div class="row">
											<div class="list_approve">
												<?php if($type == 'product_variant'){ 
													echo new_html_entity_decode($this->load->view('products/render_attribute'));
												}else{
													echo new_html_entity_decode($this->load->view('products/render_variant'));
												} ?>

											</div>

										</div>
									</div>
									<div role="tabpanel" class="tab-pane tab_sales_hide  <?php echo new_html_entity_decode($tab_sales_hide); ?>" id="tab_sales">
										<div class="row">
											<div class="col-md-6">

												<div class="form-group">
													<label for="profit_rate" class="control-label clearfix"><?php echo _l('invoice_policy_label'); ?></label>
													<div class="radio radio-primary radio-inline" >
														<input  type="radio" id="ordered_quantities" name="invoice_policy" value="ordered_quantities" <?php echo new_html_entity_decode($ordered_quantities) ; ?>>
														<label for="ordered_quantities"><?php echo _l('ordered_quantities'); ?></label>

													</div>
													<br>
													<div class="radio radio-primary radio-inline" >
														<input  type="radio" id="delivered_quantities" name="invoice_policy" value="delivered_quantities" <?php  echo new_html_entity_decode($delivered_quantities); ?>>
														<label for="delivered_quantities"><?php echo _l('delivered_quantities'); ?></label>

													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-12">
												<?php echo render_textarea('description_sale', 'description_sale', $description_sale); ?>
											</div>
										</div>
									</div>

									<div role="tabpanel" class="tab-pane tab_purchase_hide  <?php echo new_html_entity_decode($tab_purchase_hide); ?>" id="tab_purchase">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													<label class="control-label" for="supplier_taxes_id"><?php echo _l('supplier_taxes_label'); ?></label>
													<select class="selectpicker display-block" data-width="100%" id="supplier_taxes_id" name="supplier_taxes_id[]" multiple="true" data-actions-box="true" data-none-selected-text="<?php echo _l('no_tax'); ?>">
														<?php foreach($taxes as $tax){ ?>
															<?php 
																$supplier_taxes_selected='';

																if(isset($array_supplier_taxes_id) && count($array_supplier_taxes_id) > 0){
																	if(in_array($tax['id'], $array_supplier_taxes_id)){
																		$supplier_taxes_selected .= 'selected';
																	}
																}
															 ?>
															<option value="<?php echo new_html_entity_decode($tax['id']); ?>" data-subtext="<?php echo new_html_entity_decode($tax['name']); ?>" <?php echo new_html_entity_decode($supplier_taxes_selected); ?>><?php echo new_html_entity_decode($tax['taxrate']); ?>%</option>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane " id="tab_inventory">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<h4><?php echo _l('operations') ; ?></h4>
													<label><?php echo _l('routes'); ?></label>
													<div class="checkbox checkbox-primary">
														<input  type="checkbox" id="replenish_on_order" name="replenish_on_order" value="replenish_on_order" <?php echo new_html_entity_decode($replenish_on_order) ?>>
														<label for="replenish_on_order"><?php echo _l('replenish_on_order_MTO'); ?></label>
													</div>
													<div class="checkbox checkbox-primary">
														<input  type="checkbox" id="manufacture" name="manufacture" value="manufacture" <?php echo new_html_entity_decode($manufacture) ?> >
														<label for="manufacture"><?php echo _l('manufacture'); ?></label>
													</div>

												</div>
												<?php echo render_input('manufacturing_lead_time','manufacturing_lead_time',$manufacturing_lead_time,'number'); ?> 
												<?php echo render_input('customer_lead_time','customer_lead_time',$customer_lead_time,'number'); ?> 
											</div>
											<div class="col-md-6">
												<h4><?php echo _l('logistics') ; ?></h4>
												<?php echo render_input('weight','product_weight',$weight,'number'); ?> 
												<?php echo render_input('volume','product_volume',$volume,'number'); ?> 
												<?php echo render_input('hs_code','hs_code',$hs_code,'text'); ?> 
											</div>

										</div>
										<div class="row">
											<div class="col-md-12">
												<?php echo render_textarea('description_delivery_orders', 'description_delivery_orders', $description_delivery_orders); ?>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<?php echo render_textarea('description_receipts', 'description_receipts', $description_receipts); ?>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<?php echo render_textarea('description_internal_transfers', 'description_internal_transfers', $description_internal_transfers); ?>
											</div>
										</div>

									</div>
								</div>
							</div>

						</div>

						<div class="modal-footer">
							<?php if($type == 'product_variant'){ ?>
								<a href="<?php echo admin_url('manufacturing/product_variant_management'); ?>"  class="btn btn-default mr-2 "><?php echo _l('hr_close'); ?></a>
							<?php }else{ ?>
							<a href="<?php echo admin_url('manufacturing/product_management'); ?>"  class="btn btn-default mr-2 "><?php echo _l('hr_close'); ?></a>
						<?php } ?>
							<?php if(has_permission('manufacturing', '', 'create') || has_permission('manufacturing', '', 'edit')){ ?>
								<button type="submit" class="btn btn-info pull-right submit_button"><?php echo _l('submit'); ?></button>

							<?php } ?>
						</div>

					</div>
				</div>
			</div>

			<?php echo form_close(); ?>
		</div>
	</div>
	<div id="box-loading"></div>

	<?php init_tail(); ?>
	<?php 
	require('modules/manufacturing/assets/js/products/add_edit_product_js.php');
	?>
</body>
</html>
