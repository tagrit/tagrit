<div class="modal fade z-index-none" id="warehouse_modal">
	<div class="modal-dialog ">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php echo pur_html_entity_decode($title); ?></h4>
			</div>
			<?php echo form_open(admin_url('warehouse/order_return_create_stock_import_export/'.$id), array('id' => 'select_warehouse_modal', 'autocomplete'=>'off')); ?>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">

						<?php echo render_select('warehouse_id', $warehouses, array('warehouse_id', array('warehouse_name')), 'warehouse_name', '', ['data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', true); ?>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<?php if(has_permission('purchase_order_return', '', 'create') || has_permission('purchase_order_return', '', 'edit')){ ?>
					<button  type="submit" class="btn btn-info pull-right mright10"><?php echo _l('submit'); ?></button>
				<?php } ?>
			</div>

		</div>

		<?php echo form_close(); ?>
	</div>
</div>
</div>
<?php require('modules/purchase/assets/js/order_returns/select_warehouse_modal_js.php'); ?>

