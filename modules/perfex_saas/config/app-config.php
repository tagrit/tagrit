<?php

defined('BASEPATH') or exit('No direct script access allowed');

try {

    // Allow room for config customization and constants for saas
    $saas_custom_config_file = APPPATH . 'config/my_saas_config.php';
    if (file_exists($saas_custom_config_file))
        include_once($saas_custom_config_file);

    /**
     * Require the root helper file.
     * This helper is independent on the CI class and early required functions by saas are loaded from here.
     */
    if (!function_exists('perfex_saas_init')) {
        if (!@include_once(__DIR__ . '/../helpers/perfex_saas_core_helper.php'))
            throw new \Exception("Error loading perfex saas core", 1);
    }

    /**
     * Init perfex saas and detect the active tenant if any
     * This method call with set $GLOBALS[PERFEX_SAAS_MODULE_NAME . '_tenant'] and can be used henceforth as session is not ready for use here.
     */
    perfex_saas_init();


    /**
     * SaaS Initiated. Load tenants relative contstants including storage control.
     * We need this to ensure control of storage constant definitions
     */
    if (!defined('APP_MODULES_PATH') && !@include_once(__DIR__ . '/my_constants.php'))
        throw new \Exception("Error loading perfex saas my constants file", 1);

    // bootstraping successful
} catch (\Throwable $th) {

    exit("SaaS Boostraping error: " . $th->getMessage() . "<br/><br/>" . $th->getTraceAsString());
}
