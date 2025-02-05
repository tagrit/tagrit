<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_106 extends App_module_migration
{
     public function up()
     {
        $CI = &get_instance();
        
        //Version 1.0.6

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
        add_option('acc_wh_loss_payment_account', 37);
        add_option('acc_wh_loss_deposit_to', 1);

        add_option('acc_wh_adjustment_payment_account', 87);
        add_option('acc_wh_adjustment_deposit_to', 37);

        add_option('acc_wh_opening_stock_automatic_conversion', 1);
        if (acc_account_exists('acc_opening_balance_equity')) {
            add_option('acc_wh_opening_stock_payment_account', acc_account_exists('acc_opening_balance_equity'));
        }
        add_option('acc_wh_opening_stock_deposit_to', 37);
     }
}
