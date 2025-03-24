<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_110 extends App_module_migration
{
   public function up()
   {
      $CI = &get_instance();

      if (!$CI->db->field_exists('failed_debugger' ,db_prefix() . 'ma_email_logs')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_email_logs`
                ADD COLUMN `failed_debugger` TEXT NULL');
      }
   }
}
