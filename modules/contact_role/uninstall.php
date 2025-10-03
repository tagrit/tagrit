<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

$db_prefix = db_prefix();
$table = $db_prefix . 'contact_roles';
$contact_table = $db_prefix . 'contacts';
$field_name = 'contact_role_id';

if ($CI->db->field_exists($field_name, $contact_table)) {
    $CI->db->query("ALTER TABLE `$contact_table` DROP `$field_name`");
}

if ($CI->db->table_exists($table)) {
    $CI->db->query("DROP TABLE $table");
}