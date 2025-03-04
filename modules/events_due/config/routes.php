<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
 * General Routes
 **/
$route['admin/events_due/dashboard'] = 'events_due/dashboard/index';


//events
$route['admin/events_due/events'] = 'events_due/events/index';
$route['admin/events_due/events/create'] = 'events_due/events/create';
$route['admin/events_due/events/store'] = 'events_due/events/store';
$route['admin/events_due/events/edit/(:num)'] = 'events_due/events/edit/$event_id';
$route['admin/events_due/events/update'] = 'events_due/events/update';
$route['admin/events_due/events/store_event_name'] = 'events_due/events/store_event_name';
$route['admin/events_due/events/store'] = 'events_due/events/store';




//registrations
$route['admin/events_due/registrations'] = 'events_due/registrations/index';
$route['admin/events_due/registrations/create'] = 'events_due/registrations/store';
$route['admin/events_due/registrations/store'] = 'events_due/registrations/store';
$route['admin/events_due/registrations/edit/(:num)'] = 'events_due/registrations/edit/$registration_id';
$route['admin/events_due/registrations/update'] = 'events_due/registrations/update';

//settings
$route['admin/events_due/settings/main'] = 'events_due/settings/main';
$route['admin/events_due/settings/import_client_event_registration'] = 'events_due/settings/import_client_event_registration';

//reports
$route['admin/events_due/reports/main'] = 'events_due/reports/main';

