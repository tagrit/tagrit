<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php hooks()->do_action('app_customers_portal_head'); ?>
<div class="panel_s">
	<div class="panel-body">
		<div class="row">

			<div class="col-md-12">

				<div class="card">
					<div class="row">
						<div class="col-md-12">
							<div class="card-header pb-0">
								<div class="row">
									<div class="col-md-6">
										<h4><?php  echo new_html_entity_decode(_l('wh_shipment_tooltip')); ?></h4>
									</div>

									<div class="col-md-6 ">

										<a href="<?php echo site_url('warehouse/warehouse_client/client_update_shipment_status/product_delivered/'.$shipment->id); ?>" class="btn btn-info pull-right ml-2 <?php if($shipment_staus_order == 4){ echo '';}else{ echo ' hide';} ?> "><?php echo _l('product_delivered'); ?></a>
										<h4 class="pull-right mright5"><?php  echo new_html_entity_decode( $shipment->shipment_number); ?></h4>&nbsp;

									</div>
								</div>
							</div>
						</div>
						<div class="col-md-1">
							<!-- <img class="img img-thumbnail pull-right mt-3 mr-4" width="80px" src=""> -->
						</div>
					</div>
					<hr class="no-margin">

					<div class="card-block">

						<div class="row">
							<div class="col-md-12 padding-bottom-3x mb-1">
								<div class="card mb-3">
									<div class="p-4 text-center text-white text-lg bg-dark rounded-top"><span class="text-uppercase"><?php echo _l('wh_shipment_number'); ?> - </span><span class="text-medium"><?php echo new_html_entity_decode($shipment->shipment_number); ?></span></div>
									<div class="d-flex flex-wrap flex-sm-nowrap justify-content-between py-3 px-2 bg-secondary">
										<div class="w-100 text-center py-1 px-2"><span class="text-medium"></span></div>
										<div class="w-100 text-center py-1 px-2"><span class="text-medium">Status: </span><?php echo _l($shipment->shipment_status); ?></div>
										<div class="w-100 text-center py-1 px-2"><span class="text-medium"></span></div>
									</div>

									<div class="card-body">
										<div class="steps d-flex flex-wrap flex-sm-nowrap justify-content-between padding-top-2x padding-bottom-1x">
											<div class="step <?php echo new_html_entity_decode($confirmed_order); ?>">
												<div class="step-icon-wrap">
													<div class="step-icon"><i class="fa fa-cart-arrow-down mtop-18"></i></div>
												</div>
												<h4 class="step-title"><?php echo _l('confirmed_order'); ?></h4>
											</div>
											<div class="step <?php echo new_html_entity_decode($processing_order); ?>">
												<div class="step-icon-wrap">
													<div class="step-icon"><i class="fa fa-gear mtop-18"></i></div>
												</div>
												<h4 class="step-title"><?php echo _l('processing_order'); ?></h4>
											</div>
											<div class="step <?php echo new_html_entity_decode($quality_check); ?>">
												<div class="step-icon-wrap">
													<div class="step-icon"><i class="fa fa-edit mtop-18"></i></div>
												</div>
												<h4 class="step-title"><?php echo _l('quality_check'); ?></h4>
											</div>
											<div class="step <?php echo new_html_entity_decode($product_dispatched); ?>">
												<div class="step-icon-wrap">
													<div class="step-icon"><i class="fa fa-car mtop-18"></i></div>
												</div>
												<h4 class="step-title"><?php echo _l('product_dispatched'); ?></h4>
											</div>
											<div class="step <?php echo new_html_entity_decode($product_delivered); ?>">
												<div class="step-icon-wrap">
													<div class="step-icon"><i class="fa fa-home mtop-18"></i></div>
												</div>
												<h4 class="step-title"><?php echo _l('product_delivered'); ?></h4>
											</div>
										</div>
									</div>
								</div>

							</div>
						</div>
						<hr class="no-mtop">
						
						<div class="row">
							<div class="col-md-7">

								<div class="row">
									<div class="col-md-6 col-sm-6">
										<h4 class="bold">
											<span id="invoice-number">
												<?php echo new_html_entity_decode($shipment->shipment_number); ?>
											</span>
										</h4>
										<address>
											<?php echo format_organization_info(); ?>
										</address>
										<?php if(isset($invoices)){ ?>
											
											<p class="no-mbot">
												<span class="bold">
													<?php echo _l('invoices'); ?>
													<a href="<?php echo site_url('invoice/'.$invoices->id.'/'.$invoices->hash) ?>" ><?php echo format_invoice_number($goods_delivery->invoice_id); ?></a>
												</span>
												<h5 class="bold">
												</h5>
											</p>
										<?php } ?>
									</div>

									<div class="col-md-6 col-sm-6 text-right">
										<?php if(isset($get_client)){ ?>
											<span class="bold"><?php echo _l('invoice_bill_to'); ?>:</span>
											<address class="invoice-html-customer-shipping-info">
												<b><?php echo new_html_entity_decode($get_client->company); ?></b>
												<br>
												<?php echo isset($get_client) ? $get_client->billing_street : ''; ?>
												<br><?php echo isset($get_client) ? $get_client->billing_city : ''; ?> <?php echo isset($get_client) ? $get_client->billing_state : ''; ?>
												<br><?php echo isset($get_client) ? get_country_short_name($get_client->billing_country) : ''; ?> <?php echo isset($get_client) ? $get_client->billing_zip : ''; ?>
											</address>
											<span class="bold"><?php echo _l('ship_to'); ?>:</span>
											<address class="invoice-html-customer-shipping-info">
												<?php echo isset($get_client) ? $get_client->shipping_street : ''; ?>
												<br><?php echo isset($get_client) ? $get_client->shipping_city : ''; ?> <?php echo isset($get_client) ? $get_client->shipping_state : ''; ?>
												<br><?php echo isset($get_client) ? get_country_short_name($get_client->shipping_country) : ''; ?> <?php echo isset($get_client) ? $get_client->shipping_zip : ''; ?>
											</address>
										<?php } ?>
										<p class="no-mbot">
											<span class="bold">
												<?php echo _l('order_date'); ?>
											</span>
											<?php echo _dt($shipment->datecreated); ?>
										</p>
									</div>

									<?php if(new_strlen($goods_delivery->description ?? '') > 0){ ?>
										<div class="col-md-12">
											<p class="no-mbot">
												<span class="bold">
													<?php echo _l('admin_note'); ?>
												</span>
												<?php echo new_html_entity_decode($goods_delivery->description); ?>
											</p>
										</div>
									<?php } ?>

									<div class="col-md-12">
										<!-- Nav tabs -->
										<ul class="nav nav-tabs  tabs" role="tablist">
											<?php if(isset($goods_deliveries) && count($goods_deliveries) > 0){ ?>
												<li class="nav-item active">
													<a class="nav-link" data-toggle="tab" href="#delivery_note" role="tab" aria-selected="false"><strong><?php echo _l('goods_delivery'); ?></strong></a>
												</li>
											<?php } ?>
											<?php if(isset($packing_lists) && count($packing_lists) > 0){ ?>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#packing_list" role="tab" aria-selected="false"><strong><?php echo _l('wh_packing_list'); ?></strong></a>
												</li>
											<?php } ?>

										</ul>
										<!-- Tab panes -->
										<div class="tab-content tabs mt-4">
											<?php echo  $this->load->view('warehouse/client_shipments/includes/delivery_note'); ?>
											<?php echo  $this->load->view('warehouse/client_shipments/includes/packing_list'); ?>
										</div>
									</div>

								</div>

							</div>

							<div class="col-md-5">
								<div class="activity-feed pr-0">
									<?php $key = 0; ?>
									<?php foreach ($arr_activity_logs as $date => $activity_log) { ?>
										<div class="feed-item ">
											<div class="date <?php if($key == 0){ echo ' text-info';} ?>">
												<div class="row">
													<div class="col-md-8 col-sm-8">
														<span class="text-has-action <?php if($key == 0){ echo ' text-info';} ?>" ><?php echo _dt($activity_log['date']).' '; ?> </span>
														<span class="text-has-action <?php if($key == 0){ echo ' text-info';} ?>" ><?php echo new_html_entity_decode($activity_log['description']).' '; ?></span>
														
														<?php if($activity_log['rel_type'] == 'shipment' && (is_admin() || has_permission('warehouse', '', 'delete') ) ){ ?>
														</div>

														<div class="col-md-4 col-sm-4">

														
														<?php } ?>
														
													</div>
												</div>

											</div>

										</div>
										<?php $key++; ?>
									<?php } ?>

								</div>
							</div>

						</div>
					</div>
				</div>

			</div>
		</div>

	</div>

	<div class="row">
		<div class="col-md-12 ">
			<div class="panel-body bottom-transaction">
				<div class="btn-bottom-toolbar text-right">
					<a href="<?php echo site_url('warehouse/warehouse_client/shipments'); ?>"class="btn btn-info text-right"><?php echo _l('close'); ?></a>
				</div>
			</div>
			<div class="btn-bottom-pusher"></div>
		</div>
	</div>

</div>
<div id="modal_wrapper"></div>

<?php hooks()->do_action('app_customers_portal_footer'); ?>


<?php require 'modules/warehouse/assets/js/shipments/shipment_detail_js.php';?>

