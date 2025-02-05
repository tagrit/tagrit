<?php

defined('BASEPATH') or exit('No direct script access allowed');

$modal_type = 'module';
$title = _l('perfex_saas_module_marketplace_title');
$subtitle = _l('perfex_saas_module_marketplace_subtitle');

$na = _l('NA');
$modal_id = 'moduleModal';
$subtotal_selector = '.modules-subtotal';
$paid_id_selector = "#paid-modules";
$items_input_name = 'purchased_modules[]';


$allow_request = $allow_module_request;
$request_url = $module_request_url;
$purchased_items  = $purchased_modules;
$items = $modules; // Data for modules

$CI = &get_instance();
$onetime_purchased_invoices = $invoice->onetime_purchased_module_invoice ?? new stdClass();
foreach ($onetime_purchased_invoices as $item_id => $invoice_id) {
    $onetime_purchased_invoices->{$item_id} = $CI->invoices_model->get($invoice_id);
}

include('marketplace_modal.php');
include('marketplace_script.php');