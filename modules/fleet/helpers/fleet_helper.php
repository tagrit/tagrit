<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Handles upload for driver documents
 * @param  mixed $id expense id
 * @return void
 */
function handle_driver_document_attachments($id)
{
    if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES['file']['error']);
        die;
    }
    $path = FLEET_MODULE_UPLOAD_FOLDER . '/driver_documents/' . $id . '/';
    $CI   = & get_instance();

    if (isset($_FILES['file']['name'])) {

        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = $_FILES['file']['name'];
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                    ];

                $CI->misc_model->add_attachment_to_database($id, 'fle_driver_document', $attachment);
            }
        }
    }
}


/**
 * render booking status html
 * @param  [type]  $id           
 * @param  [type]  $type         
 * @param  string  $status_value 
 * @param  boolean $ChangeStatus 
 * @return [type]                
 */
function fleet_render_status_html($id, $type, $status_value = '', $ChangeStatus = true)
{
    $status = '';
    $statuses = [];
    if($type == 'booking'){
        $statuses = fleet_booking_status();
    }else if($type == 'logbook'){
        $statuses = fleet_logbook_status();
    }else if($type == 'work_order'){
        $statuses = fleet_work_order_status();
    }else{
        $statuses = fleet_booking_status();
    }

    foreach ($statuses as $s) {
        if ($s['id'] == $status_value) {
            $status = $s;
            break;
        }
    }

    $outputStatus    = '';

    $outputStatus .= '<span class="inline-block label" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '" task-status-table="' . $status_value . '">';
    $outputStatus .= $status['name'];
    $canChangeStatus = (has_permission('service_management', '', 'edit') || is_admin());

    if ($canChangeStatus && $ChangeStatus) {
        $outputStatus .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
        $outputStatus .= '<a href="#" class="dropdown-toggle text-dark dropdown-font-size" id="tableTaskStatus-' . $id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $outputStatus .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
        $outputStatus .= '</a>';

        $outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskStatus-' . $id . '">';
        foreach ($statuses as $taskChangeStatus) {
            if ($status_value != $taskChangeStatus['id']) {
                $outputStatus .= '<li>
                <a href="#" onclick="'.$type.'_status_mark_as(\'' . $taskChangeStatus['id'] . '\',' . $id . '); return false;">
                ' . _l('task_mark_as', $taskChangeStatus['name']) . '
                </a>
                </li>';
            }
        }
        $outputStatus .= '</ul>';
        $outputStatus .= '</div>';
    }

    $outputStatus .= '</span>';

    return $outputStatus;
}

/**
 * booking status
 * @param  string $status 
 * @return [type]         
 */
function fleet_booking_status()
{
    $statuses = [

        [
            'id'             => 'new',
            'color'          => '#2196f3',
            'name'           => _l('new'),
            'order'          => 1,
            'filter_default' => false,
        ],
        [
            'id'             => 'approved',
            'color'          => '#3db8da',
            'name'           => _l('approved'),
            'order'          => 2,
            'filter_default' => false,
        ],
        [
            'id'             => 'rejected',
            'color'          => '#4caf50',
            'name'           => _l('rejected'),
            'order'          => 3,
            'filter_default' => true,
        ],
        [
            'id'             => 'processing',
            'color'          => '#3b82f6',
            'name'           => _l('processing'),
            'order'          => 4,
            'filter_default' => false,
        ],
        [
            'id'             => 'complete',
            'color'          => '#84c529',
            'name'           => _l('complete'),
            'order'          => 5,
            'filter_default' => false,
        ],
        [
            'id'             => 'cancelled',
            'color'          => '#d71a1a',
            'name'           => _l('cancelled'),
            'order'          => 6,
            'filter_default' => false,
        ],
        
    ];

    usort($statuses, function ($a, $b) {
        return $a['order'] - $b['order'];
    });

    return $statuses;
}


/**
 * logbook status
 * @param  string $status 
 * @return [type]         
 */
function fleet_logbook_status()
{

    $statuses = [

        [
            'id'             => 'new',
            'color'          => '#2196f3',
            'name'           => _l('new'),
            'order'          => 1,
            'filter_default' => false,
        ],
        [
            'id'             => 'processing',
            'color'          => '#3b82f6',
            'name'           => _l('processing'),
            'order'          => 2,
            'filter_default' => false,
        ],
        [
            'id'             => 'complete',
            'color'          => '#84c529',
            'name'           => _l('complete'),
            'order'          => 3,
            'filter_default' => false,
        ],
        [
            'id'             => 'cancelled',
            'color'          => '#d71a1a',
            'name'           => _l('cancelled'),
            'order'          => 4,
            'filter_default' => false,
        ],
        
    ];

    usort($statuses, function ($a, $b) {
        return $a['order'] - $b['order'];
    });

    return $statuses;
}

/**
 * work_order status
 * @param  string $status 
 * @return [type]         
 */
function fleet_work_order_status()
{

    $statuses = [

        [
            'id'             => 'open',
            'color'          => '#2196f3',
            'name'           => _l('open'),
            'order'          => 1,
            'filter_default' => false,
        ],
        [
            'id'             => 'in_progress',
            'color'          => '#3b82f6',
            'name'           => _l('in_progress'),
            'order'          => 2,
            'filter_default' => false,
        ],
        [
            'id'             => 'parts_ordered',
            'color'          => '#ffa500',
            'name'           => _l('parts_ordered'),
            'order'          => 3,
            'filter_default' => false,
        ],
        [
            'id'             => 'complete',
            'color'          => '#84c529',
            'name'           => _l('complete'),
            'order'          => 4,
            'filter_default' => false,
        ],
        
    ];

    usort($statuses, function ($a, $b) {
        return $a['order'] - $b['order'];
    });

    return $statuses;
}

/**
     * [new_html_entity_decode description]
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
if (!function_exists('new_html_entity_decode')) {
    
    function new_html_entity_decode($str){
        return html_entity_decode($str ?? '');
    }
}


/**
 * Gets the part name by id.
 *
 * @param        $id   The id
 */
if (!function_exists('fleet_get_part_name_by_id')) {
    function fleet_get_part_name_by_id($id){
        $CI             = &get_instance();
        $CI->load->model('fleet/fleet_model');
        $part = $CI->fleet_model->get_part($id);

        if($part){
            return $part->name;
        }

        return '';
    }
}

/**
 * Gets the part type name by id.
 *
 * @param        $id   The id
 */
if (!function_exists('fleet_get_part_type_name_by_id')) {
    function fleet_get_part_type_name_by_id($id){
        $CI             = &get_instance();
        $CI->load->model('fleet/fleet_model');
        $part = $CI->fleet_model->get_data_part_types($id);

        if($part){
            return $part->name;
        }

        return '';
    }
}

/**
 * Gets the part group name by id.
 *
 * @param        $id   The id
 */
if (!function_exists('fleet_get_part_group_name_by_id')) {
    function fleet_get_part_group_name_by_id($id){
        $CI             = &get_instance();
        $CI->load->model('fleet/fleet_model');
        $part = $CI->fleet_model->get_data_part_groups($id);

        if($part){
            return $part->name;
        }

        return '';
    }
}


/**
 * Gets the vehicle name by id.
 *
 * @param        $id   The id
 */
if (!function_exists('fleet_get_vehicle_name_by_id')) {
    function fleet_get_vehicle_name_by_id($id){
        $CI             = &get_instance();
        $CI->load->model('fleet/fleet_model');
        $part = $CI->fleet_model->get_vehicle($id);

        if($part){
            return $part->name;
        }

        return '';
    }
}

/**
 * Gets the vehicle group name by id.
 *
 * @param        $id   The id
 */
if (!function_exists('fleet_get_vehicle_group_name_by_id')) {
    function fleet_get_vehicle_group_name_by_id($id){
        $CI             = &get_instance();
        $CI->load->model('fleet/fleet_model');
        $vehicle = $CI->fleet_model->get_data_vehicle_groups($id);

        if($vehicle){
            return $vehicle->name;
        }

        return '';
    }
}

/**
 * Gets the vehicle group name by id.
 *
 * @param        $id   The id
 */
if (!function_exists('fleet_get_vehicle_type_name_by_id')) {
    function fleet_get_vehicle_type_name_by_id($id){
        $CI             = &get_instance();
        $CI->load->model('fleet/fleet_model');
        $vehicle = $CI->fleet_model->get_data_vehicle_types($id);

        if($vehicle){
            return $vehicle->name;
        }

        return '';
    }
}

/**
 * Gets the vehicle group name by id.
 *
 * @param        $id   The id
 */
if (!function_exists('fleet_get_vehicle_current_meter')) {
    function fleet_get_vehicle_current_meter($id){
        $CI             = &get_instance();
        $CI->load->model('fleet/fleet_model');
        $current_meter = $CI->fleet_model->get_vehicle_current_meter($id);

        if($current_meter){
            return $current_meter;
        }

        return '';
    }
}

/**
 * Gets the vehicle group name by id.
 *
 * @param        $id   The id
 */
if (!function_exists('fleet_get_vehicle_current_meter_date')) {
    function fleet_get_vehicle_current_meter_date($id){
        $CI             = &get_instance();
        $CI->load->model('fleet/fleet_model');
        $current_meter_date = $CI->fleet_model->get_vehicle_current_meter_date($id);

        if($current_meter_date){
            return $current_meter_date;
        }

        return '';
    }
}


/**
 * Gets the vehicle group name by id.
 *
 * @param        $id   The id
 */
if (!function_exists('fleet_get_vehicle_current_operator')) {
    function fleet_get_vehicle_current_operator($id){
        $CI             = &get_instance();
        $CI->load->model('fleet/fleet_model');
        $current_operator = $CI->fleet_model->get_vehicle_current_operator($id);

        if($current_operator){
            return get_staff_full_name($current_operator);
        }

        return '';
    }
}

/**
 * get status modules wh
 * @param  string $module_name 
 * @return boolean             
 */
if (!function_exists('fleet_get_status_modules')) {
    function fleet_get_status_modules($module_name){
        $CI             = &get_instance();

        $sql = 'select * from '.db_prefix().'modules where module_name = "'.$module_name.'" AND active =1 ';
        $module = $CI->db->query($sql)->row();
        if($module){
            return true;
        }else{
            return false;
        }
    }
}
