<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_106 extends App_module_migration
{
     public function up()
     {
        $CI = &get_instance();
        
        add_option('ma_unsubscribe', 1);
     }
}
