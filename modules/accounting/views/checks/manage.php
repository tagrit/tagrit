<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s mbot10">
               <div class="panel-body _buttons">
                  <?php echo form_hidden('type',$type); ?>

                  <div class="horizontal-scrollable-tabs preview-tabs-top">
                   <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                   <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                   <div class="horizontal-tabs">

                     <ul class="nav nav-tabs nav-tabs-horizontal no-margin" role="tablist">
                           <?php if(has_permission('accounting_bills','','create')){ ?>
                               <li class="<?php echo ($type == 'new_bill' ? 'active' : '') ?>">
                                 <a href="<?php echo admin_url('accounting/bill'); ?>"><?php echo _l('add_new_bill'); ?></a>
                               </li>
                           <?php } ?>
                         <li class="<?php echo ($type == 'unpaid' ? 'active' : '') ?>">
                           <a href="<?php echo admin_url('accounting/bills?type=unpaid'); ?>"><?php echo _l('unpaid_bills'); ?></a>
                         </li>
                         <li class="<?php echo ($type == 'approved' ? 'active' : '') ?>">
                              <a href="<?php echo admin_url('accounting/bills?type=approved'); ?>"><?php echo _l('approved_bills'); ?></a>
                         </li>
                         <li class="<?php echo ($type == 'check' ? 'active' : '') ?>">
                           <a href="<?php echo admin_url('accounting/checks'); ?>"><?php echo _l('write_checks'); ?></a>
                         </li>
                         <li class="<?php echo ($type == 'paid' ? 'active' : '') ?>">
                           <a href="<?php echo admin_url('accounting/bills?type=paid'); ?>"><?php echo _l('paid_bills'); ?></a>
                         </li>
                         <li class="<?php echo ($type == 'check_register' ? 'active' : '') ?>">
                              <a href="<?php echo admin_url('accounting/check_register'); ?>"><?php echo _l('check_register'); ?></a>
                         </li>
                         <li class="<?php echo ($type == 'configure_checks' ? 'active' : '') ?>">
                              <a href="<?php echo admin_url('accounting/configure_checks'); ?>"><?php echo _l('configure_checks'); ?></a>
                         </li>
                     </ul>
                   </div>
                 </div>
               </div>
            </div>
            <div class="row">
				<?php
				$this->load->view('accounting/checks/list_template');
				?>
			</div>
         </div>
      </div>
   </div>
</div>
<div id="content_print" class="hide"></div>
<script>var hidden_columns = [2,6,7,8];</script>
<?php init_tail(); ?>
<?php require 'modules/accounting/assets/js/checks/manage_js.php';?>

</body>
</html>
