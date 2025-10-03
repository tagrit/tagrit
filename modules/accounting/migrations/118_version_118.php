<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_118 extends App_module_migration
{
    public function up()
    {        
        $CI = &get_instance();
      
        if (!$CI->db->field_exists('credit_note_refund_payment_account' ,db_prefix() . 'acc_payment_mode_mappings')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_payment_mode_mappings`
            ADD COLUMN `credit_note_refund_payment_account` INT(11) NOT NULL DEFAULT \'0\',
            ADD COLUMN `credit_note_refund_deposit_to` INT(11) NOT NULL DEFAULT \'0\';');
        }
    }
}
