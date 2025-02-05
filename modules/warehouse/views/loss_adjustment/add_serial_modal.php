<div class="modal fade z-index-none" id="addSerialNumberModal">
	<div class="modal-dialog setting-transaction-table">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php echo new_html_entity_decode($title); ?></h4>
			</div>
			<?php echo form_open(admin_url('warehouse/serial_number_modal'), array('id' => 'serial_number_modal', 'autocomplete'=>'off')); ?>
			<div class="modal-body">
				<input type="hidden" name="prefix_name" value="<?php echo new_html_entity_decode($prefix_name); ?>">
				<div class="row">
					<div class="col-md-12">
						<div class="form"> 
							<div id="fill_multiple_serial_number_hs" class="col-md-12 fill_multiple_serial_number handsontable htColumnHeaders">
							</div>
							<?php echo form_hidden('fill_multiple_serial_number_hs'); ?>
						</div>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<a href="javascript:void(0)"class="btn btn-info pull-right mright10 display-block btn_submit_multiple_serial_number" ><?php echo _l('submit'); ?></a>
			</div>

		</div>

		<?php echo form_close(); ?>
	</div>
</div>
</div>
<?php require('modules/warehouse/assets/js/adjustment_fill_multiple_serial_number_js.php'); ?>