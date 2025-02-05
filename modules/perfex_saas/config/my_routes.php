<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once('middleware_hooks.php');

if (perfex_saas_is_tenant()) {

    $tenant = perfex_saas_tenant();

    $route['admin/billing/my_account'] = 'perfex_saas/admin/companies/client_portal_bridge';

    $route['billing/my_account/magic_auth'] = 'perfex_saas/authentication/tenant_admin_magic_auth';

    // Custom modules management page for tenants
    unset($route['admin/modules']);
    unset($route['admin/modules/(:any)']);
    unset($route['admin/modules/(:any)/(:any)']);

    $route['admin/apps/modules'] = 'perfex_saas/admin/tenant_modules_page';
    $route['admin/apps/modules/(:any)'] = 'perfex_saas/admin/tenant_modules_page/$1';
    $route['admin/apps/modules/(:any)/(:any)/(:any)'] = 'perfex_saas/admin/tenant_modules_page/$1/$2/$3';

    // Ensure this custom routes is defined if the tenant is identified by request uri segment
    if ($tenant->http_identification_type === PERFEX_SAAS_TENANT_MODE_PATH) {
        $tenant_slug = $tenant->slug;
        $tenant_route_sig = perfex_saas_tenant_url_signature($tenant_slug); //i.e $tenant_route_sig

        // Clone existing static routes with saas id prefix
        foreach ($route as $key => $value) {
            $new_key = perfex_saas_tenant_url_signature($tenant_slug) . "/" . ($key == '/' ? '' : $key);
            $route[$new_key] = $value;
        }
    }
}

if (!perfex_saas_is_tenant()) {

    /** Landing page handling */
    $landing_options = perfex_saas_get_options(['perfex_saas_landing_page_url']);
    $landing_page_url = $landing_options['perfex_saas_landing_page_url'] ?? '';
    if ($landing_page_url && filter_var($landing_page_url, FILTER_VALIDATE_URL)) {
        $method = 'proxy';
        $route['/'] = 'perfex_saas/landing/' . $method;
        $route['default_controller'] = 'perfex_saas/landing/' . $method;
        $route['404_override']         = 'perfex_saas/landing/show_404';

        // ensure the user is redirected to client portal after logging in and not landing page
        hooks()->add_action('after_contact_login', function () {
            $CI = &get_instance();
            if (!$CI->session->has_userdata('red_url')) {
                $CI->session->set_userdata([
                    'red_url' => site_url('clients/'),
                ]);
            }
        });
    }
    /** Ends Landing page handling */

    // Admin perefex saas routes i.e pacakages and companies/instances management
    // @todo Remove below lines in favour of whitelabel options
    $route['admin/perfex_saas/pricing'] = 'perfex_saas/admin/packages/pricing';
    $route['admin/perfex_saas/(:any)'] = 'perfex_saas/admin/$1';
    $route['admin/perfex_saas/(:any)/(:any)'] = 'perfex_saas/admin/$1/$2';
    $route['admin/perfex_saas/(:any)/(:any)/(:any)'] = 'perfex_saas/admin/$1/$2/$3';
    $route['admin/perfex_saas/(:any)/(:any)/(:any)/(:any)'] = 'perfex_saas/admin/$1/$2/$3/$4';

    // Admin perefex saas routes i.e pacakages and companies/instances management
    // Rewrite route for more whitelabelling
    $route['admin/' . PERFEX_SAAS_ROUTE_NAME . '/pricing'] = 'perfex_saas/admin/packages/pricing';
    $route['admin/' . PERFEX_SAAS_ROUTE_NAME . '/(:any)'] = 'perfex_saas/admin/$1';
    $route['admin/' . PERFEX_SAAS_ROUTE_NAME . '/(:any)/(:any)'] = 'perfex_saas/admin/$1/$2';
    $route['admin/' . PERFEX_SAAS_ROUTE_NAME . '/(:any)/(:any)/(:any)'] = 'perfex_saas/admin/$1/$2/$3';
    $route['admin/' . PERFEX_SAAS_ROUTE_NAME . '/(:any)/(:any)/(:any)/(:any)'] = 'perfex_saas/admin/$1/$2/$3/$4';

    // API route
    $route[PERFEX_SAAS_ROUTE_NAME . '/api/(:any)'] = 'perfex_saas/api/api/$1';
    $route[PERFEX_SAAS_ROUTE_NAME . '/api/(:any)/(:any)'] = 'perfex_saas/api/api/$1/$2';
    $route[PERFEX_SAAS_ROUTE_NAME . '/api/(:any)/(:any)/(:any)'] = 'perfex_saas/api/api/$1/$2/$3';


    // Client routes
    $route['clients/packages/(:any)/select'] = 'perfex_saas/perfex_saas_client/subscribe/$1';
    $route['clients/my_account'] = 'perfex_saas/perfex_saas_client/my_account';
    $route['clients/my_account/cancel_subscription'] = 'perfex_saas/perfex_saas_client/cancel_saas_subscription';
    $route['clients/my_account/resume_subscription'] = 'perfex_saas/perfex_saas_client/resume_saas_subscription';
    $route['clients/companies'] = 'perfex_saas/perfex_saas_client/companies';
    $route['clients/companies/(:any)'] = 'perfex_saas/perfex_saas_client/$1';
    $route['clients/companies/(:any)/(:any)'] = 'perfex_saas/perfex_saas_client/$1/$2';

    $route['clients/ps_magic/(:any)'] = 'perfex_saas/authentication/magic_auth/$1';
    $route['clients/ps_magic/(:any)/(:any)'] = 'perfex_saas/authentication/magic_auth/$1/$2';

    $route['billing/my_account/magic_auth'] = 'perfex_saas/authentication/client_magic_auth';
}