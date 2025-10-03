<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_133 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        //Version 1.3.3

        if (!$CI->db->field_exists('recurring' ,db_prefix() . 'acc_journal_entries')) {
          $CI->db->query("ALTER TABLE `" . db_prefix() . "acc_journal_entries`
            ADD COLUMN `recurring` int(11) NOT NULL DEFAULT '0',
            ADD COLUMN `recurring_type` varchar(10) DEFAULT NULL,
            ADD COLUMN `custom_recurring` tinyint(1) NOT NULL DEFAULT '0',
            ADD COLUMN `cycles` int(11) NOT NULL DEFAULT '0',
            ADD COLUMN `total_cycles` int(11) NOT NULL DEFAULT '0',
            ADD COLUMN `is_recurring_from` int(11) DEFAULT NULL,
            ADD COLUMN `last_recurring_date` date DEFAULT NULL
            ");
        }
    }
}
