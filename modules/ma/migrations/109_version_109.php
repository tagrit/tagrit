<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_109 extends App_module_migration
{
   public function up()
   {
      $CI = &get_instance();

      if (!$CI->db->field_exists('confirm' ,db_prefix() . 'ma_email_logs')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_email_logs`
              ADD COLUMN `confirm` TINYINT(1) NOT NULL DEFAULT 0');
      }
   }
}
