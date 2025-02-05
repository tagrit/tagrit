<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s">
					<?php echo form_open_multipart(admin_url('warehouse/packing_list'), array('id'=>'add_edit_packing_list')); ?>
					<div class="panel-body">

						<div class="row">
							<div class="col-md-12">
								<h4 class="no-margin font-bold "><i class="fa fa-inbox" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
								<hr>
							</div>
						</div>

						<?php 
						$id = '';
						$additional_discount = 0;
						$shipping_fee = 0;
						if(isset($packing_list)){
							$id = $packing_list->id;
							echo form_hidden('isedit');
							$additional_discount = $packing_list->additional_discount;
							$shipping_fee = $packing_list->shipping_fee;
						}
						?>
						<input type="hidden" name="id" value="<?php echo new_html_entity_decode($id); ?>">
						<input type="hidden" name="edit_approval" value="<?php echo new_html_entity_decode($edit_approval); ?>">
						<input type="hidden" name="save_and_send_request" value="false">
						<input type="hidden" name="main_additional_discount" value="<?php echo new_html_entity_decode($additional_discount); ?>">
						<input type="hidden" name="main_shipping_fee" value="<?php echo new_html_entity_decode($shipping_fee); ?>">
						<?php 
						$input_number_attr = ['min' => '0.00', 'step' => 'any'];
						$volume_attr = ['min' => '0.00', 'step' => 'any', 'readonly' => true];
						$shipping_fee_number_attr = ['min' => '0.00', 'step' => 'any'];
						$packing_list_code = isset($packing_list)? $packing_list->packing_list_number : (isset($goods_code) ? $goods_code : '');
						$packing_list_name = isset($packing_list)? $packing_list->packing_list_name : $packing_list_name_ex;
						$clientid = isset($packing_list)? $packing_list->clientid : '';
						$delivery_note_id = isset($packing_list)? $packing_list->delivery_note_id : '';
						$width = isset($packing_list)? $packing_list->width : 0.0;
						$height = isset($packing_list)? $packing_list->height : 0.0;
						$lenght = isset($packing_list)? $packing_list->lenght : 0.0;
						$weight = isset($packing_list)? $packing_list->weight : 0.0;
						$volume = isset($packing_list)? $packing_list->volume : 0.0;
						$client_note = isset($packing_list)? $packing_list->client_note : '';
						$admin_note = isset($packing_list)? $packing_list->admin_note : '';


						?>

						<!-- start -->
						<div class="row" >
							<div class="col-md-6">
								<?php echo render_select('delivery_note_id', $goods_deliveries, array('id', array('goods_delivery_code')), 'stock_export', $delivery_note_id, ['data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', true); ?>

								<?php echo render_select('clientid', $clients, array('userid', array('company')), 'client', $clientid, ['data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', true); ?>

								<div class="row">
									<div class="col-md-12">
										<hr class="hr-10" />
										<a href="#" class="edit_shipping_billing_info" data-toggle="modal" data-target="#billing_and_shipping_details"><i class="fa-regular fa-pen-to-square"></i></a>
										<?php $this->load->view('warehouse/packing_lists/billing_and_shipping_template'); ?>
									</div>
									<div class="col-md-6">
										<p class="bold"><?php echo _l('invoice_bill_to'); ?></p>
										<address>
											<span class="billing_street">
												<?php $billing_street = (isset($packing_list) ? $packing_list->billing_street : '--'); ?>
												<?php $billing_street = ($billing_street == '' ? '--' :$billing_street); ?>
												<?php echo new_html_entity_decode($billing_street); ?></span><br>
												<span class="billing_city">
													<?php $billing_city = (isset($packing_list) ? $packing_list->billing_city : '--'); ?>
													<?php $billing_city = ($billing_city == '' ? '--' :$billing_city); ?>
													<?php echo new_html_entity_decode($billing_city); ?></span>,
													<span class="billing_state">
														<?php $billing_state = (isset($packing_list) ? $packing_list->billing_state : '--'); ?>
														<?php $billing_state = ($billing_state == '' ? '--' :$billing_state); ?>
														<?php echo new_html_entity_decode($billing_state); ?></span>
														<br/>
														<span class="billing_country">
															<?php $billing_country = (isset($packing_list) ? get_country_short_name($packing_list->billing_country) : '--'); ?>
															<?php $billing_country = ($billing_country == '' ? '--' :$billing_country); ?>
															<?php echo new_html_entity_decode($billing_country); ?></span>,
															<span class="billing_zip">
																<?php $billing_zip = (isset($packing_list) ? $packing_list->billing_zip : '--'); ?>
																<?php $billing_zip = ($billing_zip == '' ? '--' :$billing_zip); ?>
																<?php echo new_html_entity_decode($billing_zip); ?></span>
															</address>
														</div>
														<div class="col-md-6">
															<p class="bold"><?php echo _l('ship_to'); ?></p>
															<address>
																<span class="shipping_street">
																	<?php $shipping_street = (isset($packing_list) ? $packing_list->shipping_street : '--'); ?>
																	<?php $shipping_street = ($shipping_street == '' ? '--' :$shipping_street); ?>
																	<?php echo new_html_entity_decode($shipping_street); ?></span><br>
																	<span class="shipping_city">
																		<?php $shipping_city = (isset($packing_list) ? $packing_list->shipping_city : '--'); ?>
																		<?php $shipping_city = ($shipping_city == '' ? '--' :$shipping_city); ?>
																		<?php echo new_html_entity_decode($shipping_city); ?></span>,
																		<span class="shipping_state">
																			<?php $shipping_state = (isset($packing_list) ? $packing_list->shipping_state : '--'); ?>
																			<?php $shipping_state = ($shipping_state == '' ? '--' :$shipping_state); ?>
																			<?php echo new_html_entity_decode($shipping_state); ?></span>
																			<br/>
																			<span class="shipping_country">
																				<?php $shipping_country = (isset($packing_list) ? get_country_short_name($packing_list->shipping_country) : '--'); ?>
																				<?php $shipping_country = ($shipping_country == '' ? '--' :$shipping_country); ?>
																				<?php echo new_html_entity_decode($shipping_country); ?></span>,
																				<span class="shipping_zip">
																					<?php $shipping_zip = (isset($packing_list) ? $packing_list->shipping_zip : '--'); ?>
																					<?php $shipping_zip = ($shipping_zip == '' ? '--' :$shipping_zip); ?>
																					<?php echo new_html_entity_decode($shipping_zip); ?></span>
																				</address>
																			</div>
																		</div>

								<div class="form-group">
									<label for="number">
										<?php echo _l('packing_list_number'); ?>
									</label>
									<div class="input-group">
										<span class="input-group-addon">
											<?php echo new_html_entity_decode($packing_list_code); ?>
										</span>
										<input type="text" name="packing_list_name" class="form-control" value="<?php echo new_html_entity_decode($packing_list_name); ?>" >
									</div>
								</div>

							</div>
<!-- custom_name_for_meter
custom_name_for_kg
custom_name_for_m3 -->
							<div class="col-md-6">
								<div class="row">
									<div class="col-md-6">
										<?php echo render_input('width', _l('width_m_label').'('.get_option('custom_name_for_meter').')' ,$width, 'number', $input_number_attr) ?>
									</div>
									<div class="col-md-6">
										<?php echo render_input('height',_l('height_m_label').'('.get_option('custom_name_for_meter').')',$height, 'number', $input_number_attr) ?>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<?php echo render_input('lenght',_l('lenght_m_label').'('.get_option('custom_name_for_meter').')',$lenght, 'number', $input_number_attr) ?>
									</div>
									<div class="col-md-6">
										<?php echo render_input('weight',_l('weight_kg_label').'('.get_option('custom_name_for_kg').')',$weight, 'number', $input_number_attr) ?>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<?php echo render_input('volume',_l('volume_m3_label').'('.get_option('custom_name_for_m3').')',$volume, 'number', $volume_attr) ?>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<?php echo render_textarea('client_note','client_note', $client_note,array(),array(),'mtop15'); ?>
									</div>
								</div>
								
							</div>

						</div>

					</div>

					<div class="panel-body mtop10 invoice-item">
						<div class="row hide">
							<div class="col-md-4">
								<?php $this->load->view('warehouse/item_include/main_item_select'); ?>
							</div>
						</div>

						<div class="table-responsive s_table ">
							<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
								<thead>
									<tr>
										<th></th>
										<th width="20%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
										<th width="10%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
										<th width="10%" align="right"><?php echo _l('rate'); ?></th>
										<th width="12%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
										<th width="15%" align="right"><?php echo _l('subtotal'); ?></th>
										<th width="10%" align="right"><?php echo _l('discount'); ?></th>
										<th width="10%" align="right"><?php echo _l('discount(money)'); ?></th>
										<th width="15%" align="right"><?php echo _l('total_money'); ?></th>

										<th align="center"><i class="fa fa-cog"></i></th>
									</tr>
								</thead>
								<tbody>
									<?php echo new_html_entity_decode($packing_list_row_template); ?>
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
											<?php echo render_input('additional_discount','',$additional_discount, 'number', $input_number_attr); ?>
										</td>
									</tr>
									<tr id="total_discount">
										<td><span class="bold"><?php echo _l('total_discount'); ?> :</span>
										</td>
										<td class="wh-total_discount">
										</td>
									</tr>
									<tr id="wh_shipping_fee">
										<td><span class="bold"><?php echo _l('wh_shipping_fee'); ?> :</span>
										</td>
										<td class="wh-shipping_fee" width="30%">
											<?php echo render_input('shipping_fee','',$shipping_fee, 'number', $shipping_fee_number_attr); ?>
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

								<?php echo render_textarea('admin_note','admin_note',$admin_note,array(),array(),'mtop15'); ?>

								<div class="btn-bottom-toolbar text-right">
									<a href="<?php echo admin_url('warehouse/manage_packing_list'); ?>"class="btn btn-default text-right mright5"><?php echo _l('close'); ?></a>

									<?php if(wh_check_approval_setting('5') != false) { ?>
										<?php if(isset($packing_list) && $packing_list->approval != 1){ ?>
											<a href="javascript:void(0)"class="btn btn-info pull-right mright5 add_packing_list_send" ><?php echo _l('save_send_request'); ?></a>
										<?php }elseif(!isset($packing_list)){ ?>
											<a href="javascript:void(0)"class="btn btn-info pull-right mright5 add_packing_list_send" ><?php echo _l('save_send_request'); ?></a>
										<?php } ?>
									<?php } ?>

									<?php if (is_admin() || has_permission('wh_packing_list', '', 'edit') || has_permission('wh_packing_list', '', 'create')) { ?>
										<?php if(isset($packing_list) && $packing_list->approval == 0){ ?>
											<a href="javascript:void(0)"class="btn btn-info pull-right mright5 add_packing_list" ><?php echo _l('save'); ?></a>
										<?php }elseif(!isset($packing_list)){ ?>
											<a href="javascript:void(0)"class="btn btn-info pull-right mright5 add_packing_list" ><?php echo _l('save'); ?></a>
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
<?php require 'modules/warehouse/assets/js/packing_lists/add_edit_packing_list_js.php';?>
</body>
</html>



