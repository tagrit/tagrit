<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This is a common class for managing magic authentications
 */
class Authentication extends ClientsController
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Method to login into an instance magically from the client dashboard.
     * It create auto login cookie (used by perfex core) and redirect to the company admin address.
     * Perfex pick the cookie and authorized. The cookie is localized to the company address only and inserted into db using the instance context.
     * Also when retrieving the cookie from db, the db_simple_query restrict the selection to the instance.
     *
     * @param string $slug
     * @return void
     */
    public function magic_auth($slug, $urlmode = 'path')
    {
        // Ensure we have an authenticated client
        if (!is_client_logged_in() || perfex_saas_is_tenant()) {
            perfex_saas_show_tenant_error(_l('perfex_saas_permission_denied'), _l('perfex_saas_authentication_required_for_magic_login'), 404);
        }

        $company = $this->perfex_saas_model->get_entity_by_slug('companies', $slug, 'parse_company');
        if (!$company) {
            perfex_saas_show_tenant_error(_l('perfex_saas_permission_denied'), _l('perfex_saas_page_not_found'), 404);
        }

        // Ensure the company belongs to the logged in client
        if ($company->clientid !== get_client_user_id()) {
            perfex_saas_show_tenant_error(_l('perfex_saas_permission_denied'), '');
        }

        // Ensure the contact have access to login
        if (!perfex_saas_contact_can_magic_auth($company)) {
            perfex_saas_show_tenant_error(_l('perfex_saas_permission_denied'), '');
        }

        $auth_code = perfex_saas_generate_magic_auth_code($company->clientid);

        $support_custom_domain_magic_login = get_option('perfex_saas_enable_cross_domain_bridge') == "1";
        $links = perfex_saas_tenant_base_url($company, '', 'all');

        if ($urlmode == 'all' || !isset($links[$urlmode])) {
            $urlmode = 'subdomain';
            if (!empty($links['custom_domain']) && $support_custom_domain_magic_login)
                $urlmode = 'custom_domain';
        }

        // Improve experience in newly created instance on cpanel/plesk that might not have subdomain SSL fully setup.
        if ((time() - strtotime($company->created_at)) < 60 * 10 && $urlmode != 'path') { // if created less than 20min
            $package = $this->perfex_saas_model->get_company_invoice($company->clientid);
            if (!empty($package->db_scheme) && in_array($package->db_scheme, ['cpanel', 'plesk'])) {

                $can_optimize = false;

                if ($package->db_scheme === 'cpanel')
                    $can_optimize = perfex_saas_cpanel_enable_addondomain();

                if ($package->db_scheme === 'plesk')
                    $can_optimize = perfex_saas_plesk_enable_aliasdomain();

                $integration_mode = get_option('perfex_saas_' . $package->db_scheme . '_addondomain_mode');
                if ($can_optimize && $integration_mode) {
                    if (!in_array($integration_mode, ['all', 'subdomain']))
                        $can_optimize = false;
                }

                if ($can_optimize)
                    $urlmode = 'path';
            }
        }

        $redirect = empty($links[$urlmode]) ? $links['path'] : $links[$urlmode];

        $query = 'billing/my_account/magic_auth?auth_code=' . urlencode($auth_code);
        if (!empty($redirect_query = $this->input->get('redirect', true)))
            $query .= '&redirect=' . $redirect_query;

        return redirect($redirect . $query);
    }

    /**
     * Authenticate a tenant admin into the Saas client portal.
     *
     * This method signs in a tenant admin into the Saas client portal, allowing them to access client-specific features from the instance.
     *
     * @return void
     */
    public function client_magic_auth()
    {

        try {
            // Check if the user is a tenant or if the client bridge is not enabled
            if (perfex_saas_is_tenant() || get_option('perfex_saas_enable_client_bridge') !== "1") {
                throw new \Exception(_l('perfex_saas_permission_denied'), 1);
            }

            // Determine the redirect URL or use a default if not provided
            $redirect = $this->input->get('redirect', true) ?? 'clients/my_account';

            // If the client is already logged in with a magic code, redirect
            if (is_client_logged_in() && $this->session->has_userdata('magic_code')) {
                return redirect($redirect);
            }

            // Validate and authorize the magic authentication code
            $clientid = perfex_saas_validate_and_authorize_magic_auth_code();
            $contact = perfex_saas_get_primary_contact($clientid);

            if (!$contact) {
                throw new \Exception(_l('perfex_saas_error_finding_primary_contact'), 1);
            }

            // Sign in as a client in Saas (i.e., superclient)
            login_as_client($contact->userid);
            $user_data = [
                'magic_auth'       => [
                    'cross_domain' => (int)$this->input->get('cross_domain', true),
                    'source_url' => $this->input->get('source_url', true)
                ],
            ];
            $this->session->set_userdata($user_data);

            return redirect($redirect);
        } catch (\Throwable $th) {
            perfex_saas_show_tenant_error(_l('perfex_saas_authentication_error'), $th->getMessage());
        }
    }

    /**
     * Auto-magic login into the current tenant instance as an admin using a magic code set from another instance.
     *
     * This method allows switching to another instance from the current one by automatically signing in as a tenant admin.
     *
     * @return void
     */
    public function tenant_admin_magic_auth()
    {
        try {

            // Check if the user is not a tenant or if instance switching is not enabled
            if (!perfex_saas_is_tenant()) {
                throw new \Exception(_l('perfex_saas_permission_denied'), 1);
            }

            // Determine the redirect URL or use a default if not provided
            $redirect = $this->input->get('redirect', true) ?? '';

            // If the user is already an admin, redirect to the admin dashboard
            if (is_admin()) {
                return redirect(admin_url($redirect));
            }

            // Validate and authorize the magic authentication code
            $clientid = perfex_saas_validate_and_authorize_magic_auth_code();

            // Ensure that the client matches the current tenant instance
            if ((int)perfex_saas_tenant()->clientid !== $clientid) {
                throw new \Exception(_l('perfex_saas_permission_denied'), 1);
            }

            // Sign in as the current tenant instance's admin
            perfex_saas_tenant_admin_autologin();

            $redirect .=  (empty($redirect) ? '?' : '&') . '_magic_auth_session';
            return redirect(admin_url($redirect));
        } catch (\Throwable $th) {
            perfex_saas_show_tenant_error(_l('perfex_saas_authentication_error'), $th->getMessage());
        }
    }
}