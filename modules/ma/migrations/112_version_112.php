<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_112 extends App_module_migration
{
   public function up()
   {
      $CI = &get_instance();

      if (!$CI->db->table_exists(db_prefix() . 'ma_smtp_configs')) {
          $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_smtp_configs (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` TEXT NOT NULL,
            `configs` TEXT NULL,
            `is_default` TINYINT(11) NOT NULL DEFAULT 0,
            `addedfrom` INT(11) NULL,
            `dateadded` DATETIME NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
      }

      if (!$CI->db->field_exists('smtp_config' ,db_prefix() . 'ma_campaigns')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_campaigns`
                ADD COLUMN `smtp_config` int(11) NULL,
                ADD COLUMN `email_limit_config` int(11) NULL'
            );
      }

      if (!$CI->db->table_exists(db_prefix() . 'ma_email_limit_configs')) {
          $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_email_limit_configs (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` TEXT NOT NULL,
            `configs` TEXT NULL,
            `is_default` TINYINT(11) NOT NULL DEFAULT 0,
            `addedfrom` INT(11) NULL,
            `dateadded` DATETIME NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
      }
   }
}
