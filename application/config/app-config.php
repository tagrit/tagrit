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
    'app.tagrit.com'          => 'production'
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

// Define constants using getenv() with '_DEFAULT' suffix
define('APP_BASE_URL_DEFAULT', getenv('APP_BASE_URL'));
define('APP_DB_USERNAME_DEFAULT', getenv('APP_DB_USERNAME'));
define('APP_DB_PASSWORD_DEFAULT', getenv('APP_DB_PASSWORD'));
define('APP_DB_NAME_DEFAULT', getenv('APP_DB_NAME'));
define('APP_DB_HOSTNAME_DEFAULT', getenv('APP_DB_HOSTNAME') ?: 'localhost');
define('APP_ENC_KEY_DEFAULT', getenv('APP_ENC_KEY'));

// Now define the actual constants, they can either be overridden or kept as defaults
define('APP_BASE_URL', defined('APP_BASE_URL_DEFAULT') ? APP_BASE_URL_DEFAULT : 'https://default-url.com');
define('APP_DB_USERNAME', defined('APP_DB_USERNAME_DEFAULT') ? APP_DB_USERNAME_DEFAULT : 'default_user');
define('APP_DB_PASSWORD', defined('APP_DB_PASSWORD_DEFAULT') ? APP_DB_PASSWORD_DEFAULT : 'default_password');
define('APP_DB_NAME', defined('APP_DB_NAME_DEFAULT') ? APP_DB_NAME_DEFAULT : 'default_db_name');
define('APP_DB_HOSTNAME', defined('APP_DB_HOSTNAME_DEFAULT') ? APP_DB_HOSTNAME_DEFAULT : 'localhost');
define('APP_ENC_KEY', defined('APP_ENC_KEY_DEFAULT') ? APP_ENC_KEY_DEFAULT : 'default_encryption_key');

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
