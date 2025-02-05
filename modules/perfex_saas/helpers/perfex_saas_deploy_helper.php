<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Get the DSN (Database Source Name) for a company.
 *
 * This function retrieves the DSN for a company, which is used to establish a database connection.
 * It checks if the company already has a DSN assigned. If not, it checks the company's invoice and
 * package details to determine the appropriate DSN. The function handles different database
 * deployment schemes, such as multitenancy, single database per company, and sharding.
 *
 * @param object|null $company The company object for which to get the DSN. Can be null.
 * @param object|null $invoice The invoice object associated with the company. Can be null.
 *
 * @return array The DSN details as an associative array.
 *
 * @throws \Exception When a valid data center cannot be found.
 */
function perfex_saas_get_company_dsn($company = null, $invoice = null)
{
    $default_dsn = perfex_saas_master_dsn();
    $CI = &get_instance();

    if (!empty($company->dsn)) {
        $dsn = perfex_saas_parse_dsn($company->dsn);

        // If no user, set the default user
        if (!empty($dsn['host']) && $dsn['host'] === $default_dsn['host'] && empty($dsn['user'])) {
            $dsn['user'] = empty($dsn['user']) ? $default_dsn['user'] : $dsn['user'];
            $dsn['password'] = empty($dsn['password']) ? $default_dsn['password'] : $dsn['password'];
        }

        if (perfex_saas_is_valid_dsn($dsn) === true) {
            return $dsn;
        }

        if (isset($dsn['dbname']) && $dsn['dbname'] == APP_DB_NAME && empty($dsn['password'])) {
            return $default_dsn;
        }

        if (isset($dsn['dbname']) && $dsn['dbname'] == perfex_saas_db($company->slug)) {
            $default_dsn['dbname'] = $dsn['dbname'];
            return $default_dsn;
        }
    }

    if (empty($company->dsn) && !empty($invoice)) {
        $invoice = is_null($invoice) ? $CI->perfex_saas_model->get_company_invoice($company->clientid) : $invoice;

        if (isset($invoice->db_scheme) && !empty($invoice->db_scheme)) {
            $db_scheme = $invoice->db_scheme;

            if ($db_scheme == 'multitenancy') {
                return $default_dsn;
            }

            if ($db_scheme == 'single') {
                $default_dsn['dbname'] = perfex_saas_db($company->slug);
                return $default_dsn;
            }

            $packageid = $invoice->{perfex_saas_column('packageid')};

            list($populations, $pools) = !empty($invoice->db_pools) && !is_string($invoice->db_pools) ?  $CI->perfex_saas_model->get_db_pools_population((array)$invoice->db_pools) :
                $CI->perfex_saas_model->get_db_pools_population_by_packgeid($packageid);

            asort($populations);

            $selected_pool = [];
            if ($db_scheme == 'single_pool') {
                if (((int)array_values($populations)[0]) != 0) {
                    $admin = perfex_saas_get_super_admin();
                    $staffid = $admin->staffid;

                    // Notify the super admin about the database exhaustion.
                    if (add_notification([
                        'touserid' => $staffid,
                        'description' => 'perfex_saas_not_package_db_list_exhausted',
                        'link' => PERFEX_SAAS_MODULE_NAME . '/packages/edit/' . $packageid,
                        'additional_data' => serialize([$invoice->name])
                    ])) {
                        pusher_trigger_notification([$staffid]);
                    }
                } else {
                    $selected_pool = $pools[array_keys($populations)[0]];
                }
            }

            if ($db_scheme == 'shard') {
                $selected_pool = $pools[array_keys($populations)[0]];
            }

            $filter = hooks()->apply_filters('perfex_saas_module_maybe_create_tenant_dsn', ['dsn' => $selected_pool, 'tenant' => $company, 'invoice' => $invoice]);
            $selected_pool = $filter['dsn'];

            if (!empty($selected_pool)) {
                $selected_pool['source'] = 'pool';
                return $selected_pool;
            }
        }
    }

    throw new \Exception(_l('perfex_saas_error_finding_valid_datacenter'), 1);
}

/**
 * Deploy companies.
 *
 * This function deploys companies by updating their status to 'deploying' and then attempting to deploy
 * each company using the `perfex_saas_deploy_company()` function. If the deployment is successful, the
 * company's status is updated to 'active'. If any errors occur during deployment, they are logged and
 * the company is removed and deleted from the database.
 *
 * @param string $company_id The ID of the company to deploy. Can be empty.
 * @param string $clientid The ID of the client associated with the company. Can be empty.
 * @param int $limit The maximum number of companies to deploy at a time.
 *
 * @return array An array containing the total number of companies, the errors encountered during deployment,
 *               and the total number of successfully deployed companies.
 */
function perfex_saas_deployer($company_id = '', $clientid = '', $limit = 5)
{
    if (perfex_saas_is_tenant()) return;

    $CI = &get_instance();
    $CI->db->where('status', PERFEX_SAAS_STATUS_PENDING);
    $CI->db->limit($limit);

    if (!empty($clientid)) {
        $CI->db->where('clientid', $clientid);
    }

    $pending_companies = $CI->perfex_saas_model->companies($company_id);

    if (!empty($company_id)) {
        $pending_companies = [$pending_companies];
    }

    $errors = [];
    $total_deployed = 0;
    $cron_urls = [];

    foreach ($pending_companies as $company) {
        try {
            if (empty($company)) continue;

            // check it the company have primary contact with verified email
            $contact = perfex_saas_get_primary_contact($company->clientid);
            if (!$contact || ($contact && is_null($contact->email_verified_at))) {
                $_status_note = _l('perfex_saas_deployment_requires_active_contact');
                if ($company->status_note != $_status_note) {
                    log_message("error", "Skipping deploy for $company->slug till a verified primary contact is linked");
                    $CI->perfex_saas_model->add_or_update('companies', ['id' => $company->id, 'status_note' => $_status_note]);
                }
                continue;
            }

            // Set to deploying
            $CI->perfex_saas_model->add_or_update('companies', ['id' => $company->id, 'status' => PERFEX_SAAS_STATUS_DEPLOYING]);

            // Attempt deploy
            $deploy = perfex_saas_deploy_company($company);

            if ($deploy !== true) {
                if ($deploy instanceof \Throwable) { // Most likely throw due to debug mode
                    $deploy = $deploy->getMessage() . ': ' . $deploy->getTraceAsString();
                }
                throw new \Exception($deploy, 1);
            }

            $CI->perfex_saas_model->add_or_update('companies', ['id' => $company->id, 'status' => PERFEX_SAAS_STATUS_ACTIVE, 'status_note' => '']);

            $total_deployed += 1;

            $url = perfex_saas_tenant_base_url($company, 'cron/index?setup=1', 'path');

            try {
                // Install modules by triggering cron
                perfex_saas_trigger_module_install('*', $company->slug);
                $cron_req = perfex_saas_http_request($url, ['timeout' => 10]);
            } catch (\Throwable $th) {
            }

            // MAke client response incase empty response returned.
            if (!isset($cron_req) || empty($cron_req['response']))
                $cron_urls[] = $url;
        } catch (\Throwable $e) {

            // Rollback deployment and creation of the instance
            $error = "$company->name deploy error: " . $e->getMessage();
            $errors[] = $error;
            log_message('error', $error);

            try {
                // Re-fetch from db incase of update to DSN.
                $company = $CI->perfex_saas_model->companies($company->id);
                perfex_saas_remove_company($company);
            } catch (\Throwable $th) {
            }

            try {
                $CI->perfex_saas_model->delete('companies', $company->id);
            } catch (\Throwable $th) {
            }
        }
    }

    set_alert('danger', implode("\n", $errors));

    return ['total' => count($pending_companies), 'errors' => $errors, 'total_success' => $total_deployed, 'cron_urls' => $cron_urls];
}




/**
 * Deploy a company.
 *
 * This function is responsible for deploying a company by performing various steps
 * such as detecting the appropriate data center, validating the data center, importing
 * SQL seed file, setting up the data center, securing installation settings, registering
 * the first administrative user, and sending notifications.
 *
 * @param object $company   The company object containing information about the company to be deployed.
 * @param boolean $silent_mode If to send notification or not. Default to false
 *  
 * @return bool|string|throwable      Returns true if the deployment is successful, otherwise returns an error message.
 * @throws \Throwable       Throws an exception if there is an error during the deployment process.
 */
function perfex_saas_deploy_company($company, $silent_mode = false)
{
    try {

        //Clear
        perfex_saas_deploy_step($company->slug, '');

        $CI = &get_instance();
        $invoice = $CI->perfex_saas_model->get_company_invoice($company->clientid);


        // Seeding source
        $seed_source = get_option('perfex_saas_tenant_seeding_source');
        $seed_source_reference = '';
        if ($seed_source == PERFEX_SAAS_SEED_SOURCE_TENANT)
            $seed_source_reference = get_option('perfex_saas_seeding_tenant');

        if (!empty($invoice->metadata->seeding_tenant)) {
            $seed_source = PERFEX_SAAS_SEED_SOURCE_TENANT;
            $seed_source_reference = $invoice->metadata->seeding_tenant;
        }

        // Get data center
        perfex_saas_deploy_step($company->slug, _l('perfex_saas_detecting_appropriate_datacenter'));
        $dsn = perfex_saas_get_company_dsn($company, $invoice);

        perfex_saas_deploy_step($company->slug, _l('perfex_saas_validating_datacenter'));
        if (!perfex_saas_is_valid_dsn($dsn, true))
            throw new \Exception(_l('perfex_saas_invalid_datacenter'), 1);

        // Save the DSN if it is obtained from the package pool
        // This is necessary to keep company data intact in case of a package update on database pools
        if (isset($dsn['source']) && $dsn['source'] == 'pool') {

            $data = ['id' => $company->id, 'dsn' => $CI->encryption->encrypt(perfex_saas_dsn_to_string($dsn))];
            $CI->perfex_saas_model->add_or_update('companies', $data);
        }

        // Setup the db structure (i.e tables)
        perfex_saas_deploy_step($company->slug, _l('perfex_saas_preparing_datacenter_for_installation'));
        perfex_saas_setup_dsn($dsn, $company->slug);

        perfex_saas_deploy_step($company->slug, _l('perfex_saas_deploying_seed_to_datacenter'));
        perfex_saas_setup_seed($company, $dsn, $seed_source, $seed_source_reference);
        hooks()->do_action(
            'perfex_saas_after_tenant_seeding',
            [
                'seed_source' => $seed_source,
                'company' => $company,
                'dsn' => $dsn,
                'seed_source' => $seed_source,
                'seed_source_reference' => $seed_source_reference
            ]
        );

        perfex_saas_deploy_step($company->slug, _l('perfex_saas_securing_installation_settings'));
        perfex_saas_clear_sensitive_data($company, $dsn, $invoice, $seed_source);


        perfex_saas_deploy_step($company->slug, _l('perfex_saas_registering_first_administrative_user'));
        perfex_saas_setup_tenant_admin($company, $dsn);

        perfex_saas_deploy_step($company->slug, _l('perfex_saas_preparing_push_notifications'));

        $notifiedUsers = [];

        if (!$silent_mode) {

            // Notify supper admin
            $admin = perfex_saas_get_super_admin();
            $staffid = $admin->staffid;
            if (add_notification([
                'touserid' => $staffid,
                'description' => 'perfex_saas_not_customer_create_instance',
                'link' => 'clients/client/' . $company->clientid,
                'additional_data' => serialize([$company->name])
            ])) {
                array_push($notifiedUsers, $staffid);
            }

            perfex_saas_deploy_step($company->slug, _l('perfex_saas_sending_push_notification_to_the_company_and_superadmin'));
            pusher_trigger_notification($notifiedUsers);

            perfex_saas_deploy_step($company->slug, _l('perfex_saas_sending_email_notification_to_the_company_contact_and_superadmin'));

            // Send email to customer about deployment
            $contact = perfex_saas_get_primary_contact($company->clientid);

            if (!empty($contact->email)) {
                send_mail_template('customer_deployed_instance', PERFEX_SAAS_MODULE_NAME, $contact->email, $company->clientid, $contact->id, $company);
            }

            // Send email to admin about the deployment
            if (!empty($admin->email)) {
                send_mail_template('customer_deployed_instance_for_admin', PERFEX_SAAS_MODULE_NAME, $admin->email, $company->clientid, $contact->id, $company);
            }

            perfex_saas_deploy_step($company->slug, _l('perfex_saas_complete'));


            hooks()->do_action('perfex_saas_module_tenant_deployed', ['tenant' => $company, 'invoice' => $invoice]);
        }

        //Clear
        perfex_saas_deploy_step($company->slug, '');

        return true;
    } catch (\Throwable $th) {

        log_message("error", $th->getMessage());

        if (!$silent_mode) {
            try {
                // Noify supper admin
                $admin = perfex_saas_get_super_admin();
                $staffid = $admin->staffid;

                $notifiedUsers = [];
                if (add_notification([
                    'touserid' => $staffid,
                    'description' => 'perfex_saas_not_customer_create_instance_failed',
                    'link' => 'clients/client/' . $company->clientid,
                    'additional_data' => serialize([$company->name, $th->getMessage()])
                ])) {
                    array_push($notifiedUsers, $staffid);
                }

                pusher_trigger_notification($notifiedUsers);
            } catch (\Throwable $th) {
                log_message("error", $th->getMessage());
            }

            hooks()->do_action('perfex_saas_module_tenant_deploy_failed', $company);
        }

        if (ENVIRONMENT == 'development') {
            if (ob_get_contents()) ob_clean();
            return $th;
        }

        return $th->getMessage();
    }
    return false;
}

/**
 * Log active step in company deployment.
 * 
 * This will be helpful to improve user UX by showing progress of the deployment.
 *
 * @param string $slug The tenant slug.
 * @param string $step The description text for the step.
 * @return void
 */
function perfex_saas_deploy_step(string $slug, string $step = '')
{
    $tmp_dir = get_temp_dir();

    if (!is_writable($tmp_dir)) {
        log_message("error", "Cant not write deploy step to file");
        return;
    }

    $file = rtrim($tmp_dir, '/') . '/' . $slug . '.lock';

    if (empty($step)) {
        return @unlink($file);
    }

    if (!file_exists($file)) touch($file);

    $history = file_get_contents($file);
    $history = empty($history) ? [] : json_decode(trim($history), true);
    if (!is_array($history)) $history = [];

    $history[] = $step;
    write_file($file, json_encode($history));
}

/**
 * Set session for the active step in company deployment.
 * 
 * This will be helpful to improve user UX by showing progress of the deployment.
 *
 * @param string $slug The tenant slug
 * @return array
 */
function perfex_saas_get_deploy_step(string $slug): array
{
    if (empty($slug)) return [];

    $tmp_dir = get_temp_dir();
    $file = rtrim($tmp_dir, '/') . '/' . $slug . '.lock';

    if (!file_exists($file)) return [" "];

    $history = file_get_contents($file);
    $history = empty($history) ? [] : json_decode(trim($history), true);
    if (!is_array($history)) $history = [];

    return $history;
}

/**
 * Remove crm instance for a company.
 * 
 * This function attempt to remove the instance data and delete the instance from the database.
 * It send email notification to both the admin and user about the removal of the instance.
 *
 * @param object $company The company instance to delete
 * @param boolean $silent_mode If to send notification or not. Default to false
 * 
 * @return boolean|string True when successful or string stating the error encountered.
 */
function perfex_saas_remove_company($company, $silent_mode = false)
{

    try {

        if (perfex_saas_is_tenant()) return;

        if (!isset($company->slug) || empty($company->slug))
            throw new \Exception(_l('perfex_saas_company_slug_is_missing_!'), 1);

        $slug = $company->slug;

        //Clear
        perfex_saas_deploy_step($company->slug, '');

        $CI = &get_instance();
        $invoice = $CI->perfex_saas_model->get_company_invoice($company->clientid);

        // Get the data center
        perfex_saas_deploy_step($company->slug, _l('perfex_saas_detecting_appropriate_datacenter'));
        $dsn = perfex_saas_get_company_dsn($company, $invoice);

        perfex_saas_deploy_step($company->slug, _l('perfex_saas_validating_datacenter'));
        if (!perfex_saas_is_valid_dsn($dsn, true))
            throw new \Exception(_l('perfex_saas_invalid_datacenter'), 1);

        perfex_saas_deploy_step($company->slug, _l('perfex_saas_removing_data_from_datacenter'));

        $tenant_dbprefix = perfex_saas_tenant_db_prefix($slug);
        $db = perfex_saas_load_ci_db_from_dsn($dsn, ['dbprefix' => $tenant_dbprefix]);

        // Get all table list
        $tables = $db->list_tables(); //neutral global query, wont fail
        $deleted_tables = 0;

        // Loop through all and remove all data with tenant column of the company slug
        foreach ($tables as $table) {
            if (str_starts_with($table, $tenant_dbprefix)) {
                perfex_saas_raw_query("DROP TABLE $table", $dsn, false, false, null, true);
                $deleted_tables++;
            }
        }

        // Check tenant owns db and table is empty
        if (
            $dsn['dbname'] === perfex_saas_db($slug) &&
            count($tables) === $deleted_tables
        ) {
            try {
                // Drop database if using single and not other tenant on the db
                perfex_saas_raw_query("DROP DATABASE `" . $dsn['dbname'] . '`', $dsn, false, false, null, true);
            } catch (\Throwable $th) {
                log_message("error", $th->getMessage());
            }
        }

        // Storage clean: Clear tenant general file uploads
        $tenant_upload_folder = perfex_saas_tenant_upload_base_path($company);
        perfex_saas_remove_dir($tenant_upload_folder);

        // Storage clean: Clear tenants media upload
        $tenant_media_folder =  perfex_saas_tenant_media_base_path($company);
        perfex_saas_remove_dir($tenant_media_folder);

        perfex_saas_deploy_step($company->slug, _l('perfex_saas_preparing_push_notifications'));

        $notifiedUsers = [];

        if (!$silent_mode) {
            // Notify supper admin
            $admin = perfex_saas_get_super_admin();
            $staffid = $admin->staffid;
            if (add_notification([
                'touserid' => $staffid,
                'description' => 'perfex_saas_not_customer_instance_removed',
                'link' => 'clients/client/' . $company->clientid,
                'additional_data' => serialize([$company->name])
            ])) {
                array_push($notifiedUsers, $staffid);
            }

            perfex_saas_deploy_step($company->slug, _l('perfex_saas_sending_push_notification_to_the_company_and_superadmin'));
            pusher_trigger_notification($notifiedUsers);

            perfex_saas_deploy_step($company->slug, _l('perfex_saas_sending_email_notification_to_the_company_contact_and_superadmin'));
            // Send email to customer about removal
            $contact = perfex_saas_get_primary_contact($company->clientid);
            if (!empty($contact->email)) {
                send_mail_template('customer_removed_instance', PERFEX_SAAS_MODULE_NAME, $contact->email, $company->clientid, $contact->id, $company);
            }

            // Send email to admin about the removal
            if (!empty($contact->id) && !empty($admin->email)) {
                send_mail_template('customer_removed_instance_for_admin', PERFEX_SAAS_MODULE_NAME, $admin->email, $company->clientid, $contact->id, $company);
            }

            perfex_saas_deploy_step($company->slug, _l('perfex_saas_complete'));

            hooks()->do_action('perfex_saas_module_tenant_removed', $company);
        }

        //Clear
        perfex_saas_deploy_step($company->slug, '');

        return true;
    } catch (\Throwable $th) {

        log_message("error", $th->getMessage());

        if (!$silent_mode) {
            try {
                // Notify supper admin
                $admin = perfex_saas_get_super_admin();
                $staffid = $admin->staffid;

                $notifiedUsers = [];
                if (add_notification([
                    'touserid' => $staffid,
                    'description' => 'perfex_saas_not_customer_create_instance_failed',
                    'link' => 'clients/client/' . $company->clientid,
                    'additional_data' => serialize([$company->name, $th->getMessage()])
                ])) {
                    array_push($notifiedUsers, $staffid);
                }

                pusher_trigger_notification($notifiedUsers);
            } catch (\Throwable $th) {
                log_message("error", $th->getMessage());
            }


            hooks()->do_action('perfex_saas_module_tenant_removal_failed', $company);
        }

        if (ENVIRONMENT == 'development') throw $th;

        return $th->getMessage();
    }
    return false;
}

/**
 * Get the default tables defined in SQL migration files within a specified folder.
 *
 * @param string $folder_path (Optional) The folder path to search for SQL files. If not provided, it defaults to the folder for the Perfex Saas module's default seeds.
 * @return array An array of unique table names, with leading and trailing spaces removed.
 */
function perfex_saas_master_default_tables($folder_path = '')
{
    // If the folder path is not provided, use the default path for the Perfex Saas module's default seeds.
    $folder_path = empty($folder_path) ? module_dir_path(PERFEX_SAAS_MODULE_NAME, 'migrations/default_seeds') : $folder_path;

    $table_names = [];

    if (is_dir($folder_path)) {
        $extensions = ['sql'];

        // Iterate through files in the specified folder and its subdirectories.
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder_path),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $file_extension = pathinfo($file, PATHINFO_EXTENSION);

            if ($file->isFile() && in_array($file_extension, $extensions)) {
                // Read the contents of the SQL file.
                $file_contents = file_get_contents($file);

                // Search for CREATE TABLE statements in SQL files and extract table names.
                preg_match_all('/CREATE TABLE\s+`?(\w+)`?/i', $file_contents, $matches);
                $table_names = array_merge($table_names, $matches[1]);
            }
        }
    }

    // Remove leading and trailing spaces and ensure table names are unique.
    $table_names = array_map('trim', $table_names);
    $table_names = array_unique($table_names);

    return $table_names;
}

/**
 * Sets up tables on a dsn from a given data source name (DSN).
 * The data source table should start with master db prefix i.e 'tbl'
 * 
 * @param array $dsn The data source name (DSN) configuration
 * @param string $slug The db prefix for the tenant
 * @param array $source_dsn The master source dsn to be used as template
 * @param bool $return_queries_only Optional . Will return the queries only and will not run
 * @return void
 */
function perfex_saas_setup_dsn($dsn, $slug, $source_dsn = [], $return_queries_only = false)
{
    $dbprefix = perfex_saas_master_db_prefix();

    // List all tables from master 
    $master_tables = perfex_saas_master_default_tables();

    // Get tables with seed and join with master tables
    $master_tables = array_unique(array_merge($master_tables, perfex_saas_seed_tables()));

    $show_create_queries = [];

    $hooks_payload = ['dsn' => $dsn, 'source_dsn' => $source_dsn, 'slug' => $slug, 'tables' => $master_tables, 'queries' => $show_create_queries];
    $filter = hooks()->apply_filters('perfex_saas_dsn_setup_source_dsn_queries', $hooks_payload);
    $show_create_queries = (array)($filter['queries'] ?? []);

    if (empty($show_create_queries)) {
        foreach ($master_tables as $table) {

            // skip saas tables and non perfex tables
            if (str_starts_with($table, perfex_saas_table('')) || !str_starts_with($table, $dbprefix)) continue;

            // Get table structure and confirm it exist
            $sql = '';
            try {
                // @todo Provide alternative to use the default seeding file to speed things up here.
                $query = "SHOW CREATE TABLE `$table` -- create tables";
                $row = perfex_saas_raw_query_row($query, $source_dsn, true);
                $new_table =  perfex_saas_tenant_db_prefix($slug, $table);

                $sql = $row->{"Create Table"};
                if (stripos($sql, 'CREATE TABLE IF NOT EXISTS') === false)
                    $sql = str_ireplace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $sql);
            } catch (\Throwable $th) {
                log_message('error', $th->getMessage());
                continue;
            }

            if (empty($sql)) continue;

            $table_without_prefix =  str_ireplace($dbprefix, '', $table);
            $new_table_without_master_prefix = str_ireplace($dbprefix, '', $new_table);

            // Rename table and foreign key
            $sql = str_ireplace(
                [
                    $table,
                    'KEY `' . $table_without_prefix,
                    'CONSTRAINT `' . $table_without_prefix
                ],
                [
                    $new_table,
                    'KEY `' . $new_table_without_master_prefix,
                    'CONSTRAINT `' . $new_table_without_master_prefix
                ],
                $sql
            );

            $show_create_queries[] =  $sql;
            $show_create_queries[] = "ALTER TABLE `$new_table` AUTO_INCREMENT = 1";
        }

        $hooks_payload['queries'] = $show_create_queries;
        $filter = hooks()->apply_filters('perfex_saas_after_dsn_setup_source_dsn_queries', $hooks_payload);
        $show_create_queries = (array)($filter['queries'] ?? []);
        if (empty($show_create_queries)) return;
    }

    $sql_commands_to_run =  implode('*;*', $show_create_queries);

    // Loop through all table and replace table name in query with new table.
    foreach ($master_tables as $table) {

        $new_table =  perfex_saas_tenant_db_prefix($slug, $table);

        $table_without_prefix =  str_ireplace($dbprefix, '', $table);
        $new_table_without_master_prefix = str_ireplace($dbprefix, '', $new_table);

        // Rename table and foreign key
        $sql_commands_to_run = str_ireplace(
            [
                '`' . $table,
                'KEY `' . $table_without_prefix,
                'CONSTRAINT `' . $table_without_prefix
            ],
            [
                '`' . $new_table,
                'KEY `' . $new_table_without_master_prefix,
                'CONSTRAINT `' . $new_table_without_master_prefix
            ],
            $sql_commands_to_run
        );
    }

    $sql_commands_to_run = explode('*;*', $sql_commands_to_run);

    if ($return_queries_only) return $sql_commands_to_run;

    return perfex_saas_raw_query($sql_commands_to_run, $dsn, false, false, null, true, true);
}

/**
 * Get the default seeding tables for master
 * @param string $dbprefix Optional or it use master prefix
 * @return array
 */
function perfex_saas_default_seed_tables($dbprefix = '')
{
    $dbprefix = !empty($dbprefix) ? $dbprefix : perfex_saas_master_db_prefix();

    $table_selectors = [
        'emailtemplates' => ['type', 'slug', 'language'],
        'leads_email_integration' => ['id'],
        'leads_sources' => ['name'],
        'leads_status' => ['name'],
        'options' => ['name'],
        'roles' => ['name'],
        'tickets_priorities' => ['name'],
        'tickets_status' => ['name'],
        'countries' => ['short_name', 'calling_code'],
        'currencies' => ['name', 'symbol'],
        'migrations' => ['version'],
        'customfields' => ['id', 'slug']
    ];

    $tables = [];
    foreach ($table_selectors as $table => $selectors) {
        $tables[$dbprefix . $table] = $selectors;
    }

    return $tables;
}

/**
 * Return list of tables select for seeding tenant new db
 *
 * @return array
 */
function perfex_saas_seed_tables($dbprefix = '')
{
    $master_dbprefix = perfex_saas_master_db_prefix();
    $dbprefix = !empty($dbprefix) ? $dbprefix : $master_dbprefix;

    $seed_tables = get_option('perfex_saas_tenants_seed_tables');
    $seed_tables = empty($seed_tables) ? [] : (array)json_decode($seed_tables);

    if (empty($seed_tables)) {
        $default_table_selectors = perfex_saas_default_seed_tables($dbprefix);
        return array_keys($default_table_selectors);
    }

    if ($dbprefix !== $master_dbprefix) {
        foreach ($seed_tables as $key => $s_table) {
            if (str_starts_with($s_table, $master_dbprefix)) {
                $seed_tables[$key] = substr_replace($s_table, $dbprefix, 0, strlen($master_dbprefix));
            }
        }
    }

    $seed_tables[] = $dbprefix . 'options';
    $seed_tables[] = $dbprefix . 'migrations';

    // Require this to ensure leads_email_integration form will save as first row in this table is required by Perfex
    // Perfex treat this more like a setting row
    $lei_table = $dbprefix . 'leads_email_integration';
    if (!in_array($lei_table, $seed_tables))
        $seed_tables[] = $lei_table;

    return $seed_tables;
}

/**
 * Seed the tenant using the given dsn connection
 *
 * @param object $company
 * @param mixed $dsn The tenant dsn
 * @param string $seed_source The type of source of the data for seeding
 * @param string $seed_source_reference The reference to the seed source i.e tenant slug
 * @return void
 */
function perfex_saas_setup_seed($company, $dsn, $seed_source, $seed_source_reference = '')
{
    if ($seed_source == PERFEX_SAAS_SEED_SOURCE_FILE) {

        // Only seed from file
        return perfex_saas_seed_tenant_from_sql_files($company, $dsn);
    }

    if ($seed_source == PERFEX_SAAS_SEED_SOURCE_TENANT && !empty($seed_source_reference)) {

        // Seed from a tenant
        $dbprefix = perfex_saas_tenant_db_prefix($seed_source_reference);
        $source_tenant = get_instance()->perfex_saas_model->get_company_by_slug($seed_source_reference);
        $source_dsn = perfex_saas_get_company_dsn($source_tenant);
        return perfex_saas_seed_tenant_from_dsn($company, $dsn, $source_dsn, $dbprefix);
    }

    // Seed from master
    $dbprefix = perfex_saas_master_db_prefix();
    return perfex_saas_seed_tenant_from_dsn($company, $dsn, [], $dbprefix);
}

/**
 * Seed a tenant from a source dsn.
 * 
 * @param object $company
 * @param mixed $dsn
 * @param mixed $source_dsn
 * @param string $dbprefix
 * @param bool $return_queries_only Optional false, set to true to get seeding sql queries as array
 * @return mixed void|array
 */
function perfex_saas_seed_tenant_from_dsn($company, $dsn, $source_dsn, $dbprefix, $return_queries_only = false)
{
    $CI = &get_instance();

    $default_senstive_fields = perfex_saas_sensitive_options_fields();

    $slug = $company->slug;
    $tenant_dbprefix = perfex_saas_tenant_db_prefix($slug);
    $source_is_super_admin = $dbprefix === perfex_saas_master_db_prefix() || empty($source_dsn);

    $hooks_payload = ['company' => $company, 'dsn' => $dsn, 'source_dsn' => $source_dsn, 'dbprefix' => $dbprefix, 'return_queries_only' => $return_queries_only];
    $filter = hooks()->apply_filters('perfex_saas_before_tenant_seeding_from_dsn', $hooks_payload);
    if (empty($filter) || !isset($filter['company'])) // When empty, discontinue process
        return;

    $return_queries_only = $filter['return_queries_only'] ?? $return_queries_only;
    $source_dsn = $filter['source_dsn'] ?? $source_dsn;
    $dsn = $filter['dsn'] ?? $dsn;

    // Define the table selectors that specify the columns to be selected from each table
    $default_table_selectors = perfex_saas_default_seed_tables($dbprefix);
    $seed_tables = perfex_saas_seed_tables($dbprefix);

    // Set db prefix for insert string generation
    $db = clone ($CI->db);
    $db->dbprefix = $tenant_dbprefix;

    // Loop through each table selector
    // Initialize an array to store all multi-insert queries
    $queries = [];

    // Loop through each table selector
    foreach ($seed_tables as $table) {
        $tenant_table = str_replace_first($dbprefix, $tenant_dbprefix, $table);
        $selectors = $default_table_selectors[$table] ?? [];

        $q = "SELECT * FROM $table";

        // Retrieve rows from the master database
        $rows = perfex_saas_raw_query($q, $source_dsn, true, false);

        if (empty($rows)) {
            continue;
        }

        // Collect column names
        $columns = array_keys((array)$rows[0]);
        $columns_str = "`" . implode("`,`", $columns) . "`";

        // Initialize a set to track seen unique keys
        $seen_keys = [];

        // Initialize an array to hold the values for the insert statement
        $values = [];

        foreach ($rows as $row) {
            $row = (array)$row;

            if ($table === $dbprefix . 'leads_email_integration') {
                $row['id'] = 1;
            }

            if ($table === $dbprefix . 'leads_email_integration' && $source_is_super_admin) {
                // Clear the whole row to prevent revealing the super admin leads email integration details with tenants.
                $row = [
                    'id' => 1,
                    'active' => 0,
                    'email' => '',
                    'imap_server' => '',
                    'password' => '',
                    'check_every' => 10,
                    'responsible' => 0,
                    'lead_source' => 0,
                    'lead_status' => 0,
                    'encryption' => 'tls',
                    'folder' => 'INBOX',
                    'last_run' => '',
                    'notify_lead_imported' => 1,
                    'notify_lead_contact_more_times' => 1,
                    'notify_type' => 'assigned',
                    'notify_ids' => '',
                    'mark_public' => 0,
                    'only_loop_on_unseen_emails' => 1,
                    'delete_after_import' => 0,
                    'create_task_if_customer' => 1
                ];
            }

            // Skip perfex saas options
            if ($table === $dbprefix . 'options') {

                if (str_starts_with($row['name'], 'perfex_saas'))
                    continue;

                if (in_array($row['name'], $default_senstive_fields)) continue;
            }

            if (empty($selectors)) {
                $primary_col = array_keys($row)[0];
                $selectors = [$primary_col];
            }

            // Check if the row already exists in the company's database
            //@todo Remove this block as the neccessity is low compare to the imposed performance bottleneck especially with remote dbs
            /*    $primary_col = array_keys($row)[0];
                $where = empty($selectors) ? ["`$primary_col` = " . $db->escape($row[$primary_col])] : [];
                foreach ($selectors as $selector) {
                    $value = $row[$selector];
                    $where[] = "`$selector`=" . $db->escape($value);
                }
                $result = perfex_saas_raw_query("SELECT * FROM $tenant_table WHERE " . implode(' AND ', $where) . ' LIMIT 1', $dsn, true);
                if (!empty($result)) {
                    continue;
                }
            */

            // Build a unique key based on selectors
            $unique_key_parts = [];
            foreach ($selectors as $selector) {
                $unique_key_parts[] = $row[$selector];
            }
            $unique_key = implode('_', $unique_key_parts);

            // Skip if this unique key has already been seen
            if (isset($seen_keys[$unique_key])) {
                continue;
            }

            // Mark the unique key as seen
            $seen_keys[$unique_key] = true;

            // Collect values for the row
            $row_values = array_map(function ($value) use ($db) {
                return $db->escape($value);
            }, $row);
            $values[] = '(' . implode(',', $row_values) . ')';
        }

        // Create and store the multi-insert query
        if (!empty($values)) {
            $values_str = implode(',', $values);
            $queries[] = "INSERT INTO `$tenant_table` ($columns_str) VALUES $values_str;";
        }
    }

    $hooks_payload['queries'] = $queries;
    $hooks_payload['tenant_dbprefix'] = $tenant_dbprefix;
    $filter = hooks()->apply_filters('perfex_saas_tenant_seeding_queries', $hooks_payload);
    $queries = (array)($filter['queries'] ?? []);

    if ($return_queries_only)
        return $queries;

    // Execute the queries to insert the seed data into the company's database
    perfex_saas_raw_query($queries, $dsn, false, false, null, true, false);
}

/**
 * Seed a tenant from sql folder directory
 *
 * @param object $company
 * @param mixed $dsn
 * @param string $folder_path Optional Absolute path to folder containing sql files or an sql file
 * @return int Total number of queries run
 */
function perfex_saas_seed_tenant_from_sql_files($company, $dsn, $folder_path = '')
{
    $CI = &get_instance();
    $CI->load->library(PERFEX_SAAS_MODULE_NAME . '/SqlScriptParser');

    $slug = $company->slug;
    $tenant_dbprefix = perfex_saas_tenant_db_prefix($slug);

    $folder_path = empty($folder_path) ? module_dir_path(PERFEX_SAAS_MODULE_NAME, 'migrations/default_seeds') : $folder_path;
    // Iterate through files in the specified folder and its subdirectories.
    $files = is_file($folder_path) ? [$folder_path] : new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder_path),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $queries = [];
    $totalQueries = 0;

    foreach ($files as $file) {
        $file_extension = pathinfo($file, PATHINFO_EXTENSION);
        if ($file_extension == 'sql') {

            $sqlStatements = $CI->sqlscriptparser->parse($file, function ($sqlString) use ($tenant_dbprefix) {
                $dbprefix = perfex_saas_master_db_prefix();

                return str_ireplace([
                    'INSERT INTO  ', // Trim space around the key
                    'INSERT INTO `' . $dbprefix,
                    "INSERT INTO '$dbprefix",
                    'INSERT INTO "' . $dbprefix,
                    "INSERT INTO $dbprefix",
                ], [
                    'INSERT INTO ',
                    'INSERT INTO `' . $tenant_dbprefix,
                    "INSERT INTO '$tenant_dbprefix",
                    'INSERT INTO "' . $tenant_dbprefix,
                    "INSERT INTO $tenant_dbprefix",
                ], $sqlString);
            });

            foreach ($sqlStatements as $statement) {
                $distilled = $CI->sqlscriptparser->removeComments($statement);
                if (!empty($distilled) && str_starts_with($distilled, 'INSERT INTO')) {
                    $queries[] = $distilled;
                    $totalQueries++;
                }
            }
        }
    }

    // Execute the queries to insert the seed data into the company's database
    perfex_saas_raw_query($queries, $dsn, false, false, null, true, false);

    return $totalQueries;
}

/**
 * Retrieves the super admin for a specific company or the master tenant.
 *
 * @param string $slug (Optional) The slug of the company. Defaults to the master tenant slug.
 * @param array $dsn (Optional) The data source name (DSN) configuration. Defaults to an empty array.
 * @return object|false The super admin object if found, false otherwise.
 */
function perfex_saas_get_super_admin($slug = '', $dsn = [])
{
    $dbprefix = empty($slug) ? perfex_saas_master_db_prefix() : perfex_saas_tenant_db_prefix($slug);
    $table = $dbprefix . "staff";

    // Retrieve the super admin from the database
    return perfex_saas_raw_query_row("SELECT * FROM `$table` WHERE `admin`='1' AND `active`='1' ORDER BY `staffid` ASC LIMIT 1", $dsn, true);
}

/**
 * Function to add admin login credential to an instance setup.
 * 
 * Will not run if admin already exist on the DB.
 *
 * @param object $tenant The company instance object containing information about the company.
 * @param array $dsn
 * @return void
 */
function perfex_saas_setup_tenant_admin($tenant, $dsn)
{
    $CI = &get_instance();

    $tenant_dbprefix = perfex_saas_tenant_db_prefix($tenant->slug);
    $table = $tenant_dbprefix . "staff";

    $result = perfex_saas_get_super_admin($tenant->slug, $dsn);
    if (isset($result->email)) return true; //already exist

    // Get contact from customer
    $contact = $CI->clients_model->get_contact(get_primary_contact_user_id($tenant->clientid));

    // Fallback to the active staff
    if (!$contact && is_staff_logged_in())
        $contact = get_staff(get_staff_user_id());

    if (!$contact) throw new \Exception(_l('perfex_saas_error_getting_contact_to_be_used_as_administrator_on_the_new_instance'), 1);

    // Insert admin login to the instance
    $data = [];
    $data['firstname']   = $contact->firstname;
    $data['lastname']    = $contact->lastname;
    $data['email']       = $contact->email;
    $data['phonenumber'] = $contact->phonenumber;
    $data['password'] = $contact->password;
    $data['admin']  = 1;
    $data['active'] = 1;
    $data['datecreated'] = date('Y-m-d H:i:s');

    // Set db prefix for insert string generation
    $db = clone ($CI->db);
    $db->dbprefix = $tenant_dbprefix;
    $admin_insert_query = $db->insert_string($table, $data);

    return perfex_saas_raw_query($admin_insert_query, $dsn);
}

/**
 * Clears sensitive data and sahred data from the company instance.
 *
 * @param object $company The company object containing information about the company.
 * @param array $dsn_array The data source name (DSN) configuration.
 * @param object|null $invoice (Optional) The invoice object. Defaults to null.
 * @param string $seed_source The source of the data
 * @return void
 */
function perfex_saas_clear_sensitive_data($company, $dsn_array, $invoice = null, $seed_source = null)
{
    $slug = $company->slug;
    $tenant_dbprefix = perfex_saas_tenant_db_prefix($slug);
    $options_table = $tenant_dbprefix . "options";
    $emailtemplate_table = $tenant_dbprefix . "emailtemplates";

    if (perfex_saas_is_tenant_installation_secured($slug, $dsn_array)) return;

    $where = "WHERE ";
    $queries = [];

    // Update company name
    $queries[] = "UPDATE `" . $options_table . "` SET `value`='$company->name' $where `name` = 'companyname'";

    if ($seed_source == PERFEX_SAAS_SEED_SOURCE_MASTER) {
        // Clean mask and shared fields
        if ($invoice) {
            if (!empty($invoice->metadata->shared_settings)) {
                $shared_settings = $invoice->metadata->shared_settings;
                $_secret_fields = (array)(empty($shared_settings->masked) ? [] : $shared_settings->masked);
                $_shared_fields = (array)(empty($shared_settings->shared) ? [] : $shared_settings->shared);

                $shared_fields = "'" . implode("','", $_shared_fields) . "'";
                $mask_fields = "'" . implode("','", $_secret_fields) . "'";

                if (!empty($_shared_fields)) {
                    // Empty shared options so they can always be taken from the master tenant
                    $queries[] = "UPDATE `" . $options_table . "` SET `value`='' $where (`name` IN ($shared_fields))";
                }

                if (!empty($_secret_fields)) {
                    // Empty secret fields
                    $queries[] = "UPDATE `" . $options_table . "` SET `value`='' $where (`name` IN ($mask_fields))";
                }
            }
        }

        // Reset general sensitive options in the new template
        $sensitive_fields = perfex_saas_sensitive_options_fields();
        $sensitive_fields = "'" . implode("','", $sensitive_fields) . "'";
        $queries[] = "UPDATE `" . $options_table . "` SET `value`='' $where (`name` IN ($sensitive_fields) )";
    }

    // Reset next invoice, estimate, creditnote number
    $next_fields = ['next_invoice_number', 'next_estimate_number', 'next_credit_note_number'];
    $next_fields =  "'" . implode("','", $next_fields) . "'";
    $queries[] = "UPDATE `" . $options_table . "` SET `value`='1' $where `name` IN ($next_fields)";

    // Remove saas releated settings
    $queries[] = "DELETE FROM `" . $options_table . "` WHERE `name` LIKE 'perfex_saas%'";

    // Remove SAAS email templates
    $queries[] = "DELETE FROM $emailtemplate_table $where `slug` LIKE 'company-instance%'";

    // Run all queries in a single transaction
    perfex_saas_raw_query($queries, $dsn_array, false, true);

    perfex_saas_secure_tenant_installation($slug, $dsn_array);
}

/**
 * Sensitive options list of field name
 *
 * @return array
 */
function perfex_saas_sensitive_options_fields()
{
    $sensitive_fields = get_option('perfex_saas_sensitive_options');
    if (empty($sensitive_fields)) {
        $sensitive_options = get_instance()->perfex_saas_model->sensitive_shared_options();
        $sensitive_fields = array_column($sensitive_options, 'key');
    } else {
        $sensitive_fields = (array) json_decode($sensitive_fields);
    }
    return $sensitive_fields;
}

/**
 * Insert installation secured flag for a tenant, marking complete deployment
 *
 * @param string $slug
 * @param array|string $dsn_array
 * @return void
 */
function perfex_saas_secure_tenant_installation($slug, $dsn_array)
{
    $tenant_dbprefix = perfex_saas_tenant_db_prefix($slug);
    $options_table = $tenant_dbprefix . "options";

    $flag_query = "INSERT INTO `" . $options_table . "` (`id`, `name`, `value`, `autoload`) VALUES (NULL, 'perfex_saas_installation_secured', 'true', '0')";
    perfex_saas_raw_query($flag_query, $dsn_array, false, true);
}

/**
 * Check if installation secured flag is set for a tenant
 *
 * @param string $slug
 * @param array|string $dsn_array
 * @param bool $drop_option_table_if_not_fit
 * @return bool
 */
function perfex_saas_is_tenant_installation_secured($slug, $dsn_array, $drop_option_table_if_not_fit = false)
{
    $tenant_dbprefix = perfex_saas_tenant_db_prefix($slug);
    $options_table = $tenant_dbprefix . "options";
    $migration_table = $tenant_dbprefix . "migrations";

    $r = [];
    $version = null;
    try {

        // @todo compare with health instance or master instead of setting fixed number
        $mininum_perfex_option_count = 200; // Perfex has minimum of 300 in standard installation as of v3.1.6

        // Check if installation has already been secured
        $r = perfex_saas_raw_query("SELECT `value` FROM $options_table WHERE `name`='perfex_saas_installation_secured'", $dsn_array, true);
        $all_options = perfex_saas_raw_query("SELECT `id` FROM $options_table", $dsn_array, true);
        $version = perfex_saas_raw_query_row("SELECT `version` FROM $migration_table LIMIT 1;", $dsn_array, true)->version;

        $total_options = count($all_options);

        // Drop the options table
        if ($drop_option_table_if_not_fit && !empty($version) && $total_options < $mininum_perfex_option_count) {
            perfex_saas_raw_query("TRUNCATE TABLE $options_table", $dsn_array);
        }

        return $total_options > $mininum_perfex_option_count && count($r) > 0 && !empty($version);
    } catch (\Throwable $th) {
    }

    return false;
}

/**
 * Simulate modules installation for tenants. 
 * This is neccessary so that module installation data is seed properly into each tenants.
 *
 * @return void
 */
function perfex_saas_setup_modules_for_tenant($verbose = true)
{
    $CI = &get_instance();
    if (!perfex_saas_is_tenant()) throw new \Exception("Error Processing Request: Invalid context for perfex_saas_setup_modules_for_tenant", 1);

    $tenant = perfex_saas_tenant();
    $modules = perfex_saas_tenant_modules($tenant, false, false, false, false, true);
    if ($verbose)
        echo "Setting up modules for $tenant->slug<br/>";

    // Handle core module requiring DB changes
    $core_required_default_modules = ['surveys', 'goals'];
    foreach ($core_required_default_modules as $module) {

        if (in_array($module, $modules)) continue;

        if ($verbose)
            echo "Installing core module ($module) - required for perfect running of the CRM<br/>";

        $CI->app_modules->activate($module);
        $CI->app_modules->deactivate($module);
    }

    foreach ($modules as $key => $name) {
        try {

            if ($name === PERFEX_SAAS_MODULE_NAME) {
                echo "Skipping SaaS module<br/>";
                continue;
            }

            if ($name === 'debug_mode') {
                echo "Skipping Debug mode module for tenant installation.";
                $CI->app_modules->deactivate($name);
                continue;
            }

            if ($verbose) {
                echo "Start Running for: $name <br/>";
                echo "activating<br/>";
            }
            $CI->app_modules->activate($name);

            if ($CI->app_modules->new_version_available($name)) {
                if ($verbose)
                    echo "updating version<br/>";
                $CI->app_modules->update_to_new_version($name);
            }

            if ($CI->app_modules->is_database_upgrade_required($name)) {
                if ($verbose)
                    echo "upgrading db <br/>";
                if ($CI->app_modules->upgrade_database($name) !== true) {
                    if ($verbose)
                        echo "error updating the database - triggering reinstall for $name <br/>";

                    // Remove from db
                    $CI->db->where('module_name', $name);
                    $CI->db->delete(db_prefix() . 'modules');

                    $CI->app_modules->activate($name);
                }
            }

            if ($verbose)
                echo "End Running for: $name <br/><br/>";
        } catch (\Throwable $th) {
            if ($verbose) {
                echo "<br/>Error installing module $name:" . $th->getMessage() . '<br/>';
                echo "<br/>" . $th->getTraceAsString() . '<br/>';
            }
            log_message('error', "Error installing module $name:" . $th->getMessage() . "\n" . $th->getTraceAsString());
        }
    }

    // Deactive disabled modules and active modules not assigned to the tenant
    $all_active_modules = array_keys($CI->app_modules->get_activated());
    $active_non_assigned_modules = array_diff($all_active_modules, $modules);

    $disabled_modules = array_diff(perfex_saas_tenant_modules($tenant, false, true, true), $modules);
    $disabled_modules = array_merge($active_non_assigned_modules, $disabled_modules);

    foreach ($disabled_modules as $key => $name) {
        try {
            if ($name === PERFEX_SAAS_MODULE_NAME) {
                echo "Skipping SaaS module<br/>";
                continue;
            }

            if ($verbose) {
                echo "Start Running for: $name <br/>";
                echo "deactivating<br/>";
            }

            // Some modules are activated but not loaded, ensure the are loaded
            // for vital function in migration or deactivation.
            $module_info = $CI->app_modules->get($name);
            require_once($module_info['init_file']);

            if ($CI->app_modules->new_version_available($name)) {
                if ($verbose)
                    echo "updating version<br/>";
                $CI->app_modules->update_to_new_version($name);
                if ($verbose)
                    echo "done updating version<br/>";
            }

            if ($CI->app_modules->is_database_upgrade_required($name)) {
                if ($verbose)
                    echo "updating database<br/>";
                $CI->app_modules->upgrade_database($name);
                if ($verbose)
                    echo "done updating database<br/>";
            }

            if ($CI->app_modules->is_active($name)) {
                $CI->app_modules->deactivate($name);
            } else {
                if ($verbose)
                    echo "Skipping $name module as it is not active<br/>";
            }

            if ($verbose)
                echo "End Running for: $name <br/><br/>";
        } catch (\Throwable $th) {
            if ($verbose)
                echo "Error deactivating module $name:" . $th->getMessage() . '<br/>';
            log_message('error', "Error deactivating module $name:" . $th->getMessage() . "\n" . $th->getTraceAsString());
        }
    }
}

/**
 * Function to manage demo instances resetting.
 *
 * @return void
 */
function perfex_saas_reset_demo_instances()
{
    $CI = &get_instance();

    $key = 'perfex_saas_demo_instance';
    $reset_key = $key . '_reset_hour';
    $history_key = $key . '_last_reset_time';

    $hours_interval = get_option($reset_key);
    $last_reset_stamp = (int)get_option($history_key);
    if (!empty($last_reset_stamp)) {
        // Check if hours interval has elapsed otherwise return
        $diff = time() - $last_reset_stamp;
        $hours_elapsed = $diff / 3600; // Convert the difference to hours

        if ($hours_elapsed < $hours_interval) {
            return; // Interval has not elapsed, exit the function
        }
    }

    $instances = get_option($key);
    $instances = empty($instances) ? [] : json_decode($instances);

    foreach ($instances as $slug) {
        $company = $CI->perfex_saas_model->get_company_by_slug($slug);
        if (empty($company->slug)) continue;

        // Check if restore file exist to use or generate one
        try {
            perfex_saas_remove_company($company, true);
            perfex_saas_deploy_company($company, true);
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }
    }

    // Update the last reset timestamp
    update_option($history_key, time());
}

/**
 * Sync the tenant with the seedling source.
 *
 * @param object $company
 * @return void
 * 
 */
function perfex_saas_sync_tenant_with_seed($company)
{

    // @todo Sync with source seed or active seedign source on the package
}