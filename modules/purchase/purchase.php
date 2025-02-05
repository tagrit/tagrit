<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Purchase
Description: Purchase Management Module is a tool for managing your day-to-day purchases. It is packed with all necessary features that are needed by any business, which has to buy raw material for manufacturing or finished good purchases for trading
Version: 1.5.0
Requires at least: 2.3.*
Author: Tagrit ERP Team
Author URI: https://tagrit.com
*/

define('PURCHASE_MODULE_NAME', 'purchase');
define('PURCHASE_MODULE_UPLOAD_FOLDER', module_dir_path(PURCHASE_MODULE_NAME, 'uploads'));
define('PURCHASE_ORDER_RETURN_MODULE_UPLOAD_FOLDER', module_dir_path(PURCHASE_MODULE_NAME, 'uploads/order_return/'));

hooks()->add_action('admin_init', 'purchase_permissions');
hooks()->add_action('app_admin_footer', 'purchase_head_components');
hooks()->add_action('app_admin_footer', 'purchase_add_footer_components');
hooks()->add_action('app_admin_head', 'purchase_add_head_components');
hooks()->add_action('admin_init', 'purchase_module_init_menu_items');
hooks()->add_action('before_expense_form_name','init_vendor_option');
hooks()->add_action('after_custom_fields_select_options','init_vendor_customfield');
hooks()->add_action('after_custom_fields_select_options','init_po_customfield');
hooks()->add_action('after_custom_fields_select_options','init_vendor_contacts_customfield');
hooks()->add_action('after_customer_admins_tab', 'init_tab_pur_order');
hooks()->add_action('after_custom_profile_tab_content', 'init_content_pur_order');

//PO task
hooks()->add_action('task_related_to_select', 'po_related_to_select'); // old
//hooks()->add_filter('before_return_relation_values', 'po_relation_values', 10, 2); // old
hooks()->add_filter('before_return_relation_data', 'po_relation_data', 10, 4); // old
hooks()->add_action('task_modal_rel_type_select', 'po_task_modal_rel_type_select'); // new
hooks()->add_filter('relation_values', 'po_get_relation_values', 10, 2); // new
hooks()->add_filter('get_relation_data', 'po_get_relation_data', 10, 4); // new
hooks()->add_filter('tasks_table_row_data', 'po_add_table_row', 10, 3);

//Purchase quotation task
hooks()->add_action('task_related_to_select', 'pq_related_to_select'); // old
//hooks()->add_filter('before_return_relation_values', 'pq_relation_values', 10, 2); // old
hooks()->add_filter('before_return_relation_data', 'pq_relation_data', 10, 4); // old
hooks()->add_action('task_modal_rel_type_select', 'pq_task_modal_rel_type_select'); // new
hooks()->add_filter('relation_values', 'pq_get_relation_values', 10, 2); // new
hooks()->add_filter('get_relation_data', 'pq_get_relation_data', 10, 4); // new
hooks()->add_filter('tasks_table_row_data', 'pq_add_table_row', 10, 3);

//Purchase contract task
hooks()->add_action('task_related_to_select', 'pc_related_to_select'); // old
//hooks()->add_filter('before_return_relation_values', 'pc_relation_values', 10, 2); // old
hooks()->add_filter('before_return_relation_data', 'pc_relation_data', 10, 4); // old
hooks()->add_action('task_modal_rel_type_select', 'pc_task_modal_rel_type_select'); // new
hooks()->add_filter('relation_values', 'pc_get_relation_values', 10, 2); // new
hooks()->add_filter('get_relation_data', 'pc_get_relation_data', 10, 4); // new
hooks()->add_filter('tasks_table_row_data', 'pc_add_table_row', 10, 3);

//Purchase invoice task
hooks()->add_action('task_related_to_select', 'pi_related_to_select'); // old
//hooks()->add_filter('before_return_relation_values', 'pi_relation_values', 10, 2); // old
hooks()->add_filter('before_return_relation_data', 'pi_relation_data', 10, 4); // old
hooks()->add_action('task_modal_rel_type_select', 'pi_task_modal_rel_type_select'); // new
hooks()->add_filter('relation_values', 'pi_get_relation_values', 10, 2); // new
hooks()->add_filter('get_relation_data', 'pi_get_relation_data', 10, 4); // new
hooks()->add_filter('tasks_table_row_data', 'pi_add_table_row', 10, 3);

//debit note relation value
hooks()->add_filter('relation_values', 'debit_note_get_relation_values', 10, 2); // new
hooks()->add_filter('get_relation_data', 'debit_note_relation_data', 10, 4); // new

//cronjob auto reset purchase order/request number
hooks()->add_action('after_cron_run', 'reset_pur_order_number');
hooks()->add_action('after_cron_run', 'reset_pur_request_number');

//cronjob recurring purchase invoice
hooks()->add_action('after_cron_run', 'recurring_purchase_invoice');

//get currency
hooks()->add_action('after_cron_run', 'pur_cronjob_currency_rates');

// Purchase dashboard widget
hooks()->add_filter('get_dashboard_widgets', 'purchase_add_dashboard_widget');
hooks()->add_action('app_admin_footer', 'purchase_load_js');

//Filter sale upload path debit note
hooks()->add_filter('get_upload_path_by_type', 'debit_note_upload_file_path', 10, 2);

// Purchase invoice customfield
hooks()->add_action('after_custom_fields_select_options', 'init_invoice_customfield');

// Reload language for vendor portal
hooks()->add_action('after_load_admin_language', 'reload_language');

//Project hook
hooks()->add_filter('project_tabs', 'init_po_project_tabs');

// Mail template language
hooks()->add_filter('email_template_language', 'update_email_lang_for_vendor', 10, 2);

// Purchase load theme style
hooks()->add_filter('get_styling_areas', 'purchase_before_load_theme_style');

//Expense table vendor data
hooks()->add_filter('expenses_table_columns', 'purchase_add_vendor_column');
hooks()->add_filter('expenses_table_sql_columns', 'purchase_add_vendor_sql_column');
hooks()->add_filter('expenses_table_row_data', 'purchase_add_vendor_row_data', 10, 2);
hooks()->add_action('purchase_init',PURCHASE_MODULE_NAME.'_appint');
hooks()->add_action('pre_activate_module', PURCHASE_MODULE_NAME.'_preactivate');
hooks()->add_action('pre_deactivate_module', PURCHASE_MODULE_NAME.'_predeactivate');
hooks()->add_action('pre_uninstall_module', PURCHASE_MODULE_NAME.'_uninstall');
//Purchase mail template
register_merge_fields('purchase/merge_fields/purchase_order_merge_fields');
register_merge_fields('purchase/merge_fields/purchase_request_merge_fields');
register_merge_fields('purchase/merge_fields/purchase_quotation_merge_fields');
register_merge_fields('purchase/merge_fields/debit_note_merge_fields');
register_merge_fields('purchase/merge_fields/purchase_statement_merge_fields');
register_merge_fields('purchase/merge_fields/vendor_merge_fields');
register_merge_fields('purchase/merge_fields/purchase_contract_merge_fields');
register_merge_fields('purchase/merge_fields/purchase_approve_merge_fields');
register_merge_fields('purchase/merge_fields/purchase_request_approval_merge_fields');


hooks()->add_filter('other_merge_fields_available_for', 'purchase_register_other_merge_fields');

define('PURCHASE_PATH', 'modules/purchase/uploads/');
define('PURCHASE_MODULE_ITEM_UPLOAD_FOLDER', 'modules/purchase/uploads/item_img/');

define('PURCHASE_REVISION', 150);
define('COMMODITY_ERROR_PUR', FCPATH );
define('COMMODITY_EXPORT_PUR', FCPATH );
define('PURCHASE_IMPORT_ITEM_ERROR', 'modules/purchase/uploads/import_item_error/');
define('PURCHASE_IMPORT_VENDOR_ERROR', 'modules/purchase/uploads/import_vendor_error/');

/**
* Register activation module hook
*/
register_activation_hook(PURCHASE_MODULE_NAME, 'purchase_module_activation_hook');
/**
* Load the module helper
*/
$CI = & get_instance();
$CI->load->helper(PURCHASE_MODULE_NAME . '/purchase');

//Vendor portal UI
if(get_status_modules_pur('theme_style') == 1){
    hooks()->add_action('app_vendor_head', 'theme_style_vendor_area_head');
}

function purchase_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(PURCHASE_MODULE_NAME, [PURCHASE_MODULE_NAME]);

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function purchase_module_init_menu_items() {

    $CI = &get_instance();
    if (has_permission('purchase_items', '', 'view') || has_permission('purchase_vendors', '', 'view') || has_permission('purchase_vendor_items', '', 'view') || has_permission('purchase_request', '', 'view') || has_permission('purchase_quotations', '', 'view') || has_permission('purchase_orders', '', 'view') || has_permission('purchase_contracts', '', 'view') || has_permission('purchase_invoices', '', 'view') || has_permission('purchase_reports', '', 'view') || has_permission('purchase_debit_notes', '', 'view') || has_permission('purchase_settings', '', 'edit') || has_permission('purchase_vendors', '', 'view_own') || has_permission('purchase_vendor_items', '', 'view_own') || has_permission('purchase_request', '', 'view_own') || has_permission('purchase_quotations', '', 'view_own') || has_permission('purchase_orders', '', 'view_own') || has_permission('purchase_contracts', '', 'view_own') || has_permission('purchase_invoices', '', 'view_own') || has_permission('purchase_debit_notes', '', 'view_own') || has_permission('purchase_order_return', '', 'view_own') || has_permission('purchase_order_return', '', 'view') ) {
        $CI->app_menu->add_sidebar_menu_item('purchase', [
            'name' => _l('purchase'),
            'icon' => 'fa fa-shopping-cart',
            'position' => 30,
        ]);
    }

    

        $CI->db->where('module_name', 'warehouse');
        $module = $CI->db->get(db_prefix() . 'modules')->row();

        if(has_permission('purchase_items', '', 'view') ){
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-items',
                'name' => _l('items'),
                'icon' => 'fa fa-clone menu-icon',
                'href' => admin_url('purchase/items'),
                'position' => 1,
            ]);
        }

        if(has_permission('purchase_vendors', '', 'view') || has_permission('purchase_vendors', '', 'view_own')){
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'vendors',
                'name' => _l('vendor'),
                'icon' => 'fa fa-users',
                'href' => admin_url('purchase/vendors'),
                'position' => 2,
            ]);
        }

        if(has_permission('purchase_vendor_items', '', 'view') || has_permission('purchase_vendor_items', '', 'view_own')){
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'vendors-items',
                'name' => _l('vendor_item'),
                'icon' => 'fa fa-newspaper',
                'href' => admin_url('purchase/vendor_items'),
                'position' => 3,
            ]);
        }

        if(has_permission('purchase_request', '', 'view') || has_permission('purchase_request', '', 'view_own')){
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-request',
                'name' => _l('purchase_request'),
                'icon' => 'fa fa-shopping-basket',
                'href' => admin_url('purchase/purchase_request'),
                'position' => 4,
            ]);
        }

        if(has_permission('purchase_quotations', '', 'view')  || has_permission('purchase_quotations', '', 'view_own')){
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-quotation',
                'name' => _l('quotations'),
                'icon' => 'fa fa-file-powerpoint',
                'href' => admin_url('purchase/quotations'),
                'position' => 5,
            ]);
        }

        if(has_permission('purchase_orders', '', 'view') || has_permission('purchase_orders', '', 'view_own')){
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-order',
                'name' => _l('purchase_order'),
                'icon' => 'fa fa-cart-plus',
                'href' => admin_url('purchase/purchase_order'),
                'position' => 6,
            ]);
        }

        if(has_permission('purchase_order_return', '', 'view') || has_permission('purchase_order_return', '', 'view_own')){
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'return-order',
                'name' => _l('pur_return_orders'),
                'icon' => 'fa fa-reply-all',
                'href' => admin_url('purchase/order_returns'),
                'position' => 7,
            ]);
        }

        if(has_permission('purchase_contracts', '', 'view') || has_permission('purchase_contracts', '', 'view_own')){
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-contract',
                'name' => _l('contracts'),
                'icon' => 'fa fa-file-text',
                'href' => admin_url('purchase/contracts'),
                'position' => 8,
            ]);
        }

        if(has_permission('purchase_debit_notes', '', 'view') || has_permission('purchase_debit_notes', '', 'view_own')){
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug'     => 'purchase-debit-note',
                'name'     => _l('pur_debit_note'),
                'icon'     => 'fa fa-credit-card',
                'href'     => admin_url('purchase/debit_notes'),
                'position' => 9,
            ]);
        }

        if(has_permission('purchase_invoices', '', 'view') || has_permission('purchase_invoices', '', 'view_own')){
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-invoices',
                'name' => _l('invoices'),
                'icon' => 'fa fa-clipboard',
                'href' => admin_url('purchase/invoices'),
                'position' => 10,
            ]);
        }

        if(has_permission('purchase_reports', '', 'view') ){
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase_reports',
                'name' => _l('reports'),
                'icon' => 'fa fa-bar-chart',
                'href' => admin_url('purchase/reports'),
                'position' => 11,
            ]);
        }
    

    if (is_admin() || has_permission('purchase_settings', '', 'edit')) {
        $CI->app_menu->add_sidebar_children_item('purchase', [
            'slug' => 'purchase-settings',
            'name' => _l('setting'),
            'icon' => 'fa fa-gears',
            'href' => admin_url('purchase/setting'),
            'position' => 12,
        ]);
    }

}

/**
 * { purchase add dashboard widget }
 *
 * @param        $widgets  The widgets
 *
 * @return       ( description_of_the_return_value )
 */
function purchase_add_dashboard_widget($widgets)
{
    if (has_permission('purchase', '', 'view') || is_admin()) {
        $widgets[] = [
                'path'      => 'purchase/purchase_widget',
                'container' => 'top-12',
            ];
    }

    return $widgets;
}

function purchase_load_js($dashboard_js){
        $CI = &get_instance();
        $viewuri = $_SERVER['REQUEST_URI'];
        if (has_permission('purchase', '', 'view') || is_admin()) {
            if(!(strpos($viewuri, '/admin') === false)){
                $dashboard_js .=  $CI->load->view('purchase/purchase_dashboard_js'); 
            }
        }
        return $dashboard_js;
}

/**
 * { purchase permissions }
 */
function purchase_permissions() {
    $capabilities = [];
    $capabilities_rp = [];
    $capabilities_own = [];

    $capabilities['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];


    $capabilities_rp['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
    ];

     $capabilities_setting['capabilities'] = [
        'edit' => _l('permission_edit'),
    ];

    $capabilities_own['capabilities'] = [
        'view_own' => _l('permission_view') . '(' . _l('permission_own') . ')',
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('purchase_items', $capabilities, _l('purchase_items'));
    register_staff_capabilities('purchase_vendors', $capabilities_own, _l('purchase_vendors'));
    register_staff_capabilities('purchase_vendor_items', $capabilities_own, _l('purchase_vendor_items'));
    register_staff_capabilities('purchase_request', $capabilities_own, _l('purchase_request'));
    register_staff_capabilities('purchase_quotations', $capabilities_own, _l('purchase_quotations'));
    register_staff_capabilities('purchase_orders', $capabilities_own, _l('purchase_orders'));
    register_staff_capabilities('purchase_order_return', $capabilities_own, _l('purchase_order_return'));
    register_staff_capabilities('purchase_contracts', $capabilities_own, _l('purchase_contracts'));
    register_staff_capabilities('purchase_invoices', $capabilities_own, _l('purchase_invoices'));
    register_staff_capabilities('purchase_debit_notes', $capabilities_own, _l('purchase_debit_notes'));
    register_staff_capabilities('purchase_reports', $capabilities_rp, _l('purchase_reports'));
    register_staff_capabilities('purchase_settings', $capabilities_setting, _l('purchase_settings'));

    register_staff_capabilities('purchase_order_change_approve_status', $capabilities_setting, _l('purchase_order_change_approve_status'));
    
    register_staff_capabilities('purchase_estimate_change_approve_status', $capabilities_setting, _l('purchase_quotations_change_approve_status'));
    register_staff_capabilities('purchase_request_change_approve_status', $capabilities_setting, _l('purchase_request_change_approve_status'));

}

/**
 * { purchase_before_load_theme_style }
 *
 * @param      <type>  $area   The area
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function purchase_before_load_theme_style($area){
    $viewuri = $_SERVER['REQUEST_URI'];
    if (!(strpos($viewuri, 'purchase/vendors_portal') === false)) {
        $area['general'] = [];
        $area['tabs'] = [];
        $area['buttons'] = [];
        $area['admin'] = [];
        $area['modals'] = [];
        $area['tags'] = [];
        $area['customers'] = [];
    }

    return $area;
}

/**
 * purchase add footer components
 * @return
 */
function purchase_add_footer_components() {
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if(!(strpos($viewuri, '/admin/purchase/vendors') === false)){
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/vendor_manage.js') .'?v=' . PURCHASE_REVISION.'"></script>';
    }
    if(!(strpos($viewuri, '/admin/purchase/purchase_request') === false)){    
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/pur_request_manage.js') .'?v=' . PURCHASE_REVISION.'"></script>';
    }
    if(!(strpos($viewuri, '/admin/purchase/quotations') === false)){
        echo '<script src="'. base_url('assets/plugins/signature-pad/signature_pad.min.js').'"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/quotation_manage.js') .'?v=' . PURCHASE_REVISION.'"></script>';
    }
    if(!(strpos($viewuri, '/admin/purchase/pur_request') === false)){
        
    }
    if(!(strpos($viewuri, '/admin/purchase/view_pur_request') === false)){
        echo '<link rel="stylesheet prefetch" href="'.base_url('modules/purchase/assets/plugins/handsontable/chosen.css').'">';
        echo '<script src="'. base_url('assets/plugins/signature-pad/signature_pad.min.js').'"></script>';
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js').'"></script>';
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js').'"></script>'; 
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/numbro/languages.min.js').'"></script>';
    }
    if(!(strpos($viewuri, '/admin/purchase/purchase_order') === false)){
        echo '<script src="'. base_url('assets/plugins/signature-pad/signature_pad.min.js').'"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/purchase_order_manage.js') .'?v=' . PURCHASE_REVISION.'"></script>';
    }
    if(!(strpos($viewuri, '/admin/purchase/contracts') === false)){
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/contract_manage.js') .'?v=' . PURCHASE_REVISION.'"></script>';
    }
    if(!(strpos($viewuri, '/admin/purchase/contract') === false)){
       
        echo '<script src="'.base_url('assets/plugins/signature-pad/signature_pad.min.js').'"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/pur_order') === false)) {
        echo '<link rel="stylesheet prefetch" href="'.base_url('modules/purchase/assets/plugins/handsontable/chosen.css').'">';
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js').'"></script>';
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js').'"></script>'; 
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/numbro/languages.min.js').'"></script>'; 
    }
    if (!(strpos($viewuri, '/admin/purchase/estimate') === false)) {
        echo '<link rel="stylesheet prefetch" href="'.base_url('modules/purchase/assets/plugins/handsontable/chosen.css').'">';
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js').'"></script>';
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js').'"></script>'; 
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/numbro/languages.min.js').'"></script>';
    }
    if (!(strpos($viewuri, 'purchase/vendors_portal/add_update_quotation') === false)) {
        echo '<script type="text/javascript" src="' . site_url('assets/plugins/tinymce/tinymce.min.js') . '?v=' . PURCHASE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, 'purchase/vendors_portal/add_update_quotation') === false)) {
        echo '<script type="text/javascript" src="' . site_url('assets/js/app.js') . '?v=' . PURCHASE_REVISION . '"></script>';
        echo '<script type="text/javascript" src="' . site_url('assets/plugins/accounting.js/accounting.js') . '?v=' . PURCHASE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, 'purchase/vendors_portal/add_update_invoice') === false)) {
        echo '<script type="text/javascript" src="' . site_url('assets/js/app.js') . '?v=' . PURCHASE_REVISION . '"></script>';
        echo '<script type="text/javascript" src="' . site_url('assets/plugins/accounting.js/accounting.js') . '?v=' . PURCHASE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, 'purchase/vendors_portal/pur_order') === false)) {
        echo '<link rel="stylesheet prefetch" href="'.base_url('modules/purchase/assets/plugins/handsontable/chosen.css').'">';
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js').'"></script>';
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js').'"></script>'; 
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/numbro/languages.min.js').'"></script>';
    }

    if (!(strpos($viewuri, '/admin/purchase/reports') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>'; 
    }

    if (!(strpos($viewuri, '/admin/purchase/items') === false)) {
         echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.jquery.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.js') . '"></script>';
         
    }

    if (!(strpos($viewuri, '/admin/purchase/new_vendor_items') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/vendor_items.js') .'?v=' . PURCHASE_REVISION.'"></script>';
    }

    if (!(strpos($viewuri, '/admin/purchase/setting?group=commodity_group') === false)) {
       
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<script src="https://momentjs.com/downloads/moment-timezone.min.js"></script>';
    }

    if (!(strpos($viewuri, '/admin/purchase/setting?group=sub_group') === false)) {
        
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
         echo '<link rel="stylesheet prefetch" href="'.base_url('modules/purchase/assets/plugins/handsontable/chosen.css').'">';
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js').'"></script>';
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js').'"></script>'; 
         echo '<script src="https://momentjs.com/downloads/moment-timezone.min.js"></script>';
    }

    if(!(strpos($viewuri, '/admin/purchase/invoices') === false)){    
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/manage_invoices.js') .'?v=' . PURCHASE_REVISION.'"></script>';
    }



    if(!(strpos($viewuri, '/admin/purchase/purchase_invoice') === false)){
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/pur_invoice_preview.js') .'?v=' . PURCHASE_REVISION.'"></script>';
    }

    if(!(strpos($viewuri, '/admin/purchase/setting?group=currency_rates') === false)){
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/currency_rate.js') .'?v=' . PURCHASE_REVISION.'"></script>';
    }
    

    if(!(strpos($viewuri, '/admin/projects/view') === false)  && !(strpos($viewuri, '?group=purchase_order') === false)){
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/po_on_project.js') .'?v=' . PURCHASE_REVISION.'"></script>';
    }

    if(!(strpos($viewuri, '/admin/projects/view') === false)  && !(strpos($viewuri, '?group=purchase_contract') === false)){
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/pur_contract_on_project.js') .'?v=' . PURCHASE_REVISION.'"></script>';
    }

    if(!(strpos($viewuri, '/admin/projects/view') === false)  && !(strpos($viewuri, '?group=purchase_request') === false)){
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/pur_request_on_project.js') .'?v=' . PURCHASE_REVISION.'"></script>';
    }

}

/**
 * add purchase add head components
 * @return
 */
function purchase_add_head_components() {
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if(!(strpos($viewuri, '/admin/purchase/pur_request') === false)){
        
    }
    if(!(strpos($viewuri, '/admin/purchase/view_pur_request') === false)){
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/purchase/estimate') === false)){
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/purchase/vendors_portal/add_update_quotation') === false)){
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/numbro/languages.min.js').'"></script>';
    }

    if(!(strpos($viewuri, '/purchase/vendors_portal/pur_order') === false)){
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/numbro/languages.min.js').'"></script>';
    }

    if(!(strpos($viewuri, '/purchase/vendors_portal/detail_item') === false)){
        

         echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.jquery.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/detail_vendor_item.js') .'?v=' . PURCHASE_REVISION.'"></script>';
     }

    if(!(strpos($viewuri, '/purchase/vendors_portal/pur_request') === false)){
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<script src="'.base_url('modules/purchase/assets/plugins/handsontable/numbro/languages.min.js').'"></script>';
    }

    if(!(strpos($viewuri, '/admin/purchase/pur_order') === false)){
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/admin/purchase/setting?group=units') === false)){
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/setting.css') . '"  rel="stylesheet" type="text/css" />';
    }
}

/**
 * purchase head components
 * @return
 */
function purchase_head_components() {
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    
    if(!(strpos($viewuri, '/admin/purchase') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/style.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/purchase/contract') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/contract.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/setting') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/setting.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/commodity_list.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/purchase/purchase_order') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order_manage.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/purchase/pur_order') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/purchase/pur_request') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_request.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/purchase/purchase_request') === false)){    
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_request_manage.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/purchase/view_pur_request') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/view_pur_request.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/purchase/estimate') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/estimate_template.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, 'purchase/vendors_portal/add_update_quotation') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/estimate_template.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, 'purchase/vendors_portal/pur_order') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/estimate_template.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, 'purchase/vendors_portal/order_return') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/estimate_template.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, 'purchase/vendors_portal/add_update_invoice') === false)){ 
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/add_update_invoice.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, 'purchase/vendors_portal/invoices') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/manage_vendor_invoice.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, 'purchase/vendors_portal/items') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/vendor_item_style.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, 'purchase/vendors_portal/detail_item') === false)) {
        echo '<link href="' . base_url('modules/warehouse/assets/css/styles.css') .'?v=' . PURCHASE_REVISION. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, 'purchase/vendors_portal/invoice/') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/manage_vendor_invoice.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/purchase/quotations') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/estimate_preview_template.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/purchase/vendor') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order_manage.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/admin/purchase/items') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/commodity_list.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.css') . '"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/admin/purchase/pur_invoice') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_invoice.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/admin/purchase/purchase_invoice') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_invoice.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/admin/purchase/payment_invoice') === false)){
        echo '<script src="'. base_url('assets/plugins/signature-pad/signature_pad.min.js').'"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/payment_invoice.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/admin/purchase/order_returns') === false)){
        echo '<script src="'. base_url('assets/plugins/signature-pad/signature_pad.min.js').'"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/style.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, 'purchase/vendors_portal/add_update_items') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/vendor_item.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, 'purchase/vendors_portal/') === false)){
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/vendor_style.css') .'?v=' . PURCHASE_REVISION.'"  rel="stylesheet" type="text/css" />';

    }
}   

/**
 * Initializes the vendor option.
 *
 * @param      string  $expense  The expense
 */
function init_vendor_option($expense = ''){
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    $list_vendor = $CI->purchase_model->get_vendor();
    $option = '';
    $option .= '<div class="row">';
    $option .= '<div class="col-md-12">';
    $option .= '<lable for="vendor">'._l('vendor').'</label>';
    $option .= '<select name="vendor" id="vendor" data-width="100%" class="selectpicker" data-live-search="true" data-none-selected-text="'. _l('ticket_settings_none_assigned').'">';
    $select = '';
    $option .= '<option value=""></option>';
    foreach($list_vendor as $ven){
        if( $expense != '' && $expense->vendor == $ven['userid']){
            $select = 'selected';
        }else{
            $select = ''; 
        }
        $option .= '<option value="'.$ven['userid'].'" '.$select.'>'. $ven['company'].'</option>';
    }
    $option .= '</select>';
    $option .= '</div>';
    $option .= '</div>';
    $option .= '<br>';
    echo pur_html_entity_decode($option);
}

/**
 * Initializes the vendor customfield.
 *
 * @param      string  $custom_field  The custom field
 */
function init_vendor_customfield($custom_field = ''){
    $select = '';
    if($custom_field != ''){
        if($custom_field->fieldto == 'vendors'){
            $select = 'selected';
        }
    }

    $html = '<option value="vendors" '.$select.' >'. _l('vendors').'</option>';

    echo pur_html_entity_decode($html);
}

/**
 * Initializes the purchase order customfield.
 *
 * @param      string  $custom_field  The custom field
 */
function init_po_customfield($custom_field = ''){
    $select = '';
    if($custom_field != ''){
        if($custom_field->fieldto == 'pur_order'){
            $select = 'selected';
        }
    }

    $html = '<option value="pur_order" '.$select.' >'. _l('pur_order').'</option>';

    echo pur_html_entity_decode($html);
}

/**
 * Initializes the purchase order customfield.
 *
 * @param      string  $custom_field  The custom field
 */
function init_vendor_contacts_customfield($custom_field = ''){
    $select = '';
    if($custom_field != ''){
        if($custom_field->fieldto == 'vendor_contacts'){
            $select = 'selected';
        }
    }

    $html = '<option value="vendor_contacts" '.$select.' >'. _l('vendor_contacts').'</option>';

    echo pur_html_entity_decode($html);
}

/**
 * Initializes the tab purchase order in client.
 *
 *
 */
function init_tab_pur_order() {
    echo '<li role="presentation">
                  <a href="#pur_order" aria-controls="pur_order" role="tab" data-toggle="tab">
                  ' . _l('pur_order') . '
                  </a>
               </li>';
}

/**
 * Initializes the tab content purchase order.
 *
 *
 */
function init_content_pur_order($client) {
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    if ($client) {
        echo '<div role="tabpanel" class="tab-pane" id="pur_order">';
        require "modules/purchase/views/client_pur_order.php";
        echo '</div>';
    }
}

/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function po_related_to_select($value)
{

    $selected = '';
    if($value == 'pur_order'){
        $selected = 'selected';
    }
    echo "<option value='pur_order' ".$selected.">".
                               _l('pur_order')."
                           </option>";

}

/**
 * PO relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function po_relation_values($values, $relation = null)
{

    if ($values['type'] == 'pur_order' || $values['type'] == 'purchase_order') {
        if (is_array($relation)) {
            $values['id']   = $relation['id'];
            $values['name'] = $relation['pur_order_number'];
        } else {
            $values['id']   = $relation->id;
            $values['name'] = $relation->pur_order_number;
        }
        $values['link'] = admin_url('purchase/purchase_order/' . $values['id']);
    }

    return $values;
}

/**
 * PO relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function po_relation_data($data, $type, $rel_id, $q = '')
{

    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if ($type == 'pur_order') {
        if ($rel_id != '') {
            $data = $CI->purchase_model->get_pur_order($rel_id);
        } else {
            $data   = [];
        }
    }
    return $data;
}


/**
 * PO add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function po_add_table_row($row ,$aRow)
{

    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if($aRow['rel_type'] == 'pur_order'){
        $po = $CI->purchase_model->get_pur_order($aRow['rel_id']);

           if ($po) {

                 $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('purchase/purchase_order/' . $po->id) . '">' . $po->pur_order_number . '</a><br />';

                $row[2] =   $row[2].$str;
            }

    }

    return $row;
}

/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function pq_related_to_select($value)
{

    $selected = '';
    if($value == 'pur_quotation'){
        $selected = 'selected';
    }
    echo "<option value='pur_quotation' ".$selected.">".
                               _l('purchase_quotation')."
                           </option>";

}

/**
 * pq relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function pq_relation_values($values, $relation = null)
{

    if ($values['type'] == 'pur_quotation') {
        if (is_array($relation)) {
            $values['id']   = $relation['id'];
            $values['name'] = format_pur_estimate_number($relation['id']);
        } else {
            $values['id']   = $relation->id;
            $values['name'] = format_pur_estimate_number($relation->id);
        }
        $values['link'] = admin_url('purchase/quotations/' . $values['id']);
    }

    return $values;
}

/**
 * pq relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function pq_relation_data($data, $type, $rel_id, $q = '')
{

    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if ($type == 'pur_quotation') {
        if ($rel_id != '') {
            $data = $CI->purchase_model->get_estimate($rel_id);
        } else {
            $data   = [];
        }
    }
    return $data;
}


/**
 * pq add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function pq_add_table_row($row ,$aRow)
{

    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if($aRow['rel_type'] == 'pur_quotation'){
        $pq = $CI->purchase_model->get_estimate($aRow['rel_id']);

           if ($pq) {

                $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('purchase/quotations/' . $pq->id) . '">' . format_pur_estimate_number($pq->id) . '</a><br />';

                $row[2] =  $row[2].$str;
            }

    }

    return $row;
}

/**
 * reset purchase order number
 *  
 */
function reset_pur_order_number($manually)
{
    $CI = &get_instance();

    if(get_option('reset_purchase_order_number_every_month') == 1){
        if (date('d') == 1) {
            if(date('Y-m-d') != get_purchase_option('date_reset_number')){
                $CI->db->where('option_name','next_po_number');
                $CI->db->update(db_prefix().'purchase_option',['option_val' => '1']);
                if ($CI->db->affected_rows() > 0) {
                    $CI->db->where('option_name','date_reset_number');
                    $CI->db->update(db_prefix().'purchase_option',['option_val' => date('Y-m-d')]);
                }
            }
        }
    }
}

/**
 * reset purchase order number
 *  
 */
function reset_pur_request_number($manually)
{
    $CI = &get_instance();

    if (date('m-d') == '01-01') {
        if(date('Y-m-d') != get_purchase_option('date_reset_pr_number')){
            $CI->db->where('option_name','next_pr_number');
            $CI->db->update(db_prefix().'purchase_option',['option_val' => '1']);
            if ($CI->db->affected_rows() > 0) {
                $CI->db->where('option_name','date_reset_pr_number');
                $CI->db->update(db_prefix().'purchase_option',['option_val' => date('Y-m-d')]);
            }
        }
    }
}

/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function pc_related_to_select($value)
{

    $selected = '';
    if($value == 'pur_contract'){
        $selected = 'selected';
    }
    echo "<option value='pur_contract' ".$selected.">".
                               _l('purchase_contract')."
                           </option>";

}

/**
 * purchase contract relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function pc_relation_values($values, $relation = null)
{

    if ($values['type'] == 'pur_contract') {
        if (is_array($relation)) {
            $values['id']   = $relation['id'];
            $values['name'] = get_pur_contract_number($relation['id']);
        } else {
            $values['id']   = $relation->id;
            $values['name'] = get_pur_contract_number($relation->id);
        }
        $values['link'] = admin_url('purchase/contract/' . $values['id']);
    }

    return $values;
}

/**
 * purchase contract relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function pc_relation_data($data, $type, $rel_id, $q = '')
{

    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if ($type == 'pur_contract') {
        if ($rel_id != '') {
            $data = $CI->purchase_model->get_contract($rel_id);
        } else {
            $data   = [];
        }
    }
    return $data;
}


/**
 * pq add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function pc_add_table_row($row ,$aRow)
{

    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if($aRow['rel_type'] == 'pur_contract'){
        $pc = $CI->purchase_model->get_contract($aRow['rel_id']);

           if ($pc) {

                $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('purchase/contract/' . $pc->id) . '">' . get_pur_contract_number($pc->id) . '</a><br />';

                $row[2] =  $row[2].$str;
            }

    }

    return $row;
}


/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function pi_related_to_select($value)
{

    $selected = '';
    if($value == 'pur_invoice'){
        $selected = 'selected';
    }
    echo "<option value='pur_invoice' ".$selected.">".
                               _l('pur_invoice')."
                           </option>";

}

/**
 * purchase contract relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function pi_relation_values($values, $relation = null)
{

    if ($values['type'] == 'pur_invoice') {
        if (is_array($relation)) {
            $values['id']   = $relation['id'];
            $values['name'] = get_pur_invoice_number($relation['id']);
        } else {
            $values['id']   = $relation->id;
            $values['name'] = get_pur_invoice_number($relation->id);
        }
        $values['link'] = admin_url('purchase/purchase_invoice/' . $values['id']);
    }

    return $values;
}

/**
 * purchase contract relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function pi_relation_data($data, $type, $rel_id, $q = '')
{

    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if ($type == 'pur_invoice') {
        if ($rel_id != '') {
            $data = $CI->purchase_model->get_pur_invoice($rel_id);
        } else {
            $data   = [];
        }
    }
    return $data;
}


/**
 * pq add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function pi_add_table_row($row ,$aRow)
{

    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if($aRow['rel_type'] == 'pur_invoice'){
        $pc = $CI->purchase_model->get_pur_invoice($aRow['rel_id']);

           if ($pc) {

                $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('purchase/purchase_invoice/' . $pc->id) . '">' . get_pur_invoice_number($pc->id) . '</a><br />';

                $row[2] =  $row[2].$str;
            }

    }

    return $row;
}

/**
 * Register other merge fields for purchase
 *
 * @param [array] $for
 * @return void
 */
function purchase_register_other_merge_fields($for) {
    $for[] = 'purchase_order';

    return $for;
}


if(get_status_modules_pur('theme_style') == 1){
    /**
     * Clients area theme applied styles
     * @return null
     */
    function theme_style_vendor_area_head()
    {   
        theme_style_render(['general', 'tabs', 'buttons', 'customers', 'modals']);
        theme_style_custom_css_pur('theme_style_custom_clients_area');
    }

    /**
     * Custom CSS
     * @param  string $main_area clients or admin area options
     * @return null
     */
    function theme_style_custom_css_pur($main_area)
    {
        $clients_or_admin_area             = get_option($main_area);
        $custom_css_admin_and_clients_area = get_option('theme_style_custom_clients_and_admin_area');
        if (!empty($clients_or_admin_area) || !empty($custom_css_admin_and_clients_area)) {
            echo '<style id="theme_style_custom_css">' . PHP_EOL;
            if (!empty($clients_or_admin_area)) {
                $clients_or_admin_area = clear_textarea_breaks($clients_or_admin_area);
                echo $clients_or_admin_area . PHP_EOL;
            }
            if (!empty($custom_css_admin_and_clients_area)) {
                $custom_css_admin_and_clients_area = clear_textarea_breaks($custom_css_admin_and_clients_area);
                echo $custom_css_admin_and_clients_area . PHP_EOL;
            }
            echo '</style>' . PHP_EOL;
        }
    }
}

/**
 * recurring purchase invoice
 *
 */
function recurring_purchase_invoice($manually) {
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    $CI->purchase_model->recurring_purchase_invoice($manually);
}

/**
 * { function_description }
 *
 * @param      <type>  $path   The path
 * @param      <type>  $type   The type
 */
function debit_note_upload_file_path($path, $type){
    if($type == 'debit_note'){
        $path = PURCHASE_MODULE_UPLOAD_FOLDER. '/debit_notes/';
    }
    return $path;
}

/**
 * Initializes the purchase order customfield.
 *
 * @param      string  $custom_field  The custom field
 */
function init_invoice_customfield($custom_field = '') {
    $select = '';
    if ($custom_field != '') {
        if ($custom_field->fieldto == 'pur_invoice') {
            $select = 'selected';
        }
    }

    $html = '<option value="pur_invoice" ' . $select . ' >' . _l('purchase_invoice') . '</option>';

    echo pur_html_entity_decode($html);
}

/**
 * po task modal rel type select
 * @param  object $value
 * @return
 */
function po_task_modal_rel_type_select($value) {
    $selected = '';
    if (isset($value) && isset($value['rel_type']) && $value['rel_type'] == 'pur_order') {
        $selected = 'selected';
    }
    echo "<option value='pur_order' " . $selected . ">" .
    _l('pur_order') . "
                           </option>";

}

/**
 * pq get relation values description
 * @param  object $values
 * @param  object $relation
 * @return
 */
function po_get_relation_values($values, $relation = null) {
    if ($values['type'] == 'pur_order' || $values['type'] == 'purchase_order') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = $relation['pur_order_number'];
        } else {
            $values['id'] = $relation->id;
            $values['name'] = $relation->pur_order_number;
        }
        $values['link'] = admin_url('purchase/purchase_order/' . $values['id']);
    }

    return $values;
}

/**
 * po get relation data
 * @param  object $data
 * @param  object $obj
 * @return
 */
function po_get_relation_data($data, $obj, $q = '') {
    $type = $obj['type'];
    $rel_id = $obj['rel_id'];
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if ($type == 'pur_order' || $type == 'purchase_order') {
        if ($rel_id != '') {
            $data = $CI->purchase_model->get_pur_order($rel_id);
        } else {
            if($q != ''){
                $data = $CI->purchase_model->get_pur_order_search($q);
            }
        }
    }
    return $data;
}

/**
 * PO relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function debit_note_get_relation_values($values, $relation = null)
{

    if ($values['type'] == 'debit_note') {
        if (is_array($relation)) {
            $values['id']   = $relation['id'];
            $values['name'] = $relation['number'];
        } else {
            $values['id']   = $relation->id;
            $values['name'] = format_debit_note_number($relation->id);
        }
        $values['link'] = admin_url('purchase/debit_notes/' . $values['id']);
    }

    return $values;
}

/**
 * po get relation data
 * @param  object $data
 * @param  object $obj
 * @return
 */
function debit_note_relation_data($data, $obj, $q = '') {
    $type = $obj['type'];
    $rel_id = $obj['rel_id'];
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if ($type == 'debit_note') {
        if ($rel_id != '') {
            $data = $CI->purchase_model->get_debit_note($rel_id);
        } else {
            if($q != ''){
                $data = $CI->purchase_model->get_debit_note_search($q);
            }
        }
    }
    return $data;
}

/**
 * pq task modal rel type select
 * @param  object $value
 * @return
 */
function pq_task_modal_rel_type_select($value) {
    $selected = '';
    if (isset($value) && isset($value['rel_type']) && $value['rel_type'] == 'pur_quotation') {
        $selected = 'selected';
    }
    echo "<option value='pur_quotation' " . $selected . ">" .
    _l('purchase_quotation') . "
                           </option>";

}

/**
 * pq get relation values description
 * @param  object $values
 * @param  object $relation
 * @return
 */
function pq_get_relation_values($values, $relation = null) {
    if ($values['type'] == 'pur_quotation') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = format_pur_estimate_number($relation['id']);
        } else {
            $values['id'] = $relation->id;
            $values['name'] = format_pur_estimate_number($relation->id);
        }
        $values['link'] = admin_url('purchase/quotations/' . $values['id']);
    }

    return $values;
}

/**
 * pq get relation data
 * @param  object $data
 * @param  object $obj
 * @return
 */
function pq_get_relation_data($data, $obj, $q = '') {
    $type = $obj['type'];
    $rel_id = $obj['rel_id'];
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if ($type == 'pur_quotation') {
        if ($rel_id != '') {
            $data = $CI->purchase_model->get_estimate($rel_id);
        } else {
            if($q != ''){
                $data = $CI->purchase_model->get_estimate_search($q);
            }
        }
    }
    return $data;
}

/**
 * pq task modal rel type select
 * @param  object $value
 * @return
 */
function pc_task_modal_rel_type_select($value) {
    $selected = '';
    if (isset($value) && isset($value['rel_type']) && $value['rel_type'] == 'pur_contract') {
        $selected = 'selected';
    }
    echo "<option value='pur_contract' " . $selected . ">" .
    _l('purchase_contract') . "
                           </option>";

}

/**
 * pc get relation values description
 * @param  object $values
 * @param  object $relation
 * @return
 */
function pc_get_relation_values($values, $relation = null) {
    if ($values['type'] == 'pur_contract') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = get_pur_contract_number($relation['id']);
        } else {
            $values['id'] = $relation->id;
            $values['name'] = get_pur_contract_number($relation->id);
        }
        $values['link'] = admin_url('purchase/contract/' . $values['id']);
    }

    return $values;
}

/**
 * pc get relation data
 * @param  object $data
 * @param  object $obj
 * @return
 */
function pc_get_relation_data($data, $obj, $q = '') {
    $type = $obj['type'];
    $rel_id = $obj['rel_id'];
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if ($type == 'pur_contract') {
        if ($rel_id != '') {
            $data = $CI->purchase_model->get_contract($rel_id);
        } else {
            if($q != ''){
                $data = $CI->purchase_model->get_contract_seach($rel_id);
            }
        }
    }
    return $data;
}

/**
 * pi task modal rel type select
 * @param  object $value
 * @return
 */
function pi_task_modal_rel_type_select($value) {
    $selected = '';
    if (isset($value) && isset($value['rel_type']) && $value['rel_type'] == 'pur_invoice') {
        $selected = 'selected';
    }
    echo "<option value='pur_invoice' " . $selected . ">" .
    _l('pur_invoice') . "
                           </option>";

}

/**
 * pi get relation values description
 * @param  object $values
 * @param  object $relation
 * @return
 */
function pi_get_relation_values($values, $relation = null) {
    if ($values['type'] == 'pur_invoice') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = get_pur_invoice_number($relation['id']);
        } else {
            $values['id'] = $relation->id;
            $values['name'] = get_pur_invoice_number($relation->id);
        }
        $values['link'] = admin_url('purchase/purchase_invoice/' . $values['id']);
    }

    return $values;
}

/**
 * pi get relation data
 * @param  object $data
 * @param  object $obj
 * @return
 */
function pi_get_relation_data($data, $obj, $q = '') {
    $type = $obj['type'];
    $rel_id = $obj['rel_id'];
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if ($type == 'pur_invoice') {
        if ($rel_id != '') {
            $data = $CI->purchase_model->get_pur_invoice($rel_id);
        } else {
            if($q != ''){
                $data = $CI->purchase_model->get_pur_invoice_search($rel_id);
            }
        }
    }
    return $data;
}

/**
 * reset purchase order number
 *
 */
function pur_cronjob_currency_rates($manually) {
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    if (date('G') == '16' && get_option('cr_automatically_get_currency_rate') == 1) {
        if(date('Y-m-d') != get_option('cur_date_cronjob_currency_rates')){
            $CI->purchase_model->cronjob_currency_rates($manually);
        }
    }
    

}

/**
 * Initializes the po project tabs.
 *
 * @param        $tabs   The tabs
 *
 * @return       ( tabs )
 */
function init_po_project_tabs($tabs){
    $tabs['purchase'] = [
        'slug' => 'purchase',
        'name' => _l('purchase'),
        'icon' => 'fa fa-cart-plus',
        'position' => 51,
        'collapse' => true,
        'visible' => true,
        'href' => '#',
        'badge' => [],
        'children' => [
            0 => [
                'parent_slug' => 'purchase',
                'slug' => 'purchase_request',
                'name' => _l('purchase_request'),
                'view' => 'purchase/pur_request_on_project',
                'position' => 5,
                'visible' => true,
                'icon' => '',
                'href' => '#',
                'badge' => [],
            ],
            1 => [
                'parent_slug' => 'purchase',
                'slug' => 'purchase_order',
                'name' => _l('purchase_order'),
                'view' => 'purchase/po_on_project',
                'position' => 5,
                'visible' => true,
                'icon' => '',
                'href' => '#',
                'badge' => [],
            ],
            2 => [
                'parent_slug' => 'purchase',
                'slug' => 'purchase_contract',
                'name' => _l('purchase_contract'),
                'view' => 'purchase/pur_contract_on_project',
                'position' => 5,
                'visible' => true,
                'icon' => '',
                'href' => '#',
                'badge' => [],
            ],
        ],
    ];

    return $tabs;
}

/**
 * { update email language for vendor }
 */
function update_email_lang_for_vendor($language, $data){

    $purchase_slug_arr = [
        'purchase-request-to-contact',
        'debit-note-to-contact',
        'purchase-quotation-to-contact',
        'purchase-request-to-contact',
        'purchase-statement-to-contact',
        'vendor-registration-confirmed',
        'purchase-order-to-contact',
        'purchase-contract-to-contact',
    ];

    if( in_array($data['template']->slug, $purchase_slug_arr)){
        if($data['template']->slug == 'vendor-registration-confirmed'){
            return $language;
        }else{
            $vendor_lang = get_vendor_language_by_email($data['email']);
            if($vendor_lang != ''){
                $language = $vendor_lang;
            }
        }
    }

    return $language;
}

/**
 * { reload language }
 */
function reload_language($language){
    $CI = &get_instance();
    if($CI instanceof AdminController){
        $CI->lang->load($language . '_lang', $language);
        if (file_exists(APPPATH . 'language/' . $language . '/custom_lang.php')) {
            $CI->lang->load('custom_lang', $language);
        }

        $GLOBALS['language'] = $language;
        $GLOBALS['locale']   = get_locale_key($language);
    }else{
        if($CI instanceof Vendors_portal){
            $vendor_id = get_vendor_user_id();

            if($vendor_id != 0){
                $CI->db->select('default_language');
                $CI->db->where('userid', $vendor_id);
                $lang = $CI->db->get(db_prefix().'pur_vendor')->row();
                if($lang && $lang->default_language != ''){
                    $CI->lang->load($lang->default_language . '_lang', $lang->default_language);
                    $CI->lang->load('purchase' . '/' .'purchase', $lang->default_language);

                    if (file_exists(APPPATH . 'language/' . $lang->default_language . '/custom_lang.php')) {
                        $CI->lang->load('custom_lang', $lang->default_language);
                    }
                    $GLOBALS['language'] = $lang->default_language;
                    $GLOBALS['locale']   = get_locale_key($lang->default_language);
                }else{
                    $CI->lang->load($language . '_lang', $language);
                    if (file_exists(APPPATH . 'language/' . $language . '/custom_lang.php')) {
                        $CI->lang->load('custom_lang', $language);
                    }
                    $GLOBALS['language'] = $language;
                    $GLOBALS['locale']   = get_locale_key($language);
                }
            }else{
                $CI->lang->load($language . '_lang', $language);
                if (file_exists(APPPATH . 'language/' . $language . '/custom_lang.php')) {
                    $CI->lang->load('custom_lang', $language);
                }
                $GLOBALS['language'] = $language;
                $GLOBALS['locale']   = get_locale_key($language);
            }
        }
    }
}

/**
 * { purchase_add_vendor_column }
 *
 * @param      <type>  $table_data  The table data
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function purchase_add_vendor_column($table_data){
    $table_data[] = _l('pur_vendor');
    return $table_data;
}

/**
 * { purchase_add_vendor_sql_column }
 *
 * @param      <type>  $acolumn  The acolumn
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function purchase_add_vendor_sql_column($acolumn){
    $acolumn[] = 'vendor';
    return $acolumn;
}

/**
 * { purchase_add_vendor_row_data }
 *
 * @param      <type>  $row    The row
 * @param      <type>  $arow   The arow
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function purchase_add_vendor_row_data($row, $arow){
    if(is_numeric($arow['vendor']) && $arow['vendor'] > 0){

        $row[] = '<a href="'.admin_url('purchase/vendor/'.$arow['vendor']).'">'. get_vendor_company_name($arow['vendor']).'</a>';
    }else{
        $row[] = '';
    }
    return $row;
}
function purchase_appint(){
    $CI = & get_instance();    
    require_once 'libraries/gtsslib.php';
    $purchase_api = new PurchaseLic();
    $purchase_gtssres = $purchase_api->verify_license(true);    
    if(!$purchase_gtssres || ($purchase_gtssres && isset($purchase_gtssres['status']) && !$purchase_gtssres['status'])){
         $CI->app_modules->deactivate(PURCHASE_MODULE_NAME);
        set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        redirect(admin_url('modules'));
    }    
}
function purchase_preactivate($module_name){
    if ($module_name['system_name'] == PURCHASE_MODULE_NAME) {             
        require_once 'libraries/gtsslib.php';
        $purchase_api = new PurchaseLic();
        $purchase_gtssres = $purchase_api->verify_license();          
        if(!$purchase_gtssres || ($purchase_gtssres && isset($purchase_gtssres['status']) && !$purchase_gtssres['status'])){
             $CI = & get_instance();
            $data['submit_url'] = $module_name['system_name'].'/gtsverify/activate'; 
            $data['original_url'] = admin_url('modules/activate/'.PURCHASE_MODULE_NAME); 
            $data['module_name'] = PURCHASE_MODULE_NAME; 
            $data['title'] = "Module License Activation"; 
            echo $CI->load->view($module_name['system_name'].'/activate', $data, true);
            exit();
        }        
    }
}
function purchase_predeactivate($module_name){
    if ($module_name['system_name'] == PURCHASE_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $purchase_api = new PurchaseLic();
        $purchase_api->deactivate_license();
    }
}
function purchase_uninstall($module_name){
    if ($module_name['system_name'] == PURCHASE_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $purchase_api = new PurchaseLic();
        $purchase_api->deactivate_license();
    }
}