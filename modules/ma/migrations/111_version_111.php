<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_111 extends App_module_migration
{
   public function up()
   {
      $CI = &get_instance();

      if (!$CI->db->field_exists('subject' ,db_prefix() . 'ma_email_designs')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_email_designs`
                ADD COLUMN `subject` TEXT NULL');
      }
   }
}
