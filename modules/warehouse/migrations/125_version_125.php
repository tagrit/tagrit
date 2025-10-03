<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_125 extends App_module_migration
{
	public function up()
	{   
		$CI = &get_instance();
		
		//Version 125
		if (!$CI->db->field_exists('type_of_packing_list' ,db_prefix() . 'wh_packing_lists')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_packing_lists`
				ADD COLUMN `type_of_packing_list` VARCHAR(100)  NULL DEFAULT 'total'
				;");
		}

		if (!$CI->db->field_exists('delivery_status' ,db_prefix() . 'wh_packing_lists')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_packing_lists`
				ADD COLUMN `delivery_status` VARCHAR(100)  NULL DEFAULT 'wh_ready_to_deliver'
				;");
		}

		if (!$CI->db->field_exists('delivery_status' ,db_prefix() . 'goods_delivery')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
				ADD COLUMN `delivery_status` VARCHAR(100)  NULL DEFAULT 'ready_for_packing'
				;");
		}

		// purchase, => can_be_purchased
		// inventory => can_be_inventory
		// loyalty => can_be_sold
		// omni_sale => can_be_sold
		// sale_invoice => can_be_sold
		// manufacturing order => can_be_manufacturing
		// affiliate => can_be_sold

		if (!$CI->db->field_exists('can_be_sold' ,db_prefix() . 'items')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "items`
				ADD COLUMN `can_be_sold` VARCHAR(100) NULL DEFAULT 'can_be_sold'
				;");
		}
		if (!$CI->db->field_exists('can_be_purchased' ,db_prefix() . 'items')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "items`
				ADD COLUMN `can_be_purchased` VARCHAR(100) NULL DEFAULT 'can_be_purchased' 
				;");
		}
		if (!$CI->db->field_exists('can_be_manufacturing' ,db_prefix() . 'items')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "items`
				ADD COLUMN `can_be_manufacturing` VARCHAR(100) NULL DEFAULT 'can_be_manufacturing' 
				;");
		}

		if (!$CI->db->field_exists('can_be_inventory' ,db_prefix() . 'items')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "items`
				ADD COLUMN `can_be_inventory` VARCHAR(100) NULL DEFAULT 'can_be_inventory' 
				;");
		}

		//add shipment on Omnisales module
		if (!$CI->db->table_exists(db_prefix() . 'wh_omni_shipments')) {
			$CI->db->query('CREATE TABLE `' . db_prefix() . "wh_omni_shipments` (

				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`cart_id` INT(11) NULL,
				`shipment_number` VARCHAR(100) NULL,
				`planned_shipping_date` DATETIME NULL,
				`shipment_status` VARCHAR(50) NULL,
				`datecreated` DATETIME NULL,

				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
			}

		}
	}
