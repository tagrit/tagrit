<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_128 extends App_module_migration
{
    public function up()
    {        
        $CI = &get_instance();
        
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
    }
}
