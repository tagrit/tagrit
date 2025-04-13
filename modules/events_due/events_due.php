<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Events Due
Description: System for Training Events
Version: 1.0
Author: Kevin Amayi & Kefa Hamisi
Requires at least: 2.3.*
*/

const EVENTS_DUE_MODULE_NAME = 'events_due';

class Events_Due_System
{
    public function __construct()
    {
        $this->register_hooks();
    }

    private function register_hooks()
    {
        // Register uninstall hook
        register_uninstall_hook(EVENTS_DUE_MODULE_NAME, [$this, 'uninstall']);

        // Register activation hook
        register_activation_hook(EVENTS_DUE_MODULE_NAME, [$this, 'events_due_module_activation_hook']);

        // Register admin menu items
        hooks()->add_action('admin_init', [$this, 'init_menu_items_and_create_permissions']);

        // Corrected hooks to use [$this, 'method_name'] instead of a plain function name
        hooks()->add_action('app_admin_head', [$this, 'events_due_head_components']);
        hooks()->add_action('app_admin_footer', [$this, 'events_due_footer_components']);
        
        // Global css
        // hooks()->add_action('app_admin_head', 'global_styles');
    }

    /**
     * Add head components
     */
    public function events_due_head_components()
    {
        $viewuri = $_SERVER['REQUEST_URI'];


        //events css
        if (strpos($viewuri, 'admin/events_due/events') !== false) {
            echo '<link href="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/css/events/list_events.css') . '" rel="stylesheet" type="text/css" />';
        }

        if (strpos($viewuri, 'admin/events_due/events/create') !== false) {
            echo '<link href="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/css/events/create_event.css') . '" rel="stylesheet" type="text/css" />';
        }

        if (strpos($viewuri, 'admin/events_due/events/edit') !== false) {
            echo '<link href="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/css/events/edit_event.css') . '" rel="stylesheet" type="text/css" />';
        }

        if (strpos($viewuri, 'admin/events_due/events/view') !== false) {
            echo '<link href="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/css/events/view_event.css') . '" rel="stylesheet" type="text/css" />';
        }


        //registration css
        if (strpos($viewuri, 'admin/events_due/registrations') !== false) {
            echo '<link href="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/css/registrations/create_registrations.css') . '" rel="stylesheet" type="text/css" />';
        }

        // dashboard css
        if (strpos($viewuri, 'admin/events_due/dashboard') !== false) {
            echo '<link href="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/css/dashboard.css') . '" rel="stylesheet" type="text/css" />';
        }

        //reports css
        if (strpos($viewuri, 'admin/events_due/reports/main') !== false) {
            echo '<link href="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/css/reports.css') . '" rel="stylesheet" type="text/css" />';
        }

        //settings css
        if (strpos($viewuri, 'admin/events_due/settings') !== false) {
            echo '<link href="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/css/settings.css') . '" rel="stylesheet" type="text/css" />';
        }
        if (strpos($viewuri, 'admin/events_due') !== false) {
            echo '<link href="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/css/styles.css') . '" rel="stylesheet" type="text/css" />';

        }

    }


    public function events_due_footer_components()
    {
        $viewuri = $_SERVER['REQUEST_URI'];

        //events js
        if (strpos($viewuri, 'admin/events_due/events') !== false) {
            echo '<script src="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/js/events/list_events.js') . '"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>';
        }

        if (strpos($viewuri, 'admin/events_due/events/create') !== false) {
            echo '<script src="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/js/events/create_event.js') . '"></script>';
        }

        if (strpos($viewuri, 'admin/events_due/events/edit') !== false) {
            echo '<script src="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/js/events/edit_event.js') . '"></script>';
        }

        //registration js
        if (strpos($viewuri, 'admin/events_due/registrations') !== false) {
            echo '<script defer src="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/js/registrations/create_registrations.js') . '"></script>';
        }

        //dashboard js
        if (strpos($viewuri, 'admin/events_due/dashboard') !== false) {
            echo '<script src="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/js/dashboard.js') . '"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>';
        }

        //reports js
        if (strpos($viewuri, 'admin/events_due/reports/main') !== false) {
            echo '<script src="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/js/reports.js') . '"></script>';
        }

        //settings js
        if (strpos($viewuri, 'admin/events_due/settings') !== false) {
            echo '<script src="' . module_dir_url(EVENTS_DUE_MODULE_NAME, 'assets/js/settings.js') . '"></script>';
        }

    }

    public function uninstall()
    {
        require_once __DIR__ . '/uninstall.php';
    }

    public function events_due_module_activation_hook()
    {
        require_once __DIR__ . '/install.php';
    }

    public function init_menu_items_and_create_permissions()
    {
        $this->create_permissions();

        $CI = &get_instance();

        // Define the base menu
        $menu = [
            'slug' => EVENTS_DUE_MODULE_NAME,
            'name' => 'Events',
            'icon' => 'fa fa-calendar-check',
            'position' => 5,
            'children' => []
        ];

        $menu['children'][] = [
            'slug' => 'events_due_dashboard',
            'name' => 'Dashboard',
            'href' => site_url('admin/' . EVENTS_DUE_MODULE_NAME . '/dashboard'),
            'icon' => 'fa fa-chart-pie',
            'position' => 10,
        ];

        $menu['children'][] = [
            'slug' => 'events_due_events',
            'name' => 'View Events',
            'href' => site_url('admin/' . EVENTS_DUE_MODULE_NAME . '/events'),
            'icon' => 'fa fa-calendar-alt',
            'position' => 11,
        ];


        $menu['children'][] = [
            'slug' => 'event_registration',
            'name' => 'Event Registration',
            'href' => admin_url(EVENTS_DUE_MODULE_NAME . '/registrations/create'),
            'icon' => 'fa fa-calendar-plus',
            'position' => 12,
        ];

        $menu['children'][] = [
            'slug' => 'events_due_reports',
            'name' => 'Client Records',
            'href' => admin_url(EVENTS_DUE_MODULE_NAME . '/reports/main'),
            'icon' => 'fa fa-file-alt',
            'position' => 13,
        ];

        $menu['children'][] = [
            'slug' => 'settings',
            'name' => 'Settings',
            'href' => admin_url(EVENTS_DUE_MODULE_NAME . '/settings/main'),
            'icon' => 'fa fa-cog',
            'position' => 14,
        ];

        // Add main menu
        $CI->app_menu->add_sidebar_menu_item($menu['slug'], [
            'name' => $menu['name'],
            'icon' => $menu['icon'],
            'position' => $menu['position'],
        ]);

        // Add child menu items
        foreach ($menu['children'] as $menu_item) {
            $this->add_menu_item_with_children($CI, $menu_item, $menu['slug']);
        }
    }

    private function add_menu_item_with_children($CI, $menu_item, $parent_slug)
    {
        $CI->app_menu->add_sidebar_children_item($parent_slug, [
            'slug' => $menu_item['slug'],
            'name' => $menu_item['name'],
            'href' => $menu_item['href'] ?? '#',
            'icon' => $menu_item['icon'] ?? '',
            'position' => $menu_item['position'] ?? 10,
        ]);

        if (!empty($menu_item['children'])) {
            foreach ($menu_item['children'] as $child_item) {
                $this->add_menu_item_with_children($CI, $child_item, $menu_item['slug']);
            }
        }
    }

    private function create_permissions() {}
}

// Instantiate the module class to initialize it
new Events_Due_System();
