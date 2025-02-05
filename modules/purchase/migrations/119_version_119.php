<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_119 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        //Ver 1.1.9
        if ($CI->db->field_exists('address' ,db_prefix() . 'pur_vendor')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
		      CHANGE COLUMN `address` `address` TEXT NULL DEFAULT NULL
		  ;");
		}
    }
}