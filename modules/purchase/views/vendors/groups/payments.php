<div class="col-md-12" id="small-table">
  <div class="row">
      <h4 class="no-margin font-bold"><i class="fa fa-file-text-o" aria-hidden="true"></i> <?php echo _l('payments'); ?></h4>
      <hr />
    </div>    
    <br>
    
    <table class="table dt-table">
       <thead>
        <th><?php echo _l('invoices'); ?></th>
         <th><?php echo _l('payments_table_amount_heading'); ?></th>
          <th><?php echo _l('payments_table_mode_heading'); ?></th>
          <th><?php echo _l('payment_transaction_id'); ?></th>
          <th><?php echo _l('payments_table_date_heading'); ?></th>
          <th><?php echo _l('options'); ?></th>
       </thead>
      <tbody>
         <?php foreach($payments as $p) { ?>
          <?php 
            $base_currency = get_base_currency_pur(); 
            $inv_currency = get_invoice_currency_id($p['pur_invoice']);

            if($inv_currency != 0){
              $base_currency = pur_get_currency_by_id($inv_currency);
            }
          ?>
          <tr>
          <td><a href="<?php echo admin_url('purchase/purchase_invoice/' . $p['pur_invoice']); ?>" ><?php echo get_pur_invoice_number($p['pur_invoice']); ?></a></td>
          <td><?php echo app_format_money($p['amount'],$base_currency->symbol); ?></td>
          <td><?php echo get_payment_mode_by_id($p['paymentmode']); ?></td>
          <td><?php echo pur_html_entity_decode($p['transactionid']); ?></td>
          <td><?php echo _d($p['date']); ?></td>
          <td>
            <?php if(has_permission('purchase_invoices','','edit') || is_admin()){ ?>
              <a href="<?php echo admin_url('purchase/payment_invoice/'.$p['id']); ?>" target="_blank" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="<?php echo _l('view'); ?>" ><i class="fa fa-eye "></i></a>
            <?php } ?>
          </td>
         </tr>
         <?php } ?>
      </tbody>
   </table> 
</div>
