<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_hidden('_attachment_sale_id',$order_return->id); ?>
<?php echo form_hidden('_attachment_sale_type','estimate'); ?>
<div class="col-md-12 no-padding">
	<div class="panel_s">
		<div class="panel-body">
			
			<div class="ribbon success"><span><?php echo _l('pur_'.$order_return->status); ?></span></div>
			
			<div class="horizontal-scrollable-tabs preview-tabs-top">
				<div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
				<div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
				<div class="horizontal-tabs">
					<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
						<li role="presentation" class="active">
							<a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
								<?php echo _l('pur_order_return_detail'); ?>
							</a>
						</li>  
						<li role="presentation">
		                     <a href="#tab_refunds" aria-controls="tab_refunds" role="tab" data-toggle="tab">
		                     <?php echo _l('refunds'); ?>
		                     <?php if(count($order_return_refunds) > 0) {
		                        echo '<span class="badge">'.count($order_return_refunds).'</span>';
		                        }
		                        ?>
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
	
									<div class="btn-group">
										<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
										<ul class="dropdown-menu dropdown-menu-right">
											<li class="hidden-xs"><a href="<?php echo admin_url('purchase/order_return_pdf/'.$order_return->id.'?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
											<li class="hidden-xs"><a href="<?php echo admin_url('purchase/order_return_pdf/'.$order_return->id.'?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
											<li><a href="<?php echo admin_url('purchase/order_return_pdf/'.$order_return->id); ?>"><?php echo _l('download'); ?></a></li>
											<li>
												<a href="<?php echo admin_url('purchase/order_return_pdf/'.$order_return->id.'?print=true'); ?>" target="_blank">
													<?php echo _l('print'); ?>
												</a>
											</li>
										</ul>
									</div>
									<div class="btn-group">
					                     <button type="button" class="btn btn-default pull-left dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					                     <?php echo _l('more'); ?> <span class="caret"></span>
					                     </button>
					                     <ul class="dropdown-menu dropdown-menu-right">
					                     	<?php if(has_permission('purchase_order_return','','edit') && ( $order_return->status == 'confirm' || $order_return->status == 'processing') && get_order_return_remaining_refund($order_return->id) > 0){ ?>
						                     	<li>
						                           <a href="#" onclick="refund_order_return(); return false;" id="order_return_refund">
						                           <?php echo _l('refund'); ?>
						                           </a>
						                        </li>
						                    <?php } ?>

					                        <?php if(is_admin()){ ?>
					                        <?php 
						                        $statuses = [
						                        	'draft',
						                        	'processing',
						                        	'confirm',
						                        	'shipping',
						                        	'finish',
						                        	'failed',
						                        	'canceled',
						                        	'on_hold',
						                        	'status',
						                        ]; 
					                        ?>

					                        <?php foreach($statuses as $status){ ?>
					                        	<?php if($status != $order_return->status){ ?>
							                        <li>
							                           <a href="<?php echo admin_url('purchase/mark_return_order_as/'.$status.'/'.$order_return->id); ?>"><?php echo _l('invoice_mark_as',_l('pur_'.$status)); ?></a>
							                        </li>
							                    <?php } ?>
						                    <?php } ?>    
					                        <?php } ?>

	
					                        
					                        <?php if(has_permission('purchase_order_return','','delete')){ ?>
					                        <li>
					                           <a href="<?php echo admin_url('purchase/delete_order_return/'.$order_return->id); ?>" class="text-danger delete-text _delete"><?php echo _l('delete_invoice'); ?></a>
					                        </li>
					                        <?php } ?>
					           
					                    
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
										<?php echo pur_html_entity_decode($order_return->order_return_number .' - '.$order_return->order_return_name); ?>
									</span>
								</h4>
								<address>
									<?php echo format_organization_info(); ?>
								</address>
								<?php if($order_return->rel_type == 'sales_return_order'){ ?>
									<p class="no-mbot">
										<span class="bold">
											<?php echo _l('sales_return_order'); ?>
											<a href="<?php echo admin_url('omni_sales/view_order_detailt/'.$order_return->rel_id) ?>" ><?php echo $order_return->order_number; ?></a>
										</span>
										<h5 class="bold">
										</h5>
									</p>
								<?php }elseif($order_return->rel_type == 'purchasing_return_order'){ ?>
									<p class="no-mbot">
										<span class="bold">
											<?php echo _l('purchasing_return_order'); ?>
											<a href="<?php echo admin_url('purchase/purchase_order/'.$order_return->rel_id) ?>" ><?php echo $order_return->order_number; ?></a>
										</span>
										<h5 class="bold">
										</h5>
									</p>
								<?php } ?>
							</div>
							<div class="col-sm-6 text-right">
								<?php if($order_return->rel_type == 'manual' || $order_return->rel_type == 'sales_return_order'){ ?>
									<span class="bold"><?php echo _l('customer_name'); ?>:</span>
									<address>
										<?php echo pur_html_entity_decode(get_company_name($order_return->company_id)) ?>
									</address>
								<?php }else{ ?>
									<span class="bold"><?php echo _l('pur_vendor'); ?>:</span>
									<address>
										<?php echo pur_html_entity_decode(get_vendor_company_name($order_return->company_id)) ?>
									</address>
								<?php } ?>
								<span class="bold"><?php echo _l('email'); ?>:</span>
									<?php echo pur_html_entity_decode($order_return->email) ?><br>
								<span class="bold"><?php echo _l('phonenumber'); ?>:</span>
									<?php echo pur_html_entity_decode($order_return->phonenumber) ?>
								
								
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
												<th align="right" colspan="1"><?php echo pur_html_entity_decode($rate_label) ?></th>
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

												$commodity_code = pur_get_commodity_name($order_return_value['commodity_code']) != null ? pur_get_commodity_name($order_return_value['commodity_code'])->commodity_code : '';
												$commodity_name = pur_get_commodity_name($order_return_value['commodity_code']) != null ? pur_get_commodity_name($order_return_value['commodity_code'])->description : '';

												$unit_name = '';
												if(is_numeric($order_return_value['unit_id'])){
													$unit_name = get_unit_type_item($order_return_value['unit_id']) != null ? ' '.get_unit_type_item($order_return_value['unit_id'])->unit_name : '';
												}

												$commodity_name = $order_return_value['commodity_name'];
												if(strlen($commodity_name) == 0){
													$commodity_name = pur_get_item_variatiom($order_return_value['commodity_code']);
												}

												?>

												<tr>
													<td ><?php echo pur_html_entity_decode($delivery) ?></td>
													<td ><?php echo pur_html_entity_decode($commodity_name) ?></td>
													<td class="text-right"><?php echo pur_html_entity_decode($quantity).$unit_name ?></td>
													<td class="text-right"><?php echo app_format_money((float)$unit_price,'') ?></td>

													<?php echo  pur_render_taxes_html(pur_convert_item_taxes($order_return_value['tax_id'], $order_return_value['tax_rate'], $order_return_value['tax_name']), 15); ?>
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
													echo pur_html_entity_decode($tax_data['html_currency']);
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

												<?php
												$fee_for_return_order = $order_return->fee_return_order;
											
												?>
												<?php if($fee_for_return_order > 0){ ?>
													<tr id="fee_for_return_order" class="text-danger">
														<td class="bold"><?php echo _l('fee_for_return_order'); ?></td>
														<td><?php echo app_format_money((float)$fee_for_return_order, $base_currency); ?></td>
													</tr>
												<?php } ?>

												<tr id="totalmoney">
													<?php
													$total_after_discount = isset($order_return) ?  $order_return->total_after_discount : 0 ;
													if($fee_for_return_order > 0){ 
														$total_after_discount = $total_after_discount;
													}

													?>
													<td class="bold"><?php echo _l('total'); ?></td>
													<td><?php echo app_format_money((float)$total_after_discount, $base_currency); ?></td>
												</tr>
												<?php $refunded_amount = get_total_order_return_refunded($order_return->id); ?>
												<?php if($refunded_amount > 0){ ?>
													<tr id="totalrefund">
													
														<td class="bold"><?php echo _l('pur_total_refund'); ?></td>
														<td><?php echo app_format_money((float)$refunded_amount, $base_currency); ?></td>
													</tr>
													<?php if($refunded_amount < $total_after_discount){ ?>
														<tr id="amountdue" class="text-danger">
														<?php $amountdue = $total_after_discount - $refunded_amount; ?>
														<td class="bold"><?php echo _l('pur_amount_due'); ?></td>
														<td><?php echo app_format_money((float)$amountdue, $base_currency); ?></td>
													</tr>
													<?php } ?>
												<?php } ?>

											</tbody>
										</table>
									</div>

								</div>
							</div>

							                                        

								</div>

								<hr />
								<?php if($order_return->return_reason != ''){ ?>
									<div class="col-md-12 row mtop15">
										<p class="bold text-muted"><?php echo _l('pur_return_reason'); ?></p>
										<p><?php echo pur_html_entity_decode($order_return->return_reason); ?></p>
									</div>
								<?php } ?>
								
								<?php if($order_return->admin_note != ''){ ?>
									<div class="col-md-12 row mtop15">
										<p class="bold text-muted"><?php echo _l('admin_note'); ?></p>
										<p><?php echo pur_html_entity_decode($order_return->admin_note); ?></p>
									</div>
								<?php } ?>
								<?php if($order_return->return_policies_information != ''){ ?>
									<div class="col-md-12 row mtop15">
										<p class="bold text-muted"><?php echo _l('pur_return_policies_information'); ?></p>
										<p><?php echo pur_html_entity_decode($order_return->return_policies_information); ?></p>
									</div>
								<?php } ?>

							</div>
						</div>
						
						<div role="tabpanel" class="tab-pane" id="tab_refunds">
			               <?php if(count($order_return_refunds) == 0) {
			                  echo '<div class="alert alert-info no-mbot">';
			                  echo _l('not_refunds_found');
			                  echo '</div>';
			                  } else { ?>
			               <table class="table table-bordered no-mtop">
			                  <thead>
			                     <tr>
			                        <th><span class="bold"><?php echo _l('pur_date'); ?></span></th>
			                        <th><span class="bold"><?php echo _l('refund_amount'); ?></span></th>
			                        <th><span class="bold"><?php echo _l('payment_mode'); ?></span></th>
			                        <th><span class="bold"><?php echo _l('note'); ?></span></th>
			                     </tr>
			                  </thead>
			                  <tbody>
			                     <?php foreach($order_return_refunds as $refund) { ?>
			                     <tr>
			                        <td>
			                           <?php echo _d($refund['refunded_on']); ?>
			                        </td>
			                        <td>
			                           <?php echo app_format_money($refund['amount'], $base_currency); ?>
			                        </td>
			                        <td>
			                           <?php echo $refund['payment_mode_name']; ?>
			                        </td>
			                        <td>
			                           <?php if(has_permission('purchase_order_return','','delete')) { ?>
			                           <a href="<?php echo admin_url('purchase/delete_order_return_refund/'.$refund['id'].'/'.$refund['order_return_id']); ?>"
			                              class="pull-right text-danger _delete">
			                           <i class="fa fa-trash"></i>
			                           </a>
			                           <?php } ?>
			                           <?php if(has_permission('purchase_order_return','','edit')) { ?>
			                           <a href="#" onclick="edit_refund(<?php echo $refund['id']; ?>); return false;"
			                              class="pull-right mright5">
			                           <i class="fa fa-pencil-square"></i>
			                           </a>
			                           <?php } ?>
			                           <?php echo $refund['note']; ?>
			                        </td>
			                     </tr>
			                     <?php } ?>
			                  </tbody>
			               </table>
			               <?php }  ?>
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
									<button onclick="sign_request(<?php echo pur_html_entity_decode($order_return->id); ?>);" autocomplete="off" class="btn btn-success sign_request_class"><?php echo _l('e_signature_sign'); ?></button>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

		<?php require 'modules/purchase/assets/js/order_returns/view_order_return_js.php';?>
	</body>
	</html>