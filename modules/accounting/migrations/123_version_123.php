<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_123 extends App_module_migration
{
    public function up()
    {        
        
        $CI = &get_instance();
        
        if (!$CI->db->field_exists('is_imported' ,db_prefix() . 'acc_transaction_bankings')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_transaction_bankings`
          ADD COLUMN `is_imported` INT(11) NOT NULL DEFAULT 0');
        }
    }
}
