<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_131 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        //Version 1.3.1
        if (!$CI->db->field_exists('currency_rate' ,db_prefix() . 'acc_account_history')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
            ADD COLUMN `currency_rate` DECIMAL(15,6) NULL;');
        }

        if (!$CI->db->table_exists(db_prefix() . 'currency_rates')) {
          $CI->db->query('CREATE TABLE `' . db_prefix() . "currency_rates` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `from_currency_id` int(11) NULL,
            `from_currency_name` VARCHAR(100) NULL,
            `from_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
            `to_currency_id` int(11) NULL,
            `to_currency_name` VARCHAR(100) NULL,
            `to_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
            `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }

        if (!$CI->db->table_exists(db_prefix() . 'currency_rate_logs')) {
          $CI->db->query('CREATE TABLE `' . db_prefix() . "currency_rate_logs` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `from_currency_id` int(11) NULL,
            `from_currency_name` VARCHAR(100) NULL,
            `from_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
            `to_currency_id` int(11) NULL,
            `to_currency_name` VARCHAR(100) NULL,
            `to_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
            `date` DATE NULL,

            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }


        add_option('cr_date_cronjob_currency_rates', '');
        add_option('cr_automatically_get_currency_rate', 1);
        add_option('cr_global_amount_expiration', 0);
    }
}
