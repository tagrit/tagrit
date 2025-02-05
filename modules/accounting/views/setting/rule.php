<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
  <div class="row">
   <div class="col-md-12" >
    <div class="panel_s">
     <div class="panel-body">
      <div class="row">
       <div class="col-md-12">
        <h4 class="no-margin font-bold"><i class="fa fa-address-card-o" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
        <hr/>

      </div>
    </div>
    <?php $setting = []; ?>
    <?php echo form_open($this->uri->uri_string(),array('id'=>'rule-form')); ?>
    <div class="row">
      <div class="col-md-12">
        <?php $value = (isset($rule)) ? $rule->name : ''; ?>
        <?php echo render_input('name','name',$value,'text'); ?>
      </div>
      <div class="col-md-6">
        <?php $transactions = [ 
          1 => ['id' => 'money_out', 'name' => _l('money_out')],
          2 => ['id' => 'money_in', 'name' => _l('money_in')],
        ]; 
        $value = (isset($rule)) ? $rule->transaction : '';
        ?>
        <?php echo render_select('transaction',$transactions,array('id','name'),'apply_this_to_transactions_that_are',$value,array(),array(),'','',false); ?>
      </div>
      <div class="col-md-6">
        <?php $following = [ 
          1 => ['id' => 'any', 'name' => _l('any')],
          2 => ['id' => 'all', 'name' => _l('all')],
        ]; 
        $value = (isset($rule)) ? $rule->following : '';
        ?>
        <?php echo render_select('following',$following,array('id','name'),'and_include_the_following',$value,array(),array(),'','',false); ?>
      </div>
    </div>
    <div class="row">
      <?php $follow_1 = [ 
          1 => ['id' => 'description', 'name' => _l('description')],
          2 => ['id' => 'amount', 'name' => _l('acc_amount')],
        ]; ?>
      <?php $follow_2 = [ 
          1 => ['id' => 'contains', 'name' => _l('contains')],
          2 => ['id' => 'does_not_contain', 'name' => _l('does_not_contain')],
          3 => ['id' => 'is_exactly', 'name' => _l('is_exactly')],
        ]; ?>
        <?php $follow_3 = [ 
          1 => ['id' => 'does_not_equal', 'name' => _l('does_not_equal')],
          2 => ['id' => 'equals', 'name' => _l('equals')],
          3 => ['id' => 'is_greater_than', 'name' => _l('is_greater_than')],
          4 => ['id' => 'is_less_than', 'name' => _l('is_less_than')],
        ]; ?>
        <div class="list_approve mleft15 mtop15">
      <?php if(!isset($rule)) { ?>
        <div id="item_approve">
          <div class="row">
            <div class="col-md-3">                            
                <div class="select-placeholder form-group">
                  <label for="type[0]"></label>
                  <select name="type[0]" id="type[0]" data-index="0" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
                    <?php foreach($follow_1 as $val){
                     $selected = '';
                     ?>
                     <option value="<?php echo new_html_entity_decode($val['id']); ?>">
                       <?php echo new_html_entity_decode($val['name']); ?>
                     </option>
                   <?php } ?>
                 </select>
               </div> 
           </div>
           <div class="col-md-3 hide" id="div_subtype_amount_0">                            
              <div class="select-placeholder form-group">
                <label for="subtype_amount[0]"></label>
                <select name="subtype_amount[0]" id="subtype_amount[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
                  <?php foreach($follow_3 as $val){
                   $selected = '';
                   ?>
                   <option value="<?php echo new_html_entity_decode($val['id']); ?>">
                     <?php echo new_html_entity_decode($val['name']); ?>
                   </option>
                 <?php } ?>
               </select>
             </div> 
           </div>
           <div class="col-md-3" id="div_subtype_0">                            
              <div class="select-placeholder form-group">
                <label for="subtype[0]"></label>
                <select name="subtype[0]" id="subtype[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
                  <?php foreach($follow_2 as $val){
                   $selected = '';
                   ?>
                   <option value="<?php echo new_html_entity_decode($val['id']); ?>">
                     <?php echo new_html_entity_decode($val['name']); ?>
                   </option>
                 <?php } ?>
               </select>
             </div> 
           </div>
           <div class="col-md-3">                            
              <div class="form-group" app-field-wrapper="name">
                <label for="text[0]" class="control-label"></label>
                <input type="text" id="text[0]" name="text[0]" class="form-control" value="">
              </div>
           </div>
           <div class="col-md-1">
              <button name="add" class="btn new_vendor_requests btn-success mtop20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
          </div>
        </div>
      </div>
    <?php }else{ 
      ?>
      <?php foreach ($rule->details as $key => $value) { ?>
          <div id="item_approve">                            
            <div class="row">                              
              <div class="col-md-3">                              
                <div class="select-placeholder form-group">
                  <label for="type[<?php echo new_html_entity_decode($key); ?>]"></label>
                  <select name="type[<?php echo new_html_entity_decode($key); ?>]" data-index="<?php echo new_html_entity_decode($key); ?>" id="type[<?php echo new_html_entity_decode($key); ?>]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
                    <?php foreach($follow_1 as $val){
                      $selected = '';
                      if($val['id'] == $value['type']){
                        $selected = 'selected';
                      }
                      ?>
                      <option value="<?php echo new_html_entity_decode($val['id']); ?>" <?php echo new_html_entity_decode($selected); ?>>
                       <?php echo new_html_entity_decode($val['name']); ?>
                     </option>
                   <?php } ?>
                 </select>
               </div> 
             </div>
             <div class="col-md-3 <?php if($value['type'] != 'amount'){echo 'hide';}; ?>" id="div_subtype_amount_<?php echo new_html_entity_decode($key); ?>">                              
                <div class="select-placeholder form-group">
                  <label for="subtype_amount[<?php echo new_html_entity_decode($key); ?>]"></label>
                  <select name="subtype_amount[<?php echo new_html_entity_decode($key); ?>]" data-index="<?php echo new_html_entity_decode($key); ?>" id="subtype_amount[<?php echo new_html_entity_decode($key); ?>]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
                    <?php foreach($follow_3 as $val){
                      $selected = '';
                      if($val['id'] == $value['subtype_amount']){
                        $selected = 'selected';
                      }
                      ?>
                      <option value="<?php echo new_html_entity_decode($val['id']); ?>" <?php echo new_html_entity_decode($selected); ?>>
                       <?php echo new_html_entity_decode($val['name']); ?>
                     </option>
                   <?php } ?>
                 </select>
               </div> 
             </div>
             <div class="col-md-3 <?php if($value['type'] == 'amount'){echo 'hide';}; ?>" id="div_subtype_<?php echo new_html_entity_decode($key); ?>">                              
                <div class="select-placeholder form-group">
                  <label for="subtype[<?php echo new_html_entity_decode($key); ?>]"></label>
                  <select name="subtype[<?php echo new_html_entity_decode($key); ?>]" id="subtype[<?php echo new_html_entity_decode($key); ?>]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
                    <?php foreach($follow_2 as $val){
                      $selected = '';
                      if($val['id'] == $value['subtype']){
                        $selected = 'selected';
                      }
                      ?>
                      <option value="<?php echo new_html_entity_decode($val['id']); ?>" <?php echo new_html_entity_decode($selected); ?>>
                       <?php echo new_html_entity_decode($val['name']); ?>
                     </option>
                   <?php } ?>
                 </select>
               </div> 
             </div>
             <div class="col-md-3">                            
              <div class="form-group" app-field-wrapper="name">
                <label for="text[<?php echo new_html_entity_decode($key); ?>]" class="control-label"></label>
                <input type="text" id="text[<?php echo new_html_entity_decode($key); ?>]" name="text[<?php echo new_html_entity_decode($key); ?>]" class="form-control" value="<?php echo new_html_entity_decode($value['text']); ?>">
              </div>
           </div>
             <div class="col-md-1">
                <?php if($key != 0){ ?>
                  <button name="add" class="btn remove_vendor_requests btn-danger mtop20" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
                <?php }else{ ?>
                  <button name="add" class="btn new_vendor_requests btn-success mtop20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                <?php } ?>
            </div>
          </div>

        </div>
    <?php }
  } ?>
</div>
</div>
  <?php $then = [ 
    1 => ['id' => 'assign', 'name' => _l('assign')],
    2 => ['id' => 'exclude', 'name' => _l('exclude')],
  ]; 
  $value = (isset($rule)) ? $rule->then : '';
  ?>
  <?php echo render_select('then',$then,array('id','name'),'then',$value,array(),array(),'','',false); ?>
<div id="then_assign" class="<?php if($value == 'exclude'){echo 'hide';} ?>">

  <div class="form-group">
    <?php
      $mapping_type = (isset($rule) ? $rule->mapping_type : ''); 
      ?>
    <label for="mapping_type"><?php echo _l('mapping_type'); ?></label><br />
    <div class="radio radio-inline radio-primary">
      <input type="radio" name="mapping_type" id="mapping_type_full_amount" value="full_amount" <?php if($mapping_type == 'full_amount'|| $mapping_type == ''){echo 'checked';} ?>>
      <label for="mapping_type_full_amount"><?php echo _l("full_amount"); ?></label>
    </div>
    <div class="radio radio-inline radio-primary">
      <input type="radio" name="mapping_type" id="mapping_type_split_percentage" value="split_percentage" <?php if($mapping_type == 'split_percentage'){echo 'checked';} ?>>
      <label for="mapping_type_split_percentage"><?php echo _l("split_percentage"); ?></label>
    </div>
    <div class="radio radio-inline radio-primary">
      <input type="radio" name="mapping_type" id="mapping_type_split_fixed" value="split_fixed" <?php if($mapping_type == 'split_fixed'){echo 'checked';} ?>>
      <label for="mapping_type_split_fixed"><?php echo _l("split_fixed"); ?></label>
    </div>
  </div>

  <div class="row full_amount <?php if($mapping_type != 'full_amount' && $mapping_type != ''){echo 'hide';} ?>">
    <div class="col-md-6">
      <?php $value = (isset($rule)) ? $rule->account : ''; ?>
      <?php echo render_select('account',$accounts,array('id','name', 'account_type_name'),'account',$value,array(),array(),'','',false); ?>
    </div>
  </div>

  <div class="list_split <?php if($mapping_type != 'split_percentage'){echo 'hide';} ?>">
    <?php if(!isset($rule) || (isset($rule) && $rule->split_percentage == null)) { ?>
    <div id="item_split">
      <div class="row">
        <div class="col-md-3">
          <div class="form-group" app-field-wrapper="name">
            <label for="percentage[0]" class="control-label"><?php echo _l('percentage'); ?></label>
            <input type="text" id="percentage[0]" name="percentage[0]" class="form-control" value="">
          </div>
        </div>
        <div class="col-md-3">                            
          <?php echo render_select('account_split[0]',$accounts,array('id','name', 'account_type_name'),'account','',array(),array(),'','',false); ?>
        </div>
        <div class="col-md-1">
          <button name="add" class="btn new_split btn-success mtop20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
        </div>
      </div>
    </div>
    <?php }else{ 
      $split_percentage = json_decode($rule->split_percentage ?? '', true);
      $percentage = $split_percentage['percentage'] ?? [];
      $account_split = $split_percentage['account_split'] ?? [];
      foreach ($percentage as $key => $value) {
      ?>
      <div id="item_split">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group" app-field-wrapper="name">
              <label for="percentage[<?php echo new_html_entity_decode($key); ?>]" class="control-label"><?php echo _l('percentage'); ?></label>
              <input type="text" id="percentage[<?php echo new_html_entity_decode($key); ?>]" name="percentage[<?php echo new_html_entity_decode($key); ?>]" class="form-control" value="<?php echo new_html_entity_decode($value); ?>">
            </div>
          </div>
          <div class="col-md-3">                            
            <?php echo render_select('account_split['.$key.']',$accounts,array('id','name', 'account_type_name'),'account',$account_split[$key],array(),array(),'','',false); ?>
          </div>
          <div class="col-md-1">
            <?php if($key != 0){ ?>
              <button name="add" class="btn remove_split btn-danger mtop20" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
            <?php }else{ ?>
              <button name="add" class="btn new_split btn-success mtop20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
            <?php } ?>
          </div>
        </div>
      </div>
    <?php } ?>
    <?php } ?>
  </div>

  <div class="div_split_fixed <?php if($mapping_type != 'split_fixed'){echo 'hide';} ?>">
    <div class="list_split_fixed">
    <?php if(!isset($rule) || (isset($rule) && $rule->split_amount == null)) { ?>
      <div id="item_split_fixed">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group" app-field-wrapper="name">
              <label for="fixed_amount[0]" class="control-label"><?php echo _l('fixed_amount'); ?></label>
              <input type="text" id="fixed_amount[0]" name="fixed_amount[0]" class="form-control" value="">
            </div>
          </div>
          <div class="col-md-3">                            
            <?php echo render_select('account_split_fixed[0]',$accounts,array('id','name', 'account_type_name'),'account',$value,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-1">
            <button name="add" class="btn new_split_fixed btn-success mtop20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
          </div>
        </div>
      </div>
    <?php }else{ 
      $split_amount = json_decode($rule->split_amount ?? '', true);
      $fixed_amount = $split_amount['fixed_amount'] ?? [];
      $account_split_fixed = $split_amount['account_split_fixed'] ?? [];
      foreach ($fixed_amount as $key => $value) {
      ?>
      <div id="item_split_fixed">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group" app-field-wrapper="name">
              <label for="fixed_amount[<?php echo new_html_entity_decode($key); ?>]" class="control-label"><?php echo _l('fixed_amount'); ?></label>
              <input type="text" id="fixed_amount[<?php echo new_html_entity_decode($key); ?>]" name="fixed_amount[<?php echo new_html_entity_decode($key); ?>]" class="form-control" value="<?php echo new_html_entity_decode($value); ?>">
            </div>
          </div>
          <div class="col-md-3">                            
            <?php echo render_select('account_split_fixed['.$key.']',$accounts,array('id','name', 'account_type_name'),'account',$account_split_fixed[$key],array(),array(),'','',false); ?>
          </div>
          <div class="col-md-1">
            <?php if($key != 0){ ?>
              <button name="add" class="btn remove_split_fixed btn-danger mtop20" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
            <?php }else{ ?>
              <button name="add" class="btn new_split_fixed btn-success mtop20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
            <?php } ?>
          </div>
        </div>
      </div>
    <?php } ?>
    <?php } ?>
    </div>
    <div class="row">
        <div class="col-md-3">
          <div class="form-group" app-field-wrapper="name">
            <h4 class="mtop25"><?php echo _l('Remainder'); ?></h4>
          </div>
        </div>
        <div class="col-md-3">                            
          <?php $value = (isset($account_split_fixed)) ? $account_split_fixed[10000] : ''; ?>
          <?php echo render_select('account_split_fixed[10000]',$accounts,array('id','name', 'account_type_name'),'account',$value,array(),array(),'','',false); ?>
        </div>
      </div>

  </div>

  <?php $value = (isset($rule)) ? $rule->auto_add : ''; ?>
  <div class="col-md-6">
      <h5 class="title mbot5"><?php echo _l('automatically_confirm_transactions_this_rule_applies_to') ?></h5>
      <div class="row">
          <div class="col-md-6 mtop10 border-right">
            <span><?php echo _l('auto_add'); ?> </span>
          </div>
          <div class="col-md-6 mtop10">
              <div class="onoffswitch">
                  <input type="checkbox" id="auto_add" data-perm-id="3" class="onoffswitch-checkbox" <?php if($value == '1'){echo 'checked';} ?>  value="1" name="auto_add">
                  <label class="onoffswitch-label" for="auto_add"></label>
              </div>
          </div>
      </div>
    </div>

</div>
<div class="modal-footer">
  <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
</div>
</div>
<?php echo form_close(); ?>
</div>
</div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>
</body>
</html>