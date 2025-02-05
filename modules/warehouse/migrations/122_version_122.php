<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_122 extends App_module_migration
{
	public function up()
	{   
		$CI = &get_instance();
		if (!$CI->db->field_exists('tax_rate' ,db_prefix() . 'goods_receipt_detail')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt_detail`
		      ADD COLUMN `tax_rate` TEXT NULL
		  ;");
		}

		if (!$CI->db->field_exists('sub_total' ,db_prefix() . 'goods_receipt_detail')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt_detail`
				ADD COLUMN `sub_total` DECIMAL(15,2) NULL DEFAULT '0'
				;");
		}

		if (!$CI->db->field_exists('tax_name' ,db_prefix() . 'goods_receipt_detail')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt_detail`
				ADD COLUMN `tax_name` TEXT NULL
				;");
		}

		if (!$CI->db->field_exists('commodity_name' ,db_prefix() . 'internal_delivery_note_detail')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "internal_delivery_note_detail`
				ADD COLUMN `commodity_name` TEXT NULL
				;");
		}

		if (!$CI->db->field_exists('commodity_name' ,db_prefix() . 'wh_loss_adjustment_detail')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "wh_loss_adjustment_detail`
				ADD COLUMN `commodity_name` TEXT NULL
				;");
		}
		if (!$CI->db->field_exists('tax_rate' ,db_prefix() . 'goods_delivery_detail')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery_detail`
				ADD COLUMN `tax_rate` TEXT NULL,
				ADD COLUMN `tax_name` TEXT NULL,
				ADD COLUMN `sub_total` DECIMAL(15,2) NULL DEFAULT '0'
				;");
		}
		if (!$CI->db->field_exists('additional_discount' ,db_prefix() . 'goods_delivery')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
				ADD COLUMN `additional_discount` DECIMAL(15,2) NULL DEFAULT '0'
				;");
		}

		if (!$CI->db->field_exists('sub_total' ,db_prefix() . 'goods_delivery')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
				ADD COLUMN `sub_total` DECIMAL(15,2) NULL DEFAULT '0'
				;");
		}

	}
}
