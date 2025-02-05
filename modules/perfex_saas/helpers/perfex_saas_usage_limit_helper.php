<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Map of limitation filters and their corresponding tables.
 */
const PERFEX_SAAS_LIMIT_FILTERS_TABLES_MAP = [
    'before_create_staff_member' => 'staff',
    'before_client_added' => 'clients',
    'before_create_contact' => 'contacts',
    'before_contract_added' => 'contracts',
    'before_invoice_added' => 'invoices',
    'before_estimate_added' => 'estimates',
    'before_create_credit_note' => 'creditnotes',
    'before_create_proposal' => 'proposals',
    'before_add_project' => 'projects',
    'before_add_task' => 'tasks',
    'before_ticket_created' => 'tickets', // has two option $data and $admin
    'before_lead_added' => 'leads',
    'before_item_created' => 'items',
];

const PERFEX_SAAS_LIMIT_FILTERS_TABLES_CREATED_DATE_MAP = [
    'contracts' => 'dateadded',
    'projects' => 'project_created',
    'tasks' => 'dateadded',
    'tickets' => 'date',
    'leads' => 'dateadded',
];

/**
 * Register the limitation filters and their corresponding validation function.
 */
function perfex_saas_register_limitation_filters()
{
    foreach (PERFEX_SAAS_LIMIT_FILTERS_TABLES_MAP as $event => $table) {
        // Set priority to 0 as we want this to run before any other attached hooks to the filter.
        hooks()->add_filter($event, 'perfex_saas_validate_limits', 0);
    }

    // Register for storage limit
    $tenant = perfex_saas_tenant();
    if (!perfex_saas_tenant_storage_is_unlimited($tenant)) {

        // Check tenant storage limits
        $storage_validation_handler = function () {
            $tenant = perfex_saas_tenant();
            load_admin_language();
            perfex_saas_limit_reached_middleware(
                'storage',
                _l('perfex_saas_storage_exhausted', perfex_saas_tenant_used_storage($tenant, true) . '/' . perfex_saas_tenant_storage_limit($tenant)),
                _l('perfex_saas_quota_exhausted_cron', [$tenant->slug, "storage"])
            );
        };

        // Detect file upload and check for storage limits
        if (!empty($uploaded_files = $_FILES)) {

            // Update storage size use.
            // @todo Ensure this wont be taking too long and move to cron only in future if possible. 
            $tenant = perfex_saas_update_tenant_storage_size($tenant);

            // Check if user have enough storage left
            if (!perfex_saas_tenant_has_enough_storage($tenant, $uploaded_files)) {
                $storage_validation_handler();
            }
        }

        hooks()->add_action('before_make_backup', function () use ($storage_validation_handler) { // Backup folder limit
            if (!perfex_saas_tenant_has_enough_storage(perfex_saas_tenant())) {
                $storage_validation_handler();
            }
        });
    }
}

/**
 * Validate the limits for a specific event.
 * i.e check limit for invoices.
 *
 * @param mixed $data - The data passed to the filter hook.
 * @param mixed $admin - Optional. The admin data passed to the filter hook.
 * @return mixed - The filtered data.
 * @throws \Exception - When an unsupported limitation filter is encountered.
 */
function perfex_saas_validate_limits($data, $admin = null)
{
    // Get the active filter
    $filter = hooks()->current_filter();

    // Get the filter table
    $limit_name = PERFEX_SAAS_LIMIT_FILTERS_TABLES_MAP[$filter];

    // Ensure we have a table for the filter
    if (empty($limit_name)) {
        throw new \Exception("Unsupported limitation filter: $filter", 1);
    }

    // Get the tenant 
    $tenant = perfex_saas_tenant();

    // Get tenant details and get package limit
    $quota = perfex_saas_tenant_resources_quota($tenant, $limit_name);

    // Ulimited pass
    if ($quota === -1) return $data;

    // Get count for the active tenant from table and match against package
    $usage = perfex_saas_get_tenant_quota_usage($tenant, [$limit_name])[$limit_name];

    // If quota is exceeded, set flash and redirect back
    $reached_limit = $quota <= $usage;

    if ($reached_limit) {
        $msg_key = 'perfex_saas_quota_exhausted';
        return perfex_saas_limit_reached_middleware(
            $limit_name,
            _l($msg_key, $limit_name),
            _l($msg_key . '_cron', [$tenant->slug, $limit_name])
        );
    }

    return $data;
}

/**
 * Get the usage of tenant quotas.
 *
 * @param mixed $tenant - The tenant object.
 * @param string[] $limits - Optional. The list of limits to retrieve usage for. Will use global list when empty.
 * @param mixed $package - Optional. The tenant package for detecting dsn
 * @return array - The usage list for each limit.
 */
function perfex_saas_get_tenant_quota_usage($tenant, $limits = [], $package = null)
{
    $tenant_slug = $tenant->slug;

    /** The tenant db prefix */
    $dbprefix = perfex_saas_tenant_db_prefix($tenant_slug);

    $usage_list = [];
    $queries = [];

    $limits = empty($limits) ? array_values(PERFEX_SAAS_LIMIT_FILTERS_TABLES_MAP) : $limits;
    $dsn = perfex_saas_get_company_dsn($tenant, $package);

    if (!isset($tenant->package_invoice->metadata)) {
        $package_invoice = perfex_saas_get_client_package_invoice($tenant->clientid);
        $tenant->package_invoice = $package_invoice;
    }

    $apply_period_limit = false;
    $limit_preiod = $tenant->package_invoice->metadata->resource_limit_period ?? 'lifetime';
    $apply_period_limit = $limit_preiod !== 'lifetime';

    // Get last recurring date until invoice recurring hour. The original value of last recurring_date only date without time.
    // @todo Find means of increasing accuracy by adding time of recurring_date to SQL check
    $invoice_hour = intval(get_option('invoice_auto_operations_hour')) ?? 9;
    $time = $invoice_hour . ':00:00'; //$time = '12:00:00';
    $last_recurring_date = date('Y-m-d', strtotime(empty($tenant->package_invoice->last_recurring_date) ? $tenant->package_invoice->date : $tenant->package_invoice->last_recurring_date));
    $last_recurring_datetime = $last_recurring_date . ' ' . $time;
    if (!empty($tenant->package_invoice->datecreated)) {
        $datecreated = date('Y-m-d', strtotime($tenant->package_invoice->datecreated));
        $last_recurring_datetime = $datecreated == $last_recurring_date ? date('Y-m-d h:i:s', strtotime($tenant->package_invoice->datecreated)) : $last_recurring_datetime;
    }

    foreach ($limits as $limit) {
        $table = $dbprefix . $limit;
        $where_clause = '';
        if ($apply_period_limit && $limit !== 'items') {
            $date_field = PERFEX_SAAS_LIMIT_FILTERS_TABLES_CREATED_DATE_MAP[$limit] ?? 'datecreated';
            $where_clause = " WHERE $date_field > '$last_recurring_datetime'";
        }

        // Get count for the active tenant from table and match against package.
        $queries[$limit] = "SELECT COUNT(*) as total FROM $table $where_clause";
    }

    $queries = hooks()->apply_filters('perfex_saas_tenant_quota_usage_queries', $queries, ['tenant' => $tenant, 'limits' => $limits, 'package' => $package]);

    // Run queries and set limits
    $usages = perfex_saas_raw_query($queries, $dsn, true);

    foreach ($usages as $limit => $usage) {
        $usage_list[$limit] = (int)$usage[0]->total;
    }

    $usage_list = hooks()->apply_filters('perfex_saas_tenant_quota_usage', $usage_list, ['tenant' => $tenant, 'limits' => $limits, 'package' => $package]);

    return $usage_list;
}


/**
 * Exit the program with a limitation reached message.
 * The function attempt to handle base on varying scenario of request.
 *
 * @param object $tenant
 * @param string $limit_name
 * @return void
 */
function perfex_saas_limit_reached_middleware($limit_name, $msg, $cron_msg)
{
    if (!defined('CRON')) {
        set_alert('danger', $msg);

        // Handle ajax requests
        if (get_instance()->input->is_ajax_request()) {
            header('HTTP/1.0 400 Bad error');
            echo $limit_name === 'tasks' || ($limit_name == 'storage' && stripos(uri_string(), 'tasks/task')) ? json_encode($msg) : $msg;
            exit;
        }

        perfex_saas_redirect_back();
    } else {
        log_message('info', $cron_msg ?? $msg);
    }
    exit;
}

/**
 * Get the tenant quota for a resources
 *
 * @param object $tenant
 * @param string $resources
 * @return int
 */
function perfex_saas_tenant_resources_quota($tenant, $resources)
{

    // Get tenant details and get package limit
    $quota = (int)($tenant->package_invoice->metadata->limitations->{$resources} ?? -1);

    // Ulimited pass
    if ($quota === -1) return $quota;

    // Add extra purchased limits
    $extra_quota = (int)($tenant->package_invoice->custom_limits->{$resources} ?? 0);
    $quota = $extra_quota + $quota;

    return $quota;
}

/**
 * Get the max number of instance for a tenant using the package and invoice
 *
 * @param object $package_invoice Package-Invoice for the tenant
 * @return int
 */
function perfex_saas_get_tenant_instance_limit($package_invoice)
{
    $max_instance_limit = (int)($package_invoice->metadata->max_instance_limit ?? 1);
    $extra_instance_limit = (int)($package_invoice->custom_limits->{'tenant_instance'} ?? 0);
    $max_instance_limit = $max_instance_limit + $extra_instance_limit;
    return $max_instance_limit;
}