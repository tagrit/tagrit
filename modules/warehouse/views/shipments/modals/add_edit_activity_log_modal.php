<!-- status modal start -->
<div class="modal fade" id="add_activity_log" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
					<span class="add-title"><?php echo _l('wh_add_shipment_log'); ?></span>
				</h4>
			</div>
			<?php echo form_open_multipart(site_url('warehouse/shipment_add_edit_activity_log'),array('id'=>'form_activity_log')); ?>

			<div class="modal-body content">
				<div class="row">
					<?php 
					$id = '';
					$description = '';
					$created_date = _dt(date('Y-m-d H:i:s'));
					$status = 'checked';

					if(isset($activity_log)){
						$id = $activity_log->id;
						$description = $activity_log->description;
						$created_date = _dt($activity_log->date);
					}

					?>
					<input type="hidden" name="id" value="<?php echo new_html_entity_decode($id); ?>">  
					<input type="hidden" name="rel_id" value="<?php echo new_html_entity_decode($shipment_id); ?>">  
					<input type="hidden" name="cart_id" value="<?php echo new_html_entity_decode($cart_id); ?>">  

					<div class="col-md-12">
						<div class="form-group">
							<?php echo render_datetime_input( 'date', 'datecreated', $created_date); ?>  
						</div>
					</div>

					<div class="col-md-12">
						<?php echo render_textarea('description','wh_shipment_log', $description); ?>
					</div>
						<div class="col-md-12">
							<?php if($allow_attachment){ ?>
								<div id="dropzoneDragArea" class="dz-default dz-message">
									<span><?php echo _l('attach_images'); ?></span>
								</div>
								<div class="dropzone-previews"></div>
							<?php } ?>

							<div id="images_old_preview">
								<?php 
								if(isset($images_old_value)){
									echo new_html_entity_decode($images_old_value);
								}
								?>
							</div>
						</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default close-status-modal" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-info" ><?php echo _l('submit'); ?></button>
			</div>
			<?php echo form_close(); ?>

		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- status modal end -->
<?php require 'modules/warehouse/assets/js/shipments/activity_log_modal_js.php';?>

