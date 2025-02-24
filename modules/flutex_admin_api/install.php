<?php

defined('BASEPATH') || exit('No direct script access allowed');

add_option('flutex_admin_api_enabled', 1);
add_option('flutex_admin_login_allowed', 1);
add_option('flutex_admin_last_verification', '00-00-0000');

if (table_exists('staff')) {
	if (!get_instance()->db->field_exists('flutex_api_key', db_prefix() . 'staff')) {
	    get_instance()->db->query('ALTER TABLE `' . db_prefix() . 'staff` ADD `flutex_api_key` TEXT NULL DEFAULT NULL AFTER `password`');
	}
}
