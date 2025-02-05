<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_108 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        //Version 1.0.8

        if (!$CI->db->table_exists(db_prefix() . 'acc_budgets')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_budgets` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `year` INT(11) NOT NULL,
              `name` VARCHAR(200) NULL,
              `type` VARCHAR(45) NULL,
              `data_source` VARCHAR(45) NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }

        if (!$CI->db->table_exists(db_prefix() . 'acc_budget_details')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . "acc_budget_details` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `budget_id` INT(11) NOT NULL,
              `month` INT(11) NOT NULL,
              `year` INT(11) NOT NULL,
              `account` INT(11) NULL,
              `amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }
    }
}
