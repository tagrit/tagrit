<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_009 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        add_option('perfex_saas_cron_cache', '');
        add_option('perfex_saas_tenants_seed_tables', '');
        add_option('perfex_saas_sensitive_options', '');
        perfex_saas_install();
    }
    public function down()
    {
    }
}
