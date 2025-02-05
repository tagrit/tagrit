<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This file contain core helper functions for the module.
 * All core function for boostraping are defined here along side important constant.
 * CI get_instance() or any other core function of codeigniter as app are not fully loaded and should be avoided in this helper
 */

require(__DIR__ . '/../config/constants.php');
require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/perfex_saas_php8_polyfill_helper.php');
require(__DIR__ . '/perfex_saas_storage_helper.php');

use PHPSQLParser\PHPSQLParser;

/**
 * Class to manage custom db connection through PDO
 */
class SaaSPDOConnectionManager
{
    private static $connections = [];

    public static function getConnection($dsn, $username, $password, $options = [], $use_cache = true)
    {
        $key = md5($dsn . $username . $password . serialize($options));

        if (!$use_cache || !isset(self::$connections[$key])) {
            self::$connections[$key] = new PDO($dsn, $username, $password, $options);
        }

        return self::$connections[$key];
    }
}

/**
 * Initializes the Perfex SAAS module.
 * Sets up the SAAS environment based on the requested tenant.
 *
 * @return void
 */
function perfex_saas_init()
{
    try {
        if (isset($_SERVER['REQUEST_URI'])) {

            $request_uri = $_SERVER['REQUEST_URI'];
            $host  = perfex_saas_http_host($_SERVER);

            // Can identify tenant by url segment and or host (subdomain or cname/custom domain)
            $tenant_info = perfex_saas_get_tenant_info_by_http($request_uri, $host);
            $options = perfex_saas_get_options(
                [
                    'perfex_saas_enable_client_bridge',
                    'perfex_saas_enable_cross_domain_bridge',
                    'perfex_saas_enable_instance_switch',
                    'perfex_saas_client_bridge_account_menu_position',
                    PERFEX_SAAS_GLOBAL_ACTIVE_MODULES_OPTION_KEY,
                    'perfex_saas_masked_settings_pages',
                    'perfex_saas_trial_expire_page_url',
                    'perfex_saas_enable_single_package_mode',
                    'perfex_saas_require_invoice_payment_status',
                    'perfex_saas_disable_cron_job_setup_tab',
                    'show_subscriptions_in_customers_area',
                    'perfex_saas_trial_notification_day',
                    'perfex_saas_enable_tenant_admin_modules_page'
                ],
                true
            );

            if ($tenant_info) {
                $tenant_path_id = $tenant_info['path_id'];
                $tenant_id = $tenant_info['slug'];
                $tenancy_access_mode = $tenant_info['mode'];
                $field =  $tenancy_access_mode == PERFEX_SAAS_TENANT_MODE_DOMAIN ? 'custom_domain' : 'slug'; // path and subdomain mode use slug for search

                if ($field == 'custom_domain') {
                    $tenant_id = $tenant_info['custom_domain'];
                    $field = 'custom_domain';
                }

                // Determine the tenant base URL
                $_base_url = perfex_saas_url_origin($_SERVER) . '/';
                $tenant_base_url = $_base_url;

                if (!empty($tenant_path_id)) {
                    $tenant_base_url .= "$tenant_path_id/";
                }

                if (!$tenant_id)
                    perfex_saas_show_tenant_error("Invalid Tenant", "We could not find the requested instance.", 404);

                // Check if tenant exists
                $tenant = perfex_saas_search_tenant_by_field($field, $tenant_id);
                if (!$tenant) {
                    perfex_saas_show_tenant_error("Invalid Tenant", "The requested tenant does not exist.", 404);
                }

                // Decode metadata and package/invoice details
                $tenant->metadata = (object)json_decode($tenant->metadata);

                // Get package and invoice details
                $package_invoice = perfex_saas_get_client_package_invoice($tenant->clientid, ['include_cancelled' => true]);
                if ($package_invoice) {
                    $tenant->package_invoice = $package_invoice;
                }

                $tenant->saas_options = $options;

                // Add the identity mode
                $tenant->http_identification_type = $tenancy_access_mode;

                // @todo Determine if we should check package for permission to use custom domain if the tenant is recognized by custom domain.

                // Set global variable for the tenant
                $GLOBALS[PERFEX_SAAS_MODULE_NAME . '_tenant'] = $tenant;

                $tenant_cookie_path = '/';

                // Tenant is gotten from request uri match i.e tenant_slug/ps
                if (!empty($tenant_path_id)) {

                    $folder = trim(parse_url($_base_url, PHP_URL_PATH), '/');
                    $tenant_upload_folder  = $tenant_path_id . '/' . PERFEX_SAAS_UPLOAD_BASE_DIR;

                    /**
                     * Assimilate $tenant_path_id segment into request script name
                     * to trick CI3 of using base url with the folder and tenant path id inclusive
                     */
                    $script_name = $_SERVER['SCRIPT_NAME'];
                    if (str_ends_with($script_name, '.php')) {
                        $virtual_prefix = '/' . $tenant_path_id;
                        if (!empty($folder)) {
                            $script_name = str_ireplace($folder . '/', '', $script_name);
                            $virtual_prefix = '/' . $folder . '/' . $tenant_path_id;
                        }

                        $_SERVER['SCRIPT_NAME'] = $virtual_prefix . $script_name;
                    } else if (str_starts_with($_SERVER['SCRIPT_NAME'], '/index.php')) {
                        // LiteSpeed server compact support where script name is /index.php/tenantid/ps/some/path
                        $_SERVER['SCRIPT_NAME'] =  (empty($folder) ? '' : '/' . $folder) . '/' . $tenant_path_id . '/index.php';
                    }

                    // Fall back for fixing repeated url path in path id
                    $request_uri_updated = false;
                    // Replace repeated $id segment
                    if (stripos($request_uri, "/$tenant_path_id/$tenant_path_id") !== false) {
                        $_SERVER['REQUEST_URI'] = str_ireplace("/$tenant_path_id/$tenant_path_id", "/$tenant_path_id", $request_uri);
                        $request_uri_updated = true;
                    }

                    $folder = trim(parse_url($_base_url, PHP_URL_PATH), '/');
                    if (!empty($folder) && $folder !== '/') {
                        // Replace repeated subfolder/ $id segment
                        if (stripos($request_uri, "/$folder/$tenant_path_id/$folder/$tenant_path_id") !== false) {
                            $_SERVER['REQUEST_URI'] = str_ireplace("/$folder/$tenant_path_id/$folder/$tenant_path_id", "/$folder/$tenant_path_id", $request_uri);
                            $request_uri_updated = true;
                        }
                    }

                    if ($request_uri_updated) {
                        if (empty($_POST)) {
                            $url = perfex_saas_url_origin($_SERVER, true, true);
                            header("Location: $url");
                            exit;
                        }
                    }
                    // End fall back


                    // Serve static files
                    if (stripos($request_uri, ".") && stripos($request_uri, "://") === false) {
                        $request_uri_path = $request_uri;
                        $pos = strpos($request_uri_path, '?');
                        if ($pos !== false) {
                            $request_uri_path = substr($request_uri_path, 0, $pos);
                        }
                        if (
                            stripos($request_uri_path, ".") &&
                            stripos($request_uri_path, ".php") === false &&
                            stripos($request_uri_path, ".html") === false &&
                            stripos($request_uri_path, ".htm") === false &&
                            stripos($request_uri_path, $tenant_upload_folder) === false
                        ) {
                            $url = str_ireplace("/$tenant_path_id", '', perfex_saas_url_origin($_SERVER, true, true));
                            header("Location: $url");
                            exit;
                        }
                    }

                    // update cookie path
                    $tenant_cookie_path = empty($folder) ? '/' . $tenant_path_id . '/' : "/$folder/$tenant_path_id/";
                }

                // Define constants for the tenants. If any of these have been defined earlier, an error should be thrown,
                // as it is important that the user of the module has not defined these custom constants.
                define('PERFEX_SAAS_TENANT_SLUG', $tenant->slug);
                define('PERFEX_SAAS_TENANT_BASE_URL', $tenant_base_url);
                define('APP_SESSION_COOKIE_NAME', $tenant->slug . '_sp_session');
                define('APP_COOKIE_PREFIX', $tenant->slug);
                define('APP_COOKIE_PATH', $tenant_cookie_path);
                define('APP_COOKIE_DOMAIN', $host);
            }

            // If using custom domain it neccessary we set cookie same site to none 
            // for tenant brige/cross login and iframe loading from other domain (i.e custom domain) to work
            $cookie_same_site =   defined('APP_SESSION_COOKIE_SAME_SITE_DEFAULT') ? APP_SESSION_COOKIE_SAME_SITE_DEFAULT : 'Lax';
            if (!$tenant_info && ($options['perfex_saas_enable_cross_domain_bridge'] ?? '0') == '1') {

                $is_admin = str_starts_with(ltrim($request_uri, '/'), 'admin') !== false;
                $is_in_local = perfex_saas_host_is_local(perfex_saas_get_saas_default_host(), true);
                if (!$is_admin && !$is_in_local) {
                    $cookie_same_site = 'none';
                    defined('APP_COOKIE_SECURE') or define('APP_COOKIE_SECURE', true);
                    defined('APP_COOKIE_HTTPONLY') or define('APP_COOKIE_HTTPONLY', true);
                }
            }
            // Ensure we always have APP_SESSION_COOKIE_SAME_SITE defined is tenant or not
            defined('APP_SESSION_COOKIE_SAME_SITE') or define('APP_SESSION_COOKIE_SAME_SITE', $cookie_same_site);
        }
    } catch (\Throwable $th) {
        // Handle any exceptions that occur during initialization
        perfex_saas_show_tenant_error("Initialization Error", $th->getMessage(), 500);
    }

    // Define APP_BASE_URL based on the tenant's base URL, fallback to the default base URL if not available
    define('APP_BASE_URL', defined('PERFEX_SAAS_TENANT_BASE_URL') ? PERFEX_SAAS_TENANT_BASE_URL : APP_BASE_URL_DEFAULT);
}


/**
 * Get tenant information based on the provided HTTP request URI and host.
 * If the returned array contain non empty slug, then tenant match/search should be made by 'slug' field otherwise 'custom_domain'.
 * The returned array also contain 'mode' key which can either be 'domain' - custom domain, 'subdomain' or 'path', depending on how the 
 * tenant is recognized.
 *
 * @param string $request_uri The request URI.
 * @param string $host The HTTP host.
 * @return array|false Tenant information if found, otherwise false.
 * @throws Exception If invalid input is provided or no tenant is found.
 */
function perfex_saas_get_tenant_info_by_http($request_uri, $host)
{
    // Validate and sanitize input
    if (!is_string($request_uri) || !is_string($host)) {
        throw new Exception('Invalid host value provided.');
    }

    $tenant_info = false;
    $mode = PERFEX_SAAS_TENANT_MODE_DOMAIN;

    // Try by subdomain or domain first before url match
    if (!empty($host) && !perfex_saas_host_is_local($host)) {
        $tenant_info = perfex_saas_get_tenant_info_by_host($host);
        if (!empty($tenant_info['slug']))
            $mode = PERFEX_SAAS_TENANT_MODE_SUBDOMAIN;
    }

    if (!$tenant_info) {
        // Get tenant information from request URI
        $tenant_info = perfex_saas_get_tenant_info_by_request_uri($request_uri);
        if ($tenant_info)
            $mode = PERFEX_SAAS_TENANT_MODE_PATH;
    }

    if (!$tenant_info) {
        return false;
    }

    return [
        'path_id' => $tenant_info['path_id'] ?? '',
        'slug' => $tenant_info['slug'] ?? '',
        'custom_domain' => $tenant_info['custom_domain'] ?? '',
        'mode' => $mode
    ];
}


/**
 * Extracts the tenant information from the request URI.
 *
 * @param string $request_uri The request URI.
 * @return array|false The tenant information array or false if not found.
 */
function perfex_saas_get_tenant_info_by_request_uri($request_uri)
{
    $saas_url_marker = '/' . PERFEX_SAAS_ROUTE_ID;
    // Should match either /tenant/ps/* or /tenant/ps
    $saas_url_id_pos = stripos($request_uri, $saas_url_marker . '/');

    if ($saas_url_id_pos === false && str_ends_with($request_uri, $saas_url_marker))
        $saas_url_id_pos = stripos($request_uri, $saas_url_marker);

    if ($saas_url_id_pos !== false) {

        // Extract tenant slug and id
        $tenant_slug = substr($request_uri, 1, $saas_url_id_pos - 1);
        // Find the position of the last slash
        $lastSlashPos = strrpos($tenant_slug, '/');
        // Extract the substring after the last slash
        if ($lastSlashPos !== false)
            $tenant_slug = substr($tenant_slug, $lastSlashPos + 1);

        // Get the directory in case the perfex is installed in subfolder
        $base_url_path = parse_url(perfex_saas_default_base_url());
        if (!isset($base_url_path['path'])) {
            throw new \Exception("Your base url in app/app-config.php should end with trailing slash !", 1);
        }

        $base_url_path = $base_url_path['path'];

        if (!empty($tenant_slug) && str_starts_with($request_uri, $base_url_path . $tenant_slug . $saas_url_marker)) {

            $id = trim($tenant_slug . $saas_url_marker, '/'); // i.e. tenantslug/ps

            return [
                'slug' => $tenant_slug,
                'path_id' => $id,
            ];
        }
    }

    return false;
}

/**
 * Get tenant information based on the provided host.
 * Returned array contain either of non empty 'custom_domain' and 'slug' but not both.
 *
 * @param string $http_host The HTTP host.
 * @return array|false Tenant information if found or false is on same domain with saas base domain
 * @throws Exception If no tenant is found or an invalid subdomain is detected.
 */
function perfex_saas_get_tenant_info_by_host($http_host)
{
    // Validate input
    if (!filter_var($http_host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) || stripos($http_host, '/') !== false) {
        throw new Exception('Invalid HTTP host provided: ' . $http_host);
    }

    // Get the default host and the URL host
    $app_host = perfex_saas_get_saas_default_host();

    $host = $http_host;
    $tenant_slug = '';

    if (str_starts_with($app_host, 'www.')) {
        $app_host = str_ireplace('www.', '', $app_host);
    }

    if (str_starts_with($host, 'www.')) {
        $host = str_ireplace('www.', '', $host);
    }

    // Compare real app host with the current host address
    if ($app_host === $host) {
        return false;
    }

    // if current request is not using the default base host, check for alt host
    if (!str_ends_with($host, $app_host)) {

        // Get the alternative host if provided in saas settings
        $alt_app_host = perfex_saas_get_saas_alternative_host();

        // Check if request is using alternative host
        if (!empty($alt_app_host) && str_ends_with($host, $alt_app_host)) {

            // We want to resolve false if host is same as alternative host
            if ($alt_app_host === $host) {
                return false;
            }

            $app_host = $alt_app_host;
        }
    }

    // Check for subdomain
    if (str_ends_with($host, $app_host)) {
        $subdomain = trim(str_ireplace($app_host, '', $host), '.');

        if (empty($subdomain) || stripos($subdomain, '.') !== false) {
            throw new Exception('Invalid subdomain detected.');
        }

        $tenant_slug = $subdomain; // Assign the subdomain as the tenant slug
        $host = ""; // Reset the host value
    }

    return [
        'custom_domain' => $host, // Custom domain (without "www")
        'slug' => $tenant_slug // Tenant slug
    ];
}

/**
 * Get the default app base url host. Use the address for installation before setting up SaaS.
 *
 * @return string
 */
function perfex_saas_get_saas_default_host()
{
    return parse_url(perfex_saas_default_base_url(), PHP_URL_HOST);
}

/**
 * Get the app alternative base host 
 *
 * @return string
 */
function perfex_saas_get_saas_alternative_host()
{
    return defined('PERFEX_SAAS_ALTERNATIVE_HOST') ? PERFEX_SAAS_ALTERNATIVE_HOST : '';
}

/**
 * Get the default app base url. i.e the super http url.
 *
 * @param string $path
 * @return string
 */
function perfex_saas_default_base_url($path = '')
{
    return APP_BASE_URL_DEFAULT . $path;
}

/**
 * Detect if the provided host is a localhost
 *
 * @param string $host
 * @param bool $strict Determine if to include some test extension i.e .test .dev e.t.c
 * @return bool
 */
function perfex_saas_host_is_local($host, $strict = false)
{
    $localhosts = ['localhost', '127.0.0.1', '::1'];
    foreach ($localhosts as $localhost) {
        if ($host === $localhost || str_starts_with($host, $localhost))
            return true;
    }

    if ($strict && str_ends_with($host, '.test')) return true;

    if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return true;
    }

    return false;
}

/**
 * Retrieve a tenant by a certain db column/field in companies table
 *
 * @param string $field The db column name
 * @param string $value The value to search for
 * @param bool $permit_cleaning If value is allowed to be cleaned or used as provided
 * @return object|null The tenant object if found, null otherwise
 */
function perfex_saas_search_tenant_by_field($field, $value, $permit_cleaning = true)
{
    $value_cleaned = $value;

    if ($field == 'slug' && $permit_cleaning) {
        $value_cleaned = perfex_saas_clean_slug($value);
    }

    $tenant_table = perfex_saas_table('companies');
    $query = "SELECT `slug`, `name`, `dsn`, `clientid`, `metadata`, `status` FROM `$tenant_table` WHERE `$field` = :value";
    $parameters = [':value' => $value_cleaned];

    $tenant_info = perfex_saas_raw_query_row($query, [], true, true, $parameters);
    if ($tenant_info) {
        return $tenant_info;
    }

    // Backward compatibility with hyphen to underscore
    if ($permit_cleaning && $field == 'slug' && $value_cleaned != $value) {
        return perfex_saas_search_tenant_by_field($field, $value, false);
    }

    return null;
}

/**
 * Retrieve the package invoice for a client.
 * 
 * Use Perfex_saas_model::get_company_invoice() for comprehensive details
 * 
 * @param int $clientid The client ID
 * @param array $options The optional option params . Available entry: parser_callback,include_cancelled,parser_callback
 * @return object|null The package invoice object if found, null otherwise
 */
function perfex_saas_get_client_package_invoice($clientid, $options = [])
{
    $clientid = (int)$clientid;
    $dbprefix = perfex_saas_master_db_prefix();
    $invoice_table = $dbprefix . 'invoices';
    $package_table = perfex_saas_table('packages');
    $client_table = $dbprefix . 'clients';
    $contact_table = $dbprefix . 'contacts';
    $subscription_table = $dbprefix . 'subscriptions';
    $package_column = perfex_saas_column('packageid');

    // Get client metadata
    $metadata = perfex_saas_get_or_save_client_metadata($clientid);
    $metadata = (object)(empty($metadata) ? [] : $metadata);
    $subscription_id = (int)($metadata->subscription_id ?? 0);
    $subscription_package_id = $metadata->subscription_package_id ?? 0;
    if (empty($subscription_package_id)) $subscription_id = 0;

    // Check for future trial in meta
    $should_mock = !empty($metadata->trial_period_ends) && !empty($metadata->trial_package_id) && empty($metadata->trial_cancelled);

    //$extra_where = [" AND `recurring` > '0'"];
    $extra_where = [];

    // Override is client have a subscription id
    if (!empty($subscription_id)) {

        $extra_where = [" AND `subscription_id` = '$subscription_id'"];
    }

    if (!isset($options['include_cancelled'])) {

        $extra_where[] = " AND `$invoice_table`.`status` != '5'"; // Must not be cancelled
    }

    $extra_where = implode(' ', $extra_where);

    $fields = "`$invoice_table`.*,
            `$package_table`.`status` as `package_status`, `slug`, 
            `firstname`, `lastname`, `email`,
            `$package_table`.`name`, `$package_table`.`description`, `$package_table`.`price`, 
            `$package_table`.`bill_interval`, `$package_table`.`is_default`, 
            `$package_table`.`is_private`, `$package_table`.`db_scheme`, `$package_table`.`db_pools`, `$package_table`.`modules`, `$package_table`.`metadata`, `$package_table`.`trial_period`";

    $q = "SELECT 
            $fields 
        FROM `$invoice_table` 
            INNER JOIN `$package_table` ON `$package_table`.`id` = `$package_column` 
            INNER JOIN `$client_table` ON `$client_table`.`userid` = `$invoice_table`.`clientid` 
            LEFT JOIN `$contact_table` ON `$contact_table`.`userid` = `$client_table`.`userid` AND `is_primary`='1' 
        WHERE `$package_column` IS NOT NULL AND is_recurring_from IS NULL AND `clientid`=:clientid $extra_where 
        ORDER BY `recurring` DESC, `$package_column` DESC, FIELD(`$invoice_table`.`status`,'6','2','3','1','4','5'), `$invoice_table`.`datecreated` DESC;";

    // Subscription 
    if (!empty($subscription_id)) {

        $extra_where = isset($options['include_cancelled']) ? '' : " AND `$subscription_table`.`status` != 'canceled' AND `$subscription_table`.`status` != 'incomplete_expired'";
        $q = "SELECT 
            `$subscription_table`.`stripe_subscription_id`, `$subscription_table`.`id` as subscription_id2, `$subscription_table`.`hash` as subscription_hash, `$subscription_table`.`ends_at` as subscription_ends_at, `$subscription_table`.`status` as subscription_status, `$subscription_table`.`created` as subscription_created, next_billing_cycle,
            $fields 
        FROM `$subscription_table` 
            INNER JOIN `$package_table` ON `$package_table`.`id`='$subscription_package_id'
            INNER JOIN `$client_table` ON `$client_table`.`userid` = `$subscription_table`.`clientid` 
            LEFT JOIN `$invoice_table` ON `$invoice_table`.`subscription_id` = `$subscription_table`.`id`
            LEFT JOIN `$contact_table` ON `$contact_table`.`userid` = `$client_table`.`userid` AND `is_primary`='1' 
        WHERE `$package_table`.`id` = '$subscription_package_id' AND `$subscription_table`.`clientid`=:clientid AND `$subscription_table`.`id` = '$subscription_id' $extra_where
        ORDER BY `subscription_id` DESC, FIELD(`$invoice_table`.`status`,'6','2','3','1','4','5'), `$invoice_table`.`datecreated` DESC;";
    }

    // Return virtual draft invoice for trial
    if ($should_mock) {

        $q = "SELECT 
            `$client_table`.`userid` as tuserid, `$package_table`.`id` as package_id,
            $fields
        FROM `$client_table` 
            INNER JOIN `$package_table` ON `$package_table`.`id` = '$metadata->trial_package_id'
            LEFT JOIN `$invoice_table` ON `$invoice_table`.`id` IS NULL
            LEFT JOIN `$contact_table` ON `$contact_table`.`userid`=:clientid AND `is_primary`='1' 
        WHERE `$package_table`.`id` = '$metadata->trial_package_id' AND `$client_table`.`userid`=:clientid
        LIMIT 1;";
    }

    $parameters = [':clientid' => $clientid];
    $package_invoice = perfex_saas_raw_query_row($q, [], true, true, $parameters);

    if (!$package_invoice || empty($package_invoice)) {

        return null;
    }

    // Perfex requires cancelled invoice to be set to non-recurring to prevent further charges otherwise saas invoice should be recurring.
    if (!$should_mock && (int)$package_invoice->recurring == '0' && $package_invoice->status != '5' && empty($package_invoice->subscription_id2)) {
        return null;
    }

    // Prepare the mock for trial
    if ($should_mock) {
        $package_invoice->status = '6'; // Draft
        $package_invoice->id = PHP_INT_MAX;
        $package_invoice->hash = $package_invoice->id;
        $package_invoice->is_mock = true;
        $package_invoice->duedate = $metadata->trial_period_ends ?? '';
        $package_invoice->date = $metadata->trial_period_start ?? '';
        $package_invoice->on_trial = true;
        $package_invoice->{$package_column} = $package_invoice->package_id;
    }

    // Pepare the invoice for subscription
    if (!empty($subscription_id)) {

        // Pepare the mock invoice for subscription
        if (empty($package_invoice->subscription_id)) {
            $package_invoice->id = PHP_INT_MAX;
            $package_invoice->hash = $package_invoice->id;
            $package_invoice->is_mock = true;
            $package_invoice->duedate = $metadata->subscription_created ?? $metadata->trial_period_ends ?? '';
            $package_invoice->date = $metadata->subscription_created ?? '';
            $package_invoice->{$package_column} = $subscription_package_id;
            $package_invoice->subscription_id = $subscription_id;
        } else if (is_null($package_invoice->duedate)) { // Ensure valid duedate
            $package_invoice->duedate = $metadata->subscription_created ?? '';
        }
    }

    // Set subscription package id
    if (!empty($package_invoice->subscription_id)) {
        $package_invoice->{$package_column} = $metadata->subscription_package_id;
        $subscription_status = $package_invoice->status;
        switch ($package_invoice->subscription_status) {
            case 'active':
                $subscription_status = '2';
                break;

            case 'canceled':
            case 'incomplete_expired':
                $subscription_status = '5';
                break;
        }
        $package_invoice->status = $subscription_status;
    }

    if (empty($package_invoice->{$package_column} ?? '')) return null;

    // We should have due date
    if (is_null($package_invoice->duedate)) {
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development')
            throw new \Exception("Invoice should have a duedate", 1);
        return null;
    }

    $package_invoice->trial_package_id = $metadata->trial_package_id ?? '';


    // Decode package/invoice details
    // Add invoice customization
    $package_invoice->custom_limits = (object)($metadata->custom_limits ?? []);
    $package_invoice->purchased_modules = (object)($metadata->purchased_modules ?? []);
    $package_invoice->purchased_services = (object)($metadata->purchased_services ?? []);
    $package_invoice->onetime_purchased_service_invoice = (object)($metadata->onetime_purchased_service_invoice ?? []);
    $package_invoice->onetime_purchased_module_invoice = (object)($metadata->onetime_purchased_module_invoice ?? []);
    $package_invoice->onetime_purchased_items_invoice = array_merge(
        array_values((array)$package_invoice->onetime_purchased_service_invoice),
        array_values((array)$package_invoice->onetime_purchased_module_invoice)
    );

    // check if not upaid/overdue/partialy paid child invoice
    if (!isset($options['skip_children'])) {
        // get children invoices for the recurring invoice that is either unpaid,overdue or partially paid
        $unpaid_child = perfex_saas_get_company_invoice_child_invoices($package_invoice, true);
        if ($unpaid_child) {
            unset($unpaid_child->{$package_column});
            $package_invoice = (object)array_merge((array)$package_invoice, (array)$unpaid_child);
        }
    }

    if (isset($options['parser_callback']) && is_callable($options['parser_callback'])) {
        $package_invoice = call_user_func($options['parser_callback'], $package_invoice);
    } else {
        // Little parsing of the package columns
        if (!empty($package_invoice->metadata)) {
            $package_invoice->metadata = json_decode($package_invoice->metadata);
        }
        if (!empty($package_invoice->modules)) {
            $package_invoice->modules = json_decode($package_invoice->modules, true);
        }
    }
    return (object)$package_invoice;
}

/**
 * Get company invoice child recurring invoices that is/are either unpaid/overdue or partially paid.
 * It return child invoices created out of renewal.
 *
 * @param object $invoice
 * @param boolean $single_row if to return single row or all matches
 * @param array $excluded_invoices List of invoice id to exclude check on i.e we do not want to include lifetime purchased services invoices.
 * @return mixed
 */
function perfex_saas_get_company_invoice_child_invoices($invoice, $single_row = true, $excluded_invoices = [])
{
    $dbprefix = perfex_saas_master_db_prefix();
    $invoice_table = $dbprefix . 'invoices';
    $invoiceid = (int)$invoice->id;

    if (empty($excluded_invoices)) {
        $excluded_invoices = $invoice->onetime_purchased_items_invoice;
    }

    $extra_where = '';
    // Exclude some invoices like lifetime/onetime purchased services invoices
    if (!empty($excluded_invoices))
        $extra_where = ' AND id NOT IN (' . implode(',', $excluded_invoices) . ')';

    $query = "
        SELECT * 
        FROM `$invoice_table` 
        WHERE `is_recurring_from` = '$invoiceid'" .
        " AND `status` < 5 " . // not cancelled or draft i.e Invoices_model::STATUS_CANCELLED
        " AND `status` != 2 " .  // not paid i.e Invoices_model::STATUS_PAID
        $extra_where .
        ";";

    return $single_row ? perfex_saas_raw_query_row($query, [], true) : perfex_saas_raw_query($query, [], true);
}

function perfex_saas_invoice_is_on_trial(object $invoice)
{
    return isset($invoice->on_trial) && $invoice->on_trial;
}

function perfex_saas_get_invoice_payment_endpoint($invoice)
{
    $invoice_pay_endpoint = $invoice_pay_endpoint = $invoice->subscription_id ? "subscription/$invoice->subscription_hash" : "invoice/$invoice->id/$invoice->hash";
    return $invoice_pay_endpoint;
}
/**##################################################################################################################***
 *                                                                                                                      *
 *                                               Common tenant helpers methods                                          *
 *                                                                                                                      *
 ***##################################################################################################################**/

if (!function_exists('dd')) {
    function dd()
    {
        var_dump(func_get_args());
        exit();
    }
}

/**
 * Function to generate name for instance db.
 * Add unique signature to DB created by the saas
 *
 * @param string $db
 * @throws Exception    When the length of the db will be higher than 64 characters.
 * @return string
 */
function perfex_saas_db($db)
{
    $db = PERFEX_SAAS_MODULE_NAME . '_db_' . $db;

    // Convert slug to lowercase
    $db = strtolower($db);

    // Replace non-alphanumeric characters with underscore
    $db = preg_replace('/[^a-z0-9]+/', '_', $db);

    // Remove leading digits or underscores
    $db = preg_replace('/^[0-9_]+/', '', $db);

    // Remove leading and trailing underscores
    $db = trim($db, '_');

    // throw error when length is above 64 characters (database name limit)
    if (strlen($db) > 64) throw new \Exception("Database name provided has exceed the 64 character limit: $db", 1);

    return $db;
}

/**
 * Method to prefix saas table names
 *
 * @param string $table
 * @return string
 */
function perfex_saas_table($table)
{
    $db_prefix = perfex_saas_master_db_prefix();

    return $db_prefix . PERFEX_SAAS_MODULE_NAME . '_' . $table;
}


/**
 * Method to generate perfex saas column name for perfex tables
 *
 * @param string $column
 * @return string
 */
function perfex_saas_column($column)
{
    return PERFEX_SAAS_MODULE_NAME . '_' . $column;
}


/**
 * Function to get master slug
 *
 * @return string
 */
function perfex_saas_master_tenant_slug()
{
    return 'master';
}

/**
 * check is request is instance request or saas module
 *
 * @return     bool
 */
function perfex_saas_is_tenant()
{
    return defined('PERFEX_SAAS_TENANT_BASE_URL') && defined('PERFEX_SAAS_TENANT_SLUG') && !empty($GLOBALS[PERFEX_SAAS_MODULE_NAME . '_tenant']);
}


/**
 * Get the active tenant
 * 
 * @return     object|false
 * 
 * Returned object can have 'package_invoice' and object contain bot property of invoice and the package together.
 */
function perfex_saas_tenant()
{
    if (!perfex_saas_is_tenant()) return false;

    $tenant = (object)$GLOBALS[PERFEX_SAAS_MODULE_NAME . '_tenant'];

    return $tenant;
}

/**
 * Get super admin options from the tenant context.
 *
 * @param string $key
 * @param object|null $tenant
 * @return mixed
 */
function perfex_saas_tenant_get_super_option($key = '', $tenant = null)
{
    $tenant = empty($tenant) ? perfex_saas_tenant() : $tenant;
    if (empty($tenant)) return null;

    $options = (array)($tenant->saas_options ?? []);
    if (empty($key)) return $options;

    return $options[$key] ?? null;
}

/**
 * Check if tenant may use a particular feature or settings
 *
 * @param string $permission
 * @return bool
 */
function perfex_saas_tenant_is_enabled($permission)
{
    $permission = 'perfex_saas_enable_' . $permission;
    $options = perfex_saas_tenant_get_super_option();
    if ($options && isset($options[$permission]))
        return $options[$permission] === 'yes' || ((int)$options[$permission]) === 1;

    return false;
}


/**
 * Get the active tenant slug
 *
 * @return     string|false
 */
function perfex_saas_tenant_slug()
{
    if (!perfex_saas_is_tenant()) return false;

    return defined('PERFEX_SAAS_TENANT_SLUG') ? PERFEX_SAAS_TENANT_SLUG : false;
}

/**
 * Prefix a text with the route ID
 *
 * @param string $append
 * @return string
 */
function perfex_saas_route_id_prefix($append)
{
    return PERFEX_SAAS_ROUTE_ID . '_' . $append;
}

/**
 * Get the database prefix for master instance.
 *
 * If the function `db_prefix()` exists, it will be used to retrieve the database prefix.
 * Otherwise, it will fallback to the default prefix 'tbl'.
 *
 * @return string The database prefix for Perfex SAAS custom tables.
 */
function perfex_saas_master_db_prefix()
{
    if (function_exists('db_prefix') && !defined('APP_DB_PREFIX'))
        return db_prefix();
    return 'tbl';
}

/**
 * Return tenant add with master db prefix
 *
 * @param string $slug
 * @param string $table
 * @return string
 */
function perfex_saas_tenant_db_prefix($slug, $table = '')
{
    $master_prefix = perfex_saas_master_db_prefix();
    $suffix = $master_prefix;
    if (!empty($table)) {
        $suffix = str_starts_with($table, $master_prefix) ? $table : $master_prefix . $table;
    }

    return perfex_saas_str_to_valid_table_name($slug) . '_' . $suffix;
}

/**
 * Convert string to table friendly name
 *
 * @param string $input_string
 * @return string
 * @throws Exception
 */
function perfex_saas_str_to_valid_table_name($input_string)
{
    // Remove any non-alphanumeric characters except underscores
    $clean_string = preg_replace('/[^a-zA-Z0-9_]/', '_', $input_string);

    // Remove leading digits or underscores
    $clean_string = preg_replace('/^[0-9_]+/', '', $clean_string);

    $clean_string = trim($clean_string, '_');

    // Ensure the table name starts with a letter or underscore
    if (!preg_match('/^[a-zA-Z_]/', $clean_string)) {
        throw new \Exception("Table name should start with underscore or letter", 1);
    }

    // throw error when length is close to half of 64 characters (database name limit)
    if (strlen($clean_string) > 32) throw new \Exception("Table name provided has exceed the 32 character limit: $clean_string", 1);

    return $clean_string;
}

/**
 * Handle conversion of underscore and dash in slug
 *
 * @param string $slug
 * @param string $scenario
 * @return string
 */
function perfex_saas_clean_slug($slug, $scenario = '')
{
    // translate underscore to dash for url friendly subdomain or path url.
    if ($scenario == 'url')
        $slug = str_replace('_', '-', $slug);
    else
        $slug = str_replace('-', '_', $slug); // translate dash to underscore for slug
    return $slug;
}

/**##################################################################################################################***
 *                                                                                                                      *
 *                                               Raw Database helpers                                                   *
 *                                                                                                                      *
 ***##################################################################################################################**/

/**
 * Retrieves the master database connection details.
 *
 * @return array  The master database connection details
 */
function perfex_saas_master_dsn()
{
    return array(
        'driver' => APP_DB_DRIVER,
        'host' => defined('APP_DB_HOSTNAME_DEFAULT') ? APP_DB_HOSTNAME_DEFAULT : APP_DB_HOSTNAME,
        'user' => defined('APP_DB_USERNAME_DEFAULT') ? APP_DB_USERNAME_DEFAULT : APP_DB_USERNAME,
        'password' => defined('APP_DB_PASSWORD_DEFAULT') ? APP_DB_PASSWORD_DEFAULT : APP_DB_PASSWORD,
        'dbname' => defined('APP_DB_NAME_DEFAULT') ? APP_DB_NAME_DEFAULT : APP_DB_NAME
    );
}

/**
 * Execute a raw SQL query using PDO and prevent SQL vulnerabilities.
 *
 * The query is executed using the provided PDO connection and can contain placeholders for query parameters.
 * The function supports single queries and multiple queries in an array.
 * The function is expected to be used internally and should be use with parameters when running input from the user/public.
 *
 * @param string|string[] $query              The SQL query or array of queries to execute.
 * @param array           $dsn                The database connection details. Defaults to an empty array.
 * @param bool            $return             Whether to return the query results. Defaults to false.
 * @param bool            $atomic             Whether to run the queries in a transaction. Defaults to true.
 * @param callable|null   $callback           Optional callback function to execute on each result row.
 * @param bool            $disable_foreign_key Whether to disable foreign key checks. Defaults to false.
 * @param bool            $stop_on_error      Whether to stop execution on the first query error. Defaults to true.
 * @param array           $query_params       Array of query parameters to bind to the prepared statement. Defaults to an empty array.
 *
 * @return mixed|null The query result or null if there was an error.
 *
 * @throws \PDOException If there is a database error and the environment is set to development.
 * @throws \Exception    If there is a non-database-related error and the environment is set to development.
 */
function perfex_saas_raw_query($query, $dsn = [], $return = false, $atomic = true, $callback = null, $disable_foreign_key = false, $stop_on_error = true, $query_params = [])
{

    if (empty($dsn)) {

        $dsn = perfex_saas_master_dsn();
    }

    if (is_string($dsn)) { //conn is dsn sting
        $dsn = perfex_saas_parse_dsn($dsn);
    }

    // Get PDO
    $pdo = perfex_saas_get_pdo_conn($dsn, true);

    $is_multi_query = is_array($query);
    $resultList = array();

    try {

        if ($is_multi_query && !empty($query_params))
            throw new \Exception("Query parameter binding is not supported for multiple query", 1);

        $pre_queries = [];
        $post_queries = [];
        $queries = $is_multi_query ? $query : [$query];

        if ($disable_foreign_key) {
            $pre_queries[] = "SET foreign_key_checks = 0;";
            $post_queries[] = "SET foreign_key_checks = 1;"; // add to end

            // Added for case of some db with this required i.e Digitalocean remote or mysql v8+
            $pre_queries[] = "SET SQL_REQUIRE_PRIMARY_KEY = 0;";
        }

        // Run prequeries. These are safe to run without binding
        foreach ($pre_queries as $pr_q) {
            try {
                $pdo->query($pr_q);
            } catch (\Throwable $th) {
                if (stripos($th->getMessage(), 'SQL_REQUIRE_PRIMARY_KEY') == false)
                    log_message("error", "Database Error: " . $th->getMessage() . ': ' . $pr_q);
            }
        }

        if ($atomic) {
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
            $pdo->beginTransaction();
        }


        foreach ($queries as $index => $q) {

            $stmt = false;

            if (!$stop_on_error) {
                try {
                    $stmt = perfex_saas_pdo_safe_query($pdo, $q, $query_params);
                } catch (\Throwable $th) {
                    log_message("error", "Database Error: " . $th->getMessage());
                    $stmt = false;
                }
            } else {
                $stmt = perfex_saas_pdo_safe_query($pdo, $q, $query_params);
            }


            $results = [false];

            if ($stmt) {

                $results = [true];

                if ($return) {
                    $results = [];
                    while ($row = $stmt->fetchObject()) {
                        $results[] = $row;
                        if ($callback && is_callable($callback)) {
                            call_user_func($callback, $row);
                        }
                    }
                }
                $stmt->closeCursor();
            }

            $resultList[$index] = $results;
        }

        // Safe queries
        foreach ($post_queries as $po_q) {
            try {
                $pdo->query($po_q);
            } catch (\Throwable $th) {
                log_message("error", "Database Error: " . $th->getMessage() . ': ' . $po_q);
            }
        }

        if ($atomic) {
            try {
                $pdo->commit();
            } catch (\Throwable $th) {
            }
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
        }
    } catch (\Throwable $e) {

        log_message("error", "Database Error: " . $e->getMessage() . ': ' . @$q);

        if ($atomic) {
            try {
                $pdo->rollBack();
            } catch (\Throwable $th) {
            }
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
        }

        // Close connections
        unset($stmt);
        unset($pdo);

        if (ENVIRONMENT === 'development') {
            throw $e;
        }
        return null;
    }

    // Close connections
    unset($stmt);
    unset($pdo);

    return $is_multi_query ? $resultList : $resultList[0];
}

/**
 * Executes a safe query using prepared statements with PDO.
 *
 * @param PDO $pdo The PDO instance.
 * @param string $query The SQL query.
 * @param array $parameters The query parameters to bind.
 * @return PDOStatement|false The PDOStatement object if successful, false otherwise.
 */
function perfex_saas_pdo_safe_query($pdo, $query, $parameters)
{
    $statement = $pdo->prepare($query);

    foreach ($parameters as $key => $value) {
        $statement->bindParam($key, $value);
    }

    return $statement->execute() ? $statement : false;
}

/**
 * Executes a raw query and returns the first row of the result set.
 *
 * @param string|string[] $query The SQL query.
 * @param array $dsn The connection details.
 * @param bool $return Whether to return the result set or not.
 * @param bool $atomic Whether to run the query in a transaction or not.
 * @param array $query_params The query parameters.
 * @return mixed|null The first row of the result set if successful, null otherwise.
 */
function perfex_saas_raw_query_row($query, $dsn = [], $return = false, $atomic = true, $query_params = [])
{
    $result = perfex_saas_raw_query($query, $dsn, $return, $atomic, null, false, true, $query_params);
    if (!$result) {
        return $result;
    }
    return $result[0];
}


/**
 * Executes a database query based on the provided SQL statement in context of the current instance.
 *
 * @param string $sql The SQL query.
 * @return mixed The result of the query execution.
 */
function perfex_saas_db_query($sql)
{
    $slug = perfex_saas_tenant_slug();

    if (!$slug) {
        // Default saas panel.
        if (
            stripos($sql, perfex_saas_table('')) !== false //saas table queries
        )
            return $sql;

        // Always set this for security. This ensure other tenant data is not loaded for master instance on multitenant singledb
        $slug = perfex_saas_master_tenant_slug();
    }
    return perfex_saas_simple_query($slug, $sql);
}


/**
 * Function validate SQL QUERY.
 * This function can be used for logging all SQL queries and make extra validations
 *
 * @param string $slug The tenant slug.
 * @param string $sql The SQL query.
 * @throws \Exception
 * @return mixed|string The parsed SQL query or the result of the query execution.
 * @todo Improve performance.
 */
function perfex_saas_simple_query($slug, $sql)
{
    $is_master = $slug === perfex_saas_master_tenant_slug();
    if ($is_master) return $sql;

    $master_dbprefix = perfex_saas_master_db_prefix();
    $tenant_dbprefix = perfex_saas_tenant_db_prefix($slug);

    $sql = trim($sql);

    // Detect wiki third party module and exempt patch due to use of conflicting namespace in the module. Exempted queries will be intercepted if will change db structure
    $exemptPatching = strpos($sql, 'TBLArticles') && strpos($sql, 'TBLBooks') && strpos($sql, $tenant_dbprefix . 'wiki_books') && strpos($sql, $tenant_dbprefix . 'wiki_articles');

    // Ebb developer habit control for hard coded tables
    if (!$exemptPatching) {
        // Perfex crm and some author often used the default db prefix 'tbl' hardcoded sometimes in some queries. i.e /application/libraries/import/Import_leads.php LINE 47 and 48:
        // @todo Remove this line when Perfex core fixes above mentioned instances.
        $sql = str_ireplace($tenant_dbprefix . 'tbl', $tenant_dbprefix, $sql);
        // end of patch to remove

        if (stripos($sql, $master_dbprefix)) {

            $sql = str_ireplace(
                ["`$master_dbprefix", "($master_dbprefix", ".$master_dbprefix"],
                ["`$tenant_dbprefix", "($tenant_dbprefix", ".$tenant_dbprefix"],
                $sql
            );

            // Handle cases of namespace select statement
            $pattern = '/(?<=\s|,)\b' . $master_dbprefix . '(\w*)(?=\.)/';
            $replacement = $tenant_dbprefix . '$1';
            $sql = preg_replace($pattern, $replacement, $sql);

            if (stripos($sql, ' ' . $master_dbprefix)) {
                // Handle common SQL keywords prefix
                // @since v0.2.6 Previous str_ireplace method was converted to preg replace for speed
                $pattern = '/\b(FROM|INSERT INTO|UPDATE|DELETE FROM|CREATE TABLE|ALTER TABLE|ORDER BY|JOIN)\s+' . preg_quote($master_dbprefix, '/') . '/i';
                $replacement = '$1 ' . $tenant_dbprefix;
                $sql = preg_replace($pattern, $replacement, $sql);
            }
        }
    }
    // End of patch

    $will_change_db_struct = stripos($sql, 'ALTER TABLE') !== false ||
        stripos($sql, 'TRUNCATE TABLE') !== false ||
        stripos($sql, 'DROP TABLE') !== false ||
        stripos($sql, 'RENAME TABLE') !== false ||
        (stripos($sql, ' RENAME') !== false && stripos($sql, 'ALTER TABLE') !== false) ||
        str_starts_with($sql, 'DROP DATABASE') || str_starts_with($sql, 'GRANT');

    // Deny, unsupported, tenant shouldnt be able to do any of this query
    if ($will_change_db_struct) {

        $parser = new PHPSQLParser($sql);
        $parsed  = $parser->parsed;

        $key = strtoupper(key($parsed));

        if (!in_array($key, ['TRUNCATE', 'DROP', 'RENAME'])) {

            // Any normal insert or write query should not reach here and we can loosly check for master prefix in query.
            $_sql = trim($sql);
            $will_change_db_struct_on_master = stripos($_sql, ' ' . $master_dbprefix) !== false ||
                stripos($_sql, '`' . $master_dbprefix) !== false ||
                stripos($_sql, '(' . $master_dbprefix) !== false ||
                stripos($_sql, '.' . $master_dbprefix) !== false;

            if ($will_change_db_struct_on_master) {
                throw new \Exception("Query running out of bounds for the tenant: $sql", 1);
            }

            // Add this block to handle constraint key for singledatabase multiple tenant cases.
            // Otherwise, sharing module with constrain key prevent installation on same database with multiple tenants.
            if ($key === 'ALTER') {
                if (stripos($sql, 'ADD CONSTRAINT ') !== false) {
                    $backtick_version_absent =  stripos($_sql, 'ADD CONSTRAINT `' . $slug) === false;
                    $non_backtick_version_absent = stripos($_sql, 'ADD CONSTRAINT ' . $slug) === false;
                    if ($backtick_version_absent) {
                        $sql = str_ireplace('ADD CONSTRAINT `', 'ADD CONSTRAINT `' . $slug . '_', $_sql);
                    } else if ($non_backtick_version_absent && $backtick_version_absent) {
                        $sql = str_ireplace('ADD CONSTRAINT ', 'ADD CONSTRAINT ' . $slug . '_', $_sql);
                    }
                }
            }

            return $sql;
        }


        // Perform more complex check.
        // Permit queries on table that belong to the tenant without a database namespace
        $query_table_names = [];
        perfex_saas_extract_dangerous_query_table_name($parsed, $query_table_names);
        if (!empty($query_table_names)) {
            // Found tables, check if all tables belong to the tenant or not
            $_tables_with_issues = [];
            foreach ($query_table_names as $table) {
                $table = str_replace(['"', "'", "`"], '', trim($table));
                if (stripos($table, '.') !== false || !str_starts_with($table, $tenant_dbprefix)) {
                    $_tables_with_issues[] = $table;
                    break;
                }
            }
            if (empty($_tables_with_issues))
                return $sql;
        }

        // @todo Evaluate allowing all query pass through

        throw new \Exception("Unsupported query for tenant: $sql", 1);
    }

    return $sql;
}

/**
 * Function to recursively search for the 'table' key in the parsed array
 *
 * @param array $parsed
 * @return array
 */
function perfex_saas_extract_dangerous_query_table_name($parsed, &$tableNames)
{
    // Check if the parsed array contains the 'table' key directly
    if (isset($parsed['table'])) {
        $tableNames[] = $parsed['table'];
    }

    // Recursively search in the sub-trees for more table names
    foreach ($parsed as $value) {
        if (isset($value['expr_type']) && $value['expr_type'] !== 'table' && $value['expr_type'] !== 'expression')
            continue;

        if (is_array($value)) {
            perfex_saas_extract_dangerous_query_table_name($value, $tableNames);
        }
    }
}



/**##################################################################################################################***
 *                                                                                                                      *
 *                                               Database locator and DSN                                               *
 *                                                                                                                      *
 ***##################################################################################################################**/

/**
 * Check if a domain string is valid domain name.
 *
 * @return bool
 */
function perfex_saas_is_valid_custom_domain($domain)
{

    // Length Check
    if (strlen($domain) > 255) {
        return false;
    }

    // Character Set Check
    if (!preg_match('/^[A-Za-z0-9.-]+$/', $domain)) {
        return false;
    }

    if (str_starts_with($domain, '.') || str_ends_with($domain, '.') || !stripos($domain, '.'))
        return false;

    // Dont allow subdomain of main host
    if (str_ends_with($domain, perfex_saas_get_saas_default_host()))
        return false;


    // Label Length Check
    $labels = explode('.', $domain);
    foreach ($labels as $label) {
        if (strlen($label) > 63) {
            return false;
        }
    }

    if (!filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
        return false;
    }

    return true;
}

/**
 * Convert an array DSN to a string representation.
 * 
 * @param array $dsn The array DSN containing driver, host, dbname, user, and password.
 * @param bool $with_auth Whether to include authentication details in the DSN.
 * @return string The DSN string representation.
 */
function perfex_saas_dsn_to_string(array $dsn, $with_auth = true)
{
    // Extract the individual components from the DSN array
    $driver = $dsn['driver'] ?? APP_DB_DRIVER;
    $host = $dsn['host'] ?? APP_DB_HOSTNAME_DEFAULT;
    $dbname = $dsn['dbname'] ?? '';
    $user = $dsn['user'] ?? '';
    $password = $dsn['password'] ?? '';

    // Build the basic DSN string
    $dsn_string = $driver . ':host=' . $host . ';dbname=' . $dbname;

    // If 'with_auth' is false, return the basic DSN string without authentication details
    if (!$with_auth) {
        return $dsn_string;
    }

    // Append the authentication details to the DSN string
    $dsn_string = $dsn_string . ';user=' . $user . ';password=' . $password . ';';

    return $dsn_string;
}



/**
 * Parse a DSN string and return the parsed components.
 *
 * Example dsn string: mysql:host=127.0.0.1;dbname=demodb;user=demouser;password=diewo;eg@j$l!;
 * DSN should follow above pattern and should ends with ";".
 * 
 * @param string $dsn The DSN string to parse.
 * @param array $returnKeys The specific keys to return from the parsed DSN.
 * @return array The parsed DSN components.
 * @throws Exception When the DSN string is empty or invalid.
 */
function perfex_saas_parse_dsn($dsn, $returnKeys = [])
{
    // Define the default indexes for parsing
    $indexes = ['host', 'dbname', 'user', 'password'];

    // Check if specific keys are requested for return
    $returnSet = is_array($returnKeys) && !empty($returnKeys);
    if ($returnSet) {
        $indexes = $returnKeys;
    }

    // Check if the DSN string is empty or invalid
    if (empty($dsn) || (false === ($pos = stripos($dsn, ":")))) {
        $error = "Empty or Invalid DSN string";
        log_message("error", "$error: $dsn");
        throw new Exception($error);
    }

    // Extract the driver from the DSN string
    $driver = strtolower(substr($dsn, 0, $pos)); // always returns a string

    // Check if the driver is empty
    if (empty($driver)) {
        throw new Exception(_l("perfex_saas_invalid_dsn_no_driver"));
    }

    // Initialize the parsed DSN array with the driver
    $parsedDsn = ['driver' => $driver];

    // Define the keys used for mapping and their order in the DSN string
    $mapKeys = [':host=', ';dbname=', ';user=', ';password='];

    // Iterate through the map keys to extract values from the DSN string
    foreach ($mapKeys as $i => $key) {
        $position = stripos($dsn, $key);
        $nextPosition = ($i + 1) >= count($mapKeys) ? stripos($dsn, ';', -1) : stripos($dsn, $mapKeys[$i + 1]);

        // Get the length of the value using the next position minus the key position
        $valueLength = $nextPosition - $position;
        $value = substr($dsn, $position, $valueLength);

        // Remove the key from the captured value
        $value = str_ireplace($key, '', $value);

        // Clean the DSN key
        $key = str_ireplace([':', '=', ';'], '', $key);

        $parsedDsn[$key] = $value;
    }

    // Set the return value based on the requested keys
    $r = $parsedDsn;

    if ($returnSet) {
        $r = [];
        foreach ($indexes as $key) {
            // Check if the parsed DSN contains the requested key
            if (!isset($parsedDsn[$key])) {
                throw new RuntimeException(_l('perfex_saas_dsn_missing_key', $key));
            }

            $r[$key] = $parsedDsn[$key];
        }
    }

    return $r;
}

/**
 * Check if a DSN is valid by testing the database connection.
 *
 * @param array $dsn The DSN array to validate.
 * @param bool $use_cache Flag to indicate whether to use the cached connection.
 * @return bool|string Returns true if the DSN is valid, otherwise returns an error message.
 */
function perfex_saas_is_valid_dsn(array $dsn, $use_cache = true)
{
    try {
        // Check if the required DSN components (host, user, dbname) are present
        if (empty($dsn['host'] ?? '') || empty($dsn['user'] ?? '') || empty($dsn['dbname'] ?? '')) {
            throw new \Exception(_l('perfex_saas_host__user_and_dbname_is_required_for_valid_dsn'), 1);
        }

        // Test the database connection
        $conn = perfex_saas_get_pdo_conn($dsn, $use_cache);

        if (!$conn) {
            throw new \Exception("Error establishing connection", 1);
        }

        return true;
    } catch (\Throwable $th) {
        return $th->getMessage();
    }
}

/**
 * Get a PDO database connection based on the provided DSN.
 *
 * @param array $dsn The DSN array containing driver, host, dbname, user, and password.
 * @param bool $use_cache Flag to indicate whether to use the cached connection.
 * @return PDO The PDO database connection.
 */
function perfex_saas_get_pdo_conn($dsn, $use_cache = true)
{
    // PDO uses 'mysql' instead of 'mysqli'
    if (!isset($dsn['driver']) || (isset($dsn['driver']) && $dsn['driver'] == 'mysqli')) {
        $dsn['driver'] = 'mysql';
    }

    $dsn_string = perfex_saas_dsn_to_string($dsn, false);

    $pdo = SaaSPDOConnectionManager::getConnection(
        $dsn_string,
        $dsn['user'],
        $dsn['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'"
        ],
        $use_cache
    );
    return $pdo;
}




/**##################################################################################################################/**
 *                                                                                                                      *
 *                                                    UI and Http helpers                                               *
 *                                                                                                                      *
 ***##################################################################################################################**/

/**
 * Show a custom error page for the tenant (middleware).
 *
 * @param string $heading The heading of the error page.
 * @param string $message The error message to display.
 * @param int $error_code The error code to display (default: 403).
 * @param string $template The error template file to use (default: '404').
 * @param string $tag The error tag
 */
function perfex_saas_show_tenant_error($heading, $message, $error_code = 403, $template = '404', $tag = '')
{
    // Apply the filter to determine if the abrupting error should be prevented or not
    if (function_exists('hooks')) {
        $filter = hooks()->apply_filters('perfex_saas_show_tenant_error', ['show_error' => true, 'tag' => $tag, 'heading' => $heading, 'message' => $message, 'error_code' => $error_code, 'template' => $template]);

        if (isset($filter['show_error']) && $filter['show_error'] != true) return;

        $message = $filter['message'] ?? $message;
        $heading = $filter['heading'] ?? $heading;
        $error_code = $filter['error_code'] ?? $error_code;
        $template = $filter['template'] ?? $template;
    }

    $error_file = APPPATH . 'views/errors/html/error_' . $template . '.php';

    $message = "
        $message 
        <script>
            let tag = document.querySelector('h2');
            tag.innerHTML = tag.innerHTML.replace('404', '$error_code');
        </script>
    ";

    if (file_exists($error_file)) {
        require_once($error_file);
        exit();
    }

    echo ($heading . '<br/><br/>' . $message);
    exit();
}




/**
 * Generate the URL signature for the tenant withot trailing (left or right) slash
 *
 * @param string $slug The slug of the tenant.
 * @return string The URL signature.
 */
function perfex_saas_tenant_url_signature($slug)
{
    $path_prefix = PERFEX_SAAS_ROUTE_ID;
    return "$slug/$path_prefix";
}


/**
 * Get the host name base on the server variables.
 *
 * @param array $server The server variables.
 * @param bool $use_forwarded_host Whether to use the forwarded host.
 * @return string The http host name.
 * @throws Exception When no valid host if found
 */
function perfex_saas_http_host($server, $use_forwarded_host = true)
{
    $host = ($use_forwarded_host && isset($server['HTTP_X_FORWARDED_HOST'])) ? $server['HTTP_X_FORWARDED_HOST'] : (isset($server['HTTP_HOST']) ? $server['HTTP_HOST'] : null);
    if (empty($host) || !filter_var($host, FILTER_VALIDATE_DOMAIN))
        throw new \Exception("Error detecting valid http host", 1);
    return $host;
}

/**
 * Determine if the server request is https (TLS).
 * Attempt to detect using CI is_https. If not, it check for other fallbacks.
 *
 * @param array $server Optional
 * @return bool
 */
function perfex_saas_is_https($server = [])
{
    if (function_exists('is_https') && is_https()) {
        return true;
    }

    $server = empty($server) ? $_SERVER : $server;

    if (isset($server['HTTP_X_FORWARDED_PROTO'])) {
        if ($server['HTTP_X_FORWARDED_PROTO'] == 'https') {
            return true;
        }
    }

    if (isset($server['HTTP_CF_VISITOR'])) {
        $cf_visitor = json_decode($server['HTTP_CF_VISITOR']);
        if (isset($cf_visitor->scheme) && $cf_visitor->scheme == 'https') {
            return true;
        }
    }

    if (isset($server['SERVER_PROTOCOL'])) {
        $ssl = isset($server['HTTPS']) && $server['HTTPS'] == 'on';
        $sp = strtolower($server['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . ($ssl ? 's' : '');
        if ($protocol == 'https') {
            return true;
        }
    }

    return false;
}

/**
 * Get the URL origin based on the server variables.
 *
 * @param array $server The server variables.
 * @param bool $use_forwarded_host Whether to use the forwarded host.
 * @return string The URL origin.
 */
function perfex_saas_url_origin($server, $use_forwarded_host = true, $include_full_uri = false)
{
    $ssl = perfex_saas_is_https($server);
    $protocol = $ssl ? 'https' : 'http';
    $port = $server['SERVER_PORT'];
    $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
    $host = perfex_saas_http_host($server, $use_forwarded_host);
    $host = isset($host) ? $host : $server['SERVER_NAME'] . $port;

    $folder = parse_url(perfex_saas_default_base_url(), PHP_URL_PATH);

    if ($include_full_uri) {
        $folder = $server['REQUEST_URI'] ?? '';
    }

    return trim($protocol . '://' . $host . $folder, '/');
}


/**
 * Redirect the user back to the previous page or a default page.
 */
function perfex_saas_redirect_back()
{
    if (function_exists('redirect')) {

        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            return redirect($_SERVER['HTTP_REFERER']);
        }

        if (function_exists('admin_url')) {
            // If HTTP_REFERER is not set or empty, redirect to a default page
            return redirect(admin_url());
        }
    }

    header('Location: ' . perfex_saas_url_origin($_SERVER));
    exit();
}


/**
 * Perform an HTTP request using cURL.
 *
 * @param string $url     The URL to send the request to.
 * @param array  $options An array of options for the request.
 *
 * @return array An array containing the 'error' and 'response' from the request.
 */
function perfex_saas_http_request($url, $options)
{
    // Initialize cURL
    $curl = curl_init($url);

    // Set SSL verification and timeout options
    $verify_ssl = (int) ($options['sslverify'] ?? 0);
    $timeout = (int) ($options['timeout'] ?? 30);

    if ($options) {
        // Get request method
        $method = strtoupper($options["method"] ?? "GET");

        // Get request data and headers
        $data = @$options["data"];
        $headers = (array) @$options["headers"];

        // Set JSON data and headers for POST requests
        if ($method === "POST") {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        // Set custom headers if provided
        if ($headers) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
    }

    // Set common cURL options
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYHOST => $verify_ssl,
        CURLOPT_TIMEOUT => (int) $timeout,
    ]);

    // Make the request
    $result = curl_exec($curl);

    // Check for errors
    $error = '';
    if (curl_error($curl) || curl_errno($curl)) {
        $error = 'Curl Error - "' . curl_error($curl) . '" - Code: ' . curl_errno($curl);
    }

    // Close the cURL session
    curl_close($curl);

    return ['error' => $error, 'response' => $result];
}

/**
 * Get the list of modules available to the tenant.
 *
 * @param object|null $tenant                       The tenant object. If null, the current tenant will be used.
 * @param bool        $include_saas_module           Whether to include the SAAS module in the list.
 * @param bool        $include_tenant_disabled_modules Whether to include tenant-disabled modules. i.e modules in deactivated list from disabled modules on saas client portal
 * @param bool        $include_admin_disabled_modules  Whether to include admin-disabled modules.
 * @param bool        $include_registered_global_perfex_saas_modules  Whether to include Perfex Saas specific modules registered as global (cross-contex)
 * @param bool        $include_tenant_local_disabled_modules Whether to include tenant local disabled modules. i.e modules deactivated through the tenant admin modules page
 *  
 * @return array The list of tenant modules.
 */
function perfex_saas_tenant_modules(
    object $tenant = null,
    bool $include_saas_module = true,
    bool $include_tenant_disabled_modules = false,
    bool $include_admin_disabled_modules = false,
    bool $include_registered_global_perfex_saas_modules = false,
    bool $include_tenant_local_disabled_modules = false
) {
    // Get the tenant object
    $tenant = $tenant ?? perfex_saas_tenant();

    // Get the package and modules
    $package = isset($tenant->package_invoice) ? $tenant->package_invoice : null;
    $modules = (array) ($package->modules ?? []);

    // Get the metadata and approved/disabled modules
    $metadata = (object) $tenant->metadata;
    $admin_approved_modules = isset($metadata->admin_approved_modules) ? (array) $metadata->admin_approved_modules : [];
    $admin_disabled_modules = isset($metadata->admin_disabled_modules) ? (array) $metadata->admin_disabled_modules : [];
    $disabled_modules = isset($metadata->disabled_modules) ? (array) $metadata->disabled_modules : [];

    $tenant_modules = [];

    // Include SAAS module if required.
    // NOTE: make saas module the first module to be loaded when $include_saas_module is true
    if ($include_saas_module) {
        $tenant_modules[] = PERFEX_SAAS_MODULE_NAME;
    }

    // Include user purchased modules
    $purchased_modules = (array)($tenant->package_invoice->purchased_modules ?? []);

    // Merge package modules and admin-approved modules
    $tenant_modules = array_merge($tenant_modules, $modules, $admin_approved_modules, $purchased_modules);

    // Include module specific to Perfex SaaS module that is registered as global (cross-context)
    if ($include_registered_global_perfex_saas_modules) {
        $tenant_modules = array_merge($tenant_modules, perfex_saas_registered_extensions($tenant));
    }

    // Make the package and assigned modules unique
    $tenant_modules = array_filter($tenant_modules, 'strlen');
    $tenant_modules = array_unique($tenant_modules);

    // Remove disabled modules if not included
    if (!$include_tenant_disabled_modules) {
        $tenant_modules = array_diff($tenant_modules, $disabled_modules);
    }

    // Remove admin-disabled modules if not included
    if (!$include_admin_disabled_modules) {
        $tenant_modules = array_diff($tenant_modules, $admin_disabled_modules);
    }

    // Remove tenant admin local deactivated modules. 
    // This can only be used in tenant context to prevent DB call to the tenant instance.
    if (!$include_tenant_local_disabled_modules && perfex_saas_is_tenant()) {
        $locally_disabled_modules = (array)json_decode(get_option('tenant_local_disabled_modules') ?? '');
        $tenant_modules = array_diff($tenant_modules, $locally_disabled_modules);
    }

    return (array) $tenant_modules;
}

/**
 * Get the list of saas module extension modules.
 *
 * @param object|null $tenant                       The tenant object. If null, the current tenant will be used.
 * @param bool        $global_only           Where to include the global registered extension or not.
 * *
 * @return array The list of modules that are extension of the Perfex SaaS module
 */
function perfex_saas_registered_extensions(object $tenant = null, $global_only = true)
{
    // Get the tenant object
    $tenant = $tenant ?? perfex_saas_tenant();

    $perfex_saas_global_active_modules = [];

    if ($global_only) {
        try {
            $global_active_modules = perfex_saas_tenant_get_super_option(PERFEX_SAAS_GLOBAL_ACTIVE_MODULES_OPTION_KEY, $tenant);

            if (!empty($global_active_modules)) {
                $perfex_saas_global_active_modules =  (array)json_decode($global_active_modules);
            }
        } catch (\Throwable $th) {
            if (function_exists('log_message'))
                log_message('error', $th->getMessage() . ' ' . $th->getTraceAsString());
        }
    }

    return $perfex_saas_global_active_modules;
}

/**
 * Get the list of default modules disabled for the tenant.
 *
 * @param object|null $tenant   The tenant object. If null, the current tenant will be used.
 *
 * @return array    The list of tenant disabled default modules.
 */
function perfex_saas_tenant_disabled_default_modules(object $tenant = null, $mode = "controller")
{
    static $cache = [];

    if (isset($cache[$mode]))
        return $cache[$mode];

    $can_cache = false;

    // Get the tenant object
    $tenant = $tenant ?? perfex_saas_tenant();

    // Get the package and modules
    $package = isset($tenant->package_invoice) ? $tenant->package_invoice : null;
    $modules = (array) ($package->metadata->disabled_default_modules ?? []);

    // Get the metadata and approved/disabled modules
    $metadata = (object) ($tenant->metadata ?? []);
    $admin_disabled_modules =  (array) ($metadata->admin_disabled_default_modules ?? []);

    // Merge package modules and admin-approved modules
    $tenant_default_disabled_modules = array_merge($modules, $admin_disabled_modules);

    if (function_exists('hooks')) {
        $tenant_default_disabled_modules  = hooks()->apply_filters('perfex_saas_tenant_disabled_default_modules_list_filter', $tenant_default_disabled_modules, $tenant, $mode);
        $can_cache = true;
    }

    if ($mode !== "controller") {
        // Adapt for menu and tabs check
        foreach ($tenant_default_disabled_modules as $key => $value) {
            if (empty($value)) unset($tenant_default_disabled_modules[$key]);
            if (stripos($value, '_') !== false)
                $tenant_default_disabled_modules[] = str_replace('_', '-', $value);

            if ($value === 'tickets') {
                $tenant_default_disabled_modules[] = 'support';
            }
        }
    }

    $tenant_default_disabled_modules = array_filter($tenant_default_disabled_modules, 'strlen');

    $tenant_default_disabled_modules = perfex_saas_alias_disabled_default_modules($tenant_default_disabled_modules);

    if ($can_cache) {
        $cache[$mode] = $tenant_default_disabled_modules;
    }

    return $tenant_default_disabled_modules;
}

/**
 * Alias default disabled modules
 *
 * @param array $default_disabled_modules
 *
 * @return array The list of aliased disabled default modules.
 */
function perfex_saas_alias_disabled_default_modules($default_disabled_modules)
{
    // Add controller alias for some specific cases
    if (in_array('invoices', $default_disabled_modules)) {
        $default_disabled_modules[] = 'taxes';
        $default_disabled_modules[] = 'currencies';
    }

    if (in_array('payments', $default_disabled_modules)) {
        $default_disabled_modules[] = 'paymentmodes';
        $default_disabled_modules[] = 'currencies';
    }

    if (in_array('credit_notes', $default_disabled_modules)) {
        $default_disabled_modules[] = 'creditnotes';
    }

    return $default_disabled_modules;
}


/**
 * Get master settings from options table.
 *
 * @param mixed $fields     The masked fields.
 * @param bool $parse       If to flatten the result into field => value pair
 * @return mixed            The shared secret master settings.
 * @Bridge-Function
 */
function perfex_saas_get_options($field, bool $parse = true)
{
    $single_option = is_string($field);
    $fields = "'" . implode("','", $single_option ? [$field] : $field) . "'";
    $option_query = 'SELECT name, value FROM ' . perfex_saas_master_db_prefix() . "options WHERE `name` IN ($fields)";

    // Perform a raw query to fetch the shared secret master settings
    $result = perfex_saas_raw_query($option_query, [], true);

    if (!$parse) return $result;

    $fields_value = [];
    foreach ($result as $row) {
        $fields_value[$row->name] = $row->value;
    }

    if ($single_option) return isset($fields_value[$field]) ? $fields_value[$field] : '';

    return $fields_value;
}

/**
 * Update master settings value
 *
 * @param string $field
 * @param string $value
 * @return mixed
 * @Bridge-Function
 */
function perfex_saas_update_option($field, $value)
{
    $option_query = 'UPDATE `' . perfex_saas_master_db_prefix() . "options` SET `value` ='$value' WHERE `name`='$field'";
    return perfex_saas_raw_query($option_query, []);
}

/**
 * Save or update client metatdata
 *
 * @param mixed $clientid
 * @param array $update_data
 * @return array|null Return array of the metadata field when matched or updated otherwise null.
 * @Bridge-Function
 */
function perfex_saas_get_or_save_client_metadata($clientid, $update_data = [])
{
    $table = perfex_saas_table('client_metadata');
    $client_metadata = perfex_saas_raw_query_row("SELECT * FROM $table WHERE `clientid`='$clientid';", [], true);
    $metadata = empty($client_metadata->metadata) ? [] : (array)json_decode($client_metadata->metadata);

    if (empty($update_data)) {
        return $metadata;
    }

    $id = $client_metadata->id ?? null;
    $where = !empty($id) ? "WHERE `id`='$id' AND `clientid`='$clientid'" : '';
    $metadata = array_merge($metadata, $update_data);
    $metadata_json = json_encode($metadata);
    if (!empty($id))
        $query = "UPDATE $table SET `metadata`='$metadata_json', `clientid` = '$clientid' $where";
    else
        $query = "INSERT INTO `$table` (`id`, `metadata`, `clientid`) VALUES (NULL, '$metadata_json', '$clientid')";

    $updated = perfex_saas_raw_query($query);
    if ($updated)
        return $metadata;

    return null;
}

/**
 * Search the meta field column of client table by a field and value
 *
 * @param string $field
 * @param mixed $value
 * @param mixed $clientid
 * @return array|null
 */
function perfex_saas_search_client_metadata($field, $value, $is_unsigned = true, $clientid = null)
{
    $table = perfex_saas_table('client_metadata');

    // Start building the query with named parameters
    if ($is_unsigned)
        $query = "SELECT * FROM $table WHERE CAST(JSON_EXTRACT(metadata, '$.$field') as UNSIGNED) = :value";
    else
        $query = "SELECT * FROM $table WHERE JSON_EXTRACT(metadata, '$.$field') = :value";

    // Parameters to bind
    $params = [
        'value'     => $value      // Value to compare
    ];

    // If client ID is provided, add it to the query and parameters
    if ($clientid) {
        $query .= " AND `clientid` = :clientid";
        $params['clientid'] = $clientid;
    }

    $client_metadata = perfex_saas_raw_query_row($query, [], true, false, $params);
    $metadata = empty($client_metadata->metadata) ? [] : (array)json_decode($client_metadata->metadata);
    return isset($metadata[$field]) && $metadata[$field] == $value ? $metadata : null;
}

/**
 * Prepare a remote url to perform system update/extension
 *
 * @param string $purchase_code
 * @param integer $action
 * @param string $module
 * @return string
 */
function perfex_saas_get_system_update_url($purchase_code, $action = 1, $module = '')
{
    return str_replace(['[PC]', '[AC]', '[MD]'], [$purchase_code, $action, $module], PERFEX_SAAS_UPDATE_URL);
}