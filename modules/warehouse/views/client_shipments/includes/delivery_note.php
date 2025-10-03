<?php if(isset($goods_deliveries) && count($goods_deliveries) > 0){ ?>
	<div role="tabpanel" class="tab-pane active" id="delivery_note">
		<div class="panel_s no-shadow">

			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table items items-preview-delivery-note estimate-items-preview" data-type="estimate">
							<thead>
								<tr>
									<th  colspan="1"><?php echo _l('goods_delivery_code') ?></th>
									<th  colspan="1"><?php echo _l('customer_name') ?></th>
									<th align="right" colspan="1"><?php echo _l('subtotal') ?></th>
									<th align="right" colspan="1"><?php echo _l('total_discount') ?></th>
									<th align="right" colspan="1"><?php echo _l('total_money') ?></th>
									<th align="right" colspan="1"><?php echo _l('delivery_status') ?></th>
								</tr>
							</thead>
							<tbody class="ui-sortable">
								<?php 
								$subtotal = 0 ;
								foreach ($goods_deliveries as $key => $delivery_note) {
									$total_discount = 0 ;
									$total_discount += (float)$delivery_note['total_discount']  + (float)$delivery_note['additional_discount'];
									?>

									<tr>
										<td ><a href="<?php echo site_url('warehouse/warehouse_client/stock_export_pdf/'.$delivery_note['id'].'?output_type=I'); ?>" target="_blank" ><?php echo new_html_entity_decode($delivery_note['goods_delivery_code']) ?></a></td>
										<td ><?php echo get_company_name($delivery_note['customer_code']) ?></td>
										<td class="text-right"><?php echo app_format_money($delivery_note['sub_total'], '') ?></td>
										<td class="text-right"><?php echo app_format_money($total_discount, '') ?></td>
										<td class="text-right"><?php echo app_format_money($delivery_note['after_discount'], '') ?></td>
										<td class="text-right"><?php echo render_delivery_status_html($delivery_note['id'], 'delivery', $delivery_note['delivery_status'], false); ?></td>
									</tr>
								<?php  } ?>
							</tbody>
						</table>

					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>