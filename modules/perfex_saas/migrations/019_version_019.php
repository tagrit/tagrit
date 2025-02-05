<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_019 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $CI = &get_instance();
        $CI->load->model(PERFEX_SAAS_MODULE_NAME . '/' . PERFEX_SAAS_MODULE_NAME . '_model');

        // Get all instances
        $companies = $CI->perfex_saas_model->companies();

        $CI->load->config('migration');
        $latest_version = $CI->config->item('migration_version');

        if ($latest_version > 306) {
            try {
                // Forward compatibility for those already on v3.1.0 i.e 310 of perfex and have created instance before receiving this update.
                // We attempt to downgrade the instance back to 360 for it to pick properly upgraded through cron on inline upgrade in this release.
                foreach ($companies as $company) {
                    $dsn = perfex_saas_get_company_dsn($company);
                    $db_prefix = perfex_saas_tenant_db_prefix($company->slug);
                    $query = 'SELECT version FROM ' . $db_prefix . 'migrations LIMIT 1;';
                    $version = (int)(perfex_saas_raw_query_row($query, $dsn, true)->version ?? '');
                    if (empty($version)) continue;

                    if ($version > 306) {
                        $query = 'UPDATE ' . $db_prefix . "migrations SET version='306' LIMIT 1";
                        perfex_saas_raw_query($query, $dsn);
                    }
                }
            } catch (\Throwable $th) {
                log_message("error", $th->getMessage());
            }
        }

        perfex_saas_install();
    }

    public function down()
    {
    }
}