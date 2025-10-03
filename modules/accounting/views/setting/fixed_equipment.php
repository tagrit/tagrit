<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 

  $acc_fe_asset_automatic_conversion = get_option('acc_fe_asset_automatic_conversion');
  $acc_fe_asset_payment_account = get_option('acc_fe_asset_payment_account');
  $acc_fe_asset_deposit_to = get_option('acc_fe_asset_deposit_to');

  $acc_fe_license_automatic_conversion = get_option('acc_fe_license_automatic_conversion');
  $acc_fe_license_payment_account = get_option('acc_fe_license_payment_account');
  $acc_fe_license_deposit_to = get_option('acc_fe_license_deposit_to');

  $acc_fe_component_automatic_conversion = get_option('acc_fe_component_automatic_conversion');
  $acc_fe_component_payment_account = get_option('acc_fe_component_payment_account');
  $acc_fe_component_deposit_to = get_option('acc_fe_component_deposit_to');

  $acc_fe_consumable_automatic_conversion = get_option('acc_fe_consumable_automatic_conversion');
  $acc_fe_consumable_payment_account = get_option('acc_fe_consumable_payment_account');
  $acc_fe_consumable_deposit_to = get_option('acc_fe_consumable_deposit_to');

  $acc_fe_asset_automatic_conversion = get_option('acc_fe_asset_automatic_conversion');
  $acc_fe_asset_payment_account = get_option('acc_fe_asset_payment_account');
  $acc_fe_asset_deposit_to = get_option('acc_fe_asset_deposit_to');

  $acc_fe_maintenance_automatic_conversion = get_option('acc_fe_maintenance_automatic_conversion');
  $acc_fe_maintenance_payment_account = get_option('acc_fe_maintenance_payment_account');
  $acc_fe_maintenance_deposit_to = get_option('acc_fe_maintenance_deposit_to');

  $acc_fe_depreciation_automatic_conversion = get_option('acc_fe_depreciation_automatic_conversion');
  $acc_fe_depreciation_payment_account = get_option('acc_fe_depreciation_payment_account');
  $acc_fe_depreciation_deposit_to = get_option('acc_fe_depreciation_deposit_to');
 ?>

<?php echo form_open(admin_url('accounting/update_fixed_equipment_automatic_conversion'),array('id'=>'fixed-equipment-mapping-setup-form')); ?>
<div class="row">
  <div class="col-md-12">
    <h4><?php echo _l('automatic_conversion'); ?></h4>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5 class="title mbot5"><?php echo _l('fe_asset') ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_fe_asset_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_fe_asset_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_fe_asset_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_fe_asset_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_fe_asset_automatic_conversion == 0){echo 'hide';} ?>" id="div_fe_asset_automatic_conversion">
          <div class="col-md-6">
            <?php echo render_select('acc_fe_asset_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_fe_asset_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_fe_asset_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_fe_asset_deposit_to,array(),array(),'','',false); ?>
          </div>
        </div>
      </div>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5 class="title mbot5"><?php echo _l('fe_license') ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_fe_license_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_fe_license_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_fe_license_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_fe_license_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_fe_license_automatic_conversion == 0){echo 'hide';} ?>" id="div_fe_license_automatic_conversion">
          <div class="col-md-6">
            <?php echo render_select('acc_fe_license_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_fe_license_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_fe_license_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_fe_license_deposit_to,array(),array(),'','',false); ?>
          </div>
        </div>
      </div>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5 class="title mbot5"><?php echo _l('fe_component') ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_fe_component_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_fe_component_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_fe_component_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_fe_component_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_fe_component_automatic_conversion == 0){echo 'hide';} ?>" id="div_fe_component_automatic_conversion">
          <div class="col-md-6">
            <?php echo render_select('acc_fe_component_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_fe_component_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_fe_component_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_fe_component_deposit_to,array(),array(),'','',false); ?>
          </div>
        </div>
      </div>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5 class="title mbot5"><?php echo _l('fe_consumable') ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_fe_consumable_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_fe_consumable_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_fe_consumable_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_fe_consumable_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_fe_consumable_automatic_conversion == 0){echo 'hide';} ?>" id="div_fe_consumable_automatic_conversion">
          <div class="col-md-6">
            <?php echo render_select('acc_fe_consumable_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_fe_consumable_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_fe_consumable_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_fe_consumable_deposit_to,array(),array(),'','',false); ?>
          </div>
        </div>
      </div>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5 class="title mbot5"><?php echo _l('fe_maintenance') ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_fe_maintenance_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_fe_maintenance_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_fe_maintenance_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_fe_maintenance_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_fe_maintenance_automatic_conversion == 0){echo 'hide';} ?>" id="div_fe_maintenance_automatic_conversion">
          <div class="col-md-6">
            <?php echo render_select('acc_fe_maintenance_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_fe_maintenance_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_fe_maintenance_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_fe_maintenance_deposit_to,array(),array(),'','',false); ?>
          </div>
        </div>
      </div>
      <div class="div_content">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6 border-right">
                <h5 class="title mbot5"><?php echo _l('fe_depreciation') ?></h5>
              </div>
              <div class="col-md-6 mtop5">
                  <div class="onoffswitch">
                      <input type="checkbox" id="acc_fe_depreciation_automatic_conversion" data-perm-id="3" class="onoffswitch-checkbox" <?php if($acc_fe_depreciation_automatic_conversion == '1'){echo 'checked';} ?>  value="1" name="acc_fe_depreciation_automatic_conversion">
                      <label class="onoffswitch-label" for="acc_fe_depreciation_automatic_conversion"></label>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row <?php if($acc_fe_depreciation_automatic_conversion == 0){echo 'hide';} ?>" id="div_fe_depreciation_automatic_conversion">
          <div class="col-md-6">
            <?php echo render_select('acc_fe_depreciation_payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$acc_fe_depreciation_payment_account,array(),array(),'','',false); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_select('acc_fe_depreciation_deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$acc_fe_depreciation_deposit_to,array(),array(),'','',false); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<hr>
<button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
<?php echo form_close(); ?>

