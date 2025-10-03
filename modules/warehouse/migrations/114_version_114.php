<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_114 extends App_module_migration
{
     public function up()
     {   
        $CI = &get_instance();

        //add internal delivery note, function
        if (!$CI->db->table_exists(db_prefix() . 'internal_delivery_note')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . "internal_delivery_note` (
              `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

              `internal_delivery_name` text NULL ,
              `description` text NULL ,
              `staff_id` int(11) NULL ,
              `date_c` date NULL ,
              `date_add` date NULL,
              `internal_delivery_code` varchar(100) NULL ,
              `approval` INT(11) NULL DEFAULT 0 COMMENT 'status approval ',
              `addedfrom` INT(11) null,
              `total_amount` decimal(15,2) null ,
              `datecreated` datetime null ,

              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }

        if (!$CI->db->table_exists(db_prefix() . 'internal_delivery_note_detail')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . "internal_delivery_note_detail` (
              `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `internal_delivery_id` int(11) NOT NULL,
              `commodity_code` varchar(100) NULL,
              `from_stock_name` text NULL,
              `to_stock_name` text NULL,
              `unit_id` text NULL,
              `available_quantity` text NULL,
              `quantities` text NULL,
              `unit_price` varchar(100) NULL,
              `into_money` varchar(100) NULL,
              `note` text NULL ,

              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }

        add_option('internal_delivery_number_prefix', 'ID', 1);
        add_option('next_internal_delivery_mumber', 1, 1);
        add_option('item_sku_prefix', '', 1);


        if (!$CI->db->field_exists('from_stock_name' ,db_prefix() . 'goods_transaction_detail')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_transaction_detail`
              ADD COLUMN `from_stock_name` int(11),
              ADD COLUMN `to_stock_name` int(11)
          ;");
        }  

   

     }
}
