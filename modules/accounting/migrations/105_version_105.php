<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_105 extends App_module_migration
{
     public function up()
     {
        $CI = &get_instance();
        
        //Version 1.0.5
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
     }
}
