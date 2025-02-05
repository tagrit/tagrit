<div class="modal fade z-index-none" id="serialNumberModal">
	<div class="modal-dialog setting-transaction-table">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php echo new_html_entity_decode($title); ?></h4>
			</div>
			<?php echo form_open(admin_url('warehouse/serial_number_modal'), array('id' => 'serial_number_modal', 'autocomplete'=>'off')); ?>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive s_table ">
							<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
								<thead>
									<tr>
										<th width="20%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
										<th width="15%" align="left"><?php echo _l('wh_serial_number'); ?></th>
									</tr>
								</thead>
								<tbody class="body_content">
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<?php echo new_html_entity_decode($table_serial_number); ?>

			</div>
			<div class="modal-footer">
				<a href="javascript:void(0)"class="btn btn-info pull-right mright10 display-block btn_submit_multiple_serial_number" ><?php echo _l('submit'); ?></a>
			</div>

		</div>

		<?php echo form_close(); ?>
	</div>
</div>
</div>
<?php require('modules/warehouse/assets/js/goods_deliveries/select_serial_number_modal_js.php'); ?>