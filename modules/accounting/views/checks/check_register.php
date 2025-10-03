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
               <div class="col-md-12" id="small-table">
                  <div class="panel_s">
                     <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <?php echo render_select('bank_account_check',$accounts,array('id','name', 'account_type_name'),'bank_account'); ?>
                            </div>
                          <div class="col-md-3">
                              <?php 
                                $method = [
                                  1 => ['id' => '1', 'name' => _l('issued')],
                                  2 => ['id' => '2', 'name' => _l('printing_error')],
                                  3 => ['id' => '3', 'name' => _l('void')],
                                ];
                                echo render_select('status', $method, array('id', 'name'),'status', '', array(), array(), '', '', true);
                                ?>
                          </div>
                          <div class="col-md-3">
                            <?php echo render_date_input('from_date','from_date'); ?>
                          </div>
                          <div class="col-md-3">
                            <?php echo render_date_input('to_date','to_date'); ?>
                          </div>
                        </div>
                        <div class="clearfix"></div>
                       <?php 
                        $table_data = array(
                          _l('acc_date'),
                          _l('check_number'),
                          _l('payee'),
                          _l('expense_dt_table_heading_amount'),
                          _l('status'),
                         );

                        render_datatable($table_data, (isset($class) ? $class : 'checks'));
                        ?>
                     </div>
                  </div>
               </div>
               <div class="col-md-7 small-table-right-col">
                  <div id="bill_div" class="hide">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<?php require 'modules/accounting/assets/js/checks/check_register_js.php';?>

</body>
</html>
