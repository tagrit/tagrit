<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_hidden('_attachment_sale_id',$order_return->id); ?>
<?php echo form_hidden('_attachment_sale_type','estimate'); ?>
<div class="col-md-12 no-padding">
	<div class="panel_s">
		<div class="panel-body">
			<?php if($order_return->approval == 0){ ?>
				<div class="ribbon info"><span><?php echo _l('not_yet_approve'); ?></span></div>
			<?php }elseif($order_return->approval == 1){ ?>
				<div class="ribbon success"><span><?php echo _l('approved'); ?></span></div>
			<?php }elseif($order_return->approval == -1){ ?>  
				<div class="ribbon danger"><span><?php echo _l('reject'); ?></span></div>
			<?php } ?>
			<div class="horizontal-scrollable-tabs preview-tabs-top">
				<div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
				<div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
				<div class="horizontal-tabs">
					<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
						<li role="presentation" class="active">
							<a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
								<?php echo _l('wh_order_return_detail'); ?>
							</a>
						</li>  
						<li role="presentation" class="hide" >
							<a href="#tab_activilog" class="tab_activilog" aria-controls="tab_activilog" role="tab" data-toggle="tab">
								<?php echo _l('wh_activilog'); ?>
							</a>
						</li>
						<li role="presentation" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>" class="tab-separator toggle_view">
							<a href="#" onclick="small_table_full_view(); return false;">
								<i class="fa fa-expand"></i>
							</a>
						</li>

					</ul>
				</div>
			</div>

			<div class="clearfix"></div>
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">

					<div id="estimate-preview">
						<div class="row mtop10">
							<div class="col-md-3">
							</div>
							<div class="col-md-9 _buttons">
								<div class="visible-xs">
									<div class="mtop10"></div>
								</div>
								<div class="pull-right">
									<?php if((has_permission('wh_receipt_return_order', '', 'edit') || is_admin()) && ($order_return->approval == 0)){ ?>
										<a href="<?php echo admin_url('warehouse/order_return/'.$order_return->rel_type.'/'.$order_return->id); ?>" data-toggle="tooltip" title="<?php echo _l('edit'); ?>" class="btn btn-default btn-with-tooltip" data-placement="bottom"><i class="fa-regular fa-pen-to-square"></i></a>
									<?php } ?>
									<div class="btn-group">
										<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
										<ul class="dropdown-menu dropdown-menu-right">
											<li class="hidden-xs"><a href="<?php echo admin_url('warehouse/order_return_pdf/'.$order_return->id.'?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
											<li class="hidden-xs"><a href="<?php echo admin_url('warehouse/order_return_pdf/'.$order_return->id.'?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
											<li><a href="<?php echo admin_url('warehouse/order_return_pdf/'.$order_return->id); ?>"><?php echo _l('download'); ?></a></li>
											<li>
												<a href="<?php echo admin_url('warehouse/order_return_pdf/'.$order_return->id.'?print=true'); ?>" target="_blank">
													<?php echo _l('print'); ?>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<hr />
						<div class="row">
							<div class="col-md-6">
								<h4 class="bold">
									<span id="invoice-number">
										<?php echo new_html_entity_decode($order_return->order_return_number .' - '.$order_return->order_return_name); ?>
									</span>
								</h4>
								<address>
									<?php echo format_organization_info(); ?>
								</address>
								<?php if($order_return->rel_type == 'i_sales_return_order'){ ?>
									<p class="no-mbot">
										<span class="bold">
											<?php echo _l('sales_return_order'); ?>
											<a href="<?php echo admin_url('omni_sales/sales_order_manage_order_return#'.$order_return->rel_id) ?>" ><?php echo wh_get_order_return_code($order_return->rel_id); ?></a>
										</span>
										<h5 class="bold">
										</h5>
									</p>
								<?php }elseif($order_return->rel_type == 'i_purchasing_return_order'){ ?>
									<p class="no-mbot">
										<span class="bold">
											<?php echo _l('purchasing_return_order'); ?>
											<a href="<?php echo admin_url('purchase/order_returns#'.$order_return->rel_id) ?>" ><?php echo wh_get_order_return_code($order_return->rel_id); ?></a>
										</span>
										<h5 class="bold">
										</h5>
									</p>
								<?php }elseif($order_return->rel_type == 'manual' && $order_return->receipt_delivery_type == 'inventory_receipt_voucher_returned_goods'){ ?>
									<?php 
									
									$goods_delivery_code = '';
									$get_goods_delivery_code = get_goods_delivery_code($order_return->rel_id);
									if($get_goods_delivery_code){
										$goods_delivery_code = $get_goods_delivery_code->goods_delivery_code;
									}

									?>

									<p class="no-mbot">
										<span class="bold">
											<?php echo _l('from_stock_export'); ?>
											<a href="<?php echo admin_url('warehouse/manage_delivery#'.$order_return->rel_id) ?>" ><?php echo new_html_entity_decode($goods_delivery_code); ?></a>
										</span>
										<h5 class="bold">
										</h5>
									</p>

								<?php }elseif($order_return->rel_type == 'manual' && $order_return->receipt_delivery_type == 'inventory_delivery_voucher_returned_purchasing_goods'){ ?>
									<?php 
									$goods_receipt_code = '';
									$get_goods_receipt_code = get_goods_receipt_code($order_return->rel_id);
									if($get_goods_receipt_code){
										$goods_receipt_code = $get_goods_receipt_code->goods_receipt_code;
									}

									 ?>
									<p class="no-mbot">
										<span class="bold">
											<?php echo _l('from_stock_import'); ?>
											<a href="<?php echo admin_url('warehouse/manage_purchase#'.$order_return->rel_id) ?>" ><?php echo new_html_entity_decode($goods_receipt_code); ?></a>
										</span>
										<h5 class="bold">
										</h5>
									</p>

								<?php } ?>

								<?php if($order_return->receipt_delivery_id != 0){ ?>

									<?php if(($order_return->rel_type == 'manual' && $order_return->receipt_delivery_type == 'inventory_receipt_voucher_returned_goods') || $order_return->rel_type == 'i_sales_return_order'){ ?>
										<?php 
										$create_goods_receipt_code = '';
										$get_new_goods_receipt_code = get_goods_receipt_code($order_return->receipt_delivery_id);
										if($get_new_goods_receipt_code){
											$create_goods_receipt_code = $get_new_goods_receipt_code->goods_receipt_code;
										}

										?>
										<p class="no-mbot">
											<span class="bold">
												<?php echo _l('goods_receipt'); ?>
												<a href="<?php echo admin_url('warehouse/manage_purchase#'.$order_return->receipt_delivery_id) ?>" ><?php echo new_html_entity_decode($create_goods_receipt_code); ?></a>
											</span>
											<h5 class="bold">
											</h5>
										</p>

									<?php }elseif(($order_return->rel_type == 'manual' && $order_return->receipt_delivery_type == 'inventory_delivery_voucher_returned_purchasing_goods') || $order_return->rel_type == 'i_purchasing_return_order'){?>

										<?php 

										$create_goods_delivery_code = '';
										$get_new_goods_delivery_code = get_goods_delivery_code($order_return->receipt_delivery_id);
										if($get_new_goods_delivery_code){
											$create_goods_delivery_code = $get_new_goods_delivery_code->goods_delivery_code;
										}

										?>
										<p class="no-mbot">
											<span class="bold">
												<?php echo _l('stock_export'); ?>
												<a href="<?php echo admin_url('warehouse/manage_delivery#'.$order_return->receipt_delivery_id) ?>" ><?php echo new_html_entity_decode($create_goods_delivery_code); ?></a>
											</span>
											<h5 class="bold">
											</h5>
										</p>
									<?php } ?>

								<?php } ?>
							</div>
							<div class="col-sm-6 text-right">
								<?php if( ($order_return->rel_type == 'manual' && $order_return->rel_type == 'inventory_receipt_voucher_returned_goods') || $order_return->rel_type == 'i_sales_return_order') { ?>
									<span class="bold"><?php echo _l('customer_name'); ?>:</span>
									<address>
										<?php echo new_html_entity_decode(get_company_name($order_return->company_id)) ?>
									</address>
								<?php }else{ ?>
									<span class="bold"><?php echo _l('wh_vendor'); ?>:</span>
									<address>
										<?php if(get_status_modules_wh('purchase')){ ?>
											<?php echo new_html_entity_decode(get_vendor_company_name($order_return->company_id)) ?>
										<?php }else{ ?>
											<?php echo new_html_entity_decode($order_return->company_name) ?>
										<?php } ?>
									</address>
								<?php } ?>
								<span class="bold"><?php echo _l('email'); ?>:</span>
									<?php echo new_html_entity_decode($order_return->email) ?><br>
								<span class="bold"><?php echo _l('phonenumber'); ?>:</span>
									<?php echo new_html_entity_decode($order_return->phonenumber) ?>
								
								
								<p class="no-mbot">
									<span class="bold">
										<?php echo _l('order_return_date'); ?>
									</span>
									<?php echo _d($order_return->datecreated); ?>
								</p>
							</div>
						</div>
						<?php 
						$rate_label = _l('rate');

						if($order_return->rel_type == 'sales_return_order'){
							$rate_label = _l('rate');
						}elseif($order_return->rel_type == 'purchasing_return_order'){
							$rate_label = _l('purchase_price');
						}
						?>
						<div class="row">
							<div class="col-md-12">
								<div class="table-responsive">
									<table class="table items items-preview estimate-items-preview" data-type="estimate">
										<thead>
											<tr>
												<th align="center">#</th>
												<th  colspan="1"><?php echo _l('commodity_code') ?></th>
												<th align="right" colspan="1"><?php echo _l('quantity') ?></th>
												<th align="right" colspan="1"><?php echo new_html_entity_decode($rate_label) ?></th>
												<th align="right" colspan="1"><?php echo _l('invoice_table_tax_heading') ?></th>
												<th align="right" colspan="1"><?php echo _l('subtotal') ?></th>
												<th align="right" colspan="1"><?php echo _l('discount').'(%)' ?></th>
												<th align="right" colspan="1"><?php echo _l('discount(money)') ?></th>
												<th align="right" colspan="1"><?php echo _l('total_money') ?></th>

											</tr>
										</thead>
										<tbody class="ui-sortable">
											<?php 
											$subtotal = 0 ;
											foreach ($order_return_detail as $delivery => $order_return_value) {
												$delivery++;
												$discount = (isset($order_return_value) ? $order_return_value['discount'] : '');
												$discount_money = (isset($order_return_value) ? $order_return_value['discount_total'] : '');

												$quantity = (isset($order_return_value) ? $order_return_value['quantity'] : '');
												$unit_price = (isset($order_return_value) ? $order_return_value['unit_price'] : '');
												$total_after_discount = (isset($order_return_value) ? $order_return_value['total_after_discount'] : '');

												$commodity_code = get_commodity_name($order_return_value['commodity_code']) != null ? get_commodity_name($order_return_value['commodity_code'])->commodity_code : '';
												$commodity_name = get_commodity_name($order_return_value['commodity_code']) != null ? get_commodity_name($order_return_value['commodity_code'])->description : '';

												$unit_name = '';
												if(is_numeric($order_return_value['unit_id'])){
													$unit_name = get_unit_type($order_return_value['unit_id']) != null ? ' '.get_unit_type($order_return_value['unit_id'])->unit_name : '';
												}

												$commodity_name = $order_return_value['commodity_name'];
												if(new_strlen($commodity_name  ?? '') == 0){
													$commodity_name = wh_get_item_variatiom($order_return_value['commodity_code']);
												}

												?>

												<tr>
													<td ><?php echo new_html_entity_decode($delivery) ?></td>
													<td ><?php echo new_html_entity_decode($commodity_name) ?></td>
													<td class="text-right"><?php echo new_html_entity_decode($quantity).$unit_name ?></td>
													<td class="text-right"><?php echo app_format_money((float)$unit_price,'') ?></td>

													<?php echo  wh_render_taxes_html(wh_convert_item_taxes($order_return_value['tax_id'], $order_return_value['tax_rate'], $order_return_value['tax_name']), 15); ?>
													<td class="text-right"><?php echo app_format_money((float)$order_return_value['sub_total'],'') ?></td>
													<td class="text-right"><?php echo app_format_money((float)$discount,'') ?></td>
													<td class="text-right"><?php echo app_format_money((float)$discount_money,'') ?></td>
													<td class="text-right"><?php echo app_format_money((float)$total_after_discount,'') ?></td>
												</tr>
											<?php  } ?>
										</tbody>
									</table>

									<div class="col-md-8 col-md-offset-4">
										<table class="table text-right">
											<tbody>
												<tr id="subtotal">
													<td class="bold"><?php echo _l('subtotal'); ?></td>
													<td><?php echo app_format_money((float)$order_return->subtotal, $base_currency); ?></td>
												</tr>
												<?php if(isset($order_return) && $tax_data['html_currency'] != ''){
													echo new_html_entity_decode($tax_data['html_currency']);
												} ?>
												<tr id="total_discount">
													<?php
													$discount_total = 0 ;
													if(isset($order_return)){
														$discount_total += (float)$order_return->discount_total  + (float)$order_return->additional_discount;
													}
													?>
													<td class="bold"><?php echo _l('total_discount'); ?></td>
													<td><?php echo app_format_money((float)$discount_total, $base_currency); ?></td>
												</tr>
												<tr id="totalmoney">
													<?php
													$total_after_discount = isset($order_return) ?  $order_return->total_after_discount : 0 ;
													?>
													<td class="bold"><?php echo _l('total_money'); ?></td>
													<td><?php echo app_format_money((float)$total_after_discount, $base_currency); ?></td>
												</tr>
											</tbody>
										</table>
									</div>

								</div>
							</div>

							<div class="col-md-12">
								<div class="project-overview-right">
									<?php if(count($list_approve_status) > 0){ ?>

										<div class="row">
											<div class="col-md-12 project-overview-expenses-finance">
												<div class="col-md-4 text-center">
												</div>
												<?php 
												$this->load->model('staff_model');
												$enter_charge_code = 0;
												foreach ($list_approve_status as $value) {
													$value['staffid'] = new_explode(', ',$value['staffid']);
													if($value['action'] == 'sign'){
														?>
														<div class="col-md-3 text-center">
															<p class="text-uppercase text-muted no-mtop bold">
																<?php
																$staff_name = '';
																$st = _l('status_0');
																$color = 'warning';
																foreach ($value['staffid'] as $key => $val) {
																	if($staff_name != '')
																	{
																		$staff_name .= ' or ';
																	}
																	if($this->staff_model->get($val)){
																		$staff_name .= $this->staff_model->get($val)->firstname;
																	}
																}
																echo new_html_entity_decode($staff_name); 
															?></p>
															<?php if($value['approve'] == 1){ 
																?>

																<?php if (file_exists(WAREHOUSE_ORDER_RETURN_MODULE_UPLOAD_FOLDER . $order_return->id . '/signature_'.$value['id'].'.png') ){ ?>

																	<img src="<?php echo site_url('modules/warehouse/uploads/order_returns/'.$order_return->id.'/signature_'.$value['id'].'.png'); ?>" class="img-width-height">

																<?php }else{ ?>
																	<img src="<?php echo site_url('modules/warehouse/uploads/image_not_available.jpg'); ?>" class="img-width-height">
																<?php } ?>


															<?php }
															?> 
															<p class="text-muted no-mtop bold">  
																<?php echo new_html_entity_decode($value['note']) ?>
															</p>   
														</div>
													<?php }else{ ?>
														<div class="col-md-3 text-center">
															<p class="text-uppercase text-muted no-mtop bold">
																<?php
																$staff_name = '';
																foreach ($value['staffid'] as $key => $val) {
																	if($staff_name != '')
																	{
																		$staff_name .= ' or ';
																	}
																	if($this->staff_model->get($val)){
																		$staff_name .= $this->staff_model->get($val)->firstname;
																	}
																}
																echo new_html_entity_decode($staff_name); 
															?></p>
															<?php if($value['approve'] == 1){ 
																?>
																<img src="<?php echo site_url('modules/warehouse/uploads/approval/approved.png') ; ?>" class="img-width-height">
															<?php }elseif($value['approve'] == -1){ ?>
																<img src="<?php echo site_url('modules/warehouse/uploads/approval/rejected.png') ; ?>" class="img-width-height">
															<?php }
															?>  

															<p class="text-muted no-mtop bold">  
																<?php echo new_html_entity_decode($value['note']) ?>
															</p>
														</div>
													<?php }
												} ?>
											</div>
										</div>

									<?php } ?>
								</div>

								<div class="pull-right">

									<?php 
									if($order_return->approval != 1 && ($check_approve_status == false ))

										{ ?>
											<?php if($check_appr && $check_appr != false){ ?>
												<a data-toggle="tooltip" class="btn btn-success lead-top-btn lead-view send_request_approve_class" data-placement="top" href="#" onclick="send_request_approve(<?php echo new_html_entity_decode($order_return->id); ?>); return false;"><?php echo _l('send_request_approve'); ?></a>
											<?php } ?>

										<?php }
										if(isset($check_approve_status['staffid'])){
											?>
											<?php 
											if(in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign)){ ?>
												<div class="btn-group" >
													<a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>
													<ul class="dropdown-menu dropdown-menu-right menu-width-height" >
														<li>
															<div class="col-md-12">
																<?php echo render_textarea('reason', 'reason'); ?>
															</div>
														</li>
														<li>
															<div class="row text-right col-md-12">
																<a href="#" onclick="approve_request(<?php echo new_html_entity_decode($order_return->id); ?>); return false;" class="btn btn-success button-margin approve_request_class" ><?php echo _l('approve'); ?></a>
																<a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_request(<?php echo new_html_entity_decode($order_return->id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a></div>
															</li>
														</ul>
													</div>
												<?php }
												?>

												<?php
												if(in_array(get_staff_user_id(), $check_approve_status['staffid']) && in_array(get_staff_user_id(), $get_staff_sign)){ ?>
													<button onclick="accept_action();" class="btn btn-success pull-right action-button"><?php echo _l('e_signature_sign'); ?></button>
												<?php }
												?>
												<?php 
											}
											?>
										</div>

									</div>  

									<div class="col-md-12">
										<?php if( ( ($order_return->rel_type == 'manual' && $order_return->receipt_delivery_type == 'inventory_delivery_voucher_returned_purchasing_goods') || ($order_return->rel_type == 'i_purchasing_return_order' ) ) &&  (int)$order_return->approval == 1 && (int)$order_return->receipt_delivery_id == 0 ){ ?>

											<a data-toggle="tooltip" class="btn btn-success lead-top-btn lead-view send_request_approve_class" data-placement="top" href="#" onclick="open_warehouse_modal(<?php echo new_html_entity_decode($order_return->id); ?>, <?php echo new_html_entity_decode($order_return->id); ?>); return false;"><?php echo _l('wh_create_inventory_delivery_vocucher'); ?></a>

										<?php } ?>
									</div>                                        

								</div>

								<hr />
								
								<?php if($order_return->return_reason != ''){ ?>
									<div class="col-md-12 row mtop15">
										<p class="bold text-muted"><?php echo _l('reason_return'); ?></p>
										<p><?php echo new_html_entity_decode($order_return->return_reason); ?></p>
									</div>
								<?php } ?>
								<?php if($order_return->admin_note != ''){ ?>
									<div class="col-md-12 row mtop15">
										<p class="bold text-muted"><?php echo _l('admin_note'); ?></p>
										<p><?php echo new_html_entity_decode($order_return->admin_note); ?></p>
									</div>
								<?php } ?>
								
								<?php if($order_return->return_policies_information != ''){ ?>
									<div class="col-md-12 row mtop15 table-responsive">
										<p class="bold text-muted"><?php echo _l('return_policies_information'); ?></p>
										<p><?php echo new_html_entity_decode($order_return->return_policies_information); ?></p>
									</div>
								<?php } ?>

							</div>
						</div>
						
						<div role="tabpanel" class="tab-pane" id="tab_activilog">
							<div class="panel_s no-shadow">
								<div class="activity-feed">
									<?php foreach($activity_log as $log){ ?>
										<div class="feed-item">
											<div class="date">
												<span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($log['date']); ?>">
													<?php echo time_ago($log['date']); ?>
												</span>
												<?php if($log['staffid'] == get_staff_user_id() || is_admin() || has_permission('wh_receipt_return_order','','delete()')){ ?>
													<a href="#" class="pull-right text-danger" onclick="delete_wh_activitylog(this,<?php echo new_html_entity_decode($log['id']); ?>);return false;"><i class="fa fa fa-times"></i></a>
												<?php } ?>
											</div>
											<div class="text">
												<?php if($log['staffid'] != 0){ ?>
													<a href="<?php echo admin_url('profile/'.$log["staffid"]); ?>">
														<?php echo staff_profile_image($log['staffid'],array('staff-profile-xs-image pull-left mright5'));
														?>
													</a>
													<?php
												}
												$additional_data = '';
												if(!empty($log['additional_data'])){
													$additional_data = unserialize($log['additional_data']);
													echo ($log['staffid'] == 0) ? _l($log['description'],$additional_data) : $log['full_name'] .' - '._l($log['description'],$additional_data);
												} else {
													echo new_html_entity_decode($log['full_name']) . ' - ';
													echo _l($log['description']);
												}
												?>
											</div>

										</div>
									<?php } ?>
								</div>
								<div class="col-md-12">
									<?php echo render_textarea('wh_activity_textarea','','',array('placeholder'=>_l('wh_activilog')),array(),'mtop15'); ?>
									<div class="text-right">
										<button id="wh_enter_activity" class="btn btn-info"><?php echo _l('submit'); ?></button>
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
					</div>

					<div class="modal fade" id="add_action" tabindex="-1" role="dialog">
						<div class="modal-dialog">
							<div class="modal-content">

								<div class="modal-body">
									<p class="bold" id="signatureLabel"><?php echo _l('signature'); ?></p>
									<div class="signature-pad--body">
										<canvas id="signature" height="130" width="550"></canvas>
									</div>
									<input type="text" class="sig-input-style" tabindex="-1" name="signature" id="signatureInput">
									<div class="dispay-block">
										<button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
									<button onclick="sign_request(<?php echo new_html_entity_decode($order_return->id); ?>);" autocomplete="off" class="btn btn-success sign_request_class"><?php echo _l('e_signature_sign'); ?></button>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

		<?php require 'modules/warehouse/assets/js/order_returns/view_order_return_js.php';?>
	</body>
	</html>