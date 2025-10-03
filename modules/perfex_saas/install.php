<?php defined('BASEPATH') or exit('No direct script access allowed');

// Add default setting options 
add_option('perfex_saas_enable_auto_trial', '1');
add_option('perfex_saas_autocreate_first_company', '1');
add_option('perfex_saas_reserved_slugs', 'www,app,deal,controller,master,ww3,hack');
add_option('perfex_saas_control_client_menu', '1');
add_option('perfex_saas_cron_cache', '');
add_option('perfex_saas_tenants_seed_tables', '');
add_option('perfex_saas_sensitive_options', '');
add_option('perfex_saas_enable_single_package_mode', '0');
add_option('perfex_saas_enable_client_menu_in_bridge', '0');
add_option('perfex_saas_enable_custom_module_request', '1');
add_option('perfex_saas_enable_deploy_splash_screen', '1');
add_option('perfex_saas_deploy_splash_screen_theme', 'verbose');
add_option('perfex_saas_enable_client_menu_in_interim_pages', '0');
add_option('perfex_saas_client_bridge_account_menu_position', 'setup');
add_option('perfex_saas_registered_global_active_modules', '[]');
add_option('perfex_saas_masked_settings_pages', '');
add_option('perfex_saas_autolaunch_instance', 'new');
add_option('perfex_saas_restricted_clients_id', '', 1);
add_option('perfex_saas_tenant_seeding_source', 'master');
add_option('perfex_saas_instance_delete_pending_days', 0);
add_option('perfex_saas_allow_customer_cancel_subscription', 1);
add_option('perfex_saas_enable_api', 1);
add_option('perfex_saas_api_allow_public_access_to_doc', 1);

// From v0.1.1
add_option('perfex_saas_enable_client_bridge', '1');
add_option('perfex_saas_enable_cross_domain_bridge', '0');
add_option('perfex_saas_enable_instance_switch', '1');

add_option('perfex_saas_enable_package_grouping', '1');

$custom_domain_guide_for_client = '<p><strong>For Linking Your Root Domain (e.g., yourbusiness.com):<br /></strong></p><ol><li><p><strong>Get Your Domain:</strong> Purchase your domain name from a registrar like Namecheap or GoDaddy (or any of your choice), if you haven\'t already.</p></li><li><p><strong>Access DNS Records:</strong> Log in to your domain registrar\'s website and find the DNS settings section.</p></li><li><p><strong>Configure DNS Settings for Root Domain:<br /></strong></p><ul><li>Add a new "A Record" for the root domain:<br /><ul><li><strong>Host:</strong> @</li><li><strong>Value:</strong> {ip_address}</li><li><strong>TTL:</strong> Automatic<br /><br /></li></ul></li><li>Add another "A Record" for the "www" version (optional):<br /><ul><li><strong>Host:</strong> www</li><li><strong>Value:</strong> {ip_address}</li><li><strong>TTL:</strong> Automatic<br /><br /></li></ul></li></ul></li><li><p><strong>Wait for Changes:</strong> Allow up to 48 hours for the changes to propagate across the internet.</p></li><li><p><strong>Done!:</strong> Once propagated, both your root domain and optional "www" version will be linked to your SaaS platform.<br /><br /><br /></p></li></ol><p><strong>For Linking a Subdomain (e.g., crm.yourbusiness.com):<br /></strong></p><ol><li><p><strong>Create Your Subdomain:</strong></p><ul><li>In the DNS settings section of your registrar\'s website, find the option to create a subdomain.<br /><br /></li><li>Enter "crm" (or your desired prefix) as the subdomain.<br /><br /></li></ul></li><li><p><strong>Access DNS Records:</strong></p><ul><li>Once the subdomain is created, locate the DNS settings for the subdomain.<br /><br /></li></ul></li><li><p><strong>Configure DNS Settings for Subdomain:</strong></p><ul><li>Add a new "CNAME Record" for the subdomain:<br /><ul><li><strong>Host:</strong> crm (or your chosen prefix)</li><li><strong>Value:</strong> {subdomain}</li><li><strong>TTL:</strong> Automatic<br /><br /></li></ul></li><li>Add another "CNAME Record" for the "www" version (optional):<br /><ul><li><strong>Host:</strong> <a href="http://www.crm">www.crm</a> (or your chosen prefix)</li><li><strong>Value:</strong> {subdomain}</li><li><strong>TTL:</strong> Automatic<br /><br /></li></ul></li></ul></li><li><p><strong>Wait for Changes:</strong> Allow up to 48 hours for the changes to propagate.</p></li><li><p><strong>Test Your Subdomain:</strong> After propagation, enter the subdomain URL (e.g., crm.yourbusiness.com) into a browser to ensure it loads correctly.</p></li></ol>';
add_option('perfex_saas_custom_domain_guide', $custom_domain_guide_for_client);

// Create saas module tables

// Create packages table for managing saas packages
if (!$CI->db->table_exists(perfex_saas_table('packages'))) {
    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `" . perfex_saas_table('packages') . "` (
            `id` int NOT NULL AUTO_INCREMENT,
            `name` varchar(150) DEFAULT NULL,
            `description` text,
            `slug` varchar(150) DEFAULT NULL,
            `price` decimal(10,2) DEFAULT '0.00',
            `bill_interval` varchar(150) DEFAULT NULL,
            `is_default` int NOT NULL DEFAULT '0',
            `is_private` int NOT NULL DEFAULT '0',
            `db_scheme` varchar(50) DEFAULT NULL,
            `db_pools` text,
            `status` int NOT NULL DEFAULT '1',
            `modules` text,
            `metadata` text COMMENT 'Extra data such as modules that are shown on package view list',
            `trial_period` int DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `slug` (`slug`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";"
    );
}

// Create companines table for managing instances created
if (!$CI->db->table_exists(perfex_saas_table('companies'))) {
    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `" . perfex_saas_table('companies') . "` (
            `id` int NOT NULL AUTO_INCREMENT,
            `clientid` int NOT NULL,
            `slug` varchar(30) NOT NULL,
            `name` varchar(100) NOT NULL,
            `status` enum('active','inactive','disabled','banned','pending','deploying','pending-delete') NOT NULL DEFAULT 'pending',
            `status_note` TEXT,
            `dsn` text,
            `custom_domain` VARCHAR(150) DEFAULT NULL,
            `metadata` text COMMENT 'Extra data',
            `created_at` datetime DEFAULT NULL,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `slug` (`slug`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";"
    );
}

// Add status note
if (!$CI->db->field_exists('status_note', perfex_saas_table('companies'))) {
    $CI->db->query(
        "ALTER TABLE `" . perfex_saas_table('companies') . "` ADD `status_note` TEXT AFTER `status`;"
    );
}

// Add package relation column to invoices
if (!$CI->db->field_exists(perfex_saas_column('packageid'), db_prefix() . 'invoices')) {
    $CI->db->query(
        "ALTER TABLE `" . db_prefix() . "invoices` ADD `" . perfex_saas_column('packageid') . "` INT NULL DEFAULT NULL AFTER `subscription_id`;"
    );
}

// Add client metadata table
if (!$CI->db->table_exists(perfex_saas_table('client_metadata'))) {
    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `" . perfex_saas_table('client_metadata') . "` (
            `id` int NOT NULL AUTO_INCREMENT,
            `clientid` int NOT NULL,
            `metadata` text COMMENT 'Extra data',
            PRIMARY KEY (`id`),
            UNIQUE KEY `clientid` (`clientid`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";"
    );
}

// Add api users table
if (!$CI->db->table_exists(perfex_saas_table('api_users'))) {
    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `" . perfex_saas_table('api_users') . "` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(50) NOT NULL,
        `token` VARCHAR(150) NOT NULL,
        `permissions` text,
        PRIMARY KEY (`id`),
        UNIQUE KEY `pf_api_users_token` (`token`)
      ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";"
    );
}

// Add email templates
$deployed_template = [
    'type' => 'client',
    'slug' => 'company-instance-deployed',
    'name' => 'Deployed CRM instance',
    'subject' => 'Company CRM Instance created successfully',
    'message' => 'Dear {contact_firstname},<br/><br/>
    I am writing to inform you that we have successfully deployed a CRM instance for your company <b>{instance_name}</b>. 
    <br/><br/>
    You can access your instance at <b>{instance_admin_url}</b>. Kindly log in with your current email and the respective password. 
    <br/><br/>
    Your customer can access from <b>{instance_url}</b>.<br/><br/>
    Please let us know if you have any questions or concerns. We are always happy to help.<br/><br/>
    
    Best regards,<br/>
    {email_signature}'
];

$removed_template = [
    'type' => 'client',
    'slug' => 'company-instance-removed',
    'subject' => 'Company CRM Instance removed',
    'name' => 'Removed CRM instance',
    'message' => '
    Dear {contact_firstname},<br/><br/>
    
    I am writing to inform you that your company <b>{instance_name}</b> has been removed successfully. 
    <br/><br/>
    If this is not from you or your staff, kindly reach out to us.
    <br/><br/>
    Best regards,<br/>
    {email_signature}'
];

$deployed_template_for_admin = [
    'type' => 'staff',
    'slug' => 'company-instance-deployed-for-admin',
    'name' => 'Deployed CRM instance for admin',
    'subject' => 'A CRM Instance was deployed',
    'message' => 'Dear Super Admin,<br/><br/>

    I am writing to inform you that a new instance <b>({instance_name})</b> has been created on your platform for <b>{client_company}</b>. You can check the instance at <b>{instance_url}</b>.
    <br/><br/>
    Best regards.<br/>
    {email_signature}'
];

$removed_template_for_admin = [
    'type' => 'staff',
    'slug' => 'company-instance-removed-for-admin',
    'name' => 'Removed CRM instance for admin',
    'subject' => 'Company CRM Instance removed successfully',
    'message' => 'Dear Super Admin,<br/><br/>

    I am writing to inform you that an instance has been removed from your platform for <b>{client_company}</b>. The name of the instance is <b>{instance_name}</b>.
    <br/><br/>
    Best regards.<br/>
    {email_signature}'
];

$autoremoval_template = [
    'type' => 'client',
    'slug' => 'company-instance-auto-removal-notice',
    'name' => 'Auto removal of inactive CRM instance',
    'subject' => 'Important Notice: Your Company CRM Instance Will Be Removed in {period_left}',
    'message' => '
    Dear {contact_firstname},<br/><br/>
    
    I am writing to inform you that your CRM instance <b>{instance_name} ({instance_slug})</b> has not been accessed by any staff for the past {inactive_period}. 
    <br/><br/>
    Your CRM will be permanently removed in {period_left}. 
    To prevent removal of <b>{instance_name}</b>, 
    kindly login directly now through your staff login page: <a href="{instance_admin_url}">{instance_admin_url}</a> or through your billing portal: <a href="{site_login_url}">{site_login_url}</a>.
    <br/><br/>
    Best regards,<br/>
    {email_signature}'
];


$custom_domain_request_template_for_admin = [
    'type' => 'staff',
    'slug' => 'company-instance-custom-domain-request-for-admin',
    'name' => 'Custom domain request for admin',
    'subject' => 'New Custom Domain Request',
    'message' => 'Dear Super Admin,<br/><br/>
    I am writing to inform you that you have a new custom domain linking request for <b>{instance_name}</b>. 
    <br/>
    Tenant ID: {instance_slug}<br/>
    Custom domain: {instance_custom_domain}<br/>
    Customer: {contact_firstname} - {contact_email}
    <br/><br/>
    You can access the instance directly from <b>{admin_url}saas/companies/edit/{instance_id}</b>.
    <br/><br/>
    Best regards,<br/>
    {email_signature}'
];

$custom_domain_approved_template = [
    'type' => 'client',
    'slug' => 'company-instance-custom-domain-approved',
    'name' => 'Custom domain approved',
    'subject' => 'Custom Domain Added Successfully',
    'message' => 'Dear {contact_firstname},<br/><br/>
    I am writing to inform you that we have successfully link your custom domain ( {instance_custom_domain} ) to your company <b>{instance_name}</b>. 
    <br/><br/>
    You can access your instance at <b>{instance_admin_url}</b>.
    <br/><br/>
    Your customer can access from <b>{instance_url}</b>.<br/><br/>
    {extra_note}<br/>
    Please let us know if you have any questions or concerns. We are always happy to help.<br/><br/>
    Best regards,<br/>
    {email_signature}'
];

$custom_domain_rejected_template = [
    'type' => 'client',
    'slug' => 'company-instance-custom-domain-rejected',
    'name' => 'Custom domain cancelled',
    'subject' => 'Custom Domain Request Cancelled',
    'message' => 'Dear {contact_firstname},<br/><br/>
    I am writing to inform you that we are unable to link your custom domain ( {instance_custom_domain} ) to your company <b>{instance_name}</b> due to the following reason: 
    <br/>
    {extra_note}
    <br/><br/>
    Please ensure you have followed the instructions for linking the domain as stated outlined on your dashboard.
    <br/><br/>
    You can always request new domain at your earliest convenient time.<br/><br/>
    Best regards,<br/>
    {email_signature}'
];

$CI->load->model('emails_model');
$templates = [
    $deployed_template,
    $deployed_template_for_admin,
    $removed_template,
    $removed_template_for_admin,
    $autoremoval_template,
    $custom_domain_request_template_for_admin,
    $custom_domain_approved_template,
    $custom_domain_rejected_template
];
$fromname = '{companyname} | CRM';
foreach ($templates as $t) {
    //this helper check buy slug and create if not exist by slug
    create_email_template($t['subject'], $t['message'], $t['type'], $t['name'], $t['slug']);
}

// Remove and clean old files i.e file renamed that might still exist in tenant folder
$files = [
    'hooks/cpanel.php',
    'libraries/Cpanel_api.php',
    'hooks/contact_permission.php'
];
foreach ($files as $file) {
    $file = __DIR__ . '/' . $file;
    if (file_exists($file))
        @unlink($file);
}