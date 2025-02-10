<div id="accordion">
  <div class="card">
    <table class="tree">
      <tbody>
        <tr>
          <td colspan="2">
              <h3 class="text-center no-margin-top-20 no-margin-left-24"><?php echo get_option('companyname'); ?></h3>
          </td>
          <td></td>
        </tr>
        <tr>
          <td colspan="2">
            <h4 class="text-center no-margin-top-20 no-margin-left-24"><?php echo _l('bank_reconciliation_summary'); ?></h4>
          </td>
          <td></td>
        </tr>
        <tr>
          <td colspan="2">
            <p class="text-center no-margin-top-20 no-margin-left-24"><?php echo $data_report['account_name'] .', '._l('period_ending').' '._d($data_report['statement_ending_date']); ?></p>
          </td>
          <td></td>
        </tr>
        <tr>
          <td>
          </td>
          <td></td>
        </tr>
        <tr class="tr_header">
          <td></td>
          <td class="th_total text-bold"><?php echo _d($data_report['statement_ending_date']); ?></td>
        </tr>
        <?php
          $row_index = 0;
          $parent_index = 0;
          $row_index += 1;
          $parent_index = $row_index;
          if($data_report['statement_ending_date'] != ''){
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent text-bold"><?php echo _l('beginning_balance'); ?></td>
            <td class="total_amount text-bold"><?php echo app_format_money($data_report['beginning_balance'], $currency->name); ?></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('cleared_transactions'); ?></td>
            <td class="total_amount"></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('checks_and_payments'); ?> - <?php echo $data_report['checks_and_payments_items']; ?></td>
            <td class="total_amount"><?php echo app_format_money($data_report['checks_and_payments'], $currency->name); ?></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('deposits_and_credits'); ?> - <?php echo $data_report['deposits_and_credits_items']; ?></td>
            <td class="total_amount"><?php echo app_format_money($data_report['deposits_and_credits'], $currency->name); ?></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent text-bold">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('total_for', _l('cleared_transactions')); ?></td>
            <td class="total_amount text-bold"><?php echo app_format_money($data_report['cleared_transactions'], $currency->name); ?></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent"></td>
            <td class="total_amount"></td>
          </tr>
          <?php $row_index += 1; ?>

          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent text-bold"><?php echo _l('cleared_balance'); ?></td>
            <td class="total_amount text-bold"><?php echo app_format_money( ($data_report['cleared_transactions'] + ($data_report['beginning_balance']) ), $currency->name); ?></td>
          </tr>

           <?php $row_index += 1; ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent"></td>
            <td class="total_amount"></td>
          </tr>

            <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('uncleared_transactions'); ?></td>
            <td class="total_amount"></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('checks_and_payments'); ?> - <?php echo $data_report['unclear_checks_and_payments_items']; ?></td>
            <td class="total_amount"><?php echo app_format_money($data_report['unclear_checks_and_payments'], $currency->name); ?></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('deposits_and_credits'); ?> - <?php echo $data_report['unclear_deposits_and_credits_items']; ?></td>
            <td class="total_amount"><?php echo app_format_money($data_report['unclear_deposits_and_credits'], $currency->name); ?></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent text-bold">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('total_for', _l('uncleared_transactions')); ?></td>
            <td class="total_amount text-bold"><?php echo app_format_money($data_report['uncleared_transactions'], $currency->name); ?></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent"></td>
            <td class="total_amount"></td>
          </tr>
         
          <?php $row_index += 1; ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent"></td>
            <td class="total_amount"></td>
          </tr>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent text-bold"><?php echo _l('register_balance_as_of', _d($data_report['statement_ending_date'])); ?></td>
            <td class="total_amount text-bold"><?php echo app_format_money(($data_report['uncleared_transactions'] + ($data_report['beginning_balance']) + ($data_report['cleared_transactions']) ), $currency->name); ?></td>
          </tr>
          <?php $row_index += 1; ?>
          <?php if($data_report['new_checks_and_payments'] != 0 || $data_report['new_deposits_and_credits'] != 0){ ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('new_transactions'); ?></td>
            <td class="total_amount"></td>
          </tr>
          <?php $row_index += 1; ?>
          <?php if($data_report['new_checks_and_payments'] != 0){ ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('checks_and_payments'); ?> - <?php echo $data_report['new_checks_and_payments_items']; ?></td>
            <td class="total_amount"><?php echo app_format_money($data_report['new_checks_and_payments'], $currency->name); ?></td>
          </tr>
          <?php } ?>
          <?php $row_index += 1; ?>
          <?php if($data_report['new_deposits_and_credits'] != 0){ ?>
            <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
              <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('deposits_and_credits'); ?> - <?php echo $data_report['new_deposits_and_credits_items']; ?></td>
              <td class="total_amount"><?php echo app_format_money($data_report['new_deposits_and_credits'], $currency->name); ?></td>
            </tr>
          <?php } ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent text-bold">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('total_for', _l('new_transactions')); ?></td>
            <td class="total_amount text-bold"><?php echo app_format_money($data_report['new_transactions'], $currency->name); ?></td>
          </tr>
          <?php } ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent"></td>
            <td class="total_amount"></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent text-bold"><?php echo _l('ending_balance'); ?></td>
            <td class="total_amount text-bold"><?php echo app_format_money($data_report['ending_balance'], $currency->name); ?></td>
          </tr>
        <?php } ?>
        </tbody>
    </table>
  </div>
</div>