<?php

defined('BASEPATH') or exit('No direct script access allowed');

if ($is_tenant) return;

// Add path id to saas custom config file
hooks()->add_filter('before_settings_updated', function ($data) {

    $field = 'perfex_saas_route_id';
    if (isset($data['settings'][$field])) {
        $path_id = strtolower(slug_it($data['settings'][$field]));

        unset($data['settings'][$field]);

        $can_save = perfex_saas_write_custom_constant('PERFEX_SAAS_ROUTE_ID', $path_id);

        if ($can_save)
            $data['settings'][$field] = $path_id;
    }

    return $data;
});
