<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12 <?php if(isset($check)){ echo 'no-padding';} ?>">
 <div class="panel_s">
  <div class="panel-body">
    <input type="hidden" name="max_check_number" value="4">
    <?php echo form_hidden('bank_account_id', isset($check) ? $check->bank_account : ''); ?>
    <?php echo form_open_multipart(admin_url('accounting/check'),array('id'=>'check-form')) ;?>
    <?php $check_id = isset($check) ? $check->id : '';
    echo form_hidden('id', $check_id); 
    $routing_number_icon_a = 'a';
    $routing_number_icon_b = 'a';

    $bank_account_icon_a = 'a';
    $bank_account_icon_b = 'a';

    $current_check_no_icon_a = 'a';
    $current_check_no_icon_b = 'a';

    $check_type = 'type_1';

    $acc_routing_number_icon_a = get_option('acc_routing_number_icon_a');
    if($acc_routing_number_icon_a != ''){
      $routing_number_icon_a = $acc_routing_number_icon_a;
    }
    $acc_routing_number_icon_b = get_option('acc_routing_number_icon_b');
    if($acc_routing_number_icon_b != ''){
      $routing_number_icon_b = $acc_routing_number_icon_b;
    }

    $acc_bank_account_icon_a = get_option('acc_bank_account_icon_a');
    if($acc_bank_account_icon_a != ''){
      $bank_account_icon_a = $acc_bank_account_icon_a;
    }
    $acc_bank_account_icon_b = get_option('acc_bank_account_icon_b');
    if($acc_bank_account_icon_b != ''){
      $bank_account_icon_b = $acc_bank_account_icon_b;
    }

    $acc_current_check_no_icon_a = get_option('acc_current_check_no_icon_a');
    if($acc_current_check_no_icon_a != ''){
      $current_check_no_icon_a = $acc_current_check_no_icon_a;
    }
    $acc_current_check_no_icon_b = get_option('acc_current_check_no_icon_b');
    if($acc_current_check_no_icon_b != ''){
      $current_check_no_icon_b = $acc_current_check_no_icon_b;
    }

    $acc_check_type = get_option('acc_check_type');
    if($acc_check_type != ''){
      $check_type = $acc_check_type;
    }

    $amount_attr = '';
    $check_amount = (isset($check) ? number_format($check->amount,2) : number_format($bill_amount, 2));
    ?>
   <?php if(!isset($check) || (isset($check) && isset($is_edit))){ ?>
    <div class="row">
      <div class="col-md-6">
         <h4><?php echo _l('check_ensure_configured_note_1'); ?></h4>
      </div>
      <div class="col-md-6">
        
      </div>
    </div>
    <hr class="hr-panel-heading" />
    <div class="row">
      <div class="col-md-3 mbot15">
        <div class="row">
          <div class="col-md-12">
            <a href="javascript:void(0);" class="text-mute" onclick="open_config(); return false;"><p class="mbot5"><i class="fa fa-gear"></i><?php echo ' '._l('acc_configuration'); ?><i id="i-angel" class="fa fa-angle-left pull-right"></i></p></a>
            <hr class="mtop5 mbot5">
          </div>
          <div id="config_div" class="col-md-12 hide">
              <label class="custom-checkbox">
                <?php echo _l('include_company_name_address'); ?>
                <?php $value = (isset($check) ? $check->include_company_name_address : 1); ?>
                <input type="checkbox" name="include_company_name_address" value="1" <?php if($value == 1){ echo 'checked="checked"'; } ?> >
                <span class="checkmark"></span>
              </label>
              <br>
              <label class="custom-checkbox">
               <?php echo _l('include_routing_account_numbers') ?>
               <?php $value = (isset($check) ? $check->include_routing_account_numbers : 1); ?>
               <input type="checkbox" name="include_routing_account_numbers" value="1" <?php if($value == 1){ echo 'checked="checked"'; } ?>>
               <span class="checkmark"></span>
             </label>
             <br>
             <label class="custom-checkbox">
               <?php echo _l('include_check_number') ?>
               <?php $value = (isset($check) ? $check->include_check_number : 1); ?>
               <input type="checkbox" name="include_check_number" value="1" <?php if($value == 1){ echo 'checked="checked"'; } ?>>
               <span class="checkmark"></span>
             </label>
             <br>
             <label class="custom-checkbox">
               <?php echo _l('include_bank_name') ?>
               <?php $value = (isset($check) ? $check->include_bank_name : 1); ?>
               <input type="checkbox" name="include_bank_name" value="1" <?php if($value == 1){ echo 'checked="checked"'; } ?>>
               <span class="checkmark"></span>
             </label>
           </div>
         </div>
     </div>

     <div class="col-md-12">
      <div class="row">
        <div class="col-md-6">
          <?php $value = (isset($check) ? $check->bank_account : $bank_account_check); ?>
          <?php echo render_select('bank_account', $accounts, array('id', 'name'), '<span class="text-danger">* </span>'. _l('acc_bank_account'), $value); ?>
          
      </div>
      <div class="col-md-6 ">
        <?php echo render_input('bank_account_balance', 'balance', '', 'text', array('disabled' => true)); ?>
      </div>
      <div class="col-md-6">
        <?php $value = (isset($check) ? $check->number : 0); ?>
        <?php echo render_input('check_number', 'check_number', $value, 'number'); ?>
        
      </div>
    </div>


  <?php 
        if(is_numeric($bill_ids) || $bill != '' || (isset($check) && $check->bill_items != '')){

          if(!isset($check)){
            $check_amount = 0;
          }

          if(is_numeric($bill_ids)){
            $bill_debit = $this->accounting_model->get_bill_mapping_details($bill_ids, 'debit');
            $bill_item = $this->accounting_model->get_bill_mapping_details($bill_ids, 'item');
            $bill_items = array_merge($bill_debit, $bill_item);

          }else{
            $bill_debit = $this->accounting_model->get_bill_mapping_details($bill, 'debit');
            $bill_item = $this->accounting_model->get_bill_mapping_details($bill, 'item');
            $bill_items = array_merge($bill_debit, $bill_item);
          }

            foreach($bill_items as $key => $item){
                if($item['type'] == 'item'){
                    $bill_items[$key]['name'] = acc_get_item_name_by_id($item['item_id']);
                }else{
                    $bill_items[$key]['name'] = isset($account_name[$item['account']]) ? $account_name[$item['account']] : '';
                }
            }
            $value = (isset($check) ? explode(',', $check->bill_items) : '');
        echo render_select('bill_items[]',$bill_items,array('id','name','amount'),'<small class="req text-danger">* </small> Bill Item', $value, array('multiple' => true, 'data-actions-box' => true, 'required' => true), array(), '', '', false);
        $amount_attr = 'readonly="true"';
      ?>

       <div id="pay-bill-items">
           <?php if(isset($check)){ ?>
            <table class="table invoice-mapping-table">
                <thead>
                <tr>
                  <th width="50%"><?php echo _l('acc_item'); ?></th>
                  <th width="25%"><?php echo _l('amount'); ?></th>
                  <th width="25%"><?php echo _l('acc_amount_paid'); ?></th>
                </tr>
                </thead>
                <tbody>
           <?php foreach ($check->pay_bill_item_paid as $key => $value) {
                echo '<tr>
                   <td>
                      '. render_input('pay_bill_item['.$value['item_id'].']', '', $value['item_name'],'text', array('readonly' => true)).'
                   </td>
                   <td>
                      '. render_input('pay_bill_amount['.$value['item_id'].']', '',number_format($value['item_amount'],2),'text', array('readonly' => true, 'data-type' => 'currency')).'
                   </td>
                   <td>
                      '. render_input('pay_bill_amount_paid['.$value['item_id'].']', '',number_format($value['amount_paid'],2),'text', array('required' => true, 'data-type' => 'currency', 'max-amount' => $value['item_amount'])).'
                   </td>
                </tr>';
            } ?>
                </tbody>
            </table>
           <?php } ?>
       </div>

      <?php } ?>
  </div>
 

</div>
<?php } ?>


<div class="col-md-12">
 <div class="row frcard">
  <?php if(isset($check) && $check->issue == 3){ ?>
    <img src="<?php echo site_url('modules/accounting/assets/images/void-dataversity.png'); ?>" class="img_void_check_style">
  <?php } ?>
  <?php $card_image = site_url('modules/accounting/assets/images/check_card.png') ?>
  <div class="col-md-10 check-card" style="background: #f0f0f0 url('<?php echo new_html_entity_decode($card_image); ?>')">
    <div class="row mbot15">
      <div class="col-md-4">
        <div class="address<?php echo ((isset($check) && $check->include_company_name_address != 1) ? ' hide' : '') ?>">
          <h4 class="no-margin">
            <?php echo new_html_entity_decode($company_name); ?>
          </h4>
          <strong>
            <?php  if(isset($check)){
              $address = $check->address;
              $city = $check->city;
              $state = $check->state;
              $zip = $check->zip;
            }
            echo new_html_entity_decode($address); 
            echo form_hidden('address', $address);
            
            ?>                
          </strong>
          <br>
          <strong>
            <?php 
            echo new_html_entity_decode($city.' '.$state.' '.$zip); 
            echo form_hidden('city', $city);
            echo form_hidden('state', $state);
            echo form_hidden('zip', $zip);
            ?>
          </strong>
        </div>
      </div>
      <div class="col-md-4">
        <div class="bank-name <?php echo ((isset($check) && $check->include_bank_name != 1) ? ' hide' : '') ?>">
          <?php if(isset($check)){ ?>
            <h4 class="no-margin">
              <?php echo new_html_entity_decode($check->bank_name); ?>
              <?php echo form_hidden('bank_name', $check->bank_name); ?>
            </h4>
            <strong>
              <?php echo new_html_entity_decode($check->address_line_1); ?> 
            </strong>
            <?php echo form_hidden('address_line_1', $check->address_line_1); ?>
            <br>
            <strong>
              <?php echo new_html_entity_decode($check->address_line_2); ?>             
            </strong>
            <?php echo form_hidden('address_line_2', $check->address_line_2); ?>
          <?php } ?>
        </div>
      </div>
      <div class="col-md-4">
        <?php 
        $number = 0;

        if(isset($check)){
          $number = $check->number;
          $bill = $check->bill;
        }

        echo form_hidden('number', $number);
        echo form_hidden('bill', $bill);
        echo form_hidden('bill_ids', $bill_ids);
        ?>
        <h4 class="pull-right bold check_number_label no-margin <?php echo ((isset($check) && $check->include_check_number != 1) ? ' hide' : '') ?>"><?php echo str_pad($number, 4, '0', STR_PAD_LEFT); ?></h4>

        <?php $value = (isset($check) ? _d($check->date) : _d(date('Y-m-d'))); ?>
        <div class="form-inline date-input"  style="display: flex;">
          <label for="date" class="mtop10 mright10">Date</label>
          <div class="input-group date"><input type="text" id="date" name="date" class="form-control datepicker" value="<?php echo new_html_entity_decode($value); ?>" autocomplete="off"><div class="input-group-addon">
            <i class="fa fa-calendar calendar-icon"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php $value = (isset($check) ? $check->rel_id : $vendor); ?>
  <div class="row mtop10">
    <div class="col-md-9">
      <div class="form-inline" style="display: flex;">
       <label for="rel_id" class="mtop10 mright10" style="white-space: nowrap;"><?php echo _l('pay_to_the_order_of'); ?></label>
       <select name="rel_id" id="rel_id" data-width="100%" required="true" class="selectpicker" data-live-search="true" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
         <option value=""></option>
         <?php foreach($vendors as $ven){
          $select = '';
          if( $value == $ven['userid']){
            $select = 'selected';
          }else{
            $select = '';
          }
          echo '<option value="'.$ven['userid'].'" '.$select.'>'. $ven['company'].'</option>';
        } ?>
      </select>
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-inline"  style="display: flex; width: 100%;">
      <label for="amount" class="mtop10 mright10"><?php echo new_html_entity_decode($currency->symbol); ?></label>
      <div class="form-group" app-field-wrapper="amount" style="width: 100%;">
       <input type="text" id="amount" name="amount" class="form-control" value="<?php echo new_html_entity_decode($check_amount); ?>" style="width: 100%;" data-type="currency" <?php echo $amount_attr; ?>>
     </div>
   </div>
 </div>
</div>
<div class="row mtop15 mbot5">
  <div class="col-md-1">
  </div>
  <div class="col-md-8 money_text check-border-bottom bold"></div>
  <div class="col-md-3 bold">
   <?php echo 'Dollars'; ?>
 </div>
</div>

<div class="row mtop15 mbot5" style="height: 50px">
  <div class="col-md-1"></div>
  <div id="vendor-address" class="col-md-6">
    
    <?php $value = (isset($check) ? $check->rel_id : $vendor); ?>
    <?php 
          echo acc_format_organization_info($value);
       ?>
  </div>
  <div class="col-md-2"></div>
  <div class="col-md-3">
    <?php if($check_type == 'type_3' || $check_type == 'type_4'){ ?> 
      <div class="col-md-12 check-border-bottom check-sign" style="height: 50px">
        
      </div>
    <?php } ?>
  </div>
</div>

<?php $value = (isset($check) ? $check->memo : ''); ?>
<div class="row" style="height: 80px;">
  <div class="col-md-9">
    <div class="row">
      <div class="col-md-12">
        <div class="form-inline mtop20"  style="display: flex; width: 100%;">
          <label for="memo" class="mtop10 mright10"><?php echo _l('acc_memo') ?></label>
          <div class="form-group" app-field-wrapper="memo" style="width: 100%;">
            <input type="text" id="memo" name="memo" class="form-control" value="<?php echo new_html_entity_decode($value); ?>" style="width: 100%;">
          </div>
        </div>
      </div>
      <div class="col-md-12 mtop15">
        <h4 class="card-number mtop5 bold d-flex">
          <div class="hide_check_number px-10<?php echo ((isset($check) && $check->include_check_number != 1) ? ' hide' : '') ?>">
            <?php if($current_check_no_icon_a != '' && $current_check_no_icon_a != 'e'){ 
              echo $current_check_no_icon_a;
               } ?><span id="check_number_span" style=""><?php echo str_pad($number, 4, '0', STR_PAD_LEFT); ?></span><?php if($current_check_no_icon_b != '' && $current_check_no_icon_b != 'e'){ 
              echo $current_check_no_icon_b;
              } ?>
          </div>

          <div class="hide_routing_account_numbers px-10<?php echo ((isset($check) && $check->include_routing_account_numbers != 1) ? ' hide' : '') ?>">
            <?php if($routing_number_icon_a != '' && $routing_number_icon_a != 'e'){ 
              echo $routing_number_icon_a;
              } ?><span id="routing_number_span">000000000</span><?php if($routing_number_icon_b != '' && $routing_number_icon_b != 'e'){ 
              echo $routing_number_icon_b;
              } ?>
          </div>
          <div class="hide_routing_account_numbers px-10<?php echo ((isset($check) && $check->include_routing_account_numbers != 1) ? ' hide' : '') ?>">
            <?php if($bank_account_icon_a != '' && $bank_account_icon_a != 'e'){ 
              echo $bank_account_icon_a;
              } ?><span id="account_number_span">000000000</span><?php if($bank_account_icon_b != '' && $bank_account_icon_b != 'e'){ 
              echo $bank_account_icon_b;
              } ?>
          </div>
        </h4>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="col-md-12 check-border-bottom check-sign"  style="height: 50px">
      <?php 
      if($check_type != 'type_3' && $check_type != 'type_4'){
        if(isset($check) && $check->signed == 1){
          $path = ACCOUTING_PATH.'checks/signature/'.$check->id.'/signature_'.$check->id;
          if (file_exists($path.'.png')) {
             $path = $path.'.png';
          }else{
             $path = $path.'.jpeg';
          }

          echo '<img src="'. site_url($path).'" class="img_style">';
        }else{
          echo '<a href="#" onclick="sign_action();" class="btn btn-success pull-right mbot5">'. _l('e_signature_sign').'</a>';
        } 
      }
      ?>
    </div>
  </div>
</div>

<div class="row mbot15"></div>
</div>
</div>
<div class="row">
 <hr class="hr-panel-heading" />
 <h5 class="no-margin mbot15">Bill Payment Information:</h5>
 <div class="mtop15">
  <table class="table table-bill-payment-information scroll-responsive">
    <thead>
     <tr>
       <th class="not-export sorting_disabled" rowspan="1" colspan="1" aria-label=" - "><span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="bill-payment-information"><label></label></div></th>
       <th><?php echo _l('acc_due_date'); ?></th>
       <th><?php echo _l('vendor'); ?></th>
       <th><?php echo _l('ref_number'); ?></th>
       <th><?php echo _l('amount'); ?></th>
       <th><?php echo _l('bill_date'); ?></th>
     </tr>
   </thead>

 </table>
</div>
</div>
<div id="bill_check_data"></div>
<div class="additional"></div>
</div>

<div class="btn-bottom-toolbar text-right">
    <a href="#" class="btn btn-primary pull-right mtop5" onclick="save_and_print_multiple_check(); return false;" data-toggle="tooltip" data-title="<?php echo _l('print_multiple_saved_checks_note'); ?>"><?php echo _l('print_multiple_saved_checks'); ?></a>
        <a href="#" class="btn btn-primary mright5 pull-right mtop5" onclick="save_and_print_later(); return false;" data-toggle="tooltip" data-title="<?php echo _l('save_print_later_note'); ?>"><?php echo _l('save_print_later'); ?></a>
        <a href="#" class="btn btn-primary mright5 pull-right mtop5" onclick="save_and_print_a_check(); return false;" data-toggle="tooltip" data-title="<?php echo _l('save_print_now_note'); ?>"><?php echo _l('save_print_now'); ?></a>
        <a href="#" class="btn btn-primary pull-right mright5 mtop5" onclick="save_a_check(); return false;"><?php echo _l('save'); ?></a>
</div>

<div class="modal fade" id="add_signature" tabindex="-1" role="dialog">
 <div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-body">
      <div class="form-group">
        <div class="checkbox checkbox-primary">
          <input type="checkbox" name="checkbox_signature" checked id="checkbox_signature" value="1">
          <label for="checkbox_signature"><?php echo _l('signature'); ?></label>
        </div>
      </div>
      <div class="div_signature">
        <div class="signature-pad--body">
          <canvas id="signature" height="130" width="550"></canvas>
        </div>
        <input type="text" class="ip_style" tabindex="-1" name="signature" id="signatureInput">
        <div class="dispay-block mtop5">
          <button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>
        </div>
      </div>
      <div class="form-group">
        <hr>
        <div class="checkbox checkbox-primary">
          <input type="checkbox" name="checkbox_signature_available" id="checkbox_signature_available" value="1">
          <label for="checkbox_signature_available"><?php echo _l('signature_is_available'); ?></label>
        </div>
      </div>
      <div class="div_signature_available hide">
        <?php echo new_html_entity_decode(load_signature_is_available()); ?>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
      <a href="#" class="btn btn-default" onclick="import_signature_modal(); return false;"><?php echo _l('import_signature'); ?></a>
      <a onclick="sign_check(<?php echo isset($check) ? $check->id : 0; ?>);" autocomplete="off" class="btn btn-success"><?php echo _l('e_signature_sign'); ?></a>
    </div>

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php echo form_close(); ?>
</div>
</div>
</div>



<div class="modal fade" id="import_signature_modal" tabindex="-1" role="dialog">
 <div class="modal-dialog">
  <div class="modal-content">
    <?php echo form_open_multipart(admin_url('accounting/import_signature/'.(isset($check) ? $check->id : 0)),array('id'=>'import-signature-form')) ;?>
    <div class="modal-body">
      <?php echo form_hidden('checkid'); ?>
      <?php echo render_input('file_sign','choose_signature','','file', array('accept' => "image/png, image/jpeg")); ?> 
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
      <button type="submit" class="btn btn-default"><?php echo _l('import'); ?></button>
    </div>

    <?php echo form_close(); ?>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php if(isset($check)){ ?>       
  <?php require 'modules/accounting/assets/js/checks/check_js.php'; ?>
  <?php } ?>