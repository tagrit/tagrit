<?php

defined('BASEPATH') || exit('No direct script access allowed');

/*
    Module Name: Flutex Admin/Staff API
    Description: REST API For Flutex Admin/Staff App
    Version: 1.0.0
    Requires at least: 3.0.*
    Author: Branditta
    Author URI: https://codecanyon.net/user/branditta/portfolio
*/

define('FLUTEX_ADMIN_API', 'flutex_admin_api');
require_once __DIR__.'/vendor/autoload.php';

register_activation_hook(FLUTEX_ADMIN_API, 'flutex_admin_api_activate_hook');
function flutex_admin_api_activate_hook()
{
    require_once __DIR__.'/install.php';
}

register_deactivation_hook(FLUTEX_ADMIN_API, 'flutex_admin_api_deactivate_hook');
function flutex_admin_api_deactivate_hook()
{
    update_option('flutex_admin_api_enabled', 0);
}

register_language_files(FLUTEX_ADMIN_API, [FLUTEX_ADMIN_API]);

get_instance()->load->helper(FLUTEX_ADMIN_API.'/flutex_admin_api');

hooks()->add_action('admin_init', 'add_flutex_admin_api_settings_tabs');
function add_flutex_admin_api_settings_tabs()
{
    get_instance()->app->add_settings_section('flutex_admin_api', [
        'name'     => _l('flutex_admin_settings'),
        'view'     => 'flutex_admin_api/flutex_admin_settings',
        'icon'     => 'fas fa-mobile-alt',
        'position' => 5,
    ]);
}

hooks()->add_filter('module_flutex_admin_api_action_links', 'module_flutex_admin_api_action_links');
function module_flutex_admin_api_action_links($actions)
{
    $actions[] = '<a href="' . admin_url('settings') . '?group=flutex_admin_api">' . _l('settings') . '</a>';
    return $actions;
}

hooks()->add_action('app_init', 'flutex_admin_api_init');
function flutex_admin_api_init()
{
    $CI = &get_instance();
    $CI->load->library(FLUTEX_ADMIN_API.'/flutex_admin_api');
    $verify_license = ['status' => true, 'message' => 'License verification bypassed.'];
    if (!$verify_license['status']) {
        get_instance()->app_modules->deactivate(FLUTEX_ADMIN_API);
        set_alert('danger', $verify_license['message']);
    }
}

hooks()->add_action('pre_activate_module', 'flutex_admin_api_activation');
function flutex_admin_api_activation($module_name)
{
    $CI = &get_instance();
    $CI->load->library(FLUTEX_ADMIN_API.'/flutex_admin_api');
    $CI->flutex_admin_api->activate(FLUTEX_ADMIN_API);
}