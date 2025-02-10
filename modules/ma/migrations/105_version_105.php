<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_105 extends App_module_migration
{
     public function up()
     {
        $CI = &get_instance();
        
        add_option('ma_lead_required_phone', 0);
     }
}
