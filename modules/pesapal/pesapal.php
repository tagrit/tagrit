<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Pesapal
Description: Module for handling online payments through Pesapal.
Version: 1.0.1
Requires at least: 2.3.*
Author: Swivernet
*/

define('PESAPAL_MODULE_NAME', 'pesapal');

register_payment_gateway(PESAPAL_MODULE_NAME.'_gateway', PESAPAL_MODULE_NAME);

/**
* Register activation module hook
*/
register_activation_hook(PESAPAL_MODULE_NAME, 'pesapal_module_activation_hook');

/**
 * @return [type]
 */
function pesapal_module_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(PESAPAL_MODULE_NAME, [PESAPAL_MODULE_NAME]);