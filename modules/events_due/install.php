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
        `country` VARCHAR(100) NOT NULL,
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

// Create table `{database_prefix}_events_due_events`
if (!$CI->db->table_exists(db_prefix() . 'events_due_events')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'events_due_events` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `event_name` VARCHAR(255) NOT NULL,
        `event_code` VARCHAR(100) NOT NULL UNIQUE,
        `location_id` INT NOT NULL,
        `venue_id` INT NOT NULL,
        `type` ENUM("Physical", "Virtual") NOT NULL,
        `duration` ENUM("5 Days", "7 Days", "10 Days", "14 Days") NOT NULL,
        `cost_net` DECIMAL(10,2) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`location_id`) REFERENCES ' . db_prefix() . 'events_due_locations(`id`),
        FOREIGN KEY (`venue_id`) REFERENCES ' . db_prefix() . 'events_due_venues(`id`)
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
