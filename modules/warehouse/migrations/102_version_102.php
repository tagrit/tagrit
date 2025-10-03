<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_102 extends App_module_migration
{
     public function up()
     {
          $CI = &get_instance();

          if (!$CI->db->field_exists('tax_id', 'goods_delivery_detail')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
              ADD COLUMN `tax_id` varchar(100)
              ;');            
          }
          if (!$CI->db->field_exists('total_after_discount', 'goods_delivery_detail')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
              ADD COLUMN `total_after_discount` varchar(100)
              ;');            
          }
          
          
     }
}
