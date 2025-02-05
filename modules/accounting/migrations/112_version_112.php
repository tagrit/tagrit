<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_112 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        //Version 1.1.2

        
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

        
    }
}
