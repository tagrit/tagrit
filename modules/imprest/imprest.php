<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Imprest Management
Description: System for Managing Petty Cashes for a Company
Version: 1.0
Author: Kevin Amayi
Requires at least: 2.3.*
*/


require_once 'helpers/migration_helper.php';

const IMPREST_MODULE_NAME = 'imprest';

// Run migrations when the module is loaded
run_module_migrations();


class Imprest_Management_System
{
    public function __construct()
    {
        $this->register_hooks();
    }

    private function register_hooks()
    {
        // Register uninstall hook
        register_uninstall_hook(IMPREST_MODULE_NAME, [$this, 'uninstall']);

        // Register activation hook
        register_activation_hook(IMPREST_MODULE_NAME, [$this, 'imprest_module_activation_hook']);

        // Register admin menu items
        hooks()->add_action('admin_init', [$this, 'init_menu_items_and_create_permissions']);

        // Register quick menu custom items
        hooks()->add_action('app_admin_quick_actions', [$this, 'add_quick_action']);
    }

    public function add_quick_action($actions)
    {
        $actions[] = [
            'name' => _l('Request Fund'),
            'url' => admin_url(IMPREST_MODULE_NAME . '/fund_requests/create'),
            'icon' => 'fa fa-hand-holding',
        ];
        return $actions;
    }

    public function uninstall()
    {
        require_once __DIR__ . '/uninstall.php';
    }

    public function imprest_module_activation_hook()
    {
        require_once __DIR__ . '/install.php';
    }

    public function init_menu_items_and_create_permissions()
    {
        $this->create_permissions();

        $CI = &get_instance();

        // Define the base menu
        $menu = [
            'slug' => IMPREST_MODULE_NAME,
            'name' => 'imprest',
            'icon' => 'fa fa-money-bill-wave',
            'position' => 5,
            'children' => []
        ];

        $menu['children'][] = [
            'slug' => 'impress-dashboard',
            'name' => 'Dashboard',
            'href' => site_url('admin/' . IMPREST_MODULE_NAME . '/dashboard'),
            'icon' => 'fa fa-home',
            'position' => 10,
        ];

        if (staff_can('manages_expense_categories', 'imprest-expense-categories')) {
            $menu['children'][] = [
                'slug' => 'expense_categories',
                'name' => 'Expense Categories',
                'href' => admin_url(IMPREST_MODULE_NAME . '/expense_categories'),
                'icon' => 'fa fa-tags',
                'position' => 12,
            ];
        }

        if (!staff_can('manages_expense_categories', 'imprest-expense-categories')) {
            $menu['children'][] = [
                'slug' => 'fund_request',
                'name' => 'Request Funds',
                'href' => admin_url(IMPREST_MODULE_NAME . '/fund_requests/create'),
                'icon' => 'fa fa-hand-holding',
                'position' => 13,
            ];
        }

        $menu['children'][] = [
            'slug' => 'fund_request_list',
            'name' => 'Fund Requests',
            'href' => admin_url(IMPREST_MODULE_NAME . '/fund_requests'),
            'icon' => 'fa fa-list',
            'position' => 14,
        ];

        if (staff_can('manages_expense_categories', 'imprest-expense-categories')) {
            $menu['children'][] = [
                'slug' => 'settings',
                'name' => 'Settings',
                'href' => admin_url(IMPREST_MODULE_NAME . '/settings/main'),
                'icon' => 'fa fa-cog',
                'position' => 15,
            ];
        }

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
        // Events permissions
        register_staff_capabilities('imprest-events', [
            'capabilities' => [
                'view_all_events' => 'View (Global)',
                'create_events' => 'Create',
                'edit_events' => 'Edit',
            ]
        ], 'Imprest Events');

        // Expense categories permissions
        register_staff_capabilities('imprest-expense-categories', [
            'capabilities' => [
                'manages_expense_categories' => 'Manage',
            ]
        ], 'Imprest Expense Categories');

        // Fund requests permissions
        register_staff_capabilities('imprest-fund-requests', [
            'capabilities' => [
                'view_all_fund_requests' => 'View (Global)',
                'approve_fund_requests' => 'Approve',
                'reject_fund_requests' => 'Reject',
            ]
        ], 'Imprest Fund Requests');

        // Fund reconciliations permissions
        register_staff_capabilities('imprest-fund-reconciliations', [
            'capabilities' => [
                'clear_fund_reconciliations' => 'Clear',
                'reject_fund_reconciliations' => 'Reject',
            ]
        ], 'Imprest Fund Reconciliations');
    }
}

// Instantiate the module class to initialize it
new Imprest_Management_System();
