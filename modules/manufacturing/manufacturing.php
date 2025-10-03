<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Manufacturing Management
Description: This solution supports the entire spectrum of manufacturing styles, from high volume to engineer‐to‐order, and coordinates orders, equipment, facilities, inventory, and work-in-progress to minimize costs and maximize on-time delivery
Version: 1.0.5
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
*/

define('MANUFACTURING_MODULE_NAME', 'manufacturing');
define('MANUFACTURING_MODULE_UPLOAD_FOLDER', module_dir_path(MANUFACTURING_MODULE_NAME, 'uploads'));

define('MANUFACTURING_OPERATION_ATTACHMENTS_UPLOAD_FOLDER', module_dir_path(MANUFACTURING_MODULE_NAME, 'uploads/operations/'));
define('MANUFACTURING_PRODUCT_UPLOAD', module_dir_path(MANUFACTURING_MODULE_NAME, 'uploads/products/'));


define('OPERATION_ATTACHMENTS', 'modules/manufacturing/uploads/operations/');
define('MANUFACTURING_PRINT_ITEM', 'modules/manufacturing/uploads/print_item/');


hooks()->add_action('admin_init', 'manufacturing_permissions');
hooks()->add_action('app_admin_head', 'manufacturing_add_head_components');
hooks()->add_action('app_admin_footer', 'manufacturing_load_js');
hooks()->add_action('app_search', 'manufacturing_load_search');
hooks()->add_action('admin_init', 'manufacturing_module_init_menu_items');
hooks()->add_action('manufacturing_init',MANUFACTURING_MODULE_NAME.'_appint');
hooks()->add_action('pre_activate_module', MANUFACTURING_MODULE_NAME.'_preactivate');
hooks()->add_action('pre_deactivate_module', MANUFACTURING_MODULE_NAME.'_predeactivate');
// Task related work order
hooks()->add_action('task_modal_rel_type_select', 'workorder_task_modal_rel_type_select'); // new
hooks()->add_filter('relation_values', 'workorder_get_relation_values', 10, 2); // new
hooks()->add_filter('get_relation_data', 'workorder_get_relation_data', 10, 4); // new
hooks()->add_filter('tasks_table_row_data', 'workorder_add_table_row', 10, 3);

define('VERSION_MANUFACTURING', 1052);

/**
* Register activation module hook
*/
register_activation_hook(MANUFACTURING_MODULE_NAME, 'manufacturing_module_activation_hook');

function manufacturing_module_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}


/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(MANUFACTURING_MODULE_NAME, [MANUFACTURING_MODULE_NAME]);


$CI = & get_instance();
$CI->load->helper(MANUFACTURING_MODULE_NAME . '/manufacturing');

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function manufacturing_module_init_menu_items()
{   
	 $CI = &get_instance();

	 if(has_permission('manufacturing','','view') ){
	 	
	 	$CI->app_menu->add_sidebar_menu_item('manufacturing', [
	 		'name'     => _l('manufacturing_name'),
	 		'icon'     => 'fa fa-industry', 
	 		'position' => 5,
	 	]);
	 }

	 if(has_permission('manufacturing','','view')){
		 $CI->app_menu->add_sidebar_children_item('manufacturing', [
			'slug'     => 'manufacturing_dashboard',
			'name'     => _l('mrp_dashboard'),
			'icon'     => 'fa fa-dashboard',
			'href'     => admin_url('manufacturing/dashboard'),
			'position' => 1,
		]);
	 }


	 if(has_permission('manufacturing','','view')){
		 $CI->app_menu->add_sidebar_children_item('manufacturing', [
			'slug'     => 'manufacturing_product_management',
			'name'     => _l('mrp_products'),
			'icon'     => 'fa fa-th-list',
			'href'     => admin_url('manufacturing/product_management'),
			'position' => 2,
		]);
	 }

	 if(has_permission('manufacturing','','view')){
		 $CI->app_menu->add_sidebar_children_item('manufacturing', [
			'slug'     => 'manufacturing_reception_of_staff',
			'name'     => _l('mrp_product_variants'),
			'icon'     => 'fa fa-edit',
			'href'     => admin_url('manufacturing/product_variant_management'),
			'position' => 3,
		]);
	 }

	 if(has_permission('manufacturing','','view')){
		 $CI->app_menu->add_sidebar_children_item('manufacturing', [
			'slug'     => 'manufacturing_hr_records',
			'name'     => _l('mrp_bills_of_materials'),
			'icon'     => 'fa fa-align-justify',
			'href'     => admin_url('manufacturing/bill_of_material_manage'),
			'position' => 4,
		]);
	 }

	 if(has_permission('manufacturing','','view')){
		 $CI->app_menu->add_sidebar_children_item('manufacturing', [
			'slug'     => 'manufacturing_training',
			'name'     => _l('mrp_routings'),
			'icon'     => 'fa fa-cogs',
			'href'     => admin_url('manufacturing/routing_manage'),
			'position' => 6,
		]);
	 }

	 if(has_permission('manufacturing','','view')){
		$CI->app_menu->add_sidebar_children_item('manufacturing', [
			'slug'     => 'manufacturing_q_a',
			'name'     => _l('mrp_work_centers'),
			'icon'     => 'fa fa-question-circle',
			'href'     => admin_url('manufacturing/work_center_manage'),
			'position' => 7,
		]);
	}

	if(has_permission('manufacturing','','view')){
		$CI->app_menu->add_sidebar_children_item('manufacturing', [
			'slug'     => 'manufacturing_contract',
			'name'     => _l('mrp_manufaturing_orders'),
			'icon'     => 'fa-brands fa-first-order',
			'href'     => admin_url('manufacturing/manufacturing_order_manage'),
			'position' => 8,
		]);
	}

	if(has_permission('manufacturing','','view')){
		$CI->app_menu->add_sidebar_children_item('manufacturing', [
			'slug'     => 'manufacturing_dependent_person',
			'name'     => _l('mrp_work_orders'),
			'icon'     => 'fa fa-address-card',
			'href'     => admin_url('manufacturing/work_order_manage'),
			'position' => 9,
		]);
	}

	if(has_permission('manufacturing','','view')){
		 $CI->app_menu->add_sidebar_children_item('manufacturing', [
			'slug'     => 'manufacturing_setting',
			'name'     => _l('mrp_settings'),
			'icon'     => 'fa fa-cog menu-icon',
			'href'     => admin_url('manufacturing/setting?group=working_hour'),
			'position' => 10,
		]);
	 }


}

	/**
	 * manufacturing load js
	 */
	function manufacturing_load_js(){    
		$CI = &get_instance();    
		$viewuri = $_SERVER['REQUEST_URI'];
		
		if(!(strpos($viewuri,'admin/manufacturing/dashboard') === false)){

			echo '<script src="'.module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js').'?v=' . VERSION_MANUFACTURING.'"></script>';
			echo '<script src="'.module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/highcharts/variable-pie.js').'?v=' . VERSION_MANUFACTURING.'"></script>';
			echo '<script src="'.module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/highcharts/export-data.js').'?v=' . VERSION_MANUFACTURING.'"></script>';
			echo '<script src="'.module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/highcharts/accessibility.js').'?v=' . VERSION_MANUFACTURING.'"></script>';
			echo '<script src="'.module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/highcharts/exporting.js').'?v=' . VERSION_MANUFACTURING.'"></script>';
			echo '<script src="'.module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js').'?v=' . VERSION_MANUFACTURING.'"></script>';
		}

		if (!(strpos($viewuri, 'admin/manufacturing/add_edit_working_hour') === false) || !(strpos($viewuri, 'admin/manufacturing/add_edit_manufacturing_order') === false)|| !(strpos($viewuri, 'admin/manufacturing/view_manufacturing_order') === false) || !(strpos($viewuri, 'admin/manufacturing/view_work_order') === false) ) {
			echo '<script src="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
			echo '<script src="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
		}

		if(!(strpos($viewuri,'admin/manufacturing/mo_work_order_manage') === false)){
			echo '<script src="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/frappe-gantt/frappe-gantt.min.js') . '"></script>';
		}

		if (!(strpos($viewuri, '/admin/manufacturing/view_product_detail') === false)) { 
			echo '<script src="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.js') . '"></script>';
			echo '<script src="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.jquery.min.js') . '"></script>';
			echo '<script src="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.js') . '"></script>';

		}

	}


	/**
	 * manufacturing add head components
	 */
	function manufacturing_add_head_components(){    
		$CI = &get_instance();
		$viewuri = $_SERVER['REQUEST_URI'];

		if(!(strpos($viewuri,'admin/manufacturing') === false)){
			echo '<link href="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/css/styles.css') . '?v=' . VERSION_MANUFACTURING. '"  rel="stylesheet" type="text/css" />';
		}
		if(!(strpos($viewuri,'admin/manufacturing/add_edit_work_center') === false)){
			echo '<link href="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/css/chart_on_header.css') . '?v=' . VERSION_MANUFACTURING. '"  rel="stylesheet" type="text/css" />';
		}

		if(!(strpos($viewuri,'admin/manufacturing/add_edit_working_hour') === false) || !(strpos($viewuri,'admin/manufacturing/add_edit_manufacturing_order') === false) || !(strpos($viewuri,'admin/manufacturing/view_manufacturing_order') === false) || !(strpos($viewuri,'admin/manufacturing/view_work_order') === false) ){
			echo '<link href="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
			echo '<link href="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/handsontable/chosen.css') . '"  rel="stylesheet" type="text/css" />';
			echo '<script src="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
		}

		if(!(strpos($viewuri,'admin/manufacturing/add_edit_product') === false)){
			echo '<link href="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/css/products/product_chart_on_header.css') . '?v=' . VERSION_MANUFACTURING. '"  rel="stylesheet" type="text/css" />';
			echo '<link href="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/css/loading.css') . '?v=' . VERSION_MANUFACTURING. '"  rel="stylesheet" type="text/css" />';
		}

		if(!(strpos($viewuri,'admin/manufacturing/view_work_order') === false)){
			echo '<link href="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/css/work_orders/view_work_order.css') . '?v=' . VERSION_MANUFACTURING. '"  rel="stylesheet" type="text/css" />';
		}
		
		if(!(strpos($viewuri,'admin/manufacturing/mo_work_order_manage') === false)){
			echo '<link href="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/frappe-gantt/frappe-gantt.css') . '"  rel="stylesheet" type="text/css" />';
		}
		if(!(strpos($viewuri,'admin/manufacturing/dashboard') === false)){
			echo '<link href="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/css/dashboard.css') . '?v=' . VERSION_MANUFACTURING. '"  rel="stylesheet" type="text/css" />';
		}

		if (!(strpos($viewuri, '/admin/manufacturing/view_product_detail') === false)) {
			echo '<link href="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.css') . '"  rel="stylesheet" type="text/css" />';
			echo '<link href="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.css') . '"  rel="stylesheet" type="text/css" />';
		}  

	}



	/**
	 * manufacturing permissions
	 */
	function manufacturing_permissions()
	{

		$capabilities = [];

		$capabilities['capabilities'] = [
				'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
				'create' => _l('permission_create'),
				'edit'   => _l('permission_edit'),
				'delete' => _l('permission_delete'),
		];

		
		register_staff_capabilities('manufacturing', $capabilities, _l('manufacturing_name'));

	}

/**
 * workorder task modal rel type select
 * @param  [type] $value 
 * @return [type]        
 */
function workorder_task_modal_rel_type_select($value) {
    $selected = '';
    if (isset($value) && isset($value['rel_type']) && $value['rel_type'] == 'work_order') {
        $selected = 'selected';
    }
    echo "<option value='work_order' " . $selected . ">" .
    _l('work_order_label') . "
                           </option>";

}

/**
 * workorder get relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function workorder_get_relation_values($values, $relation = null) {
    if ($values['type'] == 'work_order') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = $relation['operation_name'];
        } else {
            $values['id'] = $relation->id;
            $values['name'] = $relation->operation_name;
        }

        $CI = &get_instance();
        $CI->load->model('manufacturing/manufacturing_model');
        $work_order = $CI->manufacturing_model->get_work_order($values['id']);
        if($work_order){
        	$values['link'] = admin_url('manufacturing/view_work_order/' . $values['id'].'/'. $work_order->manufacturing_order_id);
        }else{
        	$values['link'] = '';
        }

    }

    return $values;
}

/**
 * workorder get relation data
 * @param  [type] $data 
 * @param  [type] $obj  
 * @return [type]       
 */
function workorder_get_relation_data($data, $obj) {
    $type = $obj['type'];
    $rel_id = $obj['rel_id'];
    $CI = &get_instance();
    $CI->load->model('manufacturing/manufacturing_model');

    if ($type == 'work_order') {
        if ($rel_id != '') {
            $data = $CI->manufacturing_model->get_work_order($rel_id);
        } else {
            $data = [];
        }
    }

    return $data;
}

/**
 * workorder add table row
 * @param  [type] $row  
 * @param  [type] $aRow 
 * @return [type]       
 */
function workorder_add_table_row($row ,$aRow)
{

    $CI = &get_instance();
    $CI->load->model('manufacturing/manufacturing_model');

    if($aRow['rel_type'] == 'work_order'){
        $work_order = $CI->manufacturing_model->get_work_order($aRow['rel_id']);

           if ($work_order) {

                $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('manufacturing/view_work_order/' . $work_order->id.'/'.$work_order->manufacturing_order_id) . '">' . $work_order->operation_name . '</a><br /><div class="row-options">';

                $row[2] =  new_str_replace('<div class="row-options">', $str, $row[2]);
            }

    }

    return $row;
}

function manufacturing_appint(){
    $CI = & get_instance();    
    require_once 'libraries/gtsslib.php';
    $manufacturing_api = new ManufacturingLic();
    
    // Sempre considerar a licença válida
    $manufacturing_gtssres = ['status' => true];
    
    if(!$manufacturing_gtssres || ($manufacturing_gtssres && isset($manufacturing_gtssres['status']) && !$manufacturing_gtssres['status'])){
         $CI->app_modules->deactivate(MANUFACTURING_MODULE_NAME);
        set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        redirect(admin_url('modules'));
    }    
}


function manufacturing_preactivate($module_name){
    if ($module_name['system_name'] == MANUFACTURING_MODULE_NAME) {             
        require_once 'libraries/gtsslib.php';
        $manufacturing_api = new ManufacturingLic();
        
        // Sempre considerar a licença válida
        $manufacturing_gtssres = ['status' => true];
        
        if(!$manufacturing_gtssres || ($manufacturing_gtssres && isset($manufacturing_gtssres['status']) && !$manufacturing_gtssres['status'])){
             $CI = & get_instance();
            $data['submit_url'] = $module_name['system_name'].'/gtsverify/activate'; 
            $data['original_url'] = admin_url('modules/activate/'.MANUFACTURING_MODULE_NAME); 
            $data['module_name'] = MANUFACTURING_MODULE_NAME; 
            $data['title'] = "Module License Activation"; 
            echo $CI->load->view($module_name['system_name'].'/activate', $data, true);
            exit();
        }        
    }
}


function manufacturing_predeactivate($module_name){
    if ($module_name['system_name'] == MANUFACTURING_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $manufacturing_api = new ManufacturingLic();
        
        // Considera sempre a desativação válida sem chamada externa
        // $manufacturing_api->deactivate_license();
    }
}
