<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This is a trait containing common function for managing tenant
 */
trait Tenant_trait
{
    /**
     * Common url to redirect to
     *
     * @var string
     */
    public $redirect_url = '';

    /**
     * Method to deploy a company instance
     * @param int $clientid (optional) The client id
     * @return array
     */
    public function deployTenants(?int $clientid)
    {
        // Checking status
        $is_status_check = (int)$this->input->get('status');
        if ($is_status_check) {

            $this->db->where('status', 'deploying');
            $companies = $this->perfex_saas_model->companies();
            $slug = empty($companies) ? '' : $companies[0]->slug;
            $status = perfex_saas_get_deploy_step($slug);
            return ['status' => array_unique($status)];
        }

        // Close session lock to ensure pooling for status is not blocked
        session_write_close();

        // Trigger deploy
        return perfex_saas_deployer('', $clientid);
    }


    /**
     * Method to subscribe to a package.
     * It assign the package to user and generate and invoice using perfex invoicing system.
     *
     * @param int $clientid
     * @param string $packageslug
     * @return array
     */
    public function subscribeToPackage(int $clientid, string $packageslug)
    {
        try {

            $invoice = null;
            $package = $this->perfex_saas_model->get_entity_by_slug('packages', $packageslug);
            if (!empty($package->id ?? ''))
                $invoice = (object)$this->perfex_saas_model->generate_company_invoice($clientid, $package->id);

            if (isset($invoice->action_url))
                return array_merge(['redirect' => $invoice->action_url], (array)$invoice);

            // Ensure we have the invoice created
            if (!$invoice) {
                return [
                    'error' => _l('perfex_saas_error_creating_invoice'),
                    'redirect' => base_url('clients/?subscription')
                ];
            }

            $this->db->where('clientid', $clientid);
            $companies = $this->perfex_saas_model->companies();

            if (empty($companies)) {

                if (get_option('perfex_saas_autocreate_first_company') == '1') {

                    // Create defalt company for the client
                    $company_name = get_client($clientid)->company;
                    $data = [
                        'name' => empty($company_name) ? 'Company#1' : $company_name,
                        'clientid' => $clientid
                    ];

                    // Add custom domain and subdomain from session if any
                    $data = hooks()->apply_filters('perfex_saas_create_instance_data', $data);

                    $_id = $this->perfex_saas_model->create_or_update_company($data, $invoice);

                    $custom_domain = $data['custom_domain'] ?? '';

                    // Notify supper admin on custom domain if needed
                    if (!empty($custom_domain)) {
                        $company = $this->perfex_saas_model->companies($_id);
                        perfex_saas_send_customdomain_request_notice($company, $custom_domain, $invoice);
                    }

                    hooks()->do_action('perfex_saas_after_client_create_instance', $_id);
                }
            }

            $message = '';
            $redirect = base_url('clients?companies');
            if (!in_array($invoice->status, [Invoices_model::STATUS_PAID]) && !perfex_saas_invoice_is_on_trial($invoice))
                $redirect = base_url(perfex_saas_get_invoice_payment_endpoint($invoice));

            if (!empty($companies))
                $message = _l('added_successfully', _l('invoice'));

            return [
                'success' => $message,
                'redirect' => $redirect,
                'invoice' => $invoice,
                'package' => $package
            ];
        } catch (\Throwable $th) {
            return [
                'error' => $th->getMessage(),
                'redirect' => base_url('clients/?subscription')
            ];
        }
    }

    /**
     * Method to update subscription
     * It handle the custom limit and package customization feature.
     *
     * @param int $clientid
     * @param object $invoice
     * @param object $package
     * @return array
     */
    public function updateSubscription(int $clientid, $invoice = null, $package = null)
    {
        // Check if the client has a subscription i.e invoice
        if (!$invoice)
            $invoice = $this->perfex_saas_model->get_company_invoice($clientid);

        // Check if on trial, cancel trial and subscribe
        if (isset($invoice->on_trial) && $invoice->on_trial) {
            $invoice = (object)$this->perfex_saas_model->generate_company_invoice($clientid, $invoice->trial_package_id);
        }

        if (isset($invoice->action_url)) {
            return ['redirect' => $invoice->action_url];
        }

        if (empty($invoice->db_scheme)) {
            return [
                'error' => _l('perfex_saas_no_invoice_client_for_client'),
                'redirect' => base_url('clients/?subscription')
            ];
        }

        if (!$package)
            $package = $this->perfex_saas_model->packages($invoice->{perfex_saas_column('packageid')});

        if (!perfex_saas_is_single_package_mode() && ($package->metadata->allow_customization ?? '') === 'no') {
            return [
                'error' => _l('perfex_saas_permission_denied'),
                'redirect' => base_url('clients/?subscription')
            ];
        }

        $post_data = $this->getPostData(null, true);
        if (empty($post_data) && $this->session->has_userdata('cached_post_data')) {
            $post_data = $this->session->userdata('cached_post_data');
            $this->session->unset_userdata('cached_post_data');
        }

        // Save and update invoice
        if (empty($post_data)) {
            if (strtoupper($this->input->method()) !== 'POST') {
                return [
                    'invoice' => $invoice,
                    'package' => $package
                ];
            }
        }


        try {

            // Regenerate invoice if on trial. Cancel trial and generate active invoice
            $on_trial = perfex_saas_invoice_is_on_trial($invoice);
            if ($on_trial)
                $invoice = $this->perfex_saas_model->generate_company_invoice($clientid, $package->id);

            if (isset($invoice->action_url)) {
                return ['redirect' => $invoice->action_url];
            }

            $data_source = [
                'purchased_modules' => (array)($post_data['purchased_modules'] ?? []),
                'purchased_services' => (array) ($post_data['purchased_services'] ?? []),
                // Get existing limits and merge
                'custom_limits' => array_merge((array)$invoice->custom_limits, (array)($post_data['custom_limits'] ?? []))
            ];

            $allow_module_marketplace = ($package->metadata->disable_module_marketplace ?? '') !== 'yes';
            if (!$allow_module_marketplace)
                $data_source['purchased_modules'] = (array)($invoice->purchased_modules ?? []);

            $allow_service_marketplace = ($package->metadata->disable_service_marketplace ?? '') !== 'yes';
            if (!$allow_service_marketplace)
                $data_source['purchased_services'] = (array)($invoice->purchased_services ?? []);

            /** @var Perfex_saas_custom_limit $customization */
            $customization = $this->perfex_saas_model->prepare_invoice_customization_items($package, $data_source);
            $custom_limits = $customization->custom_limits;
            $purchased_modules = $customization->purchased_modules;
            $purchased_services = $customization->purchased_services;

            // Get client metadata
            $client_metadata = perfex_saas_get_or_save_client_metadata($clientid);


            /**
             * Clean one time purchased invoices off cancelled invoices
             * This ensure cancled service is removed and can be repurchased at customer will
             */
            try {
                $_client_metadata_dirty_keys = [];
                foreach (['service', 'module'] as $_res) {

                    $purchased_key = 'purchased_' . $_res . 's'; // i.e purchased_services
                    $onetime_invoices_key = 'onetime_purchased_' . $_res . '_invoice'; // i.e onetime_purchased_service_invoice

                    $onetime_purchased_invoices = (object)($client_metadata[$onetime_invoices_key] ?? new stdClass);

                    foreach ($client_metadata[$purchased_key] as $index => $value) {
                        if (!empty($invoice_id = $onetime_purchased_invoices->{$value} ?? '')) {
                            $_item_invoice = $this->invoices_model->get($invoice_id);
                            if ($_item_invoice->status == Invoices_model::STATUS_CANCELLED) {
                                unset($client_metadata[$purchased_key][$index]);
                                unset($client_metadata[$onetime_invoices_key]->{$value});
                                $_client_metadata_dirty_keys[] = $purchased_key;
                                $_client_metadata_dirty_keys[] = $onetime_invoices_key;
                            }
                        }
                    }
                }
                if (!empty($_client_metadata_dirty_keys)) {
                    $_client_metadata_update = array_intersect_key($client_metadata, array_flip(array_values($_client_metadata_dirty_keys)));
                    $client_metadata = perfex_saas_get_or_save_client_metadata($clientid, $_client_metadata_update);
                }
            } catch (\Throwable $th) {
                log_message('error', 'saas:clean-invoice:error:' . $th->getMessage());
            }


            /**
             * Check if form has customization (i.e there is an update)
             * Otherwise return
             */
            $new_customizations = ['custom_limits' => $custom_limits, 'purchased_modules' => $purchased_modules, 'purchased_services' => $purchased_services];
            $old_customizations = array_intersect_key($client_metadata, array_flip(array_keys($new_customizations)));
            $old_customizations = json_decode(json_encode($old_customizations), true);
            $has_new_customization = perfex_saas_arrays_are_different($new_customizations, $old_customizations);

            if (!$has_new_customization) {
                return [
                    'success' => '',
                    'redirect' => uri_string(),
                    'invoice' => $invoice,
                    'package' => $package
                ];
            }

            /**
             * This allow those in Germany for instance where a paid invoice can not be updated use deferred future prorate invoice
             * with support for customization. So customer customization or changes to the invoice are kept in draft/unpaid
             * until the next recurring subscription is created 
             */
            $deferred_billing_status = get_option('perfex_saas_deferred_billing_status');
            if (!empty($deferred_billing_status) && empty($invoice->subscription_id) && in_array($invoice->status, [Invoices_model::STATUS_PAID])) {
                // Detach the current invoice
                $this->perfex_saas_model->cancel_company_invoice($clientid);

                // Regenerate a new invoice
                $extra_custom_data = ['status' => $deferred_billing_status];

                // Add duedate to especially track draft and convert to proper invoice
                $re_create_at = perfex_saas_get_recurring_invoice_next_date($invoice);
                if ($re_create_at !== false)
                    $extra_custom_data['duedate'] = $re_create_at;

                $invoice = $this->perfex_saas_model->generate_company_invoice($clientid, $package->id, $extra_custom_data);

                if (isset($invoice->action_url)) {
                    return ['redirect' => $invoice->action_url];
                }
            }

            // Update the invoice
            $update = $this->perfex_saas_model->update_company_invoice($invoice, $clientid, $customization);
            if ($update) {

                if (isset($update->action_url)) {
                    $this->session->set_userdata('cached_post_data', $post_data);
                    return ['redirect' => $update->action_url];
                }

                // Save new custom limit to DB
                $data = ['custom_limits' => $custom_limits, 'purchased_modules' => $purchased_modules, 'purchased_services' => $purchased_services];
                $metadata = perfex_saas_get_or_save_client_metadata($clientid, $data);
                if (!empty($metadata)) {
                    $invoice = $this->perfex_saas_model->get_company_invoice($clientid);
                }

                $redirect = current_url();
                if (!in_array($invoice->status, [Invoices_model::STATUS_PAID, Invoices_model::STATUS_DRAFT]) && !perfex_saas_invoice_is_on_trial($invoice))
                    $redirect = base_url(perfex_saas_get_invoice_payment_endpoint($invoice));

                return [
                    'success' => _l('updated_successfully', _l('perfex_saas_subscription')),
                    'redirect' => $redirect,
                    'invoice' => $invoice,
                    'package' => $package
                ];
            }

            // Log error
            log_message('error', _l('perfex_saas_error_completing_action') . ':' . ($this->db->error() ?? ''));
            throw new \Exception(_l('perfex_saas_error_completing_action'), 1);
        } catch (\Throwable $th) {
            return [
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * Cancel the client subscription
     *
     * @param integer $clientid
     * @return array
     */
    public function cancelSubscription(int $clientid)
    {
        try {
            $type    = $this->input->get('type');
            $invoice = $this->perfex_saas_model->cancel_company_invoice($clientid, $type);

            if ($invoice) {

                $package = $this->perfex_saas_model->packages($invoice->{perfex_saas_column('packageid')});

                return [
                    'success' => _l('perfex_saas_pricing_cancelled'),
                    'redirect' => '',
                    'invoice' => $invoice,
                    'package' => $package
                ];
            }

            throw new \Exception(_l('perfex_saas_invoice_cancel_error'), 1);
        } catch (\Throwable $th) {

            return [
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * Resume a client canceled subscription.
     *
     * @param integer $clientid
     * @return array
     */
    public function resumeSubscription(int $clientid)
    {
        try {
            // Check if the client has a subscription i.e invoice
            $invoice = $this->perfex_saas_model->resume_company_invoice($clientid);

            if ($invoice) {

                $package = $this->perfex_saas_model->packages($invoice->{perfex_saas_column('packageid')});

                return [
                    'success' => _l('updated_successfully', _l('perfex_saas_subscription')),
                    'redirect' => '',
                    'invoice' => $invoice,
                    'package' => $package
                ];
            }

            throw new \Exception(_l('perfex_saas_no_resumable_invoice'), 1);
        } catch (\Throwable $th) {

            return [
                'error' => $th->getMessage()
            ];
        }
    }


    /**
     * Method to validate the client's invoice.
     *
     * @param int $clientid
     * @return array Array (with error index when failed) .
     */
    private function validateClientInvoice(int $clientid)
    {
        $invoice = $this->perfex_saas_model->get_company_invoice($clientid);

        if (empty($invoice->db_scheme)) {
            return [
                'error' => _l('perfex_saas_no_invoice_client_for_client')
            ];
        }

        $on_trial = perfex_saas_invoice_is_on_trial($invoice);
        $days_left = $on_trial ? (int) perfex_saas_get_days_until($invoice->duedate) : '';
        if ($on_trial && $days_left < 1) {
            return [
                'error' => _l('perfex_saas_trial_invoice_over_not')
            ];
        }

        if (!$on_trial && perfex_saas_is_invoice_overdue_for_payment($invoice)) {
            return [
                'error' => _l('perfex_saas_clear_unpaid_invoice_note'),
                'redirect' => base_url(perfex_saas_get_invoice_payment_endpoint($invoice))
            ];
        }

        return [
            'invoice' => $invoice,
            'success' => ''
        ];
    }


    /**
     * Common method to handle create or edit form submission.
     * Client company form validation and execution are summarized in this method.
     *
     * @param int $clientid ID of the client
     * @param int $tenantid ID of the company to edit (optional)
     * @return array
     */
    private function createOrUpdateCompany(int $clientid, $tenantid = '')
    {
        // Check if the client has a subscription i.e invoice and it's not unpaid
        $validate = $this->validateClientInvoice($clientid);
        if (!isset($validate['invoice']) || isset($validate['error'])) {
            return [
                'error' => $validate['error'] ?? '',
                'redirect' => $validate['redirect'] ?? $this->redirect_url
            ];
        }
        $invoice = $validate['invoice'];

        // Company form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', _l('perfex_saas_name'), 'required');
        if ($this->form_validation->run() === false) {
            return [
                'error' => validation_errors(),
                'redirect' => $this->redirect_url
            ];
        }

        try {
            $form_data = $this->getPostData(NULL, true);

            $data = ['name' => $form_data['name']];
            $data['clientid'] = $clientid;

            $data['custom_domain'] = $form_data['custom_domain'] ?? '';
            $custom_domain = $data['custom_domain'];

            // Add disabled modules
            $disabled_modules = (array)($form_data['disabled_modules'] ?? []);
            $data['metadata'] = ['disabled_modules' => $disabled_modules];

            if (!empty($tenantid)) {
                $data['id'] = $tenantid;
            } else {
                // Creating new
                $data['slug'] = $form_data['slug'] ?? '';
            }

            // save to db
            $_id = $this->perfex_saas_model->create_or_update_company($data, $invoice);
            if ($_id) {

                // Notify supper admin on domain update
                if (!empty($custom_domain)) {
                    $company = $this->perfex_saas_model->companies($_id);
                    perfex_saas_send_customdomain_request_notice($company, $custom_domain, $invoice);
                }

                // creating new company
                if (empty($tenantid))
                    hooks()->do_action('perfex_saas_after_client_create_instance', $_id);

                $message = _l(empty($tenantid) ? 'added_successfully' : 'updated_successfully', _l('perfex_saas_company'));
                return [
                    'success' => $message,
                    'redirect' => $this->redirect_url
                ];
            }

            // Log error
            log_message('error', _l('perfex_saas_error_completing_action') . ':' . ($this->db->error() ?? ''));

            throw new \Exception(_l('perfex_saas_error_completing_action'), 1);
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'redirect' => $this->redirect_url
            ];
        }
    }

    /**
     * Method to get post data with support for application/json in api endpoints
     *
     * @param string|null $index
     * @param mixed $xss_clean
     * @return mixed
     */
    private function getPostData($index = null, $xss_clean = null)
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            return null;
        }

        $data = $this->input->post($index, $xss_clean);

        // Use stream for api endpoints
        if (
            empty($data) &&
            defined('PERFEX_SAAS_API') &&
            isset($_SERVER['CONTENT_TYPE']) &&
            $_SERVER['CONTENT_TYPE'] == 'application/json'
        ) {
            $data = $this->input->raw_input_stream;
            if ($xss_clean) {
                $data = $this->security->xss_clean($data);
            }
            $data = json_decode($data, true);
            if ($index)
                $data = $data[$index] ?? null;
        }
        return $data;
    }
}