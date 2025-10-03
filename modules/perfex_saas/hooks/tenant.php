<?php

defined('BASEPATH') or exit('No direct script access allowed');

/********TENANT/INSTANCE SPECIFIC HOOKS ******/
if ($is_tenant) {
    // Call the function to register the limitation filters
    perfex_saas_register_limitation_filters();

    // Override instant options with shared option in package where applicable
    perfex_saas_init_shared_options();

    /**
     * Hook for tenant instance settings page.
     * It attempt to mask settings and  removes upgrade and system info links.
     *
     * @return void awokumadivad
     */
    hooks()->add_action('settings_group_end', 'perfex_saas_mask_buffer_content');

    // Prevent saving enforced shared fields by overriding with master value
    hooks()->add_filter('before_settings_updated', 'perfex_saas_before_settings_updated_hook');
    function perfex_saas_before_settings_updated_hook($data)
    {
        $tenant = perfex_saas_tenant();

        // Enforced fields
        $enforced_fields = array_merge(PERFEX_SAAS_ENFORCED_SHARED_FIELDS, (array) ($tenant->package_invoice->metadata->shared_settings->enforced ?? []));
        $enforced_settings = perfex_saas_master_shared_settings($enforced_fields);
        foreach ($enforced_settings as $setting) {

            $name = $setting->name;
            $val = $setting->value;

            // Consider some special settings case . See application/models/Settings_model.php for more info
            if ($name == 'default_contact_permissions' && is_string($val) && !empty($val)) {
                $val = unserialize($val);
            } elseif ($name == 'lead_unique_validation' && is_string($val) && !empty($val)) {
                $val = json_decode($val);
            } elseif ($name == 'visible_customer_profile_tabs' && is_string($val) && !empty($val) && $val !== 'all') {
                $val = unserialize($val);
            } elseif ($name == 'default_tax' && is_string($val) && !empty($val)) {
                $val = unserialize($val);
            } elseif ($name == 'staff_notify_completed_but_not_billed_tasks' || $name == 'reminder_for_completed_but_not_billed_tasks_days') {
                if (is_string($val) && !empty($val)) {
                    $val = json_decode($val);
                }
            }

            // Override with master value
            $data['settings'][$name] = $val;
        }

        // Prevent saving masked string of setting values
        $masked_fields = (array) ($tenant->package_invoice->metadata->shared_settings->masked ?? []);
        $shared_secret_master_settings = perfex_saas_master_shared_settings($masked_fields);
        foreach ($shared_secret_master_settings as $setting) {
            if (
                isset($data['settings'][$setting->name]) &&
                perfex_saas_get_starred_string($setting->value) === perfex_saas_get_starred_string($data['settings'][$setting->name])
            ) {
                $data['settings'][$setting->name] = "";
            }
        }

        return $data;
    }

    // Add limitation statistic widget to dashboard
    hooks()->add_action('before_start_render_dashboard_content', 'perfex_saas_dashboard_hook', 8);
    function perfex_saas_dashboard_hook()
    {
        $CI = &get_instance();
        if (is_admin()) {
            $CI->load->model('invoices_model');
            $invoice = perfex_saas_tenant()->package_invoice;
            $on_trial = perfex_saas_invoice_is_on_trial($invoice);
            $days_left = $on_trial ? (int)perfex_saas_get_days_until($invoice->duedate) : '';
            $invoice_days_left = $invoice ? perfex_saas_get_days_until($invoice->duedate) : '';

            $CI->load->view(
                PERFEX_SAAS_MODULE_NAME . '/client/includes/invoice_notification',
                [
                    'invoice' => $invoice,
                    'days_left' => $days_left,
                    'on_trial' => $on_trial,
                    'invoice_days_left' => $invoice_days_left
                ]
            );
        }
    }
    hooks()->add_filter('get_dashboard_widgets', function ($widgets) {
        return array_merge([['path' => PERFEX_SAAS_MODULE_NAME . '/includes/quota_stats', 'container' => 'top-12']], $widgets);
    });

    // Load custom css
    hooks()->add_action('app_admin_assets_added', function () use ($CI) {
        if (is_admin()) {
            $CI->app_css->add('saas-admin', perfex_saas_asset_url('css/tenant-admin.css'));
            $CI->app_scripts->add('tenant-script', perfex_saas_asset_url('js/tenant-admin.js'));
        }
    });

    // Check for upload path
    hooks()->add_filter('get_upload_path_by_type', function ($path, $type) {

        $tenant_upload_folder = str_ireplace(FCPATH, '', PERFEX_SAAS_TENANT_UPLOAD_BASE_FOLDER);
        if (
            strpos($path, $tenant_upload_folder) === false
        ) {
            $path = str_ireplace(FCPATH, '', $path);
            $pos = strpos($path, PERFEX_SAAS_UPLOAD_BASE_DIR);
            if ($pos !== false)
                $path = substr_replace($path, '', $pos, strlen(PERFEX_SAAS_UPLOAD_BASE_DIR));
            $path = $tenant_upload_folder . $path;
        }

        return $path;
    }, PHP_INT_MAX, 2);
}