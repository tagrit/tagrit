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
          switch ($data_report['display_columns_by']) {
            case 'total_only':
              echo '<td></td>';
              break;

            case 'months':
              $start = $month = strtotime($data_report['from_date']);
              $end = strtotime($data_report['to_date']);
              while($month <= $end)
              {
                echo '<td></td>';
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

                  echo '<td></td>';

                  $from_date = date('Y-m-d', strtotime('+3 month', strtotime($from_date)));

                  if(strtotime($from_date) > strtotime($to_date)){
                      $month_2 = date('m', strtotime($from_date));
                      $year_2 = date('Y', strtotime($from_date));

                      if($month . ' - ' . $year != $month_2 . ' - ' . $year_2){
                          echo '<td></td>';
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

                  echo '<td></td>';

                  $from_date = date('Y-m-d', strtotime('+1 year', strtotime($from_date)));

                  if(strtotime($from_date) > strtotime($to_date)){
                      $year_2 = date('Y', strtotime($to_date));
                  
                      if($year != $year_2){
                          echo '<td></td>';
                      }
                  }
              }
              echo '<td></td>';
              break;

            case 'vendors':
              $this->load->model('purchase/purchase_model');
              $vendors = $this->purchase_model->get_vendor();
              foreach ($vendors as $key => $vendor) {
                  echo '<td></td>';
              }
              echo '<td></td>';
              echo '<td></td>';
              break;

            case 'employees':
              $this->load->model('staff_model');
              $staffs = $this->staff_model->get();
              foreach ($staffs as $key => $staff) {
                  echo '<td></td>';
              }
              echo '<td></td>';
              break;
            case 'customers':
              $this->load->model('clients_model');
              $clients = $this->clients_model->get();
              foreach ($clients as $key => $client) {
                echo '<td></td>';
              }
              echo '<td></td>';
              echo '<td></td>';
              break;
            default:
              // code...
              break;
          }
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

          
          $total = 0;
          ?>
            <?php foreach ($data_report['data'] as $val) {
              $total_amount = 0;
              $row_index += 1;
            ?>
            <tr class="treegrid-<?php echo new_html_entity_decode($row_index); ?> treegrid-parent-10000 ">
              <td>
              <?php echo new_html_entity_decode($val['name']); ?> 
              </td>
              <?php 
                foreach($val['columns'] as $column){ ?>
                  <td class="total_amount">
                   <?php echo app_format_money($column, $currency->name); ?> 
                  </td>
              <?php 
              $total += $column;
              $total_amount += $column;
              } ?>
              <?php if ($data_report['display_columns_by'] != 'total_only') { ?>
              <td class="total_amount">
                <?php echo app_format_money($total_amount, $currency->name); ?> 
              </td>
              <?php } ?>
            </tr>
          <?php } ?>
        </tbody>
    </table>
  </div>
</div>