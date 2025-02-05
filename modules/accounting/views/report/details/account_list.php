<div id="accordion">
    <div class="card">
      <table class="tree">
        <tbody>
          <tr>
            <td colspan="5">
                <h3 class="text-center no-margin-top-20 no-margin-left-24"><?php echo get_option('companyname'); ?></h3>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td colspan="5">
              <h4 class="text-center no-margin-top-20 no-margin-left-24"><?php echo _l('account_list'); ?></h4>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td colspan="5">
              <p class="text-center no-margin-top-20 no-margin-left-24"><?php echo _d(date('Y-m-d')); ?></p>
            </td>
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
          </tr>
          <tr class="tr_header">
            <td class="text-bold"><?php echo _l('number').' - '._l('acc_account'); ?></td>
            <td class="text-bold"><?php echo _l('type'); ?></td>
            <td class="text-bold"><?php echo _l('detail_type'); ?></td>
            <td class="text-bold"><?php echo _l('description'); ?></td>
            <td class="total_amount text-bold"><?php echo _l('acc_amount'); ?></td>
          </tr>
          <?php
            $row_index = 1;
            $total = 0;
            $parent_index = 0;

            $data = $this->accounting_model->get_html_account_list($data_report['data']['cash_and_cash_equivalents'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];

            $data = $this->accounting_model->get_html_account_list($data_report['data']['accounts_receivable'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];

            $data = $this->accounting_model->get_html_account_list($data_report['data']['current_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];

            $data = $this->accounting_model->get_html_account_list($data_report['data']['fixed_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];

            $data = $this->accounting_model->get_html_account_list($data_report['data']['non_current_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];

            $data = $this->accounting_model->get_html_account_list($data_report['data']['accounts_payable'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];

            $data = $this->accounting_model->get_html_account_list($data_report['data']['credit_card'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];

            $data = $this->accounting_model->get_html_account_list($data_report['data']['current_liabilities'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];

            $data = $this->accounting_model->get_html_account_list($data_report['data']['non_current_liabilities'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];

            $data = $this->accounting_model->get_html_account_list($data_report['data']['owner_equity'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];

            $data = $this->accounting_model->get_html_account_list($data_report['data']['income'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];

            $data = $this->accounting_model->get_html_account_list($data_report['data']['cost_of_sales'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];

            $data = $this->accounting_model->get_html_account_list($data_report['data']['expenses'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];

            $data = $this->accounting_model->get_html_account_list($data_report['data']['other_income'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];

            $data = $this->accounting_model->get_html_account_list($data_report['data']['other_expenses'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total += $data['total_amount'];
            ?>
          </tbody>
      </table>
    </div>
</div>