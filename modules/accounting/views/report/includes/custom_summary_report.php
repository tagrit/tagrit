<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <a href="<?php echo admin_url('accounting/report'); ?>"><?php echo _l('back_to_report_list'); ?></a>
          <hr />
          <div class="row">
            <div class="col-md-10">
              <div class="row">
              <?php echo form_open(admin_url('accounting/view_report'),array('id'=>'filter-form')); ?>
                <div class="col-md-3">
                  <?php echo render_date_input('from_date','from_date', _d($from_date)); ?>
                </div>
                <div class="col-md-3">
                  <?php echo render_date_input('to_date','to_date', _d($to_date)); ?>
                </div>
                <div class="col-md-3">
                <?php 
                $display_rows_by = [
                  1 => ['id' => 'customers', 'name' => _l('customers')],
                  2 => ['id' => 'vendors', 'name' => _l('vendors')],
                  3 => ['id' => 'employees', 'name' => _l('employees')],
                  4 => ['id' => 'product_service', 'name' => _l('product_service')],
                  5 => ['id' => 'income_statement', 'name' => _l('income_statement')],
                  6 => ['id' => 'balance_sheet', 'name' => _l('balance_sheet')],
                  7 => ['id' => 'balance_sheet_summary', 'name' => _l('balance_sheet_summary')],
                ];
                echo render_select('display_rows_by', $display_rows_by, array('id', 'name'),'display_rows_by', $accounting_display_rows_by, array(), array(), '', '', false);
                ?>
              </div>
              <div class="col-md-3">
                <?php 
                $display_columns_by = [
                  1 => ['id' => 'total_only', 'name' => _l('total_only')],
                  2 => ['id' => 'months', 'name' => _l('months')],
                  3 => ['id' => 'quarters', 'name' => _l('quarters')],
                  4 => ['id' => 'years', 'name' => _l('years')],
                  5 => ['id' => 'customers', 'name' => _l('customers')],
                  6 => ['id' => 'vendors', 'name' => _l('vendors')],
                  7 => ['id' => 'employees', 'name' => _l('employees')],
                  8 => ['id' => 'product_service', 'name' => _l('product_service')],
                ];
                echo render_select('display_columns_by', $display_columns_by, array('id', 'name'),'display_columns_by', $accounting_display_columns_by, array(), array(), '', '', false);
                ?>
              </div>
                <div class="col-md-3">
                  <?php 
                  $method = [
                          1 => ['id' => 'cash', 'name' => _l('cash')],
                          2 => ['id' => 'accrual', 'name' => _l('accrual')],
                         ];
                  echo render_select('accounting_method', $method, array('id', 'name'),'accounting_method', $accounting_method, array(), array(), '', '', false);
                  ?>
                </div>
                <div class="col-md-3">
                  <?php 
                  $page_type = [
                          1 => ['id' => 'vertical', 'name' => _l('vertical')],
                          2 => ['id' => 'horizontal', 'name' => _l('horizontal')],
                         ];
                  echo render_select('page_type', $page_type, array('id', 'name'),'page_type', '', array(), array(), '', '', false);
                  ?>
                </div>
                <div class="col-md-3">
                  <?php echo form_hidden('type', 'custom_summary_report'); ?>
                  <button type="submit" class="btn btn-info btn-submit mtop25"><?php echo _l('filter'); ?></button>
                </div>
              <?php echo form_close(); ?>
              </div>
            </div>
            <div class="col-md-2">
              <div class="btn-group pull-right mtop25">
                 <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
                 <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                       <a href="#" id="export_to_pdf_btn" onclick="printDiv(); return false;">
                       <?php echo _l('export_to_pdf'); ?>
                       </a>
                    </li>
                    <li>
                       <a href="#" onclick="printExcel(); return false;">
                       <?php echo _l('export_to_excel'); ?>
                       </a>
                    </li>
                 </ul>
              </div>
            </div>
          </div>
          <div class="row"> 
            <div class="col-md-12"> 
              <hr>
            </div>
          </div>
          <div class="page" id="DivIdToPrint">
            
        </div>
      </div>
    </div>
  </div>
</div>
<!-- box loading -->
<div id="box-loading"></div>
<?php init_tail(); ?>
</body>
</html>
