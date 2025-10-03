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
								
									<?php if (has_permission('purchase_order_return', '', 'create') || is_admin()) { ?>

										<a href="<?php echo admin_url('purchase/order_return'); ?>" class="btn btn-primary"><?php echo _l('pur_new'); ?></a>
									<?php } ?>

							</div>
						</div>
						<br/>
						<div id="filter_div" class="row">
							<div class="col-md-3">
								<?php echo render_date_input('from_date', 'from_date', $from_date); ?>
							</div>
							<div class="col-md-3">
								<?php echo render_date_input('to_date', 'to_date', $to_date); ?>
							</div>
							<div class="col-md-3">
								<?php echo render_select('vendor_ft[]',$vendors,array('userid','company'),'vendor','',array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false); ?>
							</div>

							<div class="col-md-3">
								<?php 
									$status = array();
									$status = [
										['id' => 'draft', 'label' => _l('pur_draft')],
										['id' => 'processing', 'label' => _l('pur_processing')],
										['id' => 'confirm', 'label' => _l('pur_confirm')],
										['id' => 'shipping', 'label' => _l('pur_shipping')],
										['id' => 'finish', 'label' => _l('pur_finish')],
										['id' => 'failed', 'label' => _l('pur_failed')],
										['id' => 'canceled', 'label' => _l('pur_canceled')],
										['id' => 'on_hold', 'label' => _l('pur_on_hold')],
									];
								 ?>
								<?php echo render_select('status_ft',$status,array('id','label'),'pur_status','',array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'data-actions-box'=>true),array(),'no-mbot','',true); ?>
							</div>

							<?php 
							$order_return_status = [];
							$order_return_status[] = [
								'id' => 'manual',
								'label' => _l('wh_manual'),
							];
							$order_return_status[] = [
								'id' => 'sales_return_order',
								'label' => _l('sales_return_order'),
							];
							$order_return_status[] = [
								'id' => 'purchasing_return_order',
								'label' => _l('purchasing_return_order'),
							];
							
							 ?>
					
							

						</div>

						<br/>
						<?php render_datatable(array(
							_l('id'),
							_l('pur_order_return_number'),
							_l('pur_vendor'),
							_l('pur_total_amount'),
							_l('pur_discount_total'),
							_l('pur_total_after_discount'),
							_l('pur_datecreated'),
							_l('pur_status_label'),
							// _l('option'),

							
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
<?php require 'modules/purchase/assets/js/manage_order_return_js.php';?>
</body>
</html>
