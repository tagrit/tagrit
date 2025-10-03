<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Perfex SAAS
Description: Simple comprehensive module to convert Perfex CRM to SAAS, multi-tenancy or multi-company
Version: 0.3.4
Requires at least: 3.1.*
Author: ulutfa
Author URI: https://codecanyon.net/user/ulutfa
*/
defined('PERFEX_SAAS_VERSION_NUMBER') or define('PERFEX_SAAS_VERSION_NUMBER', '0.3.4');

// Global common module constants
require_once('config/constants.php');

$CI = &get_instance();

/**
 * Load models
 */
$CI->load->model(PERFEX_SAAS_MODULE_NAME . '/' . PERFEX_SAAS_MODULE_NAME . '_model');
$CI->load->model(PERFEX_SAAS_MODULE_NAME . '/' . PERFEX_SAAS_MODULE_NAME . '_stripe_model');
$CI->load->model(PERFEX_SAAS_MODULE_NAME . '/' . PERFEX_SAAS_MODULE_NAME . '_cron_model');

/**
 * Load the module helper
 */
$CI->load->helper(PERFEX_SAAS_MODULE_NAME . '/' . PERFEX_SAAS_MODULE_NAME);
$CI->load->helper(PERFEX_SAAS_MODULE_NAME . '/' . PERFEX_SAAS_MODULE_NAME . '_core');
$CI->load->helper(PERFEX_SAAS_MODULE_NAME . '/' . PERFEX_SAAS_MODULE_NAME . '_setup');
$CI->load->helper(PERFEX_SAAS_MODULE_NAME . '/' . PERFEX_SAAS_MODULE_NAME . '_usage_limit');
$CI->load->helper(PERFEX_SAAS_MODULE_NAME . '/' . PERFEX_SAAS_MODULE_NAME . '_api');


/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(PERFEX_SAAS_MODULE_NAME, [PERFEX_SAAS_MODULE_NAME]);

hooks()->do_action('perfex_saas_loaded');

/**
 * Cron management
 */
if (perfex_saas_is_tenant()) {
    hooks()->add_action('before_cron_run', 'perfex_saas_cron', PHP_INT_MIN); // Want to run this first for tenant
} else {
    hooks()->add_action('after_cron_run', 'perfex_saas_cron');

    hooks()->add_action('before_cron_run', 'perfex_saas_cron_before', PHP_INT_MIN);

    hooks()->add_action('after_cron_run', 'perfex_saas_reset_demo_instances');
}

hooks()->add_filter('cron_functions_execute_seconds', function ($seconds) {
    // Disable cron lock for tenant. This is neccessary as there is already parent lock by the top saas cron.
    if (perfex_saas_is_tenant() && !defined('APP_DISABLE_CRON_LOCK')) define('APP_DISABLE_CRON_LOCK', true);
    return $seconds;
});
hooks()->add_filter('used_cron_features', function ($f) {
    $f[] = _l('perfex_saas_cron_feature_migration');
    return $f;
});

/**
 * Listen to any module activation and run the setup again.
 * This ensure new tables are prepared for saas.
 */
hooks()->add_action('module_activated', 'perfex_saas_trigger_module_install');
hooks()->add_action('module_deactivated', 'perfex_saas_trigger_module_install');

/**
 * Register activation module hook
 */
register_activation_hook(PERFEX_SAAS_MODULE_NAME, 'perfex_saas_module_activation_hook');

function perfex_saas_module_activation_hook()
{
    perfex_saas_install();
}

/**
 * Dactivation module hook
 */
register_deactivation_hook(PERFEX_SAAS_MODULE_NAME, 'perfex_saas_module_deactivation_hook');
function perfex_saas_module_deactivation_hook()
{
    perfex_saas_uninstall();
}

/**
 * Register admin footer hook - Common to both admin and instance
 * @todo Separate instance js customization from super admin
 */
hooks()->add_action('app_admin_footer', 'perfex_saas_admin_footer_hook');
function perfex_saas_admin_footer_hook()
{
    //load common admin asset
    $CI = &get_instance();
    $CI->load->view(PERFEX_SAAS_MODULE_NAME . '/includes/scripts');


    //load add user to package modal
    if (!perfex_saas_is_tenant() && $CI->router->fetch_class() == 'invoices')
        $CI->load->view(PERFEX_SAAS_MODULE_NAME . '/includes/add_user_to_package_modal');
}


/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
hooks()->add_action('admin_init', 'perfex_saas_module_init_menu_items');
function perfex_saas_module_init_menu_items()
{
    $CI = &get_instance();

    if (
        !perfex_saas_is_tenant() && (
            staff_can('view', 'perfex_saas_company') ||
            staff_can('view', 'perfex_saas_package') ||
            staff_can('view', 'perfex_saas_settings')
        )
    ) {

        $badge = [];
        if (get_option('perfex_saas_latest_version') !== (string)PERFEX_SAAS_VERSION_NUMBER) {
            $badge = [
                'value' => '!',
                'color' => '',
                'type'  => 'warning',
            ];
        }

        $CI->app_menu->add_sidebar_menu_item(PERFEX_SAAS_MODULE_WHITELABEL_NAME, [
            'name' => _l('perfex_saas_menu_title'),
            'icon' => 'fa fa-users tw-font-bold',
            'position' => 2,
            'badge' => $badge
        ]);

        if (staff_can('view', 'perfex_saas_api_user')) {
            $CI->app_menu->add_sidebar_children_item(PERFEX_SAAS_MODULE_WHITELABEL_NAME, [
                'slug' => PERFEX_SAAS_MODULE_WHITELABEL_NAME . '_api',
                'name' => _l('perfex_saas_api'),
                'icon' => 'fa fa-link',
                'href' => admin_url(PERFEX_SAAS_ROUTE_NAME . '/api'),
                'position' => 1,
            ]);
        }

        if (staff_can('view', 'perfex_saas_packages')) {
            $is_single_package_mode = perfex_saas_is_single_package_mode();
            $CI->app_menu->add_sidebar_children_item(PERFEX_SAAS_MODULE_WHITELABEL_NAME, [
                'slug' => PERFEX_SAAS_MODULE_WHITELABEL_NAME . '_packages',
                'name' => $is_single_package_mode ? _l('perfex_saas_pricing') : _l('perfex_saas_packages'),
                'icon' => 'fa fa-list',
                'href' => $is_single_package_mode ? admin_url(PERFEX_SAAS_ROUTE_NAME . '/pricing') : admin_url(PERFEX_SAAS_ROUTE_NAME . '/packages'),
                'position' => 5,
            ]);
        }

        if (staff_can('view', 'perfex_saas_company')) {
            $CI->app_menu->add_sidebar_children_item(PERFEX_SAAS_MODULE_WHITELABEL_NAME, [
                'slug' => PERFEX_SAAS_MODULE_WHITELABEL_NAME . '_company',
                'name' => _l('perfex_saas_tenants'),
                'icon' => 'fa fa-university',
                'href' => admin_url(PERFEX_SAAS_ROUTE_NAME . '/companies'),
                'position' => 10,
            ]);
        }

        if (staff_can('view', 'perfex_saas_packages')) {
            $CI->app_menu->add_sidebar_children_item(PERFEX_SAAS_MODULE_WHITELABEL_NAME, [
                'slug' => PERFEX_SAAS_MODULE_WHITELABEL_NAME . '_invoices',
                'name' => _l('perfex_saas_invoices'),
                'icon' => 'fa-solid fa-receipt',
                'href' => admin_url('invoices') . '?' . PERFEX_SAAS_FILTER_TAG,
                'position' => 15,
            ]);
        }

        if (staff_can('view', 'perfex_saas_settings')) {
            $CI->app_menu->add_sidebar_children_item(PERFEX_SAAS_MODULE_WHITELABEL_NAME, [
                'slug' => PERFEX_SAAS_MODULE_WHITELABEL_NAME . '_settings',
                'name' => _l('perfex_saas_settings'),
                'icon' => 'fa fa-cog',
                'href' => admin_url('settings?group=' . PERFEX_SAAS_MODULE_WHITELABEL_NAME),
                'position' => 20,
            ]);

            $CI->app_menu->add_sidebar_children_item(PERFEX_SAAS_MODULE_WHITELABEL_NAME, [
                'slug' => PERFEX_SAAS_MODULE_WHITELABEL_NAME . '_update_ext',
                'name' => _l('perfex_saas_update_ext_menu'),
                'icon' => 'fa fa-plug',
                'href' => admin_url(PERFEX_SAAS_ROUTE_NAME . '/system'),
                'position' => 30,
                'badge' => $badge
            ]);

            // SaaS tab on settings page
            $settings_tab = [
                'name'     => _l('settings_group_' . PERFEX_SAAS_MODULE_NAME),
                'view'     => 'perfex_saas/settings/index',
                'position' => -5,
                'icon'     => 'fa fa-users',
            ];

            if (method_exists($CI->app, 'add_settings_section_child'))
                $CI->app->add_settings_section_child('general', PERFEX_SAAS_MODULE_WHITELABEL_NAME, $settings_tab);
            else
                $CI->app_tabs->add_settings_tab(PERFEX_SAAS_MODULE_WHITELABEL_NAME, $settings_tab);
        }
    }

    if (perfex_saas_is_tenant()) {
        // Reserved routes
        $restricted_menus = ['modules'];
        foreach ($restricted_menus as $menu) {
            $CI->app_menu->add_setup_menu_item($menu, ['name' => '', 'href' => '', 'disabled' => true, 'collapse' => true, 'children' => []]);
        }
    }
}

/**
 * Common hook to filter dangerous file extension when updating settings
 */
hooks()->add_filter('before_settings_updated', 'perfex_saas_before_settings_updated_common_hook');
function perfex_saas_before_settings_updated_common_hook($data)
{
    $filter_permitted_extensions = function ($extensions_string) {
        $_exts = explode(',', $extensions_string);
        if (count($_exts) > 100) throw new \Exception("Ext size too large: Error Processing Request", 1);

        $allowed_files = [];
        foreach ($_exts as $ext) {
            $ext = trim($ext);
            if (str_starts_with($ext, '.') && !in_array($ext, PERFEX_SAAS_DANGEROUS_EXTENSIONS)) {
                $allowed_files[] = $ext;
            }
        }
        return implode(',', $allowed_files);
    };

    if (isset($data['settings']['allowed_files'])) {
        $data['settings']['allowed_files'] = $filter_permitted_extensions($data['settings']['allowed_files']);
    }

    if (isset($data['settings']['ticket_attachments_file_extensions'])) {
        $data['settings']['ticket_attachments_file_extensions'] = $filter_permitted_extensions($data['settings']['ticket_attachments_file_extensions']);
    }

    return $data;
}



/********SAAS CLIENTS AND SUPER ADMIN HOOKS ******/
$is_tenant = perfex_saas_is_tenant();
$is_admin = is_admin();
$is_client = is_client_logged_in();

// Perfex allow admin and client login session on the same browser. We need to optimize is_client in such scenario
if ($is_admin && $is_client && is_subclass_of($CI->router->fetch_class(), 'AdminController')) {
    $is_client = false;
}

if (!$is_tenant) {

    // Add contact Permissions os super admin can create contact of a company with some saas feature control
    hooks()->add_filter('get_contact_permissions', function ($permissions) {
        return array_merge($permissions, perfex_saas_contact_permissions());
    });

    // We do not want to laod module for the exluded super clients and does without any saas permission
    if ($is_client && !perfex_saas_client_can_use_saas())
        return;
}

if (!$is_tenant) {

    // Log a selected plan id whenever we have it. I.e the copied package url
    $plan_identifier = perfex_saas_route_id_prefix('plan');
    if (!empty($package_slug = $CI->input->post_get($plan_identifier, true))) {
        $CI->session->set_userdata([$plan_identifier => $package_slug]);
    }

    $slug_identifier = perfex_saas_route_id_prefix('slug');
    $custom_domain_identifier = perfex_saas_route_id_prefix('custom_domain');
    // Log package, subdomain and custom domain from registration form
    if (!$is_client) {
        $company_slug = $CI->input->post('slug', true);
        if (!empty($company_slug)) {
            $CI->session->set_userdata([$slug_identifier => $company_slug]);
        }

        $custom_domain = $CI->input->post('custom_domain', true);
        if (!empty($custom_domain)) {
            $CI->session->set_userdata([$custom_domain_identifier => $custom_domain]);
        }
    }

    // Add custom domain and subdomain from session if any
    hooks()->add_filter('perfex_saas_create_instance_data', function ($data) use ($CI, $custom_domain_identifier, $slug_identifier) {
        $company_slug = $CI->session->{$slug_identifier};
        if (!empty($company_slug) && !isset($data['slug'])) {
            $data['slug'] = $company_slug;
        }

        $custom_domain = $CI->session->{$custom_domain_identifier};
        if (!empty($custom_domain) && !isset($data['custom_domain']) && perfex_saas_is_valid_custom_domain($custom_domain)) {
            $data['custom_domain'] = $custom_domain;
        }

        return $data;
    });

    // Clear the session if present after success creating an instance
    hooks()->add_action('perfex_saas_after_client_create_instance', function ($id) use ($CI, $plan_identifier, $custom_domain_identifier, $slug_identifier) {
        if ($id) {
            foreach ([$custom_domain_identifier, $slug_identifier, $plan_identifier] as $key) {
                if ($CI->session->has_userdata($key))
                    $CI->session->unset_userdata($key);
            }
        }
    });

    /******* SUPER CLIENT SPECIFIC HOOKS *********/
    if ($is_client) {
        // Auto subscribe to package when logged in as client
        if (perfex_saas_contact_can_manage_subscription())
            perfex_saas_autosubscribe();
    }

    // Use naked hooks out of $is_client to ensure availability in simulations of the client hooks from within admin panel i.e client menus
    hooks()->add_action('clients_init', 'perfex_saas_clients_area_menu_items');
    function perfex_saas_clients_area_menu_items()
    {
        if (is_client_logged_in()) {
            if (perfex_saas_contact_can_manage_instances())
                add_theme_menu_item('companies', [
                    'name' => _l('perfex_saas_client_menu_companies'),
                    'href' => site_url('clients/?companies'),
                    'position' => -2,
                    'href_attributes' => [
                        'class' => 'ps-spa',
                        'data-tab' => "#companies"
                    ]
                ]);

            if (perfex_saas_contact_can_manage_subscription()) {
                add_theme_menu_item('subscription', [
                    'name' => _l('perfex_saas_client_menu_subscription'),
                    'href' => perfex_saas_is_single_package_mode() ? site_url('clients/my_account') : site_url('clients/?subscription'),
                    'position' => -1,
                    'href_attributes' => [
                        'class' => 'ps-spa',
                        'data-tab' => "#subscription"
                    ]
                ]);

                add_theme_menu_item('marketplace', [
                    'name' => _l('perfex_saas_marketplace_client_menu'),
                    'href' => site_url('clients/my_account?view-modal=module'),
                    'position' => -1,
                ]);
            }
        }
    }
    // Add home view for client
    hooks()->add_action('client_area_after_project_overview', 'perfex_saas_show_client_home');
    function perfex_saas_show_client_home()
    {
        include_once(__DIR__ . '/views/client/home.php');
    }

    // Client panel scripts and widgets
    hooks()->add_action('app_customers_head', function () {
        include_once(__DIR__ . '/views/client/scripts.php');
    });




    /******* SUPER ADMIN PANEL SPECIFIC HOOKS *********/
    if ($is_admin || is_staff_member()) {
        if ($is_admin) {
            /**
             * Register permissions
             */
            hooks()->add_action('admin_init', 'perfex_saas_permissions');
            function perfex_saas_permissions()
            {
                $capabilities = [];
                $capabilities['capabilities'] = [
                    'view' => _l('perfex_saas_permission_view'),
                ];
                register_staff_capabilities('perfex_saas_dashboard', $capabilities, _l('perfex_saas') . ' ' . _l('perfex_saas_dashboard'));

                $capabilities = [];
                $capabilities['capabilities'] = [
                    'view' => _l('perfex_saas_permission_view'),
                    'create' => _l('perfex_saas_permission_create'),
                    'edit' => _l('perfex_saas_permission_edit'),
                    'delete' => _l('perfex_saas_permission_delete'),
                ];
                register_staff_capabilities('perfex_saas_companies', $capabilities, _l('perfex_saas') . ' ' . _l('perfex_saas_companies'));
                register_staff_capabilities('perfex_saas_packages', $capabilities, _l('perfex_saas') . ' ' . _l('perfex_saas_packages'));
                register_staff_capabilities('perfex_saas_api_user', $capabilities, _l('perfex_saas') . ' ' . _l('perfex_saas_api_user'));

                $capabilities = [];
                $capabilities['capabilities'] = [
                    'view' => _l('perfex_saas_permission_view'),
                    'edit' => _l('perfex_saas_permission_edit'),
                ];
                register_staff_capabilities('perfex_saas_settings', $capabilities, _l('perfex_saas') . ' ' . _l('perfex_saas_settings'));
            }
        }

        //dashboard
        if (staff_can('view', 'perfex_saas_dashboard')) {
            hooks()->add_filter('get_dashboard_widgets', function ($widgets) {

                return array_merge([['path' => PERFEX_SAAS_MODULE_NAME . '/dashboard/overview_widget', 'container' => 'top-12']], $widgets);
            });

            hooks()->add_action('before_start_render_dashboard_content', 'perfex_saas_dashboard_hook');
            function perfex_saas_dashboard_hook()
            {
                get_instance()->load->view(PERFEX_SAAS_MODULE_NAME . '/dashboard/index', []);
            }
        }

        /** Invoice view hooks and filters */
        if (staff_can('view', 'perfex_saas_packages')) {
            // Add packageid column to the datatable column and hide
            hooks()->add_filter('invoices_table_columns', 'perfex_saas_invoices_table_columns');
            function perfex_saas_invoices_table_columns($cols)
            {
                $cols[perfex_saas_column('packageid')] = ['name' => perfex_saas_column('packageid'), 'th_attrs' => ['class' => 'not_visible']];
                return $cols;
            }

            // Add packageid to selected invoice fields
            hooks()->add_filter('invoices_table_sql_columns', 'perfex_saas_invoices_table_sql_columns');
            function perfex_saas_invoices_table_sql_columns($fields)
            {
                $fields[] = perfex_saas_column('packageid');
                return $fields;
            }

            // Add package name to recurring bill on invoices list
            hooks()->add_filter('invoices_table_row_data', 'perfex_saas_invoices_table_row_data', 10, 2);
            function perfex_saas_invoices_table_row_data($row, $data)
            {
                $label = _l('perfex_saas_invoice_recurring_indicator');
                $col = perfex_saas_column('packageid');
                if (!empty($data[$col])) {
                    $packageid = $data[$col];
                    $package_name = get_instance()->perfex_saas_model->packages($packageid)->name;
                    $row[0] = str_ireplace($label, $label . ' | ' . $package_name, $row[0]);
                }
                $row[] = '';
                return $row;
            }


            // Add package selection to invoice edit/create
            hooks()->add_action('before_render_invoice_template', 'perfex_saas_after_render_invoice_template_hook');
            function perfex_saas_after_render_invoice_template_hook($invoice)
            {
                $col_name = perfex_saas_column('packageid');
                if (empty($invoice->{$col_name})) return;
                $CI = &get_instance();
                $data = [
                    'packages' => $CI->perfex_saas_model->packages(),
                    'invoice' => $invoice,
                    'col_name' => $col_name,
                    'invoice_packageid' => $invoice->{$col_name}
                ];

                $CI->load->view(PERFEX_SAAS_MODULE_NAME . '/includes/select_package_invoice_template', $data);
            }
        }

        /************Settings */
        // Ensure perfex saas setting is use as default when no settings group is defined
        hooks()->add_action('before_settings_group_view', 'perfex_saas_before_settings_group_view_hook');
        function perfex_saas_before_settings_group_view_hook($tab)
        {

            if (empty(get_instance()->input->get('group'))) { //root settings

                redirect(admin_url('settings?group=' . PERFEX_SAAS_MODULE_WHITELABEL_NAME));
            }
        }

        // Get modules whitelabeling settings
        hooks()->add_filter('before_settings_updated', 'perfex_saas_before_settings_updated_hook');
        function perfex_saas_before_settings_updated_hook($data)
        {
            $perfex_saas_settings_array_fields = [
                'perfex_saas_custom_modules_name',
                'perfex_saas_tenants_seed_tables',
                'perfex_saas_sensitive_options',
                'perfex_saas_modules_marketplace',
                'perfex_saas_restricted_clients_id',
                'perfex_saas_custom_services',
                'perfex_saas_require_invoice_payment_status',
                'perfex_saas_demo_instance'
            ];
            foreach ($perfex_saas_settings_array_fields as $key) {
                if (isset($data['settings'][$key])) {
                    $data['settings'][$key] = json_encode($data['settings'][$key]);
                }
            }

            $encrypted_fields = ['perfex_saas_cpanel_password', 'perfex_saas_plesk_password', 'perfex_saas_mysql_root_password'];
            $CI = &get_instance();
            foreach ($encrypted_fields as $key => $field) {
                if (isset($data['settings'][$field]))
                    $data['settings'][$field] = $CI->encryption->encrypt($data['settings'][$field]);
            }

            return $data;
        }
    }
}


/********OTHER SPECIFIC HOOKS ******/
$folder_path = __DIR__ . '/hooks/';
$feature_hook_files = glob($folder_path . '*.php');
$feature_hook_files = hooks()->apply_filters('perfex_saas_extra_hook_files', $feature_hook_files);
foreach ($feature_hook_files as $file) {
    if (is_file($file)) {
        require_once $file;
    }
}


// Manual run test or cron for development purpose only
if (!empty($CI->input->get(PERFEX_SAAS_MODULE_NAME . '_dev'))) {

    // Only permit this in development mode and user should be logged in as admin.
    $is_developer = ENVIRONMENT === 'development' && !perfex_saas_is_tenant() && $is_admin;
    if (!$is_developer) {
        exit("This action can only be run in development mode");
    }

    $action = $CI->input->get('action');

    if ($action === 'test') {
        include_once(__DIR__ . '/test.php');
    }

    if ($action === 'cron') {
        perfex_saas_cron();
    }
    exit();
}