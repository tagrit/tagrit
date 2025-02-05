<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_120 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        //Ver 1.2.0
        if ($CI->db->field_exists('unit_price' ,db_prefix() . 'pur_estimate_detail')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
            CHANGE COLUMN `unit_price` `unit_price` DECIMAL(15,2) NULL DEFAULT NULL
          ;");
        }
    }
}