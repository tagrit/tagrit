<?php

defined('BASEPATH') or exit('No direct script access allowed');

/** Management of database auto upgrade */
hooks()->add_action('database_updated', function ($updateToVersion) {
    if (!perfex_saas_is_tenant()) perfex_saas_install();
}, PHP_INT_MAX);

// Tenant
if ($is_tenant) {
    // Auto upgrade database for the tenant when needed
    hooks()->add_action('app_init', function () {
        $CI = &get_instance();
        if ($CI->app->is_db_upgrade_required($CI->app->get_current_db_version())) {

            // Add this hook to apply custom redirection
            hooks()->add_action('database_updated', function ($updateToVersion) {
                update_option('last_updated_date', time());
                update_option('update_info_message', '');
                redirect(admin_url(''), 'refresh');
            }, PHP_INT_MAX);

            hooks()->do_action('pre_upgrade_database');
            $CI->app->upgrade_database();
            exit("");
        }
    });
}