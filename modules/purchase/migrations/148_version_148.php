<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_148 extends App_module_migration
{
    public function up()
    {
    	$CI = &get_instance();

    	add_option('pur_company_country_text', '');

		if (!$CI->db->field_exists('shipping_country_text' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		      ADD COLUMN `shipping_country_text` TEXT NULL
		  ;");
		}
    }

}