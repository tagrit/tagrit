<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s">
					<div class="panel-body">
						<?php echo form_hidden('delivery_id',$delivery_id); ?>
						<div class="row">
							<div class="col-md-11 ">
								<h4 class="no-margin font-bold"><i class="fa fa-reply-all menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
							</div>
							<div class="col-md-1 pull-right">
								<a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view_proposal('.order_return_sm','#order_return_sm_view'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
							</div>
						</div>
						<hr />

						<div class="row">    
							<div class="col-md-3">
								
								<?php if (has_permission('wh_receipt_return_order', '', 'create') || is_admin()) { ?>
									<div class="btn-group">
										<a href="#" class="btn btn-info pull-left mright10 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('add').' '; ?><span class="caret"></span></a>
										<ul class="dropdown-menu dropdown-menu-right">
											<li class="hidden-xs"><a href="<?php echo admin_url('warehouse/order_return/inventory_receipt'); ?>">
												<?php echo _l('wh_inventory_receipt_voucher_returned_goods'); ?></a>
											</li>
											<li class="hidden-xs"><a href="<?php echo admin_url('warehouse/order_return/inventory_delivery'); ?>">
												<?php echo _l('wh_inventory_delivery_voucher_returned_purchasing_goods'); ?></a>
											</li>
										</ul>
									</div>

								<?php } ?>

							</div>
						</div>
						<br/>
						<div class="row">
							<div class="col-sm-4 col-md-2">
								<?php echo render_date_input('from_date', 'from_date', $from_date); ?>
							</div>
							<div class="col-sm-4 col-md-2">
								<?php echo render_date_input('to_date', 'to_date', $to_date); ?>
							</div>
							<?php 
							$receipt_delivery_type = [];
							$receipt_delivery_type[] = [
								'id' => 'inventory_receipt_voucher_returned_goods',
								'label' => _l('wh_inventory_receipt_voucher_returned_goods'),
							];
							$receipt_delivery_type[] = [
								'id' => 'inventory_delivery_voucher_returned_purchasing_goods',
								'label' => _l('wh_inventory_delivery_voucher_returned_purchasing_goods'),
							];
							?>
							
							<div class="col-sm-4 col-md-3">
								<?php echo render_select('receipt_delivery_type[]', $receipt_delivery_type, array('id', array('label')), 'type', '', ['multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', false); ?>
							</div>
							<div class="col-sm-6 col-md-3">
								<?php echo render_select('staff_id[]', $staffs, array('staffid', array('full_name')), 'als_staff', '', ['multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', false); ?>
							</div>
							

							<?php 
							$order_return_status = [];
							$order_return_status[] = [
								'id' => 1,
								'label' => _l('approved'),
							];
							$order_return_status[] = [
								'id' => 5,
								'label' => _l('not_yet_approve'),
							];
							$order_return_status[] = [
								'id' => -1,
								'label' => _l('reject'),
							];
							
							?>
							<div class="col-sm-6 col-md-2">
								<?php echo render_select('status_id[]', $order_return_status, array('id', array('label')), 'status', [], ['multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker'], array(), '', '', false); ?>
							</div>
							

						</div>

						<br/>
						<?php render_datatable(array(
							_l('id'),
							_l('order_return_number'),
							_l('customer_name'),
							_l('total_amount'),
							_l('discount_total'),
							_l('total_after_discount'),
							_l('datecreated'),
							_l('type'),
							_l('status_label'),
							_l('option'),
							
							
						),'table_manage_order_return',['order_return_sm' => 'order_return_sm']); ?>
						
					</div>
				</div>
			</div>
			<div class="col-md-7 small-table-right-col">
				<div id="order_return_sm_view" class="hide">
				</div>
			</div>
			<?php $invoice_value = isset($invoice_id) ? $invoice_id: '' ;?>
			<?php echo form_hidden('invoice_id', $invoice_value) ?>

		</div>
	</div>
</div>
<div id="warehouse_modal_wrapper"></div>

<script>var hidden_columns = [3,4,8];</script>
<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/order_returns/manage_order_return_js.php';?>
</body>
</html>
