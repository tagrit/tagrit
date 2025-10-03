<?php

defined('BASEPATH') or exit('No direct script access allowed');

if ($is_tenant) return;

// Add alternative base host to saas custom config file
hooks()->add_filter('before_settings_updated', function ($data) {

    $field = 'perfex_saas_alternative_base_host';
    if (isset($data['settings'][$field])) {
        $alt_base_host = strtolower(trim($data['settings'][$field]));

        // clean and ensure to have domain without scheme or path
        $alt_base_host = str_ireplace(['https://', 'http://', ' '], '', explode('?', $alt_base_host)[0]);
        $alt_base_host = explode('/', $alt_base_host)[0];
        $alt_base_host = trim($alt_base_host, '.');
        $alt_base_host = count(explode('.', $alt_base_host)) > 1 ? $alt_base_host : '';

        unset($data['settings'][$field]);

        $can_save = perfex_saas_write_custom_constant('PERFEX_SAAS_ALTERNATIVE_HOST', $alt_base_host);
        if ($can_save)
            $data['settings'][$field] = $alt_base_host;
    }

    return $data;
});
