<div id="accordion">
  <div class="card">
    <table class="tree">
      <tbody>
        <tr>
          <td colspan="3">
              <h3 class="text-center no-margin-top-20 no-margin-left-24"><?php echo get_option('companyname'); ?></h3>
          </td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="3">
            <h4 class="text-center no-margin-top-20 no-margin-left-24"><?php echo _l('profit_and_loss_as_of_total_income'); ?></h4>
          </td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="3">
            <p class="text-center no-margin-top-20 no-margin-left-24"><?php echo _d($data_report['from_date']) .' - '. _d($data_report['to_date']); ?></p>
          </td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr class="border-top">
          <td></td>
          <td colspan="2" class="text-center th_total_2 text-bold"><?php echo _l('total'); ?></td>
          <td></td>
        </tr>
        <tr class="border-bottom">
          <td></td>
          <td class="th_total_2 text-bold"><?php echo _d($data_report['from_date']) .' - '. _d($data_report['to_date']); ?></td>
          <td class="th_total_2 text-bold"><?php echo _l('percent_of_income'); ?></td>
        </tr>
        <?php
          $row_index = 0;
          $parent_index = 0;
          $row_index += 1;
          $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_income'); ?></td>
            <td class="total_amount"></td>
            <td class="total_amount"></td>
          </tr>
          <?php
          $row_index += 1;
          ?>
          <?php 
            $_index = $row_index;
            $data = $this->accounting_model->get_html_profit_and_loss_as_of_total_income($data_report['data']['income'], $data_report['total']['income'],['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_income = $data['total_amount'];
            $percent_income = $data['percent'];
            ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?>  parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_income'); ?></td>
            <td class="total_amount"><?php echo app_format_money($total_income, $currency->name); ?> </td>
            <td class="total_amount">100% </td>
          </tr>

          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
           <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_cost_of_sales'); ?></td>
            <td></td>
            <td></td>
          </tr>
          <?php 
            $data = $this->accounting_model->get_html_profit_and_loss_as_of_total_income($data_report['data']['cost_of_sales'], $data_report['total']['income'],['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_cost_of_sales = $data['total_amount'];
            $percent_cost_of_sales = $data['percent'];

          ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?>  parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_cost_of_sales'); ?></td>
            <td class="total_amount"><?php echo app_format_money($total_cost_of_sales, $currency->name); ?> </td>
            <td class="total_amount"><?php echo new_html_entity_decode($percent_cost_of_sales); ?>% </td>
          </tr>
          <?php 
          $acc_enable_income_statement_modifications = get_option('acc_enable_income_statement_modifications');
          $total_net_income = 0;
          if($acc_enable_income_statement_modifications == 1){
          ?>
              <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_net_income'); ?></td>
            <td class="total_amount"></td>
            <td class="total_amount"></td>
          </tr>
          <?php
          $row_index += 1;
          ?>
          <?php 
            $_index = $row_index;
            $data = $this->accounting_model->get_html_profit_and_loss_as_of_total_income($data_report['data']['net_income'], $data_report['total']['net_income'],['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_net_income = $data['total_amount'];
            $percent_net_income = $data['percent'];
            ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?>  parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_net_income'); ?></td>
            <td class="total_amount"><?php echo app_format_money($total_net_income, $currency->name); ?> </td>
            <td class="total_amount"><?php echo new_html_entity_decode($percent_net_income); ?>% </td>
          </tr>
          <?php } ?>
          <?php if($acc_enable_income_statement_modifications != 1){ ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('gross_profit_uppercase'); ?></td>
            <td class="total_amount"><?php echo app_format_money($total_income - $total_cost_of_sales, $currency->name); ?> </td>
            <td class="total_amount"><?php echo new_html_entity_decode($percent_income - $percent_cost_of_sales); ?>% </td>
          </tr>
          <?php } ?>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_other_income'); ?></td>
            <td></td>
            <td></td>
          </tr>
          <?php 
            $data = $this->accounting_model->get_html_profit_and_loss_as_of_total_income($data_report['data']['other_income'], $data_report['total']['income'],['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_other_income = $data['total_amount'];
            $percent_other_income = $data['percent'];

            ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?>  parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_other_income_loss'); ?></td>
            <td class="total_amount"><?php echo app_format_money($total_other_income, $currency->name); ?> </td>
            <td class="total_amount"><?php echo new_html_entity_decode($percent_other_income); ?>% </td>
          </tr>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_expenses'); ?></td>
            <td></td>
            <td></td>
          </tr>
          <?php 
          $data = $this->accounting_model->get_html_profit_and_loss_as_of_total_income($data_report['data']['expenses'], $data_report['total']['income'],['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_expenses = $data['total_amount'];
            $percent_expenses = $data['percent'];

           ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?>  parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_expenses'); ?></td>
            <td class="total_amount"><?php echo app_format_money($total_expenses, $currency->name); ?> </td>
            <td class="total_amount"><?php echo new_html_entity_decode($percent_expenses); ?>% </td>
          </tr>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_other_expenses'); ?></td>
            <td></td>
            <td></td>
          </tr>
          <?php 
          $data = $this->accounting_model->get_html_profit_and_loss_as_of_total_income($data_report['data']['other_expenses'], $data_report['total']['income'],['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_other_expenses = $data['total_amount'];
            $percent_other_expenses = $data['percent'];

            $row_index += 1;
          ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?>  parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_other_expenses'); ?></td>
            <td class="total_amount"><?php echo app_format_money($total_other_expenses, $currency->name); ?> </td>
            <td class="total_amount"><?php echo new_html_entity_decode($percent_other_expenses); ?>% </td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('net_earnings_uppercase'); ?></td>
            <td class="total_amount"><?php echo app_format_money(($total_income + $total_other_income + $total_net_income) - ($total_cost_of_sales + $total_expenses + $total_other_expenses), $currency->name); ?> </td>
            <?php if($data_report['total']['income'] != 0){ ?>
              <td class="total_amount"><?php echo round(((($total_income + $total_other_income + $total_net_income) - ($total_cost_of_sales + $total_expenses + $total_other_expenses)) / $data_report['total']['income']) * 100, 2); ?>% </td>
            <?php }else{ ?>
              <td class="total_amount">0%</td>
           <?php } ?>
          </tr>
        </tbody>
    </table>
  </div>
</div>