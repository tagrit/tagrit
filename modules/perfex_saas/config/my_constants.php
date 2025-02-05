<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Define the base upload folder
 */
$upload_folder = PERFEX_SAAS_UPLOAD_BASE_DIR;
$tenants_upload_folder = $upload_folder . 'tenants/';

$master_base_path = FCPATH . $upload_folder;
$storage_base_path = $master_base_path;

if (perfex_saas_is_tenant()) {

    $tenant = perfex_saas_tenant();
    $tenant_slug = $tenant->slug;
    perfex_saas_handle_tenant_404_static_file_request($tenant, $upload_folder, $tenants_upload_folder);

    $storage_base_path = FCPATH . $tenants_upload_folder;
    if (!file_exists($storage_base_path))
        perfex_saas_create_dir($storage_base_path);

    $storage_base_path .= $tenant_slug . '/';
    if (!file_exists($storage_base_path))
        perfex_saas_create_dir($storage_base_path);
}

define('PERFEX_SAAS_TENANT_UPLOAD_BASE_FOLDER', $storage_base_path);

$attachment_folders = [

    // Customer attachments folder from profile
    'CLIENT_ATTACHMENTS_FOLDER' => 'clients' . '/',

    // All tickets attachments
    'TICKET_ATTACHMENTS_FOLDER' => 'ticket_attachments' . '/',

    // Company attachments, favicon, logo etc..
    'COMPANY_FILES_FOLDER' => 'company' . '/',

    // Staff profile images
    'STAFF_PROFILE_IMAGES_FOLDER' => 'staff_profile_images' . '/',

    // Contact profile images
    'CONTACT_PROFILE_IMAGES_FOLDER' => 'client_profile_images' . '/',

    // Newsfeed attachments
    'NEWSFEED_FOLDER' => 'newsfeed' . '/',

    // Contracts attachments
    'CONTRACTS_UPLOADS_FOLDER' => 'contracts' . '/',

    // Tasks attachments
    'TASKS_ATTACHMENTS_FOLDER' => 'tasks' . '/',

    // Invoice attachments
    'INVOICE_ATTACHMENTS_FOLDER' => 'invoices' . '/',

    // Estimate attachments
    'ESTIMATE_ATTACHMENTS_FOLDER' => 'estimates' . '/',

    // Proposal attachments
    'PROPOSAL_ATTACHMENTS_FOLDER' => 'proposals' . '/',

    // Expenses receipts
    'EXPENSE_ATTACHMENTS_FOLDER' => 'expenses' . '/',

    // Lead attachments
    'LEAD_ATTACHMENTS_FOLDER' => 'leads' . '/',

    // Project files attachments
    'PROJECT_ATTACHMENTS_FOLDER' => 'projects' . '/',

    // Project discussions attachments
    'PROJECT_DISCUSSION_ATTACHMENT_FOLDER' => 'discussions' . '/',

    // Credit notes attachment folder
    'CREDIT_NOTES_ATTACHMENTS_FOLDER' => 'credit_notes' . '/',
];

/**
 * Define the constants
 */
foreach ($attachment_folders as $key => $folder_name) {
    $path = $storage_base_path . $folder_name;
    define($key, $path);
    if (!file_exists($path)) {
        perfex_saas_create_dir($path, $blueprint = $master_base_path . $folder_name);
    }
}

/**
 * Modules Path
 */
define('APP_MODULES_PATH', FCPATH . 'modules/');
/**
 * Helper libraries path
 */
define('LIBSPATH', APPPATH . 'libraries/');