<?php

defined('BASEPATH') or exit('No direct script access allowed');

$modal_type = 'service';
$title = _l('perfex_saas_service_marketplace_title');
$subtitle = _l('perfex_saas_service_marketplace_subtitle');

$na = _l('NA');
$modal_id = 'serviceModal';
$subtotal_selector = '.services-subtotal';
$paid_id_selector = "#paid-services";
$items_input_name = 'purchased_services[]';

$allow_request = $allow_service_request;
$request_url = $service_request_url;
$purchased_items  = $purchased_services;
$items = $services; // Data for services

$CI = &get_instance();
$onetime_purchased_invoices = $invoice->onetime_purchased_service_invoice ?? new stdClass();
foreach ($onetime_purchased_invoices as $item_id => $invoice_id) {
    $onetime_purchased_invoices->{$item_id} = $CI->invoices_model->get($invoice_id);
}

include('marketplace_modal.php');
include('marketplace_script.php');