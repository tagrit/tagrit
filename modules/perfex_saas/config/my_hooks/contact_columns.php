<?php

defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_filter('contact_columns', function ($columns) {

    $CI = &get_instance();
    $contactFields = $CI->db->list_fields(db_prefix() . 'contacts');
    if (is_array($contactFields) && is_array($columns)) {
        $columns = array_unique(array_merge($columns, $contactFields));
    }
    return $columns;
});