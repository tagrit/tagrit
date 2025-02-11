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
            'name' => 'Events Due',
            'icon' => 'fa fa-calendar-check',
            'position' => 5,
            'children' => []
        ];

        $menu['children'][] = [
            'slug' => 'events_due_dashboard',
            'name' => 'Dashboard',
            'href' => site_url('admin/' . EVENTS_DUE_MODULE_NAME . '/dashboard'),
            'icon' => 'fa fa-home',
            'position' => 10,
        ];

        $menu['children'][] = [
            'slug' => 'events_due_events',
            'name' => 'Events',
            'href' => site_url('admin/' . EVENTS_DUE_MODULE_NAME . '/events'),
            'icon' => 'fa fa-calendar-alt',
            'position' => 11,
        ];


        $menu['children'][] = [
            'slug' => 'event_registration',
            'name' => 'Event Registration',
            'href' => admin_url(EVENTS_DUE_MODULE_NAME . '/registrations'),
            'icon' => 'fa fa-calendar-plus',
            'position' => 12,
        ];

        $menu['children'][] = [
            'slug' => 'events_due_reports',
            'name' => 'Reports',
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

    private function create_permissions()
    {
    }
}

// Instantiate the module class to initialize it
new Events_Due_System();
