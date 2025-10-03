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
            <h4 class="text-center no-margin-top-20 no-margin-left-24"><?php echo _l('transaction_list_by_date'); ?></h4>
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
          <td class="text-bold"><?php echo _l('description'); ?></td>
          <td class="text-bold"><?php echo _l('acc_account'); ?></td>
          <td class="text-bold"><?php echo _l('split'); ?></td>
          <td class="total_amount text-bold"><?php echo _l('acc_amount'); ?></td>
        </tr>
        <?php
         $row_index = 0; 
         $parent_index = 0; 
         $total_debit = 0; 
         $total_credit = 0; 
         ?>

         <?php foreach ($data_report['data'] as $val) { 
              $row_index += 1;
              $total_debit += $val['debit'];
              $total_credit += $val['credit'];
            ?>
            <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-10000">
              <td>
                <?php $url = get_url_by_type_id($val['rel_type'], $val['rel_id']); ?>
              <a href="<?php echo new_html_entity_decode($url); ?>" class="text-default-bl"><?php echo _d($val['date']); ?></a> 
              </td>
              <td>
              <?php echo new_html_entity_decode($val['type']); ?> 
              </td>
              <td>
              <?php echo new_html_entity_decode($val['description']); ?> 
              </td>
              <td>
              <?php echo new_html_entity_decode($val['name']); ?> 
              </td>
              <td>
              <?php echo new_html_entity_decode($val['split']); ?> 
              </td>
              <td class="total_amount">
              <?php echo app_format_money($val['amount'], $currency->name); ?> 
              </td>
            </tr>
          <?php }
            $row_index += 1;
           ?>
      </tbody>
    </table>
  </div>
</div>