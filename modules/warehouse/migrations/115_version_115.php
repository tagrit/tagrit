<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_115 extends App_module_migration
{
     public function up()
     {   
        $CI = &get_instance();

        //current version on eoffice 1.1.3
        //maximum stock
        if (!$CI->db->field_exists('inventory_number_max' ,db_prefix() . 'inventory_commodity_min')) { 
            $CI->db->query('ALTER TABLE `' . db_prefix() . "inventory_commodity_min`
                ADD COLUMN `inventory_number_max` varchar(100) NULL default 0
            ;");
          }

        //Goods receipt

        if (!$CI->db->field_exists('project' ,db_prefix() . 'goods_receipt')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt`
              ADD COLUMN `project` TEXT  NULL
          ;");
        }
        if (!$CI->db->field_exists('type' ,db_prefix() . 'goods_receipt')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt`
              ADD COLUMN `type` TEXT  NULL
          ;");
        }

        if (!$CI->db->field_exists('department' ,db_prefix() . 'goods_receipt')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt`
              ADD COLUMN `department` int(11)  NULL
          ;");
        }

        if (!$CI->db->field_exists('requester' ,db_prefix() . 'goods_receipt')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt`
              ADD COLUMN `requester` int(11)  NULL
          ;");
        }

        if (!$CI->db->field_exists('expiry_date' ,db_prefix() . 'goods_receipt')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt`
              ADD COLUMN `expiry_date` DATE NULL
          ;");
        }
        if (!$CI->db->field_exists('invoice_no' ,db_prefix() . 'goods_receipt')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt`
              ADD COLUMN `invoice_no` text NULL
          ;");
        }

        /*Required PO selected  when create goods received voucher*/
        add_option('goods_receipt_required_po', 0, 1);
        add_option('goods_delivery_required_po', 0, 1);

        //Goods delivery
        if (!$CI->db->field_exists('project' ,db_prefix() . 'goods_delivery')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
              ADD COLUMN `project` TEXT  NULL
          ;");
        }
        if (!$CI->db->field_exists('type' ,db_prefix() . 'goods_delivery')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
              ADD COLUMN `type` TEXT  NULL
          ;");
        }

        if (!$CI->db->field_exists('department' ,db_prefix() . 'goods_delivery')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
              ADD COLUMN `department` int(11)  NULL
          ;");
        }

        if (!$CI->db->field_exists('requester' ,db_prefix() . 'goods_delivery')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
              ADD COLUMN `requester` int(11)  NULL
          ;");
        }

        if (!$CI->db->field_exists('invoice_no' ,db_prefix() . 'goods_delivery')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
              ADD COLUMN `invoice_no` text NULL
          ;");
        }

        //goods delivery invoice
        if (!$CI->db->table_exists(db_prefix() . 'goods_delivery_invoices_pr_orders')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . "goods_delivery_invoices_pr_orders` (
              `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `rel_id` int(11) NULL COMMENT  'goods_delivery_id',
              `rel_type` int(11) NULL COMMENT 'invoice_id or purchase order id',

              `type` varchar(100) NULL COMMENT'invoice,  purchase_orders',

              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }

        if (!$CI->db->field_exists('pr_order_id' ,db_prefix() . 'goods_delivery')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
              ADD COLUMN `pr_order_id` int(11) NULL
          ;");
        }

        //add vat column in lead
        if (!$CI->db->field_exists('vat' ,db_prefix() . 'leads')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "leads`
              ADD COLUMN `vat` varchar(50) NULL
          ;");
        }

        //table brand
        if (!$CI->db->table_exists(db_prefix() . 'wh_brand')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_brand` (
              `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` text NULL ,

              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }

        //table model
        if (!$CI->db->table_exists(db_prefix() . 'wh_model')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_model` (
              `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` text NULL ,
              `brand_id` int(11) NOT NULL,

              PRIMARY KEY (`id`,`brand_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }


        //table series
        if (!$CI->db->table_exists(db_prefix() . 'wh_series')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_series` (
              `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` text NULL ,
              `model_id` int(11) NOT NULL,

              PRIMARY KEY (`id`,`model_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }

        if (!$CI->db->field_exists('series_id' ,db_prefix() . 'items')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
              ADD COLUMN `series_id` TEXT  NULL
          ;");
        }

        if (!$CI->db->field_exists('processing' ,db_prefix() . 'proposals')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "proposals`
              ADD COLUMN `processing` TEXT  NULL
          ;");
        }
        
        //warehouse custom fields
        if (!$CI->db->table_exists(db_prefix() . 'wh_custom_fields')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_custom_fields` (
              `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `custom_fields_id` int NULL ,
              `warehouse_id` text NULL,

              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }

   

     }
}
