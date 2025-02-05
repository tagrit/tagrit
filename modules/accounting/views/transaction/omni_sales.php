<div class="horizontal-scrollable-tabs">
   <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
   <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
   <div class="horizontal-tabs">
      <ul class="nav nav-tabs profile-tabs row customer-profile-tabs nav-tabs-horizontal" role="tablist">
         <li role="presentation" class="<?php if($tab_2 == 'omni_sales_return_order'){echo 'active';}; ?>">
            <a href="<?php echo admin_url('accounting/transaction?group=omni_sales&tab=omni_sales_return_order'); ?>">
              <i class="fa fa-file-text-o"></i>&nbsp;<?php echo _l('omni_sales_return_order'); ?> <span class="text-danger"><?php echo '('.$count_omni_sales_return_order.')'; ?></span>
            </a>
         </li>
         <li role="presentation" class="<?php if($tab_2 == 'omni_sales_refund'){echo 'active';}; ?>">
            <a href="<?php echo admin_url('accounting/transaction?group=omni_sales&tab=omni_sales_refund'); ?>">
              <i class="fa fa-file"></i>&nbsp;<?php echo _l('omni_sales_refund'); ?> <span class="text-danger"><?php echo '('.$count_omni_sales_refund.')'; ?></span>
            </a>
         </li>
      </ul>
   </div>
    <?php echo form_hidden('currency_id', $currency->id); ?>
  <?php $this->load->view($tab_2,array('bulk_actions'=>true)); ?>
</div>
