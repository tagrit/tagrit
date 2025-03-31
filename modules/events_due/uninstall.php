<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Declare $CI as global
global $CI;
$CI =& get_instance();

// Disable foreign key checks to prevent constraint issues
$CI->db->query('SET FOREIGN_KEY_CHECKS = 0;');

// Drop tables if they exist
$tables_to_drop = [
    'events_due_registrations',
    'events_due_venues',
    'events_due_locations',
    '_notification_queue',
    'email_reminder_period'
];

foreach ($tables_to_drop as $table) {
    $CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . $table . '`;');
}

// Re-enable foreign key checks
$CI->db->query('SET FOREIGN_KEY_CHECKS = 1;');
