<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Handles upload for expenses receipt
 * @param  mixed $id expense id
 * @return void
 */
function ma_handle_asset_attachments($id)
{
    if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES['file']['error']);
        die;
    }
    $path = MA_MODULE_UPLOAD_FOLDER . '/assets/' . $id . '/';
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

                $CI->misc_model->add_attachment_to_database($id, 'ma_asset', $attachment);
            }
        }
    }
}

function ma_get_category_name($id){
    $CI             = &get_instance();
    $sql = 'select * from '.db_prefix().'ma_categories where id = "'.$id.'"';
    $category = $CI->db->query($sql)->row();

    if($category){
        return $category->name;
    }else{
        return '';
    }
}


function ma_get_email_template_name($id){
    $CI             = &get_instance();
    $sql = 'select * from '.db_prefix().'ma_email_templates where id = "'.$id.'"';
    $category = $CI->db->query($sql)->row();

    if($category){
        return $category->name;
    }else{
        return '';
    }
}

function ma_get_asset_name($id){
    $CI             = &get_instance();
    $sql = 'select * from '.db_prefix().'ma_assets where id = "'.$id.'"';
    $category = $CI->db->query($sql)->row();

    if($category){
        return $category->name;
    }else{
        return '';
    }
}

function ma_get_text_message_name($id){
    $CI             = &get_instance();
    $sql = 'select * from '.db_prefix().'ma_text_messages where id = "'.$id.'"';
    $category = $CI->db->query($sql)->row();

    if($category){
        return $category->name;
    }else{
        return '';
    }
}

function ma_get_campaign_name($id){
    $CI             = &get_instance();
    $sql = 'select * from '.db_prefix().'ma_campaigns where id = "'.$id.'"';
    $category = $CI->db->query($sql)->row();

    if($category){
        return $category->name;
    }else{
        return '';
    }
}


function ma_lead_total_point($id){
    $CI             = &get_instance();
    
    $sql = 'select SUM(point) as total, lead_id from '.db_prefix().'ma_point_action_logs where lead_id = "'.$id.'" group by lead_id';
    $point = $CI->db->query($sql)->row();

    if($point){
        return $point->total;
    }else{
        return 0;
    }
}


function ma_client_total_point($id){
    $CI             = &get_instance();
   
    $sql = 'select SUM(point) as total, client_id from '.db_prefix().'ma_point_action_logs where client_id = "'.$id.'" group by client_id';
    $point = $CI->db->query($sql)->row();

    if($point){
        return $point->total;
    }else{
        return 0;
    }
}


function ma_lead_total_point_by_campaign($lead_id, $campaign_id){
    $CI             = &get_instance();
   
    $sql = 'select SUM(point) as total, lead_id from '.db_prefix().'ma_point_action_logs where lead_id = "'.$lead_id.'" and campaign_id = "'.$campaign_id.'" group by lead_id';
    $point = $CI->db->query($sql)->row();

    if($point){
        return $point->total;
    }else{
        return 0;
    }
}

/**
 * Check if staff member can view segment
 * @param  mixed $id segment id
 * @param  mixed $staff_id
 * @return boolean
 */
function user_can_view_segment($id, $staff_id = false)
{
    $CI = &get_instance();

    $staff_id = $staff_id ? $staff_id : get_staff_user_id();

    if (has_permission('ma_segments', $staff_id, 'view')) {
        return true;
    }

    $CI->db->select('id, addedfrom');
    $CI->db->from(db_prefix() . 'ma_segments');
    $CI->db->where('id', $id);
    $segment = $CI->db->get()->row();

    if ((has_permission('ma_segments', $staff_id, 'view_own') && $segment->addedfrom == $staff_id)) {
        return true;
    }

    return false;
}

/**
 * Check if staff member can view stage
 * @param  mixed $id stage id
 * @param  mixed $staff_id
 * @return boolean
 */
function user_can_view_stage($id, $staff_id = false)
{
    $CI = &get_instance();

    $staff_id = $staff_id ? $staff_id : get_staff_user_id();

    if (has_permission('ma_stages', $staff_id, 'view')) {
        return true;
    }

    $CI->db->select('id, addedfrom');
    $CI->db->from(db_prefix() . 'ma_stages');
    $CI->db->where('id', $id);
    $stage = $CI->db->get()->row();

    if ((has_permission('ma_stages', $staff_id, 'view_own') && $stage->addedfrom == $staff_id)) {
        return true;
    }

    return false;
}

/**
 * Check if staff member can view campaign
 * @param  mixed $id campaign id
 * @param  mixed $staff_id
 * @return boolean
 */
function user_can_view_campaign($id, $staff_id = false)
{
    $CI = &get_instance();

    $staff_id = $staff_id ? $staff_id : get_staff_user_id();

    if (has_permission('ma_campaigns', $staff_id, 'view')) {
        return true;
    }

    $CI->db->select('id, addedfrom');
    $CI->db->from(db_prefix() . 'ma_campaigns');
    $CI->db->where('id', $id);
    $campaign = $CI->db->get()->row();

    if ((has_permission('ma_campaigns', $staff_id, 'view_own') && $campaign->addedfrom == $staff_id)) {
        return true;
    }

    return false;
}

/**
 * Check if staff member can view email
 * @param  mixed $id email id
 * @param  mixed $staff_id
 * @return boolean
 */
function user_can_view_email($id, $staff_id = false)
{
    $CI = &get_instance();

    $staff_id = $staff_id ? $staff_id : get_staff_user_id();

    if (has_permission('ma_channels', $staff_id, 'view')) {
        return true;
    }

    $CI->db->select('id, addedfrom');
    $CI->db->from(db_prefix() . 'ma_emails');
    $CI->db->where('id', $id);
    $email = $CI->db->get()->row();

    if ((has_permission('ma_channels', $staff_id, 'view_own') && $email->addedfrom == $staff_id)) {
        return true;
    }

    return false;
}

/**
 * Check if staff member can view sms
 * @param  mixed $id sms id
 * @param  mixed $staff_id
 * @return boolean
 */
function user_can_view_sms($id, $staff_id = false)
{
    $CI = &get_instance();

    $staff_id = $staff_id ? $staff_id : get_staff_user_id();

    if (has_permission('ma_channels', $staff_id, 'view')) {
        return true;
    }

    $CI->db->select('id, addedfrom');
    $CI->db->from(db_prefix() . 'ma_sms');
    $CI->db->where('id', $id);
    $sms = $CI->db->get()->row();

    if ((has_permission('ma_channels', $staff_id, 'view_own') && $sms->addedfrom == $staff_id)) {
        return true;
    }

    return false;
}


/**
 * Check if staff member can view point_action
 * @param  mixed $id point_action id
 * @param  mixed $staff_id
 * @return boolean
 */
function user_can_view_point_action($id, $staff_id = false)
{
    $CI = &get_instance();

    $staff_id = $staff_id ? $staff_id : get_staff_user_id();

    if (has_permission('ma_points', $staff_id, 'view')) {
        return true;
    }

    $CI->db->select('id, addedfrom');
    $CI->db->from(db_prefix() . 'ma_point_actions');
    $CI->db->where('id', $id);
    $point_action = $CI->db->get()->row();

    if ((has_permission('ma_points', $staff_id, 'view_own') && $point_action->addedfrom == $staff_id)) {
        return true;
    }

    return false;
}


/**
 * Check if staff member can view asset
 * @param  mixed $id asset id
 * @param  mixed $staff_id
 * @return boolean
 */
function user_can_view_asset($id, $staff_id = false)
{
    $CI = &get_instance();

    $staff_id = $staff_id ? $staff_id : get_staff_user_id();

    if (has_permission('ma_components', $staff_id, 'view')) {
        return true;
    }

    $CI->db->select('id, addedfrom');
    $CI->db->from(db_prefix() . 'ma_assets');
    $CI->db->where('id', $id);
    $asset = $CI->db->get()->row();

    if ((has_permission('ma_components', $staff_id, 'view_own') && $asset->addedfrom == $staff_id)) {
        return true;
    }

    return false;
}

/**
 * Custom CSS
 * @param  string $main_area clients or admin area options
 * @return null
 */
function ma_form_style_custom_css()
{
    $custom_css_admin_and_clients_area = get_option('ma_form_style');
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

function ma_get_segment_name($id){
    $CI             = &get_instance();
    $sql = 'select * from '.db_prefix().'ma_segments where id = "'.$id.'"';
    $segment = $CI->db->query($sql)->row();

    if($segment){
        return $segment->name;
    }else{
        return '';
    }
}

function ma_get_stage_name($id){
    $CI             = &get_instance();
    $sql = 'select * from '.db_prefix().'ma_stages where id = "'.$id.'"';
    $stage = $CI->db->query($sql)->row();

    if($stage){
        return $stage->name;
    }else{
        return '';
    }
}

function ma_get_leads_status_name($id){
    $CI             = &get_instance();
    $sql = 'select * from '.db_prefix().'leads_status where id = "'.$id.'"';
    $leads_status = $CI->db->query($sql)->row();

    if($leads_status){
        return $leads_status->name;
    }else{
        return '';
    }
}