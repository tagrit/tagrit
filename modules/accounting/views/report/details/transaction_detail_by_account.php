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
            <h4 class="text-center no-margin-top-20 no-margin-left-24"><?php echo _l('transaction_detail_by_account'); ?></h4>
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
          <td class="text-bold"><?php echo _l('customer'); ?></td>
          <td class="text-bold"><?php echo _l('description'); ?></td>
          <td class="text-bold"><?php echo _l('split'); ?></td>
          <td class="total_amount text-bold"><?php echo _l('acc_amount'); ?></td>
          <td class="total_amount text-bold"><?php echo _l('balance'); ?></td>
        </tr>
        <?php
         $row_index = 0;
         $parent_index = 0;

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['accounts_receivable'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['cash_and_cash_equivalents'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['current_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['fixed_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['non_current_assets'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['accounts_payable'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['credit_card'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['current_liabilities'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['non_current_liabilities'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['owner_equity'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['income'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['other_income'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['cost_of_sales'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['expenses'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);

        $data = $this->accounting_model->get_html_transaction_detail_by_account($data_report['data']['other_expenses'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency);
        $row_index = $data['row_index'];
        echo new_html_entity_decode($data['html']);
        ?>
        
      </tbody>
    </table>
  </div>
</div>