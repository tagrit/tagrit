<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Contact Role
Description: A module to manage roles for contacts.
Version: 1.0.0
Requires at least: 3.0.*
Author: ulutfa
Author URI: https://codecanyon.net/user/ulutfa
*/

defined('CONTACT_ROLE_MODULE') or define('CONTACT_ROLE_MODULE', 'contact_role');



$CI = &get_instance();

/**
 * Load the models
 */
$CI->load->model(CONTACT_ROLE_MODULE . '/' . CONTACT_ROLE_MODULE . '_model');

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(CONTACT_ROLE_MODULE, [CONTACT_ROLE_MODULE]);

/**
 * Register activation module hook
 */
register_activation_hook(CONTACT_ROLE_MODULE, function () {

    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
});


// Get the setup role menu and modify
hooks()->add_action('admin_init', function () {

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view' => _l('perfex_saas_permission_view'),
        'create' => _l('perfex_saas_permission_create'),
        'edit' => _l('perfex_saas_permission_edit'),
        'delete' => _l('perfex_saas_permission_delete'),
    ];

    register_staff_capabilities(CONTACT_ROLE_MODULE, $capabilities, _l(CONTACT_ROLE_MODULE));

    $CI = &get_instance();
    if (has_permission(CONTACT_ROLE_MODULE, '', 'view')) {

        //$CI->app_menu->add_setup_children_item('roles',

        $existing_roles = $CI->app_menu->get_setup_menu_items()['roles'];
        if (!empty($existing_roles)) {
            $existing_roles = isset($existing_roles[0]) ? $existing_roles : [$existing_roles];
            if (count($existing_roles) == 1) {
                $existing_roles[0]['name'] = _l('acs_staff_roles');
            }

            foreach ($existing_roles as $_role) {
                $CI->app_menu->add_setup_children_item(
                    'roles',
                    $_role
                );
            }
        }

        $CI->app_menu->add_setup_children_item('roles', [
            'slug' => CONTACT_ROLE_MODULE,
            'name' => _l(CONTACT_ROLE_MODULE . '_staff_menu'),
            'icon' => '',
            'position' => 55,
            'href' => admin_url(CONTACT_ROLE_MODULE),
        ]);
    }
});

// Inject role selection to contact modal view
hooks()->add_action('after_contact_modal_content_loaded', function () {
    $CI = &get_instance();
    $data = ['roles' => $CI->contact_role_model->get()];
    $CI->load->view(CONTACT_ROLE_MODULE . '/includes/contact_modal_role', $data);
});

// Auto click view a contact info modal directly from the role form view
hooks()->add_action('app_admin_footer', function () {
    if (!empty($_GET['auto_click_contact_id']) && !empty($_GET['client_id'])) {
        echo '<script>document.addEventListener("DOMContentLoaded", function(){ contact(' . (int)$_GET['client_id'] . ',' . (int)$_GET['auto_click_contact_id'] . ') });</script>';
    }
});


// Ensure contact_role_id field not added to clients fields
hooks()->add_filter('contact_columns', function ($fields) {
    $fields[] = 'contact_role_id';
    return $fields;
});
hooks()->add_filter('before_client_added', function ($data) {
    if (isset($data['contact_role_id']))
        unset($data['contact_role_id']);
    return $data;
});