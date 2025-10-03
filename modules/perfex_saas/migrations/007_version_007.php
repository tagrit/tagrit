<?php

defined('BASEPATH') or exit('No direct script access allowed');
set_time_limit(0);

class Migration_Version_007 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function down()
    {
    }

    public function up()
    {
        try {

            $CI = &get_instance();

            if (empty($CI->input->get('ps_backup_confirmed', true))) {
                echo "<h3>This version (v0.0.7) migration involves heavy changes to DB structure <br/>
                and we are migrating from shared table schema into separate table (read more: <a href='https://docs.perfextosaas.com/readme/database-schemes/' target='_blank'>Database Schema</a>). <br/> 
                <br/>Also, all tenant data tables on all scheme are renamed to include tenant identity.<br/> 
                for each tenants, this will ensure more security and data isolation.<br/><br/>
                Thus, it requires you to make a full database and file backups for master and all tenants.
                <br/><br/>Click continue only if you have make neccessary backups</h3>";

                echo "<br/>You can refresh the page incase of timeouts.<br/><br/>";

                echo "<div><a href='" . base_url('admin/modules') . "'>Cancel</a></div><br/>";
                echo "<div><a onclick='return confirm(\"Are you sure you have make neccessary backups?\");' style='color:red;' href='?ps_backup_confirmed=yes'><b>Continue</b></a></div>";
                exit();
            }

            // Move tenants to separate table
            $this->migrationv007();
        } catch (\Throwable $th) {

            echo 'Migration error, reach out to us on <b>support@perfextosaas.com</b> for assistance with below error details: <br/>';
            exit($th->getMessage());
        }
    }

    /**
     * Fucntion to migrate tenants from uptil v0.0.6 to v0.0.7
     *
     * @return void
     */
    private function migrationv007()
    {
        $CI = &get_instance();

        /** Compile all data centers */
        $databases = [
            perfex_saas_master_dsn()
        ];

        $packages = $CI->perfex_saas_model->packages();
        foreach ($packages as $invoice) {

            if (empty($invoice->db_pools)) continue;

            if (!is_string($invoice->db_pools)) {
                $invoice->db_pools = perfex_saas_dsn_to_string($invoice->db_pools);
            }
            $dsn = perfex_saas_parse_dsn($invoice->db_pools);
            $databases[] = $dsn;
        }

        $companies = $CI->perfex_saas_model->companies();
        foreach ($companies as $key => $company) {

            $dsn = perfex_saas_get_company_dsn($company);

            //skip companies running on same db as master
            if ($dsn['host'] == APP_DB_HOSTNAME_DEFAULT && $dsn['dbname'] == APP_DB_NAME_DEFAULT) {
                continue;
            }

            $databases[] = $dsn;
        }


        // Validate all data center
        foreach ($databases as $key => $dsn) {
            try {
                if (($valid = perfex_saas_is_valid_dsn($dsn)) !== true)
                    unset($databases[$key]);
            } catch (\Throwable $th) {
                throw new \Exception("Invalid DSN center. Can not proceed with migraiton. Ensure all db pools are valid or remove invalid pools manually: " . perfex_saas_dsn_to_string($dsn) . ". " . $th->getMessage(), 1);
            }
        }

        $master_slug = perfex_saas_master_tenant_slug();
        $dbprefix = perfex_saas_master_db_prefix();

        foreach ($databases as $dsn) {

            // Get the list of tables starting with "tbl_" in the tenant database
            $sql_tables = "SHOW TABLES LIKE '" . $dbprefix . "%'";
            $tables = perfex_saas_raw_query($sql_tables, $dsn, true);

            if (empty($tables)) {
                echo "Skipping " . $dsn['dbname'] . " : no table founds<br/>";
                continue;
            }

            $col = array_key_first((array)$tables[0]);

            // Get the list of tenants from tbl_options (Assuming you have a table named 'tbl_options')
            $sql = "SELECT DISTINCT " . PERFEX_SAAS_TENANT_COLUMN . " FROM " . $dbprefix . 'options';

            // Get all tenants on the table
            $tenants = perfex_saas_raw_query($sql, $dsn, true);

            if (count($tenants) <= 0) {
                echo "Skipping " . $dsn['dbname'] . " : no tenants founds<br/>";
                continue;
            }

            if (count($tenants) == 1 && $tenants[0]->{PERFEX_SAAS_TENANT_COLUMN} === $master_slug) {
                echo "Skipping " . $dsn['dbname'] . " : Only master tenant is on this database<br/>";
                continue;
            }

            $has_master = false;

            // Loop through each tenant
            foreach ($tenants as $k => $tenant) {

                $tenant_id = $tenant->{PERFEX_SAAS_TENANT_COLUMN};
                if ($tenant_id === $master_slug) {
                    $has_master = true;
                    continue;
                }

                if (empty($tenant_id)) {
                    continue;
                }

                echo "Processing migration for $tenant_id<br/>";

                perfex_saas_setup_dsn($dsn, $tenant_id, $dsn);

                foreach ($tables as $key => $row) {

                    // Table name with prefix.
                    $table = $row->{$col};
                    $new_table = perfex_saas_tenant_db_prefix($tenant_id, $table);

                    if (str_starts_with($table, perfex_saas_table('')) || !str_starts_with($table, $dbprefix)) continue;

                    try {
                        perfex_saas_raw_query('TRUNCATE TABLE ' . $new_table, $dsn, false, false, null, true, false);
                    } catch (\PDOException $th) {
                        //throw $th;
                    }

                    // Migrate tenant data from the original table to the new prefixed table
                    $sql_migrate = "SET SESSION sql_mode = ''; INSERT INTO `$new_table` SELECT * FROM `$table` WHERE `" . PERFEX_SAAS_TENANT_COLUMN . "` = '$tenant_id';";
                    if (perfex_saas_raw_query($sql_migrate, $dsn, false, false, null, true)) {
                        perfex_saas_raw_query("DELETE FROM `$table` WHERE `" . PERFEX_SAAS_TENANT_COLUMN . "` = '" . $tenant_id . "'", $dsn, false, false, null, true);
                        //echo "Data migration for tenant $tenant_id successful for $table.\n";
                    } else {
                        throw new \Exception("Data migration for tenant $tenant_id failed: $table to " . $new_table . "\n");
                    }
                }

                $_query = "UPDATE `" . perfex_saas_tenant_db_prefix($tenant_id, 'leads_email_integration') . "` SET `id` = '1' WHERE `id` != '1' LIMIT 1;";
                perfex_saas_raw_query($_query, $dsn, false, false, null, true, false);
            }

            // Drop the old tables
            if ($has_master === false)
                foreach ($tables as $k => $row) {
                    $table = $row->{$col};
                    if (str_starts_with($table, perfex_saas_table('')) || !str_starts_with($table, $dbprefix)) continue;

                    perfex_saas_raw_query("DROP TABLE $table", $dsn, false, false, null, true, false);
                }
        }

        echo "<br/>MIGRATION COMPLETED SUCCESSFULLY !";
    }
}
