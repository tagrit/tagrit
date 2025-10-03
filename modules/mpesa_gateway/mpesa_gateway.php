<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Mpesa
Description: Mpesa Payment Gateway module (Mpesa express, B2C, Lipa push)
Version: 1.0.0
Requires at least: 2.3.*
Author: ulutfa
Author URI: https://codecanyon.net/user/ulutfa
*/

define('MPESA_GATEWAY_MODULE_NAME', 'mpesa_gateway');

$CI = &get_instance();

/**
 * Register activation module hook
 */
register_activation_hook(MPESA_GATEWAY_MODULE_NAME, 'mpesa_gateway_activation_hook');

function mpesa_gateway_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(MPESA_GATEWAY_MODULE_NAME, [MPESA_GATEWAY_MODULE_NAME]);

/**
 * Actions to inject menus next to the module on module list page
 */
hooks()->add_filter('module_mpesa_gateway_action_links', 'module_mpesa_gateway_action_links');

/**
 * Add additional settings for this module in the module list area
 * @param  array $actions current actions
 * @return array
 */
function module_mpesa_gateway_action_links($actions)
{
    $actions[] = '<a href="' . admin_url('settings?group=payment_gateways&tab=online_payments_mpesa_tab') . '">' . _l('settings') . '</a>';
    $actions[] = '<a href="' . admin_url(MPESA_GATEWAY_MODULE_NAME . '/logs') . '">' . _l('transaction_logs') . '</a>';

    return $actions;
}

/**
 * Actions for injecting mpesa modal and script into the invoice html view.
 */
hooks()->add_action('after_right_panel_invoicehtml', 'mpesa_gateway_html');

/**
 * Add mepsa payment script into invoice page.
 *
 * @param object $invoice
 * @return void
 */
function mpesa_gateway_html($invoice)
{
    $gateway = get_instance()->mpesa_gateway;
    $id = $gateway->getId();
    if (is_payment_mode_allowed_for_invoice($id, $invoice->id)) {
        $ci = get_instance();
        require_once(__DIR__ . '/views/mpesa_gateway.php');
    }
}

//register the module as a payment gateway
register_payment_gateway('mpesa_gateway', 'mpesa_gateway');