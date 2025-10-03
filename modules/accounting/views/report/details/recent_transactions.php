<div id="accordion">
  <div class="card">
    <table class="tree">
      <tbody>
        <tr>
          <td colspan="6">
              <h3 class="text-center no-margin-top-20 no-margin-left-24"><?php echo get_option('companyname'); ?></h3>
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="6">
            <h4 class="text-center no-margin-top-20 no-margin-left-24"><?php echo _l('recent_transactions'); ?></h4>
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="6">
            <p class="text-center no-margin-top-20 no-margin-left-24"><?php echo _d($data_report['from_date']) .' - '. _d($data_report['to_date']); ?></p>
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr class="tr_header">
          <td class="text-bold"><?php echo _l('invoice_payments_table_date_heading'); ?></td>
          <td class="text-bold"><?php echo _l('transaction_type'); ?></td>
          <td class="text-bold"><?php echo _l('customer'); ?></td>
          <td class="text-bold"><?php echo _l('description'); ?></td>
          <td class="text-bold"><?php echo _l('acc_account'); ?></td>
          <td class="th_total text-bold"><?php echo _l('acc_amount'); ?></td>
        </tr>
        <?php
         $row_index = 1; 
         $parent_index = 1; 
         $total_amount = 0; 
         ?>

          <?php foreach ($data_report['data'] as $key => $value) { ?>
            <?php
         $row_index += 1; 
         $parent_index = $row_index; 
         $total_amount = 0; 
         ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l($key); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
         <?php foreach ($value as $val) { ?>
              <?php 
              $row_index += 1;
              $total_amount += $val['amount'];
            ?>
            <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> ">
              <td>
                <?php $url = get_url_by_type_id($val['rel_type'], $val['rel_id']); ?>
              <a href="<?php echo html_entity_decode($url); ?>" class="text-default-bl"><?php echo _d($val['date']); ?> </a>
              </td>
              <td>
              <?php echo new_html_entity_decode($val['type']); ?> 
              </td>
              <td>
              <?php echo get_company_name($val['customer']); ?> 
              </td>
              <td>
              <?php echo new_html_entity_decode($val['description']); ?> 
              </td>
              <td>
              <?php echo new_html_entity_decode($val['name']); ?> 
              </td>
              <td class="total_amount">
              <?php echo app_format_money($val['amount'], $currency->name); ?> 
              </td>
            </tr>
          <?php }
            $row_index += 1;
           ?>
           <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_for', _l($key)); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total_amount, $currency->name); ?> </td>
          </tr>
          <?php } ?> 
          
      </tbody>
    </table>
  </div>
</div>