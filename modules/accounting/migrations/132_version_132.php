<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_132 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        //Version 1.3.2

        if (!$CI->db->field_exists('reference' ,db_prefix() . 'acc_journal_entries')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_journal_entries`
            ADD COLUMN `reference` varchar(100) NULL;');
        }
    }
}
