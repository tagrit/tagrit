<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_109 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        //Version 1.0.9

        if (!$CI->db->field_exists('vendor' ,db_prefix() . 'acc_account_history')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history`
            ADD COLUMN `vendor` INT(11) NULL;');
        }
    }
}
