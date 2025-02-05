<?php if(isset($packing_lists) && count($packing_lists) > 0){ ?>
	<div role="tabpanel" class="tab-pane" id="packing_list">
		<div class="panel_s no-shadow">

			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table items items-preview estimate-items-preview" data-type="estimate">
							<thead>
								<tr>
									<th  colspan="1"><?php echo _l('packing_list_number') ?></th>
									<th  colspan="1"><?php echo _l('customer_name') ?></th>
									<th align="right" colspan="1"><?php echo _l('wh_dimension') ?></th>
									<th align="right" colspan="1"><?php echo _l('volume_m3_label') ?></th>
									<th align="right" colspan="1"><?php echo _l('total_amount') ?></th>
									<th align="right" colspan="1"><?php echo _l('discount_total') ?></th>
									<th align="right" colspan="1"><?php echo _l('total_after_discount') ?></th>
									<th align="right" colspan="1"><?php echo _l('datecreated') ?></th>
									<th align="right" colspan="1"><?php echo _l('delivery_status') ?></th>
								</tr>
							</thead>
							<tbody class="ui-sortable">
								<?php 
								$subtotal = 0 ;
								foreach ($packing_lists as $key => $packing_list) {
									?>

									<tr>
										<td ><a href="<?php echo site_url('warehouse/warehouse_client/client_packing_list_pdf/'.$packing_list['id'].'?output_type=I'); ?>" target="_blank"><?php echo new_html_entity_decode($packing_list['packing_list_number'] .' - '.$packing_list['packing_list_name']) ?></a></td>
										<td ><?php echo get_company_name($packing_list['clientid']) ?></td>
										<td class="text-right"><?php echo new_html_entity_decode($packing_list['width'].' x '.$packing_list['height'].' x '.$packing_list['lenght']) ?></td>
										<td class="text-right"><?php echo app_format_money($packing_list['volume'], '') ?></td>
										<td class="text-right"><?php echo app_format_money($packing_list['total_amount'], '') ?></td>
										<td class="text-right"><?php echo app_format_money($packing_list['discount_total']+$packing_list['additional_discount'], '') ?></td>
										<td class="text-right"><?php echo app_format_money($packing_list['total_after_discount'], '') ?></td>
										<td class="text-right"><?php echo _dt($packing_list['datecreated']) ?></td>
										
										<td class="text-right"><?php echo render_delivery_status_html($packing_list['id'], 'packing_list', $packing_list['delivery_status'], false) ?></td>
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