<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_130 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        //Version 1.3.0

        add_option('acc_wh_stock_import_return_automatic_conversion', 0);
        add_option('acc_wh_stock_import_return_payment_account', 1);
        add_option('acc_wh_stock_import_return_deposit_to', 37);
        add_option('acc_wh_stock_export_profit_automatic_conversion', 1);
    }
}
