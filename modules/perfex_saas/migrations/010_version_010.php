<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_010 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        perfex_saas_install();

        // update package option to 'yes_3' show limit with module
        $CI = &get_instance();
        $packages = $CI->perfex_saas_model->packages();
        foreach ($packages as $key => $package) {
            if (!isset($package->metadata)) continue;
            $package->metadata->show_limits_on_package = "yes_3";
            $CI->perfex_saas_model->add_or_update('packages', ['id' => $package->id, 'metadata' => json_encode($package->metadata)]);
        }
    }
    public function down()
    {
    }
}
