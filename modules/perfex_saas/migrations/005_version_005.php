<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_005 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        perfex_saas_uninstall();
        perfex_saas_install();
        update_option('perfex_saas_force_redirect_to_dashboard', '1');
    }

    public function down()
    {
    }
}
