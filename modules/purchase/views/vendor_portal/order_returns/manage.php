<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div class="row">
	
	<div class="col-md-12">
		<div class="panel_s">
			<div class="panel-body">
				<h4><?php echo pur_html_entity_decode($title) ?></h4>
				<hr class="mtop5">
				<table class="table dt-table">
			       <thead>
			       	<th><?php echo '# '._l('pur_order_return_number'); ?></th>
			          <th><?php echo _l('total'); ?></th>
			          <th><?php echo _l('pur_datecreated'); ?></th>
			          <th><?php echo _l('pur_status'); ?></th>
			       </thead>
			      <tbody>
			         <?php foreach($order_returns as $or) { ?>
			         	<?php 
			         		$base_currency = get_base_currency_pur(); 
			         		if($or['currency'] != 0){
			         			$base_currency = pur_get_currency_by_id($or['currency']);
			         		}
			         	?>
			         <tr class="inv_tr">
			         	<td class="inv_tr"><a href="<?php echo site_url('purchase/vendors_portal/order_return/'.$or['id']); ?>"><?php echo pur_html_entity_decode($or['order_return_name']); ?></a></td>
			         	<td><?php echo app_format_money($or['total_after_discount'], $base_currency); ?></td>
			         	<td><?php echo _dt($or['datecreated']); ?></td>
			         	<td><span class="label label-success"><?php echo _l('pur_'.$or['status']); ?></span></td>
			         </tr>
			         <?php } ?>
			      </tbody>
			   </table>	
			</div>
		</div>
	</div>
</div>
<?php hooks()->do_action('app_admin_footer'); ?>