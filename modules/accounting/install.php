<?php

defined('BASEPATH') or exit('No direct script access allowed');

add_option('acc_first_month_of_financial_year', 'January');
add_option('acc_first_month_of_tax_year', 'same_as_financial_year');
add_option('acc_accounting_method', 'accrual');
add_option('acc_close_the_books', 0);
add_option('acc_allow_changes_after_viewing', 'allow_changes_after_viewing_a_warning');
add_option('acc_close_book_password');
add_option('acc_close_book_passwordr');
add_option('acc_enable_account_numbers', 0);
add_option('acc_show_account_numbers', 0);
add_option('acc_closing_date');

add_option('acc_add_default_account', 0);
add_option('acc_add_default_account_new', 0);
add_option('acc_invoice_automatic_conversion', 1);
add_option('acc_payment_automatic_conversion', 1);
add_option('acc_credit_note_automatic_conversion', 1);
add_option('acc_credit_note_refund_automatic_conversion', 1);
add_option('acc_expense_automatic_conversion', 1);
add_option('acc_tax_automatic_conversion', 1);

add_option('acc_invoice_payment_account', 66);
add_option('acc_invoice_deposit_to', 1);
add_option('acc_payment_payment_account', 1);
add_option('acc_payment_deposit_to', 13);
add_option('acc_credit_note_payment_account', 1);
add_option('acc_credit_note_deposit_to', 13);
add_option('acc_credit_note_refund_payment_account', 1);
add_option('acc_credit_note_refund_deposit_to', 13);
add_option('acc_expense_payment_account', 13);
add_option('acc_expense_deposit_to', 37);
add_option('acc_tax_payment_account', 29);
add_option('acc_tax_deposit_to', 1);
add_option('acc_expense_tax_payment_account', 13);
add_option('acc_expense_tax_deposit_to', 29);

add_option('acc_active_payment_mode_mapping', 1);
add_option('acc_active_expense_category_mapping', 1);

if (!$CI->db->table_exists(db_prefix() . 'acc_accounts')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_accounts` (
  	  `id` INT(11) NOT NULL AUTO_INCREMENT,
	  `name` VARCHAR(255) NOT NULL,
    `key_name` VARCHAR(255) NULL,
	  `number` VARCHAR(45) NULL,
	  `parent_account` INT(11) NULL,
	  `account_type_id` INT(11) NOT NULL,
	  `account_detail_type_id` INT(11) NOT NULL,
	  `balance` DECIMAL(15,2) NULL,
	  `balance_as_of` DATE NULL,
	  `description` TEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_account_history')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_account_history` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `account` INT(11) NOT NULL,
      `debit` DECIMAL(15,2) NOT NULL DEFAULT 0,
      `credit` DECIMAL(15,2) NOT NULL DEFAULT 0,
      `description` TEXT NULL,
      `rel_id` INT(11) NULL,
      `rel_type` VARCHAR(45) NULL,
      `datecreated` DATETIME NULL,
      `addedfrom` INT(11) NULL,
      `customer` INT(11) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_transfers')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_transfers` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `transfer_funds_from` INT(11) NOT NULL,
      `transfer_funds_to` INT(11) NOT NULL,
      `transfer_amount` DECIMAL(15,2) NULL,
      `date` VARCHAR(45) NULL,
      `description` TEXT NULL,
      `datecreated` DATETIME NULL,
      `addedfrom` INT(11) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_journal_entries')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_journal_entries` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `number` VARCHAR(45) NULL,
      `description` TEXT NULL,
      `journal_date` DATE NULL,
      `amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
      `datecreated` DATETIME NULL,
      `addedfrom` INT(11) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_transaction_bankings')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_transaction_bankings` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `date` DATE NOT NULL,
      `withdrawals` DECIMAL(15,2) NOT NULL DEFAULT 0,
      `deposits` DECIMAL(15,2) NOT NULL DEFAULT 0,
      `payee` VARCHAR(255) NULL,
      `description` TEXT NULL,
      `datecreated` DATETIME NULL,
      `addedfrom` INT(11) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_reconciles')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_reconciles` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `account` INT(11) NOT NULL,
      `beginning_balance` DECIMAL(15,2) NOT NULL,
      `ending_balance` DECIMAL(15,2) NOT NULL,
      `ending_date` DATE NOT NULL,
      `expense_date` DATE NULL,
      `service_charge` DECIMAL(15,2) NULL,
      `expense_account` INT(11) NULL,
      `income_date` DATE NULL,
      `interest_earned` DECIMAL(15,2) NULL,
      `income_account` INT(11) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('reconcile' ,db_prefix() . 'acc_account_history')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
    ADD COLUMN `reconcile` INT(11) NOT NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('finish' ,db_prefix() . 'acc_reconciles')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_reconciles`
    ADD COLUMN `finish` INT(11) NOT NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('split' ,db_prefix() . 'acc_account_history')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
    ADD COLUMN `split` INT(11) NOT NULL DEFAULT 0;');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_banking_rules')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_banking_rules` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(255) NOT NULL,
      `transaction` VARCHAR(45) NULL,
      `following` VARCHAR(45) NULL,
      `then` VARCHAR(45) NULL,
      `payment_account` INT(11) NULL,
      `deposit_to` INT(11) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_banking_rule_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_banking_rule_details` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `rule_id` INT(11) NOT NULL,
      `type` VARCHAR(45) NULL,
      `subtype` VARCHAR(45) NULL,
      `text` VARCHAR(255) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('auto_add' ,db_prefix() . 'acc_banking_rules')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_banking_rules`
    ADD COLUMN `auto_add` INT(11) NOT NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('subtype_amount' ,db_prefix() . 'acc_banking_rule_details')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_banking_rule_details`
    ADD COLUMN `subtype_amount` VARCHAR(45) NULL;');
}

if (!$CI->db->field_exists('default_account' ,db_prefix() . 'acc_accounts')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_accounts`
    ADD COLUMN `default_account` INT(11) NOT NULL DEFAULT 0,
    ADD COLUMN `active` INT(11) NOT NULL DEFAULT 1;');
}

if (!$CI->db->field_exists('item' ,db_prefix() . 'acc_account_history')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
    ADD COLUMN `item` INT(11) NULL,
    ADD COLUMN `paid` INT(1) NOT NULL DEFAULT 0;');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_item_automatics')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_item_automatics` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `item_id` INT(11) NOT NULL,
      `inventory_asset_account` INT(11) NOT NULL DEFAULT 0,
      `income_account` INT(11) NOT NULL DEFAULT 0,
      `expense_account` INT(11) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_tax_mappings')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_tax_mappings` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `tax_id` INT(11) NOT NULL,
      `payment_account` INT(11) NOT NULL DEFAULT 0,
      `deposit_to` INT(11) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('date' ,db_prefix() . 'acc_account_history')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
    ADD COLUMN `date` DATE NULL;');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_expense_category_mappings')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_expense_category_mappings` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `category_id` INT(11) NOT NULL,
      `payment_account` INT(11) NOT NULL DEFAULT 0,
      `deposit_to` INT(11) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('tax' ,db_prefix() . 'acc_account_history')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
    ADD COLUMN `tax` INT(11) NULL;');
}


if (!$CI->db->field_exists('expense_payment_account' ,db_prefix() . 'acc_tax_mappings')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_tax_mappings`
    ADD COLUMN `expense_payment_account` INT(11) NOT NULL DEFAULT \'0\',
    ADD COLUMN `expense_deposit_to` INT(11) NOT NULL DEFAULT \'0\';');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_payment_mode_mappings')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_payment_mode_mappings` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `payment_mode_id` INT(11) NOT NULL,
      `payment_account` INT(11) NOT NULL DEFAULT 0,
      `deposit_to` INT(11) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

add_option('acc_payment_expense_automatic_conversion', 1);
add_option('acc_payment_sale_automatic_conversion', 1);
add_option('acc_expense_payment_payment_account', 1);
add_option('acc_expense_payment_deposit_to', 1);

if (!$CI->db->table_exists(db_prefix() . 'acc_account_type_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_account_type_details` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `account_type_id` INT(11) NOT NULL,
      `name` VARCHAR(255) NOT NULL,
      `note` TEXT NULL,
      `statement_of_cash_flows` VARCHAR(255) NULL,
      PRIMARY KEY (`id`)
    ) AUTO_INCREMENT=200, ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('preferred_payment_method' ,db_prefix() . 'acc_expense_category_mappings')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_expense_category_mappings`
    ADD COLUMN `preferred_payment_method` INT(11) NOT NULL DEFAULT \'0\';');
}

if (!$CI->db->field_exists('expense_payment_account' ,db_prefix() . 'acc_payment_mode_mappings')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_payment_mode_mappings`
    ADD COLUMN `expense_payment_account` INT(11) NOT NULL DEFAULT \'0\',
    ADD COLUMN `expense_deposit_to` INT(11) NOT NULL DEFAULT \'0\';');
}

if (get_option('acc_expense_deposit_to') == 37){
  update_option('acc_expense_deposit_to', 80);
}

if (!$CI->db->field_exists('payslip_type' ,db_prefix() . 'acc_account_history')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
    ADD COLUMN `payslip_type` VARCHAR(45) NULL;');
}

if (!acc_account_exists('acc_opening_balance_equity')) {
  $CI->db->query("INSERT INTO `". db_prefix() ."acc_accounts` (`name`, `key_name`, `account_type_id`, `account_detail_type_id`, `default_account`, `active`) VALUES ('', 'acc_opening_balance_equity', '10', '71', '1', '1');");
}

add_option('acc_pl_total_insurance_automatic_conversion', 1);
add_option('acc_pl_total_insurance_payment_account', 13);
add_option('acc_pl_total_insurance_deposit_to', 32);

add_option('acc_pl_tax_paye_automatic_conversion', 1);
add_option('acc_pl_tax_paye_payment_account', 13);
add_option('acc_pl_tax_paye_deposit_to', 28);

add_option('acc_pl_net_pay_automatic_conversion', 1);
add_option('acc_pl_net_pay_payment_account', 13);
add_option('acc_pl_net_pay_deposit_to', 56);

add_option('acc_wh_stock_import_automatic_conversion', 1);
add_option('acc_wh_stock_import_payment_account', 87);
add_option('acc_wh_stock_import_deposit_to', 37);

add_option('acc_wh_stock_export_automatic_conversion', 1);
add_option('acc_wh_stock_export_payment_account', 37);
add_option('acc_wh_stock_export_deposit_to', 1);

add_option('acc_wh_loss_adjustment_automatic_conversion', 1);
add_option('acc_wh_decrease_payment_account', 37);
add_option('acc_wh_decrease_deposit_to', 1);

add_option('acc_wh_increase_payment_account', 87);
add_option('acc_wh_increase_deposit_to', 37);

add_option('acc_wh_opening_stock_automatic_conversion', 1);

if (acc_account_exists('acc_opening_balance_equity')) {
    add_option('acc_wh_opening_stock_payment_account', acc_account_exists('acc_opening_balance_equity'));
}
add_option('acc_wh_opening_stock_deposit_to', 37);

add_option('acc_pur_order_automatic_conversion', 1);
add_option('acc_pur_order_payment_account', 13);
add_option('acc_pur_order_deposit_to', 80);

add_option('acc_pur_payment_automatic_conversion', 1);
add_option('acc_pur_payment_payment_account', 16);
add_option('acc_pur_payment_deposit_to', 37);

//Version 1.0.8

if (!$CI->db->table_exists(db_prefix() . 'acc_budgets')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_budgets` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `year` INT(11) NOT NULL,
      `name` VARCHAR(200) NULL,
      `type` VARCHAR(45) NULL,
      `data_source` VARCHAR(45) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_budget_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_budget_details` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `budget_id` INT(11) NOT NULL,
      `month` INT(11) NOT NULL,
      `year` INT(11) NOT NULL,
      `account` INT(11) NULL,
      `amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('vendor' ,db_prefix() . 'acc_account_history')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
    ADD COLUMN `vendor` INT(11) NULL;');
}

if (!$CI->db->field_exists('itemable_id' ,db_prefix() . 'acc_account_history')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
    ADD COLUMN `itemable_id` INT(11) NULL;');
}


//-------------------------

if (!$CI->db->field_exists('cleared' ,db_prefix() . 'acc_account_history')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
    ADD COLUMN `cleared` INT(11) NOT NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('access_token' ,db_prefix() . 'acc_accounts')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_accounts`
    ADD COLUMN `access_token` TEXT NULL,
    ADD COLUMN `account_id` VARCHAR(255) NULL,
    ADD COLUMN `plaid_status` TINYINT(5) NOT NULL DEFAULT 0 COMMENT "1=>verified, 0=>not verified",
    ADD COLUMN `plaid_account_name` VARCHAR(255) NULL;');
}

if (!$CI->db->field_exists('transaction_id' ,db_prefix() . 'acc_transaction_bankings')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_transaction_bankings`
    ADD COLUMN `transaction_id` varchar(150) NULL,
    ADD COLUMN `bank_id` INT(11) NULL,
    ADD COLUMN `status` TINYINT(5) NOT NULL DEFAULT 0 COMMENT "1=>posted, 2=>pending";');
}

if (!$CI->db->field_exists('matched' ,db_prefix() . 'acc_transaction_bankings')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_transaction_bankings`
    ADD COLUMN `matched` INT(11) NOT NULL DEFAULT 0;');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_plaid_transaction_logs')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_plaid_transaction_logs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `bank_id` int(11) DEFAULT NULL,
        `last_updated` date DEFAULT NULL,
        `transaction_count` int(11) DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `addedFrom` int(11) DEFAULT NULL,
        `company` int(11) DEFAULT NULL,
        `status` int(11) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('opening_balance' ,db_prefix() . 'acc_reconciles')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_reconciles`
    ADD COLUMN `opening_balance` INT(11) NOT NULL DEFAULT 0;');
}


if (!$CI->db->field_exists('debits_for_period' ,db_prefix() . 'acc_reconciles')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_reconciles`
  ADD COLUMN `debits_for_period` DECIMAL(15,2) NULL');
}

if (!$CI->db->field_exists('credits_for_period' ,db_prefix() . 'acc_reconciles')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_reconciles`
  ADD COLUMN `credits_for_period`  DECIMAL(15,2) NULL');
}

if (!$CI->db->field_exists('dateadded' ,db_prefix() . 'acc_reconciles')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_reconciles`
    ADD COLUMN `dateadded` DATETIME NULL,
    ADD COLUMN `addedfrom` INT(11) NULL
    ');
}

if (!$CI->db->field_exists('reconcile' ,db_prefix() . 'acc_transaction_bankings')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_transaction_bankings`
    ADD COLUMN `reconcile` INT(11) NOT NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('adjusted' ,db_prefix() . 'acc_transaction_bankings')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_transaction_bankings`
    ADD COLUMN `adjusted` INT(11) NOT NULL DEFAULT 0;');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_matched_transactions')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'acc_matched_transactions` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `account_history_id` INT(11) NULL,
        `history_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
        `rel_id` INT(11) NULL,
        `rel_type` VARCHAR(255) NULL,
        `amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
        `company` int(11) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('reconcile' ,db_prefix() . 'acc_matched_transactions')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_matched_transactions`
    ADD COLUMN `reconcile` INT(11) NOT NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('sub_type' ,db_prefix() . 'acc_account_history')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
    ADD COLUMN `sub_type` VARCHAR(45) NULL;');
}

add_option('acc_mrp_manufacturing_order_automatic_conversion', 1);
add_option('acc_mrp_material_cost_payment_account', 13);
add_option('acc_mrp_material_cost_deposit_to', 45);
add_option('acc_mrp_labour_cost_payment_account', 13);
add_option('acc_mrp_labour_cost_deposit_to', 18);

add_option('acc_pur_order_return_automatic_conversion', 1);
add_option('acc_pur_order_return_payment_account', 80);
add_option('acc_pur_order_return_deposit_to', 13);

add_option('acc_pur_refund_automatic_conversion', 1);
add_option('acc_pur_refund_payment_account', 37);
add_option('acc_pur_refund_deposit_to', 16);

add_option('acc_pur_invoice_automatic_conversion', 1);
add_option('acc_pur_invoice_payment_account', 13);
add_option('acc_pur_invoice_deposit_to', 80);


add_option('acc_omni_sales_order_return_automatic_conversion', 1);
add_option('acc_omni_sales_order_return_payment_account', 1);
add_option('acc_omni_sales_order_return_deposit_to', 66);

add_option('acc_omni_sales_refund_automatic_conversion', 1);
add_option('acc_omni_sales_refund_payment_account', 13);
add_option('acc_omni_sales_refund_deposit_to', 1);


if (!$CI->db->table_exists(db_prefix() . 'acc_checks')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_checks` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `rel_id` INT(11) NULL,
      `rel_type` VARCHAR(25) NULL,
      `amount` DECIMAL(15,2) NULL,
      `date` DATE NULL,
      `memo` VARCHAR(255) NULL,
      `dateadded` datetime NULL,
      `addedfrom` INT(11) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('address' ,db_prefix() . 'acc_checks')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_checks`
  ADD COLUMN `address` TEXT NULL');
}


if (!$CI->db->field_exists('bank_account' ,db_prefix() . 'acc_checks')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_checks`
  ADD COLUMN `bank_account` INT(11) NOT NULL');
}

if (!$CI->db->field_exists('number' ,db_prefix() . 'acc_checks')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_checks`
  ADD COLUMN `number` INT(11) NOT NULL');
}


if (!$CI->db->field_exists('signed' ,db_prefix() . 'acc_checks')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_checks`
  ADD COLUMN `signed` INT(11) NOT NULL DEFAULT 0');
}


if (!$CI->db->field_exists('include_company_name_address' ,db_prefix() . 'acc_checks')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_checks`
    ADD COLUMN `include_company_name_address` INT(11) NOT NULL DEFAULT 1,
    ADD COLUMN `include_routing_account_numbers` INT(11) NOT NULL DEFAULT 1');
}



if (!$CI->db->field_exists('bill' ,db_prefix() . 'acc_checks')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_checks`
    ADD COLUMN `bill` INT(11) NULL'
    );
}


if (!$CI->db->field_exists('city' ,db_prefix() . 'acc_checks')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_checks`
    ADD COLUMN `city` varchar(100) NULL,
    ADD COLUMN `zip` varchar(15) NULL,
    ADD COLUMN `state` varchar(50) NULL'
    );
}


if (!$CI->db->field_exists('issue' ,db_prefix() . 'acc_checks')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_checks`
    ADD COLUMN `issue` INT(11) NULL'
    );
}


if (!$CI->db->field_exists('include_check_number' ,db_prefix() . 'acc_checks')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_checks`
    ADD COLUMN `include_check_number` INT(11) NOT NULL DEFAULT 1,
    ADD COLUMN `include_bank_name` INT(11) NOT NULL DEFAULT 1,
    ADD COLUMN `bank_name` varchar(255) NULL,
    ADD COLUMN `address_line_1` text NULL,
    ADD COLUMN `address_line_2` text NULL');
}


if (!$CI->db->field_exists('vendor_city' ,db_prefix() . 'acc_checks')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_checks`
    ADD COLUMN `vendor_city` varchar(100) NULL,
    ADD COLUMN `vendor_zip` varchar(15) NULL,
    ADD COLUMN `vendor_state` varchar(50) NULL,
    ADD COLUMN `vendor_address` TEXT NULL;'
    );
}

if (!$CI->db->field_exists('reason_for_void' ,db_prefix() . 'acc_checks')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_checks`
    ADD COLUMN `reason_for_void` TEXT NULL'
    );
}


if (!$CI->db->field_exists('bill_items' ,db_prefix() . 'acc_checks')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_checks`
  ADD COLUMN `bill_items` VARCHAR(255) NULL');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_check_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_check_details` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `check_id` INT(11) NULL,
      `bill` INT(11) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->table_exists(db_prefix() . 'acc_pay_bills')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_pay_bills` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `expense` int(11) NULL,
      `amount` DECIMAL(15,2) NULL,
      `reference_no` varchar(100) NULL,
      `date` date NULL,
      `dateadded` datetime NULL,
      `addedfrom` INT(11) NULL,
      `company` INT(11) NULL,
      `account_debit` INT(11) NULL,
      `account_credit` INT(11) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('bill' ,db_prefix() . 'acc_pay_bills')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_pay_bills`
  ADD COLUMN `bill` INT(11) NOT NULL DEFAULT 0');
}

if (!$CI->db->field_exists('vendor' ,db_prefix() . 'acc_pay_bills')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_pay_bills`
  ADD COLUMN `vendor` INT(11) NOT NULL DEFAULT 0');
}


if (!$CI->db->field_exists('pay_number' ,db_prefix() . 'acc_pay_bills')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_pay_bills`
    ADD COLUMN `pay_number` INT(11) NULL'
    );
}

if (!$CI->db->field_exists('payment_method' ,db_prefix() . 'acc_pay_bills')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_pay_bills`
    ADD COLUMN `payment_method` VARCHAR(255) NULL;');
}

if (!$CI->db->field_exists('bill_items' ,db_prefix() . 'acc_pay_bills')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_pay_bills`
  ADD COLUMN `bill_items` VARCHAR(255) NULL');
}


if (!$CI->db->table_exists(db_prefix() . 'pur_vendor')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_vendor` (
      `userid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `company` varchar(200) NULL,
      `vat` varchar(200) NULL,
      `phonenumber` varchar(30) NULL,
      `country` int(11) NOT NULL DEFAULT '0',
      `city` varchar(100) NULL,
      `zip` varchar(15) NULL,
      `state` varchar(50) NULL,
      `address` varchar(100) NULL,
      `website` varchar(150) NULL,
      `datecreated` DATETIME NOT NULL,
      `active` INT(11) NOT NULL DEFAULT '1',
      `leadid` INT(11) NULL,
      `billing_street` varchar(200) NULL,
      `billing_city` varchar(100) NULL,
      `billing_state` varchar(100) NULL,
      `billing_zip` varchar(100) NULL,
      `billing_country` int(11) NULL DEFAULT '0',
      `shipping_street` varchar(200) NULL,
      `shipping_city` varchar(100) NULL,
      `shipping_state` varchar(100) NULL,
      `shipping_zip` varchar(100) NULL,
      `shipping_country` int(11) NULL DEFAULT '0',
      `longitude` varchar(191) NULL,
      `latitude` varchar(191) NULL,
      `default_language` varchar(40) NULL,
      `default_currency` INT(11) NOT NULL DEFAULT '0',
      `show_primary_contact` INT(11) NOT NULL DEFAULT '0',
      `stripe_id` varchar(40) NULL,
      `registration_confirmed` INT(11) NOT NULL DEFAULT '1',
      `addedfrom` INT(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`userid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('category' ,db_prefix() . 'pur_vendor')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
      ADD COLUMN `category` TEXT  NULL
  ;");
}

if (!$CI->db->field_exists('bank_detail' ,db_prefix() . 'pur_vendor')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
      ADD COLUMN `bank_detail` TEXT  NULL
  ;");
}

if (!$CI->db->field_exists('payment_terms' ,db_prefix() . 'pur_vendor')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
      ADD COLUMN `payment_terms` TEXT  NULL
  ;");
}

if (!$CI->db->field_exists('vendor_code' ,db_prefix() . 'pur_vendor')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
      ADD COLUMN `vendor_code` VARCHAR(100)  NULL
  ;");
}

if ($CI->db->field_exists('address' ,db_prefix() . 'pur_vendor')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
      CHANGE COLUMN `address` `address` TEXT NULL DEFAULT NULL
  ;");
}

if (!$CI->db->field_exists('return_within_day' ,db_prefix() . 'pur_vendor')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_vendor`
  ADD COLUMN `return_within_day` INT(11) NULL
  ');
}

if (!$CI->db->field_exists('return_order_fee' ,db_prefix() . 'pur_vendor')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_vendor`
  ADD COLUMN `return_order_fee` DECIMAL(15,2) NULL
  ');
}

if (!$CI->db->field_exists('return_policies' ,db_prefix() . 'pur_vendor')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_vendor`
  ADD COLUMN `return_policies` TEXT NULL
  ');
}

if (!$CI->db->field_exists('vendor', db_prefix() .'expenses')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'expenses` 
  ADD COLUMN `vendor` INT(11) NULL;');            
}


if (!$CI->db->field_exists('due_date' ,db_prefix() . 'expenses')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'expenses`
    ADD COLUMN `due_date` DATE NULL;');
}

if (!$CI->db->field_exists('date_paid' ,db_prefix() . 'expenses')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'expenses`
    ADD COLUMN `date_paid` DATE NULL'
    );
}

if (!$CI->db->field_exists('status' ,db_prefix() . 'expenses')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'expenses`
    ADD COLUMN `status` INT(11) NULL;');
}

if (!$CI->db->field_exists('is_bill' ,db_prefix() . 'expenses')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'expenses`
    ADD COLUMN `is_bill` INT(11) NOT NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('reason_for_void' ,db_prefix() . 'expenses')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'expenses`
    ADD COLUMN `reason_for_void` TEXT NULL'
    );
}

if (!$CI->db->field_exists('approved' ,db_prefix() . 'expenses')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'expenses`
  ADD COLUMN `voided` INT(11) NOT NULL DEFAULT 0,
  ADD COLUMN `approved` INT(11) NOT NULL DEFAULT 0');
}


if (!$CI->db->table_exists(db_prefix() . 'acc_pay_bill_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_pay_bill_details` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `pay_bill` INT(11) NULL,
      `bill_id` INT(11) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_bill_mappings')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_bill_mappings` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `bill_id` INT(11) NULL,
      `type` VARCHAR(25) NULL,
      `account` INT(11) NULL,
      `amount` DECIMAL(15,2) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->table_exists(db_prefix() . 'acc_pay_bill_item_paid')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'acc_pay_bill_item_paid` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `pay_bill_id` int(11) NOT NULL DEFAULT 0,
        `item_id` INT(11) NULL,
        `item_name` VARCHAR(255) NULL,
        `item_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
        `amount_paid` DECIMAL(15,2) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('check_id' ,db_prefix() . 'acc_pay_bill_item_paid')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_pay_bill_item_paid`
  ADD COLUMN `check_id` INT(11) NOT NULL DEFAULT 0;');
}


if (!$CI->db->field_exists('bill_item' ,db_prefix() . 'acc_account_history')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
    ADD COLUMN `bill_item` INT(11) NOT NULL DEFAULT 0;');
}


add_option('acc_routing_number_icon_a', 'a');
add_option('acc_routing_number_icon_b', 'a');

add_option('acc_bank_account_icon_a', 'a');
add_option('acc_bank_account_icon_b', 'a');

add_option('acc_current_check_no_icon_a', 'a');
add_option('acc_current_check_no_icon_b', 'a');

add_option('acc_check_type', 'type_1');


if (!$CI->db->field_exists('bank_account' ,db_prefix() . 'acc_accounts')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_accounts`
  ADD COLUMN `bank_account` TEXT NULL');
}

if (!$CI->db->field_exists('bank_routing' ,db_prefix() . 'acc_accounts')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_accounts`
  ADD COLUMN `bank_routing` TEXT NULL');
}

if (!$CI->db->field_exists('address_line_1' ,db_prefix() . 'acc_accounts')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_accounts`
  ADD COLUMN `address_line_1` TEXT NULL');
}


if (!$CI->db->field_exists('address_line_2' ,db_prefix() . 'acc_accounts')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_accounts`
  ADD COLUMN `address_line_2` TEXT NULL');
}


if (!$CI->db->field_exists('bank_name' ,db_prefix() . 'acc_accounts')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_accounts`
    ADD COLUMN `bank_name` varchar(255) NULL;');
}

if (!$CI->db->field_exists('number' ,db_prefix() . 'acc_account_history')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
    ADD COLUMN `number` VARCHAR(100) NULL;');         
}


if (!$CI->db->table_exists(db_prefix() . 'acc_print_later')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_print_later` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `rel_id` INT(11) NULL,
      `rel_type` VARCHAR(45) NULL,
      `account` INT(11) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->table_exists(db_prefix() . 'acc_checks_printed')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_checks_printed` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `check_id` INT(11) NULL,
      `bank_account` INT(11) NULL,
      `first_check_number` INT(11) NULL,
      `printed_at` DATETIME NULL,
      `printed_by` INT(11) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->field_exists('issue' ,db_prefix() . 'acc_account_history')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
    ADD COLUMN `issue` INT(11) NOT NULL DEFAULT 0;'
    );
}

add_option('acc_fe_asset_automatic_conversion', 1);
add_option('acc_fe_asset_payment_account', 13);
add_option('acc_fe_asset_deposit_to', 1);

add_option('acc_fe_license_automatic_conversion', 1);
add_option('acc_fe_license_payment_account', 13);
add_option('acc_fe_license_deposit_to', 1);

add_option('acc_fe_consumable_automatic_conversion', 1);
add_option('acc_fe_consumable_payment_account', 13);
add_option('acc_fe_consumable_deposit_to', 1);

add_option('acc_fe_component_automatic_conversion', 1);
add_option('acc_fe_component_payment_account', 13);
add_option('acc_fe_component_deposit_to', 1);

add_option('acc_fe_maintenance_automatic_conversion', 1);
add_option('acc_fe_maintenance_payment_account', 13);
add_option('acc_fe_maintenance_deposit_to', 1);

add_option('acc_fe_depreciation_automatic_conversion', 1);
add_option('acc_fe_depreciation_payment_account', 13);
add_option('acc_fe_depreciation_deposit_to', 1);

if (!$CI->db->field_exists('credit_note_refund_payment_account' ,db_prefix() . 'acc_payment_mode_mappings')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_payment_mode_mappings`
    ADD COLUMN `credit_note_refund_payment_account` INT(11) NOT NULL DEFAULT \'0\',
    ADD COLUMN `credit_note_refund_deposit_to` INT(11) NOT NULL DEFAULT \'0\';');
}

add_option('acc_wh_stock_export_profit_payment_account', 66);
add_option('acc_wh_stock_export_profit_deposit_to', 1);

if (!$CI->db->table_exists(db_prefix() . 'acc_expense_category_mapping_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_expense_category_mapping_details` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `category_mapping_id` INT(11) NOT NULL,
      `payment_mode_id` INT(11) NOT NULL,
      `payment_account` INT(11) NOT NULL DEFAULT 0,
      `deposit_to` INT(11) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('added_from_reconcile' ,db_prefix() . 'acc_account_history')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
  ADD COLUMN `added_from_reconcile` INT(11) NOT NULL DEFAULT 0');
}

if (!$CI->db->field_exists('bank_reconcile' ,db_prefix() . 'acc_account_history')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
  ADD COLUMN `bank_reconcile` INT(11) NOT NULL DEFAULT 0');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_bank_reconciles')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_bank_reconciles` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `account` INT(11) NOT NULL,
      `opening_balance` DECIMAL(15,2) NOT NULL DEFAULT 0,
      `beginning_balance` DECIMAL(15,2) NOT NULL,
      `ending_balance` DECIMAL(15,2) NOT NULL,
      `ending_date` DATE NOT NULL,
      `finish` INT(11) NOT NULL DEFAULT 0,
      `debits_for_period` DECIMAL(15,2) NOT NULL,
      `credits_for_period` DECIMAL(15,2) NOT NULL,
      `dateadded` DATETIME NULL,
      `addedfrom` INT(11) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('is_imported' ,db_prefix() . 'acc_transaction_bankings')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_transaction_bankings`
  ADD COLUMN `is_imported` INT(11) NOT NULL DEFAULT 0');
}

add_option('update_bank_account_v124', 0);
if (get_option('update_bank_account_v124') == 0) {

    $CI->load->model('accounting/accounting_model');
    $CI->accounting_model->update_bank_account_v124();

    update_option('update_bank_account_v124', 1);
}

if (!$CI->db->table_exists(db_prefix() . 'acc_income_statement_modifications')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_income_statement_modifications` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `account` INT(11) NULL,
      `active` INT(11) NOT NULL DEFAULT 1,
      `account_type` INT(11) NULL,
      `options` TEXT,
      `dateadded` DATETIME NULL,
      `addedfrom` INT(11) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

add_option('update_income_statement_modifications_v125', 0);
if (get_option('update_income_statement_modifications_v125') == 0) {

    $CI->load->model('accounting/accounting_model');
    $CI->accounting_model->reset_income_statement_modifications();

    update_option('update_income_statement_modifications_v125', 1);
}


if (!$CI->db->field_exists('type' ,db_prefix() . 'acc_income_statement_modifications')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_income_statement_modifications`
  ADD COLUMN `type` TEXT NULL');
}

add_option('acc_enable_income_statement_modifications', 0);

if (!$CI->db->field_exists('balance' ,db_prefix() . 'clients')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'clients`
  ADD COLUMN `balance` DECIMAL(15,2) NULL,
  ADD COLUMN `balance_as_of` DATE NULL');
}

if (!$CI->db->field_exists('balance' ,db_prefix() . 'pur_vendor')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_vendor`
  ADD COLUMN `balance` DECIMAL(15,2) NULL,
  ADD COLUMN `balance_as_of` DATE NULL');
}

add_option('acc_invoice_discount_payment_account', 1);
add_option('acc_invoice_discount_deposit_to', 19);


// 1.2.8
if (!$CI->db->field_exists('mapping_type' ,db_prefix() . 'acc_banking_rules')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_banking_rules`
  ADD COLUMN `mapping_type` VARCHAR(25) NULL,
  ADD COLUMN `account` INT(11) NULL,
  ADD COLUMN `split_percentage` TEXT NULL,
  ADD COLUMN `split_amount` TEXT NULL'
  );
}

if (!$CI->db->field_exists('banking_rule' ,db_prefix() . 'acc_transaction_bankings')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_transaction_bankings`
  ADD COLUMN `banking_rule` INT(11) NOT NULL DEFAULT 0'
  );
}

add_option('acc_pur_tax_automatic_conversion', 1);
add_option('acc_pur_tax_payment_account', 13);
add_option('acc_pur_tax_deposit_to', 29);

if (!$CI->db->field_exists('purchase_payment_account' ,db_prefix() . 'acc_tax_mappings')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_tax_mappings`
    ADD COLUMN `purchase_payment_account` INT(11) NOT NULL DEFAULT \'0\',
    ADD COLUMN `purchase_deposit_to` INT(11) NOT NULL DEFAULT \'0\';');
}

add_option('acc_wh_stock_import_return_automatic_conversion', 0);
add_option('acc_wh_stock_import_return_payment_account', 1);
add_option('acc_wh_stock_import_return_deposit_to', 37);
add_option('acc_wh_stock_export_profit_automatic_conversion', 1);

if (!$CI->db->field_exists('currency_rate' ,db_prefix() . 'acc_account_history')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
    ADD COLUMN `currency_rate` DECIMAL(15,6) NULL;');
}

if (!$CI->db->table_exists(db_prefix() . 'currency_rates')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "currency_rates` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `from_currency_id` int(11) NULL,
    `from_currency_name` VARCHAR(100) NULL,
    `from_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
    `to_currency_id` int(11) NULL,
    `to_currency_name` VARCHAR(100) NULL,
    `to_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
    `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'currency_rate_logs')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "currency_rate_logs` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `from_currency_id` int(11) NULL,
    `from_currency_name` VARCHAR(100) NULL,
    `from_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
    `to_currency_id` int(11) NULL,
    `to_currency_name` VARCHAR(100) NULL,
    `to_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
    `date` DATE NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


add_option('cr_date_cronjob_currency_rates', '');
add_option('cr_automatically_get_currency_rate', 1);
add_option('cr_global_amount_expiration', 0);

if (!$CI->db->field_exists('reference' ,db_prefix() . 'acc_journal_entries')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_journal_entries`
    ADD COLUMN `reference` varchar(100) NULL;');
}

if (!$CI->db->field_exists('recurring' ,db_prefix() . 'acc_journal_entries')) {
  $CI->db->query("ALTER TABLE `" . db_prefix() . "acc_journal_entries`
    ADD COLUMN `recurring` int(11) NOT NULL DEFAULT '0',
    ADD COLUMN `recurring_type` varchar(10) DEFAULT NULL,
    ADD COLUMN `custom_recurring` tinyint(1) NOT NULL DEFAULT '0',
    ADD COLUMN `cycles` int(11) NOT NULL DEFAULT '0',
    ADD COLUMN `total_cycles` int(11) NOT NULL DEFAULT '0',
    ADD COLUMN `is_recurring_from` int(11) DEFAULT NULL,
    ADD COLUMN `last_recurring_date` date DEFAULT NULL
    ");
}

if (!$CI->db->field_exists('item_id' ,db_prefix() . 'acc_bill_mappings')) {
  $CI->db->query("ALTER TABLE `" . db_prefix() . "acc_bill_mappings`
    ADD COLUMN `item_id` int(11) NOT NULL DEFAULT '0',
    ADD COLUMN `qty` decimal(15,2) NOT NULL DEFAULT '0',
    ADD COLUMN `cost` decimal(15,2) NOT NULL DEFAULT '0',
    ADD COLUMN `description` varchar(255) NULL
    ");
}

