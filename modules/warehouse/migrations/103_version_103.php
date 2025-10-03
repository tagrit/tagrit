<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_103 extends App_module_migration
{
     public function up()
     {
          $CI = &get_instance();

          if (!$CI->db->field_exists('invoice_id', 'goods_delivery')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery` 
              ADD COLUMN `invoice_id` varchar(100)
              ;');            
          }

          if (!$CI->db->field_exists('lot_number', 'goods_receipt_detail')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_receipt_detail` 
              ADD COLUMN `lot_number` varchar(100)
              ;');            
          }
          
          if (!$CI->db->field_exists('lot_number', 'inventory_manage')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'inventory_manage` 
              ADD COLUMN `lot_number` varchar(100)
              ;');            
          }
          
          
     }
}
