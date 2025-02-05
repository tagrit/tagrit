<?php if(isset($goods_delivery) && count($goods_delivery) > 0){ ?>
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
									<th align="right" colspan="1"><?php echo _l('day_vouchers') ?></th>
									<th align="right" colspan="1"><?php echo _l('staff_id') ?></th>
									<th align="right" colspan="1"><?php echo _l('status_label') ?></th>
									<th align="right" colspan="1"><?php echo _l('delivery_status') ?></th>
								</tr>
							</thead>
							<tbody class="ui-sortable">
								<?php 
								$subtotal = 0 ;
								foreach ($goods_delivery as $key => $delivery_note) {
									$total_discount = 0 ;
									$total_discount += (float)$delivery_note['total_discount']  + (float)$delivery_note['additional_discount'];
									?>

									<tr>
										<td ><a href="<?php echo admin_url('warehouse/manage_delivery/' . $delivery_note['id'] ) ?>" ><?php echo new_html_entity_decode($delivery_note['goods_delivery_code']) ?></a></td>
										<td ><?php echo get_company_name($delivery_note['customer_code']) ?></td>
										<td class="text-right"><?php echo app_format_money($delivery_note['sub_total'], '') ?></td>
										<td class="text-right"><?php echo app_format_money($total_discount, '') ?></td>
										<td class="text-right"><?php echo app_format_money($delivery_note['after_discount'], '') ?></td>
										<td class="text-right"><?php echo _d($delivery_note['date_add']) ?></td>
										<td class="text-right">
											<a href="<?php echo admin_url('staff/profile/' . $delivery_note['staff_id']) ?>" ><?php echo staff_profile_image($delivery_note['staff_id'], [
												'staff-profile-image-small',
											]) ?></a>
											<a href="<?php echo admin_url('staff/profile/' . $delivery_note['staff_id'])  ?>" ><?php echo get_staff_full_name($delivery_note['staff_id']) ?></a>
										</td>
										<?php 
										$approve_data = '';
										if($delivery_note['approval'] == 1){
											$approve_data = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'._l('approved').'</span><span class="hide">, </span></span>&nbsp';
										}elseif($delivery_note['approval'] == 0){
											$approve_data = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'._l('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
										}elseif($delivery_note['approval'] == -1){
											$approve_data = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'._l('reject').'</span><span class="hide">, </span></span>&nbsp';
										}
										?>
										<td class="text-right"><?php echo new_html_entity_decode($approve_data); ?></td>
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