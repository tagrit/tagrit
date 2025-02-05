<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_011 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        add_option('perfex_saas_enable_client_bridge', '0');
        add_option('perfex_saas_enable_instance_switch', '0');
        perfex_saas_install();
    }
    public function down()
    {
    }
}
