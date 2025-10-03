<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_102 extends App_module_migration
{
     public function up()
     {
        $CI = &get_instance();
        
        
        //Version 1.0.2
        add_option('acc_active_payment_mode_mapping', 1);
        add_option('acc_active_expense_category_mapping', 1);

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
     }
}
