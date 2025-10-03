<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_105 extends App_module_migration
{
     public function up()
     {
          $CI = &get_instance();

          if (!$CI->db->field_exists('guarantee', 'items')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'items` 
              ADD COLUMN `guarantee` text  NULL 
              
              ;');            
          }
		
		 if (!$CI->db->field_exists('guarantee_period', 'goods_delivery_detail')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
              ADD COLUMN `guarantee_period` text  NULL 
              
              ;');            
          }

          if (!$CI->db->field_exists('expiry_date', 'wh_loss_adjustment_detail')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'wh_loss_adjustment_detail` 
              ADD COLUMN `expiry_date` text NULL ,
              ADD COLUMN `lot_number` text NULL
              ;');            
          }

          
     }
}
