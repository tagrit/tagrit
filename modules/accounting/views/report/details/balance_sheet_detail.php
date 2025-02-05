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
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="7">
            <h4 class="text-center no-margin-top-20 no-margin-left-24"><?php echo _l('balance_sheet_detail'); ?></h4>
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="7">
            <p class="text-center no-margin-top-20 no-margin-left-24"><?php echo _d($data_report['from_date']) .' - '. _d($data_report['to_date']); ?></p>
          </td>
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
        </tr>
        <tr class="tr_header">
          <td class="text-bold"><?php echo _l('invoice_payments_table_date_heading'); ?></td>
          <td class="text-bold"><?php echo _l('transaction_type'); ?></td>
          <td class="text-bold"><?php echo _l('description'); ?></td>
          <td class="total_amount text-bold"><?php echo _l('debit'); ?></td>
          <td class="total_amount text-bold"><?php echo _l('credit'); ?></td>
          <td class="total_amount text-bold"><?php echo _l('acc_amount'); ?></td>
          <td class="total_amount text-bold"><?php echo _l('balance'); ?></td>
        </tr>
        <tr class="treegrid-100000 parent-node expanded">
          <td class="parent"><?php echo _l('acc_assets'); ?></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <?php
         $row_index = 0;
         $parent_index = 100000;
         $total_assets = 0;
         $balance_assets = 0;
          $data = $this->accounting_model->get_html_balance_sheet_detail($data_report['data']['accounts_receivable'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
         $total_assets += $data['total_amount'];
         $balance_assets += $data['total_balance'];

          $data = $this->accounting_model->get_html_balance_sheet_detail($data_report['data']['cash_and_cash_equivalents'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_assets += $data['total_amount'];
          $balance_assets += $data['total_balance'];

          $data = $this->accounting_model->get_html_balance_sheet_detail($data_report['data']['current_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_assets += $data['total_amount'];
          $balance_assets += $data['total_balance'];

          $data = $this->accounting_model->get_html_balance_sheet_detail($data_report['data']['fixed_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_assets += $data['total_amount'];
          $balance_assets += $data['total_balance'];

          $data = $this->accounting_model->get_html_balance_sheet_detail($data_report['data']['non_current_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_assets += $data['total_amount'];
          $balance_assets += $data['total_balance'];

          ?>
        <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_assets'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total_assets, $currency->name); ?> </td>
            <td class="total_amount"><?php echo app_format_money($balance_assets, $currency->name); ?> </td>
          </tr>
        <tr class="treegrid-100001 parent-node expanded">
          <td class="parent"><?php echo _l('liabilities_and_shareholders_equity'); ?></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <?php $row_index += 1;
          $_parent_index = $row_index; 
          ?>
        <tr class="treegrid-<?php echo new_html_entity_decode($_parent_index); ?> treegrid-parent-100001 parent-node expanded">
          <td class="parent"><?php echo _l('liabilities'); ?></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <?php 
        $total_liabilities = 0;
        $balance_liabilities = 0;
          $data = $this->accounting_model->get_html_balance_sheet_detail($data_report['data']['accounts_payable'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0], $_parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
         $total_liabilities += $data['total_amount'];
         $balance_liabilities += $data['total_balance'];

         $data = $this->accounting_model->get_html_balance_sheet_detail($data_report['data']['credit_card'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0], $_parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
         $total_liabilities += $data['total_amount'];
         $balance_liabilities += $data['total_balance'];

         $data = $this->accounting_model->get_html_balance_sheet_detail($data_report['data']['current_liabilities'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0], $_parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
         $total_liabilities += $data['total_amount'];
         $balance_liabilities += $data['total_balance'];

         $data = $this->accounting_model->get_html_balance_sheet_detail($data_report['data']['non_current_liabilities'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0], $_parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
         $total_liabilities += $data['total_amount'];
         $balance_liabilities += $data['total_balance'];

         $row_index += 1;
         ?>
         <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-100001 parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_for', _l('liabilities')); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total_liabilities, $currency->name); ?> </td>
            <td class="total_amount"><?php echo app_format_money($balance_liabilities, $currency->name); ?> </td>
          </tr>
         <?php
         $row_index += 1;
          $_parent_index = $row_index ; 
          $total_equity = 0;
          $balance_equity = 0;
         ?>
         <tr class="treegrid-<?php echo new_html_entity_decode($_parent_index); ?> treegrid-parent-100001 parent-node expanded">
          <td class="parent"><?php echo _l('equity'); ?></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
         <?php 
         $data = $this->accounting_model->get_html_balance_sheet_detail($data_report['data']['owner_equity'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0], $_parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
         $total_equity += $data['total_amount'];
         $total_equity += $data_report['net_income'];
         $balance_equity += $data['total_balance'];
         $row_index += 1;

        ?>
        <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($_parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('acc_net_income'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($data_report['net_income'], $currency->name); ?> </td>
            <td class="total_amount"><?php echo app_format_money(($data_report['net_income_beginning_balance'] + $data_report['net_income']), $currency->name); ?> </td>
          </tr>
          <?php $row_index += 1; 
          $balance_equity += ($data_report['net_income_beginning_balance'] + $data_report['net_income']);
          ?>
        
        <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-100001 parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_for', _l('equity')); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total_equity, $currency->name); ?> </td>
            <td class="total_amount"><?php echo app_format_money($balance_equity, $currency->name); ?> </td>
          </tr>
          <?php $row_index += 1; 
          $total_liabilities_and_equity = $total_equity + $total_liabilities;
          $balance_liabilities_and_equity = $balance_equity + $balance_liabilities;
        ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-100011 parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_liabilities_and_equity'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total_liabilities_and_equity, $currency->name); ?> </td>
            <td class="total_amount"><?php echo app_format_money($balance_liabilities_and_equity, $currency->name); ?> </td>
          </tr>
      </tbody>
    </table>
  </div>
</div>