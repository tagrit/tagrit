<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_139 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();
		
		if (!$CI->db->field_exists('purchase_price' ,db_prefix() . 'wh_order_return_details')){
		    $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_return_details`
		      ADD COLUMN `purchase_price` decimal(15,2)  DEFAULT '0.00'
		  ;");
		}
	}
}
