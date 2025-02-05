<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Define global constants
defined('PERFEX_SAAS_MODULE_NAME') or define('PERFEX_SAAS_MODULE_NAME', 'perfex_saas');
defined('PERFEX_SAAS_MODULE_NAME_SHORT') or define('PERFEX_SAAS_MODULE_NAME_SHORT', 'ps');
defined('PERFEX_SAAS_MODULE_WHITELABEL_NAME') or define('PERFEX_SAAS_MODULE_WHITELABEL_NAME', 'saas');

/**@deprecated 0.0.6 */
defined('PERFEX_SAAS_TENANT_COLUMN') or define('PERFEX_SAAS_TENANT_COLUMN', 'perfex_saas_tenant_id');

defined('PERFEX_SAAS_ROUTE_ID') or define('PERFEX_SAAS_ROUTE_ID', 'ps');
defined('PERFEX_SAAS_FILTER_TAG') or define('PERFEX_SAAS_FILTER_TAG', 'psaas');
defined('APP_DB_DRIVER') or define('APP_DB_DRIVER', 'mysqli');
defined('PERFEX_SAAS_MAX_SLUG_LENGTH') or define('PERFEX_SAAS_MAX_SLUG_LENGTH', 20);

/** @var string Perfex CRM base upload folder with trailing slash */
defined('PERFEX_SAAS_UPLOAD_BASE_DIR') or define('PERFEX_SAAS_UPLOAD_BASE_DIR', 'uploads/');

// Tenant recognition modes
defined('PERFEX_SAAS_TENANT_MODE_PATH') or define('PERFEX_SAAS_TENANT_MODE_PATH', 'path');
defined('PERFEX_SAAS_TENANT_MODE_DOMAIN') or define('PERFEX_SAAS_TENANT_MODE_DOMAIN', 'custom_domain');
defined('PERFEX_SAAS_TENANT_MODE_SUBDOMAIN') or define('PERFEX_SAAS_TENANT_MODE_SUBDOMAIN', 'subdomain');

/** @var string[] List of options field that will should not be controlled by tenants i.e security fields */
defined('PERFEX_SAAS_ENFORCED_SHARED_FIELDS') or define('PERFEX_SAAS_ENFORCED_SHARED_FIELDS', ['allowed_files', 'ticket_attachments_file_extensions']);

/** @var string[] List of dangerous extensions */
defined('PERFEX_SAAS_DANGEROUS_EXTENSIONS') or define('PERFEX_SAAS_DANGEROUS_EXTENSIONS', [
    ".php", ".exe", ".sh", ".bat", ".cmd", ".js", ".vbs",
    ".py", ".pl", ".jsp", ".aspx", ".cgi", ".htaccess", ".ini", ".dll", ".java", ".applet"
]);


defined('PERFEX_SAAS_CRON_PROCESS_MODULE') or define('PERFEX_SAAS_CRON_PROCESS_MODULE', 'module-update');
defined('PERFEX_SAAS_CRON_PROCESS_SINGLE_TENANT_MODULE') or define('PERFEX_SAAS_CRON_PROCESS_SINGLE_TENANT_MODULE', 'module-update-single-tenant');
defined('PERFEX_SAAS_CRON_PROCESS_PACKAGE') or define('PERFEX_SAAS_CRON_PROCESS_PACKAGE', 'package-update');

defined('PERFEX_SAAS_GLOBAL_ACTIVE_MODULES_OPTION_KEY') or define('PERFEX_SAAS_GLOBAL_ACTIVE_MODULES_OPTION_KEY', 'perfex_saas_registered_global_active_modules');

/** Route prefix */
defined('PERFEX_SAAS_ROUTE_NAME') or define('PERFEX_SAAS_ROUTE_NAME', 'saas');

defined('PERFEX_SAAS_UPDATE_URL') or define('PERFEX_SAAS_UPDATE_URL', 'https://perfextosaas.com/evanto.php?purchase_code=[PC]&action=[AC]&module=[MD]');

defined('PERFEX_SAAS_MINIMUM_AUTO_INSTANCE_REMOVE_GRACE_PERIOD') or define('PERFEX_SAAS_MINIMUM_AUTO_INSTANCE_REMOVE_GRACE_PERIOD', 7); // 7 days

/** Seed source flags */
defined('PERFEX_SAAS_SEED_SOURCE_FILE') or define('PERFEX_SAAS_SEED_SOURCE_FILE', 'file');
defined('PERFEX_SAAS_SEED_SOURCE_TENANT') or define('PERFEX_SAAS_SEED_SOURCE_TENANT', 'tenant');
defined('PERFEX_SAAS_SEED_SOURCE_MASTER') or define('PERFEX_SAAS_SEED_SOURCE_MASTER', 'master');

/** Status flags */
defined('PERFEX_SAAS_STATUS_PENDING') or define('PERFEX_SAAS_STATUS_PENDING', 'pending');
defined('PERFEX_SAAS_STATUS_PENDING_DELETE') or define('PERFEX_SAAS_STATUS_PENDING_DELETE', 'pending-delete');
defined('PERFEX_SAAS_STATUS_ACTIVE') or define('PERFEX_SAAS_STATUS_ACTIVE', 'active');
defined('PERFEX_SAAS_STATUS_INACTIVE') or define('PERFEX_SAAS_STATUS_INACTIVE', 'inactive');
defined('PERFEX_SAAS_STATUS_BANNED') or define('PERFEX_SAAS_STATUS_BANNED', 'banned');
defined('PERFEX_SAAS_STATUS_DEPLOYING') or define('PERFEX_SAAS_STATUS_DEPLOYING', 'deploying');