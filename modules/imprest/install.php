<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Create table `tbl_settings`
if (!$CI->db->table_exists(db_prefix() . '_settings')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . '_settings` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `fund_reconciliation` TEXT NOT NULL,
        `events` TEXT NOT NULL,
        `custom_fields` TEXT NOT NULL,
        `notifications` TEXT NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Serialize the data
$fundReconciliationSettings = serialize(['max_unreconciled_amount' => '200000']);
$customFieldsSettings = serialize([
    'staff_custom_id' => '1',
    'event_custom_id' => '2'
]);
$notificationStatuses = serialize([
    'Funds Requested',
    'Approved',
    'Rejected',
    'Reconciliation Rejected',
    'Cleared',
]);

$eventsMandatoryField = serialize([
    'name',
    'venue',
    'organization',
    'dates',
    'delegates_details',
    'trainers',
    'facilitator',
    'revenue',
]);

// Define the data to insert
$data = [
    'fund_reconciliation' => $fundReconciliationSettings,
    'events' => $eventsMandatoryField,
    'custom_fields' => $customFieldsSettings,
    'notifications' => $notificationStatuses
];

// Insert the data into the `tbl_settings` table
$CI->db->insert(db_prefix() . '_settings', $data);

// Define the email template data
$email_templates_data = [
    [
        'type' => 'notifications',
        'slug' => 'fund-request-updated',
        'language' => 'english',
        'name' => 'Fund Request Updated (sent staff member)',
        'subject' => 'Fund Request Status Updated',
        'message' => '<p>Hello staff_name,<br><br>We wanted to inform you that your Fund Request fund_request_number has been updated. New Status: status.<br><br>Login to see more details.</p>
     <p>Kind Regards,<br><br></p>',
        'fromname' => '{companyname} | CRM',
        'plaintext' => 0,
        'active' => 1,
        'order' => 0
    ],
    [
        'type' => 'notifications',
        'slug' => 'funds-requested',
        'language' => 'english',
        'name' => 'New Fund Request (sent admin)',
        'subject' => 'New Fund Request',
        'message' => '<p>Hello staff_name,<br><br>We wanted to inform you that there is a new Fund Request fund_request_number .<br><br>Login to see more details.</p>
     <p>Kind Regards,<br><br></p>',
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


// Create table `tbl_events_details`
if (!$CI->db->table_exists(db_prefix() . '_events_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . '_events_details` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `venue` VARCHAR(255) NULL,
        `start_date` DATE NULL,
        `end_date` DATE NULL,
        `no_of_delegates` INT NULL,
        `charges_per_delegate` DECIMAL(15,2) NULL,
        `division` VARCHAR(255) NULL,
        `trainers` TEXT NOT NULL,
        `facilitator` VARCHAR(255) NULL,
        `organization` VARCHAR(255) NULL,
        `revenue` DECIMAL(15,2) NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Create table `tbl_events`
if (!$CI->db->table_exists(db_prefix() . '_events')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . '_events` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Create table `tbl_expense_categories`
if (!$CI->db->table_exists(db_prefix() . '_expense_categories')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . '_expense_categories` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        PRIMARY KEY (`id`),
       UNIQUE KEY `unique_name` (`name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Create table `tbl_expense_subcategories`
if (!$CI->db->table_exists(db_prefix() . '_expense_subcategories')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . '_expense_subcategories` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `category_id` INT NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_name` (`name`),
        FOREIGN KEY (`category_id`) REFERENCES `' . db_prefix() . '_expense_categories`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Create table `tbl_fund_requests`
if (!$CI->db->table_exists(db_prefix() . '_fund_requests')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . '_fund_requests` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `fund_request_date` DATE NOT NULL,
        `event_id` INT NOT NULL,
        `event_detail_id` INT NULL,  
        `requested_by` INT NOT NULL,
        `rejection_reason` TEXT NOT NULL,
        `reference_no` VARCHAR(255) NULL,
        `status` ENUM("pending_approval", "approved", "rejected", "pending_reconciliation", "reconciliation_ongoing", "cleared", "reconciliation_rejected") NOT NULL DEFAULT "pending_approval",
        `additional_fund_request` ENUM("0", "1") NOT NULL DEFAULT "0",
        PRIMARY KEY (`id`),
        FOREIGN KEY (`event_detail_id`) REFERENCES `' . db_prefix() . '_events_details`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`event_id`) REFERENCES `' . db_prefix() . '_events`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}


// Create table `tbl_fund_request_items`
if (!$CI->db->table_exists(db_prefix() . '_fund_request_items')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . '_fund_request_items` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `fund_request_id` INT NOT NULL,
        `expense_subcategory_id` INT NOT NULL,
        `amount_requested` DECIMAL(15,2) NOT NULL,
        `receipt_url` VARCHAR(255),
        `cleared` TINYINT(1) NOT NULL DEFAULT 0, -- Added cleared column with default value 0
        PRIMARY KEY (`id`),
        FOREIGN KEY (`fund_request_id`) REFERENCES `' . db_prefix() . '_fund_requests`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Create table `tbl_speaker_details`
if (!$CI->db->table_exists(db_prefix() . '_speaker_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . '_speaker_details` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `fund_request_id` INT NOT NULL,
        `speaker_name` VARCHAR(255) NOT NULL,
        `rate_per_day` DECIMAL(15,2) NOT NULL,
        `number_of_days` INT NOT NULL,
        `total` DECIMAL(15,2),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`fund_request_id`) REFERENCES `' . db_prefix() . '_fund_requests`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Create table `tbl_hotel_conferencing_details`
if (!$CI->db->table_exists(db_prefix() . '_hotel_conferencing_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . '_hotel_conferencing_details` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `fund_request_id` INT NOT NULL,
        `hotel_name` VARCHAR(255) NOT NULL,
        `amount_per_person` DECIMAL(15,2) NOT NULL,
        `number_of_days` INT NOT NULL,
        `number_of_persons` INT NOT NULL,
        `total` DECIMAL(15,2),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`fund_request_id`) REFERENCES `' . db_prefix() . '_fund_requests`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Create table `tbl_hotel_conferencing_details`
if (!$CI->db->table_exists(db_prefix() . '_hotel_accommodation_details')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . '_hotel_accommodation_details (
        id INT NOT NULL AUTO_INCREMENT,
        fund_request_id INT NOT NULL,
        hotel_name VARCHAR(255) NOT NULL,
        amount_per_person DECIMAL(15,2) NOT NULL,
        dinner DECIMAL(15,2) NOT NULL,
        number_of_nights INT NOT NULL,
        number_of_persons INT NOT NULL,
        total DECIMAL(15,2),
        PRIMARY KEY (id),
        FOREIGN KEY (fund_request_id) REFERENCES ' . db_prefix() . '_fund_requests(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Create table `tbl_additional_funds_details`
if (!$CI->db->table_exists(db_prefix() . '_additional_funds_details')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . '_additional_funds_details (
        id INT NOT NULL AUTO_INCREMENT,
        fund_request_id INT NOT NULL,
        reason VARCHAR(255) NOT NULL,
        amount DECIMAL(15,2) NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (fund_request_id) REFERENCES ' . db_prefix() . '_fund_requests(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}


// Insert example data for `tbl_expense_categories`
$CI->db->query('INSERT INTO `' . db_prefix() . '_expense_categories` (`name`) VALUES
("Transport"),
("Printing"),
("Hotel Conferencing"),
("Hotel Accommodation"),
("Airtime Allowance"),
("Client Gifts"),
("Speaker Costs"),
("Miscellaneous"),
("CPD Allowance"),
("Additional Funds");');

$CI->db->query('INSERT INTO `' . db_prefix() . '_expense_subcategories` (`name`, `category_id`) VALUES
-- Category ID 1
("Flight Tickets", 1),
("Taxi Charges", 1),
("Fuel Allowance", 1),
("SGR Tickets", 1),

-- Category ID 2
("Conference Folders", 2),
("Certificates", 2),
("Nametags", 2),
("Registration Sheets", 2),
("Allowance Notes", 2),

-- Category ID 3
("Conferencing Amount", 3),

-- Category ID 4
("Accommodation Amount", 4),

-- Category ID 5
("Airtime Allowance", 5),

-- Category ID 6
("Cost per Bag", 6),
("Cost Per Hoodie", 6),
("Cost per Jackets", 6),
("Cost per Other Item", 6),

-- Category ID 7
("Speaker Costs", 7),

-- Category ID 8
("Mpesa Transactions", 8),
("Other Costs1", 8),
("Other Costs2", 8),
("CPD Allowance", 9),
("Additional Funds", 10);');

// Example data for categories
$categories = [
    ['name' => 'Transport', 'description' => 'Transport'],
    ['name' => 'Printing', 'description' => 'Printing'],
    ['name' => 'Hotel Conferencing', 'description' => 'Hotel Conferencing'],
    ['name' => 'Hotel Accommodation', 'description' => 'Hotel Accommodation'],
    ['name' => 'Airtime Allowance', 'description' => 'Airtime Allowance'],
    ['name' => 'Client Gifts', 'description' => 'Client Gifts'],
    ['name' => 'Speaker Costs', 'description' => 'Speaker Costs'],
    ['name' => 'Miscellaneous', 'description' => 'Miscellaneous'],
    ['name' => 'CPD Allowance', 'description' => 'CPD Allowance'],
    ['name' => 'Additional Funds', 'description' => 'Additional Funds'],
];

// Loop through each category
foreach ($categories as $category) {
    // Check if the category already exists
    $existingCategory = $CI->db->get_where(db_prefix() . 'expenses_categories', ['name' => $category['name']])->row();

    if (!$existingCategory) {
        // Insert the category if it does not exist
        $CI->db->insert(db_prefix() . 'expenses_categories', $category);
    }
}


//loop through category and create necessary accounts
foreach ($categories as $category) {

    // Check if the category already exists in acc_accounts
    $existingCategory = $CI->db->get_where(db_prefix() . 'acc_accounts', ['name' => $category['name']])->row();

    if (!$existingCategory) {
        // Insert the category into acc_account_type_details
        $CI->db->insert(db_prefix() . 'acc_account_type_details', [
            'name' => $category['name'],
            'account_type_id' => 14,
            'note' => $category['name'],
        ]);

        // Get the inserted ID
        $account_detail_type_id = $CI->db->insert_id();

        // Insert into acc_accounts using the retrieved account_detail_type_id
        $CI->db->insert(db_prefix() . 'acc_accounts', [
            'name' => $category['name'],
            'account_type_id' => 14,
            'account_detail_type_id' => $account_detail_type_id,
            'active' => 1,
        ]);
    }
}


if (!$CI->db->field_exists('reference_no', db_prefix() . 'acc_account_history')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'acc_account_history` 
        ADD COLUMN `reference_no` VARCHAR(255);');
}


