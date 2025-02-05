<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Common general tests
 * 
 * @todo Separate each test to Helpers test for more cases.
 */
class GeneralTest extends PerfexSaasTest
{

    /**
     * Test for import processes including tenant database connection and deploying/removal of instance.
     * 
     * Required to be run on host 'localhost' and only on development on testing.
     *
     * @return void
     */
    public function testInstanceDeploySchemes()
    {
        $this->CI->load->model('invoices_model');

        // Test parameters
        $company = (object) ['name' => 'tenant_ulutfa', 'slug' => 'tenant_ulutfa', 'dsn' => APP_DB_DRIVER . ':host=localhost;dbname=' . APP_DB_NAME . ';user=;password=;', 'status' => 'active'];
        $t_package = $this->CI->perfex_saas_model->packages()[0];
        $t_company = $this->CI->perfex_saas_model->companies()[0];

        $default_dsn = [
            "driver" => APP_DB_DRIVER,
            "host" => APP_DB_HOSTNAME,
            "dbname" => APP_DB_NAME,
            "user" => APP_DB_USERNAME,
            "password" => ""
        ];


        // Default DB connection should be fine.
        $dsn_string = perfex_saas_dsn_to_string(['dbname' => 'test']);
        $test = perfex_saas_is_valid_dsn($default_dsn);
        $expected_result = true;
        $test_name = 'Should connect with the provided default DB data';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));


        // Test dsn parsing and assignment
        $dsn_string = perfex_saas_dsn_to_string(['dbname' => 'test']);
        $test = perfex_saas_parse_dsn($dsn_string);
        $expected_result = [
            "driver" => APP_DB_DRIVER,
            "host" => APP_DB_HOSTNAME,
            "dbname" => 'test',
            "user" => '',
            "password" => ""
        ];
        $test_name = 'Should provide full dsn structure when passing incomplete DSN i.e user, host and password';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));

        // Perfex table column 
        $test = perfex_saas_column('packageid');
        $expected_result = PERFEX_SAAS_MODULE_NAME . '_packageid';
        $test_name = 'should prefix perfex table column with proper prefix';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));

        // Saas table prefix
        $test = perfex_saas_table('company');
        $expected_result = db_prefix() . PERFEX_SAAS_MODULE_NAME . '_company';
        $test_name = 'should prefix saas table with proper prefix';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));


        // Saas database prefix
        $test = perfex_saas_db('company');
        $expected_result = PERFEX_SAAS_MODULE_NAME . '_db_company';
        $test_name = 'should generate right instance database name';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));


        // Parse dsn to string
        $test = perfex_saas_dsn_to_string(['dbname' => 'demo']);
        $expected_result = APP_DB_DRIVER . ':host=localhost;dbname=demo;user=;password=;';
        $test_name = 'should parse incomplete dsn to normal complete dsn string with default host and driver';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));


        // Ensure unique slug is generated
        $test = perfex_saas_generate_unique_slug($t_company->slug, 'companies');
        $expected_result =  (str_starts_with($test, $t_company->slug)) && $test != $t_company->slug ? $test : "something other than $test";
        $test_name = 'should generate unique slug';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));


        $test = perfex_saas_generate_unique_slug($t_company->slug, 'companies', $t_company->id);
        $expected_result =  $t_company->slug;
        $test_name = 'should return same slug when the slug is only used by the provided entity';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));


        // Ensure db connection is valid
        $test = perfex_saas_is_valid_dsn($default_dsn, false);
        $expected_result =  true;
        $test_name = 'should validate dsn connection';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));

        // Ensure throw message on invalid dsn connection
        $test = perfex_saas_is_valid_dsn(array_merge($default_dsn, ['password' => time()]), false);
        $expected_result =  stripos($test, 'Access denied for user') !== false ? $test : "*Access denied for user*";
        $test_name = 'should validate wrong dsn connection';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));


        // Get company dsn for multitenancy
        $test = perfex_saas_get_company_dsn($company);
        $expected_result = $default_dsn;
        if (is_array($test))
            ksort($test);
        if (is_array($expected_result))
            ksort($expected_result);
        $test_name = 'company created with multitenancy db scheme should have same root db credentials';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));


        // Get company dsn from single db schme
        $company->dsn = APP_DB_DRIVER . ':host=localhost;dbname=perfex_saas_db_tenant_ulutfa;user=;password=;';
        $test = perfex_saas_get_company_dsn($company);
        $expected_result = [
            "driver" => APP_DB_DRIVER,
            "host" => APP_DB_HOSTNAME,
            "dbname" => "perfex_saas_db_tenant_ulutfa",
            "user" => APP_DB_USERNAME,
            "password" => ""
        ];
        if (is_array($test))
            ksort($test);
        if (is_array($expected_result))
            ksort($expected_result);
        $test_name = 'company created with single db scheme should have root db credentials but different db name for the company instance';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));


        // Get company dsn from custom dsn/shard
        $company->dsn = APP_DB_DRIVER . ':host=127.0.0.1;dbname=test;user=root;password=;';
        $test = perfex_saas_get_company_dsn($company);
        $expected_result = [
            "driver" => APP_DB_DRIVER,
            "host" => '127.0.0.1',
            "dbname" => "test",
            "user" => 'root',
            "password" => ""
        ];
        $test_name = 'company created with shard db scheme should have same full credential has in the provided pool';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));


        // Get company dsn from the customer subscribed package - single
        $company->dsn = '';

        $c = perfex_saas_column('packageid');
        $invoice = (object) ['db_scheme' => 'single', "$c" => $t_package->id];
        $test = perfex_saas_get_company_dsn($company, $invoice);

        $expected_result = array_merge($default_dsn, ['dbname' => perfex_saas_db($company->slug)]);
        if (is_array($test))
            ksort($test);
        if (is_array($expected_result))
            ksort($expected_result);
        $test_name = 'company created with empty dsn that belongs to user with package of single db scheme should have same full credential has the default db except for dbname';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));



        // Get company dsn from the customer subscribed package - single from pool
        $company->dsn = '';
        $expected_result = [
            "driver" => APP_DB_DRIVER,
            "host" => '127.0.0.1',
            "dbname" => "josh",
            "user" => 'root',
            "password" => "",
            "source" => "pool"
        ];
        $c = perfex_saas_column('packageid');
        $invoice = (object) ['db_scheme' => 'single_pool', "$c" => $t_package->id, 'db_pools' => [$expected_result]];
        $test = perfex_saas_get_company_dsn($company, $invoice);
        if (is_array($test))
            ksort($test);
        if (is_array($expected_result))
            ksort($expected_result);
        $test_name = 'company created with empty dsn that belongs to user with package of single form pool db scheme should have same full credential from least populated db from the provided pool';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));



        $company->clientid = get_staff_user_id();

        // Test instance deployment
        $company->dsn = APP_DB_DRIVER . ':host=localhost;dbname=perfex_saas_db_tenant_ulutfa;user=;password=;';
        $this->CI->perfex_saas_model->create_database(perfex_saas_db($company->slug));
        $test = perfex_saas_deploy_company($company);
        $expected_result = true;
        $test_name = 'deploy an instance';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));

        // Test instance removal
        $test = perfex_saas_remove_company($company);
        $expected_result = true;
        $test_name = 'delete an instance data';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));

        // TODO: more related invoice test



        // Test email template parsing
        $expected_result = true;
        $test = send_mail_template('customer_deployed_instance', PERFEX_SAAS_MODULE_NAME, get_staff(1)->email, $company->clientid, get_contact_user_id($company->clientid), $company);
        $test_name = 'send email template for deployed instance';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));

        $expected_result = true;
        $test = send_mail_template('customer_deployed_instance_for_admin', PERFEX_SAAS_MODULE_NAME, get_staff(1)->email, $company->clientid, get_contact_user_id($company->clientid), $company);
        $test_name = 'send email template for deployed instance to admin';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));

        $expected_result = true;
        $test = send_mail_template('customer_removed_instance', PERFEX_SAAS_MODULE_NAME, get_staff(1)->email, $company->clientid, get_contact_user_id($company->clientid), $company);
        $test_name = 'send email template for removed instance';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));

        $expected_result = true;
        $test = send_mail_template('customer_removed_instance_for_admin', PERFEX_SAAS_MODULE_NAME, get_staff(1)->email, $company->clientid, get_contact_user_id($company->clientid), $company);
        $test_name = 'send email template for removed instance to admin';
        $this->CI->unit->run($test, $expected_result, $test_name, $this->customTestNote($test, $expected_result));
    }
}
