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
            `location_id` INT NOT NULL,
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

// Create table `{database_prefix}_notification_queue`
if (!$CI->db->table_exists(db_prefix() . '_notification_queue')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . '_notification_queue` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `type` VARCHAR(50) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `client_name` VARCHAR(255) NOT NULL,
        `event_name` VARCHAR(255) NOT NULL,
        `event_date` DATE NOT NULL,
        `event_location` VARCHAR(255) NOT NULL,
        `status` ENUM("pending", "sent") NOT NULL DEFAULT "pending",
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Create table `{database_prefix}email_reminder_period`
if (!$CI->db->table_exists(db_prefix() . 'email_reminder_period')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'email_reminder_period` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `days` INT NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Create table `event_attendance_sheets`
if (!$CI->db->table_exists(db_prefix() . 'event_attendance_sheets')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'event_attendance_sheets` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `event_id` INT NOT NULL,
        `location` VARCHAR(255) NOT NULL,
        `venue` VARCHAR(255) NOT NULL,
        `attendance_url` TEXT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Check if `start_date` column exists
$columns = $CI->db->list_fields(db_prefix() . 'event_attendance_sheets');
if (!in_array('start_date', $columns)) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'event_attendance_sheets` ADD COLUMN `start_date` DATE NULL;');
}

// Check if `end_date` column exists
if (!in_array('end_date', $columns)) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'event_attendance_sheets` ADD COLUMN `end_date` DATE NULL;');
}

$CI->db->query("DELETE FROM " . db_prefix() . "events_due_locations");

// Insert locations
$locations = [
    'Diani', 'Mombasa', 'Machakos', 'Nakuru', 'Naivasha',
    'Kisumu', 'Thika', 'Eldoret', 'Dubai', 'Arusha',
    'Malaysia', 'Singapore'
];

foreach ($locations as $location) {
    // Check if the location already exists
    $CI->db->where('name', $location);
    $query = $CI->db->get(db_prefix() . 'events_due_locations');

    // If the location does not exist, insert it
    if ($query->num_rows() == 0) {
        $CI->db->insert(db_prefix() . 'events_due_locations', ['name' => $location]);
    }
}


// Fetch location IDs for mapping
$location_ids = [];
$location_results = $CI->db->get(db_prefix() . 'events_due_locations')->result();
foreach ($location_results as $location) {
    $location_ids[$location->name] = $location->id;
}

$CI->db->query("DELETE FROM " . db_prefix() . "events_due_venues");


// Venues with their corresponding locations
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

// Insert venues with the correct location_id
foreach ($venues as $venue) {
    if (isset($location_ids[$venue['location']])) {
        $CI->db->insert(db_prefix() . 'events_due_venues', [
            'name' => $venue['name'],
            'location_id' => $location_ids[$venue['location']]
        ]);
    }
}


// Define the email template data
$email_templates_data = [
    [
        'type' => 'notifications',
        'slug' => 'event-due-registration',
        'language' => 'english',
        'name' => 'Event Registration (sent client)',
        'subject' => 'Event Registration',
        'message' => '<p>Hello client,<br><br>We wanted to inform you that your registration for event_name is successful, Kindly check the document below for further details.</p>
     <p>Kind Regards,<br><br></p>',
        'fromname' => '{companyname} | CRM',
        'plaintext' => 0,
        'active' => 1,
        'order' => 0
    ],
    [
        'type' => 'notifications',
        'slug' => 'event-reminder',
        'language' => 'english',
        'name' => 'Event Reminder (sent client)',
        'subject' => 'Upcoming Event Reminder',
        'message' => '<p>Hello client_name,<br><br>
        This is a friendly reminder about your upcoming event: <strong>event_name</strong>.<br>
        The event is scheduled for <strong>event_date</strong> at <strong>event_location</strong>.<br><br>
        Please confirm your attendance and get back in case your want to require additional information.<br><br>
        Looking forward to your participation!<br><br>
        Kind Regards,<br>',
        'fromname' => '{companyname} | CRM',
        'plaintext' => 0,
        'active' => 1,
        'order' => 0
    ]

];

// Loop through each email template
foreach ($email_templates_data as $template) {
    $existing_template = $CI->db->get_where(db_prefix() . 'emailtemplates', [
        'slug' => $template['slug'],
        'language' => $template['language']
    ])->row();

    if (!$existing_template) {
        $CI->db->insert(db_prefix() . 'emailtemplates', $template);
    }
}

// Create table `event_unique_codes`
if (!$CI->db->table_exists(db_prefix() . 'event_unique_codes')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'event_unique_codes` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `event_id` INT NOT NULL,
        `event_unique_code` VARCHAR(100) NOT NULL,
        `location` VARCHAR(255) NOT NULL,
        `venue` VARCHAR(255) NOT NULL,
        `start_date` DATE NOT NULL,
        `end_date` DATE NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Get the event details
$event_details = $CI->db->get(db_prefix() . '_events_details')->result();
foreach ($event_details as $event) {

    if (empty($event->location)) {
        $event->location = $event->venue;
    }

    // Prepare the data to insert or update
    $data = [
        'event_id' => $event->event_id,
        'location' => $event->location,
        'venue' => $event->venue,
        'start_date' => $event->start_date,
        'end_date' => $event->end_date,
    ];

    // Check if the event already exists
    $CI->db->where('event_id', $event->event_id);
    $CI->db->where('location', $event->location);
    $CI->db->where('venue', $event->venue);
    $CI->db->where('start_date', $event->start_date);
    $CI->db->where('end_date', $event->end_date);
    $query = $CI->db->get(db_prefix() . 'event_unique_codes');

    if ($query->num_rows() > 0) {
        // If the event exists, update it
        $CI->db->where('event_id', $event->event_id);
        $CI->db->where('location', $event->location);
        $CI->db->where('venue', $event->venue);
        $CI->db->where('start_date', $event->start_date);
        $CI->db->where('end_date', $event->end_date);
        $CI->db->update(db_prefix() . 'event_unique_codes', $data);
    } else {
        // If the event does not exist, insert it
        $data['event_unique_code'] = generateEventUniqueCode($event->event_id, $event->venue, $event->location, $event->start_date);
        $CI->db->insert(db_prefix() . 'event_unique_codes', $data);
    }
}


// Method to generate unique event code
function generateEventUniqueCode($event_id, $venue, $location, $start_date)
{
    // Retrieve the event details
    $CI = &get_instance(); // Get the CI instance
    $event = $CI->db->get_where(db_prefix() . '_events', ['id' => $event_id])->row();

    if (!$event) {
        return null;
    }

    // Ensure all values are non-null and default to empty strings if necessary
    $eventName = isset($event->name) ? $event->name : '';
    $venue = isset($venue) ? $venue : '';
    $location = isset($location) ? $location : '';

    // Clean and format inputs
    $eventPart = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $eventName), 0, 4));
    $venuePart = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $venue), 0, 3));
    $locationPart = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $location), 0, 3));
    $startDatePart = date('dmy', strtotime($start_date));

    // Combine to create the code
    return "{$eventPart}-{$venuePart}-{$locationPart}-{$startDatePart}";
}
