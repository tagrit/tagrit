<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
 * General Routes
 **/
$route['admin/imprest/dashboard'] = 'imprest/dashboard/index';
$route['admin/imprest/email_worker/send_email'] = 'imprest/email_worker/send_email';


//events
$route['admin/imprest/events/index'] = 'imprest/events/index';
$route['admin/imprest/events/create'] = 'imprest/events/create';
$route['admin/imprest/events/store'] = 'imprest/events/store';
$route['admin/imprest/events/edit/(:num)'] = 'imprest/events/edit/$event_id';
$route['admin/imprest/events/update'] = 'imprest/events/update';


//expense categories
$route['admin/imprest/expense_categories'] = 'imprest/expense_categories/index';
$route['admin/imprest/expense_categories/store'] = 'imprest/expense_categories/store';
$route['admin/imprest/expense_categories/add_subcategory'] = 'imprest/expense_categories/add_subcategory';
$route['admin/imprest/expense_categories/delete_subcategory/(:num)'] = 'imprest/expense_categories/delete_subcategory/$subcategory_is';


//fund requests
$route['admin/imprest/fund_requests'] = 'imprest/fund_requests/index';
$route['admin/imprest/fund_requests/create'] = 'imprest/fund_requests/create';
$route['admin/imprest/fund_requests/store'] = 'imprest/fund_requests/store';
$route['admin/imprest/fund_requests/view/(:num)'] = 'imprest/fund_requests/view/$fund_request_id';
$route['admin/imprest/fund_requests/approve/(:num)'] = 'imprest/fund_requests/approve/$fund_request_id';
$route['admin/imprest/fund_requests/reject/(:num)'] = 'imprest/fund_requests/reject/$fund_request_id';
$route['admin/imprest/fund_requests/request_additional_funds'] = 'imprest/fund_requests/request_additional_funds';


//fund reconciliations
$route['admin/imprest/fund_reconciliations/create'] = 'imprest/fund_reconciliations/create';
$route['admin/imprest/fund_reconciliations/edit/(:num)'] = 'imprest/fund_reconciliations/edit/$fund_request_id';
$route['admin/imprest/fund_reconciliations/request/(:num)'] = 'imprest/fund_reconciliations/request/$fund_request_id';
$route['admin/imprest/fund_reconciliations/view/(:num)'] = 'imprest/fund_reconciliations/view/$fund_request_id';
$route['admin/imprest/fund_reconciliations/reconcile/(:num)'] = 'imprest/fund_reconciliations/reconcile/$fund_request_id';
$route['admin/imprest/fund_reconciliations/reject/(:num)'] = 'imprest/fund_reconciliations/reject/$fund_request_id';
$route['admin/imprest/fund_reconciliations/clear/(:num)'] = 'imprest/fund_reconciliations/clear/$fund_request_id';
$route['admin/imprest/fund_reconciliations/cleared_view/(:num)'] = 'imprest/fund_reconciliations/cleared_view/$fund_request_id';
$route['admin/imprest/fund_reconciliations/clear_item'] = 'imprest/fund_reconciliations/clear_item';


//settings
$route['admin/imprest/settings/main'] = 'imprest/settings/main';
$route['admin/imprest/settings/set_max_unreconciled_amount'] = 'imprest/settings/set_max_unreconciled_amount';
$route['admin/imprest/settings/set_email_notification_statuses'] = 'imprest/settings/set_email_notification_statuses';
$route['admin/imprest/settings/set_event_mandatory_fields'] = 'imprest/settings/set_event_mandatory_fields';

