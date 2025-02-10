<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Sage Accounting integration
Description: Sage Accounting integration
Version: 1.0.0
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
*/

define('SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME', 'sage_accounting_integration');
define('SAGE_ACCOUNTING_INTEGRATION_REVISION', 100);

hooks()->add_action('after_cron_run', 'cron_sage_accounting_integrations');
hooks()->add_action('admin_init', 'sage_accounting_integration_module_init_menu_items');
hooks()->add_action('admin_init', 'sage_accounting_integration_permissions');
hooks()->add_action('app_admin_head', 'sage_accounting_integration_head_components');
hooks()->add_action('app_admin_footer', 'sage_accounting_integration_add_footer_components');
hooks()->add_action('sage_accounting_integration_init',SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME.'_appint');
hooks()->add_action('pre_activate_module', SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME.'_preactivate');
hooks()->add_action('pre_deactivate_module', SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME.'_predeactivate');

/**
 * Register activation module hook
 */
register_activation_hook(SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME, 'sage_accounting_integration_module_activation_hook');

function sage_accounting_integration_module_activation_hook() {
	$CI = &get_instance();
	require_once __DIR__ . '/install.php';
}

/**
* Load the module helper
*/
$CI = & get_instance();
$CI->load->helper(SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME . '/sage_accounting_integration');

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME, [SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME]);

/**
 * Init sage_accounting_integration module menu items in setup in admin_init hook
 * @return null
 */
function sage_accounting_integration_module_init_menu_items() {
	if (has_permission('sage_accounting_integration', '', 'view')) {
		$CI = &get_instance();
		$CI->app_menu->add_sidebar_menu_item('sage_accounting_integration', [
			'name' => _l('sage_accounting_integration'),
			'icon' => 'fa fa-calendar',
			'position' => 30,
		]);

		$CI->app_menu->add_sidebar_children_item('sage_accounting_integration', [
			'slug' => 'sage_accounting-integration-management',
			'name' => _l('management'),
			'icon' => 'fa fa-list',
			'href' => admin_url('sage_accounting_integration/manage?group=customers'),
			'position' => 1,
		]);

		$CI->app_menu->add_sidebar_children_item('sage_accounting_integration', [
			'slug' => 'sage_accounting-integration-sync-logs',
			'name' => _l('sync_logs'),
			'icon' => 'fa fa-list',
			'href' => admin_url('sage_accounting_integration/sync_logs'),
			'position' => 1,
		]);

		$CI->app_menu->add_sidebar_children_item('sage_accounting_integration', [
			'slug' => 'sage_accounting-integration-settings',
			'name' => _l('settings'),
			'icon' => 'fa fa-cog',
			'href' => admin_url('sage_accounting_integration/setting'),
			'position' => 2,
		]);
	}
}


/**
 * resource workload permissions
 * @return
 */
function sage_accounting_integration_permissions() {
	$capabilities = [];

	$capabilities['capabilities'] = [
		'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
	];

	register_staff_capabilities('sage_accounting_integration', $capabilities, _l('sage_accounting_integration'));
}

/**
 * add head components
 */
function sage_accounting_integration_head_components() {
	$CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];
	if (!(strpos($viewuri, 'admin/sage_accounting_integration/manage?group=expenses') === false)) {
		echo '<link href="' . module_dir_url(SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME, 'assets/css/box_loading.css') . '?v=' . SAGE_ACCOUNTING_INTEGRATION_REVISION . '"  rel="stylesheet" type="text/css" />';
	}    

	if (!(strpos($viewuri, 'admin/sage_accounting_integration/manage?group=invoices') === false)) {
		echo '<link href="' . module_dir_url(SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME, 'assets/css/box_loading.css') . '?v=' . SAGE_ACCOUNTING_INTEGRATION_REVISION . '"  rel="stylesheet" type="text/css" />';
	}

	if (!(strpos($viewuri, 'admin/sage_accounting_integration/manage?group=payments') === false)) {
		echo '<link href="' . module_dir_url(SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME, 'assets/css/box_loading.css') . '?v=' . SAGE_ACCOUNTING_INTEGRATION_REVISION . '"  rel="stylesheet" type="text/css" />';
	}  

	if (!(strpos($viewuri, 'admin/sage_accounting_integration/manage?group=customers') === false)) {
		echo '<link href="' . module_dir_url(SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME, 'assets/css/box_loading.css') . '?v=' . SAGE_ACCOUNTING_INTEGRATION_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
}

/**
 * add footer components
 * @return
 */
function sage_accounting_integration_add_footer_components() {
	$CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];
	if (!(strpos($viewuri, 'admin/sage_accounting_integration/manage?group=expenses') === false)) {
		echo '<script src="' . module_dir_url(SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME, 'assets/js/integrations/expenses.js') . '?v=' . SAGE_ACCOUNTING_INTEGRATION_REVISION . '"></script>';
	}    

	if (!(strpos($viewuri, 'admin/sage_accounting_integration/manage?group=invoices') === false)) {
		echo '<script src="' . module_dir_url(SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME, 'assets/js/integrations/invoices.js') . '?v=' . SAGE_ACCOUNTING_INTEGRATION_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/sage_accounting_integration/manage?group=payments') === false)) {
		echo '<script src="' . module_dir_url(SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME, 'assets/js/integrations/payments.js') . '?v=' . SAGE_ACCOUNTING_INTEGRATION_REVISION . '"></script>';
	}  

	if (!(strpos($viewuri, 'admin/sage_accounting_integration/manage?group=customers') === false)) {
		echo '<script src="' . module_dir_url(SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME, 'assets/js/integrations/customers.js') . '?v=' . SAGE_ACCOUNTING_INTEGRATION_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/sage_accounting_integration/sync_logs') === false)) {
		echo '<script src="' . module_dir_url(SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME, 'assets/js/sync_logs.js') . '?v=' . SAGE_ACCOUNTING_INTEGRATION_REVISION . '"></script>';
	}
}

function cron_sage_accounting_integrations(){
	$CI = &get_instance();

	if(get_option('acc_integration_sage_accounting_active') == 1 && get_option('acc_integration_sage_accounting_initialized') == 1){
        $CI->load->model('sage_accounting_integration/sage_accounting_integration_model');
        $CI->sage_accounting_integration_model->init_sage_accounting_config();

        if(get_option('acc_integration_sage_accounting_sync_from_system') == 1){
            $CI->sage_accounting_integration_model->create_sage_accounting_customer();
            $CI->sage_accounting_integration_model->create_sage_accounting_invoice();
            $CI->sage_accounting_integration_model->create_sage_accounting_payment();
            $CI->sage_accounting_integration_model->create_sage_accounting_expense();
        }

        if(get_option('acc_integration_sage_accounting_sync_to_system') == 1){
            $CI->sage_accounting_integration_model->get_sage_accounting_customer();
            $CI->sage_accounting_integration_model->get_sage_accounting_invoice();
            $CI->sage_accounting_integration_model->get_sage_accounting_payment();
            $CI->sage_accounting_integration_model->get_sage_accounting_expense();
        }
    }
}
function sage_accounting_integration_appint(){
    $CI = & get_instance();    
    require_once 'libraries/gtsslib.php';
    $si_api = new SageAccountingLic();
    $si_gtssres = $si_api->verify_license(true);    
    if(!$si_gtssres || ($si_gtssres && isset($si_gtssres['status']) && !$si_gtssres['status'])){
         $CI->app_modules->deactivate(SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME);
        set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        redirect(admin_url('modules'));
    }    
}

function sage_accounting_integration_preactivate($module_name){
    if ($module_name['system_name'] == SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME) {             
        require_once 'libraries/gtsslib.php';
        $si_api = new SageAccountingLic();
        $si_gtssres = $si_api->verify_license();          
        if(!$si_gtssres || ($si_gtssres && isset($si_gtssres['status']) && !$si_gtssres['status'])){
             $CI = & get_instance();
            $data['submit_url'] = $module_name['system_name'].'/gtsverify/activate'; 
            $data['original_url'] = admin_url('modules/activate/'.SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME); 
            $data['module_name'] = SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME; 
            $data['title'] = "Module License Activation"; 
            echo $CI->load->view($module_name['system_name'].'/activate', $data, true);
            exit();
        }        
    }
}

function sage_accounting_integration_predeactivate($module_name){
    if ($module_name['system_name'] == SAGE_ACCOUNTING_INTEGRATION_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $si_api = new SageAccountingLic();
        $si_api->deactivate_license();
    }
}