

           <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash();?>">

              <div class="row">
                <?php if(isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 1) {?>

                  <div class="col-md-3">

                        <p>Account Name: <?php echo $account_data[0]['account_name']; ?></p> 
                        <p>Status : Verified</p>


                  </div>

                <?php } ?>

                <div class="col-md-3">

                    <select id="bank_account" name="bank_account" class="selectpicker" data-width="100%">

                     <option value="">Select Bank Account</option>

                     <?php 

                     foreach($bank_accounts as $b_acc){?>

                        <option value="<?php echo $b_acc['id']; ?>" <?php echo (isset($_GET['id']) && $_GET['id']==$b_acc['id']?"selected":"");  ?> data-subtext="<?php echo $b_acc['account_type_name']; ?>"><?php echo $b_acc['name']; ?></option>

                     <?php

                     }

                     ?>

                 </select>

                </div>

                

                <div class="col-md-6">

                    <?php if(isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 0) {?>

                      <button type="button" class="btn btn-info btn-submit" id="linkButton"><?php echo _l('verify_bank_account'); ?></button>

                    <?php } ?>

                    <?php if(isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 1) {?>

                      <button type="button" class="btn btn-info btn-submit" id="linkButton"><?php echo _l('re_verify_bank_account'); ?></button>
                      <button type="button" id="delete_button" class="btn btn-warning btn-submit" onclick="updatePlaidStatus()"><?php echo _l('delete_verification'); ?></button>

                    <?php } ?>

                </div>

            </div>

           <?php if(isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 1) {?> 

            <div class="row">
                <div class="col-md-3">
                    <h5 class="heading">Last Refresh Date: </h5>
                </div>
                <div class="col-md-3">
                    <?php 
                        $value = '';
                        if(isset($refresh_data) && $refresh_data != NULL && $refresh_data[0]['refresh_date'] != NULL ){ 
                            $value = _d($refresh_data[0]['refresh_date']); 
                        }
                    ?>
                    <?php echo render_date_input('last_refresh_date', '', $value, array('disabled' => true)); ?>
                    
                </div>
            </div>

            <h4 style="">Import Transactions</h4>            
            <br>
            <div class="row">
                <div class="col-md-3">
                    <h5 class="heading">Date from which to import transactions:</h5>
                </div>
                <div class="col-md-3">
                    <?php $value = $last_updated != '' ? _d($last_updated) : ''; ?>
                    <?php echo render_date_input('from_date','',$value); ?>
                    
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-info btn-submit" id="import_button" onclick="submitForm()"><?php echo _l('import_new_transaction'); ?></button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-6">
                    <h5 class="heading">Up to 500 transactions can be imported at a time. It may take a few minutes to grab them all from your bank.</h5>
                </div>
            </div>


            <br>

            <?php } ?>
            <?php if(isset($transactions) && isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 1) { ?>        
                <table class="table table-banking">
                  <thead>
                    <th><?php echo _l('invoice_payments_table_date_heading'); ?></th>
                    <!-- <th><?php echo _l('check_#'); ?></th> -->
                    <th><?php echo _l('payee'); ?></th>
                    <th><?php echo _l('description'); ?></th>
                    <th><?php echo _l('withdrawals'); ?></th>
                    <th><?php echo _l('deposits'); ?></th>
                    <th><?php echo _l('imported_date'); ?></th>
                  </thead>
                  <tbody>
                    
                  </tbody>
                </table>

                <hr>

              <?php } ?>

            


<!-- box loading -->

<div id="box-loading"></div>


