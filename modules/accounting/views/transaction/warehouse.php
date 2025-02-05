<div class="horizontal-scrollable-tabs">
   <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
   <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
   <div class="horizontal-tabs">
      <ul class="nav nav-tabs profile-tabs row customer-profile-tabs nav-tabs-horizontal" role="tablist">
         <li role="presentation" class="<?php if($tab_2 == 'stock_import'){echo 'active';}; ?>">
            <a href="<?php echo admin_url('accounting/transaction?group=warehouse&tab=stock_import'); ?>">
              <i class="fa fa-object-group"></i>&nbsp;<?php echo _l('stock_import'); ?> <span class="text-danger"><?php echo '('.$count_stock_import.')'; ?></span>
            </a>
         </li>
         <li role="presentation" class="<?php if($tab_2 == 'stock_export'){echo 'active';}; ?>">
            <a href="<?php echo admin_url('accounting/transaction?group=warehouse&tab=stock_export'); ?>">
              <i class="fa fa-object-ungroup"></i>&nbsp;<?php echo _l('stock_export'); ?> <span class="text-danger"><?php echo '('.$count_stock_export.')'; ?></span>
            </a>
         </li>
         <li role="presentation" class="<?php if($tab_2 == 'loss_adjustment'){echo 'active';}; ?>">
            <a href="<?php echo admin_url('accounting/transaction?group=warehouse&tab=loss_adjustment'); ?>">
              <i class="fa fa-adjust"></i>&nbsp;<?php echo _l('loss_adjustment'); ?> <span class="text-danger"><?php echo '('.$count_loss_adjustment.')'; ?></span>
            </a>
         </li>
         <li role="presentation" class="<?php if($tab_2 == 'opening_stock'){echo 'active';}; ?>">
            <a href="<?php echo admin_url('accounting/transaction?group=warehouse&tab=opening_stock'); ?>">
              <i class="fa fa-calendar"></i>&nbsp;<?php echo _l('opening_stock'); ?> <span class="text-danger"><?php echo '('.$count_opening_stock.')'; ?></span>
            </a>
         </li>
      </ul>
   </div>

   <?php echo form_hidden('currency_id', $currency->id); ?>
   <?php $this->load->view($tab_2,array('bulk_actions'=>true)); ?>
</div>
