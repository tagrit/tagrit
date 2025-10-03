<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_120 extends App_module_migration
{
	public function up()
	{   
		$CI = &get_instance();

		//version_118x add activity log for Delivery note
		if (!$CI->db->table_exists(db_prefix() . 'wh_goods_delivery_activity_log')) {
			$CI->db->query('CREATE TABLE `' . db_prefix() . "wh_goods_delivery_activity_log` (
				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`rel_id` int NULL ,
				`rel_type` varchar(100) NULL ,
				`description` mediumtext NULL,
				`additional_data` text NULL,
				`date` datetime NULL,
				`staffid` int(11) NULL,
				`full_name` varchar(100) NULL,

				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
		}

		//add create inventory delivery was partial from invoice, add column
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
