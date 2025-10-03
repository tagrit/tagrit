<div class="modal fade z-index-none" id="appointmentModal">
	<div class="modal-dialog setting-handsome-table">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

				<h4 class="modal-title"><?php echo new_html_entity_decode($title); ?></h4>
			</div>
			<?php echo form_open(admin_url('warehouse/add_opening_stock'), array('id' => 'add_opening_stock', 'autocomplete'=>'off')); ?>
			<div class="modal-body">

				<div class="row">
					<div class="col-md-12">
						<?php if(count($serial_number_data) > 0){ ?>
						<table class="table dt-table border table-striped">
							<thead>
								<th><?php echo _l('_order'); ?></th>
								<th><?php echo _l('wh_transaction_name'); ?></th>
								<th><?php echo _l('wh_transaction_type'); ?></th>
								<th><?php echo _l('datecreated'); ?></th>
							</thead>
							<tbody>
								<?php foreach($serial_number_data as $key => $serial_number_value){ ?>
									
									<tr>
										<td>
											<?php echo html_entity_decode($key+1); ?>
										</td>
										<?php if($serial_number_value['rel_type'] == 'receipt_note'){ ?>
											<td>
												<a href="<?php echo admin_url('warehouse/manage_purchase/' . $serial_number_value['rel_id']) ?>" target="_blank" ><?php echo new_html_entity_decode($serial_number_value['name']); ?></a>

											</td>
											<td><?php echo _l('stock_import'); ?></td>
										<?php }elseif($serial_number_value['rel_type'] == 'delivery_note'){ ?>
											<td>
												<a href="<?php echo admin_url('warehouse/manage_delivery/' . $serial_number_value['rel_id']) ?>" target="_blank" ><?php echo new_html_entity_decode($serial_number_value['name']); ?></a>

											</td>
											<td><?php echo _l('stock_export'); ?></td>
										<?php }elseif($serial_number_value['rel_type'] == 'packing_list'){ ?>
											<td>
												<a href="<?php echo  admin_url('warehouse/manage_packing_list/' . $serial_number_value['rel_id']) ?>" target="_blank" ><?php echo new_html_entity_decode($serial_number_value['name']); ?></a>

											</td>
											<td><?php echo _l('wh_packing_list'); ?></td>
										<?php }elseif($serial_number_value['rel_type'] == 'loss_adjustment'){ ?>
											<td>
												<a href="<?php echo  admin_url('warehouse/view_lost_adjustment/' . $serial_number_value['rel_id']) ?>" target="_blank" ><?php echo new_html_entity_decode($serial_number_value['name']); ?></a>

											</td>
											<td><?php echo _l('lost_adjustment'); ?></td>
										<?php }elseif($serial_number_value['rel_type'] == 'internal_delivery'){ ?>
											<td>
												<a href="<?php  echo  admin_url('warehouse/manage_internal_delivery/' . $serial_number_value['rel_id']) ?>" target="_blank" ><?php echo new_html_entity_decode($serial_number_value['name']); ?></a>

											</td>
											<td><?php echo _l('internal_delivery_note'); ?></td>
										<?php } ?>
										<td><?php echo _d(date('Y-m-d', strtotime($serial_number_value['date_created']))); ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php }else{ ?>
						<h5 class="add_opening_stock"><?php echo _l('wh_No_data_related_to_serial_number_found'); ?></h5>

					<?php } ?>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php echo _l('close'); ?></button>
			</div>

		</div>

		<?php echo form_close(); ?>
	</div>
</div>
</div>
