<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Warehouse
Description: Module manage warehouse, stock imported, stock export, Loss and adjustment,report...
Version: 1.3.9
Requires at least: 2.3.*
Author: Tagrit ERP Team
Author URI: https://tagrit.com
*/

define('WAREHOUSE_MODULE_NAME', 'warehouse');
define('WAREHOUSE_MODULE_UPLOAD_FOLDER', module_dir_path(WAREHOUSE_MODULE_NAME, 'uploads'));
define('WAREHOUSE_STOCK_IMPORT_MODULE_UPLOAD_FOLDER', module_dir_path(WAREHOUSE_MODULE_NAME, 'uploads/stock_import/'));
define('WAREHOUSE_STOCK_EXPORT_MODULE_UPLOAD_FOLDER', module_dir_path(WAREHOUSE_MODULE_NAME, 'uploads/stock_export/'));
define('WAREHOUSE_LOST_ADJUSTMENT_MODULE_UPLOAD_FOLDER', module_dir_path(WAREHOUSE_MODULE_NAME, 'uploads/lost_adjustment/'));
define('WAREHOUSE_INTERNAL_DELIVERY_MODULE_UPLOAD_FOLDER', module_dir_path(WAREHOUSE_MODULE_NAME, 'uploads/internal_delivery/'));
define('WAREHOUSE_PACKING_LIST_MODULE_UPLOAD_FOLDER', module_dir_path(WAREHOUSE_MODULE_NAME, 'uploads/packing_lists/'));
define('WAREHOUSE_ORDER_RETURN_MODULE_UPLOAD_FOLDER', module_dir_path(WAREHOUSE_MODULE_NAME, 'uploads/order_returns/'));
define('WAREHOUSE_PROPOSAL_UPLOAD_FOLDER', module_dir_path(WAREHOUSE_MODULE_NAME, 'uploads/proposal/'));
define('WAREHOUSE_ITEM_UPLOAD', module_dir_path(WAREHOUSE_MODULE_NAME, 'uploads/item_img/'));
define('WAREHOUSE_SHIPMENT_UPLOAD', module_dir_path(WAREHOUSE_MODULE_NAME, 'uploads/shipments/'));

define('WAREHOUSE_PRINT_ITEM', 'modules/warehouse/uploads/print_item/');
define('WAREHOUSE_EXPORT_ITEM', 'modules/warehouse/uploads/export_item/');
define('WAREHOUSE_IMPORT_ITEM_ERROR', 'modules/warehouse/uploads/import_item_error/');
define('WAREHOUSE_IMPORT_OPENING_STOCK', 'modules/warehouse/uploads/import_opening_stock_error/');
define('WAREHOUSE_REPORT', 'modules/warehouse/uploads/reports/');

define('REVISION', 139);
//true display: brand, model, series in settings menu
define('ACTIVE_BRAND_MODEL_SERIES', false);
define('ACTIVE_PROPOSAL', true);
define('ACTIVE_PROPOSAL_OLD_CUSTOMER', false);
define('WAREHOUSE_PATH_LIBRARIES', 'modules/warehouse/libraries');

hooks()->add_action('admin_init', 'warehouse_permissions');
hooks()->add_action('app_admin_head', 'warehouse_add_head_components');
hooks()->add_action('app_admin_footer', 'warehouse_load_js');
hooks()->add_action('admin_init', 'warehouse_module_init_menu_items');
define('WAREHOUSE_PATH', 'modules/warehouse/uploads/');
hooks()->add_action('after_invoice_view_as_client_link', 'warehouse_module_init_tab');
hooks()->add_action('invoice_add_good_delivery_tab_content', 'warehouse_module_init_tab_content');
define('COMMODITY_ERROR', FCPATH );
define('COMMODITY_EXPORT', FCPATH );

hooks()->add_filter('create_goods_receipt', 'warehouse_create_goods_receipt');
hooks()->add_action('after_invoice_added', 'warehouse_create_goods_delivery');
//inventory received
hooks()->add_action('task_related_to_select', 'inventory_received_related_to_select'); // Old
//hooks()->add_filter('before_return_relation_values', 'inventory_received_relation_values', 10, 2); // Old
hooks()->add_filter('before_return_relation_data', 'inventory_received_relation_data', 10, 4); // Old
hooks()->add_action('task_modal_rel_type_select', 'inventory_received_task_modal_rel_type_select'); // new
hooks()->add_filter('relation_values', 'inventory_received_get_relation_values', 10, 2); // new
hooks()->add_filter('get_relation_data', 'inventory_received_get_relation_data', 10, 4); // new
hooks()->add_filter('tasks_table_row_data', 'inventory_received_add_table_row', 10, 3);

//invetory delivery
hooks()->add_action('task_related_to_select', 'inventory_delivery_related_to_select'); // Old
//hooks()->add_filter('before_return_relation_values', 'inventory_delivery_relation_values', 10, 2); // Old
hooks()->add_filter('before_return_relation_data', 'inventory_delivery_relation_data', 10, 4); // Old
hooks()->add_action('task_modal_rel_type_select', 'inventory_delivery_task_modal_rel_type_select'); // new
hooks()->add_filter('relation_values', 'inventory_delivery_get_relation_values', 10, 2); // new
hooks()->add_filter('get_relation_data', 'inventory_delivery_get_relation_data', 10, 4); // new
hooks()->add_filter('tasks_table_row_data', 'inventory_delivery_add_table_row', 10, 3);

//cancelled the invoice
hooks()->add_action('invoice_marked_as_cancelled', 'wh_invoice_marked_as_cancelled');
//uncancelled the invoice
hooks()->add_action('invoice_unmarked_as_cancelled', 'wh_invoice_unmarked_as_cancelled');

//add cronjob send notifi, email when inventory min
hooks()->add_action('after_cron_settings_last_tab', 'wh_cron_settings_tab');
hooks()->add_action('after_cron_settings_last_tab_content', 'wh_cron_settings_tab_content');
hooks()->add_filter('before_settings_updated', 'warehouse_cronjob_settings_update');
hooks()->add_action('settings_tab_footer', 'wh_cron_settings_tab_footer');

//cronjob notification inventory stock
hooks()->add_action('after_cron_run', 'items_send_notification_inventory_warning');

register_merge_fields('warehouse/merge_fields/inventory_warning_merge_fields');
hooks()->add_filter('other_merge_fields_available_for', 'inventory_warning_register_other_merge_fields');

//update invoice => update goods delivery node
hooks()->add_action('after_invoice_updated', 'wh_update_goods_delivery');

//warehouse add customfield
hooks()->add_action('after_custom_fields_select_options','init_warehouse_customfield');

//warehouse before admin view create invoice: display available quantity
hooks()->add_filter('before_admin_view_create_invoice', 'wh_before_admin_view_create_invoice');
hooks()->add_filter('admin_invoice_ajax_search_item', 'wh_admin_invoice_ajax_search_item', 10, 2);

//display Shipment in order detail of omnisale
hooks()->add_action('omni_order_detail_header', 'omni_order_detail_add_button_header');
hooks()->add_action('omni_sales_after_invoice_added', 'wh_omni_sales_after_invoice_added');
hooks()->add_action('omni_sales_after_delivery_note_added', 'wh_omni_sales_after_delivery_note_added');

//hook purchase module 
hooks()->add_action('after_purchase_order_add', 'wh_after_purchase_order_add');
hooks()->add_action('after_purchase_order_approve', 'wh_after_purchase_order_add');

//hook shipment menu
hooks()->add_action('customers_navigation_end', 'init_shipment_portal_menu');
hooks()->add_action('app_customers_portal_head', 'warehouse_client_add_head_components');
hooks()->add_action('app_customers_portal_footer', 'warehouse_client_add_footer_components');

//hook before invoice deleted
hooks()->add_action('before_invoice_deleted', 'warehouse_before_invoice_deleted');

if(ACTIVE_PROPOSAL_OLD_CUSTOMER){
//update proposal
hooks()->add_action('proposal_related_to_select', 'proposal_related_to_select');
hooks()->add_filter('before_return_relation_data', 'proposal_relation_data', 10, 4);
hooks()->add_filter('before_return_relation_values', 'proposal_relation_values', 10, 2);
hooks()->add_filter('before_search_proposal_relation_values', 'proposal_search_relation_values', 10, 1);

hooks()->add_action('warehouse_render_search_js_content', 'warehouse_render_search_js');

hooks()->add_action('proposal_render_input_group_t', 'proposal_render_input_groupt');
hooks()->add_action('proposal_render_input_group_b', 'proposal_render_input_groupb');

//add proposal
hooks()->add_filter('before_create_proposal', 'proposal_before_create_proposal');
hooks()->add_filter('before_proposal_updated', 'proposal_before_update_proposal');
// add proposal attchement file
hooks()->add_action('proposal_render_last_tab', 'wh_proposal_render_last_tab');
hooks()->add_action('proposal_render_last_tab_content', 'wh_proposal_render_last_tab_content');
hooks()->add_action('proposal_load_js_file', 'wh_proposal_load_js_file');

//item render warehouse
hooks()->add_action('item_render_input_form', 'item_wh_render_warehouse');

//item render series
hooks()->add_action('item_render_input_form', 'item_wh_render_series');
hooks()->add_filter('before_get_item', 'wh_proposal_before_get_item', 10, 1);

//lead render input vat
hooks()->add_action('lead_render_input_form', 'lead_wh_render_input_vat');
//proposal render li processing
hooks()->add_action('proposal_render_li_item', 'proposal_wh_render_li');
hooks()->add_action('proposal_render_lead_to_customer', 'wh_proposal_render_lead_to_customer');

//proposal add column in table
hooks()->add_filter('proposals_table_columns', 'wh_proposal_add_table_column', 10, 2);
hooks()->add_filter('proposals_table_row_data', 'wh_proposal_add_table_row', 10, 3);
hooks()->add_filter('proposals_table_sql_columns', 'wh_proposal_table_sql_columns', 10, 3);

//table proposal filter
hooks()->add_filter('proposals_table_filter_columns', 'wh_proposal_add_filter_column', 10, 2);
hooks()->add_action('proposals_manage_add_input', 'wh_proposals_manage_add_input');
hooks()->add_action('proposals_manage_add_li', 'wh_proposals_manage_add_li');

}
hooks()->add_action('warehouse_init',WAREHOUSE_MODULE_NAME.'_appint');
hooks()->add_action('pre_activate_module', WAREHOUSE_MODULE_NAME.'_preactivate');
hooks()->add_action('pre_deactivate_module', WAREHOUSE_MODULE_NAME.'_predeactivate');
hooks()->add_action('pre_uninstall_module', WAREHOUSE_MODULE_NAME.'_uninstall');
/**
* Register activation module hook
*/
register_activation_hook(WAREHOUSE_MODULE_NAME, 'warehouse_module_activation_hook');


/**
 * warehouse module activation hook
 * @return [type] 
 */
function warehouse_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(WAREHOUSE_MODULE_NAME, [WAREHOUSE_MODULE_NAME]);


$CI = & get_instance();
$CI->load->helper(WAREHOUSE_MODULE_NAME . '/warehouse');

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function warehouse_module_init_menu_items()
{
    $CI = &get_instance();
    if (has_permission('warehouse_item', '', 'view') || has_permission('wh_stock_import', '', 'view')  || has_permission('wh_stock_import', '', 'view_own') || has_permission('wh_stock_export', '', 'view') || has_permission('wh_stock_export', '', 'view_own') || has_permission('wh_packing_list', '', 'view')  || has_permission('wh_packing_list', '', 'view_own') || has_permission('wh_internal_delivery_note', '', 'view')  || has_permission('wh_internal_delivery_note', '', 'view_own') || has_permission('wh_loss_adjustment', '', 'view')  || has_permission('wh_loss_adjustment', '', 'view_own') || has_permission('wh_receipt_return_order', '', 'view') || has_permission('wh_receipt_return_order', '', 'view_own') || has_permission('wh_warehouse', '', 'view') || has_permission('wh_warehouse_history', '', 'view') || has_permission('wh_report', '', 'view') || has_permission('wh_setting', '', 'view')) {

       $CI->app_menu->add_sidebar_menu_item('warehouse', [
            'name'     => _l('warehouse'),
            'icon'     => 'fa fa-snowflake',
            'position' => 30,
        ]);
        
       if (has_permission('warehouse_item', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('warehouse', [
            'slug'     => 'wa_commodity_list',
            'name'     => _l('items'),
            'icon'     => 'fa fa-clone menu-icon',
            'href'     => admin_url('warehouse/commodity_list'),
            'position' => 1,
        ]);

    }

    if (has_permission('wh_stock_import', '', 'view') || has_permission('wh_stock_import', '', 'view_own')) {

        $CI->app_menu->add_sidebar_children_item('warehouse', [
            'slug'     => 'wa_manage_goods_receipt',
            'name'     => _l('stock_import'),
            'icon'     => 'fa fa-object-group',
            'href'     => admin_url('warehouse/manage_purchase'),
            'position' => 2,
        ]);
    }

    if (has_permission('wh_stock_export', '', 'view') || has_permission('wh_stock_export', '', 'view_own')) {
        $CI->app_menu->add_sidebar_children_item('warehouse', [
            'slug'     => 'wa_manage_goods_delivery',
            'name'     => _l('stock_export'),
            'icon'     => 'fa fa-object-ungroup',
            'href'     => admin_url('warehouse/manage_delivery'),
            'position' => 3,
        ]);
    }

    if (has_permission('wh_packing_list', '', 'view') || has_permission('wh_packing_list', '', 'view_own')) {
        $CI->app_menu->add_sidebar_children_item('warehouse', [
            'slug'     => 'wa_manage_packing_list',
            'name'     => _l('wh_packing_lists'),
            'icon'     => 'fa fa-inbox',
            'href'     => admin_url('warehouse/manage_packing_list'),
            'position' => 4,
        ]);
    }

    if (has_permission('wh_internal_delivery_note', '', 'view') || has_permission('wh_internal_delivery_note', '', 'view_own')) {
        $CI->app_menu->add_sidebar_children_item('warehouse', [
            'slug'     => 'wa_manage_internal_delivery',
            'name'     => _l('internal_delivery_note'),
            'icon'     => 'fa fa-rss-square',
            'href'     => admin_url('warehouse/manage_internal_delivery'),
            'position' => 5,
        ]);
    }

    if (has_permission('wh_loss_adjustment', '', 'view') || has_permission('wh_loss_adjustment', '', 'view_own')) {

        $CI->app_menu->add_sidebar_children_item('warehouse', [
            'slug'     => 'wa_manage_loss_adjustment',
            'name'     => _l('loss_adjustment'),
            'icon'     => 'fa fa-adjust',
            'href'     => admin_url('warehouse/loss_adjustment'),
            'position' => 6,
        ]);
    }

    if (has_permission('wh_receipt_return_order', '', 'view') || has_permission('wh_receipt_return_order', '', 'view_own')) {
        $CI->app_menu->add_sidebar_children_item('warehouse', [
            'slug'     => 'wa_manage_order_return',
            'name'     => _l('inventory_receipt_inventory_delivery_returns_goods'),
            'icon'     => 'fa fa-reply-all',
            'href'     => admin_url('warehouse/manage_order_return'),
            'position' => 7,
        ]);
        
    }

    if (has_permission('wh_warehouse', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('warehouse', [
            'slug'     => 'wa_manage_warehouse',
            'name'     => _l('_warehouse'),
            'icon'     => 'fa fa-home menu-icon',
            'href'     => admin_url('warehouse/warehouse_mange'),
            'position' => 7,
        ]);
    }


    if(ACTIVE_PROPOSAL_OLD_CUSTOMER){
            //add all warehouse on menu item
        foreach (get_warehouse_name() as $warehouse_item) {
            $CI->app_menu->add_sidebar_children_item('warehouse', [
                'slug'     => 'wa_manage_warehouse_'.$warehouse_item['warehouse_id'],
                'name'     => $warehouse_item['warehouse_name'],
                'icon'     => 'fa fa-home menu-icon',
                'href'     => admin_url('warehouse/view_warehouse_detail/'.$warehouse_item['warehouse_id']),
                'position' => 4,
            ]);
        }
    }
    

    if (has_permission('wh_warehouse_history', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('warehouse', [
            'slug'     => 'wa_warehouse_history',
            'name'     => _l('warehouse_history'),
            'icon'     => 'fa fa-calendar menu-icon',
            'href'     => admin_url('warehouse/warehouse_history'),
            'position' => 8,
        ]);
    }

    if (has_permission('wh_report', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('warehouse', [
            'slug'     => 'wa_report',
            'name'     => _l('report'),
            'icon'     => 'fa fa-area-chart menu-icon',
            'href'     => admin_url('warehouse/manage_report'),
            'position' => 8,
        ]);
        
    }

    if (has_permission('wh_setting', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('warehouse', [
            'slug'     => 'ware_settings',
            'name'     => _l('settings'),
            'icon'     => 'fa fa-gears',
            'href'     => admin_url('warehouse/setting'),
            'position' => 8,
        ]);
    }

    }
}


/**
 * warehouse load js
 * @return library 
 */
function warehouse_load_js(){
    $CI = &get_instance();

    $viewuri = $_SERVER['REQUEST_URI'];


     if (!(strpos($viewuri, '/admin/warehouse') === false)) {   
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/signature_pad.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/deactivate_hotkey.js') . '?v=' . REVISION . '"></script>';
     }

    if (!(strpos($viewuri, '/admin/warehouse/setting?group=approval_setting') === false)) {

         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/approval_setting.js').'?v=' . REVISION.'"></script>';
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/manage_setting.js').'?v=' . REVISION.'"></script>';
    }

    if (!(strpos($viewuri, '/admin/warehouse/setting?group=approval_setting') === false)) {
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/manage_setting.js').'?v=' . REVISION.'"></script>';
    }

    if (!(strpos($viewuri, '/admin/warehouse/setting?group=colors') === false)) {
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/color.js').'?v=' . REVISION.'"></script>';
    }


    if (!(strpos($viewuri, '/admin/warehouse/goods_delivery') === false) || !(strpos($viewuri, '/admin/warehouse/manage_goods_receipt') === false)) {
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/warehouse/manage_purchase') === false)) { 
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/manage_purchase.js').'?v=' . REVISION.'"></script>';
    }

    if (!(strpos($viewuri, '/admin/warehouse/manage_report') === false)) { 
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/stock_summary_report.js').'?v=' . REVISION.'"></script>';
        echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/inventory_valuation_report.js').'?v=' . REVISION.'"></script>';
    }

    if (!(strpos($viewuri, '/admin/warehouse/manage_stock_take') === false)) { 
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/manage_stock_take.js').'?v=' . REVISION.'"></script>';
    }

    if (!(strpos($viewuri, '/admin/warehouse/view_commodity_detail') === false) || !(strpos($viewuri, '/admin/warehouse/shipment_detail') === false)) { 
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.jquery.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.js') . '"></script>';
         
    }

    if (!(strpos($viewuri, '/admin/warehouse/commodity_list') === false)) { 
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.jquery.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.js') . '"></script>';
         
    }
    
	if (!(strpos($viewuri, '/admin/warehouse/add_loss_adjustment') === false)) {
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/admin/warehouse/loss_adjustment') === false)) { 
        echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/loss_adjustment_manage.js').'?v=' . REVISION.'"></script>';
    }


    if (!(strpos($viewuri, '/admin/warehouse/setting') === false)) { 
        echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/manage_setting.js').'?v=' . REVISION.'"></script>';
    }
    
    if (!(strpos($viewuri, '/admin/warehouse/setting?group=brand') === false)) {
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/brand.js').'?v=' . REVISION.'"></script>';
    }
    if (!(strpos($viewuri, '/admin/warehouse/setting?group=model') === false)) {
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/model.js').'?v=' . REVISION.'"></script>';
    }
    if (!(strpos($viewuri, '/admin/warehouse/setting?group=series') === false)) {
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/series.js').'?v=' . REVISION.'"></script>';
    }
    if (!(strpos($viewuri, '/admin/warehouse/setting?group=warehouse_custom_fields') === false)) {
         echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/warehouse_custom_fields.js').'?v=' . REVISION.'"></script>';
    }
    
        
        
}


/**
 * warehouse add head components
 * @return library 
 */
function warehouse_add_head_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];


    if (!(strpos($viewuri, '/admin/warehouse') === false)) {  
        echo '<link href="' . base_url('modules/warehouse/assets/css/styles.css') .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/handsontable/chosen.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/edit_delivery.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/commodity_list.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, '/admin/warehouse/setting?group=bodys') === false)) { 

        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/body.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, '/admin/warehouse/setting?group=colors') === false)) { 

        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/body.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, '/admin/warehouse/setting?group=commodity_group') === false)) {     
        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/body.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/warehouse/setting?group=commodity_type') === false)) {
        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/body.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
    }



    if (!(strpos($viewuri, '/admin/warehouse/manage_report') === false)) {
        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/report.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, '/admin/warehouse/manage_report?group=inventory_valuation_report') === false)) {
        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/report.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
    }

    
    if (!(strpos($viewuri, '/admin/warehouse/view_commodity_detail') === false) || !(strpos($viewuri, '/admin/warehouse/shipment_detail') === false)) {
        echo '<link href="' . base_url('modules/warehouse/assets/css/styles.css') .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.css') . '"  rel="stylesheet" type="text/css" />';
    }   

    if (!(strpos($viewuri, '/admin/warehouse/setting?group=approval_setting') === false)) {
        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/approval_setting.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
       
    }   
    
     if (!(strpos($viewuri, '/admin/warehouse/setting') === false)) {
       echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/body.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
       
    }   

    if (!(strpos($viewuri, '/admin/warehouse/setting?group=rule_sale_price') === false) || !(strpos($viewuri, '/admin/warehouse/setting') === false)) {
       echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/rule_sale_price.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
       
    }

    if (!(strpos($viewuri, '/admin/warehouse/setting?group=inventory_setting') === false)) {
       echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/rule_sale_price.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
       
    } 
    if (!(strpos($viewuri, '/admin/proposals/proposal') === false)) {
       echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/proposal_add_new_lead.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
       
    }
     if (!(strpos($viewuri, '/admin/warehouse/setting?group=warehouse_custom_fields') === false)) {
       echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/warehouse_custom_fields.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />';
       
    }

    if (!(strpos($viewuri, '/admin/warehouse/import_opening_stock') === false)) {
       echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/import_opening_stock.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />'; 
    }
    
    if (!(strpos($viewuri, '/admin/warehouse/import_xlsx_commodity') === false)) {
       echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/import_opening_stock.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />'; 
    }
    
    if (!(strpos($viewuri, '/admin/warehouse/goods_delivery') === false) || !(strpos($viewuri, '/admin/warehouse/manage_goods_receipt') === false)) {
       echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/goods_delivery.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />'; 
    }
     
    if (!(strpos($viewuri, '/admin/warehouse/commodity_list') === false)) {
       echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/add_opening_stock.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />'; 
    }

    if (!(strpos($viewuri, '/admin/warehouse/shipment_detail') === false)) {
       echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/shipments/order_status.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />'; 
    }

    if (!(strpos($viewuri, '/admin/manage_goods_receipt') === false)) {  
        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/handsontable/chosen.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
    }


}



/**
 * warehouse permissions
 * @return capabilities 
 */
function warehouse_permissions()
{
    $capabilities = [];

    $capabilitieswithoutViewOwn['capabilities'] = [
            'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
    ];
    $capabilities_all['capabilities'] = [
            'view_own' => _l('permission_view_own'),
            'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
    ];
    

    $capabilities_basic_view['capabilities'] = [
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
    ];

    $capabilities_basic_edit['capabilities'] = [
        'edit'   => _l('permission_edit'),
    ];

    register_staff_capabilities('warehouse_item', $capabilitieswithoutViewOwn, _l('warehouse').' - '._l('items'));
    register_staff_capabilities('wh_stock_import', $capabilities_all, _l('stock_import'));
    register_staff_capabilities('wh_stock_export', $capabilities_all, _l('stock_export'));
    register_staff_capabilities('wh_stock_export_serial_number', $capabilities_basic_edit, _l('wh_inventory_delivery_change_serial_numbers'));
    register_staff_capabilities('wh_packing_list', $capabilities_all, _l('wh_packing_lists'));
    register_staff_capabilities('wh_internal_delivery_note', $capabilities_all, _l('internal_delivery_note'));
    register_staff_capabilities('wh_loss_adjustment', $capabilities_all, _l('loss_adjustment'));
    register_staff_capabilities('wh_receipt_return_order', $capabilities_all, _l('inventory_receipt_inventory_delivery_returns_goods'));
    register_staff_capabilities('wh_warehouse', $capabilitieswithoutViewOwn, _l('_warehouse'));
    register_staff_capabilities('wh_warehouse_history', $capabilities_basic_view, _l('warehouse_history'));
    register_staff_capabilities('wh_report', $capabilities_basic_view, _l('warehouse').' - '._l('report'));
    register_staff_capabilities('wh_setting', $capabilitieswithoutViewOwn, _l('warehouse').' - '._l('setting'));

}

/**
 * warehouse module init tab
 *  
 */
function warehouse_module_init_tab($invoice_id){
    $li_tab ='';
    if (has_permission('wh_stock_export', '', 'view')) {
        $li_tab .='<li role="presentation">';
        $li_tab .='<a href="' . admin_url('warehouse/manage_delivery_filter/'.$invoice_id->id).'" >'._l('goods_delivery_tab').'</a>';
        $li_tab .='</li>';
    }
    echo new_html_entity_decode($li_tab);

}

/**
 * warehouse module init tab content
 * @param  [integer] $invoice_id 
 *            
 */
function warehouse_module_init_tab_content($invoice_id){
    $CI = &get_instance();
    $CI->load->model('warehouse/warehouse_model');

    $array_goods_delivery = $CI->warehouse_model->get_goods_delivery_from_invoice($invoice_id);

    $table_content ='';

    $table_content .='<div role="tabpanel" class="tab-pane" id="tab_goods_delivery">';
    $table_content .= '<table class="table dt-table border table-striped">';
    $table_content .= '<thead>';
    $table_content .= '<th><span class="bold">'. _l('goods_delivery_code').'</span></th>';
    $table_content .= '<th><span class="bold">'. _l('accounting_date').'</span></th>';
    $table_content .= '<th><span class="bold">'. _l('day_vouchers').'</span></th>';
    $table_content .= '<th><span class="bold">'. _l('subtotal').'</span></th>';
    $table_content .= '<th><span class="bold">'. _l('total_discount').'</span></th>';
    $table_content .= '<th><span class="bold">'. _l('total_money').'</span></th>';
    $table_content .= '<th><span class="bold">'. _l('status').'</span></th>';
    $table_content .= '</thead>';
    $table_content .='<tbody>';
    foreach ($array_goods_delivery as $value) {

    $table_content .='<tr>';
        $table_content .='<td>';
        $table_content .= '<a href="#" >' .$value['goods_delivery_code'] . '</a>';
        $table_content .= '<div class="row-options">';
        $table_content .= '<a href="' . admin_url('warehouse/manage_delivery/' . $value['id'] ).'" onclick="init_goods_delivery('.$value['id'].'); return false;">' . _l('view') . '</a>';
        $table_content .= '</div>';
        $table_content .='</td>';


        $table_content .='<td>'._d($value['date_c']).'</td>';
        $table_content .='<td>'._d($value['date_add']).'</td>';
        $table_content .='<td>'.app_format_money((float)($value['total_money']),'').'</td>';
        $table_content .='<td>'.app_format_money((float)($value['total_discount']),'').'</td>';
        $table_content .='<td>'.app_format_money((float)($value['after_discount']),'').'</td>'; 
        $table_content .='<td>';
        if($value['approval'] == 1){
            $table_content .= '<span class="label label-success  s-status invoice-status-2"><span class="tag">'._l('approved').'</span><span class="hide">, </span></span>&nbsp';
        }elseif($value['approval'] == 0){
            $table_content .= '<span class="label label-default  s-status invoice-status-6"><span class="tag">'._l('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
        }else{
            $table_content .= '<span class="label label-danger  s-status invoice-status-1"><span class="tag">'._l('reject').'</span><span class="hide">, </span></span>&nbsp';
        }
        $table_content .='</td>';

    $table_content .='</tr>';


    }
    $table_content .='</tbody>';
    $table_content .='</table>';
    $table_content .='</div>';

    echo new_html_entity_decode($table_content);

}


/**
 *[warehouse create goods receipt
 * @param  array $data 
 *     
 */
function warehouse_create_goods_receipt($data)
{   

    if($data['status'] == '2' && (get_warehouse_option('auto_create_goods_received') == 1) && (get_warehouse_option('goods_receipt_warehouse') != '') && (get_warehouse_option('goods_receipt_warehouse') != '0')){
        //purchase order is approval
        $CI = &get_instance();
        $CI->load->model('warehouse/warehouse_model');

        $CI->warehouse_model->auto_create_goods_receipt_with_purchase_order($data);
    }
    return true;
}


/**
 * warehouse create goods delivery
 * @param  array $value 
 *       
 */
function warehouse_create_goods_delivery($invoice_id)
{

    if($invoice_id){
        if(get_warehouse_option('auto_create_goods_delivery') == 1){
            //purchase order is approval
            $CI = &get_instance();
            $CI->load->model('warehouse/warehouse_model');

            $CI->warehouse_model->auto_create_goods_delivery_with_invoice($invoice_id);
        }

    }
    return true;

}

/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function inventory_received_related_to_select($value)
{

    $selected = '';
    if($value == 'stock_import'){
        $selected = 'selected';
    }
    echo "<option value='stock_import' ".$selected.">".
                               _l('stock_import')."
                           </option>";

}

/**
 * inventory received relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function inventory_received_relation_values($values, $relation)
{

    if ($values['type'] == 'stock_import') {
        if (is_array($relation)) {
            $values['id']   = $relation['id'];
            $values['name'] = $relation['goods_receipt_code'];
        } else {
            $values['id']   = $relation->id;
            $values['name'] = $relation->goods_receipt_code;
        }
        $values['link'] = admin_url('warehouse/manage_purchase/' . $values['id']);
    }

    return $values;
}

/**
 * inventory received relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function inventory_received_relation_data($data, $type, $rel_id, $q)
{

    $CI = &get_instance();
    $CI->load->model('warehouse/warehouse_model');

    if ($type == 'stock_import') {
        if ($rel_id != '') {
            $data = $CI->warehouse_model->get_goods_receipt($rel_id);
        } else {
            $data   = [];
        }
    }
    return $data;
}


/**
 * inventory received add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function inventory_received_add_table_row($row ,$aRow)
{

    $CI = &get_instance();
    $CI->load->model('warehouse/warehouse_model');

    if($aRow['rel_type'] == 'stock_import'){
        $inventory_received = $CI->warehouse_model->get_goods_receipt($aRow['rel_id']);

           if ($inventory_received) {

                $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('warehouse/manage_purchase/' . $inventory_received->id) . '">' . $inventory_received->goods_receipt_code . '</a><br />';

                $row[2] =  new_str_replace('<br />', $str, $row[2]);
            }

    }

    return $row;
}


//inventory delivery
/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function inventory_delivery_related_to_select($value)
{

    $selected = '';
    if($value == 'stock_export'){
        $selected = 'selected';
    }
    echo "<option value='stock_export' ".$selected.">".
                               _l('stock_export')."
                           </option>";

}

/**
 * inventory delivery relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function inventory_delivery_relation_values($values, $relation)
{

    if ($values['type'] == 'stock_export') {
        if (is_array($relation)) {
            $values['id']   = $relation['id'];
            $values['name'] = $relation['goods_delivery_code'];
        } else {
            $values['id']   = $relation->id;
            $values['name'] = $relation->goods_delivery_code;
        }
        $values['link'] = admin_url('warehouse/manage_delivery/' . $values['id']);
    }

    return $values;
}

/**
 * inventory delivery relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function inventory_delivery_relation_data($data, $type, $rel_id, $q)
{

    $CI = &get_instance();
    $CI->load->model('warehouse/warehouse_model');

    if ($type == 'stock_export') {
        if ($rel_id != '') {
            $data = $CI->warehouse_model->get_goods_delivery($rel_id);
        } else {
            $data   = [];
        }
    }
    return $data;
}


/**
 * inventory delivery add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function inventory_delivery_add_table_row($row ,$aRow)
{

    $CI = &get_instance();
    $CI->load->model('warehouse/warehouse_model');

    if($aRow['rel_type'] == 'stock_export'){
        $inventory_delivery = $CI->warehouse_model->get_goods_delivery($aRow['rel_id']);

           if ($inventory_delivery) {

                $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('warehouse/manage_purchase/' . $inventory_delivery->id) . '">' . $inventory_delivery->goods_delivery_code . '</a><br />';

                $row[2] =  new_str_replace('<br />', $str, $row[2]);
            }

    }

    return $row;
}

/**
 * [warehouse_reverse_goods_delivery
 * @param  integer $invoice 
 * @return boolean          
 */
function wh_invoice_marked_as_cancelled($invoice_id)
{
    if($invoice_id){
        if(get_warehouse_option('cancelled_invoice_reverse_inventory_delivery_voucher') == 1 ){
            $CI = &get_instance();
            $CI->load->model('warehouse/warehouse_model');
            $CI->warehouse_model->inventory_cancel_invoice($invoice_id);
        }
    }
    return true;

}

/**
 * wh invoice unmarked as cancelled
 * @param  integer $invoice_id 
 * @return boolean             
 */
function wh_invoice_unmarked_as_cancelled($invoice_id)
{
    
    if($invoice_id){
        if(get_warehouse_option('auto_create_goods_delivery') == 1){
            if(get_warehouse_option('uncancelled_invoice_create_inventory_delivery_voucher') == 1 ){
                //purchase order is approval
                $CI = &get_instance();
                $CI->load->model('warehouse/warehouse_model');

                $CI->warehouse_model->auto_create_goods_delivery_with_invoice($invoice_id);
            }
        }

    }

    return true;

}

/**
 * wh cron settings tab
 * @return view 
 */
function wh_cron_settings_tab()
{
    get_instance()->load->view('warehouse/cronjob_tab/settings_tab');
}

/**
 * wh cron settings tab content
 * @return view 
 */
function wh_cron_settings_tab_content()
{
    get_instance()->load->view('warehouse/cronjob_tab/settings_tab_content');
}

/**
 * warehouse cronjob settings update
 * @param  array $data 
 * @return array       
 */
function warehouse_cronjob_settings_update($data)
{

    if(isset($data['inventory_cronjob_notification_recipients'])){
        $data['settings']['inventory_cronjob_notification_recipients'] = implode(',', $data['inventory_cronjob_notification_recipients']);
        unset($data['inventory_cronjob_notification_recipients']);
    }else{
         $data['settings']['inventory_cronjob_notification_recipients'] = '';
    }

    return $data;
}

/**
 * wh cron settings tab footer
 * @return view 
 */
function wh_cron_settings_tab_footer()
{
    echo  require 'modules/warehouse/assets/js/inventory_cronjob_setting_js.php';

}

/**
 * items send notification inventory warning
 *  
 */
function items_send_notification_inventory_warning($manually)
{
        $CI = &get_instance();

        $inventorys_cronjob_active = get_option('inventorys_cronjob_active');

        $inventory_auto_operations_hour = get_option('inventory_auto_operations_hour');
        $automatically_send_items_expired_before = get_option('automatically_send_items_expired_before');
        $inventory_cronjob_notification_recipients = get_option('inventory_cronjob_notification_recipients');

        $invoice_hour_auto_operations = get_option('invoice_auto_operations_hour');

        //hour
        if ($inventory_auto_operations_hour == '') {
            $inventory_auto_operations_hour = 9;
        }

        //day
        if ($automatically_send_items_expired_before == '') {
            $automatically_send_items_expired_before = 1;
        }
        

        $inventory_auto_operations_hour = intval($inventory_auto_operations_hour);
        $hour_now                     = date('G');


        if($inventorys_cronjob_active == '0'){
            return;
        }

        if ($hour_now != $inventory_auto_operations_hour && $manually === false) {
            return;
        }

        /*get inventory stock, expriry date*/
        $CI->load->model('warehouse/warehouse_model');
        $CI->warehouse_model->items_send_notification_inventory_warning();

}


/**
 * Register other merge fields for inventory warning
 *
 * @param [array] $for
 * @return void
 */
function inventory_warning_register_other_merge_fields($for) {
    $for[] = 'inventory_warning';

    return $for;
}


/**
 * wh update goods delivery
 * @param  integer $value 
 * @return boolean        
 */
function wh_update_goods_delivery($invoice_id)
{
    //update invoice => deleted inventory delivery old, create inventory form invoice id

    if($invoice_id){

        $CI = &get_instance();
        $CI->load->model('warehouse/warehouse_model');

        if(get_warehouse_option('cancelled_invoice_reverse_inventory_delivery_voucher') == 1 ){
            $CI->warehouse_model->invoice_update_delete_goods_delivery_detail($invoice_id);
        }

        if(get_warehouse_option('auto_create_goods_delivery') == 1){
                //purchase order is approval
            $CI->warehouse_model->auto_create_goods_delivery_with_invoice($invoice_id, true);
        }

    }

    return true;
}

/**
 * Initializes the warehouse customfield.
 *
 * @param      string  $custom_field  The custom field
 */
function init_warehouse_customfield($custom_field = ''){
    $select = '';
    if($custom_field != ''){
        if($custom_field->fieldto == 'warehouse_name'){
            $select = 'selected';
        }
    }

    $html = '<option value="warehouse_name" '.$select.' >'. _l('_warehouse').'</option>';

    echo new_html_entity_decode($html);
}


//update proposal in core

/**
 * warehouse render search
 * @return view 
 */
function warehouse_render_search()
{
    get_instance()->load->view('warehouse/proposal/proposal_search_customer');
}

/**
 * proposal customer add js
 * @return view 
 */
function warehouse_render_search_js()
{
    echo  require 'modules/warehouse/assets/js/proposal_search_customer_js.php';

}

/**
 * proposal related to select
 * @param  string $value 
 * @return string        
 */
function proposal_related_to_select($value)
{

    $selected = '';
    if($value == 'customer_lead'){
        $selected = 'selected';
    }
    echo "<option value='customer_lead' ".$selected.">".
                               _l('customer_lead')."
                           </option>";

}

/**
 * inventory delivery relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function proposal_relation_data($data, $type, $rel_id, $q)
{


    $CI = &get_instance();
    $CI->load->model('warehouse/warehouse_model');

    if ($type == 'customer_lead') {

        return   $data = $CI->warehouse_model->get_client_lead($q, $rel_id);
        
        
    }
    return $data;
}


/**
 * proposal relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function proposal_relation_values($values, $relation)
{


    if ($values['type'] == 'customer_lead') {
        if($relation['proposal_wh'] == 'customer'){
            //customer
            $id='';
            if (is_array($relation)) {
                $values['id']   = 'customer_'.$relation['userid'];
                $values['name'] = '('.$relation['vat'].') '.$relation['company'];
                $id            .= $relation['userid'];
            } else {
                $values['id']   = 'customer_'.$relation->userid;
                $values['name'] = '('.$relation->vat.') '.$relation->company;
                $id             .= $relation->userid;

            }
            $values['link'] = admin_url('clients/client/' . $id);
        }else{

        //lead
             $id='';
            if (is_array($relation)) {
                $values['id']   = 'lead_'.$relation['id'];
                $values['name'] = '('.$relation['vat'].') '.$relation['name'];
                if ($relation['email'] != '') {
                    $values['name'] .= ' - ' . $relation['email'];
                }
                $id .= $relation['id'];
            } else {
                $values['id']   = 'lead_'.$relation->id;
                $values['name'] = '('.$relation->vat.') '.$relation->name;
                if ($relation->email != '') {
                    $values['name'] .= ' - ' . $relation->email;
                }
                $id .= $relation->id;

            }
            $values['link'] = admin_url('leads/index/' . $id);

        }

    }

    return $values;
}


/**
 * proposal search relation values
 * @param  string $rel_id   
 * @param  string $rel_type 
 * @return string           
 */
function proposal_search_relation_values($value)
{
    if($value['rel_type'] == 'customer_lead'){
        $data=[];
        if(preg_match('/^customer_/', $value['rel_id'])){
            $data['rel_id'] = new_str_replace('customer_', '', $value['rel_id']);
            $data['rel_type'] = 'customer';

        }elseif(preg_match('/^lead_/', $value['rel_id'])){

            $data['rel_id'] = new_str_replace('lead_', '', $value['rel_id']);
            $data['rel_type'] = 'lead';
        }

        return $data;
    }

    return $value;
}

/**
 * proposal render input groupt
 * @param  [type] $value 
 * @return [type]        
 */
function proposal_render_input_groupt($value)
{

    echo '<div class="form-group mbot25 items-wrapper select-placeholder input-group-select">
                      <div class="input-group-proposal input-group-select">';

}


/**
 * proposal render input groupb
 * @param  [type] $value 
 * @return [type]        
 */
function proposal_render_input_groupb($value)
{
    $selected = '';
    if($value == ''){
        $selected = ' hide';
    }

    echo '<div class="btn "'. $selected .'" id="input-group-addon-wh" style="opacity :1">
                           <a href="#" onclick="init_lead(); return false;">
                            <i class="fa fa-plus"></i>
                          </a>
                        </div>

                      </div>
                    </div>';

}


/**
 * proposal before create proposal
 * @param  array $value 
 * @return array        
 */
function proposal_before_create_proposal($value)
{

    if(isset($value)){
        if(isset($value['data']['rel_type']) && $value['data']['rel_type'] == 'customer_lead'){

            if(preg_match('/^customer_/', $value['data']['rel_id'])){
                $value['data']['rel_id'] = new_str_replace('customer_', '', $value['data']['rel_id']);
                $value['data']['rel_type'] = 'customer';

            }elseif(preg_match('/^lead_/', $value['data']['rel_id'])){

               $value['data']['rel_id'] = new_str_replace('lead_', '', $value['data']['rel_id']);
                $value['data']['rel_type'] = 'lead';
            }


        }

        if(isset($value['data']['warehouse_id_f'])){
            unset($value['data']['warehouse_id_f']);
        }
        if(isset($value['data']['brand_id'])){
            unset($value['data']['brand_id']);
        }

        if(isset($value['data']['model_id'])){
            unset($value['data']['model_id']);
        }
        if(isset($value['data']['series_id'])){
            unset($value['data']['series_id']);
        }
        
        return $value;
    }

    return $value;
}

/**
 * wh proposal render last tab
 * @return view 
 */
function wh_proposal_render_last_tab()
{
    get_instance()->load->view('warehouse/proposal/proposal_attachmentfile_tab');
}

/**
 * wh proposal render last tab content
 * @return view 
 */
function wh_proposal_render_last_tab_content($value)
{   
    $data=[];
    $data['proposal_id'] = $value;
    get_instance()->load->view('warehouse/proposal/proposal_attachmentfile_tab_content', $data);
}

/**
 * wh proposal load js file
 * @return view 
 */
function wh_proposal_load_js_file()
{
    echo  require 'modules/warehouse/assets/js/wh_proposal_preview_js.php';

}


/**
 * item wh render warehouse
 * @return views 
 */
function item_wh_render_warehouse()
{   
    $data=[];
    $data['warehouses'] = get_warehouse_name();

    get_instance()->load->view('warehouse/proposal/proposal_wh_render_warehouse', $data);
}

/**
 * item wh render series
 * @return view 
 */
function item_wh_render_series()
{   
    $data=[];
    $data['series_id'] = get_series_name();

    get_instance()->load->view('warehouse/proposal/proposal_wh_render_series', $data);
}

/**
 * wh proposal before get item
 * @param  string $select 
 * @return string         
 */
function wh_proposal_before_get_item($select)
{
    $select .=' warehouse_id, series_id, ';

    return $select;
}

/**
 * proposal before update proposal
 * @param  array $value 
 * @return array        
 */
function proposal_before_update_proposal($value)
{

    if(isset($value)){
        if(isset($value['data']['rel_type']) && $value['data']['rel_type'] == 'customer_lead'){

            if(preg_match('/^customer_/', $value['data']['rel_id'])){
                $value['data']['rel_id'] = new_str_replace('customer_', '', $value['data']['rel_id']);
                $value['data']['rel_type'] = 'customer';

            }elseif(preg_match('/^lead_/', $value['data']['rel_id'])){

               $value['data']['rel_id'] = new_str_replace('lead_', '', $value['data']['rel_id']);
                $value['data']['rel_type'] = 'lead';
            }


        }

        if(isset($value['data']['warehouse_id_f'])){
            unset($value['data']['warehouse_id_f']);
        }
        if(isset($value['data']['brand_id'])){
            unset($value['data']['brand_id']);
        }

        if(isset($value['data']['model_id'])){
            unset($value['data']['model_id']);
        }
        if(isset($value['data']['series_id'])){
            unset($value['data']['series_id']);
        }
        
        return $value;
    }

    return $value;
}

/**
 * item wh render series
 * @return view 
 */
function lead_wh_render_input_vat($value)
{   
    $data['vat'] = $value;
    get_instance()->load->view('warehouse/proposal/lead_wh_render_input_vat', $data);
}

/**
 * proposal wh render li
 * @param  array $proposal 
 * @return view           
 */
function proposal_wh_render_li($proposal)
{   

$data=[];
$data['proposal'] = $proposal['proposal'];

    if($proposal['proposal']->processing == NULL){
        //check lead before convert
        //get lead value
        $status_convert=false;
        $lead_id='';

        $CI = &get_instance();
        $CI->load->model('leads_model');

        if($proposal['proposal']->rel_type =='lead'){
            $lead_value = $CI->leads_model->get($proposal['proposal']->rel_id);

            if($lead_value){
                if($lead_value->status == '2'){
                    $status_convert=true;
                    $lead_id = $lead_value->id;
                }
            }
        }
        
        $data['status_convert'] = $status_convert;
        $data['lead_id'] = $lead_id;
        
        get_instance()->load->view('warehouse/proposal/proposal_wh_render_li', $data);
    }

}

/**
 * wh proposal render last tab content
 * @return view 
 */
function wh_proposal_render_lead_to_customer($value)
{   

        $status_convert=false;
        $lead_id='';

        $CI = &get_instance();
        $CI->load->model('leads_model');

    if($value->processing == NULL){
        //check lead before convert
        //get lead value
        $status_convert=false;
        $lead_id='';

        $CI = &get_instance();
        $CI->load->model('leads_model');

        if($value->rel_type =='lead'){
            $lead_value = $CI->leads_model->get($value->rel_id);

            if($lead_value){
                if($lead_value->status == '2'){
                    $status_convert=true;
                    $lead_id = $lead_value->id;
                }
            }
        }
           
        if($status_convert == true){
            $data=[];
            $data['lead'] = $lead_value;
            $data['proposal_id'] = $value->id;
            get_instance()->load->view('warehouse/proposal/wh_proposal_render_lead_to_customer', $data);
        }
    }

}

/**
 * task bookmarks add table column
 * @param  array $table_data 
 * @return array             
 */
function wh_proposal_add_table_column($table_data)
{      
    array_push($table_data, _l('processing'));
    return $table_data;
}

/**
 * contact bookmarks_add_table_row
 * @param  [type] $row  
 * @param  [type] $aRow 
 * @return [type]       
 */
function wh_proposal_add_table_row($row ,$aRow)
{

    $CI = &get_instance();
    $icon = '';

    if ($aRow['processing'] == 1) {
        $status      = _l('processing');
        $label_class = 'info';

        $icon .=  '<span class="label label-' . $label_class . ' s-status proposal-status-processing">' . $status . '</span>';
    }else{
        $icon .= '';
    }


    $row[] = $icon;
    return $row;
}


/**
 * wh proposal table sql columns
 * @param  [type] $column 
 * @return [type]         
 */
function wh_proposal_table_sql_columns($column)
{
    array_push($column, 'processing');
    return $column;
    
}

/**
 * wh proposal add filter column
 * @param  array $array_filter 
 * @param  array $filter_value 
 * @return array               
 */
function wh_proposal_add_filter_column($array_filter, $filter_value)
{   

    if($filter_value['proposals_processing']){
        array_push($array_filter, 'OR processing = 1');
    }

    return $array_filter;
}


/**
 * wh proposals manage add input
 * @return html input 
 */
function wh_proposals_manage_add_input($proposals_processing)
{

    echo form_hidden('proposals_'.'processing', $proposals_processing);

}

/**
 * wh proposals manage add li
 * @return html li 
 */
function wh_proposals_manage_add_li()
{
    get_instance()->load->view('warehouse/proposal/wh_proposals_manage_add_li');

}

/**
 * { filter items list }
 *
 * @param        $data   The data
 */
function filter_items_list_wh($data){

    $CI = &get_instance();
    $CI->load->model('warehouse/warehouse_model');
    
    foreach($data as $group_id => $items){
        foreach($items as $key => $item){
            if($item['parent_id'] == 0 || $item['parent_id'] == null || $item['parent_id'] == ''){
                $child_items = $CI->warehouse_model->get_product_by_parent_id($item['id']);
                if(count($child_items) > 0){
                    unset($data[$group_id][$key]);
                }
            }
        }
    }

    return $data;
}

/**
 * inventory received task modal rel type select
 * @param  object $value
 * @return string
 */
function inventory_received_task_modal_rel_type_select($value) {
    $selected = '';
    if (isset($value) && isset($value['rel_type']) && $value['rel_type'] == 'stock_import') {
        $selected = 'selected';
    }
    echo "<option value='stock_import' " . $selected . ">" .
    _l('stock_import') . "
                           </option>";

}

/**
 * inventory received get relation values description
 * @param  object $values
 * @param  object $relation
 * @return
 */
function inventory_received_get_relation_values($values, $relation = null) {
    if ($values['type'] == 'stock_import') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = $relation['goods_receipt_code'];
        } else {
            $values['id'] = $relation->id;
            $values['name'] = $relation->goods_receipt_code;
        }
        $values['link'] = admin_url('warehouse/manage_purchase/' . $values['id']);
    }

    return $values;
}

/**
 * inventory received get relation data
 * @param  object $data
 * @param  object $obj
 * @return
 */
function inventory_received_get_relation_data($data, $obj) {
    $type = $obj['type'];
    $rel_id = $obj['rel_id'];
    $CI = &get_instance();
    $CI->load->model('warehouse/warehouse_model');

    if ($type == 'stock_import') {
        if ($rel_id != '') {
            $data = $CI->warehouse_model->get_goods_receipt($rel_id);
        } else {
            $data = [];
        }
    }

    return $data;
}

/**
 * inventory delivery task modal rel type select
 * @param  object $value
 * @return string
 */
function inventory_delivery_task_modal_rel_type_select($value) {
    $selected = '';
    if (isset($value) && isset($value['rel_type']) && $value['rel_type'] == 'stock_export') {
        $selected = 'selected';
    }
    echo "<option value='stock_export' " . $selected . ">" .
    _l('stock_export') . "
                           </option>";

}

/**
 * inventory delivery get relation values description
 * @param  object $values
 * @param  object $relation
 * @return
 */
function inventory_delivery_get_relation_values($values, $relation = null) {
    if ($values['type'] == 'stock_export') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = $relation['goods_delivery_code'];
        } else {
            $values['id'] = $relation->id;
            $values['name'] = $relation->goods_delivery_code;
        }
        $values['link'] = admin_url('warehouse/manage_delivery/' . $values['id']);
    }

    return $values;
}

/**
 * inventory delivery get relation data
 * @param  object $data
 * @param  object $obj
 * @return
 */
function inventory_delivery_get_relation_data($data, $obj) {

    $type = $obj['type'];
    $rel_id = $obj['rel_id'];
    $CI = &get_instance();
    $CI->load->model('warehouse/warehouse_model');

    if ($type == 'stock_export') {
        if ($rel_id != '') {
            $data = $CI->warehouse_model->get_goods_delivery($rel_id);
        } else {
            $data = [];
        }
    }
    return $data;
}

/**
 * wh before admin view create invoice
 * @param  [type] $items 
 * @return [type]        
 */
function wh_before_admin_view_create_invoice($items) {
    if(count($items) > 0){
        $CI = &get_instance();
        $CI->load->model('warehouse/warehouse_model');
        $items = $CI->warehouse_model->wh_get_grouped('can_be_sold');
        return $items;
    }
    return $items;
}

/**
 * wh admin invoice ajax search item
 * @param  [type] $data 
 * @return [type]       
 */
function wh_admin_invoice_ajax_search_item($data, $search) {
    $CI = &get_instance();
    $CI->load->model('warehouse/warehouse_model');
    $data = $CI->warehouse_model->wh_commodity_code_search($search, 'rate', 'can_be_sold');
    return $data;
}

/**
 * omni order detail add button header
 * @param  [type] $order 
 * @return [type]        
 */
function omni_order_detail_add_button_header($order){
    if(get_status_modules_wh('omni_sales')){
    //check status
        $CI = &get_instance();
        $CI->load->model('omni_sales/omni_sales_model');
        $CI->load->model('warehouse/warehouse_model');
        $cart = $CI->omni_sales_model->get_cart($order->id);
        $shipment = $CI->warehouse_model->get_shipment_by_order($order->id);
        if(($cart && $cart->status > 0) || isset($shipment)){
            if(isset($shipment)){
                echo '<a href="'.admin_url('warehouse/shipment_detail/' .$order->id).'" class="btn btn-primary mleft5 pull-right"  data-toggle="tooltip" data-title="'._l('wh_shipment_tooltip').'">'._l('wh_shipment').'</a>';
            }
        }
    }
}

/**
 * wh omni sales after invoice added
 * @param  [type] $order_id 
 * @return [type]           
 */
function wh_omni_sales_after_invoice_added($order_id)
{
    if(is_numeric($order_id)){
        $CI = &get_instance();
        $CI->load->model('warehouse/warehouse_model');

        //create shipment info from order
        $CI->warehouse_model->create_shipment_from_order($order_id);
    }
    return true;
}

/**
 * wh omni sales after delivery note added
 * @param  [type] $order_id 
 * @return [type]           
 */
function wh_omni_sales_after_delivery_note_added($order_id)
{
    if($order_id){
        $CI = &get_instance();
        $CI->load->model('warehouse/warehouse_model');
        $shipment = $CI->warehouse_model->get_shipment_by_order($order_id);
        $shipment_id = 0;

        if($shipment){
            $shipment_id = $shipment->id;
            $shipment_log = _l('inventory_delivery_voucher_have_been_created');
            $CI->warehouse_model->log_wh_activity($shipment->id, 'shipment', $shipment_log);
        }else{
            $shipment_id = $CI->warehouse_model->create_shipment_from_order($order_id);
            if(is_numeric($shipment_id)){
                $shipment_log = _l('inventory_delivery_voucher_have_been_created');
                $CI->warehouse_model->log_wh_activity($shipment_id, 'shipment', $shipment_log);
            }
        }

        $CI->warehouse_model->update_shipment_status($shipment_id, ['shipment_status' => 'processing_order']);
    }
    return true;
}

/**
 * wh after purchase order add
 * @param  [type] $purchase_order_id 
 * @return [type]                    
 */
function wh_after_purchase_order_add($purchase_order_id)
{
    if(get_status_modules_wh('purchase')){
        $CI = &get_instance();
        $CI->load->model('purchase/purchase_model');
        $pur_order = $CI->purchase_model->get_pur_order($purchase_order_id);

        if(isset($pur_order) && (int)$pur_order->approve_status == 2 && (get_warehouse_option('auto_create_goods_received') == 1) && (get_warehouse_option('goods_receipt_warehouse') != '') && (get_warehouse_option('goods_receipt_warehouse') != '0')){
            //purchase order is approval
            $CI = &get_instance();
            $CI->load->model('warehouse/warehouse_model');

            $CI->warehouse_model->auto_create_goods_receipt_with_purchase_order(['id' => $purchase_order_id]);
        }
        return true;
    }
}

function init_shipment_portal_menu()
{
    $item ='';
    if(is_client_logged_in() && get_warehouse_option('wh_display_shipment_on_client_portal') == 1){
        $item .= '<li class="customers-nav-item">';
        $item .= '<a href="'.site_url('warehouse/warehouse_client/shipments').'">'._l("wh_shipments").'';        
        $item .= '</a>';
        $item .= '</li>';
    }
    echo new_html_entity_decode($item);

}


function warehouse_client_add_head_components() {
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    if (!(strpos($viewuri, '/warehouse/warehouse_client/shipment_detail') === false)) {
       echo '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/shipments/order_status.css')  .'?v=' . REVISION. '"  rel="stylesheet" type="text/css" />'; 
    }

}


function warehouse_client_add_footer_components() {
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];


}

/**
 * warehouse before invoice deleted
 * @param  [type] $invoice_id 
 * @return [type]             
 */
function warehouse_before_invoice_deleted($invoice_id)
{
    if($invoice_id){
        $CI = &get_instance();
        $CI->load->model('warehouse/warehouse_model');
        $CI->warehouse_model->inventory_cancel_invoice($invoice_id);
    }
    return true;
}

function warehouse_appint(){
    $CI = & get_instance();    
    require_once 'libraries/gtsslib.php';
    $warehouse_api = new WarehouseLic();
    $warehouse_gtssres = $warehouse_api->verify_license(true);    
    if(!$warehouse_gtssres || ($warehouse_gtssres && isset($warehouse_gtssres['status']) && !$warehouse_gtssres['status'])){
         $CI->app_modules->deactivate(WAREHOUSE_MODULE_NAME);
        set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        redirect(admin_url('modules'));
    }    
}

function warehouse_preactivate($module_name){
    if ($module_name['system_name'] == WAREHOUSE_MODULE_NAME) {             
        require_once 'libraries/gtsslib.php';
        $warehouse_api = new WarehouseLic();
        $warehouse_gtssres = $warehouse_api->verify_license();          
        if(!$warehouse_gtssres || ($warehouse_gtssres && isset($warehouse_gtssres['status']) && !$warehouse_gtssres['status'])){
             $CI = & get_instance();
            $data['submit_url'] = $module_name['system_name'].'/gtsverify/activate'; 
            $data['original_url'] = admin_url('modules/activate/'.WAREHOUSE_MODULE_NAME); 
            $data['module_name'] = WAREHOUSE_MODULE_NAME; 
            $data['title'] = "Module License Activation"; 
            echo $CI->load->view($module_name['system_name'].'/activate', $data, true);
            exit();
        }        
    }
}

function warehouse_predeactivate($module_name){
    if ($module_name['system_name'] == WAREHOUSE_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $warehouse_api = new WarehouseLic();
        $warehouse_api->deactivate_license();
    }
}

function warehouse_uninstall($module_name){
    if ($module_name['system_name'] == WAREHOUSE_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $warehouse_api = new WarehouseLic();
        $warehouse_api->deactivate_license();
    }
}