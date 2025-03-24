<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();


// Create table `{database_prefix}_events_due_locations`
if (!$CI->db->table_exists(db_prefix() . 'events_due_locations')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'events_due_locations` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Create table `{database_prefix}_events_due_venues`
if (!$CI->db->table_exists(db_prefix() . 'events_due_venues')) {
    $table_name = db_prefix() . 'events_due_venues';
    $charset = $CI->db->char_set; // Get charset
    $CI->db->query("
        CREATE TABLE `$table_name` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=$charset;
    ");
}


// Create table `{database_prefix}_events_due_registrations`
if (!$CI->db->table_exists(db_prefix() . 'events_due_registrations')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'events_due_registrations` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `status` ENUM("Pending", "Confirmed", "Canceled") NOT NULL DEFAULT "Pending",
        `payment_status` ENUM("Pending", "Paid") NOT NULL DEFAULT "Pending",
        `invoice_sent` BOOLEAN NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}


//insert locations
$locations = [
    'Diani', 'Mombasa', 'Machakos', 'Nakuru', 'Naivasha',
    'Kisumu', 'Thika', 'Eldoret', 'Dubai', 'Arusha',
    'Malaysia', 'Singapore'
];

foreach ($locations as $location) {
    $CI->db->insert(db_prefix() . 'events_due_locations', ['name' => $location]);
}

//insert venues
$venues = [
    ['name' => 'Sarova Hotel'],
    ['name' => 'Voyager Hotel'],
    ['name' => 'Baobab Hotel'],
    ['name' => 'Seo Hotel'],
    ['name' => 'Maanzoni Lodge'],
    ['name' => 'Blooming Suites Hotel'],
    ['name' => 'Eseriani Hotel'],
    ['name' => 'Sarova Woodlands Hotel'],
    ['name' => 'Ole Ken Hotel'],
    ['name' => 'Sarova Imperial'],
    ['name' => 'The Luke Hotel'],
    ['name' => 'Mt. Meru Hotel'],
    ['name' => 'Ibis Bencoolen'],
];

foreach ($venues as $venue) {
    $CI->db->insert(db_prefix() . 'events_due_venues', ['name' => $venue['name']]);
}
