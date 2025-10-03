<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_117 extends App_module_migration
{
     public function up()
     {   
        $CI = &get_instance();
        
          if (!$CI->db->field_exists('city' ,db_prefix() . 'warehouse')) { 
            $CI->db->query('ALTER TABLE `' . db_prefix() . "warehouse`
                ADD COLUMN `city` TEXT  NULL
            ;");
          }

          if (!$CI->db->field_exists('state' ,db_prefix() . 'warehouse')) { 
            $CI->db->query('ALTER TABLE `' . db_prefix() . "warehouse`
                ADD COLUMN `state` TEXT  NULL
            ;");
          }

          if (!$CI->db->field_exists('zip_code' ,db_prefix() . 'warehouse')) { 
            $CI->db->query('ALTER TABLE `' . db_prefix() . "warehouse`
                ADD COLUMN `zip_code` TEXT  NULL
            ;");
          }

          if (!$CI->db->field_exists('country' ,db_prefix() . 'warehouse')) { 
            $CI->db->query('ALTER TABLE `' . db_prefix() . "warehouse`
                ADD COLUMN `country` TEXT  NULL
            ;");
          }

          if (!$CI->db->field_exists('parent_id' ,db_prefix() . 'items')) { 
            $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
                ADD COLUMN `parent_id` int(11)  NULL  DEFAULT NULL
            ;");
          }

          if (!$CI->db->field_exists('attributes' ,db_prefix() . 'items')) { 
            $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
                ADD COLUMN `attributes` LONGTEXT  NULL
            ;");
          }

          if (!$CI->db->field_exists('parent_attributes' ,db_prefix() . 'items')) { 
            $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
                ADD COLUMN `parent_attributes` LONGTEXT  NULL
            ;");
          }

     }
}
