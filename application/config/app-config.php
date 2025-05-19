<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Get current host
$host = $_SERVER['HTTP_HOST'] ?? '';

// Whitelisted hosts and their environment mappings
$environments = [
    'localhost'               => 'local',
    '127.0.0.1'               => 'local',
    'dev.tagrit.com'          => 'development',
    'autoupdate.tagrit.com'   => 'auto-update',
    'staging.tagrit.com'      => 'staging',
    'erp.tagrit.com'          => 'production'
];

// Default to production if host is unrecognized
$environment = $environments[$host] ?? 'production';

// Load corresponding env file
$env_file_path = __DIR__ . "/.env.$environment.php";
if (file_exists($env_file_path)) {
    require_once $env_file_path;
} else {
    die("Missing environment config file: .env.$environment.php");
}

// Define constants using getenv()
define('APP_BASE_URL_DEFAULT', getenv('APP_BASE_URL'));
define('APP_DB_USERNAME_DEFAULT', getenv('APP_DB_USERNAME'));
define('APP_DB_PASSWORD_DEFAULT', getenv('APP_DB_PASSWORD'));
define('APP_DB_NAME_DEFAULT', getenv('APP_DB_NAME'));
define('APP_DB_HOSTNAME_DEFAULT', getenv('APP_DB_HOSTNAME') ?: 'localhost');
define('APP_ENC_KEY', getenv('APP_ENC_KEY'));

// Charset and collation
define('APP_DB_CHARSET', 'utf8mb4');
define('APP_DB_COLLATION', 'utf8mb4_unicode_ci');

// Session handling
define('SESS_DRIVER', 'database');
define('SESS_SAVE_PATH', 'sessions');
define('APP_SESSION_COOKIE_SAME_SITE_DEFAULT', 'Lax');

// CSRF protection
define('APP_CSRF_PROTECTION', true);

// Perfex SaaS config (unchanged)
require_once(FCPATH . 'modules/perfex_saas/config/app-config.php');
