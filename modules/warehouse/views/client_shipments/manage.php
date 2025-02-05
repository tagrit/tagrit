<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s section-heading section-invoices">
	<div class="panel-body">
		<h4 class="no-margin section-text"><?php echo _l('wh_shipments'); ?></h4>
	</div>
</div>
<div class="panel_s">
	<div class="panel-body">
		<!-- <?php get_template_part('invoices_stats'); ?> -->
		<hr />
		<table class="table dt-table table-invoices" data-order-col="1" data-order-type="desc">
			<thead>
				<tr>
					<th class="th-invoice-number"><?php echo _l('wh_shipment_number'); ?></th>
					<th class="th-invoice-date"><?php echo _l('datecreated'); ?></th>
					<th class="th-invoice-duedate"><?php echo _l('status_label'); ?></th>
					
				</tr>
			</thead>
			<tbody>
				<?php foreach($shipments as $shipment){ ?>
					<tr>
						<?php if($shipment['shipment_hash'] != null && new_strlen($shipment['shipment_hash'] ?? '') > 0){ ?>
							<td data-order="<?php echo $shipment['shipment_number']; ?>"><a href="<?php echo site_url('warehouse/warehouse_client/shipment_detail_hash/' . $shipment['shipment_hash']); ?>" class="invoice-number"><?php echo $shipment['shipment_number']; ?></a></td>
						<?php }else{ ?>
							<td data-order="<?php echo $shipment['shipment_number']; ?>"><a href="<?php echo site_url('warehouse/warehouse_client/shipment_detail/' . $shipment['goods_delivery_id']); ?>" class="invoice-number"><?php echo $shipment['shipment_number']; ?></a></td>
						<?php } ?>

						<td data-order="<?php echo $shipment['datecreated']; ?>"><?php echo _dt($shipment['datecreated']); ?></td>
						<td><?php echo format_shipment_status($shipment['shipment_status'], 'inline-block', true); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
