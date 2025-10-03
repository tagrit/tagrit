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
            <h4 class="text-center no-margin-top-20 no-margin-left-24"><?php echo _l('trial_balance'); ?></h4>
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
        <tr class="tr_header">
          <td class="text-bold"><?php echo _l('acc_account'); ?></td>
          <td class="th_total text-bold"><?php echo _l('debit'); ?></td>
          <td class="th_total text-bold"><?php echo _l('credit'); ?></td>
        </tr>
        <?php
          $row_index = 0;
          $parent_index = 0;
          $total_credit = 0;
          $total_debit = 0;

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['cash_and_cash_equivalents'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['accounts_receivable'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['current_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['fixed_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['non_current_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['accounts_payable'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['credit_card'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['current_liabilities'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['non_current_liabilities'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['owner_equity'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['income'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['cost_of_sales'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['expenses'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['other_income'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          $data = $this->accounting_model->get_html_trial_balance($data_report['data']['other_expenses'], ['html' => '', 'row_index' => $row_index + 1, 'total_credit' => 0, 'total_debit' => 0], $parent_index, $currency);
          $row_index = $data['row_index'];
          echo new_html_entity_decode($data['html']);
          $total_debit += $data['total_debit'];
          $total_credit += $data['total_credit'];

          ?>
     
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-10000 tr_total">
            <td class="parent"><?php echo _l('total'); ?></td>
            <td class="total_amount"><?php echo app_format_money($total_debit, $currency->name); ?> </td>
            <td class="total_amount"><?php echo app_format_money($total_credit, $currency->name); ?> </td>
          </tr>
        </tbody>
    </table>
  </div>
</div>