<?php defined('BASEPATH') or exit('No direct script access allowed');

require_once 'Tenant_trait.php';

/**
 * This is a client class for managing instances and subscribing to a package.
 */
class Perfex_saas_client extends ClientsController
{
    use Tenant_trait;

    /**
     * Common url to redirect to
     *
     * @var string
     */
    public $redirect_url = '';

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        // Load essensial models
        $this->load->model('payment_modes_model');
        $this->load->model('invoices_model');
        $this->load->model('currencies_model');

        $this->redirect_url = base_url('clients?companies');

        if (!is_client_logged_in()) {
            return redirect($this->redirect_url);
        }

        if (!perfex_saas_client_can_use_saas()) {
            return redirect(base_url('clients'));
        }
    }

    /**
     * Method to create a company instance
     *
     * @return void
     */
    public function create()
    {
        if (!perfex_saas_contact_can_manage_instances()) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        if (!$this->input->post()) {
            return show_404();
        }

        return $this->create_or_edit_company();
    }

    /**
     * Method to handle company editing
     *
     * @param string $slug
     * @return void
     */
    public function edit($slug)
    {
        if (!perfex_saas_contact_can_manage_instances()) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        if (!$this->input->post()) {
            return show_404();
        }

        $id = $this->get_auth_company_by_slug($slug)->id;
        return $this->create_or_edit_company($id);
    }

    /**
     * Method to deploy a company instance (AJAX)
     *
     * @return void
     */
    public function deploy()
    {
        $clientid = (int)get_client_user_id();
        echo json_encode($this->deployTenants($clientid));
        exit();
    }

    /**
     * Method to delete a company instance
     *
     * @param string $slug
     * @return void
     */
    public function delete($slug)
    {
        if ($this->input->post() && perfex_saas_contact_can_manage_instances()) {
            $company = $this->get_auth_company_by_slug($slug);
            $id = (int)$company->id;
            if ($id) {
                $removed = $this->perfex_saas_model->delete_company($id);
                if ($removed) {
                    set_alert('success', _l('deleted', _l('perfex_saas_company')) . ($removed !== true ? ' ' . _l('perfex_saas_with_error') . ': ' . $removed : ''));
                } else {
                    set_alert('danger', _l('perfex_saas_error_completing_action') . (is_string($removed) ? ': ' . $removed : ''));
                }
            }
        }
        return redirect($this->redirect_url);
    }

    /**
     * Method to subscribe to a package.
     * It assign the package to user and generate and invoice using perfex invoicing system.
     *
     * @param string $packageslug
     * @return void
     */
    public function subscribe($packageslug)
    {
        if (!perfex_saas_contact_can_manage_subscription()) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $clientid = (int)get_client_user_id();
        $service = $this->subscribeToPackage($clientid, $packageslug);
        $status = isset($service['error']) ? 'danger' : 'success';
        $message = $service['error'] ?? $service['success'] ?? '';
        $redirect_url = empty($service['redirect'] ?? '') ? $this->redirect_url : $service['redirect'];
        if (!empty($message)) {
            set_alert($status, $message);
        }

        return $this->client_redirect($redirect_url);
    }

    /**
     * Method to subscribe to a package.
     * It assign the package to user and generate and invoice using perfex invoicing system.
     *
     * @param string $packageslug
     * @return void
     */
    public function my_account()
    {
        if (!perfex_saas_contact_can_manage_subscription()) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $clientid = (int)get_client_user_id();

        // Check if the client has a subscription i.e invoice
        $invoice = $this->perfex_saas_model->get_company_invoice($clientid);
        if (!$invoice) {
            set_alert('warning', _l('perfex_saas_no_invoice_client_for_client'));
            return $this->client_redirect(site_url());
        }

        $package = $this->perfex_saas_model->packages($invoice->{perfex_saas_column('packageid')});

        // Process when invoice not set or on every post request.
        if (!empty($this->input->post())) {

            // Save and update invoice
            $service = $this->updateSubscription($clientid, $invoice, $package);

            $status = isset($service['error']) ? 'danger' : 'success';
            $message = $service['error'] ?? $service['success'] ?? '';
            $redirect_url = empty($service['redirect'] ?? '') ? $this->redirect_url : $service['redirect'];
            if (!empty($message)) {
                set_alert($status, $message);
            }
            return $this->client_redirect($redirect_url);
        }

        $data['title'] = _l('perfex_saas_pricing');
        $data['package'] = $package;
        $data['invoice'] = $invoice;
        $this->data($data);
        $this->view('client/my_account');
        $this->layout();
    }

    /**
     * Cancel client subscription
     *
     * @return void
     */
    public function cancel_saas_subscription()
    {
        if (!perfex_saas_contact_can_manage_subscription()) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        if (!(int)get_option('perfex_saas_allow_customer_cancel_subscription')) {
            return redirect($this->redirect_url);
        }

        $clientid = get_client_user_id();
        $service = $this->cancelSubscription($clientid);
        $status = isset($service['error']) ? 'danger' : 'success';
        $message = $service['error'] ?? $service['success'] ?? '';
        if (!empty($message)) {
            set_alert($status, $message);
        }

        // Use defautl base url to ensure client is redirected back to super portal.
        $redirect = perfex_saas_default_base_url('clients');
        if ($this->session->has_userdata('magic_auth'))
            $redirect .= '?portal-message=home';
        return redirect($redirect);
    }

    /**
     * Resume subscription by resubscribing when possible.
     *
     * @return void
     */
    public function resume_saas_subscription()
    {
        if (!perfex_saas_contact_can_manage_subscription()) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $redirect_url = base_url('clients/' . perfex_saas_is_single_package_mode() ? 'my_account' : '?subscription');

        if (!(int)get_option('perfex_saas_allow_customer_cancel_subscription')) {
            return redirect($redirect_url);
        }

        $clientid = get_client_user_id();
        $service = $this->resumeSubscription($clientid);
        $status = isset($service['error']) ? 'danger' : 'success';
        $message = $service['error'] ?? $service['success'] ?? '';
        if (!empty($message)) {
            set_alert($status, $message);
        }
        return redirect($redirect_url);
    }

    /**
     * Method to get a company by slug and ensure it belongs to the logged-in client.
     * Will redirect if failed.
     *
     * @param string $slug The slug of the company.
     * @return mixed The company object if found and authorized, or void otherwise.
     */
    private function get_auth_company_by_slug($slug)
    {
        $clientid = get_client_user_id();

        // Get company and validate
        $company = $this->perfex_saas_model->get_company_by_slug($slug, $clientid);

        if (empty($company)) {
            redirect($this->redirect_url);
        }

        if ($clientid != $company->clientid) {
            return access_denied('perfex_saas_companies');
        }

        return $company;
    }


    /**
     * Common method to handle create or edit form submission.
     * Client company form validation and execution are summarized in this method.
     *
     * @param string $id ID of the company to edit (optional)
     * @return void
     */
    private function create_or_edit_company($id = '')
    {
        $clientid = (int)get_client_user_id();

        $service = $this->createOrUpdateCompany($clientid, $id);
        $status = isset($service['error']) ? 'danger' : 'success';
        $message = $service['error'] ?? $service['success'] ?? '';
        $redirect_url = empty($service['redirect'] ?? '') ? $this->redirect_url : $service['redirect'];
        if (!empty($message)) {
            set_alert($status, $message);
        }
        return $this->client_redirect($redirect_url);
    }

    private function client_redirect($redirect_url)
    {
        // Check for third party redirection when in framer i.e single portal mode and use client for redirection
        if ($this->session->has_userdata('magic_auth') && stripos($redirect_url, parse_url($this->redirect_url)['host']) === false) {
            if (stripos($redirect_url, '://') === false)
                $redirect_url = perfex_saas_default_base_url($redirect_url);
            return redirect($this->redirect_url . "&portal-message=openInParent&portal-message-value=" . urlencode(base64_encode($redirect_url)));
        }
        return redirect($redirect_url);
    }
}