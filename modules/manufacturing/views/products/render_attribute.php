	

<?php if(isset($product) && count(json_decode($product->attributes)) > 0){ ?>
	<!-- update -->
	<?php 
	$attributes_decode = json_decode($product->attributes);
	//get parent attributes
	$parent_item = mrp_get_product($product->parent_id);
	if($parent_item){
		$p_variant = json_decode($parent_item->parent_attributes);
	}

	if(isset($p_variant) && count($p_variant) > 0){ ?>
		<div class="row">
			<div class="col-md-12">
				<?php foreach ($p_variant as $value) { ?>

					<?php 
					$attribute_option ='';
					for ($x = 0; $x < count($attributes_decode); $x++) {

						if($value->name == $attributes_decode[$x]->name){
							$attribute_option .= $attributes_decode[$x]->option;
							break;
						}
					}
					?>

					<div class="col-md-6">
						<div class ="form-group">
							<label for="variation_names_<?php echo new_html_entity_decode($value->name); ?>" class="control-label"><small class="req text-danger">* </small><?php echo new_html_entity_decode($value->name) ; ?></label>
							<select name="variation_names_<?php echo new_html_entity_decode($value->name); ?>" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="" required>
								<option value=""></option>
								<?php  foreach ($value->options as $options_key => $options_value) { 
									$selected='';
									if($attribute_option == $options_value){
										$selected .= 'selected';
									}
								?>

									<option value="<?php echo new_html_entity_decode($options_value); ?>" <?php echo new_html_entity_decode($selected); ?>><?php echo new_html_entity_decode($options_value); ?></option>

								<?php } ?>
							</select>
						</div>
					</div>

				<?php } ?>
			</div>
		</div>
	<?php } ?>


<?php }else{ ?>
	<!-- add new -->
	<div id="item_approve">
		<div class="col-md-11">

			<div class="col-md-4">
				<?php echo render_input('name[0]','variation_name','', 'text'); ?>
			</div>
			<div class="col-md-8">
				<div class="options_wrapper">
					<?php 
					$variation_attr =[];
					$variation_attr['rows'] = '1';
					?>
					<span class="pull-left fa fa-question-circle" data-toggle="tooltip" title="" data-original-title="Populate the field by separating the options by coma. eq. apple,orange,banana"></span>
					<?php echo render_textarea('options[0]', 'variation_options', '', $variation_attr); ?>
				</div>
			</div>
		</div>
		<div class="col-md-1 new_vendor_requests_button">
			<span class="pull-bot">
				<button name="add" class="btn new_wh_approval btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
			</span>
		</div>
	</div>
	<?php } ?>