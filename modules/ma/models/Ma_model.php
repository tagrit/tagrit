<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Marketing Automation model
 */
class Ma_model extends App_Model
{
	public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add new category
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_category($data)
    {
        $data['description'] = nl2br($data['description']);
        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'ma_categories', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * update new category
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_category($data, $id)
    {
        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_categories', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete category from database, if used return array with key referenced
     */
    public function delete_category($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_categories');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get category
     * @param  mixed $id category id (Optional)
     * @return mixed     object or array
     */
    public function get_category($id = '', $type = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'ma_categories')->row();
        }

        if ($type != '') {
            $this->db->where('type', $type);
        }

        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'ma_categories')->result_array();
    }

    /**
     * Add new stage
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_stage($data)
    {
        $data['description'] = nl2br($data['description']);
        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'ma_stages', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * update new stage
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_stage($data, $id)
    {
        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_stages', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete stage from database
     */
    public function delete_stage($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_stages');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('stage_id', $id);
            $this->db->delete(db_prefix() . 'ma_lead_stages');

            return true;
        }

        return false;
    }

    /**
     * Get stage
     * @param  mixed $id stage id (Optional)
     * @return mixed     object or array
     */
    public function get_stage($id = '', $where = [], $count = false, $is_kanban = false)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            $stage = $this->db->get(db_prefix() . 'ma_stages')->row();

            return $stage;
        }

        $this->db->where($where);

        if($is_kanban == false){
            $this->db->where('published', 1);
        }
        $this->db->order_by('name', 'asc');

        if($count == true){
            return $this->db->count_all_results(db_prefix() . 'ma_stages');
        }else{
            return $this->db->get(db_prefix() . 'ma_stages')->result_array();
        }
    }

    /**
     * Add new segment
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_segment($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if (isset($data['type'])) {
            $type = $data['type'];
            unset($data['type']);
        }

        if (isset($data['customer_type'])) {
            $customer_type = $data['customer_type'];
            unset($data['customer_type']);
        }

        if (isset($data['sub_type_1'])) {
            $sub_type_1 = $data['sub_type_1'];
            unset($data['sub_type_1']);
        }

        if (isset($data['sub_type_2'])) {
            $sub_type_2 = $data['sub_type_2'];
            unset($data['sub_type_2']);
        }

        if (isset($data['value'])) {
            $value = $data['value'];
            unset($data['value']);
        }

        $data['description'] = nl2br($data['description']);
        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'ma_segments', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if($type){
                foreach($type as $k => $t){
                    $node = [];
                    $node['segment_id'] = $insert_id;
                    $node['type'] = $t;
                    $node['customer_type'] = $customer_type[$k];
                    $node['sub_type_1'] = $sub_type_1[$k];
                    $node['sub_type_2'] = $sub_type_2[$k];
                    $node['value'] = $value[$k];

                    $this->db->insert(db_prefix() . 'ma_segment_filters', $node);
                }
            }

            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get segment
     * @param  mixed $id segment id (Optional)
     * @return mixed     object or array
     */
    public function get_segment($id = '', $where = [], $count = false, $is_kanban = false)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            $segment = $this->db->get(db_prefix() . 'ma_segments')->row();

            if($segment){
                $this->db->where('segment_id', $id);
                $segment->filters = $this->db->get(db_prefix() . 'ma_segment_filters')->result_array();
            }

            return $segment;
        }

        $this->db->where($where);

        if($is_kanban == false){
            $this->db->where('published', 1);
        }
        $this->db->order_by('name', 'asc');

        if($count == true){
            return $this->db->count_all_results(db_prefix() . 'ma_segments');
        }else{
            return $this->db->get(db_prefix() . 'ma_segments')->result_array();
        }
    }

    /**
     * Add new segment
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_segment($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if (isset($data['customer_type'])) {
            $customer_type = $data['customer_type'];
            unset($data['customer_type']);
        }

        if (isset($data['type'])) {
            $type = $data['type'];
            unset($data['type']);
        }

        if (isset($data['sub_type_1'])) {
            $sub_type_1 = $data['sub_type_1'];
            unset($data['sub_type_1']);
        }

        if (isset($data['sub_type_2'])) {
            $sub_type_2 = $data['sub_type_2'];
            unset($data['sub_type_2']);
        }

        if (isset($data['value'])) {
            $value = $data['value'];
            unset($data['value']);
        }

        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_segments', $data);

        $this->db->where('segment_id', $id);
        $this->db->delete(db_prefix() . 'ma_segment_filters');

        if($type){
            foreach($type as $k => $t){
                $node = [];
                $node['segment_id'] = $id;
                $node['type'] = $t;
                $node['customer_type'] = $customer_type[$k];
                $node['sub_type_1'] = $sub_type_1[$k];
                $node['sub_type_2'] = $sub_type_2[$k];
                $node['value'] = $value[$k];

                $this->db->insert(db_prefix() . 'ma_segment_filters', $node);
            }
        }

        return true;
    }

    /**
     * delete segment
     * @param  integer ID
     * @return mixed
     */
    public function delete_segment($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_segments');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('segment_id', $id);
            $this->db->delete(db_prefix() . 'ma_segment_filters');

            $this->db->where('segment_id', $id);
            $this->db->delete(db_prefix() . 'ma_lead_segments');

            return true;
        }

        return false;
    }

    /**
     * add form
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_form($data)
    {
        $data                       = $this->_do_lead_form_responsibles($data);
        $data['success_submit_msg'] = nl2br($data['success_submit_msg']);
        $data['form_key']           = app_generate_hash();

        $data['create_task_on_duplicate'] = (int) isset($data['create_task_on_duplicate']);
        $data['mark_public']              = (int) isset($data['mark_public']);

        if (isset($data['allow_duplicate'])) {
            $data['allow_duplicate']           = 1;
            $data['track_duplicate_field']     = '';
            $data['track_duplicate_field_and'] = '';
            $data['create_task_on_duplicate']  = 0;
        } else {
            $data['allow_duplicate'] = 0;
        }

        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'ma_forms', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {

            return $insert_id;
        }

        return false;
    }

    /**
     * update form
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_form($id, $data)
    {
        $data                       = $this->_do_lead_form_responsibles($data);
        $data['success_submit_msg'] = nl2br($data['success_submit_msg']);

        $data['create_task_on_duplicate'] = (int) isset($data['create_task_on_duplicate']);
        $data['mark_public']              = (int) isset($data['mark_public']);

        if (isset($data['allow_duplicate'])) {
            $data['allow_duplicate']           = 1;
            $data['track_duplicate_field']     = '';
            $data['track_duplicate_field_and'] = '';
            $data['create_task_on_duplicate']  = 0;
        } else {
            $data['allow_duplicate'] = 0;
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_forms', $data);

        return ($this->db->affected_rows() > 0 ? true : false);
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete stage from database
     */
    public function delete_form($id)
    {   
        $affected_rows = 0;
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_forms');
        if ($this->db->affected_rows() > 0) {
            $affected_rows++;
        }

        $this->db->where('from_ma_form_id', $id);
        $this->db->update(db_prefix() . 'leads', [
            'from_ma_form_id' => 0,
        ]);

        if ($this->db->affected_rows() > 0) {
            $affected_rows++;
        }

        if ($affected_rows > 0) {
            return true;
        }

        return false;
    }

    /**
     *  do lead form responsibles
     * @param  array
     * @return array
     */
    private function _do_lead_form_responsibles($data)
    {
        if (isset($data['notify_lead_imported'])) {
            $data['notify_lead_imported'] = 1;
        } else {
            $data['notify_lead_imported'] = 0;
        }

        if ($data['responsible'] == '') {
            $data['responsible'] = 0;
        }
        if ($data['notify_lead_imported'] != 0) {
            if ($data['notify_type'] == 'specific_staff') {
                if (isset($data['notify_ids_staff'])) {
                    $data['notify_ids'] = serialize($data['notify_ids_staff']);
                    unset($data['notify_ids_staff']);
                } else {
                    $data['notify_ids'] = serialize([]);
                    unset($data['notify_ids_staff']);
                }
                if (isset($data['notify_ids_roles'])) {
                    unset($data['notify_ids_roles']);
                }
            } else {
                if (isset($data['notify_ids_roles'])) {
                    $data['notify_ids'] = serialize($data['notify_ids_roles']);
                    unset($data['notify_ids_roles']);
                } else {
                    $data['notify_ids'] = serialize([]);
                    unset($data['notify_ids_roles']);
                }
                if (isset($data['notify_ids_staff'])) {
                    unset($data['notify_ids_staff']);
                }
            }
        } else {
            $data['notify_ids']  = serialize([]);
            $data['notify_type'] = null;
            if (isset($data['notify_ids_staff'])) {
                unset($data['notify_ids_staff']);
            }
            if (isset($data['notify_ids_roles'])) {
                unset($data['notify_ids_roles']);
            }
        }

        return $data;
    }

    /**
     * get form
     * @param  array or String
     * @return object
     */
    public function get_form($where)
    {
        $this->db->where($where);

        return $this->db->get(db_prefix() . 'ma_forms')->row();
    }

    /**
     * get forms
     * @param  array
     * @return array
     */
    public function get_forms($where = [])
    {
        $this->db->where($where);

        return $this->db->get(db_prefix() . 'ma_forms')->result_array();
    }

    /**
     * Add new asset
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_asset($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        $this->db->insert(db_prefix() . 'ma_assets', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get asset
     * @param  mixed $id asset id (Optional)
     * @return mixed     object or array
     */
    public function get_asset($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            $asset = $this->db->get(db_prefix() . 'ma_assets')->row();

            if($asset){
                $asset->attachment            = '';
                $asset->filetype              = '';
                $asset->attachment_added_from = 0;

                $this->db->where('rel_id', $id);
                $this->db->where('rel_type', 'ma_asset');
                $file = $this->db->get(db_prefix() . 'files')->row();

                if ($file) {
                    $asset->attachment            = $file->file_name;
                    $asset->filetype              = $file->filetype;
                    $asset->attachment_added_from = $file->staffid;
                }

            }

            return $asset;
        }
        $this->db->where('published', 1);
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'ma_assets')->result_array();
    }

    /**
     * Add new asset
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_asset($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_assets', $data);

        if($this->db->affected_rows() > 0){ 
            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete asset from database, if used return array with key referenced
     */
    public function delete_asset($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_assets');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('asset_id', $id);
            $this->db->delete(db_prefix() . 'ma_asset_download_logs');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'ma_asset');
            $this->db->delete(db_prefix().'files');

            if (is_dir(MA_MODULE_UPLOAD_FOLDER .'/assets/'. $id)) {
                delete_dir(MA_MODULE_UPLOAD_FOLDER .'/assets/'. $id);
            }

            return true;
        }

        return false;
    }

    /**
     * Add new point_action
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_point_action($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if (!isset($data['add_points_by_country'])) {
            $data['add_points_by_country'] = 0;
        }

        if (isset($data['country'])) {
            $country = $data['country'];
            unset($data['country']);
        }

        if (isset($data['list_change_points'])) {
            $list_change_points = $data['list_change_points'];
            unset($data['list_change_points']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        $this->db->insert(db_prefix() . 'ma_point_actions', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            foreach ($country as $key => $value) {
                if($value != ''){
                    $this->db->insert(db_prefix() . 'ma_point_action_details', [
                        'point_action_id' => $insert_id,
                        'country' => $value,
                        'change_points' => $list_change_points[$key],
                    ]);
                }
            }

            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get point_action
     * @param  mixed $id point_action id (Optional)
     * @return mixed     object or array
     */
    public function get_point_action($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            $point_action = $this->db->get(db_prefix() . 'ma_point_actions')->row();

            if($point_action){
                $this->db->where('point_action_id', $id);
                $point_action->change_point_details = $this->db->get(db_prefix() . 'ma_point_action_details')->result_array();
            }

            return $point_action;
        }

        $this->db->where('published', 1);
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'ma_point_actions')->result_array();
    }

    /**
     * Add new point_action
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_point_action($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if (!isset($data['add_points_by_country'])) {
            $data['add_points_by_country'] = 0;
        }

        if (isset($data['country'])) {
            $country = $data['country'];
            unset($data['country']);
        }

        if (isset($data['list_change_points'])) {
            $list_change_points = $data['list_change_points'];
            unset($data['list_change_points']);
        }

        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_point_actions', $data);

        $this->db->where('point_action_id', $id);
        $this->db->delete(db_prefix() . 'ma_point_action_details');

        foreach ($country as $key => $value) {
            if($value != ''){
                $this->db->insert(db_prefix() . 'ma_point_action_details', [
                    'point_action_id' => $id,
                    'country' => $value,
                    'change_points' => $list_change_points[$key],
                ]);
            }
        }

        if($this->db->affected_rows() > 0){ 
            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete point_action from database, if used return array with key referenced
     */
    public function delete_point_action($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_point_actions');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Add new point_trigger
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_point_trigger($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        $this->db->insert(db_prefix() . 'ma_point_triggers', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get point_trigger
     * @param  mixed $id point_trigger id (Optional)
     * @return mixed     object or array
     */
    public function get_point_trigger($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            $point_trigger = $this->db->get(db_prefix() . 'ma_point_triggers')->row();

            return $point_trigger;
        }
        
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'ma_point_triggers')->result_array();
    }

    /**
     * Add new point_trigger
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_point_trigger($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_point_triggers', $data);

        if($this->db->affected_rows() > 0){ 
            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete point_trigger from database, if used return array with key referenced
     */
    public function delete_point_trigger($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_point_triggers');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Add new marketing_message
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_marketing_message($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        $this->db->insert(db_prefix() . 'ma_marketing_messages', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get marketing_message
     * @param  mixed $id marketing_message id (Optional)
     * @return mixed     object or array
     */
    public function get_marketing_message($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            $marketing_message = $this->db->get(db_prefix() . 'ma_marketing_messages')->row();

            return $marketing_message;
        }
        
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'ma_marketing_messages')->result_array();
    }

    /**
     * Add new marketing_message
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_marketing_message($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_marketing_messages', $data);

        if($this->db->affected_rows() > 0){ 
            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete marketing_message from database, if used return array with key referenced
     */
    public function delete_marketing_message($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_marketing_messages');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Add new email
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_email($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        $this->db->insert(db_prefix() . 'ma_emails', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if ($data['email_template'] != '') {
                $email_template = $this->get_email_template($data['email_template']);
                foreach($email_template->data_design as $design){
                    $this->db->insert(db_prefix() . 'ma_email_designs', [
                        'email_id' => $insert_id,
                        'language' => $design['language'],
                        'data_design' => $design['data_design'],
                        'data_html' => $design['data_html'],
                    ]);
                }
            }

            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get email
     * @param  mixed $id email id (Optional)
     * @return mixed     object or array
     */
    public function get_email($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            $email = $this->db->get(db_prefix() . 'ma_emails')->row();

            if($email){
                $this->db->where('email_id', $id);
                $email->data_design = $this->db->get(db_prefix() . 'ma_email_designs')->result_array();
            }

            return $email;
        }
        
        $this->db->where('published', 1);
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'ma_emails')->result_array();
    }

    /**
     * Add new email
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_email($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if ($data['email_template'] != '') {
            $email = $this->get_email($id);
        }

        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_emails', $data);

        if($this->db->affected_rows() > 0){ 
            if ($data['email_template'] != '') {
                if($email->email_template != $data['email_template']){
                    $this->db->where('email_id', $id);
                    $this->db->delete(db_prefix() . 'ma_email_designs');

                    $email_template = $this->get_email_template($data['email_template']);
                    foreach($email_template->data_design as $design){
                        $this->db->insert(db_prefix() . 'ma_email_designs', [
                            'email_id' => $id,
                            'language' => $design['language'],
                            'data_design' => $design['data_design'],
                            'data_html' => $design['data_html'],
                        ]);
                    }
                }
            }

            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete email from database
     */
    public function delete_email($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_emails');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('email_id', $id);
            $this->db->delete(db_prefix() . 'ma_email_logs');

            $this->db->where('email_id', $id);
            $this->db->delete(db_prefix() . 'ma_email_designs');

            return true;
        }

        return false;
    }

    /**
     * Add new text_message
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_text_message($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'ma_text_messages', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get text_message
     * @param  mixed $id text_message id (Optional)
     * @return mixed     object or array
     */
    public function get_text_message($id = '')
    {
        if (is_numeric($id)) {

            $this->db->where('id', $id);
            $text_message = $this->db->get(db_prefix() . 'ma_text_messages')->row();

            if($text_message){
                $this->db->where('sms_template_id', $id);
                $text_message->data_design = $this->db->get(db_prefix() . 'ma_sms_template_designs')->result_array();
            }

            return $text_message;
        }
        
        $this->db->where('published', 1);
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'ma_text_messages')->result_array();
    }

    /**
     * Add new text_message
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_text_message($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_text_messages', $data);

        if($this->db->affected_rows() > 0){ 
            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete text_message from database
     */
    public function delete_text_message($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_text_messages');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('sms_template_id', $id);
            $this->db->delete(db_prefix() . 'ma_sms_template_designs');

            return true;
        }

        return false;
    }

    /**
     * Change segment published
     * @param  mixed $id     segment id
     * @param  mixed $status status(0/1)
     */
    public function change_segment_published($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_segments', [
            'published' => $status,
        ]);

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function get_data_segment_pie_chart()
    {
        $where = '';

        $categories = $this->get_category('', 'segment');
        $categoryIds = [];

        $where = '';
        foreach ($categories as $category) {
            if ($this->input->post('segment_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        $data_chart = [];
        foreach($categories as $category){
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }

            $this->db->where('category', $category['id']);
            $segment = $this->db->count_all_results(db_prefix().'ma_segments');

            $data_chart[] = ['name' => $category['name'], 'y' => $segment, 'color' => $category['color']];
        }

        return $data_chart;
    }

    /**
     * @return array
     */
    public function get_data_segment_column_chart()
    {
        $categoryIds = [];

        $categories = $this->get_category('', 'segment');
        $categoryIds = [];
        $where = '';
        foreach ($categories as $category) {
            if ($this->input->post('segment_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        if (count($categoryIds) > 0) {
            $where = 'category IN (' . implode(', ', $categoryIds) . ')';
        }

        $header = [];
        $data_chart = [];
        foreach($categories as $category){
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }

            $header[] = $category['name'];

            $this->db->where('category', $category['id']);
            $segment = $this->db->count_all_results(db_prefix().'ma_segments');

            $data_chart[] = ['name' => $category['name'], 'y' => $segment, 'color' => $category['color']];
        }

        return ['header' => $header, 'data' => $data_chart];
    }

    /**
     * Does a segment kanban query.
     *
     * @param      int   $staff_id  The staff identifier
     * @param      integer  $page      The page
     * @param      array    $where     The where
     * @param      boolean  $count     The count
     *
     * @return     object
     */
    public function do_segment_kanban_query($category, $page = 1, $where = [], $count = false)
    {
        if ($count == false) {
            if ($page > 1) {
                $page--;
                $position = ($page * 10);
                $this->db->limit(10, $position);
            } else {
                $this->db->limit(10);
            }
        }

        return $this->get_segment('', $where, $count, true);
    }

    /**
     * update segment category
     *
     * @param      object  $data   The data
     */
    public function update_segment_category($data)
    {
        $this->db->where('id', $data['segment_id']);
        $this->db->update(db_prefix() . 'ma_segments', ['category' => $data['category']]);
    }

    /**
     * Add new campaign
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_campaign($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if($data['start_date'] != ''){
            $data['start_date'] = to_sql_date($data['start_date']);
        }
        if($data['end_date'] != ''){
            $data['end_date'] = to_sql_date($data['end_date']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        $this->db->insert(db_prefix() . 'ma_campaigns', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Add new campaign
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_campaign($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if($data['start_date'] != ''){
            $data['start_date'] = to_sql_date($data['start_date']);
        }
        if($data['end_date'] != ''){
            $data['end_date'] = to_sql_date($data['end_date']);
        }

        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_campaigns', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get campaign
     * @param  mixed $id campaign id (Optional)
     * @return mixed     object or array
     */
    public function get_campaign($id = '', $where = [], $count = false, $is_kanban = false)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            $campaign = $this->db->get(db_prefix() . 'ma_campaigns')->row();

            return $campaign;
        }

        $this->db->where($where);
        if($is_kanban == false){
            $this->db->where('published', 1);
        }
        $this->db->order_by('name', 'asc');

        if($count == true){
            return $this->db->count_all_results(db_prefix() . 'ma_campaigns');
        }else{
            return $this->db->get(db_prefix() . 'ma_campaigns')->result_array();
        }
    }
    /**
     * @param  array
     * @return boolean
     */
    public function workflow_builder_save($data){
        if(isset($data['campaign_id']) && $data['campaign_id'] != ''){
            $this->db->where('id', $data['campaign_id']);
            $this->db->update(db_prefix() . 'ma_campaigns', ['workflow' => json_encode($data['workflow'])]);

            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }

        return false;
    }


    /**
     * Change campaign published
     * @param  mixed $id     campaign id
     * @param  mixed $status status(0/1)
     */
    public function change_campaign_published($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_campaigns', [
            'published' => $status,
        ]);

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function get_data_campaign_pie_chart()
    {
        $where = '';

        $categories = $this->get_category('', 'campaign');
        $categoryIds = [];

        $where = '';
        foreach ($categories as $category) {
            if ($this->input->post('campaign_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        $data_chart = [];
        foreach($categories as $category){
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }

            $this->db->where('category', $category['id']);
            $campaign = $this->db->count_all_results(db_prefix().'ma_campaigns');

            $data_chart[] = ['name' => $category['name'], 'y' => $campaign, 'color' => $category['color']];
        }

        return $data_chart;
    }

    /**
     * @return array
     */
    public function get_data_campaign_column_chart()
    {
        $categoryIds = [];

        $categories = $this->get_category('', 'campaign');
        $categoryIds = [];
        $where = '';
        foreach ($categories as $category) {
            if ($this->input->post('campaign_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        if (count($categoryIds) > 0) {
            $where = 'category IN (' . implode(', ', $categoryIds) . ')';
        }

        $header = [];
        $data_chart = [];
        foreach($categories as $category){
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }

            $header[] = $category['name'];

            $this->db->where('category', $category['id']);
            $campaign = $this->db->count_all_results(db_prefix().'ma_campaigns');

            $data_chart[] = ['name' => $category['name'], 'y' => $campaign, 'color' => $category['color']];
        }

        return ['header' => $header, 'data' => $data_chart];
    }

    /**
     * Does a campaign kanban query.
     *
     * @param      int   $staff_id  The staff identifier
     * @param      integer  $page      The page
     * @param      array    $where     The where
     * @param      boolean  $count     The count
     *
     * @return     object
     */
    public function do_campaign_kanban_query($category, $page = 1, $where = [], $count = false)
    {
        if ($count == false) {
            if ($page > 1) {
                $page--;
                $position = ($page * 10);
                $this->db->limit(10, $position);
            } else {
                $this->db->limit(10);
            }
        }

        return $this->get_campaign('', $where, $count, true);
    }

    /**
     * update campaign category
     *
     * @param      object  $data   The data
     */
    public function update_campaign_category($data)
    {
        $this->db->where('id', $data['campaign_id']);
        $this->db->update(db_prefix() . 'ma_campaigns', ['category' => $data['category']]);
    }

    /**
     * Change stage published
     * @param  mixed $id     stage id
     * @param  mixed $status status(0/1)
     */
    public function change_stage_published($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_stages', [
            'published' => $status,
        ]);

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function get_data_stage_pie_chart()
    {
        $where = '';

        $categories = $this->get_category('', 'stage');
        $categoryIds = [];

        $where = '';
        foreach ($categories as $category) {
            if ($this->input->post('stage_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        $data_chart = [];
        foreach($categories as $category){
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }

            $this->db->where('category', $category['id']);
            $stage = $this->db->count_all_results(db_prefix().'ma_stages');

            $data_chart[] = ['name' => $category['name'], 'y' => $stage, 'color' => $category['color']];
        }

        return $data_chart;
    }
    
    /**
     * @return array
     */
    public function get_data_stage_column_chart()
    {
        $categoryIds = [];

        $categories = $this->get_category('', 'stage');
        $categoryIds = [];
        $where = '';
        foreach ($categories as $category) {
            if ($this->input->post('stage_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        if (count($categoryIds) > 0) {
            $where = 'category IN (' . implode(', ', $categoryIds) . ')';
        }

        $header = [];
        $data_chart = [];
        foreach($categories as $category){
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }

            $header[] = $category['name'];

            $this->db->where('category', $category['id']);
            $stage = $this->db->count_all_results(db_prefix().'ma_stages');

            $data_chart[] = ['name' => $category['name'], 'y' => $stage, 'color' => $category['color']];
        }

        return ['header' => $header, 'data' => $data_chart];
    }

    /**
     * Does a stage kanban query.
     *
     * @param      int   $staff_id  The staff identifier
     * @param      integer  $page      The page
     * @param      array    $where     The where
     * @param      boolean  $count     The count
     *
     * @return     object
     */
    public function do_stage_kanban_query($category, $page = 1, $where = [], $count = false)
    {
        if ($count == false) {
            if ($page > 1) {
                $page--;
                $position = ($page * 10);
                $this->db->limit(10, $position);
            } else {
                $this->db->limit(10);
            }
        }

        return $this->get_stage('', $where, $count, true);
    }

    /**
     * update stage category
     *
     * @param      object  $data   The data
     */
    public function update_stage_category($data)
    {
        $this->db->where('id', $data['stage_id']);
        $this->db->update(db_prefix() . 'ma_stages', ['category' => $data['category']]);
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete category from database
     */
    public function delete_campaign($id)
    {
        $this->delete_test_campaign($id);
        
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_campaigns');

        if ($this->db->affected_rows() > 0) {
            $this->db->where('campaign_id', $id);
            $this->db->delete(db_prefix() . 'ma_point_action_logs');

            return true;
        }

        return false;
    }

    /**
     * email template design save
     * @param  array
     * @return boolean
     */
    public function email_template_design_save($data){
        if(isset($data['id']) && $data['id'] != ''){
            $this->db->where('id', $data['id']);
            $this->db->update(db_prefix() . 'ma_email_template_designs', ['data_html' => json_encode($data['data_html']), 'data_design' => json_encode($data['data_design'])]);

            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add new email_template
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_email_template($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        $this->db->insert(db_prefix() . 'ma_email_templates', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get email_template
     * @param  mixed $id email_template id (Optional)
     * @return mixed     object or array
     */
    public function get_email_template($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            $email_template = $this->db->get(db_prefix() . 'ma_email_templates')->row();

            if($email_template){
                $this->db->where('email_template_id', $id);
                $email_template->data_design = $this->db->get(db_prefix() . 'ma_email_template_designs')->result_array();
            }

            return $email_template;
        }
        
        $this->db->where('published', 1);
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'ma_email_templates')->result_array();
    }

    /**
     * Add new email_template
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_email_template($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_email_templates', $data);

        if($this->db->affected_rows() > 0){ 
            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete email template from database
     */
    public function delete_email_template($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_email_templates');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('email_template_id', $id);
            $this->db->delete(db_prefix() . 'ma_email_template_designs');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  string
     * @return array or boolean
     */
    public function get_lead_by_segment($id, $return_type = 'leads'){
        $where = '';

        $segment = $this->get_segment($id);
               
        if($segment){
            if($segment->filter_type == 'customer'){
                if($return_type == 'leads'){
                    return [];
                }elseif($return_type == 'where'){
                    return '1=0';
                }
            }

            foreach ($segment->filters as $filter) {
                if($where != ''){
                    $where .= ' '. strtoupper($filter['sub_type_1']).' ';
                }

                $type = $filter['type'];

                switch ($filter['type']) {
                    case 'tag':
                        $type = db_prefix().'tags.name';

                        $where_tag = '';
                        switch ($filter['sub_type_2']) {
                            case 'equals':
                                $where_tag .= $type.' = "'.trim($filter['value']).'"';
                                break;
                            case 'not_equal':
                                $where_tag .= $type.' != "'.trim($filter['value']).'"';
                                break;
                            case 'greater_than':
                                $where_tag .= $type.' > "'.trim($filter['value']).'"';
                                break;
                            case 'greater_than_or_equal':
                                $where_tag .= $type.' >= "'.trim($filter['value']).'"';
                                break;
                            case 'less_than':
                                $where_tag .= $type.' < "'.trim($filter['value']).'"';
                                break;
                            case 'less_than_or_equal':
                                $where_tag .= $type.' <= "'.trim($filter['value']).'"';
                                break;
                            case 'empty':
                                $where_tag .= $type.' = ""';
                                break;
                            case 'not_empty':
                                $where_tag .= $type.' != ""';
                                break;
                            case 'like':
                                $where_tag .= $type.' LIKE "%'.trim($filter['value']).'%"';
                                break;
                            case 'not_like':
                                $where_tag .= $type.' NOT LIKE "%'.trim($filter['value']).'%"';
                                break;
                            default:
                                break;
                        }

                        $where .= '('.db_prefix().'leads.id in (SELECT '.db_prefix().'leads.id as id FROM '.db_prefix().'leads   
                        LEFT JOIN '.db_prefix().'taggables ON ' . db_prefix() . 'taggables.rel_id = ' . db_prefix() . 'leads.id and rel_type = "lead" 
                        LEFT JOIN '.db_prefix().'tags ON ' . db_prefix() . 'tags.id = ' . db_prefix() . 'taggables.tag_id
                        WHERE ma_unsubscribed = 0 AND '.$where_tag.'
                        GROUP BY '.db_prefix().'leads.id))';
                        
                        break;

                    case 'status':
                        $type = db_prefix().'leads_status.name';

                        $where_status = '';
                        switch ($filter['sub_type_2']) {
                            case 'equals':
                                $where_status .= $type.' = "'.$filter['value'].'"';
                                break;
                            case 'not_equal':
                                $where_status .= $type.' != "'.$filter['value'].'"';
                                break;
                            case 'greater_than':
                                $where_status .= $type.' > "'.$filter['value'].'"';
                                break;
                            case 'greater_than_or_equal':
                                $where_status .= $type.' >= "'.$filter['value'].'"';
                                break;
                            case 'less_than':
                                $where_status .= $type.' < "'.$filter['value'].'"';
                                break;
                            case 'less_than_or_equal':
                                $where_status .= $type.' <= "'.$filter['value'].'"';
                                break;
                            case 'empty':
                                $where_status .= $type.' = ""';
                                break;
                            case 'not_empty':
                                $where_status .= $type.' != ""';
                                break;
                            case 'like':
                                $where_status .= $type.' LIKE "%'.trim($filter['value']).'%"';
                                break;
                            case 'not_like':
                                $where_status .= $type.' NOT LIKE "%'.trim($filter['value']).'%"';
                                break;
                            default:
                                break;
                        }

                        $where .= '('.db_prefix().'leads.id in (SELECT '.db_prefix().'leads.id as id FROM '.db_prefix().'leads 
                        LEFT JOIN '.db_prefix().'leads_status ON ' . db_prefix() . 'leads_status.id = ' . db_prefix() . 'leads.status
                        WHERE ma_unsubscribed = 0 AND '.$where_status.'  
                        GROUP BY '.db_prefix().'leads.id))';

                        break;

                    case 'country':
                        $type = db_prefix().'countries.short_name';

                        $where_country = '';
                        switch ($filter['sub_type_2']) {
                            case 'equals':
                                $where_country .= $type.' = "'.$filter['value'].'"';
                                break;
                            case 'not_equal':
                                $where_country .= $type.' != "'.$filter['value'].'"';
                                break;
                            case 'greater_than':
                                $where_country .= $type.' > "'.$filter['value'].'"';
                                break;
                            case 'greater_than_or_equal':
                                $where_country .= $type.' >= "'.$filter['value'].'"';
                                break;
                            case 'less_than':
                                $where_country .= $type.' < "'.$filter['value'].'"';
                                break;
                            case 'less_than_or_equal':
                                $where_country .= $type.' <= "'.$filter['value'].'"';
                                break;
                            case 'empty':
                                $where_country .= $type.' = ""';
                                break;
                            case 'not_empty':
                                $where_country .= $type.' != ""';
                                break;
                            case 'like':
                                $where_country .= $type.' LIKE "%'.trim($filter['value']).'%"';
                                break;
                            case 'not_like':
                                $where_country .= $type.' NOT LIKE "%'.trim($filter['value']).'%"';
                                break;
                            default:
                                break;
                        }

                        $where .= '('.db_prefix().'leads.id in (SELECT '.db_prefix().'leads.id as id FROM '.db_prefix().'leads 
                        LEFT JOIN '.db_prefix().'countries ON ' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'leads.country 
                        WHERE ma_unsubscribed = 0 AND '.$where_country.' 
                        GROUP BY '.db_prefix().'leads.id))';

                        break;

                    case 'source':
                        $type = db_prefix().'leads_sources.name';
                        $where_source = '';
                        switch ($filter['sub_type_2']) {
                            case 'equals':
                                $where_source .= $type.' = "'.$filter['value'].'"';
                                break;
                            case 'not_equal':
                                $where_source .= $type.' != "'.$filter['value'].'"';
                                break;
                            case 'greater_than':
                                $where_source .= $type.' > "'.$filter['value'].'"';
                                break;
                            case 'greater_than_or_equal':
                                $where_source .= $type.' >= "'.$filter['value'].'"';
                                break;
                            case 'less_than':
                                $where_source .= $type.' < "'.$filter['value'].'"';
                                break;
                            case 'less_than_or_equal':
                                $where_source .= $type.' <= "'.$filter['value'].'"';
                                break;
                            case 'empty':
                                $where_source .= $type.' = ""';
                                break;
                            case 'not_empty':
                                $where_source .= $type.' != ""';
                                break;
                            case 'like':
                                $where_source .= $type.' LIKE "%'.trim($filter['value']).'%"';
                                break;
                            case 'not_like':
                                $where_source .= $type.' NOT LIKE "%'.trim($filter['value']).'%"';
                                break;
                            default:
                                break;
                        }

                        $where .= '('.db_prefix().'leads.id in (SELECT '.db_prefix().'leads.id as id FROM '.db_prefix().'leads 
                        LEFT JOIN '.db_prefix().'leads_sources ON ' . db_prefix() . 'leads_sources.id = ' . db_prefix() . 'leads.source 
                        WHERE ma_unsubscribed = 0 AND '.$where_source.' 
                        GROUP BY '.db_prefix().'leads.id))';

                        break;

                    default:
                        if(!is_numeric($filter['type'])){
                            $type = db_prefix().'leads.'.$filter['type'];

                            switch ($filter['sub_type_2']) {
                                case 'equals':
                                    $where .= $type.' = "'.$filter['value'].'"';
                                    break;
                                case 'not_equal':
                                    $where .= $type.' != "'.$filter['value'].'"';
                                    break;
                                case 'greater_than':
                                    $where .= $type.' > "'.$filter['value'].'"';
                                    break;
                                case 'greater_than_or_equal':
                                    $where .= $type.' >= "'.$filter['value'].'"';
                                    break;
                                case 'less_than':
                                    $where .= $type.' < "'.$filter['value'].'"';
                                    break;
                                case 'less_than_or_equal':
                                    $where .= $type.' <= "'.$filter['value'].'"';
                                    break;
                                case 'empty':
                                    $where .= $type.' = ""';
                                    break;
                                case 'not_empty':
                                    $where .= $type.' != ""';
                                    break;
                                case 'like':
                                    $where .= $type.' LIKE "%'.trim($filter['value']).'%"';
                                    break;
                                case 'not_like':
                                    $where .= $type.' NOT LIKE "%'.trim($filter['value']).'%"';
                                    break;
                                default:
                                    break;
                            }
                        }else{

                            $type = '(SELECT count(0) FROM '.db_prefix().'customfieldsvalues WHERE relid = '.db_prefix().'leads.id AND fieldid = '.$filter['type'].' AND fieldto = "leads" ';

                            switch ($filter['sub_type_2']) {
                                case 'equals':
                                    $type .= ' AND value = "'.$filter['value'].'"';
                                    break;
                                case 'not_equal':
                                    $type .= ' AND value != "'.$filter['value'].'"';
                                    break;
                                case 'greater_than':
                                    $type .= ' AND value > "'.$filter['value'].'"';
                                    break;
                                case 'greater_than_or_equal':
                                    $type .= ' AND value >= "'.$filter['value'].'"';
                                    break;
                                case 'less_than':
                                    $type .= ' AND value < "'.$filter['value'].'"';
                                    break;
                                case 'less_than_or_equal':
                                    $type .= ' AND value <= "'.$filter['value'].'"';
                                    break;
                                case 'empty':
                                    $type .= ' AND value = ""';
                                    break;
                                case 'not_empty':
                                    $type .= ' AND value != ""';
                                    break;
                                case 'like':
                                    $type .= ' AND value LIKE "%'.trim($filter['value']).'%"';
                                    break;
                                case 'not_like':
                                    $type .= ' AND value NOT LIKE "%'.trim($filter['value']).'%"';
                                    break;
                                default:
                                    break;
                            }

                            $type .= ') > 0';

                            $where .= $type;
                            
                        }
                        
                        break;
                }
            }
        }

        $where_lead_segment = 'SELECT lead_id FROM '.db_prefix().'ma_lead_segments WHERE deleted = 0 AND segment_id = '. $id;

        if($where != ''){
          $where = '('.$where.' OR '.db_prefix().'leads.id in ('.$where_lead_segment.'))';
        }else{
          $where = '('.db_prefix().'leads.id in ('.$where_lead_segment.'))';
        }

        if($return_type == 'leads'){
            $this->db->select('*, '.db_prefix().'leads.id as id');
            $this->db->where($where);
            $this->db->where('ma_unsubscribed', 0);
            $leads = $this->db->get(db_prefix().'leads')->result_array();
            
            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_campaign_by_segment($id){
        $where = '(workflow LIKE \'%\\\\\\\\"segment\\\\\\\\":\\\\\\\\"'.$id.'\\\\\\\\"%\' OR workflow LIKE \'%\\\\\\\\"customer_segment\\\\\\\\":\\\\\\\\"'.$id.'\\\\\\\\"%\')';
        $this->db->where('start_date <= "'.date('Y-m-d').'" AND end_date >= "'.date('Y-m-d').'"');
        $this->db->where($where);
        $campaigns = $this->db->get(db_prefix().'ma_campaigns')->result_array();

        $this->db->where('"'.date('Y-m-d').'" >= end_date');
        $this->db->where($where);
        $old_campaigns = $this->db->get(db_prefix().'ma_campaigns')->result_array();
        
        return ['campaigns' => count($campaigns), 'old_campaigns' => count($old_campaigns)];
    }

    /**
     * @param  integer
     * @param  string
     * @return array or string
     */
    public function get_lead_by_campaign($id, $return_type = 'leads'){
        $campaign = $this->get_campaign($id);
        $where = '';

        if($campaign->workflow != null && $campaign->workflow != ''){
            if(json_decode($campaign->workflow ?? '') != null){
                $workflow = json_decode(json_decode($campaign->workflow ?? ''), true);

                foreach($workflow['drawflow']['Home']['data'] as $data){
                    if($data['class'] == 'flow_start'){
                        if(!isset($data['data']['data_type']) || $data['data']['data_type'] == 'lead'){
                            if(!isset($data['data']['lead_data_from']) || $data['data']['lead_data_from'] == 'segment'){
                                if(isset($data['data']['segment'])){
                                    $where = $this->get_lead_by_segment($data['data']['segment'], 'where');
                                }
                            }else{
                                if(isset($data['data']['form'])){
                                    $where = 'from_ma_form_id = '.$data['data']['form'];
                                }
                            }
                        }
                    }
                }
            }
        }

        if($where == ''){
            $where = '1=0';
        }

        $where .= ' AND '.db_prefix().'leads.id not in (SELECT id FROM '.db_prefix().'ma_campaign_lead_exceptions where id = '.$id.')';

        if($return_type == 'leads'){
            $this->db->select(db_prefix().'leads.*, '.db_prefix().'leads_status.name as status_name, '.db_prefix().'countries.short_name as country_name, '.db_prefix().'leads_sources.name as source_name');
            $this->db->where($where);
            $this->db->where('ma_unsubscribed', 0);
            $this->db->join(db_prefix() . 'leads_status', '' . db_prefix() . 'leads_status.id = ' . db_prefix() . 'leads.status', 'left');
            $this->db->join(db_prefix() . 'leads_sources', '' . db_prefix() . 'leads_sources.id = ' . db_prefix() . 'leads.country', 'left');
            $this->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'leads.country', 'left');
            $leads = $this->db->get(db_prefix().'leads')->result_array();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return boolean
     */
    public function run_campaigns($id){
        $campaign = $this->get_campaign($id);
        $workflow = json_decode(json_decode($campaign->workflow ?? '') ?? '', true);
        
        if(!isset($workflow['drawflow']['Home']['data'])){
            return false;
        }

        $workflow = $workflow['drawflow']['Home']['data'];
        $data_flow = [];

        $data = [];
        $data['campaign'] = $campaign;
        $data['workflow'] = $workflow;
        
        $leads = $this->get_lead_by_campaign($id);
        foreach($leads as $lead){
            $data['lead'] = $lead;
            $data['contact'] = $lead;
            foreach($workflow as $data_workflow){
                $data['node'] = $data_workflow;

                if($data_workflow['class'] == 'flow_start'){
                    if(!$this->check_workflow_node_log($data)){
                        $this->save_workflow_node_log($data);
                    }

                    foreach ($data_workflow['outputs']['output_1']['connections'] as $connection) {
                        $data['node'] = $workflow[$connection['node']];
                        $this->run_workflow_node($data);
                    }
                }
            }
        }

        $clients = $this->get_client_by_campaign($id);
        foreach($clients as $client){
            $data['client'] = $client;
            $data['contact'] = $client;
            foreach($workflow as $data_workflow){
                $data['node'] = $data_workflow;

                if($data_workflow['class'] == 'flow_start'){
                    if(!$this->check_workflow_node_log($data)){
                        $this->save_workflow_node_log($data);
                    }

                    foreach ($data_workflow['outputs']['output_1']['connections'] as $connection) {
                        $data['node'] = $workflow[$connection['node']];
                        $this->run_workflow_node($data);
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function run_workflow_node($data){
        $output = $this->check_workflow_node_log($data);
        
        if(!$output){
            switch ($data['node']['class']) {
                case 'email':
                    $success = $this->handle_email_node($data);

                    if($success){
                        $this->save_workflow_node_log($data);

                        foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node($data);
                        }
                    }

                    break;

                case 'sms':
                    $success = $this->handle_sms_node($data);

                    if($success){
                        $this->save_workflow_node_log($data);

                        foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node($data);
                        }
                    }

                    break;

                case 'action':
                    $success = $this->handle_action_node($data);

                    if($success){
                        $this->save_workflow_node_log($data);

                        foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node($data);
                        }
                    }

                    break;

                case 'condition':
                    $success = $this->handle_condition_node($data);
                    if($success == 'output_1'){
                        $this->save_workflow_node_log($data);

                        foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node($data);
                        }

                    }elseif($success == 'output_2'){
                        $this->save_workflow_node_log($data, 'output_2');

                        foreach ($data['node']['outputs']['output_2']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node($data);
                        }
                    }

                    break;

                case 'filter':
                    $success = $this->handle_filter_node($data);
                    if($success == 'output_1'){
                        $this->save_workflow_node_log($data);

                        foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node($data);
                        }

                    }elseif($success == 'output_2'){
                        $this->save_workflow_node_log($data, 'output_2');

                        foreach ($data['node']['outputs']['output_2']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node($data);
                        }
                    }
                    break;

                default:
                    // code...
                    break;
            }
        }else{
            foreach ($data['node']['outputs'][$output]['connections'] as $connection) {
                $data['node'] = $data['workflow'][$connection['node']];
                $this->run_workflow_node($data);
            }
        }

        return true;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function handle_email_node($data, $testing = false){
        if(isset($data['node']['data']['email']) && $data['contact']['email'] != ''){
            if(!isset($data['node']['data']['complete_action'])){
                $data['node']['data']['complete_action'] = 'right_away';
            }

            switch ($data['node']['data']['complete_action']) {
                case 'right_away':
                    $email = $this->get_email($data['node']['data']['email']);
                    $log_id = $this->save_email_log([
                        'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                        'client_id' => (isset($data['client']) ? $data['client']['userid'] : 0), 
                        'email_id' => $email->id, 
                        'email_template_id' => $email->email_template, 
                        'campaign_id' => $data['campaign']->id,
                        'email' => $data['contact']['email'],
                    ]);
                    
                    if($testing || get_option('ma_email_sending_limit') != 1){
                        $this->ma_send_email($data['contact']['email'], $email, $data, $log_id);
                    }

                    return true;

                    break;
                case 'after':
                    if(!isset($data['node']['data']['waiting_number'])){
                        $data['node']['data']['waiting_number'] = 1;
                    }
                    
                    if(!isset($data['node']['data']['waiting_type'])){
                        $data['node']['data']['waiting_type'] = 'minutes';
                    }

                    foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                        $this->db->where('campaign_id', $data['campaign']->id);
                        if(isset($data['lead'])){
                            $this->db->where('lead_id', $data['lead']['id']);
                        }else{
                            $this->db->where('client_id', $data['client']['id']);
                        }
                        $this->db->where('node_id', $connection['node']);
                        $logs = $this->db->get(db_prefix().'ma_campaign_flows')->row();

                        if($logs){
                            $time = date('Y-m-d H:i:s', strtotime($logs->dateadded." +".$data['node']['data']['waiting_number']." ".$data['node']['data']['waiting_type']));

                            if(date('Y-m-d H:i:s') >= $time){
                                $email = $this->get_email($data['node']['data']['email']);
                                $log_id = $this->save_email_log([
                                    'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                                    'client_id' => (isset($data['client']) ? $data['client']['userid'] : 0), 
                                    'email_id' => $email->id, 
                                    'email_template_id' => $email->email_template, 
                                    'campaign_id' => $data['campaign']->id,
                                    'email' => $data['contact']['email'],
                                ]);

                                if($testing || get_option('ma_email_sending_limit') != 1){
                                    $this->ma_send_email($data['contact']['email'], $email, $data, $log_id);
                                }

                                return true;
                            }
                        }
                    }

                    break;
                case 'exact_time':
                    $time = date('Y-m-d H:i:s', strtotime(date('Y-m-d').' '.$data['node']['data']['exact_time']));

                    if(date('Y-m-d H:i:s') >= $time){
                        $email = $this->get_email($data['node']['data']['email']);
                        $log_id = $this->save_email_log([
                            'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                            'client_id' => (isset($data['client']) ? $data['client']['userid'] : 0), 
                            'email_id' => $email->id, 
                            'email_template_id' => $email->email_template, 
                            'campaign_id' => $data['campaign']->id,
                            'email' => $data['contact']['email'],
                        ]);

                        if($testing || get_option('ma_email_sending_limit') != 1){
                            $success = $this->ma_send_email($data['contact']['email'], $email, $data, $log_id);
                        }

                        return true;
                    }

                    break;
                case 'exact_time_and_date':
                    $time = $data['node']['data']['exact_time_and_date'];

                    if(date('Y-m-d H:i:s') >= $time){
                        $email = $this->get_email($data['node']['data']['email']);
                        $log_id = $this->save_email_log([
                            'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                            'client_id' => (isset($data['client']) ? $data['client']['userid'] : 0), 
                            'email_id' => $email->id, 
                            'email_template_id' => $email->email_template, 
                            'campaign_id' => $data['campaign']->id,
                            'email' => $data['contact']['email'],
                        ]);

                        if($testing || get_option('ma_email_sending_limit') != 1){
                            $success = $this->ma_send_email($data['contact']['email'], $email, $data, $log_id);
                        }

                        return true;
                    }
                    
                    break;
                
                default:
                    // code...
                    break;
            }
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function handle_sms_node($data){
        if(isset($data['node']['data']['sms']) && $data['contact']['phonenumber'] != ''){
            if(!isset($data['node']['data']['complete_action'])){
                $data['node']['data']['complete_action'] = 'right_away';
            }

            switch ($data['node']['data']['complete_action']) {
                case 'right_away':
                    $sms = $this->get_sms($data['node']['data']['sms']);

                    $content = $this->get_sms_content_by_contact($sms->id, $data['contact']);

                    $this->sendSMS($content, $data['contact']['phonenumber'], $data);
                    $this->save_sms_log([
                        'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                        'client_id' => (isset($data['client']) ? $data['client']['userid'] : 0),
                        'sms_id' => $sms->id, 
                        'text_message_id' => $sms->sms_template, 
                        'campaign_id' => $data['campaign']->id
                    ]);

                    return true;

                    break;
                case 'after':
                    if(!isset($data['node']['data']['waiting_number'])){
                        $data['node']['data']['waiting_number'] = 1;
                    }
                    
                    if(!isset($data['node']['data']['waiting_type'])){
                        $data['node']['data']['waiting_type'] = 'minutes';
                    }

                    foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                        $this->db->where('campaign_id', $data['campaign']->id);
                        if(isset($data['lead'])){
                            $this->db->where('lead_id', $data['lead']['id']);
                        }else{
                            $this->db->where('client_id', $data['client']['id']);
                        }
                        $this->db->where('node_id', $connection['node']);
                        $logs = $this->db->get(db_prefix().'ma_campaign_flows')->row();

                        if($logs){
                            $time = date('Y-m-d H:i:s', strtotime($logs->dateadded." +".$data['node']['data']['waiting_number']." ".$data['node']['data']['waiting_type']));

                            if(date('Y-m-d H:i:s') >= $time){
                                $sms = $this->get_sms($data['node']['data']['sms']);
                                $content = $this->get_sms_content_by_contact($sms->id, $data['contact']);

                                $this->sendSMS($content, $data['contact']['phonenumber'], $data);
                                $this->save_sms_log([
                                    'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                                    'client_id' => (isset($data['client']) ? $data['client']['userid'] : 0),
                                    'sms_id' => $sms->id, 
                                    'text_message_id' => $sms->sms_template, 
                                    'campaign_id' => $data['campaign']->id
                                ]);

                                return true;
                            }
                        }
                    }

                    break;
                case 'exact_time':
                    $time = date('Y-m-d H:i:s', strtotime(date('Y-m-d').' '.$data['node']['data']['exact_time']));

                    if(date('Y-m-d H:i:s') >= $time){
                        $sms = $this->get_sms($data['node']['data']['sms']);
                        $content = $this->get_sms_content_by_contact($sms->id, $data['contact']);

                        $this->sendSMS($content, $data['contact']['phonenumber'], $data);
                        $this->save_sms_log([
                            'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                            'client_id' => (isset($data['client']) ? $data['client']['userid'] : 0),
                            'sms_id' => $sms->id, 
                            'text_message_id' => $sms->sms_template, 
                            'campaign_id' => $data['campaign']->id
                        ]);

                        return true;
                    }

                    break;
                case 'exact_time_and_date':
                    $time = $data['node']['data']['exact_time_and_date'];

                    if(date('Y-m-d H:i:s') >= $time){
                        $sms = $this->get_sms($data['node']['data']['sms']);
                        $content = $this->get_sms_content_by_contact($sms->id, $data['contact']);

                        $this->sendSMS($content, $data['contact']['phonenumber'], $data);
                        $this->save_sms_log([
                            'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                            'client_id' => (isset($data['client']) ? $data['client']['userid'] : 0),
                            'sms_id' => $sms->id, 
                            'text_message_id' => $sms->sms_template, 
                            'campaign_id' => $data['campaign']->id
                        ]);

                        return true;
                    }
                    
                    break;
                
                default:
                    // code...
                    break;
            }
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function handle_action_node($data){
        if(!isset($data['node']['data']['action'])){
            $data['node']['data']['action'] = 'change_segments';
        }

        if(!isset($data['node']['data']['action_segment_type'])){
            $data['node']['data']['action_segment_type'] = 'lead';
        }

        switch ($data['node']['data']['action']) {
            case 'change_segments':

                if($data['node']['data']['action_segment_type'] == 'customer'){
                    if(isset($data['node']['data']['customer_segment']) && isset($data['client'])){
                        $this->change_segment($data['client']['userid'], $data['node']['data']['customer_segment'], $data['campaign']->id, 'client');

                        return true;
                    }
                }else{
                    if(isset($data['node']['data']['segment']) && isset($data['lead'])){
                        $this->change_segment($data['lead']['id'], $data['node']['data']['segment'], $data['campaign']->id);

                        return true;
                    }
                }


                break;
            case 'change_stages':
                if(isset($data['node']['data']['stage']) && isset($data['lead'])){
                    $this->change_stage($data['lead']['id'], $data['node']['data']['stage'], $data['campaign']->id);

                    return true;
                }

                break;
            case 'change_points':
                if(isset($data['node']['data']['point'])){
                    $this->db->insert(db_prefix().'ma_point_action_logs', [
                        'campaign_id' => $data['campaign']->id, 
                        'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                        'client_id' => (isset($data['client']) ? $data['client']['userid'] : 0), 
                        'point_action_id' => 0, 
                        'point' => $data['node']['data']['point'],
                        'dateadded' => date('Y-m-d H:i:s'), 
                    ]);

                    return true;
                }

                break;

            case 'point_action':
                if(isset($data['node']['data']['point_action'])){

                    $change_points = $this->get_change_point_by_contact($data['node']['data']['point_action'], $data['contact']);

                    $this->db->insert(db_prefix().'ma_point_action_logs', [
                        'campaign_id' => $data['campaign']->id, 
                        'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                        'client_id' => (isset($data['client']) ? $data['client']['userid'] : 0), 
                        'point_action_id' => $data['node']['data']['point_action'], 
                        'point' => $change_points,
                        'dateadded' => date('Y-m-d H:i:s'), 
                    ]);

                    return true;
                }

                break;

            case 'delete_lead':

                if(isset($data['lead'])){
                    $this->load->model('leads_model');
                    $this->leads_model->delete($data['lead']['id']);
                }
                
                return true;

                break;

            case 'remove_from_campaign':
                $type = 'lead';
                if(!isset($data['lead'])){
                    $type = 'client';
                }

                $this->remove_from_campaign($data['campaign']->id, $data['contact']['id'], $type);

                return true;

                break;

            case 'convert_to_customer':
                if(isset($data['lead'])){
                    $this->convert_lead_to_customer($data['lead']);
                }

                return true;
                
                break; 

            case 'change_lead_status':
                if(isset($data['node']['data']['lead_status']) && isset($data['lead'])){
                    $this->load->model('leads_model');
                    $this->leads_model->update_lead_status(['leadid' => $data['lead']['id'], 'status' => $data['node']['data']['lead_status']]);
                }

                return true;
                
                break; 

            case 'add_tags':
                if(isset($data['node']['data']['tags']) && isset($data['lead'])){
                    $tags = prep_tags_input(get_tags_in($data['lead']['id'], 'lead'));

                    if($tags != ''){
                        $tags .= ','.$data['node']['data']['tags'];
                    }else{
                        $tags = $data['node']['data']['tags'];
                    }
                    handle_tags_save($tags, $data['lead']['id'], 'lead');
                }

                return true;
                
                break; 
            
            default:
                // code...
                break;
        }
    }

    /**
     * @param  array
     * @return boolean
     */
    public function handle_filter_node($data){
        if(!isset($data['node']['data']['complete_action'])){
            $data['node']['data']['complete_action'] = 'right_away';
        }

        switch ($data['node']['data']['complete_action']) {
            case 'right_away':
                if($this->check_contact_filter($data, $data['node'])){
                    return 'output_1';
                }else{
                    return 'output_2';
                }

                break;
            case 'after':
                if(!isset($data['node']['data']['waiting_number'])){
                    $data['node']['data']['waiting_number'] = 1;
                }
                
                if(!isset($data['node']['data']['waiting_type'])){
                    $data['node']['data']['waiting_type'] = 'minutes';
                }

                foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                    $this->db->where('campaign_id', $data['campaign']->id);
                    if(isset($data['lead'])){
                        $this->db->where('lead_id', $data['lead']['id']);
                    }else{
                        $this->db->where('client_id', $data['client']['id']);
                    }
                    $this->db->where('node_id', $connection['node']);
                    $logs = $this->db->get(db_prefix().'ma_campaign_flows')->row();

                    if($logs){
                        $time = date('Y-m-d H:i:s', strtotime($logs->dateadded." +".$data['node']['data']['waiting_number']." ".$data['node']['data']['waiting_type']));

                        if(date('Y-m-d H:i:s') >= $time){
                            if($this->check_contact_filter($data, $data['node'])){
                                return 'output_1';
                            }else{
                                return 'output_2';
                            }
                        }
                    }
                }
            
                break;
            default:
                // code...
                break;
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function handle_condition_node($data){

        foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
            $this->db->where('campaign_id', $data['campaign']->id);
            if(isset($data['lead'])){
                $this->db->where('lead_id', $data['lead']['id']);
            }else{
                $this->db->where('client_id', $data['client']['id']);
            }
            $this->db->where('node_id', $connection['node']);
            $logs = $this->db->get(db_prefix().'ma_campaign_flows')->row();

            if($logs){
                if(!isset($data['node']['data']['waiting_number'])){
                    $data['node']['data']['waiting_number'] = 1;
                }

                if(!isset($data['node']['data']['waiting_type'])){
                    $data['node']['data']['waiting_type'] = 'minutes';
                }

                $time = date('Y-m-d H:i:s', strtotime($logs->dateadded." +".$data['node']['data']['waiting_number']." ".$data['node']['data']['waiting_type']));

                if(date('Y-m-d H:i:s') >= $time){

                    if(!isset($data['node']['data']['track'])){
                        $data['node']['data']['track'] = 'delivery';
                    }

                    switch ($data['node']['data']['track']) {
                        case 'delivery':
                            foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                                $node = $data['workflow'][$connection['node']];
                                if($node['class'] == 'email'){
                                    if(isset($node['data']['email'])){
                                        if($this->check_condition_email($data, $node['data']['email'], 'delivery')){
                                            return 'output_1';
                                        }else{
                                            return 'output_2';
                                        }
                                    }
                                }
                            }
                            break;

                        case 'opens':
                            foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                                $node = $data['workflow'][$connection['node']];
                                if($node['class'] == 'email'){
                                    if(isset($node['data']['email'])){
                                        if($this->check_condition_email($data, $node['data']['email'], 'open')){
                                            return 'output_1';
                                        }else{
                                            return 'output_2';
                                        }
                                    }
                                }
                            }
                            break;

                        case 'clicks':
                            foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                                $node = $data['workflow'][$connection['node']];
                                if($node['class'] == 'email'){
                                    if(isset($node['data']['email'])){
                                        if($this->check_condition_email($data, $node['data']['email'], 'click')){
                                            return 'output_1';
                                        }else{
                                            return 'output_2';
                                        }
                                    }
                                }
                            }
                            break;

                        case 'confirm':
                            foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                                $node = $data['workflow'][$connection['node']];
                                if($node['class'] == 'email'){
                                    if(isset($node['data']['email'])){
                                        if($this->check_condition_email($data, $node['data']['email'], 'confirm')){
                                            return 'output_1';
                                        }else{
                                            return 'output_2';
                                        }
                                    }
                                }
                            }
                            break;
                        
                        default:
                            
                            break;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param  array
     * @param  string
     * @return boolean
     */
    public function save_workflow_node_log($data, $output = 'output_1'){
        $this->db->where('campaign_id', $data['campaign']->id);

        if(isset($data['lead'])){
            $this->db->where('lead_id', $data['lead']['id']);
        }else{
            $this->db->where('client_id', $data['client']['userid']);
        }

        $this->db->where('node_id', $data['node']['id']);
        $logs = $this->db->get(db_prefix().'ma_campaign_flows')->row();

        if(!$logs){

            $this->db->insert(db_prefix().'ma_campaign_flows', [
                'campaign_id' => $data['campaign']->id, 
                'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                'client_id' => (isset($data['client']) ? $data['client']['userid'] : 0), 
                'node_id' => $data['node']['id'], 
                'output' => $output, 
                'dateadded' => date('Y-m-d H:i:s'), 
            ]);
        }

        return true;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function check_workflow_node_log($data){
        $this->db->where('campaign_id', $data['campaign']->id);

        if(isset($data['lead'])){
            $this->db->where('lead_id', $data['lead']['id']);
        }else{
            $this->db->where('client_id', $data['client']['id']);
        }

        $this->db->where('node_id', $data['node']['id']);
        $logs = $this->db->get(db_prefix().'ma_campaign_flows')->row();

        if($logs){
            return $logs->output;
        }

        return false;
    }

    //send sms with setting
    /**
     * sendSMS
     * @param  [type] $request 
     * @return [type]          
     */
    public function sendSMS($request, $phone, $data = []) {

        if (empty($phone)) {
            return false;
        }

        $gateway = $this->app_sms->get_active_gateway();

        if ($gateway !== false) {
            $ci = &get_instance();

            $className = 'sms_' . $gateway['id'];
            $message = $this->parse_content_merge_fields($request, $data);

            $message = clear_textarea_breaks($message);

            $retval = $ci->{$className}->send($phone, $message);

            return $retval;
        }

        return false;
    }

    /**
     * @param  integer
     * @param  integer
     * @param  integer
     * @return boolean
     */
    public function change_segment($lead_id, $segment_id, $campaign_id, $type = 'lead'){
        if($type == 'lead'){
            $this->db->where('lead_id', $lead_id);
        }else{
            $this->db->where('client_id', $lead_id);
        }

        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('segment_id', $segment_id);
        $logs = $this->db->get(db_prefix().'ma_lead_segments')->row();

        if(!$logs){
            $segment = $this->get_segment($segment_id);
            $segments = $this->get_segment('', 'category = '.$segment->category);

            foreach ($segments as $value) {
                $this->db->where('segment_id', $value['id']);
                if($type == 'lead'){
                    $this->db->where('lead_id', $lead_id);
                }else{
                    $this->db->where('client_id', $lead_id);
                }
                $this->db->update(db_prefix().'ma_lead_segments', [
                    'deleted' => 1, 
                    'date_delete' => date('Y-m-d H:i:s'), 
                ]);               
            }

            $data_insert = [
                'campaign_id' => $campaign_id, 
                'segment_id' => $segment_id, 
                'dateadded' => date('Y-m-d H:i:s'), 
            ];

            if($type == 'lead'){
                $data_insert['lead_id'] = $lead_id;
            }else{
                $data_insert['client_id'] = $lead_id;
            }

            $this->db->insert(db_prefix().'ma_lead_segments', $data_insert);
        }

        return true;
    }

    /**
     * @param  integer
     * @param  integer
     * @param  integer
     * @return boolean
     */
    public function change_stage($lead_id, $stage_id, $campaign_id){
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('lead_id', $lead_id);
        $this->db->where('stage_id', $stage_id);
        $logs = $this->db->get(db_prefix().'ma_lead_stages')->row();

        if(!$logs){
            $stage = $this->get_stage($stage_id);
            $stages = $this->get_stage('', 'category = '.$stage->category);

            foreach ($stages as $value) {
                $this->db->where('stage_id', $value['id']);
                $this->db->where('lead_id', $lead_id);
                $this->db->update(db_prefix().'ma_lead_stages', [
                    'deleted' => 1, 
                    'date_delete' => date('Y-m-d H:i:s'), 
                ]);               
            }

            $this->db->insert(db_prefix().'ma_lead_stages', [
                'campaign_id' => $campaign_id, 
                'lead_id' => $lead_id, 
                'stage_id' => $stage_id, 
                'dateadded' => date('Y-m-d H:i:s'), 
            ]);
        }

        return true;
    }

    /**
     * Convert lead to client
     * @since  version 1.0.1
     * @return mixed
     */
    public function convert_lead_to_customer($lead)
    {
        $default_country  = get_option('customer_default_country');

        if(mb_strpos($lead['name'],' ') !== false){
           $_temp = explode(' ',$lead['name']);
           $firstname = $_temp[0];
           if(isset($_temp[2])){
             $lastname = $_temp[1] . ' ' . $_temp[2];
          } else {
             $lastname = $_temp[1];
          }
       } else {
          $lastname = '';
          $firstname = $lead->name;
       }

        $data             = [
            'leadid' => $lead['id'],
            'password' => '1',
            'firstname' => $firstname,
            'lastname' => $lastname,
            'title' => $lead['title'],
            'email' => $lead['email'],
            'company' => $lead['company'],
            'phonenumber' => $lead['phonenumber'],
            'website' => $lead['website'],
            'address' => $lead['address'],
            'city' => $lead['city'],
            'state' => $lead['state'],
            'country' => $lead['country'],
            'zip' => $lead['zip'],
            'fakeusernameremembered' => '',
            'fakepasswordremembered' => '',
        ];

        if ($data['country'] == '' && $default_country != '') {
            $data['country'] = $default_country;
        }

        $data['billing_street']  = $data['address'];
        $data['billing_city']    = $data['city'];
        $data['billing_state']   = $data['state'];
        $data['billing_zip']     = $data['zip'];
        $data['billing_country'] = $data['country'];

        $data['is_primary'] = 1;
        $id                 = $this->clients_model->add($data, true);
        if ($id) {
            $primary_contact_id = get_primary_contact_user_id($id);

            if (!has_permission('customers', '', 'view') && get_option('auto_assign_customer_admin_after_lead_convert') == 1) {
                $this->db->insert(db_prefix() . 'customer_admins', [
                    'date_assigned' => date('Y-m-d H:i:s'),
                    'customer_id'   => $id,
                    'staff_id'      => get_staff_user_id(),
                ]);
            }
            $this->load->model('leads_model');
            
            $this->leads_model->log_lead_activity($data['leadid'], 'not_lead_activity_converted', false, serialize([
                get_staff_full_name(),
            ]));
            $default_status = $this->leads_model->get_status('', [
                'isdefault' => 1,
            ]);
            $this->db->where('id', $data['leadid']);
            $this->db->update(db_prefix() . 'leads', [
                'date_converted' => date('Y-m-d H:i:s'),
                'status'         => $default_status[0]['id'],
                'junk'           => 0,
                'lost'           => 0,
            ]);
            // Check if lead email is different then client email
            $contact = $this->clients_model->get_contact(get_primary_contact_user_id($id));
          
            // set the lead to status client in case is not status client
            $this->db->where('isdefault', 1);
            $status_client_id = $this->db->get(db_prefix() . 'leads_status')->row()->id;
            $this->db->where('id', $data['leadid']);
            $this->db->update(db_prefix() . 'leads', [
                'status' => $status_client_id,
            ]);

            set_alert('success', _l('lead_to_client_base_converted_success'));

            if (is_gdpr() && get_option('gdpr_after_lead_converted_delete') == '1') {
                // When lead is deleted
                // move all proposals to the actual customer record
                $this->db->where('rel_id', $data['leadid']);
                $this->db->where('rel_type', 'lead');
                $this->db->update('proposals', [
                    'rel_id'   => $id,
                    'rel_type' => 'customer',
                ]);

                $this->leads_model->delete($data['leadid']);

                $this->db->where('userid', $id);
                $this->db->update(db_prefix() . 'clients', ['leadid' => null]);
            }

            log_activity('Created Lead Client Profile [LeadID: ' . $data['leadid'] . ', ClientID: ' . $id . ']');
            hooks()->do_action('lead_converted_to_customer', ['lead_id' => $data['leadid'], 'customer_id' => $id]);
        }
    }

    /**
     * @param  array
     * @param  array
     * @return boolean
     */
    public function check_contact_filter($data, $node){
        $contact = $data['contact'];

        if(!isset($node['data']['filter_type'])){
            $node['data']['filter_type'] = 'lead';
        }

        if(!isset($node['data']['customer_name_of_variable'])){
            $node['data']['customer_name_of_variable'] = 'company';
        }

        if(!isset($node['data']['name_of_variable'])){
            $node['data']['name_of_variable'] = 'name';
        }
        
        if(!isset($node['data']['condition'])){
            $node['data']['condition'] = 'equals';
        }

        if(!isset($node['data']['value_of_variable'])){
            $node['data']['value_of_variable'] = '';
        }

        if($node['data']['name_of_variable'] == 'tag'){
            return false;
        }

        if($node['data']['filter_type'] == 'customer'){
            switch ($node['data']['customer_name_of_variable']) {
                case 'country':
                    $name_of_variable = 'country_name';
                    break;
                default:
                    $name_of_variable = $node['data']['customer_name_of_variable'];
                    break;
            }
        }else{
            switch ($node['data']['name_of_variable']) {
                case 'status':
                    $name_of_variable = 'status_name';
                    break;
                case 'country':
                    $name_of_variable = 'country_name';
                    break;
                case 'source':
                    $name_of_variable = 'source_name';
                    break;
                default:
                    $name_of_variable = $node['data']['name_of_variable'];
                    break;
            }
        }

        if(!isset($contact[$name_of_variable])){
            return false;
        }

        switch ($node['data']['condition']) {
            case 'equals':
                if($node['data']['value_of_variable'] == $contact[$name_of_variable]){
                    return true;
                }
                break;
            case 'not_equal':
                if($node['data']['value_of_variable'] != $contact[$name_of_variable]){
                    return true;
                }
                break;
            case 'greater_than':
                if($node['data']['value_of_variable'] = $contact[$name_of_variable]){
                    return true;
                }
                break;
            case 'greater_than_or_equal':
                if($node['data']['value_of_variable'] <= $contact[$name_of_variable]){
                    return true;
                }
                break;
            case 'less_than':
                if($node['data']['value_of_variable'] > $contact[$name_of_variable]){
                    return true;
                }
                break;
            case 'less_than_or_equal':
                if($node['data']['value_of_variable'] <= $contact[$name_of_variable]){
                    return true;
                }
                break;
            case 'empty':
                if($contact[$name_of_variable] == ''){
                    return true;
                }
                break;
            case 'not_empty':
                if($contact[$name_of_variable] != ''){
                    return true;
                }
                break;
            case 'like':
                if (!(strpos(strtolower($contact[$name_of_variable]), strtolower($node['data']['value_of_variable'])) === false)) {
                    return true;
                }
                break;
            case 'not_like':
                if (!(strpos(strtolower($contact[$name_of_variable]), strtolower($node['data']['value_of_variable'])) !== false)) {
                    return true;
                }
                break;
            default:
                break;

        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function save_email_log($data){
        
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['hash'] = app_generate_hash();
        
        $this->db->insert(db_prefix().'ma_email_logs', $data);

        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function save_sms_log($data){
        
        $data['delivery'] = 1;
        $data['delivery_time'] = date('Y-m-d H:i:s');
        $data['dateadded'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix().'ma_sms_logs', $data);

        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * download asset
     * @param  string $hash_share
     * @return boolean
     */
    public function download_asset($asset_id, $asset_log_id = 0) {
        $browser = $this->getBrowser();

        $this->db->insert(db_prefix() . 'ma_asset_download_logs', [
            'ip' => $this->get_client_ip(),
            'browser_name' => $browser['name'],
            'http_user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'asset_id' => $asset_id,
            'asset_log_id' => $asset_log_id,
            'time' => date('Y-m-d H:i:s'),
        ]);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * get Browser info
     * @return array
     */
    public function getBrowser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/coc_coc_browser/i', $u_agent)) {
            $bname = 'Cốc Cốc';
            $ub = "coc_coc_browser";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {$version = "?";}

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern,
        );
    }

    /**
     * Function to get the client IP address
     * @return string
     */
    public function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_asset_download_chart($asset_id = '')
    {
        $this->db->select('date_format(time, \'%Y-%m-%d\') as time, COUNT(*) as count_download');
        if($asset_id != ''){
            $this->db->where('asset_id', $asset_id);
        }
        $this->db->group_by('date_format(time, \'%Y-%m-%d\')');
        $asset_download = $this->db->get(db_prefix().'ma_asset_download_logs')->result_array();
        $data_asset_download = [];
        foreach($asset_download as $download){
            $data_asset_download[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_download']];
        }
        
        return $data_asset_download;
    }

    /**
     * @param  integer
     * @param  string
     * @return array
     */
    public function get_lead_by_email_template($id, $return_type = 'leads'){
        
        $where = '('.db_prefix().'leads.id in (SELECT lead_id FROM '.db_prefix().'ma_email_logs WHERE email_template_id = '.$id.' GROUP BY lead_id))';

        if($return_type == 'leads'){
            $this->db->where($where);
            $this->db->where('ma_unsubscribed', 0);
            $leads = $this->db->get(db_prefix().'leads')->result_array();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_campaign_by_email_template($id){
        $where = 'workflow LIKE \'%\\\\\\\\"email_template\\\\\\\\":\\\\\\\\"'.$id.'\\\\\\\\"%\'';
        $this->db->where('start_date <= "'.date('Y-m-d').'" AND end_date >= "'.date('Y-m-d').'"');
        $this->db->where($where);
        $campaigns = $this->db->get(db_prefix().'ma_campaigns')->result_array();

        $this->db->where('end_date <= "'.date('Y-m-d').'"');
        $this->db->where($where);
        $old_campaigns = $this->db->get(db_prefix().'ma_campaigns')->result_array();

        return ['campaigns' => count($campaigns), 'old_campaigns' => count($old_campaigns)];
    }

    /**
     * @param  integer
     * @param  array
     * @return array
     */
    public function get_data_email_template_chart($email_template_id = '', $data_filter = [])
    {
        $where = $this->get_where_report_period('date_format(dateadded, \'%Y-%m-%d\')');

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($email_template_id != ''){
            $this->db->where('email_template_id', $email_template_id);
        }
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $this->db->where('(delivery = 1 OR failed = 1)');
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->result_array();
        $data_delivery = [];
        foreach($email_logs as $download){
            $data_delivery[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($email_template_id != ''){
            $this->db->where('email_template_id', $email_template_id);
        }
        $this->db->where('open', 1);
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $this->db->where('(delivery = 1 OR failed = 1)');
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->result_array();
        $data_open = [];
        foreach($email_logs as $download){
            $data_open[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($email_template_id != ''){
            $this->db->where('email_template_id', $email_template_id);
        }
        $this->db->where('click', 1);
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $this->db->where('(delivery = 1 OR failed = 1)');
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->result_array();
        $data_click = [];
        foreach($email_logs as $download){
            $data_click[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('delivery'), 'data' => $data_delivery, 'color' => '#008ece'];
        $data_return[] = ['name' => _l('read'), 'data' => $data_open, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('click'), 'data' => $data_click, 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_email_template_by_campaign_chart($email_template_id)
    {
        $this->db->select('campaign_id');
        $this->db->where('email_template_id', $email_template_id);
        $this->db->where('(delivery = 1 OR failed = 1)');
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $this->db->group_by('campaign_id');
        $campaign_ids = $this->db->get(db_prefix().'ma_email_logs')->result_array();

        $data_header = [];
        $data_delivery = [];
        $data_open = [];
        $data_click = [];

        foreach($campaign_ids as $value){
            $campaign = $this->get_campaign($value['campaign_id']);
            if($campaign){
                $data_header[] = $campaign->name;

                $this->db->where('email_template_id', $email_template_id);
                $this->db->where('campaign_id', $value['campaign_id']);
                $this->db->where('(lead_id != 0 or client_id != 0)');
                $this->db->where('(delivery = 1 OR failed = 1)');
                $count_delivery = $this->db->count_all_results(db_prefix().'ma_email_logs');
                $data_delivery[] = $count_delivery;

                $this->db->where('email_template_id', $email_template_id);
                $this->db->where('campaign_id', $value['campaign_id']);
                $this->db->where('open', 1);
                $this->db->where('(lead_id != 0 or client_id != 0)');
                $this->db->where('(delivery = 1 OR failed = 1)');
                $count_open = $this->db->count_all_results(db_prefix().'ma_email_logs');
                $data_open[] = $count_open;

                $this->db->where('email_template_id', $email_template_id);
                $this->db->where('campaign_id', $value['campaign_id']);
                $this->db->where('click', 1);
                $this->db->where('(lead_id != 0 or client_id != 0)');
                $this->db->where('(delivery = 1 OR failed = 1)');
                $count_click = $this->db->count_all_results(db_prefix().'ma_email_logs');
                $data_click[] = $count_click;
            }
        }

        $data_return = [];
        $data_return[] = ['name' => _l('delivery'), 'data' => $data_delivery, 'color' => '#008ece'];
        $data_return[] = ['name' => _l('read'), 'data' => $data_open, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('click'), 'data' => $data_click, 'color' => '#ff6f00'];

        return ['header' => $data_header, 'data' => $data_return];
    }

    /**
     * @param  integer
     * @param  string
     * @return array
     */
    public function get_lead_by_point_action($id, $return_type = 'leads'){
        
        $where = '('.db_prefix().'leads.id in (SELECT lead_id FROM '.db_prefix().'ma_point_action_logs WHERE point_action_id = '.$id.' GROUP BY lead_id))';

        if($return_type == 'leads'){
            $this->db->where($where);
            $this->db->where('ma_unsubscribed', 0);
            $leads = $this->db->get(db_prefix().'leads')->result_array();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_point_action_chart($point_action_id = '')
    {
        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_action');
        if($point_action_id != ''){
            $this->db->where('point_action_id', $point_action_id);
        }
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $point_action = $this->db->get(db_prefix().'ma_point_action_logs')->result_array();

        $data_point_action = [];
        foreach($point_action as $action){
            $data_point_action[] = [strtotime($action['time'].' 00:00:00') * 1000, (int)$action['count_action']];
        }
        
        return $data_point_action;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_point_action_by_campaign_chart($point_action_id)
    {
        $this->db->select('campaign_id');
        $this->db->where('point_action_id', $point_action_id);
        $this->db->group_by('campaign_id');
        $campaign_ids = $this->db->get(db_prefix().'ma_point_action_logs')->result_array();

        $data_header = [];
        $data_action = [];
        $data_open = [];
        $data_click = [];

        foreach($campaign_ids as $value){
            $campaign = $this->get_campaign($value['campaign_id']);
            $data_header[] = $campaign->name;

            $this->db->where('point_action_id', $point_action_id);
            $this->db->where('campaign_id', $value['campaign_id']);
            $count_action = $this->db->count_all_results(db_prefix().'ma_point_action_logs');
            $data_action[] = $count_action;
        }

        $data_return = [];
        $data_return[] = ['name' => _l('point_action'), 'data' => $data_action, 'color' => '#008ece'];

        return ['header' => $data_header, 'data' => $data_return];
    }

    /**
     * Gets the where report period.
     *
     * @param      string  $field  The field
     *
     * @return     string  The where report period.
     */
    private function get_where_report_period($field = 'date')
    {
        $months_report      = $this->input->get('date_filter');
        
        $custom_date_select = '';
        if ($months_report != '') {
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int) $months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth   = date('Y-m-t');
                }

                $custom_date_select = '(' . $field . ' BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
            } elseif ($months_report == 'last_30_days') {
                $custom_date_select = '(' . $field . ' BETWEEN "' . date('Y-m-d', strtotime('today - 30 days')) . '" AND "' . date('Y-m-d') . '")';
            } elseif ($months_report == 'this_month') {
                $custom_date_select = '(' . $field . ' BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
            } elseif ($months_report == 'last_month') {
                $this_month = date('m') - 1;
                $custom_date_select = '(' . $field . ' BETWEEN "' . date("Y-m-d", strtotime("first day of previous month")) . '" AND "' . date("Y-m-d", strtotime("last day of previous month")) . '")';
            }elseif ($months_report == 'this_quarter') {
                $current_month = date('m');
                  $current_year = date('Y');
                  if($current_month>=1 && $current_month<=3)
                  {
                    $start_date = date('Y-m-d', strtotime('1-January-'.$current_year));  // timestamp or 1-Januray 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-April-'.$current_year));  // timestamp or 1-April 12:00:00 AM means end of 31 March
                  }
                  else  if($current_month>=4 && $current_month<=6)
                  {
                    $start_date = date('Y-m-d', strtotime('1-April-'.$current_year));  // timestamp or 1-April 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-July-'.$current_year));  // timestamp or 1-July 12:00:00 AM means end of 30 June
                  }
                  else  if($current_month>=7 && $current_month<=9)
                  {
                    $start_date = date('Y-m-d', strtotime('1-July-'.$current_year));  // timestamp or 1-July 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-October-'.$current_year));  // timestamp or 1-October 12:00:00 AM means end of 30 September
                  }
                  else  if($current_month>=10 && $current_month<=12)
                  {
                    $start_date = date('Y-m-d', strtotime('1-October-'.$current_year));  // timestamp or 1-October 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-January-'.($current_year+1)));  // timestamp or 1-January Next year 12:00:00 AM means end of 31 December this year
                  }
                $custom_date_select = '(' . $field . ' BETWEEN "' .
                $start_date .
                '" AND "' .
                $end_date . '")';

            }elseif ($months_report == 'last_quarter') {
                $current_month = date('m');
                    $current_year = date('Y');

                  if($current_month>=1 && $current_month<=3)
                  {
                    $start_date = date('Y-m-d', strtotime('1-October-'.($current_year-1)));  // timestamp or 1-October Last Year 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-January-'.$current_year));  // // timestamp or 1-January  12:00:00 AM means end of 31 December Last year
                  } 
                  else if($current_month>=4 && $current_month<=6)
                  {
                    $start_date = date('Y-m-d', strtotime('1-January-'.$current_year));  // timestamp or 1-Januray 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-April-'.$current_year));  // timestamp or 1-April 12:00:00 AM means end of 31 March
                  }
                  else  if($current_month>=7 && $current_month<=9)
                  {
                    $start_date = date('Y-m-d', strtotime('1-April-'.$current_year));  // timestamp or 1-April 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-July-'.$current_year));  // timestamp or 1-July 12:00:00 AM means end of 30 June
                  }
                  else  if($current_month>=10 && $current_month<=12)
                  {
                    $start_date = date('Y-m-d', strtotime('1-July-'.$current_year));  // timestamp or 1-July 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-October-'.$current_year));  // timestamp or 1-October 12:00:00 AM means end of 30 September
                  }
                $custom_date_select = '(' . $field . ' BETWEEN "' .
                $start_date .
                '" AND "' .
                $end_date . '")';

            }elseif ($months_report == 'this_year') {
                $custom_date_select = '(' . $field . ' BETWEEN "' .
                date('Y-m-d', strtotime(date('Y-01-01'))) .
                '" AND "' .
                date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
            } elseif ($months_report == 'last_year') {
                $custom_date_select = '(' . $field . ' BETWEEN "' .
                date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
                '" AND "' .
                date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from'));
                $to_date   = to_sql_date($this->input->post('report_to'));
                if ($from_date == $to_date) {
                    $custom_date_select = '' . $field . ' = "' . $from_date . '"';
                } else {
                    $custom_date_select = '(' . $field . ' BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                }
            } elseif(!(strpos($months_report, 'financial_year') === false)){
                $year = explode('financial_year_', $months_report);

                $custom_date_select = '(' . $field . ' BETWEEN "' . date($year[1].'-01-01') . '" AND "' . date(($year[1]).'-12-t') . '")';
            }
        }

        return $custom_date_select;
    }

    /**
     * @param  string
     * @param  array
     * @return array
     */
    public function get_data_form_submit_chart($form_id = '', $data_filter = [])
    {
        $where = $this->get_where_report_period('date_format(dateadded, \'%Y-%m-%d\')');

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_submit');
        $this->db->where('from_ma_form_id != 0');
        if($form_id != ''){
            $this->db->where('from_ma_form_id', $form_id);
        }

        if($where != ''){
            $this->db->where($where);
        }

        $this->db->where('ma_unsubscribed', 0);
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $form_submit = $this->db->get(db_prefix().'leads')->result_array();
        $data_form_submit = [];
        foreach($form_submit as $submit){
            $data_form_submit[] = [strtotime($submit['time'].' 00:00:00') * 1000, (int)$submit['count_submit']];
        }
        
        return $data_form_submit;
    }

    /**
     * @param  array
     * @return array
     */
    public function get_data_lead_chart($data_filter = [])
    {
        $where = $this->get_where_report_period('date_format(dateadded, \'%Y-%m-%d\')');

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_lead');
       
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->where('ma_unsubscribed', 0);
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $lead_created = $this->db->get(db_prefix().'leads')->result_array();

        $data_created = [];
        foreach($lead_created as $lead){
            $data_created[] = [strtotime($lead['time'].' 00:00:00') * 1000, (int)$lead['count_lead']];
        }

        $where = $this->get_where_report_period('date_format(date_converted, \'%Y-%m-%d\')');

        $this->db->select('date_format(date_converted, \'%Y-%m-%d\') as time, COUNT(*) as count_lead');
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->where('ma_unsubscribed', 0);
        $this->db->group_by('date_format(date_converted, \'%Y-%m-%d\')');
        $lead_converted = $this->db->get(db_prefix().'leads')->result_array();
        $data_converted = [];
        foreach($lead_converted as $lead){
            $data_converted[] = [strtotime($lead['time'].' 00:00:00') * 1000, (int)$lead['count_lead']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('created'), 'data' => $data_created, 'color' => '#008ece'];
        $data_return[] = ['name' => _l('converted'), 'data' => $data_converted, 'color' => '#84c529'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @param  string
     * @return array
     */
    public function get_lead_by_stage($id, $return_type = 'leads'){
        
        $where = '('.db_prefix().'leads.id in (SELECT lead_id FROM '.db_prefix().'ma_lead_stages WHERE stage_id = '.$id.' AND deleted = 0 GROUP BY lead_id))';
       
        if($return_type == 'leads'){
            $this->db->where($where);
            $this->db->where('ma_unsubscribed', 0);
            $leads = $this->db->get(db_prefix().'leads')->result_array();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_campaign_by_stage($id){
        $where = 'workflow LIKE \'%\\\\\\\\"stage\\\\\\\\":\\\\\\\\"'.$id.'\\\\\\\\"%\'';

        $this->db->where('start_date <= "'.date('Y-m-d').'" AND end_date >= "'.date('Y-m-d').'"');
        $this->db->where($where);
        $campaigns = $this->db->get(db_prefix().'ma_campaigns')->result_array();

        $this->db->where('end_date <= "'.date('Y-m-d').'"');
        $this->db->where($where);
        $old_campaigns = $this->db->get(db_prefix().'ma_campaigns')->result_array();

        return ['campaigns' => count($campaigns), 'old_campaigns' => count($old_campaigns)];
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_segment_detail_chart($segment_id){

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        $this->db->where('segment_id', $segment_id);
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_lead_segments')->result_array();
        $data_added = [];
        foreach($email_logs as $download){
            $data_added[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $this->db->select('date_format(date_delete, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        $this->db->where('segment_id', $segment_id);
        $this->db->where('deleted', 1);
        $this->db->group_by('date_format(date_delete, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_lead_segments')->result_array();
        $data_removed = [];
        foreach($email_logs as $download){
            $data_removed[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('added'), 'data' => $data_added, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('removed'), 'data' => $data_removed, 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_segment_by_campaign_chart($segment_id)
    {
        $this->db->select('campaign_id');
        $this->db->where('segment_id', $segment_id);
        $this->db->group_by('campaign_id');
        $campaign_ids = $this->db->get(db_prefix().'ma_lead_segments')->result_array();

        $data_header = [];
        $data_lead = [];

        foreach($campaign_ids as $value){
            $campaign = $this->get_campaign($value['campaign_id']);
            $data_header[] = $campaign->name;

            $this->db->where('segment_id', $segment_id);
            $this->db->where('campaign_id', $value['campaign_id']);
            $count_lead = $this->db->count_all_results(db_prefix().'ma_lead_segments');
            $data_lead[] = $count_lead;
        }

        $data_return = [];
        $data_return[] = ['name' => _l('contact'), 'data' => $data_lead, 'color' => '#008ece'];

        return ['header' => $data_header, 'data' => $data_return];
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_stage_detail_chart($stage_id){

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        $this->db->where('stage_id', $stage_id);
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_lead_stages')->result_array();
        $data_added = [];
        foreach($email_logs as $download){
            $data_added[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $this->db->select('date_format(date_delete, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        $this->db->where('stage_id', $stage_id);
        $this->db->where('deleted', 1);
        $this->db->group_by('date_format(date_delete, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_lead_stages')->result_array();
        $data_removed = [];
        foreach($email_logs as $download){
            $data_removed[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('added'), 'data' => $data_added, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('removed'), 'data' => $data_removed, 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_stage_by_campaign_chart($stage_id)
    {
        $this->db->select('campaign_id');
        $this->db->where('stage_id', $stage_id);
        $this->db->group_by('campaign_id');
        $campaign_ids = $this->db->get(db_prefix().'ma_lead_stages')->result_array();

        $data_header = [];
        $data_lead = [];

        foreach($campaign_ids as $value){
            $campaign = $this->get_campaign($value['campaign_id']);
            $data_header[] = $campaign->name;

            $this->db->where('stage_id', $stage_id);
            $this->db->where('campaign_id', $value['campaign_id']);
            $count_lead = $this->db->count_all_results(db_prefix().'ma_lead_stages');
            $data_lead[] = $count_lead;
        }

        $data_return = [];
        $data_return[] = ['name' => _l('point_lead'), 'data' => $data_lead, 'color' => '#008ece'];

        return ['header' => $data_header, 'data' => $data_return];
    }

    /**
     * @param  integer
     * @param  integer
     * @return boolean
     */
    public function remove_from_campaign($campaign_id, $contact_id, $contact_type){
        
        if($contact_type == 'lead'){
            $this->db->insert(db_prefix().'ma_campaign_lead_exceptions' , [
                'campaign_id' => $campaign_id, 
                'lead_id' => $contact_id, 
                'dateadded' => date('Y-m-d H:i:s')
            ]);
        }else{
            $this->db->insert(db_prefix().'ma_campaign_client_exceptions' , [
                'campaign_id' => $campaign_id, 
                'client_id' => $contact_id, 
                'dateadded' => date('Y-m-d H:i:s')
            ]);
        }

        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * @param  integer
     * @param  integer
     * @return boolean
     */
    public function check_lead_exception($campaign_id, $lead_id){
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('lead_id', $lead_id);
        $lead_exception = $this->db->get(db_prefix().'ma_campaign_lead_exceptions')->row();

        if ($lead_exception) {
            return true;
        }

        return false;
    }

    /**
     * @param  integer
     * @param  string
     * @param  string
     * @return mixed
     */
    public function get_object_by_campaign($campaign_id, $type = '', $return = 'id'){
        $campaign = $this->get_campaign($campaign_id);
        $leads = $this->get_lead_by_campaign($campaign_id);
        $total_lead = count($leads);

        $workflow = [];
        if($campaign->workflow != null){
            $workflow = explode('\"'.$type.'\":\"',$campaign->workflow);
        }

        $where = '';
        $object = [];
        if(isset($workflow[1])){
            foreach($workflow as $k => $data){
                if($k != 0){
                    $_workflow = explode('\"',$data);
                    if(isset($_workflow[1]) && !in_array($_workflow[0], $object)){
                        $object[] = $_workflow[0];
                    }
                }
            }
        }

        $data_return = [];
        if($return == 'object'){
            foreach($object as $id){
                switch ($type) {
                    case 'point_action':
                        $point_action = $this->get_point_action($id);
                        if($point_action){
                            $this->db->where('point_action_id', $id);
                            $this->db->where('campaign_id', $campaign_id);
                            $point_action->total = $this->db->count_all_results(db_prefix().'ma_point_action_logs');
                            $data_return[] = $point_action;
                        }
                        break;
                    case 'email':
                        $email_template = $this->get_email($id);
                        if($email_template){
                            $this->db->where('email_id', $id);
                            $this->db->where('campaign_id', $campaign_id);
                            $this->db->where('(lead_id != 0 or client_id != 0)');
                            $this->db->where('(delivery = 1 OR failed = 1)');
                            $email_template->total = $this->db->count_all_results(db_prefix().'ma_email_logs');

                            $this->db->where('email_id', $id);
                            $this->db->where('campaign_id', $campaign_id);
                            $this->db->where('open', 1);
                            $this->db->where('(lead_id != 0 or client_id != 0)');
                            $this->db->where('(delivery = 1 OR failed = 1)');
                            $email_template->open = $this->db->count_all_results(db_prefix().'ma_email_logs');
                            $email_template->open_percent = $email_template->total != 0 ? round(($email_template->open/$email_template->total) * 100, 2) : 0;

                            $this->db->where('email_id', $id);
                            $this->db->where('campaign_id', $campaign_id);
                            $this->db->where('click', 1);
                            $this->db->where('(lead_id != 0 or client_id != 0)');
                            $this->db->where('(delivery = 1 OR failed = 1)');
                            $email_template->click = $this->db->count_all_results(db_prefix().'ma_email_logs');
                            $email_template->click_percent = $email_template->total != 0 ? round(($email_template->click/$email_template->total) * 100, 2) : 0;

                            $email_template->average_time_to_open = $this->get_average_time_to_open_email($campaign_id, $id);
                            $data_return[] = $email_template;
                        }
                        break;
                    case 'segment':
                        $segment = $this->get_segment($id);
                        if($segment){
                            $this->db->where('segment_id', $id);
                            $this->db->where('campaign_id', $campaign_id);
                            $segment->total = $this->db->count_all_results(db_prefix().'ma_lead_segments');
                            $data_return[] = $segment;
                        }
                        break;

                    case 'customer_segment':
                        $segment = $this->get_segment($id);
                        if($segment){
                            $this->db->where('segment_id', $id);
                            $this->db->where('campaign_id', $campaign_id);
                            $segment->total = $this->db->count_all_results(db_prefix().'ma_lead_segments');
                            $data_return[] = $segment;
                        }
                        break;

                    case 'stage':
                        $stage = $this->get_stage($id);
                        if($stage){
                            $this->db->where('stage_id', $id);
                            $this->db->where('campaign_id', $campaign_id);
                            $stage->total = $this->db->count_all_results(db_prefix().'ma_lead_stages');
                            $stage->percent = $total_lead != 0 ? round(($stage->total / $total_lead) * 100, 2) : 0;

                            $data_return[] = $stage;
                        }
                        break;
                    case 'sms':
                        $sms = $this->get_sms($id);
                        if($sms){
                            $this->db->where('sms_id', $id);
                            $this->db->where('campaign_id', $campaign_id);
                            $sms->total = $this->db->count_all_results(db_prefix().'ma_sms_logs');
                            
                            $data_return[] = $sms;
                        }
                        break;
                    
                    default:
                        // code...
                        break;
                }
            }

            return $data_return;
        }
        
        return $object;
    }

    /**
     * @return boolean
     */
    public function ma_cron_campaign(){
        $where = 'start_date <= "'.date('Y-m-d').'" AND end_date >= "'.date('Y-m-d').'" AND published = 1';
        $campaigns = $this->get_campaign('', $where);

        foreach($campaigns as $campaign){
            $this->run_campaigns($campaign['id']);
        }

        return true;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_campaign_email_chart($campaign_id = '')
    {

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $this->db->where('(failed = 1)');
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->result_array();
        $data_fail = [];
        foreach($email_logs as $download){
            $data_fail[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $this->db->where('(delivery = 1)');
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->result_array();
        $data_delivery = [];
        foreach($email_logs as $download){
            $data_delivery[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $this->db->where('open', 1);
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->result_array();
        $data_open = [];
        foreach($email_logs as $download){
            $data_open[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $this->db->where('click', 1);
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->result_array();
        $data_click = [];
        foreach($email_logs as $download){
            $data_click[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('fail'), 'data' => $data_fail, 'color' => '#d8341b'];
        $data_return[] = ['name' => _l('delivery'), 'data' => $data_delivery, 'color' => '#008ece'];
        $data_return[] = ['name' => _l('read'), 'data' => $data_open, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('click'), 'data' => $data_click, 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_campaign_segment_chart($campaign_id = ''){

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }

        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_lead_segments')->result_array();
        $data_added = [];
        foreach($email_logs as $download){
            $data_added[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $this->db->select('date_format(date_delete, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->where('deleted', 1);
        $this->db->group_by('date_format(date_delete, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_lead_segments')->result_array();
        $data_removed = [];
        foreach($email_logs as $download){
            $data_removed[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('added'), 'data' => $data_added, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('removed'), 'data' => $data_removed, 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_campaign_stage_chart($campaign_id = ''){

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_lead_stages')->result_array();
        $data_added = [];
        foreach($email_logs as $download){
            $data_added[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $this->db->select('date_format(date_delete, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->where('deleted', 1);
        $this->db->group_by('date_format(date_delete, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_lead_stages')->result_array();
        $data_removed = [];
        foreach($email_logs as $download){
            $data_removed[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('added'), 'data' => $data_added, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('removed'), 'data' => $data_removed, 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @param  string
     * @return array
     */
    public function get_lead_by_text_message($id, $return_type = 'leads'){
        
        $where = '('.db_prefix().'leads.id in (SELECT lead_id FROM '.db_prefix().'ma_sms_logs WHERE text_message_id = '.$id.' GROUP BY lead_id))';

        if($return_type == 'leads'){
            $this->db->where($where);
            $this->db->where('ma_unsubscribed', 0);
            $leads = $this->db->get(db_prefix().'leads')->result_array();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_campaign_by_text_message($id){
        $where = 'workflow LIKE \'%\\\\\\\\"text_message\\\\\\\\":\\\\\\\\"'.$id.'\\\\\\\\"%\'';

        $this->db->where('start_date <= "'.date('Y-m-d').'" AND end_date >= "'.date('Y-m-d').'"');
        $this->db->where($where);
        $campaigns = $this->db->get(db_prefix().'ma_campaigns')->result_array();

        $this->db->where('end_date <= "'.date('Y-m-d').'"');
        $this->db->where($where);
        $old_campaigns = $this->db->get(db_prefix().'ma_campaigns')->result_array();

        return ['campaigns' => count($campaigns), 'old_campaigns' => count($old_campaigns)];
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_text_message_by_campaign_chart($text_message_id)
    {
        $this->db->select('campaign_id');
        $this->db->where('text_message_id', $text_message_id);
        $this->db->group_by('campaign_id');
        $campaign_ids = $this->db->get(db_prefix().'ma_sms_logs')->result_array();

        $data_header = [];
        $data_action = [];
        $data_open = [];
        $data_click = [];

        foreach($campaign_ids as $value){
            $campaign = $this->get_campaign($value['campaign_id']);
            $data_header[] = $campaign->name;

            $this->db->where('text_message_id', $text_message_id);
            $this->db->where('campaign_id', $value['campaign_id']);
            $count_action = $this->db->count_all_results(db_prefix().'ma_sms_logs');
            $data_action[] = $count_action;
        }

        $data_return = [];
        $data_return[] = ['name' => _l('text_message'), 'data' => $data_action, 'color' => '#008ece'];

        return ['header' => $data_header, 'data' => $data_return];
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_text_message_chart($text_message_id = '')
    {
        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_action');
        if($text_message_id != ''){
            $this->db->where('text_message_id', $text_message_id);
        }
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $text_message = $this->db->get(db_prefix().'ma_sms_logs')->result_array();

        $data_text_message = [];
        foreach($text_message as $action){
            $data_text_message[] = [strtotime($action['time'].' 00:00:00') * 1000, (int)$action['count_action']];
        }
        
        return $data_text_message;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_campaign_text_message_chart($campaign_id = '')
    {
        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_action');
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $text_message = $this->db->get(db_prefix().'ma_sms_logs')->result_array();

        $data_text_message = [];
        foreach($text_message as $action){
            $data_text_message[] = [strtotime($action['time'].' 00:00:00') * 1000, (int)$action['count_action']];
        }
        
        return $data_text_message;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_campaign_point_action_chart($campaign_id = '')
    {
        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_action');
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $point_action = $this->db->get(db_prefix().'ma_point_action_logs')->result_array();

        $data_point_action = [];
        foreach($point_action as $action){
            $data_point_action[] = [strtotime($action['time'].' 00:00:00') * 1000, (int)$action['count_action']];
        }
        
        return $data_point_action;
    }

    /**
     * @param  string
     * @param  integer
     * @param  integer
     * @return string
     */
    public function parse_content_merge_fields($content, $data = [], $log_id = ''){
        if (!class_exists('other_merge_fields', false)) {
            $this->load->library('merge_fields/other_merge_fields');
        }

        $merge_fields = [];
        $merge_fields = array_merge($merge_fields, $this->other_merge_fields->format());

        foreach ($merge_fields as $key => $val) {
            $content = stripos($content ?? '', $key) !== false
            ? str_replace($key, $val, $content ?? '')
            : str_replace($key, '', $content ?? '');
        }

        if(isset($data['lead'])){
            $this->load->library('merge_fields/leads_merge_fields');
            $merge_fields = array_merge($merge_fields, $this->leads_merge_fields->format($data['lead']['id']));

            $lead_name = $merge_fields['{lead_name}'];
            $lead_last_name = (strpos($lead_name, '') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $lead_name);
            $lead_first_name = trim(preg_replace('#'.preg_quote($lead_last_name, '#').'#', '', $lead_name));

            $content = stripos($content ?? '', '{lead_first_name}') !== false
                ? str_replace('{lead_first_name}', $lead_first_name, $content ?? '')
                : str_replace('{lead_first_name}', '', $content ?? '');

            $content = stripos($content ?? '', '{lead_last_name}') !== false
                ? str_replace('{lead_last_name}', $lead_last_name, $content ?? '')
                : str_replace('{lead_last_name}', '', $content ?? '');

            foreach ($merge_fields as $key => $val) {
                $content = stripos($content ?? '', $key) !== false
                ? str_replace($key, $val, $content ?? '')
                : str_replace($key, '', $content ?? '');
            }


        }

        if(isset($data['client'])){
            $this->load->library('merge_fields/client_merge_fields');
            $merge_fields = array_merge($merge_fields, $this->client_merge_fields->format($data['client']['userid']));

            foreach ($merge_fields as $key => $val) {
                $content = stripos($content ?? '', $key) !== false
                ? str_replace($key, $val, $content ?? '')
                : str_replace($key, '', $content ?? '');
            }
        }

        if($log_id != ''){
            $this->db->where('id', $log_id);
            $email_log = $this->db->get(db_prefix().'ma_email_logs')->row();

            $dom = new DOMDocument();
            $dom->loadHTML(html_entity_decode($content ?? ''), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            // Lấy danh sách tất cả các thẻ a
            $anchors = $dom->getElementsByTagName('a');

            // Thêm sự kiện chuyển hướng vào mỗi thẻ a
            foreach ($anchors as $anchor) {
                $a_text = trim($anchor->nodeValue);
                $href = $anchor->getAttribute('href');

                if($a_text == 'ACCEPT_BTN'){
                    $anchor->setAttribute('href', site_url('ma/ma_public/click/'.$email_log->hash.'?confirm=1&href='.$href));
                }elseif($a_text == 'DECLINE_BTN'){
                    $anchor->setAttribute('href', site_url('ma/ma_public/click/'.$email_log->hash.'?confirm=0&href='.$href));
                }else{
                    $anchor->setAttribute('href', site_url('ma/ma_public/click/'.$email_log->hash.'?href='.$href));
                }
            }

            // Chuyển đổi DOMDocument trở lại chuỗi HTML
            $content = $dom->saveHTML();
            $content = str_replace('ACCEPT_BTN', _l('accept'), $content ?? '');
            $content = str_replace('DECLINE_BTN', _l('decline'), $content ?? '');
            
            $content .= '<img alt="" src="'.site_url('ma/ma_public/images/'.$email_log->hash.'.jpg').'" width="1" height="1" />';
        }

        return $content;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_form_chart($form_id = '')
    {
        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_submit');
        if($form_id != ''){
            $this->db->where('form_id', $form_id);
        }
        $this->db->where('(from_ma_form_id != "" or from_ma_form_id is not null)');
        $this->db->where('ma_unsubscribed', 0);
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $point_action = $this->db->get(db_prefix().'leads')->result_array();

        $data_point_action = [];
        foreach($point_action as $action){
            $data_point_action[] = [strtotime($action['time'].' 00:00:00') * 1000, (int)$action['count_submit']];
        }
        
        return $data_point_action;
    }

    /**
     * { mfa setting by admin }
     *
     * @param         $data   The data
     *
     * @return     boolean  
     */
    public function ma_sms_setting($data){
        
        $affected_rows = 0;

        $setting_dt = []; 
        if(isset($data['settings'])){
            $setting_dt['settings'] = $data['settings'];
            unset($data['settings']);
        }

        if(count($setting_dt) > 0){
            $this->load->model('payment_modes_model');
            $this->load->model('settings_model');
            $succ = $this->settings_model->update($setting_dt);
            if($succ > 0){
                $affected_rows++;
            }
        }

        if($affected_rows > 0){
            return true;
        }
        return false;
    }

    /**
     * @param  integer
     * @param  string
     * @return array
     */
    public function get_lead_by_email($id, $return_type = 'leads'){
        
        $where = '('.db_prefix().'leads.id in (SELECT lead_id FROM '.db_prefix().'ma_email_logs WHERE email_id = '.$id.' AND (delivery = 1 OR failed = 1) GROUP BY lead_id))';

        if($return_type == 'leads'){
            $this->db->where($where);
            $this->db->where('ma_unsubscribed', 0);
            $leads = $this->db->get(db_prefix().'leads')->result_array();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_campaign_by_email($id){
        $where = 'workflow LIKE \'%\\\\\\\\"email\\\\\\\\":\\\\\\\\"'.$id.'\\\\\\\\"%\'';

        $this->db->where('start_date <= "'.date('Y-m-d').'" AND end_date >= "'.date('Y-m-d').'"');
        $this->db->where($where);
        $campaigns = $this->db->get(db_prefix().'ma_campaigns')->result_array();

        $this->db->where('end_date <= "'.date('Y-m-d').'"');
        $this->db->where($where);
        $old_campaigns = $this->db->get(db_prefix().'ma_campaigns')->result_array();

        return ['campaigns' => count($campaigns), 'old_campaigns' => count($old_campaigns)];
    }

    /**
     * @param  integer
     * @param  array
     * @return array
     */
    public function get_data_email_chart($email_id = '', $data_filter = [])
    {
        $where = $this->get_where_report_period('date_format(dateadded, \'%Y-%m-%d\')');
        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($email_id != ''){
            $this->db->where('email_id', $email_id);
        }
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->where('(delivery = 1 OR failed = 1)');
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->result_array();
        $data_delivery = [];
        foreach($email_logs as $download){
            $data_delivery[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($email_id != ''){
            $this->db->where('email_id', $email_id);
        }
        $this->db->where('open', 1);
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->where('(delivery = 1 OR failed = 1)');
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->result_array();
        $data_open = [];
        foreach($email_logs as $download){
            $data_open[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($email_id != ''){
            $this->db->where('email_id', $email_id);
        }
        $this->db->where('click', 1);
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->where('(delivery = 1 OR failed = 1)');
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->result_array();
        $data_click = [];
        foreach($email_logs as $download){
            $data_click[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('delivery'), 'data' => $data_delivery, 'color' => '#008ece'];
        $data_return[] = ['name' => _l('read'), 'data' => $data_open, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('click'), 'data' => $data_click, 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_email_by_campaign_chart($email_id)
    {
        $this->db->select('campaign_id');
        $this->db->where('email_id', $email_id);
        $this->db->where('(delivery = 1 OR failed = 1)');
        $this->db->group_by('campaign_id');
        $campaign_ids = $this->db->get(db_prefix().'ma_email_logs')->result_array();

        $data_header = [];
        $data_delivery = [];
        $data_open = [];
        $data_click = [];

        foreach($campaign_ids as $value){
            $campaign = $this->get_campaign($value['campaign_id']);
            if($campaign){
                $data_header[] = $campaign->name;

                $this->db->where('email_id', $email_id);
                $this->db->where('campaign_id', $value['campaign_id']);
                $this->db->where('(delivery = 1 OR failed = 1)');
                $count_delivery = $this->db->count_all_results(db_prefix().'ma_email_logs');
                $data_delivery[] = $count_delivery;

                $this->db->where('email_id', $email_id);
                $this->db->where('campaign_id', $value['campaign_id']);
                $this->db->where('open', 1);
                $this->db->where('(delivery = 1 OR failed = 1)');
                $count_open = $this->db->count_all_results(db_prefix().'ma_email_logs');
                $data_open[] = $count_open;

                $this->db->where('email_id', $email_id);
                $this->db->where('campaign_id', $value['campaign_id']);
                $this->db->where('click', 1);
                $this->db->where('(delivery = 1 OR failed = 1)');
                $count_click = $this->db->count_all_results(db_prefix().'ma_email_logs');
                $data_click[] = $count_click;
            }
        }

        $data_return = [];
        $data_return[] = ['name' => _l('delivery'), 'data' => $data_delivery, 'color' => '#008ece'];
        $data_return[] = ['name' => _l('read'), 'data' => $data_open, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('click'), 'data' => $data_click, 'color' => '#ff6f00'];

        return ['header' => $data_header, 'data' => $data_return];
    }

    /**
     * @param  array
     * @return boolean
     */
    public function email_design_save($data){
        if(isset($data['id']) && $data['id'] != ''){
            $this->db->where('id', $data['id']);
            $this->db->update(db_prefix() . 'ma_email_designs', ['data_html' => json_encode($data['data_html']), 'data_design' => json_encode($data['data_design'])]);

            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add new sms
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_sms($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        $this->db->insert(db_prefix() . 'ma_sms', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if ($data['sms_template'] != '') {
                $sms_template = $this->get_text_message($data['sms_template']);
                foreach($sms_template->data_design as $design){
                    $this->db->insert(db_prefix() . 'ma_sms_designs', [
                        'sms_id' => $insert_id,
                        'language' => $design['language'],
                        'content' => $design['content'],
                    ]);
                }
            }

            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get sms
     * @param  mixed $id sms id (Optional)
     * @return mixed     object or array
     */
    public function get_sms($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            $sms = $this->db->get(db_prefix() . 'ma_sms')->row();

            if($sms){
                $this->db->where('sms_id', $id);
                $sms->data_design = $this->db->get(db_prefix() . 'ma_sms_designs')->result_array();
            }

            return $sms;
        }
        
        $this->db->where('published', 1);
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'ma_sms')->result_array();
    }

    /**
     * Add new sms
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_sms($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if ($data['sms_template'] != '') {
            $sms = $this->get_text_message($data['sms_template']);
        }

        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ma_sms', $data);

        if($this->db->affected_rows() > 0){ 
            if ($data['sms_template'] != '') {
                if($sms->sms_template != $data['sms_template']){

                    $this->db->where('sms_id', $id);
                    $this->db->delete(db_prefix() . 'ma_sms_designs');

                    $sms_template = $this->get_text_message($data['sms_template']);
                    foreach($sms_template->data_design as $design){
                        $this->db->insert(db_prefix() . 'ma_sms_designs', [
                            'sms_id' => $id,
                            'language' => $design['language'],
                            'content' => $design['content'],
                        ]);
                    }
                }
            }

            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete sms from database
     */
    public function delete_sms($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_sms');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('sms_id', $id);
            $this->db->delete(db_prefix() . 'ma_sms_logs');

            $this->db->where('sms_id', $id);
            $this->db->delete(db_prefix() . 'ma_sms_designs');

            return true;
        }

        return false;
    }

    /**
     * @param  integer
     * @param  string
     * @return array
     */
    public function get_lead_by_sms($id, $return_type = 'leads'){
        
        $where = '('.db_prefix().'leads.id in (SELECT lead_id FROM '.db_prefix().'ma_sms_logs WHERE sms_id = '.$id.' GROUP BY lead_id))';

        if($return_type == 'leads'){
            $this->db->where($where);
            $this->db->where('ma_unsubscribed', 0);
            $leads = $this->db->get(db_prefix().'leads')->result_array();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_campaign_by_sms($id){
        $where = 'workflow LIKE \'%\\\\\\\\"sms\\\\\\\\":\\\\\\\\"'.$id.'\\\\\\\\"%\'';

        $this->db->where('start_date <= "'.date('Y-m-d').'" AND end_date >= "'.date('Y-m-d').'"');
        $this->db->where($where);
        $campaigns = $this->db->get(db_prefix().'ma_campaigns')->result_array();

        $this->db->where('end_date <= "'.date('Y-m-d').'"');
        $this->db->where($where);
        $old_campaigns = $this->db->get(db_prefix().'ma_campaigns')->result_array();

        return ['campaigns' => count($campaigns), 'old_campaigns' => count($old_campaigns)];
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_sms_by_campaign_chart($sms_id)
    {
        $this->db->select('campaign_id');
        $this->db->where('sms_id', $sms_id);
        $this->db->group_by('campaign_id');
        $campaign_ids = $this->db->get(db_prefix().'ma_sms_logs')->result_array();

        $data_header = [];
        $data_action = [];
        $data_open = [];
        $data_click = [];

        foreach($campaign_ids as $value){
            $campaign = $this->get_campaign($value['campaign_id']);
            $data_header[] = $campaign->name;

            $this->db->where('sms_id', $sms_id);
            $this->db->where('campaign_id', $value['campaign_id']);
            $count_action = $this->db->count_all_results(db_prefix().'ma_sms_logs');
            $data_action[] = $count_action;
        }

        $data_return = [];
        $data_return[] = ['name' => _l('sms'), 'data' => $data_action, 'color' => '#008ece'];

        return ['header' => $data_header, 'data' => $data_return];
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_sms_chart($sms_id = '')
    {
        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_action');
        if($sms_id != ''){
            $this->db->where('sms_id', $sms_id);
        }
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $sms = $this->db->get(db_prefix().'ma_sms_logs')->result_array();

        $data_sms = [];
        foreach($sms as $action){
            $data_sms[] = [strtotime($action['time'].' 00:00:00') * 1000, (int)$action['count_action']];
        }
        
        return $data_sms;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_campaign_sms_chart($campaign_id = '')
    {
        $this->db->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_action');
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->group_by('date_format(dateadded, \'%Y-%m-%d\')');
        $sms = $this->db->get(db_prefix().'ma_sms_logs')->result_array();

        $data_sms = [];
        foreach($sms as $action){
            $data_sms[] = [strtotime($action['time'].' 00:00:00') * 1000, (int)$action['count_action']];
        }
        
        return $data_sms;
    }

    /**
     * Send email - No templates used only simple string
     * @since Version 1.0.2
     * @param  string $email   email
     * @param  string $ma_email_object email object
     * @param  integer $log_id   email log ID
     * @return boolean
     */
    public function ma_send_email($email, $ma_email_object, $data = [], $log_id = '', $email_design_id = '')
    {   
        $this->load->model('emails_model');

        $subject = $ma_email_object->subject;

        $content = $this->get_email_content_by_contact($ma_email_object->id, $data, $email_design_id);

        $message = $this->parse_content_merge_fields(json_decode($content ?? ''), $data, $log_id);

        $from_name = get_option('companyname');
        if($ma_email_object->from_name != ''){
            $from_name = $ma_email_object->from_name;
        }

        

        $bcc_address = '';
        if($ma_email_object->bcc_address != ''){
            $bcc_address = $ma_email_object->bcc_address;
        }

        $reply_to = '';
        if($ma_email_object->reply_to_address != ''){
            $reply_to = $ma_email_object->reply_to_address;
        }

        $subject = $this->parse_content_merge_fields($subject, $data);

        $from_name = $this->parse_content_merge_fields($from_name, $data);

        $from_email = get_option('smtp_email');

        $this->load->config('email');
        if(get_option('ma_smtp_type') == 'other_smtp'){
            $from_email = trim(get_option('ma_smtp_email'));

            $this->email->useragent  = trim(get_option('ma_mail_engine'));
            $this->email->protocol  = trim(get_option('ma_email_protocol'));
            $this->email->smtp_crypto  = trim(get_option('ma_smtp_encryption'));
            $this->email->smtp_host  = trim(get_option('ma_smtp_host'));

            if (get_option('ma_smtp_username') == '') {
                $this->email->smtp_user    = trim(get_option('ma_smtp_email'));
            } else {
                $this->email->smtp_user    = trim(get_option('ma_smtp_username'));
            }

            $charset = strtoupper(get_option('ma_smtp_email_charset'));
            $charset = trim($charset);
            if ($charset == '' || strcasecmp($charset,'utf8') == 'utf8') {
                $charset = 'utf-8';
            }

            $this->email->charset  = $charset;
            $this->email->smtp_pass  = $this->encryption->decrypt(get_option('ma_smtp_password'));
            $this->email->smtp_port  = trim(get_option('ma_smtp_port'));
        }

        if($ma_email_object->from_address != ''){
            $from_email = $ma_email_object->from_address;
        }

        $cnf = [
            'from_email' => $from_email,
            'from_name'  => $from_name,
            'email'      => $email,
            'subject'    => $subject,
            'message'    => $message,
            'bcc'    => $bcc_address,
            'reply_to'    => $reply_to,
        ];

        $cnf['message'] = check_for_links($cnf['message']);

        $this->email->clear(true);
        $this->email->set_newline(config_item('newline'));
        $this->email->from($cnf['from_email'], $cnf['from_name']);
        $this->email->to($cnf['email']);

        $bcc = '';
        // Used for action hooks
        if (isset($cnf['bcc']) && $cnf['bcc'] != '') {
            $bcc = $cnf['bcc'];
            if (is_array($bcc)) {
                $bcc = implode(', ', $bcc);
            }
        }

        $systemBCC = get_option('ma_bcc_emails');
        if ($systemBCC != '') {
            if ($bcc != '') {
                $bcc .= ', ' . $systemBCC;
            } else {
                $bcc .= $systemBCC;
            }
        }
        if ($bcc != '') {
            $this->email->bcc($bcc);
        }

        if (isset($cnf['cc'])) {
            $this->email->cc($cnf['cc']);
        }

        if (isset($cnf['reply_to']) && $cnf['reply_to'] != '') {
            $this->email->reply_to($cnf['reply_to']);
        }

        if($ma_email_object->attachment != '0' && $ma_email_object->attachment != ''){
            $this->db->where('rel_id', $ma_email_object->attachment);
            $this->db->where('rel_type', 'ma_asset');
            $file = $this->db->get(db_prefix() . 'files')->row();
            if($file){
                $path = MA_MODULE_UPLOAD_FOLDER . '/assets/' . $file->rel_id . '/' . $file->file_name;

                $asset_hash = app_generate_hash();
                $this->save_asset_log([
                        'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                        'client_id' => (isset($data['client']) ? $data['client']['userid'] : 0),
                        'asset_id' => $ma_email_object->attachment, 
                        'hash' => $asset_hash,
                        'campaign_id' => $data['campaign']->id
                    ]);

                $cnf['message'] .= '<hr><strong>Attachment: </strong> <a href="'.site_url('ma/ma_public/asset/'.$asset_hash).'">'.$file->file_name.'</a>';
            }
        }

        if($log_id != ''){
            $this->db->where('id', $log_id);
            $email_log = $this->db->get(db_prefix().'ma_email_logs')->row();

            $unsubscribe = get_option('ma_unsubscribe');
            if($unsubscribe == 1){
                $unsubscribe_text = get_option('ma_unsubscribe_text');
                if($unsubscribe_text == ''){
                    $unsubscribe_text = _l('unsubscribe');
                }

                $cnf['message'] .= '<br><br><a href="'.site_url('ma/ma_public/unsubscribe/'.$email_log->hash).'" target="_blank">'.$unsubscribe_text.'</a>';
            }
        }

        $this->email->subject($cnf['subject']);

        $this->email->message($cnf['message']);

        $this->email->set_alt_message(strip_html_tags($cnf['message'], '<br/>, <br>, <br />'));       
        if (!filter_var($cnf['email'], FILTER_VALIDATE_EMAIL)) {
            $success = false;
        }else{
            $success = $this->email->send();
        }

        if ($success) {
            if($log_id != ''){
                log_activity('Email sent to: ' . $cnf['email'] . ' Subject: ' . $cnf['subject']);

                $this->db->where('id', $log_id);
                $this->db->update(db_prefix().'ma_email_logs', ['delivery' => 1, 'delivery_time' => date('Y-m-d H:i:s'), 'bcc_address' => $bcc_address != '' ? 1 : 0]);
            }

            return true;
        }else{
            if($log_id != ''){
                $this->db->where('id', $log_id);
                $this->db->update(db_prefix().'ma_email_logs', ['failed' => 1, 'failed_time' => date('Y-m-d H:i:s')]);
            }
        }

        return false;
    }

    /**
     * @param  array
     * @param  integer
     * @param  string
     * @return boolean
     */
    public function check_condition_email($data, $email_id, $type){
        if(isset($data['lead'])){
            $this->db->where('lead_id', $data['lead']['id']);
        }else{
            $this->db->where('client_id', $data['client']['id']);
        }
        $this->db->where('campaign_id', $data['campaign']->id);
        $this->db->where('email_id', $email_id);
        $this->db->where($type, 1);
        $check = $this->db->get(db_prefix().'ma_email_logs')->row();
        if($check){
            return true;
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function clone_email_template($data){
        $email_template = $this->get_email_template($data['id']);
        $data_insert = (array)$email_template;

        unset($data_insert['id']);
        $data_insert['name'] = $data['name'];
        $data_insert['addedfrom'] = get_staff_user_id();
        $data_insert['dateadded'] = date('Y-m-d H:i:s');
        $data_design = $data_insert['data_design'];
        unset($data_insert['data_design']);

        $this->db->insert(db_prefix().'ma_email_templates', $data_insert);

        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            foreach ($data_design as $key => $value) {
                $this->db->insert(db_prefix().'ma_email_template_designs', [
                    'email_template_id' => $insert_id,
                    'language' => $value['language'],
                    'data_design' => $value['data_design'],
                    'data_html' => $value['data_html'],
                ]);
            }

            return $insert_id;
        }

        return false;
    }

    /**
     * add email template language
     * @param array $data
     */
    public function add_email_template_language($data){
        $this->db->where('email_template_id', $data['email_template_id']);
        $this->db->where('language', $data['language']);
        $email_template_design = $this->db->get(db_prefix() . 'ma_email_template_designs')->row();

        if($email_template_design){
            return false;
        }

        $this->db->insert(db_prefix() . 'ma_email_template_designs', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Get email_template
     * @param  mixed $id email_template id (Optional)
     * @return mixed     object or array
     */
    public function get_email_template_design($id)
    {
        $this->db->where('id', $id);

        $email_template_design = $this->db->get(db_prefix() . 'ma_email_template_designs')->row();

        return $email_template_design;
    }

    /**
     * clone email template design
     * @param  array $data 
     * @return [type]       
     */
    public function clone_email_template_design($data){
        $design = $this->get_email_template_design($data['from_language']);

        $this->db->where('email_template_id', $data['email_template_id']);
        $this->db->where('language', $data['to_language']);
        $email_template_design = $this->db->get(db_prefix() . 'ma_email_template_designs')->row();

        if($email_template_design){
            $this->db->where('id', $email_template_design->id);
            $this->db->update(db_prefix() . 'ma_email_template_designs', [
                'data_design' => $design->data_design,
                'data_html' => $design->data_html,
            ]);

            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }else{
            $this->db->insert(db_prefix() . 'ma_email_template_designs', [
                'email_template_id' => $data['email_template_id'],
                'language' => $data['to_language'],
                'data_design' => $design->data_design,
                'data_html' => $design->data_html,
            ]);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                return $insert_id;
            }

        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete email template design from database
     */
    public function delete_email_template_design($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_email_template_designs');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }


    /**
     * add email language
     * @param array $data
     */
    public function add_email_language($data){
        $this->db->where('email_id', $data['email_id']);
        $this->db->where('language', $data['language']);
        $email_design = $this->db->get(db_prefix() . 'ma_email_designs')->row();

        if($email_design){
            return false;
        }

        $this->db->insert(db_prefix() . 'ma_email_designs', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Get email
     * @param  mixed $id email id (Optional)
     * @return mixed     object or array
     */
    public function get_email_design($id)
    {
        $this->db->where('id', $id);

        $email_design = $this->db->get(db_prefix() . 'ma_email_designs')->row();

        return $email_design;
    }

    /**
     * clone email design
     * @param  array $data 
     * @return [type]       
     */
    public function clone_email_design($data){
        $design = $this->get_email_design($data['from_language']);

        $this->db->where('email_id', $data['email_id']);
        $this->db->where('language', $data['to_language']);
        $email_design = $this->db->get(db_prefix() . 'ma_email_designs')->row();

        if($email_design){
            $this->db->where('id', $email_design->id);
            $this->db->update(db_prefix() . 'ma_email_designs', [
                'data_design' => $design->data_design,
                'data_html' => $design->data_html,
            ]);

            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }else{
            $this->db->insert(db_prefix() . 'ma_email_designs', [
                'email_id' => $data['email_id'],
                'language' => $data['to_language'],
                'data_design' => $design->data_design,
                'data_html' => $design->data_html,
            ]);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                return $insert_id;
            }

        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete email design from database
     */
    public function delete_email_design($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_email_designs');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * add sms language
     * @param array $data
     */
    public function add_sms_language($data){
        $this->db->where('sms_id', $data['sms_id']);
        $this->db->where('language', $data['language']);
        $sms_design = $this->db->get(db_prefix() . 'ma_sms_designs')->row();

        if($sms_design){
            return false;
        }

        $this->db->insert(db_prefix() . 'ma_sms_designs', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Get sms
     * @param  mixed $id sms id (Optional)
     * @return mixed     object or array
     */
    public function get_sms_design($id)
    {
        $this->db->where('id', $id);

        $sms_design = $this->db->get(db_prefix() . 'ma_sms_designs')->row();

        return $sms_design;
    }

    /**
     * clone sms design
     * @param  array $data 
     * @return [type]       
     */
    public function clone_sms_design($data){
        $design = $this->get_sms_design($data['from_language']);

        $this->db->where('sms_id', $data['sms_id']);
        $this->db->where('language', $data['to_language']);
        $sms_design = $this->db->get(db_prefix() . 'ma_sms_designs')->row();

        if($sms_design){
            $this->db->where('id', $sms_design->id);
            $this->db->update(db_prefix() . 'ma_sms_designs', [
                'content' => $design->content,
            ]);

            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }else{
            $this->db->insert(db_prefix() . 'ma_sms_designs', [
                'sms_id' => $data['sms_id'],
                'language' => $data['to_language'],
                'content' => $design->content,
            ]);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                return $insert_id;
            }

        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete sms design from database
     */
    public function delete_sms_design($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_sms_designs');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function sms_design_save($data){
        if(isset($data['id']) && $data['id'] != ''){
            $this->db->where('id', $data['id']);
            $this->db->update(db_prefix() . 'ma_sms_designs', ['content' => $data['content']]);

            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * add sms_template language
     * @param array $data
     */
    public function add_sms_template_language($data){
        $this->db->where('sms_template_id', $data['sms_template_id']);
        $this->db->where('language', $data['language']);
        $sms_template_design = $this->db->get(db_prefix() . 'ma_sms_template_designs')->row();

        if($sms_template_design){
            return false;
        }

        $this->db->insert(db_prefix() . 'ma_sms_template_designs', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Get sms_template
     * @param  mixed $id sms_template id (Optional)
     * @return mixed     object or array
     */
    public function get_sms_template_design($id)
    {
        $this->db->where('id', $id);

        $sms_template_design = $this->db->get(db_prefix() . 'ma_sms_template_designs')->row();

        return $sms_template_design;
    }

    /**
     * clone sms_template design
     * @param  array $data 
     * @return [type]       
     */
    public function clone_sms_template_design($data){
        $design = $this->get_sms_template_design($data['from_language']);

        $this->db->where('sms_template_id', $data['sms_template_id']);
        $this->db->where('language', $data['to_language']);
        $sms_template_design = $this->db->get(db_prefix() . 'ma_sms_template_designs')->row();

        if($sms_template_design){
            $this->db->where('id', $sms_template_design->id);
            $this->db->update(db_prefix() . 'ma_sms_template_designs', [
                'content' => $design->content,
            ]);

            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }else{
            $this->db->insert(db_prefix() . 'ma_sms_template_designs', [
                'sms_template_id' => $data['sms_template_id'],
                'language' => $data['to_language'],
                'content' => $design->content,
            ]);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                return $insert_id;
            }

        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete sms_template design from database
     */
    public function delete_sms_template_design($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ma_sms_template_designs');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function sms_template_design_save($data){
        if(isset($data['id']) && $data['id'] != ''){
            $this->db->where('id', $data['id']);
            $this->db->update(db_prefix() . 'ma_sms_template_designs', ['content' => $data['content']]);

            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * get sms content by contact
     * @param  integer $sms_id 
     * @param  array $contact   
     * @return string       
     */
    public function get_sms_content_by_contact($sms_id, $contact){
        $language = $contact['default_language'];
        if($language == ''){
            $language = get_option('active_language');
        }

        $this->db->where('sms_id', $sms_id);
        $this->db->where('language', $language);
        $design = $this->db->get(db_prefix().'ma_sms_designs')->row();

        if ($design) {
            return html_entity_decode($design->content);
        }else{
            $this->db->where('sms_id', $sms_id);
            $design2 = $this->db->get(db_prefix().'ma_sms_designs')->row();

            if ($design2) {
                return html_entity_decode($design2->content);
            }
        }

        return '';
    }

    /**
     * get email content by contact
     * @param  integer $email_id 
     * @param  integer $lead   
     * @return string       
     */
    public function get_email_content_by_contact($email_id, $data, $email_design_id = ''){
        if($email_design_id == ''){
            $language = '';
            if(isset($data['lead'])){
                $language = $data['lead']['default_language'];
            }elseif(isset($data['client'])){
                $language = $data['client']['default_language'];
            }

            if($language == ''){
                $language = get_option('active_language');
            }

            $this->db->where('email_id', $email_id);
            $this->db->where('language', $language);
            $design = $this->db->get(db_prefix().'ma_email_designs')->row();
            
            if ($design) {
                return $design->data_html;
            }else{
                $this->db->where('email_id', $email_id);
                $design2 = $this->db->get(db_prefix().'ma_email_designs')->row();

                if ($design2) {
                    return $design2->data_html;
                }
            }
        }else{
            $this->db->where('id', $email_design_id);
            $design = $this->db->get(db_prefix().'ma_email_designs')->row();

            if ($design) {
                return $design->data_html;
            }
        }


        return '';
    }

    /**
     * get change point by lead
     * @param  object $sms_id 
     * @param  integer $lead   
     * @return string       
     */
    public function get_change_point_by_contact($point_action_id, $contact){
        $point_action = $this->get_point_action($point_action_id);

        if($contact['country'] != '' && $point_action->add_points_by_country == 1){
            $this->db->where('point_action_id', $point_action_id);
            $this->db->where('country', $contact['country']);
            $detail = $this->db->get(db_prefix().'ma_point_action_details')->row();

            if ($detail) {
                return $detail->change_points;
            }
        }

        return $point_action->change_points;
    }

    /**
     * get campaign log by lead
     * @param  integer $lead_id 
     * @return array         
     */
    public function get_campaigns_by_lead($lead_id){
        $this->db->distinct();
        $this->db->select('campaign_id');
        $this->db->where('lead_id', $lead_id);
        $campaign_logs = $this->db->get(db_prefix().'ma_campaign_flows')->result_array();
        return $campaign_logs;
    }

    /**
     * @param  integer
     * @param  string
     * @return array or string
     */
    public function get_client_by_campaign($id, $return_type = 'clients'){
        $campaign = $this->get_campaign($id);
        $where = '';

        if($campaign->workflow != ''){
            $workflow = json_decode(json_decode($campaign->workflow), true);

            foreach($workflow['drawflow']['Home']['data'] as $data){
                if($data['class'] == 'flow_start'){
                    if(isset($data['data']['data_type']) && $data['data']['data_type'] == 'customer'){

                        if(!isset($data['data']['client_data_from']) || $data['data']['client_data_from'] == 'segment'){
                            if(isset($data['data']['customer_segment'])){
                                $where = $this->get_client_by_segment($data['data']['customer_segment'], 'where');
                            }
                        }else{
                            if(isset($data['data']['customer_group'])){
                                $where = $this->get_client_by_group($data['data']['customer_group'], 'where');
                            }
                        }

                        if($where == ''){
                            $where = '1=1';
                        }

                        if(isset($data['data']['customer_sendto'])){
                            if($data['data']['customer_sendto'] == ''){
                                $where .= ' AND ' . db_prefix() . 'clients.active = 1';
                            }elseif($data['data']['customer_sendto'] == 'inactive'){
                                $where .= ' AND ' . db_prefix() . 'clients.active = 0';
                            }
                        }else{
                            $where .= ' AND ' . db_prefix() . 'clients.active = 1';
                        }
                    }
                }
            }
        }

        if($where == ''){
            $where = '1=0';
        }

        $this->db->where('campaign_id', $id);
        $client_exception = $this->db->get(db_prefix().'ma_campaign_client_exceptions')->result_array();
        $client_exception_where = '';

        foreach($client_exception as $client){
            if($client_exception_where == ''){
                $client_exception_where = $client['client_id'];
            }else{
                $client_exception_where .= ','.$client['client_id'];
            }
        }

        if($client_exception_where != ''){
            $where .= ' AND '.db_prefix().'clients.userid not in ('.$client_exception_where.')';
        }

        if($return_type == 'clients'){
            $this->db->select('*, '.db_prefix() . 'clients.userid as id');
            $this->db->where($where);
            $this->db->where('ma_unsubscribed', 0);
            $this->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'clients.country', 'left');
            $this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.userid = ' . db_prefix() . 'clients.userid AND is_primary = 1', 'left');
            $clients = $this->db->get(db_prefix().'clients')->result_array();

            return $clients;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  string
     * @return array or boolean
     */
    public function get_client_by_group($id, $return_type = 'clients'){
        $this->db->where('groupid', $id);
        $groups = $this->db->get(db_prefix().'customer_groups')->result_array();

        $where = '';

        $where_group = '';
        foreach ($groups as $key => $value) {
            if($where_group != ''){
              $where_group .= ','.$value['customer_id'];
            }else{
              $where_group .= $value['customer_id'];
            }
        }

        if($where_group != ''){
            $where .=db_prefix().'clients.userid in ('.$where_group.')';
        }
        
        if($where != ''){
          $where = '('.$where.')';
        }else{
          $where = '1=0';
        }

        if($return_type == 'clients'){
            $this->db->where($where);
            $this->db->where('active', 1);
            $this->db->where('ma_unsubscribed', 0);
            $leads = $this->db->get(db_prefix().'clients')->result_array();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function save_asset_log($data){
        
        $data['dateadded'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix().'ma_asset_logs', $data);

        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Update smtp settings
     * @param  array $data all settings
     * @return integer
     */
    public function save_smtp_setting($data){
        $affectedRows = 0;
        foreach ($data['settings'] as $name => $val) {
            if($name == 'ma_smtp_password'){
                if (!empty($val)) {
                    $val = $this->encryption->encrypt($val);
                }
            }

            if (update_option($name, $val)) {
                $affectedRows++;
            }
        }

        return $affectedRows;
    }

    /**
     * update general setting
     *
     * @param      array   $data   The data
     *
     * @return     boolean 
     */
    public function update_setting($data){
        $affectedRows = 0;
    
        foreach ($data as $key => $value) {
            $this->db->where('name', $key);
            $this->db->update(db_prefix() . 'options', [
                    'value' => $value,
                ]);
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
        }

        if ($affectedRows > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_campaign_email_total_chart($campaign_id = '', $data_filter = [])
    {
        $where = $this->get_where_report_period('date_format(dateadded, \'%Y-%m-%d\')');

        $this->db->select('COUNT(*) as count_email');
        if($where != ''){
            $this->db->where($where);
        }
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->row();
        $total = (int)$email_logs->count_email;

        $this->db->select('COUNT(*) as count_email');
        if($where != ''){
            $this->db->where($where);
        }
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->where('failed = 1');
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->row();
        $data_fail = (int)$email_logs->count_email;

        $this->db->select('COUNT(*) as count_email');
        if($where != ''){
            $this->db->where($where);
        }
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->where('delivery = 1');
        $this->db->where('failed = 0');
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->row();
        $data_delivery = (int)$email_logs->count_email;

        $this->db->select('COUNT(*) as count_email');
        if($where != ''){
            $this->db->where($where);
        }
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->where('open', 1);
        $this->db->where('failed = 0');
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->row();
        $data_open = (int)$email_logs->count_email;
        

        $this->db->select('COUNT(*) as count_email');
        if($where != ''){
            $this->db->where($where);
        }
        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }
        $this->db->where('click', 1);
        $this->db->where('failed = 0');
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $email_logs = $this->db->get(db_prefix().'ma_email_logs')->row();
        $data_click = (int)$email_logs->count_email;

        $data_return = [];
        $data_return[] = ['name' => _l('fail'), 'data' => [$total != 0 ? round(($data_fail / $total) * 100, 2) : 0], 'color' => '#d8341b'];
        $data_return[] = ['name' => _l('delivery'), 'data' => [$total != 0 ? round(($data_delivery / $total) * 100, 2) : 0], 'color' => '#008ece'];
        $data_return[] = ['name' => _l('read'), 'data' =>  [$data_delivery != 0 ? round(($data_open / $data_delivery) * 100, 2) : 0], 'color' => '#84c529'];
        $data_return[] = ['name' => _l('click'), 'data' => [$data_delivery != 0 ? round(($data_click / $data_delivery) * 100, 2) : 0], 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * [get_average_time_to_open_email description]
     * @param  string $campaign_id [description]
     * @param  string $email_id    [description]
     * @return [type]              [description]
     */
    public function get_average_time_to_open_email($campaign_id = '', $email_id = '')
    { 
        if($email_id != ''){
            $this->db->where('email_id', $email_id);
        }

        if($campaign_id != ''){
            $this->db->where('campaign_id', $campaign_id);
        }else{
            $this->db->where('campaign_id > 0');
        }

        $this->db->where('open', 1);
        $this->db->where('open_time is not null');
        $emailData = $this->db->get(db_prefix().'ma_email_logs')->result_array();

        // Tính tổng thời gian mở email
        $totalOpenTime = 0;
        $numEmails = count($emailData);

        foreach ($emailData as $email) {
            if(!$email['delivery_time'] || $email['delivery_time'] == "0000-00-00 00:00:00" || $email['open_time'] == "0000-00-00 00:00:00"){
                continue;
            }
            
            $sendTime = strtotime($email['delivery_time'] ?? 'now');
            $openTime = strtotime($email['open_time']);
            $openDuration = $openTime - $sendTime;
            $totalOpenTime += $openDuration;
        }

        // Tính thời gian mở email trung bình
        $averageOpenTime = $numEmails != 0 ? $totalOpenTime / $numEmails : 0;

        // Tính số ngày, giờ và phút
        $days = floor($averageOpenTime / (60 * 60 * 24));
        $averageOpenTime -= $days * (60 * 60 * 24);
        $hours = floor($averageOpenTime / (60 * 60));
        $averageOpenTime -= $hours * (60 * 60);
        $minutes = floor($averageOpenTime / 60);

        // Xây dựng chuỗi kết quả
        $result = "";
        if ($days > 0) {
            $result .= $days . ' ' ._l('day');
        }
        if ($hours > 0) {
            $result .= ' ' . $hours . ' ' ._l('hour');
        }
        if ($minutes > 0) {
            $result .= ' ' . $minutes . ' ' ._l('minute');
        }

        if($numEmails > 0 && $result == ''){
            $result = '1 '._l('minute');
        }

        return $result;
    }

    public function convert_email_template_v104(){
        $email_templates = $this->db->get(db_prefix() . 'ma_email_templates')->result_array();
        $count = 0;
        
        foreach($email_templates as $template){
            $this->db->where('email_template_id', $template['id']);
            $this->db->where('language', 'english');
            $data_design = $this->db->get(db_prefix() . 'ma_email_template_designs')->row();

            if(!$data_design){
                $data = [];
                $data['email_template_id'] = $template['id'];
                $data['language'] = 'english';
                $data['data_html'] = $template['data_html'];
                $data['data_design'] = $template['data_design'];

                $this->db->insert(db_prefix() . 'ma_email_template_designs', $data);

                $insert_id = $this->db->insert_id();
                if ($insert_id) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Update general settings
     * @param  array $data all settings
     * @return integer
     */
    public function save_general_setting($data){
        $affectedRows = 0;

        if(!isset($data['ma_lead_required_phone'])){
            $data['ma_lead_required_phone'] = 0;
        }

        if (update_option('ma_lead_required_phone', $data['ma_lead_required_phone'])) {
            $affectedRows++;
        }

        return $affectedRows;
    }

    public function check_email_sending_limit(){
        if(get_option('ma_email_sending_limit') == 1){
            $time = date('Y-m-d H:i:s', strtotime("now -".get_option('ma_email_interval')." ".get_option('ma_email_repeat_every')));
            
            $this->db->where('(delivery_time >= "'.$time.'" OR failed_time >= "'.$time.'")');
            $this->db->where('(lead_id != 0 OR client_id != 0)');
            $this->db->where('(delivery = 1 OR failed = 1)');
            $this->db->where('bcc_address', 1);
            $count_bcc = $this->db->count_all_results(db_prefix().'ma_email_logs');

            $this->db->where('(delivery_time >= "'.$time.'" OR failed_time >= "'.$time.'")');
            $this->db->where('(lead_id != 0 or client_id != 0)');
            $this->db->where('(delivery = 1 OR failed = 1)');
            $this->db->where('bcc_address', 0);
            $count = $this->db->count_all_results(db_prefix().'ma_email_logs');

            $total = $count + ($count_bcc * 2);
            if($total >= get_option('ma_email_limit')){
                return false;
            }
        }
        return true;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function clone_campaign($data){
        $campaign = $this->get_campaign($data['id']);
        $data_insert = (array)$campaign;

        unset($data_insert['id']);
        $data_insert['name'] = $data['name'];
        $data_insert['addedfrom'] = get_staff_user_id();
        $data_insert['dateadded'] = date('Y-m-d H:i:s');

        $this->db->insert(db_prefix().'ma_campaigns', $data_insert);

        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Send email - No templates used only simple string
     * @since Version 1.0.2
     * @param  string $email   email
     * @param  string $ma_email_object email object
     * @param  integer $log_id   email log ID
     * @return boolean
     */
    public function ma_send_email_limit($email_log)
    {   
        $email = $email_log['email'];
        $ma_email_object = $this->get_email($email_log['email_id']);

        $data = [];
        if($email_log['lead_id'] != 0){
            $this->load->model('leads_model');
            $lead = $this->leads_model->get($email_log['lead_id']);
            $data['lead'] = (array)$lead;
        }elseif($email_log['client_id'] != 0){
            $client = $this->clients_model->get($email_log['client_id']);
            $data['client'] = (array)$client;
        }

        $this->load->model('emails_model');

        $subject = $ma_email_object->subject;

        $content = $this->get_email_content_by_contact($ma_email_object->id, $data);

        $message = $this->parse_content_merge_fields(json_decode($content ?? ''), $data, $email_log['id']);

        $from_name = get_option('companyname');
        if($ma_email_object->from_name != ''){
            $from_name = $ma_email_object->from_name;
        }

        $from_email = get_option('smtp_email');
        if($ma_email_object->from_address != ''){
            $from_email = $ma_email_object->from_address;
        }

        $bcc_address = '';
        if($ma_email_object->bcc_address != ''){
            $bcc_address = $ma_email_object->bcc_address;
        }

        $reply_to = '';
        if($ma_email_object->reply_to_address != ''){
            $reply_to = $ma_email_object->reply_to_address;
        }

        $subject = $this->parse_content_merge_fields($subject, $data);

        $from_name = $this->parse_content_merge_fields($from_name, $data);

        $cnf = [
            'from_email' => $from_email,
            'from_name'  => $from_name,
            'email'      => $email,
            'subject'    => $subject,
            'message'    => $message,
            'bcc'    => $bcc_address,
            'reply_to'    => $reply_to,
        ];

        $cnf['message'] = check_for_links($cnf['message']);

        $this->load->config('email');
        if(get_option('ma_smtp_type') == 'other_smtp'){
            $this->email->useragent  = trim(get_option('ma_mail_engine'));
            $this->email->protocol  = trim(get_option('ma_email_protocol'));
            $this->email->smtp_crypto  = trim(get_option('ma_smtp_encryption'));
            $this->email->smtp_host  = trim(get_option('ma_smtp_host'));

            if (get_option('ma_smtp_username') == '') {
                $this->email->smtp_user    = trim(get_option('ma_smtp_email'));
            } else {
                $this->email->smtp_user    = trim(get_option('ma_smtp_username'));
            }

            $charset = strtoupper(get_option('ma_smtp_email_charset'));
            $charset = trim($charset);
            if ($charset == '' || strcasecmp($charset,'utf8') == 'utf8') {
                $charset = 'utf-8';
            }

            $this->email->charset  = $charset;
            $this->email->smtp_pass  = $this->encryption->decrypt(get_option('ma_smtp_password'));
            $this->email->smtp_port  = trim(get_option('ma_smtp_port'));
        }


        $this->email->clear(true);
        $this->email->set_newline(config_item('newline'));
        $this->email->from($cnf['from_email'], $cnf['from_name']);
        $this->email->to($cnf['email']);

        $bcc = '';
        // Used for action hooks
        if (isset($cnf['bcc']) && $cnf['bcc'] != '') {
            $bcc = $cnf['bcc'];
            if (is_array($bcc)) {
                $bcc = implode(', ', $bcc);
            }
        }

        $systemBCC = get_option('ma_bcc_emails');
        if ($systemBCC != '') {
            if ($bcc != '') {
                $bcc .= ', ' . $systemBCC;
            } else {
                $bcc .= $systemBCC;
            }
        }
        if ($bcc != '') {
            $this->email->bcc($bcc);
        }

        if (isset($cnf['cc'])) {
            $this->email->cc($cnf['cc']);
        }

        if (isset($cnf['reply_to']) && $cnf['reply_to'] != '') {
            $this->email->reply_to($cnf['reply_to']);
        }

        if($ma_email_object->attachment != '0' && $ma_email_object->attachment != ''){
            $this->db->where('rel_id', $ma_email_object->attachment);
            $this->db->where('rel_type', 'ma_asset');
            $file = $this->db->get(db_prefix() . 'files')->row();
            if($file){
                $path = MA_MODULE_UPLOAD_FOLDER . '/assets/' . $file->rel_id . '/' . $file->file_name;

                $asset_hash = app_generate_hash();
                $this->save_asset_log([
                        'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                        'client_id' => (isset($data['client']) ? $data['client']['userid'] : 0),
                        'asset_id' => $ma_email_object->attachment, 
                        'hash' => $asset_hash,
                        'campaign_id' => $email_log['campaign_id']
                    ]);

                $cnf['message'] .= '<hr><strong>Attachment: </strong> <a href="'.site_url('ma/ma_public/asset/'.$asset_hash).'">'.$file->file_name.'</a>';
            }
        }

        $unsubscribe = get_option('ma_unsubscribe');
        if($unsubscribe == 1){
            $unsubscribe_text = get_option('ma_unsubscribe_text');
            if($unsubscribe_text == ''){
                $unsubscribe_text = _l('unsubscribe');
            }

            $cnf['message'] .= '<br><br><a href="'.site_url('ma/ma_public/unsubscribe/'.$email_log['hash']).'" target="_blank">'.$unsubscribe_text.'</a>';
        }

        $this->email->subject($cnf['subject']);

        $this->email->message($cnf['message']);

        $this->email->set_alt_message(strip_html_tags($cnf['message'], '<br/>, <br>, <br />'));       

        $success = $this->email->send();

        if ($success) {
            log_activity('Email sent to: ' . $cnf['email'] . ' Subject: ' . $cnf['subject']);

            $this->db->where('id', $email_log['id']);
            $this->db->update(db_prefix().'ma_email_logs', ['delivery' => 1, 'delivery_time' => date('Y-m-d H:i:s'), 'bcc_address' => $bcc_address != '' ? 1 : 0]);

            return true;
        }else{
            $this->db->where('id', $email_log['id']);
            $this->db->update(db_prefix().'ma_email_logs', ['failed' => 1, 'failed_time' => date('Y-m-d H:i:s')]);
        }

        return false;
    }

    public function ma_cron_email_limit(){
        $this->db->where('delivery = 0 AND failed = 0 AND email IS NOT NULL');
        $email_logs = $this->db->get(db_prefix(). 'ma_email_logs')->result_array();
        
        foreach ($email_logs as $log) {
            if(!$this->check_email_sending_limit()){
                break;
            }
            
            $this->ma_send_email_limit($log);
        }

        return true;
    }

    public function get_campaign_email_stats(){
        $campaigns = $this->get_campaign();

        foreach ($campaigns as $campaign) {
            $campaign['email_sending_stats'] = $this->get_email_progress_by_campaign($campaign['id']);
        }

        return $campaigns;
    }

    public function get_email_progress_by_campaign($campaign_id){

        //Email is waiting to be sent
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $this->db->where('delivery = 0 AND failed = 0 AND email IS NOT NULL');
        $this->db->where('campaign_id', $campaign_id);
        $email_waiting_sent = $this->db->count_all_results(db_prefix(). 'ma_email_logs');

        //Email is waiting to be sent, have bcc
        $this->db->where('delivery = 0 AND failed = 0 AND email IS NOT NULL');
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $this->db->where('bcc_address', 1);
        $this->db->where('campaign_id', $campaign_id);
        $email_waiting_sent += $this->db->count_all_results(db_prefix(). 'ma_email_logs');

        //Email has been sent
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $this->db->where('(delivery = 1 OR failed = 1)');
        $this->db->where('campaign_id', $campaign_id);
        $email_was_sent = $this->db->count_all_results(db_prefix(). 'ma_email_logs');

        //Email has been sent, have bcc
        $this->db->where('(lead_id != 0 or client_id != 0)');
        $this->db->where('delivery = 1');
        $this->db->where('bcc_address', 1);
        $this->db->where('campaign_id', $campaign_id);
        $email_was_sent += $this->db->count_all_results(db_prefix(). 'ma_email_logs');

        $total = $email_waiting_sent + $email_was_sent;
        $percent_sent = $total != 0 ? round(($email_was_sent/$total) * 100) : 0;

        $planned_time = '';
        if($email_waiting_sent > 0){
            $ma_email_limit = get_option('ma_email_limit');
            if($ma_email_limit != 0 && $ma_email_limit != ''){
                $t = get_option('ma_email_interval') * round($email_waiting_sent / $ma_email_limit);
                $planned_time = date('Y-m-d H:i:s', strtotime("now +".$t." ".get_option('ma_email_repeat_every')));
            }
        }

        $data = [];
        $data['total'] = $total;
        $data['email_waiting_sent'] = $email_waiting_sent;
        $data['email_was_sent'] = $email_was_sent;
        $data['percent_sent'] = $percent_sent;
        $data['percent_waiting_sent'] = 100 - $percent_sent;
        $data['planned_time'] = $planned_time;

        return $data;
    }

    /**
     * Update email limit settings
     * @param  array $data all settings
     * @return integer
     */
    public function save_email_limit_setting($data){
        $affectedRows = 0;
        foreach ($data['settings'] as $name => $val) {
            if (update_option($name, $val)) {
                $affectedRows++;
            }
        }

        return $affectedRows;
    }

    public function add_test_campaign($data){
        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $insert_id = $this->db->insert(db_prefix().'ma_campaign_test', $data);

        if($insert_id){
            return $insert_id;
        }

        return false;
    }

    public function get_campaign_test($campaign_id = '')
    {
        $this->db->where('campaign_id', $campaign_id);
        $campaign_test = $this->db->get(db_prefix() . 'ma_campaign_test')->row();

        if($campaign_test){
            $this->db->where('campaign_id', $campaign_id);
            $campaign_test->data_logs = $this->db->get(db_prefix() . 'ma_campaign_test_logs')->result_array();
        }

        return $campaign_test;
    }

    /**
     * @param  integer
     * @return boolean
     */
    public function run_campaign_test($id){
        $campaign = $this->get_campaign($id);
        $workflow = json_decode(json_decode($campaign->workflow ?? ''), true);
        
        if(!isset($workflow['drawflow']['Home']['data'])){
            return false;
        }

        $workflow = $workflow['drawflow']['Home']['data'];
        $data_flow = [];

        $data = [];
        $data['campaign'] = $campaign;
        $data['workflow'] = $workflow;
        
        $campaign_test = $this->get_campaign_test($id);
        if($campaign_test){
            $data['campaign_test'] = $campaign_test;
            $data['contact']['email'] = $campaign_test->email;

            if($data['campaign_test']->delete_lead == 1 || $data['campaign_test']->remove_from_campaign == 1){
                return false;
            }

            foreach($workflow as $data_workflow){
                $data['node'] = $data_workflow;

                if($data_workflow['class'] == 'flow_start'){
                    if(!$this->check_workflow_node_test_log($data)){
                        $this->save_workflow_node_test_log($data);
                    }

                    foreach ($data_workflow['outputs']['output_1']['connections'] as $connection) {
                        $data['node'] = $workflow[$connection['node']];
                        $this->run_workflow_node_test($data);
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function check_workflow_node_test_log($data){
        $this->db->where('campaign_id', $data['campaign']->id);
        $this->db->where('node_id', $data['node']['id']);
        $logs = $this->db->get(db_prefix().'ma_campaign_test_logs')->row();

        if($logs){
            return $logs->output;
        }

        return false;
    }

    /**
     * @param  array
     * @param  string
     * @return boolean
     */
    public function save_workflow_node_test_log($data, $output = 'output_1'){
        $this->db->where('campaign_id', $data['campaign']->id);
        $this->db->where('node_id', $data['node']['id']);
        $logs = $this->db->get(db_prefix().'ma_campaign_test_logs')->row();

        if(!$logs){
            $this->db->insert(db_prefix().'ma_campaign_test_logs', [
                'campaign_id' => $data['campaign']->id, 
                'node_id' => $data['node']['id'], 
                'output' => $output, 
                'result' => isset($data['result']) ? $data['result'] : '', 
                'dateadded' => date('Y-m-d H:i:s'), 
            ]);
        }

        return true;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function run_workflow_node_test($data){
        $output = $this->check_workflow_node_test_log($data);
        if($data['campaign_test']->delete_lead == 1 || $data['campaign_test']->remove_from_campaign == 1){
            return false;
        }

        if(!$output){
            switch ($data['node']['class']) {
                case 'email':
                    $success = $this->handle_email_node($data, true);

                    if($success){
                        $data['result'] = _l('send_email_successfully');
                        $this->save_workflow_node_test_log($data);

                        foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node_test($data);
                        }
                    }

                    break;

                case 'sms':
                    //$success = $this->handle_sms_node($data);

                    //if($success){
                        $data['result'] = _l('send_sms_successfully');
                        $this->save_workflow_node_test_log($data);

                        foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node_test($data);
                        }
                    //}

                    break;

                case 'action':

                    if(!isset($data['node']['data']['action'])){
                        $data['node']['data']['action'] = 'change_segments';
                    }

                    switch ($data['node']['data']['action']) {
                        case 'change_segments':
                            if(isset($data['node']['data']['segment'])){
                                $this->db->where('id', $data['campaign_test']->id);
                                $this->db->update(db_prefix(). 'ma_campaign_test', ['segment_id' => $data['node']['data']['segment']]);

                                $segment = $this->get_segment($data['node']['data']['segment']);
                                $data['result'] = _l('change_segment_to').' <span class="text-info">'. $segment->name.'</span>';
                            }

                            break;
                        case 'change_stages':
                            if(isset($data['node']['data']['stage'])){
                                $this->db->where('id', $data['campaign_test']->id);
                                $this->db->update(db_prefix(). 'ma_campaign_test', ['stage_id' => $data['node']['data']['stage']]);

                                $stage = $this->get_stage($data['node']['data']['stage']);
                                $data['result'] = _l('change_stage_to').' <span class="text-warning">'. $stage->name.'</span>';
                            }

                            break;
                        case 'change_points':
                            if(isset($data['node']['data']['point'])){
                                $this->db->where('id', $data['campaign_test']->id);
                                $this->db->update(db_prefix(). 'ma_campaign_test', ['point' => $data['campaign_test']->point + $data['node']['data']['point']]);

                                $data['campaign_test']->point = $data['campaign_test']->point + $data['node']['data']['point'];
                                $text_class = 'text-success';
                                if($data['node']['data']['point'] < 0){
                                    $text_class = 'text-danger';
                                }

                                $data['result'] = _l('change_points').': <span class="'.$text_class.'">'. $data['node']['data']['point'].'</span>';
                            }

                            break;

                        case 'point_action':
                            if(isset($data['node']['data']['point_action'])){
                                $point_action = $this->get_point_action($data['node']['data']['point_action']);

                                $this->db->where('id', $data['campaign_test']->id);
                                $this->db->update(db_prefix(). 'ma_campaign_test', ['point' => $data['campaign_test']->point + $point_action->change_points]);

                                $data['campaign_test']->point = $data['campaign_test']->point + $point_action->change_points;

                                $text_class = 'text-success';
                                if($point_action->change_points < 0){
                                    $text_class = 'text-danger';
                                }

                                $data['result'] = _l('point_action').': '. $point_action->name.'(<span class="'.$text_class.'">'.$point_action->change_points.'</span>)';
                            }

                            break;

                        case 'delete_lead':
                            $this->db->where('id', $data['campaign_test']->id);
                            $this->db->update(db_prefix(). 'ma_campaign_test', ['delete_lead' => 1]);

                            $data['campaign_test']->delete_lead = 1;

                            $data['result'] = _l('delete_lead');
                            
                            break;

                        case 'remove_from_campaign':
                            $this->db->where('id', $data['campaign_test']->id);
                            $this->db->update(db_prefix(). 'ma_campaign_test', ['remove_from_campaign' => 1]);

                            $data['campaign_test']->remove_from_campaign = 1;

                            $data['result'] = _l('remove_from_campaign');

                            break;

                        case 'convert_to_customer':
                            $this->db->where('id', $data['campaign_test']->id);
                            $this->db->update(db_prefix(). 'ma_campaign_test', ['convert_to_customer' => 1]);

                            $data['campaign_test']->convert_to_customer = 1;

                            $data['result'] = _l('convert_to_customer');

                            break; 

                        case 'change_lead_status':
                            if(isset($data['node']['data']['lead_status'])){
                                $this->load->model('leads_model');
                                $status = $this->leads_model->get_status($data['node']['data']['lead_status']);

                                $this->db->where('id', $data['campaign_test']->id);
                                $this->db->update(db_prefix(). 'ma_campaign_test', ['status' => $data['node']['data']['lead_status']]);

                                $data['result'] = _l('change_lead_status').': <span class="text-secondary">'. $status->name.'</span>';
                            }

                            break; 

                        case 'add_tags':
                            if(isset($data['node']['data']['tags'])){
                                $this->db->where('id', $data['campaign_test']->id);
                                $this->db->update(db_prefix(). 'ma_campaign_test', ['tags' => $data['node']['data']['tags']]);

                                $tags = $data['node']['data']['tags'];
                                $data['result'] = _l('add_tags').': <span class="text-muted">'. $tags.'</span>';
                            }

                            break; 
                        
                        default:
                            // code...
                            break;
                    }

                    $this->save_workflow_node_test_log($data);

                    foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                        $data['node'] = $data['workflow'][$connection['node']];
                        $this->run_workflow_node_test($data);
                    }

                    break;

                case 'condition':
                    $success = $this->handle_condition_node_test($data);

                    if(!isset($data['node']['data']['track'])){
                        $data['node']['data']['track'] = 'delivery';
                    }

                    $text_class = 'text-primary';
                    if($data['node']['data']['track'] == 'open'){
                        $text_class = 'text-success';
                    }elseif($data['node']['data']['track'] == 'click'){
                        $text_class = 'text-warning';
                    }

                    if($success == 'output_1'){
                        $data['result'] = _l('condition').': '. _l('condition_tracking_results', '<span class="'.$text_class.'">'.$data['node']['data']['track'].'</span>'). '<span class="text-success">'._l('settings_yes').'</span>';
                        $this->save_workflow_node_test_log($data);

                        foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node_test($data);
                        }

                    }elseif($success == 'output_2'){
                        $data['result'] = _l('condition').': '. _l('condition_tracking_results', '<span class="'.$text_class.'">'.$data['node']['data']['track'].'</span>'). '<span class="text-danger">'._l('settings_no').'</span>';
                        $this->save_workflow_node_test_log($data, 'output_2');

                        foreach ($data['node']['outputs']['output_2']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node_test($data);
                        }
                    }

                    break;

                case 'filter':
                    $success = $this->handle_filter_node_test($data);
                    if(!isset($data['node']['data']['name_of_variable'])){
                        $data['node']['data']['name_of_variable'] = 'name';
                    }

                    if($success == 'output_1'){
                        $data['result'] = _l('filter_and_result', '<span class="text-primary">'.$data['node']['data']['name_of_variable'].'</span>'). '<span class="text-success">'._l('settings_yes').'</span>';
                        $this->save_workflow_node_test_log($data);

                        foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node_test($data);
                        }

                    }elseif($success == 'output_2'){
                        $data['result'] = _l('filter_and_result', '<span class="text-primary">'.$data['node']['data']['name_of_variable'].'</span>'). '<span class="text-danger">'._l('settings_no').'</span>';
                        $this->save_workflow_node_test_log($data, 'output_2');

                        foreach ($data['node']['outputs']['output_2']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node_test($data);
                        }
                    }
                    break;

                default:
                    // code...
                    break;
            }
        }else{
            foreach ($data['node']['outputs'][$output]['connections'] as $connection) {
                $data['node'] = $data['workflow'][$connection['node']];
                $this->run_workflow_node_test($data);
            }
        }

        return true;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function handle_condition_node_test($data){

        foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
            $this->db->where('campaign_id', $data['campaign']->id);
            $this->db->where('node_id', $connection['node']);
            $logs = $this->db->get(db_prefix().'ma_campaign_test_logs')->row();

            if($logs){
                if(!isset($data['node']['data']['waiting_number'])){
                    $data['node']['data']['waiting_number'] = 1;
                }

                if(!isset($data['node']['data']['waiting_type'])){
                    $data['node']['data']['waiting_type'] = 'minutes';
                }

                $time = date('Y-m-d H:i:s', strtotime($logs->dateadded." +".$data['node']['data']['waiting_number']." ".$data['node']['data']['waiting_type']));

                if(date('Y-m-d H:i:s') >= $time){

                    if(!isset($data['node']['data']['track'])){
                        $data['node']['data']['track'] = 'delivery';
                    }

                    switch ($data['node']['data']['track']) {
                        case 'delivery':
                            foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                                $node = $data['workflow'][$connection['node']];
                                if($node['class'] == 'email'){
                                    if(isset($node['data']['email'])){
                                        if($this->check_condition_email_test($data, $node['data']['email'], 'delivery')){
                                            return 'output_1';
                                        }else{
                                            return 'output_2';
                                        }
                                    }
                                }
                            }
                            break;

                        case 'opens':
                            foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                                $node = $data['workflow'][$connection['node']];
                                if($node['class'] == 'email'){
                                    if(isset($node['data']['email'])){
                                        if($this->check_condition_email_test($data, $node['data']['email'], 'open')){
                                            return 'output_1';
                                        }else{
                                            return 'output_2';
                                        }
                                    }
                                }
                            }
                            break;

                        case 'clicks':
                            foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                                $node = $data['workflow'][$connection['node']];
                                if($node['class'] == 'email'){
                                    if(isset($node['data']['email'])){
                                        if($this->check_condition_email_test($data, $node['data']['email'], 'click')){
                                            return 'output_1';
                                        }else{
                                            return 'output_2';
                                        }
                                    }
                                }
                            }
                            break;

                        case 'confirm':
                            foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                                $node = $data['workflow'][$connection['node']];
                                if($node['class'] == 'email'){
                                    if(isset($node['data']['email'])){
                                        if($this->check_condition_email_test($data, $node['data']['email'], 'confirm')){
                                            return 'output_1';
                                        }else{
                                            return 'output_2';
                                        }
                                    }
                                }
                            }
                            break;
                        
                        default:
                            
                            break;
                    }
                }
            }
        }

        return false;
    }

    public function check_condition_email_test($data, $email_id, $type){
        $this->db->where('campaign_id', $data['campaign']->id);
        $this->db->where('email_id', $email_id);
        $this->db->where('lead_id', 0);
        $this->db->where('client_id', 0);
        $this->db->where($type, 1);
        $check = $this->db->get(db_prefix().'ma_email_logs')->row();
        if($check){
            return true;
        }

        return false;
    }

    public function handle_filter_node_test($data){
        if(!isset($data['node']['data']['complete_action'])){
            $data['node']['data']['complete_action'] = 'right_away';
        }

        switch ($data['node']['data']['complete_action']) {
            case 'right_away':
                if($this->check_filter_test($data, $data['node'])){
                    return 'output_1';
                }else{
                    return 'output_2';
                }

                break;
            case 'after':
                if(!isset($data['node']['data']['waiting_number'])){
                    $data['node']['data']['waiting_number'] = 1;
                }
                
                if(!isset($data['node']['data']['waiting_type'])){
                    $data['node']['data']['waiting_type'] = 'minutes';
                }

                foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                    $this->db->where('campaign_id', $data['campaign']->id);
                    $this->db->where('node_id', $connection['node']);
                    $logs = $this->db->get(db_prefix().'ma_campaign_test_logs')->row();

                    if($logs){
                        $time = date('Y-m-d H:i:s', strtotime($logs->dateadded." +".$data['node']['data']['waiting_number']." ".$data['node']['data']['waiting_type']));

                        if(date('Y-m-d H:i:s') >= $time){
                            if($this->check_filter_test($data, $data['node'])){
                                return 'output_1';
                            }else{
                                return 'output_2';
                            }
                        }
                    }
                }
            
                break;
            default:
                // code...
                break;
        }

        return false;
    }

    public function check_filter_test($data, $node){
        $contact = (array)$data['campaign_test'];
        if(!isset($node['data']['name_of_variable'])){
            $node['data']['name_of_variable'] = 'name';
        }
        
        if(!isset($node['data']['condition'])){
            $node['data']['condition'] = 'equals';
        }

        if(!isset($node['data']['value_of_variable'])){
            $node['data']['value_of_variable'] = '';
        }

        if($node['data']['name_of_variable'] == 'tag'){
            return false;
        }

        switch ($node['data']['name_of_variable']) {
            case 'status':
                $name_of_variable = 'status_name';
                break;
            case 'country':
                $name_of_variable = 'country_name';
                break;
            case 'source':
                $name_of_variable = 'source_name';
                break;
            default:
                $name_of_variable = $node['data']['name_of_variable'];
                break;
        }

        if(!isset($contact[$name_of_variable])){
            return false;
        }

        switch ($node['data']['condition']) {
            case 'equals':
                if($node['data']['value_of_variable'] == $contact[$name_of_variable]){
                    return true;
                }
                break;
            case 'not_equal':
                if($node['data']['value_of_variable'] != $contact[$name_of_variable]){
                    return true;
                }
                break;
            case 'greater_than':
                if($node['data']['value_of_variable'] = $contact[$name_of_variable]){
                    return true;
                }
                break;
            case 'greater_than_or_equal':
                if($node['data']['value_of_variable'] <= $contact[$name_of_variable]){
                    return true;
                }
                break;
            case 'less_than':
                if($node['data']['value_of_variable'] > $contact[$name_of_variable]){
                    return true;
                }
                break;
            case 'less_than_or_equal':
                if($node['data']['value_of_variable'] <= $contact[$name_of_variable]){
                    return true;
                }
                break;
            case 'empty':
                if($contact[$name_of_variable] == ''){
                    return true;
                }
                break;
            case 'not_empty':
                if($contact[$name_of_variable] != ''){
                    return true;
                }
                break;
            case 'like':
                if (!(strpos(strtolower($contact[$name_of_variable]), strtolower($node['data']['value_of_variable'])) === false)) {
                    return true;
                }
                break;
            case 'not_like':
                if (!(strpos(strtolower($contact[$name_of_variable]), strtolower($node['data']['value_of_variable'])) !== false)) {
                    return true;
                }
                break;
            default:
                break;

        }

        return false;
    }

    public function delete_test_campaign($id)
    {
        $this->db->where('campaign_id', $id);
        $this->db->delete(db_prefix() . 'ma_campaign_test');
        if ($this->db->affected_rows() > 0) {

            $this->db->where('campaign_id', $id);
            $this->db->delete(db_prefix() . 'ma_campaign_test_logs');

            $this->db->where('campaign_id', $id);
            $this->db->where('lead_id', 0);
            $this->db->where('client_id', 0);
            $this->db->delete(db_prefix() . 'ma_email_logs');

            return true;
        }

        return false;
    }

    public function refresh_test_campaign($id)
    {
        $this->db->where('campaign_id', $id);
        $this->db->update(db_prefix() . 'ma_campaign_test', ['segment_id' => 0, 'stage_id' => 0, 'point' => 0, 'delete_lead' => 0, 'remove_from_campaign' => 0, 'convert_to_customer' => 0, 'tags' => '', 'status' => '']);

        $this->db->where('campaign_id', $id);
        $this->db->delete(db_prefix() . 'ma_campaign_test_logs');

        $this->db->where('campaign_id', $id);
        $this->db->where('lead_id', 0);
        $this->db->where('client_id', 0);
        $this->db->delete(db_prefix() . 'ma_email_logs');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function ma_cron_test_campaign(){
        $campaign_test = $this->db->get(db_prefix() . 'ma_campaign_test')->result_array();

        foreach($campaign_test as $campaign){
            $this->run_campaign_test($campaign['campaign_id']);
        }

        return true;
    }

    /**
     * @param  integer ID
     * @param  string
     * @return array or boolean
     */
    public function get_client_by_segment($id, $return_type = 'clients'){
        $where = '';

        $segment = $this->get_segment($id);
               
        if($segment){
            if($segment->filter_type != 'customer'){
                if($return_type == 'clients'){
                    return [];
                }elseif($return_type == 'where'){
                    return '1=0';
                }
            }

            foreach ($segment->filters as $filter) {
                if($where != ''){
                    $where .= ' '. strtoupper($filter['sub_type_1']).' ';
                }

                $type = $filter['customer_type'];

                switch ($filter['customer_type']) {
                    case 'groups':
                        $type = db_prefix().'customers_groups.name';

                        $where_group = '';
                        switch ($filter['sub_type_2']) {
                            case 'equals':
                                $where_group .= $type.' = "'.$filter['value'].'"';
                                break;
                            case 'not_equal':
                                $where_group .= $type.' != "'.$filter['value'].'"';
                                break;
                            case 'greater_than':
                                $where_group .= $type.' > "'.$filter['value'].'"';
                                break;
                            case 'greater_than_or_equal':
                                $where_group .= $type.' >= "'.$filter['value'].'"';
                                break;
                            case 'less_than':
                                $where_group .= $type.' < "'.$filter['value'].'"';
                                break;
                            case 'less_than_or_equal':
                                $where_group .= $type.' <= "'.$filter['value'].'"';
                                break;
                            case 'empty':
                                $where_group .= $type.' = ""';
                                break;
                            case 'not_empty':
                                $where_group .= $type.' != ""';
                                break;
                            case 'like':
                                $where_group .= $type.' LIKE "%'.trim($filter['value']).'%"';
                                break;
                            case 'not_like':
                                $where_group .= $type.' NOT LIKE "%'.trim($filter['value']).'%"';
                                break;
                            default:
                                break;
                        }

                        $where .= '('.db_prefix().'clients.userid in (SELECT '.db_prefix().'clients.userid as id FROM '.db_prefix().'clients 
                        LEFT JOIN '.db_prefix().'customer_groups ON ' . db_prefix() . 'customer_groups.customer_id = ' . db_prefix() . 'clients.userid
                        LEFT JOIN '.db_prefix().'customers_groups ON ' . db_prefix() . 'customers_groups.id = ' . db_prefix() . 'customer_groups.groupid
                        WHERE ma_unsubscribed = 0 AND '.$where_group.'))';

                        break;

                    case 'currency':
                        $this->load->model('currencies_model');
                        $currency_name = $this->currencies_model->get_base_currency()->name;
                        $type = 'IF('.db_prefix().'currencies.name is not null AND '.db_prefix().'currencies.name != "", '.db_prefix().'currencies.name , "'.$currency_name.'")';
                        $where_currency = '';
                        switch ($filter['sub_type_2']) {
                            case 'equals':
                                $where_currency .= $type.' = "'.$filter['value'].'"';
                                break;
                            case 'not_equal':
                                $where_currency .= $type.' != "'.$filter['value'].'"';
                                break;
                            case 'greater_than':
                                $where_currency .= $type.' > "'.$filter['value'].'"';
                                break;
                            case 'greater_than_or_equal':
                                $where_currency .= $type.' >= "'.$filter['value'].'"';
                                break;
                            case 'less_than':
                                $where_currency .= $type.' < "'.$filter['value'].'"';
                                break;
                            case 'less_than_or_equal':
                                $where_currency .= $type.' <= "'.$filter['value'].'"';
                                break;
                            case 'empty':
                                $where_currency .= $type.' = ""';
                                break;
                            case 'not_empty':
                                $where_currency .= $type.' != ""';
                                break;
                            case 'like':
                                $where_currency .= $type.' LIKE "%'.trim($filter['value']).'%"';
                                break;
                            case 'not_like':
                                $where_currency .= $type.' NOT LIKE "%'.trim($filter['value']).'%"';
                                break;
                            default:
                                break;
                        }

                        $where .= '('.db_prefix().'clients.userid in (SELECT '.db_prefix().'clients.userid FROM '.db_prefix().'clients 
                        LEFT JOIN '.db_prefix().'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'clients.default_currency
                        WHERE ma_unsubscribed = 0 AND '.$where_currency.'))';

                        break;

                    case 'country':
                        $type = db_prefix().'countries.short_name';

                        $where_country = '';
                        switch ($filter['sub_type_2']) {
                            case 'equals':
                                $where_country .= $type.' = "'.$filter['value'].'"';
                                break;
                            case 'not_equal':
                                $where_country .= $type.' != "'.$filter['value'].'"';
                                break;
                            case 'greater_than':
                                $where_country .= $type.' > "'.$filter['value'].'"';
                                break;
                            case 'greater_than_or_equal':
                                $where_country .= $type.' >= "'.$filter['value'].'"';
                                break;
                            case 'less_than':
                                $where_country .= $type.' < "'.$filter['value'].'"';
                                break;
                            case 'less_than_or_equal':
                                $where_country .= $type.' <= "'.$filter['value'].'"';
                                break;
                            case 'empty':
                                $where_country .= $type.' = ""';
                                break;
                            case 'not_empty':
                                $where_country .= $type.' != ""';
                                break;
                            case 'like':
                                $where_country .= $type.' LIKE "%'.trim($filter['value']).'%"';
                                break;
                            case 'not_like':
                                $where_country .= $type.' NOT LIKE "%'.trim($filter['value']).'%"';
                                break;
                            default:
                                break;
                        }

                        $where .= '('.db_prefix().'clients.userid in (SELECT '.db_prefix().'clients.userid as id FROM '.db_prefix().'clients 
                        LEFT JOIN '.db_prefix().'countries ON ' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'clients.country 
                        WHERE ma_unsubscribed = 0 AND '.$where_country.' 
                        GROUP BY '.db_prefix().'clients.userid))';

                        break;

                    default:

                        if(!is_numeric($filter['customer_type'])){
                            $type = db_prefix().'clients.'.$filter['customer_type'];

                            switch ($filter['sub_type_2']) {
                                case 'equals':
                                    $where .= $type.' = "'.$filter['value'].'"';
                                    break;
                                case 'not_equal':
                                    $where .= $type.' != "'.$filter['value'].'"';
                                    break;
                                case 'greater_than':
                                    $where .= $type.' > "'.$filter['value'].'"';
                                    break;
                                case 'greater_than_or_equal':
                                    $where .= $type.' >= "'.$filter['value'].'"';
                                    break;
                                case 'less_than':
                                    $where .= $type.' < "'.$filter['value'].'"';
                                    break;
                                case 'less_than_or_equal':
                                    $where .= $type.' <= "'.$filter['value'].'"';
                                    break;
                                case 'empty':
                                    $where .= $type.' = ""';
                                    break;
                                case 'not_empty':
                                    $where .= $type.' != ""';
                                    break;
                                case 'like':
                                    $where .= $type.' LIKE "%'.trim($filter['value']).'%"';
                                    break;
                                case 'not_like':
                                    $where .= $type.' NOT LIKE "%'.trim($filter['value']).'%"';
                                    break;
                                default:
                                    break;
                            }
                        }else{

                            $type = '(SELECT count(0) FROM '.db_prefix().'customfieldsvalues WHERE relid = '.db_prefix().'clients.userid AND fieldid = '.$filter['customer_type'].' AND fieldto = "customers" ';

                            switch ($filter['sub_type_2']) {
                                case 'equals':
                                    $type .= ' AND value = "'.$filter['value'].'"';
                                    break;
                                case 'not_equal':
                                    $type .= ' AND value != "'.$filter['value'].'"';
                                    break;
                                case 'greater_than':
                                    $type .= ' AND value > "'.$filter['value'].'"';
                                    break;
                                case 'greater_than_or_equal':
                                    $type .= ' AND value >= "'.$filter['value'].'"';
                                    break;
                                case 'less_than':
                                    $type .= ' AND value < "'.$filter['value'].'"';
                                    break;
                                case 'less_than_or_equal':
                                    $type .= ' AND value <= "'.$filter['value'].'"';
                                    break;
                                case 'empty':
                                    $type .= ' AND value = ""';
                                    break;
                                case 'not_empty':
                                    $type .= ' AND value != ""';
                                    break;
                                case 'like':
                                    $type .= ' AND value LIKE "%'.trim($filter['value']).'%"';
                                    break;
                                case 'not_like':
                                    $type .= ' AND value NOT LIKE "%'.trim($filter['value']).'%"';
                                    break;
                                default:
                                    break;
                            }

                            $type .= ') > 0';

                            $where .= $type;
                        }
                        
                        break;
                }
            }
        }

        $where_lead_segment = 'SELECT client_id FROM '.db_prefix().'ma_lead_segments WHERE deleted = 0 AND segment_id = '. $id;

        if($where != ''){
          $where = '('.$where.' OR '.db_prefix().'clients.userid in ('.$where_lead_segment.'))';
        }else{
          $where = '('.db_prefix().'clients.userid in ('.$where_lead_segment.'))';
        }

        if($return_type == 'clients'){
            $this->db->select('*, '.db_prefix().'clients.userid as id');
            $this->db->where($where);
            $this->db->where('ma_unsubscribed', 0);
            $clients = $this->db->get(db_prefix().'clients')->result_array();

            return $clients;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }
}