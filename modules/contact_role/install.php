<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Create tables
$db_prefix = db_prefix();
$table = $db_prefix . 'contact_roles';
$contact_table = $db_prefix . 'contacts';
$field_name = 'contact_role_id';

if (!$CI->db->field_exists($field_name, $contact_table)) {
    $CI->db->query("ALTER TABLE `$contact_table` ADD `$field_name` INT NULL");
}

if (!$CI->db->table_exists($table)) {
    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `" . $table . "` (
            `id` int NOT NULL AUTO_INCREMENT,
            `name` varchar(250) NOT NULL,
            `permissions` text,
            `email_notifications` text,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";"
    );
}

if (!$CI->db->field_exists('email_notifications', $table)) {
    $CI->db->query("ALTER TABLE `$table` ADD `email_notifications` TEXT");
}