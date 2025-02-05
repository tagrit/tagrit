<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <hr />
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse1"><h4><?php echo _l('business_overview'); ?></h4></a>
                </h4>
              </div>
              <div id="collapse1" class="panel-collapse collapse in">
                <ul class="list-group">
                  <li class="list-group-item">
                    <div class="row">
                      
                    <div class="col-md-6">
                      <a href="<?php echo admin_url('accounting/rp_balance_sheet_comparison'); ?>"><h4 class="no-margin"><?php echo _l('balance_sheet_comparison'); ?></h4></a>
                      <p><?php echo _l('balance_sheet_comparison_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_balance_sheet_detail'); ?>"><h4 class="no-margin"><?php echo _l('balance_sheet_detail'); ?></h4></a>
                      <p><?php echo _l('balance_sheet_detail_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_balance_sheet_summary'); ?>"><h4 class="no-margin"><h4 class="no-margin"><?php echo _l('balance_sheet_summary'); ?></h4></a>
                      <p><?php echo _l('balance_sheet_summary_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_balance_sheet'); ?>"><h4 class="no-margin"><h4 class="no-margin"><?php echo _l('balance_sheet'); ?></h4></a>
                      <p><?php echo _l('balance_sheet_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_business_snapshot'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('business_snapshot'); ?></h4></a>
                      <p class="hide"><?php echo _l('business_snapshot_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_custom_summary_report'); ?>"><h4 class="no-margin"><h4 class="no-margin"><?php echo _l('custom_summary_report'); ?></h4></a>
                      <p><?php echo _l('custom_summary_report_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_profit_and_loss_as_of_total_income'); ?>"><h4 class="no-margin"><h4 class="no-margin"><?php echo _l('profit_and_loss_as_of_total_income'); ?></h4></a>
                      <p><?php echo _l('profit_and_loss_as_of_total_income_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_profit_and_loss_comparison'); ?>"><h4 class="no-margin"><h4 class="no-margin"><?php echo _l('profit_and_loss_comparison'); ?></h4></a>
                      <p><?php echo _l('profit_and_loss_comparison_note'); ?></p>
                    </div>
                    <div class="col-md-6">
                      
                      <a href="<?php echo admin_url('accounting/rp_profit_and_loss_detail'); ?>"><h4 class="no-margin"><?php echo _l('profit_and_loss_detail'); ?></h4></a>
                      <p><?php echo _l('profit_and_loss_detail_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_profit_and_loss_year_to_date_comparison'); ?>"><h4 class="no-margin"><?php echo _l('profit_and_loss_year_to_date_comparison'); ?></h4></a>
                      <p><?php echo _l('profit_and_loss_year_to_date_comparison_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_profit_and_loss_by_customer'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('profit_and_loss_by_customer'); ?></h4></a>
                      <p class="hide"><?php echo _l('profit_and_loss_by_customer_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_profit_and_loss_by_month'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('profit_and_loss_by_month'); ?></h4></a>
                      <p class="hide"><?php echo _l('profit_and_loss_by_month_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_profit_and_loss_12_months'); ?>"><h4 class="no-margin"><?php echo _l('profit_and_loss_12_months'); ?></h4></a>
                      <p><?php echo _l('profit_and_loss_12_months_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_profit_and_loss'); ?>"><h4 class="no-margin"><?php echo _l('profit_and_loss'); ?></h4></a>
                      <p><?php echo _l('profit_and_loss_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_quarterly_profit_and_loss_summary'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('quarterly_profit_and_loss_summary'); ?></h4></a>
                      <p class="hide"><?php echo _l('quarterly_profit_and_loss_summary_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_statement_of_cash_flows'); ?>"><h4 class="no-margin"><?php echo _l('statement_of_cash_flows'); ?></h4></a>
                      <p><?php echo _l('statement_of_cash_flows_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_statement_of_changes_in_equity'); ?>"><h4 class="no-margin"><?php echo _l('statement_of_changes_in_equity'); ?></h4></a>
                      <p><?php echo _l('statement_of_changes_in_equity_note'); ?></p>
                    </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse5"><h4><?php echo _l('bookkeeping'); ?></h4></a>
                </h4>
              </div>
              <div id="collapse5" class="panel-collapse collapse in">
                <ul class="list-group">
                  <li class="list-group-item">
                    <div class="row">
                     <div class="col-md-6">
                        <a href="<?php echo admin_url('accounting/rp_account_list'); ?>"><h4 class="no-margin"><?php echo _l('account_list'); ?></h4></a>
                      <p><?php echo _l('account_list_note'); ?></p>
                        <a href="<?php echo admin_url('accounting/rp_balance_sheet_comparison'); ?>"><h4 class="no-margin"><?php echo _l('balance_sheet_comparison'); ?></h4></a>
                      <p><?php echo _l('balance_sheet_comparison_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_balance_sheet'); ?>"><h4 class="no-margin"><?php echo _l('balance_sheet'); ?></h4></a>
                      <p><?php echo _l('balance_sheet_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_exceptions_to_closing_date'); ?>"  class="hide"><h4 class="no-margin"><?php echo _l('exceptions_to_closing_date'); ?></h4></a>
                      <p class="hide"><?php echo _l('exceptions_to_closing_date_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_general_ledger'); ?>"><h4 class="no-margin"><?php echo _l('general_ledger'); ?></h4></a>
                      <p><?php echo _l('general_ledger_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_journal'); ?>"><h4 class="no-margin"><?php echo _l('journal'); ?></h4></a>
                      <p><?php echo _l('journal_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_profit_and_loss_comparison'); ?>"><h4 class="no-margin"><?php echo _l('profit_and_loss_comparison'); ?></h4></a>
                      <p><?php echo _l('profit_and_loss_comparison_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_profit_and_loss'); ?>"><h4 class="no-margin"><?php echo _l('profit_and_loss'); ?></h4></a>
                      <p><?php echo _l('profit_and_loss_note'); ?></p>
                    </div>
                    <div class="col-md-6">
                      <a href="<?php echo admin_url('accounting/rp_account_history'); ?>"><h4 class="no-margin"><?php echo _l('account_history'); ?></h4></a>
                      <p><?php echo _l('account_history_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_recent_transactions'); ?>"><h4 class="no-margin"><?php echo _l('recent_transactions'); ?></h4></a>
                      <p><?php echo _l('recent_transactions_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_reconciliation_reports'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('reconciliation_reports'); ?></h4></a>
                      <p class="hide"><?php echo _l('reconciliation_reports_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_recurring_template_list'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('recurring_template_list'); ?></h4></a>
                      <p class="hide"><?php echo _l('recurring_template_list_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_statement_of_cash_flows'); ?>"><h4 class="no-margin"><?php echo _l('statement_of_cash_flows'); ?></h4></a>
                      <p><?php echo _l('statement_of_cash_flows_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_transaction_detail_by_account'); ?>"><h4 class="no-margin"><?php echo _l('transaction_detail_by_account'); ?></h4></a>
                      <p><?php echo _l('transaction_detail_by_account_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_transaction_list_by_date'); ?>"><h4 class="no-margin"><?php echo _l('transaction_list_by_date'); ?></h4></a>
                      <p><?php echo _l('transaction_list_by_date_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_transaction_list_with_splits'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('transaction_list_with_splits'); ?></h4></a>
                      <p class="hide"><?php echo _l('transaction_list_with_splits_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_trial_balance'); ?>"><h4 class="no-margin"><?php echo _l('trial_balance'); ?></h4></a>
                      <p><?php echo _l('trial_balance_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_bank_reconciliation_summary'); ?>"><h4 class="no-margin"><?php echo _l('bank_reconciliation_summary'); ?></h4></a>
                      <p class=""><?php echo _l('bank_reconciliation_summary_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_bank_reconciliation_detail'); ?>"><h4 class="no-margin"><?php echo _l('bank_reconciliation_detail'); ?></h4></a>
                      <p class=""><?php echo _l('bank_reconciliation_detail_note'); ?></p>
                    </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse44"><h4><?php echo _l('budget'); ?></h4></a>
                </h4>
              </div>
              <div id="collapse44" class="panel-collapse collapse in">
                <ul class="list-group">
                  <li class="list-group-item">
                    <div class="row">
                     <div class="col-md-6">
                       <a href="<?php echo admin_url('accounting/rp_budget_overview'); ?>"><h4 class="no-margin"><?php echo _l('budget_overview'); ?></h4></a>
                      <p><?php echo _l('budget_overview_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_profit_and_loss_budget_vs_actual'); ?>"><h4 class="no-margin"><?php echo _l('profit_and_loss_budget_vs_actual'); ?></h4></a>
                      <p><?php echo _l('profit_and_loss_budget_vs_actual_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_profit_and_loss_budget_performance'); ?>"><h4 class="no-margin"><?php echo _l('profit_and_loss_budget_performance'); ?></h4></a>
                      <p><?php echo _l('profit_and_loss_budget_performance_note'); ?></p>
                    </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse7"><h4><?php echo _l('sales_tax'); ?></h4></a>
                </h4>
              </div>
              <div id="collapse7" class="panel-collapse collapse in">
                <ul class="list-group">
                  <li class="list-group-item">
                    <div class="row">
                      <div class="col-md-6">
                        <a href="<?php echo admin_url('accounting/rp_tax_detail_report'); ?>"><h4 class="no-margin"><?php echo _l('tax_detail_report'); ?></h4></a>
                        <p><?php echo _l('tax_detail_report_note'); ?></p>
                        <a href="<?php echo admin_url('accounting/rp_tax_exception_report'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('tax_exception_report'); ?></h4></a>
                        <p class="hide"><?php echo _l('tax_exception_report_note'); ?></p>
                        <a href="<?php echo admin_url('accounting/rp_tax_summary_report'); ?>"><h4 class="no-margin"><?php echo _l('tax_summary_report'); ?></h4></a>
                        <p><?php echo _l('tax_summary_report_note'); ?></p>
                      </div>
                      <div class="col-md-6">
                        <a href="<?php echo admin_url('accounting/rp_tax_liability_report'); ?>"><h4 class="no-margin"><?php echo _l('tax_liability_report'); ?></h4></a>
                        <p><?php echo _l('tax_liability_report_note'); ?></p>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse14"><h4><?php echo _l('who_owes_you'); ?></h4></a>
                </h4>
              </div>
              <div id="collapse14" class="panel-collapse collapse in">
                <ul class="list-group">
                  <li class="list-group-item">
                    <div class="row">
                     <div class="col-md-6">
                        <a href="<?php echo admin_url('accounting/rp_accounts_receivable_ageing_summary'); ?>"><h4 class="no-margin"><?php echo _l('accounts_receivable_ageing_summary'); ?></h4></a>
                        <p><?php echo _l('accounts_receivable_ageing_summary_note'); ?></p>
                        <a href="<?php echo admin_url('accounting/rp_accounts_receivable_ageing_detail'); ?>"><h4 class="no-margin"><?php echo _l('accounts_receivable_ageing_detail'); ?></h4></a>
                        <p><?php echo _l('accounts_receivable_ageing_detail_note'); ?></p>
                    </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse2"><h4><?php echo _l('sales_and_customers'); ?></h4></a>
                </h4>
              </div>
              <div id="collapse2" class="panel-collapse collapse in">
                <ul class="list-group">
                  <li class="list-group-item">
                    <div class="row">
                      
                    <div class="col-md-6">
                      <a href="<?php echo admin_url('accounting/rp_customer_contact_list'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('customer_contact_list'); ?></h4></a>
                      <p class="hide"><?php echo _l('customer_contact_list_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_deposit_detail'); ?>"><h4 class="no-margin"><?php echo _l('deposit_detail'); ?></h4></a>
                      <p><?php echo _l('deposit_detail_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_estimates_by_customer'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('estimates_by_customer'); ?></h4></a>
                      <p class="hide"><?php echo _l('estimates_by_customer_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_income_by_customer_summary'); ?>"><h4 class="no-margin"><?php echo _l('income_by_customer_summary'); ?></h4></a>
                      <p><?php echo _l('income_by_customer_summary_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_inventory_valuation_detail'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('inventory_valuation_detail'); ?></h4></a>
                      <p class="hide"><?php echo _l('inventory_valuation_detail_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_inventory_valuation_summary'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('inventory_valuation_summary'); ?></h4></a>
                      <p class="hide"><?php echo _l('inventory_valuation_summary_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_payment_method_list'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('payment_method_list'); ?></h4></a>
                      <p class="hide"><?php echo _l('payment_method_list_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_product_service_list'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('product_service_list'); ?></h4></a>
                      <p class="hide"><?php echo _l('product_service_list_note'); ?></p>
                    </div>
                    <div class="col-md-6">
                      <a href="<?php echo admin_url('accounting/rp_sales_by_customer_detail'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('sales_by_customer_detail'); ?></h4></a>
                      <p class="hide"><?php echo _l('sales_by_customer_detail_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_sales_by_customer_summary'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('sales_by_customer_summary'); ?></h4></a>
                      <p class="hide"><?php echo _l('sales_by_customer_summary_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_sales_by_product_service_detail'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('sales_by_product_service_detail'); ?></h4></a>
                      <p class="hide"><?php echo _l('sales_by_product_service_detail_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_sales_by_product_service_summary'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('sales_by_product_service_summary'); ?></h4></a>
                      <p class="hide"><?php echo _l('sales_by_product_service_summary_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_stock_take_worksheet'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('stock_take_worksheet'); ?></h4></a>
                      <p class="hide"><?php echo _l('stock_take_worksheet_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_time_activities_by_customer_detail'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('time_activities_by_customer_detail'); ?></h4></a>
                      <p class="hide"><?php echo _l('time_activities_by_customer_detail_note'); ?></p>
                      <a href="<?php echo admin_url('accounting/rp_transaction_list_by_customer'); ?>" class="hide"><h4 class="no-margin"><?php echo _l('transaction_list_by_customer'); ?></h4></a>
                      <p class="hide"><?php echo _l('transaction_list_by_customer_note'); ?></p>
                    </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse15"><h4><?php echo _l('what_you_owe'); ?></h4></a>
                </h4>
              </div>
              <div id="collapse15" class="panel-collapse collapse in">
                <ul class="list-group">
                  <li class="list-group-item">
                    <div class="row">
                     <div class="col-md-6">
                        <a href="<?php echo admin_url('accounting/rp_accounts_payable_ageing_summary'); ?>"><h4 class="no-margin"><?php echo _l('accounts_payable_ageing_summary'); ?></h4></a>
                        <p><?php echo _l('accounts_payable_ageing_summary_note'); ?></p>
                        <a href="<?php echo admin_url('accounting/rp_accounts_payable_ageing_detail'); ?>"><h4 class="no-margin"><?php echo _l('accounts_payable_ageing_detail'); ?></h4></a>
                        <p><?php echo _l('accounts_payable_ageing_detail_note'); ?></p>
                    </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse3"><h4><?php echo _l('expenses_and_suppliers'); ?></h4></a>
                </h4>
              </div>
              <div id="collapse3" class="panel-collapse collapse in">
                <ul class="list-group">
                  <li class="list-group-item">
                    <div class="row">
                      <div class="col-md-6">
                          <a href="<?php echo admin_url('accounting/rp_check_detail'); ?>"><h4 class="no-margin"><?php echo _l('cheque_detail'); ?></h4></a>
                        <p><?php echo _l('check_detail_note'); ?></p>
                          <a href="<?php echo admin_url('accounting/rp_expenses_by_supplier_summary'); ?>" class="hide"><h4><?php echo _l('expenses_by_supplier_summary'); ?></h4></a>
                        <p class="hide"><?php echo _l('expenses_by_supplier_summary_note'); ?></p>
                          <a href="<?php echo admin_url('accounting/rp_purchases_by_product_service_detail'); ?>" class="hide"><h4><?php echo _l('purchases_by_product_service_detail'); ?></h4></a>
                        <p class="hide"><?php echo _l('purchases_by_product_service_detail_note'); ?></p>
                      </div>
                      <div class="col-md-6">
                          <a href="<?php echo admin_url('accounting/rp_purchases_by_supplier_detail'); ?>" class="hide"><h4><?php echo _l('purchases_by_supplier_detail'); ?></h4></a>
                        <p class="hide"><?php echo _l('purchases_by_supplier_detail_note'); ?></p>
                          <a href="<?php echo admin_url('accounting/rp_supplier_contact_list'); ?>" class="hide"><h4><?php echo _l('supplier_contact_list'); ?></h4></a>
                        <p class="hide"><?php echo _l('supplier_contact_list_note'); ?></p>
                          <a href="<?php echo admin_url('accounting/rp_transaction_list_by_supplier'); ?>" class="hide"><h4><?php echo _l('transaction_list_by_supplier'); ?></h4></a>
                        <p class="hide"><?php echo _l('transaction_list_by_supplier_note'); ?></p>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="panel-group hide">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse4"><h4><?php echo _l('employees'); ?></h4></a>
                </h4>
              </div>
              <div id="collapse4" class="panel-collapse collapse in">
                <ul class="list-group">
                  <li class="list-group-item">
                    <div class="row">
                     <div class="col-md-6">
                        <a href="<?php echo admin_url('accounting/rp_employee_contact_list'); ?>"><h4 class="no-margin"><?php echo _l('employee_contact_list'); ?></h4></a>
                      <p><?php echo _l('employee_contact_list_note'); ?></p>
                        <a href="<?php echo admin_url('accounting/rp_recent_edited_time_activities'); ?>"><h4 class="no-margin"><?php echo _l('recent_edited_time_activities'); ?></h4></a>
                      <p><?php echo _l('recent_edited_time_activities_note'); ?></p>
                        <a href="<?php echo admin_url('accounting/rp_time_activities_by_employee_detail'); ?>"><h4 class="no-margin"><?php echo _l('time_activities_by_employee_detail'); ?></h4></a>
                      <p><?php echo _l('time_activities_by_employee_detail_note'); ?></p>
                    </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          
          <div class="panel-group hide">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse6"><h4><?php echo _l('payroll'); ?></h4></a>
                </h4>
              </div>
              <div id="collapse6" class="panel-collapse collapse in">
                <ul class="list-group">
                  <li class="list-group-item">
                    <div class="row">
                     <div class="col-md-6">
                        <a href="<?php echo admin_url('accounting/rp_employee_contact_list'); ?>"><h4 class="no-margin"><?php echo _l('employee_contact_list'); ?></h4></a>
                      <p><?php echo _l('employee_contact_list_note'); ?></p>
                        <a href="<?php echo admin_url('accounting/rp_recent_edited_time_activities'); ?>"><h4 class="no-margin"><?php echo _l('recent_edited_time_activities'); ?></h4></a>
                      <p><?php echo _l('recent_edited_time_activities_note'); ?></p>
                        <a href="<?php echo admin_url('accounting/rp_time_activities_by_employee_detail'); ?>"><h4 class="no-margin"><?php echo _l('time_activities_by_employee_detail'); ?></h4></a>
                      <p><?php echo _l('time_activities_by_employee_detail_note'); ?></p>
                    </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
</body>
</html>
