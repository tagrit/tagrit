<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_121 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        // ---------------- version 1.2.1
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

        if (!$CI->db->field_exists('tax_name' ,db_prefix() . 'pur_request_detail')){
            $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
          ADD COLUMN `tax_name` TEXT NULL 
          ;");
        }

        if (!$CI->db->field_exists('tax_name' ,db_prefix() . 'pur_estimate_detail')){
            $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
          ADD COLUMN `tax_name` TEXT NULL 
          ;");
        }

        if (!$CI->db->field_exists('item_name' ,db_prefix() . 'pur_estimate_detail')){
            $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
          ADD COLUMN `item_name` TEXT NULL 
          ;");
        }

        if (!$CI->db->field_exists('tax_name' ,db_prefix() . 'pur_order_detail')){
            $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
          ADD COLUMN `tax_name` TEXT NULL 
          ;");
        }

        if (!$CI->db->field_exists('item_name' ,db_prefix() . 'pur_order_detail')){
            $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
          ADD COLUMN `item_name` TEXT NULL 
          ;");
        }

        if (!$CI->db->field_exists('send_to_vendors' ,db_prefix() . 'pur_request')){
            $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
          ADD COLUMN `send_to_vendors` TEXT NULL 
          ;");
        }

        if (!$CI->db->table_exists(db_prefix() . 'items_of_vendor')) {
          $CI->db->query('CREATE TABLE `' . db_prefix() . "items_of_vendor` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `vendor_id` INT(11) NOT NULL,
            `description` TEXT NOT NULL,
            `long_description` TEXT NULL,
            `rate` DECIMAL(15,2) NULL,
            `tax` int(11) NULL,
            `tax2` int(11) NULL,
            `unit` varchar(40) NULL,
            `group_id` int(11) NOT NULL,
            `commodity_code` varchar(100) NOT NULL,
            `commodity_barcode` TEXT NULL,
            `unit_id` int(11) NULL,
            `sku_code` VARCHAR(200) NULL,
            `sku_name` VARCHAR(200) NULL,
            `sub_group` VARCHAR(200) NULL,
            `active` INT(11) NULL,
            `parent` INT(11) NULL,
            `attributes` LONGTEXT NULL,
            `parent_attributes` LONGTEXT NULL,
            `commodity_type` INT(11) NULL,
            `origin` VARCHAR(100) NULL,
            `commodity_name` VARCHAR(200) NOT NULL,
            `series_id` TEXT NULL,
            `long_descriptions` LONGTEXT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
      }

      if (!$CI->db->field_exists('share_status' ,db_prefix() . 'items_of_vendor')){
          $CI->db->query('ALTER TABLE `' . db_prefix() . "items_of_vendor`
        ADD COLUMN `share_status` int(1) NULL DEFAULT 0
        ;");
      }

      if (!$CI->db->field_exists('from_vendor_item' ,db_prefix() . 'items')){
          $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
        ADD COLUMN `from_vendor_item` int(11) NULL
        ;");
      }


      if (!$CI->db->field_exists('add_from_type' ,db_prefix() . 'pur_invoices')){
          $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
        ADD COLUMN `add_from_type` varchar(20) NULL
        ;");
      }

      if (!$CI->db->table_exists(db_prefix() . 'currency_rates')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . "currency_rates` (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `from_currency_id` int(11) NULL,
          `from_currency_name` VARCHAR(100) NULL,
          `from_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
          `to_currency_id` int(11) NULL,
          `to_currency_name` VARCHAR(100) NULL,
          `to_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
          `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
      }

      if (!$CI->db->table_exists(db_prefix() . 'currency_rate_logs')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . "currency_rate_logs` (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `from_currency_id` int(11) NULL,
          `from_currency_name` VARCHAR(100) NULL,
          `from_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
          `to_currency_id` int(11) NULL,
          `to_currency_name` VARCHAR(100) NULL,
          `to_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
          `date` DATE NULL,

          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
      }


      add_option('cr_date_cronjob_currency_rates', '');
      add_option('cr_automatically_get_currency_rate', 1);
      add_option('cr_global_amount_expiration', 0);

      if (!$CI->db->field_exists('currency' ,db_prefix() . 'pur_request')){
          $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
        ADD COLUMN `currency` INT(11) NULL DEFAULT 0
        ;");
      }

      if (!$CI->db->field_exists('currency' ,db_prefix() . 'pur_orders')){
          $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
        ADD COLUMN `currency` INT(11) NULL DEFAULT 0
        ;");
      }

      if (!$CI->db->field_exists('currency' ,db_prefix() . 'pur_invoices')){
          $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
        ADD COLUMN `currency` INT(11) NULL DEFAULT 0
        ;");
      }
  }
}