<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_107 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        //Version 1.0.7

        add_option('acc_pur_order_automatic_conversion', 1);
        add_option('acc_pur_order_payment_account', 13);
        add_option('acc_pur_order_deposit_to', 80);

        add_option('acc_pur_payment_automatic_conversion', 1);
        add_option('acc_pur_payment_payment_account', 16);
        add_option('acc_pur_payment_deposit_to', 37);
    }
}
