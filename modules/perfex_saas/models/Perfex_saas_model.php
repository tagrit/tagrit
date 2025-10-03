<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once 'Perfex_saas_subscription_trait.php';

class Perfex_saas_model extends App_Model
{
    use Perfex_saas_subscription_trait;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get data from a table.
     *
     * @param string $table The name of the table.
     * @param string $id The ID of the data to retrieve. If empty, retrieve all data.
     * @return mixed The retrieved data.
     */
    function get($table, $id = '')
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->order_by('id', 'ASC');

        if (!empty($id)) {
            $this->db->where('id', $id);
        }

        $query = $this->db->get();

        return empty($id) ? $query->result() : $query->row();
    }

    /**
     * Get an entity by slug.
     *
     * @param string $entity The entity name.
     * @param string $slug The slug of the entity.
     * @param string $parse_method The slef method to use for parsing the entity.
     * @return mixed The retrieved entity.
     */
    function get_entity_by_slug($entity, $slug, $parse_method = '')
    {
        $this->db->select();
        $this->db->from(perfex_saas_table($entity));
        $this->db->where('slug', $slug);

        $row = $this->db->get()->row();

        if (!empty($parse_method) && !empty($row)) {
            $row = $this->{$parse_method}($row);
        }

        return $row;
    }

    /**
     * Add or update an entity.
     *
     * @param string $entity The entity name.
     * @param array $data The data to add or update.
     * @return int|bool The ID of the added or updated entity, or false on failure.
     */
    public function add_or_update(string $entity, array $data)
    {
        return $this->add_or_update_raw(perfex_saas_table($entity), $data);
    }

    /**
     * Add or update an entity using raw table name.
     *
     * @param string $table The name of the table.
     * @param array $data The data to add or update.
     * @return int|bool The ID of the added or updated entity, or false on failure.
     */
    public function add_or_update_raw(string $table, array $data)
    {
        $id = false;

        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            if ($this->db->update($table, $data)) {
                $id = $data['id'];
            }
        } else {
            $this->db->insert($table, $data);
            $id = $this->db->insert_id();
        }

        return $id;
    }

    /**
     * Delete an entity by ID.
     *
     * @param string $entity The entity name.
     * @param mixed $id The ID of the entity to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(string $entity, $id)
    {
        $this->db->where('id', $id);
        return $this->db->delete(perfex_saas_table($entity));
    }

    /**
     * Clone an entity by ID.
     *
     * @param string $entity The entity name.
     * @param mixed $id The ID of the entity to clone.
     * @return int|bool The ID of the cloned entity, or false on failure.
     */
    public function clone(string $entity, $id)
    {
        $table = perfex_saas_table($entity);

        $entity_data = $this->get($table, $id);
        if (!$entity_data) {
            return false;
        }

        $total = count($this->get($table));

        if (isset($entity_data->name)) {
            $entity_data->name = $entity_data->name . '#' . ($total + 1);
        }

        if (isset($entity_data->slug)) {
            $entity_data->slug = perfex_saas_generate_unique_slug($entity_data->name, $entity);
        }

        if (isset($entity_data->is_default)) {
            $entity_data->is_default = 0;
        }

        unset($entity_data->id);

        $filter =  hooks()->apply_filters('perfex_saas_package_clone_filter', ['entity' => $entity, 'entity_data' => (array)$entity_data]);
        $entity = $filter['entity'];
        $entity_data =  (array)$filter['entity_data'];

        return $this->add_or_update($entity, $entity_data);
    }

    /**
     * Check if the database user has create privilege
     * @return bool
     */
    public function db_user_has_create_privilege()
    {
        $debug_mode = $this->db->db_debug;
        $has_priviledge = false;
        try {
            $db = perfex_saas_db('testdb');
            $this->db->db_debug = false;
            if ($this->db->query('CREATE DATABASE ' . $db)) {
                if (!$this->db->query('DROP DATABASE `' . $db . '`'))
                    throw new \Exception("Error dropping test db $db", 1);
            } else {
                throw new \Exception("Error creating database", 1);
            }
            $has_priviledge = true;
        } catch (\Throwable $th) {
            log_message('error', 'Database user dont have permission to create new db:' . $th->getMessage());
        }
        $this->db->db_debug = $debug_mode;
        return $has_priviledge;
    }

    /**
     * Create a database
     * @param string $db The name of the database to create
     * @return bool|string True on success, error message on failure
     */
    public function create_database($db)
    {
        try {
            if (!$this->db->query("CREATE DATABASE IF NOT EXISTS `$db`")) {
                throw new \Exception("Error creating database $db", 1);
            }
            return true;
        } catch (\Throwable $th) {
            log_message('error', 'Database user dont have permission to create new db:' . $th->getMessage());
            return $th->getMessage();
        }
        return false;
    }

    /**
     * Get database pools population by package ID
     * @param int $package_id The ID of the package
     * @return array Array containing population map and pools map
     */
    public function get_db_pools_population_by_packgeid($package_id)
    {
        $packages = $this->packages($package_id);
        $packages = !empty($package_id) ? [$packages] : $packages;

        $dbprefix = perfex_saas_master_db_prefix();

        $query = "SHOW TABLES LIKE '%\_" . $dbprefix . "staff'";
        $population_map = [];
        $pools_map = [];

        foreach ($packages as $p) {
            $pools = $p->db_pools;

            if (!empty($pools)) {
                foreach ($pools as $pool) {
                    $pool = (array)$pool;
                    $key = perfex_saas_dsn_to_string($pool, false);

                    if (!isset($pools_map[$key])) {
                        try {

                            $population = count(perfex_saas_raw_query($query, $pool, true));
                            $population_map[$key] = (int)$population;
                            $pools_map[$key] = $pool;
                        } catch (\Throwable $th) {
                            log_message("error", "Error reading stat from database: " . ($pool['dbname'] ?? '') . $th->getMessage());
                        }
                    }
                }
            }
        }

        return [$population_map, $pools_map];
    }

    /**
     * Get database pools population
     * @param array $pools Array of database pools
     * @return array Array containing population map and pools map
     */
    public function get_db_pools_population($pools)
    {
        $dbprefix = perfex_saas_master_db_prefix();

        $query = "SHOW TABLES LIKE '%\_" . $dbprefix . "staff'";
        $population_map = [];
        $pools_map = [];

        foreach ($pools as $pool) {
            $pool = (array)$pool;
            $key = perfex_saas_dsn_to_string($pool, false);

            if (!isset($pools_map[$key])) {

                $total = count(perfex_saas_raw_query($query, $pool, true));
                $population = $total;
                $population_map[$key] = (int)$population;
                $pools_map[$key] = $pool;
            }
        }

        return [$population_map, $pools_map];
    }

    /**
     * Method to make basic statistic about a pacakge
     *
     * @param int $packageid
     * @return object
     */
    function package_stats($packageid)
    {

        list($populations) = $this->get_db_pools_population_by_packgeid($packageid);
        $total_pool_population = array_sum(array_values($populations));

        $packageid_col = perfex_saas_column('packageid');
        $query = 'SELECT COUNT(DISTINCT(' .  $packageid_col . ')) as total FROM `' . perfex_saas_master_db_prefix() . 'invoices` WHERE `' . $packageid_col . "`=$packageid";
        $resp = perfex_saas_raw_query_row($query, [], true);
        $total_subscribed = $resp->total ?? 0;

        return (object)['total_invoices' => $total_subscribed, 'total_pool_population' => $total_pool_population];
    }

    /**
     * Get the chart dataset for packag invoice dourghnut chart
     *
     * @return array
     */
    public function package_invoice_chart()
    {
        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];

        $packages = $this->packages();
        foreach ($packages as $package) {

            $result = $this->package_stats($package->id)->total_invoices;
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            array_push($chart['labels'], $package->name);
            array_push($_data['backgroundColor'], $color);
            array_push($_data['hoverBackgroundColor'], adjust_color_brightness($color, -20));
            array_push($_data['data'], $result);
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }


    /**
     * Get the list of database schemes for tenant management.
     *
     * @return array The array of database schemes.
     */
    public function db_schemes()
    {
        // Define an array of database schemes for tenant management
        $schemes = [
            ['key' => 'multitenancy', 'label' => _l('perfex_saas_use_the_current_active_database_for_all_tenants')], // Option for using the current active database for all tenants
            ['key' => 'single', 'label' => _l('perfex_saas_use_single_database_for_each_company_instance.')], // Option for using a single database for each company instance
            ['key' => 'single_pool', 'label' => _l('perfex_saas_single_pool_db_scheme')], // Option for using a single pool database scheme
            ['key' => 'shard', 'label' => _l('perfex_saas_distribute_companies_data_among_the_provided_databases_in_the_pool')], // Option for distributing companies' data among provided databases in the pool
        ];

        // Check if the current database user has the privilege to create a new database
        if (!$this->db_user_has_create_privilege()) {
            unset($schemes[1]); // Remove the option for single database per company if the privilege is not available
        }

        $schemes = hooks()->apply_filters('perfex_saas_module_db_schemes', $schemes);

        return $schemes;
    }

    /**
     * Get the alternative list of database schemes for tenant management.
     *
     * @return array The array of alternative database schemes.
     */
    public function db_schemes_alt()
    {
        // Define an array of alternative database schemes for tenant management
        $schemes = [
            ['key' => 'package', 'label' => _l('perfex_saas_auto_detect_from_client_subcribed_package')], // Option for auto-detecting database scheme from client subscribed package
            ['key' => 'multitenancy', 'label' => _l('perfex_saas_use_the_current_active_database')], // Option for using the current active database
            ['key' => 'single', 'label' => _l('perfex_saas_create_a_separate_database')], // Option for creating a separate database
            ['key' => 'shard', 'label' => _l('perfex_saas_i_will_provide_database_credential')], // Option for providing database credentials
        ];

        // Check if the current database user has the privilege to create a new database
        if (!$this->db_user_has_create_privilege()) {
            unset($schemes[1]); // Remove the option for using the current active database if the privilege is not available
        }

        $schemes = hooks()->apply_filters('perfex_saas_module_db_schemes_alt', $schemes);

        return $schemes;
    }

    /**
     * Get the list of company status options.
     *
     * @return array The array of company status options.
     */
    public function company_status_list()
    {
        $statuses = [];
        // Return an array of company status options
        $editable_statuses = [PERFEX_SAAS_STATUS_ACTIVE, PERFEX_SAAS_STATUS_INACTIVE, PERFEX_SAAS_STATUS_BANNED];
        foreach ($editable_statuses as $key => $value) {
            $statuses[] = ['key' => $value, 'label' => _l('perfex_saas_' . $value)];
        }
        return $statuses;
    }

    /**
     * Get the list of yes/no options.
     *
     * @return array The array of yes/no options.
     */
    public function yes_no_options()
    {
        // Return an array of yes/no options
        return [
            ['key' => 'no', 'label' => _l('perfex_saas_no')], // Option for "No"
            ['key' => 'yes', 'label' => _l('perfex_saas_yes')] // Option for "Yes"
        ];
    }

    /**
     * Get the shared options from the database.
     *
     * @return array The array of shared options.
     */
    public function shared_options()
    {
        $options_table = perfex_saas_master_db_prefix() . 'options';
        // Retrieve the options from the database
        $this->db->select("`name` as 'key', REPLACE(`name`,'_',' ') as 'name'");
        $this->db->where("`name` NOT LIKE 'perfex_saas%'");
        $results = $this->db->get($options_table)->result();
        return $results;
    }

    /**
     * Get options that are classified as dangerous to share for tenant seeding
     *
     * @return array
     */
    public function sensitive_shared_options()
    {
        $options_table = perfex_saas_master_db_prefix() . 'options';
        $sql = "SELECT `name` as 'key', REPLACE(`name`,'_',' ') as 'name' FROM `" . $options_table . "` WHERE
                `name` LIKE '%password%' OR `name` LIKE '%key%' OR `name` LIKE '%secret%' OR `name` LIKE '%\_id' OR `name` LIKE '%token' OR
                `name` LIKE '%company_logo%' OR `name` ='favicon' OR `name` ='main_domain' OR
                `name` LIKE 'invoice_company\_%' OR `name` ='company_vat' OR `name` ='company_state' OR
                `name` LIKE 'perfex_saas%'
                ";
        $results = $this->db->query($sql)->result();
        return $results;
    }

    /**
     * List of default inbuilt perfex modules
     *
     * @param boolean $parse If to format or not into system_name and custom_name associative array.
     * @return array
     */
    public function default_modules($parse = true)
    {
        $default_modules = ['leads', 'projects', 'tasks', 'expenses', 'proposals', 'estimates', 'estimate_request', 'tickets', 'reports', 'contracts', 'knowledge_base', 'custom_fields', 'credit_notes', 'subscriptions', 'invoices', 'items', 'payments'];
        asort($default_modules);
        if ($parse) {
            foreach ($default_modules as $key => $value) {
                $default_modules[$key] = ['system_name' => $value, 'custom_name' => ucfirst(str_replace('_', ' ', $value))];
            }
        }
        return $default_modules;
    }

    /**
     * Get the list of modules installed on perfex.
     *
     * @param bool $exclude_self Flag to exclude the perfex saas module.
     * @return array The array of modules.
     */
    public function modules($exclude_self = true)
    {
        // Get the list of modules
        $_modules = $this->app_modules->get();
        $modules = [];

        $option_getter_method = perfex_saas_is_tenant() ? 'perfex_saas_get_options' : 'get_option';

        // Retrieve the custom module names from the options
        $custom_modules_name = $option_getter_method('perfex_saas_custom_modules_name');
        $custom_modules_name = empty($custom_modules_name) ? [] : json_decode($custom_modules_name, true);

        $modules_market_settings = json_decode($option_getter_method('perfex_saas_modules_marketplace') ?? '', true);

        foreach ($_modules as $value) {
            $module_id = $value['system_name'];

            // Check if the module is the self module and exclude it if necessary
            if ($module_id == PERFEX_SAAS_MODULE_NAME && $exclude_self) {
                continue;
            }

            if ($module_id == 'debug_mode') continue; // Prevent debug mode assignment for tenants.

            $modules[$module_id] = $value;

            // Assign the custom name to the module if available, otherwise use the default module name
            $modules[$module_id]['custom_name'] = isset($custom_modules_name[$module_id]) ? $custom_modules_name[$module_id] : $modules[$module_id]['headers']['module_name'];
            $modules[$module_id]['custom_name'] = _l($modules[$module_id]['custom_name'], '', false);

            // Add marketplace info
            $modules[$module_id]['description'] = $modules_market_settings[$module_id]['description'] ?? '';
            $modules[$module_id]['price'] = $modules_market_settings[$module_id]['price'] ?? '';
            $modules[$module_id]['billing_mode'] = $modules_market_settings[$module_id]['billing_mode'] ?? '';
            $modules[$module_id]['image'] = $modules_market_settings[$module_id]['image'] ?? '';
        }

        $modules  = hooks()->apply_filters('perfex_saas_module_list_filter', $modules);

        return $modules;
    }

    /**
     * Return list of custom services
     *
     * @return array
     */
    public function services()
    {
        $option = get_option('perfex_saas_custom_services');
        $option = empty($option) ? [] : json_decode($option, true);
        foreach ($option as $key => $value) {
            if (empty($key) || empty($value))
                unset($option[$key]);
        }
        return $option;
    }

    /**
     * Mark a package as default.
     *
     * @param int $package_id The ID of the package to mark as default.
     * @return bool True on success, false on failure.
     */
    public function mark_package_as_default($package_id)
    {
        $table = perfex_saas_table('packages');
        $this->db->update($table, ['is_default' => 0]);

        $this->db->where('id', $package_id);
        return $this->db->update($table, ['is_default' => 1]);
    }

    /**
     * Get all packages or single package by id.
     *
     * @param mixed $id The ID of the package to retrieve. If empty, retrieve all packages.
     * @return array|object|null The retrieved packages.
     */
    function packages($id = '')
    {
        $packages = $this->get(perfex_saas_table('packages'), $id);

        if (!empty($id) && !empty($packages)) {
            $packages = [$packages];
        }

        foreach ($packages as $key => $package) {
            $package = $this->parse_package($package);
            $packages[$key] = $package;
        }

        // Sort the packages by metadata->priority
        usort($packages, function ($a, $b) {
            // Handle cases where metadata or priority may not exist
            $priorityA = isset($a->metadata->priority) ? $a->metadata->priority : PHP_INT_MIN;
            $priorityB = isset($b->metadata->priority) ? $b->metadata->priority : PHP_INT_MIN;

            // Reverse the comparison for descending order
            return $priorityB <=> $priorityA;
        });

        return !empty($id) ? ($packages[0] ?? null) : $packages;
    }

    /**
     * Filter packages by assigned clients ids
     *
     * @param array $packages
     * @param string|int $clientid
     * @param string|int $subscribed_package_id Optional
     * @return array
     */
    function packages_filter_by_assigned_client($packages, $clientid, $subscribed_package_id = '')
    {
        foreach ($packages as $key => $package) {

            if ($package->id == $subscribed_package_id) continue;

            // Filter package assignment to customer
            $assigned_clients = $package->metadata->assigned_clients ?? '';
            if (!empty($assigned_clients)) {

                if (is_string($assigned_clients)) $assigned_clients = (array)json_decode($assigned_clients, true);

                $assigned_clients = array_filter($assigned_clients);
                if (!empty($assigned_clients) && !in_array($clientid, $assigned_clients)) {
                    unset($packages[$key]);
                }
            }
        }

        return $packages;
    }

    /**
     * Get the default package
     *
     * @return mixed
     */
    function default_package()
    {
        $this->db->where('is_default', 1);
        $default_package = $this->packages();
        return $default_package[0] ?? null;
    }

    /**
     * Get all companies or single company speicifc by id.
     *
     * @param mixed $id The ID of the company to retrieve. If empty, retrieve all companies.
     * @param bool  $skip_parsing If to parse or not
     * @return array|object The retrieved companies.
     */
    public function companies($id = '', $skip_parsing = false)
    {
        if (is_client_logged_in()) {
            $this->db->where('clientid', get_client_user_id());
        }

        $companies = $this->get(perfex_saas_table('companies'), $id);
        if (!empty($id)) {
            $companies = [$companies];
        }

        if (!$skip_parsing) {
            foreach ($companies as $key => $company) {
                if (!empty($company))
                    $companies[$key] = $this->parse_company($company);
            }
        }

        return !empty($id) ? $companies[0] : $companies;
    }

    /**
     * Get a company by its slug.
     *
     * @param string $slug The company slug.
     * @param string $clientid The client ID.
     * @return mixed The company with the given slug.
     */
    public function get_company_by_slug($slug, $clientid = '')
    {
        if ($clientid) {
            $this->db->where('clientid', $clientid);
        }
        return $this->get_entity_by_slug('companies', $slug, 'parse_company');
    }


    /**
     * Create or update a company.
     *
     * @param mixed $data The company data.
     * @param mixed $invoice The invoice data.
     * @return mixed The ID of the created or updated company.
     * @throws \Exception When the company payload is malformed or certain conditions are not met.
     */
    public function create_or_update_company($data, $invoice)
    {

        $company = null;
        if (!empty($data['id'])) {
            $company = $this->companies($data['id']);
        }

        $creating_new = empty($company->id);

        if ($creating_new || empty($data['id'])) {
            if (empty($data['clientid']) || empty($data['name'])) {
                throw new \Exception(_l('perfex_saas_malformed_company_payload'), 1);
            }
        }


        $creating_as_admin = !is_client_logged_in() && (
            staff_can('create', 'perfex_saas_companies') &&
            staff_can('edit', 'perfex_saas_companies')
        );

        $data['metadata'] = isset($data['metadata']) ? (array)$data['metadata'] : [];


        // Handle custom domain - updating or create
        $custom_domain = $data['custom_domain'] ?? '';
        if (!empty($custom_domain)) {

            // Check if custom domain is actullay changing 
            if ($custom_domain !== ($company->custom_domain ?? '')) {
                if (!perfex_saas_is_valid_custom_domain($custom_domain))
                    throw new \Exception(_l('perfex_saas_invalid_custom_domain', $custom_domain));

                // Ensure custom domain not taken
                $this->db->where('custom_domain', $custom_domain);
                if (!$creating_new) {
                    $this->db->where('slug !=', $company->slug);
                }
                $count = count($this->get(perfex_saas_table('companies')));
                if ($count) throw new \Exception(_l('perfex_saas_custom_domain_exist'), 1);


                $autoapprove = (int)($invoice->metadata->autoapprove_custom_domain ?? 0);
                if (!$creating_as_admin && !$autoapprove) { // Make pending
                    $data['metadata']['pending_custom_domain'] = $custom_domain;
                    unset($data['custom_domain']);
                }
            }
        }


        // Create actions
        if ($creating_new) {

            // Check limit for the owner
            $max_instance_limit = perfex_saas_get_tenant_instance_limit($invoice);
            $this->db->where('clientid', $data['clientid']);
            $count = count($this->companies());
            if ($max_instance_limit > 0 && $count >= $max_instance_limit) {
                throw new \Exception(_l('perfex_saas_max_instance_reached' . ($creating_as_admin ? '_admin' : ''), $max_instance_limit), 1);
            }

            // Handle slug
            $slug = isset($data['slug']) && !empty($data['slug']) ? $data['slug'] : explode(' ', $data['name'])[0];

            // Ensure we have valid unique slug
            $slug = perfex_saas_generate_unique_slug($slug, 'companies', $data['id'] ?? '');

            $data['slug'] = $slug;

            // Set default to empty for client and leave for admin.
            $data['dsn'] = $creating_as_admin ? ($data['dsn'] ?? '') : '';

            // Determine the dsn if none is provided so far
            if (empty($data['dsn'])) {

                // If invoice is single db, set the dbname. This prevents saving the master db credential to the database.
                if ($invoice->db_scheme == 'single') {

                    if (!$this->db_user_has_create_privilege()) {
                        throw new \Exception(_l('perfex_saas_db_scheme_single_not_supported'), 1);
                    }

                    $dbname = perfex_saas_db($data['slug']);
                    $create_db = $this->create_database($dbname);
                    if ($create_db !== true) {
                        throw new \Exception(_l('Error creating database: ' . $create_db), 1);
                    }

                    $data['dsn'] = perfex_saas_dsn_to_string([
                        'dbname' => $dbname
                    ]);
                }

                if ($invoice->db_scheme == 'multitenancy') {
                    $data['dsn'] = perfex_saas_dsn_to_string([
                        'dbname' => APP_DB_NAME,
                    ]);
                }

                if ($invoice->db_scheme == 'single_pool' || $invoice->db_scheme == 'shard') {

                    $dsn = perfex_saas_get_company_dsn($company, $invoice);
                    if (!perfex_saas_is_valid_dsn($dsn, true)) {
                        throw new \Exception(_l('perfex_saas_invalid_datacenter'), 1);
                    }

                    $data['dsn'] = perfex_saas_dsn_to_string($dsn);
                }
            }

            if (empty($company->id)) { // Create
                // Make pending by default. Only pending will be picked up by deployer.
                $data['status'] = PERFEX_SAAS_STATUS_PENDING;
            }

            // Make pending by default. Only pending will be picked up by deployer.
            $data['status'] = PERFEX_SAAS_STATUS_PENDING;

            if (empty($data['created_at'] ?? '')) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }
        }

        $filter = hooks()->apply_filters('perfex_saas_module_tenant_data_payload', ['data' => $data, 'tenant' => $company, 'invoice' => $invoice, 'error' => '']);
        $data = $filter['data'];

        if (!empty($filter['error'])) {
            throw new \Exception($filter['error'], 1);
        }


        if (isset($data['dsn']) && !is_string($data['dsn'])) {
            throw new \Exception("DSN must be provided in string format", 1);
        }

        // Updating
        if (!$creating_new) {
            if (!$creating_as_admin) {
                if (
                    isset($data['status']) &&
                    $data['status'] !== PERFEX_SAAS_STATUS_PENDING_DELETE
                ) {
                    unset($data['status']);
                }
            }

            // Ensure slug is not updated
            if (isset($data['slug'])) {
                unset($data['slug']);
            }
        }

        // Admin options
        if (isset($data['db_scheme'])) {
            unset($data['db_scheme']);
        }

        if (isset($data['db_pools'])) {
            unset($data['db_pools']);
        }

        // Encrypt any DSN info to be saved to the DB
        if ((is_admin() || empty($company->id)) && isset($data['dsn']) && !empty($data['dsn'])) {
            $data['dsn'] = $this->encryption->encrypt($data['dsn']);
        }

        $old_metadata = (array)(isset($company->metadata) ? $company->metadata : []);
        if (isset($data['metadata'])) {
            $data['metadata'] = json_encode(array_merge($old_metadata, $data['metadata']));
        }

        // Save and make deployment another job
        $_id = $this->add_or_update('companies', $data);

        // Trigger module setup for the tenant
        if (!defined('CRON'))
            perfex_saas_trigger_module_install('', $company->slug ?? $data['slug']);

        hooks()->do_action('perfex_saas_module_tenant_created_or_updated', $_id);

        return $_id;
    }

    /**
     * Delete a company.
     *
     * @param string $id The ID of the company to delete
     * @return mixed String when error otherwise int or bool
     */
    public function delete_company(int $id)
    {
        $company = $this->companies($id);
        $invoice  = $this->get_company_invoice($company->clientid);

        if (!$company) return false;

        // Check for soft deleted and procees
        $days_to_wait = (float)get_option('perfex_saas_instance_delete_pending_days');
        $delay_tenant_removal = $days_to_wait > 0;

        if (ENVIRONMENT == 'development') {
            // Dont delay instance in deployment in development mode
            if (in_array($company->status, [PERFEX_SAAS_STATUS_DEPLOYING, PERFEX_SAAS_STATUS_PENDING])) {
                $delay_tenant_removal = false;
            }
        }

        if ($delay_tenant_removal) {
            $data = ['id' => $id, 'status' => PERFEX_SAAS_STATUS_PENDING_DELETE, 'metadata' => ['deleted_at' => date('Y-m-d H:i:s')]];
            if ($this->create_or_update_company($data, $invoice))
                return true;
            else
                return false;
        }

        $removed = false;
        try {
            $removed = perfex_saas_remove_company($company);
        } catch (\Throwable $th) {
            $removed = $th->getMessage();
        }

        if ($this->delete('companies', $id))
            return $removed;

        return false;
    }

    /**
     * Get invoice total from all statuses
     * @since  Version 0.0.5
     * @param  mixed $data
     * @return array
     */
    public function get_invoices_total($data)
    {
        $this->load->model('currencies_model');
        $this->load->model('invoices_model');

        if (isset($data['currency'])) {
            $currencyid = $data['currency'];
        } else {
            $currencyid = $this->currencies_model->get_base_currency()->id;
        }

        $result            = [];
        $result['due']     = [];
        $result['paid']    = [];
        $result['overdue'] = [];

        $has_permission_view                = staff_can('view', 'invoices');
        $noPermissionsQuery                 = get_invoices_where_sql_for_staff(get_staff_user_id());

        $dbprefix = perfex_saas_master_db_prefix();

        for ($i = 1; $i <= 3; $i++) {
            $select = 'id,total';
            if ($i == 1) {
                $select .= ', (SELECT total - (SELECT COALESCE(SUM(amount),0) FROM ' . $dbprefix . 'invoicepaymentrecords WHERE invoiceid = ' . $dbprefix . 'invoices.id) - (SELECT COALESCE(SUM(amount),0) FROM ' . $dbprefix . 'credits WHERE ' . $dbprefix . 'credits.invoice_id=' . $dbprefix . 'invoices.id)) as outstanding';
            } elseif ($i == 2) {
                $select .= ',(SELECT SUM(amount) FROM ' . $dbprefix . 'invoicepaymentrecords WHERE invoiceid=' . $dbprefix . 'invoices.id) as total_paid';
            }
            $this->db->select($select);
            $this->db->from($dbprefix . 'invoices');
            $this->db->where('currency', $currencyid);

            // Must be recurring or subscription
            $this->db->where(' (recurring > 0 OR subscription_id > 0) ');

            // Must have packageid
            $this->db->where(perfex_saas_column('packageid') . ' IS NOT NULL');
            $this->db->where(perfex_saas_column('packageid') . " != ''");

            // Exclude cancelled invoices
            $this->db->where('status !=', Invoices_model::STATUS_CANCELLED);

            // Exclude draft
            $this->db->where('status !=', Invoices_model::STATUS_DRAFT);

            if (isset($data['project_id']) && $data['project_id'] != '') {
                $this->db->where('project_id', $data['project_id']);
            } elseif (isset($data['customer_id']) && $data['customer_id'] != '') {
                $this->db->where('clientid', $data['customer_id']);
            }

            if ($i == 3) {
                $this->db->where('status', Invoices_model::STATUS_OVERDUE);
            } elseif ($i == 1) {
                $this->db->where('status !=', Invoices_model::STATUS_PAID);
            }

            if (isset($data['years']) && count($data['years']) > 0) {
                $this->db->where_in('YEAR(date)', $data['years']);
            }

            if (!$has_permission_view) {
                $whereUser = $noPermissionsQuery;
                $this->db->where('(' . $whereUser . ')');
            }

            $invoices = $this->db->get()->result_array();

            foreach ($invoices as $invoice) {
                if ($i == 1) {
                    $result['due'][] = $invoice['outstanding'];
                } elseif ($i == 2) {
                    $result['paid'][] = $invoice['total_paid'];
                } elseif ($i == 3) {
                    $result['overdue'][] = $invoice['total'];
                }
            }
        }
        $currency             = get_currency($currencyid);
        $result['due']        = array_sum($result['due']);
        $result['paid']       = array_sum($result['paid']);
        $result['overdue']    = array_sum($result['overdue']);
        $result['currency']   = $currency;
        $result['currencyid'] = $currencyid;

        return $result;
    }

    /**
     * Parse a package object.
     *
     * @param object $package The package object to parse.
     * @return object The parsed package object.
     */
    public function parse_package(object $package)
    {
        if (isset($package->metadata)) {
            $package->metadata = (object)json_decode($package->metadata);

            // Parse discount for client or js
            $formatted_discounts = [];
            $discounts = $package->metadata->discounts ?? null;
            if (!empty($discounts)) {
                foreach ($discounts->limits as $key => $limit) {
                    if (!isset($formatted_discounts[$limit]))
                        $formatted_discounts[$limit] = [];

                    $unit = (int)$discounts->units[$key];
                    $formatted_discounts[$limit][$unit] = [
                        'unit' => $discounts->units[$key],
                        'percent' => $discounts->percents[$key]
                    ];
                }
                $package->metadata->formatted_discounts = (object)$formatted_discounts;
            }
        }

        if (isset($package->db_pools)) {
            $package->db_pools = (array)json_decode($this->encryption->decrypt($package->db_pools));
        }
        if (isset($package->modules)) {
            $package->modules = (array)json_decode($package->modules);
        }

        return $package;
    }

    /**
     * Parse a company object.
     *
     * @param object $company The company object to parse.
     * @return object The parsed company object.
     */
    public function parse_company(object $company)
    {
        if (isset($company->metadata)) {
            $company->metadata = (object)json_decode($company->metadata);
        }

        if (!empty($company->dsn)) {
            $company->dsn = $this->encryption->decrypt($company->dsn);
        }

        return $company;
    }
}