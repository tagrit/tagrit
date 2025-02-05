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
          <?php echo form_open(admin_url('accounting/finish_reconcile_account'),array('id'=>'reconcile-account-form','autocomplete'=>'off')); ?>
            <?php echo form_hidden('account', $account->id); ?>
            <?php echo form_hidden('reconcile', $reconcile->id); ?>
            <?php echo form_hidden('history_ids'); ?>
            <?php echo form_hidden('finish', 0); ?>
          <?php echo form_close(); ?>

          <div class="row">
          <div class="col-md-6">
            <h4 class="no-margin font-bold"><?php echo _l($title).' '. ($account->name != '' ? $account->name : _l($account->key_name)); ?></h4>
          </div>
          <div class="col-md-6">
            <button type="button" class="btn btn-success pull-right mleft5" onclick="finish_now(); return false;"><?php echo _l('finish_now'); ?></button>
            <button type="button" class="btn btn-info pull-right mleft5" onclick="save_for_later(); return false;"><?php echo _l('save_for_later'); ?></button>
            <button type="button" class="btn btn-default pull-right mleft5" onclick="edit_info(<?php echo new_html_entity_decode($account->id); ?>); return false;"><?php echo _l('edit_info'); ?></button>
          </div>
          </div>
          <hr />
          <div class="integrations-banking-reconcile-ui">
            <div class="reconcile-stage">
            <table class="stage-expanded-section">
              <tbody>
                <tr>
                  <td>
                    <table class="equation-top">
                      <tbody>
                        <tr>
                          <td>
                            <div class="automationID-StatementEndingBalance">
                              <div class="money-field">
                                <div class="inlineBlock">
                                  <div class="ha-numeral medium"><?php echo app_format_money($reconcile->ending_balance, $currency->name); ?></div>
                                  <div class="ha-numeral description">
                                    <span><?php echo _l('statement_ending_balance_uppercase'); ?></span>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </td>
                          <td class="operator">-</td>
                          <td colspan="2">
                            <div class="automationID-ClearedBalance">
                              <div class="money-field">
                                <div class="inlineBlock">
                                  <div class="ha-numeral medium" id="cleared_balance_amount"><?php echo app_format_money($reconcile->beginning_balance - $reconcile->service_charge + $reconcile->interest_earned, $currency->name); ?></div>
                                  <div class="ha-numeral description">
                                    <span><?php echo _l('cleared_balance_uppercase'); ?></span>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </td>
                          <td class="spacer-large"></td>
                        </tr>
                        <tr class="brace">
                          <td colspan="3" class="brace-head"></td>
                          <td colspan="2"></td>
                        </tr>
                      </tbody>
                    </table>
                    <table class="equation-bottom">
                      <tbody>
                        <tr class="brace">
                          <td></td>
                          <td colspan="5" class="brace-arms"></td>
                          <td></td>
                        </tr>
                        <tr>
                          <td class="spacer-large"></td>
                          <td>
                            <div class="automationID-BeginningBalance">
                              <div class="money-field">
                                <div class="inlineBlock">
                                  <div class="ha-numeral" id="beginning_balance_amount"><?php echo app_format_money($reconcile->beginning_balance, $currency->name); ?></div>
                                  <div class="ha-numeral description">
                                    <span><?php echo _l('beginning_balance_uppercase'); ?></span>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </td>
                          <td class="operator"><div>-</div></td>
                          <td>
                            <div class="automationID-Payments">
                              <div class="money-field">
                                <div class="inlineBlock">
                                  <div class="ha-numeral" id="payment_amount"><?php echo app_format_money($reconcile->service_charge, $currency->name); ?></div>
                                  <div class="ha-numeral description">
                                    <span id="count_payments"><?php echo ($reconcile->service_charge > 0 ? '1 ': '0 '). _l('payments_uppercase'); ?></span>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </td>
                          <td class="operator"><div>+</div></td>
                          <td>
                            <div class="automationID-Deposits">
                              <div class="money-field">
                                <div class="inlineBlock">
                                  <div class="ha-numeral" id="deposit_amount"><?php echo app_format_money($reconcile->interest_earned, $currency->name); ?></div>
                                  <div class="ha-numeral description">
                                    <span id="count_deposit"><?php echo ($reconcile->interest_earned > 0 ? '1 ': '0 '). _l('deposits_uppercase'); ?></span>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </td>
                          <td class="spacer"></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td class="difference">
                    <div class="automationID-Difference">
                      <div class="difference-icon"><i class="hidden-xs ha-round-badge ha-warn"></i></div>
                      <div class="money-field">
                        <div class="inlineBlock">
                          <div class="ha-numeral medium" id="difference_amount"><?php echo app_format_money($reconcile->ending_balance - ($reconcile->beginning_balance - $reconcile->service_charge + $reconcile->interest_earned), $currency->name); ?></div>
                          <div class="ha-numeral description">
                            <span class="tool-tipped"><?php echo _l('difference_uppercase'); ?></span>
                          </div>
                        </div>
                      </div>
                      <div class="tooltip-empty-prevsib"></div>
                    </div>
                    <button type="button" class="btn btn-info" onclick="calculate(); return false;"><?php echo _l('calculate'); ?></button>
                  </td>
                </tr>
              </tbody>
            </table>
            </div>
          </div>
          <table class="table table-reconcile-history scroll-responsive">
           <thead>
              <tr>
                <th class="not-export sorting_disabled" rowspan="1" colspan="1" aria-label=" - "><span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all_a" data-to-table="reconcile-history"><label></label></div></th>
                 <th><?php echo _l('expense_dt_table_heading_date'); ?></th>
                 <th><?php echo _l('type'); ?></th>
                 <th><?php echo _l('acc_account'); ?></th>
                 <th><?php echo _l('payee'); ?></th>
                 <th><?php echo _l('description'); ?></th>
                 <th><?php echo _l('payment'); ?></th>
                 <th><?php echo _l('deposit'); ?></th>
              </tr>
           </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="adjustment-modal">
  <div class="modal-dialog">
    <div class="modal-content">
        <?php echo form_open(admin_url('accounting/adjustment'),array('id'=>'adjustment-form','autocomplete'=>'off')); ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l('finish_difference_header')?></h4>
      </div>
      <div class="modal-body">
        <p><?php echo _l('finish_difference_note_1'); ?></p>
        <p><?php echo _l('finish_difference_note'); ?></p>
        <?php echo form_hidden('adjustment_amount'); ?>
        <?php echo form_hidden('account', $account->id); ?>
        <?php echo form_hidden('reconcile', $reconcile->id); ?>
        <?php echo render_date_input('adjustment_date', 'adjustment_date', _d($reconcile->ending_date)); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info intext-btn"><?php echo _l('add_adjustment_and_finish'); ?></button>
      </div>
        <?php echo form_close(); ?>
    </div>
  </div>
</div>

<div class="modal fade" id="edit-info-modal">
  <div class="modal-dialog">
    <div class="modal-content">
        <?php echo form_open(admin_url('accounting/edit_reconcile'),array('id'=>'edit-reconcile-form','autocomplete'=>'off')); ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l($title).' '. ($account->name != '' ? $account->name : _l($account->key_name)); ?></h4>
      </div>
      <div class="modal-body">
        <h5><?php echo _l('add_the_following_information'); ?></h5>
        <div class="row">
          <div class="col-md-4">
            <?php echo render_input('beginning_balance','beginning_balance',$reconcile->beginning_balance,'text', $arrAtt2); ?>
          </div>
          <div class="col-md-4">
            <?php echo render_input('ending_balance','ending_balance',$reconcile->ending_balance,'text', $arrAtt); ?>
          </div>
          <div class="col-md-4">
            <?php echo render_date_input('ending_date','ending_date',_d($reconcile->ending_date)); ?>
          </div>
        </div>
        <br>
        <h5 class="hide"><?php echo _l('enter_the_service_charge_or_interest_earned_if_necessary'); ?></h5>
        <div class="row hide">
          <div class="col-md-4">
            <?php echo render_date_input('expense_date','invoice_payments_table_date_heading', _d($reconcile->expense_date)); ?>
          </div>
          <div class="col-md-4">
            <?php echo render_input('service_charge','service_charge',$reconcile->service_charge,'text', $arrAtt); ?>
          </div>
          <div class="col-md-4">
            <?php echo render_select('expense_account',$accounts,array('id','name', 'account_type_name'),'expense_account',$reconcile->expense_account,array(),array(),'','',false); ?>
          </div>
        </div>
        <div class="row hide">
          <div class="col-md-4">
            <?php echo render_date_input('income_date','invoice_payments_table_date_heading',_d($reconcile->income_date)); ?>
          </div>
          <div class="col-md-4">
            <?php echo render_input('interest_earned','interest_earned',$reconcile->interest_earned,'text', $arrAtt); ?>
          </div>
          <div class="col-md-4">
            <?php echo render_select('income_account',$accounts,array('id','name', 'account_type_name'),'income_account',$reconcile->income_account,array(),array(),'','',false); ?>
          </div>
        </div>
        <?php echo form_hidden('reconcile_id', $reconcile->id); ?>
        <?php echo form_hidden('account', $account->id); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info intext-btn"><?php echo _l('submit'); ?></button>
      </div>
        <?php echo form_close(); ?>
    </div>
  </div>
</div>
<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/accounting/assets/js/reconcile/reconcile_account_js.php'; ?>
