<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_134 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        //Version 1.3.34

        if (!$CI->db->field_exists('item_id' ,db_prefix() . 'acc_bill_mappings')) {
          $CI->db->query("ALTER TABLE `" . db_prefix() . "acc_bill_mappings`
            ADD COLUMN `item_id` int(11) NOT NULL DEFAULT '0',
            ADD COLUMN `qty` decimal(15,2) NOT NULL DEFAULT '0',
            ADD COLUMN `cost` decimal(15,2) NOT NULL DEFAULT '0',
            ADD COLUMN `description` varchar(255) NULL
            ");
        }
    }
}
