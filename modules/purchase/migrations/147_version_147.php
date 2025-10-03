<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_147 extends App_module_migration
{
    public function up()
    {
    	$CI = &get_instance();

    	if (!$CI->db->field_exists('discount_type' ,db_prefix() . 'pur_invoices')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
		      ADD COLUMN `discount_type` TEXT NULL
		  ;");
		}

		if (!$CI->db->field_exists('discount_type' ,db_prefix() . 'wh_order_returns')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
		  ADD COLUMN `discount_type` TEXT NULL 
		  ;");
		}
    }

}