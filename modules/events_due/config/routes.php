<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
 * General Routes
 **/
$route['admin/events_due/dashboard'] = 'events_due/dashboard/index';

//events
$route['admin/events_due/events/index'] = 'events_due/events/index';
$route['admin/events_due/events/store'] = 'events_due/events/store';
$route['admin/events_due/events/edit'] = 'events_due/events/edit';
$route['admin/events_due/events/update'] = 'events_due/events/update';
$route['admin/events_due/events/view'] = 'events_due/events/view';
$route['admin/events_due/events/event_confirmation'] = 'events_due/events/event_confirmation';
$route['admin/events_due/events/upload_attendance_sheet'] = 'events_due/events/upload_attendance_sheet';

//registrations
$route['admin/events_due/registrations/create'] = 'events_due/registrations/create';
$route['admin/events_due/registrations/store'] = 'events_due/registrations/store';
$route['admin/events_due/registrations/edit/(:num)'] = 'events_due/registrations/edit/$registration_id';
$route['admin/events_due/registrations/update'] = 'events_due/registrations/update';
$route['admin/events_due/registrations/create_unique_code_manually'] = 'events_due/registrations/create_unique_code_manually';

//cronjobs
$route['events_due/cronjobs/send_reminders'] = 'events_due/cronjobs/send_reminders';
$route['events_due/cronjobs/process_queue'] = 'events_due/cronjobs/process_queue';
$route['events_due/cronjobs/send_attendance_list'] = 'events_due/cronjobs/send_attendance_list';

//get venues and locations
$route['admin/events_due/locations'] = 'events_due/events/locations/index';
$route['admin/events_due/venues'] = 'events_due/events/venues/index';
$route['admin/events_due/setups'] = 'events_due/events/setups/index';
$route['admin/events_due/durations'] = 'events_due/events/durations/index';

//settings
$route['admin/events_due/settings/main'] = 'events_due/settings/main';
$route['admin/events_due/settings/upload_excel'] = 'events_due/settings/upload_excel';
$route['admin/events_due/settings/download_sample'] = 'events_due/settings/download_sample';
$route['admin/events_due/settings/set_reminder_period'] = 'events_due/settings/set_reminder_period';

//reports
$route['admin/events_due/reports/main'] = 'events_due/reports/main';
$route['admin/events_due/reports/fetch_filtered_data'] = 'events_due/reports/fetch_filtered_data';
$route['admin/events_due/reports/clear_filters'] = 'events_due/reports/clear_filters';
$route['admin/events_due/reports/save_filters'] = 'events_due/reports/save_filters';
$route['admin/events_due/reports/get_filters'] = 'events_due/reports/get_filters';
$route['admin/events_due/reports/export_filtered_report'] = 'events_due/reports/export_filtered_report';

