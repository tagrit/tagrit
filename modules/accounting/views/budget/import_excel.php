<?php defined('BASEPATH') or exit('No direct script access allowed'); 
?>
<?php 
  $file_header = array();
$file_header[] = _l('acc_name');  
$file_header[] = _l('acc_year');
$file_header[] = _l('acc_type');
$file_header[] = _l('acc_month');
$file_header[] = _l('quarter');
$file_header[] = _l('acc_account');
$file_header[] = _l('acc_amount');
 ?>

<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body backdrop">
            <div id ="dowload_file_sample_for_month" class="display-inline">
            </div>
            <div id ="dowload_file_sample_for_quarter" class="display-inline">
            </div>
            <div id ="dowload_file_sample_for_year" class="display-inline">
            </div>
            <hr>
            <div class="row">
              <div class="col-md-4">
               <?php echo form_open_multipart(admin_url('accounting/import_account_excel'),array('id'=>'import_form'));?>
                  <?php echo render_input('fiscal_year_for_this_budget','fiscal_year_for_this_budget',date('Y'),'number'); ?>
                  <?php echo _l('year_and_type_note_1'); ?>
                  <br>
                 
                  <div class="form-group">
                    <div class="radio radio-primary">
                      <input type="radio" id="profit_and_loss_accounts" name="budget_type" value="profit_and_loss_accounts" checked>
                      <label for="profit_and_loss_accounts"><?php echo _l('profit_and_loss_accounts'); ?></label>
                    </div>

                    <div class="radio radio-primary">
                      <input type="radio" id="balance_sheet_accounts" name="budget_type" value="balance_sheet_accounts">
                      <label for="balance_sheet_accounts"><?php echo _l('balance_sheet_accounts'); ?></label>
                    </div>
                  </div>
                    <?php 
                      $import_type = [
                        1 => ['id' => 'month', 'name' => _l('month')],
                        2 => ['id' => 'quarter', 'name' => _l('quarter')],
                        3 => ['id' => 'year', 'name' => _l('year')]
                      ];

                      echo render_select('import_type', $import_type, array('id', 'name'),'type', 'month', array('required' => true), array(), '', '', false); ?>
                    <?php echo render_input('file_csv','choose_excel_file','','file'); ?>
                    <div class="form-group">
                      <button id="uploadfile" type="button" class="btn btn-info import" onclick="return uploadfilecsv();" ><?php echo _l('import'); ?></button>
                    </div>
                  <?php echo form_close(); ?>
              </div>
              <div class="col-md-8">
                <div class="form-group" id="file_upload_response">
                  
                </div>
                
              </div>
            </div>
            <?php if(!isset($simulate)) { ?>
            <ul>
              <li class="text-danger"><i class="font-italic">1. <?php echo _l('file_xlsx_budget'); ?></i></li>
              <li class="text-danger"><i class="font-italic">2. <?php echo _l('file_xlsx_budget_1'); ?></i></li>
              <li class="text-danger"><i class="font-italic">3. <?php echo _l('file_xlsx_budget_2'); ?></i></li>
              <li class="text-danger"><i class="font-italic">4. <?php echo _l('file_xlsx_budget_3'); ?></i></li>
            </ul>

              <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- box loading -->
<div id="box-loading"></div>
<?php init_tail(); ?>

<?php require 'modules/accounting/assets/js/budget/import_excel_budget_js.php';?>
</body>
</html>
