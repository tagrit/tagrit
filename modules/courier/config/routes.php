<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
 * General Routes
 **/
$route['admin/courier/dashboard'] = 'courier/dashboard';
$route['admin/courier/states'] = 'courier/states';
$route['courier/tracking'] = 'tracker/tracking';
$route['courier/tracking/shipment_info'] = 'tracker/shipment_info';

/*
 * Shipments Routes
 **/
$route['admin/courier/shipments/main'] = 'courier/shipments/main';
$route['admin/courier/shipments/dashboard'] = 'courier/shipments/dashboard';
$route['admin/courier/shipments'] = 'courier/shipments/index';
$route['admin/courier/shipments/create'] = 'courier/shipments/create';
$route['admin/courier/shipments/delete'] = 'courier/shipments/delete';
$route['admin/courier/shipments/store'] = 'courier/shipments/store';
$route['admin/courier/shipments/list_invoices'] = 'courier/shipments/list_invoices';
$route['admin/courier/shipments/waybill/(:num)'] = 'courier/shipments/waybill/$1';
$route['admin/courier/shipments/commercial_invoice/(:num)'] = 'courier/shipments/commercial_invoice/$1';
$route['admin/courier/shipments/update_status/(:num)'] = 'courier/shipments/update_status/$1';
$route['admin/courier/shipments/manifest'] = 'courier/shipments/manifest';
$route['admin/courier/shipments/generate_manifest'] = 'courier/shipments/generate_manifest';
$route['admin/courier/shipments/insert_manifest'] = 'courier/shipments/insert_manifest';
$route['admin/courier/shipments/filter_shipments'] = 'courier/shipments/filter_shipments';
$route['admin/courier/shipments/clear_filters'] = 'courier/shipments/clear_filters';


/*
 * Pickups Routes
 **/
$route['admin/courier/pickups/main'] = 'courier/pickups/main';
$route['admin/courier/pickups/dashboard'] = 'courier/pickups/dashboard';
$route['admin/courier/pickups'] = 'courier/pickups/index';
$route['admin/courier/pickups/create'] = 'courier/pickups/create';
$route['admin/courier/pickups/store'] = 'courier/pickups/store';
$route['admin/courier/pickups/delete/(:num)'] = 'courier/pickups/delete/$1';
$route['admin/courier/pickups/update_status'] = 'courier/pickups/update_status';
$route['admin/courier/pickups/view/(:num)'] = 'courier/pickups/view/$1';



/*
 * Courier Companies Routes
 **/
$route['admin/courier/companies'] = 'courier/companies/index';
$route['admin/courier/companies/main'] = 'courier/companies/main';
$route['admin/courier/companies/dashboard'] = 'courier/companies/dashboard';
$route['admin/courier/companies/create'] = 'courier/companies/create';
$route['admin/courier/companies/store'] = 'courier/companies/store';
$route['admin/courier/companies/delete/(:num)'] = 'courier/companies/delete/$1';



/*
 * Agents Routes
 **/
$route['admin/courier/agents/main'] = 'courier/agents/main';
$route['admin/courier/agents'] = 'courier/agents/index';
$route['admin/courier/agents/create'] = 'courier/agents/create';
$route['admin/courier/agents/store'] = 'courier/agents/store';


/*
 * Manifests Routes
 **/
$route['admin/courier/manifests'] = 'courier/manifests/index';
$route['admin/courier/manifests/store'] = 'courier/manifests/store';
$route['admin/courier/manifests/view/(:num)'] = 'courier/manifests/view/$i';



/*
 * Agents Routes
 **/
$route['admin/courier/agents/main'] = 'courier/agents/main';
$route['admin/courier/agents'] = 'courier/agents/index';
$route['admin/courier/agents/create'] = 'courier/agents/create';
$route['admin/courier/agents/store'] = 'courier/agents/store';
$route['admin/courier/agents/agent_number'] = 'courier/agents/agent_number';
$route['admin/courier/agents/sync_role_permissions'] = 'courier/agents/sync_role_permissions';
$route['admin/courier/agents/delete/(:num)'] = 'courier/agents/delete/$i';




/*
 * Settings Routes
 **/
$route['admin/courier/settings/main'] =  'courier/settings/main';
$route['admin/courier/settings/dimensional_factor'] =  'courier/settings/dimensional_factor';
