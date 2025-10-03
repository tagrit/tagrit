<div class="modal fade" id="appointmentModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

				<h4 class="modal-title"><?php echo new_html_entity_decode(_l('add_bills_of_material_l')); ?></h4>
			</div>
			<?php echo form_open(admin_url('manufacturing/add_bill_of_material_modal'), array('id' => 'add_bill_of_material', 'autocomplete'=>'off')); ?>
			<div class="modal-body">
				<div class="tab-content">
					<div class="row">
						<?php $ready_to_produce = 'components_for_1st'; ?>
						<div class="col-md-12">
							<div class="col-md-12">
								<?php echo render_input('bom_code','BOM_code', $bom_code,'text'); ?> 
							</div>
							<div class="col-md-12">
								<?php echo render_select('product_id',$parent_product,array('id','description'),'product_label',''); ?>
							</div>
							<div class="col-md-12">
								<?php echo render_select('product_variant_id',$parent_product,array('id','description'),'product_variant',''); ?>

							</div>

							<div class="col-md-6">
								<?php echo render_input('product_qty','product_qty', 1.0,'number'); ?> 
							</div>

							<div class="col-md-6">
								<?php echo render_select('unit_id',$units,array('unit_type_id', 'unit_name'), 'unit_of_measure', '',[], [], '', '' , false); ?>
							</div>

							<div class="col-md-6">
								<?php echo render_select('routing_id',$routings,array('id', array('routing_code','routing_name')), 'routing_label', '',[], [], '', '' , true); ?>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label for="profit_rate" class="control-label clearfix"><?php echo _l('bom_type'); ?></label>
									<div class="radio radio-primary radio-inline" >
										<input type="radio" id="manufacture_this_product" name="bom_type" value="manufacture_this_product" checked="true">
										<label for="manufacture_this_product"><?php echo _l('manufacture_this_product'); ?></label>

									</div>
									<br>
									<div class="radio radio-primary radio-inline" >
										<input type="radio" id="kit" name="bom_type" value="kit" >
										<label for="kit"><?php echo _l('kit'); ?></label>

									</div>
									<div class="kit_hide hide">
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
								<?php echo render_select('consumption',$consumption_type,array('name', 'label'), 'consumption', '',[], [], '', '' , false); ?>
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
<?php require('modules/manufacturing/assets/js/bill_of_materials/add_edit_bill_of_material_js.php'); ?>