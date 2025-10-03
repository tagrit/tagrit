<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_121 extends App_module_migration
{
	public function up()
	{   
		$CI = &get_instance();

        //version_118x add create inventory delivery was partial from invoice, add column
		if (!$CI->db->field_exists('wh_delivered_quantity' ,db_prefix() . 'itemable')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "itemable`
				ADD COLUMN `wh_delivered_quantity` DECIMAL(15,2)  DEFAULT '0'
				;");
		}

		if (!$CI->db->field_exists('type_of_delivery' ,db_prefix() . 'goods_delivery')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
				ADD COLUMN `type_of_delivery` VARCHAR(100)  NULL DEFAULT 'total'
				;");
		}

	}
}
