<?php

defined('BASEPATH') or exit('No direct script access allowed');

// This code fixes first time installation issue where error 500 might be experienced on some setup immediately after fresh install
if (!defined('PERFEX_SAAS_MODULE_NAME') && !isset($_GET['reload'])) {
    $url = ($_SERVER['REQUEST_URI'] ?? '');
    $url = (empty($_GET) ? explode('?', $url)[0] . '?reload=1' : $url . '&reload=1');
    header('Location: ' . $url);
    exit();
}

// Include the middlewares
require_once(__DIR__ . '/../helpers/perfex_saas_middleware_helper.php');

/**
 * Detect the global tenant and define the database credential or use default db credentials.
 * We have to run this detection here as the stored DB credentials are ecnrypted and thus we need the Encryption library to decrypt.
 * Encryption library can not be in config because of race effect (db_prefix function by perfex) when loading at such early time.
 * Thus we move the segments here. 
 */
$GLOBALS['_encryption'] = load_class('Encryption');
$dsn = ['host' => '', 'user' => '', 'password' => '', 'dbname' => ''];

// Check if the its a tenant and use the tenant dsn
if (isset($GLOBALS[PERFEX_SAAS_MODULE_NAME . '_tenant'])) {
    $GLOBALS[PERFEX_SAAS_MODULE_NAME . '_tenant']->dsn = $GLOBALS['_encryption']->decrypt($GLOBALS[PERFEX_SAAS_MODULE_NAME . '_tenant']->dsn);
    $dsn = (array)perfex_saas_parse_dsn($GLOBALS[PERFEX_SAAS_MODULE_NAME . '_tenant']->dsn);
}

// Define database credentials
define('APP_DB_HOSTNAME', empty($dsn['host']) ? APP_DB_HOSTNAME_DEFAULT : $dsn['host']);
define('APP_DB_USERNAME', empty($dsn['user']) ? APP_DB_USERNAME_DEFAULT : $dsn['user']);
define('APP_DB_PASSWORD', empty($dsn['password']) ? APP_DB_PASSWORD_DEFAULT : $dsn['password']);
define('APP_DB_NAME', empty($dsn['dbname']) ? APP_DB_NAME_DEFAULT : $dsn['dbname']);
if (perfex_saas_is_tenant())
    define('APP_DB_PREFIX',  perfex_saas_tenant_db_prefix(perfex_saas_tenant_slug()));

// Run middlewares for the tenant. i.e permission and module control. Also add important hooks.
perfex_saas_middleware();




/******************* EARLY TIME RQUIRED HOOKS **********************************/
/**
 * Early time hooks for email template.
 * Must be placed here in hooks to ensure its loaded with perfex email template loading.
 */
hooks()->add_filter('register_merge_fields', 'perfex_saas_email_template_merge_fields');
function perfex_saas_email_template_merge_fields($fields)
{
    $fields[] =  'perfex_saas/merge_fields/perfex_saas_company_merge_fields';
    return $fields;
}

/**
 * Media file folder.
 * Set max number for priority to ensure the function is more or less the last to be called.
 * However, we nee to set the hook in early part of execution to ensure its availability to other script using media folder.
 */
hooks()->add_filter('get_media_folder', 'perfex_saas_set_media_folder_hook', PHP_INT_MAX);
function perfex_saas_set_media_folder_hook($data)
{
    $tenant_slug = perfex_saas_is_tenant() ? perfex_saas_tenant_slug() : perfex_saas_master_tenant_slug();
    if (empty($tenant_slug)) throw new \Exception("Media Error: Error Processing Request", 1);

    return $data . '/' . $tenant_slug;
}

/********OTHER MIDDLEWARE SPECIFIC HOOKS ******/
$folder_path = __DIR__ . '/my_hooks/';
$feature_hook_files = glob($folder_path . '*.php');
$feature_hook_files = hooks()->apply_filters('perfex_saas_extra_middleware_hook_files', $feature_hook_files);
foreach ($feature_hook_files as $file) {
    if (is_file($file)) {
        require_once $file;
    }
}