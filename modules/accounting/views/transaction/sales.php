<div class="horizontal-scrollable-tabs">
   <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
   <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
   <div class="horizontal-tabs">
      <ul class="nav nav-tabs profile-tabs row customer-profile-tabs nav-tabs-horizontal" role="tablist">
         <li role="presentation" class="<?php if($tab_2 == 'payment'){echo 'active';}; ?>">
            <a href="<?php echo admin_url('accounting/transaction?group=sales&tab=payment'); ?>">
              <i class="fa fa-credit-card"></i>&nbsp;<?php echo _l('payment'); ?> <span class="text-danger"><?php echo '('.$count_payment.')'; ?></span>
            </a>
         </li>
         <li role="presentation" class="<?php if($tab_2 == 'invoice'){echo 'active';}; ?>">
            <a href="<?php echo admin_url('accounting/transaction?group=sales&tab=invoice'); ?>">
              <i class="fa fa-file-text"></i>&nbsp;<?php echo _l('invoice'); ?> <span class="text-danger"><?php echo '('.$count_invoice.')'; ?></span>
            </a>
         </li>

         <?php if(acc_required_omni_sales_module()){ ?>
            <li role="presentation" class="<?php if($tab_2 == 'omni_sales_return_order'){echo 'active';}; ?>">
               <a href="<?php echo admin_url('accounting/transaction?group=sales&tab=omni_sales_return_order'); ?>">
                 <i class="fa fa-file-text"></i>&nbsp;<?php echo _l('omni_sales_return_order'); ?> <span class="text-danger"><?php echo '('.$count_omni_sales_return_order.')'; ?></span>
               </a>
            </li>
            <li role="presentation" class="<?php if($tab_2 == 'omni_sales_refund'){echo 'active';}; ?>">
               <a href="<?php echo admin_url('accounting/transaction?group=sales&tab=omni_sales_refund'); ?>">
                 <i class="fa fa-file"></i>&nbsp;<?php echo _l('omni_sales_refund'); ?> <span class="text-danger"><?php echo '('.$count_omni_sales_refund.')'; ?></span>
               </a>
            </li>
         <?php } ?>
      </ul>
   </div>
    <?php echo form_hidden('currency_id', $currency->id); ?>
  <?php $this->load->view($tab_2,array('bulk_actions'=>true)); ?>
</div>
