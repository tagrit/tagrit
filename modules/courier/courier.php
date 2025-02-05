<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Courier 
Description: Integrated Transportation System for Freight Shipping, Courier Services, and Logistics
Version: 1.0
Author: Kefa Hamisi and Kevin Amayi
Requires at least: 2.3.*
*/

const COURIER_MODULE_NAME = 'courier';
const CONFIG_FILE = 'config';

class Courier_Logistic_System {

    public function __construct() {
        $this->register_hooks();
    }

    private function register_hooks() {
        // Register uninstall hook
        register_uninstall_hook(COURIER_MODULE_NAME, [$this, 'uninstall']);

        // Register activation hook
        register_activation_hook(COURIER_MODULE_NAME, [$this, 'courier_module_activation_hook']);

        // Register admin menu items
        hooks()->add_action('admin_init', [$this, 'init_menu_items_and_create_permissions']);
    }

    public function uninstall() {
        require_once __DIR__ . '/uninstall.php';
    }

    public function courier_module_activation_hook() {
        require_once __DIR__ . '/install.php';
    }

    public function init_menu_items_and_create_permissions() {

        // Create permissions
        $this->create_permissions();

        $CI =& get_instance();
        $CI->load->model('courier/Driver_model');

        // Get current user role
        $user_id = get_staff_user_id();
        $user_role = $CI->Driver_model->get_staff_role($user_id); // Assuming 'get_staff_role' returns the role name or role id

        // Define the full menu
        $menu = [
            'slug' => 'courier-management',
            'name' => 'Courier',
            'icon' => 'fa fa-cubes',
            'position' => 5,
            'children' => []
        ];

        // Check if the user's role is "Fleet: Driver"
        if ($user_role === 'Fleet: Driver') {
            // Only show Pickups for drivers
            $menu['children'][] = [
                'slug' => 'pickups',
                'name' => 'Pickups',
                'href' => admin_url(COURIER_MODULE_NAME . '/pickups/main'),
                'icon' => 'fa fa-truck',
                'position' => 11,
            ];
        } else {
            // Full menu for other roles
            $menu['children'] = [
                // Dashboard menu
                [
                    'slug' => 'dashboard',
                    'name' => 'Dashboard',
                    'href' => admin_url(COURIER_MODULE_NAME . '/dashboard'),
                    'icon' => 'fa fa-home',
                    'position' => 10,
                ],
                // Shipments menu
                [
                    'slug' => 'shipments',
                    'name' => 'Shipments',
                    'href' => admin_url(COURIER_MODULE_NAME . '/shipments/main'),
                    'icon' => 'fa fa-globe',
                    'position' => 11,
                ],
                // Pickups menu
                [
                    'slug' => 'pickups',
                    'name' => 'Pickups',
                    'href' => admin_url(COURIER_MODULE_NAME . '/pickups/main'),
                    'icon' => 'fa fa-truck',
                    'position' => 12,
                ],
                // Courier Companies menu
                [
                    'slug' => 'courier_companies',
                    'name' => 'Courier Companies',
                    'href' => admin_url(COURIER_MODULE_NAME . '/companies/main'),
                    'icon' => 'fa fa-building',
                    'position' => 13,
                ],
                // Manifest menu
                [
                    'slug' => 'manifests',
                    'name' => 'Manifests',
                    'href' => admin_url(COURIER_MODULE_NAME . '/manifests'),
                    'icon' => 'fa fa-file',
                    'position' => 14,
                ],
                // Agents menu
                [
                    'slug' => 'agents',
                    'name' => 'Agents',
                    'href' => admin_url(COURIER_MODULE_NAME . '/agents/main'),
                    'icon' => 'fa fa-users',
                    'position' => 15,
                ],
                // Client Portal menu
                [
                    'slug' => 'client_portal',
                    'name' => 'Client Portal',
                    'href' => base_url('courier/tracking'),
                    'icon' => 'fa fa-user',
                    'position' => 16,
                ],
                // Settings menu
                [
                    'slug' => 'settings',
                    'name' => 'Settings',
                    'href' => admin_url(COURIER_MODULE_NAME . '/settings/main'),
                    'icon' => 'fa fa-cogs',
                    'position' => 17,
                ],
            ];
        }

        // Add Courier Management menu item
        $CI->app_menu->add_sidebar_menu_item($menu['slug'], [
            'name' => $menu['name'],
            'icon' => $menu['icon'],
            'position' => $menu['position'],
        ]);

        // Add children menu items under Courier Management
        foreach ($menu['children'] as $menu_item) {
            $this->add_menu_item_with_children($CI, $menu_item, $menu['slug']);
        }
    }

    private function add_menu_item_with_children($CI, $menu_item, $parent_slug) {
        $CI->app_menu->add_sidebar_children_item($parent_slug, [
            'slug' => $menu_item['slug'],
            'name' => $menu_item['name'],
            'href' => isset($menu_item['href']) ? $menu_item['href'] : '#',
            'icon' => isset($menu_item['icon']) ? $menu_item['icon'] : '',
            'position' => isset($menu_item['position']) ? $menu_item['position'] : 10,
        ]);

        if (isset($menu_item['children'])) {
            foreach ($menu_item['children'] as $child_item) {
                $this->add_menu_item_with_children($CI, $child_item, $menu_item['slug']);
            }
        }
    }


    private function create_permissions(){
        $config = [];

        //shipments permissions
        $config['capabilities'] = [
            'view_all_shipments'   => 'View (Global)'
        ];
        register_staff_capabilities('courier-shipments',$config, _l('Shipments'));

        //pickups permission
        $config['capabilities'] = [
            'view_all_pickups'   => 'View (Global)'
        ];
        register_staff_capabilities('courier-pickups',$config, _l('Pickups'));

    }
}

// Instantiate the module class to initialize it
new Courier_Logistic_System();
