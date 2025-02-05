<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12" id="small-table">
  <div class="row">
      <div class="col-md-4"><h4 class="no-margin font-bold"><i class="fa fa-file-text-o" aria-hidden="true"></i> <?php echo _l('expenses'); ?></h4></div>
       <?php if(is_admin() || has_permission('expenses', '', 'create')){ ?>
      <div class="col-md-8"><a href="<?php echo admin_url('expenses/expense'); ?>" class="btn btn-info pull-right"><?php echo _l('new_expense'); ?></a></div>
    <?php } 
    $base_currency = get_base_currency_pur();
    ?>
    <div class="col-md-12">
       <hr />
    </div>
     
    </div>    

    <table class="table dt-table">
       <thead>
        <th><?php echo _l('category'); ?></th>
         <th><?php echo _l('payments_table_amount_heading'); ?></th>
          <th><?php echo _l('name'); ?></th>
          <th><?php echo _l('date'); ?></th>
          <th><?php echo _l('payment_mode'); ?></th>
       </thead>
      <tbody>
         <?php foreach($expenses as $p) { ?>
          <tr>
          <td><a href="<?php echo admin_url('expenses/list_expenses/' . $p['id']); ?>" ><?php echo pur_html_entity_decode($p['category_name']); ?></a></td>
          <td><?php echo app_format_money($p['amount'],$base_currency->symbol); ?></td>
          <td><?php echo pur_html_entity_decode($p['expense_name']); ?></td>
          <td><?php echo _d($p['date']); ?></td>
          <td><?php echo pur_html_entity_decode($p['paymentmode']); ?></td>
         </tr>
         <?php } ?>
      </tbody>
   </table> 
</div>
