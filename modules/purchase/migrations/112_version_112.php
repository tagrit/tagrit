<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_112 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        //Ver 1.1.2
		if ($CI->db->field_exists('quantity' ,db_prefix() . 'pur_order_detail')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
		      CHANGE COLUMN `quantity` `quantity` DECIMAL(15,2) NOT NULL 
		  ;");
		}

		if ($CI->db->field_exists('quantity' ,db_prefix() . 'pur_estimate_detail')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
		       CHANGE COLUMN `quantity` `quantity` DECIMAL(15,2) NOT NULL 
		  ;");
		}

		if ($CI->db->field_exists('quantity' ,db_prefix() . 'pur_request_detail')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
		       CHANGE COLUMN `quantity` `quantity` DECIMAL(15,2) NOT NULL 
		  ;");
		}
    }
}