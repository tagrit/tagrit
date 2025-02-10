<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_108 extends App_module_migration
{
   public function up()
   {
      $CI = &get_instance();

      if (!$CI->db->field_exists('filter_type' ,db_prefix() . 'ma_segments')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_segments`
              ADD COLUMN `filter_type` TEXT NULL');
      }

      if (!$CI->db->field_exists('customer_type' ,db_prefix() . 'ma_segment_filters')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_segment_filters`
              ADD COLUMN `customer_type` TEXT NULL');
      }

      if (!$CI->db->field_exists('client_id' ,db_prefix() . 'ma_lead_segments')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_lead_segments`
              ADD COLUMN `client_id` INT(11) NULL');
      }
   }
}
