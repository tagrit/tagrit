<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 

  $acc_wh_stock_import_automatic_conversion = get_option('acc_wh_stock_import_automatic_conversion');
  $acc_wh_stock_import_return_automatic_conversion = get_option('acc_wh_stock_import_return_automatic_conversion');
  $acc_wh_stock_import_payment_account = get_option('acc_wh_stock_import_payment_account');
  $acc_wh_stock_import_deposit_to = get_option('acc_wh_stock_import_deposit_to');
  $acc_wh_stock_import_return_payment_account = get_option('acc_wh_stock_import_return_payment_account');
  $acc_wh_stock_import_return_deposit_to = get_option('acc_wh_stock_import_return_deposit_to');

  $acc_wh_stock_export_automatic_conversion = get_option('acc_wh_stock_export_automatic_conversion');
  $acc_wh_stock_export_profit_automatic_conversion = get_option('acc_wh_stock_export_profit_automatic_conversion');
  $acc_wh_stock_export_payment_account = get_option('acc_wh_stock_export_payment_account');
  $acc_wh_stock_export_deposit_to = get_option('acc_wh_stock_export_deposit_to');
  $acc_wh_stock_export_profit_payment_account = get_option('acc_wh_stock_export_profit_payment_account');
  $acc_wh_stock_export_profit_deposit_to = get_option('acc_wh_stock_export_profit_deposit_to');

  $acc_wh_loss_adjustment_automatic_conversion = get_option('acc_wh_loss_adjustment_automatic_conversion');
  $acc_wh_decrease_payment_account = get_option('acc_wh_decrease_payment_account');
  $acc_wh_decrease_deposit_to = get_option('acc_wh_decrease_deposit_to');
  $acc_wh_increase_payment_account = get_option('acc_wh_increase_payment_account');
  $acc_wh_increase_deposit_to = get_option('acc_wh_increase_deposit_to');

  $acc_wh_opening_stock_automatic_conversion = get_option('acc_wh_opening_stock_automatic_conversion');
  $acc_wh_opening_stock_payment_account = get_option('acc_wh_opening_stock_payment_account');
  $acc_wh_opening_stock_deposit_to = get_option('acc_wh_opening_stock_deposit_to');
 ?>

<?php echo form_open(admin_url('accounting/update_warehouse_automatic_conversion'),array('id'=>'werehouse-mapping-setup-form')); ?>
<div class="row">
  <div class="col-md-12">
    <h4><?php echo _l('automatic_conversion'); ?></h4>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5 class="title mbot5"><?php echo _l('stock_import') ?></h5>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5><?php echo _l('default'); ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_wh_stock_import_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_wh_stock_import_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_wh_stock_import_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_wh_stock_import_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_wh_stock_import_automatic_conversion == 0){echo 'hide';} ?>" id="div_wh_stock_import_automatic_conversion">
          <div class="col-md-6">
            <?php echo render_select('acc_wh_stock_import_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_wh_stock_import_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_wh_stock_import_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_wh_stock_import_deposit_to,array(),array(),'','',false); ?>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5><?php echo _l('inventory_receiving_voucher_for_return_order'); ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_wh_stock_import_return_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_wh_stock_import_return_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_wh_stock_import_return_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_wh_stock_import_return_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_wh_stock_import_return_automatic_conversion == 0){echo 'hide';} ?>" id="div_wh_stock_import_return_automatic_conversion">
          <div class="col-md-6">
            <?php echo render_select('acc_wh_stock_import_return_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_wh_stock_import_return_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_wh_stock_import_return_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_wh_stock_import_return_deposit_to,array(),array(),'','',false); ?>
          </div>
        </div>
      </div>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5 class="title mbot5"><?php echo _l('stock_export') ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5><?php echo _l('acc_inventory'); ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_wh_stock_export_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_wh_stock_export_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_wh_stock_export_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_wh_stock_export_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_wh_stock_export_automatic_conversion == 0){echo 'hide';} ?>" id="div_wh_stock_export_automatic_conversion">
          <div class="col-md-6">
            <?php echo render_select('acc_wh_stock_export_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_wh_stock_export_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_wh_stock_export_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_wh_stock_export_deposit_to,array(),array(),'','',false); ?>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5><?php echo _l('profit'); ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_wh_stock_export_profit_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_wh_stock_export_profit_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_wh_stock_export_profit_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_wh_stock_export_profit_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_wh_stock_export_profit_automatic_conversion == 0){echo 'hide';} ?>" id="div_wh_stock_export_profit_automatic_conversion">
          <div class="col-md-6">
            <?php echo render_select('acc_wh_stock_export_profit_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_wh_stock_export_profit_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_wh_stock_export_profit_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_wh_stock_export_profit_deposit_to,array(),array(),'','',false); ?>
          </div>
        </div>
      </div>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5 class="title mbot5"><?php echo _l('loss_adjustment') ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_wh_loss_adjustment_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_wh_loss_adjustment_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_wh_loss_adjustment_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_wh_loss_adjustment_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_wh_loss_adjustment_automatic_conversion == 0){echo 'hide';} ?>" id="div_wh_loss_adjustment_automatic_conversion">
          <div class="col-md-12">
            <h5><?php echo _l('increase'); ?></h5>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_wh_increase_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_wh_increase_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_wh_increase_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_wh_increase_deposit_to,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-12">
            <h5><?php echo _l('decrease'); ?></h5>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_wh_decrease_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_wh_decrease_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_wh_decrease_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_wh_decrease_deposit_to,array(),array(),'','',false); ?>
          </div>
        </div>
      </div>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5 class="title mbot5"><?php echo _l('opening_stock') ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_wh_opening_stock_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_wh_opening_stock_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_wh_opening_stock_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_wh_opening_stock_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_wh_opening_stock_automatic_conversion == 0){echo 'hide';} ?>" id="div_wh_opening_stock_automatic_conversion">
          <div class="col-md-6">
            <?php echo render_select('acc_wh_opening_stock_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_wh_opening_stock_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_wh_opening_stock_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_wh_opening_stock_deposit_to,array(),array(),'','',false); ?>
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

