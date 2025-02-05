<?php

use Carbon\Carbon;

defined('BASEPATH') or exit('No direct script access allowed');

class Perfex_saas_cron_model extends App_model
{
    /**
     * Timeout limit in seconds
     *
     * @var integer
     */
    private $available_execution_time = 25;

    /**
     * Monitor of used seconds
     *
     * @var integer
     */
    private $start_time;

    private $cron_cache;

    public $setup_mode = false;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $max_time = (int)ini_get("max_execution_time");
        if ($max_time <= 0)
            $max_time = 60 * 60; //1 hour;

        $this->available_execution_time = $max_time - 5; //minus 5 seconds for cleanup
        $this->start_time = time();

        $this->setup_mode = !empty($_GET['setup']);
        $setup_mode = $this->setup_mode;

        // Ensure cron is run for tenant when ?setup=1 flag is set
        hooks()->add_filter('cron_functions_execute_seconds', function ($seconds) use ($setup_mode) {
            if ($setup_mode)
                $seconds = 0;
            return $seconds;
        });
    }

    protected function log($msg)
    {
        if (isset($_GET['verbose'])) {
            echo "$msg <br/><br/>";
        }
    }

    /**
     * Init cron activites for both tenant and master routine
     *
     * @return void
     */
    public function init()
    {
        $this->log("SaaS cron initiating...");

        $this->cron_cache = $this->get_settings();

        // Run saas related cron task for tenants
        if (perfex_saas_is_tenant()) {

            $this->log("Using tenant context...");

            try {

                $this->log("Running db upgrade if neccessary...");

                // Check and run db upgrade if neccessary
                $this->run_database_upgrade();

                $tenant = perfex_saas_tenant();

                $this->log("Checking and setting tirgger if neccessary...");

                $should_run_setup_triggers = $this->setup_mode || perfex_saas_should_run_cron_triggers_for_tenant($tenant);
                if ($should_run_setup_triggers) {
                    // Update modules
                    perfex_saas_setup_modules_for_tenant();
                }
            } catch (\Throwable $th) {
                log_message('error', $th->getMessage());
            }
            return;
        }

        // Check for updates
        $this->log("Check latest SaaS version...");
        $this->check_saas_latest_version();

        // Run master cron instance. From here, deployer is called and tenants cron triggered is called.
        try {

            $this->log("Running deployment of any pending instance...");

            // Run deployment of new instances
            perfex_saas_deployer();

            // Trigger cron for active instances
            $start_from_id = (int) ($this->cron_cache->last_proccessed_instance_id ?? 0);

            $this->log("Launching cron job running for tenants...");

            $this->perfex_saas_model->db->where('id >', $start_from_id)->where('status', 'active');
            $companies = $this->perfex_saas_model->companies();

            // Run cron for each instance and return the last processed instance id
            $last_proccessed_instance_id = $this->run_tenants_cron($companies);
            $this->cron_cache->last_proccessed_instance_id = $last_proccessed_instance_id;

            // Update cron cache
            $this->cron_cache->cron_last_success_runtime = time();

            $this->log("Stopping tenants cron at $last_proccessed_instance_id ...");

            if ($last_proccessed_instance_id === 0) {
                // Reset module activation pointer and other triggers since all company must have been processed
                $processes = [PERFEX_SAAS_CRON_PROCESS_MODULE, PERFEX_SAAS_CRON_PROCESS_SINGLE_TENANT_MODULE];
                $_cron_cache = $this->get_settings();
                foreach ($processes as $process) {

                    if (isset($this->cron_cache->{$process}))
                        unset($this->cron_cache->{$process});

                    if (isset($_cron_cache->{$process}))
                        unset($_cron_cache->{$process});
                }
                $this->save_settings($_cron_cache, false);

                $this->log("Running health check for third party modules");

                $this->modules_health_check();

                $this->log("Removing due instances pending delete");

                // Check for removals
                $this->remove_instances_pending_delete();

                hooks()->do_action('perfex_saas_after_cron');
            }

            $this->save_settings($this->cron_cache);

            $this->log("End of the SaaS cron");
        } catch (\Throwable $th) {
            $this->log("Error occured:" . $th->getMessage());
            log_message('error', $th->getMessage());
        }
    }

    /**
     * Get cron cache
     *
     * @return object
     */
    public function get_settings($field = '')
    {
        $settings = perfex_saas_get_options('perfex_saas_cron_cache');
        $cron_cache = (object) (empty($settings) ? [] : json_decode($settings));
        if ($field) return $cron_cache->{$field} ?? '';
        return $cron_cache;
    }

    /**
     * Update cron cache
     *
     * @param array|object $settings
     * @return object
     */
    public function save_settings($settings, $merge = true)
    {
        $settings = $merge ? array_merge((array)$this->get_settings(), (array)$settings) : (array)$settings;
        perfex_saas_update_option('perfex_saas_cron_cache', json_encode($settings));
        return (object)$settings;
    }

    /**
     * Run cron for all tenants.
     * 
     * It uses Timeouter to detect timeout and return last processed id
     *
     * @param integer $start_from_id    The company id to start from.
     * @return integer The last processed company id
     */
    public function run_tenants_cron($companies)
    {
        $this->load->library(PERFEX_SAAS_MODULE_NAME . '/Timeouter');

        // Get all instance and run cron
        foreach ($companies as $company) {

            $this->log("Running cron for " . $company->slug);

            $time_elapsed = (time() - $this->start_time);

            try {

                // Start timeout
                Timeouter::limit($this->available_execution_time - $time_elapsed, 'Time out.');

                declare(ticks=1) {

                    try {

                        $package_invoice = perfex_saas_get_client_package_invoice($company->clientid);
                        $company->package_invoice = $package_invoice;
                        $metadata = $company->metadata;

                        // Calculate total storage and update if neccessary
                        perfex_saas_update_tenant_storage_size($company);

                        // Auto removal of inactive tenant
                        $auto_remove_inactive_instance = $package_invoice->metadata->auto_remove_inactive_instance ?? 'no';
                        if ($auto_remove_inactive_instance === 'yes') {

                            $this->log("Checking for autoremoval needs...");

                            $min_grace_period = PERFEX_SAAS_MINIMUM_AUTO_INSTANCE_REMOVE_GRACE_PERIOD; // default to 7
                            $days_to_send_removal_note = [$min_grace_period, 3, 1]; // Send notice 7, 3 and 1 day to deletion
                            $removal_notices = $metadata->removal_notices ?? [];
                            $removal_notices = is_array($removal_notices) ? $removal_notices : json_decode(empty($removal_notices) ? '[]' : $removal_notices, true);
                            $inactive_days_grace_period = intval($package_invoice->metadata->auto_remove_inactive_instance_days ?? '0');

                            if ($inactive_days_grace_period >= $min_grace_period) {

                                // Check for inactivity and auto-removal
                                $dsn = perfex_saas_get_company_dsn($company);
                                $staff_table = perfex_saas_tenant_db_prefix($company->slug, 'staff');
                                $query = "SELECT last_activity, staffid FROM $staff_table ORDER BY last_activity ASC LIMIT 1";

                                // Fetch the last activity time of staff
                                $query = perfex_saas_raw_query_row($query, $dsn, true);
                                $last_active_time = $query->last_activity ?? '';

                                // If last_active_time is empty, use company's updated_at
                                $last_active_time = empty($last_active_time) ? $company->updated_at : $last_active_time;


                                // Get the current date at midnight
                                $now = new DateTime();
                                $now->setTime(0, 0); // Set time to 00:00 to match "today"
                                // Create a DateTime object from the provided last active timestamp
                                $last_active = DateTime::createFromFormat('Y-m-d H:i:s', $last_active_time);
                                $days_of_inactivity = $now->diff($last_active)->days;

                                $has_sent_notifications = count($removal_notices) === count($days_to_send_removal_note);
                                if ($days_of_inactivity > $inactive_days_grace_period && !$has_sent_notifications) {

                                    // Subtract inactive days grace period and add minimum grace period
                                    $now->modify("-$inactive_days_grace_period days"); // Subtract inactive days grace period
                                    $now->modify("+$min_grace_period days"); // Add minimum grace period
                                    $new_last_active = $now->format('Y-m-d H:i:s');

                                    perfex_saas_raw_query("UPDATE $staff_table SET `last_activity`='$new_last_active' WHERE staffid='{$query->staffid}'", $dsn);
                                    continue;
                                }

                                // Check if there is still time within the grace period
                                $time_remaining_in_grace_period = $inactive_days_grace_period - $days_of_inactivity;

                                // Send notifications
                                if ($time_remaining_in_grace_period <= $min_grace_period) {
                                    // Send notifications in advance day 7, 3 and 1
                                    if (
                                        !in_array($time_remaining_in_grace_period, $removal_notices) && in_array($time_remaining_in_grace_period, $days_to_send_removal_note)
                                    ) {

                                        load_client_language($company->clientid);

                                        // Send email to customer about deployment
                                        $contact = perfex_saas_get_primary_contact($company->clientid);

                                        $extra_fields = [
                                            '{site_login_url}' => base_url('login'),
                                            '{period_left}' => _l($time_remaining_in_grace_period == 1 ? 'perfex_saas_days_format_singular' : 'perfex_saas_days_format', $time_remaining_in_grace_period),
                                            '{inactive_period}' => _l('perfex_saas_days_format', [$days_of_inactivity]),
                                        ];

                                        if (!empty($contact->email)) {
                                            if (send_mail_template('customer_instance_auto_removal_notice', PERFEX_SAAS_MODULE_NAME, $contact->email, $company->clientid, $contact->id, $company, $extra_fields)) {
                                                $removal_notices[] = $time_remaining_in_grace_period;
                                                $metadata->removal_notices = $removal_notices;
                                                $this->perfex_saas_model->add_or_update('companies', ['id' => $company->id, 'metadata' => json_encode($metadata)]);
                                            }
                                        }
                                    }
                                }

                                // Remove the instance
                                if ($days_of_inactivity > $inactive_days_grace_period && $has_sent_notifications) {

                                    $this->log("Auto-removing this tenant " . $company->slug);

                                    try {
                                        perfex_saas_remove_company($company);
                                    } catch (\Throwable $th) {
                                        log_message("error", "Autoremoval error for $company->slug: {$th->getMessage()}");
                                    }

                                    $this->perfex_saas_model->delete('companies', $company->id);
                                }
                            }
                        }


                        // Sync tenant seeding
                        perfex_saas_sync_tenant_with_seed($company);

                        $url_path = 'cron/index';
                        if ($this->setup_mode)
                            $url_path .= '?setup=1';

                        $url = perfex_saas_tenant_base_url($company, $url_path, 'path');

                        $this->log("Visiting tenant cron via $url");

                        // Simulate cron command: wget -q -O- http://saasdomain.com/demoinstance/ps/cron/index
                        $cron_result = perfex_saas_http_request($url, ['timeout' => 20]);

                        $this->log("<br/><br/><br/>RESPONSE: <code>" . htmlentities(json_encode($cron_result)) . "</code><br/><br/>");

                        if (!$cron_result || (!empty($cron_result['error']) && !empty($cron_result['result']))) {
                            $_cron_error_msg = "Cron: Error running cron on $url :" . $cron_result['error'];
                            $this->log($_cron_error_msg);
                            log_message("error", $_cron_error_msg);
                        }
                    } catch (\Throwable $th) {

                        $_error_msg = "Cron job failure for $company->slug :" . $th->getMessage();
                        $this->log($_error_msg);
                        log_message("error", $_error_msg);
                    }
                }

                Timeouter::end();
            } catch (\Throwable $th) {

                $this->log("Error Running cron for " . $company->slug);

                Timeouter::end();
                return $company->id;
            }
        }

        return 0;
    }

    /**
     * Run perfex database upgrade.
     * This should be used for the tenant or master. Its advisable to run for only tenants 
     * and master admin should run db upgrade from the screen UI
     *
     * @return void
     */
    public function run_database_upgrade()
    {
        if ($this->app->is_db_upgrade_required($this->app->get_current_db_version())) {

            hooks()->do_action('pre_upgrade_database');

            if (perfex_saas_is_tenant()) {
                // Reset the database update info from tenant view
                hooks()->add_action('database_updated', function () {
                    update_option('update_info_message', '');
                }, PHP_INT_MAX);
            }

            // This call will redirect and code should not be placed after following line.
            $this->app->upgrade_database();
        }
    }

    /**
     * Method to make modules that uses file based validation available to all tenants.
     * Needed as some module does not use purchsae code in database. Thus not sharable through package shared settings.
     * Performs other health checks for modules.
     *
     * @return void
     */
    public function modules_health_check()
    {
        // Load health checker
        $this->load->library(PERFEX_SAAS_MODULE_NAME . '/modules_health_checker');

        // Ensure file based license modules are fine
        $modules = $this->app_modules->get();
        $this->modules_health_checker->checkFileLicenses($modules);

        // Ensure modules follow standard and inherit from App_Controller
        $dir = module_dir_path('api/controllers');
        $this->modules_health_checker->replaceClassExtensions($dir, 'CI_Controller', 'App_Controller');
    }
    /**
     * Check for the module version.
     */
    public function check_saas_latest_version()
    {
        $purchase_code = get_option('perfex_saas_purchase_code');

        if (empty($purchase_code)) return;

        $url = perfex_saas_get_system_update_url($purchase_code, 2);

        $request = (object)perfex_saas_http_request($url, []);
        $response = (object)json_decode($request->response ?? '');

        $remote_modules = (object)($response->modules ?? []);
        $remote_version = $remote_modules->{PERFEX_SAAS_MODULE_NAME}->version ?? '';

        update_option('perfex_saas_latest_version', $remote_version);
    }

    /**
     * Check for companies marked as delete and delete them permanently
     *
     * @return void
     */
    public function remove_instances_pending_delete()
    {
        $days_to_wait = (float)get_option('perfex_saas_instance_delete_pending_days');
        $hours_to_wait = $days_to_wait * 24;

        $this->perfex_saas_model->db->where('status', PERFEX_SAAS_STATUS_PENDING_DELETE);
        $companies = $this->perfex_saas_model->companies();

        foreach ($companies as $company) {
            try {

                if ($company->status !== PERFEX_SAAS_STATUS_PENDING_DELETE) continue;

                $deleted_timestamp = $company->metadata->deleted_at;

                $now = new DateTime();
                $deleted = DateTime::createFromFormat('Y-m-d H:i:s', $deleted_timestamp);
                $interval = $now->diff($deleted);
                $elasped_hours = ($interval->days * 24) + $interval->h;

                if ($elasped_hours > $hours_to_wait) {

                    try {
                        perfex_saas_remove_company($company);
                    } catch (\Throwable $th) {
                        log_message('error', $th->getMessage());
                    }
                    $this->perfex_saas_model->delete('companies', $company->id);
                }
            } catch (\Throwable $th) {
                log_message('error', $th->getMessage());
            }
        }
    }
}