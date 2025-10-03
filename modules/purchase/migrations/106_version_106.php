<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_106 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        if (!$CI->db->field_exists('clients' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		    ADD COLUMN `clients` TEXT NULL
		  ;");
		}

		// version 1.0.6  purchase request fix
		if ($CI->db->field_exists('inventory_quantity' ,db_prefix() . 'pur_request_detail')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
		    CHANGE COLUMN `inventory_quantity` `inventory_quantity` INT(11) NULL DEFAULT '0'
		  ;");
		}
    }
}
