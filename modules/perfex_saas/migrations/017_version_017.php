<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_017 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        perfex_saas_install();
    }
    public function down()
    {
    }
}
