<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_111 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        //Version 1.1.1

        add_option('acc_credit_note_automatic_conversion', 1);
        add_option('acc_credit_note_payment_account', 1);
        add_option('acc_credit_note_deposit_to', 13);
        
    }
}
