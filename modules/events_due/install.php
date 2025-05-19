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
        `client_list` TEXT NOT NULL,
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
    'Kisumu', 'Thika', 'Eldoret', 'Dubai','Nanyuki',
    'Singapore', 'Nairobi', 'Turkey', 'Dubai', 'Uganda',
    'Tanzania', 'Rwanda'
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

    //mombasa venues
    ['name' => 'Sarova WhiteSands Hotel', 'location' => 'Mombasa'],
    ['name' => 'Voyager Beach Resort Hotel', 'location' => 'Mombasa'],
    ['name' => 'Bamburi Beach Hotel', 'location' => 'Mombasa'],
    ['name' => 'Travellers Beach Hotel', 'location' => 'Mombasa'],

    //Nairobi venues
    ['name' => 'Nairobi Safari Club', 'location' => 'Nairobi'],
    ['name' => 'Clarion Hotel', 'location' => 'Nairobi'],
    ['name' => 'HillPark Hotel', 'location' => 'Nairobi'],

    //Diani venues
    ['name' => 'Baobab Hotel', 'location' => 'Diani'],
    ['name' => 'Leopard Beach Resort', 'location' => 'Diani'],

    //Nanyuki venues
    ['name' => 'Nanyuki', 'location' => 'Nanyuki'],

    //Machakos venues
    ['name' => 'Seo Hotel', 'location' => 'Machakos'],
    ['name' => 'Maanzoni Hotel', 'location' => 'Machakos'],
    ['name' => 'Maanzoni Lodge', 'location' => 'Machakos'],

    //Naivasha venues
    ['name' => 'Blooming Suites Hotel', 'location' => 'Naivasha'],
    ['name' => 'Eseriani Hotel/Resort', 'location' => 'Naivasha'],
    ['name' => 'Lake Naivasha Resort', 'location' => 'Naivasha'],

    //Nakuru venues
    ['name' => 'Sarova Woodlands Hotel', 'location' => 'Nakuru'],
    ['name' => 'Ole Ken Hotel', 'location' => 'Nakuru'],
    ['name' => 'Merica Hotel', 'location' => 'Nakuru'],

    //Kisumu venues
    ['name' => 'Imperial Sarova Hotel', 'location' => 'Kisumu'],
    ['name' => 'Imperial Express Hotel', 'location' => 'Kisumu'],

    //Thika venues
    ['name' => 'The Luke Hotel', 'location' => 'Thika'],
    ['name' => 'Thika Green Golf Hotel', 'location' => 'Thika'],

    //Eldoret venues
    ['name' => 'Boma Inn Hotel', 'location' => 'Eldoret'],
    ['name' => 'Eka Hotel', 'location' => 'Eldoret'],

    //Singapore venues
    ['name' => 'Ibis Bencoolen', 'location' => 'Singapore'],

    //Turkey venues
    ['name' => 'Novotel İstanbul Bosphorus', 'location' => 'Turkey'],
    ['name' => 'Radisson Blue, Turkey', 'location' => 'Turkey'],
    ['name' => 'Radisson Sisli', 'location' => 'Turkey'],

    //Dubai venues
    ['name' => 'Grand Excelsior Hotel', 'location' => 'Dubai'],
    ['name' => 'Avani Hotel', 'location' => 'Dubai'],
    ['name' => 'Hilton Garden Inn Al Muraqabat', 'location' => 'Dubai'],
    ['name' => 'Land Mark Hotel', 'location' => 'Dubai'],

    //Uganda Hotel
    ['name' => 'Hotel African', 'location' => 'Uganda'],
    ['name' => 'Golden Tullip', 'location' => 'Uganda'],

    //Tanzania
    ['name' => 'Mt Meru Arusha', 'location' => 'Tanzania'],

    //Rwanda
    ['name' => 'Onomo Hotel', 'location' => 'Rwanda'],
    ['name' => 'Park Inn by Radison Blue', 'location' => 'Rwanda'],

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
        'subject' => 'event_name, date, location',
        'message' => '
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=Generator content="Microsoft Word 15 (filtered)">
<style>
@font-face { font-family:"Cambria Math"; }
@font-face { font-family:Calibri; }
@font-face { font-family:Aptos; }
@font-face { font-family:"Century Gothic"; }
p.MsoNormal, li.MsoNormal, div.MsoNormal {
    margin:0in;
    font-size:11.0pt;
    font-family:"Calibri",sans-serif;
}
a:link, span.MsoHyperlink {
    color:#0563C1;
    text-decoration:underline;
}
p {
    margin-right:0in;
    margin-left:0in;
    font-size:12.0pt;
    font-family:"Aptos",sans-serif;
}
</style>
</head>
<body lang=EN-US link="#0563C1" vlink="#954F72" style="word-wrap:break-word">
<div>
<p style="margin:0in;text-align:justify"><b><span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:#2E75B6">Dear client_name,</span></b></p>
<p><b><i><u><span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:#2E75B6;background:white">THIS EVENT </span></u></i></b><a href="https://www.capabuil.com/wp-content/uploads/2023/06/CAPABUIL-LTD-JUL-DEC-2023-NITA-Certified-CPD-Calendar-New-version.pdf"><b><i><span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:#2E75B6;background:white"><span style="color:windowtext">WILL</span></span></i></b></a><b><i><u><span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:#2E75B6;background:white"> NOT BE CANCELLED OR POSTPONED</span></u></i></b></p>
<p style="margin-right:26.05pt"><em><span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:#222222;background:white;font-style:normal">We acknowledge your dedication to expand your professional horizons in the journey of career growth and development; thank you for choosing us.</span></em></p>
<p style="margin-right:26.05pt"><em><b><span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:#2E75B6;background:white;font-style:normal">TRAINING:</span></b></em> <em><span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:#222222;background:white;font-style:normal">event_name</span></em></p>
<p style="margin-right:18.95pt"><em><b><span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:#2E75B6;background:white;font-style:normal">DATES: </span></b></em> <em><b><span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:black;background:white;font-style:normal">date</span></b></em></p>
<p style="margin-right:18.95pt"><em><b><span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:#2E75B6;background:white;font-style:normal">VENUE: </span></b></em> <em><b><span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:black;background:white;font-style:normal">location</span></b></em></p>
<p><em><span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:#222222;background:white;font-style:normal">We have attached: the <b>Course Content, Invitation Letter</b> &amp; <b>Proforma Invoice</b> to help you/your colleagues in getting the necessary approvals for the above-mentioned training.</span></em></p>
<p style="margin-right:18.95pt"><a href="https://www.capabuil.com/download-cpd-calenders/?external=1"><b><span style="color:windowtext">DOWNLOAD CPD TRAINING CALENDARS</span></b></a> <em><span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:#222222;background:white;font-style:normal">to access the full listing of our competency <b>NITA &amp; ODPC APPROVED</b> programs </span></em> <em><b><span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:#2E75B6;background:white;font-style:normal">designed to meet your researched needs, rather than standard packages.</span></b></em></p>
<p style="text-align:justify">
  <em>
    <span style="font-size:10.0pt;font-family:\'Century Gothic\',sans-serif;color:#222222;background:white;font-style:normal">
      Don\'t hesitate to contact us anytime for more information/support in your learning/development - 
      <span style="color:#2E75B6;font-weight:bold">0722-998-105</span> / 
      <span style="color:#2E75B6;font-weight:bold">0717-165-425</span> / 
      <span style="color:#2E75B6;font-weight:bold">0712-843-395</span> day/ night.
    </span>
  </em>
</p>
</div>
</body>
</html>',
        'fromname' => '{companyname}',
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
        'message' => '
    <html> 
    <head>
    <meta http-equiv=Content-Type content="text/html; charset=windows-1252">
    <style>
    p { font-family:"Century Gothic", sans-serif; font-size:10pt; line-height:1.5; }
    </style>
    </head>
    <body>
    <p><b>Dear client_name,</b></p>
    <p>We remain grateful for your trust in our affordable quality services.</p>

    <p><b><u style="color:#0070C0;">ATTENDANCE CONFIRMATION</u></b></p>

    <p>Kindly confirm the Payment and Attendance Status for the training starting next week from <b>date</b> at <b>location</b>.</p>

    <p><b>Event Name:</b> event_name</p>

    <p><b>Expected Attendees:</b><br>
    client_list
    </p>

    <p><b><u style="color:#0070C0;">PAYMENT STATUS</u></b></p>

    <p>As part of the business capacity recovery measures, the management has approved a policy to require clients to pay for services and seminars upfront for the upcoming months to enable stabilization of business sustainability and funding performance indicators.</p>

    <p>Upon approval of the training, we will also require <b>LSO/Commitment Letter</b> from your organization for record purposes.</p>

    <p>Kindly share the <b>Payment evidence</b> with our finance team via mail <a href="mailto:customerservice@capabuil.com">customerservice@capabuil.com</a> to enable us firm up the resource facilitation by our training host as well as other service providers.</p>

    <p>Kind regards,</p>
    </body>
    </html>
    ',
        'fromname' => '{companyname}',
        'plaintext' => 0,
        'active' => 1,
        'order' => 0
    ],
    [
        'type' => 'notifications',
        'slug' => 'event-status-notification',
        'language' => 'english',
        'name' => 'Event Status Update (sent to staff)',
        'subject' => 'Client Event Status List',
        'message' => '
<html> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<style>
p { font-family:"Century Gothic", sans-serif; font-size:10pt; line-height:1.5; }
</style>
</head>
<body>

<p>event_status_content</p>

<p>Regards,<br>{companyname}</p>
</body>
</html>
',
        'fromname' => '{companyname}',
        'plaintext' => 0,
        'active' => 1,
        'order' => 0
    ]
];

// Loop through each email template
foreach ($email_templates_data as $template) {
    $CI->db->where([
        'slug' => $template['slug'],
        'language' => $template['language']
    ])->delete(db_prefix() . 'emailtemplates');

    $CI->db->insert(db_prefix() . 'emailtemplates', $template);
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
        'event_unique_code' => generateEventUniqueCode($event->event_id, $event->venue, $event->location, $event->start_date),
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
