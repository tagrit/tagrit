<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-12">
		<?php $pur_order = get_pur_order_by_client($client->userid) ?>
		<table class="table dt-table" >
        <thead>
           <tr>
              <th ><?php echo _l('purchase_order'); ?></th>
              <th ><?php echo _l('po_value'); ?></th>
              <th ><?php echo _l('tax_value'); ?></th>
              <th ><?php echo _l('po_value_included_tax'); ?></th>
              <th ><?php echo _l('order_date'); ?></th>
           </tr>
        </thead>
        <tbody>
          <?php if(count($pur_order) > 0){ ?>
          <?php foreach($pur_order as $p){ ?>
            <tr>
              <td><a href="<?php echo admin_url('purchase/purchase_order/'.$p['id']); ?>"><?php echo pur_html_entity_decode($p['pur_order_number'].' - '.$p['pur_order_name']); ?></a></td>
              <td><?php echo pur_html_entity_decode(app_format_money($p['subtotal'],'')); ?></td>
              <td><?php echo pur_html_entity_decode(app_format_money($p['total_tax'],'')); ?></td>
              <td><?php echo pur_html_entity_decode(app_format_money($p['total'],'')); ?></td>
              <td>
                <span class="label label-primary"><?php echo pur_html_entity_decode(_d($p['order_date'])); ?></span>
              </td>
              
            </tr>
          <?php } ?>
        <?php } ?>
        </tbody>
     </table>
	</div>
</div>