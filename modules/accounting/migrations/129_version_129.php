<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_129 extends App_module_migration
{
    public function up()
    {        
        $CI = &get_instance();
        
        add_option('acc_pur_tax_automatic_conversion', 1);
        add_option('acc_pur_tax_payment_account', 13);
        add_option('acc_pur_tax_deposit_to', 29);

        if (!$CI->db->field_exists('purchase_payment_account' ,db_prefix() . 'acc_tax_mappings')) {
          $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_tax_mappings`
            ADD COLUMN `purchase_payment_account` INT(11) NOT NULL DEFAULT \'0\',
            ADD COLUMN `purchase_deposit_to` INT(11) NOT NULL DEFAULT \'0\';');
        }
    }
}
