<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s accounting-template">
					<?php echo form_open_multipart($this->uri->uri_string(), array('id'=>'add_edit_order_return')); ?>
					<div class="panel-body">

						<div class="row">
							<div class="col-md-12">
								<h4 class="no-margin font-bold "><i class="fa fa-inbox" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
								<hr>
							</div>
						</div>

						<?php 
						$id = '';
						$rel_type = 'purchasing_return_order';
						$additional_discount = 0;
						if(isset($order_return)){
							$id = $order_return->id;
							echo form_hidden('isedit');
							$additional_discount = $order_return->additional_discount;
							$rel_type = $order_return->rel_type;

						}
						?>
						<input type="hidden" name="id" value="<?php echo pur_html_entity_decode($id); ?>">
						<input type="hidden" name="save_and_send_request" value="false">
						<input type="hidden" name="rel_type" value="<?php echo pur_html_entity_decode($rel_type); ?>">
						<input type="hidden" name="main_additional_discount" value="<?php echo pur_html_entity_decode($additional_discount); ?>">
						<?php 
						$input_number_attr = ['min' => '0.00', 'step' => 'any', 'readonly' => true];
						$volume_attr = ['min' => '0.00', 'step' => 'any', 'readonly' => true];
						$order_return_code = isset($order_return)? $order_return->order_return_number : (isset($goods_code) ? $goods_code : '');
						$order_return_name = isset($order_return)? $order_return->order_return_name : $order_return_name_ex;
						$company_id = isset($order_return)? $order_return->company_id : '';
						$rel_id = isset($order_return)? $order_return->rel_id : '';
						$admin_note = isset($order_return)? $order_return->admin_note : '';
						$pur_return_policies_information = isset($order_return)? $order_return->return_policies_information : '';
						$email = isset($order_return)? $order_return->email : '';
						$phonenumber = isset($order_return)? $order_return->phonenumber : '';
						$order_number = isset($order_return)? $order_return->order_number : '';
						$order_date = isset($order_return)? _dt($order_return->order_date) : _dt(date("Y-m-d H:i:s"));
						$number_of_item = isset($order_return)? $order_return->number_of_item : 0;
						$order_total = isset($order_return)? $order_return->order_total : 0;
						$datecreated = isset($order_return)? _dt($order_return->datecreated) : _dt(date("Y-m-d H:i"));
						$return_type = isset($order_return)? $order_return->return_type : '';
						$return_reason = (isset($order_return) ? $order_return->return_reason : '');

						$rel_id_lable = '';
						$rel_id_data = []; 
						$company_id_lable = _l('wh_customer');
						$company_id_data = $clients;
						$rate_label = _l('rate');
						$main_item_select_hide = '';

						if($rel_type == 'sales_return_order'){
							$rel_id_lable = _l('wh_sales_order');
							$rel_id_data = $this->warehouse_model->get_omni_sale_order_list();
							$company_id_lable = _l('wh_customer');
							$company_id_data = $this->clients_model->get();
							$rate_label = _l('rate');
							$main_item_select_hide = 'hide';

						}elseif($rel_type == 'purchasing_return_order'){
							$rel_id_lable = _l('pur_purchasing_order');
							$rel_id_data = $this->purchase_model->get_pur_order_for_order_return(); 
							$company_id_lable = _l('pur_vendor');;
							$company_id_data = $this->purchase_model->get_vendor();
							$rate_label = _l('purchase_price');
							$main_item_select_hide = 'hide';
						}


						?>

						<!-- start -->
						<div class="row" >
							<div class="col-md-6">
								<?php $fee_return_order = isset($order_return) ? $order_return->fee_return_order : 0;
											echo form_hidden('fee_return_order', $fee_return_order); ?>
								<div class="row">
									<div class="col-md-6">
										<?php if($rel_type == 'purchasing_return_order'){ ?>
											<?php echo render_select('rel_id', $rel_id_data, array('id', array('pur_order_number')), $rel_id_lable, $rel_id, ['data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', true); ?>
										<?php } ?>
									</div>

									<div class="col-md-6 form-group">
										<label for="number">
											<?php echo _l('order_return_number'); ?>
										</label>
										<div class="input-group">
											<span class="input-group-addon">
												<?php echo pur_html_entity_decode($order_return_code); ?>
											</span>
											<input type="text" name="order_return_name" class="form-control" value="<?php echo pur_html_entity_decode($order_return_name); ?>" >
										</div>
									</div>
								</div>

								<?php echo render_select('company_id', $company_id_data, array('userid', array('company')), $company_id_lable, $company_id, ['data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', true); ?>

								<div class="row">
									<div class="col-md-6">
										<?php echo render_input('email','email',$email, 'text') ?>
									</div>
									<div class="col-md-6">
										<?php echo render_input('phonenumber','phonenumber',$phonenumber, 'text') ?>
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
	
								<div class="row">
									<div class="col-md-6">
										<?php echo render_datetime_input('datecreated','datecreated',$datecreated) ?>
									</div>
									<div class="col-md-6">
										<?php 
										$return_type_data = [];
	
										$return_type_data[] = [
											'id' => 'partially',
											'label' => _l('pur_partially'),
										];
										$return_type_data[] = [
											'id' => 'fully',
											'label' => _l('pur_fully'),
										];
										
										 ?>
										<?php echo render_select('return_type',$return_type_data,array('id', 'label'), 'return_type', $return_type) ?>
									</div>
									
								</div>
								<div class="row">
									<div class="col-md-6">
										<?php
					                        $currency_attr = array('data-show-subtext'=>true, 'disabled' => 1);

					                        $selected = '';
					                        foreach($currencies as $currency){
					                          if(isset($order_return) && $order_return->currency != 0){
					                            if($currency['id'] == $order_return->currency){
					                              $selected = $currency['id'];
					                            }
					                          }else{
					                           if($currency['isdefault'] == 1){
					                             $selected = $currency['id'];
					                           }
					                          }
					                        }
					       
					                        ?>
					                     <?php echo render_select('currency', $currencies, array('id','name','symbol'), 'invoice_add_edit_currency', $selected, $currency_attr); ?>
									</div>

									<div class="col-md-6 ">
				                       <div class="form-group select-placeholder">
				                          <label for="discount_type"
				                              class="control-label"><?php echo _l('discount_type'); ?></label>
				                          <select name="discount_type" class="selectpicker" data-width="100%"
				                              data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
				                             
				                              <option value="before_tax" <?php
				                        if (isset($order_return)) {
				                            if ($order_return->discount_type == 'before_tax') {
				                                echo 'selected';
				                            }
				                        } ?>><?php echo _l('discount_type_before_tax'); ?></option>
				                              <option value="after_tax" <?php if (isset($order_return)) {
				                            if ($order_return->discount_type == 'after_tax' || $order_return->discount_type == null) {
				                                echo 'selected';
				                            }
				                        }else {
				                          echo 'selected';
				                        } ?>><?php echo _l('discount_type_after_tax'); ?></option>
				                          </select>
				                      </div>
				                    </div>
								</div>
								
							</div>

						</div>

					</div>

					<div class="panel-body mtop10 invoice-item">
						<div class="row <?php echo pur_html_entity_decode($main_item_select_hide); ?>">
							<div class="col-md-4">
								<?php $this->load->view('purchase/item_include/main_item_select'); ?>
							</div>
						</div>

						<div class="table-responsive s_table ">
							<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
								<thead>
									<tr>
										<th></th>
										<th width="22%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
										<th width="10%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
										<th width="10%" align="right"><?php echo pur_html_entity_decode($rate_label); ?></th>
										<th width="10%" align="right"><?php echo _l('subtotal'); ?></th>
										<th width="12%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
										<th width="10%" align="right"><?php echo _l('discount'); ?></th>
										<th width="10%" align="right"><?php echo _l('discount(money)'); ?></th>
										<th width="10%" align="right"><?php echo _l('total_money'); ?></th>
										<th class="hide" width="%" align="right"><?php echo _l('reason_return'); ?></th>

										<th align="center"><i class="fa fa-cog"></i></th>
									</tr>
								</thead>
								<tbody>
									<?php echo pur_html_entity_decode($order_return_row_template); ?>
								</tbody>
							</table>
						</div>
						<div class="col-md-8 col-md-offset-4">
							<table class="table text-right">
								<tbody>
									<?php echo form_hidden('order_discount', 0); ?>
									<tr id="subtotal">
										<td><span class="bold"><?php echo _l('subtotal'); ?> :</span>
										</td>
										<td class="wh-subtotal">
										</td>
									</tr>
									<tr id="wh_additional_discount" class="hide">
										<td><span class="bold"><?php echo _l('additional_discount'); ?> :</span>
										</td>
										<td class="wh-additional_discount" width="30%">
											<?php echo render_input('additional_discount','',$additional_discount, 'number', $volume_attr); ?>
										</td>
									</tr>
									<tr id="total_discount">
										<td><span class="bold"><?php echo _l('total_discount'); ?> :</span>
										</td>
										<td class="wh-total_discount">
										</td>
									</tr>
									<tr id="fee_return_order">
										<td><span class="bold"><?php echo _l('fee_for_return_order'); ?> :</span>
										</td>
										<td class="wh-fee_for_return_order">
											
										</td>
									</tr>

									<tr id="totalmoney">
										<td><span class="bold"><?php echo _l('total'); ?> :</span>
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
								<?php echo render_textarea('return_reason','pur_return_reason',$return_reason,array(),array(),'mtop15'); ?>
								<?php echo render_textarea('admin_note','admin_note',$admin_note,array(),array(),'mtop15'); ?>

								<div class=" row ">
									<div class="col-md-12">
										<label><strong><?php echo _l('pur_return_policies_information'); ?></strong></label>
										
										<p id="return_policies_information"></p>
										
									</div>
								</div>

								<div class="btn-bottom-toolbar text-right">
									<a href="<?php echo admin_url('warehouse/manage_order_return'); ?>"class="btn btn-default text-right mright5"><?php echo _l('close'); ?></a>

									<?php if (is_admin() || has_permission('purchase_order_return', '', 'edit') || has_permission('purchase_order_return', '', 'create')) { ?>
										
										<a href="javascript:void(0)"class="btn btn-info pull-right mright5 add_order_return" ><?php echo _l('save'); ?></a>
										
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
<?php require 'modules/purchase/assets/js/order_returns/order_return_js.php';?>
</body>
</html>



