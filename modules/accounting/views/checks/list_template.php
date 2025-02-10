<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12">
   <div class="panel_s mbot10">
      <div class="panel-body _buttons">
         <div class="display-block text-right">
            <div class="row">
               <div class="col-md-12">
                  <div class="row">
                     <div class="col-md-12 mbot10">
                        <a href="<?php echo admin_url('accounting/check'); ?>" class="btn btn-info pull-left new new-check-list mright5 mbot5"><?php echo _l('create_new_check'); ?></a>
                        <a href="#" class="btn btn-info pull-left new new-check-list mright5 mbot5" onclick="reprint_check(); return false;"><?php echo _l('reprint'); ?></a>
                        <!-- <a href="#" class="btn btn-info pull-left new new-check-list mright5 mbot5" onclick="print_form(); return false;"><?php echo _l('print_checks'); ?></a> -->
                        <div id="div_check_btn_left" style="display: table;"></div>
                     </div>
                  
                     <div class="col-md-2">

                        <div class="form-inline" style="display: flex;">
                         
                         <select name="bank_account_check" id="bank_account_check" data-width="100%" class="selectpicker" data-live-search="true" data-none-selected-text="<?php echo _l('acc_filter_by_bank_account'); ?>">
                         <option value=""></option>
                            <?php foreach($accounts as $ven){
                                $select = '';
                                
                                echo '<option value="'.$ven['id'].'" '.$select.'>'. $ven['name'].'</option>';
                            } ?>
                         </select>
                      </div>
                     </div>

                     <div class="col-md-2">

                        <div class="form-inline" style="display: flex;">
                         <!-- <label for="vendor_ft" class="mtop10 mright10" style="white-space: nowrap;">Bank Account</label> -->
                         <select name="vendor_ft" id="vendor_ft" data-width="100%" class="selectpicker" data-live-search="true" data-none-selected-text="<?php echo _l('acc_filter_by_vendor'); ?>">
                         <option value=""></option>
                            <?php foreach($vendors as $vendor){
                                $select = '';
                                
                                echo '<option value="'.$vendor['userid'].'" '.$select.'>'. $vendor['company'].'</option>';
                            } ?>
                         </select>
                      </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-inline" style="display: flex;">
                         <?php 
                           $statuses = [
                              ['id' => 1, 'name' => _l('issued')],
                              ['id' => 2, 'name' => _l('printing_error')],
                              ['id' => 3, 'name' => _l('void')],
                              ['id' => 4, 'name' => _l('not_issued_yet')],
                           ];
                         ?>

                         <select name="status" id="status" data-width="100%" class="selectpicker" data-live-search="true" data-none-selected-text="<?php echo _l('acc_filter_by_status'); ?>">
                         <option value=""></option>
                            <?php foreach($statuses as $status){
                                $select = '';
                                
                                echo '<option value="'.$status['id'].'" '.$select.'>'. $status['name'].'</option>';
                            } ?>
                         </select>
                      </div>
                     </div>

                     <div class="col-md-2">
                         <?php echo render_date_input('from_date_ft','','', ['placeholder' => _l('acc_from_date')]); ?>
                     </div>
                     <div class="col-md-2">
                         <?php echo render_date_input('to_date_ft','','', ['placeholder' => _l('acc_to_date')]); ?>
                     </div>
                   
                     <div class="col-md-1">
                        <a href="<?php echo admin_url('accounting/configure_checks'); ?>"  class="btn btn-icon" data-toggle="tooltip" data-placement="top" title="<?php echo _l('configure_checks'); ?>"><i class="fa fa-gear"></i></a>

                         <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs pull-right" onclick="check_toggle_small_view('.table-checks','#check'); return false;" data-toggle="tooltip" title="<?php echo _l('estimates_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
                     </div>
                  </div>
               </div>              
             
            </div>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="col-md-12" id="small-table">
         <div class="panel_s">
            <div class="panel-body">
               <!-- if checkid found in url -->
               <?php echo form_hidden('checkid',$checkid); ?>
              
              
               <?php $this->load->view('accounting/checks/checks_table_html'); ?>
                
            </div>
         </div>
      </div>
      <div class="col-md-9 small-table-right-col">
         <div id="check" class="hide">
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="checks-to-print-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title add-title"><?php echo _l('print_checks')?></h4>
         </div>
         <div class="modal-body">
               <?php echo form_open(admin_url('accounting/download_checks'),array('id'=>'download_checks')); ?>

            <div class="row">
               <div class="col-md-6">
                  <div class="form-inline" style="display: flex;">
                      <label for="bank_account_form_check" class="mtop10 mright10" style="white-space: nowrap;"><?php echo _l('acc_bank_account'); ?></label>
                      <select name="bank_account_form_check" id="bank_account_form_check" data-width="100%" class="selectpicker" data-live-search="true" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                      <option value=""></option>
                         <?php foreach($accounts as $ven){
                             $select = '';
                             
                             echo '<option value="'.$ven['id'].'" '.$select.'>'. $ven['name'].'</option>';
                         } ?>
                      </select>
                   </div>
               </div>
               
            </div>
            <h4 class=""><?php echo _l('please_select_checks_to_print'); ?></h4>
            <table class="table table-checks-to-print scroll-responsive dataTable">
                 <thead>
                    <tr>
                        <th class="not-export sorting_disabled" rowspan="1" colspan="1" aria-label=" - ">
                           <span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="checks-to-print"><label for="mass_select_all">Select all</label></div>
                        </th>
                       <th><?php echo _l('acc_date'); ?></th>
                       <th><?php echo _l('check_number'); ?></th>
                       <th><?php echo _l('payee'); ?></th>
                       <th class="text-right"><?php echo _l('amount'); ?></th>
                    </tr>
                 </thead>
                 <tbody>
                </tbody>
              </table>
              <div class="row">
                 <div class="col-md-5 col-md-offset-7">
                     <table class="table text-right">
                        <tbody>
                           <tr>
                              <td><span class="bold"><?php echo _l('invoice_total'); ?></span>
                              </td>
                              <td id="checks-to-print-total-amount">
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                 </div>
              <hr>
              <?php echo form_hidden('print_again', 0); ?>
               <div class="text-right">
                  <a href="#" class="btn btn-info" onclick="reprint_check(); return false;"><?php echo _l('reprint'); ?></a>
                  <!-- <a href="#" class="btn btn-info" onclick="select_all(); return false;"><?php echo _l('select_all'); ?></a>
                  <a href="#" class="btn btn-info" onclick="select_none(); return false;"><?php echo _l('select_none'); ?></a> -->
                  <!-- <a href="#" class="btn btn-info" onclick="clear_print_later(); return false;"><?php echo _l('clear'); ?></a> -->
                  <a href="#" class="btn btn-info" data-dismiss="modal"><?php echo _l('cancel'); ?></a>
                  <!-- <button type="btn" onclick="print_check(false); return false;" class="btn btn-info"><?php echo _l('print'); ?></button> -->
                  <button type="btn" onclick="print_check(true); return false;" class="btn btn-info"><?php echo _l('print_and_issue'); ?></button>
               </div> 

                <!-- default accrual -->

               <?php echo form_close(); ?>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="reprint-check-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <?php echo form_open(admin_url('accounting/reprint_check'),array('id'=>'reprint_check')); ?>
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title add-title"><?php echo _l('reprint')?></h4>
         </div>
         <div class="modal-body">
            <div class="form-group">
               <lable for="reprint_check"><span class="text-danger">* </span><?php echo _l('select_checks_to_reprint'); ?></label>
               <select name="reprint_check[]" id="reprint_check" data-width="100%" class="selectpicker" required="true" data-live-search="true" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" multiple="1">
                  <?php foreach($list_checks as $che){ ?>
                  <option value="<?php echo new_html_entity_decode($che['id']); ?>"><?php echo str_pad($che['number'], 4, '0', STR_PAD_LEFT); ?></option>
                  <?php } ?>
               </select>
            </div>
            <div class="form-group">
             <div class="checkbox checkbox-primary">
               <input type="checkbox" name="is_new_check_number" checked id="is_new_check_number" value="1">
               <label for="is_new_check_number"><?php echo _l('new_check_number'); ?> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('new_check_number_note'); ?>"></i></label>
             </div>
           </div>
            <div class="div_new_check_number">
            <?php echo render_input('new_check_number','new_beginning_check_number', '','text'); ?>
            </div>
         </div>
         <div class="modal-footer">
           <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
           <button type="submit" class="btn btn-info intext-btn"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>
<div class="modal fade" id="void-check-modal">
   <div class="modal-dialog">
      <div class="modal-content">
         <?php echo form_open(admin_url('accounting/void_check'),array('id'=>'void_check')); ?>
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title add-title"><?php echo _l('void')?></h4>
         </div>
         <div class="modal-body">
            <?php echo form_hidden('void_check'); ?>
            <?php echo render_textarea('reason_for_void','reason_for_void'); ?>
            <h5>Are you sure you want to void this check?</h5>
         </div>
         <div class="modal-footer">
           <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
           <a href="#" onclick="void_check_save(); return false;" class="btn btn-info intext-btn">OK</a>
         </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>
<div class="content_cart hide"></div>