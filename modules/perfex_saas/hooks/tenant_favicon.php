<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$is_tenant) {
    return;
}

if (!function_exists('perfex_saas_rename_tenant_favicon')) {
    function perfex_saas_rename_tenant_favicon($hook_data = null)
    {
        $CI = &get_instance();

        // Get the value directly from db i.e dont use get_option as cache old value pre image upload will be returned
        $row = $CI->db->select('value')->where('name', 'favicon')->get(db_prefix() . 'options')->row();
        $favicon = $row->value ?? '';

        if (!empty($favicon) && str_starts_with($favicon, 'favicon')) {

            $path = get_upload_path_by_type('company');
            $extension = pathinfo($favicon, PATHINFO_EXTENSION);
            $filename = md5('favicon_' . perfex_saas_tenant_slug() . '_' . time()) . '.' . $extension;

            $old_file = $path . $favicon;
            if (file_exists($old_file) && rename($old_file, $path . $filename))
                update_option('favicon', $filename);
        }

        return $hook_data;
    }
}

/**
 * Rename favicon after settings updated if neccessary.
 * This will be emitted when settings is update and only after the logo and favicon is updated
 */
hooks()->add_filter('before_settings_updated', 'perfex_saas_rename_tenant_favicon');

/**
 * Backward compatibility for old icon. 
 * To be removed in future updates.
 * 
 */
hooks()->add_action('before_cron_run', 'perfex_saas_rename_tenant_favicon');