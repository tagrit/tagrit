<div class="horizontal-scrollable-tabs">
   <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
   <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
   <div class="horizontal-tabs">
      <ul class="nav nav-tabs profile-tabs row customer-profile-tabs nav-tabs-horizontal" role="tablist">
         <li role="presentation" class="<?php if($tab_2 == 'assets'){echo 'active';}; ?>">
            <a href="<?php echo admin_url('accounting/transaction?group=fixed_equipment&tab=fe_assets'); ?>">
              <i class="fa fa-credit-card"></i>&nbsp;<?php echo _l('assets'); ?> <span class="text-danger"><?php echo '('.$count_asset.')'; ?></span>
            </a>
         </li>
         <li role="presentation" class="<?php if($tab_2 == 'fe_licenses'){echo 'active';}; ?>">
            <a href="<?php echo admin_url('accounting/transaction?group=fixed_equipment&tab=fe_licenses'); ?>">
              <i class="fa fa-file-text"></i>&nbsp;<?php echo _l('licenses'); ?> <span class="text-danger"><?php echo '('.$count_license.')'; ?></span>
            </a>
         </li>
         <li role="presentation" class="<?php if($tab_2 == 'fe_components'){echo 'active';}; ?>">
            <a href="<?php echo admin_url('accounting/transaction?group=fixed_equipment&tab=fe_components'); ?>">
              <i class="fa fa-file"></i>&nbsp;<?php echo _l('components'); ?> <span class="text-danger"><?php echo '('.$count_component.')'; ?></span>
            </a>
         </li>
         <li role="presentation" class="<?php if($tab_2 == 'fe_consumables'){echo 'active';}; ?>">
            <a href="<?php echo admin_url('accounting/transaction?group=fixed_equipment&tab=fe_consumables'); ?>">
              <i class="fa fa-file-text"></i>&nbsp;<?php echo _l('consumables'); ?> <span class="text-danger"><?php echo '('.$count_consumable.')'; ?></span>
            </a>
         </li>
         <li role="presentation" class="<?php if($tab_2 == 'fe_maintenances'){echo 'active';}; ?>">
            <a href="<?php echo admin_url('accounting/transaction?group=fixed_equipment&tab=fe_maintenances'); ?>">
              <i class="fa fa-file-text"></i>&nbsp;<?php echo _l('maintenances'); ?> <span class="text-danger"><?php echo '('.$count_maintenance.')'; ?></span>
            </a>
         </li>
         <li role="presentation" class="<?php if($tab_2 == 'fe_depreciations'){echo 'active';}; ?>">
            <a href="<?php echo admin_url('accounting/transaction?group=fixed_equipment&tab=fe_depreciations'); ?>">
              <i class="fa fa-file-text"></i>&nbsp;<?php echo _l('depreciations'); ?> <span class="text-danger"><?php echo '('.$count_depreciation.')'; ?></span>
            </a>
         </li>
      </ul>
   </div>
    <?php echo form_hidden('currency_id', $currency->id); ?>
  <?php $this->load->view($tab_2,array('bulk_actions'=>true)); ?>
</div>



