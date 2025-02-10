<?php defined('BASEPATH') or exit('No direct script access allowed'); 
?>
<?php 
  $file_header = array();
$file_header[] = _l('invoice_payments_table_date_heading');
$file_header[] = _l('withdrawals');
$file_header[] = _l('deposits');
$file_header[] = _l('payee');
$file_header[] = _l('description');

 ?>

<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div id ="dowload_file_sample">
            
            
            </div>

            <?php if(!isset($simulate)) { ?>
            <ul>
              <li class="text-danger">1. <?php echo _l('file_xlsx_banking'); ?></li>
              <li class="text-danger">3. <?php echo _l('file_xlsx_format'); ?></li>
            </ul>
            <div class="table-responsive no-dt">
              <table class="table table-hover table-bordered">
                <thead>
                  <tr>
                    <?php
                      $total_fields = 0;
                      
                      for($i=0;$i<count($file_header);$i++){
                          ?>
                          <th class="bold">
                          <?php if($i != 4){ ?>
                            <span class="text-danger">*</span> 
                          <?php } ?>
                            <?php echo new_html_entity_decode($file_header[$i]) ?> </th>
                          <?php 
                          ?>
                          
                          <?php

                          $total_fields++;
                      }

                    ?>

                    </tr>
                  </thead>
                  <tbody>
                    <?php for($i = 0; $i<1;$i++){
                      echo '<tr>';
                      for($x = 0; $x<count($file_header);$x++){
                        echo '<td>- </td>';
                      }
                      echo '</tr>';
                    }
                    ?>
                  </tbody>
                </table>
              </div>
              <hr>

              <?php } ?>
            
            <div class="row">
              <div class="col-md-4">
               <?php echo form_open_multipart(admin_url('accounting/import_xlsx_banking'),array('id'=>'import_form')) ;?>
                    <?php echo form_hidden('leads_import','true'); ?>
                    <?php echo render_select('bank_account',$bank_accounts,array('id','name', 'account_type_name'),'bank_account', $bank_id); ?>
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
            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- box loading -->
<div id="box-loading"></div>
<?php init_tail(); ?>

<?php require 'modules/accounting/assets/js/banking/import_xlsx_posted_bank_transactions_js.php';?>
</body>
</html>
