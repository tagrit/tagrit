<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_016 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $key = 'perfex_saas_registered_global_active_modules';
        if (empty(get_option($key)))
            update_option('perfex_saas_registered_global_active_modules', '[]');

        perfex_saas_install();

        update_option('aside_menu_active', json_encode([]));
        hooks()->do_action('aside_menu_resetted');
    }
    public function down()
    {
    }
}
