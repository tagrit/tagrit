<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_124 extends App_module_migration
{
    public function up()
    {        
        
        $CI = &get_instance();
                
        add_option('update_bank_account_v124', 0);
        if (get_option('update_bank_account_v124') == 0) {

            $CI->load->model('accounting/accounting_model');
            $CI->accounting_model->update_bank_account_v124();

            update_option('update_bank_account_v124', 1);
        }
    }
}
