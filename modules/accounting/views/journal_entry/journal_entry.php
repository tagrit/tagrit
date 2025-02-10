<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <?php $arrAtt = array();
                $arrAtt['data-type']='currency';
                ?>
          <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'journal-entry-form','autocomplete'=>'off')); ?>
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <hr />
          <div class="row">
            <div class="col-md-6">
              <div class="col-md-6">
                <?php $value = (isset($journal_entry) ? $journal_entry->number : $next_number); ?>
                <?php echo render_input('number','number',$value,'number'); ?>
              </div>
              <div class="col-md-6">
                <?php $value = (isset($journal_entry) ? _d($journal_entry->journal_date) : _d(date('Y-m-d'))); ?>
                <?php echo render_date_input('journal_date','journal_date',$value); ?>
              </div>
              <div class="col-md-12">
                <?php $value = (isset($journal_entry) ? $journal_entry->reference : ''); ?>
                <?php echo render_input('reference','reference',$value); ?>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
               <div class="col-md-12">
                 <div class="form-group select-placeholder"<?php if(isset($journal_entry) && !empty($journal_entry->is_recurring_from)){ ?> data-toggle="tooltip" data-title="<?php echo _l('create_recurring_from_child_error_message', [_l('invoice_lowercase'),_l('invoice_lowercase'), _l('invoice_lowercase')]); ?>"<?php } ?>>
                    <label for="recurring" class="control-label">
                    <?php echo _l('recurring_journal_entry'); ?>
                    </label>
                    <select class="selectpicker"
                    data-width="100%"
                    name="recurring"
                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                    <?php
                    // The problem is that this invoice was generated from previous recurring invoice
                    // Then this new invoice you set it as recurring but the next invoice date was still taken from the previous invoice.
                    if(isset($journal_entry) && !empty($journal_entry->is_recurring_from)){echo 'disabled';} ?>
                    >
                       <?php for($i = 0; $i <=12; $i++){ ?>
                       <?php
                          $selected = '';
                          if(isset($journal_entry)){
                            if($journal_entry->custom_recurring == 0){
                             if($journal_entry->recurring == $i){
                               $selected = 'selected';
                             }
                           }
                          }
                          if($i == 0){
                           $reccuring_string =  _l('invoice_add_edit_recurring_no');
                          } else if($i == 1){
                           $reccuring_string = _l('invoice_add_edit_recurring_month',$i);
                          } else {
                           $reccuring_string = _l('invoice_add_edit_recurring_months',$i);
                          }
                          ?>
                       <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $reccuring_string; ?></option>
                       <?php } ?>
                       <option value="custom" <?php if(isset($journal_entry) && $journal_entry->recurring != 0 && $journal_entry->custom_recurring == 1){echo 'selected';} ?>><?php echo _l('recurring_custom'); ?></option>
                    </select>
                 </div>
              </div>
              <div class="recurring_custom <?php if((isset($journal_entry) && $journal_entry->custom_recurring != 1) || (!isset($journal_entry))){echo 'hide';} ?>">
                 <div class="col-md-6">
                    <?php $value = (isset($journal_entry) && $journal_entry->custom_recurring == 1 ? $journal_entry->recurring : 1); ?>
                    <?php echo render_input('repeat_every_custom','',$value,'number',array('min'=>1)); ?>
                 </div>
                 <div class="col-md-6">
                    <select name="repeat_type_custom" id="repeat_type_custom" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                       <option value="day" <?php if(isset($journal_entry) && $journal_entry->custom_recurring == 1 && $journal_entry->recurring_type == 'day'){echo 'selected';} ?>><?php echo _l('invoice_recurring_days'); ?></option>
                       <option value="week" <?php if(isset($journal_entry) && $journal_entry->custom_recurring == 1 && $journal_entry->recurring_type == 'week'){echo 'selected';} ?>><?php echo _l('invoice_recurring_weeks'); ?></option>
                       <option value="month" <?php if(isset($journal_entry) && $journal_entry->custom_recurring == 1 && $journal_entry->recurring_type == 'month'){echo 'selected';} ?>><?php echo _l('invoice_recurring_months'); ?></option>
                       <option value="year" <?php if(isset($journal_entry) && $journal_entry->custom_recurring == 1 && $journal_entry->recurring_type == 'year'){echo 'selected';} ?>><?php echo _l('invoice_recurring_years'); ?></option>
                    </select>
                 </div>
              </div>
              <div id="cycles_wrapper" class="<?php if(!isset($journal_entry) || (isset($journal_entry) && $journal_entry->recurring == 0)){echo ' hide';}?>">
                 <div class="col-md-12">
                    <?php $value = (isset($journal_entry) ? $journal_entry->cycles : 0); ?>
                    <div class="form-group recurring-cycles">
                      <label for="cycles"><?php echo _l('recurring_total_cycles'); ?>
                        <?php if(isset($journal_entry) && $journal_entry->total_cycles > 0){
                          echo '<small>' . _l('cycles_passed', $journal_entry->total_cycles) . '</small>';
                        }
                        ?>
                      </label>
                      <div class="input-group">
                        <input type="number" class="form-control"<?php if($value == 0){echo ' disabled'; } ?> name="cycles" id="cycles" value="<?php echo $value; ?>" <?php if(isset($journal_entry) && $journal_entry->total_cycles > 0){echo 'min="'.($journal_entry->total_cycles).'"';} ?>>
                        <div class="input-group-addon">
                          <div class="checkbox">
                            <input type="checkbox"<?php if($value == 0){echo ' checked';} ?> id="unlimited_cycles">
                            <label for="unlimited_cycles"><?php echo _l('cycles_infinity'); ?></label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <table id="journal-entry-rows" class="table invoice-mapping-table items">
            <thead>
              
            <tr>
              <th width="25%"><?php echo _l('account'); ?></th>
              <th width="15%"><?php echo _l('debit'); ?></th>
              <th width="15%"><?php echo _l('credit'); ?></th>
              <th width="35%"><?php echo _l('description'); ?></th>
              <th width="10%"></th>
            </tr>
            </thead>
          <?php if(isset($journal_entry)){
            $i               = 0;
            foreach($journal_entry->details as $detail){ 
              $debit_amount = $detail['debit'] != 0 ? number_format($detail['debit'],2) : '';
              $credit_amount = $detail['credit'] != 0 ? number_format($detail['credit'],2) : '';
              ?>
              <tr class="bill-debit-account-<?php echo new_html_entity_decode($i); ?> template_children">
               <td>
                  <?php echo render_select('account['.$i.']', $accounts, array('id','name', 'account_type_name'), '',$detail['account'], array('required' => true)); ?>
               </td>
               <td>
                  <?php echo render_input('debit_amount['.$i.']', '',$debit_amount,'text', array('data-type' => 'currency', 'data-index' => $i)); ?>
               </td>
               <td>
                  <?php echo render_input('credit_amount['.$i.']', '',$credit_amount,'text', array('data-type' => 'currency', 'data-index' => $i)); ?>
               </td>
               <td>
                  <?php echo render_input('description_detail['.$i.']', '',$detail['description']); ?>
               </td>
               <td>
                  <button name="add_template" class="btn <?php if($i == 0){ echo 'new_template btn-success'; }else{ echo 'remove_template btn-danger';} ?>" data-ticket="true" type="button"><i class="fa <?php if($i == 0){ echo 'fa-plus'; }else{ echo 'fa-minus';} ?>"></i></button>
               </td>
            </tr>
            <?php 
            $i++;
            }
          }else{
            ?>
            <tr class="bill-debit-account-0 bill-item-id-0 template_children">
               <td>
                  <?php echo render_select('account[0]', $accounts, array('id','name', 'account_type_name'), '','', array('required' => true)); ?>
               </td>
               <td>
                  <?php echo render_input('debit_amount[0]', '','','text', array('data-type' => 'currency', 'data-index' => 0)); ?>
               </td>
               <td>
                  <?php echo render_input('credit_amount[0]', '','','text', array('data-type' => 'currency', 'data-index' => 0)); ?>
               </td>
               <td>
                  <?php echo render_input('description_detail[0]', '',''); ?>
               </td>
               <td>
                  <button name="add_template" class="btn new_template btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
               </td>
            </tr>
          <?php } ?>
          </table>
          <div class="col-md-8 col-md-offset-4">
         <table class="table text-right">
            <tbody>
                <tr>
                  <td></td>
                  <td class="text-right bold"><?php echo _l('debit'); ?></td>
                  <td class="text-right bold"><?php echo _l('credit'); ?></td>
                </tr>
               <tr>
                  <td><span class="bold"><?php echo _l('invoice_total'); ?> :</span>
                  </td>
                  <td class="total_debit">
                    <?php $value = (isset($journal_entry) ? $journal_entry->amount : 0); ?>
                    <?php echo app_format_money($value, $currency->name); ?>
                  </td>
                  <td class="total_credit">
                    <?php $value = (isset($journal_entry) ? $journal_entry->amount : 0); ?>
                    <?php echo app_format_money($value, $currency->name); ?>
                  </td>
               </tr>
            </tbody>
         </table>
        </div>
          <?php echo form_hidden('amount'); ?>
          <div class="row">
            <div class="col-md-12">
              <p class="bold"><?php echo _l('dt_expense_description'); ?></p>
              <?php $value = (isset($journal_entry) ? $journal_entry->description : ''); ?>
              <?php echo render_textarea('description','',$value,array(),array(),'','tinymce'); ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">    
              <div class="modal-footer">
                <button type="button" class="btn btn-info journal-entry-form-submiter"><?php echo _l('submit'); ?></button>
              </div>
            </div>
          </div>
          <?php echo form_close(); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/accounting/assets/js/journal_entry/journal_entry_js.php';?>