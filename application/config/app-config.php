<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Get the current hostname
$host = $_SERVER['HTTP_HOST'] ?? '';

// Define environment mappings
$environments = [
    'localhost'   => 'local',
    '127.0.0.1'   => 'local',
    'dev'         => 'dev',
    'autoupdate'  => 'autoupdate',
    'staging'     => 'staging',
    'app'         => 'production'
];

// Default to production
$environment = 'production';

foreach ($environments as $key => $env) {
    if (stripos($host, $key) !== false) {
        $environment = $env;
        break;
    }
}

// Define database credentials
$db_config = [
    'local' => [
        'BASE_URL'  => 'http://localhost/tagrit/',
        'USERNAME'  => 'root',
        'PASSWORD'  => '',
        'DB_NAME'   => 'tagrit_new'
    ],
    'dev' => [
        'BASE_URL'  => 'https://dev.tagrit.com/',
        'USERNAME'  => 'tagrit_dev',
        'PASSWORD'  => '?=HeYVENjdEi',
        'DB_NAME'   => 'tagrit_dev'
    ],
    'autoupdate' => [
        'BASE_URL'  => 'https://autoupdate.tagrit.com/',
        'USERNAME'  => 'tagrit_tagrit',
        'PASSWORD'  => 'Y)GxB~MGB8-T',
        'DB_NAME'   => 'tagrit_auto_update'
    ],
    'staging' => [
        'BASE_URL'  => 'https://staging.tagrit.com/',
        'USERNAME'  => 'tagrit_staging',
        'PASSWORD'  => 'CnlKs6btjof&',
        'DB_NAME'   => 'tagrit_staging'
    ],
    'production' => [
        'BASE_URL'  => 'https://app.tagrit.com/',
        'USERNAME'  => 'tagrit_auth',
        'PASSWORD'  => 'Mynewpass123#%',
        'DB_NAME'   => 'tagrit_live'
    ]
];

// Ensure a valid environment is selected; if not, default to production
if (!isset($db_config[$environment])) {
    $environment = 'production';
}

// Define constants
define('APP_BASE_URL_DEFAULT', $db_config[$environment]['BASE_URL']);
define('APP_DB_USERNAME_DEFAULT', $db_config[$environment]['USERNAME']);
define('APP_DB_PASSWORD_DEFAULT', $db_config[$environment]['PASSWORD']);
define('APP_DB_NAME_DEFAULT', $db_config[$environment]['DB_NAME']);
define('APP_DB_HOSTNAME_DEFAULT', 'localhost');
define('APP_ENC_KEY', '85bec75a1a6136881a01c08b1fdc31d8');

/**
 * @since  2.3.0
 * Database charset
 */
define('APP_DB_CHARSET', 'utf8mb4');

/**
 * @since  2.3.0
 * Database collation
 */
define('APP_DB_COLLATION', 'utf8mb4_unicode_ci');

/**
 *
 * Session handler driver
 * By default the database driver will be used.
 *
 * For files session use this config:
 * define('SESS_DRIVER', 'files');
 * define('SESS_SAVE_PATH', NULL);
 * In case you are having problem with the SESS_SAVE_PATH consult with your hosting provider to set "session.save_path" value to php.ini
 *
 */
define('SESS_DRIVER', 'database');
define('SESS_SAVE_PATH', 'sessions');
define('APP_SESSION_COOKIE_SAME_SITE_DEFAULT', 'Lax');

/**
 * Enables CSRF Protection
 */
define('APP_CSRF_PROTECTION', true);//perfex-saas:start:app-config.php
//dont remove/change above line
require_once(FCPATH . 'modules/perfex_saas/config/app-config.php');
//dont remove/change below line
//perfex-saas:end:app-config.php