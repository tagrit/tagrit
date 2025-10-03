<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_133 extends App_module_migration
{
    public function up()
    {
    	$CI = &get_instance();
       	add_option('pur_company_address', '', 1);
		add_option('pur_company_city', '', 1);
		add_option('pur_company_state', '', 1);
		add_option('pur_company_zipcode', '', 1);
		add_option('pur_company_country_code', '', 1);

		if (!$CI->db->field_exists('shipping_address' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		      ADD COLUMN `shipping_address` TEXT NULL
		  ;");
		}

		if (!$CI->db->field_exists('shipping_city' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		      ADD COLUMN `shipping_city` TEXT NULL
		  ;");
		}

		if (!$CI->db->field_exists('shipping_state' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		      ADD COLUMN `shipping_state` TEXT NULL
		  ;");
		}

		if (!$CI->db->field_exists('shipping_zip' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		      ADD COLUMN `shipping_zip` TEXT NULL
		  ;");
		}

		if (!$CI->db->field_exists('shipping_country' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		      ADD COLUMN `shipping_country` INT(11) NULL
		  ;");
		}
    }
}