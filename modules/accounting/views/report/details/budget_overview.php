<div id="accordion">
  <div class="card">
    <?php if($data_report['type'] != ''){ ?>
    <table class="tree">
      <?php if($data_report['type'] == 'profit_and_loss_accounts'){ ?>
      <tbody>
        <tr class="treegrid-01 parent-node expanded">
          <td colspan="14">
              <h3 class="text-center no-margin-top-20 no-margin-left-24"><?php echo get_option('companyname'); ?></h3>
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr class="treegrid-02 parent-node expanded">
          <td colspan="14">
            <h4 class="text-center no-margin-top-20 no-margin-left-24"><?php echo _l('budget_overview'); ?></h4>
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr class="treegrid-03 parent-node expanded">
          <td colspan="14">
            <p class="text-center no-margin-top-20 no-margin-left-24"><?php echo _d($data_report['from_date']) .' - '. _d($data_report['to_date']); ?></p>
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
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
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr class="tr_header">
          <td></td>
          <?php 
          foreach($data_report['headers'] as $header){ ?>
            <td class="th_total_width_auto text-bold"><?php echo new_html_entity_decode($header); ?></td>
          <?php } ?>
          <td class="th_total_width_auto text-bold"><?php echo _l('total'); ?></td>
        </tr>
        <?php
          $row_index = 0;
          $parent_index = 0;
          $row_index += 1;
          $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_income'); ?></td>

            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>

            <td class="total_amount"></td>
          </tr>
          <?php
          $row_index += 1;
          ?>
          <?php 
            $_index = $row_index;
            $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['income'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_income = $data['total_amount'];

            ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_income'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total_income, $currency->name); ?> </td>
          </tr>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
           <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_cost_of_sales'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>

          </tr>
          <?php 
          $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['cost_of_sales'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_cost_of_sales = $data['total_amount'];
           ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_cost_of_sales'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total_cost_of_sales, $currency->name); ?> </td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('gross_profit_uppercase'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total_income - $total_cost_of_sales, $currency->name); ?> </td>
          </tr>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_other_income'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <?php 
            $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['other_income'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_other_income = $data['total_amount'];

           ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_other_income_loss'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total_other_income, $currency->name); ?> </td>
          </tr>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_expenses'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <?php 
          $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['expenses'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_expenses = $data['total_amount'];

           ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_expenses'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total_expenses, $currency->name); ?> </td>
          </tr>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_other_expenses'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <?php 
          $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['other_expenses'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_other_expenses = $data['total_amount'];
            
            $row_index += 1;
          ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_other_expenses'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total_other_expenses, $currency->name); ?> </td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('net_earnings_uppercase'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money(($total_income + $total_other_income) - ($total_cost_of_sales + $total_expenses + $total_other_expenses), $currency->name); ?> </td>
          </tr>
        </tbody>
      <?php }else{ ?>
        <tbody>
          <tr class="treegrid-01 parent-node expanded">
          <td colspan="14">
              <h3 class="text-center no-margin-top-20 no-margin-left-24"><?php echo get_option('companyname'); ?></h3>
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr class="treegrid-02 parent-node expanded">
          <td colspan="14">
            <h4 class="text-center no-margin-top-20 no-margin-left-24"><?php echo _l('budget_overview'); ?></h4>
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr class="treegrid-03 parent-node expanded">
          <td colspan="14">
            <p class="text-center no-margin-top-20 no-margin-left-24"><?php echo _d($data_report['from_date']) .' - '. _d($data_report['to_date']); ?></p>
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
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
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr class="tr_header">
          <td></td>
          <?php 
          foreach($data_report['headers'] as $header){ ?>
            <td class="th_total_width_auto text-bold"><?php echo new_html_entity_decode($header); ?></td>
          <?php } ?>
          <td class="th_total_width_auto text-bold"><?php echo _l('total'); ?></td>
        </tr>
        <tr class="treegrid-1000 parent-node expanded">
          <td class="parent"><?php echo _l('acc_assets'); ?></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <?php
          $row_index = 0;
          $parent_index = 0;
          $row_index += 1;
          $parent_index = $row_index;
          $total_current_assets = 0;
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> treegrid-parent-1000 parent-node expanded">
            <td class="parent"><?php echo _l('acc_current_assets'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <?php
          $row_index += 1;
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_accounts_receivable'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <?php 
            $_index = $row_index;
            $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['accounts_receivable'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_current_assets += $data['total_amount'];
            ?>
          <?php 
            $row_index += 1;
           ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_accounts_receivable'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($data['total_amount'], $currency->name); ?> </td>
          </tr>
          <?php 

            $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['cash_and_cash_equivalents'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_current_assets += $data['total_amount'];

            $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['current_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_current_assets += $data['total_amount'];
          
            $row_index += 1;
          ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-1000 parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_current_assets'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total_current_assets, $currency->name); ?> </td>
          </tr>
          <?php 
            $row_index += 1;
            $parent_index = $row_index;
            $total_long_term_assets = 0;
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> treegrid-parent-1000 parent-node expanded">
            <td class="parent"><?php echo _l('long_term_assets'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <?php 
            $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['fixed_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_long_term_assets += $data['total_amount'];

            $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['non_current_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_long_term_assets += $data['total_amount'];

            $row_index += 1;
            ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-1000 parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_long_term_assets'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total_long_term_assets, $currency->name); ?> </td>
          </tr>
          <?php 
            $row_index += 1;
            ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> tr_total">
            <td class="parent"><?php echo _l('total_assets'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total_current_assets + $total_long_term_assets, $currency->name); ?> </td>
          </tr>
          <?php 
            $row_index += 1;
            ?>
            <tr class="treegrid-1001 parent-node expanded">
              <td class="parent"><?php echo _l('liabilities_and_shareholders_equity'); ?></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <?php
            $row_index += 1;
            $parent_index = $row_index;
            $total_current_liabilities = 0;
            ?>
            <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> treegrid-parent-1001 parent-node expanded">
              <td class="parent"><?php echo _l('acc_current_liabilities'); ?></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <?php $row_index += 1; ?>
            <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
              <td class="parent"><?php echo _l('accounts_payable'); ?></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <?php 
              $_index = $row_index;
              $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['accounts_payable'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $_index, $currency);
              $row_index = $data['row_index'];
              echo new_html_entity_decode($data['html']);
              $total_current_liabilities += $data['total_amount'];
              ?>
            <?php $row_index += 1; ?>
            <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> tr_total">
              <td class="parent"><?php echo _l('total_accounts_payable'); ?></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td class="total_amount"><?php echo app_format_money($data['total_amount'], $currency->name); ?> </td>
            </tr>
            <?php 
            $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['credit_card'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_current_liabilities += $data['total_amount'];

            $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['current_liabilities'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_current_liabilities += $data['total_amount'];
            ?>
           
            <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-1001 tr_total">
              <td class="parent"><?php echo _l('total_current_liabilities'); ?></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td class="total_amount"><?php echo app_format_money($total_current_liabilities, $currency->name); ?> </td>
            </tr>
            <?php $row_index += 1; ?>
            <?php
            $row_index += 1;
            $parent_index = $row_index;
            $total_non_current_liabilities = 0;
            ?>
            <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> treegrid-parent-1001 parent-node expanded">
              <td class="parent"><?php echo _l('acc_non_current_liabilities'); ?></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <?php 
            $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['non_current_liabilities'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_non_current_liabilities += $data['total_amount'];
            ?>

            <?php $row_index += 1; ?>
            <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-1001 tr_total">
              <td class="parent"><?php echo _l('total_non_current_liabilities'); ?></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td class="total_amount"><?php echo app_format_money($total_non_current_liabilities, $currency->name); ?> </td>
            </tr>
            <?php $row_index += 1; ?>
            <?php
            $row_index += 1;
            $parent_index = $row_index;
            $total_shareholders_equity = 0;
            ?>
            <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> treegrid-parent-1001 parent-node expanded">
              <td class="parent"><?php echo _l('shareholders_equity'); ?></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <?php $row_index += 1; ?>
            <?php 
            $data = $this->accounting_model->get_html_profit_and_loss_12_months($data_report['data']['owner_equity'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_shareholders_equity += $data['total_amount'];
            ?>

            <?php $row_index += 1; ?>
            <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-1001 tr_total">
              <td class="parent"><?php echo _l('total_shareholders_equity'); ?></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td class="total_amount"><?php echo app_format_money($total_shareholders_equity, $currency->name); ?> </td>
            </tr>
            <?php $row_index += 1; ?>
            <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> tr_total">
              <td class="parent"><?php echo _l('total_liabilities_and_equity'); ?></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td class="total_amount"><?php echo app_format_money($total_shareholders_equity + $total_non_current_liabilities + $total_current_liabilities, $currency->name); ?> </td>
            </tr>
            <?php $row_index += 1; ?>
        </tbody>
      <?php } ?>
    </table>
      <?php }else{ ?>
            <h3 class="text-center"><?php echo get_option('companyname'); ?></h3>
            <h4 class="text-center"><?php echo _l('budget_overview'); ?></h4>
            <h4 class="text-center text-danger"><?php echo _l('no_budget_has_been_created_2'); ?></h4>
      <?php } ?>
  </div>
</div>