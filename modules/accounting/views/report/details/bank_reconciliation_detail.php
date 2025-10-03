<div id="accordion">
  <div class="card">
    <table class="tree">
      <tbody>
        <tr>
          <td colspan="7">
              <h3 class="text-center no-margin-top-20 no-margin-left-24"><?php echo get_option('companyname'); ?></h3>
          </td>
          <td></td>
          <td></td>
          <!-- <td></td> -->
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="7">
            <h4 class="text-center no-margin-top-20 no-margin-left-24"><?php echo _l('bank_reconciliation_detail'); ?></h4>
          </td>
          <td></td>
          <td></td>
          <!-- <td></td> -->
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="7">
            <p class="text-center no-margin-top-20 no-margin-left-24"><?php echo $data_report['account_name'] .', '._l('period_ending').' '._d($data_report['statement_ending_date']); ?></p>
          </td>
          <td></td>
          <!-- <td></td> -->
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td>
          </td>
          <!-- <td></td> -->
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr class="tr_header">
          <td class="text-bold"><?php echo _l('type'); ?></td>
          <td class="text-bold"><?php echo _l('invoice_payments_table_date_heading'); ?></td>
          <!-- <td class="text-bold"><?php echo _l('number'); ?></td> -->
          <td class="text-bold"><?php echo _l('name'); ?></td>
          <td class="text-bold"><?php echo _l('split'); ?></td>
          <td class="text-bold"><?php echo _l('description'); ?></td>
          <td class="total_amount text-bold"><?php echo _l('acc_amount'); ?></td>
          <td class="total_amount text-bold"><?php echo _l('balance'); ?></td>
        </tr>
        <?php
          $row_index = 0;
          $parent_index = 0;
          $row_index += 1;
          $parent_index = $row_index;
          if($data_report['statement_ending_date'] != ''){
            $balance = $data_report['beginning_balance'];
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent text-bold"><?php echo _l('beginning_balance'); ?></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount text-bold"><?php echo app_format_money($data_report['beginning_balance'], $currency->name); ?></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('cleared_transactions'); ?></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('checks_and_payments'); ?> - <?php echo $data_report['checks_and_payments_items']; ?></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"></td>
          </tr>
          <?php 
            $parent_index = $row_index;
            $row_index += 1;
            foreach ($data_report['checks_and_payments_details'] as $detail) { 
                $url = get_url_by_type_id($detail['rel_type'], $detail['rel_id']);
                $balance = $balance + ($detail['amount']);
              ?>
              <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
                <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l($detail['rel_type']); ?></td>
                <td><a href="<?php echo new_html_entity_decode($url); ?>" class="text-default-bl"><?php echo _d($detail['date']); ?></a></td>
                <!-- <td><?php echo $detail['number'] != '' ? '#'.$detail['number'] : ''; ?></td> -->
                <td><?php echo $detail['name']; ?></td>
                <td><?php echo $detail['split']; ?></td>
                <td><?php echo $detail['description']; ?></td>
                <td class="total_amount"><?php echo app_format_money($detail['amount'], $currency->name); ?></td>
                <td class="total_amount"><?php echo app_format_money($balance, $currency->name); ?></td>
              </tr>
          <?php
            $row_index += 1;
            }
          ?>

          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('deposits_and_credits'); ?> - <?php echo $data_report['deposits_and_credits_items']; ?></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"></td>
          </tr>
          <?php 
            $parent_index = $row_index;
            $row_index += 1;
            foreach ($data_report['deposits_and_credits_details'] as $detail) { 
                $url = get_url_by_type_id($detail['rel_type'], $detail['rel_id']);
                $balance = $balance + ($detail['amount']);
              ?>
              <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
                <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l($detail['rel_type']); ?></td>
                <td><a href="<?php echo new_html_entity_decode($url); ?>" class="text-default-bl"><?php echo _d($detail['date']); ?></a></td>
                <!-- <td><?php echo $detail['number'] != '' ? '#'.$detail['number'] : ''; ?></td> -->
                <td><?php echo $detail['name']; ?></td>
                <td><?php echo $detail['split']; ?></td>
                <td><?php echo $detail['description']; ?></td>
                <td class="total_amount"><?php echo app_format_money($detail['amount'], $currency->name); ?></td>
                <td class="total_amount"><?php echo app_format_money($balance, $currency->name); ?></td>
              </tr>
          <?php
            $row_index += 1;
            }
          ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent text-bold">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('total_for', _l('cleared_transactions')); ?></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount text-bold"><?php echo app_format_money($data_report['cleared_transactions'], $currency->name); ?></td>
            <td class="total_amount text-bold"><?php echo app_format_money($balance, $currency->name); ?></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent"></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"></td>
          </tr>
          <?php $row_index += 1; ?>

          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent text-bold"><?php echo _l('cleared_balance'); ?></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount text-bold"><?php echo app_format_money(($data_report['cleared_transactions'] + ($data_report['beginning_balance'])), $currency->name); ?></td>
            <td class="total_amount text-bold"><?php echo app_format_money($balance, $currency->name); ?></td>
          </tr>

           <?php $row_index += 1; ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent"></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"></td>
          </tr>

           <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('uncleared_transactions'); ?></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('checks_and_payments'); ?> - <?php echo $data_report['unclear_checks_and_payments_items']; ?></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"></td>
          </tr>
          <?php 
            $parent_index = $row_index;
            $row_index += 1;
            foreach ($data_report['unclear_checks_and_payments_details'] as $detail) { 
                $url = get_url_by_type_id($detail['rel_type'], $detail['rel_id']);
                $balance = $balance + ($detail['amount']);
              ?>
              <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
                <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l($detail['rel_type']); ?></td>
                <td><a href="<?php echo new_html_entity_decode($url); ?>" class="text-default-bl"><?php echo _d($detail['date']); ?></a></td>
                <!-- <td><?php echo $detail['number'] != '' ? '#'.$detail['number'] : ''; ?></td> -->
                <td><?php echo $detail['name']; ?></td>
                <td><?php echo $detail['split']; ?></td>
                <td><?php echo $detail['description']; ?></td>
                <td class="total_amount"><?php echo app_format_money($detail['amount'], $currency->name); ?></td>
                <td class="total_amount"><?php echo app_format_money($balance, $currency->name); ?></td>
              </tr>
          <?php
            $row_index += 1;
            }
          ?>

          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('deposits_and_credits'); ?> - <?php echo $data_report['unclear_deposits_and_credits_items']; ?></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"></td>
          </tr>
          <?php 
            $parent_index = $row_index;
            $row_index += 1;
            foreach ($data_report['unclear_deposits_and_credits_details'] as $detail) { 
                $url = get_url_by_type_id($detail['rel_type'], $detail['rel_id']);
                $balance = $balance + ($detail['amount']);
              ?>
              <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
                <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l($detail['rel_type']); ?></td>
                <td><a href="<?php echo new_html_entity_decode($url); ?>" class="text-default-bl"><?php echo _d($detail['date']); ?></a></td>
                <!-- <td><?php echo $detail['number'] != '' ? '#'.$detail['number'] : ''; ?></td> -->
                <td><?php echo $detail['name']; ?></td>
                <td><?php echo $detail['split']; ?></td>
                <td><?php echo $detail['description']; ?></td>
                <td class="total_amount"><?php echo app_format_money($detail['amount'], $currency->name); ?></td>
                <td class="total_amount"><?php echo app_format_money($balance, $currency->name); ?></td>
              </tr>
          <?php
            $row_index += 1;
            }
          ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent text-bold">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('total_for', _l('uncleared_transactions')); ?></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount text-bold"><?php echo app_format_money($data_report['uncleared_transactions'], $currency->name); ?></td>
            <td class="total_amount text-bold"><?php echo app_format_money($balance, $currency->name); ?></td>
          </tr>


          <?php $row_index += 1; ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent"></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"></td>
          </tr>



          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent text-bold"><?php echo _l('register_balance_as_of', _d($data_report['statement_ending_date'])); ?></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount text-bold"><?php echo app_format_money(($data_report['uncleared_transactions'] + ($data_report['beginning_balance']) + ($data_report['cleared_transactions']) ), $currency->name); ?></td>
            <td class="total_amount text-bold"><?php echo app_format_money($balance, $currency->name); ?></td>
          </tr>
          <?php $row_index += 1; ?>
          <?php if($data_report['new_transactions'] != 0){ ?>
            <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
              <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('new_transactions'); ?></td>
              <td></td>
              <td></td>
              <!-- <td></td> -->
              <td></td>
              <td></td>
              <td></td>
              <td class="total_amount"></td>
            </tr>
          <?php if($data_report['new_checks_and_payments_items'] != 0){ ?>
            <?php $row_index += 1; ?>
            <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
              <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('checks_and_payments'); ?> - <?php echo $data_report['new_checks_and_payments_items']; ?></td>
              <td></td>
              <!-- <td></td> -->
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td class="total_amount"></td>
            </tr>
            <?php 
              $parent_index = $row_index;
              $row_index += 1;
              foreach ($data_report['new_checks_and_payments_details'] as $detail) { 
                $url = get_url_by_type_id($detail['rel_type'], $detail['rel_id']);
                $balance = $balance + ($detail['amount']);
                ?>
                <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
                  <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l($detail['rel_type']); ?></td>
                  <td><a href="<?php echo new_html_entity_decode($url); ?>" class="text-default-bl"><?php echo _d($detail['date']); ?></a></td>
                  <!-- <td><?php echo $detail['number'] != '' ? '#'.$detail['number'] : ''; ?></td> -->
                  <td><?php echo $detail['name']; ?></td>
                  <td><?php echo $detail['split']; ?></td>
                  <td><?php echo $detail['description']; ?></td>
                  <td class="total_amount"><?php echo app_format_money($detail['amount'], $currency->name); ?></td>
                  <td class="total_amount"><?php echo app_format_money($balance, $currency->name); ?></td>
                </tr>
            <?php
              $row_index += 1;
              }
            ?>
          <?php } ?>
          <?php if($data_report['new_deposits_and_credits_items'] != 0){ ?>
            <?php $row_index += 1; ?>
            <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
              <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('deposits_and_credits'); ?> - <?php echo $data_report['new_deposits_and_credits_items']; ?></td>
              <td></td>
              <td></td>
              <td></td>
              <!-- <td></td> -->
              <td></td>
              <td></td>
              <td class="total_amount"></td>
            </tr>
            <?php 
              $parent_index = $row_index;
              $row_index += 1;
              foreach ($data_report['new_deposits_and_credits_details'] as $detail) { 
                $url = get_url_by_type_id($detail['rel_type'], $detail['rel_id']);
                $balance = $balance + ($detail['amount']);
                ?>
                <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
                  <td class="parent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l($detail['rel_type']); ?></td>
                  <td><a href="<?php echo new_html_entity_decode($url); ?>" class="text-default-bl"><?php echo _d($detail['date']); ?></a></td>
                  <!-- <td><?php echo $detail['number'] != '' ? '#'.$detail['number'] : ''; ?></td> -->
                  <td><?php echo $detail['name']; ?></td>
                  <td><?php echo $detail['split']; ?></td>
                  <td><?php echo $detail['description']; ?></td>
                  <td class="total_amount"><?php echo app_format_money($detail['amount'], $currency->name); ?></td>
                  <td class="total_amount"><?php echo app_format_money($balance, $currency->name); ?></td>
                </tr>
            <?php
              $row_index += 1;
              }
            ?>
            <?php } ?>
            <?php $row_index += 1; ?>
            <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
              <td class="parent text-bold">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _l('total_for', _l('new_transactions')); ?></td>
              <td></td>
              <!-- <td></td> -->
              <td></td>
              <td></td>
              <td></td>
              <td class="total_amount text-bold"><?php echo app_format_money($data_report['new_transactions'], $currency->name); ?></td>
              <td class="total_amount text-bold"><?php echo app_format_money($balance, $currency->name); ?></td>
            </tr>
          <?php } ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent"></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"></td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded">
            <td class="parent text-bold"><?php echo _l('ending_balance'); ?></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td class="total_amount text-bold"><?php echo app_format_money($data_report['ending_balance'] , $currency->name); ?></td>
            <td class="total_amount text-bold"><?php echo app_format_money($data_report['ending_balance'] , $currency->name); ?></td>
          </tr>
        <?php } ?>
        </tbody>
    </table>
  </div>
</div>