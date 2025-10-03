<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <?php $arrAtt = array();
                $arrAtt['data-type']='currency';

                $arrAtt2 = array();
                $arrAtt2['data-type']='currency';
                $arrAtt2['readonly']='true';
                ?>
          <?php echo form_open_multipart(admin_url('accounting/reconcile'),array('id'=>'reconcile-account-form','autocomplete'=>'off')); ?>
          <?php echo form_hidden('resume', $resume); ?>
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <p ><?php echo _l('open_your_statement_and_we_will_get_started'); ?></p>
          <hr />
            <h5><?php echo _l('which_account_do_you_want_to_reconcile'); ?></h5>
            <?php echo render_select('account',$accounts,array('id','name', 'account_type_name'),'acc_account','',array(),array(),'','',false); ?>
            <div id="divInfo" class="<?php if($resume == 1){echo 'hide';} ?>">
            <br>
            <h5><?php echo _l('add_the_following_information'); ?></h5>
            <div class="row">
              <div class="col-md-4">
                <?php echo render_input('beginning_balance','beginning_balance', app_format_money($beginning_balance, $currency->name),'text', $arrAtt2); ?>
              </div>
              <div class="col-md-4">
                <?php echo render_input('ending_balance','ending_balance','','text', $arrAtt); ?>
              </div>
              <div class="col-md-4">
                <?php echo render_date_input('ending_date','ending_date'); ?>
              </div>
            </div>
              <br>
              <h5 class="hide"><?php echo _l('enter_the_service_charge_or_interest_earned_if_necessary'); ?></h5>
              <div class="row hide">
                <div class="col-md-4">
                  <?php echo render_date_input('expense_date','invoice_payments_table_date_heading'); ?>
                </div>
                <div class="col-md-4">
                  <?php echo render_input('service_charge','service_charge','','text', $arrAtt); ?>
                </div>
                <div class="col-md-4">
                  <?php echo render_select('expense_account',$accounts,array('id','name', 'account_type_name'),'expense_account','',array(),array(),'','',false); ?>
                </div>
              </div>
              <div class="row hide">
                <div class="col-md-4">
                  <?php echo render_date_input('income_date','invoice_payments_table_date_heading'); ?>
                </div>
                <div class="col-md-4">
                  <?php echo render_input('interest_earned','interest_earned','','text', $arrAtt); ?>
                </div>
                <div class="col-md-4">
                  <?php echo render_select('income_account',$accounts,array('id','name', 'account_type_name'),'income_account','',array(),array(),'','',false); ?>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                    <hr>
                    <button type="submit" class="btn btn-info pull-right <?php if(!has_permission('accounting_reconcile', '', 'create')){echo 'hide';} ?>"><?php echo _l('start_reconciling'); ?></button>
                    <button type="button" class="btn btn-warning pull-right mright5 hide_restored <?php echo new_html_entity_decode($hide_restored); ?> <?php if(!has_permission('accounting_reconcile', '', 'edit')){echo 'hide';} ?>"><?php echo _l('acc_restored_last'); ?></button>
                </div>
              </div>
            </div>
            <div id="divResume" class="<?php if($resume == 0){echo 'hide';} ?>">
              <div class="row">
                <div class="col-md-12">
                    <hr>
                    <button type="submit" class="btn btn-info pull-right <?php if(!has_permission('accounting_reconcile', '', 'edit')){echo 'hide';} ?>"><?php echo _l('resume_reconciling'); ?></button>
                    <button type="button" class="btn btn-warning pull-right mright5 hide_restored <?php echo new_html_entity_decode($hide_restored); ?> <?php if(!has_permission('accounting_reconcile', '', 'edit')){echo 'hide';} ?>"><?php echo _l('acc_restored_last'); ?></button>
                   
                </div>
              </div>
            </div>
          <?php echo form_close(); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
</body>
</html>
