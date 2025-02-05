<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Fleet model
 */
class Fleet_model extends App_Model {
	public function __construct() {
		parent::__construct();
	}

	/**
     * add new vehicle group
     * @param array $data
     * @return integer
     */
    public function add_vehicle_group($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'fleet_vehicle_groups', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * update vehicle group
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_vehicle_group($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_vehicle_groups', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * delete vehicle group
     * @param integer $id
     * @return boolean
     */

    public function delete_vehicle_group($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_vehicle_groups');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get vehicle groups
     * @param  integer $id    member group id
     * @param  array  $where
     * @return object
     */
    public function get_data_vehicle_groups($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'fleet_vehicle_groups')->row();
        }

        $this->db->where($where);
        $this->db->order_by('name', 'asc');
        $vehicle_groups = $this->db->get(db_prefix() . 'fleet_vehicle_groups')->result_array();

        return $vehicle_groups;
    }

    /**
     * add new vehicle type
     * @param array $data
     * @return integer
     */
    public function add_vehicle_type($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'fleet_vehicle_types', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * update vehicle type
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_vehicle_type($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_vehicle_types', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * delete vehicle type
     * @param integer $id
     * @return boolean
     */

    public function delete_vehicle_type($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_vehicle_types');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get vehicle types
     * @param  integer $id    member type id
     * @param  array  $where
     * @return object
     */
    public function get_data_vehicle_types($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'fleet_vehicle_types')->row();
        }

        $this->db->where($where);
        $this->db->order_by('name', 'asc');
        $vehicle_types = $this->db->get(db_prefix() . 'fleet_vehicle_types')->result_array();

        return $vehicle_types;
    }

    /**
     * add new vehicle
     * @param array $data
     * @return integer
     */
    public function add_vehicle($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'fleet_vehicles', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * get driver
     * @param  integer $id    member type id
     * @param  array  $where
     * @return object
     */
    public function get_driver($id = '', $where = [])
    {

        $role_id = $this->get_fleet_driver_role_id();

        $this->db->where('role', $role_id);

        if (is_numeric($id)) {
            $this->db->where('staffid', $id);
            return $this->db->get(db_prefix() . 'staff')->row();
        }

        $this->db->where($where);
        $vehicle_types = $this->db->get(db_prefix() . 'staff')->result_array();

        return $vehicle_types;
    }

    /**
     * add new driver
     * @param array $data
     * @return integer
     */
    public function add_driver($id)
    {
        $role_id = $this->get_fleet_driver_role_id();
        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'staff', ['role' => $role_id]);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
    
    /**
     * delete driver
     * @param array $data
     * @return integer
     */
    public function delete_driver($id)
    {
        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'staff', ['role' => 0]);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * delete vehicle
     * @param integer $id
     * @return boolean
     */

    public function delete_vehicle($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_vehicles');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get vehicle
     * @param  integer $id    member type id
     * @param  array  $where
     * @return object
     */
    public function get_vehicle($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'fleet_vehicles')->row();
        }

        $this->db->where($where);
        $this->db->order_by('name', 'asc');
        $vehicles = $this->db->get(db_prefix() . 'fleet_vehicles')->result_array();

        return $vehicles;
    }

    /**
     * update vehicle
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_vehicle($data, $id)
    {
        $vehicle = $this->get_vehicle($id);

        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_vehicles', $data);

        if ($this->db->affected_rows() > 0) {

            if(isset($data['vehicle_group_id']) && $vehicle->vehicle_group_id != $data['vehicle_group_id']){
                $this->db->insert(db_prefix() . 'fleet_vehicle_histories', [
                    'vehicle_id' => $id,
                    'type' => 'group_change',
                    'from_value' => fleet_get_vehicle_group_name_by_id($vehicle->vehicle_group_id),
                    'to_value' => fleet_get_vehicle_group_name_by_id($data['vehicle_group_id']),
                    'datecreated' => date('Y-m-d H:i:s'),
                    'addedfrom' => get_staff_user_id(),
                ]);
            }

            if(isset($data['status']) && $vehicle->status != $data['status']){
                $this->db->insert(db_prefix() . 'fleet_vehicle_histories', [
                    'vehicle_id' => $id,
                    'type' => 'status_change',
                    'from_value' => _l($vehicle->status),
                    'to_value' => _l($data['status']),
                    'datecreated' => date('Y-m-d H:i:s'),
                    'addedfrom' => get_staff_user_id(),
                ]);
            }

            return true;
        }

        return false;
    }

    /**
     * add new driver document
     * @param array $data
     * @return integer
     */
    public function add_driver_document($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'fleet_driver_documents', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Gets the driver document.
     *
     * @param        $id     The identifier
     */
    public function get_driver_document($id = '', $where = []){
        $this->db->from(db_prefix() . 'fleet_driver_documents');
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'fleet_driver_documents.id', $id);
            $driver_document = $this->db->get()->row();
            if ($driver_document) {
                $this->db->where('rel_id', $id);
                $this->db->where('rel_type', 'fle_driver_document');
                $driver_document->files = $this->db->get(db_prefix() . 'files')->result_array();

                $driver_document->vehicle = $this->get_vehicle($driver_document->vehicle_id);
            }

            return $driver_document;
        }

        return $this->db->get()->result_array();
    }

    /**
     * update driver document
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_driver_document($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_driver_documents', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * add new vehicle assignment
     * @param array $data
     * @return integer
     */
    public function add_vehicle_assignment($data)
    {
        $data['starting_odometer'] = str_replace(',', '', $data['starting_odometer']);
        $data['ending_odometer'] = str_replace(',', '', $data['ending_odometer']);

        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'fleet_vehicle_assignments', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * update vehicle assignments
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_vehicle_assignment($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['starting_odometer'] = str_replace(',', '', $data['starting_odometer']);
        $data['ending_odometer'] = str_replace(',', '', $data['ending_odometer']);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_vehicle_assignments', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Gets the vendor.
     *
     * @param      string        $id     The identifier
     * @param      array|string  $where  The where
     *
     * @return     <type>        The vendor or list vendors.
     */
    public function get_vendor($id = '', $where = [])
    {
        $this->db->select(implode(',', prefixed_table_fields_array(db_prefix() . 'pur_vendor')) );

        $this->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'pur_vendor.country', 'left');


        if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $this->db->where($where);
        }

        if (is_numeric($id)) {

            $this->db->where(db_prefix().'pur_vendor.userid', $id);
            $vendor = $this->db->get(db_prefix() . 'pur_vendor')->row();

            if ($vendor && get_option('company_requires_vat_number_field') == 0) {
                $vendor->vat = null;
            }


            return $vendor;

        }

        $this->db->order_by('company', 'asc');

        return $this->db->get(db_prefix() . 'pur_vendor')->result_array();
    }

    /**
     * add maintenances
     * @param array $data 
     */
    public function add_maintenances($data){
        if(isset($data['parts'])){
            $data['parts'] = implode(',',$data['parts']);
        }

        if(isset($data['start_date'])){
            $data['start_date'] = to_sql_date($data['start_date']);
        }
        if(isset($data['completion_date'])){
            $data['completion_date'] = to_sql_date($data['completion_date']);
        }
        $data['cost'] = str_replace(',', '', $data['cost']);

        $this->db->insert(db_prefix().'fleet_maintenances', $data);
        $insert_id = $this->db->insert_id();
        if($insert_id){

            return $insert_id;
        }
        return 0;
    }
    /**
     * update maintenances
     * @param array $data 
     */
    public function update_maintenances($data, $id){
        if(isset($data['parts'])){
            $data['parts'] = implode(',',$data['parts']);
        }

        if(isset($data['start_date'])){
            $data['start_date'] = to_sql_date($data['start_date']);
        }
        if(isset($data['completion_date'])){
            $data['completion_date'] = to_sql_date($data['completion_date']);
        }
        
        $data['cost'] = str_replace(',', '', $data['cost']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'fleet_maintenances', $data);
        if($this->db->affected_rows() > 0) {

            return true;
        }
        return false;
    }


    /**
     * delete maintenances
     * @param  integer $id 
     * @return boolean     
     */
    public function delete_maintenances($id){
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'fleet_maintenances');
        if($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get maintenances
     * @param  integer $id 
     * @return integer     
     */
    public function get_maintenances($id = ''){
        if($id != ''){
            $this->db->where('id', $id);
            $maintenance = $this->db->get(db_prefix().'fleet_maintenances')->row();

            if($maintenance){
                $maintenance->vehicle = $this->get_vehicle($maintenance->vehicle_id);
                $maintenance->garage = $this->get_garages($maintenance->garage_id);
            }
            return $maintenance;
        }
        else{
            return $this->db->get(db_prefix().'fleet_maintenances')->result_array();
        }
    }

    /**
     * add garages
     * @param array $data 
     */
    public function add_garages($data){
        
        $this->db->insert(db_prefix().'fleet_garages', $data);
        $insert_id = $this->db->insert_id();
        if($insert_id){

            return $insert_id;
        }
        return 0;
    }
    /**
     * update garages
     * @param array $data 
     */
    public function update_garages($data){

        $this->db->where('id', $data['id']);
        $this->db->update(db_prefix().'fleet_garages', $data);
        if($this->db->affected_rows() > 0) {

            return true;
        }
        return false;
    }


    /**
     * delete garages
     * @param  integer $id 
     * @return boolean     
     */
    public function delete_garages($id){
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'fleet_garages');
        if($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get garages
     * @param  integer $id 
     * @return integer     
     */
    public function get_garages($id = ''){
        if($id != ''){
            $this->db->where('id', $id);
            return $this->db->get(db_prefix().'fleet_garages')->row();
        }
        else{
            return $this->db->get(db_prefix().'fleet_garages')->result_array();
        }
    }

    /**
     * add fuel_history
     * @param array $data 
     */
    public function add_fuel_history($data){
        $data['price'] = str_replace(',', '', $data['price']);
        
        $this->db->insert(db_prefix().'fleet_fuel_history', $data);
        $insert_id = $this->db->insert_id();
        if($insert_id){

            return $insert_id;
        }
        return 0;
    }
    /**
     * update fuel_history
     * @param array $data 
     */
    public function update_fuel_history($data, $id){
        
        $data['price'] = str_replace(',', '', $data['price']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'fleet_fuel_history', $data);
        if($this->db->affected_rows() > 0) {

            return true;
        }
        return false;
    }


    /**
     * delete fuel_history
     * @param  integer $id 
     * @return boolean     
     */
    public function delete_fuel_history($id){
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'fleet_fuel_history');
        if($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get fuel_history
     * @param  integer $id 
     * @return integer     
     */
    public function get_fuel_history($id = '', $where = [])
    {

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'fleet_fuel_history')->row();
        }

        $this->db->where($where);
        $fuel_history = $this->db->get(db_prefix() . 'fleet_fuel_history')->result_array();

        return $fuel_history;
    }

    /**
     * add position training
     * @param [type] $data 
     */
    public function add_inspection_form($data)
    {
        if (isset($data['recurring'])) {
            if ($data['recurring'] == 'custom') {
                $data['recurring_type']   = $data['repeat_type_custom'];
                $data['custom_recurring'] = 1;
                $data['recurring']        = $data['repeat_every_custom'];
            } else {
                $data['recurring_type']   = null;
                $data['custom_recurring'] = 0;
            }
        } else {
            $data['custom_recurring'] = 0;
            $data['recurring']        = 0;
            $data['recurring_type']   = null;
        }

        $this->db->insert(db_prefix().'fleet_inspection_forms', [
            'name'         => $data['name'],
            'recurring'         => $data['recurring'],
            'recurring_type'         => $data['recurring_type'],
            'custom_recurring'         => $data['custom_recurring'],
            'cycles'         => $data['cycles'] ?? 0,
            'total_cycles'         => $data['total_cycles'] ?? 0,
            'color'         => $data['color'],
            'slug'            => slug_it($data['name']),
            'description'     => $data['description'],
            'datecreated'     => date('Y-m-d H:i:s'),
            'addedfrom'     => get_staff_user_id(),
            'hash'            => app_generate_hash(),
        ]);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }


    /**
     * update position training
     * @param  [type] $data        
     * @param  [type] $id 
     * @return [type]              
     */
    public function update_inspection_form($data, $id)
    {
        if (isset($data['recurring'])) {
            if ($data['recurring'] == 'custom') {
                $data['recurring_type']   = $data['repeat_type_custom'];
                $data['custom_recurring'] = 1;
                $data['recurring']        = $data['repeat_every_custom'];
            } else {
                $data['recurring_type']   = null;
                $data['custom_recurring'] = 0;
            }
        } else {
            $data['custom_recurring'] = 0;
            $data['recurring']        = 0;
            $data['recurring_type']   = null;
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix().'fleet_inspection_forms', [
            'name'         => $data['name'],
            'recurring'         => $data['recurring'],
            'recurring_type'         => $data['recurring_type'],
            'custom_recurring'         => $data['custom_recurring'],
            'cycles'         => $data['cycles'] ?? 0,
            'total_cycles'         => $data['total_cycles'] ?? 0,
            'slug'            => slug_it($data['name']),
            'description'     => $data['description'],
            'color'         => $data['color'],
        ]);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * get position training
     * @param  integer $id 
     * @return array     
     */
    public function get_inspection_form($id = '')
    {
        $this->db->where('id', $id);
        $inspection_form = $this->db->get(db_prefix().'fleet_inspection_forms')->row();
        if (!$inspection_form) {
            return false;
        }
        $this->db->where('rel_id', $inspection_form->id);
        $this->db->where('rel_type', 'inspection_form');
        $this->db->order_by('question_order', 'asc');
        $questions = $this->db->get(db_prefix().'fleet_inspection_question_forms')->result_array();
        $i         = 0;
        foreach ($questions as $question) {
            $this->db->where('questionid', $question['questionid']);
            $box                      = $this->db->get(db_prefix().'fleet_inspection_question_box')->row();
            $questions[$i]['boxid']   = $box->boxid;
            $questions[$i]['boxtype'] = $box->boxtype;
            if ($box->boxtype == 'checkbox' || $box->boxtype == 'radio' || $box->boxtype == 'pass_fail') {
                $this->db->order_by('questionboxdescriptionid', 'asc');
                $this->db->where('boxid', $box->boxid);
                $boxes_description = $this->db->get(db_prefix().'fleet_inspection_question_box_description')->result_array();
                if (count($boxes_description) > 0) {
                    $questions[$i]['box_descriptions'] = [];
                    foreach ($boxes_description as $box_description) {
                        $questions[$i]['box_descriptions'][] = $box_description;
                    }
                }
            }
            $i++;
        }
        $inspection_form->questions = $questions;

        return $inspection_form;
    }

    /**
     * add training question
     * @param [type] $data 
     */
    public function add_inspection_question_form($data)
    {
        $questionid = $this->insert_inspection_question($data['training_id']);
        if ($questionid) {
            $boxid    = $this->insert_question_type($data['type'], $questionid);
            $response = [
                'questionid' => $questionid,
                'boxid'      => $boxid,
            ];
            if ($data['type'] == 'checkbox' or $data['type'] == 'radio') {
                $questionboxdescriptionid = $this->add_box_description($questionid, $boxid);
                array_push($response, [
                    'questionboxdescriptionid' => $questionboxdescriptionid,
                ]);
            }elseif($data['type'] == 'pass_fail'){
                $questionboxdescriptionid = $this->add_box_description($questionid, $boxid, _l('pass'), 0);
                $questionboxdescriptionid2 = $this->add_box_description($questionid, $boxid, _l('fail'), 1);
                array_push($response, [
                    'questionboxdescriptionid' => $questionboxdescriptionid,
                    'questionboxdescriptionid2' => $questionboxdescriptionid2,
                ]);
               
            }

            return $response;
        }

        return false;
    }


    /**
     * insert training question
     * @param  [type] $training_id 
     * @param  string $question    
     * @return [type]              
     */
    private function insert_inspection_question($training_id, $question = '')
    {
        $this->db->insert(db_prefix().'fleet_inspection_question_forms', [
            'rel_id'   => $training_id,
            'rel_type' => 'inspection_form',
            'question' => $question,
        ]);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Training Question Added [TrainingID: ' . $training_id . ']');
        }

        return $insert_id;
    }


    /**
     * Add new question type
     * @param  string $type       checkbox/textarea/radio/input
     * @param  mixed $questionid question id
     * @return mixed
     */
    private function insert_question_type($type, $questionid)
    {
        $this->db->insert(db_prefix().'fleet_inspection_question_box', [
            'boxtype'    => $type,
            'questionid' => $questionid,
        ]);

        return $this->db->insert_id();
    }


    /**
     * update question
     * @param  array $data 
     * @return boolean        
     */
    public function update_inspection_question_form($data)
    {
        $_required = 1;
        if ($data['question']['required'] == 'false') {
            $_required = 0;
        }
        $affectedRows = 0;
        $this->db->where('questionid', $data['questionid']);
        $this->db->update(db_prefix().'fleet_inspection_question_forms', [
            'question' => $data['question']['value'],
            'required' => $_required,
        ]);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        if (isset($data['boxes_description'])) {
            foreach ($data['boxes_description'] as $box_description) {
                $this->db->where('questionboxdescriptionid', $box_description[0]);
                $this->db->update(db_prefix().'fleet_inspection_question_box_description', [
                    'description' => $box_description[1],
                ]);
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
            }
        }
        if ($affectedRows > 0) {
            log_activity('Training Question Updated [QuestionID: ' . $data['questionid'] . ']');

            return true;
        }

        return false;
    }


    /**
     * update inspection questions orders
     * @param  array $data 
     */
    public function update_inspection_questions_orders($data)
    {
        foreach ($data['data'] as $question) {
            $this->db->where('questionid', $question[0]);
            $this->db->update(db_prefix().'fleet_inspection_question_forms', [
                'question_order' => $question[1],
            ]);
        }
    }


    /**
     * remove question
     * @param  integer $questionid 
     * @return boolean             
     */
    public function remove_question($questionid)
    {
        $affectedRows = 0;
        $this->db->where('questionid', $questionid);
        $this->db->delete(db_prefix().'fleet_inspection_question_box_description');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        $this->db->where('questionid', $questionid);
        $this->db->delete(db_prefix().'fleet_inspection_question_box');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        $this->db->where('questionid', $questionid);
        $this->db->delete(db_prefix().'fleet_inspection_question_forms');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        if ($affectedRows > 0) {
            log_activity('Training Question Deleted [' . $questionid . ']');

            return true;
        }

        return false;
    }


    /**
     * remove box description
     * @param  integer $questionbod 
     * @return boolean                           
     */
    public function remove_box_description($questionboxdescriptionid)
    {
        $this->db->where('questionboxdescriptionid', $questionboxdescriptionid);
        $this->db->delete(db_prefix().'fleet_inspection_question_box_description');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }


    /**
     * add box description
     * @param integer $questionid  
     * @param integer $boxid       
     * @param string $description
     * @return  integer
     */
    public function add_box_description($questionid, $boxid, $description = '', $is_fail = 0)
    {
        $this->db->insert(db_prefix().'fleet_inspection_question_box_description', [
            'questionid'  => $questionid,
            'boxid'       => $boxid,
            'description' => $description,
            'is_fail' => $is_fail,
        ]);

        return $this->db->insert_id();
    }

    /**
     * delete inspection form
     * @param  [type] $trainingid 
     * @return [type]             
     */
    public function delete_inspection_form($id)
    {
        $affectedRows = 0;
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'fleet_inspection_forms');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'inspection_form');
            $questions = $this->db->get(db_prefix().'fleet_inspection_question_forms')->result_array();
            foreach ($questions as $question) {
                $this->db->where('questionid', $question['questionid']);
                $this->db->delete(db_prefix().'fleet_inspection_question_box');
                $this->db->where('questionid', $question['questionid']);
                $this->db->delete(db_prefix().'fleet_inspection_question_box_description');
            }

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'inspection_form');
            $this->db->delete(db_prefix().'fleet_inspection_question_forms');
        }
        if ($affectedRows > 0) {

            return true;
        }

        return false;
    }

    /**
     * get inspection_forms
     * @param  integer $id 
     * @return integer     
     */
    public function get_inspection_forms($id = ''){
        if($id != ''){
            $this->db->where('id', $id);
            return $this->db->get(db_prefix().'fleet_inspection_forms')->row();
        }
        else{
            return $this->db->get(db_prefix().'fleet_inspection_forms')->result_array();
        }
    }

    /**
     * add inspections
     * @param array $data 
     */
    public function add_inspection($data, $is_recurring = 0){
        if($is_recurring == 0){
            $inspection_form = $this->get_inspection_form($data['inspection_form_id']);
            $data['recurring'] = $inspection_form->recurring;
            $data['recurring_type'] =  $inspection_form->recurring_type;
            $data['custom_recurring'] =  $inspection_form->custom_recurring;
            $data['cycles'] =  $inspection_form->cycles;
            $data['total_cycles'] =  $inspection_form->total_cycles;
        }

        if(!isset($data['datecreated'])){
            $data['datecreated'] = date('Y-m-d H:i:s');
            $data['addedfrom'] = get_staff_user_id();
        }

        $selectable = [];
        if(isset($data['selectable'])){
            $selectable = $data['selectable'];
            unset($data['selectable']);
        }

        $question = [];
        if(isset($data['question'])){
            $question = $data['question'];
            unset($data['question']);
        }

        $this->db->insert(db_prefix().'fleet_inspections', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            if (isset($selectable) && sizeof($selectable) > 0) {
                foreach ($selectable as $boxid => $question_answers) {
                    foreach ($question_answers as $questionid => $answer) {
                        $count = count($answer);
                        for ($i = 0; $i < $count; $i++) {
                            $this->db->insert(db_prefix().'fleet_inspection_results', [
                                'boxid'            => $boxid,
                                'boxdescriptionid' => $answer[$i],
                                'questionid'       => $questionid,
                                'inspection_id'      => $insert_id,
                            ]);
                        }
                    }
                }
            }

            if (isset($question)) {
                foreach ($question as $questionid => $val) {
                    $boxid = $this->get_question_box_id($questionid);
                    $this->db->insert(db_prefix().'fleet_inspection_results', [
                        'boxid'       => $boxid,
                        'questionid'  => $questionid,
                        'answer'      => $val[0],
                        'inspection_id' => $insert_id,
                    ]);
                }
            }

            return true;
        }

        if($insert_id){

            return $insert_id;
        }
        return 0;
    }
    /**
     * update inspections
     * @param array $data 
     */
    public function update_inspection($data, $id){
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'fleet_inspections', [
            'vehicle_id'                => $data['vehicle_id'],
            'inspection_form_id'        => $data['inspection_form_id'],
        ]);

        $this->db->where('inspection_id', $id);
        $this->db->delete(db_prefix().'fleet_inspection_results');

        $selectable = [];
        if(isset($data['selectable'])){
            $selectable = $data['selectable'];
            unset($data['selectable']);
        }

        $question = [];
        if(isset($data['question'])){
            $question = $data['question'];
            unset($data['question']);
        }

        if (isset($selectable) && sizeof($selectable) > 0) {
            foreach ($selectable as $boxid => $question_answers) {
                foreach ($question_answers as $questionid => $answer) {
                    $count = count($answer);
                    for ($i = 0; $i < $count; $i++) {
                        $this->db->insert(db_prefix().'fleet_inspection_results', [
                            'boxid'            => $boxid,
                            'boxdescriptionid' => $answer[$i],
                            'questionid'       => $questionid,
                            'inspection_id'      => $id,
                        ]);
                    }
                }
            }
        }

        if (isset($question)) {
            foreach ($question as $questionid => $val) {
                $boxid = $this->get_question_box_id($questionid);
                $this->db->insert(db_prefix().'fleet_inspection_results', [
                    'boxid'       => $boxid,
                    'questionid'  => $questionid,
                    'answer'      => $val[0],
                    'inspection_id' => $id,
                ]);
            }
        }

        return true;
    }


    /**
     * delete inspections
     * @param  integer $id 
     * @return boolean     
     */
    public function delete_inspection($id){
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'fleet_inspections');
        if($this->db->affected_rows() > 0) {
            $this->db->where('inspection_id', $id);
            $this->db->delete(db_prefix().'fleet_inspection_results');

            return true;
        }
        return false;
    }

    /**
     * get inspections
     * @param  integer $id 
     * @return integer     
     */
    public function get_inspections($id){
        if($id != ''){
            $this->db->where('id', $id);
            $inspection = $this->db->get(db_prefix().'fleet_inspections')->row();
            if($inspection){
                $inspection->inspection_form = $this->get_inspection_form($inspection->inspection_form_id);
                $inspection->inspection_results = $this->get_inspection_results($id);
            }

            return $inspection;
        }
        else{
            return $this->db->get(db_prefix().'fleet_inspections')->result_array();
        }
    }

    /**
     * Get quesion box id
     * @param  mixed $questionid questionid
     * @return integer
     */
    private function get_question_box_id($questionid)
    {
        $this->db->select('boxid');
        $this->db->from(db_prefix().'fleet_inspection_question_box');
        $this->db->where('questionid', $questionid);
        $box = $this->db->get()->row();

        return $box->boxid;
    }

    /**
     * get inspection results
     * @param  integer $id 
     * @param  array  $where
     * @return object
     */
    public function get_inspection_results($inspection_id)
    {
        
        $this->db->where('inspection_id', $inspection_id);
        $inspection_results = $this->db->get(db_prefix() . 'fleet_inspection_results')->result_array();

        return $inspection_results;
    }

    /**
     * check format date Y-m-d
     *
     * @param      String   $date   The date
     *
     * @return     boolean
     */
    public function check_format_date($date)
    {
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get fleet parts group id
     * @return id
     */
    public function get_fleet_parts_group_id()
    {
        $this->db->where('name', 'Fleet: Parts');
        $this->db->where('commodity_group_code', 'FLEET_PARTS');
        $items_groups = $this->db->get(db_prefix() . 'items_groups')->row();
        if($items_groups){
            return $items_groups->id;
        }
        return '';
    }

    /**
     * get fleet parts group id
     * @return id
     */
    public function get_fleet_driver_role_id()
    {
        $this->db->where('name', 'Fleet: Driver');
        $role = $this->db->get(db_prefix() . 'roles')->row();
        if($role){
            return $role->roleid;
        }
        return '';
    }

    /**
     * add benefit_and_penalty
     * @param array $data 
     */
    public function add_benefit_and_penalty($data){
        
        $data['reward'] = str_replace(',', '', $data['reward']);
        $data['amount_of_damage'] = str_replace(',', '', $data['amount_of_damage']);
        $data['amount_of_compensation'] = str_replace(',', '', $data['amount_of_compensation']);
        $this->db->insert(db_prefix().'fleet_benefit_and_penalty', $data);
        $insert_id = $this->db->insert_id();
        if($insert_id){

            return $insert_id;
        }
        return 0;
    }
    /**
     * update benefit_and_penalty
     * @param array $data 
     */
    public function update_benefit_and_penalty($data, $id){
        $data['reward'] = str_replace(',', '', $data['reward']);
        $data['amount_of_damage'] = str_replace(',', '', $data['amount_of_damage']);
        $data['amount_of_compensation'] = str_replace(',', '', $data['amount_of_compensation']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'fleet_benefit_and_penalty', $data);
        if($this->db->affected_rows() > 0) {

            return true;
        }
        return false;
    }

    /**
     * delete benefit_and_penalty
     * @param  integer $id 
     * @return boolean     
     */
    public function delete_benefit_and_penalty($id){
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'fleet_benefit_and_penalty');
        if($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get benefit_and_penalty
     * @param  integer $id 
     * @return integer     
     */
    public function get_benefit_and_penalty($id = ''){
        if($id != ''){
            $this->db->where('id', $id);
            return $this->db->get(db_prefix().'fleet_benefit_and_penalty')->row();
        }
        else{
            return $this->db->get(db_prefix().'fleet_benefit_and_penalty')->result_array();
        }
    }

    /**
     * add new criteria
     * @param array $data
     * @return integer
     */
    public function add_criteria($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'fleet_criterias', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * update criteria
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_criteria($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_criterias', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * delete criteria
     * @param integer $id
     * @return boolean
     */

    public function delete_criteria($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_criterias');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get criterias
     * @param  integer $id    member group id
     * @param  array  $where
     * @return object
     */
    public function get_criterias($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'fleet_criterias')->row();
        }

        $this->db->where($where);
        $this->db->order_by('name', 'asc');
        $criterias = $this->db->get(db_prefix() . 'fleet_criterias')->result_array();

        return $criterias;
    }

    /**
     * delete vehicle_assignment
     * @param integer $id
     * @return boolean
     */

    public function delete_vehicle_assignment($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_vehicle_assignments');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get vehicle assignments
     * @param  integer $id 
     * @return integer     
     */
    public function get_vehicle_assignment($id = ''){
        if($id != ''){
            $this->db->where('id', $id);
            return $this->db->get(db_prefix().'fleet_vehicle_assignments')->row();
        }
        else{
            return $this->db->get(db_prefix().'fleet_vehicle_assignments')->result_array();
        }
    }

    /**
     * add new booking
     * @param array $data
     * @return integer
     */
    public function add_booking($data)
    {
        if(!isset($data['number'])){
            $data['number'] = 'BOOKING'.time();
        }

        $this->db->insert(db_prefix() . 'fleet_bookings', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * update booking
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_booking($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_bookings', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * get booking
     * @param  integer $id    member type id
     * @param  array  $where
     * @return object
     */
    public function get_booking($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'fleet_bookings')->row();
        }

        $this->db->where($where);
        $this->db->order_by('id', 'desc');
        $bookings = $this->db->get(db_prefix() . 'fleet_bookings')->result_array();

        return $bookings;
    }

    /**
     * booking change status
     * @param  array  $data         
     * @param  string  $booking_number 
     * @param  integer $admin_action 
     * @return bool                
     */
    public function booking_change_status($status, $booking_id){
        $this->db->where('id',$booking_id);
        $data_update['status'] = $status;
        $this->db->update(db_prefix().'fleet_bookings',$data_update);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * create invoice by booking
     * @param  integer $id the booking id
     * @return boolean
     */
    public function create_invoice_by_booking($id)
    {
        $booking = $this->get_booking($id);
        $items = [];

        $this->load->model('invoices_model');
        $this->load->model('clients_model');
        $this->load->model('payment_modes_model');
        $client = $this->clients_model->get($booking->userid);
        $payment_modes = $this->payment_modes_model->get();
        $count    = 0;
        $newitems = [];
        array_push($newitems, array('order' => 1, 'description' => 'Fleet Booking', 'long_description' => '', 'qty' => 1, 'unit' => '', 'rate' => $booking->amount, 'taxname' => ''));

        $__number        = get_option('next_invoice_number');
        $_invoice_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
        $this->db->where('isdefault', 1);
        $curreny = $this->db->get(db_prefix() . 'currencies')->row()->id;

        if ($booking) {
            $data['clientid']                 = $booking->userid;
            $data['billing_street']           = $client->billing_street;
            $data['billing_city']             = $client->billing_city;
            $data['billing_state']            = $client->billing_state;
            $data['billing_zip']              = $client->billing_zip;
            $data['billing_country']          = $client->billing_country;
            $data['include_shipping']         = 1;
            $data['show_shipping_on_invoice'] = 1;
            $data['shipping_street']          = $client->shipping_street;
            $data['shipping_city']            = $client->shipping_city;
            $data['shipping_state']           = $client->shipping_state;
            $data['shipping_zip']             = $client->shipping_zip;
            $date_format                      = get_option('dateformat');
            $date_format                      = explode('|', $date_format);
            $date_format                      = $date_format[0];
            $data['date']                     = date($date_format);
            $data['duedate']                  = date($date_format);

            $data['currency']            = $curreny;
            $data['number']              = $_invoice_number;
            $data['total']               = $booking->amount;
            $data['subtotal']            = $booking->amount;
            $data['from_fleet']            = 1;

            $payment_model_list = [];
            if ($payment_modes) {
                foreach($payment_modes as $payment_mode){
                    $payment_model_list[] = $payment_mode['id'];
                }
            }
            $data["allowed_payment_modes"] = $payment_model_list;
            $data['newitems'] = $newitems;

            $invoice_id = $this->invoices_model->add($data);

            if ($invoice_id) {
                $invoice = $this->invoices_model->get($invoice_id);
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'fleet_bookings', ['invoice_id' => $invoice_id, 'invoice_hash' => $invoice->hash]);
            }

            return $invoice_id;
        }
        return false;
    }

    /**
     * delete booking
     * @param integer $id
     * @return boolean
     */

    public function delete_booking($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_bookings');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * add maintenance team
     * @param array $data 
     */
    public function add_maintenance_team($data){
        $this->db->where('staffid',$data['staffid']);
        $this->db->where('garage_id',$data['garage_id']);
        $team = $this->db->get(db_prefix().'fleet_maintenance_teams')->row();

        if(!$team){
            $this->db->insert(db_prefix().'fleet_maintenance_teams', $data);
            $insert_id = $this->db->insert_id();
            if($insert_id){

                return $insert_id;
            }
        }

        return 0;
    }

    /**
     * add new logbook
     * @param array $data
     * @return integer
     */
    public function add_logbook($data)
    {
        $this->db->insert(db_prefix() . 'fleet_logbooks', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * update logbook
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_logbook($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_logbooks', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * get logbook
     * @param  integer $id    member type id
     * @param  array  $where
     * @return object
     */
    public function get_logbook($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $logbook = $this->db->get(db_prefix() . 'fleet_logbooks')->row();
            if($logbook){
                $logbook->booking = $this->get_booking($logbook->booking_id);
                $logbook->vehicle = $this->get_vehicle($logbook->vehicle_id);
            }

            return $logbook;
        }

        $this->db->where($where);
        $this->db->order_by('id', 'desc');
        $logbooks = $this->db->get(db_prefix() . 'fleet_logbooks')->result_array();

        return $logbooks;
    }

    /**
     * logbook change status
     * @param  array  $data         
     * @param  string  $logbook_number 
     * @param  integer $admin_action 
     * @return bool                
     */
    public function logbook_change_status($status, $logbook_id){
        $this->db->where('id',$logbook_id);
        $data_update['status'] = $status;
        $this->db->update(db_prefix().'fleet_logbooks',$data_update);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    /**
     * delete logbook
     * @param integer $id
     * @return boolean
     */

    public function delete_logbook($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_logbooks');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

     /**
     * add new time_card
     * @param array $data
     * @return integer
     */
    public function add_time_card($data)
    {
        $this->db->insert(db_prefix() . 'fleet_time_cards', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * update time_card
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_time_card($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_time_cards', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * get time_card
     * @param  integer $id    member type id
     * @param  array  $where
     * @return object
     */
    public function get_time_card($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $time_card = $this->db->get(db_prefix() . 'fleet_time_cards')->row();

            return $time_card;
        }

        $this->db->where($where);
        $this->db->order_by('id', 'desc');
        $time_cards = $this->db->get(db_prefix() . 'fleet_time_cards')->result_array();

        return $time_cards;
    }

    /**
     * delete time_card
     * @param integer $id
     * @return boolean
     */

    public function delete_time_card($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_time_cards');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * add new insurance
     * @param array $data
     * @return integer
     */
    public function add_insurance($data)
    {
        $data['amount'] = str_replace(',', '', $data['amount']);

        $this->db->insert(db_prefix() . 'fleet_insurances', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * update insurance
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_insurance($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['amount'] = str_replace(',', '', $data['amount']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_insurances', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * get insurance
     * @param  integer $id    member type id
     * @param  array  $where
     * @return object
     */
    public function get_insurance($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $insurance = $this->db->get(db_prefix() . 'fleet_insurances')->row();

            return $insurance;
        }

        $this->db->where($where);
        $this->db->order_by('id', 'desc');
        $insurances = $this->db->get(db_prefix() . 'fleet_insurances')->result_array();

        return $insurances;
    }

    /**
     * delete insurance
     * @param integer $id
     * @return boolean
     */

    public function delete_insurance($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_insurances');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * add new insurance_category
     * @param array $data
     * @return integer
     */
    public function add_insurance_category($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'fleet_insurance_categories', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * update insurance_category
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_insurance_category($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_insurance_categories', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * delete insurance_category
     * @param integer $id
     * @return boolean
     */

    public function delete_insurance_category($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_insurance_categories');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get insurance_categorys
     * @param  integer $id    member group id
     * @param  array  $where
     * @return object
     */
    public function get_insurance_category($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'fleet_insurance_categories')->row();
        }

        $this->db->where($where);
        $this->db->order_by('name', 'asc');
        $insurance_categorys = $this->db->get(db_prefix() . 'fleet_insurance_categories')->result_array();

        return $insurance_categorys;
    }

    /**
     * add new insurance_type
     * @param array $data
     * @return integer
     */
    public function add_insurance_type($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'fleet_insurance_types', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * update insurance_type
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_insurance_type($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_insurance_types', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * delete insurance_type
     * @param integer $id
     * @return boolean
     */

    public function delete_insurance_type($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_insurance_types');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get insurance_types
     * @param  integer $id    member group id
     * @param  array  $where
     * @return object
     */
    public function get_insurance_type($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'fleet_insurance_types')->row();
        }

        $this->db->where($where);
        $this->db->order_by('name', 'asc');
        $insurance_types = $this->db->get(db_prefix() . 'fleet_insurance_types')->result_array();

        return $insurance_types;
    }

    /**
     * add new event
     * @param array $data
     * @return integer
     */
    public function add_event($data)
    {
        $this->db->insert(db_prefix() . 'fleet_events', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * update event
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_event($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_events', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * get event
     * @param  integer $id    member type id
     * @param  array  $where
     * @return object
     */
    public function get_event($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'fleet_events')->row();
        }

        $this->db->where($where);
        $this->db->order_by('id', 'desc');
        $events = $this->db->get(db_prefix() . 'fleet_events')->result_array();

        return $events;
    }
    /**
     * delete event
     * @param integer $id
     * @return boolean
     */

    public function delete_event($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_events');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * add new work_order
     * @param array $data
     * @return integer
     */
    public function add_work_order($data)
    {
        if(!isset($data['number'])){
            $data['number'] = 'WO'.time();
        }

        if(isset($data['parts'])){
            $data['parts'] = implode(',',$data['parts']);
        }

        if(isset($data['issue_date'])){
            $data['issue_date'] = to_sql_date($data['issue_date']);
        }

        if(isset($data['start_date'])){
            $data['start_date'] = to_sql_date($data['start_date']);
        }

        if(isset($data['complete_date'])){
            $data['complete_date'] = to_sql_date($data['complete_date']);
        }

        $data['total'] = str_replace(',', '', $data['total']);
        $data['odometer_in'] = str_replace(',', '', $data['odometer_in']);
        $data['odometer_out'] = str_replace(',', '', $data['odometer_out']);

        $this->db->insert(db_prefix() . 'fleet_work_orders', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            
            return $insert_id;
        }

        return false;
    }

    /**
     * update work_order
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_work_order($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if(isset($data['issue_date'])){
            $data['issue_date'] = to_sql_date($data['issue_date']);
        }

        if(isset($data['start_date'])){
            $data['start_date'] = to_sql_date($data['start_date']);
        }

        if(isset($data['complete_date'])){
            $data['complete_date'] = to_sql_date($data['complete_date']);
        }

        if(isset($data['parts'])){
            $data['parts'] = implode(',',$data['parts']);
        }

        $data['total'] = str_replace(',', '', $data['total']);
        $data['odometer_in'] = str_replace(',', '', $data['odometer_in']);
        $data['odometer_out'] = str_replace(',', '', $data['odometer_out']);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_work_orders', $data);

        $this->db->where('work_order_id', $id);
        $this->db->delete(db_prefix() . 'fleet_work_order_details');

        $data_insert = [];
        $this->load->model('invoice_items_model');

        return true;
    }

    /**
     * get work_order
     * @param  integer $id    member type id
     * @param  array  $where
     * @return object
     */
    public function get_work_order($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $work_order = $this->db->get(db_prefix() . 'fleet_work_orders')->row();

            return $work_order;
        }

        $this->db->where($where);
        $this->db->order_by('id', 'desc');
        $work_orders = $this->db->get(db_prefix() . 'fleet_work_orders')->result_array();

        return $work_orders;
    }

    /**
     * work_order change status
     * @param  array  $data         
     * @param  string  $work_order_number 
     * @param  integer $admin_action 
     * @return bool                
     */
    public function work_order_change_status($status, $work_order_id){
        $this->db->where('id',$work_order_id);
        $data_update['status'] = $status;
        $this->db->update(db_prefix().'fleet_work_orders',$data_update);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
/**
     * delete work_order
     * @param integer $id
     * @return boolean
     */

    public function delete_work_order($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_work_orders');
        if ($this->db->affected_rows() > 0) {

            $this->db->where('work_order_id', $id);
            $this->db->delete(db_prefix() . 'fleet_work_order_details');

            return true;
        }
        return false;
    }

    /**
     * create expense by work_order
     * @param  integer $id the work_order id
     * @return boolean
     */
    public function create_expense_by_work_order($id)
    {
        $work_order = $this->get_work_order($id);
        $items = [];

        $this->load->model('expenses_model');

        $this->db->where('isdefault', 1);
        $curreny = $this->db->get(db_prefix() . 'currencies')->row()->id;

        if ($work_order) {
            $data = [];
            $data['expense_name']                 = _l('work_order_number').': '.$work_order->number;
            $data['vendor']                 = $work_order->vendor_id;
            $data['amount']                 = $work_order->total;
            $data['category']                 = $this->get_fleet_expense_category_id();
            $data['note']                 = '';
            
            $date_format                      = get_option('dateformat');
            $date_format                      = explode('|', $date_format);
            $date_format                      = $date_format[0];
            $data['date']                     = date($date_format);

            $data['currency']            = $curreny;
            $data['from_fleet']            = 1;

            $expense_id = $this->expenses_model->add($data);

            if ($expense_id) {
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'fleet_work_orders', ['expense_id' => $expense_id]);
            }

            return $expense_id;
        }
        return false;
    }

    /**
     * get fleet parts group id
     * @return id
     */
    public function get_fleet_expense_category_id()
    {
        $this->db->where('name', 'Fleet: Work Order');
        $category = $this->db->get(db_prefix() . 'expenses_categories')->row();
        if($category){
            return $category->id;
        }
        return '';
    }

    /**
     * delete driver_document
     * @param integer $id
     * @return boolean
     */

    public function delete_driver_document($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_driver_documents');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'fle_driver_document');
            $this->db->delete(db_prefix().'files');
            if($this->db->affected_rows() > 0){ 
                $affectedRows++;
            }

            if (is_dir(FLEET_MODULE_UPLOAD_FOLDER .'/driver_documents/'. $id)) {
                delete_dir(FLEET_MODULE_UPLOAD_FOLDER .'/driver_documents/'. $id);
            }

            return true;
        }
        return false;
    }

    /**
     * Delete driver_document attachment
     * @param  mixed $id driver_document id
     * @return boolean
     */
    public function delete_driver_document_attachment($file, $document_id)
    {
        if (is_dir(FLEET_MODULE_UPLOAD_FOLDER .'/driver_documents/'. $document_id)) {
            if (unlink(FLEET_MODULE_UPLOAD_FOLDER .'/driver_documents/'.$document_id.'/'. $file->file_name)) {
                $this->db->where('id', $file->id);
                $this->db->delete(db_prefix() . 'files');

                return true;
            }
        }

        return false;
    }

    /**
     * get data sales chart
     * @param  array $data_filter
     * @return array
     */
    public function get_data_sales_chart(){
        $this->load->model('currencies_model');
        $currency = $this->currencies_model->get_base_currency();
        if(isset($data_filter['currency'])){
            $data_currency = $data_filter['currency'];
        }else{
            $data_currency = $currency->id;
        }


        $where = $this->get_where_report_period('date');

        if($where != ''){
            $this->db->where($where);
        }

        $this->db->where('from_fleet', 1);
        $invoices = $this->db->get(db_prefix().'invoices')->result_array();

        if($where != ''){
            $this->db->where($where);
        }
        $this->db->where('from_fleet', 1);
        $expenses = $this->db->get(db_prefix().'expenses')->result_array();

        $data_return = [];
        $data_date = [];

        $list_invoice = '0';
        foreach ($invoices as $key => $value) {
            $list_invoice .= ','.$value['id'];
            
            if(isset($data_date[$value['date']])){
                $data_date[$value['date']]['payment'] += floatval($value['subtotal']);
            }else{
                $data_date[$value['date']] = [];
                $data_date[$value['date']]['payment'] = floatval($value['subtotal']);
                $data_date[$value['date']]['expense'] = 0;
            }
        }

        $list_expense = '0';

        foreach ($expenses as $key => $value) {
            $list_expense .= ','.$value['id'];
            
            if(isset($data_date[$value['date']])){
                $data_date[$value['date']]['expense'] += floatval($value['amount']);
            }else{
                $data_date[$value['date']] = [];
                $data_date[$value['date']]['expense'] = floatval($value['amount']);
                $data_date[$value['date']]['payment'] = 0;
            }
        }

        

        $sales = [];
        $expenses = [];
        $categories = [];
        $date_array = [];

        foreach ($data_date as $d => $val) {
            $_date = $d;
            foreach ($data_date as $date => $value) {
                if(strtotime($_date) > (strtotime($date)) && !in_array($date,$date_array)){
                    $_date = $date;
                }elseif(!in_array($date,$date_array) && in_array($_date,$date_array)){
                    $_date = $date;
                }
            }

            $date_array[] = $_date;

        }

        foreach ($date_array as $date) {
            if(isset($data_date[$date])){
                $sales[] = $data_date[$date]['payment'];
                $expenses[] = $data_date[$date]['expense'];
                $categories[] = _d($date);
            }
        }

        $data_return = [
            'data' => [
                ['name' => _l('invoices'), 'data' => $sales],
                ['name' => _l('expenses'), 'data' => $expenses],
            ],
            'categories' => $categories
        ];
        return $data_return;
    }

    /**
     * get data profit and loss chart
     * @param  array $data_filter 
     * @return array              
     */
    public function get_data_profit_and_loss_chart(){
        $accounting_method = get_option('acc_accounting_method');

        $where = $this->get_where_report_period();

        if($where != ''){
            $this->db->where($where);
        }

        $this->db->where('from_fleet', 1);
        $invoices = $this->db->get(db_prefix().'invoices')->result_array();

        if($where != ''){
            $this->db->where($where);
        }
        $this->db->where('from_fleet', 1);
        $expenses = $this->db->get(db_prefix().'expenses')->result_array();

        $data_report = [];
        $expense = 0;
        $income = 0;
        
        foreach ($expenses as $key => $value) {
            $expense += floatval($value['amount']);
        }

        foreach ($invoices as $key => $value) {
            $income += floatval($value['subtotal']);
        }
        
        $net_income = round($income - $expense, 2);

        return [$net_income, round($income, 2), round($expense, 2)];
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

                $first_month_of_financial_year = get_option('acc_first_month_of_financial_year');

                $month = date('m', strtotime($first_month_of_financial_year));
                $custom_date_select = '(' . $field . ' BETWEEN "' . date($year[1].'-'.$month.'-01') . '" AND "' . date(($year[1]+1).'-'.$month.'-01') . '")';
            }
        }

        return $custom_date_select;
    }

    /**
     * @param  integer
     * @param  array
     * @return array
     */
    public function get_data_fuel_chart()
    {
        $where = $this->get_where_report_period('date_format(fuel_time, \'%Y-%m-%d\')');

        $this->db->select('date_format(fuel_time, \'%Y-%m-%d\') as time, SUM(price) as total');
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->group_by('date_format(fuel_time, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'fleet_fuel_history')->result_array();
        $data_delivery = [];
        foreach($email_logs as $download){
            $data_delivery[] = [strtotime($download['time'].' 00:00:00') * 1000, (float)$download['total']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('amount'), 'data' => $data_delivery, 'color' => '#008ece'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @param  array
     * @return array
     */
    public function get_data_maintenance_chart()
    {
        $where = $this->get_where_report_period('start_date');

        $this->db->select('start_date as time, SUM(cost) as total');
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->group_by('start_date');
        $email_logs = $this->db->get(db_prefix().'fleet_maintenances')->result_array();
        $data_delivery = [];
        foreach($email_logs as $download){
            $data_delivery[] = [strtotime($download['time'].' 00:00:00') * 1000, (float)$download['total']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('amount'), 'data' => $data_delivery, 'color' => '#008ece'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @param  array
     * @return array
     */
    public function get_data_event_chart()
    {
        $where = $this->get_where_report_period('date_format(event_time, \'%Y-%m-%d\')');

        $this->db->select('date_format(event_time, \'%Y-%m-%d\') as time, COUNT(*) as total');
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->group_by('date_format(event_time, \'%Y-%m-%d\')');
        $email_logs = $this->db->get(db_prefix().'fleet_events')->result_array();
        $data_delivery = [];
        foreach($email_logs as $download){
            $data_delivery[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['total']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('total'), 'data' => $data_delivery, 'color' => '#008ece'];
        
        return $data_return;
    }


    /**
     * get data event stats
     *
     * @return  object
     */
    public function event_stats()
    {
        $where = $this->get_where_report_period('start_date');

        $this->db->select('event_type, COUNT(*) as total');
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->group_by('event_type');
        $event_logs = $this->db->get(db_prefix().'fleet_events')->result_array();

        $chart = [];
        foreach($event_logs as $download){
            $chart[] = ['name' => _l($download['event_type']), 'y' => (int)$download['total']];
        }

        return $chart;
    }

    /**
     * get data work_order stats
     *
     * @return  object
     */
    public function work_order_stats()
    {
        $where = $this->get_where_report_period('issue_date');

        $this->db->select('status, COUNT(*) as total');
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->group_by('status');
        $event_logs = $this->db->get(db_prefix().'fleet_work_orders')->result_array();

        $chart = [];
        foreach($event_logs as $download){
            $chart[] = ['name' => _l($download['status']), 'y' => (int)$download['total']];
        }

        return $chart;
    }

    /**
     * @param  integer
     * @param  array
     * @return array
     */
    public function get_data_work_order_chart()
    {
        $where = $this->get_where_report_period('issue_date');

        $this->db->select('issue_date as time, SUM(total) as total');
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->group_by('time');
        $email_logs = $this->db->get(db_prefix().'fleet_work_orders')->result_array();
        $data_delivery = [];
        foreach($email_logs as $download){
            $data_delivery[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['total']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('total'), 'data' => $data_delivery, 'color' => '#008ece'];
        
        return $data_return;
    }

    /**
     * get data work_performance stats
     *
     * @return  object
     */
    public function work_performance_stats()
    {
        $where = $this->get_where_report_period('date');

        $this->db->select('status, COUNT(*) as total');
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->group_by('status');
        $event_logs = $this->db->get(db_prefix().'fleet_logbooks')->result_array();

        $chart = [];
        foreach($event_logs as $download){
            $chart[] = ['name' => _l($download['status']), 'y' => (int)$download['total']];
        }

        return $chart;
    }

    /**
     * @param  integer
     * @param  array
     * @return array
     */
    public function get_data_work_performance_chart()
    {
        $where = $this->get_where_report_period('date');

        $this->db->select('date as time, COUNT(*) as total');
        if($where != ''){
            $this->db->where($where);
        }
        $this->db->group_by('time');
        $email_logs = $this->db->get(db_prefix().'fleet_logbooks')->result_array();
        $data_delivery = [];
        foreach($email_logs as $download){
            $data_delivery[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['total']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('total'), 'data' => $data_delivery, 'color' => '#008ece'];
        
        return $data_return;
    }

    public function get_calendar_data($start, $end, $client_id = '', $contact_id = '', $filters = false)
    {
        $start      = $this->db->escape_str($start);
        $end        = $this->db->escape_str($end);
        $client_id  = $this->db->escape_str($client_id);
        $contact_id = $this->db->escape_str($contact_id);

        $is_admin                     = is_admin();
        $has_permission_invoices      = has_permission('invoices', '', 'view');
        $data                         = [];

        $client_data = false;
       

        $hook = [
            'client_data' => $client_data,
        ];
        if ($client_data == true) {
            $hook['client_id']  = $client_id;
            $hook['contact_id'] = $contact_id;
        }

        $data = hooks()->apply_filters('before_fetch_events', $data, $hook);

        $ff = false;
        if ($filters) {
            // excluded calendar_filters from post
            $ff = (count($filters) > 1 && isset($filters['calendar_filters']) ? true : false);
        }

        if (get_option('show_invoices_on_calendar') == 1 && !$ff || $ff && array_key_exists('invoices', $filters)) {
            $noPermissionsQuery = get_invoices_where_sql_for_staff(get_staff_user_id());

            $this->db->select('duedate as date,number,id,clientid,hash,' . get_sql_select_client_company());
            $this->db->from(db_prefix() . 'invoices');
            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid=' . db_prefix() . 'invoices.clientid', 'left');
            $this->db->where_not_in('status', [
                2,
                5,
            ]);

            $this->db->where('(duedate BETWEEN "' . $start . '" AND "' . $end . '")');
            $this->db->where('from_fleet', 1);

            if ($client_data) {
                $this->db->where('clientid', $client_id);

                if (get_option('exclude_invoice_from_client_area_with_draft_status') == 1) {
                    $this->db->where('status !=', 6);
                }
            } else {
                if (!$has_permission_invoices) {
                    $this->db->where($noPermissionsQuery);
                }
            }
            $invoices = $this->db->get()->result_array();
            foreach ($invoices as $invoice) {
                if (($client_data && !$has_contact_permission_invoices) || (!$client_data && !user_can_view_invoice($invoice['id']))) {
                    continue;
                }

                $rel_showcase = '';

                /**
                 * Show company name on calendar tooltip for admins
                 */
                if (!$client_data) {
                    $rel_showcase = ' (' . $invoice['company'] . ')';
                }

                $number = format_invoice_number($invoice['id']);

                $invoice['_tooltip'] = _l('calendar_invoice') . ' - ' . $number . $rel_showcase;
                $invoice['title']    = $number;
                $invoice['color']    = get_option('calendar_invoice_color');

                if (!$client_data) {
                    $invoice['url'] = admin_url('invoices/list_invoices/' . $invoice['id']);
                } else {
                    $invoice['url'] = site_url('invoice/' . $invoice['id'] . '/' . $invoice['hash']);
                }

                array_push($data, $invoice);
            }
        }

        $events = $this->get_all_events($start, $end);
        foreach ($events as $event) {
            if ($event['driver_id'] != get_staff_user_id() && !$is_admin) {
                $event['is_not_creator'] = true;
                $event['onclick']        = true;
            }

            $event['_tooltip'] = _l('calendar_event') . ' - ' . $event['subject'];
            $event['title'] = _l('calendar_event') . ' - ' . $event['subject'];
            $event['color']    = get_option('calendar_project_color');
            $event['start']    = $event['event_time'];

            array_push($data, $event);
        }

        $bookings = $this->get_all_bookings($start, $end);
        foreach ($bookings as $booking) {
            $booking['_tooltip'] = $booking['number'];
            $booking['title'] = $booking['number'];
            $booking['url'] = admin_url('fleet/booking_detail/' . $booking['id']);
            $booking['color']    = get_option('calendar_proposal_color');
            $booking['date']    = $booking['delivery_date'];

            array_push($data, $booking);
        }

        $work_orders = $this->get_all_work_orders($start, $end);
        foreach ($work_orders as $work_order) {
            $work_order['_tooltip'] = $work_order['number'];
            $work_order['title'] = $work_order['number'];
            $work_order['url'] = admin_url('fleet/work_order_detail/' . $work_order['id']);
            $work_order['color']    = get_option('calendar_reminder_color');
            $work_order['date']    = $work_order['issue_date'];

            array_push($data, $work_order);
        }

        $expenses = $this->get_all_expenses($start, $end);
        foreach ($expenses as $expense) {
            $expense['_tooltip'] = $expense['id'].'- '.$expense['expense_name'];
            $expense['title'] = $expense['id'].'- '.$expense['expense_name'];
            $expense['url'] = admin_url('expenses#' . $expense['id']);
            $expense['color']    = get_option('calendar_estimate_color');

            array_push($data, $expense);
        }

        return hooks()->apply_filters('calendar_data', $data, [
            'start'      => $start,
            'end'        => $end,
            'client_id'  => $client_id,
            'contact_id' => $contact_id,
        ]);
    }

    /**
     * Get all user events
     * @return array
     */
    public function get_all_events($start, $end)
    {
        // Check if is passed start and end date
        $this->db->where('(date_format(event_time, \'%Y-%m-%d\') BETWEEN "' . $start . '" AND "' . $end . '")');
      
        return $this->db->get(db_prefix() . 'fleet_events')->result_array();
    }

    /**
     * Get all user bookings
     * @return array
     */
    public function get_all_bookings($start, $end)
    {
        // Check if is passed start and end date
        $this->db->where('(delivery_date BETWEEN "' . $start . '" AND "' . $end . '")');
      
        return $this->db->get(db_prefix() . 'fleet_bookings')->result_array();
    }

    /**
     * Get all user work_orders
     * @return array
     */
    public function get_all_work_orders($start, $end)
    {
        // Check if is passed start and end date
        $this->db->where('(issue_date BETWEEN "' . $start . '" AND "' . $end . '")');
      
        return $this->db->get(db_prefix() . 'fleet_work_orders')->result_array();
    }

    /**
     * Get all user expenses
     * @return array
     */
    public function get_all_expenses($start, $end)
    {
        // Check if is passed start and end date
        $this->db->where('from_fleet', 1);
        $this->db->where('(date BETWEEN "' . $start . '" AND "' . $end . '")');
      
        return $this->db->get(db_prefix() . 'expenses')->result_array();
    }

    /**
     * Calculating Fleet Fuel Economy
     * @return array
     */
    public function calculating_fuel_consumption($vehicle_id)
    {
        $odometer_min = 0;
        $vehicle = $this->get_vehicle($vehicle_id);
        if($vehicle && $vehicle->odometer != null){
            $odometer_min = $vehicle->odometer;
        }else{
            $this->db->select_min('odometer');
            $this->db->where('vehicle_id', $vehicle_id);
            $odometer_min = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->odometer;
        }

        if($odometer_min < 0 || $odometer_min == null){
            $odometer_min = 0;
        }

        $this->db->select_max('odometer');
        $this->db->where('vehicle_id', $vehicle_id);
        $odometer_max = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->odometer;

        $this->db->select_sum('gallons');
        $this->db->where('vehicle_id', $vehicle_id);
        $total_gallons = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->gallons;

        if($odometer_min == $odometer_max){
            $total_km = (float)$odometer_max;
        }else{
            $total_km = (float)$odometer_max - (float)$odometer_min;
        }

        $consumption_100km = 0;
        if($total_km != 0){
            $consumption_100km = ((float)$total_gallons/$total_km) * 100;
        }

        return [
                'total_gallons' => $total_gallons != null ? $total_gallons : 0, 
                'total_km' => $total_km, 
                'consumption_100km' => $consumption_100km, 
            ];
    }

    /**
     * Fuel Consumption Ranking
     * @return array
     */
    public function fuel_consumption_ranking()
    {
        $vehicles = $this->get_vehicle();

        $consumption_list = [];
        $vehicle_name = [];
        foreach($vehicles as $vehicle){
            $consumption = $this->calculating_fuel_consumption($vehicle['id']);
            $consumption_list[$vehicle['id']] = $consumption['consumption_100km'];
            $vehicle_name[$vehicle['id']] = $vehicle['name'];
        }

        asort($consumption_list);

        $ranking_list = [];
        $rank = 0;
        foreach($consumption_list as $key => $consumption){
            if($consumption <= 0){
                continue;
            }

            $rank++;
            $ranking_list[] = ['vehicle_id' => $key,'vehicle_name' => $vehicle_name[$key],'consumption_100km' => number_format($consumption, 4),'rank' => $rank];
            if($rank == 10){
                break;
            }
        }

        return $ranking_list;
    }

    /**
     * Calculating driver point
     * @return array
     */
    public function calculating_driver_point($driver_id)
    {
        
        $this->db->select('SUM(rating) as rating, COUNT(*) as total');
        $this->db->where('rating != 0');
        $this->db->where('((select count(*) from ' . db_prefix() . 'fleet_logbooks where ' . db_prefix() . 'fleet_logbooks.booking_id = ' . db_prefix() . 'fleet_bookings.id and ' . db_prefix() . 'fleet_logbooks.driver_id = "'.$driver_id.'") > 0)');
        $driver_count = $this->db->get(db_prefix() . 'fleet_bookings')->row();
        $rating = 0;
        $point = 0;
        if($driver_count->total > 0 && $driver_count->rating != null){
            $rating = number_format($driver_count->rating/$driver_count->total, 1);
            $point = (($rating)*0.3)*($driver_count->total/0.7);
        }

        return [
                'total_rating' => $driver_count->total, 
                'rating' => $rating, 
                'point' => number_format($point, 4), 
            ];
    }

    /**
     * driver Ranking
     * @return array
     */
    public function driver_ranking()
    {
        $drivers = $this->get_driver();

        $point_list = [];
        $rating_list = [];
        $total_rating_list = [];
        foreach($drivers as $driver){
            $driver_point = $this->calculating_driver_point($driver['staffid']);
            $point_list[$driver['staffid']] = $driver_point['point'];
            $rating_list[$driver['staffid']] = $driver_point['rating'];
            $total_rating_list[$driver['staffid']] = $driver_point['total_rating'];
        }

        arsort($point_list);

        $ranking_list = [];
        $rank = 0;
        foreach($point_list as $key => $point){
            if($point <= 0){
                continue;
            }

            $rank++;
            $ranking_list[] = ['driver_id' => $key,'driver_name' => get_staff_full_name($key), 'rating' => $rating_list[$key], 'total_rating' => $total_rating_list[$key], 'point' => $point,'rank' => $rank];
            if($rank == 10){
                break;
            }
        }
        return $ranking_list;
    }

    /**
     * add new part group
     * @param array $data
     * @return integer
     */
    public function add_part_group($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'fleet_part_groups', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * update part group
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_part_group($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_part_groups', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * delete part group
     * @param integer $id
     * @return boolean
     */

    public function delete_part_group($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_part_groups');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get part groups
     * @param  integer $id    member group id
     * @param  array  $where
     * @return object
     */
    public function get_data_part_groups($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'fleet_part_groups')->row();
        }

        $this->db->where($where);
        $this->db->order_by('name', 'asc');
        $part_groups = $this->db->get(db_prefix() . 'fleet_part_groups')->result_array();

        return $part_groups;
    }

    /**
     * add new part type
     * @param array $data
     * @return integer
     */
    public function add_part_type($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'fleet_part_types', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * update part type
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_part_type($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_part_types', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * delete part type
     * @param integer $id
     * @return boolean
     */

    public function delete_part_type($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_part_types');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get part types
     * @param  integer $id    member type id
     * @param  array  $where
     * @return object
     */
    public function get_data_part_types($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'fleet_part_types')->row();
        }

        $this->db->where($where);
        $this->db->order_by('name', 'asc');
        $part_types = $this->db->get(db_prefix() . 'fleet_part_types')->result_array();

        return $part_types;
    }

    /**
     * add new part
     * @param array $data
     * @return integer
     */
    public function add_part($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if(isset($data['purchase_date'])){
            $data['purchase_date'] = to_sql_date($data['purchase_date']);
        }

        if(isset($data['warranty_expiration_date'])){
            $data['warranty_expiration_date'] = to_sql_date($data['warranty_expiration_date']);
        }

        if(isset($data['in_service_date'])){
            $data['in_service_date'] = to_sql_date($data['in_service_date']);
        }

        if(isset($data['out_of_service_date'])){
            $data['out_of_service_date'] = to_sql_date($data['out_of_service_date']);
        }

        $data['purchase_price'] = str_replace(',', '', $data['purchase_price']);
        $data['estimated_resale_value'] = str_replace(',', '', $data['estimated_resale_value']);

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'fleet_parts', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            if($data['driver_id'] != ''){
                $this->db->insert(db_prefix() . 'fleet_part_histories', [
                    'part_id' => $insert_id,
                    'type' => 'assignee',
                    'driver_id' => $data['driver_id'],
                    'start_time' => date('Y-m-d H:i:s'),
                    'start_by' => get_staff_user_id(),
                ]);
            }

            if($data['vehicle_id'] != ''){
                $this->db->insert(db_prefix() . 'fleet_part_histories', [
                    'part_id' => $insert_id,
                    'type' => 'linked_vehicle',
                    'vehicle_id' => $data['vehicle_id'],
                    'start_time' => date('Y-m-d H:i:s'),
                    'start_by' => get_staff_user_id(),
                ]);
            }

            return $insert_id;
        }

        return false;
    }

    /**
     * delete part
     * @param integer $id
     * @return boolean
     */

    public function delete_part($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_parts');
        if ($this->db->affected_rows() > 0) {

            $this->db->where('part_id', $id);
            $this->db->delete(db_prefix() . 'fleet_part_histories');

            return true;
        }
        return false;
    }

    /**
     * get part
     * @param  integer $id    member type id
     * @param  array  $where
     * @return object
     */
    public function get_part($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'fleet_parts')->row();
        }

        $this->db->where($where);
        $this->db->order_by('name', 'asc');
        $parts = $this->db->get(db_prefix() . 'fleet_parts')->result_array();

        return $parts;
    }

    /**
     * update part
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_part($data, $id)
    {
        $part = $this->get_part($id);

        if (isset($data['id'])) {
            unset($data['id']);
        }

        if($data['driver_id'] == ''){
            $data['driver_id'] = 0;
        }

        if($data['vehicle_id'] == ''){
            $data['vehicle_id'] = 0;
        }

        if(isset($data['purchase_date'])){
            $data['purchase_date'] = to_sql_date($data['purchase_date']);
        }

        if(isset($data['warranty_expiration_date'])){
            $data['warranty_expiration_date'] = to_sql_date($data['warranty_expiration_date']);
        }

        if(isset($data['in_service_date'])){
            $data['in_service_date'] = to_sql_date($data['in_service_date']);
        }

        if(isset($data['out_of_service_date'])){
            $data['out_of_service_date'] = to_sql_date($data['out_of_service_date']);
        }

        $data['purchase_price'] = str_replace(',', '', $data['purchase_price']);
        $data['estimated_resale_value'] = str_replace(',', '', $data['estimated_resale_value']);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_parts', $data);

        if ($this->db->affected_rows() > 0) {

            if($part->driver_id != $data['driver_id']){
                $this->db->where('end_time is null');
                $this->db->where('type', 'assignee');
                $this->db->update(db_prefix() . 'fleet_part_histories', [
                    'end_time' => date('Y-m-d H:i:s'),
                    'end_by' => get_staff_user_id(),
                ]);

                if($data['driver_id'] != 0){
                    $this->db->insert(db_prefix() . 'fleet_part_histories', [
                        'part_id' => $id,
                        'type' => 'assignee',
                        'driver_id' => $data['driver_id'],
                        'start_time' => date('Y-m-d H:i:s'),
                        'start_by' => get_staff_user_id(),
                    ]);
                }
            }

            if($part->vehicle_id != $data['vehicle_id']){
                $this->db->where('end_time is null');
                $this->db->where('type', 'linked_vehicle');
                $this->db->update(db_prefix() . 'fleet_part_histories', [
                    'end_time' => date('Y-m-d H:i:s'),
                    'end_by' => get_staff_user_id(),
                ]);

                if($data['vehicle_id'] != 0){
                    $this->db->insert(db_prefix() . 'fleet_part_histories', [
                        'part_id' => $id,
                        'type' => 'linked_vehicle',
                        'vehicle_id' => $data['vehicle_id'],
                        'start_time' => date('Y-m-d H:i:s'),
                        'start_by' => get_staff_user_id(),
                    ]);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * add new insurance company
     * @param array $data
     * @return integer
     */
    public function add_insurance_company($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'fleet_insurance_company', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * update insurance company
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_insurance_company($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_insurance_company', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * delete insurance company
     * @param integer $id
     * @return boolean
     */

    public function delete_insurance_company($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_insurance_company');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get insurance company
     * @param  integer $id    member type id
     * @param  array  $where
     * @return object
     */
    public function get_data_insurance_company($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'fleet_insurance_company')->row();
        }

        $this->db->where($where);
        $this->db->order_by('name', 'asc');
        $insurance_company = $this->db->get(db_prefix() . 'fleet_insurance_company')->result_array();

        return $insurance_company;
    }

    /**
     * add new insurance status
     * @param array $data
     * @return integer
     */
    public function add_insurance_status($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'fleet_insurance_status', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * update insurance status
     * @param array $data
     * @param integer $id
     * @return integer
     */
    public function update_insurance_status($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fleet_insurance_status', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * delete insurance status
     * @param integer $id
     * @return boolean
     */

    public function delete_insurance_status($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fleet_insurance_status');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get insurance status
     * @param  integer $id    member type id
     * @param  array  $where
     * @return object
     */
    public function get_data_insurance_status($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'fleet_insurance_status')->row();
        }

        $this->db->where($where);
        $this->db->order_by('name', 'asc');
        $insurance_status = $this->db->get(db_prefix() . 'fleet_insurance_status')->result_array();

        return $insurance_status;
    }

    /**
     * [vehicle_operating_cost_summary description]
     * @return [type] [description]
     */
    public function vehicle_operating_cost_summary($vehicle_id = ''){
        $this->db->select_sum('price');
        if($vehicle_id != ''){
            $this->db->where('vehicle_id', $vehicle_id);
        }

        $fuel_costs = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->price;

        $this->db->select_sum('total');
        if($vehicle_id != ''){
            $this->db->where('vehicle_id', $vehicle_id);
        }

        $work_order_costs = $this->db->get(db_prefix() . 'fleet_work_orders')->row()->total;

        $total_cost = (float)$fuel_costs + (float)$work_order_costs;

        return ['fuel_costs' => $fuel_costs, 'work_order_costs' => $work_order_costs, 'total_cost' => $total_cost];
    }

    /**
     * [get_data_operating_cost_pie_chart description]
     * @return [type] [description]
     */
    public function operating_cost_stats()
    {
        $this->db->select_sum('price');

        $fuel_costs = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->price;

        $this->db->select_sum('total');

        $work_order_costs = $this->db->get(db_prefix() . 'fleet_work_orders')->row()->total;

        $chart = [];
        $chart[] = ['name' => _l('fuel_costs'), 'y' => (float)$fuel_costs];
        $chart[] = ['name' => _l('work_order_costs'), 'y' => (float)$work_order_costs];

        return $chart;
    }

    /**
     * @param  integer
     * @param  array
     * @return array
     */
    public function get_data_operating_cost_chart()
    {

        $this->db->order_by('fuel_time');
        $fuel_history = $this->db->get(db_prefix() . 'fleet_fuel_history')->result_array();
        
        $data_fuel = [];
        foreach($fuel_history as $history){
            $data_fuel[] = [strtotime($history['fuel_time']) * 1000, (float)$history['price']];
        }

        $this->db->order_by('issue_date');
        $work_orders = $this->db->get(db_prefix() . 'fleet_work_orders')->result_array();
        
        $data_work_order = [];
        foreach($work_orders as $work_order){
            $data_work_order[] = [strtotime($work_order['issue_date']. ' 00:00:00') * 1000, (float)$work_order['total']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('fuel_costs'), 'data' => $data_fuel, 'color' => '#008ece'];
        $data_return[] = ['name' => _l('work_order_costs'), 'data' => $data_work_order, 'color' => '#84c529'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @param  array
     * @return array
     */
    public function get_data_total_cost_trend_chart()
    {
        $fuel_history = $this->db->get(db_prefix() . 'fleet_fuel_history')->result_array();
        
        $data_costs = [];

        foreach($fuel_history as $history){
            $date = date('Y-m-d', strtotime($history['fuel_time']));
            
            $key = strtotime($date. ' 00:00:00');
            if(isset($data_costs[$key])){
                $data_costs[$key] += (float)$history['price'];
            }else{
                $data_costs[$key] = (float)$history['price'];
            }
        }

        $work_orders = $this->db->get(db_prefix() . 'fleet_work_orders')->result_array();
        
        foreach($work_orders as $work_order){
            $date = $work_order['issue_date'];
            $key = strtotime($date. ' 00:00:00');

            if(isset($data_costs[$key])){
                $data_costs[$key] += (float)$work_order['total'];
            }else{
                $data_costs[$key] = (float)$work_order['total'];
            }
        }

        ksort($data_costs);

        $data_total_costs = [];
        foreach($data_costs as $date => $cost){
            $data_total_costs[] = [$date * 1000, (float)$cost];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('total_cost'), 'data' => $data_total_costs, 'color' => '#008ece'];
        
        return $data_return;
    }

    /**
     * [vehicle_operating_cost_summary description]
     * @return [type] [description]
     */
    public function expense_summary_by_vehicle_group($vehicle_group_id = ''){
        if($vehicle_group_id != ''){
            $this->db->where('vehicle_group_id', $vehicle_group_id);
        }

        $vehicles = $this->db->get(db_prefix() . 'fleet_vehicles')->result_array();

        $total_vehicle = count($vehicles);

        $total_cost = 0;
        $vehicle_ids = '';
        foreach($vehicles as $vehicle){
            $operating_cost_summary = $this->vehicle_operating_cost_summary($vehicle['id']);
            $total_cost += $operating_cost_summary['total_cost'];

            if($vehicle_ids == ''){
                $vehicle_ids = $vehicle['id'];
            }else{
                $vehicle_ids .= ','.$vehicle['id'];
            }
        }

        $total_transaction = 0;
        if($vehicle_ids != ''){
            $this->db->where('vehicle_id in ('. $vehicle_ids.')');
            $fuel_history = $this->db->get(db_prefix() . 'fleet_fuel_history')->result_array();
            $total_transaction += count($fuel_history);
            $this->db->where('vehicle_id in ('. $vehicle_ids.')');
            $work_orders = $this->db->get(db_prefix() . 'fleet_work_orders')->result_array();
            $total_transaction += count($work_orders);
        }


        return ['total_transaction' => $total_transaction, 'total_vehicle' => $total_vehicle, 'total_cost' => $total_cost];
    }

    /**
     * [status_summary_by_vehicle description]
     * @param  string $vehicle_group_id [description]
     * @return [type]                   [description]
     */
    public function status_summary_by_vehicle($vehicle_id = ''){

        $this->db->where('type', 'status_change');

        if($vehicle_id != ''){
            $this->db->where('vehicle_id', $vehicle_id);
        }

        $this->db->order_by('datecreated', 'desc');
        $vehicle_histories = $this->db->get(db_prefix() . 'fleet_vehicle_histories')->result_array();

        $total_change = count($vehicle_histories);

        $data_return = [];
        $data_return['total_change'] = $total_change;
        $data_return['active'] = '';
        $data_return['inactive'] = '';
        $data_return['in_shop'] = '';
        $data_return['out_of_service'] = '';
        $data_return['sold'] = '';

        foreach ($vehicle_histories as $key => $history) {
            switch ($history['to_value']) {
                case _l('active'):
                        if($data_return['active'] == ''){
                            $data_return['active'] = time_ago($history['datecreated']);
                        }
                    break;
                case _l('inactive'):
                        if($data_return['inactive'] == ''){
                            $data_return['inactive'] = time_ago($history['datecreated']);
                        }
                    break;
                case _l('in_shop'):
                        if($data_return['in_shop'] == ''){
                            $data_return['in_shop'] = time_ago($history['datecreated']);
                        }
                    break;
                case _l('out_of_service'):
                        if($data_return['out_of_service'] == ''){
                            $data_return['out_of_service'] = time_ago($history['datecreated']);
                        }
                    break;
                case _l('sold'):
                        if($data_return['sold'] == ''){
                            $data_return['sold'] = time_ago($history['datecreated']);
                        }
                    break;
                
                default:
                    // code...
                    break;
            }
        }



        return $data_return;
    }

    /**
     * [assignment_summary_by_vehicle description]
     * @param  string $vehicle_id [description]
     * @return [type]             [description]
     */
    public function assignment_summary_by_vehicle($vehicle_id, $from_date, $to_date){
        $interval = date_diff(date_create($from_date), date_create($to_date));

        $days = $interval->format('%a') + 1;
        
        $dateWhere = '';

        if ($from_date != '' && $to_date != '') {
            $dateWhere = 'IF(date_format(end_time, \'%Y-%m-%d\') IS NOT NULL,((date_format(start_time, \'%Y-%m-%d\') <= "' . $from_date . '" and date_format(end_time, \'%Y-%m-%d\') >= "' . $from_date . '") or (date_format(start_time, \'%Y-%m-%d\') <= "' . $to_date . '" and date_format(end_time, \'%Y-%m-%d\') >= "' . $to_date . '") or (date_format(start_time, \'%Y-%m-%d\') > "' . $from_date . '" and date_format(end_time, \'%Y-%m-%d\') < "' . $to_date . '")), (date_format(start_time, \'%Y-%m-%d\') >= "' . $from_date . '" and date_format(start_time, \'%Y-%m-%d\') <= "' . $to_date . '"))';
        } elseif ($from_date != '') {
            $dateWhere = '(date_format(start_time, \'%Y-%m-%d\') >= "' . $from_date . '" or IF(date_format(end_time, \'%Y-%m-%d\') IS NOT NULL, date_format(end_time, \'%Y-%m-%d\') >= "' . $from_date . '", 1=0)';
        } elseif ($to_date != '') {
            $dateWhere = '(date_format(start_time, \'%Y-%m-%d\') <= "' . $to_date . '" or IF(date_format(end_time, \'%Y-%m-%d\') IS NOT NULL,date_format(end_time, \'%Y-%m-%d\') <= "' . $to_date . '", 1=0))';
        }

        if ($dateWhere != '') {
            $this->db->where($dateWhere);
        }

        if($vehicle_id != ''){
            $this->db->where('vehicle_id', $vehicle_id);
        }

        $vehicle_assignments = $this->db->get(db_prefix() . 'fleet_vehicle_assignments')->result_array();
        $assigned_days = 0;
        foreach ($vehicle_assignments as $key => $value) {
            $start_time = date('Y-m-d', strtotime($value['start_time']));
            $end_time = date('Y-m-d', strtotime($value['end_time']));

            if($value['end_time'] == null || strtotime($value['end_time']) > strtotime($to_date)){
                $end_time = $to_date;
            }

            if(strtotime($value['start_time']) < strtotime($from_date)){
                $start_time = $from_date;
            }

            $interval = date_diff(date_create($start_time), date_create($end_time));
            $assigned_days += $interval->format('%a') + 1;
        }

        $assigned = ($assigned_days/$days) * 100;
        $assigned = $assigned <= 100 ? $assigned : 100;

        $operator_list = [];
        $assignments = count($vehicle_assignments);

        foreach ($vehicle_assignments as $key => $value) {
            if(!in_array($value['driver_id'],$operator_list)){
                $operator_list[] = $value['driver_id'];
            }
        }
        $operators = count($operator_list);
        return ['assignments' => $assignments, 'operators' => $operators, 'assigned' => number_format($assigned,2)];
    }

    public function get_data_vehicle_assignment_summary_chart($data_filter){

        $from_date = date('Y-m-01');
        $to_date = date('Y-m-d');

        if(isset($data_filter['from_date'])){
            $from_date = to_sql_date($data_filter['from_date']);
        }

        if(isset($data_filter['to_date'])){
            $to_date = to_sql_date($data_filter['to_date']);
        }

        $vehicles = $this->get_vehicle();

        $data_return = [];

        $count_100 = 0;
        $count_80_99 = 0;
        $count_60_79 = 0;
        $count_40_59 = 0;
        $count_20_39 = 0;
        $count_1_19 = 0;
        $count_0 = 0;

        foreach ($vehicles as $key => $vehicle) {
            $summary = $this->assignment_summary_by_vehicle($vehicle['id'], $from_date, $to_date);

            if ($summary['assigned'] == 100) {
                $count_100 ++;
            } elseif ($summary['assigned'] >= 80) {
                $count_80_99++;
            } elseif ($summary['assigned'] >= 60) {
                $count_60_79++;
            } elseif ($summary['assigned'] >= 40) {
                $count_40_59++;
            } elseif ($summary['assigned'] >= 20) {
                $count_20_39++;
            } elseif ($summary['assigned'] >= 1) {
                $count_1_19++;
            } else{
                $count_0++;
            }
        }

        $data_return[] = $count_100;
        $data_return[] = $count_80_99;
        $data_return[] = $count_60_79;
        $data_return[] = $count_40_59;
        $data_return[] = $count_20_39;
        $data_return[] = $count_1_19;
        $data_return[] = $count_0;

        return $data_return;
    }

    /**
     * @param  integer
     * @param  array
     * @return array
     */
    public function get_data_inspection_submissions_list_chart()
    {
        $inspections = $this->db->get(db_prefix() . 'fleet_inspections')->result_array();
        
        $data_costs = [];

        foreach($inspections as $history){
            $date = date('Y-m-d', strtotime($history['datecreated']));
            
            $key = strtotime($date. ' 00:00:00');
            if(isset($data_costs[$key])){
                $data_costs[$key] += 1;
            }else{
                $data_costs[$key] = 1;
            }
        }

        ksort($data_costs);

        $data_total_costs = [];
        foreach($data_costs as $date => $cost){
            $data_total_costs[] = [$date * 1000, (float)$cost];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('count'), 'data' => $data_total_costs, 'color' => '#008ece'];
        
        return $data_return;
    }

    /**
     * [vehicle_operating_cost_summary description]
     * @return [type] [description]
     */
    public function inspections_summary_by_vehicle($vehicle_id = ''){
        if($vehicle_id != ''){
            $this->db->where('vehicle_id', $vehicle_id);
        }

        $inspections = $this->db->get(db_prefix() . 'fleet_inspections')->result_array();

        $submission_count = count($inspections);

        $form_ids = [];
        foreach($inspections as $inspection){
            if(!in_array($inspection['inspection_form_id'], $form_ids)){
                $form_ids[] = $inspection['inspection_form_id'];
            }
        }

        return ['submission_count' => $submission_count, 'forms_count' => count($form_ids)];
    }

    /**
     * [fuel_summary_by_vehicle description]
     * @return [type] [description]
     */
    public function fuel_summary_by_vehicle($vehicle_id = ''){
        $consumption = $this->calculating_fuel_consumption($vehicle_id);
        
        $this->db->select_sum('price');
        if($vehicle_id != ''){
            $this->db->where('vehicle_id', $vehicle_id);
        }

        $fuel_costs = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->price;
        $economy = $consumption['total_gallons'] != 0 ? round(($consumption['total_km']/$consumption['total_gallons']), 2) : 0;
        $cost_gallons = $consumption['total_km'] != 0 ? round(($fuel_costs/$consumption['total_km']), 2) : 0;
        return ['usage' => $consumption['total_km'], 'gallons' => $consumption['total_gallons'], 'economy' => $economy, 'total_fuel_cost' => $fuel_costs, 'cost_gallons' => $cost_gallons];
    }

    /**
     * [get_fuel_summary description]
     * @return [type] [description]
     */
    public function get_fuel_summary(){
        $vehicles = $this->get_vehicle();


        $data_return = [];
        $data_return['usage'] = 0;
        $data_return['gallons'] = 0;
        $data_return['total_fuel_cost'] = 0;
        $data_return['economy'] = 0;
        $data_return['cost_gallons'] = 0;

        $economy = 0;
        $count = 0;

        $cost_gallons = 0;
        foreach($vehicles as $vehicle){
            $fuel_summary_by_vehicle = $this->fuel_summary_by_vehicle($vehicle['id']);

            $data_return['usage'] += $fuel_summary_by_vehicle['usage'];
            $data_return['gallons'] += $fuel_summary_by_vehicle['gallons'];
            $data_return['total_fuel_cost'] += $fuel_summary_by_vehicle['total_fuel_cost'];
            $economy += $fuel_summary_by_vehicle['economy'];
            $cost_gallons += $fuel_summary_by_vehicle['cost_gallons'];
            $count++;
        }

        if($count == 0){
            $count = 1;
        }
        
        $data_return['economy'] = round(($economy/$count), 2);
        $data_return['cost_gallons'] = round(($cost_gallons/$count), 2);

        return $data_return;
    }

    public function get_data_inspection_submissions_summary_chart(){
        $inspections = $this->db->get(db_prefix() . 'fleet_inspections')->result_array();

        $data_return = [];
        $data = [];

        foreach ($inspections as $key => $inspection) {
            if(isset($data[$inspection['addedfrom']])){
                $data[$inspection['addedfrom']] += 1;
            }else{
                $data[$inspection['addedfrom']] = 1;
            }
        }

        foreach ($data as $key => $value) {
            $data_return['categories'][] = get_staff_full_name($key);
            $data_return['data'][] = $value;
        }

        return $data_return;
    }

    public function fuel_summary_by_location(){
        $fuel_history = $this->db->get(db_prefix().'fleet_fuel_history')->result_array();

        $data_return = [];
        foreach ($fuel_history as $key => $value) {
            if($value['vendor_id'] != 0){
                $vendor = $this->get_vendor($value['vendor_id']);

                if($vendor && $vendor->state != ''){
                    if(isset($data_return[$vendor->state])){
                        $data_return[$vendor->state]['transactions']++;
                        $data_return[$vendor->state]['gallons'] += $value['gallons'];
                        $data_return[$vendor->state]['cost'] += $value['price'];
                    }else{
                        $data_return[$vendor->state] = [];
                        $data_return[$vendor->state]['transactions'] = 1;
                        $data_return[$vendor->state]['gallons'] = $value['gallons'];
                        $data_return[$vendor->state]['cost'] = $value['price'];
                    }
                }else{
                    if(isset($data_return['Unknown'])){
                        $data_return['Unknown']['transactions']++;
                        $data_return['Unknown']['gallons'] += $value['gallons'];
                        $data_return['Unknown']['cost'] += $value['price'];
                    }else{
                        $data_return['Unknown'] = [];
                        $data_return['Unknown']['transactions'] = 1;
                        $data_return['Unknown']['gallons'] = $value['gallons'];
                        $data_return['Unknown']['cost'] = $value['price'];
                    }
                }
            }else{
                if(isset($data_return['Unknown'])){
                    $data_return['Unknown']['transactions']++;
                    $data_return['Unknown']['gallons'] += $value['gallons'];
                    $data_return['Unknown']['cost'] += $value['price'];
                }else{
                    $data_return['Unknown'] = [];
                    $data_return['Unknown']['transactions'] = 1;
                    $data_return['Unknown']['gallons'] = $value['gallons'];
                    $data_return['Unknown']['cost'] = $value['price'];
                }
            }
        }

        return $data_return;
    }

    /**
     * [utilization_summary_by_vehicle description]
     * @return [type] [description]
     */
    public function utilization_summary_by_vehicle($vehicle_id = ''){
        $odometer_min = 0;
        $vehicle = $this->get_vehicle($vehicle_id);
        if($vehicle && $vehicle->odometer != null){
            $odometer_min = $vehicle->odometer;
        }else{
            $this->db->select_min('odometer');
            $this->db->where('vehicle_id', $vehicle_id);
            $odometer_min = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->odometer;
        }

        if($odometer_min < 0 || $odometer_min == null){
            $odometer_min = 0;
        }

        $this->db->select_max('odometer');
        $this->db->where('vehicle_id', $vehicle_id);
        $odometer_max = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->odometer;

        $this->db->select_sum('gallons');
        $this->db->where('vehicle_id', $vehicle_id);
        $total_gallons = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->gallons;

        if($odometer_min == $odometer_max){
            $total_km = (float)$odometer_max;
        }else{
            $total_km = (float)$odometer_max - (float)$odometer_min;
        }
        
        $this->db->select_min('fuel_time');
        $this->db->where('vehicle_id', $vehicle_id);
        $fuel_time_min = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->fuel_time;
        $min_date = $fuel_time_min != null ? date('Y-m-d', strtotime($fuel_time_min)) : '';

        $this->db->select_max('fuel_time');
        $this->db->where('vehicle_id', $vehicle_id);
        $fuel_time_max = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->fuel_time;
        $max_date = $fuel_time_max != null ? date('Y-m-d', strtotime($fuel_time_max)) : '';

        $interval = date_diff(date_create($min_date), date_create($max_date));
        $days = $interval->format('%a') + 1;

        $avg_day = $total_km/$days;
        return ['min_value' => (float)$odometer_min, 'max_value' => (float)$odometer_max, 'min_date' => $min_date, 'max_date' => $max_date, 'usage' => $total_km, 'avg_day' => round($avg_day, 2)];
    }

    public function fleet_inspection_schedule(){
        $this->db->from(db_prefix() . 'fleet_inspections');
        $this->db->where('recurring !=', 0);
        $this->db->where('(cycles != total_cycles OR cycles=0)');
        $inspections = $this->db->get()->result_array();

        foreach ($inspections as $inspection) {

            // Current date
            $date = new DateTime(date('Y-m-d'));
            // Check if is first recurring
            if (!$inspection['last_recurring_date']) {
                $last_recurring_date = date('Y-m-d', strtotime($inspection['datecreated']));
            } else {
                $last_recurring_date = date('Y-m-d', strtotime($inspection['last_recurring_date']));
            }
            if ($inspection['custom_recurring'] == 0) {
                $inspection['recurring_type'] = 'MONTH';
            }

            $re_create_at = date('Y-m-d', strtotime('+' . $inspection['recurring'] . ' ' . strtoupper($inspection['recurring_type']), strtotime($last_recurring_date)));

            if (date('Y-m-d') >= $re_create_at) {

                // Recurring invoice date is okey lets convert it to new invoice
                $new_inspection_data                     = $inspection;
                unset($new_inspection_data['id']);
                $new_inspection_data['datecreated']     = _d($re_create_at);
               
                $new_inspection_data['is_recurring_from']     = $inspection['id'];
                $new_inspection_data['recurring']     = 0;
                $new_inspection_data['recurring_type']     = NULL;
                $new_inspection_data['custom_recurring']     = 0;
                $new_inspection_data['cycles']     = 0;
                $new_inspection_data['total_cycles']     = 0;
                $new_inspection_data['last_recurring_date']     = NULL;
                
                $id = $this->add_inspection($new_inspection_data, 1);
                if ($id) {
                    // Update last recurring date to this invoice
                    $this->db->where('id', $inspection['id']);
                    $this->db->update(db_prefix() . 'fleet_inspections', [
                        'last_recurring_date' => $re_create_at,
                    ]);

                    $this->db->where('id', $inspection['id']);
                    $this->db->set('total_cycles', 'total_cycles+1', false);
                    $this->db->update(db_prefix() . 'fleet_inspections');
                }
            }
        }
    }

    public function get_data_cost_meter_trend_chart($data_filter){

        $from_date = date('Y-m-01');
        $to_date = date('Y-m-d');

        if(isset($data_filter['from_date'])){
            $from_date = to_sql_date($data_filter['from_date']);
        }

        if(isset($data_filter['to_date'])){
            $to_date = to_sql_date($data_filter['to_date']);
        }

        $vehicles = $this->get_vehicle();

        $data_return = [];
        $row = [];
        foreach ($vehicles as $key => $vehicle) {
            $start = $month = strtotime($from_date);
            $end = strtotime($to_date);

            while($month < $end)
            {
                $this->db->select('sum(price) as price, MIN(odometer) as odometer_min, MAX(odometer) as odometer_max');
                $this->db->where('vehicle_id', $vehicle['id']);
                $this->db->where('(month(fuel_time) = "' . date('m',$month) . '" and year(fuel_time) = "' . date('Y',$month) . '")');
                $fuel_history = $this->db->get(db_prefix().'fleet_fuel_history')->row();

                $price = $fuel_history->price != '' ? $fuel_history->price : 0;
                $odometer_min = $fuel_history->odometer_min != '' ? $fuel_history->odometer_min : 0;
                $odometer_max = $fuel_history->odometer_max != '' ? $fuel_history->odometer_max : 0;

                if($odometer_min == $odometer_max){
                    $this->db->select_max('odometer');
                    $this->db->where('vehicle_id', $vehicle['id']);
                    $this->db->where('(month(fuel_time) = "' . (date('m',$month) - 1) . '" and year(fuel_time) = "' . date('Y',$month) . '")');
                    $odometer_min = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->odometer;
                    
                    if($odometer_min < 0 || $odometer_min == null){
                        $odometer_min = 0;
                    }
                }

                $usage = $odometer_max - $odometer_min;

                if(isset($row[date('Y-m-01', $month)])){
                    $row[date('Y-m-01', $month)] += $usage != 0 ? $price/$usage : 0;
                }else{
                    $row[date('Y-m-01', $month)] = $usage != 0 ? $price/$usage : 0;
                }
               
                $month = strtotime("+1 month", $month);
            }
        }

        $data_delivery = [];
        foreach($row as $key => $download){
            $data_delivery[] = [strtotime($key.' 00:00:00') * 1000, round((float)$download, 2)];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('cost_kilometer'), 'data' => $data_delivery, 'color' => '#008ece'];


        return $data_return;
    }

    public function cost_meter_trend_by_vehicle($vehicle_id, $from_date, $to_date){


        $data_return = [];

        $start = $month = strtotime($from_date);
        $end = strtotime($to_date);

        while($month < $end)
        {
            $this->db->select('sum(price) as price, MIN(odometer) as odometer_min, MAX(odometer) as odometer_max');
            $this->db->where('vehicle_id', $vehicle_id);
            $this->db->where('(month(fuel_time) = "' . date('m',$month) . '" and year(fuel_time) = "' . date('Y',$month) . '")');
            $fuel_history = $this->db->get(db_prefix().'fleet_fuel_history')->row();
            $price = $fuel_history->price != '' ? $fuel_history->price : 0;
            $odometer_min = $fuel_history->odometer_min != '' ? $fuel_history->odometer_min : 0;
            $odometer_max = $fuel_history->odometer_max != '' ? $fuel_history->odometer_max : 0;

            if($odometer_min == $odometer_max){
                $this->db->select_max('odometer');
                $this->db->where('vehicle_id', $vehicle_id);
                $this->db->where('(month(fuel_time) = "' . (date('m',$month) - 1) . '" and year(fuel_time) = "' . date('Y',$month) . '")');
                $odometer_min = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->odometer;

                if($odometer_min < 0 || $odometer_min == null){
                    $odometer_min = 0;
                }
            }

            $usage = $odometer_max - $odometer_min;

            if(isset($data_return[date('Y-m-01', $month)])){
                $data_return[date('Y-m-01', $month)] += $usage != 0 ? $price/$usage : 0;
            }else{
                $data_return[date('Y-m-01', $month)] = $usage != 0 ? $price/$usage : 0;
            }
           
            $month = strtotime("+1 month", $month);
        }

        return $data_return;
    }

    public function get_vehicle_current_meter($vehicle_id){

        $data_return = [];

        $this->db->select_max('odometer');
        $this->db->where('vehicle_id', $vehicle_id);
        $current_meter = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->odometer;

        if($current_meter < 0 || $current_meter == null){
            $vehicle = $this->get_vehicle($vehicle_id);

            $current_meter = $vehicle->odometer;
        }

        return $current_meter;
    }

    public function get_vehicle_current_meter_date($vehicle_id){

        $data_return = [];

        $this->db->select_max('fuel_time');
        $this->db->where('vehicle_id', $vehicle_id);
        $current_meter_date = $this->db->get(db_prefix() . 'fleet_fuel_history')->row()->fuel_time;

        return $current_meter_date;
    }

    public function get_vehicle_current_operator($vehicle_id){

        $data_return = [];
        $dateWhere = 'IF(date_format(end_time, \'%Y-%m-%d\') IS NOT NULL,((date_format(start_time, \'%Y-%m-%d\') <= "' . date('Y-m-d') . '" and date_format(end_time, \'%Y-%m-%d\') >= "' . date('Y-m-d') . '")), (date_format(start_time, \'%Y-%m-%d\') >= "' . date('Y-m-d') . '"))';

        $this->db->where('vehicle_id', $vehicle_id);
        $this->db->where($dateWhere);
        $vehicle_assignments = $this->db->get(db_prefix() . 'fleet_vehicle_assignments')->row();

        if($vehicle_assignments){
            return $vehicle_assignments->driver_id;
        }
        return 0;
    }
}
