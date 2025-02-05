<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Disable customer portal
Description: A module to disable customer portal.
Version: 1.0.1
Requires at least: 3.0.*
Author: ulutfa
Author URI: https://codecanyon.net/user/ulutfa
*/

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files('disable_customer_panel', ['disable_customer_panel']);

hooks()->add_action('after_clients_area_init', 'disable_customer_panel');

hooks()->add_action('clients_authentication_constructor', 'disable_customer_panel');

hooks()->add_filter('theme_menu_items', function ($navs) {

    if (isset($navs['register']))
        unset($navs['register']);

    return $navs;
});

function disable_customer_panel()
{
    $url = admin_url();
    header("Location: $url");
    exit();
}


// show warning when module is activated for SaaS
hooks()->add_action('before_start_render_dashboard_content', function () {
    if (function_exists('perfex_saas_is_tenant') && !perfex_saas_is_tenant()) {
        if (is_admin()) {
            $html = '<div class="col-md-12"><div class="alert alert-warning" font-medium>';
            $html .= '<h4>' . _l('saas_customer_portal_disabled') . '</h4>';
            $html .= '<p></p><p>' . _l('saas_customer_portal_disabled_message', admin_url('modules/deactivate/disable_customer_panel')) . '</p>';
            $html .= '</div></div>';
            echo $html;
        }
    }
});
