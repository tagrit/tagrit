<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
	<?php $base_currency = get_base_currency_pur(); ?>
	<div class="col-md-12">
		<h3 id="greeting" class="no-mtop"></h3>
		
			<div class="panel_s">
				<div class="panel-body">
					<h3 class="text-success projects-summary-heading no-mtop mbot15"><?php echo _l('summary'); ?></h3>
					<div class="row">
						<?php get_template_part_pur('_summary'); ?>
					</div>
				</div>
			</div>
	
		<div class="panel_s">
			<div class="panel-body">
				<table class="table dt-table" >
		            <thead>
		               <tr>
		                  <th ><?php echo _l('purchase_order'); ?></th>
		                  <th ><?php echo _l('po_value'); ?></th>
		                  <th ><?php echo _l('tax_value'); ?></th>
		                  <th ><?php echo _l('po_value_included_tax'); ?></th>
		                  <th ><?php echo _l('order_date'); ?></th>
		                 
		            </thead>
		            <tbody>
		            	<?php foreach($pur_order as $p){

		            		if($p['currency'] != 0){
					            $base_currency = pur_get_currency_by_id($p['currency']);
					        }

		            	 ?>
		            		<tr>
		            			<td><a href="<?php echo site_url('purchase/vendors_portal/pur_order/'.$p['id']); ?>"><?php echo pur_html_entity_decode($p['pur_order_number'].' - '.$p['pur_order_name']); ?></a></td>
		            			<td><?php echo pur_html_entity_decode(app_format_money($p['subtotal'],$base_currency->symbol)); ?></td>
		            			<td><?php echo pur_html_entity_decode(app_format_money($p['total_tax'],$base_currency->symbol)); ?></td>
		            			<td><?php echo pur_html_entity_decode(app_format_money($p['total'],$base_currency->symbol)); ?></td>
		            			<td><span class="label label-primary"><?php echo pur_html_entity_decode(_d($p['order_date'])); ?></span></td>
		            		</tr>
		            	<?php } ?>
		            </tbody>
		         </table>
			</div>
		</div>
	</div>
</div>
<?php require 'modules/purchase/assets/js/home_js.php';?>