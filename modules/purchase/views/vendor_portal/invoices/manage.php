<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div class="row">
	
	<div class="col-md-12">
		<div class="panel_s">
			<div class="panel-body">
				<h4><?php echo pur_html_entity_decode($title) ?></h4>
				<hr class="mtop5">
				<a href="<?php echo site_url('purchase/vendors_portal/add_update_invoice'); ?>" class="btn btn-info"><?php echo _l('add_new'); ?></a>
				<br><br>

				<table class="table dt-table">
			       <thead>
			       	<th><?php echo _l('invoice_code'); ?></th>
			       	<th><?php echo _l('invoice_no'); ?></th>
			         <th><?php echo _l('contract'); ?></th>
			          <th><?php echo _l('pur_order'); ?></th>
			          <th><?php echo _l('invoice_date'); ?></th>
			          <th><?php echo _l('invoice_amount'); ?></th>
			          <th><?php echo _l('tax_value'); ?></th>
			          <th><?php echo _l('total_included_tax'); ?></th>
			          <th><?php echo _l('payment_status'); ?></th>
			          <th><?php echo _l('options'); ?></th>
			       </thead>
			      <tbody>
			         <?php foreach($invoices as $inv) { ?>
			         	<?php 
			         		$base_currency = get_base_currency_pur(); 
			         		if($inv['currency'] != 0){
			         			$base_currency = pur_get_currency_by_id($inv['currency']);
			         		}
			         	?>
			         <tr class="inv_tr">
			         	<td class="inv_tr"><a href="<?php echo site_url('purchase/vendors_portal/invoice/'.$inv['id']); ?>"><?php echo pur_html_entity_decode($inv['invoice_number']); ?></a></td>
			         	<td class="inv_tr"><?php 
			         		$vendor_invoice_number = ($inv['vendor_invoice_number'] != '' ? $inv['vendor_invoice_number'] : $inv['invoice_number']);
			         		echo pur_html_entity_decode($vendor_invoice_number); 
			         	?></td>
			         	<td class="inv_tr"><?php echo get_pur_contract_number($inv['contract'],''); ?></td>
			         	<td class="inv_tr"><?php echo get_pur_order_subject($inv['pur_order']); ?></td>
			         	<td class="inv_tr"><?php echo '<span class="label label-info">'._d($inv['invoice_date']).'</span>'; ?></td>
			         	<td class="inv_tr"><?php echo app_format_money($inv['subtotal'],$base_currency->symbol); ?></td>
			         	<td class="inv_tr"><?php echo app_format_money($inv['tax'],$base_currency->symbol); ?></td>
			         	<td class="inv_tr"><?php echo app_format_money($inv['total'],$base_currency->symbol); ?></td>
			         	<td class="inv_tr"><?php 
			         	$class = '';
			            if($inv['payment_status'] == 'unpaid'){
			                $class = 'danger';
			            }elseif($inv['payment_status'] == 'paid'){
			                $class = 'success';
			            }elseif ($inv['payment_status'] == 'partially_paid') {
			                $class = 'warning';
			            }

			            echo  '<span class="label label-'.$class.' s-status invoice-status-3">'._l($inv['payment_status']).'</span>';

			         	?>
			         	</td>
			         	<td>
			         		<a href="<?php echo site_url('purchase/vendors_portal/add_update_invoice/'.$inv['id']); ?>" class="btn btn-warning btn-icon"><i class="fa fa-pencil"></i></a>
			         		<a href="<?php echo site_url('purchase/vendors_portal/delete_invoice/'.$inv['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
			         	</td>
			         </tr>
			         <?php } ?>
			      </tbody>
			   </table>	
			</div>
		</div>
	</div>
</div>
<?php hooks()->do_action('app_admin_footer'); ?>