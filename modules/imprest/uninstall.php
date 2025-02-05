<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Declare $CI as global
global $CI;
$CI =& get_instance();

// Drop tables if they exist
$tables_to_drop = [
    'settings',
    'additional_funds_details',
    'hotel_conferencing_details',
    'hotel_accommodation_details',
    'speaker_details',
    'fund_request_items',
    'fund_requests',
    'expense_subcategories',
    'expense_categories',
    'events_details',
    'events',
];

foreach ($tables_to_drop as $table) {
    $CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . '_' . $table . '`;');
}

$query = $CI->db->select('id, additional_data')
    ->from(db_prefix() . 'notifications')
    ->get();

if ($query->num_rows() > 0) {
    foreach ($query->result() as $notification) {
        $additional_data = @unserialize($notification->additional_data);

        // Check if unserialization is successful and contains 'imprest'
        if (is_array($additional_data) && in_array('imprest', $additional_data)) {
            // Delete the notification
            $CI->db->where('id', $notification->id);
            $CI->db->delete(db_prefix() . 'notifications');
        }
    }
}

// Delete emailtemplates with slug 'fund-request-updated'
$CI->db->where('slug', 'fund-request-updated');
$CI->db->delete(db_prefix() . 'emailtemplates');