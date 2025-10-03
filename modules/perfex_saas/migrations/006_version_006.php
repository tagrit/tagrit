<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_006 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        update_option('perfex_saas_enable_subdomain_input_on_signup_form', '1');
        update_option('perfex_saas_enable_customdomain_input_on_signup_form', '1');
    }

    public function down()
    {
    }
}
