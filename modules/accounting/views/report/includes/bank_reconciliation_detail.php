<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="panel_s">
      <div class="panel-body">
        <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
        <a href="<?php echo admin_url('accounting/report'); ?>"><?php echo _l('back_to_report_list'); ?></a>
        <br>
        <hr />
        <div class="row">
          <div class="col-md-10">
            <div class="row">
              <?php echo form_open(admin_url('accounting/view_report'),array('id'=>'filter-form')); ?>
              <div class="col-md-4">
                <?php
                echo render_select('reconcile_account',$bank_accounts,array('id','name', 'account_type_name'),'acc_account', $default_account, array(), array(), '', '', false); ?>
              </div>
              <div class="col-md-4">
                <?php echo render_select('reconcile',$reconcile,array('id','ending_date'),'statement_ending_date', $default_reconcile, array(), array(), '', '', false); ?>
              </div>
              <div class="col-md-3">
                <?php echo form_hidden('type', 'bank_reconciliation_detail'); ?>
                <button type="submit" class="btn btn-info btn-submit mtop25"><?php echo _l('filter'); ?></button>
              </div>
              <?php echo form_close(); ?>
            </div>
          </div>
          <div class="col-md-2">
              <div class="btn-group pull-right mtop25">
                 <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
                 <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                       <a href="#" onclick="printDiv2(); return false;">
                       <?php echo _l('export_to_pdf'); ?>
                       </a>
                    </li>
                    <li>
                       <a href="#" onclick="printExcel(); return false;">
                       <?php echo _l('export_to_excel'); ?>
                       </a>
                    </li>
                 </ul>
              </div>
            </div>
     </div>
     <div class="row"> 
      <div class="col-md-12"> 
        <hr>
      </div>
    </div>
    <div  class="report-container">
      <div class="page-size2" id="DivIdToPrint">
      </div>
    </div>
  </div>
</div>
</div>
</div>
<!-- box loading -->
<div id="box-loading"></div>
<?php init_tail(); ?>
</body>
</html>
