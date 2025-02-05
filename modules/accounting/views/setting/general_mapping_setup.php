<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 

  $acc_invoice_automatic_conversion = get_option('acc_invoice_automatic_conversion');
  $acc_payment_automatic_conversion = get_option('acc_payment_automatic_conversion');
  $acc_expense_automatic_conversion = get_option('acc_expense_automatic_conversion');
  $acc_tax_automatic_conversion = get_option('acc_tax_automatic_conversion');
  $acc_payment_expense_automatic_conversion = get_option('acc_payment_expense_automatic_conversion');
  $acc_payment_sale_automatic_conversion = get_option('acc_payment_sale_automatic_conversion');

  $acc_invoice_payment_account = get_option('acc_invoice_payment_account');
  $acc_invoice_deposit_to = get_option('acc_invoice_deposit_to');
  $acc_invoice_discount_payment_account = get_option('acc_invoice_discount_payment_account');
  $acc_invoice_discount_deposit_to = get_option('acc_invoice_discount_deposit_to');
  $acc_payment_payment_account = get_option('acc_payment_payment_account');
  $acc_payment_deposit_to = get_option('acc_payment_deposit_to');
  $acc_expense_payment_payment_account = get_option('acc_expense_payment_payment_account');
  $acc_expense_payment_deposit_to = get_option('acc_expense_payment_deposit_to');
  
  $acc_expense_payment_account = get_option('acc_expense_payment_account');
  $acc_expense_deposit_to = get_option('acc_expense_deposit_to');
  $acc_tax_payment_account = get_option('acc_tax_payment_account');
  $acc_tax_deposit_to = get_option('acc_tax_deposit_to');
  $acc_expense_tax_payment_account = get_option('acc_expense_tax_payment_account');
  $acc_expense_tax_deposit_to = get_option('acc_expense_tax_deposit_to');

  $acc_active_payment_mode_mapping = get_option('acc_active_payment_mode_mapping');
  $acc_active_expense_category_mapping = get_option('acc_active_expense_category_mapping');

  $acc_credit_note_automatic_conversion = get_option('acc_credit_note_automatic_conversion');
  $acc_credit_note_payment_account = get_option('acc_credit_note_payment_account');
  $acc_credit_note_deposit_to = get_option('acc_credit_note_deposit_to');

  $acc_credit_note_refund_automatic_conversion = get_option('acc_credit_note_refund_automatic_conversion');
  $acc_credit_note_refund_payment_account = get_option('acc_credit_note_refund_payment_account');
  $acc_credit_note_refund_deposit_to = get_option('acc_credit_note_refund_deposit_to');

  $acc_pur_tax_payment_account = get_option('acc_pur_tax_payment_account');
  $acc_pur_tax_deposit_to = get_option('acc_pur_tax_deposit_to');
 ?>
 
<?php echo form_open(admin_url('accounting/update_automatic_conversion'),array('id'=>'general-settings-form')); ?>
<div class="row">
  <div class="col-md-12">
    <h4><?php echo _l('automatic_conversion'); ?> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('automatic_conversion_note'); ?>"></i></h4>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5 class="title mbot5"><?php echo _l('invoice') ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_invoice_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_invoice_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_invoice_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_invoice_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_invoice_automatic_conversion == 0){echo 'hide';} ?>" id="div_invoice_automatic_conversion">
          <div class="col-md-12">
            <h5><?php echo _l('default_for_all_item'); ?></h5>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_invoice_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_invoice_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_invoice_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_invoice_deposit_to,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-12">
            <h5><?php echo _l('discount'); ?></h5>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_invoice_discount_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_invoice_discount_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_invoice_discount_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_invoice_discount_deposit_to,array(),array(),'','',false); ?>
          </div>
        </div>
      </div>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6">
                <h5 class="title mbot5"><?php echo _l('payment') ?></h5>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-6 border-right">
                    <h5><?php echo _l('sales'); ?></h5>
                  </div>
                  <div class="col-md-6 mtop5">
                      <div class="onoffswitch">
                          <input type="checkbox" id="acc_payment_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_payment_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_payment_automatic_conversion">
                          <label class="onoffswitch-label" for="acc_payment_automatic_conversion"></label>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="<?php if($acc_payment_automatic_conversion == 0){echo 'hide';} ?>" id="div_payment_automatic_conversion">
            <div class="col-md-6">
              <?php echo render_select('acc_payment_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_payment_payment_account,array(),array(),'','',false); ?>
            </div>
            <div class="col-md-6">
              <?php echo render_select('acc_payment_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_payment_deposit_to,array(),array(),'','',false); ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-6 border-right">
                    <h5><?php echo _l('expenses'); ?></h5>
                  </div>
                  <div class="col-md-6 mtop5">
                      <div class="onoffswitch">
                          <input type="checkbox" id="acc_payment_expense_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_payment_expense_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_payment_expense_automatic_conversion">
                          <label class="onoffswitch-label" for="acc_payment_expense_automatic_conversion"></label>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="<?php if($acc_payment_expense_automatic_conversion == 0){echo 'hide';} ?>" id="div_payment_expense_automatic_conversion">
            <div class="col-md-6">
              <?php echo render_select('acc_expense_payment_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_expense_payment_payment_account,array(),array(),'','',false); ?>
            </div>
            <div class="col-md-6">
              <?php echo render_select('acc_expense_payment_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_expense_payment_deposit_to,array(),array(),'','',false); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6">
                <h5 class="title mbot5"><?php echo _l('credit_note') ?></h5>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-6 border-right">
                    <h5><?php echo _l('sales'); ?></h5>
                  </div>
                  <div class="col-md-6 mtop5">
                      <div class="onoffswitch">
                          <input type="checkbox" id="acc_credit_note_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_credit_note_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_credit_note_automatic_conversion">
                          <label class="onoffswitch-label" for="acc_credit_note_automatic_conversion"></label>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="<?php if($acc_credit_note_automatic_conversion == 0){echo 'hide';} ?>" id="div_credit_note_automatic_conversion">
            <div class="col-md-6">
              <?php echo render_select('acc_credit_note_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_credit_note_payment_account,array(),array(),'','',false); ?>
            </div>
            <div class="col-md-6">
              <?php echo render_select('acc_credit_note_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_credit_note_deposit_to,array(),array(),'','',false); ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-6 border-right">
                    <h5><?php echo _l('refund'); ?></h5>
                  </div>
                  <div class="col-md-6 mtop5">
                      <div class="onoffswitch">
                          <input type="checkbox" id="acc_credit_note_refund_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_credit_note_refund_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_credit_note_refund_automatic_conversion">
                          <label class="onoffswitch-label" for="acc_credit_note_refund_automatic_conversion"></label>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="<?php if($acc_credit_note_refund_automatic_conversion == 0){echo 'hide';} ?>" id="div_credit_note_refund_automatic_conversion">
            <div class="col-md-6">
              <?php echo render_select('acc_credit_note_refund_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_credit_note_refund_payment_account,array(),array(),'','',false); ?>
            </div>
            <div class="col-md-6">
              <?php echo render_select('acc_credit_note_refund_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_credit_note_refund_deposit_to,array(),array(),'','',false); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5 class="title mbot5"><?php echo _l('expense_default') ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_expense_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_expense_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_expense_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_expense_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_expense_automatic_conversion == 0){echo 'hide';} ?>" id="div_expense_automatic_conversion">
          <div class="col-md-6">
            <?php echo render_select('acc_expense_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_expense_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_expense_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_expense_deposit_to,array(),array(),'','',false); ?>
          </div>
        </div>
      </div>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5 class="title mbot5"><?php echo _l('tax_default') ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_tax_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_tax_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_tax_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_tax_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_tax_automatic_conversion == 0){echo 'hide';} ?>" id="div_tax_automatic_conversion">
          <div class="col-md-12">
            <h5><?php echo _l('sales'); ?></h5>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_tax_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_tax_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_tax_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_tax_deposit_to,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-12">
            <h5><?php echo _l('expenses'); ?></h5>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_expense_tax_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_expense_tax_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_expense_tax_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_expense_tax_deposit_to,array(),array(),'','',false); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<hr>
  <div class="col-md-12">
    <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
  </div>
<?php echo form_close(); ?>
<div class="row">
  <div class="col-md-12">
    <hr>
  </div>
</div>
  <h4 class="no-margin font-bold"><?php echo _l('item_mapping_setup'); ?></h4>
<hr>
<a href="#" onclick="add_item_automatic(); return false;" class="btn btn-info mr-4 button-margin-r-b" title="<?php echo _l('add') ?> ">
  <?php echo _l('add'); ?>
</a>
<hr>
<table class="table table-item-automatic">
  <thead>
    <th><?php echo _l('invoice_items_list_description'); ?></th>
    <th><?php echo _l('invoice_items_list_rate'); ?></th>
    <th><?php echo _l('item_group_name'); ?></th>
  </thead>
  <tbody>
  </tbody>
</table>
<div class="row">
  <div class="col-md-12">
    <hr>
  </div>
  <div class="col-md-6">
    <div class="row">
      <div class="col-md-6 border-right">
        <h4 class="no-margin font-bold"><?php echo _l('payment_mode_mapping'); ?></h4>
      </div>
      <div class="col-md-6">
          <div class="onoffswitch">
              <input type="checkbox" id="acc_active_payment_mode_mapping" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_active_payment_mode_mapping == '1'){echo 'checked';} ?>  value="1" name="acc_active_payment_mode_mapping" data-switch-url="<?php echo admin_url('accounting/change_active_payment_mode_mapping') ?>" data-id="0">
              <label class="onoffswitch-label" for="acc_active_payment_mode_mapping"></label>
          </div>
      </div>
    </div>
  </div>
</div>
<hr>
<a href="#" onclick="add_payment_mode_mapping(); return false;" class="btn btn-info mr-4 button-margin-r-b" title="<?php echo _l('add') ?> ">
  <?php echo _l('add'); ?>
</a>
<hr>
<table class="table table-payment-mode-mapping">
  <thead>
    <th><?php echo _l('name'); ?></th>
    <th><?php echo _l('description'); ?></th>
  </thead>
  <tbody>
  </tbody>
</table>
<div class="row">
  <div class="col-md-12">
    <hr>
  </div>
  <div class="col-md-6">
    <div class="row">
      <div class="col-md-6 border-right">
        <h4 class="no-margin font-bold"><?php echo _l('expense_category_mapping'); ?></h4>
      </div>
      <div class="col-md-6">
          <div class="onoffswitch">
              <input type="checkbox" id="acc_active_expense_category_mapping" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_active_expense_category_mapping == '1'){echo 'checked';} ?>  value="1" name="acc_active_expense_category_mapping" data-switch-url="<?php echo admin_url('accounting/change_active_expense_category_mapping') ?>" data-id="0">
              <label class="onoffswitch-label" for="acc_active_expense_category_mapping"></label>
          </div>
      </div>
    </div>
  </div>
</div>
<hr>
<a href="#" onclick="add_expense_category_mapping(); return false;" class="btn btn-info mr-4 button-margin-r-b" title="<?php echo _l('add') ?> ">
  <?php echo _l('add'); ?>
</a>
<hr>
<table class="table table-expense-category-mapping">
  <thead>
    <th><?php echo _l('id'); ?></th>
    <th><?php echo _l('name'); ?></th>
    <th><?php echo _l('description'); ?></th>
    <th><?php echo _l('acc_preferred_payment_method'); ?></th>
  </thead>
  <tbody>
  </tbody>
</table>
<div class="row">
  <div class="col-md-12">
    <hr>
  </div>
</div>
  <h4 class="no-margin font-bold"><?php echo _l('tax_mapping_setup'); ?></h4>
<hr>
<a href="#" onclick="add_tax_mapping(); return false;" class="btn btn-info mr-4 button-margin-r-b" title="<?php echo _l('add') ?> ">
  <?php echo _l('add'); ?>
</a>
<hr>
<table class="table table-tax-mapping">
  <thead>
    <th><?php echo _l('id'); ?></th>
    <th><?php echo _l('tax_dt_name'); ?></th>
    <th><?php echo _l('tax_dt_rate'); ?></th>
  </thead>
  <tbody>
  </tbody>
</table>

<?php $arrAtt = array();
      $arrAtt['data-type']='currency';
?>
<div class="modal fade" id="item-automatic-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('item_mapping_setup')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('accounting/item_automatic'),array('id'=>'item-automatic-form'));?>
         <?php echo form_hidden('id'); ?>
         <div class="modal-body">
              <?php echo render_select('item[]',$items,array('id','description', 'sku_code'),'acc_item', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
              <?php echo render_select('inventory_asset_account',$accounts,array('id','name','account_type_name'),'inventory_asset_account', '37', array(), array(), '', '', false); ?>
              <?php echo render_select('income_account',$accounts,array('id','name','account_type_name'),'income_account', '69', array(), array(), '', '', false); ?>
              <?php echo render_select('expense_account',$accounts,array('id','name','account_type_name'),'expense_account', '16', array(), array(), '', '', false); ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>

<div class="modal fade" id="edit-item-automatic-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('item_mapping_setup')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('accounting/item_automatic'),array('id'=>'edit-item-automatic-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
              <?php echo render_select('item_id',$_items,array('id','description', 'sku_code'),'acc_item', '',array('disabled' => true), array(), '', '', false); ?>
              <?php echo render_select('inventory_asset_account',$accounts,array('id','name','account_type_name'),'inventory_asset_account', '37', array(), array(), '', '', false); ?>
              <?php echo render_select('income_account',$accounts,array('id','name','account_type_name'),'income_account', '69', array(), array(), '', '', false); ?>
              <?php echo render_select('expense_account',$accounts,array('id','name','account_type_name'),'expense_account', '16', array(), array(), '', '', false); ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>


<div class="modal fade" id="tax-mapping-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('tax_mapping_setup')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('accounting/tax_mapping'),array('id'=>'tax-mapping-form'));?>
         <?php echo form_hidden('id'); ?>
         <div class="modal-body">
              <?php echo render_select('tax[]',$taxes,array('id','name', 'taxrate'),'tax', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
              <div class="row">
                <div class="col-md-12">
                  <h5><?php echo _l('sales'); ?></h5>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_tax_payment_account,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_tax_deposit_to,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-12">
                  <h5><?php echo _l('expenses'); ?></h5>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('expense_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_expense_tax_payment_account,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('expense_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_expense_tax_deposit_to,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-12">
                  <h5><?php echo _l('purchase'); ?></h5>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('purchase_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_pur_tax_payment_account,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('purchase_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_pur_tax_deposit_to,array(),array(),'','',false); ?>
                </div>
              </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>

<div class="modal fade" id="edit-tax-mapping-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('item_mapping_setup')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('accounting/tax_mapping'),array('id'=>'edit-tax-mapping-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
              <?php echo render_select('tax_id',$_taxes,array('id','name', 'taxrate'),'tax', '',array('disabled' => true), array(), '', '', false); ?>
              <div class="col-md-12">
                  <h5><?php echo _l('sales'); ?></h5>
                </div>
              <div class="row">
                <div class="col-md-6">
                  <?php echo render_select('payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_tax_payment_account,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_tax_deposit_to,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-12">
                  <h5><?php echo _l('expenses'); ?></h5>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('expense_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_expense_tax_payment_account,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('expense_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_expense_tax_deposit_to,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('purchase_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_pur_tax_payment_account,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('purchase_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_pur_tax_deposit_to,array(),array(),'','',false); ?>
                </div>
              </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>

<div class="modal fade" id="expense-category-mapping-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('expense_category_mapping_setup')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('accounting/expense_category_mapping'),array('id'=>'expense-category-mapping-form'));?>
         <?php echo form_hidden('id'); ?>
         <div class="modal-body">
              <?php echo render_select('category[]',$categories,array('id','name'),'expense_category', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
              <div class="row">
                <div class="col-md-6">
                  <?php echo render_select('payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_expense_payment_account,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_expense_deposit_to,array(),array(),'','',false); ?>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="preferred_payment_method" <?php if($acc_expense_deposit_to == '1'){echo 'checked';} ?> id="preferred_payment_method" value="1">
                    <label for="preferred_payment_method"><?php echo _l('acc_preferred_payment_method'); ?></label>
                  </div>
                </div>
              </div>
              <div class="row list-payment-method-mapping">
                
              </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>

<div class="modal fade" id="edit-expense-category-mapping-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('expense_category_mapping_setup')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('accounting/expense_category_mapping'),array('id'=>'edit-expense-category-mapping-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
              <?php echo render_select('category_id',$_categories,array('id','name'),'expense_category', '',array('disabled' => true), array(), '', '', false); ?>
              <div class="row">
                <div class="col-md-6">
                  <?php echo render_select('payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_expense_payment_account,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_expense_deposit_to,array(),array(),'','',false); ?>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="preferred_payment_method" id="edit_preferred_payment_method" value="1">
                    <label for="edit_preferred_payment_method"><?php echo _l('acc_preferred_payment_method'); ?></label>
                  </div>
                </div>
              </div>
              <div class="row list-payment-method-mapping">
                
              </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>


<div class="modal fade" id="payment-mode-mapping-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('payment_mode_mapping')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('accounting/payment_mode_mapping'),array('id'=>'payment-mode-mapping-form'));?>
         <?php echo form_hidden('id'); ?>
         <div class="modal-body">
              <?php echo render_select('payment_mode[]',$payment_modes,array('id','name', 'payment_moderate'),'payment_mode', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
              <div class="row">
                <div class="col-md-12">
                  <h5><?php echo _l('sales'); ?></h5>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_payment_payment_account,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_payment_deposit_to,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-12">
                  <h5><?php echo _l('expenses'); ?></h5>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('expense_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_expense_payment_payment_account,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('expense_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_expense_payment_deposit_to,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-12">
                  <h5><?php echo _l('credit_note_refund'); ?></h5>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('credit_note_refund_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_credit_note_refund_payment_account,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('credit_note_refund_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_credit_note_refund_deposit_to,array(),array(),'','',false); ?>
                </div>
              </div>
         </div>


         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>

<div class="modal fade" id="edit-payment-mode-mapping-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('payment_mode_mapping')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('accounting/payment_mode_mapping'),array('id'=>'edit-payment-mode-mapping-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
              <?php echo render_select('payment_mode_id',$_payment_modes,array('id','name', 'payment_moderate'),'payment_mode', '',array('disabled' => true), array(), '', '', false); ?>
              <div class="row">
                <div class="col-md-6">
                  <?php echo render_select('payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_payment_payment_account,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_payment_deposit_to,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-12">
                  <h5><?php echo _l('expenses'); ?></h5>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('expense_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_expense_payment_payment_account,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('expense_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_expense_payment_deposit_to,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-12">
                  <h5><?php echo _l('credit_note_refund'); ?></h5>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('credit_note_refund_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_credit_note_refund_payment_account,array(),array(),'','',false); ?>
                </div>
                <div class="col-md-6">
                  <?php echo render_select('credit_note_refund_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_credit_note_refund_deposit_to,array(),array(),'','',false); ?>
                </div>
              </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>