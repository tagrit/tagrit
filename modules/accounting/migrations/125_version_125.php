<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_125 extends App_module_migration
{
    public function up()
    {        
        
        $CI = &get_instance();
                
        if (!$CI->db->table_exists(db_prefix() . 'acc_income_statement_modifications')) {
            $CI->db->query('CREATE TABLE ' . db_prefix() . "acc_income_statement_modifications (
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

        if (!$CI->db->field_exists('type' ,db_prefix() . 'acc_income_statement_modifications')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_income_statement_modifications`
          ADD COLUMN `type` TEXT NULL');
        }

        add_option('acc_enable_income_statement_modifications', 0);
        
        add_option('update_income_statement_modifications_v125', 0);
        if (get_option('update_income_statement_modifications_v125') == 0) {

            $CI->load->model('accounting/accounting_model');
            $CI->accounting_model->reset_income_statement_modifications();

            update_option('update_income_statement_modifications_v125', 1);
        }

    }
}
