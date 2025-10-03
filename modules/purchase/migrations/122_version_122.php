<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_122 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        if (!$CI->db->field_exists('order_status' ,db_prefix() . 'pur_orders')){
            $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
          ADD COLUMN `order_status` VARCHAR(30) NULL
          ;");
        }

        if (!$CI->db->field_exists('shipping_note' ,db_prefix() . 'pur_orders')){
            $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
          ADD COLUMN `shipping_note` TEXT NULL
          ;");
        }


        // Order returns
        // return request must be placed within X days after the delivery date
          add_option('pur_return_request_within_x_day', 30, 1);
          add_option('pur_fee_for_return_order', 0, 1);
          add_option('pur_return_policies_information', '', 1);

          add_option('pur_order_return_number_prefix', 'OReturn', 1);
          add_option('next_pur_order_return_number', 1, 1);


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

            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }

        if (!$CI->db->field_exists('company_id' ,db_prefix() . 'wh_order_returns')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
          ADD COLUMN `company_id` INT(11) NULL 
          ;");
        }

        if (!$CI->db->field_exists('currency' ,db_prefix() . 'wh_order_returns')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
          ADD COLUMN `currency` INT(11) NULL 
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

        if (!$CI->db->field_exists('receipt_delivery_id' ,db_prefix() . 'wh_order_returns')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
          ADD COLUMN `receipt_delivery_id` INT(1) NULL  DEFAULT '0'
          ;");
        }

        if (!$CI->db->field_exists('return_reason' ,db_prefix() . 'wh_order_returns')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_order_returns`
          ADD COLUMN `return_reason` longtext NULL
          ');
        }


        // Draft, Processing, Pending payment, Confirm, Shipping, Finish, Failed, Canceled, On Hold.
        if (!$CI->db->field_exists('status' ,db_prefix() . 'wh_order_returns')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_order_returns`
          ADD COLUMN `status` varchar(30) NULL DEFAULT "draft"                                             
          ');
        }

        if (!$CI->db->table_exists(db_prefix() . 'pur_activity_log')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_activity_log` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `rel_id` INT(11) NOT NULL,
          `rel_type` VARCHAR(45) NOT NULL,
          `staffid` INT(11) NULL,
          `date` DATETIME NULL,
          `note` TEXT NULL,
          PRIMARY KEY (`id`));');
        }

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

        if (!$CI->db->table_exists(db_prefix() . 'pur_invoice_details')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_invoice_details` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `pur_invoice` INT(11) NOT NULL,
          `item_code` VARCHAR(100) NULL,
          `description` TEXT NULL,
          `unit_id` INT(11) NULL,
          `unit_price` DECIMAL(15,2) NULL,
          `quantity` DECIMAL(15,2) NULL,
          `into_money` DECIMAL(15,2) NULL,
          `tax` TEXT NULL,
          `total` DECIMAL(15,2) NULL,
          `discount_percent` DECIMAL(15,2) NULL,
          `discount_money` DECIMAL(15,2) NULL,
          `total_money` DECIMAL(15,2) NULL,
          `tax_value` DECIMAL(15,2) NULL,
          `tax_rate` TEXT NULL,
          `tax_name` TEXT NULL,
          `item_name` TEXT NULL,
          PRIMARY KEY (`id`));');
        }


        if (!$CI->db->field_exists('discount_total' ,db_prefix() . 'pur_invoices')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_invoices`
          ADD COLUMN `discount_total` DECIMAL(15,2) NULL
          ');
        }

        if (!$CI->db->field_exists('discount_percent' ,db_prefix() . 'pur_invoices')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_invoices`
          ADD COLUMN `discount_percent` DECIMAL(15,2) NULL
          ');
        }

        if (!$CI->db->field_exists('return_within_day' ,db_prefix() . 'pur_vendor')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_vendor`
          ADD COLUMN `return_within_day` INT(11) NULL
          ');
        }

        if (!$CI->db->field_exists('return_order_fee' ,db_prefix() . 'pur_vendor')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_vendor`
          ADD COLUMN `return_order_fee` DECIMAL(15,2) NULL
          ');
        }

        if (!$CI->db->field_exists('return_policies' ,db_prefix() . 'pur_vendor')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_vendor`
          ADD COLUMN `return_policies` TEXT NULL
          ');
        }

        if (!$CI->db->table_exists(db_prefix() . 'wh_order_returns_refunds')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_order_returns_refunds` (
              `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `order_return_id` INT(11) NULL,
              `staff_id` INT(11) NULL,
              `refunded_on` date NULL,
              `payment_mode` varchar(40) NULL,
              `note` text NULL,
              `amount` decimal(15,2) NULL,
              `created_at` datetime NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }

        if (!$CI->db->field_exists('currency_rate' ,db_prefix() . 'pur_request')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_request`
          ADD COLUMN `currency_rate` DECIMAL(15,6) NULL
          ');
        }

        if (!$CI->db->field_exists('from_currency' ,db_prefix() . 'pur_request')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_request`
          ADD COLUMN `from_currency` VARCHAR(20) NULL
          ');
        }

        if (!$CI->db->field_exists('to_currency' ,db_prefix() . 'pur_request')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_request`
          ADD COLUMN `to_currency` VARCHAR(20) NULL
          ');
        }

        if (!$CI->db->field_exists('currency_rate' ,db_prefix() . 'pur_estimates')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_estimates`
          ADD COLUMN `currency_rate` DECIMAL(15,6) NULL
          ');
        }

        if (!$CI->db->field_exists('from_currency' ,db_prefix() . 'pur_estimates')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_estimates`
          ADD COLUMN `from_currency` VARCHAR(20) NULL
          ');
        }

        if (!$CI->db->field_exists('to_currency' ,db_prefix() . 'pur_estimates')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_estimates`
          ADD COLUMN `to_currency` VARCHAR(20) NULL
          ');
        }

        if (!$CI->db->field_exists('currency_rate' ,db_prefix() . 'pur_orders')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_orders`
          ADD COLUMN `currency_rate` DECIMAL(15,6) NULL
          ');
        }

        if (!$CI->db->field_exists('from_currency' ,db_prefix() . 'pur_orders')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_orders`
          ADD COLUMN `from_currency` VARCHAR(20) NULL
          ');
        }

        if (!$CI->db->field_exists('to_currency' ,db_prefix() . 'pur_orders')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_orders`
          ADD COLUMN `to_currency` VARCHAR(20) NULL
          ');
        }

        if (!$CI->db->field_exists('currency_rate' ,db_prefix() . 'pur_invoices')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_invoices`
          ADD COLUMN `currency_rate` DECIMAL(15,6) NULL
          ');
        }

        if (!$CI->db->field_exists('from_currency' ,db_prefix() . 'pur_invoices')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_invoices`
          ADD COLUMN `from_currency` VARCHAR(20) NULL
          ');
        }

        if (!$CI->db->field_exists('to_currency' ,db_prefix() . 'pur_invoices')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_invoices`
          ADD COLUMN `to_currency` VARCHAR(20) NULL
          ');
        }
        
    }
}