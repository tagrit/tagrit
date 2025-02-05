<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_127 extends App_module_migration
{
    public function up()
    {
       $CI = &get_instance();
       
       if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'pur_estimates')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimates`
		      ADD COLUMN `shipping_fee` decimal(15,2) NULL
		  ;");
		}

		if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		      ADD COLUMN `shipping_fee` decimal(15,2) NULL
		  ;");
		}

		if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'pur_invoices')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
		      ADD COLUMN `shipping_fee` decimal(15,2) NULL
		  ;");
		}
    }
}