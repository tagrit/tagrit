<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Packages extends AdminController
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        // Load essential models and libraries
        $this->load->model('currencies_model');
        $this->load->model('invoices_model');
        $this->load->model('payment_modes_model');
    }

    /**
     * Display the list of packages
     *
     * @return void
     */
    public function index()
    {

        if (!staff_can('view', 'perfex_saas_packages')) {
            return access_denied('perfex_saas_packages');
        }

        $data['title'] = _l('perfex_saas_packages');
        $data['packages'] = $this->perfex_saas_model->packages();
        $this->load->view('packages/manage', $data);
    }

    public function pricing()
    {
        if (!staff_can('edit', 'perfex_saas_packages')) {
            return access_denied('perfex_saas_packages');
        }

        $id = '';

        $default_package = $this->perfex_saas_model->default_package();
        if (!$default_package) {

            $this->db->where('is_private !=', 1);
            $packages = $this->perfex_saas_model->packages();
            if (!empty($packages[0])) {
                $this->perfex_saas_model->mark_package_as_default($packages[0]->id);
                $default_package = $this->perfex_saas_model->default_package();
            }
        }

        if ($default_package) $id = $default_package->id;

        if ($this->input->post()) {
            // Handle package update
            $this->create_or_edit_package($id);
        }

        // Load package data and display edit form
        $data['title'] = _l('perfex_saas_packages');
        $data['staff']     = $this->staff_model->get('', ['active' => 1]);

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $data['all_payment_modes'] = $this->payment_modes_model->get();

        if ($default_package)
            $data['package'] = $default_package;

        $this->load->view('packages/form', $data);
    }

    /**
     * Create a new package
     *
     * @return void
     */
    public function create()
    {

        if (!staff_can('create', 'perfex_saas_packages')) {
            return access_denied('perfex_saas_packages');
        }

        if ($this->input->post()) {
            // Handle package create
            $this->create_or_edit_package();
        }

        // Display package create form
        $data['title'] = _l('perfex_saas_packages');
        $data['staff']     = $this->staff_model->get('', ['active' => 1]);

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $data['all_payment_modes'] = $this->payment_modes_model->get();

        $this->load->view('packages/form', $data);
    }

    /**
     * Edit an existing package
     *
     * @param string $id Package ID
     * @return void
     */
    public function edit($id)
    {
        if (!staff_can('edit', 'perfex_saas_packages')) {
            return access_denied('perfex_saas_packages');
        }

        if ($this->input->post()) {
            // Handle package update
            $this->create_or_edit_package($id);
        }

        // Load package data and display edit form
        $data['title'] = _l('perfex_saas_packages');
        $data['package'] = $this->perfex_saas_model->packages($id);
        $data['staff']     = $this->staff_model->get('', ['active' => 1]);

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $data['all_payment_modes'] = $this->payment_modes_model->get();

        $this->load->view('packages/form', $data);
    }

    /**
     * Handle creating or editing of package.
     * This method is used since both create and package has very little variation in logic. DRY
     *
     * @param string $id Edit Package ID (optional)
     * @return void
     * @throws Exception Can throw DB validation/connection error and others
     */
    private function create_or_edit_package($id = '')
    {

        // Validate request
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', _l('perfex_saas_name'), 'required');
        $this->form_validation->set_rules('price', _l('perfex_saas_price'), 'required');
        if ($this->form_validation->run() == false) {
            return;
        }

        $package = null;
        if (!empty($id)) {
            $package = $this->perfex_saas_model->packages((int)$id);
        }

        $form_data = $this->input->post(NULL, true);

        $form_data['price'] = (float)$form_data['price'];
        $form_data['metadata'] = isset($form_data['metadata']) ? (array)$form_data['metadata'] : [];
        $form_data['modules'] = json_encode(isset($form_data['modules']) ? (array)$form_data['modules'] : []);
        $form_data['description'] = html_purify($form_data['description']);

        // Slug management
        $slug = isset($form_data['slug']) && !empty($form_data['slug']) ? $form_data['slug'] : $form_data['name'];
        $slug_delimiter = stripos($slug, '_') === false ? '-' : '_'; // Backward compact to support old packages already using underscore
        $slug = perfex_saas_generate_unique_slug($slug, 'packages', $id, 0, ['skip_table_compact' => true, 'max_length' => 150, 'delimiter' => $slug_delimiter]);
        $form_data['slug'] = $slug;

        // Check boxes
        $form_data['is_private'] = $this->input->post('is_private', true) ?? '0';
        $form_data['is_default'] = $this->input->post('is_default', true) ?? '0';
        $form_data['status'] = $this->input->post('status', true) ?? '0';

        // Domain check boxes
        if (!isset($form_data['metadata']['enable_subdomain']))
            $form_data['metadata']['enable_subdomain'] = '';
        if (!isset($form_data['metadata']['enable_custom_domain']))
            $form_data['metadata']['enable_custom_domain'] = '';
        if (!isset($form_data['metadata']['enable_custom_domain']))
            $form_data['metadata']['autoapprove_custom_domain'] = '';
        if (!isset($form_data['metadata']['is_liftetime_deal']))
            $form_data['metadata']['is_liftetime_deal'] = '';

        $db_scheme = $form_data['db_scheme'];
        $_db_pools = [];

        try {

            // Ensure private package is not marked as default.
            if (isset($form_data['is_private']) && isset($form_data['is_default']) && $form_data['is_private'] == '1' && $form_data['is_default'] == '1')
                throw new \Exception(_l('perfex_saas_no_private_default_package'), 1);

            $pools = $form_data['db_pools'];
            // Emty the db_pool variable
            $form_data['db_pools'] = '';

            // Handle the provided database pool.
            if (!in_array($db_scheme, ['multitenancy', 'single'])) {

                $db_pools_string = [];
                $fit_pool_list = [];

                if (!empty($pools)) {

                    // Sort and filter the pools to ensure uniqueness
                    for ($i = 0; $i < count($pools['host']); $i++) {
                        if (!empty($pools['host'][$i]) && !empty($pools['user'][$i]) && !empty($pools['dbname'][$i])) {

                            $pool =  [
                                'host' => $pools['host'][$i],
                                'user' => $pools['user'][$i],
                                'password' => $pools['password'][$i],
                                'dbname' => $pools['dbname'][$i],
                            ];

                            $dsn_string = perfex_saas_dsn_to_string($pool);

                            if (!in_array($dsn_string, $db_pools_string)) {
                                $db_pools_string[] = $dsn_string;
                                $_db_pools[] = $pool;
                            }
                        }
                    }

                    // Loop through the unique pools and test each db credentials
                    for ($j = 0; $j < count($_db_pools); $j++) {

                        $pool = $_db_pools[$j];

                        //test the db connection
                        $valid = perfex_saas_is_valid_dsn($pool);
                        if ($valid !== true) {

                            throw new \Exception("Connection Error: $valid", 1);
                        }

                        $fit_pool_list[] = $pool;
                    }
                }

                // All fine, encrypt all provided dbs
                $form_data['db_pools'] = $this->encryption->encrypt(json_encode($fit_pool_list));
            }

            $metadata = array_merge((array)$package->metadata, $form_data['metadata']);
            $form_data['metadata'] = json_encode($metadata);

            // Create or update the package
            $_id = $this->perfex_saas_model->add_or_update('packages', $form_data);
            if ($_id) {

                if ($form_data['is_default'] == '1')
                    $this->perfex_saas_model->mark_package_as_default($_id);

                $single_pricing_mode = perfex_saas_is_single_package_mode();

                // Update package invoices to reflect payment modes
                if (!empty($id) && !empty($package)) {

                    // Get the latest
                    $package = $this->perfex_saas_model->packages((int)$id);

                    $new_payment_modes = serialize($metadata['invoice']['allowed_payment_modes']);
                    $payment_modes = serialize($package->metadata->invoice->allowed_payment_modes);

                    // Update relevant invoice payment methods
                    $this->invoices_model->db
                        ->where(perfex_saas_column('packageid'), $id)
                        ->where('allowed_payment_modes', $payment_modes) // We only want to update invoice which intact package payment modes
                        ->where("`status` != '" . Invoices_model::STATUS_CANCELLED . "'")
                        ->update('invoices', ['allowed_payment_modes' => $new_payment_modes]);

                    perfex_saas_trigger_cron_process(PERFEX_SAAS_CRON_PROCESS_PACKAGE, $_id);

                    hooks()->do_action('perfex_saas_after_package_update', $package);
                }

                set_alert('success', _l(empty($id) ? 'added_successfully' : 'updated_successfully', _l($single_pricing_mode ? 'perfex_saas_pricing' : 'perfex_saas_package')));
                return redirect($single_pricing_mode ? uri_string() : admin_url(PERFEX_SAAS_ROUTE_NAME . '/packages/edit/' . $_id));
            }
        } catch (\Exception $e) {

            $this->session->set_flashdata('db_pools', $_db_pools);
            set_alert('danger', $e->getMessage());

            return perfex_saas_redirect_back();
        }
    }

    /**
     * Clone a package
     *
     * @param string $id Package ID
     * @return void
     */
    public function clone($id)
    {
        if (!staff_can('create', 'perfex_saas_packages')) {
            return access_denied('perfex_saas_packages');
        }

        if (!empty($id)) {
            $clone_id = $this->perfex_saas_model->clone('packages', (int)$id);

            hooks()->do_action('perfex_saas_after_package_clone', ['id' => $id, 'new_clone_id' => $clone_id]);
        }

        return redirect(admin_url(PERFEX_SAAS_ROUTE_NAME . '/packages'));
    }

    /**
     * Delete a package
     *
     * @return void
     */
    public function delete()
    {

        if (!staff_can('delete', 'perfex_saas_packages')) {
            return access_denied('perfex_saas_packages');
        }

        $id = (int)$this->input->post('id', true);

        if (!empty($id)) {

            // Check for invoices attached to the plan
            $this->invoices_model->db->limit(1);
            $invoices = $this->invoices_model->get('', [perfex_saas_column('packageid') => $id]);
            if (!empty($invoices)) {

                set_alert('danger', _l('perfex_saas_can_not_delete_package_with_invoices'));
                return redirect(admin_url(PERFEX_SAAS_ROUTE_NAME . '/packages'));
            }

            if ($this->perfex_saas_model->delete('packages', $id))
                set_alert('success', _l('deleted', _l('perfex_saas_package')));
        }

        return redirect(admin_url(PERFEX_SAAS_ROUTE_NAME . '/packages'));
    }

    /**
     * Method to add a company/client to a saas package by admin
     *
     * @return void
     */
    public function add_user_to_package()
    {
        if ($this->input->post()) {
            // Perform edit and add new
            $this->load->library('form_validation');

            // Set validation rules
            $this->form_validation->set_rules('clientid', _l('perfex_saas_customer'), 'required');
            $this->form_validation->set_rules('packageid', _l('perfex_saas_package'), 'required');

            if ($this->form_validation->run() !== false) {
                $packageid = $this->input->post('packageid', true);
                $clientid = $this->input->post('clientid', true);

                try {
                    // Generate company invoice
                    $invoice = $this->perfex_saas_model->generate_company_invoice($clientid, $packageid);
                    if (isset($invoice->action_url))
                        return redirect($invoice->action_url);

                    if (!empty($invoice)) {
                        set_alert('success', _l('added_successfully', _l('perfex_saas_customer')));

                        if (empty($invoice->is_mock))
                            return redirect(admin_url('invoices/list_invoices/' . $invoice->id));
                    }
                } catch (\Throwable $th) {
                    set_alert('danger', $th->getMessage());
                }
            }
        }

        // Redirect back if no post data or validation failed
        return redirect(admin_url(PERFEX_SAAS_ROUTE_NAME . '/packages'));
    }

    public function test_db()
    {
        $pool = $this->input->post('db_pools');

        try {
            if (empty($pool)) {
                throw new \Exception(_l('perfex_saas_empty_data'), 1);
            }

            $pool =  [
                'host' => $pool['host'],
                'user' => $pool['user'],
                'password' => $pool['password'],
                'dbname' => $pool['dbname'],
            ];

            //test the db connection
            $valid = perfex_saas_is_valid_dsn($pool);
            if ($valid !== true) {

                throw new \Exception("Connection Error: $valid", 1);
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Valid'
            ]);
            exit;
        } catch (\Throwable $th) {

            echo json_encode([
                'status' => 'danger',
                'message' => $th->getMessage()
            ]);
            exit;
        }
    }
}