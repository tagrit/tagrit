<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Client Menu Setup
Description: Extend the default menu setup module to allow customizing client portal menu items.
Version: 1.0.1
Requires at least: 3.0.*
*/

define('MENU_SETUP_CLIENT_MODULE_NAME', 'menu_setup_client');

// Require the menu_setup module 
if (!defined('MENU_SETUP_MODULE_NAME')) {
    require_once module_dir_path('menu_setup', 'menu_setup.php');
}

$CI = &get_instance();

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(MENU_SETUP_CLIENT_MODULE_NAME, [MENU_SETUP_CLIENT_MODULE_NAME]);

// Check disabled, position and custom settings
hooks()->add_filter('theme_menu_items', 'theme_client_menu_custom_options', 999);

hooks()->add_filter('module_menu_setup_client_action_links', 'module_menu_setup_client_action_links');
hooks()->add_action('admin_init', 'menu_setup_client_init_menu_items');

/**
 * Add additional settings for this module in the module list area
 * @param  array $actions current actions
 * @return array
 */
function module_menu_setup_client_action_links($actions)
{
    $actions[] = '<a href="' . admin_url('menu_setup_client/client_menu') . '">' . _l('client_menu') . '</a>';

    return $actions;
}

/**
 * Register activation module hook
 */
register_activation_hook(MENU_SETUP_CLIENT_MODULE_NAME, 'menu_setup_client_activation_hook');

function menu_setup_client_activation_hook()
{
    add_option('menu_setup_client_client_menu_active', '[]');
}

/**
 * Init menu setup module menu items in setup in admin_init hook
 * @return null
 */
function menu_setup_client_init_menu_items()
{
    /**
     * If the logged in user is administrator, add custom menu in Setup
     */
    if (is_admin()) {
        $CI = &get_instance();

        $CI->app_menu->add_setup_children_item('menu-options', [
            'slug'     => 'client-menu-options',
            'name'     => _l('client_menu'),
            'href'     => admin_url('menu_setup_client/client_menu'),
            'position' => 50,
        ]);
    }
}

/**
 * Get the custom client menu settings
 *
 * @return object
 */
function menu_setup_client_options()
{
    return json_decode(get_option('menu_setup_client_client_menu_active'));
}

/**
 * Apply custom client settings to the client menu items.
 * Check is menu itema is disabled, add custom name and position.
 *
 * @param array $items
 * @return array
 */
function theme_client_menu_custom_options($items)
{
    $options = menu_setup_client_options();
    foreach ($items as $key => $item) {
        if (isset($options->{$item['slug']})) {
            if (
                isset($options->{$item['slug']}->disabled)
                && $options->{$item['slug']}->disabled === 'true'
            ) {
                // Main item is disabled
                unset($items[$key]);
            } else {
                // Update position
                $items[$key]['position'] = (int) $options->{$item['slug']}->position;

                // Main item has custom name
                if (!empty($options->{$item['slug']}->custom_name)) {
                    $items[$key]['name'] = $options->{$item['slug']}->custom_name;
                }
            }


            foreach ($item['children'] as $childKey => $child) {
                if (isset($options->{$item['slug']}->children->{$child['slug']})) {
                    if (
                        isset($options->{$item['slug']}->children->{$child['slug']}->disabled)
                        && $options->{$item['slug']}->children->{$child['slug']}->disabled === 'true'
                    ) {
                        // Is disabled
                        unset($items[$key]['children'][$childKey]);
                    } else {
                        // Update position
                        if (isset($options->{$item['slug']}->children->{$child['slug']})) {
                            $items[$key]['children'][$childKey]['position'] = (int) $options->{$item['slug']}->children->{$child['slug']}->position;
                        }

                        // Has custom name
                        if (!empty($options->{$item['slug']}->children->{$child['slug']}->custom_name)) {
                            $items[$key]['children'][$childKey]['icon'] = $options->{$item['slug']}->children->{$child['slug']}->custom_name;
                        }
                    }
                }
            }
        }
    }
    return $items;
}

function menu_setup_client_apply_items_position($items, $options)
{
    return _apply_menu_items_position($items, $options);
}
