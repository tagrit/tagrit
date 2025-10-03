<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: iPay
Description: Module for handling online payments through iPay.
Version: 1.0.0
Requires at least: 2.3.*
Author: Swivernet
*/

define('IPAY_MODULE_NAME', 'ipay');

register_payment_gateway(IPAY_MODULE_NAME.'_gateway', IPAY_MODULE_NAME);

/**
* Register activation module hook
*/
register_activation_hook(IPAY_MODULE_NAME, 'ipay_module_activation_hook');

/**
 * @return [type]
 */
function ipay_module_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(IPAY_MODULE_NAME, [IPAY_MODULE_NAME]);