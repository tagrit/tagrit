<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_107 extends App_module_migration
{
   public function up()
   {
      $CI = &get_instance();

      add_option('ma_email_sending_limit', 0);
      add_option('ma_email_limit', 100);
      add_option('ma_email_interval', 60);
      add_option('ma_email_repeat_every', 'minutes');

      if (!$CI->db->field_exists('bcc_address' ,db_prefix() . 'ma_email_logs')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_email_logs`
              ADD COLUMN `bcc_address` INT(11) NOT NULL DEFAULT 0');
      }
            
      if (!$CI->db->table_exists(db_prefix() . 'ma_campaign_test')) {
          $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_campaign_test (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` TEXT NOT NULL,
            `email` TEXT NULL,
            `campaign_id` INT(255) NULL,
            `segment_id` INT(255) NULL,
            `stage_id` INT(255) NULL,
            `point` INT(255) NOT NULL DEFAULT 0,
            `tags` TEXT NULL,
            `status` INT(255) NULL,
            `addedfrom` INT(11) NULL,
            `dateadded` DATETIME NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
      }

      if (!$CI->db->table_exists(db_prefix() . 'ma_campaign_test_logs')) {
          $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_campaign_test_logs (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `campaign_id` INT(11) NOT NULL,
            `node_id` INT(11) NOT NULL,
            `output` TEXT NULL,
            `result` TEXT NULL,
            `dateadded` DATETIME NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
      }

      if (!$CI->db->field_exists('delete_lead' ,db_prefix() . 'ma_campaign_test')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_campaign_test`
              ADD COLUMN `delete_lead` INT(11) NOT NULL DEFAULT 0,
              ADD COLUMN `remove_from_campaign` INT(11) NOT NULL DEFAULT 0,
              ADD COLUMN `convert_to_customer` INT(11) NOT NULL DEFAULT 0');
      }

      add_option('ma_modify_workflow_column', 0);

      if(get_option('ma_modify_workflow_column') == 0){
          if (!$CI->db->field_exists('workflow' ,db_prefix() . 'ma_campaigns')) {
              $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_campaigns`
                  MODIFY COLUMN `workflow` LONGTEXT NULL');
          }

          update_option('ma_modify_workflow_column', 1);
      }

      if (!$CI->db->field_exists('email' ,db_prefix() . 'ma_email_logs')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_email_logs`
              ADD COLUMN `email` TEXT NULL');
      }
   }
}
