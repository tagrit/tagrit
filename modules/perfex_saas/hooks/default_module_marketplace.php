<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Define prefix to be used for the default modules
$default_modules_marketplace_prefix = 'dfm_'; // i.e default_module

/**
 * Deterimine if this hook is enabled or not
 *
 * @return bool
 */
function perfex_saas_default_modules_marketplace_enabled()
{
    static $option = null;
    if ($option === null) {

        $option_getter_method = perfex_saas_is_tenant() ? 'perfex_saas_get_options' : 'get_option';
        $option = $option_getter_method('perfex_saas_allow_default_modules_on_marketplace');
    }

    return $option == '1';
}

if (!perfex_saas_default_modules_marketplace_enabled()) return;

// Add default modules to third party modules list to ensure their availablilty for sales on marketplace and other revelvant places.
hooks()->add_filter('perfex_saas_module_list_filter', function ($modules) use ($default_modules_marketplace_prefix) {

    $default_modules = get_instance()->perfex_saas_model->default_modules();

    $option_getter_method = perfex_saas_is_tenant() ? 'perfex_saas_get_options' : 'get_option';

    // Retrieve the custom module names from the options
    $custom_modules_name = $option_getter_method('perfex_saas_custom_modules_name');
    $custom_modules_name = empty($custom_modules_name) ? [] : json_decode($custom_modules_name, true);

    $modules_market_settings = json_decode($option_getter_method('perfex_saas_modules_marketplace') ?? '', true);

    foreach ($default_modules as $d_module) {
        // Important we prefix to add uniqueness. This prevent clashes with the custom limitation pricing.
        $module_id = $default_modules_marketplace_prefix . $d_module['system_name'];
        $modules[$module_id] = $d_module;
        $modules[$module_id]['system_name'] = $module_id;

        // Assign the custom name to the module if available, otherwise use the default module name
        $modules[$module_id]['custom_name'] = isset($custom_modules_name[$module_id]) ? $custom_modules_name[$module_id] : ($modules[$module_id]['custom_name'] ?? $module_id);
        $modules[$module_id]['custom_name'] = _l($modules[$module_id]['custom_name'], '', false);

        // Add marketplace info
        $modules[$module_id]['description'] = $modules_market_settings[$module_id]['description'] ?? '';
        $modules[$module_id]['price'] = $modules_market_settings[$module_id]['price'] ?? '';
        $modules[$module_id]['billing_mode'] = $modules_market_settings[$module_id]['billing_mode'] ?? '';
        $modules[$module_id]['image'] = $modules_market_settings[$module_id]['image'] ?? '';
        $modules[$module_id]['headers'] = $modules[$module_id];
        $modules[$module_id]['headers']['module_name'] = $modules[$module_id]['custom_name'];
        $modules[$module_id]['headers']['version'] = '1.0.0';
    }

    return $modules;
});

// Exclude purchased default module from the tenant disabled default modules
hooks()->add_filter('perfex_saas_tenant_disabled_default_modules_list_filter', function ($tenant_default_disabled_modules, $tenant, $mode) use ($default_modules_marketplace_prefix) {

    $purchased_modules = (array)($tenant->package_invoice->purchased_modules ?? []);

    $disabled_modules = [];

    if (perfex_saas_is_tenant() && function_exists('get_option')) {

        $disabled_modules = json_decode(get_option('tenant_local_disabled_modules') ?? '', true);
    }

    $tenant_default_disabled_modules = array_filter($tenant_default_disabled_modules, function ($item) use ($purchased_modules, $default_modules_marketplace_prefix, $disabled_modules) {
        return in_array($default_modules_marketplace_prefix . $item, (array)$disabled_modules) ||
            !in_array($default_modules_marketplace_prefix . $item, $purchased_modules);
    });

    return $tenant_default_disabled_modules;
}, 10, 3);