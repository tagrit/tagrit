<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Hide tenants tab as configured in the package
if (!$is_tenant) return;

/**
 * Filters and adjusts the visibility of tenant-specific tabs within different sections of the application.
 * The function is primarily used to manage tab visibility based on a given set of tabs to be hidden.
 *
 * @param string $group The section or group (e.g., 'settings', 'customer_profile') where tabs are organized.
 * @param array $hidden_tabs A list of tab slugs that should be hidden.
 * @param array $tabs An array of all available tabs in the group.
 * @param array $lookup_tabs Optional, an array for additional tab lookup, primarily for settings sections.
 *
 * @return array Filtered tabs with hidden tabs removed, while ensuring reserved tabs redirect to the next valid tab.
 */
function perfex_saas_filter_tenant_tabs($group, $hidden_tabs, $tabs, $lookup_tabs = [])
{
    $CI = &get_instance();

    foreach ($hidden_tabs as $slug) {
        /**
         * Reserve default tabs for each group, ensuring smooth navigation even when hidden.
         * Hiding default tabs can lead to a 404 error on the default settings page. 
         * This logic ensures a fallback by redirecting to the next available tab.
         * This flexibility is maintained based on user requests and is useful for hiding sensitive sections like
         * logo upload management under 'settings'.
         */
        $reserved =
            ($group == 'settings' && $slug == 'general') ||
            ($group == 'customer_profile' && $slug == 'profile') ||
            ($group == 'project' && $slug == 'project_overview');

        if (isset($tabs[$slug])) {
            if ($reserved) {
                $next_slug = '';
                $slug_list = array_keys(!empty($lookup_tabs) ? $lookup_tabs : $tabs);

                for ($i = 0; $i < count($slug_list); $i++) {
                    $next_slug = $slug_list[$i];
                    if ($next_slug === $slug) continue;

                    // Find the next visible tab in the group, if available
                    if (!in_array($next_slug, $hidden_tabs)) break;
                    $next_slug = '';
                }

                if (!empty($next_slug)) {
                    // Determine if the current view is within the reserved group
                    $controller = $CI->router->fetch_class();
                    $method = $CI->router->fetch_method();

                    if (
                        ($controller === 'settings' && $method === 'index') ||
                        ($controller === 'clients' && $method === 'client' && count($CI->uri->segments) > 3) ||
                        ($controller === 'projects' && $method === 'view')
                    ) {
                        $current_group = $CI->input->get('group');

                        // Redirect to the next available tab if the current tab is hidden
                        if (empty($current_group) || $current_group == $slug) {
                            $current_url = rtrim(current_url(), '/') . '?' . urldecode(http_build_query(array_merge($_GET, ['group' => $next_slug])));
                            redirect($current_url);
                        }
                    }
                }
            }

            unset($tabs[$slug]);
        }
    }

    return $tabs;
}

/**
 * Filters and modifies tenant-specific settings tabs in the application by dynamically altering the 
 * protected property `settingsSections` within the main application instance.
 * This function manages tab visibility in the settings sections, preserving any reserved tabs
 * for smooth navigation.
 *
 * @param string $group The settings group in which tabs are organized.
 * @param array $hidden_tabs List of tab slugs to hide within the specified group.
 */
function perfex_saas_filter_tenant_settings_tabs($group, $hidden_tabs)
{
    $CI = &get_instance();
    static $settings_sections;

    if (empty($settings_sections)) {
        $sections = $CI->app->get_settings_sections();
        $section_keys = array_keys($sections);

        $flatten_section_children = function ($children) {
            $tabs = [];
            foreach ($children as $value) {
                $value['slug'] = $value['id'];

                // Skip whitelisted sections or any unwanted items by slug name
                if ($value['slug'] !== PERFEX_SAAS_MODULE_WHITELABEL_NAME) {
                    $tabs[$value['slug']] = $value;
                }
            }
            return $tabs;
        };

        $lookup_tabs = perfex_saas_app_settings_tabs();

        foreach ($section_keys as $index => $slug) {
            $section = $sections[$slug];
            $settings_sections[$slug] = $section;

            $tabs = $flatten_section_children($section['children']);
            $_tabs_filtered = array_values(perfex_saas_filter_tenant_tabs($group, $hidden_tabs, $tabs, $lookup_tabs));

            if (empty($_tabs_filtered)) {
                unset($settings_sections[$slug]);
                continue;
            }

            $settings_sections[$slug]['children'] = $_tabs_filtered;
        }
    }

    // Using reflection to modify the protected `settingsSections` property
    $reflection = new ReflectionClass($CI->app);
    $property = $reflection->getProperty('settingsSections');
    $property->setAccessible(true);
    $property->setValue($CI->app, $settings_sections);
}

/**
 * Apply the filtering of hidden tabs
 */
$tenant_hidden_tabs = perfex_saas_tenant()->package_invoice->metadata->hidden_app_tabs ?? (object)[];
foreach (perfex_saas_app_tabs_group() as $group) {

    $CI = get_instance();
    $hidden_tabs = $tenant_hidden_tabs->{$group} ?? [];
    if (empty($hidden_tabs)) continue;

    /** Handle settings section tabs (backward compact since perfex 3.2.0) */
    if ($group === 'settings' && method_exists($CI->app, 'get_settings_sections') && $CI->router->fetch_class() === 'settings') {

        hooks()->add_action("admin_init", function () use ($group, $hidden_tabs) {
            perfex_saas_filter_tenant_settings_tabs($group, $hidden_tabs);
        }, PHP_INT_MAX);
        continue;
    }


    hooks()->add_filter("{$group}_tabs", function ($tabs) use ($group, $hidden_tabs) {
        return perfex_saas_filter_tenant_tabs($group, $hidden_tabs, $tabs);
    }, PHP_INT_MAX);
}