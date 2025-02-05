<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Fleet Management
Description: One place to manage and maintain your fleet
Version: 1.0.0
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
 */

define('FLEET_MODULE_NAME', 'fleet');
define('FLEET_REVISION', 100);
define('FLEET_MODULE_UPLOAD_FOLDER', module_dir_path(FLEET_MODULE_NAME, 'uploads'));

hooks()->add_action('admin_init', 'fleet_module_init_menu_items');
hooks()->add_action('admin_init', 'fleet_permissions');
hooks()->add_action('app_admin_head', 'fleet_add_head_components');
hooks()->add_action('app_admin_footer', 'fleet_add_footer_components');
hooks()->add_action('customers_navigation_end', 'fleet_module_init_client_menu_items');
hooks()->add_action('app_customers_head', 'fleet_client_add_head_components');
hooks()->add_action('after_cron_run', 'fleet_inspection_schedule');
hooks()->add_action('fleet_init',FLEET_MODULE_NAME.'_appint');
hooks()->add_action('pre_activate_module', FLEET_MODULE_NAME.'_preactivate');
hooks()->add_action('pre_deactivate_module', FLEET_MODULE_NAME.'_predeactivate');

/**
 * Register activation module hook
 */
register_activation_hook(FLEET_MODULE_NAME, 'fleet_module_activation_hook');

$CI = &get_instance();
$CI->load->helper(FLEET_MODULE_NAME . '/fleet');

/**
 * fleet add head components
 * @return
 */
function fleet_add_head_components() {
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    if (!(strpos($viewuri, '/admin/fleet/parts') === false)) {
        echo '<link href="' . module_dir_url(FLEET_MODULE_NAME, 'assets/css/part_style.css')  .'"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, '/admin/fleet/booking_detail') === false)) {
        echo '<link href="' . module_dir_url(FLEET_MODULE_NAME, 'assets/css/client_style.css')  .'"  rel="stylesheet" type="text/css" />';
    }
}

/**
 * fleet add footer components
 * @return
 */
function fleet_add_footer_components() {
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if (!(strpos($viewuri, 'admin/fleet') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/tinymce_init.js') .'"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/settings?group=vehicle_groups') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/settings/vehicle_groups.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/settings?group=vehicle_types') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/settings/vehicle_types.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/settings?group=inspection_forms') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/settings/inspection_forms.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/settings?group=criterias') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/settings/criterias.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/settings?group=insurance_types') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/settings/insurance_types.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/settings?group=insurance_categories') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/settings/insurance_categories.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/settings?group=insurance_company') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/settings/insurance_company.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/settings?group=insurance_status') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/settings/insurance_status.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/settings?group=part_types') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/settings/part_types.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/settings?group=part_groups') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/settings/part_groups.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/vehicles') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/vehicles/manage.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/drivers') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/drivers/manage.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/maintenances') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/maintenances/manage.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/garages') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/garages/manage.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/inspections') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/inspections/manage.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/parts') === false)) { 
         echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.jquery.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/booking_detail') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/bookings/booking_detail.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/garage_detail') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/garages/garage_detail.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/logbook_detail') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/work_performances/logbook_detail.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/work_order') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/work_orders/work_order.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/work_order_detail') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/work_orders/work_order_detail.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/transactions?group=invoices') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/transactions/invoices.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/transactions?group=expenses') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/transactions/expenses.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/fleet/dashboard') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/fleet/fuel_report') === false) || !(strpos($viewuri, '/admin/fleet/maintenance_report') === false) || !(strpos($viewuri, '/admin/fleet/event_report') === false) || !(strpos($viewuri, '/admin/fleet/work_order_report') === false) || !(strpos($viewuri, '/admin/fleet/work_performance_report') === false) || !(strpos($viewuri, '/admin/fleet/income_and_expense_report') === false) || !(strpos($viewuri, '/admin/fleet/rp_') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/fuel_report') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/fuel_report.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/maintenance_report') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/maintenance_report.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/event_report') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/event_report.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/work_order_report') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/work_order_report.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/work_performance_report') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/work_performance_report.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/rp_operating_cost_summary') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/operating_cost_summary_report.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/rp_total_cost_trend') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/total_cost_trend.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/rp_status_changes') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/status_changes.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/rp_group_changes') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/group_changes.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/rp_vehicle_assignment_log') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/vehicle_assignment_log.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/rp_vehicle_assignment_summary') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/vehicle_assignment_summary.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/rp_inspection_submissions_list') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/inspection_submissions_list.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/rp_inspection_submissions_summary') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/inspection_submissions_summary.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/rp_vehicle_list') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/vehicle_list.js') . '"></script>';
    }
    
    if (!(strpos($viewuri, '/admin/fleet/rp_vehicle_renewal_reminders') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/vehicle_renewal_reminders.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/rp_inspection_failures_list') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/inspection_failures_list.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/fleet/rp_inspection_schedules') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/inspection_schedules.js') . '"></script>';
    }
    
    if (!(strpos($viewuri, '/admin/fleet/rp_cost_meter_trend') === false)) {
        echo '<script src="' . module_dir_url(FLEET_MODULE_NAME, 'assets/js/reports/cost_meter_trend.js') . '"></script>';
    }
}

/**
 * fleet module activation_hook
 * @return
 */
function fleet_module_activation_hook() {
    $CI = &get_instance();
    require_once __DIR__ . '/install.php';
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(FLEET_MODULE_NAME, [FLEET_MODULE_NAME]);

/**
 * Init fleet module menu items in setup in admin_init hook
 * @return null
 */
function fleet_module_init_menu_items() {
    $CI = &get_instance();
    if (has_permission('fleet_dashboard', '', 'view') || has_permission('fleet_vehicle', '', 'view') || has_permission('fleet_transaction', '', 'view') || has_permission('fleet_driver', '', 'view') || has_permission('fleet_work_performance', '', 'view') || has_permission('fleet_benefit_and_penalty', '', 'view') || has_permission('fleet_event', '', 'view') || has_permission('fleet_work_orders', '', 'view') || has_permission('fleet_garage', '', 'view') || has_permission('fleet_maintenance', '', 'view') || has_permission('fleet_fuel', '', 'view') || has_permission('fleet_part', '', 'view') || has_permission('fleet_insurance', '', 'view') || has_permission('fleet_inspection', '', 'view') || has_permission('fleet_bookings', '', 'view') || has_permission('fleet_report', '', 'view') || has_permission('fleet_setting', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('fleet', [
            'name' => _l('fleet'),
            'icon' => 'fa fa-truck',
            'position' => 30,
        ]);
        if (has_permission('fleet_dashboard', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-dashboard',
                'name' => _l('dashboard'),
                'icon' => 'fa fa-bar-chart',
                'href' => admin_url('fleet/dashboard'),
                'position' => 1,
            ]);
        }
        
        if (has_permission('fleet_vehicle', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-vehicle',
                'name' => _l('vehicle'),
                'icon' => 'fa fa-truck',
                'href' => admin_url('fleet/vehicles'),
                'position' => 2,
            ]);
        }

        if (has_permission('fleet_transaction', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-transaction',
                'name' => _l('transactions'),
                'icon' => 'fa fa-file-text',
                'href' => admin_url('fleet/transactions?group=invoices'),
                'position' => 2,
            ]);
        }

        if (has_permission('fleet_driver', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-driver',
                'name' => _l('driver'),
                'icon' => 'fa fa-user-circle',
                'href' => admin_url('fleet/drivers'),
                'position' => 3,
            ]);
        }

        if (has_permission('fleet_work_performance', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-work-performance',
                'name' => _l('work_performance'),
                'icon' => 'fa fa-line-chart',
                'href' => admin_url('fleet/work_performances'),
                'position' => 4,
            ]);
        }

        if (has_permission('fleet_benefit_and_penalty', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-driver-benefits-and-penalty',
                'name' => _l('benefit_and_penalty'),
                'icon' => 'fa fa-newspaper',
                'href' => admin_url('fleet/benefit_and_penalty'),
                'position' => 5,
            ]);
        }

        if (has_permission('fleet_event', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-event',
                'name' => _l('event'),
                'icon' => 'fa fa-newspaper',
                'href' => admin_url('fleet/events'),
                'position' => 6,
            ]);
        }

        if (has_permission('fleet_work_orders', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-regular-service',
                'name' => _l('work_orders'),
                'icon' => 'fa fa-money-bill',
                'href' => admin_url('fleet/work_orders'),
                'position' => 6,
            ]);
        }

        if (has_permission('fleet_garage', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-garage',
                'name' => _l('garage'),
                'icon' => 'fa fa-home',
                'href' => admin_url('fleet/garages'),
                'position' => 6,
            ]);
        }

        if (has_permission('fleet_maintenance', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-maintenance',
                'name' => _l('maintenance'),
                'icon' => 'fa fa-wrench',
                'href' => admin_url('fleet/maintenances'),
                'position' => 6,
            ]);
        }

        if (has_permission('fleet_fuel', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-fuel',
                'name' => _l('fuel'),
                'icon' => 'fa fa-gas-pump',
                'href' => admin_url('fleet/fuels'),
                'position' => 6,
            ]);
        }

        if (has_permission('fleet_part', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-parts',
                'name' => _l('parts'),
                'icon' => 'fa fa-newspaper',
                'href' => admin_url('fleet/parts'),
                'position' => 6,
            ]);
        }

        if (has_permission('fleet_insurance', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-insurance',
                'name' => _l('insurances'),
                'icon' => 'fa fa-file-text',
                'href' => admin_url('fleet/insurances'),
                'position' => 6,
            ]);
        }

        if (has_permission('fleet_inspection', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-inspection',
                'name' => _l('inspections'),
                'icon' => 'fa fa-file-text',
                'href' => admin_url('fleet/inspections'),
                'position' => 6,
            ]);
        }

        if (has_permission('fleet_bookings', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-bookings',
                'name' => _l('bookings'),
                'icon' => 'fa fa-cart-plus',
                'href' => admin_url('fleet/bookings'),
                'position' => 6,
            ]);
        }

        if (has_permission('fleet_report', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-report',
                'name' => _l('report'),
                'icon' => 'fa fa-bar-chart',
                'href' => admin_url('fleet/reports'),
                'position' => 6,
            ]);
        }

        if (has_permission('fleet_setting', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('fleet', [
                'slug' => 'fleet-settings',
                'name' => _l('settings'),
                'icon' => 'fa fa-cog',
                'href' => admin_url('fleet/settings?group=vehicle_groups'),
                'position' => 60,
            ]);
        }
    }
}

/**
 * Init fleet module permissions in setup in admin_init hook
 */
function fleet_permissions() {

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
    ];
    register_staff_capabilities('fleet_dashboard', $capabilities, _l('fleet_dashboard'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('fleet_vehicle', $capabilities, _l('fleet_vehicle'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
    ];
    register_staff_capabilities('fleet_transaction', $capabilities, _l('fleet_transaction'));
    
    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('fleet_driver', $capabilities, _l('fleet_driver'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('fleet_work_performance', $capabilities, _l('fleet_work_performance'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('fleet_benefit_and_penalty', $capabilities, _l('fleet_benefit_and_penalty'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('fleet_event', $capabilities, _l('fleet_event'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('fleet_work_orders', $capabilities, _l('fleet_work_orders'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('fleet_garage', $capabilities, _l('fleet_garage'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('fleet_maintenance', $capabilities, _l('fleet_maintenance'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('fleet_fuel', $capabilities, _l('fleet_fuel'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('fleet_part', $capabilities, _l('fleet_parts'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('fleet_insurance', $capabilities, _l('fleet_insurance'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('fleet_inspection', $capabilities, _l('fleet_inspection'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('fleet_bookings', $capabilities, _l('fleet_bookings'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
    ];
    register_staff_capabilities('fleet_report', $capabilities, _l('fleet_report'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('fleet_setting', $capabilities, _l('fleet_setting'));
}



/**
 * Init file sharing module menu items in setup in customers_navigation_end hook
 */
function fleet_module_init_client_menu_items()
{
    $menu = '';
    if (is_client_logged_in()) {
        $menu .= '<li class="customers-nav-item-fleet-booking">
                  <a href="' . site_url('fleet/fleet_client') . '">
                    <i class=""></i> '
        . _l('fleet_booking') . '
                  </a>
               </li>';
    }
    echo new_html_entity_decode($menu);
}


function fleet_client_add_head_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if(!(strpos($viewuri, '/fleet') === false)){
        echo '<link href="' . base_url('modules/fleet/assets/css/client_style.css') .'"  rel="stylesheet" type="text/css" />';
    }
}

function fleet_inspection_schedule($manually)
{
        $CI = &get_instance();

        $CI->load->model('fleet/fleet_model');
        $CI->fleet_model->fleet_inspection_schedule();
}

function fleet_appint(){
    $CI = & get_instance();    
    require_once 'libraries/gtsslib.php';
    $fleet_api = new FleetLic();
    $fleet_gtssres = $fleet_api->verify_license(true);    
    if(!$fleet_gtssres || ($fleet_gtssres && isset($fleet_gtssres['status']) && !$fleet_gtssres['status'])){
         $CI->app_modules->deactivate(FLEET_MODULE_NAME);
        set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        redirect(admin_url('modules'));
    }    
}

function fleet_preactivate($module_name){
    if ($module_name['system_name'] == FLEET_MODULE_NAME) {             
        require_once 'libraries/gtsslib.php';
        $fleet_api = new FleetLic();
        $fleet_gtssres = $fleet_api->verify_license();          
        if(!$fleet_gtssres || ($fleet_gtssres && isset($fleet_gtssres['status']) && !$fleet_gtssres['status'])){
             $CI = & get_instance();
            $data['submit_url'] = $module_name['system_name'].'/gtsverify/activate'; 
            $data['original_url'] = admin_url('modules/activate/'.FLEET_MODULE_NAME); 
            $data['module_name'] = FLEET_MODULE_NAME; 
            $data['title'] = "Module License Activation"; 
            echo $CI->load->view($module_name['system_name'].'/activate', $data, true);
            exit();
        }        
    }
}

function fleet_predeactivate($module_name){
    if ($module_name['system_name'] == FLEET_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $fleet_api = new FleetLic();
        $fleet_api->deactivate_license();
    }
}