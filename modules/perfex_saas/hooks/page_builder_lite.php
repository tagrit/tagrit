<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Check for use of page builder lite.
 */
if (!function_exists('perfex_saas_page_builder_filter_lite')) {
    function perfex_saas_page_builder_filter_lite($modules)
    {
        $module_id = 'page_builder';

        if (isset($modules[$module_id])) {
            if (!file_exists(APP_MODULES_PATH . $module_id . '/views/builders/grapejs.php'))
                unset($modules[$module_id]);
        }
        return $modules;
    }
}

hooks()->add_filter('perfex_saas_module_list_filter', 'perfex_saas_page_builder_filter_lite');

if ($is_tenant) {

    hooks()->add_filter('modules_to_load', 'perfex_saas_page_builder_filter_lite');
}