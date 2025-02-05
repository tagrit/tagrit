<?php

// uninstall.php

defined('BASEPATH') or exit('No direct script access allowed');

// Declare $CI as global
global $CI;
$CI =& get_instance();

// Drop tables if they exist
$tables_to_drop = [
    'shipment_packages',
    'shipment_fcl_packages',
    'commercial_values_items',
    'pickups',
    'pickup_contacts',
    'contact_persons',
    'third_party_shipments',
    'shipment_status_history',
    'agents',
    'shipment_stops',
    'deliveries',
    'shipments',
    'shipment_companies',
    'shipment_recipients',
    'shipment_senders',
    'shipment_statuses',
    'courier_companies',
    'dimensional_factor',
    'manifests',
    'country_states',
    'manifest_period',
    'courier_audit_logs',
    'destination_offices'
];

foreach ($tables_to_drop as $table) {
    $CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . '_' . $table . '`;');
}
