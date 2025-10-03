<?php

defined('BASEPATH') or exit('No direct script access allowed');

/** GATE KEEPERS ***/
if (perfex_saas_is_tenant()) return;

if (get_option('perfex_saas_plesk_enabled') != '1') return;

/** Load library */
$CI->load->library(PERFEX_SAAS_MODULE_NAME . '/integrations/plesk_api');


/******** HELPERS *******/
if (!function_exists('perfex_saas_plesk_enable_aliasdomain')) {
    /**
     * Check if addon domain is enabled for cpanel
     *
     * @return bool
     */
    function perfex_saas_plesk_enable_aliasdomain()
    {
        return get_option('perfex_saas_plesk_enable_aliasdomain') == '1';
    }
}

if (!function_exists('perfex_saas_plesk_allowed_for_tenant')) {
    /**
     * Check if tenant can use plesk
     *
     * @param object $tenant
     * @return bool
     */
    function perfex_saas_plesk_allowed_for_tenant($tenant)
    {
        $package_invoice = perfex_saas_get_client_package_invoice($tenant->clientid);
        $scheme = $package_invoice->db_scheme ?? '';
        return $scheme === 'plesk';
    }
}

if (!function_exists('perfex_saas_get_plesk')) {
    /**
     * Get plesk api library instance
     *
     * @return Plesk_api $plesk
     */
    function perfex_saas_get_plesk()
    {
        $CI = &get_instance();
        $host = get_option('perfex_saas_plesk_host');
        $domain = get_option('perfex_saas_plesk_primary_domain');
        $user = get_option('perfex_saas_plesk_username');
        $password = $CI->encryption->decrypt(get_option('perfex_saas_plesk_password'));

        $CI->plesk_api->init(
            $host,
            $domain,
            $user,
            $password,
            PERFEX_SAAS_MODULE_NAME_SHORT . '_'
        );
        return $CI->plesk_api;
    }
}

if (!function_exists('perfex_saas_plesk_setup_tenant_db')) {
    /**
     * Create database and user for the tenant slug
     *
     * @param string $slug
     * @return array
     */
    function perfex_saas_plesk_setup_tenant_db($slug)
    {

        $CI = &get_instance();
        $plesk = perfex_saas_get_plesk();

        $db_port = (int)$plesk->databaseServer->port;
        $db_host = $plesk->databaseServer->host;
        $db_host = empty($db_host) ? "localhost" : $db_host;

        $db_user = $plesk->addPrefix($slug);
        $db_name = $plesk->addPrefix($slug);

        if (!function_exists('random_string')) {
            $CI->load->helper('string');
        }
        $db_password = random_string('alnum', 16);


        try {
            $plesk->createDatabaseWithUser($db_user, $db_password, $db_name);
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }

        $db = [
            'host' => $db_host,
            'user' => $db_user,
            'password' => $db_password,
            'dbname' => $db_name,
        ];

        if (!empty($db_port) && $db_port !== 3306)
            $db['host'] = $db_host . ':' . $db_port;

        return $db;
    };
}

if (!function_exists('perfex_saas_plesk_rollback')) {
    /**
     * Rollback tenant db and addon domain setup
     *
     * @param object $tenant
     * @return void
     */
    function perfex_saas_plesk_rollback($tenant)
    {
        if (!perfex_saas_plesk_allowed_for_tenant($tenant)) return;

        $plesk = perfex_saas_get_plesk();

        $slug = $tenant->slug;
        $db_name = $plesk->addPrefix($slug);
        $subdomain = $slug . '.' . perfex_saas_get_saas_default_host();

        try {
            $plesk->deleteDatabase($db_name);
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }

        if (!perfex_saas_plesk_enable_aliasdomain()) return;

        try {
            $plesk->deleteSiteAlias($subdomain);
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }

        if (!empty($tenant->custom_domain)) {
            try {
                $plesk->deleteSiteAlias($tenant->custom_domain);
            } catch (\Throwable $th) {
                log_message('error', $th->getMessage());
            }
        }
    };
}


/**** HOOKS *******/

// Add as option to package db scheme
hooks()->add_filter('perfex_saas_module_db_schemes', function ($schemes) {
    $schemes[] = ['key' => 'plesk', 'label' => _l('perfex_saas_plesk_scheme')];
    return $schemes;
});

// Add as option to tenant db scheme
hooks()->add_filter('perfex_saas_module_db_schemes_alt', function ($schemes) {
    $schemes[] = ['key' => 'plesk', 'label' => _l('perfex_saas_plesk_scheme')];
    return $schemes;
});

// Create dsn: db user and password and db  e.t.c
hooks()->add_filter('perfex_saas_module_maybe_create_tenant_dsn', function ($data) {

    $tenant = $data['tenant'];
    $invoice = $data['invoice'];

    if ($invoice->db_scheme === 'plesk') {

        $data['dsn'] = perfex_saas_plesk_setup_tenant_db($tenant->slug);
    }
    return $data;
});

// Detect change in custom domain and add/delete in plesk accordingly if there is valid change
hooks()->add_filter('perfex_saas_module_tenant_data_payload', function ($payload) {

    $data = $payload['data'];
    $tenant = $payload['tenant'];
    $invoice = $payload['invoice'];

    $addondomain_mode = get_option('perfex_saas_plesk_addondomain_mode');
    $addondomain_mode = empty($addondomain_mode) ? 'all' : $addondomain_mode;

    $can_use_custom_domain = (int)($invoice->metadata->enable_custom_domain ?? 0) && in_array($addondomain_mode, ['customdomain', 'all']);

    // We only want to run this only when updating and not new.
    if (!perfex_saas_plesk_enable_aliasdomain() || !$tenant || !$can_use_custom_domain) return $payload;

    if (!perfex_saas_plesk_allowed_for_tenant($tenant)) return $payload;

    $new_domain = $data['custom_domain'] ?? '';
    $old_domain = $tenant->custom_domain ?? '';
    $slug = $tenant->slug ?? $data['slug'];
    // Create new custom domain when updating
    if ($new_domain !== $old_domain) {

        $plesk = perfex_saas_get_plesk();

        if (!empty($old_domain)) {
            // Remove old custom domain
            try {
                $plesk->deleteSiteAlias($old_domain);
            } catch (\Throwable $th) {
                log_message('error', $th->getMessage());
                get_instance()->session->set_flashdata('message-danger', $th->getMessage());
            }
        }

        // Create new addon entry
        if (!empty($new_domain)) {
            try {
                $plesk->createSiteAlias($new_domain);
            } catch (\Throwable $th) {
                $payload['data']['custom_domain'] = '';
                $payload['error'] = $th->getMessage();
                log_message('error', $th->getMessage());
            }
        }
    }

    return $payload;
}, 3);

// Create subdomain if supported in package and or custom domain
hooks()->add_action('perfex_saas_module_tenant_deployed', function ($data) {

    if (!perfex_saas_plesk_enable_aliasdomain()) return;

    $tenant = $data['tenant'];
    $invoice = $data['invoice'];

    if (!perfex_saas_plesk_allowed_for_tenant($tenant)) return;

    $plesk = perfex_saas_get_plesk();

    $slug = $tenant->slug;
    $subdomain = $slug . '.' . perfex_saas_get_saas_default_host();
    $custom_domain = $tenant->custom_domain;

    $addondomain_mode = get_option('perfex_saas_plesk_addondomain_mode');
    $addondomain_mode = empty($addondomain_mode) ? 'all' : $addondomain_mode;

    $can_use_subdomain = (int)($invoice->metadata->enable_subdomain ?? 0) && in_array($addondomain_mode, ['subdomain', 'all']);
    $can_use_custom_domain = (int)($invoice->metadata->enable_custom_domain ?? 0) && in_array($addondomain_mode, ['customdomain', 'all']);

    if (!$can_use_custom_domain && !$can_use_subdomain) return;

    // Create subdomain as addon domain for the tenant, this allow inheriting of ssl.
    if ($can_use_subdomain) {
        try {
            $plesk->createSiteAlias($subdomain);
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
            get_instance()->session->set_flashdata('message-danger', $th->getMessage());
        }
    }

    // Create custom domain as addon domain
    if (!empty($custom_domain) && $can_use_custom_domain) {
        try {
            $plesk->createSiteAlias($custom_domain);
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
            get_instance()->session->set_flashdata('message-danger', $th->getMessage());
        }
    }
}, 2);

// Remove tenant db, subdomain, addondomain and db
hooks()->add_action('perfex_saas_module_tenant_removed', 'perfex_saas_plesk_rollback');
hooks()->add_action('perfex_saas_module_tenant_removal_failed', 'perfex_saas_plesk_rollback');
hooks()->add_action('perfex_saas_module_tenant_deploy_failed', 'perfex_saas_plesk_rollback');