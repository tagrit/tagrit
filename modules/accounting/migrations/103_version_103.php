<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_103 extends App_module_migration
{
     public function up()
     {
        $CI = &get_instance();
        
        
        //Version 1.0.3
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
     }
}
