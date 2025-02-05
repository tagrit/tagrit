<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_018 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $files = [
            'hooks/cpanel.php',
            'libraries/Cpanel_api.php'
        ];
        foreach ($files as $file) {
            $file = __DIR__ . '/../' . $file;
            if (file_exists($file))
                @unlink($file);
        }

        add_option('perfex_saas_autolaunch_instance', 'new');
    }
    public function down()
    {
    }
}
