<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_126 extends App_module_migration
{
    public function up()
    {        
        $CI = &get_instance();
                
        if (!$CI->db->field_exists('balance' ,db_prefix() . 'clients')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'clients`
          ADD COLUMN `balance` DECIMAL(15,2) NULL,
          ADD COLUMN `balance_as_of` DATE NULL');
        }

        if (!$CI->db->field_exists('balance' ,db_prefix() . 'pur_vendor')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_vendor`
          ADD COLUMN `balance` DECIMAL(15,2) NULL,
          ADD COLUMN `balance_as_of` DATE NULL');
        }
    }
}
