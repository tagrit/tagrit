<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Declare $CI as global
global $CI;
$CI =& get_instance();

// create table `tbl_courier_companies`
if (!$CI->db->table_exists(db_prefix() . '_courier_companies')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . '_courier_companies` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `company_name` VARCHAR(255) NOT NULL,
        `prefix` VARCHAR(255) NOT NULL,
        `type` ENUM("internal", "third_party") NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

    // Insert data into `tbl_courier_companies`
    $CI->db->query('INSERT INTO `' . db_prefix() . '_courier_companies` (`id`, `company_name`, `prefix`, `type`) VALUES
    (1, "GO Shipping", "GOSHP", "internal"),
    (2, "DELL", "DELL", "third_party"),
    (3, "FedEx", "FEDE", "third_party"),
    (4, "Dafric", "DAFR", "third_party"),
    (5, "MultiMedia", "MILTU", "third_party");');

}


//create table  `tbl_shipment_statuses`
if (!$CI->db->table_exists(db_prefix() . '_shipment_statuses')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_shipment_statuses` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `status_name` VARCHAR(100) NOT NULL,
        `description` VARCHAR(255) NOT NULL,
        `active` TINYINT(1) NOT NULL DEFAULT \'1\',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');


    // Insert data into `tbl_shipment_statuses`
    $CI->db->query('INSERT INTO `' . db_prefix() . '_shipment_statuses` (`id`, `status_name`, `description`, `active`) VALUES
    (1, "created", "Created", 1),
    (2, "picked_up", "Picked up", 1),
    (3, "received", "Received", 1),
    (4, "dispatched", "Dispatched", 1),
    (5, "in_transit", "In Transit", 1),
    (6, "arrived_destination", "Arrived at Destination", 1),
    (7, "out_for_delivery", "Out For Delivery", 1),
    (8, "delivered", "Delivered", 1);');

}


//create table  `tbl_shipment_recipients`
if (!$CI->db->table_exists(db_prefix() . '_shipment_recipients')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_shipment_recipients` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `first_name` VARCHAR(255) NOT NULL,
        `last_name` VARCHAR(255) NOT NULL,
        `phone_number` VARCHAR(20) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `address` TEXT,
        `zipcode` VARCHAR(20) NOT NULL,
        `address_type` ENUM(\'postal_code\', \'zip_code\') NOT NULL,
        `state_id` INT NULL,
        `country_id` INT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`country_id`) REFERENCES `' . db_prefix() . 'countries`(`country_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}

// Create table `tbl_shipment_companies`
if (!$CI->db->table_exists(db_prefix() . '_shipment_companies')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_shipment_companies` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `company_name` VARCHAR(255) NOT NULL,
        `contact_person_name` VARCHAR(255) NOT NULL,
        `contact_person_phone_number` VARCHAR(20) UNIQUE NOT NULL,
        `contact_person_email` VARCHAR(255) NOT NULL,
        `contact_state_id` INT NULL,
        `contact_country_id` INT NULL,
        `contact_address_type` ENUM(\'postal_code\', \'zip_code\') NOT NULL,
        `contact_address` TEXT,
        `contact_zipcode` VARCHAR(20) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}

// Create table `tbl_recipient_companies`
if (!$CI->db->table_exists(db_prefix() . '_recipient_companies')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_recipient_companies` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `recipient_company_name` VARCHAR(255) NOT NULL,
        `recipient_contact_person_name` VARCHAR(255) NOT NULL,
        `recipient_contact_person_phone_number` VARCHAR(20) UNIQUE NOT NULL,
        `recipient_contact_person_email` VARCHAR(255) NOT NULL,
        `recipient_contact_state_id` INT NULL,
        `recipient_contact_country_id` INT NULL,
        `recipient_contact_address_type` ENUM(\'postal_code\', \'zip_code\') NOT NULL,
        `recipient_contact_address` TEXT,
        `recipient_contact_zipcode` VARCHAR(20) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}

// Create table `tbl_shipment_senders`
if (!$CI->db->table_exists(db_prefix() . '_shipment_senders')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_shipment_senders` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `first_name` VARCHAR(255) NOT NULL,
        `last_name` VARCHAR(255) NOT NULL,
        `phone_number` VARCHAR(20) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `address` TEXT,
        `zipcode` VARCHAR(20) NOT NULL,
        `address_type` ENUM(\'postal_code\', \'zip_code\') NOT NULL,
        `state_id` INT NULL,
        `country_id` INT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`country_id`) REFERENCES `tblcountries`(`country_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}


// Create table `tbl_shipments`
if (!$CI->db->table_exists(db_prefix() . '_shipments')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_shipments` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `shipping_mode` VARCHAR(255) NOT NULL,
        `shipping_category` ENUM(\'domestic\', \'international\') NOT NULL,
        `export` INT NOT NULL DEFAULT 0,
        `import` INT NOT NULL DEFAULT 0,
        `tracking_id` VARCHAR(255) NOT NULL,
        `company_type` VARCHAR(255) NOT NULL,
        `waybill_number` VARCHAR(255) NOT NULL,
        `courier_company_id` INT NOT NULL,
        `invoice_id` INT NOT NULL DEFAULT 0,
        `status_id` INT NOT NULL,
        `sender_id` INT NULL,
        `recipient_id` INT NULL,
        `company_id` INT NULL,
        `recipient_company_id` INT NULL,
        `fcl_shipment` INT NULL,
        `staff_id` INT NOT NULL,
        `packaging_charges` DECIMAL(10, 2) NOT NULL,
        `commercial_invoice_url` VARCHAR(255),
        `created_at` DATETIME,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`courier_company_id`) REFERENCES `' . db_prefix() . '_courier_companies`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`status_id`) REFERENCES `' . db_prefix() . '_shipment_statuses`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`sender_id`) REFERENCES `' . db_prefix() . '_shipment_senders`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`recipient_id`) REFERENCES `' . db_prefix() . '_shipment_recipients`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`company_id`) REFERENCES `' . db_prefix() . '_shipment_companies`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}


// Create table `tbl_third_party_shipments`
if (!$CI->db->table_exists(db_prefix() . '_third_party_shipments')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_third_party_shipments` (
        `shipment_id` INT NOT NULL,
        `tracking_id` VARCHAR(255) NOT NULL,
        `courier_company_id` INT NOT NULL,
        PRIMARY KEY (`shipment_id`),
        FOREIGN KEY (`shipment_id`) REFERENCES `' . db_prefix() . '_shipments`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`courier_company_id`) REFERENCES `' . db_prefix() . '_courier_companies`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}


// Create table `tbl_pickup_contacts`
if (!$CI->db->table_exists(db_prefix() . '_pickup_contacts')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_pickup_contacts` (
        `id` INT NOT NULL auto_increment,
        `first_name` VARCHAR(255) NOT NULL,
        `last_name` VARCHAR(255) NOT NULL,
        `phone_number` VARCHAR(20) NOT NULL,
        `email` VARCHAR(255) UNIQUE NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}


// Create table `tbl_contact_persons`
if (!$CI->db->table_exists(db_prefix() . '_contact_persons')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_contact_persons` (
        `id` INT NOT NULL auto_increment,
        `company_id` INT NOT NULL,
        `first_name` VARCHAR(255) NOT NULL,
        `last_name` VARCHAR(255) NOT NULL,
        `phone_number` VARCHAR(20) UNIQUE NOT NULL,
        `email` VARCHAR(255) UNIQUE NOT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`company_id`) REFERENCES `' . db_prefix() . '_courier_companies`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}

// Create table `tbl_pickups`
if (!$CI->db->table_exists(db_prefix() . '_pickups')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_pickups` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `pickup_date` DATE,
        `pickup_start_time` VARCHAR(20) NOT NULL,
        `pickup_end_time` VARCHAR(20) NOT NULL,
        `country_id` INT NULL,
        `state_id` INT NULL,
        `address` TEXT NOT NULL,
        `pickup_zip` VARCHAR(20) NOT NULL,
        `address_type` VARCHAR(20) NOT NULL,
        `vehicle_type` VARCHAR(20) NOT NULL,
        `shipment_id` INT NULL,
        `contact_person_id` INT NULL,
        `staff_id` INT NOT NULL,
        `driver_id` INT NOT NULL,
        `status` ENUM(\'pending\', \'picked_up\', \'delivered\') NOT NULL DEFAULT \'pending\',
        `signature_url` TEXT NOT NULL,
        `created_at` DATETIME,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`country_id`) REFERENCES `' . db_prefix() . 'countries`(`country_id`) ON DELETE CASCADE,
        FOREIGN KEY (`shipment_id`) REFERENCES `' . db_prefix() . '_shipments`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`contact_person_id`) REFERENCES `' . db_prefix() . '_pickup_contacts`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}


// Create table `tbl_shipment_fcl_packages`
if (!$CI->db->table_exists(db_prefix() . '_shipment_fcl_packages')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_shipment_fcl_packages` (
        `id` INT NOT NULL auto_increment,
        `quantity` INT NOT NULL,
        `description` VARCHAR(255) NOT NULL,
        `fcl_option` VARCHAR(255) NOT NULL,
        `shipment_id` INT NOT NULL,
        `created_at` DATETIME,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`shipment_id`) REFERENCES `' . db_prefix() . '_shipments`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}


// Create table `tbl_shipment_packages`
if (!$CI->db->table_exists(db_prefix() . '_shipment_packages')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_shipment_packages` (
        `id` INT NOT NULL auto_increment,
        `quantity` INT NOT NULL,
        `description` VARCHAR(255) NOT NULL,
        `length` DECIMAL(10, 2) NOT NULL,
        `width` DECIMAL(10, 2) NOT NULL,
        `height` DECIMAL(10, 2) NOT NULL,
        `weight` DECIMAL(10, 2) NOT NULL,
        `weight_volume` DECIMAL(10, 2) NOT NULL,
        `chargeable_weight` DECIMAL(10, 2) NOT NULL,
        `shipment_id` INT NOT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`shipment_id`) REFERENCES `' . db_prefix() . '_shipments`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}


// Create table `tbl_shipment_status_history`
if (!$CI->db->table_exists(db_prefix() . '_shipment_status_history')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_shipment_status_history` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `shipment_id` INT,
        `status_id` INT,
        `changed_at` DATETIME,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}


// Create table `tbl_shipment_stops`
if (!$CI->db->table_exists(db_prefix() . '_shipment_stops')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_shipment_stops` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `shipment_id` INT,
        `departure_point` VARCHAR(255) NOT NULL,
        `destination_point` VARCHAR(255) NOT NULL,
        `description` LONGTEXT NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}


// Create table `tbl_manifest_period`
if (!$CI->db->table_exists(db_prefix() . '_manifest_period')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_manifest_period` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `manifest_number` INT,
        `start_date` VARCHAR(255) NOT NULL,
        `end_date` VARCHAR(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}


// Create table `tbl_dimensional_factor`
if (!$CI->db->table_exists(db_prefix() . '_dimensional_factor')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_dimensional_factor` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `value` INT NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}

// Insert data into `tbl_dimensional_factor`
$CI->db->query("INSERT INTO `" . db_prefix() . "_dimensional_factor` (`id`, `name`, `value`) VALUES
    (1, 'default', 5000),
    (2, 'air_consolidation', 6000),
    (3, 'air_freight', 6000),
    (4, 'sea_lcl', 1000)
    ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `value` = VALUES(`value`);");


// Create table `tbl_deliveries`
if (!$CI->db->table_exists(db_prefix() . '_deliveries')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_deliveries` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `shipment_id` INT,
        `first_name` VARCHAR(255) NOT NULL,
        `last_name` VARCHAR(255) NOT NULL,
        `signature_url` TEXT NOT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`shipment_id`) REFERENCES `' . db_prefix() . '_shipments`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}


// Create table `tbl_commercial_values_items`
if (!$CI->db->table_exists(db_prefix() . '_commercial_values_items')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_commercial_values_items` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `quantity` INT NOT NULL,
        `description` VARCHAR(255) NOT NULL,
        `declared_value` INT NOT NULL,
        `shipment_id` INT NOT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`shipment_id`) REFERENCES `' . db_prefix() . '_shipments`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}

// Create table `tbl_agents`
if (!$CI->db->table_exists(db_prefix() . '_agents')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_agents` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `staff_id` INT NOT NULL,
        `phone_number` VARCHAR(20) NOT NULL,
        `address` TEXT NOT NULL,
        `company_name` TEXT  NULL,
        `id_file_url` TEXT NOT NULL,
        `location_file_url` TEXT  NULL,
        `cert_of_corp_url` TEXT  NULL,
        `kra_file_url` TEXT NOT NULL,
        `country_id` INT(20) NOT NULL,
        `state_id` INT(20) NOT NULL,
        `agent_number` INT NOT NULL,
        `unique_number` VARCHAR(255) NOT NULL,
        `agent_type` ENUM("individual", "company") NOT NULL,
        `status` ENUM("1", "0") NOT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`staff_id`) REFERENCES `' . db_prefix() . 'staff`(`staffid`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}


// Create table `manifests`
if (!$CI->db->table_exists(db_prefix() . '_manifests')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_manifests` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `date` DATE NOT NULL,
        `sender` VARCHAR(255) NOT NULL,
        `rcvr` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(50) NOT NULL,
        `awb_number` VARCHAR(255) NOT NULL,
        `description` TEXT NOT NULL,
        `pcs` INT NOT NULL,
        `kgs` DECIMAL(10, 2) NOT NULL,
        `rate` DECIMAL(10, 2) NOT NULL,
        `aed` DECIMAL(10, 2) NOT NULL,
        `usd` DECIMAL(10, 2) NOT NULL,
        `pack` VARCHAR(50) NOT NULL,
        `dest` VARCHAR(255) NOT NULL,
        `rmks` TEXT,
        `manifest_number` VARCHAR(255) NOT NULL,
        `flight_number` VARCHAR(255) NOT NULL,
        `status` VARCHAR(50) NOT NULL,
        `destination_id` INT NOT NULL,
        `created_at` DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}

// Create table `tbl_courier_audit_logs`
if (!$CI->db->table_exists(db_prefix() . '_courier_audit_logs')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_courier_audit_logs` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `staff_id` INT NOT NULL,
        `country_id` INT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
}

// Check if the table exists
if (!$CI->db->table_exists(db_prefix() . '_destination_offices')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_destination_offices` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `company_name` VARCHAR(255) NOT NULL,
        `location` VARCHAR(255) NOT NULL,
        `street_address` VARCHAR(255) NOT NULL,
        `landmark` VARCHAR(255) NOT NULL,
        `phone_number` VARCHAR(15) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
}


// Create table `tbl_country_states`
if (!$CI->db->table_exists(db_prefix() . '_country_states')) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . '_country_states` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `country_id` INT NOT NULL,
        `country_code` VARCHAR(255) NOT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`country_id`) REFERENCES `' . db_prefix() . 'countries`(`country_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');

    //add states
    addStates($CI);
}


//function to add states
function addStates($CI)
{
    // Load states from JSON file
    $jsonFilePath = __DIR__ . '/assets/states.json'; // Update the path to your JSON file
    $jsonContent = file_get_contents($jsonFilePath);
    $states = json_decode($jsonContent, true);

    // Check for decoding errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        log_message('error', 'Failed to decode JSON: ' . json_last_error_msg());
        return;
    }

    // Define chunk size (500 states per chunk)
    $chunkSize = 500;
    $totalStates = count($states);
    $chunks = array_chunk($states, $chunkSize);

    // Use transaction to ensure data consistency
    $CI->db->trans_start();

    foreach ($chunks as $chunk) {
        $CI->db->insert_batch(db_prefix() . '_country_states', $chunk);
    }

    // Complete the transaction
    $CI->db->trans_complete();

    // Check if the transaction was successful
    if ($CI->db->trans_status() === FALSE) {
        log_message('error', 'Failed to insert states data');
    } else {
        log_message('info', 'States data inserted successfully');
    }


}