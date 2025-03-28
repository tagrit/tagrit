<div class="modal fade" id="appointmentModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<?php 
				$title='';
				$id='';

				if(isset($bill_of_material_detail)){
					$title =_l('update_bill_of_material_detail');
					$id= $bill_of_material_detail->id;

					$product_id= $bill_of_material_detail->product_id;
					$product_qty= $bill_of_material_detail->product_qty;
					$unit_id= $bill_of_material_detail->unit_id;
					$operation_id= $bill_of_material_detail->operation_id;
					$display_order= $bill_of_material_detail->display_order;

					if(new_strlen($bill_of_material_detail->apply_on_variants) > 0 ){

						$array_apply_on_variants = new_explode(',', $bill_of_material_detail->apply_on_variants);
					}

				}else{
					$title =_l('add_bill_of_material_detail');

					$product_id= '';
					$product_qty= 1.0;
					$unit_id= '';
					$operation_id= '';
					$display_order= 1;

				}

				$bill_of_material_id = isset($bill_of_material_id) ? $bill_of_material_id : '';

				?>
				<h4 class="modal-title"><?php echo new_html_entity_decode($title); ?></h4>
			</div>
			<?php echo form_open_multipart(admin_url('manufacturing/add_edit_bill_of_material_detail/'.$id), array('id' => 'add_edit_bill_of_material_detail')); ?>
			<div class="modal-body">
				<div class="tab-content">
					<div class="row">
						<input type="hidden" value="<?php echo new_html_entity_decode($bill_of_material_id); ?>" name="bill_of_material_id">

						<div class="col-md-12">
							<div class="col-md-12"> 
								<?php echo render_select('product_id',$products,array('id','description'),'component', $product_id); ?>
							</div>

							<div class="col-md-6">
								<?php echo render_input('product_qty','product_qty', $product_qty,'number'); ?> 
							</div>

							<div class="col-md-6">
								<?php echo render_select('unit_id',$units,array('unit_type_id', 'unit_name'), 'unit_of_measure', $unit_id,[], [], '', '' , false); ?>
							</div>

							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label" for="apply_on_variants"><?php echo _l('apply_on_variants'); ?></label>
									<select class="selectpicker display-block" data-width="100%" id="apply_on_variants" name="apply_on_variants[]" multiple="true" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										<?php foreach($arr_variants as $variant){ ?>
											<?php 
											$apply_on_variants_selected='';

											if(isset($array_apply_on_variants) && count($array_apply_on_variants) > 0){
												if(in_array($variant['name'], $array_apply_on_variants)){
													$apply_on_variants_selected .= 'selected';
												}
											}
											?>
											<option value="<?php echo new_html_entity_decode($variant['name']); ?>" <?php echo new_html_entity_decode($apply_on_variants_selected); ?>><?php echo new_html_entity_decode($variant['label']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-md-12">
								<?php echo render_select('operation_id',$arr_operations,array('id', 'operation'), 'consumed_in_operation', $operation_id,[], [], '', '' , true); ?>
							</div>

						</div>

						<div class="col-md-12">
							<div class="col-md-6">
								<?php echo render_input('display_order','display_order', $display_order,'number'); ?>   
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
			</div>

		</div>

		<?php echo form_close(); ?>
	</div>
</div>
</div>
<?php require('modules/manufacturing/assets/js/bill_of_materials/bill_of_material_details/add_edit_bill_of_material_detail_js.php'); ?>