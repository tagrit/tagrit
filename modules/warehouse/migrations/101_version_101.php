<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_101 extends App_module_migration
{
     public function up()
     {
          $CI = &get_instance();

          if (!$CI->db->field_exists('discount', 'goods_receipt_detail')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_receipt_detail` 
              ADD COLUMN `discount` varchar(100)
              ;');            
          }

          if (!$CI->db->field_exists('discount_money', 'goods_receipt_detail')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_receipt_detail` 
              ADD COLUMN `discount_money` varchar(100)
              ;');            
          }

          if (!$CI->db->field_exists('discount', 'goods_delivery_detail')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
              ADD COLUMN `discount` varchar(100)
              ;');            
          }

          if (!$CI->db->field_exists('discount_money', 'goods_delivery_detail')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
              ADD COLUMN `discount_money` varchar(100)
              ;');            
          }
          if (!$CI->db->field_exists('available_quantity', 'goods_delivery_detail')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
              ADD COLUMN `available_quantity` varchar(100)
              ;');            
          }

          if (!$CI->db->field_exists('purchase_price', 'goods_transaction_detail')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_transaction_detail` 
              ADD COLUMN `purchase_price` varchar(100)
              ;');            
          }
          if (!$CI->db->field_exists('price', 'goods_transaction_detail')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_transaction_detail` 
              ADD COLUMN `price` varchar(100)
              ;');            
          }

          if (!$CI->db->field_exists('total_discount', 'goods_delivery')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery` 
              ADD COLUMN `total_discount` varchar(100)
              ;');            
          }
          if (!$CI->db->field_exists('after_discount', 'goods_delivery')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery` 
              ADD COLUMN `after_discount` varchar(100)
              ;');            
          }
          
          
     }
}
