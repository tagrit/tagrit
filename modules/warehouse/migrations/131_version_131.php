<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_131 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();
		/*for sales agent module*/
		if (!$CI->db->field_exists('order_id' ,db_prefix() . 'wh_omni_shipments')){
			$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_omni_shipments`
				ADD COLUMN `order_id` INT(11) NULL DEFAULT '0'
				;");
		}
	}
}
