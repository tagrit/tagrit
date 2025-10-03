<?php defined('BASEPATH') or exit('No direct script access allowed');

class Companies extends AdminController
{

    /**
     * @inheritDoc awokumadivad
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display the list of all companies.
     */
    function index()
    {
        // Check for permission
        if (!staff_can('view', 'perfex_saas_companies')) {
            return access_denied('perfex_saas_companies');
        }

        // Return the table data for ajax request
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(PERFEX_SAAS_MODULE_NAME, 'companies/table'));
        }

        // Show list of comapnies
        $data['companies'] = $this->perfex_saas_model->companies();
        $data['title'] = _l('perfex_saas_companies');
        $this->load->view('companies/manage', $data);
    }

    /**
     * Create a new company.
     */
    function create()
    {
        // Check permission to creat
        if (!staff_can('create', 'perfex_saas_companies')) {
            return access_denied('perfex_saas_companies');
        }

        // Save company data
        if ($this->input->post()) {

            // Perform validation and save the new company
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', _l('perfex_saas_name'), 'required');
            $this->form_validation->set_rules('clientid',  _l('perfex_saas_customer'), 'required');
            $this->form_validation->set_rules('db_scheme', _l('perfex_saas_db_scheme'), 'required');

            if ($this->form_validation->run() !== false) {

                $form_data = $this->input->post(NULL, true);

                try {

                    // Get the client invoice
                    $invoice  = $this->perfex_saas_model->get_company_invoice($form_data['clientid']);

                    // Require that the client the company is beign created for by admin has an invoice
                    if (!isset($invoice->db_scheme) || empty($invoice->db_scheme)) {
                        throw new \Exception(_l('perfex_saas_no_invoice_client'), 1);
                    }

                    $form_data['dsn'] = '';
                    $db_scheme = $form_data['db_scheme'];

                    // Use the invoice package db scheme
                    if ($db_scheme == 'package') {

                        $form_data['dsn'] = ''; // Dsn will be determined in model base on invoice package
                    }

                    if ($db_scheme == 'single' || $db_scheme == 'multitenancy') {

                        $invoice->db_scheme = $db_scheme; // make temporary update of the invoice scheme reflecting the selected scheme
                        $form_data['dsn'] = ''; // Dsn will be determined in model base on the invoice scheme
                    }

                    // Use provided custom database.
                    if ($db_scheme == 'shard') {

                        // Validate the provided custom db credentails
                        $validation = perfex_saas_is_valid_dsn($form_data['db_pools']);
                        $dsn_string = perfex_saas_dsn_to_string($form_data['db_pools']);

                        if ($validation !== true) {
                            // str replace was done to ensure text wrapping in notifications alert
                            throw new \Exception($validation . ' using dsn: ' . str_ireplace(';', "; ", $dsn_string), 1);
                        }

                        // Add the credential to dsn
                        $form_data['dsn'] = $dsn_string;
                    }

                    // Save only, deployment will be made as another job
                    $_id = $this->perfex_saas_model->create_or_update_company($form_data, $invoice);
                    if ($_id) {

                        set_alert('success', _l('added_successfully', _l('perfex_saas_company')));
                        return redirect(admin_url(PERFEX_SAAS_ROUTE_NAME . '/companies'));
                    }

                    // Log error
                    log_message('error', _l('perfex_saas_error_completing_action') . ':' . ($this->db->error() ?? ''));

                    throw new \Exception(_l('perfex_saas_error_completing_action'), 1);
                } catch (\Exception $e) {

                    set_alert('danger', $e->getMessage());
                    return perfex_saas_redirect_back();
                }
            }
        }

        // Show form to create a new company
        $data['title'] = _l('perfex_saas_companies');
        $this->load->view('companies/form', $data);
    }

    /**
     * Edit an existing company.
     *
     * @param string $id The ID of the company to edit
     */
    function edit($id)
    {
        if (!staff_can('edit', 'perfex_saas_companies')) {
            return access_denied('perfex_saas_companies');
        }

        if ($this->input->post()) {

            // Make some validation
            $this->load->library('form_validation');
            $this->form_validation->set_rules('id', _l('id'), 'required');
            $this->form_validation->set_rules('name', _l('perfex_saas_name'), 'required');
            $this->form_validation->set_rules('clientid',  _l('perfex_saas_customer'), 'required');

            if ($this->form_validation->run() !== false) {

                $form_data = $this->input->post(NULL, true);

                try {

                    // Get the client invoice
                    $invoice  = $this->perfex_saas_model->get_company_invoice($form_data['clientid']);

                    // Require that the client the company is beign created for by admin has an invoice
                    if (!isset($invoice->db_scheme) || empty($invoice->db_scheme)) {
                        throw new \Exception(_l('perfex_saas_no_invoice_client'), 1);
                    }

                    // Save and make deployment another job
                    $_id = $this->perfex_saas_model->create_or_update_company($form_data, $invoice);
                    if ($_id) {

                        set_alert('success', _l('updated_successfully', _l('perfex_saas_company')));
                        return redirect(admin_url(PERFEX_SAAS_ROUTE_NAME . '/companies'));
                    }

                    // Log error
                    log_message('error', _l('perfex_saas_error_completing_action') . ':' . ($this->db->error() ?? ''));

                    throw new \Exception(_l('perfex_saas_error_completing_action'), 1);
                } catch (\Throwable $th) {

                    set_alert('danger', $th->getMessage());
                    return perfex_saas_redirect_back();
                }
            }
        }

        // Show form to edit the new company
        $data['company'] = $this->perfex_saas_model->companies($id);
        $data['title'] = _l('perfex_saas_companies');
        $this->load->view('companies/form', $data);
    }

    function custom_domain()
    {
        if (!staff_can('edit', 'perfex_saas_companies')) {
            return access_denied('perfex_saas_companies');
        }

        if ($this->input->post()) {
            try {

                $form_data = $this->input->post(NULL, true);
                $id = $form_data['id'];
                $company = $this->perfex_saas_model->companies($id);
                $data = ['id' => $company->id, 'metadata' => ['pending_custom_domain' => '']];
                $custom_domain = $company->metadata->pending_custom_domain;
                $approve = !empty($form_data['approve']);
                if ($approve) {
                    $data['custom_domain'] = $custom_domain;
                }

                // Get the client invoice
                $invoice  = $this->perfex_saas_model->get_company_invoice($company->clientid);

                // Save and make deployment another job
                $_id = $this->perfex_saas_model->create_or_update_company($data, $invoice);
                if ($_id) {

                    perfex_saas_send_customdomain_request_notice($company, $custom_domain, $invoice, $approve ? 'approved' : 'rejected', ['extra_note' => $form_data['extra_note']]);

                    set_alert('success', _l('updated_successfully', _l('perfex_saas_custom_domain')));
                    return redirect(admin_url(PERFEX_SAAS_ROUTE_NAME . '/companies/edit/' . $_id));
                }

                // Log error
                log_message('error', _l('perfex_saas_error_completing_action') . ':' . ($this->db->error() ?? ''));

                throw new \Exception(_l('perfex_saas_error_completing_action'), 1);
            } catch (\Throwable $th) {

                set_alert('danger', $th->getMessage());
                return perfex_saas_redirect_back();
            }
        }
    }

    /**
     * Delete a company.
     *
     * @param string $id The ID of the company to delete
     * @return mixed Result of the deletion
     */
    function delete($id)
    {
        if (!staff_can('delete', 'perfex_saas_companies')) {
            return access_denied('perfex_saas_companies');
        }

        $id = (int)$id;
        $removed = $this->perfex_saas_model->delete_company($id);
        if ($removed)
            set_alert('success', _l('deleted', _l('perfex_saas_company')) . ($removed !== true ? ' ' . _l('perfex_saas_with_error') . ': ' . $removed : ''));
        else
            set_alert('danger', _l('perfex_saas_error_completing_action') . (is_string($removed) ? $removed : ''));

        return redirect(admin_url(PERFEX_SAAS_ROUTE_NAME . '/companies'));
    }

    /**
     * Update DSN of a company
     *
     * @return void
     */
    public function update_dsn()
    {
        if (!staff_can('edit', 'perfex_saas_companies')) {
            return access_denied('perfex_saas_companies');
        }

        try {
            $id = (int)$this->input->post('company_id', true);
            $dsn = $this->input->post('db_pools', false);

            if (empty($id) || empty($dsn)) throw new \Exception(_l('perfex_saas_error_completing_action'), 1);

            $data = ['id' => $id];

            // Validate the provided custom db credentails
            $validation = perfex_saas_is_valid_dsn($dsn);
            $dsn_string = perfex_saas_dsn_to_string($dsn);

            if ($validation !== true) {
                // str replace was done to ensure text wrapping in notifications alert
                throw new \Exception($validation . ' using dsn: ' . str_ireplace(';', "; ", $dsn_string), 1);
            }

            // Add the credential to dsn
            $data['dsn'] = $dsn_string;

            // Get the client invoice
            $company = $this->perfex_saas_model->companies($id);
            $invoice  = $this->perfex_saas_model->get_company_invoice($company->clientid);

            //Update
            $_id = $this->perfex_saas_model->create_or_update_company($data, $invoice);
            if (!$_id)
                throw new \Exception(_l('perfex_saas_error_completing_action'), 1);

            echo json_encode([
                'status' => 'success',
                'message' => _l('updated_successfully', _l('perfex_saas_company'))
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


    /**
     * Method to handle deploy service for a company instance (AJAX)
     *
     * @param string $company_id
     * @return void
     */
    public function deploy($company_id = '')
    {

        echo json_encode(perfex_saas_deployer($company_id));
        exit();
    }

    /**
     * Create a bridge for tenant admins to access the client portal and or other instance belonging to the same owner.
     *
     * This method allows tenant admins to access the client portal, providing a bridge
     * between the two roles. It generates a unique authentication code and redirects
     * the user to the client portal or target tenant url with the authentication code and redirect parameters.
     *
     * @return void
     */
    public function client_portal_bridge()
    {
        if (perfex_saas_is_tenant() && is_admin()) {
            $tenant = perfex_saas_tenant();
            $clientid = $tenant->clientid;
            if ($clientid) {
                $auth_code = perfex_saas_generate_magic_auth_code($clientid);
                $redirect = $this->input->get('redirect', true);
                $target = $this->input->get('target', true);

                if ($auth_code) {

                    $query = 'billing/my_account/magic_auth?auth_code=' . urlencode($auth_code);

                    if ($target === 'tenant') {
                        return redirect($redirect . $query);
                    }

                    // Prepare data for the client portal frame
                    $url = perfex_saas_default_base_url($query . '&redirect=' . $redirect . '&source_url=' . current_url());

                    // When cross domain not allow, and tenant using custom domain, open in new tab instead.
                    $support_custom_domain_magic_login = perfex_saas_tenant_is_enabled('cross_domain_bridge');
                    if ($tenant->http_identification_type === PERFEX_SAAS_TENANT_MODE_DOMAIN && !$support_custom_domain_magic_login) {
                        $url = $url . '&cross_domain=1';
                        return redirect($url);
                    }

                    $data = ['url' => $url];
                    return $this->load->view("client_portal_framer", $data);
                }
                perfex_saas_redirect_back();
            }
        }
    }
}
