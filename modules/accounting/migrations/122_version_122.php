<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_122 extends App_module_migration
{
    public function up()
    {        
        
        $CI = &get_instance();
        
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
    }
}
