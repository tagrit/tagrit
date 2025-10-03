<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * This file add cache to deploy mechanism , ensuring faster deployment on remote db and or when the seeding table is very heavy.
 */
$seed_source = get_option('perfex_saas_tenant_seeding_source');
$use_cache_file_enabled = (int)get_option('perfex_saas_enable_tenant_seeding_caching');
if (!$use_cache_file_enabled) return;

// Check if the function perfex_saas_tenant_seed_cache_get_folder does not already exist
if (!function_exists('perfex_saas_tenant_seed_cache_get_folder')) {
    /**
     * Get the folder path for the seed cache based on the provided hash key.
     * If the folder does not exist, create it.
     *
     * @param string $hash_key The hash key used to generate the folder path.
     * @return string The folder path for the seed cache.
     */
    function perfex_saas_tenant_seed_cache_get_folder($hash_key = '')
    {
        if (!function_exists('app_temp_dir')) {
            $CI = &get_instance();
            $CI->load->helper('file');
        }

        // Generate the folder path using the module directory and hashed key
        $folder_path = app_temp_dir() . '.saas_cache/';
        if (!empty($hash_key)) $folder_path = $folder_path . md5($hash_key) . '/';

        // If the folder does not exist, create it with the specified permissions
        if (!is_dir($folder_path))
            mkdir($folder_path, DIR_READ_MODE, true);

        // Return the generated folder path
        return $folder_path;
    }
}

// Check if the function perfex_saas_tenant_seed_cache_clear_cache does not already exist
if (!function_exists('perfex_saas_tenant_seed_cache_clear_cache')) {
    /**
     * Clear the seed cache by removing the seeding_cache directory.
     *
     * @return bool True if the directory was successfully removed, false otherwise.
     */
    function perfex_saas_tenant_seed_cache_clear_cache()
    {
        // Get the path to the seeding_cache directory
        $folder_path = perfex_saas_tenant_seed_cache_get_folder();

        // Remove the directory and return the result
        return perfex_saas_remove_dir($folder_path);
    }
}

// Check if the function perfex_saas_tenant_seed_cache_get_cache_file_name does not already exist
if (!function_exists('perfex_saas_tenant_seed_cache_get_cache_file_name')) {
    /**
     * Get the cache file name for a given purpose.
     *
     * @param string $purpose The purpose of the cache file ('setup' or 'seed').
     * @return string The cache file name.
     */
    function perfex_saas_tenant_seed_cache_get_cache_file_name($purpose)
    {
        // Generate and return the cache file name based on the purpose
        return 'cached_' . $purpose . '_queries.sql';
    }
}

// Check if the function perfex_saas_tenant_seed_cache_generate_cache does not already exist
if (!function_exists('perfex_saas_tenant_seed_cache_generate_cache')) {
    /**
     * Generate the seed cache for the tenant.
     */
    function perfex_saas_tenant_seed_cache_generate_cache()
    {
        // Make empty to ensure queires are not run
        $dsn = '';
        $source_dsn = [];
        $slug = 'demo' . time(); // Generate a unique slug based on the current timestamp
        $dbprefix = perfex_saas_master_db_prefix();

        // Get the seeding source option
        $seed_source = get_option('perfex_saas_tenant_seeding_source');
        if ($seed_source == PERFEX_SAAS_SEED_SOURCE_TENANT && !empty($source_tenant_slug = get_option('perfex_saas_seeding_tenant'))) {
            // If seeding from a tenant, get the tenant's database prefix and DSN
            $dbprefix = perfex_saas_tenant_db_prefix($source_tenant_slug);
            $source_tenant = get_instance()->perfex_saas_model->get_company_by_slug($source_tenant_slug);
            $source_dsn = perfex_saas_get_company_dsn($source_tenant);
        }

        // Generate a hash key and folder path for the seeding cache
        $hash_key = empty($source_dsn) ? 'super_admin' : (is_string($source_dsn) ? $source_dsn : json_encode($source_dsn));
        $folder_path = perfex_saas_tenant_seed_cache_get_folder($hash_key);
        $setup_file = $folder_path . perfex_saas_tenant_seed_cache_get_cache_file_name('setup');
        // If the setup file does not exist, trigger the setup cache
        if (!file_exists($setup_file)) {
            perfex_saas_setup_dsn($dsn, $slug, [], true);
        }

        // Determine if the source is the super admin
        $hash_key = empty($source_dsn) ? 'super_admin' : (is_string($source_dsn) ? $source_dsn : json_encode($source_dsn));
        $folder_path = perfex_saas_tenant_seed_cache_get_folder($hash_key);
        $setup_file = $folder_path . perfex_saas_tenant_seed_cache_get_cache_file_name('seed');

        // If the seeding file does not exist and the seed source is not a file, trigger the seeding cache
        if (!file_exists($setup_file)) {
            if ($seed_source != PERFEX_SAAS_SEED_SOURCE_FILE) {
                perfex_saas_seed_tenant_from_dsn((object)['slug' => $slug], $dsn, $source_dsn, $dbprefix, true);
            }
        }
    }
}

/********** Event Bindings *************/

// Cache table setup process. This will cache all tenant setup queries i.e db structure
hooks()->add_filter('perfex_saas_dsn_setup_source_dsn_queries', function ($data) {
    $dsn = $data['dsn'] ?? '';
    $source_dsn = $data['source_dsn'] ?? '';
    $slug = $data['slug'];

    $placeholder = '{TENANT_SLUG_PLACEHOLDER}';

    $hash_key = empty($source_dsn) ? 'super_admin' : (is_string($source_dsn) ? $source_dsn : json_encode($source_dsn));
    $folder_path = perfex_saas_tenant_seed_cache_get_folder($hash_key);
    $cache_file = $folder_path . perfex_saas_tenant_seed_cache_get_cache_file_name('setup');

    if (!empty($dsn) && file_exists($cache_file)) { // Load stored queries to save time in deploy
        $cached_queries = file_get_contents($cache_file);
        if (!empty($cached_queries)) {
            $CI = &get_instance();
            $CI->load->library(PERFEX_SAAS_MODULE_NAME . '/SqlScriptParser');
            $tenant_dbprefix = perfex_saas_tenant_db_prefix($slug);
            $data['queries'] = $CI->sqlscriptparser->parse($cache_file, function ($sql) use ($placeholder, $tenant_dbprefix) {
                return str_replace($placeholder, $tenant_dbprefix, $sql);
            });
            return $data;
        }
    }

    // Write the query to cache when generated
    hooks()->add_filter('perfex_saas_after_dsn_setup_source_dsn_queries', function ($data) use ($cache_file, $placeholder) {
        $queries = $data['queries'];
        $slug = $data['slug'];
        $tenant_dbprefix = perfex_saas_tenant_db_prefix($slug);

        // Write to cache file
        if (!empty($cache_file) && !empty($queries)) {
            $CI = &get_instance();
            $CI->load->helper('file');
            // Replace the tenant prefix with placeholder to be replaced during consumption
            write_file($cache_file, str_replace($tenant_dbprefix, $placeholder, implode(";\n", $queries)));
        }

        return $data;
    });

    return $data;
});

// Cache tenant seeding process i.e cache all seeding queries
hooks()->add_filter('perfex_saas_before_tenant_seeding_from_dsn', function ($data) use ($seed_source) {

    if ($seed_source == PERFEX_SAAS_SEED_SOURCE_FILE) return $data;

    $company = $data['company'] ?? '';
    $dsn = $data['dsn'] ?? '';
    $source_dsn = $data['source_dsn'] ?? '';
    $return_queries_only = $data['return_queries_only'] ?? '';

    // Cached file
    $hash_key = empty($source_dsn) ? 'super_admin' : (is_string($source_dsn) ? $source_dsn : json_encode($source_dsn));
    $folder_path = perfex_saas_tenant_seed_cache_get_folder($hash_key);
    $cache_file = $folder_path . perfex_saas_tenant_seed_cache_get_cache_file_name('seed');

    if (!empty($dsn) && file_exists($cache_file) && !$return_queries_only) {
        $total_queries_ran = perfex_saas_seed_tenant_from_sql_files($company, $dsn, $cache_file);
        if ($total_queries_ran > 0)
            return []; // Return empty array to ensure the proccess is continued as we already seed the tenant
    }

    hooks()->add_filter('perfex_saas_tenant_seeding_queries', function ($data) use ($cache_file) {
        $queries = $data['queries'] ?? [];
        if (!empty($queries)) {

            $tenant_dbprefix = $data['tenant_dbprefix'];
            // Write to cache file
            if (!empty($cache_file)) {
                $CI = &get_instance();
                $CI->load->helper('file');
                write_file($cache_file, str_replace($tenant_dbprefix, perfex_saas_master_db_prefix(), implode("\n", $queries)));
            }
        }

        return $data;
    });

    return $data;
});

// Trigger caching
hooks()->add_action('perfex_saas_after_cron', 'perfex_saas_tenant_seed_cache_generate_cache');

// Clear cache on installer run
hooks()->add_action('perfex_saas_before_installer_run', 'perfex_saas_tenant_seed_cache_clear_cache');

// Clear cache when seeding option changes
hooks()->add_filter('before_settings_updated', function ($data) {

    $should_clear_cache = false;
    $fields = ['perfex_saas_tenant_seeding_source', 'perfex_saas_seeding_tenant', 'perfex_saas_tenants_seed_tables'];

    if (isset($data['clear-seed-cache'])) {
        unset($data['clear-seed-cache']);
        //perfex_saas_tenant_seed_cache_clear_cache();
        perfex_saas_tenant_seed_cache_generate_cache();
        redirect(uri_string());
        exit;
    }

    foreach ($fields as $field) {
        if (isset($data['settings'][$field])) {

            $new_value = $data['settings'][$field];
            if (is_array($new_value)) $new_value = json_encode($new_value);

            $should_clear_cache = get_option($field) != $new_value;
            if ($should_clear_cache)
                break;
        }
    }

    if ($should_clear_cache) perfex_saas_tenant_seed_cache_clear_cache();

    return $data;
});