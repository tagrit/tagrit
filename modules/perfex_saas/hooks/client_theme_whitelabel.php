<?php

defined('BASEPATH') or exit('No direct script access allowed');

if ($is_tenant) {
    // Fallback to ensure theme is always available.
    $settings_key = 'clients_default_theme';
    $theme_name = get_option($settings_key);
    if (empty($theme_name) || !is_dir(VIEWPATH . 'themes/' . $theme_name)) {
        $new_theme_name = perfex_saas_get_options($settings_key);
        if (!empty($new_theme_name)) {
            update_option('clients_default_theme', $new_theme_name);
            header("Refresh:0");
        }
    }

    // Always return for tenant. We dont want tenant customize theme.
    return;
}


// When tenant is seeded ensure it has the right theme
hooks()->add_action('perfex_saas_after_tenant_seeding', function ($data) {

    $settings_key = 'clients_default_theme';
    if ($data['seed_source'] == PERFEX_SAAS_SEED_SOURCE_FILE && !empty($data['dsn'])) {
        $theme_name = perfex_saas_get_options($settings_key);
        $options_table = perfex_saas_tenant_db_prefix($data['company']->slug) . "options";
        $query = "UPDATE `" . $options_table . "` SET `value`= '$theme_name' WHERE `name` = '$settings_key'";
        perfex_saas_raw_query($query, $data['dsn']);
    }
});


// Rename the Perfex default theme for customer area to another name as provided in the settings
hooks()->add_filter('before_settings_updated', function ($data) {

    $field = 'perfex_saas_clients_default_theme_whitelabel_name';
    if (isset($data['settings'][$field])) {

        $new_theme_name = slug_it(strtolower(trim(str_replace(['.', '/'], '', $data['settings'][$field]))));
        $data['settings'][$field] = $new_theme_name;

        $old_theme_name = get_option($field);
        perfex_saas_whitelabel_clients_default_theme($new_theme_name, $old_theme_name);
    }

    return $data;
});

/**
 * Ensure default theme is always copied when new perfex update is installed.
 * SaaS installer will most always run when perfex db update
 */
hooks()->add_action('perfex_saas_before_installer_run', function () {
    $field = 'perfex_saas_clients_default_theme_whitelabel_name';
    $old_theme_name = get_option($field);
    perfex_saas_whitelabel_clients_default_theme($old_theme_name, $old_theme_name);
});

/**
 * Ensure the whitelabelling is reversed when uninstalling or deactivating the SaaS
 */
hooks()->add_action('perfex_saas_before_uninstaller_run', function ($clean) {
    $field = 'perfex_saas_clients_default_theme_whitelabel_name';
    $old_theme_name = get_option($field);
    perfex_saas_whitelabel_clients_default_theme('', $old_theme_name);
});

/**
 * Ensure perfex default client theme is whitelabelled to provided new name.
 *
 * @param string $new_theme_name
 * @param string $old_theme_name
 * @return void
 */
function perfex_saas_whitelabel_clients_default_theme($new_theme_name, $old_theme_name)
{
    $theme_path = VIEWPATH . 'themes/';
    $asset_base_path = FCPATH . 'assets/themes/';

    $default_theme_name = 'perfex';
    $new_theme_name = slug_it($new_theme_name);
    $old_theme_name = slug_it($old_theme_name);

    $move_theme_folder = function ($base_path, $update_option = false) use ($default_theme_name, $old_theme_name, $new_theme_name) {

        if (empty($new_theme_name))
            $new_theme_name = $default_theme_name;

        if (empty($old_theme_name))
            $old_theme_name = $default_theme_name;


        $from = $base_path . $old_theme_name;
        $to = $base_path . $new_theme_name;

        if (is_dir($base_path . $default_theme_name) && $new_theme_name !== $default_theme_name && $old_theme_name !== $default_theme_name) {
            if (xcopy($base_path . $default_theme_name, $to)) {
                perfex_saas_remove_dir($base_path . $default_theme_name);
            }
        }

        if (is_dir($from) && $old_theme_name !== $new_theme_name) {
            rename($from, $to);
            if (is_dir($from)) {
                if (xcopy($from, $to)) { // Override new theme
                    perfex_saas_remove_dir($from);
                }
            }
        }

        if (is_dir($to) && $update_option) {
            update_option('clients_default_theme', $new_theme_name);
        }
    };

    $move_theme_folder($theme_path, true);
    $move_theme_folder($asset_base_path);
}