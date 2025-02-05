<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_101 extends App_module_migration
{
     public function up()
     {
        $CI = &get_instance();
        
        
        //Version 1.0.1
        add_option('acc_invoice_automatic_conversion', 1);
        add_option('acc_payment_automatic_conversion', 1);
        add_option('acc_expense_automatic_conversion', 1);
        add_option('acc_tax_automatic_conversion', 1);
        add_option('acc_expense_tax_payment_account', 13);
        add_option('acc_expense_tax_deposit_to', 29);
        
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
     }
}
