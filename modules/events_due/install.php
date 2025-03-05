<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Create table `{database_prefix}_events_due_clients`
if (!$CI->db->table_exists(db_prefix() . 'events_due_clients')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'events_due_clients` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `full_name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL UNIQUE,
        `organization_name` VARCHAR(255) NOT NULL,
        `phone_number` VARCHAR(20) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}


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
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'events_due_venues` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `location_id` INT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`location_id`) REFERENCES ' . db_prefix() . 'events_due_locations(`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Create table `{database_prefix}_events_due_name`
if (!$CI->db->table_exists(db_prefix() . 'events_due_name')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'events_due_name` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `event_name` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}


// Create table `{database_prefix}_events_due_events`
if (!$CI->db->table_exists(db_prefix() . 'events_due_events')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'events_due_events` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `event_name_id` INT NOT NULL,
        `location_id` INT NOT NULL,
        `venue_id` INT NOT NULL,
        `setup` ENUM("Physical", "Virtual") NOT NULL,
        `duration` ENUM("5 Days", "7 Days", "10 Days", "14 Days") NOT NULL DEFAULT "5 Days",
        `division` VARCHAR(255) NOT NULL,
        `type` ENUM("Local", "International") NOT NULL,
        `cost_net` DECIMAL(10,2) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `start_date` VARCHAR(255),
        `end_date` VARCHAR(255),
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`event_name_id`) REFERENCES `' . db_prefix() . 'events_due_name`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`location_id`) REFERENCES `' . db_prefix() . 'events_due_locations`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`venue_id`) REFERENCES `' . db_prefix() . 'events_due_venues`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}


// Create table `{database_prefix}_events_due_registrations`
if (!$CI->db->table_exists(db_prefix() . 'events_due_registrations')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'events_due_registrations` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `client_id` INT NOT NULL,
        `event_id` INT NOT NULL,
        `status` ENUM("Pending", "Confirmed", "Canceled") NOT NULL DEFAULT "Pending",
        `payment_status` ENUM("Pending", "Paid") NOT NULL DEFAULT "Pending",
        `invoice_sent` BOOLEAN NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`client_id`) REFERENCES ' . db_prefix() . 'events_due_clients(`id`),
        FOREIGN KEY (`event_id`) REFERENCES ' . db_prefix() . 'events_due_events(`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Create table `{database_prefix}_events_due_invoices`
if (!$CI->db->table_exists(db_prefix() . 'events_due_invoices')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'events_due_invoices` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `registration_id` INT NOT NULL,
        `invoice_number` VARCHAR(100) NOT NULL UNIQUE,
        `total_amount` DECIMAL(10,2) NOT NULL,
        `vat` DECIMAL(10,2) NOT NULL,
        `grand_total` DECIMAL(10,2) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`registration_id`) REFERENCES ' . db_prefix() . 'events_due_registrations(`id`)
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
    ['name' => 'Sarova Hotel', 'location' => 'Mombasa'],
    ['name' => 'Voyager Hotel', 'location' => 'Mombasa'],
    ['name' => 'Baobab Hotel', 'location' => 'Diani'],
    ['name' => 'Seo Hotel', 'location' => 'Machakos'],
    ['name' => 'Maanzoni Lodge', 'location' => 'Machakos'],
    ['name' => 'Blooming Suites Hotel', 'location' => 'Naivasha'],
    ['name' => 'Eseriani Hotel', 'location' => 'Naivasha'],
    ['name' => 'Sarova Woodlands Hotel', 'location' => 'Nakuru'],
    ['name' => 'Ole Ken Hotel', 'location' => 'Nakuru'],
    ['name' => 'Sarova Imperial', 'location' => 'Kisumu'],
    ['name' => 'The Luke Hotel', 'location' => 'Thika'],
    ['name' => 'Mt. Meru Hotel', 'location' => 'Arusha'],
    ['name' => 'Ibis Bencoolen', 'location' => 'Singapore'],
];

foreach ($venues as $venue) {
    // Get location_id based on location name
    $query = $CI->db->get_where(db_prefix() . 'events_due_locations', ['name' => $venue['location']]);
    $location = $query->row();

    if ($location) {
        // Insert venue with the correct location_id
        $CI->db->insert(db_prefix() . 'events_due_venues', [
            'name' => $venue['name'],
            'location_id' => $location->id,
        ]);
    }
}
