<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_034 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        perfex_saas_install();

        $CI = &get_instance();
        $companies = $CI->perfex_saas_model->companies();
        foreach ($companies as $company) {
            $dsn_array = false;
            try {
                $dsn_array = perfex_saas_get_company_dsn($company);

                if ($dsn_array !== false) {

                    $tenant_dbprefix = perfex_saas_tenant_db_prefix($company->slug);
                    $table = $tenant_dbprefix . "leads_email_integration";

                    // Check if leads email integration seeded
                    $r = (array)perfex_saas_raw_query("SELECT `id` FROM $table", $dsn_array, true);

                    if (count($r) > 0) continue;

                    $q = "INSERT INTO `$table` (`id`, `active`, `email`, `imap_server`, `password`, `check_every`, `responsible`, `lead_source`, `lead_status`, `encryption`, `folder`, `last_run`, `notify_lead_imported`, `notify_lead_contact_more_times`, `notify_type`, `notify_ids`, `mark_public`, `only_loop_on_unseen_emails`, `delete_after_import`, `create_task_if_customer`) VALUES (1, 0, '', '', '', 10, 0, 0, 0, 'tls', 'INBOX', '', 1, 1, 'assigned', '', 0, 1, 0, 1);";
                    perfex_saas_raw_query($q, $dsn_array);
                }
            } catch (\Throwable $th) {
            }
        }
    }

    public function down()
    {
    }
}