<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s">
					<?php echo form_open_multipart(admin_url('warehouse/order_return/'.$order_return_type), array('id'=>'add_edit_order_return')); ?>
					<div class="panel-body">

						<div class="row">
							<div class="col-md-12">
								<h4 class="no-margin font-bold "><i class="fa fa-inbox" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
								<hr>
							</div>
						</div>

						<?php 
						$id = '';
						$rel_type = $order_return_type;
						$additional_discount = 0;
						if(isset($order_return)){
							$id = $order_return->id;
							echo form_hidden('isedit');
							$additional_discount = $order_return->additional_discount;
							$rel_type = $order_return->rel_type;

						}
						?>
						<input type="hidden" name="id" value="<?php echo new_html_entity_decode($id); ?>">
						<input type="hidden" name="save_and_send_request" value="false">
						<input type="hidden" name="rel_type" value="<?php echo new_html_entity_decode($rel_type); ?>">
						<input type="hidden" name="receipt_delivery_type" value="<?php echo new_html_entity_decode($receipt_delivery_type); ?>">
						<input type="hidden" name="main_additional_discount" value="<?php echo new_html_entity_decode($additional_discount); ?>">
						<?php 
						$input_number_attr = ['min' => '0.00', 'step' => 'any'];
						$discount_number_attr = ['min' => '0.00', 'step' => 'any', 'readonly' => true];
						$volume_attr = ['min' => '0.00', 'step' => 'any', 'readonly' => true];
						$order_return_code = isset($order_return)? $order_return->order_return_number : (isset($goods_code) ? $goods_code : '');
						$order_return_name = isset($order_return)? $order_return->order_return_name : $order_return_name_ex;
						$company_id = isset($order_return)? $order_return->company_id : '';
						$rel_id = isset($order_return)? $order_return->rel_id : '';
						$admin_note = isset($order_return)? $order_return->admin_note : '';
						$return_reason = isset($order_return)? $order_return->return_reason : '';
						$wh_return_policies_information = isset($order_return)? $order_return->return_policies_information : '';
						$email = isset($order_return)? $order_return->email : '';
						$phonenumber = isset($order_return)? $order_return->phonenumber : '';
						$order_number = isset($order_return)? $order_return->order_number : '';
						$order_date = isset($order_return)? _dt($order_return->order_date) : _dt(date("Y-m-d H:i:s"));
						$number_of_item = isset($order_return)? $order_return->number_of_item : 0;
						$order_total = isset($order_return)? $order_return->order_total : 0;
						$datecreated = isset($order_return)? _dt($order_return->datecreated) : _dt(date("Y-m-d H:i:s"));
						$return_type = isset($order_return)? $order_return->return_type : 'fully';
						$company_name = isset($order_return)? $order_return->company_name : '';


						$rel_id_lable = '';
						$rel_id_data = []; 
						$company_id_lable = _l('wh_customer');
						$company_id_data = $clients;
						$rate_label = _l('rate');
						$main_item_select_hide = '';

						if($receipt_delivery_type == 'inventory_receipt_voucher_returned_goods'){
							$rel_type_data = [];
							$rel_type_data[] = [
								'name' => 'manual',
								'label' => _l('wh_manual'),
							];

							if(get_status_modules_wh('omni_sales')){
								$rel_type_data[] = [
									'name' => 'i_sales_return_order',
									'label' => _l('sales_return_order'),
								];
							}

							$rel_id_lable = _l('wh_sales_order');
							if(isset($order_return)){
								if($order_return->rel_type == 'i_sales_return_order'){
									$rel_id_data = $order_return_get_sale_order;
								}else{
									$rel_id_data = $order_return_get_inventory_delivery;
								}

							}else{

								$rel_id_data = $order_return_get_inventory_delivery;
							}
							$company_id_lable = _l('wh_customer');
							$company_id_data = $this->clients_model->get();
							$rate_label = _l('rate');
							$main_item_select_hide = 'hide';

						}elseif($receipt_delivery_type == 'inventory_delivery_voucher_returned_purchasing_goods'){
							$rel_type_data = [];
							$rel_type_data[] = [
								'name' => 'manual',
								'label' => _l('wh_manual'),
							];
							if(get_status_modules_wh('purchase')){
								$rel_type_data[] = [
									'name' => 'i_purchasing_return_order',
									'label' => _l('purchasing_return_order'),
								];
							}

							$rel_id_lable = _l('wh_purchasing_order');
							if(isset($order_return)){
								if($order_return->rel_type == 'i_purchasing_return_order'){
									$rel_id_data = $order_return_get_purchasing_order;
								}else{
									$rel_id_data = $order_return_get_inventory_receipt;
								}

							}else{
								$rel_id_data = $order_return_get_inventory_receipt;
							}
							$company_id_lable = _l('wh_vendor');
							$company_id_data = $vendor_data;
							$rate_label = _l('purchase_price');
							$main_item_select_hide = 'hide';
						}
						?>

						<!-- start -->
						<div class="row" >
							<div class="col-md-6">
								<div class="row">
									<div class="col-md-6">
										<?php echo render_select('rel_type', $rel_type_data, array('name', array('label')), _l('related_type'), $rel_type, ['data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', false); ?>
									</div>
									<div class="col-md-6">
										<?php echo render_select('rel_id', $rel_id_data, array('id', array('label')), _l('related_data'), $rel_id, ['data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', true); ?>
									</div>
								</div>

							
								<?php if(!get_status_modules_wh('purchase') && $receipt_delivery_type =='inventory_delivery_voucher_returned_purchasing_goods'){ ?>
										<?php echo render_input('company_name','company_name',$company_name, 'text') ?>

								<?php }else{ ?>
									<?php echo render_select('company_id', $company_id_data, array('userid', array('company')), $company_id_lable, $company_id, ['data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', true); ?>
									<div class="row hide">
										<?php echo render_input('company_name','company_name',$company_name, 'text') ?>
									</div>
								<?php } ?>

								<div class="row">
									<div class="col-md-6">
										<?php echo render_input('email','email',$email, 'text') ?>
									</div>
									<div class="col-md-6">
										<?php echo render_input('phonenumber','phonenumber',$phonenumber, 'text') ?>
									</div>
								</div>

								<div class="form-group">
									<label for="number">
										<?php echo _l('order_return_number'); ?>
									</label>
									<div class="input-group">
										<span class="input-group-addon">
											<?php echo new_html_entity_decode($order_return_code); ?>
										</span>
										<input type="text" name="order_return_name" class="form-control" value="<?php echo new_html_entity_decode($order_return_name); ?>" >
									</div>
								</div>

							</div>

							<div class="col-md-6">
								<div class="row">
									<div class="col-md-6">
										<?php echo render_input('order_number','order_number_lable',$order_number, 'text') ?>
									</div>
									<div class="col-md-6">
										<?php echo render_datetime_input('order_date','order_date_label',$order_date) ?>
									</div>
								</div>
								<div class="row hide">
									<div class="col-md-6">
										<?php echo render_input('number_of_item','number_of_item_label',$number_of_item, 'number', $input_number_attr) ?>
									</div>
									<div class="col-md-6">
										<?php echo render_input('order_total','order_total_label',$order_total, 'number', $input_number_attr) ?>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<?php echo render_datetime_input('datecreated','datecreated',$datecreated) ?>
									</div>
									<div class="col-md-6">
										<?php 
										$return_type_data = [];
										$return_type_data[] = [
											'id' => 'partially',
											'label' => _l('partially'),
										];
										$return_type_data[] = [
											'id' => 'fully',
											'label' => _l('fully'),
										];
										
										 ?>
										<?php echo render_select('return_type',$return_type_data,array('id', 'label'), 'return_type', $return_type, [], [], '', '', false) ?>
									</div>
									
								</div>
								
							</div>

						</div>

					</div>

					<div class="panel-body mtop10 invoice-item">
						<div class="row <?php echo new_html_entity_decode($main_item_select_hide); ?>">
							<div class="col-md-4">
								<?php $this->load->view('warehouse/item_include/main_item_select'); ?>
							</div>
						</div>

						<div class="table-responsive s_table ">
							<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
								<thead>
									<tr>
										<th></th>
										<th width="30%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
										<th width="10%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
										<th width="10%" align="right"><?php echo new_html_entity_decode($rate_label); ?></th>
										<th width="12%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
										<th width="10%" align="right"><?php echo _l('subtotal'); ?></th>
										<th width="10%" align="right"><?php echo _l('discount'); ?></th>
										<th width="10%" align="right"><?php echo _l('discount(money)'); ?></th>
										<th width="10%" align="right"><?php echo _l('total_money'); ?></th>
										<th width="15%" align="right" class="hide"><?php echo _l('reason_return'); ?></th>

										<th align="center"><i class="fa fa-cog"></i></th>
									</tr>
								</thead>
								<tbody>
									<?php echo new_html_entity_decode($order_return_row_template); ?>
								</tbody>
							</table>
						</div>
						<div class="col-md-8 col-md-offset-4">
							<table class="table text-right">
								<tbody>
									<tr id="subtotal">
										<td><span class="bold"><?php echo _l('subtotal'); ?> :</span>
										</td>
										<td class="wh-subtotal">
										</td>
									</tr>
									<tr id="wh_additional_discount">
										<td><span class="bold"><?php echo _l('additional_discount'); ?> :</span>
										</td>
										<td class="wh-additional_discount" width="30%">
											<?php echo render_input('additional_discount','',$additional_discount, 'number', $discount_number_attr); ?>
										</td>
									</tr>
									<tr id="total_discount">
										<td><span class="bold"><?php echo _l('total_discount'); ?> :</span>
										</td>
										<td class="wh-total_discount">
										</td>
									</tr>
									

									<tr id="totalmoney">
										<td><span class="bold"><?php echo _l('total_money'); ?> :</span>
										</td>
										<td class="wh-total">
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div id="removed-items"></div>
					</div>


					<div class="row">
						<div class="col-md-12 mtop15">
							<div class="panel-body bottom-transaction">

								<?php echo render_textarea('return_reason','reason',$return_reason,array(),array(),'mtop15'); ?>
								<?php echo render_textarea('admin_note','admin_note',$admin_note,array(),array(),'mtop15'); ?>

								<div class=" row ">
									<div class="col-md-12">
										<label><strong><?php echo _l('return_policies_information'); ?></strong></label>
										<?php if(isset($order_return)){ ?>
											<h5><?php echo new_html_entity_decode($wh_return_policies_information) ; ?></h5>
										<?php }else{ ?>
											<h5><?php echo get_option('wh_return_policies_information') ; ?></h5>
										<?php } ?>
									</div>
								</div>

								<div class="btn-bottom-toolbar text-right">
									<a href="<?php echo admin_url('warehouse/manage_order_return'); ?>"class="btn btn-default text-right mright5"><?php echo _l('close'); ?></a>

									<?php if(wh_check_approval_setting('2') != false) { ?>
										<?php if(isset($order_return) && $order_return->approval != 1){ ?>
											<a href="javascript:void(0)"class="btn btn-info pull-right mright5 add_order_return_send" ><?php echo _l('save_send_request'); ?></a>
										<?php }elseif(!isset($order_return)){ ?>
											<a href="javascript:void(0)"class="btn btn-info pull-right mright5 add_order_return_send" ><?php echo _l('save_send_request'); ?></a>
										<?php } ?>
									<?php } ?>

									<?php if (is_admin() || has_permission('wh_receipt_return_order', '', 'edit') || has_permission('wh_receipt_return_order', '', 'create')) { ?>
										<?php if(isset($order_return) && $order_return->approval == 0){ ?>
											<a href="javascript:void(0)"class="btn btn-info pull-right mright5 add_order_return" ><?php echo _l('save'); ?></a>
										<?php }elseif(!isset($order_return)){ ?>
											<a href="javascript:void(0)"class="btn btn-info pull-right mright5 add_order_return" ><?php echo _l('save'); ?></a>
										<?php } ?>
									<?php } ?>

								</div>
							</div>
							<div class="btn-bottom-pusher"></div>
						</div>
					</div>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
</div>
</div>


<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/order_returns/add_edit_order_return_js.php';?>
</body>
</html>



