<?php

defined('BASEPATH') or exit('No direct script access allowed');

if ($is_tenant) {
    /**
     * Remove disabled default modules/feature from tenant sidebar menu,setup menu and client
     */
    $disabled_features = perfex_saas_tenant_disabled_default_modules(null, "menu");
    $GLOBALS['disabled_features'] = $disabled_features;
    hooks()->add_filter("sidebar_menu_items", "perfex_saas_remove_disabled_modules_menu_filter");
    hooks()->add_filter("setup_menu_items", "perfex_saas_remove_disabled_modules_menu_filter");
    hooks()->add_filter('theme_menu_items', 'perfex_saas_remove_disabled_modules_menu_filter');
    function perfex_saas_remove_disabled_modules_menu_filter($items)
    {

        // Initialize an array to store disabled modules' children
        $disabled_modules_children = [];

        // Define mapping of disabled features to their respective parent modules
        $feature_to_parent_module = [
            'expenses' => ['finance' => ['expenses-categories'], 'reports' => ['expenses-reports']],
            'estimates' => ['sales' => ['estimates']],
            'proposals' => ['sales' => ['proposals']],
            'invoices' => ['sales' => ['invoices'], 'finance' => ['taxes', 'currencies']],
            'items' => ['sales' => ['items']],
            'payments' => ['sales' => ['payments'], 'finance' => ['payment-modes', 'currencies']],
            'credit_notes' => ['sales' => ['credit_notes']],
            'leads' => ['reports' => ['leads-reports']],
            'knowledge_base' => ['reports' => ['knowledge-base-reports']],
        ];

        $disabled_features = $GLOBALS['disabled_features'];
        foreach ($disabled_features as $key => $feature) {
            if (isset($items[$feature]))
                unset($items[$feature]);

            if (isset($feature_to_parent_module[$feature])) {
                foreach ($feature_to_parent_module[$feature] as $parent_module => $child_menus) {
                    $disabled_modules_children[$parent_module] = array_merge($disabled_modules_children[$parent_module] ?? [], $child_menus);
                }
            }
        }

        if (!empty($disabled_modules_children)) {
            foreach ($disabled_modules_children as $key => $children) {
                if (isset($items[$key]['children'])) {
                    foreach ($items[$key]['children'] as $index => $child) {
                        if (in_array($child['slug'], $children)) {
                            unset($items[$key]['children'][$index]);
                        }
                    }
                }
            }
        }

        return $items;
    }
    // Remove disabled feature/default modules from top bar quick menu
    hooks()->add_filter("quick_actions_links", function ($items) use ($disabled_features) {
        foreach ($items as $key => $value) {
            $controller = explode('/', $value['url'])[0];
            if (
                in_array($controller, $disabled_features) ||
                (isset($value["permission"]) && in_array($value["permission"], $disabled_features)) ||
                (isset($value['name']) && in_array(strtolower($value['name'] . 's'), $disabled_features))
            ) unset($items[$key]);
        }
        return $items;
    });
    // Bind to staff permission interface for disabled default modules/features
    hooks()->add_filter("staff_can", function ($ret_val, $capability, $feature, $staff_id) use ($disabled_features) {
        if ($feature) {
            if (in_array($feature, $disabled_features)) $ret_val = false;
        }
        return $ret_val;
    }, 10, 4);

    // Exclude feature from permission list management
    hooks()->add_filter('staff_permissions', function ($corePermissions, $data) use ($disabled_features) {
        foreach ($corePermissions as $feature => $permission) {
            if (in_array($feature, $disabled_features)) unset($corePermissions[$feature]);
        }
        return $corePermissions;
    }, 10, 2);

    // Remove disabled features from the settings tabs
    hooks()->add_filter("settings_tabs", function ($tabs) use ($disabled_features) {
        foreach ($disabled_features as $feature) {
            if (isset($tabs[$feature]))
                unset($tabs[$feature]);
        }
        return $tabs;
    });
    // Filter disabled features for tenant clients contacts
    hooks()->add_filter('get_contact_permissions', function ($permissions) use ($disabled_features) {
        foreach ($permissions as $key => $value) {
            if (in_array($value['short_name'], $disabled_features)) unset($permissions[$key]);
        }
        return $permissions;
    });
    // Filter disabled feature in dashboard
    hooks()->add_filter('get_dashboard_widgets', function ($widgets) use ($disabled_features) {
        foreach ($widgets as $key => $widget) {
            $feature = explode('_', basename($widget['path']))[0];
            if (
                in_array($feature, $disabled_features) ||
                ($feature === 'finance' && in_array('invoices', $disabled_features) && in_array('estimates', $disabled_features) && in_array('proposals', $disabled_features))
            ) unset($widgets[$key]);
        }
        return $widgets;
    });


    hooks()->add_action('app_admin_footer', function () use ($disabled_features, $CI) {
        if (in_array($CI->router->fetch_class(), ['dashboard', 'staff', 'profile'])) {
            echo '<script>
                    const DISABLED_FEATURES = ' . json_encode($disabled_features) . ';
                    const DISABLED_FEATURE_ACTIVE_CONTROLLER ="' . $CI->router->fetch_class() . '";
                </script>';
            echo '<script src=' . perfex_saas_asset_url('js/disabled_features.js') . '></script>';
        }
    });
}