<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_127 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();
		
		if ($CI->db->field_exists('discount_total' ,db_prefix() . 'wh_packing_lists')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_packing_lists`
				CHANGE COLUMN `discount_total` `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
				CHANGE COLUMN `additional_discount` `additional_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
				CHANGE COLUMN `total_after_discount` `total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00'
				;");
		}
		if ($CI->db->field_exists('discount' ,db_prefix() . 'wh_packing_list_details')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_packing_list_details`
				CHANGE COLUMN `discount` `discount` DECIMAL(15,2) NULL DEFAULT '0.00',
				CHANGE COLUMN `discount_total` `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
				CHANGE COLUMN `total_after_discount` `total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00'
				;");
		}

		//serial numbers
		if (!$CI->db->table_exists(db_prefix() . 'wh_inventory_serial_numbers')) {
			$CI->db->query('CREATE TABLE `' . db_prefix() . "wh_inventory_serial_numbers` (

				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`commodity_id` INT(11) NOT NULL,
				`warehouse_id` INT(11) NULL,
				`inventory_manage_id` INT(11) NULL,
				`serial_number` VARCHAR(255) NULL,
				`is_used` VARCHAR(20) NULL DEFAULT 'no',

				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
			}

			if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'goods_receipt_detail')) {
				$CI->db->query('ALTER TABLE `' . db_prefix() . 'goods_receipt_detail`
				ADD COLUMN `serial_number` VARCHAR(255) NULL
				');
			}
			if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'goods_delivery_detail')) {
				$CI->db->query('ALTER TABLE `' . db_prefix() . 'goods_delivery_detail`
				ADD COLUMN `serial_number` VARCHAR(255) NULL
				');
			}

			if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'internal_delivery_note_detail')) {
				$CI->db->query('ALTER TABLE `' . db_prefix() . 'internal_delivery_note_detail`
				ADD COLUMN `serial_number` VARCHAR(255) NULL
				');
			}
			if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'wh_loss_adjustment_detail')) {
				$CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_loss_adjustment_detail`
				ADD COLUMN `serial_number` VARCHAR(255) NULL
				');
			}
			if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'wh_packing_list_details')) {
				$CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_packing_list_details`
				ADD COLUMN `serial_number` VARCHAR(255) NULL
				');
			}
			if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'goods_transaction_detail')) {
				$CI->db->query('ALTER TABLE `' . db_prefix() . 'goods_transaction_detail`
				ADD COLUMN `serial_number` VARCHAR(255) NULL
				');
			}

			if (!$CI->db->field_exists('purchase_price' ,db_prefix() . 'inventory_manage')) {
				$CI->db->query('ALTER TABLE `' . db_prefix() . 'inventory_manage`
				ADD COLUMN `purchase_price` DECIMAL(15,2) NULL DEFAULT "0.00"
				');
			}

			//Omni_sale add shipping fee on sales order => delivery note -  add shipping_fee
			if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'goods_delivery')) {
				$CI->db->query('ALTER TABLE `' . db_prefix() . 'goods_delivery`
				ADD COLUMN `shipping_fee` DECIMAL(15,2) NULL DEFAULT "0.00"
				');
			}
			if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'wh_packing_lists')) {
				$CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_packing_lists`
				ADD COLUMN `shipping_fee` DECIMAL(15,2) NULL DEFAULT "0.00"
				');
			}
			if (!$CI->db->field_exists('goods_delivery_id' ,db_prefix() . 'wh_omni_shipments')) {
				$CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_omni_shipments`
				ADD COLUMN `goods_delivery_id` INT(11) NULL
				');
			}

		}
	}
