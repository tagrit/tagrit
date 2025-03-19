<?php

$method = $_SERVER['REQUEST_METHOD'];

// Authentication
$route['flutex_admin_api/auth/login'] = 'authentication/login';                     // POST: Staff Login Request 
$route['flutex_admin_api/auth/logout']  = 'profile/logout';                         // POST: Staff Logout Request
$route['flutex_admin_api/auth/forgot-password'] = 'authentication/forgotPassword';         // POST: Request to Reset Staff Password

// Dashboard
$route['flutex_admin_api/dashboard'] = 'dashboard/dashboard';                       // GET: Request Dashboard Information
$route['flutex_admin_api/overview'] = 'dashboard/overview';                         // GET: Request System Overview Information
$route['flutex_admin_api/notifications'] = 'dashboard/notifications';               // GET: Request Notifications

// Profile
$route['flutex_admin_api/profile'] = 'profile/profile';                             // GET: Request Staff Information

// Projects
$route['flutex_admin_api/projects'] = 'projects/projects';                          // GET: List All Projects                   // POST: Add New Project
$route['flutex_admin_api/projects/id/(:num)']  = 'projects/projects/id/$1';         // GET: Request Project Information         // PUT: Update Project Information
$route['flutex_admin_api/projects/id/(:num)/group/(:any)']   = 'projects/projects/id/$1/group/$2'; // GET: Request Project Group Information
$route['flutex_admin_api/projects/search/(:any)']  = 'projects/search/search/$1';   // GET: Search Projects
$route['flutex_admin_api/projects/create'] = 'projects/create';                     // GET: Request Project Create Data

// Tasks
$route['flutex_admin_api/tasks'] = 'tasks/tasks';                                   // GET: List All Tasks                      // POST: Add New Task
$route['flutex_admin_api/tasks/id/(:num)']  = 'tasks/tasks/id/$1';                  // GET: Request Task Information            // PUT: Update Task Information
$route['flutex_admin_api/tasks/search/(:any)']  = 'tasks/search/search/$1';         // GET: Search Tasks

// Customers
$route['flutex_admin_api/customers'] = 'customers/customers';                       // GET: List All Customers                  // POST: Add New Customer
$route['flutex_admin_api/customers/id/(:num)']  = 'customers/customers/id/$1';      // GET: Request Customer Information        // PUT: Update Customer Information
$route['flutex_admin_api/customers/search/(:any)']  = 'customers/search/search/$1'; // GET: Search Customers
$route['flutex_admin_api/contacts/id/(:num)']  = 'customers/contacts/id/$1';        // GET: List All Customer Contacts          // POST: Add New Customer Contact
$route['flutex_admin_api/contacts/id/(:num)/contact/(:num)']  = 'customers/contacts/id/$1/contact/$2';  // GET: Request Contact Information

// Leads
$route['flutex_admin_api/leads'] = 'leads/leads';                                   // GET: List All Leads                      // POST: Add New Leads
$route['flutex_admin_api/leads/id/(:num)']  = 'leads/leads/id/$1';                  // GET: Request Lead Information            // PUT: Update Lead Information
$route['flutex_admin_api/leads/search/(:any)']  = 'leads/search/search/$1';         // GET: Search Leads

// Contracts
$route['flutex_admin_api/contracts'] = 'contracts/contracts';                       // GET: List All Contracts                   // POST: Add New Contract
$route['flutex_admin_api/contracts/id/(:num)']  = 'contracts/contracts/id/$1';      // GET: Request Contract Information         // PUT: Update Contract Information
$route['flutex_admin_api/contracts/search/(:any)']  = 'contracts/search/search/$1'; // GET: Search Contracts

// Proposals
$route['flutex_admin_api/proposals'] = 'proposals/proposals';                       // GET: List All Proposals                   // POST: Add New Proposal
$route['flutex_admin_api/proposals/id/(:num)']  = 'proposals/proposals/id/$1';      // GET: Request Proposal Information         // PUT: Update Proposal Information
$route['flutex_admin_api/proposals/search/(:any)']  = 'proposals/search/search/$1'; // GET: Search Proposals

// Estimates
$route['flutex_admin_api/estimates'] = 'estimates/estimates';                       // GET: List All Estimates                   // POST: Add New Estimate
$route['flutex_admin_api/estimates/id/(:num)']  = 'estimates/estimates/id/$1';      // GET: Request Estimate Information         // PUT: Update Estimate Information
$route['flutex_admin_api/estimates/search/(:any)']  = 'estimates/search/search/$1'; // GET: Search Estimates

// Invoices
$route['flutex_admin_api/invoices'] = 'invoices/invoices';                          // GET: List All Invoices                    // POST: Add New Invoice
$route['flutex_admin_api/invoices/id/(:num)']  = 'invoices/invoices/id/$1';         // GET: Request Invoice Information          // PUT: Update Invoice Information
$route['flutex_admin_api/invoices/search/(:any)']  = 'invoices/search/search/$1';   // GET: Search Invoices

// Tickets
$route['flutex_admin_api/tickets'] = 'tickets/tickets';                             // GET: List All Tickets                     // POST: Add New Ticket
$route['flutex_admin_api/tickets/id/(:num)']  = 'tickets/tickets/id/$1';            // GET: Request Ticket Information           // PUT: Update Ticket Information
$route['flutex_admin_api/tickets/search/(:any)']  = 'tickets/search/search/$1';     // GET: Search Tickets

// Items
$route['flutex_admin_api/items'] = 'items/items';                                   // GET: List All Items                       // POST: Add New Item
$route['flutex_admin_api/items/id/(:num)']  = 'items/items/id/$1';                  // GET: Request Item Information             // PUT: Update Item Information
$route['flutex_admin_api/items/search/(:any)']  = 'items/search/search/$1';         // GET: Search Items

// Payments
$route['flutex_admin_api/payments'] = 'payments/payments';                          // GET: List All Payments                    // POST: Add New Payment
$route['flutex_admin_api/payments/id/(:num)']  = 'payments/payments/id/$1';         // GET: Request Payment Information          // PUT: Update Payment Information
$route['flutex_admin_api/payments/search/(:any)']  = 'payments/search/search/$1';   // GET: Search Payments

// Miscellaneous
$route['flutex_admin_api/miscellaneous/client_groups']        = 'miscellaneous/client_groups';        // GET: Request Client Groups
$route['flutex_admin_api/miscellaneous/payment_modes']        = 'miscellaneous/payment_modes';        // GET: Request Payment Modes
$route['flutex_admin_api/miscellaneous/expense_categories']   = 'miscellaneous/expense_categories';   // GET: Request Expense Categories
$route['flutex_admin_api/miscellaneous/tax_data']             = 'miscellaneous/tax_data';             // GET: Request Tax Data
$route['flutex_admin_api/miscellaneous/leads_sources']        = 'miscellaneous/leads_sources';        // GET: Request Leads Sources
$route['flutex_admin_api/miscellaneous/leads_statuses']       = 'miscellaneous/leads_statuses';       // GET: Request Leads Statuses
$route['flutex_admin_api/miscellaneous/proposal_statuses']    = 'miscellaneous/proposal_statuses';    // GET: Request Proposal Statuses
$route['flutex_admin_api/miscellaneous/ticket_departments']   = 'miscellaneous/ticket_departments';   // GET: Request Ticket Departments
$route['flutex_admin_api/miscellaneous/ticket_priorities']    = 'miscellaneous/ticket_priorities';    // GET: Request Ticket Priorities
$route['flutex_admin_api/miscellaneous/ticket_services']      = 'miscellaneous/ticket_services';      // GET: Request Ticket Services
$route['flutex_admin_api/miscellaneous/currencies']           = 'miscellaneous/currencies';           // GET: Request Currencies
$route['flutex_admin_api/miscellaneous/countries']            = 'miscellaneous/countries';            // GET: Request Countries




