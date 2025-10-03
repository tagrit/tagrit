<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_126 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();
		
		// Order returns
		// return request must be placed within X days after the delivery date
		add_option('wh_return_request_within_x_day', 30, 1);
		add_option('wh_fee_for_return_order', 0, 1);
		add_option('wh_return_policies_information', '', 1);
		add_option('wh_refund_loyaty_point', '1', 1);
		add_option('order_return_number_prefix', 'ReReturn', 1);
		add_option('next_order_return_number', 1, 1);
		add_option('e_order_return_number_prefix', 'DEReturn', 1);
		add_option('e_next_order_return_number', 1, 1);

		if (!$CI->db->table_exists(db_prefix() . 'wh_order_returns')) {
			$CI->db->query('CREATE TABLE `' . db_prefix() . "wh_order_returns` (

				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`rel_id` INT(11) NULL,
				`rel_type` VARCHAR(50) NOT NULL COMMENT'manual, sales_return_order, purchasing_return_order',
				`return_type` VARCHAR(50) NULL COMMENT'manual, partially, fully',
				`company_id` INT(11) NULL,
				`company_name` VARCHAR(500) NULL,
				`email` VARCHAR(100) NULL,
				`phonenumber` VARCHAR(20) NULL,
				`order_number` VARCHAR(500) NULL,
				`order_date` DATETIME NULL,
				`number_of_item` DECIMAL(15,2) NULL DEFAULT '0.00',
				`order_total` DECIMAL(15,2) NULL DEFAULT '0.00',
				`order_return_number` VARCHAR(200) NULL,
				`order_return_name` VARCHAR(500) NULL,
				`fee_return_order` DECIMAL(15,2) NULL DEFAULT '0.00',
				`refund_loyaty_point` INT(11) NULL DEFAULT '0',
				`subtotal` DECIMAL(15,2) NULL DEFAULT '0.00',
				`total_amount` DECIMAL(15,2) NULL DEFAULT '0.00',
				`discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
				`additional_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
				`adjustment_amount` DECIMAL(15,2) NULL DEFAULT '0.00',
				`total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
				`return_policies_information` TEXT NULL,
				`admin_note` TEXT NULL,
				`approval` INT(11) NULL DEFAULT 0,
				`datecreated` DATETIME NULL,
				`staff_id` INT(11) NULL,
				`receipt_delivery_id` INT(1) NULL DEFAULT 0,

				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
			}

			if (!$CI->db->field_exists('order_return_name' ,db_prefix() . 'wh_order_returns')) { 
				$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
				ADD COLUMN `order_return_name` VARCHAR(500) NULL
				;");
			}

			if (!$CI->db->field_exists('company_id' ,db_prefix() . 'wh_order_returns')) { 
				$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
				ADD COLUMN `company_id` INT(11) NULL 
				;");
			}

			if (!$CI->db->table_exists(db_prefix() . 'wh_order_return_details')) {
				$CI->db->query('CREATE TABLE `' . db_prefix() . "wh_order_return_details` (

				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`order_return_id` INT(11) NOT NULL,
				`rel_type_detail_id` INT(11) NULL,
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
				`reason_return` VARCHAR(200) NULL,

				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
			}

			if (!$CI->db->field_exists('currency' ,db_prefix() . 'wh_order_returns')) { 
				$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
				ADD COLUMN `currency` INT(11) NULL 
				;");
			}

			if (!$CI->db->field_exists('receipt_delivery_id' ,db_prefix() . 'wh_order_returns')) { 
				$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
				ADD COLUMN `receipt_delivery_id` INT(1) NULL  DEFAULT '0'
				;");
			}

			if ($CI->db->field_exists('discount_total' ,db_prefix() . 'wh_order_returns')) { 
				$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
				CHANGE COLUMN `discount_total` `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00' 
				;");
			}
			if ($CI->db->field_exists('additional_discount' ,db_prefix() . 'wh_order_returns')) { 
				$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
				CHANGE COLUMN `additional_discount` `additional_discount` DECIMAL(15,2) NULL DEFAULT '0.00' ;");
			}
			if ($CI->db->field_exists('adjustment_amount' ,db_prefix() . 'wh_order_returns')) { 
				$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
				CHANGE COLUMN `adjustment_amount` `adjustment_amount` DECIMAL(15,2) NULL DEFAULT '0.00' ;");
			}
			if ($CI->db->field_exists('total_after_discount' ,db_prefix() . 'wh_order_returns')) { 
				$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
				CHANGE COLUMN `total_after_discount` `total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00' ;");
			}


			if ($CI->db->field_exists('discount' ,db_prefix() . 'wh_order_return_details')) { 
				$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_return_details`
				CHANGE COLUMN `discount` `discount` DECIMAL(15,2) NULL DEFAULT '0.00' 
				;");
			}
			if ($CI->db->field_exists('discount_total' ,db_prefix() . 'wh_order_return_details')) { 
				$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_return_details`
				CHANGE COLUMN `discount_total` `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00' ;");
			}
			if ($CI->db->field_exists('total_after_discount' ,db_prefix() . 'wh_order_return_details')) { 
				$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_return_details`
				CHANGE COLUMN `total_after_discount` `total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00' ;");
			}

			add_option('warehouse_receive_return_order ', 0, 1);
			if (!$CI->db->field_exists('return_reason' ,db_prefix() . 'wh_order_returns')) {
				$CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_order_returns`
				ADD COLUMN `return_reason` longtext NULL
				');
			}

			// inventory_receipt_voucher_returned_goods
			// inventory_delivery_voucher_returned_purchasing_goods
			if (!$CI->db->field_exists('receipt_delivery_type' ,db_prefix() . 'wh_order_returns')) {
				$CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_order_returns`
				ADD COLUMN `receipt_delivery_type` VARCHAR(100) NULL
				');
			}

		}
	}
