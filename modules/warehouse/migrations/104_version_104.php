<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_104 extends App_module_migration
{
     public function up()
     {
          $CI = &get_instance();

          if (!$CI->db->field_exists('expiry_date', 'goods_delivery_detail')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
              ADD COLUMN `expiry_date` text  NULL ,
              ADD COLUMN `lot_number` text NULL
              ;');            
          }
          
          if (!$CI->db->field_exists('expiry_date', 'goods_transaction_detail')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_transaction_detail` 
              ADD COLUMN `expiry_date` text NULL ,
              ADD COLUMN `lot_number` text NULL
              ;');            
          }

          
     }
}
