<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: File Sharing
Description: Centralize and share your documents with your team, your clients and your partners
Version: 1.1.0
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
*/

define('FILE_SHARING_MODULE_NAME', 'file_sharing');
define('FILE_SHARING_MODULE_UPLOAD_FOLDER', module_dir_path(FILE_SHARING_MODULE_NAME, 'uploads'));
define('FILE_SHARING_PATH', 'modules/file_sharing/uploads/');
define('FILE_SHARING_FOLDER_NAME', 'Main File Sharing');
define('FILE_SHARING_MEDIA_PATH', 'modules/file_sharing/uploads/'.FILE_SHARING_FOLDER_NAME);

hooks()->add_action('admin_init', 'file_sharing_permissions');
hooks()->add_action('app_customers_footer', 'file_sharing_client_add_footer_components');
hooks()->add_action('app_customers_head', 'file_sharing_client_add_head_components');
hooks()->add_action('customers_navigation_end', 'file_sharing_module_init_client_menu_items');
hooks()->add_action('app_admin_head', 'file_sharing_add_head_component');
hooks()->add_action('app_admin_footer', 'file_sharing_load_js');
hooks()->add_action('fs_public_head', 'fs_public_load_css');
hooks()->add_action('fs_public_footer', 'fs_public_load_js');
hooks()->add_action('admin_init', 'file_sharing_module_init_menu_items');
hooks()->add_action('file_sharing_init',FILE_SHARING_MODULE_NAME.'_appint');
hooks()->add_action('pre_activate_module', FILE_SHARING_MODULE_NAME.'_preactivate');
hooks()->add_action('pre_deactivate_module', FILE_SHARING_MODULE_NAME.'_predeactivate');
hooks()->add_action('pre_uninstall_module', FILE_SHARING_MODULE_NAME.'_uninstall');

define('FILE_SHARING_REVISION', 110);

//mail theme
register_merge_fields('file_sharing/merge_fields/fs_share_staff_merge_fields');

/**
 * Register activation module hook
 */

register_activation_hook(FILE_SHARING_MODULE_NAME, 'file_sharing_module_activation_hook');
$CI = &get_instance();

$CI->load->helper(FILE_SHARING_MODULE_NAME . '/File_sharing');

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(FILE_SHARING_MODULE_NAME, [FILE_SHARING_MODULE_NAME]);

/**
 * spreadsheet online module activation hook
 */
function file_sharing_module_activation_hook()
{
    $CI = &get_instance();
    require_once __DIR__ . '/install.php';
}

/**
 * init add head component
 */
function file_sharing_add_head_component()
{
    $CI      = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if (!(strpos($viewuri, '/admin/file_sharing/manage') === false)) {
        echo '<link href="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/css/main.css') . '?v=' . FILE_SHARING_REVISION . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/css/setting.css') . '?v=' . FILE_SHARING_REVISION . '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, '/admin/file_sharing/setting') === false)) {
        echo '<link href="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/css/setting.css') . '?v=' . FILE_SHARING_REVISION . '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, '/admin/file_sharing/sharing') === false)) {
        echo '<link href="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/css/setting.css') . '?v=' . FILE_SHARING_REVISION . '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, '/admin/file_sharing/reports') === false)) {
        echo '<link href="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/css/setting.css') . '?v=' . FILE_SHARING_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
}

/**
 * init add footer component
 */
function file_sharing_load_js()
{
    $CI          = &get_instance();
    $viewuri     = $_SERVER['REQUEST_URI'];
    $mediaLocale = get_media_locale();

    if (!(strpos($viewuri, 'admin/file_sharing/manage') === false)) {
        echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/plugins/elFinder-2.1.57/js/elfinder.full.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';
        if($mediaLocale == 'jp'){
            $mediaLocale = 'ja';
        }
        if($mediaLocale != 'en'){
            echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/plugins/elFinder-2.1.57/js/i18n/elfinder.'.$mediaLocale.'.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';    
        }
    }

    if (!(strpos($viewuri, 'admin/file_sharing/sharing') === false)) {
        echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/js/sharing.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/file_sharing/setting') === false)) {
        echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/js/setting.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/file_sharing/download_management') === false)) {
        echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/js/download_management.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/file_sharing/reports') === false)) {
        echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';
        echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';
        echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';
        echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';
        echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';
        echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';
    }
}

function file_sharing_client_add_head_components()
{
    $CI      = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    if (!(strpos($viewuri, 'file_sharing/file_sharing_client') === false)) {
        echo '<link href="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/css/main.css') . '?v=' . FILE_SHARING_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
}

function file_sharing_client_add_footer_components()
{
    $CI      = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    $mediaLocale = get_media_locale();
    
    if (!(strpos($viewuri, 'file_sharing/file_sharing_client') === false)) {
        if($mediaLocale == 'jp'){
            $mediaLocale = 'ja';
        }
        echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/plugins/elFinder-2.1.57/jquery-ui.min.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';
        echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/plugins/elFinder-2.1.57/js/elfinder.full.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';
        if($mediaLocale != 'en'){
            echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/plugins/elFinder-2.1.57/js/i18n/elfinder.'.$mediaLocale.'.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';
        }
        echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/plugins/elFinder-2.1.57/require.min.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';
        echo '<script src="' . module_dir_url(FILE_SHARING_MODULE_NAME, 'assets/js/elfinder_client.js') . '?v=' . FILE_SHARING_REVISION . '"></script>';

    }

}

function fs_public_load_css()
{
    $CI      = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    echo '<link rel="stylesheet" href="'. site_url('assets/css/style.min.css'). '?v='.FILE_SHARING_REVISION.'">';
    echo '<link rel="stylesheet" href="'. site_url('assets/builds/vendor-admin.css'). '?v='.FILE_SHARING_REVISION.'">';
}

function fs_public_load_js()
{
    $CI      = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    echo '<script src="' . site_url('assets/builds/vendor-admin.js') . '?v='.FILE_SHARING_REVISION.'"></script>';
}



/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function file_sharing_module_init_menu_items()
{
    $CI = &get_instance();
    if (has_permission('file_sharing', '', 'view_file_management') || has_permission('file_sharing', '', 'view_sharing') || has_permission('file_sharing', '', 'view_download_management') || has_permission('file_sharing', '', 'view_reports') || has_permission('file_sharing', '', 'view_setting_file_sharing')) {
        $CI->app_menu->add_sidebar_menu_item('file_sharing', [
            'name'     => _l('file_sharing'),
            'icon'     => 'fa fa-folder',
            'position' => 5,
        ]);

        if(has_permission('file_sharing', '', 'view_file_management')){
            $CI->app_menu->add_sidebar_children_item('file_sharing', [
                'slug'     => 'file_sharing_management',
                'name'     => _l('file_management'),
                'icon'     => 'fa fa-folder-open',
                'href'     => admin_url('file_sharing/manage'),
                'position' => 1,
            ]);
        }

        if(has_permission('file_sharing', '', 'view_sharing')){
            $CI->app_menu->add_sidebar_children_item('file_sharing', [
                'slug'     => 'file_sharing_sharings',
                'name'     => _l('sharing'),
                'icon'     => 'fa fa-share-square',
                'href'     => admin_url('file_sharing/sharing'),
                'position' => 2,
            ]);
        }

        if(has_permission('file_sharing', '', 'view_download_management')){
            $CI->app_menu->add_sidebar_children_item('file_sharing', [
                'slug'     => 'file_sharing_download_management',
                'name'     => _l('download_management'),
                'icon'     => 'fa fa-cloud-download',
                'href'     => admin_url('file_sharing/download_management'),
                'position' => 3,
            ]);
        }
        
        if(has_permission('file_sharing', '', 'view_reports')){
            $CI->app_menu->add_sidebar_children_item('file_sharing', [
                'slug'     => 'file_sharing_report',
                'name'     => _l('reports'),
                'icon'     => 'fa fa-area-chart',
                'href'     => admin_url('file_sharing/reports'),
                'position' => 4,
            ]);
        }

        if(has_permission('file_sharing', '', 'view_setting_file_sharing')){
            $CI->app_menu->add_sidebar_children_item('file_sharing', [
                'slug'     => 'file_sharing_setting',
                'name'     => _l('setting_file_sharing'),
                'icon'     => 'fa fa-cogs',
                'href'     => admin_url('file_sharing/setting'),
                'position' => 5,
            ]);
        }
    }
}

/**
 * Init file sharing module menu items in setup in customers_navigation_end hook
 */
function file_sharing_module_init_client_menu_items()
{
    $menu = '';
    if (is_client_logged_in() && get_option('fs_client_visible') == 1) {
        $menu .= '<li class="customers-nav-item-commission">
                  <a href="' . site_url('file_sharing/file_sharing_client') . '">
                    <i class=""></i> '
        . _l('file_sharing') . '
                  </a>
               </li>';
    }
    echo html_entity_decode($menu);
}


/**
 * Init commission module permissions in setup in admin_init hook
 */
function file_sharing_permissions() {

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view_file_management'   => _l('file_management') . '(' . _l('permission_view') . ')',
        'view_sharing'   => _l('sharing') . '(' . _l('permission_view') . ')',
        'view_download_management'   => _l('download_management') . '(' . _l('permission_view') . ')',
        'view_reports'   => _l('reports') . '(' . _l('permission_view') . ')',
        'view_setting_file_sharing'   => _l('setting_file_sharing') . '(' . _l('permission_view') . ')',
    ];
    register_staff_capabilities('file_sharing', $capabilities, _l('file_sharing'));
}

function file_sharing_appint(){
    $CI = & get_instance();    
    require_once 'libraries/gtsslib.php';
    $file_sharing_api = new FileSharingLic();
    $file_sharing_gtssres = $file_sharing_api->verify_license(true);    
    if(!$file_sharing_gtssres || ($file_sharing_gtssres && isset($file_sharing_gtssres['status']) && !$file_sharing_gtssres['status'])){
         $CI->app_modules->deactivate(FILE_SHARING_MODULE_NAME);
        set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        redirect(admin_url('modules'));
    }    
}
function file_sharing_preactivate($module_name){
    if ($module_name['system_name'] == FILE_SHARING_MODULE_NAME) {             
        require_once 'libraries/gtsslib.php';
        $file_sharing_api = new FileSharingLic();
        $file_sharing_gtssres = $file_sharing_api->verify_license();          
        if(!$file_sharing_gtssres || ($file_sharing_gtssres && isset($file_sharing_gtssres['status']) && !$file_sharing_gtssres['status'])){
             $CI = & get_instance();
            $data['submit_url'] = $module_name['system_name'].'/gtsverify/activate'; 
            $data['original_url'] = admin_url('modules/activate/'.FILE_SHARING_MODULE_NAME); 
            $data['module_name'] = FILE_SHARING_MODULE_NAME; 
            $data['title'] = "Module License Activation"; 
            echo $CI->load->view($module_name['system_name'].'/activate', $data, true);
            exit();
        }        
    }
}
function file_sharing_predeactivate($module_name){
    if ($module_name['system_name'] == FILE_SHARING_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $file_sharing_api = new FileSharingLic();
        $file_sharing_api->deactivate_license();
    }
}
function file_sharing_uninstall($module_name){
    if ($module_name['system_name'] == FILE_SHARING_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $file_sharing_api = new FileSharingLic();
        $file_sharing_api->deactivate_license();
    }
}