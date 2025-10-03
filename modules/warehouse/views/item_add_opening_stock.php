<div class="modal fade z-index-none" id="appointmentModal">
	<div class="modal-dialog setting-handsome-table">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

				<h4 class="modal-title"><?php echo new_html_entity_decode($title); ?></h4>
			</div>
			<?php echo form_open(admin_url('warehouse/add_opening_stock'), array('id' => 'add_opening_stock', 'autocomplete'=>'off')); ?>
			<div class="modal-body">
				<input type="hidden" name="parent_id" value="<?php echo new_html_entity_decode($parent_id); ?>">

				<div class="row">
					<div class="col-md-12">
						<h5 class="add_opening_stock"><?php echo _l('add_opening_stock_required'); ?></h5>
						<div class="form"> 
							<div id="item_add_opening_stock_hs" class="col-md-12 item_add_opening_stock handsontable htColumnHeaders">
							</div>
							<?php echo form_hidden('item_add_opening_stock_hs'); ?>
						</div>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<?php if(has_permission('warehouse_item', '', 'create') || has_permission('warehouse_item', '', 'edit')){ ?>
					<a href="#"class="btn btn-info pull-right mright10 display-block btn_add_opening_stock" ><?php echo _l('submit'); ?></a>

				<?php } ?>
			</div>

		</div>

		<?php echo form_close(); ?>
	</div>
</div>
</div>
<?php require('modules/warehouse/assets/js/item_add_opening_stock_js.php'); ?>