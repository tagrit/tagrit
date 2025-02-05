<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$is_tenant && $is_client) {


    if (!function_exists('perfex_saas_after_first_instance')) {
        /**
         * Redirect to provided url if not empty
         *
         * @param object $company
         * @return void
         */
        function perfex_saas_after_first_instance($company)
        {
            $CI = &get_instance();

            $flag = 'first_instance_redirection';

            $redirect_url = get_option('perfex_saas_after_first_instance_redirect_url');

            if (empty($redirect_url)) return;

            // Skip if already redirecting and is local url
            if (stripos($_SERVER["REQUEST_URI"], $flag) !== false || $CI->session->has_userdata($flag)) return;

            // Append basic tenant info
            $info = "tenant_id=$company->id&tenant_slug=$company->slug&tenant_name=$company->name&$flag";
            $redirect_url .= empty(parse_url($redirect_url, PHP_URL_QUERY)) ? "?$info" : "&$info";

            $CI->session->set_userdata($flag, 'true');

            // Make http request to deploy the company
            $deploy_url = base_url("clients/companies/deploy");
            $script = "<script>fetch('$deploy_url'); setTimeout(() => {window.location='$redirect_url'}, 200);</script>";
            exit($script);
        }
    }

    hooks()->add_action('perfex_saas_after_client_create_instance', function ($id) use ($CI) {
        if ($id) {
            $companies = $CI->perfex_saas_model->companies();
            if (count($companies) === 1 && is_contact_email_verified()) {

                $new_company = $companies[0];
                perfex_saas_after_first_instance($new_company);
            }
        }
    });

    // Handle other cases
    $client_companies = $CI->perfex_saas_model->companies();
    if (count($client_companies) === 1) {
        $new_company = $client_companies[0];
        if ($new_company->status === PERFEX_SAAS_STATUS_PENDING && is_contact_email_verified()) {
            perfex_saas_after_first_instance($new_company);
        }
    }
}
