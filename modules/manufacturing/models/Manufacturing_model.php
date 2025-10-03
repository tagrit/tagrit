<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Manufacturing model
 */
class Manufacturing_model extends App_Model
{


	/**
	 * get routings
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function get_routings($id = false)
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'mrp_routings')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from ' . db_prefix() . 'mrp_routings')->result_array();
		}
	}


	/**
	 * get work centers
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function get_work_centers($id = false)
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'mrp_work_centers')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from ' . db_prefix() . 'mrp_work_centers')->result_array();
		}
	}

	/**
	 * add work center
	 * @param [type] $data 
	 */
	public function add_work_center($data)
	{

		$this->db->insert(db_prefix().'mrp_work_centers',$data);
		$insert_id = $this->db->insert_id();

		if ($insert_id) {
			return $insert_id;
		}
		return false;
	}


	/**
	 * update work center
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function update_work_center($data, $id)
	{
		$affected_rows=0;
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'mrp_work_centers', $data);
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		if($affected_rows > 0){
			return true;
		}
		return false;   
	}
	
	/**
	 * delete work center
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_work_center($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'mrp_work_centers');

		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}


	/**
	 * get working hour
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_working_hour($id = fasle)
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			$working_hour = $this->db->get(db_prefix() . 'mrp_working_hours')->row();

			//get working detail
			$this->db->select('id, working_hour_id, working_hour_name, day_of_week, day_period, date_format(work_from, "%H:%i") as work_from , date_format(work_to, "%H:%i") as work_to, starting_date, end_date');
			$this->db->where('working_hour_id', $id);
			$working_hour_details = $this->db->get(db_prefix() . 'mrp_working_hour_times')->result_array();
			
			//get time off
			$this->db->where('working_hour_id', $id);
			$time_off = $this->db->get(db_prefix() . 'mrp_working_hour_time_off')->result_array();

			$result=[];
			$result['working_hour'] = $working_hour;
			$result['working_hour_details'] = $working_hour_details;
			$result['time_off'] = $time_off;

			return $result;
		}
		if ($id == false) {
			return $this->db->query('select * from ' . db_prefix() . 'mrp_working_hours')->result_array();
		}
	}


	/**
	 * get working hours
	 * @return [type] 
	 */
	public function get_working_hours()
	{
		return $this->db->query('select * from ' . db_prefix() . 'mrp_working_hours')->result_array();
	}


	/**
	 * delete working hour
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_working_hour($id)
	{	
		$affected_rows=0;

		$this->db->where('working_hour_id', $id);
		$this->db->delete(db_prefix() . 'mrp_working_hour_time_off');
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		$this->db->where('working_hour_id', $id);
		$this->db->delete(db_prefix() . 'mrp_working_hour_times');
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'mrp_working_hours');
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}
		
		if($affected_rows > 0){
			return true;
		}
		return false;
	}


	/**
	 * add working hour
	 * @param [type] $data 
	 */
	public function add_working_hour($data)
	{

		$affected_rows=0;

		if (isset($data['working_hour_hs'])) {
			$working_hour_hs = $data['working_hour_hs'];
			unset($data['working_hour_hs']);
		}

		if (isset($data['global_time_off_hs'])) {
			$global_time_off_hs = $data['global_time_off_hs'];
			unset($data['global_time_off_hs']);
		}

		$this->db->insert(db_prefix() . 'mrp_working_hours', $data);
		$insert_id = $this->db->insert_id();


		//create working hours data
		if(isset($working_hour_hs)){
			$working_hour_detail = json_decode($working_hour_hs);

			$es_detail = [];
			$row = [];
			$header = [];

			$header[] = 'id';
			$header[] = 'working_hour_id';
			$header[] = 'working_hour_name';
			$header[] = 'day_of_week';
			$header[] = 'day_period';
			$header[] = 'work_from';
			$header[] = 'work_to';
			$header[] = 'starting_date';
			$header[] = 'end_date';

			foreach ($working_hour_detail as $key => $value) {
				if($value[2] != ''){
					$es_detail[] = array_combine($header, $value);
				}
			}
		}

		//create global time off
		if(isset($global_time_off_hs)){
			$global_time_off_detail = json_decode($global_time_off_hs);

			$time_off_detail = [];
			$row = [];
			$header = [];

			$header[] = 'id';
			$header[] = 'working_hour_id';
			$header[] = 'reason';
			$header[] = 'starting_date';
			$header[] = 'end_date';

			foreach ($global_time_off_detail as $key => $value) {
				if($value[2] != ''){
					$time_off_detail[] = array_combine($header, $value);
				}
			}
		}


		if (isset($insert_id)) {
			$affected_rows++;

			/*insert working_hour_id*/
			foreach($es_detail as $key => $rqd){
				$es_detail[$key]['working_hour_id'] = $insert_id;
			}

			foreach($time_off_detail as $key => $time_off){
				$time_off_detail[$key]['working_hour_id'] = $insert_id;
			}

			if(count($es_detail) > 0){
				$insert_working_hour = $this->db->insert_batch(db_prefix().'mrp_working_hour_times',$es_detail);
				if($insert_working_hour > 0){
					$affected_rows++;
				}
			}

			if(count($time_off_detail) > 0){
				$insert_time_off = $this->db->insert_batch(db_prefix().'mrp_working_hour_time_off',$time_off_detail);
				if($insert_time_off > 0){
					$affected_rows++;
				}
			}

		}

		if ($affected_rows > 0) {
			return $insert_id;
		}
		return false;

	}


	/**
	 * update working hour
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function update_working_hour($data, $id)
	{
		$affected_rows=0;

		if (isset($data['working_hour_hs'])) {
			$working_hour_hs = $data['working_hour_hs'];
			unset($data['working_hour_hs']);
		}

		if (isset($data['global_time_off_hs'])) {
			$global_time_off_hs = $data['global_time_off_hs'];
			unset($data['global_time_off_hs']);
		}

		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'mrp_working_hours', $data);

		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		//create working hours data
		if(isset($working_hour_hs)){
			$working_hour_detail = json_decode($working_hour_hs);

			$es_detail = [];
			$row = [];
			$header = [];

			$header[] = 'id';
			$header[] = 'working_hour_id';
			$header[] = 'working_hour_name';
			$header[] = 'day_of_week';
			$header[] = 'day_period';
			$header[] = 'work_from';
			$header[] = 'work_to';
			$header[] = 'starting_date';
			$header[] = 'end_date';

			foreach ($working_hour_detail as $key => $value) {
				if($value[2] != ''){
					$es_detail[] = array_combine($header, $value);
				}
			}
		}

		//create global time off
		if(isset($global_time_off_hs)){
			$global_time_off_detail = json_decode($global_time_off_hs);

			$time_off_detail = [];
			$row = [];
			$header = [];

			$header[] = 'id';
			$header[] = 'working_hour_id';
			$header[] = 'reason';
			$header[] = 'starting_date';
			$header[] = 'end_date';

			foreach ($global_time_off_detail as $key => $value) {
				if($value[2] != ''){
					$time_off_detail[] = array_combine($header, $value);
				}
			}
		}


		//handle working hours
		$row_working_hour_detail = [];
		$row_working_hour_detail['update'] = []; 
		$row_working_hour_detail['insert'] = []; 
		$row_working_hour_detail['delete'] = [];
		$total = [];


		foreach ($es_detail as $key => $value) {
			if($value['id'] != ''){
				$row_working_hour_detail['delete'][] = $value['id'];
				$row_working_hour_detail['update'][] = $value;
			}else{
				unset($value['id']);
				$value['working_hour_id'] = $id;
				$row_working_hour_detail['insert'][] = $value;
			}

		}

		if(empty($row_working_hour_detail['delete'])){
			$row_working_hour_detail['delete'] = ['0'];
		}
		$row_working_hour_detail['delete'] = implode(",",$row_working_hour_detail['delete']);
		$this->db->where('id NOT IN ('.$row_working_hour_detail['delete'] .') and working_hour_id ='.$id);
		$this->db->delete(db_prefix().'mrp_working_hour_times');
		if($this->db->affected_rows() > 0){
			$affected_rows++;
		}

		if(count($row_working_hour_detail['insert']) != 0){
			$this->db->insert_batch(db_prefix().'mrp_working_hour_times', $row_working_hour_detail['insert']);
			if($this->db->affected_rows() > 0){
				$affected_rows++;
			}
		}
		if(count($row_working_hour_detail['update']) != 0){
			$this->db->update_batch(db_prefix().'mrp_working_hour_times', $row_working_hour_detail['update'], 'id');
			if($this->db->affected_rows() > 0){
				$affected_rows++;
			}
		}

		//handle time off
		$row_time_off_detail = [];
		$row_time_off_detail['update'] = []; 
		$row_time_off_detail['insert'] = []; 
		$row_time_off_detail['delete'] = [];
		$total = [];


		foreach ($time_off_detail as $key => $value) {
			if($value['id'] != ''){
				$row_time_off_detail['delete'][] = $value['id'];
				$row_time_off_detail['update'][] = $value;
			}else{
				unset($value['id']);
				$value['working_hour_id'] = $id;
				$row_time_off_detail['insert'][] = $value;
			}

		}


		if(empty($row_time_off_detail['delete'])){
			$row_time_off_detail['delete'] = ['0'];
		}
		$row_time_off_detail['delete'] = implode(",",$row_time_off_detail['delete']);
		$this->db->where('id NOT IN ('.$row_time_off_detail['delete'] .') and working_hour_id ='.$id);
		$this->db->delete(db_prefix().'mrp_working_hour_time_off');
		if($this->db->affected_rows() > 0){
			$affected_rows++;
		}

		if(count($row_time_off_detail['insert']) != 0){
			$this->db->insert_batch(db_prefix().'mrp_working_hour_time_off', $row_time_off_detail['insert']);
			if($this->db->affected_rows() > 0){
				$affected_rows++;
			}
		}
		if(count($row_time_off_detail['update']) != 0){
			$this->db->update_batch(db_prefix().'mrp_working_hour_time_off', $row_time_off_detail['update'], 'id');
			if($this->db->affected_rows() > 0){
				$affected_rows++;
			}
		}


		if ($affected_rows > 0) {
			return true;
		}
		return false;

	}
	
	/**
	 * create code
	 * @param  [type] $rel_type 
	 * @return [type]           
	 */
	public function create_code($rel_type) {
		//rel_type: position_code, staff_contract, ...
		$str_result ='';

		$prefix_str ='';
		switch ($rel_type) {
			case 'bom_code':
				$prefix_str .= get_mrp_option('bom_prefix');
				$next_number = (int) get_mrp_option('bom_number');
				$str_result .= $prefix_str.str_pad($next_number,5,'0',STR_PAD_LEFT);
				break;

			case 'routing_code':
				$prefix_str .= get_mrp_option('routing_prefix');
				$next_number = (int) get_mrp_option('routing_number');
				$str_result .= $prefix_str.str_pad($next_number,5,'0',STR_PAD_LEFT);
				break;

			case 'mo_code':
				$prefix_str .= get_mrp_option('mo_prefix');
				$next_number = (int) get_mrp_option('mo_number');
				$str_result .= $prefix_str.str_pad($next_number,5,'0',STR_PAD_LEFT);
				break;
			
			default:
				# code...
				break;
		}

		return $str_result;

	}


	/**
	 * add routing
	 * @param [type] $data 
	 */
	public function add_routing($data)
	{
		$this->db->insert(db_prefix().'mrp_routings',$data);
		$insert_id = $this->db->insert_id();

		if ($insert_id) {
			/*update next number setting*/
			$this->update_prefix_number(['routing_number' =>  get_mrp_option('routing_number')+1]);

			return $insert_id;
		}
		return false;
	}

	/**
	 * update routing
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function update_routing($data, $id)
	{
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'mrp_routings', $data);
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		if($affected_rows > 0){
			return true;
		}
		return false;  
	}
	

	/**
	 * delete routing
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_routing($id)
	{	
		$affected_rows = 0;
		//get operations by routing id
		$operations = $this->get_operation('', $id);
		foreach ($operations as $value) {
			$delete_result = $this->delete_operation($value['id']);
			if($delete_result){
				$affected_rows++;
			}
		}

		//delete data
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'mrp_routings');
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		if($affected_rows > 0){
			return true;
		}
		return false;
	}


	/**
	 * get routing detail
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function get_operation($id=false, $routing_id = false)
	{
	    if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'mrp_routing_details')->row();
		}

		if($routing_id != false){
			$this->db->where('routing_id', $routing_id);
			$this->db->order_by('display_order', 'asc');

			return $this->db->get(db_prefix() . 'mrp_routing_details')->result_array();
		}

		if ($id == false) {
			return $this->db->query('select * from ' . db_prefix() . 'mrp_routing_details')->result_array();
		}
	}


	/**
	 * add operation
	 * @param [type] $data 
	 */
	public function add_operation($data)
	{

	    if($data['duration_computation'] == 'compute_based_on_real_time'){
	    	$data['default_duration'] = 0;
	    }elseif($data['duration_computation'] == 'set_duration_manually'){
	    	$data['based_on'] = 0;
	    }

	    if($data['start_next_operation'] == 'once_all_products_are_processed'){
	    	$data['quantity_process'] = 0;
	    }

	    if(isset($data['file'])){
	    	unset($data['file']);
	    }

	    $this->db->insert(db_prefix().'mrp_routing_details',$data);
		$insert_id = $this->db->insert_id();

		if ($insert_id) {
			return $insert_id;
		}
		return false;

	}


	/**
	 * update operation
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function update_operation($data, $id)
	{
		$affected_rows=0;

		if($data['duration_computation'] == 'compute_based_on_real_time'){
	    	$data['default_duration'] = 0;
	    }elseif($data['duration_computation'] == 'set_duration_manually'){
	    	$data['based_on'] = 0;
	    }

	    if($data['start_next_operation'] == 'once_all_products_are_processed'){
	    	$data['quantity_process'] = 0;
	    }

	    if(isset($data['file'])){
	    	unset($data['file']);
	    }


		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'mrp_routing_details', $data);
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		if($affected_rows > 0){
			return true;
		}
		return false;   
	}


	/**
	 * delete operation
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_operation($id)
	{	
		//delete attachment file
		$files = $this->mrp_get_attachments_file($id, 'mrp_operation');
		foreach ($files as $file_key => $file_value) {
			$this->delete_mrp_attachment_file($file_value['id'], MANUFACTURING_OPERATION_ATTACHMENTS_UPLOAD_FOLDER);
		}

		//delete data
	    $this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'mrp_routing_details');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}


	/**
	 * mrp get attachments file
	 * @param  [type] $rel_id   
	 * @param  [type] $rel_type 
	 * @return [type]           
	 */
	public function mrp_get_attachments_file($rel_id, $rel_type)
	{
		//rel_id = id, rel_type = 'mrp_operation'
		$this->db->order_by('dateadded', 'desc');
		$this->db->where('rel_id', $rel_id);
		$this->db->where('rel_type', $rel_type);

		return $this->db->get(db_prefix() . 'files')->result_array();

	}


	/**
	 * delete mrp attachment file
	 * @param  [type] $attachment_id 
	 * @param  [type] $folder_name   
	 * @return [type]                
	 */
	public function delete_mrp_attachment_file($attachment_id, $folder_name)
	{
		$deleted    = false;
		$attachment = $this->misc_model->get_file($attachment_id);
		if ($attachment) {
			if (empty($attachment->external)) {
				unlink($folder_name .$attachment->rel_id.'/'.$attachment->file_name);
			}
			$this->db->where('id', $attachment->id);
			$this->db->delete(db_prefix() . 'files');
			if ($this->db->affected_rows() > 0) {
				$deleted = true;
				log_activity('MRP Attachment Deleted [ID: ' . $attachment->rel_id . '] folder name: '.$folder_name);
			}

			if (is_dir($folder_name .$attachment->rel_id)) {
				// Check if no attachments left, so we can delete the folder also
				$other_attachments = list_files($folder_name .$attachment->rel_id);
				if (count($other_attachments) == 0) {
					// okey only index.html so we can delete the folder also
					delete_dir($folder_name .$attachment->rel_id);
				}
			}
		}

		return $deleted;
	}


	/**
	 * get unit categories
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function get_unit_categories($id = false)
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'mrp_unit_measure_categories')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from ' . db_prefix() . 'mrp_unit_measure_categories')->result_array();
		}
	}


	/**
	 * add unit categories
	 * @param [type] $data 
	 */
	public function add_unit_categories($data)
	{
		$this->db->insert(db_prefix().'mrp_unit_measure_categories',$data);
		$insert_id = $this->db->insert_id();

		if ($insert_id) {
			return $insert_id;
		}
		return false;
	}


	/**
	 * update unit categories
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function update_unit_categories($data, $id)
	{
	    $affected_rows=0;
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'mrp_unit_measure_categories', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return false;  
	}

	/**
	 * delete unit categories
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_unit_categories($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'mrp_unit_measure_categories');

		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}


	/**
	 * get unit of measure
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function get_unit_of_measure($id = false)
	{
		if (is_numeric($id)) {
			$this->db->where('unit_type_id', $id);
			return $this->db->get(db_prefix() . 'ware_unit_type')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from ' . db_prefix() . 'ware_unit_type')->result_array();
		}
	}


	/**
	 * add unit of measure
	 * @param [type] $data 
	 */
	public function add_unit_of_measure($data)
	{
		$data['unit_code'] = strtoupper(new_str_replace(" ", "_", mrp_convert_accented_characters($data['unit_name'])));
		$data['order'] = 1;

		if(!isset($data['display'])){
			$data['display'] = 0;
		}

		if($data['unit_measure_type'] == 'bigger'){
			$data['smaller_ratio'] = 0;
		}elseif($data['unit_measure_type'] == 'smaller'){
			$data['bigger_ratio'] = 0;
		}else{
			$data['smaller_ratio'] = 0;
			$data['bigger_ratio'] = 0;
		}

		$this->db->insert(db_prefix().'ware_unit_type',$data);
		$insert_id = $this->db->insert_id();

		if ($insert_id) {
			return $insert_id;
		}
		return false;
	}


	/**
	 * update unit of measure
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function update_unit_of_measure($data, $id)
	{
		$this->db->where('unit_type_id', $id);
		$this->db->update(db_prefix() . 'ware_unit_type', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return false;  
	}

	/**
	 * delete unit of measure
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_unit_of_measure($id)
	{
		$this->db->where('unit_type_id', $id);
		$this->db->delete(db_prefix() . 'ware_unit_type');

		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get commodity
	 * @param  boolean $id
	 * @return array or object
	 */
	public function get_product($id = false)
	{

		if (is_numeric($id)) {
			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'items')->row();
		}
		if ($id == false) {
			$sql_where = db_prefix().'items.id not in ( SELECT distinct parent_id from '.db_prefix().'items WHERE parent_id is not null AND parent_id != "0" )';
			$this->db->select('*, CONCAT(commodity_code, "_", description) as description');

			$this->db->where($sql_where);
			$this->db->where('can_be_manufacturing', 'can_be_manufacturing');
			return $this->db->get(db_prefix() . 'items')->result_array();
		}

	}


	/**
	 * get product by parent id
	 * @param  [type] $parent_id 
	 * @return [type]            
	 */
	public function get_product_by_parent_id($parent_id)
	{
		$this->db->where('parent_id', $parent_id);
		$items =  $this->db->get(db_prefix() . 'items')->result_array();
		return $items;
	}


	/**
	 * mrp get item group
	 * @return [type] 
	 */
	public function mrp_get_item_group()
	{
		return $this->db->query('select * from '.db_prefix().'items_groups where display = 1 order by '.db_prefix().'items_groups.order asc ')->result_array();
	}

	/**
	 * mrp get unit
	 * @return [type] 
	 */
	public function mrp_get_unit()
	{
		return $this->db->query('select * from '.db_prefix().'ware_unit_type where display = 1 order by '.db_prefix().'ware_unit_type.order asc ')->result_array();
	}


	/**
	 * check sku duplicate
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function check_sku_duplicate($data)
    {	
    	if(isset($data['item_id']) && $data['item_id'] != ''){
    	//check update
    		$this->db->where('sku_code', $data['sku_code']);
    		$this->db->where('id != ', $data['item_id']);

    		$items = $this->db->get(db_prefix() . 'items')->result_array();

    		if(count($items) > 0){
    			return false;
    		}
    		return true;

    	}elseif(isset($data['sku_code']) && $data['sku_code'] != ''){

    	//check insert
    		$this->db->where('sku_code', $data['sku_code']);
    		$items = $this->db->get(db_prefix() . 'items')->row();
    		if($items){
    			return false;
    		}
    		return true;
    	}

    	return true;
    }


    /**
     * add product
     * @param [type] $data 
     */
    public function add_product($formdata, $rel_type)
    {
    	$data=[];
    	
    	$arr_insert_cf=[];
    	$arr_variation=[];
    	$arr_attributes=[];
    	$arr_custom_fields=[];
    	$arr_variation_temp=[];
    	$variation_name_temp='';
    	$variation_option_temp='';
    	$data['can_be_sold'] = '';
    	$data['can_be_purchased'] = '';
    	$data['can_be_manufacturing'] = '';

    	foreach ($formdata['formdata'] as $key => $value) {
    		if(preg_match('/^custom_fields/', $value['name'])){
    			$index =  new_str_replace('custom_fields[items][', '', $value['name']);
    			$index =  new_str_replace(']', '', $index);

    			$arr_custom_fields[$index] = $value['value'];

    		}elseif(preg_match('/^name/', $value['name'])){
    			$variation_name_temp = $value['value'];
    		}elseif(preg_match('/^options/', $value['name'])){
    			$variation_option_temp = $value['value'];

    			array_push($arr_variation, [
    				'name' => $variation_name_temp,
    				'options' => new_explode(',', $variation_option_temp),
    			]);

    			$variation_name_temp='';
    			$variation_option_temp='';
    		}elseif(preg_match("/^variation_names_/", $value['name'] )){
    			array_push($arr_attributes, [
    				'name' => new_str_replace('variation_names_', '', $value['name']),
    				'option' => $value['value'],
    			]);
    		}elseif($value['name'] == 'supplier_taxes_id[]'){
				if(isset($data['supplier_taxes_id'])){
    				$data['supplier_taxes_id'] .= ','.$value['value'];
    			}else{
    				$data['supplier_taxes_id'] = $value['value'];
    			}

    		}elseif( $value['name'] != 'csrf_token_name' && $value['name'] != 'id'){
    			$data[$value['name']] = $value['value'];
    		}

    	}

    	$arr_insert_cf['items_pr'] = $arr_custom_fields;

		//get attribute
    	if(count($arr_attributes) > 0){
    		$data['attributes'] = json_encode($arr_attributes);
    	}else{
    		$data['attributes'] = null;
    	}

    	if(count($arr_variation) > 0){
    		$data['parent_attributes'] = json_encode($arr_variation);
    	}else{
    		$data['parent_attributes'] = null;
    	}

    	//generate sku_code
    	if($data['sku_code'] == ''){

    		$sql_where = 'SELECT * FROM ' . db_prefix() . 'items order by id desc limit 1';
    		$res = $this->db->query($sql_where)->row();
    		$last_commodity_id = 0;
    		if (isset($res)) {
    			$last_commodity_id = $this->db->query($sql_where)->row()->id;
    		}
    		$next_commodity_id = (int) $last_commodity_id + 1;

    		$sku_code = str_pad($next_commodity_id,5,'0',STR_PAD_LEFT); 
    		$data['commodity_code'] = $sku_code;
    		$data['sku_code'] = $sku_code;

    	}else{
    		$data['commodity_code'] =  $data['sku_code'];
    	}

    	$this->db->insert(db_prefix() . 'items', $data);
    	$insert_id = $this->db->insert_id();

    	if ($insert_id) {
    		if(count($arr_insert_cf) > 0){
    			handle_custom_fields_post($insert_id, $arr_insert_cf, true);
    		}

    		//create variant
    		$add_variant=false;
    		if($rel_type == 'product'){
    			if(count($arr_variation) > 0 &&  new_strlen(json_encode($arr_variation)) > 28){
    				$response_create_variant = $this->create_variant_product($insert_id, $data, $arr_variation);

    				if($response_create_variant){
    					$add_variant = true;
    				}
    			}
    		}

    		hooks()->do_action('item_created', $insert_id);
    		log_activity('New Manufacuring Item Added [ID:' . $insert_id . ', ' . $data['description'] . ']');

    		return ['insert_id' => $insert_id, 'add_variant' => $add_variant];
    	}

    	return false;
    }


    /**
     * update product
     * @param  [type] $data 
     * @param  [type] $id   
     * @return [type]       
     */
    public function update_product($formdata, $id, $rel_type)
    {
    	$affected_rows = 0;
    	$data=[];
    	
    	$arr_insert_cf=[];
    	$arr_variation=[];
    	$arr_attributes=[];
    	$arr_custom_fields=[];
    	$arr_variation_temp=[];
    	$variation_name_temp='';
    	$variation_option_temp='';
    	$data['can_be_sold'] = '';
    	$data['can_be_purchased'] = '';
    	$data['can_be_manufacturing'] = '';

    	foreach ($formdata['formdata'] as $key => $value) {
    		if(preg_match('/^custom_fields/', $value['name'])){
    			$index =  new_str_replace('custom_fields[items][', '', $value['name']);
    			$index =  new_str_replace(']', '', $index);

    			$arr_custom_fields[$index] = $value['value'];

    		}elseif(preg_match('/^name/', $value['name'])){
    			$variation_name_temp = $value['value'];
    		}elseif(preg_match('/^options/', $value['name'])){
    			$variation_option_temp = $value['value'];

    			array_push($arr_variation, [
    				'name' => $variation_name_temp,
    				'options' => new_explode(',', $variation_option_temp),
    			]);

    			$variation_name_temp='';
    			$variation_option_temp='';
    		}elseif(preg_match("/^variation_names_/", $value['name'] )){
    			array_push($arr_attributes, [
    				'name' => new_str_replace('variation_names_', '', $value['name']),
    				'option' => $value['value'],
    			]);
    		}elseif($value['name'] == 'supplier_taxes_id[]'){
    			if(isset($data['supplier_taxes_id'])){
    				$data['supplier_taxes_id'] .= ','.$value['value'];
    			}else{
    				$data['supplier_taxes_id'] = $value['value'];
    			}

    		}elseif( $value['name'] != 'csrf_token_name' && $value['name'] != 'id'){
    			$data[$value['name']] = $value['value'];
    		}

    	}

    	$arr_insert_cf['items_pr'] = $arr_custom_fields;

		//get attribute
    	if(count($arr_attributes) > 0){
    		$data['attributes'] = json_encode($arr_attributes);
    	}else{
    		$data['attributes'] = null;
    	}

    	if(count($arr_variation) > 0){
    		$data['parent_attributes'] = json_encode($arr_variation);
    	}else{
    		$data['parent_attributes'] = null;
    	}

    	/*handle custom fields*/

    	if(count($arr_insert_cf) > 0){
    		$data_insert_cf = [];
    		handle_custom_fields_post($id, $arr_insert_cf, true);
    	}

    	$this->db->where('id', $id);
    	$this->db->update(db_prefix() . 'items', $data);
    	if ($this->db->affected_rows() > 0) {
    		$affected_rows++;
    	}
			//create, inactive variant product when update
		if($rel_type == 'product'){
			if(count($arr_variation) > 0 &&  new_strlen(json_encode($arr_variation)) > 28){
				$response_create_variant = $this->update_variant_product($id, $arr_variation);
				if($response_create_variant){
					$affected_rows++;
				}
			}else{
				//update all product to inactive
				$this->db->where('parent_id', $id);
				$this->db->update(db_prefix().'items', ['active' => 0]);
				if ($this->db->affected_rows() > 0) {
					$affected_rows++;
				}
			}
		}

		if($affected_rows > 0){
    		return true;
		}
    	return false;
    }


    /**
     * delete product
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_product($id, $rel_type)
    {

    	hooks()->do_action('delete_item_on_woocommerce', $id);

    	/*delete file attachment*/
    	$array_file= $this->mrp_get_attachments_file($id, 'commodity_item_file');
    	if(count($array_file) > 0 ){
    		foreach ($array_file as $key => $file_value) {
    			$this->delete_mrp_attachment_file($file_value['id'], MANUFACTURING_PRODUCT_UPLOAD);
    		}
    	}

    	$this->db->where('id', $id);
    	$this->db->delete(db_prefix() . 'items');
    	if ($this->db->affected_rows() > 0) {

    		return true;
    	}
    	return false;

    }


    /**
     * create variant product
     * @param  [type] $parent_id 
     * @param  [type] $data      
     * @return [type]            
     */
    public function create_variant_product($parent_id, $data, $variant)
    {	
   
    	//get last product id
    	$sql_where = 'SELECT * FROM ' . db_prefix() . 'items order by id desc limit 1';
    	$res = $this->db->query($sql_where)->row();
    	$last_commodity_id = 0;
    	if (isset($res)) {
    		$last_commodity_id = $this->db->query($sql_where)->row()->id;
    	}
    	$next_commodity_id = (int) $last_commodity_id + 1;

    	$generate_variants = $this->variant_generator($variant);
    	$varirant_data=[];

    	$description = $data['description'];
        foreach ($generate_variants as $_variant) {

        	$str_variant='';

        	if(count($variant) > 1){
        		foreach ($_variant as $value) {
        			if(new_strlen($str_variant) == 0){
        				$str_variant .= $value['option'];
        			}else{
        				$str_variant .= '-'.$value['option'];
        			}
        		}
        	}else{
        		if(new_strlen($str_variant) == 0){
        			$str_variant .= $_variant['option'];
        		}else{
        			$str_variant .= '-'.$_variant['option'];
        		}
        	}

        	$str_variant = new_str_replace(' ', '_', $str_variant);
        	$barcode_gen = mrp_generate_commodity_barcode();

        	//create sku code
    		$sku_code = str_pad($next_commodity_id,5,'0',STR_PAD_LEFT);
    		$next_commodity_id++; 
    		$data['commodity_code'] = $sku_code;
    		$data['sku_code'] = $sku_code;

    		$data['commodity_barcode'] = $barcode_gen;
    		$data['commodity_code'] = $sku_code;
    		$data['sku_code'] = $sku_code;
    		$data['parent_id'] = $parent_id;
    		$data['parent_attributes'] = null;

    		if(count($variant) > 1){
    			$data['attributes'] = json_encode($_variant);
    		}else{
    			$data['attributes'] = json_encode(array($_variant));
    		}

    		$data['description'] = $description.' '. $str_variant;

    		$varirant_data[] = $data;
        
        }
        if(count($varirant_data) != 0){
			$affected_rows = $this->db->insert_batch(db_prefix().'items', $varirant_data);
			if($affected_rows > 0){
				return true;
			}
			return false;
		}
		return false;
    }


    /**
     * variant generator
     * @param  [type]  $variants 
     * @param  integer $i        
     * @return [type]            
     */
    public function variant_generator($variants, $i = 0)
    {
    	if (!isset($variants[$i]['options'])) {
    		return array();
    	}
    	if ($i == count($variants) - 1) {
    		
    		$last_arr=[];
    		foreach ($variants[$i]['options'] as $value) {
    			$last_arr[]=[
    				'name' => $variants[$i]['name'],
    				'option' => $value,
    			];
    		}
    		return $last_arr;
    	}

    	// get combinations from subsequent variants
    	$tmp = $this->variant_generator($variants, $i + 1);

    	$result = array();
    	// concat each array from tmp with each element from $variants[$i]
    	foreach ($variants[$i]['options'] as $v) {	//pre end
    		
    		foreach ($tmp as $t) { //end
    			$tem=[];
    			$tem=[
    				'name' => $variants[$i]['name'],
    				'option' => $v,
    			];

    			if($i <= (count($variants) - 3)){
    				$result[] = array_merge( array($tem), array_values($t));
    			}else{
    				$result[] = array_merge( array($tem), array($t));

    			}
    		}
    	}
    	return $result;
    }


    /**
     * copy product image
     * @param  [type] $id 
     * @return [type]     
     */
    public function copy_product_image($id, $arr_variant = [])
    {	
    	if(count($arr_variant) == 0){
    		$arr_variant = $this->get_product_by_parent_id($id);
    	}
    	$attachments = $this->mrp_get_attachments_file($id, 'commodity_item_file');

    	foreach ($arr_variant as $variant_id) {

    		if (is_dir(MANUFACTURING_PRODUCT_UPLOAD . $id)) {
    			xcopy(MANUFACTURING_PRODUCT_UPLOAD . $id, MANUFACTURING_PRODUCT_UPLOAD . $variant_id['id']);
    		}
    		foreach ($attachments as $at) {

    			$_at      = [];
    			$_at[]    = $at;
    			$external = false;
    			if (!empty($at['external'])) {
    				$external       = $at['external'];
    				$_at[0]['name'] = $at['file_name'];
    				$_at[0]['link'] = $at['external_link'];
    				if (!empty($at['thumbnail_link'])) {
    					$_at[0]['thumbnailLink'] = $at['thumbnail_link'];
    				}
    			}

    			$this->misc_model->add_attachment_to_database($variant_id['id'],'commodity_item_file', $_at, $external);
    		}   
    	}

    	return true;
    }


    /**
     * get parent product
     * @return [type] 
     */
    public function get_parent_product()
    {
    	$sql_where = ' ('  .db_prefix().'items.parent_id is null OR  '.db_prefix().'items.parent_id = 0 OR  '.db_prefix().'items.parent_id = "" )  ';

    	$this->db->where($sql_where);
    	$this->db->where('can_be_manufacturing', 'can_be_manufacturing');
    	$products = $this->db->get(db_prefix().'items')->result_array();

    	return $products;
        
    }


    /**
     * get product variant
     * @return [type] 
     */
    public function get_product_variant()
    {
    	$sql_where =  db_prefix().'items.active = 1 AND '.db_prefix().'items.parent_id is not null AND  '.db_prefix().'items.parent_id != 0 AND  '.db_prefix().'items.attributes is not null ';

    	$this->db->select('*, CONCAT(commodity_code, "_", description) as description');
    	$this->db->where($sql_where);
    	$products = $this->db->get(db_prefix().'items')->result_array();

    	return $products;
    }


    /**
     * get bill of material
     * @param  boolean $id 
     * @return [type]      
     */
    public function get_bill_of_materials($id = false)
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'mrp_bill_of_materials')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from ' . db_prefix() . 'mrp_bill_of_materials')->result_array();
		}
	}

    /**
     * add bill of material
     * @param [type] $data 
     */
    public function add_bill_of_material($data)
	{
		$this->db->insert(db_prefix().'mrp_bill_of_materials',$data);
		$insert_id = $this->db->insert_id();

		if ($insert_id) {
			/*update next number setting*/
			$this->update_prefix_number(['bom_number' =>  get_mrp_option('bom_number')+1]);

			return $insert_id;
		}
		return false;
	}


	/**
	 * update bill of material
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function update_bill_of_material($data, $id)
	{
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'mrp_bill_of_materials', $data);
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		if($affected_rows > 0){
			return true;
		}
		return false;  
	}
	

	/**
	 * delete bill of material
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_bill_of_material($id)
	{	
		$affected_rows = 0;

		//delete bill of material details
		$this->db->where('bill_of_material_id', $id);
		$this->db->delete(db_prefix() . 'mrp_bill_of_material_details');
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		//delete data
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'mrp_bill_of_materials');
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		if($affected_rows > 0){
			return true;
		}
		return false;
	}


	/**
	 * get bill of material details
	 * @param  boolean $id                  
	 * @param  boolean $bill_of_material_id 
	 * @return [type]                       
	 */
	public function get_bill_of_material_details($id=false, $bill_of_material_id = false)
	{
	    if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'mrp_bill_of_material_details')->row();
		}

		if($bill_of_material_id != false){
			$this->db->where('bill_of_material_id', $bill_of_material_id);
			return $this->db->get(db_prefix() . 'mrp_bill_of_material_details')->result_array();
		}

		if ($id == false) {
			return $this->db->query('select * from ' . db_prefix() . 'mrp_bill_of_material_details')->result_array();
		}
	}


	/**
	 * add bill of material detail
	 * @param [type] $data 
	 */
	public function add_bill_of_material_detail($data)
	{

		if(isset($data['apply_on_variants'])){
			$data['apply_on_variants'] = implode(',', $data['apply_on_variants']);
		}
	
	    $this->db->insert(db_prefix().'mrp_bill_of_material_details',$data);
		$insert_id = $this->db->insert_id();

		if ($insert_id) {
			return $insert_id;
		}
		return false;

	}


	/**
	 * update bill of material detail
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function update_bill_of_material_detail($data, $id)
	{
		$affected_rows=0;

		if(isset($data['apply_on_variants'])){
			$data['apply_on_variants'] = implode(',', $data['apply_on_variants']);
		}else{
			$data['apply_on_variants'] = null;
		}

		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'mrp_bill_of_material_details', $data);
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		if($affected_rows > 0){
			return true;
		}
		return false;   
	}


	/**
	 * delete bill of material detail
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_bill_of_material_detail($id)
	{	
		//delete data
	    $this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'mrp_bill_of_material_details');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}


	/**
	 * get variant attribute
	 * @param  [type] $product_id 
	 * @return [type]             
	 */
	public function get_variant_attribute($product_id, $product_variant_id)
	{
		
		$arr_variant=[];
	    $product = $this->get_product($product_id);
	    if($product_variant_id != 0){
	    	$arr_product_variant = [];
	    	$product_variant = $this->get_product($product_variant_id);
	    	if($product_variant){
	    		$product_variant_attributes = json_decode($product_variant->attributes);

	    		foreach ($product_variant_attributes as $value) {
	    			$arr_product_variant[$value->name] = $value->option;
	    		}
	    	}
	    }

	    if($product){
	    	if( $product->parent_attributes != null  && $product->parent_attributes != '' && new_strlen($product->parent_attributes) > 28){
	    		$parent_attributes = json_decode($product->parent_attributes);

	    		foreach ($parent_attributes as $parent_attribute) {
	    		    foreach ($parent_attribute->options as $option) {
	    		    	if(isset($arr_product_variant)){
	    		    		if(isset($arr_product_variant[$parent_attribute->name]) && $arr_product_variant[$parent_attribute->name] == $option){
	    		    			array_push($arr_variant, [
	    		    				'name' => $parent_attribute->name.':'.$option,
	    		    				'label' => $parent_attribute->name.' : '.$option,
	    		    			]);
	    		    		}
	    		    	}else{
	    		    		array_push($arr_variant, [
	    		    			'name' => $parent_attribute->name.':'.$option,
	    		    			'label' => $parent_attribute->name.' : '.$option,
	    		    		]);
	    		    	}
	    	
	    		    }
	    		}
	    	}
	    }
	    return $arr_variant;
	}


	/**
	 * get product variants
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_product_variants($id)
	{
		$options = '';

		$product_by_parent_id = $this->get_product_by_parent_id($id);
		
		if(count($product_by_parent_id) > 0){
			$options .= '<option value=""></option>';
			foreach ($product_by_parent_id as $key => $value) {
					$options .= '<option value="' . $value['id'] . '">' . $value['description'] . '</option>';
			}
		}
		return $options;

	}


	/**
	 * get data create manufacturing order
	 * @param  [type] $product_id 
	 * @return [type]             
	 */
	public function get_data_create_manufacturing_order($product_id)
	{

		$bill_of_material_option ='';
		$routing_option ='';
		$bill_of_material_arr=[];
		$component_arr=[];
		$component_row=0;
		$unit_id='';

		//get list bills of material with BOM type is "manufacture this product"
		$bill_of_materials = $this->get_list_bill_of_material_by_product($product_id);
		if(count($bill_of_materials) > 0){
			foreach ($bill_of_materials as $bom_key => $bom_value) {
					$bill_of_material_option .= '<option value="' . $bom_value['id'] . '">' .$bom_value['description'] . '</option>';
					$bill_of_material_arr[] = $bom_value['id'];

					if(new_strlen($routing_option) == 0){
						$routing_option = $bom_value['routing_id'];
					}
			}
		}


		//get bill_of_material detail value
		if(count($bill_of_material_arr) > 0){
			$bill_of_material_details = $this->get_bill_of_material_details('', $bill_of_material_arr[0]);

			$product = $this->get_product($product_id);

			if(isset($product)){
				$component_arr = $this->get_bill_of_material_details_by_product($bill_of_material_arr[0], $product->attributes);
				$unit_id = $product->unit_id;

			}
		}

		$result=[];
		$result['bill_of_material_option'] =$bill_of_material_option; 
		$result['routing_option'] =$routing_option; 
		$result['component_arr'] =$component_arr; 
		$result['component_row'] = count($component_arr); 
		$result['unit_id'] = $unit_id; 

		return $result;
	}


	/**
	 * get product for hansometable
	 * @return [type] 
	 */
	public function get_product_for_hansometable()
	{
		$sql_where = db_prefix().'items.id not in ( SELECT distinct parent_id from '.db_prefix().'items WHERE parent_id is not null AND parent_id != "0" )';
		$this->db->select('id, CONCAT(commodity_code,"_",description) as label');
		$this->db->where($sql_where);
		return $this->db->get(db_prefix() . 'items')->result_array();

	}


	/**
	 * get unit for hansometable
	 * @return [type] 
	 */
	public function get_unit_for_hansometable()
	{
	    return $this->db->query('select unit_type_id as id, unit_name as label from ' . db_prefix() . 'ware_unit_type')->result_array();
	}

	/**
	 * get bill of material detail with product name
	 * @return [type] 
	 */
	public function get_bill_of_material_detail_with_product_name()
	{	
		$this->db->select(db_prefix().'mrp_bill_of_materials.id, CONCAT('.db_prefix().'mrp_bill_of_materials.bom_code," ", '.db_prefix().'items.description) as description');
		$this->db->from(db_prefix() . 'mrp_bill_of_materials');
	    $this->db->join(db_prefix() . 'items', db_prefix() . 'mrp_bill_of_materials.product_id = ' . db_prefix() . 'items.id', 'left');
		$bill_of_materials = $this->db->get()->result_array();
		return $bill_of_materials;
	}

	/**
	 * get bill of material details by product
	 * @param  [type] $bill_of_material_id 
	 * @param  [type] $product_attribute   
	 * @return [type]                      
	 */
	public function get_bill_of_material_details_by_product($bill_of_material_id, $product_attributes, $product_qty='')
	{	
		$component_arr = [];

		$bom_qty=1;
		$bom = $this->get_bill_of_materials($bill_of_material_id);
		if($bom){
			$bom_qty = $bom->product_qty;
		}

		if($product_attributes != null){
			$str_where = '';
			$arr_attributes = json_decode($product_attributes);

			foreach ($arr_attributes as $key => $attributes) {
				if(new_strlen($str_where) > 0){
					$str_where .= 'OR find_in_set( "'.$attributes->name.':'.$attributes->option.'", apply_on_variants)';
				}else{
					$str_where .= ' find_in_set( "'.$attributes->name.':'.$attributes->option.'", apply_on_variants)';
				}
			}

			if(new_strlen($str_where) > 0){
				$str_where .= 'OR apply_on_variants is null';
			}else{
				$str_where .= 'apply_on_variants is null';
			}

			$this->db->where('bill_of_material_id = '.$bill_of_material_id.' AND ('.$str_where.')');
			$bill_of_material_details = $this->db->get(db_prefix() . 'mrp_bill_of_material_details')->result_array();

		}else{
			$this->db->where('bill_of_material_id = '.$bill_of_material_id.' AND apply_on_variants is null ');
			$bill_of_material_details = $this->db->get(db_prefix() . 'mrp_bill_of_material_details')->result_array();
		}

		foreach ($bill_of_material_details as $bom_detail_key => $bom_detail_value) {
			if($product_qty != ''){
				$qty_to_consume = (float)$bom_detail_value['product_qty']*(float)$product_qty/$bom_qty;
			}else{
				//default mo for 1 product
				$qty_to_consume = $bom_detail_value['product_qty']*1/$bom_qty;
			}

			$last_bill_of_material = $this->get_last_bill_of_material_by_product($bom_detail_value['product_id'], $qty_to_consume, '');

			if(count($last_bill_of_material) > 0){

				$component_arr = array_merge($component_arr, $last_bill_of_material);
			}else{

				array_push($component_arr, [
					'id' => 0,
					'product_id' => $bom_detail_value['product_id'],
					'unit_id' => $bom_detail_value['unit_id'],
					'qty_to_consume' =>  $qty_to_consume,
					'qty_reserved' =>  0,
					'qty_done' =>  0,
					'check_inventory_qty' =>  true,
				]);
			}
		}

		return $component_arr;

	}

	/**
	 * add manufacturing order
	 * @param [type] $data 
	 */
	public function add_manufacturing_order($data)
	{
		$affected_rows=0;

		if (isset($data['product_tab_hs'])) {
			$product_tab_hs = $data['product_tab_hs'];
			unset($data['product_tab_hs']);
		}
		$data['manufacturing_order_code'] = $this->create_code('mo_code');
		$data['date_deadline'] = to_sql_date($data['date_deadline'], true);
		$data['date_plan_from'] = to_sql_date($data['date_plan_from'], true);

		$this->db->insert(db_prefix() . 'mrp_manufacturing_orders', $data);
		$insert_id = $this->db->insert_id();


		//create working hours data
		if(isset($product_tab_hs)){
			$working_hour_detail = json_decode($product_tab_hs);

			$es_detail = [];
			$row = [];
			$header = [];

			$header[] = 'id';
			$header[] = 'product_id';
			$header[] = 'unit_id';
			$header[] = 'qty_to_consume';
			$header[] = 'qty_reserved';
			$header[] = 'qty_done';

			foreach ($working_hour_detail as $key => $value) {
				if($value[1] != ''){
					$es_detail[] = array_combine($header, $value);
				}
			}
		}

		if(count($es_detail) > 0){

			if (isset($insert_id)) {
				$affected_rows++;

				/*insert manufacturing_order_id*/
				foreach($es_detail as $key => $rqd){
					$es_detail[$key]['manufacturing_order_id'] = $insert_id;
				}

				$insert_working_hour = $this->db->insert_batch(db_prefix().'mrp_manufacturing_order_details',$es_detail);
				if($insert_working_hour > 0){
					$affected_rows++;
				}

			}
		}

		if($insert_id){
			/*update next number setting*/
			$this->update_prefix_number(['mo_number' =>  get_mrp_option('mo_number')+1]);
		}


		if ($affected_rows > 0) {
			return $insert_id;
		}
		return false;

	}

	/**
	 * update manufacturing order
	 * @param  [type] $id   
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function update_manufacturing_order($data, $id)
	{
		$affected_rows=0;

		if (isset($data['product_tab_hs'])) {
			$product_tab_hs = $data['product_tab_hs'];
			unset($data['product_tab_hs']);
		}

		$data['date_deadline'] = to_sql_date($data['date_deadline'], true);
		$data['date_plan_from'] = to_sql_date($data['date_plan_from'], true);

		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'mrp_manufacturing_orders', $data);

		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		//create working hours data
		if(isset($product_tab_hs)){
			$working_hour_detail = json_decode($product_tab_hs);

			$es_detail = [];
			$row = [];
			$header = [];

			$header[] = 'id';
			$header[] = 'product_id';
			$header[] = 'unit_id';
			$header[] = 'qty_to_consume';
			$header[] = 'qty_reserved';
			$header[] = 'qty_done';

			foreach ($working_hour_detail as $key => $value) {
				if($value[1] != ''){
					$es_detail[] = array_combine($header, $value);
				}
			}
		}

		//handle manufacturing_orders detail
		$manufacturing_order_details = [];
		$manufacturing_order_details['update'] = []; 
		$manufacturing_order_details['insert'] = []; 
		$manufacturing_order_details['delete'] = [];
		$total = [];
		foreach ($es_detail as $key => $value) {
			if($value['id'] != ''){
				$manufacturing_order_details['delete'][] = $value['id'];
				$manufacturing_order_details['update'][] = $value;
			}else{
				unset($value['id']);
				$value['manufacturing_order_id'] = $id;
				$manufacturing_order_details['insert'][] = $value;
			}

		}

		if(empty($manufacturing_order_details['delete'])){
			$manufacturing_order_details['delete'] = ['0'];
		}
		//log Bom: 2 = Component deletion from the assembly
		$this->db->where('id NOT IN ('.implode(",",$manufacturing_order_details['delete']) .') and manufacturing_order_id ='.$id);
		$deletion_components = $this->db->get(db_prefix().'mrp_manufacturing_order_details')->result_array();

		$user_id =  get_staff_user_id();
		if(count($deletion_components) > 0){
			foreach ($deletion_components as $deletion_component) {
				$manufacturing_order_id = $id;
				$parent_product_id = 0;
				$product_id = $deletion_component['product_id'];
				$unit_id = $deletion_component['unit_id'];
				$change_type = 'Component_deletion_from_the_assembly';
				$change_quantity = $deletion_component['qty_to_consume'];
				$created_at = date('Y-m-d H:i:s');
				$staff_id = $user_id;
				$description = _l('bom_change_log_user_deletion_component_from_the_assembly');
				$rel_id = 0;
				$rel_type = '';

			    $this->bom_change_log($manufacturing_order_id, $parent_product_id, $product_id, $unit_id, $change_type, $change_quantity, $created_at, $staff_id, $description, $rel_id, $rel_type);
			}

		}

		$manufacturing_order_details['delete'] = implode(",",$manufacturing_order_details['delete']);
		$this->db->where('id NOT IN ('.$manufacturing_order_details['delete'] .') and manufacturing_order_id ='.$id);
		$this->db->delete(db_prefix().'mrp_manufacturing_order_details');
		if($this->db->affected_rows() > 0){
			$affected_rows++;
		}


		if(count($manufacturing_order_details['insert']) != 0){
			foreach ($manufacturing_order_details['insert'] as $add_component) {
				$manufacturing_order_id = $id;
				$parent_product_id = 0;
				$product_id = $add_component['product_id'];
				$unit_id = $add_component['unit_id'];
				$change_type = 'Component_additions_to_the_assembly';
				$change_quantity = $add_component['qty_to_consume'];
				$created_at = date('Y-m-d H:i:s');
				$staff_id = $user_id;
				$description = _l('bom_change_log_user_additions_component_to_the_assembly');
				$rel_id = 0;
				$rel_type = '';

			    $this->bom_change_log($manufacturing_order_id, $parent_product_id, $product_id, $unit_id, $change_type, $change_quantity, $created_at, $staff_id, $description, $rel_id, $rel_type);
			}

			$this->db->insert_batch(db_prefix().'mrp_manufacturing_order_details', $manufacturing_order_details['insert']);
			if($this->db->affected_rows() > 0){
				$affected_rows++;
			}
		}

		if(count($manufacturing_order_details['update']) != 0){

			foreach ($manufacturing_order_details['update'] as $update_component) {
				// check quantity is changed before update
				$this->db->where('id', $update_component['id']);
				$update_component_data = $this->db->get(db_prefix().'mrp_manufacturing_order_details')->row();
				if($update_component_data){
					if((float)$update_component_data->qty_to_consume != (float)$update_component['qty_to_consume']){
						$manufacturing_order_id = $id;
						$parent_product_id = 0;
						$product_id = $update_component['product_id'];
						$unit_id = $update_component['unit_id'];
						$change_type = 'Component_quantity_change_during_edit';
						$change_quantity = (float)$update_component['qty_to_consume'] - (float)$update_component_data->qty_to_consume;
						$created_at = date('Y-m-d H:i:s');
						$staff_id = $user_id;
						$description = _l('bom_change_log_user_component_quantity_change_during_edit');
						$rel_id = 0;
						$rel_type = '';

						$this->bom_change_log($manufacturing_order_id, $parent_product_id, $product_id, $unit_id, $change_type, $change_quantity, $created_at, $staff_id, $description, $rel_id, $rel_type);
					}
				}
			}

			$this->db->update_batch(db_prefix().'mrp_manufacturing_order_details', $manufacturing_order_details['update'], 'id');
			if($this->db->affected_rows() > 0){
				$affected_rows++;
			}
		}
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete manufacturing order
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_manufacturing_order($id)
	{	
		$affected_rows = 0;

		//delete data
		$this->db->where('manufacturing_order_id', $id);
		$this->db->delete(db_prefix() . 'mrp_manufacturing_order_details');
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'mrp_manufacturing_orders');
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		$this->db->where('manufacturing_order_id', $id);
		$this->db->delete(db_prefix() . 'mrp_work_orders');
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		if($affected_rows > 0){
            hooks()->do_action('after_manufacturing_order_deleted', $id);

			return true;
		}
		return false;
	}

	/**
	 * get list manufacturing order
	 * @return [type] 
	 */
	public function get_list_manufacturing_order()
	{
	    return $this->db->query('select * from ' . db_prefix() . 'mrp_manufacturing_orders')->result_array();
	}

	/**
	 * get manufacturing order
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_manufacturing_order($id)
	{
	    $this->db->where('manufacturing_order_id', $id);
	    $manufacturing_order_details = $this->db->get(db_prefix().'mrp_manufacturing_order_details')->result_array();

	    $this->db->where('id', $id);
	    $manufacturing_order = $this->db->get(db_prefix().'mrp_manufacturing_orders')->row();

	    $result=[];
	    $result['manufacturing_order_detail'] = $manufacturing_order_details;
	    $result['manufacturing_order'] = $manufacturing_order;

	    return $result;
	    
	}

	/**
	 * get list bill of material by product
	 * @param  [type] $product_id 
	 * @return [type]             
	 */
	public function get_list_bill_of_material_by_product($product_id)
	{
	    //get list bills of material with BOM type is "manufacture this product"
		$product = $this->get_product($product_id);
		if($product){
			$parent_id = $product->parent_id;
			$attributes = $product->attributes;
		}

		if(isset($parent_id) && (int)$parent_id != 0){
			$sql_where = "( (product_id = ".$parent_id." AND product_variant_id = ".$product_id.") OR( product_id = ".$parent_id." AND (product_variant_id = 0 OR product_variant_id is null))) AND bom_type = 'manufacture_this_product'"; 
		}else{
			//14/05/2021 change bom_type "Kit" TO "manufacture_this_product" 
			$sql_where = "product_id = ".$product_id." AND bom_type = 'manufacture_this_product'";
		}

		$this->db->select(db_prefix().'mrp_bill_of_materials.id,CONCAT('.db_prefix().'mrp_bill_of_materials.bom_code," ", '.db_prefix().'items.description) as description, '.db_prefix().'mrp_bill_of_materials.bom_code, routing_id');
		$this->db->from(db_prefix() . 'mrp_bill_of_materials');
		$this->db->join(db_prefix() . 'items', db_prefix() . 'mrp_bill_of_materials.product_id = ' . db_prefix() . 'items.id', 'left');
		$this->db->where($sql_where);

		$bill_of_materials = $this->db->get()->result_array();
		return $bill_of_materials;
	}

	/**
	 * update manufacturing order status
	 * @param  [type] $id     
	 * @param  [type] $status 
	 * @return [type]         
	 */
	public function update_manufacturing_order_status($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update(db_prefix().'mrp_manufacturing_orders', $data);
		if($this->db->affected_rows() > 0){
            hooks()->do_action('manufacturing_order_status_changed', ['id' => $id, 'data' => $data]);

			return true;
		}
		return false;
	}

	/**
	 * mo mark as todo
	 * @param  [type] $id 
	 * @return [type]     
	 * Check component avalability
	 */
	public function mo_mark_as_todo($id, $type)
	{	
		$this->load->model('warehouse/warehouse_model');
		$result=[];
		$affected_rows=0;
		$flag = 0;

		//get MO
		$mo_detail_update=[];
		$mo_detail_update_check_availability=[];
		$check_availability='';
		$check_availability_message='';
		$warehouse_id='';

		$mo = $this->get_manufacturing_order($id);
		if($mo['manufacturing_order']){
			$warehouse_id = $mo['manufacturing_order']->components_warehouse_id;
		}

		if($mo['manufacturing_order_detail']){
			foreach ($mo['manufacturing_order_detail'] as $mo_detail) {
				$flag_inventory = 0;

					$commodity_name='';
					$item_value = $this->get_product($mo_detail['product_id']);

					if($item_value){
						$commodity_name .= $item_value->description;
					}

					if(new_strlen($warehouse_id) > 0){
						$sql = 'SELECT  sum(inventory_number) as inventory_number from ' . db_prefix() . 'inventory_manage where warehouse_id = ' . $warehouse_id . ' AND commodity_id = ' . $mo_detail['product_id'];
					}else{
						$sql = 'SELECT  sum(inventory_number) as inventory_number from ' . db_prefix() . 'inventory_manage where commodity_id = ' . $mo_detail['product_id'];
					}

					$value = $this->db->query($sql)->row();

					if ($value) {
						$inventory_number = $value->inventory_number;

						if ((float)$value->inventory_number < (float) $mo_detail['qty_to_consume']) {
							$flag = 1;
							$flag_inventory = 1;

							$check_availability_message .= $commodity_name.' '._l('not_enough_inventory').', '._l('available_quantity').': '.(float) $value->inventory_number.'<br/>';
						}
					} else {
						$flag = 1;
						$flag_inventory = 1;

						$check_availability_message .=$commodity_name.' '. _l('Product_does_not_exist_in_stock').'<br/>';
					}

					if($type == 'mark_as_todo'){
						//mark_as_todo

						if($flag_inventory == 0){
							$qty_reserved = $mo_detail['qty_to_consume'];
						}else{
							if(isset($inventory_number) && (float)$inventory_number != 0){
								$qty_reserved = (float)$inventory_number;

							}else{
								$qty_reserved = 0;
							}
						}


						array_push($mo_detail_update, [
							'id' =>$mo_detail['id'], 
							'manufacturing_order_id' =>$mo_detail['manufacturing_order_id'],
							'product_id' =>$mo_detail['product_id'],
							'unit_id' =>$mo_detail['unit_id'],
							'qty_to_consume' =>$mo_detail['qty_to_consume'],
							'qty_reserved' =>$qty_reserved,
						]);

					}else{
						//check availability
						
						if($mo_detail['qty_reserved'] < $mo_detail['qty_to_consume']){
							if(isset($inventory_number) && (float)$inventory_number != 0){
								if(($mo_detail['qty_to_consume']-$mo_detail['qty_reserved']) <= (float)$inventory_number){
									$qty_reserved = $mo_detail['qty_to_consume']-$mo_detail['qty_reserved'];
								}else{
									$qty_reserved = $inventory_number;
								}

								array_push($mo_detail_update, [
									'id' =>$mo_detail['id'], 
									'manufacturing_order_id' =>$mo_detail['manufacturing_order_id'],
									'product_id' =>$mo_detail['product_id'],
									'unit_id' =>$mo_detail['unit_id'],
									'qty_to_consume' =>$mo_detail['qty_to_consume'],
									'qty_reserved' =>$mo_detail['qty_reserved']+$qty_reserved,
								]);

								array_push($mo_detail_update_check_availability, [
									'id' =>$mo_detail['id'], 
									'manufacturing_order_id' =>$mo_detail['manufacturing_order_id'],
									'product_id' =>$mo_detail['product_id'],
									'unit_id' =>$mo_detail['unit_id'],
									'qty_to_consume' =>$mo_detail['qty_to_consume'],
									'qty_reserved' =>$qty_reserved,
								]);

							}
						}

					}

			}
		}

		if($flag == 1){
			$result['status'] = false;
			$result['message'] = _l('component').'<br>'.$check_availability_message;
	
		}

		if(count($mo_detail_update) > 0){
			//update mo detail, reserved quantity
			$this->db->update_batch(db_prefix().'mrp_manufacturing_order_details', $mo_detail_update, 'id');
			if($this->db->affected_rows() > 0){
				$affected_rows++;
			}

			//update inventory quantity (warehouse module)
			//remove item have qty_reserved == 0
			foreach ($mo_detail_update as $mo_d_key => $mo_value) {
			   if($mo_value['qty_reserved'] == 0){
			   		unset($mo_detail_update[$mo_d_key]);
			   }
			}

			if(count($mo_detail_update) > 0){

				if($type == 'mark_as_todo'){
					//mark_as_todo
					
					$add_inventory_quantity = $this->mrp_add_inventory_quantity($mo_detail_update, 2, $warehouse_id);
				}else{
					//check availability
					
					$add_inventory_quantity = $this->mrp_add_inventory_quantity($mo_detail_update, 2, $warehouse_id);
				}

				foreach ($mo_detail_update as $update_component) {
					$manufacturing_order_id = $id;
					$parent_product_id = 0;
					$product_id = $update_component['product_id'];
					$unit_id = $update_component['unit_id'];
					$change_type = 'mrp_change_quantity';
					$change_quantity = $update_component['qty_reserved'];
					$created_at = date('Y-m-d H:i:s');
					$staff_id = '';
					$description = _l('bom_change_log_component_availability_for_manufacturing');
					$rel_id = 0;
					$rel_type = '';
					$check_availability = 1;

					$this->bom_change_log($manufacturing_order_id, $parent_product_id, $product_id, $unit_id, $change_type, $change_quantity, $created_at, $staff_id, $description, $rel_id, $rel_type, $check_availability);
				}

				if($add_inventory_quantity){
					$affected_rows++;
				}
			}

		}

		if($type == 'mark_as_todo'){

		//update MO status to "confirmed"
			$update_mo_status = $this->update_manufacturing_order_status($id, [
				'status' => 'confirmed',
			]);

			if($update_mo_status){
				$affected_rows++;
			}
		}

		if(!isset($result['status'])){
			if($affected_rows > 0){
				$result['status'] = true;
			}else{
				$result['status'] = false;
			}
		}

		if(!isset($result['message'])){
			$result['message'] = '';
		}

		return $result;
	}

	/**
	 * mo mark as todo
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function mo_mark_as_planned($id)
	{	
		$affected_rows=0;

		//insert data to work order table
		$operation_of_bom = $this->get_operation_of_bom($id);

		if(count($operation_of_bom['work_orders']) > 0){
			$this->db->insert_batch(db_prefix().'mrp_work_orders', $operation_of_bom['work_orders']);
			if($this->db->affected_rows() > 0){
				$affected_rows++;
			}
		}

		//update MO status to "planned"
		$update_mo_status = $this->update_manufacturing_order_status($id, [
			'status' => 'planned',
			'date_planned_start' => to_sql_date($operation_of_bom['date_planned_start'], true),
			'date_planned_finished' => to_sql_date($operation_of_bom['date_planned_finished'], true),
		]);
		if($update_mo_status){
			$affected_rows++;
		}

		if($affected_rows > 0){
			return true;
		}
		return false;
	}

	/**
	 * get operation of bom
	 * @param  [type] $bom_id 
	 * @return [type]         
	 */
	public function get_operation_of_bom($mo_id)
	{
	    //1.get operation of main Bom
	    //2.get operation of components in Bom (recursive).

		$work_orders=[];

	    //1.get operation of main Bom
		$manufacturing_order = $this->get_manufacturing_order($mo_id);
		$work_center_id=[];

		if($manufacturing_order['manufacturing_order']){
			$date_plan_from = $manufacturing_order['manufacturing_order']->date_plan_from;

			$mo_product_id = $manufacturing_order['manufacturing_order']->product_id;
			$product_quantity = $manufacturing_order['manufacturing_order']->product_qty;
			$product_unit = $manufacturing_order['manufacturing_order']->unit_id;
			//get operation from routing id
			$operations = $this->get_operation('', $manufacturing_order['manufacturing_order']->routing_id);
			foreach ($operations as $operation) {
				array_push($work_orders, [
					'manufacturing_order_id' => $mo_id,
					'product_id' => $mo_product_id,
					'qty_produced' => 0,
					'qty_production' => $product_quantity,
					'qty_producing' => $product_quantity,
					'unit_id' => $product_unit,
					'date_planned_start' => to_sql_date('', true),
					'date_planned_finished' => to_sql_date('', true),
					'routing_detail_id' => $operation['id'],
					'duration_expected' => $operation['default_duration'],
					'operation_name' => $operation['operation'],
					'work_center_id' => $operation['work_center_id'],
				]);

				if(!in_array($operation['work_center_id'], $work_center_id)){
					$work_center_id[] = $operation['work_center_id'];
				}
			}

		}

		//get working hours
		//get working hours by day
		$working_hours=[];
		if(count($work_center_id) > 0){
			$working_hours = $this->get_working_time_by_ids($work_center_id);
		}

	    //2.get operation of components in Bom (recursive).
		if(count($manufacturing_order['manufacturing_order_detail']) > 0){
			$bill_of_material_details = $this->get_bill_of_material_details('', $manufacturing_order['manufacturing_order']->bom_id);
			$operation_recursive = $this->get_operation_recursive($bill_of_material_details, $mo_id);

			$work_orders = array_merge($work_orders, $operation_recursive);
		}

		if(isset($date_plan_from)){
			$date_planned_start_temp=$date_plan_from;
			$date_planned_finished_temp='';
			$flag_next_day = false;
			foreach ($work_orders as $key => $work_order) {
				$flag_hour_key=0;

				if(count($working_hours) > 0 && isset($working_hours['wcenter'.$work_order['work_center_id']])){

					$wh_by_wcenter = $working_hours['wcenter'.$work_order['work_center_id']];
					$duration_expected_temp = $work_order['duration_expected'];
					while ( (float)$duration_expected_temp != 0) {


						while (!isset($wh_by_wcenter[date('N', strtotime($date_planned_start_temp))])){
							$date_planned_start_temp = date('Y-m-d', strtotime($date_planned_start_temp.'+'.'1 days')).' 00:00:01';
						}

						foreach ($wh_by_wcenter[date('N', strtotime($date_planned_start_temp))] as $hour_key => $hours) {

							if((strtotime($date_planned_start_temp) >= strtotime(date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_from'] )) && (strtotime($date_planned_start_temp) <= strtotime(date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_to'] ))){
									// <= date_planned_start_temp <=

								if(!isset($work_orders[$key]['date_planned_start']) || $work_orders[$key]['date_planned_start'] == null){
									$work_orders[$key]['date_planned_start'] = to_sql_date($date_planned_start_temp, true);
								}
								
								if($flag_next_day == true){
									$hours_time = (strtotime(date('Y-m-d', strtotime($date_planned_start_temp.'+'.'1 days')).' '.$hours['work_to'])-strtotime($date_planned_start_temp))/60;


								}else{
									$hours_time = (strtotime($hours['work_to'])-strtotime(date('H:i:s', strtotime($date_planned_start_temp))))/60;
								}

								if((float)$duration_expected_temp > (float)$hours_time){
									$duration_expected_temp = (float)$duration_expected_temp - (float)$hours_time;
									$date_planned_start_temp = date('Y-m-d H:i:s', strtotime($date_planned_start_temp.'+'.$hours_time.' minute'));

								}else{
									$date_planned_start_temp = date('Y-m-d H:i:s', strtotime($date_planned_start_temp.'+'.(float)$duration_expected_temp.' minute'));
									$duration_expected_temp = 0;

								}

								$work_orders[$key]['date_planned_finished'] = to_sql_date($date_planned_start_temp, true);
								
								if($duration_expected_temp == 0){
									$flag_next_day=false;
									break;
								}

								if($duration_expected_temp != 0 && count($wh_by_wcenter[date('N', strtotime($date_planned_start_temp))]) == $hour_key+1){
									$date_planned_start_temp = date('Y-m-d', strtotime($date_planned_start_temp.'+'.'1 days')).' 00:00:01';
									$flag_next_day=true;
								}


							}elseif(strtotime($date_planned_start_temp) <= strtotime(date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_from'] )){

								if(!isset($work_orders[$key]['date_planned_start']) && $work_orders[$key]['date_planned_start'] == null){
									$work_orders[$key]['date_planned_start'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_from'] ));
									$work_order['date_planned_start'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_from'] ));
								}
								

								$date_planned_start_temp = date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_from'] ;
								

								if($flag_next_day == true){

									$hours_time = (strtotime(date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_to'])-strtotime($date_planned_start_temp))/60;

								}else{
									$hours_time = (strtotime($hours['work_to'])-strtotime($hours['work_from']))/60;
								}

								if((float)$duration_expected_temp > (float)$hours_time){
									$duration_expected_temp = (float)$duration_expected_temp - (float)$hours_time;
									$date_planned_start_temp = date('Y-m-d H:i:s', strtotime($date_planned_start_temp.'+'.$hours_time.' minute'));

								}else{
									$date_planned_start_temp = date('Y-m-d H:i:s', strtotime($date_planned_start_temp.'+'.(float)$duration_expected_temp.' minute'));
									$duration_expected_temp = 0;
								}
								$work_orders[$key]['date_planned_finished'] = $date_planned_start_temp;

								if($duration_expected_temp == 0){
									$flag_next_day=false;
									break;
								}

								if($duration_expected_temp != 0 && count($wh_by_wcenter[date('N', strtotime($date_planned_start_temp))]) == $hour_key+1){
									$date_planned_start_temp = date('Y-m-d', strtotime($date_planned_start_temp.'+'.'1 days')).' 00:00:01';
									$flag_next_day=true;
								}


							}elseif(strtotime($date_planned_start_temp) >= strtotime(date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_to'] )){

								if(count($wh_by_wcenter[date('N', strtotime($date_planned_start_temp))]) == $hour_key+1){
									$date_planned_start_temp = date('Y-m-d', strtotime($date_planned_start_temp.'+'.'1 days')).' 00:00:01';
									$flag_next_day=true;
								}


							}else{
								
								$date_planned_start_temp = date('Y-m-d', strtotime($date_planned_start_temp.'+'.'1 days')).' 00:00:01';
								$flag_next_day=true;

							}

						}

					}

					if(!isset($work_order['status'])){

						if($key == 0 ){
							$work_orders[$key]['status'] = 'ready';
						}else{
							$work_orders[$key]['status'] = 'waiting_for_another_wo';
						}
					}

				}else{
					
					$newtimestamp = strtotime($date_planned_start_temp.' + '.(int)$work_order['duration_expected'].' minute');
					$planned_finished = date('Y-m-d H:i:s', $newtimestamp);

					$work_orders[$key]['date_planned_start'] = to_sql_date($date_planned_start_temp, true);
					$work_orders[$key]['date_planned_finished'] = to_sql_date($planned_finished, true);

					if(!isset($work_order['status'])){

						if($key == 0 ){
							$work_orders[$key]['status'] = 'ready';
						}else{
							$work_orders[$key]['status'] = 'waiting_for_another_wo';
						}
					}

					$date_planned_start_temp = $planned_finished;
				}

			}

		}
		
		$work_orders = array_reverse($work_orders);

		$data=[];
		$data['work_orders'] = $work_orders;
		$data['date_planned_start'] = $date_plan_from;
		$data['date_planned_finished'] = $date_planned_start_temp;
		return $data;
	}

	/**
	 * get_plan_start_finished
	 * @param  [type] $work_orders             
	 * @param  [type] $work_order              
	 * @param  [type] $key                     
	 * @param  [type] $wh_by_wcenter           
	 * @param  [type] $date_planned_start_temp 
	 * @param  [type] $duration_expected_temp  
	 * @param  [type] $flag_next_day           
	 * @return [type]                          
	 */
	public function get_plan_start_finished($work_orders, $work_order, $key, $wh_by_wcenter, $date_planned_start_temp, $duration_expected_temp, $flag_next_day)
	{
	

		if(isset($wh_by_wcenter[date('N', strtotime($date_planned_start_temp))])){

			foreach ($wh_by_wcenter[date('N', strtotime($date_planned_start_temp))] as $hour_key => $hours) {

				if((strtotime($date_planned_start_temp) >= strtotime(date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_from'] )) && (strtotime($date_planned_start_temp) <= strtotime(date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_to'] ))){
					// <= date_planned_start_temp <=

					if(!isset($work_orders[$key]['date_planned_start']) || $work_orders[$key]['date_planned_start'] == null){
						$work_orders[$key]['date_planned_start'] = to_sql_date($date_planned_start_temp, true);

						$work_order['date_planned_start'] = to_sql_date($date_planned_start_temp, true);
					}

					if($flag_next_day == true){

						$hours_time = (strtotime(date('Y-m-d', strtotime($date_planned_start_temp.'+'.'1 days')).' '.$hours['work_to'])-strtotime($date_planned_start_temp))/60;

					}else{
						$hours_time = (strtotime($hours['work_to'])-strtotime(date('H:i:s', strtotime($date_planned_start_temp))))/60;
					}


					if($hours_time == 0 && $hour_key+1 == count($wh_by_wcenter[date('N', strtotime($date_planned_start_temp))])){

						$next_date = date('Y-m-d', strtotime($date_planned_start_temp.'+'.'1 days')).' 00:00:00';
						$hour_diff = round(abs(strtotime($next_date) - strtotime($date_planned_start_temp))/60, 2);
						$date_planned_start_temp = date('Y-m-d H:i:s', strtotime($date_planned_start_temp.'+'.$hour_diff.' minute'));
						$flag_next_day = true;

						$this->get_plan_start_finished($work_orders, $work_order, $key, $wh_by_wcenter, $date_planned_start_temp, $duration_expected_temp, true);

					}

					if((float)$duration_expected_temp > (float)$hours_time){
						$duration_expected_temp = (float)$duration_expected_temp - (float)$hours_time;
						$date_planned_start_temp = date('Y-m-d H:i:s', strtotime($date_planned_start_temp.'+'.$hours_time.' minute'));

					}else{
						$date_planned_start_temp = date('Y-m-d H:i:s', strtotime($date_planned_start_temp.'+'.(float)$duration_expected_temp.' minute'));
						$duration_expected_temp = 0;

					}
					
					$work_orders[$key]['date_planned_finished'] = to_sql_date($date_planned_start_temp, true);

					$work_order['date_planned_finished'] = to_sql_date($date_planned_start_temp, true);


					if($duration_expected_temp != 0 && $hour_key+1 == count($wh_by_wcenter[date('N', strtotime($date_planned_start_temp))])){

						$next_date = date('Y-m-d', strtotime($date_planned_start_temp.'+'.'1 days')).' 00:00:00';
						$hour_diff = round(abs(strtotime($next_date) - strtotime($date_planned_start_temp))/60, 2);
						$date_planned_start_temp = date('Y-m-d H:i:s', strtotime($date_planned_start_temp.'+'.$hour_diff.' minute'));
						$flag_next_day = true;

						$this->get_plan_start_finished($work_orders, $work_order, $key, $wh_by_wcenter, $date_planned_start_temp, $duration_expected_temp, true);

					}

				}elseif(strtotime($date_planned_start_temp) < strtotime(date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_from'] )){
					// date_planned_start_temp <


					if(!isset($work_orders[$key]['date_planned_start']) && $work_orders[$key]['date_planned_start'] == null){
						$work_orders[$key]['date_planned_start'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_from'] ));
						$work_order['date_planned_start'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_from'] ));
					}
					$date_planned_start_temp = date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_from'] ;

					if($flag_next_day == true){

						$hours_time = (strtotime(date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_to'])-strtotime($date_planned_start_temp))/60;
					}else{
						$hours_time = (strtotime($hours['work_to'])-strtotime($hours['work_from']))/60;
					}

					if((float)$duration_expected_temp > (float)$hours_time){
						$duration_expected_temp = (float)$duration_expected_temp - (float)$hours_time;
						$date_planned_start_temp = date('Y-m-d H:i:s', strtotime($date_planned_start_temp.'+'.$hours_time.' minute'));

					}else{
						$date_planned_start_temp = date('Y-m-d H:i:s', strtotime($date_planned_start_temp.'+'.(float)$duration_expected_temp.' minute'));
						$duration_expected_temp = 0;
					}

					$work_orders[$key]['date_planned_finished'] = $date_planned_start_temp;
					$work_order['date_planned_finished'] = $date_planned_start_temp;

					if($duration_expected_temp != 0 && $hour_key+1 == count($wh_by_wcenter[date('N', strtotime($date_planned_start_temp))])){
						$next_date = date('Y-m-d', strtotime($date_planned_start_temp.'+'.'1 days')).' 00:00:00';
						$hour_diff = round(abs(strtotime($next_date) - strtotime($date_planned_start_temp))/60, 2);
						$date_planned_start_temp = date('Y-m-d H:i:s', strtotime($date_planned_start_temp.'+'.$hour_diff.' minute'));
						$flag_next_day = true;

						$this->get_plan_start_finished($work_orders, $work_order, $key, $wh_by_wcenter, $date_planned_start_temp, $duration_expected_temp, true);

					}

				}elseif(strtotime($date_planned_start_temp) > strtotime(date('Y-m-d', strtotime($date_planned_start_temp)).' '.$hours['work_to'] ) && $hour_key+1 == count($wh_by_wcenter[date('N', strtotime($date_planned_start_temp))])){

					$next_date = date('Y-m-d', strtotime($date_planned_start_temp.'+'.'1 days')).' 00:00:00';
					$hour_diff = round(abs(strtotime($next_date) - strtotime($date_planned_start_temp))/60, 2);
					$date_planned_start_temp = date('Y-m-d H:i:s', strtotime($date_planned_start_temp.'+'.$hour_diff.' minute'));
					$flag_next_day = true;

					$this->get_plan_start_finished($work_orders, $work_order, $key, $wh_by_wcenter, $date_planned_start_temp, $duration_expected_temp, true);

				}

			}


			return ['work_orders' => $work_orders, 'date_planned_start_temp' => $date_planned_start_temp, 'flag_next_day' => $flag_next_day];

		}else{

			$next_date = date('Y-m-d', strtotime($date_planned_start_temp.'+'.'1 days')).' 00:00:00';
			$hour_diff = round(abs(strtotime($next_date) - strtotime($date_planned_start_temp))/60, 2);
			$date_planned_start_temp = date('Y-m-d H:i:s', strtotime($date_planned_start_temp.'+'.$hour_diff.' minute'));
			$flag_next_day = true;

			$this->get_plan_start_finished($work_orders, $work_order, $key, $wh_by_wcenter, $date_planned_start_temp, $duration_expected_temp, true);
		}

	}

	/**
	 * get operation recursive
	 * @param  [type] $data  
	 * @param  [type] $mo_id 
	 * @return [type]        
	 */
	public function get_operation_recursive($data, $mo_id)
	{
		$work_orders=[];

		foreach ($data as $value) {
		    //get list bills of material with BOM type is "manufacture this product"
			$product = $this->get_product($value['product_id']);
			if($product){
				$parent_id = $product->parent_id;
				$attributes = $product->attributes;
			}

			if(isset($parent_id)){
				$sql_where = "( (product_id = ".$parent_id." AND product_variant_id = ".$value['product_id'].") OR( product_id = ".$parent_id." AND (product_variant_id = 0 OR product_variant_id is null))) AND bom_type = 'kit'"; 
			}else{
				$sql_where = "product_id = ".$value['product_id']."  AND bom_type = 'kit' ";
			}

			$this->db->select('*');
			$this->db->from(db_prefix() . 'mrp_bill_of_materials');
			$this->db->where($sql_where);
			$this->db->order_by('id', 'desc');

			$bill_of_material = $this->db->get()->row();
			
			//get bom detail
			if($bill_of_material){
				$mo_product_id = $bill_of_material->product_id;
				$product_quantity = $bill_of_material->product_qty;
				$product_unit = $bill_of_material->unit_id;

				//get operation from routing id
				$operations = $this->get_operation('', $bill_of_material->routing_id);
				foreach ($operations as $key => $operation) {

					if($key == 0 ){
						$status = 'ready';
					}else{
						$status = 'waiting_for_another_wo';
					}

					array_push($work_orders, [
						'manufacturing_order_id' => $mo_id,
						'product_id' => $mo_product_id,
						'qty_produced' => 0,
						'qty_production' => $product_quantity,
						'qty_producing' => $product_quantity,
						'unit_id' => $product_unit,
						'date_planned_start' => to_sql_date('', true),
						'date_planned_finished' => to_sql_date('', true),
						'routing_detail_id' => $operation['id'],
						'duration_expected' => $operation['default_duration'],
						'operation_name' => $operation['operation'],
						'work_center_id' => $operation['work_center_id'],
						'status' => $status,

					]);
				}

				$bill_of_material_details = $this->get_bill_of_material_details('', $bill_of_material->id);

			}

		}
		return $work_orders;

	}

	/**
	 * get last bill of material by product
	 * @param  [type] $product_id 
	 * @return [type]             
	 */
	public function get_last_bill_of_material_by_product($product_id, $bom_detail_to_consume, $mo_id)
	{
		$component_arr=[];

	    //get list bills of material with BOM type is "manufacture this product"
		$product = $this->get_product($product_id);
		if($product){
			$parent_id = $product->parent_id;
			$attributes = $product->attributes;
		}

		if(isset($parent_id) && (int)$parent_id != 0){
			$sql_where = "( (product_id = ".$parent_id." AND product_variant_id = ".$product_id.") OR( product_id = ".$parent_id." AND (product_variant_id = 0 OR product_variant_id is null))) AND bom_type = 'kit'"; 
		}else{
			$sql_where = "product_id = ".$product_id."  AND bom_type = 'kit'";
		}

		$this->db->select('*');
		$this->db->from(db_prefix() . 'mrp_bill_of_materials');
		$this->db->where($sql_where);
		$this->db->order_by('id', 'desc');

		$bill_of_material = $this->db->get()->row();
			
		//get bom detail
		if($bill_of_material){
			$bom_qty = $bill_of_material->product_qty;

			$bill_of_material_details = $this->get_bill_of_material_details('', $bill_of_material->id);
			foreach ($bill_of_material_details as $bom_detail_value) {

				if($bom_detail_to_consume != ''){

					$qty_to_consume = (float)$bom_detail_value['product_qty']*(float)$bom_detail_to_consume/$bom_qty;
				}else{
				//default mo for 1 product
					$qty_to_consume = $bom_detail_value['product_qty']*1/$bom_qty;
				}

				array_push($component_arr, [
					'id' => 0,
					'product_id' => $bom_detail_value['product_id'],
					'unit_id' => $bom_detail_value['unit_id'],
					'qty_to_consume' =>  $qty_to_consume,
					'qty_reserved' =>  0,
					'qty_done' =>  0,
					'check_inventory_qty' =>  false,
				]);
			}

		}
		return $component_arr;
	}


	/**
	 * get work order
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function get_work_order($id = false)
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'mrp_work_orders')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from ' . db_prefix() . 'mrp_work_orders')->result_array();
		}
	}

	/**
	 * get working tim by ids
	 * @param  [type] $ids 
	 * @return [type]      
	 */
	public function get_working_time_by_ids($ids)
	{
		$day_of_week=[];
		$day_of_week['monday'] = '1';
		$day_of_week['tuesday'] = '2';
		$day_of_week['wednesday'] = '3';
		$day_of_week['thursday'] = '4';
		$day_of_week['friday'] = '5';
		$day_of_week['saturday'] = '6';
		$day_of_week['sunday'] = '7';

		//TODO: get working hours by day
		$working_hour_data=[];

	    $this->db->select(db_prefix().'mrp_work_centers.id as work_center_id, working_hour_id, day_of_week,day_period, hours_per_day, work_from, work_to ');
		$this->db->from(db_prefix() . 'mrp_working_hours');
		$this->db->join(db_prefix() . 'mrp_working_hour_times', db_prefix() . 'mrp_working_hours.id = ' . db_prefix() . 'mrp_working_hour_times.working_hour_id', 'left');
		$this->db->join(db_prefix() . 'mrp_work_centers', db_prefix() . 'mrp_working_hours.id = ' . db_prefix() . 'mrp_work_centers.working_hours', 'left');
		$this->db->where(db_prefix().'mrp_work_centers.id IN ('.implode(",",$ids).')');
		$working_hours = $this->db->get()->result_array();

		$working_hour_data=[];
		foreach ($working_hours as $key => $value) {
		    $working_hour_data['wcenter'.$value['work_center_id']][$day_of_week[$value['day_of_week']]][] = ['work_from' => $value['work_from'], 'work_to' => $value['work_to']];
		}

		return $working_hour_data;
	}

	/**
	 * get work order previous next
	 * @param  [type] $work_order_id 
	 * @return [type]                
	 */
	public function get_work_order_previous_next($work_order_id, $manufacturing_order_id)
	{
		$prev_id='';
		$next_id='';
		$pager_value=0;
		$pager_limit=0;

		$work_orders=[];
		$this->db->where('manufacturing_order_id', $manufacturing_order_id);
		$this->db->order_by('id', 'desc');
		$get_work_orders= $this->db->get(db_prefix() . 'mrp_work_orders')->result_array();
		foreach ($get_work_orders as $key => $value) {
			$value['index'] = $key+1;
		    $work_orders[$value['id']] =  $value;
		}

		$pager_value = $work_orders[(int)$work_order_id]['index'];
		$pager_limit = count($get_work_orders);
		//prev_id
		if(count($work_orders) > 0){
			if(isset($work_orders[(int)$work_order_id+1])){
				$prev_id = (int)$work_order_id+1;
			}else{
				$prev_id = $get_work_orders[count($work_orders)-1]['id'];
			}
		}else{
			$prev_id = (int)$work_order_id;

		}

		//next_id
		if(count($work_orders) > 0){
			if(isset($work_orders[(int)$work_order_id-1])){
				$next_id = (int)$work_order_id-1;

			}else{
				$next_id = $get_work_orders[0]['id'];

			}
		}else{
			$next_id = (int)$work_order_id;

		}

		$data=[];
		$data['pager_value']= $pager_value;
		$data['pager_limit']= $pager_limit;
		$data['prev_id']= $prev_id;
		$data['next_id']= $next_id;

		return $data;
	}

	/**
	 * update work order status
	 * @param  [type] $id   
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function update_work_order_status($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update(db_prefix().'mrp_work_orders', $data);
		if($this->db->affected_rows() > 0){
			return true;
		}
		return false;
	}

	/**
	 * add time tracking
	 * @param [type] $id   
	 * @param [type] $data 
	 */
	public function add_time_tracking($data)
	{
		$insert_id = $this->db->insert(db_prefix().'mrp_work_order_time_trackings', $data);
		if($insert_id ){
			return true;
		}
		return false;
	}

	/**
	 * update time tracking
	 * @param  [type] $id   
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function update_time_tracking($work_order_id, $data)
	{
		$this->db->where('work_order_id', $work_order_id);
		$this->db->order_by('id', 'desc');
		$time_tracking = $this->db->get(db_prefix() . 'mrp_work_order_time_trackings')->row();

		if($time_tracking){
			$start_date = strtotime($time_tracking->from_date);
			$to_date = strtotime($data['to_date']);
			$duration = abs($to_date - $start_date)/(60);

			$data['duration'] = $duration;

			$this->db->where('id', $time_tracking->id);
			$this->db->update(db_prefix().'mrp_work_order_time_trackings', $data);
			if($this->db->affected_rows() > 0){
				return true;
			}
		}

		return false;
	}

	/**
	 * get time tracking details
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_time_tracking_details($id)
	{
		$this->db->select(db_prefix().'mrp_work_order_time_trackings.id , mrp_work_order_time_trackings.work_order_id, from_date, to_date, duration, CONCAT(firstname," ",lastname) as full_name');
		$this->db->from(db_prefix() . 'mrp_work_order_time_trackings');
		$this->db->join(db_prefix() . 'staff', db_prefix() . 'mrp_work_order_time_trackings.staff_id = ' . db_prefix() . 'staff.staffid', 'left');
	    $this->db->where('work_order_id', $id);
		$time_trackings = $this->db->get()->result_array();

	    $row = count($time_trackings);

	    $data=[];
	    $data['rows'] = $row;
	    $data['time_trackings'] = $time_trackings;

	    return $data;
	}

	/**
	 * get total duration
	 * @param  [type] $work_order_id 
	 * @return [type]                
	 */
	public function get_total_duration($work_order_id)
	{
		$duration = 0;
		$this->db->select('sum(duration) as duration');
	    $this->db->where('work_order_id', $work_order_id);
	    $time_tracking = $this->db->get(db_prefix() . 'mrp_work_order_time_trackings')->row();
	    if($time_tracking){
	    	$duration = $time_tracking->duration;
	    }
	    
	    return $duration;
	}

	/**
	 * mo mark as done
	 * @return [type] 
	 */
	public function  wo_mark_as_done($work_order_id, $manufacturing_order_id)
	{
		$affected_rows=0;
		$current_time=date('Y-m-d H:i:s');

		//Update time tracking
		$data_update=[
			'work_order_id' => $work_order_id,
			'to_date' => $current_time,
			'staff_id' => get_staff_user_id(),
		];
		$update_time_tracking = $this->update_time_tracking($work_order_id, $data_update);
		if($update_time_tracking){
			$affected_rows++;
		}

		//update consumed quantity
		$this->update_mo_consumed($work_order_id, $manufacturing_order_id);

		$duration = $this->get_total_duration($work_order_id);
		$work_order = $this->get_work_order($work_order_id);

		$qty_produced = 0;
		if($work_order){
			$qty_produced = $work_order->qty_producing;
		}

		$update_work_order=[];
		$update_work_order['status'] = 'finished';
		$update_work_order['date_finished'] = to_sql_date($current_time, true);
		$update_work_order['real_duration'] = $duration;
		$update_work_order['qty_produced'] = $qty_produced;
		$update_work_order['qty_producing'] = 0;

		$update_work_order = $this->update_work_order_status($work_order_id, $update_work_order);
		if($update_work_order){
			$affected_rows++;
		}

		//update next work order status
		$get_work_order_previous_next = $this->get_work_order_previous_next($work_order_id, $manufacturing_order_id);
		if($get_work_order_previous_next['next_id']){
			$next_work_order = $this->get_work_order($get_work_order_previous_next['next_id']);

			if($next_work_order && $next_work_order->status == 'waiting_for_another_wo'){
				$update_next_work_order = $this->update_work_order_status($get_work_order_previous_next['next_id'], ['status' => 'ready']);
				if($update_next_work_order){
					$affected_rows++;
				}
			}
		}

		if($affected_rows > 0){
			return true;
		}
		return false;
	}

	/**
	 * get work order timeline
	 * @param  string $manufacturing_order_id 
	 * @return [type]                         
	 */
	public function get_work_order_timeline($manufacturing_order_id='')
	{

		$data=[];
		//get manufacturing order
		$mo_ids=[];
		if($manufacturing_order_id != ''){
			$this->db->where('id', $manufacturing_order_id);
			$manufacturing_order = $this->db->get(db_prefix().'mrp_manufacturing_orders')->result_array();
			$mo_ids[] = $manufacturing_order_id; 

		}else{
			$this->db->where('status !=', 'draft');
			$manufacturing_order = $this->db->get(db_prefix().'mrp_manufacturing_orders')->result_array();

			foreach ($manufacturing_order as $value) {
				$mo_ids[] = $value['id'];
			}
		}

		$this->db->where('manufacturing_order_id  IN ('.implode(",",$mo_ids) .') ');
		$this->db->order_by('id', 'desc');

		$work_order_data = $this->db->get(db_prefix().'mrp_work_orders')->result_array();

		$work_orders=[];
		foreach ($work_order_data as $key => $work_order) {
			$work_orders[$work_order['manufacturing_order_id']][] = $work_order;
		}

		foreach ($manufacturing_order as $value) {
			$row          = [];
			$row['id']    = 'mo_' . $value['id'];
			$row['start'] = $value['date_planned_start'];
			$row['end']   = $value['date_planned_finished'];
			$row['name']  = $value['manufacturing_order_code'].' - '.mrp_get_product_name($value['product_id']);
			$data[]       = $row;

			if(isset($work_orders[$value['id']])){
				foreach ($work_orders[$value['id']] as $wo_key => $wo_value) {
					$note                 = [];
					$note['start']        = $wo_value['date_planned_start'];
					$note['progress']     = 100;
					$note['name']         = mrp_get_product_name($wo_value['product_id']).' - '.$wo_value['operation_name'] .' - '._l($wo_value['status']);
					$note['id']           = $wo_value['id'];
					$note['dependencies'] = 'mo_' . $wo_value['manufacturing_order_id'];
					$note['end'] = $wo_value['date_planned_finished'];
					$note['duration_expected'] = round($wo_value['duration_expected'], 2);
					$note['real_duration'] = round($wo_value['real_duration'], 2);
					$note['quantity_produced'] = $wo_value['qty_produced'].'/'.$wo_value['qty_production'];

					switch ($wo_value['status']) {

						case 'waiting_for_another_wo':
						$note['custom_class'] = 'br_waiting_for_another_wo';
						break;
						case 'ready':
						$note['custom_class'] = 'br_ready';
						break;
						case 'in_progress':
						$note['custom_class'] = 'br_in_progress';
						break;
						case 'finished':
						$note['custom_class'] = 'br_finished';
						break;
						case 'pause':
						$note['custom_class'] = 'br_pause';
						break;
					}
					$data[] = $note;
				}
			}
		}

		if ($data == []) {
			$data[][] = [];
		}
		return $data;

	}

	/**
	 * check manufacturing order done
	 * @param  [type] $mo_id 
	 * @return [type]        
	 */
	public function check_manufacturing_order_type($mo_id)
	{
		//get mo
		$manufacturing_order = $this->get_manufacturing_order($mo_id);

		$check_mo_done='';
		$check_create_purchase_request='';
		$check_availability='';
		$check_planned = false;
		$bom_ready_to_produce = 'all_available';

		$data_color=[];

		if($manufacturing_order['manufacturing_order']){
			$bom = $this->get_bill_of_materials($manufacturing_order['manufacturing_order']->bom_id);
			if($bom){
				$bom_ready_to_produce = $bom->ready_to_produce;
			}

			if($manufacturing_order['manufacturing_order']->status != 'draft' ){
				$this->db->where('manufacturing_order_id', $mo_id);
				$this->db->where('status != ', 'finished');
				$work_orders = $this->db->get(db_prefix().'mrp_work_orders')->result_array();
				if(count($work_orders) > 0){
					$check_mo_done = false;
				}else{

					$check_mo_done = true;
				}
			}else{
				$check_mo_done = false;
			}
		}else{
			$check_mo_done = false;
		}

		//check_create_purchase_request
		if($manufacturing_order['manufacturing_order']){
			if($manufacturing_order['manufacturing_order']->purchase_request_id == null && $manufacturing_order['manufacturing_order']->status != 'done' && $manufacturing_order['manufacturing_order']->status != 'cancelled'){
				foreach ($manufacturing_order['manufacturing_order_detail'] as $mo_detail) {
					if($mo_detail['qty_reserved'] < $mo_detail['qty_to_consume']){
						$check_create_purchase_request = true;
					}
				}

				if(!$check_create_purchase_request){
					$check_create_purchase_request = false;
				}
			}else{
				$check_create_purchase_request = false;
			}
		}else{
			$check_create_purchase_request = false;
		}


		//check availability
		if($manufacturing_order['manufacturing_order_detail']){
			$check_planned_temp = true;
			foreach ($manufacturing_order['manufacturing_order_detail'] as $mo_key => $mo_detail) {
				if($bom_ready_to_produce == 'all_available'){

					if($mo_detail['qty_reserved'] < $mo_detail['qty_to_consume']){
						$check_planned_temp = false;
					}

				}else{
					// components_for_1st
					if($mo_key == 0 && $mo_detail['qty_reserved'] >= $mo_detail['qty_to_consume']){
						$check_planned = true;
					}
				}

				if($mo_detail['qty_reserved'] < $mo_detail['qty_to_consume'] && $manufacturing_order['manufacturing_order']->status != 'done'){
					$check_availability = true;

					$data_color[$mo_key] = '#d8341b';
				}else{
					$data_color[$mo_key] = '#4caf50';
				}
			}
			if(!$check_availability){
				$check_availability = false;
			}

			if($bom_ready_to_produce == 'all_available'){
				if($check_planned_temp){
					$check_planned = true;
				}
			}

		}else{
			$check_availability = false;
		}

		$result=[];
		$result['check_mo_done'] = $check_mo_done;
		$result['check_create_purchase_request'] = $check_create_purchase_request;
		$result['check_availability'] = $check_availability;
		$result['data_color'] = $data_color;
		$result['check_planned'] = $check_planned;

		return $result;
	}


	/**
	 * mo mark as done
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function mo_mark_as_done($id, $quantity)
	{	
		$this->load->model('warehouse/warehouse_model');

		$affected_rows=0;
		$available_quantity = true;
		$mo_after_done = [];

	    //update inventory quantity related Inventory Module
		$mo = $this->get_manufacturing_order($id);
		//check available quantity before mark as done
		foreach ($mo['manufacturing_order_detail'] as $key => $mo_value) {
			if((float)$mo_value['qty_reserved'] != 0 && (float)$mo_value['qty_to_consume'] != 0){
				if($mo_value['qty_reserved'] == 0){
					$available_quantity = false;
				}
				$mo_after_done[] = [
					'id' => $mo_value['id'],
					'qty_reserved' => 0,
					'qty_done' => $mo_value['qty_reserved'],
				];
			}
		}

		if($available_quantity){

			$manufacturing_order_costing = $this->get_manufacturing_order_costing($mo['manufacturing_order']->id);
			$total_manufaturing_order_costing = (float)$manufacturing_order_costing['total_material_cost'] + (float)$manufacturing_order_costing['total_labour_cost'];
			$purchase_price = $total_manufaturing_order_costing/(float)$mo['manufacturing_order']->product_qty;

			//create inventory delivery voucher for component use in Mo, write log, don't change inventory quantity
			if($mo){
				$insert_id = $this->mo_add_goods_delivery($mo);

				if($insert_id){
					$goods_delivery_detail = $this->warehouse_model->get_goods_delivery_detail($insert_id);
					foreach ($goods_delivery_detail as $goods_delivery_detail_value) {
						$goods_delivery_detail_value['purchase_price'] = $purchase_price;					
						$this->warehouse_model->add_goods_transaction_detail($goods_delivery_detail_value, 2);
					}
				}
			}

			if($mo['manufacturing_order']){
				$mo['manufacturing_order']->product_qty = $quantity;

				$add_receipt_voucher = $this->mrp_add_good_receipt_voucher($mo['manufacturing_order']);
				if($add_receipt_voucher){
					$affected_rows++;	
				}
			}else{
				return false;
			}


			$get_product = $this->get_product($mo['manufacturing_order']->product_id);
			if($get_product){
				if((float)$get_product->purchase_price == 0){
					$this->db->where('id', $mo['manufacturing_order']->product_id);
					$this->db->update(db_prefix().'items', ['purchase_price' => $purchase_price]);
				}
			}


	    //update manufacturing order status to Finished
			$update_mo_status = $this->update_manufacturing_order_status($id, ['status' => 'done']);
			if($update_mo_status){
				$affected_rows++;
			}
			// update reserved, consumed
			if(count($mo_after_done) > 0){
				$this->db->update_batch(db_prefix().'mrp_manufacturing_order_details', $mo_after_done, 'id');
			}

			if($affected_rows > 0){
				return true;
			}
			return false;
		}else{
			return false;
		}
	}

	/**
	 * mo create purchase request
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function mo_create_purchase_request($id)
	{
		$this->load->model('taxes_model');
		$this->load->model('departments_model');
		$this->load->model('purchase/purchase_model');

		$get_base_currency = get_base_currency();
		$currency = 0;
		if($get_base_currency){
			$currency = $get_base_currency->id;
		}

		$mo = $this->get_manufacturing_order($id);
		if(isset($mo['manufacturing_order_detail'])){
			$mo_detail=[];

			$arr_product_id=[];
			foreach ($mo['manufacturing_order_detail'] as $key => $mo_value) {
				$arr_product_id[] = $mo_value['product_id'];
			}

			$this->db->where('id IN ('.implode(",",$arr_product_id) .')');
			$products = $this->db->get(db_prefix() . 'items')->result_array();

			$arr_products=[];
			foreach ($products as $product) {
				$arr_products[$product['id']] = $product;
			}

			$pu_subtotal=0;
			$pu_total_tax=0;
			$pu_total=0;

			foreach ($mo['manufacturing_order_detail'] as $key => $mo_value) {

				if($mo_value['qty_reserved'] != $mo_value['qty_to_consume']){

					$tax_select = [];

					$pu_qty = $mo_value['qty_to_consume'] - $mo_value['qty_reserved'];

					$unit_price = isset($arr_products[$mo_value['product_id']]) ? (float)$arr_products[$mo_value['product_id']]['purchase_price'] : 0;
					$list_taxrate = isset($arr_products[$mo_value['product_id']]) ? $arr_products[$mo_value['product_id']]['supplier_taxes_id'] : '';

					$taxrate= 0 ;
					$tax_id='';
					if(new_strlen($list_taxrate) > 0){
						$array_taxrate = new_explode(',', $list_taxrate);

						foreach ($array_taxrate as $taxrate_id) {
							$tax = $this->taxes_model->get($taxrate_id);
							if($tax){
								$taxrate += (float)$tax->taxrate;
								$tax_select[] = $tax->name.'|'.$tax->taxrate;
							}
						}

					}

					$tax_value = (float)$unit_price*$pu_qty*$taxrate/100;
					$into_money = (float)$unit_price*$pu_qty;
					$total = (float)$unit_price*$pu_qty+$tax_value;

					$pu_total_tax += $tax_value;
					$pu_subtotal += $into_money;
					$pu_total += $total;

					array_push($mo_detail, [
						'item_code' => $mo_value['product_id'],
						'unit_id' => $mo_value['unit_id'],
						'unit_price' => $unit_price,
						'quantity' => $pu_qty,
						'into_money' => $into_money,
						'tax' => $tax_id,
						'tax_value' => $tax_value, 
						'total' => $total,
						'inventory_quantity' => 0,
						'item_text' => mo_get_commodity_name($mo_value['product_id']),
						'tax_select' => $tax_select,
					]);
				}

			}

			$prefix = get_purchase_option('pur_request_prefix');

			$staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
			if(count($staff_departments) > 0){
				$data['department'] = $staff_departments[0];
			}else{
				$staff_departments = $this->departments_model->get();
				if(count($staff_departments) > 0){
					$data['department'] = $staff_departments[0]['departmentid'];
				}else{
					$data['department'] = 0;
				}
			}

			$dpm_name = department_pur_request_name($data['department']);

			$purchase_data=[];
			$purchase_data['number'] = get_purchase_option('next_pr_number');
			$purchase_data['pur_rq_code'] =  $prefix.'-'.str_pad($purchase_data['number'],5,'0',STR_PAD_LEFT).'-'.date('M-Y').'-'.$dpm_name;
			$purchase_data['pur_rq_name'] =  'PUR create from Manufacturing '.$mo['manufacturing_order']->manufacturing_order_code;
			$purchase_data['project'] =  '';
			$purchase_data['type'] =  '';
			$purchase_data['sale_invoice'] =  '';
			$purchase_data['requester'] =  get_staff_user_id();
			$purchase_data['from_items'] =  '1';
			$purchase_data['rq_description'] =  _l('this_puchase_request_create_from_MO_module').$mo['manufacturing_order']->manufacturing_order_code;
			$purchase_data['subtotal'] =  $pu_subtotal;
			$purchase_data['total_mn'] =  $pu_total;
			$purchase_data['department'] =  $data['department'];
			$purchase_data['currency'] =  $currency;
			$purchase_data['from_currency'] =  $currency;
			$purchase_data['currency_rate'] =  1;

			$request_detail_temp=[];
			foreach ($mo_detail as $mo_detail_value) {
				$request_detail_temp[] = [
					'item_code' => $mo_detail_value['item_code'],
					'unit_id' => $mo_detail_value['unit_id'],
					'unit_price' => $mo_detail_value['unit_price'],
					'into_money' => $mo_detail_value['into_money'],
					'total' => $mo_detail_value['total'],
					'tax_value' => $mo_detail_value['tax_value'],
					'item_text' => $mo_detail_value['item_text'] ?? '',
					'quantity' => $mo_detail_value['quantity'],
					'tax_select' => $mo_detail_value['tax_select'],
				];
			}

			$purchase_data['newitems'] = $request_detail_temp;

          	//add purchase request
			$pur_request_id = $this->purchase_model->add_pur_request($purchase_data);

			return $pur_request_id;
		}

	}

	/**
	 * mo add pur request
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function mo_add_pur_request($data)
	{

		$data['request_date'] = date('Y-m-d H:i:s');
		$data['status'] = 2;
		$data['from_items'] = 1;

		$data['subtotal'] = reformat_currency_pur($data['subtotal']);

		if(isset($data['total_mn'])){
			$data['total'] = reformat_currency_pur($data['total_mn']);    
			unset($data['total_mn']);
		}

		$data['total_tax'] = $data['total'] - $data['subtotal'];

		$dpm_name = department_pur_request_name($data['department']);
		if(mrp_get_status_modules('purchase')){
			$prefix = get_purchase_option('pur_order_prefix');
		}else{
			$prefix = '#PO';
		}

		$this->db->where('pur_rq_code',$data['pur_rq_code']);
		$check_exist_number = $this->db->get(db_prefix().'pur_request')->row();

		while($check_exist_number) {
			$data['number'] = $data['number'] + 1;
			$data['pur_rq_code'] =  $prefix.'-'.str_pad($data['number'],5,'0',STR_PAD_LEFT).'-'.date('M-Y').'-'.$dpm_name;
			$this->db->where('pur_rq_code',$data['pur_rq_code']);
			$check_exist_number = $this->db->get(db_prefix().'pur_request')->row();
		}

		$data['hash'] = app_generate_hash();



		$rq_detail = [];
		if(isset($data['request_detail'])){
			$request_detail = json_decode($data['request_detail']);
			unset($data['request_detail']);

			$row = [];
			$rq_val = [];
			$header = [];

			$header[] = 'item_code';
			$header[] = 'unit_id';
			$header[] = 'unit_price';
			$header[] = 'quantity';
			$header[] = 'into_money';
			$header[] = 'tax';
			$header[] = 'tax_value';
			$header[] = 'total';
			$header[] = 'inventory_quantity';

			foreach ($request_detail as $key => $value) {

				if($value[0] != '' && $value[0] != null){
					$rq_detail[] = array_combine($header, $value);
				}
			}
		}

		$this->db->insert(db_prefix().'pur_request',$data);
		$insert_id = $this->db->insert_id();
		if($insert_id){

            // Update next purchase order number in settings
			$next_number = $data['number']+1;
			$this->db->where('option_name', 'next_pr_number');
			$this->db->update(db_prefix() . 'purchase_option',['option_val' =>  $next_number,]);

			if(count($rq_detail) > 0){
				foreach($rq_detail as $key => $rqd){
					$rq_detail[$key]['pur_request'] = $insert_id;
					$rq_detail[$key]['tax_rate'] = $this->get_tax_rate_by_id($rqd['tax']);
					$rq_detail[$key]['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;
					if($data['status'] == 2 && $data['from_items'] != 1){
						$item_data['description'] = $rqd['item_text'];
						$item_data['purchase_price'] = $rqd['unit_price'];
						$item_data['unit_id'] = $rqd['unit_id'];
						$item_data['rate'] = '';
						$item_data['sku_code'] = '';
						$item_data['commodity_barcode'] = $this->generate_commodity_barcode();
						$item_data['commodity_code'] = $this->generate_commodity_barcode();
						$item_id = $this->add_commodity_one_item($item_data);
						if($item_id){
							$rq_detail[$key]['item_code'] = $item_id; 
						}

					}
				}
				$this->db->insert_batch(db_prefix().'pur_request_detail',$rq_detail);
			}

			return $insert_id;
		}
		return false;
	}

	/**
	 * mrp add good receipt voucher
	 * @param  [type] $mo_data 
	 * @return [type]          
	 */
	public function mrp_add_good_receipt_voucher($mo_data)
    {
    	$affected_rows=0;

    	$this->load->model('warehouse/warehouse_model');
    	//get product data
    	$product = $this->get_product($mo_data->product_id);
    	$manufacturing_order_costing = $this->get_manufacturing_order_costing($mo_data->id);
    	$total_manufaturing_order_costing = (float)$manufacturing_order_costing['total_material_cost'] + (float)$manufacturing_order_costing['total_labour_cost'];

    	if(!$product){
    		return false;
    	}

    	$good_receipt=[];
    	$good_receipt_detail=[];

    	$purchase_price = $total_manufaturing_order_costing/(float)$mo_data->product_qty;

    	$tax_value = 0;
    	$tax = $this->warehouse_model->get_taxe_value($product->tax);
    	if($tax){
    		$tax_value = $tax->taxrate;
    	}

    	$good_receipt['approval'] = 1;
		$good_receipt['goods_receipt_code'] = $this->warehouse_model->create_goods_code();
		$good_receipt['date_c'] = date('Y-m-d');
		$good_receipt['date_add'] = date('Y-m-d');
		$good_receipt['addedfrom'] =  get_staff_user_id();

		$good_receipt['total_tax_money'] = 0;
		$good_receipt['total_goods_money'] = (float)($purchase_price)*(float)($mo_data->product_qty);
		$good_receipt['value_of_inventory'] = (float)($purchase_price)*(float)($mo_data->product_qty);
		$good_receipt['total_money'] = (float)$good_receipt['total_tax_money']+(float)$good_receipt['total_goods_money'];
		$good_receipt['description'] = _l('this_receipt_voucher_for_the_product_after_it_has_been_manufactured_from_the_manufacturing_module').$mo_data->manufacturing_order_code;

		$good_receipt_detail['commodity_code'] = $product->id;
		$good_receipt_detail['warehouse_id'] = $mo_data->finished_products_warehouse_id;
		$good_receipt_detail['unit_id'] = $product->unit_id;
		$good_receipt_detail['quantities'] = $mo_data->product_qty;
		$good_receipt_detail['unit_price'] = $purchase_price;
		$good_receipt_detail['goods_money'] = $good_receipt['total_goods_money'];

		//insert good receipt
		$this->db->insert(db_prefix() . 'goods_receipt', $good_receipt);
		$insert_id = $this->db->insert_id();
		if($insert_id){
			$affected_rows++;
			$good_receipt_detail['goods_receipt_id'] = $insert_id;

			$this->db->insert(db_prefix() . 'goods_receipt_detail', $good_receipt_detail);
			$insert_detail = $this->db->insert_id();
			if($insert_detail){
				$affected_rows++;
			}

			$manufacturing_order_id = $mo_data->id;
			$parent_product_id = 0;
			$product_id = $mo_data->product_id;
			$unit_id = $mo_data->unit_id;
			$change_type = 'mrp_change_quantity';
			$change_quantity = $mo_data->product_qty;
			$created_at = date('Y-m-d H:i:s');
			$staff_id = '';
			$description = _l('bom_change_log_increase_inventory_quantity_after_successful_production');
			$rel_id = $insert_id;
			$rel_type = 'receipt_note';

			$this->bom_change_log($manufacturing_order_id, $parent_product_id, $product_id, $unit_id, $change_type, $change_quantity, $created_at, $staff_id, $description, $rel_id, $rel_type);
		}

		if(isset($insert_id)){
			/*update next number setting*/
			$this->warehouse_model->update_inventory_setting(['next_inventory_received_mumber' =>  get_warehouse_option('next_inventory_received_mumber')+1]);
		}

		//update inventory quantity
		if (isset($insert_id)) {
			if ($good_receipt['approval'] == 1) {
				$this->mrp_update_inventory_quantity($insert_id, 1, 1);
			}
		}

		if($affected_rows > 0){
			return true;
		}
		return false;
    }

    /**
     * mrp update inventory quantity
     * @param  [type] $rel_id   
     * @param  [type] $rel_type 
     * @param  [type] $status   
     * @return [type]           
     */
    public function mrp_update_inventory_quantity($rel_id, $rel_type, $status) 
    {
    	$this->load->model('warehouse/warehouse_model');

    	$data_update = [];

    	switch ($rel_type) {
		//case 1: stock_import
    	case '1':
    		$data_update['approval'] = $status;
    		$this->db->where('id', $rel_id);
    		$this->db->update(db_prefix() . 'goods_receipt', $data_update);

			// //update history stock, inventoty manage after staff approved
    		$goods_receipt_detail = $this->warehouse_model->get_goods_receipt_detail($rel_id);


    		foreach ($goods_receipt_detail as $goods_receipt_detail_value) {

    			$this->warehouse_model->add_goods_transaction_detail($goods_receipt_detail_value, 1);
    			$this->warehouse_model->add_inventory_manage($goods_receipt_detail_value, 1);
    		}

    		return true;
    		break;

    	case '2':
    		$data_update['approval'] = $status;
    		$this->db->where('id', $rel_id);
    		$this->db->update(db_prefix() . 'goods_delivery', $data_update);

			//update history stock, inventoty manage after staff approved

    		$goods_delivery_detail = $this->warehouse_model->get_goods_delivery_detail($rel_id);
    		foreach ($goods_delivery_detail as $goods_delivery_detail_value) {
				// add goods transaction detail (log) after update invetory number

    			$this->warehouse_model->add_inventory_manage($goods_delivery_detail_value, 2);

    		}

    		return true;
    		break;


    		default:
    		return false;
    		break;
    	}
    }

    /**
     * mrp revert inventory quantity child
     * @param  [type] $warehouse_id     
     * @param  [type] $commodity_id     
     * @param  [type] $inventory_number 
     * @return [type]                   
     */
    public function mrp_revert_inventory_quantity_child($warehouse_id, $commodity_id, $inventory_number)
    {
    	$affected_rows=0;

    	$this->db->where('warehouse_id', $warehouse_id);
    	$this->db->where('commodity_id', $commodity_id);
    	$total_rows = $this->db->get(db_prefix().'inventory_manage')->result_array();

    	if(count($total_rows) > 0){
    		//update
    		$inventory_number_update = (float) $total_rows[0]['inventory_number'] + (float) $inventory_number;

    		$this->db->where('id', $total_rows[0]['id']);
    		$this->db->update(db_prefix() . 'inventory_manage', [
    			'inventory_number' => $inventory_number_update,
    		]);
    		if($this->db->affected_rows() > 0){
    			$affected_rows++;
    		}

    	}else{
    		//insert

    		$this->db->insert(db_prefix() . 'inventory_manage', [
    			'warehouse_id' => $warehouse_id,
    			'commodity_id' => $commodity_id,
    			'inventory_number' => $inventory_number,

    		]);
    		if($this->db->affected_rows() > 0){
    			$affected_rows++;
    		}
    	}

    	return $affected_rows;
    }

    /**
     * mrp add inventory quantity
     * @param  [type] $data   
     * @param  [type] $status 
     * @return [type]         
     */
    public function mrp_add_inventory_quantity($data, $status, $warehouse_id)
    {
    	$affected_rows=0;

		// status '1:Goods receipt note 2:Goods delivery note',
    	if ($status == 1) {
    		foreach ($data as $key => $value) {

    			if(new_strlen($warehouse_id) > 0){
    				$revert_inventory_quantity_child = $this->mrp_revert_inventory_quantity_child($warehouse_id, $value['product_id'], $value['qty_reserved']);
    				$affected_rows += (float)$revert_inventory_quantity_child;

    			}else{

    				if(new_strlen($value['warehouse_id']) > 0){
    					$warehouse_id = new_explode(",", $value['warehouse_id'])[0];

    					$revert_inventory_quantity_child = $this->mrp_revert_inventory_quantity_child($warehouse_id, $value['product_id'], $value['qty_reserved']);
    					$affected_rows += (float)$revert_inventory_quantity_child;

    				}else{
    					$this->load->model('warehouse/warehouse_model');
    					$list_warehouse = $this->warehouse_model->get_warehouse();

    					if(count($list_warehouse) > 0){
    						$revert_inventory_quantity_child = $this->mrp_revert_inventory_quantity_child($list_warehouse[0]['warehouse_id'], $value['product_id'], $value['qty_reserved']);
    						$affected_rows += (float)$revert_inventory_quantity_child;
    					}

    				}
    			}

    		}

    	} else {
			//status == 2 export
			//update
			$data_update=[];
    		foreach ($data as $key => $value) {

    			$available_quantity_n =0;
    			$data_warehouse_id ='';

    			if(new_strlen($warehouse_id) > 0){
    				$this->db->where('warehouse_id', $warehouse_id);
    			}
    			$this->db->where('commodity_id', $value['product_id']);
    			$this->db->order_by('id', 'ASC');
    			$result = $this->db->get(db_prefix().'inventory_manage')->result_array();

    			//get available qty
    			foreach ($result as $r_key => $r_value) {
    				$available_quantity_n += (float)$r_value['inventory_number'];
    			}

    			$temp_quantities = $value['qty_reserved'];

    			$expiry_date = '';
    			$lot_number = '';
    			foreach ($result as $result_value) {
    				if (($result_value['inventory_number'] != 0) && ($temp_quantities != 0)) {

    					if ($temp_quantities >= $result_value['inventory_number']) {
    						$temp_quantities = (float) $temp_quantities - (float) $result_value['inventory_number'];

						//update inventory
    						$this->db->where('id', $result_value['id']);
    						$this->db->update(db_prefix() . 'inventory_manage', [
    							'inventory_number' => 0,
    						]);
    						if($this->db->affected_rows() > 0){
    							$affected_rows++;
    						}

    						//log lot number
    						if(($result_value['lot_number'] != null) && ($result_value['lot_number'] != '') ){
    							if(new_strlen($lot_number) != 0){
    								$lot_number .=','.$result_value['lot_number'].','.$result_value['inventory_number'];
    							}else{
    								$lot_number .= $result_value['lot_number'].','.$result_value['inventory_number'];
    							}
    						}

							//log expiry date
    						if(($result_value['expiry_date'] != null) && ($result_value['expiry_date'] != '') ){
    							if(new_strlen($expiry_date) != 0){
    								$expiry_date .=','.$result_value['expiry_date'].','.$result_value['inventory_number'];
    							}else{
    								$expiry_date .= $result_value['expiry_date'].','.$result_value['inventory_number'];
    							}
    						}


							//add warehouse id get from inventory manage
    						if(new_strlen($data_warehouse_id) != 0){
    							$data_warehouse_id .= ','.$result_value['warehouse_id'];
    						}else{
    							$data_warehouse_id .= $result_value['warehouse_id'];

    						}

    					} else {

						//update inventory
    						$this->db->where('id', $result_value['id']);
    						$this->db->update(db_prefix() . 'inventory_manage', [
    							'inventory_number' => (float) $result_value['inventory_number'] - (float) $temp_quantities,
    						]);
    						if($this->db->affected_rows() > 0){
    							$affected_rows++;
    						}

    						//log lot number
    						if(($result_value['lot_number'] != null) && ($result_value['lot_number'] != '') ){
    							if(new_strlen($lot_number) != 0){
    								$lot_number .=','.$result_value['lot_number'].','.$temp_quantities;
    							}else{
    								$lot_number .= $result_value['lot_number'].','.$temp_quantities;
    							}
    						}

							//log expiry date
    						if(($result_value['expiry_date'] != null) && ($result_value['expiry_date'] != '') ){
    							if(new_strlen($expiry_date) != 0){
    								$expiry_date .=','.$result_value['expiry_date'].','.$temp_quantities;
    							}else{
    								$expiry_date .= $result_value['expiry_date'].','.$temp_quantities;
    							}
    						}



						//add warehouse id get from inventory manage
    						if(new_strlen($data_warehouse_id) != 0){
    							$data_warehouse_id .= ','.$result_value['warehouse_id'];
    						}else{
    							$data_warehouse_id .= $result_value['warehouse_id'];

    						}

    						$temp_quantities = 0;

    					}

    				}

    			}

    			array_push($data_update, [
    				'id' => $value['id'],
    				'warehouse_id' => $data_warehouse_id,
    				'lot_number' => $lot_number,
    				'expiry_date' => $expiry_date,
    				'available_quantity' => $available_quantity_n,
    			]);
    			
    		}

    		//update warehouse_id, lot_number, expiry date into table mrp_manufacturing_order_details. So when mark as done mo => write log good delivery voucher
    		if(count($data) != 0){
    			$this->db->update_batch(db_prefix().'mrp_manufacturing_order_details', $data_update, 'id');
    			if($this->db->affected_rows() > 0){
    				$affected_rows++;
    			}
    		}


    	}

    	if($affected_rows > 0){
    		return true;
    	}

    	return false;

    }

	/**
	 * mo mark as unreserved
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function mo_mark_as_unreserved($id)
	{	
		$this->load->model('warehouse/warehouse_model');
		$result=[];
		$affected_rows=0;

		//get MO
		$mo_detail_update=[];
		$mo_detail_revert=[];

		$check_availability='';
		$check_availability_message='';
		$warehouse_id='';

		$mo = $this->get_manufacturing_order($id);
		if($mo['manufacturing_order']){
			$warehouse_id = $mo['manufacturing_order']->components_warehouse_id;
		}

		if($mo['manufacturing_order_detail']){
			foreach ($mo['manufacturing_order_detail'] as $mo_detail) {
				array_push($mo_detail_update, [
					'id' =>$mo_detail['id'], 
					'manufacturing_order_id' =>$mo_detail['manufacturing_order_id'],
					'product_id' =>$mo_detail['product_id'],
					'unit_id' =>$mo_detail['unit_id'],
					'qty_to_consume' =>$mo_detail['qty_to_consume'],
					'qty_reserved' =>0,
				]);

				if($mo_detail['qty_reserved'] != 0){

					array_push($mo_detail_revert, [
						'id' =>$mo_detail['id'], 
						'manufacturing_order_id' =>$mo_detail['manufacturing_order_id'],
						'product_id' =>$mo_detail['product_id'],
						'unit_id' =>$mo_detail['unit_id'],
						'qty_to_consume' =>$mo_detail['qty_to_consume'],
						'qty_reserved' =>$mo_detail['qty_reserved'],
						'warehouse_id' =>$mo_detail['warehouse_id'],

					]);
				}


			}
		}


		if(count($mo_detail_update) > 0){
			//update mo detail, reserved quantity
			$this->db->update_batch(db_prefix().'mrp_manufacturing_order_details', $mo_detail_update, 'id');
			if($this->db->affected_rows() > 0){
				$affected_rows++;
			}

			//revert inventory quantity (warehouse module)
			$add_inventory_quantity = $this->mrp_add_inventory_quantity($mo_detail_revert, 1, $warehouse_id);
			if($add_inventory_quantity){
				$affected_rows++;
			}

			if(count($mo_detail_revert) > 0){
				foreach ($mo_detail_revert as $mo_detail_revert_data) {
					$manufacturing_order_id = $id;
					$parent_product_id = 0;
					$product_id = $mo_detail_revert_data['product_id'];
					$unit_id = $mo_detail_revert_data['unit_id'];
					$change_type = 'mrp_change_quantity';
					$change_quantity = $mo_detail_revert_data['qty_reserved'];
					$created_at = date('Y-m-d H:i:s');
					$staff_id = '';
					$description = _l('bom_change_log_component_quantity_is_reverted');
					$rel_id = 0;
					$rel_type = '';

					$this->bom_change_log($manufacturing_order_id, $parent_product_id, $product_id, $unit_id, $change_type, $change_quantity, $created_at, $staff_id, $description, $rel_id, $rel_type);
				}
			}
		}

		//revert MO status to "draft"
		$update_mo_status = $this->update_manufacturing_order_status($id, [
			'status' => 'draft',
		]);

		if($update_mo_status){
			$affected_rows++;
		}

		if($affected_rows > 0){
			return true;
		}
		return false;

	}

	/**
	 * mo add goods delivery
	 * @param  [type]  $data 
	 * @param  boolean $id   
	 * @return [type]        
	 */
	public function mo_add_goods_delivery($mo_data)
	{
		$this->load->model('warehouse/warehouse_model');

		$mo= $mo_data['manufacturing_order'];
		$mo_detail = $mo_data['manufacturing_order_detail'];

		$bom_change_data=[];
		$data_detail=[];
		$total_money=0;
		foreach ($mo_detail as $key => $value) {
			$get_product =$this->get_product($value['product_id']);
			if($get_product){
				if((float)$value['available_quantity'] > 0 ){
					if((float)$value['qty_reserved'] > (float)$value['available_quantity']){
						$value['qty_reserved'] = (float)$value['available_quantity'];
					}
				/*get item from name*/
				$arr_item_insert[$key]['warehouse_id'] = $value['warehouse_id'];
				$arr_item_insert[$key]['expiry_date'] = $value['expiry_date'];
				$arr_item_insert[$key]['lot_number'] = $value['lot_number'];
				$arr_item_insert[$key]['available_quantity'] = $value['available_quantity'];
				$arr_item_insert[$key]['commodity_code'] = $value['product_id'];
				$arr_item_insert[$key]['quantities'] = $value['qty_reserved'];
				$arr_item_insert[$key]['unit_price'] = $get_product->rate;
				$arr_item_insert[$key]['tax_id'] = '';
				$arr_item_insert[$key]['unit_id'] = $get_product->unit_id;
				$arr_item_insert[$key]['total_money'] = (float)$value['qty_reserved']*(float)$get_product->rate;
				$arr_item_insert[$key]['total_after_discount'] = (float)$value['qty_reserved']*(float)$get_product->rate;

				$total_money += (float)$value['qty_reserved']*(float)$get_product->rate;

				// bom log
				$manufacturing_order_id = $mo->id;
				$parent_product_id = 0;
				$product_id = $value['product_id'];
				$unit_id = $get_product->unit_id;
				$change_type = 'mrp_change_quantity';
				$change_quantity = $value['qty_reserved'];
				$created_at = date('Y-m-d H:i:s');
				$staff_id = 0;
				$description = _l('bom_change_log_decrease_inventory_quantity_of_inventory_used_in_production');
				$rel_type = 'delivery_note';

				$bom_change_data[] = [
					'manufacturing_order_id' => $manufacturing_order_id,
					'parent_product_id' => $parent_product_id,
					'product_id' => $product_id,
					'unit_id' => $unit_id,
					'change_type' => $change_type,
					'change_quantity' => $change_quantity,
					'created_at' => $created_at,
					'staff_id' => $staff_id,
					'description' => $description,
					'rel_type' => $rel_type,
				];
				}
			}
		}

		$data['approval'] = 1;
		$data['goods_delivery_code'] = $this->warehouse_model->create_goods_delivery_code();
		$data['date_c'] = date('Y-m-d');
		$data['date_add'] = date('Y-m-d');
		$data['description'] = _l('this_delivery_voucher_for_the_product_used_in_the_production_module').$mo->manufacturing_order_code;
		$data['total_money'] 	= $total_money;
		$data['after_discount'] = $total_money;
		$data['addedfrom'] = get_staff_user_id();

		//insert goods delivery
		$this->db->insert(db_prefix() . 'goods_delivery', $data);
		$insert_id = $this->db->insert_id();

		/*update next number setting*/
    	$this->warehouse_model->update_inventory_setting(['next_inventory_delivery_mumber' =>  get_warehouse_option('next_inventory_delivery_mumber')+1]);

		//insert goods delivery detail
		if (isset($insert_id)) {
			foreach ($bom_change_data as $value) {
				$value['rel_id'] = $insert_id;

				$this->bom_change_log($value['manufacturing_order_id'], $value['parent_product_id'], $value['product_id'], $value['unit_id'], $value['change_type'], $value['change_quantity'], $value['created_at'], $value['staff_id'], $value['description'], $value['rel_id'], $value['rel_type']);
			}

			foreach ($arr_item_insert as $key => $value) {
			    $arr_item_insert[$key]['goods_delivery_id'] = $insert_id;
			}

			$goods_delivery_detail = $this->db->insert_batch(db_prefix().'goods_delivery_detail',$arr_item_insert);
			
        	hooks()->do_action('after_manufacturing_goods_delivery_added', $insert_id);

			return $insert_id;
		}

		return false;
	}

	/**
	 * mo add delivery log
	 * @param  [type] $data   
	 * @param  [type] $status 
	 * @return [type]         
	 */
	public function mo_add_delivery_log($data)
	{
		$this->load->model('warehouse/warehouse_model');

		$available_quantity_n =0;

		$available_quantity = $this->warehouse_model->get_inventory_by_commodity($data['commodity_code']);
		if($available_quantity){
			$available_quantity_n = $available_quantity->inventory_number;
		}


		$data['warehouse_id']='';
    		//status == 2 export
			//update
		$this->db->where('commodity_id', $data['commodity_code']);
		$this->db->order_by('id', 'ASC');

		$result = $this->db->get(db_prefix().'inventory_manage')->result_array();

		$temp_quantities = $data['quantities'];

		$expiry_date = '';
		$lot_number = '';
		foreach ($result as $result_value) {
			if (($result_value['inventory_number'] != 0) && ($temp_quantities != 0)) {

				if ($temp_quantities >= $result_value['inventory_number']) {
					$temp_quantities = (float) $temp_quantities - (float) $result_value['inventory_number'];

						//log lot number
					if(($result_value['lot_number'] != null) && ($result_value['lot_number'] != '') ){
						if(new_strlen($lot_number) != 0){
							$lot_number .=','.$result_value['lot_number'].','.$result_value['inventory_number'];
						}else{
							$lot_number .= $result_value['lot_number'].','.$result_value['inventory_number'];
						}
					}

						//log expiry date
					if(($result_value['expiry_date'] != null) && ($result_value['expiry_date'] != '') ){
						if(new_strlen($expiry_date) != 0){
							$expiry_date .=','.$result_value['expiry_date'].','.$result_value['inventory_number'];
						}else{
							$expiry_date .= $result_value['expiry_date'].','.$result_value['inventory_number'];
						}
					}


						//add warehouse id get from inventory manage
					if(new_strlen($data['warehouse_id']) != 0){
						$data['warehouse_id'] .= ','.$result_value['warehouse_id'];
					}else{
						$data['warehouse_id'] .= $result_value['warehouse_id'];

					}

				} else {

						//log lot number
					if(($result_value['lot_number'] != null) && ($result_value['lot_number'] != '') ){
						if(new_strlen($lot_number) != 0){
							$lot_number .=','.$result_value['lot_number'].','.$temp_quantities;
						}else{
							$lot_number .= $result_value['lot_number'].','.$temp_quantities;
						}
					}

						//log expiry date
					if(($result_value['expiry_date'] != null) && ($result_value['expiry_date'] != '') ){
						if(new_strlen($expiry_date) != 0){
							$expiry_date .=','.$result_value['expiry_date'].','.$temp_quantities;
						}else{
							$expiry_date .= $result_value['expiry_date'].','.$temp_quantities;
						}
					}



						//add warehouse id get from inventory manage
					if(new_strlen($data['warehouse_id']) != 0){
						$data['warehouse_id'] .= ','.$result_value['warehouse_id'];
					}else{
						$data['warehouse_id'] .= $result_value['warehouse_id'];

					}

					$temp_quantities = 0;

				}

			}

		}

			//update good delivery detail
		$this->db->where('id', $data['id']);
		$this->db->update(db_prefix() . 'goods_delivery_detail', [
			'expiry_date' => $expiry_date,
			'lot_number' => $lot_number,
			'warehouse_id' => $data['warehouse_id'],
			'available_quantity' => $available_quantity_n,
		]);

			//goods transaction detail log
		$data['expiry_date'] = $expiry_date;
		$data['lot_number'] = $lot_number;
		$this->warehouse_model->add_goods_transaction_detail($data, 2);

		return true;

	}

	/**
	 * mo mark as cancel
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function mo_mark_as_cancel($id)
	{
		$affected_rows=0;

	    //revert MO status to "cancelled"
		$update_mo_status = $this->update_manufacturing_order_status($id, [
			'status' => 'cancelled',
		]);

		if($update_mo_status){
			$affected_rows++;
		}

		if($affected_rows > 0){
			return true;
		}
		return false;
	}

	/**
	 * print barcode pdf
	 * @param  [type] $print_barcode 
	 * @return [type]                
	 */
	public function print_barcode_pdf($print_barcode)
	{
		return app_pdf('print_barcode', module_dir_path(MANUFACTURING_MODULE_NAME, 'libraries/pdf/Print_barcode_pdf.php'), $print_barcode);
	}


	/**
	 * get stock internal delivery pdf_html
	 * @param  [type] $internal_delivery_id 
	 * @return [type]                    
	 */
	public function get_print_barcode_pdf_html($data)
	{

		$display_product_name = get_warehouse_option('display_product_name_when_print_barcode');

		$get_base_currency = get_base_currency();
		$current_id='';
		if($get_base_currency){
			$current_id= $get_base_currency->id;
		}

		$html ='';

		$html .= '<table class="table">
		<tbody>';
		

		if($data['select_item'] == 0){
			//select all
			$array_commodity = $this->get_product();
			$html_child='';
			$br_tem=1;
			foreach ($array_commodity as $key => $value) {
				if($value['commodity_barcode'] != ''){

					if(!file_exists(MANUFACTURING_PRINT_ITEM. md5($value['commodity_barcode']).'.svg')){
						$this->getBarcode($value['commodity_barcode']);
					}
				}

				/*get frist 25 character */
				if(new_strlen($value['description']) > 30){
					$pos=strpos($value['description'], ' ', 30);
					$description = substr($value['description'],0,$pos ); 
				}else{
					$description = $value['description'];
				}

				/*get frist 100 character */
				if(new_strlen($value['long_description']) > 30){
					$pos=strpos($value['long_description'], ' ', 30);
					$description_sub = substr($value['long_description'],0,$pos ); 
				}else{
					$description_sub = $value['long_description'];
				}

				//final price: price*Vat
				$tax_value=0;
				if($value['tax'] != 0 && $value['tax'] != ''){
					$tax_rate = get_tax_rate($value['tax']);
					if(!is_array($tax_rate)  && isset($tax_rate)){
						$tax_value = $tax_rate->taxrate;
					}
				}

				$rate_after_tax = (float)$value['rate'] + (float)$value['rate']*$tax_value/100;

				if($value['commodity_barcode'] != ''){
					if($display_product_name == 1){
						$html_child .= '<td class="print-barcode-td-height"><span class="print-item-code print-item-name">'.$description.'</span><br><span class="print-item-code print-item-name">'.$description_sub.'</span><br><span class=" print-item-price">'._l('print_barcode_sale_price').': '.app_format_money($rate_after_tax,$current_id).'</span><span class="print-item"><img class="images_w_table" src="' . site_url('modules/manufacturing/uploads/print_item/' . md5($value['commodity_barcode']).'.svg') . '" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'.$value['commodity_barcode'].'</span></td>';
					}else{
						$html_child .= '<td class="print-barcode-td-height"><span class="print-item-code print-item-name"></span><br><span class="print-item-code print-item-name">'.$description.'</span><br><span class=" print-item-price">'._l('print_barcode_sale_price').': '.app_format_money($rate_after_tax,$current_id).'</span><span class="print-item"><img class="images_w_table" src="' . site_url('modules/manufacturing/uploads/print_item/' . md5($value['commodity_barcode']).'.svg') . '" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'.$value['commodity_barcode'].'</span></td>';

					}

					
				}else{
					if($display_product_name == 1){

						$html_child .= '<td class="print-barcode-td-height"><span class="print-item-code print-item-name">'.$description.'</span><br><span class="print-item-code print-item-name">'.$description_sub.'</span><br><span class=" print-item-price">'._l('print_barcode_sale_price').': '.app_format_money($rate_after_tax,$current_id).'</span><span class="print-item"><img class="images_w_table" src="" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'._l('the_product_has_no_barcode').'</span></td>';

					}else{
						$html_child .= '<td class="print-barcode-td-height"><span class="print-item-code print-item-name"></span><br><span class="print-item-code print-item-name">'.$description.'</span><br><span class=" print-item-price">'._l('print_barcode_sale_price').': '.app_format_money($rate_after_tax,$current_id).'</span><span class="print-item"><img class="images_w_table" src="" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'._l('the_product_has_no_barcode').'</span></td>';

					}
				
				}


				if(($key+1)%4 == 0 ){
					$html .= '<tr>'.$html_child.'</tr>';

					if($br_tem%36 == 0){
						$html .= '<br>';
					}

					$html_child='';
				}elseif(($key+1)%4 != 0 && ($key+1 == count($array_commodity))){
					$html .= '<tr>'.$html_child.'</tr>';

					if($br_tem%36 == 0){
						$html .= '<br>';
					}

					$html_child='';
				}

				$br_tem++;
				
			}



		}else{
			//select item check
			if(isset($data['item_select_print_barcode'])){

				$sql_where ='select * from '.db_prefix().'items where id IN ('.implode(", ", $data['item_select_print_barcode']).') order by id desc';
				$array_commodity =  $this->db->query($sql_where)->result_array();

				$html_child='';
				$br_tem=1;
				foreach ($array_commodity as $key => $value) {
					if($value['commodity_barcode'] != ''){

						if(!file_exists(MANUFACTURING_PRINT_ITEM. md5($value['commodity_barcode']).'.svg')){
							$this->getBarcode($value['commodity_barcode']);
						}
					}

					/*get frist 100 character */
					if(new_strlen($value['description']) > 30){
						$pos=strpos($value['description'], ' ', 30);
						$description = substr($value['description'],0,$pos ); 
					}else{
						$description = $value['description'];
					}

					/*get frist 100 character */
					if(new_strlen($value['long_description']) > 30){
						$pos=strpos($value['long_description'], ' ', 30);
						$description_sub = substr($value['long_description'],0,$pos ); 
					}else{
						$description_sub = $value['long_description'];
					}

					//final price: price*Vat
					$tax_value=0;
					if($value['tax'] != 0 && $value['tax'] != ''){
						$tax_rate = get_tax_rate($value['tax']);
						if(!is_array($tax_rate)  && isset($tax_rate)){
							$tax_value = $tax_rate->taxrate;
						}
					}

					$rate_after_tax = (float)$value['rate'] + (float)$value['rate']*$tax_value/100;

					if($value['commodity_barcode'] != ''){
						if($display_product_name == 1){

							$html_child .= '<td><span class="print-item-code print-item-name">'.$description.'</span><br><span class="print-item-code print-item-name ">'.$description_sub.'</span><br><span class=" print-item-price">'._l('print_barcode_sale_price').': '.app_format_money($rate_after_tax,$current_id).'</span><span class="print-item"><img class="images_w_table" src="' . site_url('modules/manufacturing/uploads/print_item/' . md5($value['commodity_barcode']).'.svg') . '" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'.$value['commodity_barcode'].'</span></td>';

						}else{

							$html_child .= '<td><span class="print-item-code print-item-name "></span><br><span class="print-item-code print-item-name">'.$description.'</span><br><span class=" print-item-price">'._l('print_barcode_sale_price').': '.app_format_money($rate_after_tax,$current_id).'</span><span class="print-item"><img class="images_w_table" src="' . site_url('modules/manufacturing/uploads/print_item/' . md5($value['commodity_barcode']).'.svg') . '" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'.$value['commodity_barcode'].'</span></td>';
						}
					}else{
						if($display_product_name == 1){
							$html_child .= '<td><span class="print-item-code print-item-name">'.$description.'</span><br><span class="print-item-code print-item-name ">'.$description_sub.'</span><br><span class=" print-item-price">'._l('print_barcode_sale_price').': '.app_format_money($rate_after_tax,$current_id).'</span><span class="print-item"><img class="images_w_table" src="" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'._l('the_product_has_no_barcode').'</span></td>';
						}else{
							$html_child .= '<td><span class="print-item-code print-item-name "></span><br><span class="print-item-code print-item-name">'.$description.'</span><br><span class=" print-item-price">'._l('print_barcode_sale_price').': '.app_format_money($rate_after_tax,$current_id).'</span><span class="print-item"><img class="images_w_table" src="" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'._l('the_product_has_no_barcode').'</span></td>';

						}
					}


					if(($key+1)%4 == 0 ){
						$html .= '<tr>'.$html_child.'</tr>';

						if($br_tem%36 == 0){
							$html .= '<br>';
						}

						$html_child='';
					}elseif(($key+1)%4 != 0 && ($key+1 == count($array_commodity))){
						$html .= '<tr>'.$html_child.'</tr>';

						if($br_tem%36 == 0){
							$html .= '<br>';
						}

						$html_child='';
					}

					$br_tem++;
				
				}
			}
		}

		$html .= '</tbody>
		</table>
		<br><br><br>
		';

		$html .= '<link href="' . module_dir_url(MANUFACTURING_MODULE_NAME, 'assets/css/pdf_style.css') . '"  rel="stylesheet" type="text/css" />';
		return $html;
	}

	/**
	 * getBarcode
	 * @param  [type] $sample 
	 * @return [type]         
	 */
	function getBarcode($sample)
	{
	    if (!$sample) {
	        echo "";
	    } else {
	        $barcodeobj = new TCPDFBarcode($sample, 'EAN13');
	        $code = $barcodeobj->getBarcodeSVGcode(4, 70, 'black');
			file_put_contents(MANUFACTURING_PRINT_ITEM.md5($sample).'.svg', $code);

			return true;
	    }
	}

	/**
	 * bom get product filter
	 * @param  [type] $ids 
	 * @return [type]      
	 */
	public function bom_get_product_filter($ids)
	{
		$this->db->where('id IN ('.implode(",",$ids) .')');
		$products = $this->db->get(db_prefix() . 'items')->result_array();
		return $products;
	}

	/**
	 * get mo report data
	 * @param  [type] $mo_measures 
	 * @param  [type] $from_date   
	 * @param  [type] $to_date     
	 * @return [type]              
	 */
	public function get_mo_report_data($mo_measures, $from_date, $to_date)
	{	
		$chart=[];
		
		switch ($mo_measures) {
			case 'count':
			$sql_where="SELECT  date_format(date_deadline, '%m') as mo_month, count(id) as total, status FROM ".db_prefix()."mrp_manufacturing_orders
			where date_format(date_deadline, '%Y-%m-%d') >= '".$from_date."' AND date_format(date_deadline, '%Y-%m-%d') <= '".$to_date."'
			group by date_format(date_deadline, '%m'), status
			";
				break;

			case 'total_qty':
			$sql_where="SELECT  date_format(date_deadline, '%m') as mo_month, sum(product_qty) as total, status FROM ".db_prefix()."mrp_manufacturing_orders
			where date_format(date_deadline, '%Y-%m-%d') >= '".$from_date."' AND date_format(date_deadline, '%Y-%m-%d') <= '".$to_date."'
			group by date_format(date_deadline, '%m'), status
			";
				break;
		}

		$mo = $this->db->query($sql_where)->result_array();


		$mo_by_month=[];
		foreach ($mo as $key => $mo_value) {
		    $mo_by_month[(int)$mo_value['mo_month']][$mo_value['status']] = $mo_value;
		}


		for($_month = 1 ; $_month <= 12; $_month++){

			if(isset($mo_by_month[$_month])){

				$chart['draft'][] = isset($mo_by_month[$_month]['draft']) ? (float)$mo_by_month[$_month]['draft']['total'] : 0;
				$chart['planned'][] = isset($mo_by_month[$_month]['planned']) ? (float)$mo_by_month[$_month]['planned']['total'] : 0;
				$chart['cancelled'][] = isset($mo_by_month[$_month]['cancelled']) ? (float)$mo_by_month[$_month]['cancelled']['total'] : 0;
				$chart['confirmed'][] = isset($mo_by_month[$_month]['confirmed']) ? (float)$mo_by_month[$_month]['confirmed']['total'] : 0;
				$chart['done'][] = isset($mo_by_month[$_month]['done']) ? (float)$mo_by_month[$_month]['done']['total'] : 0;
				$chart['in_progress'][] = isset($mo_by_month[$_month]['in_progress']) ? (float)$mo_by_month[$_month]['in_progress']['total'] : 0;

			}else{
				$chart['draft'][] =  0;
				$chart['planned'][] =  0;
				$chart['cancelled'][] =  0;
				$chart['confirmed'][] =  0;
				$chart['done'][] =  0;
				$chart['in_progress'][] =  0;
			}

			if($_month == 5){
				$chart['categories'][] = _l('month_05');
			}else{
				$chart['categories'][] = _l('month_'.$_month);
			}

		}

		return $chart;
	}

	/**
	 * get wo report data
	 * @param  [type] $mo_measures 
	 * @param  [type] $from_date   
	 * @param  [type] $to_date     
	 * @return [type]              
	 */
	public function get_wo_report_data($mo_measures, $from_date, $to_date)
	{
		
	    $chart=[];
	    $chart['categories']=[];
	    $chart['mo_data']=[];
	 
	    $arr_mo_id=[];
		$mo_where = "SELECT * FROM ".db_prefix()."mrp_manufacturing_orders where status = 'done' AND date_format(date_deadline, '%Y-%m-%d') >= '".$from_date."' AND date_format(date_deadline, '%Y-%m-%d') <= '".$to_date."'";
		$mo_done = $this->db->query($mo_where)->result_array();

		foreach ($mo_done as $mo) {
		    $arr_mo_id[] = $mo['id'];
		}

		if(count($arr_mo_id) > 0){
			//get manufacturing order name
			$get_mo_where = "SELECT * FROM ".db_prefix()."mrp_manufacturing_orders where  manufacturing_order_id IN (". implode(",",$arr_mo_id).")";
			$get_mo = $this->db->query($mo_where)->result_array();
			$manufacturing_order=[];
			foreach ($get_mo as $key => $value) {
				$manufacturing_order[$value['id']] = $value;
			}

			switch ($mo_measures) {
				case 'count':
					$sql_where="SELECT  count(id) as dashboard_re, manufacturing_order_id FROM ".db_prefix()."mrp_work_orders
					where manufacturing_order_id IN (". implode(",",$arr_mo_id).")
					group by manufacturing_order_id
					";
					$work_orders = $this->db->query($sql_where)->result_array();


					break;

				case 'duration_per_unit':
					$sql_where="SELECT  sum(real_duration)/sum(qty_producing) as dashboard_re, manufacturing_order_id FROM ".db_prefix()."mrp_work_orders
					where manufacturing_order_id IN (". implode(",",$arr_mo_id).")
					group by manufacturing_order_id
					";
					$work_orders = $this->db->query($sql_where)->result_array();

					break;

				case 'expected_duration':
					$sql_where="SELECT  sum(duration_expected) as dashboard_re, manufacturing_order_id FROM ".db_prefix()."mrp_work_orders
					where manufacturing_order_id IN (". implode(",",$arr_mo_id).")
					group by manufacturing_order_id
					";
					$work_orders = $this->db->query($sql_where)->result_array();

					break;

				case 'quantity':
					$sql_where="SELECT  sum(qty_producing) as dashboard_re, manufacturing_order_id FROM ".db_prefix()."mrp_work_orders
					where manufacturing_order_id IN (". implode(",",$arr_mo_id).")
					group by manufacturing_order_id
					";
					$work_orders = $this->db->query($sql_where)->result_array();
					break;

				case 'real_duration':
					$sql_where="SELECT  sum(real_duration) as dashboard_re, manufacturing_order_id FROM ".db_prefix()."mrp_work_orders
					where manufacturing_order_id IN (". implode(",",$arr_mo_id).")
					group by manufacturing_order_id
					";
					$work_orders = $this->db->query($sql_where)->result_array();

					break;
				
			}

			foreach ($work_orders as $wo_key => $wo_value) {
				$chart['categories'][] = isset($manufacturing_order[$wo_value['manufacturing_order_id']]) ? $manufacturing_order[$wo_value['manufacturing_order_id']]['manufacturing_order_code'] : '';
				$chart['mo_data'][] = (float)$wo_value['dashboard_re'];
			}

		}

		return $chart;
	}

	/**
	 * dasboard get work center
	 * @return [type] 
	 */
	public function dasboard_get_work_center()
	{
		$work_center_data=[];
		$work_order_data=[];
		//get_work_centers
		$work_centers = $this->get_work_centers();

		//get list work order
		$work_orders = $this->get_work_order();
		foreach ($work_orders as $key => $value) {
			if(strtotime($value['date_finished'] ?? '') > strtotime($value['date_planned_finished'] ?? '')){
				if(isset($work_order_data[$value['work_center_id']]['late'])){
					$work_order_data[$value['work_center_id']]['late'] += 1;
				}else{
					$work_order_data[$value['work_center_id']]['late'] = 1;
				}

			}
		}

		$get_wcenter_where = "SELECT count(id) as total, work_center_id, status FROM ".db_prefix()."mrp_work_orders
		group by work_center_id, status
		;";
		$work_center_by_status = $this->db->query($get_wcenter_where)->result_array();
		foreach ($work_center_by_status as $wo_c_key => $wo_c_value) {
			switch ($wo_c_value['status']) {
				case 'ready':
					$work_order_data[$value['work_center_id']]['ready'] = $wo_c_value['total'];
					break;

				case 'in_progress':
					$work_order_data[$value['work_center_id']]['in_progress'] = $wo_c_value['total'];
					break;
			}

		}

		foreach ($work_centers as $wo_key => $wo_value) {
			if(isset($work_order_data[$wo_value['id']])){
				$id = $wo_value['id'];
				$work_center_code = $wo_value['work_center_code'];
				$work_center_name = $wo_value['work_center_name'];
				$ready = isset($work_order_data[$wo_value['id']]['ready']) ? $work_order_data[$wo_value['id']]['ready'] : 0;
				$in_progress = isset($work_order_data[$wo_value['id']]['in_progress']) ? $work_order_data[$wo_value['id']]['in_progress'] : 0;
				$late = isset($work_order_data[$wo_value['id']]['late']) ? $work_order_data[$wo_value['id']]['late'] : 0;

				array_push($work_center_data, [
					'ready' => $ready,
					'in_progress' => $in_progress,
					'late' => $late,
					'work_center_code' => $work_center_code,
					'work_center_name' => $work_center_name,
					'id' => $id,
				]);
			}else{
				$ready =  0;
				$in_progress = 0;
				$late = 0;
				$work_center_code = $wo_value['work_center_code'];
				$work_center_name = $wo_value['work_center_name'];
				$id = $wo_value['id'];

				
				array_push($work_center_data, [
					'ready' => $ready,
					'in_progress' => $in_progress,
					'late' => $late,
					'work_center_code' => $work_center_code,
					'work_center_name' => $work_center_name,
					'id' => $id,

				]);
			}
		}

		return $work_center_data;
	}

	/**
	 *  update prefix number
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function update_prefix_number($data)
	{
		$affected_rows=0;
		foreach ($data as $key => $value) {

			$this->db->where('option_name',$key);
			$this->db->update(db_prefix() . 'mrp_option', [
				'option_val' => $value,
			]);

			if ($this->db->affected_rows() > 0) {
				$affected_rows++;
			}
			
		}

		if($affected_rows > 0){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * update variant product
	 * @param  [type] $parent_id 
	 * @param  [type] $data      
	 * @param  [type] $variant   
	 * @return [type]            
	 */
	public function update_variant_product($parent_id, $variant)
	{	
		$arr_item_active = [];
		$arr_item_active[] = $parent_id;

		//parent information
		$data = (array)$this->get_product($parent_id);
		unset($data['id']);

    	//get last product id
		$sql_where = 'SELECT * FROM ' . db_prefix() . 'items order by id desc limit 1';
		$res = $this->db->query($sql_where)->row();
		$last_commodity_id = 0;
		if (isset($res)) {
			$last_commodity_id = $this->db->query($sql_where)->row()->id;
		}
		$next_commodity_id = (int) $last_commodity_id + 1;

		$generate_variants = $this->variant_generator($variant);
		$varirant_data=[];

		$description = $data['description'];
		foreach ($generate_variants as $_variant) {

			$str_variant='';

			if(count($variant) > 1){
				foreach ($_variant as $value) {
					if(new_strlen($str_variant) == 0){
						$str_variant .= $value['option'];
					}else{
						$str_variant .= '-'.$value['option'];
					}
				}
			}else{
				if(new_strlen($str_variant) == 0){
					$str_variant .= $_variant['option'];
				}else{
					$str_variant .= '-'.$_variant['option'];
				}
			}

			$str_variant = new_str_replace(' ', '_', $str_variant);
			$barcode_gen = mrp_generate_commodity_barcode();

        	//create sku code
			$sku_code = str_pad($next_commodity_id,5,'0',STR_PAD_LEFT);
			$next_commodity_id++; 
			$data['commodity_code'] = $sku_code;
			$data['sku_code'] = $sku_code;
			$data['commodity_barcode'] = $barcode_gen;
			$data['commodity_code'] = $sku_code;
			$data['sku_code'] = $sku_code;
			$data['parent_id'] = $parent_id;
			$data['parent_attributes'] = null;

			if(count($variant) > 1){
				$data['attributes'] = json_encode($_variant);
			}else{
				$data['attributes'] = json_encode(array($_variant));
			}
			$data['description'] = $description.' '. $str_variant;

			//check if product exist, don't add
			$this->db->where('parent_id', $parent_id);
			$this->db->where('attributes', $data['attributes']);
			$child_product = $this->db->get(db_prefix().'items')->row();
			if($child_product){
				$arr_item_active[] = $child_product->id;
				continue;
			}

			$varirant_data[] = $data;

		}

		//update inactive product
		$this->db->where('parent_id', $parent_id);
		$this->db->where('id NOT IN ('.implode(",",$arr_item_active).')');
		$this->db->update(db_prefix().'items', ['active' => 0]);

		//update active product, if exist old product current is inactive
		$this->db->where('parent_id', $parent_id);
		$this->db->where('id IN ('.implode(",",$arr_item_active).')');
		$this->db->update(db_prefix().'items', ['active' => 1]);

		//add new variant product
		if(count($varirant_data) > 0){
			$affected_rows = $this->db->insert_batch(db_prefix().'items', $varirant_data);
			if($affected_rows > 0){
				//copy_product_image
				// get new product id
				$arr_variant = [];
				$this->db->where('parent_id', $parent_id);
				$this->db->where('id NOT IN ('.implode(",",$arr_item_active).')');
				$new_product_variants = $this->db->get(db_prefix().'items')->result_array();
				foreach ($new_product_variants as $product_variant) {
				    $arr_variant[] = ['id' => $product_variant['id']];
				}
				if(count($arr_variant) > 0){
					$this->copy_product_image($parent_id, $arr_variant);
				}

				return true;
			}
			return false;
		}
		return false;
	}

	/**
	 * get manufacturing order costing
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_manufacturing_order_costing($id)
	{
	    $this->db->where('manufacturing_order_id', $id);
	    $manufacturing_order_details = $this->db->get(db_prefix().'mrp_manufacturing_order_details')->result_array();

	    $this->db->where('id', $id);
	    $manufacturing_order = $this->db->get(db_prefix().'mrp_manufacturing_orders')->row();

	    $arr_work_centers = [];
	    $get_work_centers = $this->get_work_centers();
	    foreach ($get_work_centers as $work_center) {
	        $arr_work_centers[$work_center['id']] = $work_center['costs_hour'];
	    }

	    $total_material_cost = 0;
		$total_labour_cost = 0;
		$total_work_center_cost = 0;
		$total_employee_working_cost = 0;

	    foreach ($manufacturing_order_details as $key => $value) {
	    	$item = $this->get_product($value['product_id']);
	    	if($item){
		    	$rate = $item->purchase_price*$value['qty_to_consume'];
		    	$total_material_cost += $rate;
	    	}
	    }

	    $this->db->where('manufacturing_order_id', $id);
		$work_orders = $this->db->get(db_prefix() . 'mrp_work_orders')->result_array();

		$total_hour = 0;

		foreach ($work_orders as $key => $value) {
	    	$total_hour += $value['real_duration'];
	    	if(isset($arr_work_centers[$value['work_center_id']])){
	    		$total_work_center_cost += ($value['real_duration']/60) * $arr_work_centers[$value['work_center_id']];
	    	}
	    }

	    $costs_hour = get_mrp_option('cost_hour');
	    if($costs_hour != ''){
	    	$total_employee_working_cost = ($total_hour/60) * $costs_hour;
	    }
	    $total_labour_cost = $total_work_center_cost + $total_employee_working_cost;
	    return ['total_material_cost' => round($total_material_cost, 2),'total_labour_cost' => round($total_labour_cost, 2), 'total_work_center_cost' => round($total_work_center_cost, 2), 'total_employee_working_cost' => round($total_employee_working_cost, 2)];
	    
	}

	/**
	 * check planned
	 * @param  [type] $manufacturing_order_id 
	 * @return [type]                         
	 */
	public function check_planned($manufacturing_order_id)
	{
		$check_planned = false;
		$manufacturing_order = $this->get_manufacturing_order($manufacturing_order_id);
		if($manufacturing_order){

		}

		return $check_planned;
	}

	/**
	 * update_mo_consumed
	 * @param  [type] $work_order_id          
	 * @param  [type] $manufacturing_order_id 
	 * @return [type]                         
	 */
	public function update_mo_consumed($work_order_id, $manufacturing_order_id)
	{
		$manufacturing_order = $this->get_manufacturing_order($manufacturing_order_id);
		if(count($manufacturing_order) > 0){

			$product_id = $manufacturing_order['manufacturing_order']->product_id;
			$bill_of_material_id = $manufacturing_order['manufacturing_order']->bom_id;
			$get_bill_of_material_details_by_work_order = $this->get_bill_of_material_details_by_work_order($bill_of_material_id, $product_id, $work_order_id);

			if($get_bill_of_material_details_by_work_order){
				foreach ($get_bill_of_material_details_by_work_order as $work_order) {
				    $this->db->where('manufacturing_order_id', $manufacturing_order_id);
				    $this->db->where('product_id', $work_order['product_id']);
				    $this->db->update(db_prefix().'mrp_manufacturing_order_details', ['qty_reserved' => $work_order['qty_to_consume'], 'qty_done' => $work_order['qty_to_consume'] ]);

				    $get_work_order = $this->get_work_order($work_order_id);
				    if($get_work_order){
				    	$product_qty = $get_work_order->qty_production;
				    	$routing_id = $get_work_order->routing_detail_id;
				    }

				    $manufacturing_order_id = $manufacturing_order_id;
					$parent_product_id = 0;
					$product_id = $work_order['product_id'];
					$unit_id = $work_order['unit_id'];
					$change_type = 'is_use';
					$change_quantity = 0-$work_order['qty_to_consume'];
					$created_at = date('Y-m-d H:i:s');
					$staff_id = '';
					$description = _l('bom_change_log_component_is_use');
					$rel_id = $work_order_id;
					$rel_type = 'wo_order';

					$this->bom_change_log($manufacturing_order_id, $parent_product_id, $product_id, $unit_id, $change_type, $change_quantity, $created_at, $staff_id, $description, $rel_id, $rel_type);

				}
			}
		}
	}

	/**
	 * get bill of material details by work worder
	 */
	public function get_bill_of_material_details_by_work_order($bill_of_material_id, $product_id, $work_order_id)
	{	
		$product = $this->manufacturing_model->get_product($product_id);

		if($product){
			$routing_id = 0;
			$product_qty = '';
			$get_work_order = $this->get_work_order($work_order_id);
			if($get_work_order){
				$product_qty = $get_work_order->qty_production;
				$routing_id = $get_work_order->routing_detail_id;
			}
			$component_arr = [];
			$product_attributes = $product->attributes;

			$bom_qty=1;
			$bom = $this->get_bill_of_materials($bill_of_material_id);
			if($bom){
				$bom_qty = $bom->product_qty;
			}

			if($product_attributes != null){
				$str_where = '';
				$arr_attributes = json_decode($product_attributes);

				foreach ($arr_attributes as $key => $attributes) {
					if(new_strlen($str_where) > 0){
						$str_where .= 'OR find_in_set( "'.$attributes->name.':'.$attributes->option.'", apply_on_variants)';
					}else{
						$str_where .= ' find_in_set( "'.$attributes->name.':'.$attributes->option.'", apply_on_variants)';
					}
				}

				if(new_strlen($str_where) > 0){
					$str_where .= 'OR apply_on_variants is null';
				}else{
					$str_where .= 'apply_on_variants is null';
				}

				$this->db->where('bill_of_material_id = '.$bill_of_material_id.' AND operation_id = '.$routing_id.' AND ('.$str_where.')');
				$bill_of_material_details = $this->db->get(db_prefix() . 'mrp_bill_of_material_details')->result_array();

			}else{
				$this->db->where('bill_of_material_id = '.$bill_of_material_id.' AND operation_id = '.$routing_id.' AND apply_on_variants is null ');
				$bill_of_material_details = $this->db->get(db_prefix() . 'mrp_bill_of_material_details')->result_array();
			}

			foreach ($bill_of_material_details as $bom_detail_key => $bom_detail_value) {
				if($product_qty != ''){
					$qty_to_consume = (float)$bom_detail_value['product_qty']*(float)$product_qty/$bom_qty;
				}else{
				//default mo for 1 product
					$qty_to_consume = $bom_detail_value['product_qty']*1/$bom_qty;
				}

				$last_bill_of_material = $this->get_last_bill_of_material_by_product($bom_detail_value['product_id'], $qty_to_consume, '');

				if(count($last_bill_of_material) > 0){

					$component_arr = array_merge($component_arr, $last_bill_of_material);
				}else{

					array_push($component_arr, [
						'id' => 0,
						'product_id' => $bom_detail_value['product_id'],
						'unit_id' => $bom_detail_value['unit_id'],
						'qty_to_consume' =>  $qty_to_consume,
						'qty_reserved' =>  0,
						'qty_done' =>  0,
						'check_inventory_qty' =>  true,
					]);
				}
			}

			return $component_arr;
		}
		return false;
	}

	/**
	 * bom change log
	 * @param  [type]  $manufacturing_order_id 
	 * @param  [type]  $parent_product_id      
	 * @param  [type]  $product_id             
	 * @param  [type]  $unit_id                
	 * @param  [type]  $change_type            
	 * @param  [type]  $change_quantity        
	 * @param  integer $created_at             
	 * @param  [type]  $staff_id               
	 * @param  [type]  $description            
	 * @param  [type]  $rel_id                 
	 * @param  [type]  $rel_type               
	 * @return [type]                          
	 */
	public function bom_change_log($manufacturing_order_id, $parent_product_id, $product_id, $unit_id, $change_type, $change_quantity, $created_at = 0, $staff_id = 0, $description = '', $rel_id = 0, $rel_type = '', $check_availability = 0) {
    	if(new_strlen($created_at) == 0){
    		$created_at = date('Y-m-d H:i:s');
    	}
        $log = [
            'manufacturing_order_id' => $manufacturing_order_id ,
            'parent_product_id' => $parent_product_id ,
            'product_id' => $product_id ,
            'unit_id' => $unit_id ,
            'change_type' => $change_type ,
            'change_quantity' => $change_quantity ,
            'created_at' => $created_at ,
            'staff_id' => $staff_id ,
            'description' => $description ,
            'rel_id' => $rel_id ,
            'rel_type' => $rel_type ,
            'check_availability' => $check_availability ,
        ];

        $this->db->insert(db_prefix() . 'mrp_bom_changes_logs', $log);
        $insert_id = $this->db->insert_id();
        if($insert_id){
        	hooks()->do_action('affter_bom_changes_logs', $insert_id);
        	return $insert_id;
        }
        return false;
    }

//end file
}