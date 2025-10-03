<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Important helper functions are tested in this class.
 */

class HelpersTest extends PerfexSaasTest
{

    /**
     * Method to test perfex_saas_get_tenant_info_by_request_uri helper function.
     * perfex_saas_get_tenant_info_by_request_uri is used for detecting the tenant from request uri.
     *
     * @return void
     */
    public function testPerfexSaasGetTenantInfoByRequestUri()
    {
        $testCases = [
            [
                'request_uri' => '/tenant/ps',
                'expected_result' => [
                    'slug' => 'tenant',
                    'path_id' => 'tenant/ps',
                ],
                'description' => 'Valid Request URI with Tenant Slug and ID',
            ],
            [
                'request_uri' => '/tenant/ps/additional/path',
                'expected_result' => [
                    'slug' => 'tenant',
                    'path_id' => 'tenant/ps',
                ],
                'description' => 'Valid Request URI with Tenant Slug and ID (Additional Path Segments)',
            ],
            [
                'request_uri' => '/tenant/ps/additional/path/ps/',
                'expected_result' => [
                    'slug' => 'tenant',
                    'path_id' => 'tenant/ps',
                ],
                'description' => 'Valid Request URI with Tenant Slug and ID (Additional Path Segments and Double ID)',
            ],
            [
                'request_uri' => '/tenant',
                'expected_result' => false,
                'description' => 'Valid Request URI with Only Tenant Slug',
            ],
            [
                'request_uri' => '/tenant/ps_additional/path',
                'expected_result' => false,
                'description' => 'Valid Request URI with Only Tenant Slug (Additional Path Segments)',
            ],
            [
                'request_uri' => '/other',
                'expected_result' => false,
                'description' => 'Request URI without Tenant Slug and ID',
            ],
            [
                'request_uri' => '/other/additional/path/ps',
                'expected_result' => false,
                'description' => 'Request URI without Tenant Slug and ID (Additional Path Segments)',
            ],
            [
                'request_uri' => '/tenant/demo/ps/additional/path/ps',
                'expected_result' => false,
                'description' => 'Request URI subdirectory test',
            ],
        ];

        foreach ($testCases as $testCase) {
            $result = perfex_saas_get_tenant_info_by_request_uri($testCase['request_uri']);

            $this->CI->unit->run(
                $result,
                $testCase['expected_result'],
                $testCase['description'],
                $this->customTestNote($result, $testCase['expected_result'])
            );
        }
    }

    /**
     * Test for detecting tenant custom domain or subdomain
     *
     * @return void
     */
    public function testPerfexSaasGetTenantInfoByHost()
    {
        $defaultHost = perfex_saas_get_saas_default_host();
        $testCases = [
            // Host with Subdomain
            [
                'http_host' => 'www.tenant1.' . perfex_saas_get_saas_default_host(),
                'expected_result' => [
                    'custom_domain' => '',
                    'slug' => 'tenant1',
                ],
                'exception' => false,
                'exception_class' => '',
                'exception_message' => '',
                'description' => 'Default Host with Subdomain',
            ],
            // Host with Subdomain (Additional Path Segments)
            [
                'http_host' => 'www.tenant1.' . perfex_saas_get_saas_default_host(),
                'expected_result' => [
                    'custom_domain' => '',
                    'slug' => 'tenant1',
                ],
                'exception' => false,
                'exception_class' => '',
                'exception_message' => '',
                'description' => 'Host with Subdomain (Additional Path Segments)',
            ],
            // Default Host (Silent pass)
            [
                'http_host' => 'www.' . perfex_saas_get_saas_default_host(),
                'expected_result' => false,
                'exception' => false,
                'exception_class' => '',
                'exception_message' => 'No tenant found for the provided host.',
                'description' => 'Default Host False (Silent pass)',
            ],
            // Invalid Subdomain Exception
            [
                'http_host' => 'www.invalid..' . $defaultHost,
                'expected_result' => null,
                'exception' => true,
                'exception_class' => Exception::class,
                'exception_message' => 'Invalid HTTP host provided: www.invalid..' . $defaultHost,
                'description' => 'Invalid Subdomain Exception',
            ],
            // Invalid Host with path exception
            [
                'http_host' => 'www.invalid.' . $defaultHost . '/path',
                'expected_result' => null,
                'exception' => true,
                'exception_class' => Exception::class,
                'exception_message' => 'Invalid HTTP host provided: www.invalid.' . $defaultHost . '/path',
                'description' => 'Invalid Subdomain Exception',
            ],
            // Custom Domain without Subdomain
            [
                'http_host' => 'www.customdomain.com',
                'expected_result' => [
                    'custom_domain' => 'customdomain.com',
                    'slug' => '',
                ],
                'exception' => false,
                'exception_class' => '',
                'exception_message' => '',
                'description' => 'Custom Domain without Subdomain',
            ],
            // Custom Domain with Subdomain
            [
                'http_host' => 'www.subdomain.customdomain.com',
                'expected_result' => [
                    'custom_domain' => 'subdomain.customdomain.com',
                    'slug' => '',
                ],
                'exception' => false,
                'exception_class' => '',
                'exception_message' => '',
                'description' => 'Custom Domain with Subdomain',
            ],
        ];

        foreach ($testCases as $testCase) {
            $exceptionThrown = false;
            $exceptionClass = '';
            $exceptionMessage = '';

            try {
                $result = perfex_saas_get_tenant_info_by_host($testCase['http_host']);
                $this->CI->unit->run(
                    $result,
                    $testCase['expected_result'],
                    $testCase['description'],
                    $this->customTestNote($result, $testCase['expected_result'])
                );
            } catch (Exception $e) {
                $exceptionThrown = true;
                $exceptionClass = get_class($e);
                $exceptionMessage = $e->getMessage();
            }

            $this->CI->unit->run(
                $exceptionThrown,
                $testCase['exception'],
                'Exception Thrown - ' . $testCase['description'],
                $this->customTestNote($exceptionThrown, $testCase['exception'])
            );

            if ($testCase['exception']) {
                $this->CI->unit->run(
                    $exceptionClass,
                    $testCase['exception_class'],
                    'Exception Class - ' . $testCase['description'],
                    $this->customTestNote($exceptionClass, $testCase['exception_class'])
                );

                $this->CI->unit->run(
                    $exceptionMessage,
                    $testCase['exception_message'],
                    'Exception Message - ' . $testCase['description'],
                    $this->customTestNote($exceptionMessage, $testCase['exception_message'])
                );
            }
        }
    }

    /**
     * Test for getting tenant by http - request uri or host.
     *
     * @return void
     */
    public function testPerfexSaasGetTenantInfoByHttp()
    {
        $defaultHost = perfex_saas_get_saas_default_host();

        $testCases = [
            // Request URI with Tenant Info
            [
                'request_uri' => '/tenant1/ps',
                'host' => '',
                'expected_result' => [
                    'path_id' => 'tenant1/ps',
                    'slug' => 'tenant1',
                    'custom_domain' => '',
                    'mode' => 'path',
                ],
                'exception' => false,
                'exception_class' => '',
                'exception_message' => '',
                'description' => 'Request URI with Tenant Info',
            ],
            // Request URI without Tenant Info and Default App Host with Subdomain
            [
                'request_uri' => '/some-page',
                'host' => 'tenant1.' . $defaultHost,
                'expected_result' => [
                    'path_id' => '',
                    'slug' => 'tenant1',
                    'custom_domain' => '',
                    'mode' => 'subdomain'
                ],
                'exception' => false,
                'exception_class' => '',
                'exception_message' => '',
                'description' => 'Request URI without Tenant Info and Default App Host with Subdomain',
            ],
            // Invalid host exception 
            [
                'request_uri' => '/some-page',
                'host' => 'https://tenant1.' . $defaultHost,
                'expected_result' => [
                    'path_id' => '',
                    'slug' => 'tenant1',
                    'custom_domain' => '',
                    'mode' => 'subdomain'
                ],
                'exception' => true,
                'exception_class' => Exception::class,
                'exception_message' => 'Invalid HTTP host provided: https://tenant1.' . $defaultHost,
                'description' => 'Default App Host with scheme',
            ],
            // Request URI without Tenant Info and custom domain
            [
                'request_uri' => '/some-page',
                'host' => 'tenant1.example.com',
                'expected_result' => [
                    'path_id' => '',
                    'slug' => '',
                    'custom_domain' => 'tenant1.example.com',
                    'mode' => 'custom_domain'
                ],
                'exception' => false,
                'exception_class' => '',
                'exception_message' => '',
                'description' => 'Request URI without Tenant Info and custom domain',
            ],
            // Request URI without Tenant Info and Host without Subdomain
            [
                'request_uri' => '/some-page',
                'host' => 'example.com',
                'expected_result' => [
                    'path_id' => '',
                    'slug' => '',
                    'custom_domain' => 'example.com',
                    'mode' => 'custom_domain'
                ],
                'exception' => false,
                'exception_class' => '',
                'exception_message' => '',
                'description' => 'Request URI without Tenant Info and Host without Subdomain',
            ],
            // No Tenant Info Found
            [
                'request_uri' => '/some-page',
                'host' => '',
                'expected_result' => false,
                'exception' => false,
                'exception_class' => '',
                'exception_message' => '',
                'description' => 'No Tenant Info Found',
            ],
            // Invalid Input (Non-string Parameters)
            [
                'request_uri' => 123,
                'host' => null,
                'expected_result' => null,
                'exception' => true,
                'exception_class' => Exception::class,
                'exception_message' => 'Invalid input provided.',
                'description' => 'Invalid Input (Non-string Parameters)',
            ],
        ];

        foreach ($testCases as $testCase) {
            $exceptionThrown = false;
            $exceptionClass = '';
            $exceptionMessage = '';

            try {
                $result = perfex_saas_get_tenant_info_by_http($testCase['request_uri'], $testCase['host']);
                $this->CI->unit->run(
                    $result,
                    $testCase['expected_result'],
                    $testCase['description'],
                    $this->customTestNote($result, $testCase['expected_result'])
                );
            } catch (Exception $e) {
                $exceptionThrown = true;
                $exceptionClass = get_class($e);
                $exceptionMessage = $e->getMessage();
            }

            $this->CI->unit->run(
                $exceptionThrown,
                $testCase['exception'],
                'Exception Thrown - ' . $testCase['description'],
                $this->customTestNote($exceptionThrown, $testCase['exception'])
            );

            if ($testCase['exception']) {
                $this->CI->unit->run(
                    $exceptionClass,
                    $testCase['exception_class'],
                    'Exception Class - ' . $testCase['description'],
                    $this->customTestNote($exceptionClass, $testCase['exception_class'])
                );

                $this->CI->unit->run(
                    $exceptionMessage,
                    $testCase['exception_message'],
                    'Exception Message - ' . $testCase['description'],
                    $this->customTestNote($exceptionMessage, $testCase['exception_message'])
                );
            }
        }
    }


    /**
     * Test the perfex_saas_simple_query helper.
     * 
     * The helper parse query and localize to the active tenant.
     *
     * @return void
     */
    public function testPerfexSaasSimpleQuery()
    {
        $slug = 'tenant_slug';

        // Valid queries
        $valid_queries = [
            [
                'sql' => 'SELECT * FROM customers',
                'expected_result' => 'SELECT * FROM customers WHERE customers.' . PERFEX_SAAS_TENANT_COLUMN . ' = \'tenant_slug\'',
                'test_name' => 'Valid Query Test',
            ],
            [
                'sql' => 'SELECT COUNT(*) FROM orders WHERE status = "completed"',
                'expected_result' => 'SELECT COUNT(*) FROM orders WHERE status = "completed" and orders.' . PERFEX_SAAS_TENANT_COLUMN . ' = \'tenant_slug\'',
                'test_name' => 'Valid Query with Where Clause Test',
            ],
            [
                'sql' => 'SELECT * FROM products WHERE price > 100',
                'expected_result' => 'SELECT * FROM products WHERE price > 100 and products.' . PERFEX_SAAS_TENANT_COLUMN . ' = \'tenant_slug\'',
                'test_name' => 'Valid Query with Where Clause and Comparison Test',
            ],
        ];

        foreach ($valid_queries as $query) {
            $result = perfex_saas_simple_query($slug, $query['sql']);

            $this->CI->unit->run(
                $result,
                $query['expected_result'],
                $query['test_name'],
                $this->customTestNote($result, $query['expected_result'])
            );
        }

        // Invalid queries
        $invalid_queries = [
            [
                'sql' => 'ALTER TABLE customers ADD COLUMN email VARCHAR(255)',
                'expected_exception' => \Exception::class,
                'test_name' => 'Invalid Query with Unsupported Operation Test',
            ],
            [
                'sql' => 'SELECT COUNT(*) FROM (SELECT 1, 2, 3) AS temp',
                'expected_exception' => \Exception::class,
                'test_name' => 'Query without Table Test',
            ],
        ];

        foreach ($invalid_queries as $query) {
            $expected_exception = $query['expected_exception'];
            $test_name = $query['test_name'];

            try {
                perfex_saas_simple_query($slug, $query['sql']);
                $result = 'No exception thrown';
            } catch (\Exception $e) {
                $result = get_class($e);
            }

            $this->CI->unit->run(
                $result,
                $expected_exception,
                $test_name,
                $this->customTestNote($result, $expected_exception)
            );
        }

        // Write queries
        $write_queries = [
            [
                'sql' => 'INSERT INTO orders (customer_id, total) VALUES (1, 100)',
                'expected_result' => "INSERT INTO orders (customer_id, total, `" . PERFEX_SAAS_TENANT_COLUMN . "`) VALUES (1, 100, 'tenant_slug')",
                'test_name' => 'Insert Query Test',
            ],
            [
                'sql' => 'UPDATE customers SET name = "John Doe" WHERE id = 1',
                'expected_result' => 'UPDATE customers SET name = "John Doe",`' . PERFEX_SAAS_TENANT_COLUMN . '` = \'tenant_slug\' WHERE id = 1 and customers.' . PERFEX_SAAS_TENANT_COLUMN . ' = \'tenant_slug\'',
                'test_name' => 'Update Query Test',
            ],
            [
                'sql' => 'DELETE FROM products WHERE price < 10',
                'expected_result' => 'DELETE FROM products WHERE price < 10 and products.' . PERFEX_SAAS_TENANT_COLUMN . ' = \'tenant_slug\'',
                'test_name' => 'Delete Query Test',
            ],
            [
                'sql' => 'INSERT INTO orders (customer_id, total,' . PERFEX_SAAS_TENANT_COLUMN . ') VALUES (1, 100,"tenant_slug")',
                'expected_result' => "INSERT INTO orders (customer_id, total, `" . PERFEX_SAAS_TENANT_COLUMN . "`) VALUES (1, 100, 'tenant_slug')",
                'test_name' => 'Write Query with Existing Tenant Column Test',
            ],
        ];

        foreach ($write_queries as $query) {
            $result = perfex_saas_simple_query($slug, $query['sql']);

            $this->CI->unit->run(
                $result,
                $query['expected_result'],
                $query['test_name'],
                $this->customTestNote($result, $query['expected_result'])
            );
        }
    }

    /**
     * Test perfex_saas_parse_dsn helper for parsing DSN string to array
     *
     * @return void
     */
    public function testPerfexSaasParseDsn()
    {
        // Valid DSN
        $valid_dsn = 'mysql:host=localhost;dbname=database;user=username;password=pass123;';
        $valid_expected_result = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'dbname' => 'database',
            'user' => 'username',
            'password' => 'pass123',
        ];

        $valid_result = perfex_saas_parse_dsn($valid_dsn);
        $this->CI->unit->run(
            $valid_result,
            $valid_expected_result,
            'Valid DSN Test',
            $this->customTestNote($valid_result, $valid_expected_result)
        );

        // Valid DSN with specific keys requested
        $valid_dsn_specific_keys = 'pgsql:host=localhost;dbname=database;user=username;password=pass123;';
        $valid_expected_result_specific_keys = [
            'driver' => 'pgsql',
            'dbname' => 'database',
        ];

        $valid_result_specific_keys = perfex_saas_parse_dsn($valid_dsn_specific_keys, ['driver', 'dbname']);
        $this->CI->unit->run(
            $valid_result_specific_keys,
            $valid_expected_result_specific_keys,
            'Valid DSN with Specific Keys Test',
            $this->customTestNote($valid_result_specific_keys, $valid_expected_result_specific_keys)
        );

        // Empty DSN
        $empty_dsn = '';
        $empty_dsn_expected_exception = Exception::class;

        try {
            perfex_saas_parse_dsn($empty_dsn);
            $empty_dsn_result = 'No exception thrown';
        } catch (Exception $e) {
            $empty_dsn_result = get_class($e);
        }

        $this->CI->unit->run(
            $empty_dsn_result,
            $empty_dsn_expected_exception,
            'Empty DSN Test',
            $this->customTestNote($empty_dsn_result, $empty_dsn_expected_exception)
        );

        // Invalid DSN with no driver
        $invalid_dsn_no_driver = ':host=localhost;dbname=database;user=username;password=pass123';
        $invalid_dsn_no_driver_expected_exception = Exception::class;

        try {
            perfex_saas_parse_dsn($invalid_dsn_no_driver);
            $invalid_dsn_no_driver_result = 'No exception thrown';
        } catch (Exception $e) {
            $invalid_dsn_no_driver_result = get_class($e);
        }

        $this->CI->unit->run(
            $invalid_dsn_no_driver_result,
            $invalid_dsn_no_driver_expected_exception,
            'Invalid DSN with No Driver Test',
            $this->customTestNote($invalid_dsn_no_driver_result, $invalid_dsn_no_driver_expected_exception)
        );
    }

    /**
     * Test for perfex_saas_safe_query helper for importing perfex sql file
     *
     * @return void
     */
    public function testPerfexSaasSafeQuery()
    {
        // Safe query
        $safe_query = 'SELECT * FROM customers';
        $safe_expected_result = $safe_query;

        $safe_result = perfex_saas_safe_query($safe_query, 'current_database');
        $this->CI->unit->run(
            $safe_result,
            $safe_expected_result,
            'Safe Query Test',
            $this->customTestNote($safe_result, $safe_expected_result)
        );

        // Empty query
        $empty_query = '';
        $empty_query_expected_exception = Exception::class;

        try {
            perfex_saas_safe_query($empty_query, 'current_database');
            $empty_query_result = 'No exception thrown';
        } catch (Exception $e) {
            $empty_query_result = get_class($e);
        }

        $this->CI->unit->run(
            $empty_query_result,
            $empty_query_expected_exception,
            'Empty Query Test',
            $this->customTestNote($empty_query_result, $empty_query_expected_exception)
        );

        // Prohibited statements
        $prohibited_query = 'DROP TABLE customers';
        $prohibited_query_expected_exception = Exception::class;

        try {
            perfex_saas_safe_query($prohibited_query, 'current_database');
            $prohibited_query_result = 'No exception thrown';
        } catch (Exception $e) {
            $prohibited_query_result = get_class($e);
        }

        $this->CI->unit->run(
            $prohibited_query_result,
            $prohibited_query_expected_exception,
            'Prohibited Statement Test',
            $this->customTestNote($prohibited_query_result, $prohibited_query_expected_exception)
        );

        // SQL injection
        $injection_query = "SELECT * FROM customers WHERE id = 1; DROP TABLE customers";
        $injection_query_expected_exception = Exception::class;

        try {
            perfex_saas_safe_query($injection_query, 'current_database');
            $injection_query_result = 'No exception thrown';
        } catch (Exception $e) {
            $injection_query_result = get_class($e);
        }

        $this->CI->unit->run(
            $injection_query_result,
            $injection_query_expected_exception,
            'SQL Injection Test',
            $this->customTestNote($injection_query_result, $injection_query_expected_exception)
        );

        // Dangerous SQL case
        $dangerous_query = "DROP DATABASE current_database";
        $dangerous_query_expected_exception = Exception::class;

        try {
            perfex_saas_safe_query($dangerous_query, 'current_database');
            $dangerous_query_result = 'No exception thrown';
        } catch (Exception $e) {
            $dangerous_query_result = get_class($e);
        }

        $this->CI->unit->run(
            $dangerous_query_result,
            $dangerous_query_expected_exception,
            'Dangerous SQL Case Test',
            $this->customTestNote($dangerous_query_result, $dangerous_query_expected_exception)
        );
    }
}
