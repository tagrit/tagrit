<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_004 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        add_option('perfex_saas_control_client_menu', '1');
    }

    public function down()
    {
    }
}
