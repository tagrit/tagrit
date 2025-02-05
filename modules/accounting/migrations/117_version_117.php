<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_117 extends App_module_migration
{
    public function up()
    {        
        $CI = &get_instance();
        
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
  }
}
