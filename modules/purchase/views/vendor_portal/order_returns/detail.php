<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<?php if($order_return->currency != 0){
  $base_currency = pur_get_currency_by_id($order_return->currency);
}
 ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      
      <div class="col-md-12">
        <div class="panel_s accounting-template estimate">
        <div class="panel-body">

             <div class="ribbon success"><span><?php echo _l('pur_'.$order_return->status); ?></span></div>
          
          
       
            <div class="horizontal-scrollable-tabs preview-tabs-top">
            
            <div class="horizontal-tabs">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="<?php if($this->input->get('tab') != 'discussion' && $this->input->get('tab') != 'attachment'){echo 'active';} ?>">
                     <a href="#general_infor" aria-controls="general_infor" role="tab" data-toggle="tab">
                     <?php echo _l('general_infor'); ?>
                     </a>
                  </li>

               </ul>
            </div>
         </div>
          <div class="tab-content">
             <div role="tabpanel" class="tab-pane ptop10 active" id="general_infor">
              <div class="row">
                <?php if($order_return->status == 'draft' ){ ?>
                  <div class="col-md-12">
                  	<a href="javascript:void(0)" onclick="reject_order(this); return false;" class="btn btn-danger pull-right mbot10" data-order_id="<?php echo pur_html_entity_decode($order_return->id); ?>" ><?php echo _l('reject'); ?></a>

                    <a href="javascript:void(0)" onclick="confirm_order(this); return false;" class="btn btn-info pull-right mbot10 mright5" data-order_id="<?php echo pur_html_entity_decode($order_return->id); ?>" ><?php echo _l('confirm'); ?></a>

                    </div>
                  <?php } ?>
             
               <div class="col-md-6">
                  
                  <table class="table table-striped table-bordered">
                    <tr>
                      <td width="30%"><?php echo _l('pur_order_return_number'); ?></td>
                      <td><?php echo pur_html_entity_decode($order_return->order_return_name) ?></td>
                    </tr>
                    <tr>
                      <td><?php echo _l('status'); ?></td>
                      <td><?php echo _l('pur_'.$order_return->status) ?></td>
                    </tr>
                  </table>
               </div>
               <div class="col-md-6">
                  <table class="table table-striped table-bordered">
                    <tr>
                      <td width="30%"><?php echo _l('order_date'); ?></td>
                      <td><?php echo _d($order_return->order_date) ?></td>
                    </tr>
                  
                    <tr>
                      <td><?php echo _l('total'); ?></td>
                      <td><?php echo app_format_money($order_return->total_after_discount,'') ?></td>
                    </tr>
                  </table>
               </div>
               </div>
            </div>

           
          </div>
          
        </div>
        <div class="panel-body mtop10">
        <div class="row">
        <div class="col-md-12">
          <div class="table-responsive">
             <table class="table items items-preview estimate-items-preview" data-type="estimate">
				<thead>
					<tr>
						<th align="center">#</th>
						<th  colspan="1"><?php echo _l('commodity_code') ?></th>
						<th align="right" colspan="1"><?php echo _l('quantity') ?></th>
						<th align="right" colspan="1"><?php echo _l('unit_price') ?></th>
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
          </div>
         <div class="col-md-8 col-md-offset-4">
			<table class="table text-right">
				<tbody>
					<tr id="subtotal">
						<td class="bold"><?php echo _l('subtotal'); ?></td>
						<td><?php echo app_format_money((float)$order_return->subtotal, $base_currency); ?></td>
					</tr>

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
        </div>
      
        </div>

      </div>
    
      
    </div>
  </div>
</div>
</div>
<?php hooks()->do_action('app_admin_footer'); ?>
</body>
</html>
<?php require 'modules/purchase/assets/js/order_returns/order_return_vendor_js.php';?>
