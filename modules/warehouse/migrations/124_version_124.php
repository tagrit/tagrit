<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_124 extends App_module_migration
{
	public function up()
	{   
		$CI = &get_instance();
		
		//Version 124
		add_option('packing_list_number_prefix', 'PL', 1);
		add_option('next_packing_list_number', 1, 1);

		if (!$CI->db->table_exists(db_prefix() . 'wh_packing_lists')) {
			$CI->db->query('CREATE TABLE `' . db_prefix() . "wh_packing_lists` (

				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`delivery_note_id` INT(11) NULL,
				`packing_list_number` VARCHAR(100) NULL,
				`packing_list_name` VARCHAR(200) NULL,
				`width` DECIMAL(15,2) NULL DEFAULT '0.00',
				`height` DECIMAL(15,2) NULL DEFAULT '0.00',
				`lenght` DECIMAL(15,2) NULL DEFAULT '0.00',
				`weight` DECIMAL(15,2) NULL DEFAULT '0.00',
				`volume` DECIMAL(15,2) NULL DEFAULT '0.00',
				`clientid` INT(11) NULL,
				`subtotal` DECIMAL(15,2) NULL DEFAULT '0.00',
				`total_amount` DECIMAL(15,2) NULL DEFAULT '0.00',
				`discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
				`additional_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
				`total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
				`billing_street` varchar(200) DEFAULT NULL,
				`billing_city` varchar(100) DEFAULT NULL,
				`billing_state` varchar(100) DEFAULT NULL,
				`billing_zip` varchar(100) DEFAULT NULL,
				`billing_country` int(11) DEFAULT NULL,
				`shipping_street` varchar(200) DEFAULT NULL,
				`shipping_city` varchar(100) DEFAULT NULL,
				`shipping_state` varchar(100) DEFAULT NULL,
				`shipping_zip` varchar(100) DEFAULT NULL,
				`shipping_country` int(11) DEFAULT NULL,
				`client_note` TEXT NULL,
				`admin_note` TEXT NULL,
				`approval` INT(11) NULL DEFAULT 0,
				`datecreated` DATETIME NULL,
				`staff_id` INT(11) NULL,

				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
			}

			if (!$CI->db->table_exists(db_prefix() . 'wh_packing_list_details')) {
				$CI->db->query('CREATE TABLE `' . db_prefix() . "wh_packing_list_details` (

					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`packing_list_id` INT(11) NOT NULL,
					`delivery_detail_id` INT(11) NULL,
					`commodity_code` INT(11) NULL,
					`commodity_name` TEXT NULL,
					`quantity` DECIMAL(15,2) NULL DEFAULT '0.00',
					`unit_id` INT(11) NULL,
					`unit_price` DECIMAL(15,2) NULL DEFAULT '0.00',
					`sub_total` DECIMAL(15,2) NULL DEFAULT '0.00',
					`tax_id`  TEXT NULL,
					`tax_rate`  TEXT NULL,
					`tax_name`  TEXT NULL,
					`total_amount` DECIMAL(15,2) NULL DEFAULT '0.00',
					`discount` DECIMAL(15,2) NULL DEFAULT '0.00',
					`discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
					`total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00',

					PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
			}

			if (!$CI->db->field_exists('packing_qty' ,db_prefix() . 'goods_delivery_detail')) { 
				$CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery_detail`
				ADD COLUMN `packing_qty` DECIMAL(15,2) NULL DEFAULT '0.00'
				;");
			}

		}
	}
