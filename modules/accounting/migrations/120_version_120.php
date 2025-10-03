<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_120 extends App_module_migration
{
    public function up()
    {        
        add_option('acc_wh_stock_export_profit_payment_account', 66);
        add_option('acc_wh_stock_export_profit_deposit_to', 1);
    }
}
