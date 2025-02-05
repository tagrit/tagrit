<?php

defined('BASEPATH') or exit('No direct script access allowed');

/** GATE KEEPERS ***/
if (perfex_saas_is_tenant()) return;

if (get_option('perfex_saas_mysql_root_enabled') != '1') return;

/** Load library */
$CI->load->library(PERFEX_SAAS_MODULE_NAME . '/integrations/mysql_root_api');


/******** HELPERS *******/
if (!function_exists('perfex_saas_mysql_root_enable_separate_user')) {
    /**
     * Check if separate user creation is enabled.
     *
     * @return bool
     */
    function perfex_saas_mysql_root_enable_separate_user()
    {
        return get_option('perfex_saas_mysql_root_enable_separate_user') == '1';
    }
}

if (!function_exists('perfex_saas_mysql_root_allowed_for_tenant')) {
    /**
     * Check if tenant can use mysql root
     *
     * @param object $tenant
     * @return bool
     */
    function perfex_saas_mysql_root_allowed_for_tenant($tenant)
    {
        $package_invoice = perfex_saas_get_client_package_invoice($tenant->clientid);
        $scheme = $package_invoice->db_scheme ?? '';
        return $scheme === 'mysql_root';
    }
}

if (!function_exists('perfex_saas_get_mysql_root')) {
    /**
     * Get mysql_root api library instance
     *
     * @return Mysql_root_api $mysql_root
     */
    function perfex_saas_get_mysql_root()
    {
        $CI = &get_instance();

        $user = get_option('perfex_saas_mysql_root_username');
        $password = get_option('perfex_saas_mysql_root_password');
        $password = empty($password) ? $password : $CI->encryption->decrypt($password);
        $port = get_option('perfex_saas_mysql_root_port');
        $host = get_option('perfex_saas_mysql_root_host');
        $prefix = PERFEX_SAAS_MODULE_NAME_SHORT . '_';

        $CI->mysql_root_api->init(
            $user,
            $password,
            $host,
            $port,
            $prefix
        );

        return $CI->mysql_root_api;
    }
}

if (!function_exists('perfex_saas_mysql_root_setup_tenant_db')) {
    /**
     * Create database and user for the tenant slug
     *
     * @param string $slug
     * @return array
     */
    function perfex_saas_mysql_root_setup_tenant_db($slug)
    {

        $CI = &get_instance();
        $mysql_root = perfex_saas_get_mysql_root();

        $db_host = $mysql_root->host;
        $db_port = (int)$mysql_root->port;
        $db_name = $mysql_root->addPrefix($slug);
        $db_user = $mysql_root->username;
        $db_password = $mysql_root->password;

        try {
            $mysql_root->createDatabase($db_name);
            if (perfex_saas_mysql_root_enable_separate_user()) {

                if (!function_exists('random_string')) {
                    $CI->load->helper('string');
                }
                $db_user = $mysql_root->addPrefix($slug);
                $db_password = random_string('alnum', 8) . bin2hex(random_bytes(4)); // 16 length password

                $mysql_root->createDatabaseUser($db_user, $db_password);
                $mysql_root->assignUserToDatabase($db_user, $db_name);
            }
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }

        $db = [
            'host' => $db_host,
            'user' => $db_user,
            'password' => $db_password,
            'dbname' => $db_name,
        ];

        if (!empty($db_port) && $db_port !== 3306)
            $db['host'] = $db_host . ':' . $db_port;

        return $db;
    };
}

if (!function_exists('perfex_saas_mysql_root_rollback')) {
    /**
     * Rollback tenant db and addon domain setup
     *
     * @param object $tenant
     * @return void
     */
    function perfex_saas_mysql_root_rollback($tenant)
    {
        if (!perfex_saas_mysql_root_allowed_for_tenant($tenant)) return;

        $mysql_root = perfex_saas_get_mysql_root();

        $slug = $tenant->slug;
        $db_user = $mysql_root->addPrefix($slug);
        $db_name = $mysql_root->addPrefix($slug);

        try {
            $mysql_root->deleteDatabase($db_name);
            if (perfex_saas_mysql_root_enable_separate_user())
                $mysql_root->deleteDatabaseUser($db_user);
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }
    };
}

/**** HOOKS *******/

// Add mysql_root as option to package db scheme
hooks()->add_filter('perfex_saas_module_db_schemes', function ($schemes) {
    $schemes[] = ['key' => 'mysql_root', 'label' => _l('perfex_saas_mysql_root_scheme')];
    return $schemes;
});

// Add mysql_root as option to tenant db scheme
hooks()->add_filter('perfex_saas_module_db_schemes_alt', function ($schemes) {
    $schemes[] = ['key' => 'mysql_root', 'label' => _l('perfex_saas_mysql_root_scheme')];
    return $schemes;
});

// Create dsn: db user and password and db  e.t.c
hooks()->add_filter('perfex_saas_module_maybe_create_tenant_dsn', function ($data) {

    $tenant = $data['tenant'];
    $invoice = $data['invoice'];

    if ($invoice->db_scheme === 'mysql_root') {

        $data['dsn'] = perfex_saas_mysql_root_setup_tenant_db($tenant->slug);
    }
    return $data;
});

// Remove tenant db and user
hooks()->add_action('perfex_saas_module_tenant_removed', 'perfex_saas_mysql_root_rollback');
hooks()->add_action('perfex_saas_module_tenant_removal_failed', 'perfex_saas_mysql_root_rollback');
hooks()->add_action('perfex_saas_module_tenant_deploy_failed', 'perfex_saas_mysql_root_rollback');
