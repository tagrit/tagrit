<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_104 extends App_module_migration
{
     public function up()
     {
        $CI = &get_instance();
        
        add_option('convert_email_template_v104', 0);
        if (get_option('convert_email_template_v104') == 0) {

            $CI->load->model('ma/ma_model');
            $CI->ma_model->convert_email_template_v104();

            update_option('convert_email_template_v104', 1);
        }
     }
}
