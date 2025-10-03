<div id="accordion">
  <div class="card">
    <table class="tree">
      <tbody>
        <tr>
          <td colspan="14">
              <h3 class="text-center no-margin-top-20 no-margin-left-24"><?php echo get_option('companyname'); ?></h3>
          </td>
          <td></td>
        </tr>
        <tr>
          <td colspan="14">
            <h4 class="text-center no-margin-top-20 no-margin-left-24"><?php echo _l('custom_summary_report'); ?></h4>
          </td>
          <td></td>
        </tr>
        <tr>
          <td colspan="14">
            <p class="text-center no-margin-top-20 no-margin-left-24"><?php echo _d($data_report['from_date']) .' - '. _d($data_report['to_date']); ?></p>
          </td>
          <?php 
          $html_column = '';
          switch ($data_report['display_columns_by']) {
            case 'total_only':
              echo '<td></td>';
              break;

            case 'months':
              $start = $month = strtotime($data_report['from_date']);
              $end = strtotime($data_report['to_date']);
              while($month <= $end)
              {
                $html_column .= '<td></td>';
                  $month = strtotime("+1 month", $month);
              }

              echo '<td></td>';
              break;

            case 'quarters':
              $from_date = $data_report['from_date'];
              $to_date = $data_report['to_date'];

              while (strtotime($from_date) < strtotime($to_date)) {
                  $month = date('m', strtotime($from_date));
                  $year = date('Y', strtotime($from_date));

                  $html_column .= '<td></td>';

                  $from_date = date('Y-m-d', strtotime('+3 month', strtotime($from_date)));

                  if(strtotime($from_date) > strtotime($to_date)){
                      $month_2 = date('m', strtotime($from_date));
                      $year_2 = date('Y', strtotime($from_date));

                      if($month . ' - ' . $year != $month_2 . ' - ' . $year_2){
                          $html_column .= '<td></td>';
                      }
                  }
              }
              echo '<td></td>';
              break;

            case 'years':
              $from_date = $data_report['from_date'];
              $to_date = $data_report['to_date'];

              while (strtotime($from_date) < strtotime($to_date)) {
                  $year = date('Y', strtotime($from_date));

                  $html_column .= '<td></td>';

                  $from_date = date('Y-m-d', strtotime('+1 year', strtotime($from_date)));

                  if(strtotime($from_date) > strtotime($to_date)){
                      $year_2 = date('Y', strtotime($to_date));
                  
                      if($year != $year_2){
                          $html_column .= '<td></td>';
                      }
                  }
              }
              echo '<td></td>';
              break;

            case 'vendors':
              $this->load->model('purchase/purchase_model');
              $vendors = $this->purchase_model->get_vendor();
              foreach ($vendors as $key => $vendor) {
                  $html_column .= '<td></td>';
              }
              $html_column .= '<td></td>';
              echo '<td></td>';
              break;

            case 'employees':
              $this->load->model('staff_model');
              $staffs = $this->staff_model->get();
              foreach ($staffs as $key => $staff) {
                  $html_column .= '<td></td>';
              }
              echo '<td></td>';
              break;
            case 'product_service':
              $this->load->model('invoice_items_model');
              $items = $this->invoice_items_model->get();
              foreach ($items as $key => $item) {
                $html_column .= '<td></td>';
              }
              $html_column .= '<td></td>';
              echo '<td></td>';
              break;
            case 'customers':
              $this->load->model('clients_model');
              $clients = $this->clients_model->get();
              foreach ($clients as $key => $client) {
                $html_column .= '<td></td>';
              }
              $html_column .= '<td></td>';
              echo '<td></td>';
              break;
            default:
              // code...
              break;
          }
          echo $html_column; 
          ?>
          <td></td>
        </tr>
        <tr>
          <td>
          </td>
          <td></td>
        </tr>
        <tr class="tr_header">
          <td></td>
          <?php 
          switch ($data_report['display_columns_by']) {
            case 'total_only':
              echo '<td class="th_total_width_auto text-bold">'. _l('total') .'</td>';
              break;

            case 'months':
              $start = $month = strtotime($data_report['from_date']);
              $end = strtotime($data_report['to_date']);
              while($month <= $end)
              {
                echo '<td class="th_total_width_auto text-bold">'.date('F', $month).'<br>'.date('Y', $month).'</td>';
                  $month = strtotime("+1 month", $month);
              }

              echo '<td class="th_total_width_auto text-bold">'. _l('total') .'</td>';
              break;

            case 'quarters':
              $from_date = $data_report['from_date'];
              $to_date = $data_report['to_date'];

              while (strtotime($from_date) < strtotime($to_date)) {
                  $month = date('m', strtotime($from_date));
                  $year = date('Y', strtotime($from_date));
                  if($month>=1 && $month<=3)
                  {
                      $t = 'Q1 - '.$year;
                  }
                  else  if($month>=4 && $month<=6)
                  {
                      $t = 'Q2 - '.$year;
                  }
                  else  if($month>=7 && $month<=9)
                  {
                      $t = 'Q3 - '.$year;
                  }
                  else  if($month>=10 && $month<=12)
                  {
                      $t = 'Q4 - '.$year;
                  }

                  echo '<td class="th_total_width_auto text-bold">'.$t.'</td>';

                  $from_date = date('Y-m-d', strtotime('+3 month', strtotime($from_date)));

                  if(strtotime($from_date) > strtotime($to_date)){
                      $month_2 = date('m', strtotime($from_date));
                      $year_2 = date('Y', strtotime($from_date));
                      if($month_2>=1 && $month_2<=3)
                      {
                          $t_2 = 'Q1 - '.$year_2;
                      }
                      else  if($month_2>=4 && $month_2<=6)
                      {
                          $t_2 = 'Q2 - '.$year_2;
                      }
                      else  if($month_2>=7 && $month_2<=9)
                      {
                          $t_2 = 'Q3 - '.$year_2;
                      }
                      else  if($month_2>=10 && $month_2<=12)
                      {
                          $t_2 = 'Q4 - '.$year_2;
                      }

                      if($month . ' - ' . $year != $month_2 . ' - ' . $year_2){
                          echo '<td class="th_total_width_auto text-bold">'.$t_2.'</td>';
                      }
                  }
              }
              echo '<td class="th_total_width_auto text-bold">'. _l('total') .'</td>';
              break;

            case 'years':
              $from_date = $data_report['from_date'];
              $to_date = $data_report['to_date'];

              while (strtotime($from_date) < strtotime($to_date)) {
                  $year = date('Y', strtotime($from_date));

                  echo '<td class="th_total_width_auto text-bold">'.$year.'</td>';

                  $from_date = date('Y-m-d', strtotime('+1 year', strtotime($from_date)));

                  if(strtotime($from_date) > strtotime($to_date)){
                      $year_2 = date('Y', strtotime($to_date));
                  
                      if($year != $year_2){
                          echo '<td class="th_total_width_auto text-bold">'.$year_2.'</td>';
                      }
                  }
              }
              echo '<td class="th_total_width_auto text-bold">'. _l('total') .'</td>';
              break;

            case 'vendors':
              $this->load->model('purchase/purchase_model');
              $vendors = $this->purchase_model->get_vendor();
              foreach ($vendors as $key => $vendor) {
                  echo '<td class="th_total_width_auto text-bold">'.$vendor['company'].'</td>';
              }
              echo '<td class="th_total_width_auto text-bold">'. _l('not_specified') .'</td>';
              echo '<td class="th_total_width_auto text-bold">'. _l('total') .'</td>';
              break;

            case 'employees':
              $this->load->model('staff_model');
              $staffs = $this->staff_model->get();
              foreach ($staffs as $key => $staff) {
                echo '<td class="th_total_width_auto text-bold">'.$staff['full_name'].'</td>';
              }
              echo '<td class="th_total_width_auto text-bold">'. _l('total') .'</td>';
              break;

            case 'product_service':
              $this->load->model('invoice_items_model');
              $items = $this->invoice_items_model->get();
              foreach ($items as $key => $item) {
                echo '<td class="th_total_width_auto text-bold">'.$item['description'].'</td>';
              }
              echo '<td class="th_total_width_auto text-bold">'. _l('not_specified') .'</td>';
              echo '<td class="th_total_width_auto text-bold">'. _l('total') .'</td>';
              break;
            case 'customers':
              $this->load->model('clients_model');
              $clients = $this->clients_model->get();
              foreach ($clients as $key => $client) {
                echo '<td class="th_total_width_auto text-bold">'.$client['company'].'</td>';
              }
              echo '<td class="th_total_width_auto text-bold">'. _l('not_specified') .'</td>';
              echo '<td class="th_total_width_auto text-bold">'. _l('total') .'</td>';
              break;
            default:
              // code...
              break;
          }
          ?>
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
          </tr>
          <?php
          $row_index += 1;
          ?>
          <?php 
            $_index = $row_index;
            $data = $this->accounting_model->get_html_custom_summary_by_income_statement($data_report['data']['income'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency, $data_report['display_columns_by']);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_income = $data['total_amount'];
             ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_income'); ?></td>
            <?php echo $html_column; ?>
            <td class="total_amount"><?php echo app_format_money($total_income, $currency->name); ?> </td>
          </tr>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
           <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_cost_of_sales'); ?></td>
            <td></td>
            <?php echo $html_column; ?>
          </tr>
          <?php 
          $data = $this->accounting_model->get_html_custom_summary_by_income_statement($data_report['data']['cost_of_sales'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency, $data_report['display_columns_by']);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_cost_of_sales = $data['total_amount'];

           $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_cost_of_sales'); ?></td>
            <?php echo $html_column; ?>
            <td class="total_amount"><?php echo app_format_money($total_cost_of_sales, $currency->name); ?> </td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('gross_profit_uppercase'); ?></td>
            <?php echo $html_column; ?>
            <td class="total_amount"><?php echo app_format_money($total_income - $total_cost_of_sales, $currency->name); ?> </td>
          </tr>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_other_income'); ?></td>
            <td></td>
            <?php echo $html_column; ?>
          </tr>
          <?php 
          $data = $this->accounting_model->get_html_custom_summary_by_income_statement($data_report['data']['other_income'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency, $data_report['display_columns_by']);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_other_income_loss = $data['total_amount'];
           ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_other_income_loss'); ?></td>
            <?php echo $html_column; ?>
            <td class="total_amount"><?php echo app_format_money($total_other_income_loss, $currency->name); ?> </td>
          </tr>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_expenses'); ?></td>
            <td></td>
            <?php echo $html_column; ?>
          </tr>
          <?php 
            $data = $this->accounting_model->get_html_custom_summary_by_income_statement($data_report['data']['expenses'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency, $data_report['display_columns_by']);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_expenses = $data['total_amount'];
             ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_expenses'); ?></td>
            <?php echo $html_column; ?>
            <td class="total_amount"><?php echo app_format_money($total_expenses, $currency->name); ?> </td>
          </tr>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_other_expenses'); ?></td>
            <?php echo $html_column; ?>
            <td></td>
          </tr>
          <?php 
          $data = $this->accounting_model->get_html_custom_summary_by_income_statement($data_report['data']['other_expenses'], ['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency, $data_report['display_columns_by']);
            $row_index = $data['row_index'];
            echo new_html_entity_decode($data['html']);
            $total_other_expenses = $data['total_amount'];
          
            $row_index += 1;
          ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-<?php echo new_html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_other_expenses'); ?></td>
            <?php echo $html_column; ?>
            <td class="total_amount"><?php echo app_format_money($total_other_expenses, $currency->name); ?> </td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo new_html_entity_decode($row_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('net_earnings_uppercase'); ?></td>
            <?php echo $html_column; ?>
            <td class="total_amount"><?php echo app_format_money(($total_income + $total_other_income_loss) - ($total_cost_of_sales + $total_expenses + $total_other_expenses), $currency->name); ?> </td>
          </tr>
        </tbody>
    </table>
  </div>
</div>