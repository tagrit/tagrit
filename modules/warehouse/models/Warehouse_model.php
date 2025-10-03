<?php
defined('BASEPATH') or exit('No direct script access allowed');
use app\services\utilities\Arr;

/**
 * warehouse model
 */
class Warehouse_model extends App_Model {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * add commodity type
	 * @param array  $data
	 * @param boolean $id
	 * return boolean
	 */
	public function add_commodity_type($data, $id = false) {
		$affectedRows = 0;

		if (isset($data['hot_commodity_type'])) {
			$hot_commodity_type = $data['hot_commodity_type'];
			unset($data['hot_commodity_type']);
		}

		if(isset($hot_commodity_type)){
			$commodity_type_detail = json_decode($hot_commodity_type);

			$es_detail = [];
			$row = [];
			$rq_val = [];
			$header = [];

			$header[] = 'commodity_type_id';
			$header[] = 'commondity_code';
			$header[] = 'commondity_name';
			$header[] = 'order';
			$header[] = 'display';
			$header[] = 'note';


			foreach ($commodity_type_detail as $key => $value) {
				//only get row "value" != 0
				if($value[1] != ''){
					$es_detail[] = array_combine($header, $value);

				}
			}
		}
		$row = [];
		$row['update'] = []; 
		$row['insert'] = []; 
		$total = [];


		foreach ($es_detail as $key => $value) {
			if($value['display'] == 'yes'){
				$value['display'] = 1;
			}else{
				$value['display'] = 0;
			}

			if($value['commodity_type_id'] != ''){
				$row['update'][] = $value;
			}else{
				unset($value['commodity_type_id']);
				$row['insert'][] = $value;
			}

		}


		if(count($row['insert']) != 0){
			$affected_rows = $this->db->insert_batch(db_prefix().'ware_commodity_type', $row['insert']);
			if($affected_rows > 0){
				$affectedRows++;
			}
		}
		if(count($row['update']) != 0){
			$affected_rows = $this->db->update_batch(db_prefix().'ware_commodity_type', $row['update'], 'commodity_type_id');
			if($affected_rows > 0){
				$affectedRows++;
			}
		}

		if ($affectedRows > 0) {
			return true;
		}

		return false;

	}

	/**
	 *  get commodity type
	 * @param  boolean $id
	 * @return array or object
	 */
	public function get_commodity_type($id = false) {

		if (is_numeric($id)) {
			$this->db->where('commodity_type_id', $id);

			return $this->db->get(db_prefix() . 'ware_commodity_type')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from ' . db_prefix() . 'ware_commodity_type')->result_array();
		}

	}

	/**
	 * get commodity type add commodity
	 * @return array
	 */
	public function get_commodity_type_add_commodity() {

		return $this->db->query('select * from tblware_commodity_type where display = 1 order by tblware_commodity_type.order asc ')->result_array();

	}

	/**
	 * delete commodity type
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_commodity_type($id) {
		$this->db->where('commodity_type_id', $id);
		$this->db->delete(db_prefix() . 'ware_commodity_type');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * add unit type
	 * @param array  $data
	 * @param boolean $id
	 * return boolean
	 */
	public function add_unit_type($data, $id = false) {
		$affectedRows = 0;

		if (isset($data['hot_unit_type'])) {
			$hot_unit_type = $data['hot_unit_type'];
			unset($data['hot_unit_type']);
		}

		if(isset($hot_unit_type)){
			$unit_type_detail = json_decode($hot_unit_type);

			$es_detail = [];
			$row = [];
			$rq_val = [];
			$header = [];

			$header[] = 'unit_type_id';
			$header[] = 'unit_code';
			$header[] = 'unit_name';
			$header[] = 'unit_symbol';
			$header[] = 'order';
			$header[] = 'display';
			$header[] = 'note';


			foreach ($unit_type_detail as $key => $value) {
				//only get row "value" != 0
				if($value[1] != ''){
					$es_detail[] = array_combine($header, $value);

				}
			}
		}
		$row = [];
		$row['update'] = []; 
		$row['insert'] = []; 
		$total = [];


		foreach ($es_detail as $key => $value) {
			if($value['display'] == 'yes'){
				$value['display'] = 1;
			}else{
				$value['display'] = 0;
			}

			if($value['unit_type_id'] != ''){
				$row['update'][] = $value;
			}else{
				unset($value['unit_type_id']);
				$row['insert'][] = $value;
			}

		}


		if(count($row['insert']) != 0){
			$affected_rows = $this->db->insert_batch(db_prefix().'ware_unit_type', $row['insert']);
			if($affected_rows > 0){
				$affectedRows++;
			}
		}
		if(count($row['update']) != 0){
			$affected_rows = $this->db->update_batch(db_prefix().'ware_unit_type', $row['update'], 'unit_type_id');
			if($affected_rows > 0){
				$affectedRows++;
			}
		}

		if ($affectedRows > 0) {
			return true;
		}

		return false;

	}

	/**
	 * get unit type
	 * @param  boolean $id
	 * @return array or object
	 */
	public function get_unit_type($id = false) {

		if (is_numeric($id)) {
			$this->db->where('unit_type_id', $id);

			return $this->db->get(db_prefix() . 'ware_unit_type')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblware_unit_type')->result_array();
		}
		return false;
	}

	/**
	 * get unit add commodity
	 * @return array
	 */
	public function get_unit_add_commodity() {
		return $this->db->query('select * from tblware_unit_type where display = 1 order by tblware_unit_type.order asc ')->result_array();
	}

	/**
	 * get unit code name
	 * @return array
	 */
	public function get_units_code_name() {
		return $this->db->query('select unit_type_id as id, unit_name as label from ' . db_prefix() . 'ware_unit_type')->result_array();
	}

	/**
	 * get warehouse code name
	 * @return array
	 */
	public function get_warehouse_code_name() {

		$warehouseByStaff = $this->warehouse_model->getWarehouseByStaff();

		$this->db->select('warehouse_id as id, warehouse_name as label');
		$this->db->where('display', 1);
		if(is_array($warehouseByStaff)){
			$this->db->where('warehouse_id IN ('.implode(',', $warehouseByStaff).')');
		}
		$this->db->order_by(db_prefix().'warehouse.order', 'asc');
		return $this->db->get(db_prefix() . 'warehouse')->result_array();

		// return $this->db->query('select warehouse_id as id, warehouse_name as label from ' . db_prefix() . 'warehouse where display = 1 order by '.db_prefix().'warehouse.order asc')->result_array();
	}

	/**
	 * delete unit type
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_unit_type($id) {
		$this->db->where('unit_type_id', $id);
		$this->db->delete(db_prefix() . 'ware_unit_type');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * add size type
	 * @param array  $data
	 * @param boolean $id
	 * return boolean
	 */
	public function add_size_type($data, $id = false) {
		$affectedRows = 0;

		if (isset($data['hot_size_type'])) {
			$hot_size_type = $data['hot_size_type'];
			unset($data['hot_size_type']);
		}

		if(isset($hot_size_type)){
			$type_detail = json_decode($hot_size_type);

			$es_detail = [];
			$row = [];
			$rq_val = [];
			$header = [];

			$header[] = 'size_type_id';
			$header[] = 'size_code';
			$header[] = 'size_name';
			$header[] = 'size_symbol';
			$header[] = 'order';
			$header[] = 'display';
			$header[] = 'note';


			foreach ($type_detail as $key => $value) {
				//only get row "value" != 0
				if($value[1] != ''){
					$es_detail[] = array_combine($header, $value);

				}
			}
		}
		$row = [];
		$row['update'] = []; 
		$row['insert'] = []; 
		$total = [];


		foreach ($es_detail as $key => $value) {
			if($value['display'] == 'yes'){
				$value['display'] = 1;
			}else{
				$value['display'] = 0;
			}

			if($value['size_type_id'] != ''){
				$row['update'][] = $value;
			}else{
				unset($value['size_type_id']);
				$row['insert'][] = $value;
			}

		}


		if(count($row['insert']) != 0){
			$affected_rows = $this->db->insert_batch(db_prefix().'ware_size_type', $row['insert']);
			if($affected_rows > 0){
				$affectedRows++;
			}
		}
		if(count($row['update']) != 0){
			$affected_rows = $this->db->update_batch(db_prefix().'ware_size_type', $row['update'], 'size_type_id');
			if($affected_rows > 0){
				$affectedRows++;
			}
		}

		if ($affectedRows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * get size type
	 * @param  boolean $id
	 * @return array or object
	 */
	public function get_size_type($id = false) {

		if (is_numeric($id)) {
			$this->db->where('size_type_id', $id);

			return $this->db->get(db_prefix() . 'ware_size_type')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblware_size_type')->result_array();
		}

	}

	/**
	 * get size add commodity
	 * @return array
	 */
	public function get_size_add_commodity() {

		return $this->db->query('select * from tblware_size_type where display = 1 order by tblware_size_type.order asc')->result_array();

	}

	/**
	 * delete size type
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_size_type($id) {
		$this->db->where('size_type_id', $id);
		$this->db->delete(db_prefix() . 'ware_size_type');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * add style type
	 * @param array  $data
	 * @param boolean $id
	 * return boolean
	 */
	public function add_style_type($data, $id = false) {
		$affectedRows = 0;

		if (isset($data['hot_style_type'])) {
			$hot_style_type = $data['hot_style_type'];
			unset($data['hot_style_type']);
		}

		if(isset($hot_style_type)){
			$style_type_detail = json_decode($hot_style_type);

			$es_detail = [];
			$row = [];
			$rq_val = [];
			$header = [];

			$header[] = 'style_type_id';
			$header[] = 'style_code';
			$header[] = 'style_barcode';
			$header[] = 'style_name';
			$header[] = 'order';
			$header[] = 'display';
			$header[] = 'note';


			foreach ($style_type_detail as $key => $value) {
				//only get row "value" != 0
				if($value[1] != ''){
					$es_detail[] = array_combine($header, $value);

				}
			}
		}
		$row = [];
		$row['update'] = []; 
		$row['insert'] = []; 
		$total = [];


		foreach ($es_detail as $key => $value) {
			if($value['display'] == 'yes'){
				$value['display'] = 1;
			}else{
				$value['display'] = 0;
			}

			if($value['style_type_id'] != ''){
				$row['update'][] = $value;
			}else{
				unset($value['style_type_id']);
				$row['insert'][] = $value;
			}

		}


		if(count($row['insert']) != 0){
			$affected_rows = $this->db->insert_batch(db_prefix().'ware_style_type', $row['insert']);
			if($affected_rows > 0){
				$affectedRows++;
			}
		}
		if(count($row['update']) != 0){
			$affected_rows = $this->db->update_batch(db_prefix().'ware_style_type', $row['update'], 'style_type_id');
			if($affected_rows > 0){
				$affectedRows++;
			}
		}

		if ($affectedRows > 0) {
			return true;
		}

		return false;

	}

	/**
	 * get style type
	 * @param  boolean $id
	 * @return array or object
	 */
	public function get_style_type($id = false) {

		if (is_numeric($id)) {
			$this->db->where('style_type_id', $id);

			return $this->db->get(db_prefix() . 'ware_style_type')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblware_style_type')->result_array();
		}

	}

	/**
	 * get style add commodity
	 * @return array
	 */
	public function get_style_add_commodity() {

		return $this->db->query('select * from tblware_style_type where display = 1 order by tblware_style_type.order asc')->result_array();

	}

	/**
	 * delete style type
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_style_type($id) {
		$this->db->where('style_type_id', $id);
		$this->db->delete(db_prefix() . 'ware_style_type');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * add body type
	 * @param array  $data
	 * @param boolean $id
	 * return boolean
	 */
	public function add_body_type($data, $id = false) {
		$affectedRows = 0;

		if (isset($data['hot_body_type'])) {
			$hot_body_type = $data['hot_body_type'];
			unset($data['hot_body_type']);
		}

		if(isset($hot_body_type)){
			$body_type_detail = json_decode($hot_body_type);

			$es_detail = [];
			$row = [];
			$rq_val = [];
			$header = [];

			$header[] = 'body_type_id';
			$header[] = 'body_code';
			$header[] = 'body_name';
			$header[] = 'order';
			$header[] = 'display';
			$header[] = 'note';


			foreach ($body_type_detail as $key => $value) {
				//only get row "value" != 0
				if($value[1] != ''){
					$es_detail[] = array_combine($header, $value);

				}
			}
		}
		$row = [];
		$row['update'] = []; 
		$row['insert'] = []; 
		$total = [];


		foreach ($es_detail as $key => $value) {
			if($value['display'] == 'yes'){
				$value['display'] = 1;
			}else{
				$value['display'] = 0;
			}

			if($value['body_type_id'] != ''){
				$row['update'][] = $value;
			}else{
				unset($value['body_type_id']);
				$row['insert'][] = $value;
			}

		}


		if(count($row['insert']) != 0){
			$affected_rows = $this->db->insert_batch(db_prefix().'ware_body_type', $row['insert']);
			if($affected_rows > 0){
				$affectedRows++;
			}
		}
		if(count($row['update']) != 0){
			$affected_rows = $this->db->update_batch(db_prefix().'ware_body_type', $row['update'], 'body_type_id');
			if($affected_rows > 0){
				$affectedRows++;
			}
		}

		if ($affectedRows > 0) {
			return true;
		}

		return false;

	}

	/**
	 * get body type
	 * @param  boolean $id
	 * @return row or array
	 */
	public function get_body_type($id = false) {

		if (is_numeric($id)) {
			$this->db->where('body_type_id', $id);

			return $this->db->get(db_prefix() . 'ware_body_type')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblware_body_type')->result_array();
		}

	}

	/**
	 * get body add commodity
	 * @return array
	 */
	public function get_body_add_commodity() {

		return $this->db->query('select * from tblware_body_type where display = 1 order by tblware_body_type.order asc')->result_array();
	}

	/**
	 * delete body type
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_body_type($id) {
		$this->db->where('body_type_id', $id);
		$this->db->delete(db_prefix() . 'ware_body_type');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * add commodity group type
	 * @param array  $data
	 * @param boolean $id
	 * return boolean
	 */
	public function add_commodity_group_type($data, $id = false) {
		$affectedRows = 0;

		if (isset($data['hot_commodity_group_type'])) {
			$hot_commodity_group_type = $data['hot_commodity_group_type'];
			unset($data['hot_commodity_group_type']);
		}

		if(isset($hot_commodity_group_type)){
			$commodity_group_detail = json_decode($hot_commodity_group_type);

			$es_detail = [];
			$row = [];
			$rq_val = [];
			$header = [];

			$header[] = 'id';
			$header[] = 'commodity_group_code';
			$header[] = 'name';
			$header[] = 'order';
			$header[] = 'display';
			$header[] = 'note';


			foreach ($commodity_group_detail as $key => $value) {
				//only get row "value" != 0
				if($value[1] != ''){
					$es_detail[] = array_combine($header, $value);

				}
			}
		}
		$row = [];
		$row['update'] = []; 
		$row['insert'] = []; 
		$total = [];


		foreach ($es_detail as $key => $value) {
			if($value['display'] == 'yes'){
				$value['display'] = 1;
			}else{
				$value['display'] = 0;
			}

			if($value['id'] != ''){
				$row['update'][] = $value;
			}else{
				unset($value['id']);
				$row['insert'][] = $value;
			}

		}


		if(count($row['insert']) != 0){
			$affected_rows = $this->db->insert_batch(db_prefix().'items_groups', $row['insert']);
			if($affected_rows > 0){
				$affectedRows++;
			}
		}
		if(count($row['update']) != 0){
			$affected_rows = $this->db->update_batch(db_prefix().'items_groups', $row['update'], 'id');
			if($affected_rows > 0){
				$affectedRows++;
			}
		}

		if ($affectedRows > 0) {
			return true;
		}

		return false;

	}

	/**
	 * get commodity group type
	 * @param  boolean $id
	 * @return array or object
	 */
	public function get_commodity_group_type($id = false) {

		if (is_numeric($id)) {
			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'items_groups')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblitems_groups')->result_array();
		}

	}

	/**
	 * get commodity group add commodity
	 * @return array
	 */
	public function get_commodity_group_add_commodity() {

		return $this->db->query('select * from tblitems_groups where display = 1 order by tblitems_groups.order asc ')->result_array();
	}

	/**
	 * delete commodity group type
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_commodity_group_type($id) {
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'items_groups');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * add warehouse
	 * @param array  $data
	 * @param boolean $id
	 * return boolean
	 */
	public function add_warehouse($data, $id = false) {

		$data['warehouse_type'] = new_str_replace(', ', '|/\|', $data['hot_warehouse_type']);

		$data_warehouse_type = new_explode(',', $data['warehouse_type']);

		$results = 0;
		$results_update = '';
		$flag_empty = 0;

		foreach ($data_warehouse_type as $warehouse_key => $warehouse_value) {
			if ($warehouse_value == '') {
				$warehouse_value = 0;
			}
			if (($warehouse_key + 1) % 6 == 0) {
				$arr_temp['note'] = new_str_replace('|/\|', ', ', $warehouse_value);

				if ($id == false && $flag_empty == 1) {
					$this->db->insert(db_prefix() . 'warehouse', $arr_temp);
					$insert_id = $this->db->insert_id();
					if ($insert_id) {
						$results++;
					}
				}
				if (is_numeric($id) && $flag_empty == 1) {
					$this->db->where('warehouse_id', $id);
					$this->db->update(db_prefix() . 'warehouse', $arr_temp);
					if ($this->db->affected_rows() > 0) {
						$results_update = true;
					} else {
						$results_update = false;
					}
				}
				$flag_empty = 0;
				$arr_temp = [];
			} else {

				switch (($warehouse_key + 1) % 6) {
					case 1:
					$arr_temp['warehouse_code'] = new_str_replace('|/\|', ', ', $warehouse_value);
					if ($warehouse_value != '0') {
						$flag_empty = 1;
					}
					break;
					case 2:
					$arr_temp['warehouse_name'] = new_str_replace('|/\|', ', ', $warehouse_value);
					break;
					case 3:
					$arr_temp['warehouse_address'] = new_str_replace('|/\|', ', ', $warehouse_value);
					break;
					case 4:
					$arr_temp['order'] = $warehouse_value;
					break;
					case 5:
					//display 1: display (yes) , 0: not displayed (no)
					if ($warehouse_value == 'yes') {
						$display_value = 1;
					} else {
						$display_value = 0;
					}
					$arr_temp['display'] = $display_value;
					break;
				}
			}

		}

		if ($id == false) {
			return $results > 0 ? true : false;
		} else {
			return $results_update;
		}

	}

	/**
	 * get warehouse
	 * @param  boolean $id
	 * @return array or object
	 */
	public function get_warehouse($id = false, $setting = false) {

		if (is_numeric($id)) {
			$this->db->where('warehouse_id', $id);

			return $this->db->get(db_prefix() . 'warehouse')->row();
		}
		if ($id == false) {
			$warehouseByStaff = $this->warehouse_model->getWarehouseByStaff();

			$this->db->where('display', 1);
			if(is_array($warehouseByStaff) && !$setting){
				$this->db->where('warehouse_id IN ('.implode(',', $warehouseByStaff).')');
			}
			$this->db->order_by(db_prefix().'warehouse.order', 'asc');
			return $this->db->get(db_prefix() . 'warehouse')->result_array();
		}

	}

	/**
	 * get warehouse add commodity
	 * @return array
	 */
	public function get_warehouse_add_commodity() {

		$warehouseByStaff = $this->warehouse_model->getWarehouseByStaff();

		$this->db->where('display', 1);
		if(is_array($warehouseByStaff)){
			$this->db->where('warehouse_id IN ('.implode(',', $warehouseByStaff).')');
		}
		$this->db->order_by(db_prefix().'warehouse.order', 'asc');
		return $this->db->get(db_prefix() . 'warehouse')->result_array();
	}

	/**
	 * delete warehouse
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_warehouse($id) {
		$this->db->where('warehouse_id', $id);
		$this->db->delete(db_prefix() . 'warehouse');
		if ($this->db->affected_rows() > 0) {
			/*delete customfieldsvalues*/
			$this->db->where('relid', $id);
			$this->db->where('fieldto', 'warehouse_name');
			$this->db->delete(db_prefix() . 'customfieldsvalues');

			return true;
		}
		return false;
	}

	/**
	 * add commodity
	 * @param array $data
	 * @param boolean $id
	 * return boolean
	 */
	public function add_commodity($data, $id = false) {
		$data['warehouse_type'] = new_str_replace(', ', '|/\|', $data['hot_warehouse_type']);
		$data_warehouse_type = new_explode(',', $data['warehouse_type']);

		$results = 0;
		$results_update = '';
		$flag_empty = 0;

		foreach ($data_warehouse_type as $warehouse_key => $warehouse_value) {
			$data_inventory_min = [];
			if ($warehouse_value == '') {
				$warehouse_value = 0;
			}
			if (($warehouse_key + 1) % 17 == 0) {
				$arr_temp['type_product'] = new_str_replace('|/\|', ', ', $warehouse_value);

				if ($id == false && $flag_empty == 1) {
					$this->db->insert(db_prefix() . 'items', $arr_temp);
					$insert_id = $this->db->insert_id();
					if ($insert_id) {
						$data_inventory_min['commodity_id'] = $insert_id;
						$data_inventory_min['commodity_code'] = $arr_temp['commodity_code'];
						$data_inventory_min['commodity_name'] = $arr_temp['description'];
						$this->add_inventory_min($data_inventory_min);
						$results++;
					}
				}
				if (is_numeric($id)) {
					$this->db->where('id', $id);
					$this->db->update(db_prefix() . 'items', $arr_temp);
					if ($this->db->affected_rows() > 0) {
						$results_update = true;
					} else {
						$results_update = false;
					}
				}
				$flag_empty = 0;
				$arr_temp = [];
			} else {

				switch (($warehouse_key + 1) % 17) {
					case 1:
					$arr_temp['commodity_code'] = new_str_replace('|/\|', ', ', $warehouse_value);
					break;
					case 2:
					$arr_temp['commodity_barcode'] = new_str_replace('|/\|', ', ', $warehouse_value);
					break;
					case 3:
					$arr_temp['description'] = new_str_replace('|/\|', ', ', $warehouse_value);
					break;
					case 4:
					$arr_temp['unit_id'] = $warehouse_value;
					if ($warehouse_value != '0') {
						$flag_empty = 1;
					}
					break;
					case 5:
					$arr_temp['commodity_type'] = $warehouse_value;
					break;
					case 6:
					$arr_temp['warehouse_id'] = $warehouse_value;
					break;
					case 7:
					$arr_temp['group_id'] = $warehouse_value;
					break;
					case 8:
					$arr_temp['tax'] = $warehouse_value;
					break;
					case 9:
					$arr_temp['origin'] = new_str_replace('|/\|', ', ', $warehouse_value);
					break;
					case 10:
					$arr_temp['style_id'] = $warehouse_value;
					break;
					case 11:
					$arr_temp['model_id'] = $warehouse_value;
					break;
					case 12:
					$arr_temp['size_id'] = $warehouse_value;
					break;
					case 13:
					$arr_temp['images'] = $warehouse_value;
					break;
					case 14:
					$arr_temp['date_manufacture'] = $warehouse_value;
					break;
					case 15:
					$arr_temp['expiry_date'] = $warehouse_value;
					break;
					case 16:
					$arr_temp['rate'] = $warehouse_value;
					break;

				}
			}

		}

		if ($id == false) {
			return $results > 0 ? true : false;
		} else {
			return $results_update;
		}

	}

	/**
	 * get commodity
	 * @param  boolean $id
	 * @return array or object
	 */
	public function get_commodity($id = false) {

		if (is_numeric($id)) {
			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'items')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblitems')->result_array();
		}

	}

	/**
	 * get commodity code name
	 * @return array
	 */
	public function get_commodity_code_name() {
		$arr_value = $this->db->query('select * from ' . db_prefix() . 'items where active = 1 AND id not in ( SELECT distinct parent_id from '.db_prefix().'items WHERE parent_id is not null AND parent_id != "0" ) order by id asc')->result_array();
		
		return $this->item_to_variation($arr_value);

	}

	/**
	 * get items code name
	 * @return array
	 */
	public function get_items_code_name() {
		$arr_value = $this->db->query('select * from ' . db_prefix() . 'items where active = 1 AND id not in ( SELECT distinct parent_id from '.db_prefix().'items WHERE parent_id is not null AND parent_id != "0" )  order by id desc')->result_array();
		return $this->item_to_variation($arr_value);
		
	}

	/**
	 * delete commodity
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_commodity($id) {

		//check child item before delete
		if (is_reference_in_table('parent_id', db_prefix() . 'items', $id)) {
            return [
                'referenced' => true,
            ];
        }

		hooks()->do_action('delete_item_on_woocommerce', $id);
		
		/*delete commodity min*/
		$this->db->where('commodity_id', $id);
		$this->db->delete(db_prefix() . 'inventory_commodity_min');
		/*delete file attachment*/
		$array_file= $this->get_warehourse_attachments($id);
		if(count($array_file) > 0 ){
			foreach ($array_file as $key => $file_value) {
				$this->delete_commodity_file($file_value['id']);
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
	 * get commodity hansometable
	 * @param  boolean $id
	 * @return object
	 */
	public function get_commodity_hansometable($id = false) {

		if (is_numeric($id)) {
			return $this->db->query('select description, rate, unit_id, taxrate, purchase_price, tax2, '.db_prefix().'items.tax,' . db_prefix() . 'taxes.name from ' . db_prefix() . 'items left join ' . db_prefix() . 'ware_unit_type on  ' . db_prefix() . 'items.unit_id = ' . db_prefix() . 'ware_unit_type.unit_type_id
				left join ' . db_prefix() . 'taxes on ' . db_prefix() . 'items.tax = ' . db_prefix() . 'taxes.id where ' . db_prefix() . 'items.id = ' . $id)->row();
		}
	}


	/**
	 * get commodity hansometable by barcode
	 * @param  [type] $commodity barcode 
	 * @return [type]                    
	 */
	public function get_commodity_hansometable_by_barcode($commodity_barcode) {

		$item_value = $this->db->query('select description, rate, unit_id, taxrate, purchase_price, attributes, tax2,'.db_prefix().'items.tax,' . db_prefix() . 'taxes.name,'.db_prefix().'items.id ,'.db_prefix().'items.commodity_barcode,'.db_prefix().'items.commodity_code from ' . db_prefix() . 'items left join ' . db_prefix() . 'ware_unit_type on  ' . db_prefix() . 'items.unit_id = ' . db_prefix() . 'ware_unit_type.unit_type_id
			left join ' . db_prefix() . 'taxes on ' . db_prefix() . 'items.tax = ' . db_prefix() . 'taxes.id where (' . db_prefix() . 'items.commodity_barcode = ' . $commodity_barcode.' OR '.db_prefix().'items.commodity_barcode = '.substr($commodity_barcode, 0, -1).')')->row();

		return $this->row_item_to_variation($item_value);
	}

	/**
	 * create goods code
	 * @return	string
	 */
	public function create_goods_code() {
		
		$goods_code = get_warehouse_option('inventory_received_number_prefix') . get_warehouse_option('next_inventory_received_mumber');
		
		return $goods_code;

	}

	/**
	 * add goods
	 * @param array $data
	 * @param boolean $id
	 * return boolean
	 */
	public function add_goods_receipt($data, $id = false) {

		$inventory_receipts = [];
		if (isset($data['newitems'])) {
			$inventory_receipts = $data['newitems'];
			unset($data['newitems']);
		}

		unset($data['item_select']);
		unset($data['commodity_name']);
		unset($data['warehouse_id']);
		unset($data['quantities']);
		unset($data['unit_price']);
		unset($data['tax']);
		unset($data['lot_number']);
		unset($data['date_manufacture']);
		unset($data['expiry_date']);
		unset($data['note']);
		unset($data['unit_name']);
		unset($data['sub_total']);
		unset($data['commodity_code']);
		unset($data['unit_id']);
		unset($data['tax_rate']);
		unset($data['tax_name']);
		unset($data['tax_money']);
		unset($data['goods_money']);
		unset($data['serial_number']);

		if(isset($data['warehouse_id_m'])){
			$data['warehouse_id'] = $data['warehouse_id_m'];
			unset($data['warehouse_id_m']);
		}

		if(isset($data['expiry_date_m'])){
			$data['expiry_date'] = to_sql_date($data['expiry_date_m']);
			unset($data['expiry_date_m']);
		}
		
		if(isset($data['onoffswitch'])){
			if($data['onoffswitch'] == 'on'){
				$switch_barcode_scanners = true;
				unset($data['onoffswitch']);
			}
		}

		$check_appr = $this->get_approve_setting('1');
		$data['approval'] = 0;
		if ($check_appr && $check_appr != false) {
			$data['approval'] = 0;
		} else {
			$data['approval'] = 1;
		}

		if(isset($data['save_and_send_request']) ){
			$save_and_send_request = $data['save_and_send_request'];
			unset($data['save_and_send_request']);
		}
		
		/*get suppier name from supplier code*/
		if (get_status_modules_wh('purchase')) {
			if($data['supplier_code'] != ''){
				$this->load->model('purchase/purchase_model');
				$client                = $this->purchase_model->get_vendor($id);
				if(count($client) > 0 ){
					$data['supplier_name'] = $client[0]['company'];
				}

			}
		}

		if (isset($data['hot_purchase'])) {
			$hot_purchase = $data['hot_purchase'];
			unset($data['hot_purchase']);
		}
		$data['goods_receipt_code'] = $this->create_goods_code();
		
		if(!$this->check_format_date($data['date_c'])){
			$data['date_c'] = to_sql_date($data['date_c']);
		}else{
			$data['date_c'] = $data['date_c'];
		}
		
		if(!$this->check_format_date($data['date_add'])){
			$data['date_add'] = to_sql_date($data['date_add']);
		}else{
			$data['date_add'] = $data['date_add'];
		}

		if(isset($data['expiry_date'])){
			if(!$this->check_format_date($data['expiry_date'])){
				$data['expiry_date'] = to_sql_date($data['expiry_date']);
			}else{
				$data['expiry_date'] = $data['expiry_date'];
			}
		}

		$data['addedfrom'] = get_staff_user_id();
		$data['total_tax_money'] = reformat_currency_j($data['total_tax_money']);
		$data['total_goods_money'] = reformat_currency_j($data['total_goods_money']);
		$data['value_of_inventory'] = reformat_currency_j($data['value_of_inventory']);
		$data['total_money'] = reformat_currency_j($data['total_money']);

		$this->db->insert(db_prefix() . 'goods_receipt', $data);
		$insert_id = $this->db->insert_id();

		/*insert detail*/
		if ($insert_id) {
			foreach ($inventory_receipts as $inventory_receipt) {
				$inventory_receipt['goods_receipt_id'] = $insert_id;
				if($inventory_receipt['date_manufacture'] != ''){
					$inventory_receipt['date_manufacture'] = to_sql_date($inventory_receipt['date_manufacture']);
				}else{
					$inventory_receipt['date_manufacture'] = null;
				}

				if($inventory_receipt['expiry_date'] != ''){
					$inventory_receipt['expiry_date'] = to_sql_date($inventory_receipt['expiry_date']);
				}else{
					$inventory_receipt['expiry_date'] = null;
				}

				$tax_money = 0;
				$tax_rate_value = 0;
				$tax_rate = null;
				$tax_id = null;
				$tax_name = null;
				if(isset($inventory_receipt['tax_select'])){
					$tax_rate_data = $this->wh_get_tax_rate($inventory_receipt['tax_select']);
					$tax_rate_value = $tax_rate_data['tax_rate'];
					$tax_rate = $tax_rate_data['tax_rate_str'];
					$tax_id = $tax_rate_data['tax_id_str'];
					$tax_name = $tax_rate_data['tax_name_str'];
				}

				if((float)$tax_rate_value != 0){
					$tax_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] * (float)$tax_rate_value / 100;
					$goods_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] + (float)$tax_money;
					$amount = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] + (float)$tax_money;
				}else{
					$goods_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];
					$amount = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];
				}

				$sub_total = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];

				$inventory_receipt['tax_money'] = $tax_money;
				$inventory_receipt['tax'] = $tax_id;
				$inventory_receipt['goods_money'] = $goods_money;
				$inventory_receipt['tax_rate'] = $tax_rate;
				$inventory_receipt['sub_total'] = $sub_total;
				$inventory_receipt['tax_name'] = $tax_name;
				unset($inventory_receipt['order']);
				unset($inventory_receipt['id']);
				unset($inventory_receipt['tax_select']);

				$this->db->insert(db_prefix() . 'goods_receipt_detail', $inventory_receipt);
			}
		}

		if (isset($insert_id)) {
			/*write log*/
			$data_log = [];
			$data_log['rel_id'] = $insert_id;
			$data_log['rel_type'] = 'stock_import';
			$data_log['staffid'] = get_staff_user_id();
			$data_log['date'] = date('Y-m-d H:i:s');
			$data_log['note'] = "stock_import";

			$this->add_activity_log($data_log);

			/*update next number setting*/
			$this->update_inventory_setting(['next_inventory_received_mumber' =>  get_warehouse_option('next_inventory_received_mumber')+1]);

				//send request approval
			if($save_and_send_request == 'true'){
				$this->send_request_approve(['rel_id' => $insert_id, 'rel_type' => '1', 'addedfrom' => $data['addedfrom']]);
			}
		}

		//approval if not approval setting
		if (isset($insert_id)) {
			if ($data['approval'] == 1) {
				$this->update_approve_request($insert_id, 1, 1);
			}

        	hooks()->do_action('after_wh_goods_receipt_added', $insert_id);

		}

		return $insert_id > 0 ? $insert_id : false;

	}

	/**
     * Gets the tax rate by identifier.
     */
    public function get_tax_rate_by_id($tax_ids){
        $rate_str = '';
        if($tax_ids != ''){
            $tax_ids = new_explode('|', $tax_ids);
            foreach($tax_ids as $key => $tax){
                $this->db->where('id', $tax);
                $tax_if = $this->db->get(db_prefix().'taxes')->row();
                if(($key + 1) < count($tax_ids)){
                    $rate_str .= $tax_if->taxrate.'|';
                }else{
                    $rate_str .= $tax_if->taxrate;
                }
            }
        }
        return $rate_str;
    }

	/**
	 * get goods receipt
	 * @param  integer $id
	 * @return array or object
	 */
	public function get_goods_receipt($id) {
		if (is_numeric($id)) {
			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'goods_receipt')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblgoods_receipt')->result_array();
		}
	}

	/**
	 * get goods receipt detail
	 * @param  integer $id
	 * @return array
	 */
	public function get_goods_receipt_detail($id) {
		if (is_numeric($id)) {
			$this->db->where('goods_receipt_id', $id);

			return $this->db->get(db_prefix() . 'goods_receipt_detail')->result_array();
		}
		if ($id == false) {
			return $this->db->query('select * from tblgoods_receipt_detail')->result_array();
		}
	}

	/**
	 * get purchase request
	 * @param  integer $pur_order
	 * @return array
	 */
	public function get_pur_request($pur_order, $warehouse_id) {

		$arr_pur_resquest = [];
		$total_goods_money = 0;
		$total_money = 0;
		$total_tax_money = 0;
		$value_of_inventory = 0;
		$list_item = '';
		$list_item = $this->warehouse_model->create_goods_receipt_row_template();

		$sql = 'select item_code as commodity_code, ' . db_prefix() . 'items.description, ' . db_prefix() . 'items.unit_id, unit_price, quantity as quantities, ' . db_prefix() . 'pur_order_detail.tax as tax, into_money, (' . db_prefix() . 'pur_order_detail.total-' . db_prefix() . 'pur_order_detail.into_money) as tax_money, total as goods_money, wh_quantity_received, tax_rate, tax_value, '.db_prefix().'pur_order_detail.id as id from ' . db_prefix() . 'pur_order_detail
		left join ' . db_prefix() . 'items on ' . db_prefix() . 'pur_order_detail.item_code =  ' . db_prefix() . 'items.id
		left join ' . db_prefix() . 'taxes on ' . db_prefix() . 'taxes.id = ' . db_prefix() . 'pur_order_detail.tax where ' . db_prefix() . 'pur_order_detail.pur_order = ' . $pur_order;
		$results = $this->db->query($sql)->result_array();

		$currency_rate = 1;
		$this->load->model('purchase/purchase_model');
		$get_pur_order = $this->purchase_model->get_pur_order($pur_order);
		if($get_pur_order){
			if($get_pur_order->currency_rate != null){
				$currency_rate = $get_pur_order->currency_rate;
			}
		}

		$arr_results=[];
		$index=0;
        $warehouse_data = $this->warehouse_model->get_warehouse();
		foreach ($results as $key => $value) {

			if((float)$value['quantities'] - (float)$value['wh_quantity_received'] > 0){

				$unit_price = round((float)$value['unit_price']/$currency_rate, 5);

				$index++;
				$unit_name = wh_get_unit_name($value['unit_id']);
				$taxname = '';
				$date_manufacture = null;
				$expiry_date = null;
				$lot_number = null;
				if(get_option('auto_generate_lotnumber') == 1){
					$lot_number = $this->create_lot_number();
				}
				$note = null;
				$commodity_name = wh_get_item_variatiom($value['commodity_code']);
				$quantities = (float)$value['quantities'] - (float)$value['wh_quantity_received'];
				$sub_total = 0;

				$list_item .= $this->create_goods_receipt_row_template($warehouse_data, 'newitems[' . $index . ']', $commodity_name, $warehouse_id, $quantities, $unit_name, $unit_price, $taxname, $lot_number, $date_manufacture, $expiry_date, $value['commodity_code'], $value['unit_id'] , $value['tax_rate'], $value['tax_value'], $value['goods_money'], $note, $value['id'], $sub_total, '', $value['tax'], true);

				$total_goods_money_temp = ((float)$value['quantities'] - (float)$value['wh_quantity_received'])*(float)$unit_price;
				$total_goods_money += $total_goods_money_temp;
				$arr_results[$index]['quantities'] = (float)$value['quantities'] - (float)$value['wh_quantity_received'];
				$arr_results[$index]['goods_money'] = ((float)$value['quantities'] - (float)$value['wh_quantity_received'])*(float)$unit_price;


				//get tax value
				$tax_rate = 0 ;
				if($value['tax'] != null && $value['tax'] != '') {
					$arr_tax = new_explode('|', $value['tax']);
					foreach ($arr_tax as $tax_id) {

						$tax = $this->get_taxe_value($tax_id);
						if($tax){
							$tax_rate += (float)$tax->taxrate;		    	
						}

					}
				}

				$arr_results[$index]['tax_money'] = $total_goods_money_temp*(float)$tax_rate/100;
				$total_tax_money += (float)$total_goods_money_temp*(float)$tax_rate/100;

			}
			
		}


		$total_money = $total_goods_money + $total_tax_money;
		$value_of_inventory = $total_goods_money;

		$arr_pur_resquest[] = $arr_results;
		$arr_pur_resquest[] = $total_tax_money;
		$arr_pur_resquest[] = $total_goods_money;
		$arr_pur_resquest[] = $value_of_inventory;
		$arr_pur_resquest[] = $total_money;
		$arr_pur_resquest[] = count($arr_results);
		$arr_pur_resquest[] = $list_item;


		return $arr_pur_resquest;
	}

	/**
	 * get staff
	 * @param  string $id
	 * @param  array  $where
	 * @return array or object
	 */
	public function get_staff($id = '', $where = []) {
		$select_str = '*,CONCAT(firstname," ",lastname) as full_name';

		// Used to prevent multiple queries on logged in staff to check the total unread notifications in core/AdminController.php
		if (is_staff_logged_in() && $id != '' && $id == get_staff_user_id()) {
			$select_str .= ',(SELECT COUNT(*) FROM ' . db_prefix() . 'notifications WHERE touserid=' . get_staff_user_id() . ' and isread=0) as total_unread_notifications, (SELECT COUNT(*) FROM ' . db_prefix() . 'todos WHERE finished=0 AND staffid=' . get_staff_user_id() . ') as total_unfinished_todos';
		}

		$this->db->select($select_str);
		$this->db->where($where);

		if (is_numeric($id)) {
			$this->db->where('staffid', $id);
			$staff = $this->db->get(db_prefix() . 'staff')->row();

			if ($staff) {
				$staff->permissions = $this->get_staff_permissions($id);
			}

			return $staff;
		}
		$this->db->order_by('firstname', 'desc');

		return $this->db->get(db_prefix() . 'staff')->result_array();
	}

	/**
	 * update status goods
	 * @param  integer $pur_orders_id
	 * @return boolean
	 */
	public function update_status_goods($pur_orders_id) {
		$arr_temp['status_goods'] = 1;
		$this->db->where('id', $pur_orders_id);
		$this->db->update(db_prefix() . 'pur_orders', $arr_temp);
	}

	/**
	 * add goods transaction detail
	 * @param array $data
	 * @param string $status
	 */
	public function add_goods_transaction_detail($data, $status) {
		if ($status == '1') {
			$data_insert['goods_receipt_id'] = $data['goods_receipt_id'];
			$data_insert['purchase_price'] = $data['unit_price'];
			$data_insert['expiry_date'] = $data['expiry_date'];
			$data_insert['lot_number'] = $data['lot_number'];
			$data_insert['serial_number'] = $data['serial_number'];
			
		} elseif ($status == '2') {
			$data_insert['goods_receipt_id'] = $data['goods_delivery_id'];
			$data_insert['price'] = $data['unit_price'];
			$data_insert['expiry_date'] = $data['expiry_date'];
			$data_insert['lot_number'] = $data['lot_number'];
			$data_insert['serial_number'] = $data['serial_number'];
			$data_insert['purchase_price'] = $data['purchase_price'];

		}

		/*get old quantity by item, warehouse*/
		if(is_numeric($data['warehouse_id'])){
			$inventory_value = $this->get_quantity_inventory($data['warehouse_id'], $data['commodity_code']);
			$old_quantity =  null;
			if($inventory_value){
				$old_quantity = $inventory_value->inventory_number;
			}
		}else{
			$old_quantity =  (float)$data['available_quantity'];
		}

		$data_insert['goods_id'] = $data['id'];
		if(isset($data['old_quantity']) && $status == '2'){
			$data_insert['old_quantity'] = $data['old_quantity'];
		}else{
			$data_insert['old_quantity'] = $old_quantity;
		}

		$data_insert['commodity_id'] = $data['commodity_code'];
		$data_insert['quantity'] = $data['quantities'];
		$data_insert['date_add'] = date('Y-m-d H:i:s');
		$data_insert['warehouse_id'] = $data['warehouse_id'];
		$data_insert['note'] = $data['note'];
		$data_insert['status'] = $status;
		// status '1:Goods receipt note 2:Goods delivery note',
		$this->db->insert(db_prefix() . 'goods_transaction_detail', $data_insert);

		return true;
	}

	/**
	 * add inventory manage
	 * @param array $data
	 * @param string $status
	 */
	public function add_inventory_manage($data, $status) {
		// status '1:Goods receipt note 2:Goods delivery note',
		$affected_rows=0;

		if ($status == 1) {

			$this->db->where('purchase_price', $data['unit_price']);
			if(isset($data['lot_number']) && $data['lot_number'] != '0' && $data['lot_number'] != ''){
				/*have value*/
				$this->db->where('lot_number', $data['lot_number']);

			}else{

				/*lot number is 0 or ''*/
				$this->db->group_start();

				$this->db->where('lot_number', '0');
				$this->db->or_where('lot_number', '');
				$this->db->or_where('lot_number', null);

				$this->db->group_end();
			}

			if($data['expiry_date'] == ''){
				
				$this->db->where('expiry_date', null);
			}else{
				$this->db->where('expiry_date', $data['expiry_date']);
			}

			$this->db->where('warehouse_id', $data['warehouse_id']);
			$this->db->where('commodity_id', $data['commodity_code']);

			$total_rows = $this->db->count_all_results(db_prefix().'inventory_manage');

			if ($total_rows > 0) {
				$status_insert_update = false;
			} else {
				$status_insert_update = true;
			}

			if (!$status_insert_update) {
				//update
				$this->db->where('purchase_price', $data['unit_price']);
				$this->db->where('warehouse_id', $data['warehouse_id']);
				$this->db->where('commodity_id', $data['commodity_code']);

				if(isset($data['lot_number']) && $data['lot_number'] != '0' && $data['lot_number'] != ''){
					/*have value*/
					$this->db->where('lot_number', $data['lot_number']);

				}else{

					/*lot number is 0 or ''*/
					$this->db->group_start();

					$this->db->where('lot_number', '0');
					$this->db->or_where('lot_number', '');
					$this->db->or_where('lot_number', null);

					$this->db->group_end();
				}

				if($data['expiry_date'] == ''){

					$this->db->where('expiry_date', null);
				}else{
					$this->db->where('expiry_date', $data['expiry_date']);
				}


				$result = $this->db->get(db_prefix().'inventory_manage')->row();
				$inventory_number = $result->inventory_number;
				$update_id = $result->id;

				if ($status == 1) {
					//Goods receipt
					$data_update['inventory_number'] = (float) $inventory_number + (float) $data['quantities'];
				} elseif ($status == 2) {
					// 2:Goods delivery note
					$data_update['inventory_number'] = (float) $inventory_number - (float) $data['quantities'];
				}

				//update
				$this->db->where('id', $update_id);
				$this->db->update(db_prefix() . 'inventory_manage', $data_update);

				if ($this->db->affected_rows() > 0) {
					$affected_rows++;
				}

				//handle add serial number
				$this->add_serial_number($data['commodity_code'], $data['warehouse_id'], $update_id, $data['serial_number']);

			} else {
				//insert
				$data_insert['warehouse_id'] = $data['warehouse_id'];
				$data_insert['commodity_id'] = $data['commodity_code'];
				$data_insert['inventory_number'] = $data['quantities'];
				$data_insert['date_manufacture'] = $data['date_manufacture'];
				$data_insert['expiry_date'] = $data['expiry_date'];
				$data_insert['lot_number'] = $data['lot_number'];
				$data_insert['purchase_price'] = $data['unit_price'];

				$this->db->insert(db_prefix() . 'inventory_manage', $data_insert);
				$insert_id = $this->db->insert_id();

				if ($insert_id) {
					$affected_rows++;
				}
				// handle add serial number
				$this->add_serial_number($data['commodity_code'], $data['warehouse_id'], $insert_id, $data['serial_number']);

			}

			if($affected_rows > 0){
				return true;
			}
			return false;

		} else {
			//status == 2 export
			//update
			// check in case serial number exit

			if(new_strlen($data['serial_number']) > 0){
				$this->db->where('commodity_id', $data['commodity_code']);
				$this->db->where('warehouse_id', $data['warehouse_id']);
				$this->db->where('serial_number', $data['serial_number']);
				$this->db->where('is_used', 'no');
				$this->db->order_by('id', 'asc');
				$inventory_serial_numbers = $this->db->get(db_prefix().'wh_inventory_serial_numbers')->row();
				if($inventory_serial_numbers){
					$inventory_manage_id = $inventory_serial_numbers->inventory_manage_id;
				}
			}

			if(isset($inventory_manage_id)){
				$this->db->where('id', $inventory_manage_id);
			}
			$this->db->where('warehouse_id', $data['warehouse_id']);
			$this->db->where('commodity_id', $data['commodity_code']);
			$this->db->order_by('id', 'ASC');
			$result = $this->db->get('tblinventory_manage')->result_array();

			$temp_quantities = $data['quantities'];

			$expiry_date = '';
			$lot_number = '';
			$str_serial_number = '';
			$data_transaction_detail = [];

			foreach ($result as $result_value) {
				$temp_purchase_price = $result_value['purchase_price'];
				// get old quantity
	    		$inventory_value = $this->get_quantity_inventory($result_value['warehouse_id'], $result_value['commodity_id']);
				$old_quantity =  0;
				if($inventory_value){
					$old_quantity = $inventory_value->inventory_number;
				}

				if (($result_value['inventory_number'] != 0) && ($temp_quantities != 0)) {

					if ($temp_quantities >= $result_value['inventory_number']) {
						$temp_quantities = (float) $temp_quantities - (float) $result_value['inventory_number'];

						//log lot number
						if(($result_value['lot_number'] != null) && ($result_value['lot_number'] != '') ){
							if(new_strlen($lot_number ?? '') != 0){
								$lot_number .=','.$result_value['lot_number'].','.$result_value['inventory_number'];
							}else{
								$lot_number .= $result_value['lot_number'].','.$result_value['inventory_number'];
							}
						}
						
						//log expiry date
						if(($result_value['expiry_date'] != null) && ($result_value['expiry_date'] != '') ){
							if(new_strlen($expiry_date ?? '') != 0){
								$expiry_date .=','.$result_value['expiry_date'].','.$result_value['inventory_number'];
							}else{
								$expiry_date .= $result_value['expiry_date'].','.$result_value['inventory_number'];
							}
						}

						//update inventory
						$this->db->where('id', $result_value['id']);
						$this->db->update(db_prefix() . 'inventory_manage', [
							'inventory_number' => 0,
						]);

						if ($this->db->affected_rows() > 0) {
							$affected_rows++;
						}

						$old_quantity -= (float)$result_value['inventory_number'];

						//get serial number for deivery note
						$serial_number_for_delivery_note = $this->get_serial_number_for_delivery_note($result_value['commodity_id'], $result_value['warehouse_id'], $result_value['id'], $result_value['inventory_number'], $data['serial_number'], $data['id'], $data['commodity_name']);

						$data_transaction_detail[] = [
							'goods_delivery_id' => $data['goods_delivery_id'],
							'purchase_price' => $temp_purchase_price,
							'unit_price' => $data['unit_price'],
							'expiry_date' => $data['expiry_date'],
							'lot_number' => $data['lot_number'],
							'serial_number' => $serial_number_for_delivery_note,
							'warehouse_id' => $data['warehouse_id'],
							'commodity_code' => $data['commodity_code'],
							'id' => $data['id'],
							'quantities' => $result_value['inventory_number'],
							'old_quantity' => $old_quantity,
							'note' => $data['note'],
						];
						
					} else {

						//log lot number
						if(($result_value['lot_number'] != null) && ($result_value['lot_number'] != '') ){
							if(new_strlen($lot_number ?? '') != 0){
								$lot_number .=','.$result_value['lot_number'].','.$temp_quantities;
							}else{
								$lot_number .= $result_value['lot_number'].','.$temp_quantities;
							}
						}

						//log expiry date
						if(($result_value['expiry_date'] != null) && ($result_value['expiry_date'] != '') ){
							if(new_strlen($expiry_date ?? '') != 0){
								$expiry_date .=','.$result_value['expiry_date'].','.$temp_quantities;
							}else{
								$expiry_date .= $result_value['expiry_date'].','.$temp_quantities;
							}
						}


						//update inventory
						$this->db->where('id', $result_value['id']);
						$this->db->update(db_prefix() . 'inventory_manage', [
							'inventory_number' => (float) $result_value['inventory_number'] - (float) $temp_quantities,
						]);

						if ($this->db->affected_rows() > 0) {
							$affected_rows++;
						}

						$old_quantity -= (float)$temp_quantities;

						//get serial number for deivery note
						$serial_number_for_delivery_note = $this->get_serial_number_for_delivery_note($result_value['commodity_id'], $result_value['warehouse_id'], $result_value['id'], $temp_quantities, $data['serial_number'], $data['id'], $data['commodity_name']);

						$data_transaction_detail[] = [
							'goods_delivery_id' => $data['goods_delivery_id'],
							'purchase_price' => $temp_purchase_price,
							'unit_price' => $data['unit_price'],
							'expiry_date' => $data['expiry_date'],
							'lot_number' => $data['lot_number'],
							'serial_number' => $serial_number_for_delivery_note,
							'warehouse_id' => $data['warehouse_id'],
							'commodity_code' => $data['commodity_code'],
							'id' => $data['id'],
							'quantities' => $temp_quantities,
							'old_quantity' => $old_quantity,
							'note' => $data['note'],
						];

						$temp_quantities = 0;

					}

					if(new_strlen($serial_number_for_delivery_note ?? '') > 0){
						if(new_strlen($str_serial_number ?? '') > 0){
							$str_serial_number .= ','.$serial_number_for_delivery_note;
						}else{
							$str_serial_number .= $serial_number_for_delivery_note;
						}
					}

				}

			}

			//update good delivery detail
			$this->db->where('id', $data['id']);
			$this->db->update(db_prefix() . 'goods_delivery_detail', [
				'expiry_date' => $expiry_date,
				'lot_number' => $lot_number,
				'serial_number' => $str_serial_number,
			]);
			if ($this->db->affected_rows() > 0) {
				$affected_rows++;
			}

			//goods transaction detail log
			$data['expiry_date'] = $expiry_date;
			$data['lot_number'] = $lot_number;
			$data['serial_number'] = $str_serial_number;
			if(count($data_transaction_detail) > 0){
				foreach ($data_transaction_detail as $value) {
					$this->add_goods_transaction_detail($value, 2);
				}
			}

			if($affected_rows > 0){
				return true;
			}
			return false;

		}


	}

	/**
	 * check commodity exist inventory
	 * @param  integer $warehouse_id
	 * @param  integer $commodity_id
	 * @return boolean
	 */
	public function check_commodity_exist_inventory($warehouse_id, $commodity_id, $lot_number, $expiry_date) {

		if(isset($lot_number) && $lot_number != '0' && $lot_number != ''){
			/*have value*/
			$this->db->where('lot_number', $lot_number);

		}else{

			/*lot number is 0 or ''*/
			$this->db->group_start();

			$this->db->where('lot_number', '0');
			$this->db->or_where('lot_number', '');
			$this->db->or_where('lot_number', null);

			$this->db->group_end();
		}

		$this->db->where('warehouse_id', $warehouse_id);
		$this->db->where('commodity_id', $commodity_id);

		if($expiry_date == ''){
			$this->db->where('expiry_date', null);
		}else{
			$this->db->where('expiry_date', $expiry_date);
		}

		$total_rows = $this->db->count_all_results('tblinventory_manage');

		//if > 0 update, else insert
		return $total_rows > 0 ? false : true;

	}

	/**
	 * get inventory commodity
	 * @param  integer $commodity_id
	 * @return array
	 */
	public function get_inventory_commodity($commodity_id) {
		$sql = 'SELECT ' . db_prefix() . 'warehouse.warehouse_code, sum(inventory_number) as inventory_number, unit_name FROM ' . db_prefix() . 'inventory_manage
		LEFT JOIN ' . db_prefix() . 'items on ' . db_prefix() . 'inventory_manage.commodity_id = ' . db_prefix() . 'items.id
		LEFT JOIN ' . db_prefix() . 'ware_unit_type on ' . db_prefix() . 'items.unit_id = ' . db_prefix() . 'ware_unit_type.unit_type_id
		LEFT JOIN ' . db_prefix() . 'warehouse on ' . db_prefix() . 'inventory_manage.warehouse_id = ' . db_prefix() . 'warehouse.warehouse_id
		where commodity_id = ' . $commodity_id . ' group by ' . db_prefix() . 'inventory_manage.warehouse_id';
		return $this->db->query($sql)->result_array();

	}

	/**
	 * add inventory min
	 * @param array $data
	 * return boolean
	 */
	public function add_inventory_min($data) {
		if(!isset($data['inventory_number_min'])){
			$data['inventory_number_min'] = 0;
		}
		$this->db->insert(db_prefix() . 'inventory_commodity_min', $data);
		$insert_id = $this->db->insert_id();

		if ($insert_id) {
			return $insert_id;
		}
		return false;
	}

	/**
	 * get inventory min
	 * @param  boolean $id
	 * @return array or object
	 */
	public function get_inventory_min($id = false) {
		if (is_numeric($id)) {
			$this->db->where('commodity_id', $id);

			return $this->db->get(db_prefix() . 'inventory_commodity_min')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblinventory_commodity_min')->result_array();
		}
	}

	/**
	 * setting get inventory min
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function setting_get_inventory_min()
	{
		$items = $this->get_commodity();
		$inventory_min = $this->get_inventory_min();

		$item_min=[];
		foreach ($inventory_min as $value) {
		    $item_min[$value['commodity_id']] = $value;
		}

		$data_result=[];

		foreach ($items as $key => $i_value) {
		    if(isset($item_min[$i_value['id']])){

		    	$data_result[$key]['id'] = $item_min[$i_value['id']]['id'];
		    	$data_result[$key]['inventory_number_min'] = $item_min[$i_value['id']]['inventory_number_min'];
		    	$data_result[$key]['inventory_number_max'] = $item_min[$i_value['id']]['inventory_number_max'];

		    }else{
		    	$data_result[$key]['id'] = 0;
		    	$data_result[$key]['inventory_number_min'] = 0;
		    	$data_result[$key]['inventory_number_max'] = 0;
		    }

		    $data_result[$key]['commodity_id'] = $i_value['id'];
		    $data_result[$key]['commodity_code'] = $i_value['commodity_code'];
		    $data_result[$key]['commodity_name'] = $i_value['description'];
		    $data_result[$key]['sku_code'] = $i_value['sku_code'];
		}

		return $data_result;		
	}

	/**
	 * get inventory min by commodity id
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function get_inventory_min_by_commodity_id($id = false) {
		if (is_numeric($id)) {
			$this->db->where('commodity_id', $id);

			return $this->db->get(db_prefix() . 'inventory_commodity_min')->row();
		}
	}

	/**

	 * update inventory min
	 * @param  array $data
	 * @return boolean
	 */
	public function update_inventory_min($data) {
		$affectedRows =0;

		if(isset($data['inventory_min'])){
			$data_inventory = json_decode($data['inventory_min']);

			$es_detail = [];
			$row = [];
			$row['update'] = []; 
			$row['insert'] = []; 

			$rq_val = [];
			$header = [];

			$header[] = 'id';
			$header[] = 'commodity_id';
			$header[] = 'commodity_code';
			$header[] = 'commodity_name';
			$header[] = 'sku_code';
			$header[] = 'inventory_number_min';
			$header[] = 'inventory_number_max';

			foreach ($data_inventory as $key => $value) {

					$es_detail[] = array_combine($header, $value);
			}

			foreach ($es_detail as $key => $value) {

				if($value['id'] != '' && $value['id'] != '0'){
					unset($value['commodity_id']);
					unset($value['sku_code']);
					$row['update'][] = $value;
				}else{
					unset($value['id']);
					unset($value['sku_code']);
					$row['insert'][] = $value;
				}

			}

			if(count($row['insert']) != 0){
				$affected_rows = $this->db->insert_batch(db_prefix().'inventory_commodity_min', $row['insert']);
				if($affected_rows > 0){
					$affectedRows++;
				}
			}

			if(count($row['update']) != 0){
				$this->db->update_batch(db_prefix().'inventory_commodity_min', $row['update'], 'id');
				if($this->db->affected_rows() > 0){
					$affectedRows++;
				}
			}
		}

		if($affectedRows > 0){
			return true;
		}

		return false;

	}

	/**
	 * get commodity warehouse
	 * @param  boolean $id
	 * @return array
	 */
	public function get_commodity_warehouse($commodity_id = false) {
		if ($commodity_id != false) {
			$warehouseByStaff = $this->warehouse_model->getWarehouseByStaff();

    		//get parent quantity
			if(is_array($warehouseByStaff)){
				$sql = 'SELECT ' . db_prefix() . 'warehouse.warehouse_name, '.db_prefix().'warehouse.warehouse_id, '.db_prefix().'inventory_manage.inventory_number FROM ' . db_prefix() . 'inventory_manage
				LEFT JOIN ' . db_prefix() . 'warehouse on ' . db_prefix() . 'inventory_manage.warehouse_id = ' . db_prefix() . 'warehouse.warehouse_id
				where ' . db_prefix() . 'inventory_manage.commodity_id = ' . $commodity_id.' AND '.db_prefix().'inventory_manage.inventory_number > 0 AND '.db_prefix().'inventory_manage.warehouse_id IN ('.implode(",", $warehouseByStaff).')  order by '.db_prefix().'warehouse.order asc';
			}else{

				$sql = 'SELECT ' . db_prefix() . 'warehouse.warehouse_name, '.db_prefix().'warehouse.warehouse_id, '.db_prefix().'inventory_manage.inventory_number FROM ' . db_prefix() . 'inventory_manage
				LEFT JOIN ' . db_prefix() . 'warehouse on ' . db_prefix() . 'inventory_manage.warehouse_id = ' . db_prefix() . 'warehouse.warehouse_id
				where ' . db_prefix() . 'inventory_manage.commodity_id = ' . $commodity_id.' AND '.db_prefix().'inventory_manage.inventory_number > 0 order by '.db_prefix().'warehouse.order asc';

			}
			return $this->db->query($sql)->result_array();
		}

	}

	/**
	 * get total inventory commodity
	 * @param  boolean $id
	 * @return object
	 */
	public function get_total_inventory_commodity($commodity_id = false) {
		if ($commodity_id != false) {

			$sql = 'SELECT sum(inventory_number) as inventory_number FROM ' . db_prefix() . 'inventory_manage
			where ' . db_prefix() . 'inventory_manage.commodity_id = ' . $commodity_id . ' order by ' . db_prefix() . 'inventory_manage.warehouse_id';

			return $this->db->query($sql)->row();
		}

	}

	/**
	 * add approval setting
	 * @param  array $data
	 * @return boolean
	 */
	public function add_approval_setting($data) {
		unset($data['approval_setting_id']);

		if (isset($data['approver'])) {
			$setting = [];
			foreach ($data['approver'] as $key => $value) {
				$node = [];
				$node['approver'] = 'staff';
				$node['staff'] = $data['staff'][$key];
				$node['action'] = $data['action'][$key];

				$setting[] = $node;
			}
			unset($data['approver']);
			unset($data['staff']);
			unset($data['action']);
		}

		$data['setting'] = json_encode($setting);

		$this->db->insert(db_prefix() . 'wh_approval_setting', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			return true;
		}
		return false;
	}

	/**
	 * edit approval setting
	 * @param  integer $id
	 * @param   array $data
	 * @return    boolean
	 */
	public function edit_approval_setting($id, $data) {
		unset($data['approval_setting_id']);

		if (isset($data['approver'])) {
			$setting = [];
			foreach ($data['approver'] as $key => $value) {
				$node = [];
				$node['approver'] = 'staff';
				$node['staff'] = $data['staff'][$key];
				$node['action'] = $data['action'][$key];

				$setting[] = $node;
			}
			unset($data['approver']);
			unset($data['staff']);
			unset($data['action']);
		}
		$data['setting'] = json_encode($setting);

		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'wh_approval_setting', $data);

		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete approval setting
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_approval_setting($id) {
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'wh_approval_setting');

			if ($this->db->affected_rows() > 0) {
				return true;
			}
		}
		return false;
	}

	/**
	 * get approval setting
	 * @param  boolean $id
	 * @return array or object
	 */
	public function get_approval_setting($id = '') {
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'wh_approval_setting')->row();
		}
		return $this->db->get(db_prefix() . 'wh_approval_setting')->result_array();
	}

	/**
	 * get staff sign
	 * @param   integer $rel_id
	 * @param   string $rel_type
	 * @return  array
	 */
	public function get_staff_sign($rel_id, $rel_type) {
		$this->db->select('*');

		$this->db->where('rel_id', $rel_id);
		$this->db->where('rel_type', $rel_type);
		$this->db->where('action', 'sign');
		$approve_status = $this->db->get(db_prefix() . 'wh_approval_details')->result_array();
		if (isset($approve_status)) {
			$array_return = [];
			foreach ($approve_status as $key => $value) {
				array_push($array_return, $value['staffid']);
			}
			return $array_return;
		}
		return [];
	}

	/**
	 * check approval detail
	 * @param   integer $rel_id
	 * @param   string $rel_type
	 * @return  boolean
	 */
	public function check_approval_details($rel_id, $rel_type) {
		$this->db->where('rel_id', $rel_id);
		$this->db->where('rel_type', $rel_type);
		$approve_status = $this->db->get(db_prefix() . 'wh_approval_details')->result_array();

		if (count($approve_status) > 0) {
			foreach ($approve_status as $value) {
				if ($value['approve'] == -1) {
					return 'reject';
				}
				if ($value['approve'] == 0) {
					$value['staffid'] = new_explode(', ', $value['staffid']);
					return $value;
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * get list approval detail
	 * @param   integer $rel_id
	 * @param   string $rel_type
	 * @return  array
	 */
	public function get_list_approval_details($rel_id, $rel_type) {
		$this->db->select('*');
		$this->db->where('rel_id', $rel_id);
		$this->db->where('rel_type', $rel_type);
		return $this->db->get(db_prefix() . 'wh_approval_details')->result_array();
	}

	/**
	 * add activity log
	 * @param array $data
	 * return boolean
	 */
	public function add_activity_log($data) {
		$this->db->insert(db_prefix() . 'wh_activity_log', $data);
		return true;
	}

	/**
	 * get activity log
	 * @param   integer $rel_id
	 * @param   string $rel_type
	 * @return  array
	 */
	public function get_activity_log($rel_id, $rel_type) {
		$this->db->where('rel_id', $rel_id);
		$this->db->where('rel_type', $rel_type);
		return $this->db->get(db_prefix() . 'wh_activity_log')->result_array();
	}

	/**
	 * 	delete activiti log
	 * @param   integer $rel_id
	 * @param   string $rel_type
	 * @return  boolean
	 */
	public function delete_activity_log($rel_id, $rel_type) {
		$this->db->where('rel_id', $rel_id);
		$this->db->where('rel_type', $rel_type);
		$this->db->delete(db_prefix() . 'wh_activity_log');
		return true;
	}

	/**
	 *  send request approve
	 * @param  array $data
	 * @return boolean
	 */
	public function send_request_approve($data) {
		if (!isset($data['status'])) {
			$data['status'] = '';
		}

		$date_send = date('Y-m-d H:i:s');
		$data_new = $this->get_approve_setting($data['rel_type'], $data['status']);
		if(!$data_new){
			return false;
		}


		$this->delete_approval_details($data['rel_id'], $data['rel_type']);
		$list_staff = $this->staff_model->get();
		$list = [];
		$staff_addedfrom = $data['addedfrom'];
		$sender = get_staff_user_id();

		foreach ($data_new as $value) {
			$row = [];

			if ($value->approver !== 'staff') {
				$value->staff_addedfrom = $staff_addedfrom;
				$value->rel_type = $data['rel_type'];
				$value->rel_id = $data['rel_id'];

				$approve_value = $this->get_staff_id_by_approve_value($value, $value->approver);
				if (is_numeric($approve_value)) {
					$approve_value = $this->staff_model->get($approve_value)->email;
				} else {

					$this->db->where('rel_id', $data['rel_id']);
					$this->db->where('rel_type', $data['rel_type']);
					$this->db->delete('tblwh_approval_details');

					return $value->approver;
				}
				$row['approve_value'] = $approve_value;

				$staffid = $this->get_staff_id_by_approve_value($value, $value->approver);

				if (empty($staffid)) {
					$this->db->where('rel_id', $data['rel_id']);
					$this->db->where('rel_type', $data['rel_type']);
					$this->db->delete('tblwh_approval_details');

					return $value->approver;
				}

				$row['action'] = $value->action;
				$row['staffid'] = $staffid;
				$row['date_send'] = $date_send;
				$row['rel_id'] = $data['rel_id'];
				$row['rel_type'] = $data['rel_type'];
				$row['sender'] = $sender;
				$this->db->insert('tblwh_approval_details', $row);

			} else if ($value->approver == 'staff') {
				$row['action'] = $value->action;
				$row['staffid'] = $value->staff;
				$row['date_send'] = $date_send;
				$row['rel_id'] = $data['rel_id'];
				$row['rel_type'] = $data['rel_type'];
				$row['sender'] = $sender;

				$this->db->insert('tblwh_approval_details', $row);
			}
		}
		return true;
	}

	/**
	 * get approve setting
	 * @param  integer] $type
	 * @param  string $status
	 * @return object
	 */
	public function get_approve_setting($type, $status = '') {

		$this->db->select('*');
		$this->db->where('related', $type);
		$approval_setting = $this->db->get('tblwh_approval_setting')->row();
		if ($approval_setting) {
			return json_decode($approval_setting->setting);
		} else {
			return false;
		}

	}

	/**
	 * delete approval details
	 * @param  integer $rel_id
	 * @param  string $rel_type
	 * @return  boolean
	 */
	public function delete_approval_details($rel_id, $rel_type) {
		$this->db->where('rel_id', $rel_id);
		$this->db->where('rel_type', $rel_type);
		$this->db->delete(db_prefix() . 'wh_approval_details');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get staff id by approve value
	 * @param  array $data
	 * @param  integer $approve_value
	 * @return boolean
	 */
	public function get_staff_id_by_approve_value($data, $approve_value) {
		$list_staff = $this->staff_model->get();
		$list = [];
		$staffid = [];

		if ($approve_value == 'department_manager') {
			$staffid = $this->departments_model->get_staff_departments($data->staff_addedfrom)[0]['manager_id'];
		} elseif ($approve_value == 'direct_manager') {
			$staffid = $this->staff_model->get($data->staff_addedfrom)->team_manage;
		}

		return $staffid;
	}

	/**
	 *  update approval details
	 * @param  integer $id
	 * @param  array $data
	 * @return boolean
	 */
	public function update_approval_details($id, $data) {
		$data['date'] = date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'wh_approval_details', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * update approve request
	 * @param  integer $rel_ids
	 * @param  string $rel_type
	 * @param  integer $status
	 * @return boolean
	 */
	public function update_approve_request($rel_id, $rel_type, $status) {
		$data_update = [];

		switch ($rel_type) {
		//case 1: stock_import
			case '1':
			$data_update['approval'] = $status;
			$this->db->where('id', $rel_id);
			$this->db->update(db_prefix() . 'goods_receipt', $data_update);

			if((int)$status == 1){
			// //update history stock, inventoty manage after staff approved
				$goods_receipt_detail = $this->get_goods_receipt_detail($rel_id);

				/*check goods receipt from PO*/
				$flag_update_status_po = true;

				$from_po = false;
				$goods_receipt = $this->get_goods_receipt($rel_id);

				if($goods_receipt){
					if(isset($goods_receipt->pr_order_id) && ($goods_receipt->pr_order_id != 0) ){
						$from_po = true;
					}
				}

				foreach ($goods_receipt_detail as $key => $goods_receipt_detail_value) {

					/*update Without checking warehouse*/		

					if($this->check_item_without_checking_warehouse($goods_receipt_detail_value['commodity_code']) == true){

						$this->add_goods_transaction_detail($goods_receipt_detail_value, 1);
						$this->add_inventory_manage($goods_receipt_detail_value, 1);

					//update po detail
						if($from_po){
							$update_status = $this->update_po_detail_quantity($goods_receipt->pr_order_id, $goods_receipt_detail_value);
						}

					}

					if($key+1 == count($goods_receipt_detail)){

						//update po detail
						if($from_po){
							$this->db->where('pur_order', $goods_receipt->pr_order_id);
							$pur_order_detail = $this->db->get(db_prefix().'pur_order_detail')->result_array();
							foreach ($pur_order_detail as $p_key => $value) {
								if((float)$value['quantity'] > (float)$value['wh_quantity_received']){
									$flag_update_status_po = false;
								}
							}
						}

					}

				}

				/*update status po*/
				if($from_po == true && $flag_update_status_po == true){
					if (get_status_modules_wh('purchase')) {
						if ($this->db->field_exists('delivery_status' ,db_prefix() . 'pur_orders')) { 
							$this->db->where('id', $goods_receipt->pr_order_id);
							$this->db->update(db_prefix() . 'pur_orders', ['status_goods' => 1, 'delivery_status' => 1]);
						}
					}

				}

				// accounting module hook after goods receipt approve
	            hooks()->do_action('after_wh_goods_receipt_approve', $rel_id);
			}


			return true;
			break;
			case '2':
			$data_update['approval'] = $status;
			$this->db->where('id', $rel_id);
			$this->db->update(db_prefix() . 'goods_delivery', $data_update);

			if((int)$status == 1){
			//update status invoice or pur order for this inventory delivery
				$goods_delivery = $this->get_goods_delivery($rel_id);
				$goods_delivery_detail = $this->get_goods_delivery_detail($rel_id);

				if($goods_delivery){

					if(is_numeric($goods_delivery->invoice_id) && $goods_delivery->invoice_id != 0){
						$type = 'invoice';
						$rel_type = $goods_delivery->invoice_id;
					}elseif(is_numeric($goods_delivery->pr_order_id) && $goods_delivery->pr_order_id != 0){
						$type = 'purchase_orders';
						$rel_type = $goods_delivery->pr_order_id;

					}
				//check create shipment from delivery note
					if(is_numeric($goods_delivery->customer_code)){
						// create_shipment_from_delivery_note
						$this->create_shipment_from_delivery_note($rel_id);
					}

					if(isset($type)){
						if($type == 'invoice'){
						//check delivery partial or total
							$flag_update = true;
							$type_of_delivery = 'total';

							if(count($goods_delivery_detail) > 0){
								$invoice_delivery_partial_or_total = $this->invoice_delivery_partial_or_total($rel_type, $goods_delivery_detail);
								$flag_update = $invoice_delivery_partial_or_total['flag_update_status'];
								$type_of_delivery = $invoice_delivery_partial_or_total['type_of_delivery'];
							}


							$this->db->where('id', $rel_id);
							$this->db->update(db_prefix() . 'goods_delivery', ['type_of_delivery' => $type_of_delivery, 'delivery_status' => 'ready_for_packing']);

							if($flag_update == true){
								$this->db->insert(db_prefix().'goods_delivery_invoices_pr_orders', [
									'rel_id' 	=> $rel_id,
									'rel_type' 	=> $rel_type,
									'type' 		=> $type,
								]);

								if(is_numeric($goods_delivery->customer_code)){
									$this->warehouse_check_update_shipment_when_delivery_note_approval($rel_id);
								}

							//update shipment when delivery note approval
								$this->check_update_shipment_when_delivery_note_approval($rel_id);

							}

						}else{

							$this->db->insert(db_prefix().'goods_delivery_invoices_pr_orders', [
								'rel_id' 	=> $rel_id,
								'rel_type' 	=> $rel_type,
								'type' 		=> $type,
							]);
						}

					}


				}

			//update history stock, inventoty manage after staff approved

				foreach ($goods_delivery_detail as $goods_delivery_detail_value) {
				// add goods transaction detail (log) after update invetory number

				//update Without checking warehouse				
					if($this->check_item_without_checking_warehouse($goods_delivery_detail_value['commodity_code']) == true){

						$this->add_inventory_manage($goods_delivery_detail_value, 2);
					}

				}

				// accounting module hook after goods delivery approve
	            hooks()->do_action('after_wh_goods_delivery_approve', $rel_id);
			}

			return true;
			break;


			case '3':
		//update lost adjustment
			if($status == 1){
				$status = $this->change_adjust($rel_id);

				// accounting module hook after loss adjustment approve
	            hooks()->do_action('after_wh_loss_adjustment_approve', $rel_id);

				return $status;
				break;

			}else{
				$this->db->where('id', $rel_id);
				$this->db->update(db_prefix() . 'wh_loss_adjustment', [
					'status' => -1,
				]);

			}

			return false;
			break;

			case '4':
		//internal delivery note
			
			$data_update['approval'] = $status;
			$this->db->where('id', $rel_id);
			$this->db->update(db_prefix() . 'internal_delivery_note', $data_update);
			//log
			// history stock, inventoty manage after staff approved
			if((int)$status == 1){
				$internal_delivery_detail = $this->get_internal_delivery_detail($rel_id);

				foreach ($internal_delivery_detail as $internal_delivery_detail_value) {
				// add goods transaction detail (log) after update invetory number

					$this->approval_internal_delivery_detail($internal_delivery_detail_value);

				}
			}


			return false;
			break;

			case '5':
			//packing list
			$data_update['approval'] = $status;
			$this->db->where('id', $rel_id);
			$this->db->update(db_prefix() . 'wh_packing_lists', $data_update);

			if((int)$status == 1){
			//update status invoice or pur order for this inventory delivery
				$packing_list = $this->get_packing_list($rel_id);
				$packing_list_detail = $this->get_packing_list_detail($rel_id);

				if($packing_list){

					$rel_type = $packing_list->delivery_note_id;
					$type = 'goods_delivery';

				//check delivery partial or total
					$flag_update = true;
					$type_of_packing_list = 'total';

					if(count($packing_list_detail) > 0){
						$packing_list_partial_or_total = $this->packing_list_partial_or_total($rel_type, $packing_list_detail);
						$flag_update = $packing_list_partial_or_total['flag_update_status'];
						$type_of_packing_list = $packing_list_partial_or_total['type_of_packing_list'];

					//write log on delivery note
						if($type_of_packing_list == 'total'){
							$activity_log = _l('the_package_has_been_successfully_packed');
						}else{
							$activity_log = _l('part_of_the_package_has_been_successfully_packed');
						}

						$delivery_note_log_des = ' <a href="'.admin_url('warehouse/manage_packing_list/' . $rel_id).'">'.$activity_log.'</a> ';
						$this->log_wh_activity($rel_type, 'delivery', $delivery_note_log_des);
					}

					$this->db->where('id', $rel_id);
					$this->db->update(db_prefix() . 'wh_packing_lists', ['type_of_packing_list' => $type_of_packing_list, 'delivery_status' => 'wh_ready_to_deliver']);

					if($flag_update == true){
						if($type_of_packing_list == 'partial'){
							$activity_log = _l('the_package_has_been_successfully_packed');
							$delivery_note_log_des = ' <a href="'.admin_url('warehouse/manage_packing_list/' . $rel_id).'">'.$activity_log.'</a> ';
							$this->log_wh_activity($rel_type, 'delivery', $delivery_note_log_des);
						}
						$this->db->insert(db_prefix().'goods_delivery_invoices_pr_orders', [
							'rel_id' 	=> $rel_id,
							'rel_type' 	=> $rel_type,
							'type' 		=> $type,
						]);
					}
				}
			}
			return false;
			break;

			case '6':
			//order return
			$data_update['approval'] = $status;
			$this->db->where('id', $rel_id);
			$this->db->update(db_prefix() . 'wh_order_returns', $data_update);

			if((int)$status == 1){
			//handle create inventory receipt or inventory delivery
				$order_return = $this->get_order_return($rel_id);
				if($status == 1){
					if( ($order_return->rel_type == 'manual' && $order_return->receipt_delivery_type == 'inventory_delivery_voucher_returned_purchasing_goods') || ($order_return->rel_type == 'i_purchasing_return_order') ){
					// create inventory delivery
					// handle in view

					}elseif(($order_return->rel_type == 'manual' && $order_return->receipt_delivery_type == 'inventory_receipt_voucher_returned_goods') || ($order_return->rel_type == 'i_sales_return_order')){
					// create inventory receipt
					//check warehouse receive return order, if not set => create new warehouse, set default receive return order
						if(!get_option('warehouse_receive_return_order')){
							$warehouse = [];
							$warehouse = [
								'warehouse_code' => 'WH_RECEIVE',
								'warehouse_name' => 'Warehouse receive return order',
								'order' => 10,
								'warehouse_address' => '',
								'city' => '',
								'state' => '',
								'zip_code' => '',
								'country' => '',
								'note' => '',
								'display' => 'on',
							];
							$warehouse_id = $this->warehouse_model->add_one_warehouse($warehouse);
							$this->warehouse_model->update_goods_receipt_warehouse(['input_name' => 'warehouse_receive_return_order', 'input_name_status' => $warehouse_id]);
						}

						$this->order_return_create_stock_import($rel_id);
					}

					hooks()->do_action('after_receiving_or_exporting_return_order_approved', $rel_id);
				}

				if( ($order_return->rel_type == 'manual' && $order_return->receipt_delivery_type == 'inventory_delivery_voucher_returned_purchasing_goods') || ($order_return->rel_type == 'i_purchasing_return_order') ){

					if($order_return->rel_type == 'manual' && $order_return->receipt_delivery_type == 'inventory_delivery_voucher_returned_purchasing_goods'){
						$type = 'order_return_receipt';
					}else{
						$type = 'order_return_purchasing';
					}

					$this->db->insert(db_prefix().'goods_delivery_invoices_pr_orders', [
						'rel_id' 	=> $rel_id,
						'rel_type' 	=> $order_return->rel_id,
						'type' 		=> $type,
					]);

				}elseif(($order_return->rel_type == 'manual' && $order_return->receipt_delivery_type == 'inventory_receipt_voucher_returned_goods') || ($order_return->rel_type == 'i_sales_return_order')){

					if($order_return->rel_type == 'manual' && $order_return->receipt_delivery_type == 'inventory_receipt_voucher_returned_goods'){
						$type = 'order_return_delivery';
					}else{
						$type = 'order_return_sale';
					}

					$this->db->insert(db_prefix().'goods_delivery_invoices_pr_orders', [
						'rel_id' 	=> $rel_id,
						'rel_type' 	=> $order_return->rel_id,
						'type' 		=> $type,
					]);

				}
			}

			return false;
			break;

			default:
			return false;
			break;
		}
	}

	/**
	 * invoice delivery partial or total
	 * @param  [type] $invoice_id            
	 * @param  [type] $goods_delivery_detail 
	 * @return [type]                        
	 */
	public function invoice_delivery_partial_or_total($invoice_id, $goods_delivery_detail)
	{
		$type_of_delivery = 'total';
		$flag_update_status = true;

		/*get item in invoices*/
		$this->db->where('rel_id', $invoice_id);
		$this->db->where('rel_type', 'invoice');
		$this->db->where('qty != wh_delivered_quantity');

		$arr_itemable = $this->db->get(db_prefix().'itemable')->result_array();

		//get item id
		$new_goods_delivery_detail=[];
		$item_name=[];
		$item_id=[];
		foreach ($goods_delivery_detail as $value) {
		    $item_id[] = $value['commodity_code'];

		    if(isset($new_goods_delivery_detail[$value['commodity_code']])){
		    	$new_goods_delivery_detail[$value['commodity_code']][0]['quantities'] += $value['quantities'];
		    }else{
		    	$new_goods_delivery_detail[$value['commodity_code']][] = $value;
		    }
		}

		//get item name from id
		$str_item_id = implode(',', $item_id);
		$str_where ='id IN ('.$str_item_id.')';
		$this->db->where($str_where);
		$items = $this->db->get(db_prefix().'items')->result_array();

		foreach ($items as $item_value) {
		    $item_name[str_replace(' ', '_',$item_value['description'])][] = $item_value;
		}

		foreach ($arr_itemable as $key => $itemable_value) {
			if(isset($item_name[str_replace(' ', '_',$itemable_value['description'])])){
				$first_key = array_key_first($item_name[str_replace(' ', '_',$itemable_value['description'])]);
				
				if(is_numeric($first_key)){
					$itemable_id = $item_name[str_replace(' ', '_',$itemable_value['description'])][$first_key]['id'];
					// unset($item_name[str_replace(' ', '_',$itemable_value['description'])][$first_key]);
					
					if(isset($new_goods_delivery_detail[$itemable_id])){
						$delivery_first_key = array_key_first($new_goods_delivery_detail[$itemable_id]);
						if(is_numeric($delivery_first_key)){
							$check_delivery_qty = $new_goods_delivery_detail[$itemable_id][$delivery_first_key]['quantities'];
							if((float)$check_delivery_qty == ((float)$itemable_value['qty'] - (float)($itemable_value['wh_delivered_quantity'])) ){
								unset($item_name[str_replace(' ', '_',$itemable_value['description'])][$first_key]);
							}
						}
					}
				}else{
					$itemable_id = 0;
				}
			}else{
				$itemable_id = 0;
			}

			// $itemable_id = isset($item_name[$itemable_value['description']]) ?  $item_name[$itemable_value['description']]['id'] : 0;

			if($itemable_id != 0){
				if(isset($new_goods_delivery_detail[$itemable_id])){
					$delivery_first_key = array_key_first($new_goods_delivery_detail[$itemable_id]);
					if(is_numeric($delivery_first_key)){
						$delivery_qty = $new_goods_delivery_detail[$itemable_id][$delivery_first_key]['quantities'];

						if($delivery_qty > (float)$itemable_value['qty'] - (float)($itemable_value['wh_delivered_quantity']) ){
							$delivery_qty = (float)$itemable_value['qty'] - (float)($itemable_value['wh_delivered_quantity']);
						}

						$new_goods_delivery_detail[$itemable_id][$delivery_first_key]['quantities'] -= (float)$delivery_qty;

						
					}else{
						$delivery_qty = 0;
					}
				}else{
					$delivery_qty = 0;
				}

				// $delivery_qty = isset($new_goods_delivery_detail[$itemable_id]) ?  $new_goods_delivery_detail[$itemable_id]['quantities'] : 0;

		    	//check quantity in purchase order detail = wh_quantity_received
				$wh_quantity_received = (float)($itemable_value['wh_delivered_quantity']) + (float)$delivery_qty;
				if((float)$itemable_value['qty'] > $wh_quantity_received){
					$flag_update_status = false;
				}else{
					if((float)$itemable_value['qty'] == $wh_quantity_received){
						// ==
						// if(is_numeric($delivery_first_key)){
						// 	unset($new_goods_delivery_detail[$itemable_id][$delivery_first_key]);
						// }
					}
				}

				if((float)$itemable_value['wh_delivered_quantity'] == 0 && (float)$itemable_value['qty'] == $wh_quantity_received){
					if($type_of_delivery == 'total'){
						$type_of_delivery = 'total';
					}
				}

				$arr_itemable[$key]['wh_delivered_quantity'] = $wh_quantity_received;
			}else{
				$flag_update_status = false;
				$type_of_delivery = 'partial';
			}
		}

		//update wh_delivered_quantity
		if(count($arr_itemable) > 0){
			$this->db->update_batch(db_prefix().'itemable', $arr_itemable, 'id');
		}

		$result_array=[];
		$result_array['flag_update_status'] = $flag_update_status;
		$result_array['type_of_delivery'] = $type_of_delivery;
    	return $result_array;
	}

	/**
	 * stock import pdf
	 * @param  integer $purchase
	 * @return  pdf view
	 */
	function stock_import_pdf($purchase) {
		return app_pdf('purchase', module_dir_path(WAREHOUSE_MODULE_NAME, 'libraries/pdf/Purchase_pdf.php'), $purchase);
	}

	/**
	 * get stock import pdf_html
	 * @param  integer $goods_receipt_id
	 * @return html
	 */
	public function get_stock_import_pdf_html($goods_receipt_id) {
		$this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();

		// get_goods_receipt
		$goods_receipt = $this->get_goods_receipt($goods_receipt_id);
		// get_goods_receipt_detail
		$goods_receipt_detail = $this->get_goods_receipt_detail($goods_receipt_id);
		$company_name = get_option('invoice_company_name');
		$address = get_option('invoice_company_address');

		$tax_data = $this->get_html_tax_receip($goods_receipt_id);

		$day = date('d', strtotime($goods_receipt->date_add));
		$month = date('m', strtotime($goods_receipt->date_add));
		$year = date('Y', strtotime($goods_receipt->date_add));
		$warehouse_lotnumber_bottom_infor_option = get_warehouse_option('goods_delivery_pdf_display_warehouse_lotnumber_bottom_infor');
		$serial_number_html = '';
		$serial_number_index = 1;

		$html = '';
		$html .= '<table class="table">
		<tbody>
		<tr>
		<td rowspan="2" width="50%" class="text-left">'.pdf_logo_url().'</td>
		<td class="text_right_weight "><h3>' . mb_strtoupper(_l('receiving')) . '</h3></td>
		</tr>

		<tr>
		<td class="text_right">#'.$goods_receipt->goods_receipt_code.'</td>
		</tr>
		</tbody>
		</table>
		<br><br><br>
		';	

		//organization_info
		$organization_info = '<div  class="bill_to_color">';
		$organization_info .= format_organization_info();
		$organization_info .= '</div>';

		//get vendor infor

		$customer_name='';
		if (get_status_modules_wh('purchase') && ($goods_receipt->supplier_code != '') && ($goods_receipt->supplier_code != 0) ){
			$this->load->model('purchase/purchase_model');
			if($goods_receipt){
				if(is_numeric($goods_receipt->supplier_code)){

					$supplier_value = $this->purchase_model->get_vendor($goods_receipt->supplier_code);
					if($supplier_value){
						$customer_name .= $supplier_value->company;

						$supplier_value->client = $supplier_value;
						$supplier_value->clientid = '';
					}

				}

			}

			// Bill to
			$bill_to = '<b>' . _l('supplier_name') . '</b>';
			$bill_to .= '<div class="bill_to_color">';
			if(isset($supplier_value)){
				$address = '';
				$vendor_name = '';
				$ship_to = '';

				if($supplier_value){
					$address = $supplier_value->address;
					if($supplier_value->city != ''){
						$address  .= ', '.$supplier_value->city;
					}
					if($supplier_value->state != ''){
						$address  .= ', '.$supplier_value->state;
					}
					
					$vendor_name = $supplier_value->company;
					$ship_to = $supplier_value->shipping_street.'  '.$supplier_value->shipping_city.'  '.$supplier_value->shipping_state;
					if($supplier_value->shipping_street == '' && $supplier_value->shipping_city == '' && $supplier_value->shipping_state == ''){
						$ship_to = $address;
					}
				}

				$bill_to .= '<strong>'.$vendor_name.'</strong><br>';
				$bill_to .= $address;
			}else{
				$bill_to .= wh_get_vendor_company_name($goods_receipt->supplier_code);
			}
			$bill_to .= '</div>';

		}else{
			// Bill to
			$bill_to = '<b>' . _l('supplier_name') . '</b>';
			$bill_to .= '<div class="bill_to_color">';
			$bill_to .= $goods_receipt->supplier_name;
			$bill_to .= '</div>';
		}

		//invoice_data_date
		$invoice_date = '<br /><b>' . _l('invoice_data_date') . ' ' . _d($goods_receipt->date_add) . '</b><br />';

		$html .= '<table class="table">
		<tbody>
		<tr>
		<td rowspan="2" width="50%" class="text-left">'.$organization_info.'</td>
		<td rowspan="2" width="50%" class="text_right">'.$bill_to.'</td>
		</tr>
		</tbody>
		</table>
		<br><br>
		<br><br>
		';

		$html .= '<table class="table">
		<tbody>
		<tr>
		<td rowspan="2" width="50%" class="text-left"></td>
		<td rowspan="2" width="50%" class="text_right">'.$invoice_date.'</td>
		</tr>
		</tbody>
		</table>
		<br><br><br>
		<br><br><br>
		';

		$html .= '<table class="table">
		<tbody>

		<tr>
		<th class="thead-dark-ip">'. _l('_order').'</th>
		<th class="thead-dark-ip">' . _l('commodity_code') . '</th>
		<th class="thead-dark-ip">' . _l('warehouse_name') . '</th>
		<th class="thead-dark-ip">' . _l('unit_name') . '</th>
		<th class="thead-dark-ip">' . _l('quantity') . '</th>
		<th class="thead-dark-ip">' . _l('unit_price') . '</th>
		<th class="thead-dark-ip">' . _l('total_money') . '</th>
		<th class="thead-dark-ip">' . _l('tax_money') . '</th>
		<th class="thead-dark-ip">' . _l('lot_number') . '</th>
		<th class="thead-dark-ip">' . _l('expiry_date') . '</th>

		</tr>';
		foreach ($goods_receipt_detail as $receipt_key => $receipt_value) {

			$commodity_name = (isset($receipt_value) ? $receipt_value['commodity_name'] : '');
			$quantities = (isset($receipt_value) ? $receipt_value['quantities'] : '');
			$unit_price = (isset($receipt_value) ? $receipt_value['unit_price'] : '');
			$goods_money = (isset($receipt_value) ? $receipt_value['goods_money'] : '');

			$commodity_code = get_commodity_name($receipt_value['commodity_code']) != null ? get_commodity_name($receipt_value['commodity_code'])->commodity_code : ''; 

			$commodity_name = get_commodity_name($receipt_value['commodity_code']) != null ? get_commodity_name($receipt_value['commodity_code'])->description : '';

			$unit_name = get_unit_type($receipt_value['unit_id']) != null ? get_unit_type($receipt_value['unit_id'])->unit_name : '';

			$warehouse_code = get_warehouse_name($receipt_value['warehouse_id']) != null ? get_warehouse_name($receipt_value['warehouse_id'])->warehouse_name : '';

			$tax_money =(isset($receipt_value['tax_money']) ? $receipt_value['tax_money'] : '');
			$expiry_date =(isset($receipt_value['expiry_date']) ? $receipt_value['expiry_date'] : '');
			$lot_number =(isset($receipt_value['lot_number']) ? $receipt_value['lot_number'] : '');
			$commodity_name = $receipt_value['commodity_name'];
			if(new_strlen($commodity_name ?? '') == 0){
				$commodity_name = wh_get_item_variatiom($receipt_value['commodity_code']);
			}

			$key = $receipt_key+1;

			$html .= '<tr>';
			$html .= '<td class="td_style_r_ep_c"><b>' . $key . '</b></td>
			<td class="td_style_r_ep_c"><b>' . $commodity_name.'</b></td>
			<td class="td_style_r_ep_c">' . $warehouse_code . '</td>
			<td class="td_style_r_ep_c">' . $unit_name . '</td>
			<td class="td_style_r_ep_c">' . $quantities . '</td>
			<td class="td_style_r_ep_c">' . app_format_money((float) $unit_price, '') . '</td>
			<td class="td_style_r_ep_c">' . app_format_money((float) $goods_money, '') . '</td>
			<td class="td_style_r_ep_c">' . app_format_money((float) $tax_money, '') . '</td>
			<td class="td_style_r_ep_c">' . $lot_number . '</td>
			<td class="td_style_r_ep_c">' . _d($expiry_date) . '</td>
			</tr>';

			if(new_strlen($receipt_value['serial_number'] ?? '') > 0){
				$arr_serial_numbers = new_explode(',', $receipt_value['serial_number']);
				foreach ($arr_serial_numbers as $serial_number_value) {
				    
					$serial_number_html .= '<tr><td width="5%"><b>' . $serial_number_index . '</b></td>
					<td width="30%"><b>' . $commodity_name.'</b></td>
					<td width="20%">' . $warehouse_code . '</td>
					<td width="45%">' . $serial_number_value . '</td></tr>';
				    $serial_number_index++;
				}
			}
		}



		$html .= '</tbody>
		</table>
		<br/>
		';

		//display serial number
		if(new_strlen($serial_number_html ?? '') > 0){
			$html .= '<div>';
			$html .= '<b>'._l('wh_serial_number_list').'</b>';
			$html .= '</div><br/>';

			$html .= '<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
			
			<thead>
			<tr height="40" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . '; ">
			<th width="5%" >'. _l('_order').'</th>
			<th width="30%" >' . _l('commodity_code') . '</th>
			<th width="20%" >' . _l('warehouse_name') . '</th>
			<th width="45%" >' . _l('wh_serial_number') . '</th>
			</tr>
			</thead>';
			$html .= '<tbody class="tbody-main">';
			$html .= $serial_number_html;

			$html .= '</tbody>
			</table>
			<br/>
			';
		}

		$html .= '<div>';
		$html .= '<b>'._l('note_').'</b>';
		$html .= '</div>';
		$html .=  '
		<p>' . $goods_receipt->description . '</p>';


		$html .= '<table class="table">
		<tbody>
		<tr>
		<td ></td>
		<td ></td>
		<td ></td>
		<td class="text_left"><b>' . _l('total_goods_money') . '</b></td>
		<td class="text_right">' .$base_currency->symbol. app_format_money((float) $goods_receipt->total_goods_money, '') . '</td>
		</tr>

		<tr>
		<td ></td>
		<td ></td>
		<td ></td>
		<td class="text_left"><b>' . _l('value_of_inventory') . '</b></td>
		<td class="text_right">' .$base_currency->symbol. app_format_money((float) $goods_receipt->value_of_inventory, '') . '</td>
		</tr>';

		$html .= $tax_data['pdf_html'];

		$html .= '<tr>
		<td ></td>
		<td ></td>
		<td ></td>
		<td class="text_left"><b>' . _l('total_tax_money') . '</b></td>
		<td class="text_right">' .$base_currency->symbol. app_format_money((float) $goods_receipt->total_tax_money, '') . '</td>
		</tr>';

		
		
		$html .= '<tr>
		<td ></td>
		<td ></td>
		<td ></td>
		<td class="text_left"><b>' . _l('total_money') . '</b></td>
		<td class="text_right">' .$base_currency->symbol. app_format_money((float) $goods_receipt->total_money, '') . '</td>
		</tr>
		
		</tbody>
		</table>
		<br><br><br>
		';

		if($warehouse_lotnumber_bottom_infor_option == 1){
			$html .= '<table class="table">
			<tbody>
			<tr>
			<td class="fw_width35"><h4>' . _l('deliver_name') . '</h4></td>
			<td class="fw_width30"><h4>' . _l('stocker') . '</h4></td>
			<td class="fw_width30"><h4>' . _l('chief_accountant') . '</h4></td>

			</tr>
			<tr>
			<td class="fw_width35 fstyle">' . _l('sign_full_name') . '</td>
			<td class="fw_width30 fstyle ">' . _l('sign_full_name') . '</td>
			<td class="fw_width30 fstyle">' . _l('sign_full_name') . '</td>
			</tr

			</tbody>
			</table>';
		}



		$html .= '<br>
		<br>
		<br>
		<br>
		<table class="table">
		<tbody>
		<tr>';
		$html .= '<link href="' . FCPATH.'modules/warehouse/assets/css/pdf_style.css' . '"  rel="stylesheet" type="text/css" />';
		// old link
		// $html .= '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/pdf_style.css') . '"  rel="stylesheet" type="text/css" />';

		return $html;
	}

	/**
	 * send mail
	 * @param  array $data
	 * @return
	 */
	public function send_mail($data ,$staffid = ''){
		if($staffid == ''){
			$staff_id = $staffid;
		}else{
			$staff_id = get_staff_user_id();
		}

		$this->load->model('emails_model');
		if (!isset($data['status'])) {
			$data['status'] = '';
		}
		$get_staff_enter_charge_code = '';
		$mes = 'notify_send_request_approve_project';
		$staff_addedfrom = 0;
		$additional_data = $data['rel_type'];
		$object_type = $data['rel_type'];
		switch ($data['rel_type']) {
		// case '1 : stock_import':
			case '1':
			$type = _l('stock_import');
			$staff_addedfrom = $this->get_goods_receipt($data['rel_id'])->addedfrom;
			$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
			$mes = 'notify_send_request_approve_stock_import';
			$mes_approve = 'notify_send_approve_stock_import';
			$mes_reject = 'notify_send_rejected_stock_import';
			$link = 'warehouse/edit_purchase/' . $data['rel_id'];
			break;
			case '2':
			$type = _l('stock_export');
			$staff_addedfrom = $this->get_goods_delivery($data['rel_id'])->addedfrom;
			$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
			$mes = 'notify_send_request_approve_stock_export';
			$mes_approve = 'notify_send_approve_stock_export';
			$mes_reject = 'notify_send_rejected_stock_export';
			$link = 'warehouse/edit_delivery/' . $data['rel_id'];
			break;
			case '3':
			$type = _l('loss_adjustment');
			$staff_addedfrom = $this->get_loss_adjustment($data['rel_id'])->addfrom;
			$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
			$mes = 'notify_send_request_approve_loss_adjustment';
			$mes_approve = 'notify_send_approve_loss_adjustment';
			$mes_reject = 'notify_send_rejected_loss_adjustment';
			$link = 'warehouse/view_lost_adjustment/' . $data['rel_id'];
			break;

			case '4':
			$type = _l('internal_delivery_note');
			$staff_addedfrom = $this->get_internal_delivery($data['rel_id'])->addedfrom;
			$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
			$mes = 'notify_send_request_approve_internal_delivery_note';
			$mes_approve = 'notify_send_approve_internal_delivery_note';
			$mes_reject = 'notify_send_rejected_internal_delivery_note';
			$link = 'warehouse/manage_internal_delivery/' . $data['rel_id'];
			break;

			case '5':
			$type = _l('wh_packing_list');
			$staff_addedfrom = $this->get_packing_list($data['rel_id'])->staff_id;
			$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
			$mes = 'notify_send_request_approve_packing_list';
			$mes_approve = 'notify_send_approve_packing_list';
			$mes_reject = 'notify_send_rejected_packing_list';
			$link = 'warehouse/manage_packing_list/' . $data['rel_id'];
			break;

			case '6':
			$type = _l('wh_order_return');
			$staff_addedfrom = $this->get_order_return($data['rel_id'])->staff_id;
			$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
			$mes = 'notify_send_request_approve_order_return';
			$mes_approve = 'notify_send_approve_order_return';
			$mes_reject = 'notify_send_rejected_order_return';
			$link = 'warehouse/manage_order_return/' . $data['rel_id'];
			break;
			


			default:

			break;
		}

		$check_approve_status = $this->check_approval_details($data['rel_id'], $data['rel_type'], $data['status']);
		if (isset($check_approve_status['staffid'])) {

			$mail_template = 'send-request-approve';

			if (!in_array(get_staff_user_id(), $check_approve_status['staffid'])) {
				foreach ($check_approve_status['staffid'] as $value) {

					if($value != ''){
					$staff = $this->staff_model->get($value);

					if($staff){
						$notified = add_notification([
							'description' => $mes,
							'touserid' => $staff->staffid,
							'link' => $link,
							'additional_data' => serialize([
								$additional_data,
							]),
						]);
						if ($notified) {
							pusher_trigger_notification([$staff->staffid]);
						}

						//send mail
						
						$this->emails_model->send_simple_email($staff->email, _l('request_approval'), _l('email_send_request_approve', $type) .' <a href="'.admin_url($link).'">'.admin_url($link).'</a> '._l('from_staff', get_staff_full_name($staff_addedfrom)));
					}
				}


				}
			}
		}

		if (isset($data['approve'])) {
			if ($data['approve'] == 1) {
				$mes = $mes_approve;
				$mail_template = 'email_send_approve';
			} else {
				$mes = $mes_reject;
				$mail_template = 'email_send_rejected';
			}

			$staff = $this->staff_model->get($staff_addedfrom);
			$notified = add_notification([
				'description' => $mes,
				'touserid' => $staff->staffid,
				'link' => $link,
				'additional_data' => serialize([
					$additional_data,
				]),
			]);
			if ($notified) {
				pusher_trigger_notification([$staff->staffid]);
			}

			//send mail
			
			$this->emails_model->send_simple_email($staff->email, _l('approval_notification'), _l($mail_template, $type.' <a href="'.admin_url($link).'">'.admin_url($link).'</a> ').' '._l('by_staff', get_staff_full_name(get_staff_user_id())));


			foreach ($list_approve_status as $key => $value) {
				$value['staffid'] = new_explode(', ', $value['staffid']);
				if ($value['approve'] == 1 && !in_array(get_staff_user_id(), $value['staffid'])) {
					foreach ($value['staffid'] as $staffid) {

						$staff = $this->staff_model->get($staffid);
						$notified = add_notification([
							'description' => $mes,
							'touserid' => $staff->staffid,
							'link' => $link,
							'additional_data' => serialize([
								$additional_data,
							]),
						]);
						if ($notified) {
							pusher_trigger_notification([$staff->staffid]);
						}

						//send mail
						$this->emails_model->send_simple_email($staff->email, _l('approval_notification'), _l($mail_template, $type. ' <a href="'.admin_url($link).'">'.admin_url($link).'</a>').' '._l('by_staff', get_staff_full_name($staff_id)));


					}
				}
			}

		}
	}

	/**
	 * create goods delivery code
	 * @return string
	 */
	public function create_goods_delivery_code() {

		$goods_code = get_warehouse_option('inventory_delivery_number_prefix') . (get_warehouse_option('next_inventory_delivery_mumber'));
		
		return $goods_code;
	}

	/**
	 * add goods delivery
	 * @param array  $data
	 * @param boolean $id
	 * return boolean
	 */
	public function add_goods_delivery($data, $id = false) {
		$goods_deliveries = [];
		if (isset($data['newitems'])) {
			$goods_deliveries = $data['newitems'];
			unset($data['newitems']);
		}
		unset($data['item_select']);
		unset($data['commodity_name']);
		unset($data['warehouse_id']);
		unset($data['available_quantity']);
		unset($data['quantities']);
		unset($data['unit_price']);
		unset($data['note']);
		unset($data['unit_name']);
		unset($data['commodity_code']);
		unset($data['unit_id']);
		unset($data['discount']);
		unset($data['guarantee_period']);
		unset($data['tax_rate']);
		unset($data['tax_name']);
		unset($data['discount_money']);
		unset($data['total_after_discount']);
		unset($data['serial_number']);
		unset($data['without_checking_warehouse']);
		if(isset($data['onoffswitch'])){
			if($data['onoffswitch'] == 'on'){
				$switch_barcode_scanners = true;
				unset($data['onoffswitch']);
			}
		}
		$check_appr = $this->get_approve_setting('2');
		$data['approval'] = 0;
		if ($check_appr && $check_appr != false) {
			$data['approval'] = 0;
		} else {
			$data['approval'] = 1;
		}
		if(isset($data['edit_approval'])){
			unset($data['edit_approval']);
		}
		if(isset($data['save_and_send_request'])){
			$save_and_send_request = $data['save_and_send_request'];
			unset($data['save_and_send_request']);
		}
		if (isset($data['hot_purchase'])) {
			$hot_purchase = $data['hot_purchase'];
			unset($data['hot_purchase']);
		}
		$data['goods_delivery_code'] = $this->create_goods_delivery_code();
		if(!$this->check_format_date($data['date_c'])){
			$data['date_c'] = to_sql_date($data['date_c']);
		}else{
			$data['date_c'] = $data['date_c'];
		}
		if(!$this->check_format_date($data['date_add'])){
			$data['date_add'] = to_sql_date($data['date_add']);
		}else{
			$data['date_add'] = $data['date_add'];
		}
		$data['total_money'] 	= reformat_currency_j($data['total_money']);
		$data['total_discount'] = reformat_currency_j($data['total_discount']);
		$data['after_discount'] = reformat_currency_j($data['after_discount']);
		$data['addedfrom'] = get_staff_user_id();
		$data['delivery_status'] = null;
		$this->db->insert(db_prefix() . 'goods_delivery', $data);
		$insert_id = $this->db->insert_id();
		/*update save note*/
		if (isset($insert_id)) {
			foreach ($goods_deliveries as $goods_delivery) {
				$goods_delivery['goods_delivery_id'] = $insert_id;
				$goods_delivery['expiry_date'] = null;
				$goods_delivery['lot_number'] = null;
				$tax_money = 0;
				$tax_rate_value = 0;
				$tax_rate = null;
				$tax_id = null;
				$tax_name = null;
				if(isset($goods_delivery['tax_select'])){
					$tax_rate_data = $this->wh_get_tax_rate($goods_delivery['tax_select']);
					$tax_rate_value = $tax_rate_data['tax_rate'];
					$tax_rate = $tax_rate_data['tax_rate_str'];
					$tax_id = $tax_rate_data['tax_id_str'];
					$tax_name = $tax_rate_data['tax_name_str'];
				}
				if((float)$tax_rate_value != 0){
					$tax_money = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'] * (float)$tax_rate_value / 100;
					$total_money = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'] + (float)$tax_money;
					$amount = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'] + (float)$tax_money;
				}else{
					$total_money = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'];
					$amount = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'];
				}
				$sub_total = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'];
				$goods_delivery['tax_id'] = $tax_id;
				$goods_delivery['total_money'] = $total_money;
				$goods_delivery['tax_rate'] = $tax_rate;
				$goods_delivery['sub_total'] = $sub_total;
				$goods_delivery['tax_name'] = $tax_name;
				unset($goods_delivery['order']);
				unset($goods_delivery['id']);
				unset($goods_delivery['tax_select']);
				unset($goods_delivery['unit_name']);
				if(isset($goods_delivery['without_checking_warehouse'])){
					unset($goods_delivery['without_checking_warehouse']);
				}
				$this->db->insert(db_prefix() . 'goods_delivery_detail', $goods_delivery);
			}

			/*write log*/
			$data_log = [];
			$data_log['rel_id'] = $insert_id;
			$data_log['rel_type'] = 'stock_export';
			$data_log['staffid'] = get_staff_user_id();
			$data_log['date'] = date('Y-m-d H:i:s');
			$data_log['note'] = "stock_export";

			$this->add_activity_log($data_log);

			/*update next number setting*/
			$this->update_inventory_setting(['next_inventory_delivery_mumber' =>  get_warehouse_option('next_inventory_delivery_mumber')+1]);

				//send request approval
			if($save_and_send_request == 'true'){
				/*check send request with type =2 , inventory delivery voucher*/
				$check_r = $this->check_inventory_delivery_voucher(['rel_id' => $insert_id, 'rel_type' => '2']);

				if($check_r['flag_export_warehouse'] == 1){
					$this->send_request_approve(['rel_id' => $insert_id, 'rel_type' => '2', 'addedfrom' => $data['addedfrom']]);

				}
			}


		}

		//approval if not approval setting
		if (isset($insert_id)) {
			if ($data['approval'] == 1) {
				$this->update_approve_request($insert_id, 2, 1);
			}

        	hooks()->do_action('after_wh_goods_delivery_added', $insert_id);

		}

		return $insert_id > 0 ? $insert_id : false;

	}

	/**
	 * commodity goods delivery change
	 * @param  boolean $id
	 * @return  array
	 */
	public function commodity_goods_delivery_change($id = false) {

		if (is_numeric($id)) {
			$commodity_value = $this->db->query('select description, rate, unit_id, taxrate, purchase_price, guarantee, '.db_prefix().'items.tax, ' . db_prefix() . 'taxes.name from ' . db_prefix() . 'items left join ' . db_prefix() . 'ware_unit_type on  ' . db_prefix() . 'items.unit_id = ' . db_prefix() . 'ware_unit_type.unit_type_id
				left join ' . db_prefix() . 'taxes on ' . db_prefix() . 'items.tax = ' . db_prefix() . 'taxes.id where ' . db_prefix() . 'items.id = ' . $id)->row();

			$warehouse_inventory = $this->db->query('SELECT ' . db_prefix() . 'warehouse.warehouse_id as id, CONCAT(' . db_prefix() . 'warehouse.warehouse_code," - ", ' . db_prefix() . 'warehouse.warehouse_name) as label FROM ' . db_prefix() . 'inventory_manage
				LEFT JOIN ' . db_prefix() . 'warehouse on ' . db_prefix() . 'inventory_manage.warehouse_id = ' . db_prefix() . 'warehouse.warehouse_id
				where ' . db_prefix() . 'inventory_manage.commodity_id = ' . $id)->result_array();

		}

		$guarantee_new = '';
		if($commodity_value){
			if(($commodity_value->guarantee != '') && (($commodity_value->guarantee != null)))
				$guarantee_new = date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$commodity_value->guarantee.' months'));
		}

		$data['guarantee_new'] = $guarantee_new;
		$data['commodity_value'] = $commodity_value;
		$data['warehouse_inventory'] = $warehouse_inventory;
		return $data;
	}


	public function get_commodity_delivery_hansometable_by_barcode($commodity_barcode) {

		
		$item_value = $commodity_value = $this->db->query('select description, rate, unit_id, taxrate, purchase_price, guarantee, attributes, '.db_prefix().'items.tax, ' . db_prefix() . 'taxes.name from ' . db_prefix() . 'items left join ' . db_prefix() . 'ware_unit_type on  ' . db_prefix() . 'items.unit_id = ' . db_prefix() . 'ware_unit_type.unit_type_id
				left join ' . db_prefix() . 'taxes on ' . db_prefix() . 'items.tax = ' . db_prefix() . 'taxes.id where ' . db_prefix() . 'items.commodity_barcode = ' . $commodity_barcode)->row();
			$commodity_value =  $this->row_item_to_variation($item_value);

			$warehouse_inventory = $this->db->query('SELECT ' . db_prefix() . 'warehouse.warehouse_id as id, CONCAT(' . db_prefix() . 'warehouse.warehouse_code," - ", ' . db_prefix() . 'warehouse.warehouse_name) as label FROM ' . db_prefix() . 'inventory_manage
				LEFT JOIN ' . db_prefix() . 'warehouse on ' . db_prefix() . 'inventory_manage.warehouse_id = ' . db_prefix() . 'warehouse.warehouse_id
				where ' . db_prefix() . 'inventory_manage.commodity_id = ' . $this->get_commodity_id_from_barcode($commodity_barcode))->result_array();

		

		$guarantee_new = '';
		if($commodity_value){
			if(($commodity_value->guarantee != '') && (($commodity_value->guarantee != null)))
				$guarantee_new = date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$commodity_value->guarantee.' months'));
		}

		$data['guarantee_new'] = $guarantee_new;
		$data['commodity_value'] = $commodity_value;
		$data['warehouse_inventory'] = $warehouse_inventory;
		return $data;
	}

	/**
	 * get goods delivery
	 * @param  integer $id
	 * @return array or object
	 */
	public function get_goods_delivery($id) {
		if (is_numeric($id)) {
			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'goods_delivery')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblgoods_delivery order by id desc')->result_array();
		}
	}

	/**
	 * get goods delivery detail
	 * @param  integer $id
	 * @return array
	 */
	public function get_goods_delivery_detail($id) {
		if (is_numeric($id)) {
			$this->db->where('goods_delivery_id', $id);

			return $this->db->get(db_prefix() . 'goods_delivery_detail')->result_array();
		}
		if ($id == false) {
			return $this->db->query('select * from tblgoods_delivery_detail')->result_array();
		}
	}

	/**
	 * get vendor
	 * @param  string $id
	 * @param  array  $where
	 * @return array or object
	 */
	public function get_vendor($id = '', $where = []) {
		$this->db->select(implode(',', prefixed_table_fields_array(db_prefix() . 'pur_vendor')) . ',' . get_sql_select_vendor_company());

		$this->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'pur_vendor.country', 'left');
		$this->db->join(db_prefix() . 'pur_contacts', '' . db_prefix() . 'pur_contacts.userid = ' . db_prefix() . 'pur_vendor.userid AND is_primary = 1', 'left');

		if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
			$this->db->where($where);
		}

		if (is_numeric($id)) {

			$this->db->where(db_prefix() . 'pur_vendor.userid', $id);
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
	 * get vendor ajax
	 * @param  integer $pur_orders_id
	 * @return object
	 */
	public function get_vendor_ajax($pur_orders_id) {
		$data = [];
		$sql = 'SELECT *, ' . db_prefix() . 'pur_orders.project, ' . db_prefix() . 'pur_orders.type, ' . db_prefix() . 'pur_orders.department, ' . db_prefix() . 'pur_request.requester FROM ' . db_prefix() . 'pur_vendor
		left join ' . db_prefix() . 'pur_orders on ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_orders.vendor
		left join ' . db_prefix() . 'pur_request on ' . db_prefix() . 'pur_orders.pur_request = ' . db_prefix() . 'pur_request.id
		where ' . db_prefix() . 'pur_orders.id = ' . $pur_orders_id;
		$result_array = $this->db->query($sql)->row();



		$data['id'] 		= $result_array->userid;
		$data['buyer'] 		= $result_array->buyer;
		$data['project'] 	= '';
		$data['type']      	= '';
		$data['department'] = '';
		$data['requester'] 	= '';

		if (get_status_modules_wh('purchase')) {
			if(isset($result_array->project)){
				$data['project'] 	.= $result_array->project;
			}
			if(isset($result_array->type)){
				$data['type']      	.= $result_array->type;
			}
			
			if(isset($result_array->department)){
				$data['department'] .= $result_array->department;
			}
			
			if(isset($result_array->requester)){
				$data['requester'] 	.= $result_array->requester;
			}
			
		}

		return $data;

	}

	/**
	 * stock export pdf
	 * @param  integer $delivery
	 * @return pdf view
	 */
	public function stock_export_pdf($delivery) {
		return app_pdf('delivery', module_dir_path(WAREHOUSE_MODULE_NAME, 'libraries/pdf/Delivery_pdf.php'), $delivery);
	}


	/**
	 * get stock export pdf_html
	 * @param  integer $goods_delivery_id
	 * @return string
	 */
	public function get_stock_export_pdf_html($goods_delivery_id) {
		$this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();
		// get_goods_receipt
		$goods_delivery = $this->get_goods_delivery($goods_delivery_id);
		// get_goods_receipt_detail
		$goods_delivery_detail = $this->get_goods_delivery_detail($goods_delivery_id);
		$company_name = get_option('invoice_company_name');
		$address = get_option('invoice_company_address');
		$tax_data = $this->get_html_tax_delivery($goods_delivery_id);


		$day = date('d', strtotime($goods_delivery->date_add));
		$month = date('m', strtotime($goods_delivery->date_add));
		$year = date('Y', strtotime($goods_delivery->date_add));
		$warehouse_lotnumber_bottom_infor_option = get_warehouse_option('goods_delivery_pdf_display_warehouse_lotnumber_bottom_infor');

		$customer_name='';
		if($goods_delivery){
			if(is_numeric($goods_delivery->customer_code)){
				$customer_value = $this->clients_model->get($goods_delivery->customer_code);
				if($customer_value){
					$customer_name .= $customer_value->company;

					$customer_value->client = $customer_value;
					$customer_value->clientid = $customer_value->userid;
				}
			}


		}



		$html = '';

		$html .= '<table class="table">
		<tbody>
		<tr>
		<td rowspan="2" width="50%" class="text-left">'.pdf_logo_url().'</td>
		<td class="text_right_weight "><h3>' . mb_strtoupper(_l('delivery')) . '</h3></td>
		</tr>

		<tr>
		<td class="text_right">#'.$goods_delivery->goods_delivery_code.'</td>
		</tr>
		</tbody>
		</table>
		<br><br><br>
		';

	     //organization_info
		$organization_info = '<div  class="bill_to_color">';
		$organization_info .= format_organization_info();
		$organization_info .= '</div>';

		$bill_to ='';
		$ship_to ='';
		if(isset($customer_value)){
			// Bill to
			$bill_to .= '<b>' . _l('Bill to') . '</b>';
			$bill_to .= '<div class="bill_to_color">';
			$bill_to .= format_customer_info($customer_value, 'invoice', 'billing');
			$bill_to .= '</div>';

			// ship to to
			$ship_to .= '<br /><b>' . _l('ship_to') . '</b>';
			$ship_to .= '<div  class="bill_to_color">';
			$ship_to .= format_customer_info($customer_value, 'invoice', 'shipping');
			$ship_to .= '</div>';
		}

	    //invoice_data_date
		$invoice_date = '<br /><b>' . _l('invoice_data_date') . ' ' . _d($goods_delivery->date_add) . '</b><br />';

		if(is_numeric($goods_delivery->invoice_id) && $goods_delivery->invoice_id != 0){
			$invoice_date .= '<b>'._l('invoice_no').': '.format_invoice_number($goods_delivery->invoice_id).'</b>';
		}

		$html .= '<table class="table">
		<tbody>
		<tr>
		<td rowspan="2" width="50%" class="text-left">'.$organization_info.'</td>
		<td rowspan="2" width="50%" class="text_right">'.$bill_to.'</td>
		</tr>
		</tbody>
		</table>
		<br><br>
		<br><br>
		';

		$html .= '<table class="table">
		<tbody>
		<tr>
		<td rowspan="2" width="50%" class="text-left"></td>
		<td rowspan="2" width="50%" class="text_right">'.$ship_to.'</td>
		</tr>
		</tbody>
		</table>
		<br>
		';
		
		$html .= '<table class="table">
		<tbody>
		<tr>
		<td rowspan="2" width="50%" class="text-left"></td>
		<td rowspan="2" width="50%" class="text_right">'.$invoice_date.'</td>
		</tr>
		</tbody>
		</table>
		<br><br><br>
		<br><br><br>
		';

		$new_goods_delivery_detail = [];
		$new_goods_delivery_serials = [];
		if(!get_option('wh_shortened_form_pdf')){


		$html .= '<table class="table">
		<tbody>

		<tr>
		<th  class=" thead-dark"><b>#</b></th>
		<th  class=" thead-dark">' ._l('commodity_name').'</th>';
		if($warehouse_lotnumber_bottom_infor_option == 1){
			$html .= '<th  class=" thead-dark">' ._l('warehouse_name').'</th>';
		}
		$html .= '<th  class=" thead-dark">' . _l('quantity') . '</th>
		<th  class=" thead-dark">' . _l('delivered') . '</th>';
		if(get_warehouse_option('goods_delivery_pdf_display_outstanding') == 1){
			$html .= '<th  class=" thead-dark">' . _l('outstanding') . '</th>';
		}
		$html .= '<th  class=" thead-dark">' . _l('unit_price') . '</th>';
		if($warehouse_lotnumber_bottom_infor_option == 1){
			$html .= '<th  class=" thead-dark">' ._l('lot_number').'</th>';
		}
		$html .= '<th  class=" thead-dark">' . _l('subtotal') . '</th>
		<th  class=" thead-dark">' . _l('subtotal_after_tax') . '</th>
		<th  class=" thead-dark">' . _l('total_money') . '</th>

		</tr>';
		$subtotal = 0 ;

		foreach ($goods_delivery_detail as $delivery_key => $delivery_value) {

			$item_order = $delivery_key +1;

			$commodity_name = get_commodity_name($delivery_value['commodity_code']) != null ? get_commodity_name($delivery_value['commodity_code'])->description : '';

			$quantities = (isset($delivery_value) ? $delivery_value['quantities'] : '');
			$unit_price = (isset($delivery_value) ? $delivery_value['unit_price'] : '');

			$commodity_code = get_commodity_name($delivery_value['commodity_code']) != null ? get_commodity_name($delivery_value['commodity_code'])->commodity_code : '';

			$total_money = (isset($delivery_value) ? $delivery_value['total_money'] : '');
			$discount = (isset($delivery_value) ? $delivery_value['discount'] : '');
			$discount_money = (isset($delivery_value) ? $delivery_value['discount_money'] : '');
			$guarantee_period = (isset($delivery_value) ? _d($delivery_value['guarantee_period']) : '');

			$total_after_discount = (isset($delivery_value) ? $delivery_value['total_after_discount'] : '');
			$subtotal += (float)$delivery_value['quantities'] * (float)$delivery_value['unit_price'];
			$item_subtotal = (float)$delivery_value['quantities'] * (float)$delivery_value['unit_price'];

			$warehouse_name ='';

			if(isset($delivery_value['warehouse_id']) && ($delivery_value['warehouse_id'] !='')){
				$arr_warehouse = new_explode(',', $delivery_value['warehouse_id']);

				$str = '';
				if(count($arr_warehouse) > 0){

					foreach ($arr_warehouse as $wh_key => $warehouseid) {
						$str = '';
						if ($warehouseid != '' && $warehouseid != '0') {

							$team = get_warehouse_name($warehouseid);
							if($team){
								$value = $team != null ? get_object_vars($team)['warehouse_name'] : '';

								if(new_strlen($str ?? '') > 0){
									$str .= ',<span class="label label-tag tag-id-1"><span class="tag">' . $value . '</span></span>';
								}else{
									$str .= '<span class="label label-tag tag-id-1"><span class="tag">' . $value . '</span></span>';
								}

								$warehouse_name .= $str;
								if($wh_key%3 ==0){
									$warehouse_name .='<br/>';
								}
							}

						}
					}

				} else {
					$warehouse_name = '';
				}
			}


			$unit_name = '';
			if(isset($delivery_value['unit_id']) && ($delivery_value['unit_id'] !='')){
				$unit_name = get_unit_type($delivery_value['unit_id']) != null ? get_unit_type($delivery_value['unit_id'])->unit_name : '';
			}

			$lot_number ='';
			if(($delivery_value['lot_number'] != null) && ( $delivery_value['lot_number'] != '') ){
				$array_lot_number = new_explode(',', $delivery_value['lot_number']);
				foreach ($array_lot_number as $key => $lot_value) {

					if($key%2 ==0){
						$lot_number .= $lot_value;
					}else{
						$lot_number .= ' : '.$lot_value.' ';
					}

				}
			}

			if($delivery_value['commodity_name'] != null && new_strlen($delivery_value['commodity_name'] ?? '') > 0){
				$get_commodity_name = $delivery_value['commodity_name'];
			}else{
				$get_commodity_name = wh_get_item_variatiom($delivery_value['commodity_code']);
			}

			$html .= '<tr>';
			$html .= '<td class=""><b>' . (float)$item_order . '</b></td>
			<td class="td_style_r_ep_c"><b>' . $get_commodity_name . '</b></td>';
			if($warehouse_lotnumber_bottom_infor_option == 1){
				$html .= '<td class="td_style_r_ep_c"><b>' .$warehouse_name. '</b></td>';
			}
			$html .= '<td class="td_style_r_ep_c"><b>' . $quantities .' '.$unit_name. '</b></td>
			<td class="td_style_r_ep_c"><b>' . $quantities .' '.$unit_name. '</b></td>';
			if(get_warehouse_option('goods_delivery_pdf_display_outstanding') == 1){
				$html .= '<td class="td_style_r_ep"><b>0.0</b></td>';
			}

			if(get_warehouse_option('goods_delivery_pdf_display') == 1){
				$html .= ' <td class="td_style_r_ep"><b>' . app_format_money((float) $unit_price, '') . '</b></td>';
				if($warehouse_lotnumber_bottom_infor_option == 1){
					$html .= '<td class="td_style_r_ep_c"><b>' .$lot_number. '</b></td>';
				}
				$html .= '<td class="td_style_r_ep"><b>' . app_format_money((float) $item_subtotal, '') . '</b></td>
				<td class="td_style_r_ep"><b>' . app_format_money((float) $total_money, '') . '</b></td>
				<td class="td_style_r_ep"><b>' . app_format_money((float) $total_after_discount, '') . '</b></td>';

			}else{
				$html .= '<td class="td_style_r_ep"><b></b></td>';
				if($warehouse_lotnumber_bottom_infor_option == 1){
					$html .= '<td class="td_style_r_ep_c"><b>' .$lot_number. '</b></td>';
				}
				$html .= '<td class="td_style_r_ep"><b></b></td>
				<td class="td_style_r_ep"><b></b></td>
				<td class="td_style_r_ep"><b></b></td>';

			}

			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table>
		<br>
		<br>';

	}else{

		foreach ($goods_delivery_detail as $value) {
			if(isset($new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']])){

				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['quantities'] += (float)$value['quantities'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['unit_price'] += (float)$value['unit_price'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['total_money'] += (float)$value['total_money'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['discount'] += (float)$value['discount'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['discount_money'] += (float)$value['discount_money'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['total_after_discount'] += (float)$value['total_after_discount'];

			}else{
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['commodity_code'] = $value['commodity_code'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['quantities'] = (float)$value['quantities'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['unit_price'] = (float)$value['unit_price'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['total_money'] = (float)$value['total_money'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['discount'] = (float)$value['discount'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['discount_money'] = (float)$value['discount_money'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['guarantee_period'] = $value['guarantee_period'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['total_after_discount'] = (float)$value['total_after_discount'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['warehouse_id'] = $value['warehouse_id'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['unit_id'] = $value['unit_id'];
				$new_goods_delivery_detail[$value['commodity_code'].'_'.$value['warehouse_id']]['lot_number'] = $value['lot_number'];
			}

			if(isset($new_goods_delivery_serials[$value['commodity_code'].'_'.$value['warehouse_id']])){

				if(new_strlen($value['lot_number']) > 0){
					$new_goods_delivery_serials[$value['commodity_code'].'_'.$value['warehouse_id']]['serials'] .= '; '.$value['serial_number'].'_'.$value['lot_number'];
				}else{
					$new_goods_delivery_serials[$value['commodity_code'].'_'.$value['warehouse_id']]['serials'] .= '; '.$value['serial_number'];
				}
			}else{
				$new_goods_delivery_serials[$value['commodity_code'].'_'.$value['warehouse_id']]['commodity_code'] = $value['commodity_code'];
				$new_goods_delivery_serials[$value['commodity_code'].'_'.$value['warehouse_id']]['warehouse_id'] = $value['warehouse_id'];

				if(new_strlen($value['lot_number']) > 0){

					$new_goods_delivery_serials[$value['commodity_code'].'_'.$value['warehouse_id']]['serials'] = $value['serial_number'].'_'.$value['lot_number'];
				}else{

					$new_goods_delivery_serials[$value['commodity_code'].'_'.$value['warehouse_id']]['serials'] = $value['serial_number'];
				}
			}
		}



		$html .= '<table class="table">
		<tbody>

		<tr>
		<th  class=" thead-dark"><b>#</b></th>
		<th  class=" thead-dark">' ._l('commodity_name').'</th>';
		if($warehouse_lotnumber_bottom_infor_option == 1){
			$html .= '<th  class=" thead-dark">' ._l('warehouse_name').'</th>';
		}
		$html .= '<th  class=" thead-dark">' . _l('quantity') . '</th>
		<th  class=" thead-dark">' . _l('delivered') . '</th>';
		if(get_warehouse_option('goods_delivery_pdf_display_outstanding') == 1){
			$html .= '<th  class=" thead-dark">' . _l('outstanding') . '</th>';
		}
		$html .= '<th  class=" thead-dark">' . _l('unit_price') . '</th>';
		
		$html .= '<th  class=" thead-dark">' . _l('subtotal') . '</th>
		<th  class=" thead-dark">' . _l('subtotal_after_tax') . '</th>
		<th  class=" thead-dark">' . _l('total_money') . '</th>

		</tr>';
		$subtotal = 0 ;
		$item_order = 0;
		foreach ($new_goods_delivery_detail as $delivery_key => $delivery_value) {

			$item_order = $item_order +1;

			$commodity_name = get_commodity_name($delivery_value['commodity_code']) != null ? get_commodity_name($delivery_value['commodity_code'])->description : '';

			$quantities = (isset($delivery_value) ? $delivery_value['quantities'] : '');
			$unit_price = (isset($delivery_value) ? $delivery_value['unit_price'] : '');

			$commodity_code = get_commodity_name($delivery_value['commodity_code']) != null ? get_commodity_name($delivery_value['commodity_code'])->commodity_code : '';

			$total_money = (isset($delivery_value) ? $delivery_value['total_money'] : '');
			$discount = (isset($delivery_value) ? $delivery_value['discount'] : '');
			$discount_money = (isset($delivery_value) ? $delivery_value['discount_money'] : '');

			$total_after_discount = (isset($delivery_value) ? $delivery_value['total_after_discount'] : '');
			$subtotal += (float)$delivery_value['quantities'] * (float)$delivery_value['unit_price'];
			$item_subtotal = (float)$delivery_value['quantities'] * (float)$delivery_value['unit_price'];

			$warehouse_name ='';

			if(isset($delivery_value['warehouse_id']) && ($delivery_value['warehouse_id'] !='')){
				$arr_warehouse = new_explode(',', $delivery_value['warehouse_id']);

				$str = '';
				if(count($arr_warehouse) > 0){

					foreach ($arr_warehouse as $wh_key => $warehouseid) {
						$str = '';
						if ($warehouseid != '' && $warehouseid != '0') {

							$team = get_warehouse_name($warehouseid);
							if($team){
								$value = $team != null ? get_object_vars($team)['warehouse_name'] : '';

								if(new_strlen($str ?? '') > 0){
									$str .= ',<span class="label label-tag tag-id-1"><span class="tag">' . $value . '</span></span>';
								}else{
									$str .= '<span class="label label-tag tag-id-1"><span class="tag">' . $value . '</span></span>';
								}

								$warehouse_name .= $str;
								if($wh_key%3 ==0){
									$warehouse_name .='<br/>';
								}
							}

						}
					}

				} else {
					$warehouse_name = '';
				}
			}


			$unit_name = '';
			if(isset($delivery_value['unit_id']) && ($delivery_value['unit_id'] !='')){
				$unit_name = get_unit_type($delivery_value['unit_id']) != null ? get_unit_type($delivery_value['unit_id'])->unit_name : '';
			}

			$lot_number ='';
			if(($delivery_value['lot_number'] != null) && ( $delivery_value['lot_number'] != '') ){
				$array_lot_number = new_explode(',', $delivery_value['lot_number']);
				foreach ($array_lot_number as $key => $lot_value) {

					if($key%2 ==0){
						$lot_number .= $lot_value;
					}else{
						$lot_number .= ' : '.$lot_value.' ';
					}

				}
			}

			if($delivery_value['commodity_name'] != null && new_strlen($delivery_value['commodity_name'] ?? '') > 0){
				$get_commodity_name = $delivery_value['commodity_name'];
			}else{
				$get_commodity_name = wh_get_item_variatiom($delivery_value['commodity_code']);
			}

			$html .= '<tr>';
			$html .= '<td class=""><b>' . (float)$item_order . '</b></td>
			<td class="td_style_r_ep_c"><b>' . $get_commodity_name . '</b></td>';
			if($warehouse_lotnumber_bottom_infor_option == 1){
				$html .= '<td class="td_style_r_ep_c"><b>' .$warehouse_name. '</b></td>';
			}
			$html .= '<td class="td_style_r_ep_c"><b>' . $quantities .' '.$unit_name. '</b></td>
			<td class="td_style_r_ep_c"><b>' . $quantities .' '.$unit_name. '</b></td>';
			if(get_warehouse_option('goods_delivery_pdf_display_outstanding') == 1){
				$html .= '<td class="td_style_r_ep"><b>0.0</b></td>';
			}

			if(get_warehouse_option('goods_delivery_pdf_display') == 1){
				$html .= ' <td class="td_style_r_ep"><b>' . app_format_money((float) $unit_price, '') . '</b></td>';
				
				$html .= '<td class="td_style_r_ep"><b>' . app_format_money((float) $item_subtotal, '') . '</b></td>
				<td class="td_style_r_ep"><b>' . app_format_money((float) $total_money, '') . '</b></td>
				<td class="td_style_r_ep"><b>' . app_format_money((float) $total_after_discount, '') . '</b></td>';

			}else{
				$html .= '<td class="td_style_r_ep"><b></b></td>';
				if($warehouse_lotnumber_bottom_infor_option == 1){
					$html .= '<td class="td_style_r_ep_c"><b>' .$lot_number. '</b></td>';
				}
				$html .= '<td class="td_style_r_ep"><b></b></td>
				<td class="td_style_r_ep"><b></b></td>
				<td class="td_style_r_ep"><b></b></td>';

			}

			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table>
		<br>';

		// serial number
		$html .= '<div>';
		$html .= '<b>'._l('wh_serial_number_list').'</b>';
		$html .= '</div><br/>';

		$html .= '<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">

		<thead>
		<tr height="40" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . '; ">
		<th width="20%" align="left">' . _l('commodity_name') . '</th>
		<th width="20%" >' . _l('warehouse_name') . '</th>
		<th width="60%" >' . _l('wh_serial_number') .'_'._l('lot_number'). '</th>
		</tr>
		</thead>';
		$html .= '<tbody class="tbody-main">';
		$serial_number_index = 0;
		foreach ($new_goods_delivery_serials as $delivery_serial) {
			$serial_number_index++;

			$commodity_name = get_commodity_name($delivery_serial['commodity_code']) != null ? get_commodity_name($delivery_serial['commodity_code'])->description : '';
			$warehouse_name ='';
			if(isset($delivery_serial['warehouse_id']) && ($delivery_serial['warehouse_id'] !='')){
				$arr_warehouse = new_explode(',', $delivery_serial['warehouse_id']);

				$str = '';
				if(count($arr_warehouse) > 0){

					foreach ($arr_warehouse as $wh_key => $warehouseid) {
						$str = '';
						if ($warehouseid != '' && $warehouseid != '0') {

							$team = get_warehouse_name($warehouseid);
							if($team){
								$value = $team != null ? get_object_vars($team)['warehouse_name'] : '';

								if(new_strlen($str ?? '') > 0){
									$str .= ',<span class="label label-tag tag-id-1"><span class="tag">' . $value . '</span></span>';
								}else{
									$str .= '<span class="label label-tag tag-id-1"><span class="tag">' . $value . '</span></span>';
								}

								$warehouse_name .= $str;
								if($wh_key%3 ==0){
									$warehouse_name .='<br/>';
								}
							}

						}
					}

				} else {
					$warehouse_name = '';
				}
			}

			$html .= '<tr>
			<td width="20%"><b>' .$serial_number_index.'. '. $commodity_name.'</b></td>
			<td width="20%">' . $warehouse_name . '</td>
			<td width="60%">' . $delivery_serial['serials'] . '</td></tr>';
		}

		$html .= '</tbody>
		</table>
		<br/>
		';


	}

		$after_discount = isset($goods_delivery) ?  $goods_delivery->after_discount : 0 ;
		$shipping_fee = isset($goods_delivery) ?  $goods_delivery->shipping_fee : 0 ;
		if($goods_delivery->after_discount == null){
			$after_discount = $goods_delivery->total_money;
		}
		$total_discount = 0 ;
		if(isset($goods_delivery)){
			$total_discount += (float)$goods_delivery->total_discount  + (float)$goods_delivery->additional_discount;
		}
		if(get_warehouse_option('goods_delivery_pdf_display') == 1){

			$html .= '<table class="table">
			<tbody>
			<tr>
			<td ></td>
			<td ></td>
			<td ></td>
			<td class="text_left"><b>' . _l('subtotal') . '</b></td>
			<td class="text_right">' .$base_currency->symbol. app_format_money((float) $subtotal, '') . '</td>
			</tr>';

			$html .= $tax_data['pdf_html'];
			$html .='<tr><td ></td>
			<td ></td>
			<td ></td>
			<td class="text_left"><b>' . _l('total_discount') . '</b></td>
			<td class="text_right">' .$base_currency->symbol. app_format_money((float) $total_discount, '') . '</td>
			</tr>
			<tr>
			<td ></td>
			<td ></td>
			<td ></td>
			<td class="text_left"><b>' . _l('wh_shipping_fee') . '</b></td>
			<td class="text_right">' .$base_currency->symbol. app_format_money((float) $shipping_fee, '') . '</td>
			</tr>
			<tr>
			<td ></td>
			<td ></td>
			<td ></td>
			<td class="text_left"><b>' . _l('total_money') . '</b></td>
			<td class="text_right">' .$base_currency->symbol. app_format_money((float) $after_discount, '') . '</td>
			</tr>
			</tbody>
			</table>
			<br><br><br>
			';
		}else{
			$html .= '<table class="table">
			<tbody>
			<tr>
			<td ></td>
			<td ></td>
			<td ></td>
			<td class="text_left"><b>' . _l('subtotal') . '</b></td>
			<td class="text_right">......................................</td>
			</tr>
			<tr>
			<td ></td>
			<td ></td>
			<td ></td>
			<td class="text_left"><b>' . _l('total_discount') . '</b></td>
			<td class="text_right">......................................</td>
			</tr>
			<tr>
			<td ></td>
			<td ></td>
			<td ></td>
			<td class="text_left"><b>' . _l('wh_shipping_fee') . '</b></td>
			<td class="text_right">......................................</td>
			</tr>
			<tr>
			<td ></td>
			<td ></td>
			<td ></td>
			<td class="text_left"><b>' . _l('total_money') . '</b></td>
			<td class="text_right">......................................</td>
			</tr>
			
			</tbody>
			</table>
			<br><br><br>
			';
		}

		if($warehouse_lotnumber_bottom_infor_option == 1){
			$html .= '<table class="table">
			<tbody>
			<tr>
			<td class="fw_width35"><h4>' . _l('deliver_name') . '</h4></td>
			<td class="fw_width30"><h4>' . _l('stocker') . '</h4></td>
			<td class="fw_width30"><h4>' . _l('chief_accountant') . '</h4></td>

			</tr>
			<tr>
			<td class="fw_width35 fstyle">' . _l('sign_full_name') . '</td>
			<td class="fw_width30 fstyle ">' . _l('sign_full_name') . '</td>
			<td class="fw_width30 fstyle">' . _l('sign_full_name') . '</td>
			</tr

			</tbody>
			</table>';
		}

		$html .= '

		<br>
		<br>
		<br>
		<br>
		<table class="table">
		<tbody>
		<tr>';


		$html .= '<link href="' . FCPATH.'modules/warehouse/assets/css/pdf_style.css' . '"  rel="stylesheet" type="text/css" />';
		// old link
		// $html .= '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/pdf_style.css') . '"  rel="stylesheet" type="text/css" />';
		return $html;
	}

	//stock summary report for pdf
	/**
	 * get stock summary report
	 * @param  array $data
	 * @return string
	 */
	public function get_stock_summary_report($data, $excel = false) {
		$from_date = $data['from_date'];
		$to_date = $data['to_date'];



		if(!$this->check_format_date($from_date)){
			$from_date = to_sql_date($from_date);
		}
		if(!$this->check_format_date($to_date)){
			$to_date = to_sql_date($to_date);
		}

		$where_warehouse_id = '';

		$where_warehouse_id_with_internal_i = '';
		$where_warehouse_id_with_internal_e = '';

		if (isset($data['warehouse_filter']) && count($data['warehouse_filter']) > 0) {
			$arr_warehouse_id =  $data['warehouse_filter'];

			foreach ($arr_warehouse_id as $warehouse_id) {
				if ($warehouse_id != '') {

					if ($where_warehouse_id == '') {
						$where_warehouse_id .= ' (find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.to_stock_name) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.from_stock_name)';

						$where_warehouse_id_with_internal_i .= ' (find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.to_stock_name)';

						$where_warehouse_id_with_internal_e .= ' (find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.from_stock_name)';

					} else {
						$where_warehouse_id .= ' or find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.to_stock_name) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.from_stock_name)';

						$where_warehouse_id_with_internal_i .= ' or find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.to_stock_name) ';

						$where_warehouse_id_with_internal_e .= ' or find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.from_stock_name) ';

					}

				}
			}


			if ($where_warehouse_id != '') {
				$where_warehouse_id .= ')';

				$where_warehouse_id_with_internal_i .= ')';
				$where_warehouse_id_with_internal_e .= ')';
			}
		}


		$where_commodity_id = '';
		if (isset($data['commodity_filter']) && count($data['commodity_filter']) > 0) {
			$arr_commodity_id = $data['commodity_filter'];

			foreach ($arr_commodity_id as $commodity_id) {
				if ($commodity_id != '') {

					if ($where_commodity_id == '') {
						$where_commodity_id .= ' (find_in_set('.$commodity_id.', '.db_prefix().'goods_transaction_detail.commodity_id) ';
					} else {
						$where_commodity_id .= ' or find_in_set('.$commodity_id.', '.db_prefix().'goods_transaction_detail.commodity_id) ';
					}

				}
			}

			if ($where_commodity_id != '') {
				$where_commodity_id .= ')';
			}
		}

		if($where_commodity_id != ''){
			if($where_warehouse_id != ''){
				$where_warehouse_id .= ' AND '.$where_commodity_id;

				$where_warehouse_id_with_internal_i .= ' AND '.$where_commodity_id;
				$where_warehouse_id_with_internal_e .= ' AND '.$where_commodity_id;
			}else{
				$where_warehouse_id .= $where_commodity_id;

				$where_warehouse_id_with_internal_i .= $where_commodity_id;
				$where_warehouse_id_with_internal_e .= $where_commodity_id;
			}

		}

		//get_commodity_list in warehouse
		if (new_strlen($where_warehouse_id ?? '') > 0) {
			$commodity_lists = $this->db->query('SELECT commodity_id, ' . db_prefix() . 'items.commodity_code, ' . db_prefix() . 'items.rate, ' . db_prefix() . 'items.description as commodity_name, ' . db_prefix() . 'ware_unit_type.unit_name FROM ' . db_prefix() . 'goods_transaction_detail
				LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'goods_transaction_detail.commodity_id = ' . db_prefix() . 'items.id
				LEFT JOIN ' . db_prefix() . 'ware_unit_type ON ' . db_prefix() . 'items.unit_id = ' . db_prefix() . 'ware_unit_type.unit_type_id  where 1=1 AND '.$where_warehouse_id  .' AND '.db_prefix().'items.active = 1 group by commodity_id')->result_array();

		}else{

			$commodity_lists = $this->db->query('SELECT commodity_id, ' . db_prefix() . 'items.commodity_code, ' . db_prefix() . 'items.rate, ' . db_prefix() . 'items.description as commodity_name, ' . db_prefix() . 'ware_unit_type.unit_name FROM ' . db_prefix() . 'goods_transaction_detail
				LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'goods_transaction_detail.commodity_id = ' . db_prefix() . 'items.id
				LEFT JOIN ' . db_prefix() . 'ware_unit_type ON ' . db_prefix() . 'items.unit_id = ' . db_prefix() . 'ware_unit_type.unit_type_id where '.db_prefix().'items.active = 1 group by commodity_id')->result_array();
		}
		//import_openings
		//
		if (new_strlen($where_warehouse_id ?? '') > 0) {
			$import_openings = $this->db->query('SELECT commodity_id, quantity as quantity , purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 1 OR status = 4 OR status = 3) AND date_format(date_add,"%Y-%m-%d") < "' . $from_date . '" AND '.$where_warehouse_id_with_internal_i)->result_array();

		}else{

			$import_openings = $this->db->query('SELECT commodity_id, quantity as quantity , purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 1 OR status = 4 OR status = 3) AND date_format(date_add,"%Y-%m-%d") < "' . $from_date . '" ')->result_array();
		}


		$arr_import_openings = [];
		$arr_import_openings_amount = [];
		foreach ($import_openings as $import_opening_key => $import_opening_value) {
			if(isset($arr_import_openings[$import_opening_value['commodity_id']])){

				switch ($import_opening_value['status']) {
					case '1':
					$arr_import_openings_amount[$import_opening_value['commodity_id']] += (float)$import_opening_value['quantity']*(float)$import_opening_value['purchase_price'];
					$arr_import_openings[$import_opening_value['commodity_id']] 	   += (float)$import_opening_value['quantity'];
					break;
					case '3':
					if(((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity']) > 0){

						$arr_import_openings_amount[$import_opening_value['commodity_id']] += ((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity'])*(float)$import_opening_value['purchase_price'];
						$arr_import_openings[$import_opening_value['commodity_id']] 	   += ((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity']);
					}
					break;
					case '4':
					$arr_import_openings_amount[$import_opening_value['commodity_id']] += (float)$import_opening_value['quantity']*(float)$import_opening_value['purchase_price'];
					$arr_import_openings[$import_opening_value['commodity_id']] 	   += (float)$import_opening_value['quantity'];

					break;
					
				}


			}else{
				switch ($import_opening_value['status']) {
					case '1':
					$arr_import_openings_amount[$import_opening_value['commodity_id']] = (float)$import_opening_value['quantity']*(float)$import_opening_value['purchase_price'];
					$arr_import_openings[$import_opening_value['commodity_id']] 	   = (float)$import_opening_value['quantity'];
					break;
					case '3':
					if(((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity']) > 0){

						$arr_import_openings_amount[$import_opening_value['commodity_id']] = ((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity'])*(float)$import_opening_value['purchase_price'];
						$arr_import_openings[$import_opening_value['commodity_id']] 	   = ((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity']);
					}
					break;
					case '4':
					$arr_import_openings_amount[$import_opening_value['commodity_id']] = (float)$import_opening_value['quantity']*(float)$import_opening_value['purchase_price'];
					$arr_import_openings[$import_opening_value['commodity_id']] 	   = (float)$import_opening_value['quantity'];

					break;
					
				}

			}
		}


		//export_openings
		if (new_strlen($where_warehouse_id) > 0) {
			$export_openings = $this->db->query('SELECT commodity_id, quantity as quantity , purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 2 OR status = 4 OR status = 3 ) AND date_format(date_add,"%Y-%m-%d") < "' . $from_date . '" AND '.$where_warehouse_id_with_internal_e)->result_array();

		}else{

			$export_openings = $this->db->query('SELECT commodity_id, quantity as quantity , purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 2 OR status = 4 OR status = 3 ) AND date_format(date_add,"%Y-%m-%d") < "' . $from_date . '" ')->result_array();
		}

		$arr_export_openings = [];
		$arr_export_openings_amount = [];
		foreach ($export_openings as $export_opening_key => $export_opening_value) {
			//get purchase price of item, before version get sales price.
			
			if($export_opening_value['purchase_price'] != null){
				$purchase_price = $export_opening_value['purchase_price'];
			}else{
				$purchase_price = $this->get_purchase_price_from_commodity_id($export_opening_value['commodity_id']);
			}

			if(isset($arr_export_openings[$export_opening_value['commodity_id']])){
				switch ($export_opening_value['status']) {
					case '2':
					$arr_export_openings_amount[$export_opening_value['commodity_id']] += (float)$export_opening_value['quantity']*(float)$purchase_price;
					$arr_export_openings[$export_opening_value['commodity_id']] 	   += (float)$export_opening_value['quantity'];
					break;
					case '3':
					if(((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity']) < 0){

						$arr_export_openings_amount[$export_opening_value['commodity_id']] += abs((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity'])*(float)$purchase_price;
						$arr_export_openings[$export_opening_value['commodity_id']] 	   += abs((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity']);
					}
					break;
					case '4':
					$arr_export_openings_amount[$export_opening_value['commodity_id']] += (float)$export_opening_value['quantity']*(float)$purchase_price;
					$arr_export_openings[$export_opening_value['commodity_id']] 	   += (float)$export_opening_value['quantity'];

					break;
					
				}

				
			}else{
				switch ($export_opening_value['status']) {
					case '2':
					$arr_export_openings_amount[$export_opening_value['commodity_id']] = (float)$export_opening_value['quantity']*(float)$purchase_price;
					$arr_export_openings[$export_opening_value['commodity_id']] 	   = (float)$export_opening_value['quantity'];
					break;
					case '3':
					if(((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity']) < 0){

						$arr_export_openings_amount[$export_opening_value['commodity_id']] = abs((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity'])*(float)$purchase_price;
						$arr_export_openings[$export_opening_value['commodity_id']] 	   = abs((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity']);
					}
					break;
					case '4':
					$arr_export_openings_amount[$export_opening_value['commodity_id']] = (float)$export_opening_value['quantity']*(float)$purchase_price;
					$arr_export_openings[$export_opening_value['commodity_id']] 	   = (float)$export_opening_value['quantity'];

					break;
					
				}
			}
		}

		//import_periods
		if (new_strlen($where_warehouse_id) > 0) {
			$import_periods = $this->db->query('SELECT commodity_id, quantity as quantity, purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 1 OR status = 4 OR status = 3 ) AND "' . $from_date . '" <= date_format(date_add,"%Y-%m-%d") AND date_format(date_add,"%Y-%m-%d") <= "' . $to_date . '" AND '.$where_warehouse_id_with_internal_i)->result_array();

		}else{

			$import_periods = $this->db->query('SELECT commodity_id, quantity as quantity, purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 1 OR status = 4 OR status = 3) AND "' . $from_date . '" <= date_format(date_add,"%Y-%m-%d") AND date_format(date_add,"%Y-%m-%d") <= "' . $to_date . '" ')->result_array();
		}

		$arr_import_periods = [];
		$arr_import_periods_amount = [];
		foreach ($import_periods as $import_period_key => $import_period_value) {
			if(isset($arr_import_periods[$import_period_value['commodity_id']])){

				switch ($import_period_value['status']) {
					case '1':
					$arr_import_periods_amount[$import_period_value['commodity_id']] += (float)$import_period_value['quantity']*(float)$import_period_value['purchase_price'];
					$arr_import_periods[$import_period_value['commodity_id']] 	   += (float)$import_period_value['quantity'];
					break;
					case '3':
					if(((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity']) > 0){

						$arr_import_periods_amount[$import_period_value['commodity_id']] += ((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity'])*(float)$import_period_value['purchase_price'];
						$arr_import_periods[$import_period_value['commodity_id']] 	   += ((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity']);
					}
					break;
					case '4':
					$arr_import_periods_amount[$import_period_value['commodity_id']] += (float)$import_period_value['quantity']*(float)$import_period_value['purchase_price'];
					$arr_import_periods[$import_period_value['commodity_id']] 	   += (float)$import_period_value['quantity'];

					break;
					
				}


			}else{

				switch ($import_period_value['status']) {
					case '1':
					$arr_import_periods_amount[$import_period_value['commodity_id']] = (float)$import_period_value['quantity']*(float)$import_period_value['purchase_price'];
					$arr_import_periods[$import_period_value['commodity_id']] 	   = (float)$import_period_value['quantity'];
					break;
					case '3':
					if(((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity']) > 0){

						$arr_import_periods_amount[$import_period_value['commodity_id']] = ((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity'])*(float)$import_period_value['purchase_price'];
						$arr_import_periods[$import_period_value['commodity_id']] 	   = ((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity']);
					}
					break;
					case '4':
					$arr_import_periods_amount[$import_period_value['commodity_id']] = (float)$import_period_value['quantity']*(float)$import_period_value['purchase_price'];
					$arr_import_periods[$import_period_value['commodity_id']] 	   = (float)$import_period_value['quantity'];

					break;
					
				}

			}
		}
		

		//export_periods
		if (new_strlen($where_warehouse_id) > 0) {
			$export_periods = $this->db->query('SELECT commodity_id, quantity as quantity, purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 2 OR status = 4 OR status = 3 ) AND "' . $from_date . '" <= date_format(date_add,"%Y-%m-%d") AND date_format(date_add,"%Y-%m-%d") <= "' . $to_date . '" AND'.$where_warehouse_id_with_internal_e)->result_array();

		}else{

			$export_periods = $this->db->query('SELECT commodity_id, quantity as quantity, purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 2 OR status = 4 OR status = 3 ) AND "' . $from_date . '" <= date_format(date_add,"%Y-%m-%d") AND date_format(date_add,"%Y-%m-%d") <= "' . $to_date . '" ')->result_array();
		}

		$arr_export_periods = [];
		$arr_export_periods_amount = [];
		foreach ($export_periods as $export_period_key => $export_period_value) {
			//get purchase price of item, before version get sales price.

			$purchase_price = $export_period_value['purchase_price'];

			if(isset($arr_export_periods[$export_period_value['commodity_id']])){


				switch ($export_period_value['status']) {
					case '2':
					$arr_export_periods_amount[$export_period_value['commodity_id']] += (float)$export_period_value['quantity']*(float)$purchase_price;
					$arr_export_periods[$export_period_value['commodity_id']] 	   += (float)$export_period_value['quantity'];
					break;
					case '3':
					if(((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity']) < 0){

						$arr_export_periods_amount[$export_period_value['commodity_id']] += abs((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity'])*(float)$purchase_price;
						$arr_export_periods[$export_period_value['commodity_id']] 	   += abs((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity']);
					}
					break;
					case '4':
					$arr_export_periods_amount[$export_period_value['commodity_id']] += (float)$export_period_value['quantity']*(float)$purchase_price;
					$arr_export_periods[$export_period_value['commodity_id']] 	   += (float)$export_period_value['quantity'];

					break;
					
				}

				
			}else{
				switch ($export_period_value['status']) {
					case '2':
					$arr_export_periods_amount[$export_period_value['commodity_id']] = (float)$export_period_value['quantity']*(float)$purchase_price;
					$arr_export_periods[$export_period_value['commodity_id']] 	   = (float)$export_period_value['quantity'];
					break;
					case '3':
					if(((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity']) < 0){

						$arr_export_periods_amount[$export_period_value['commodity_id']] = abs((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity'])*(float)$purchase_price;
						$arr_export_periods[$export_period_value['commodity_id']] 	   = abs((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity']);
					}
					break;
					case '4':
					$arr_export_periods_amount[$export_period_value['commodity_id']] = (float)$export_period_value['quantity']*(float)$purchase_price;
					$arr_export_periods[$export_period_value['commodity_id']] 	   = (float)$export_period_value['quantity'];

					break;
					
				}

			}
		}

		//html for page
		$staff_default_language = get_staff_default_language(get_staff_user_id());
		if(is_null($staff_default_language)){
			$staff_default_language = get_option('active_language');
		}

		$from_date_html =  _l('days') . '  ' . date('d', strtotime($from_date)) . '  ' . _l('month') . '  ' . date('m', strtotime($from_date)) . '  ' . _l('year') . '  ' . date('Y', strtotime($from_date)) . '  ';
		$to_date_html = _l('days') . '  ' . date('d', strtotime($to_date)) . '  ' . _l('month') . '  ' . date('m', strtotime($to_date)) . '  ' . _l('year') . '  ' . date('Y', strtotime($to_date)) . '  ';

		if($staff_default_language == 'english'){
			$from_date_html =  date('F', strtotime($from_date)).', '.date('d', strtotime($from_date)).' '. date('Y', strtotime($from_date)) ; 
			$to_date_html = date('F', strtotime($to_date)).', '.date('d', strtotime($to_date)).' '. date('Y', strtotime($to_date)) ; 
		}elseif($staff_default_language == 'french'){
			$from_date_html =  date('d', strtotime($from_date)).' '._l(date('F', strtotime($from_date))).' '. date('Y', strtotime($from_date)) ; 
			$to_date_html = date('d', strtotime($to_date)).' '._l(date('F', strtotime($to_date))).' '. date('Y', strtotime($to_date)) ; 
		}else{
			$from_date_html =  _d($from_date); 
			$to_date_html =  _d($to_date); 
		}

		$html = '';
		$html .= ' <p><h3 class="bold align_cen text-center">' . mb_strtoupper(_l('stock_summary_report')) . '</h3></p>
		<br>
		<div class="col-md-12 pull-right">
		<div class="row">
		<div class="col-md-12 align_cen text-center">
		<p>' . _l('from_date') . ' :  <span class="fstyle">' .$from_date_html. '</p>
		<p>' . _l('to_date') . ' :  <span class="fstyle">' . $to_date_html . '</p>
		</div>
		</div>
		</div>

		<table class="table">';
		$company_name = get_option('invoice_company_name');
		$address = get_option('invoice_company_address');
		$total_opening_quatity = 0;
		$total_opening_amount = 0;
		$total_import_period_quatity = 0;
		$total_import_period_amount = 0;
		$total_export_period_quatity = 0;
		$total_export_period_amount = 0;
		$total_closing_quatity = 0;
		$total_closing_amount = 0;

		$html .= '<tbody>
		<tr>
		<td class="bold width21">' . _l('company_name') . '</td>
		<td>' . $company_name . '</td>
		</tr>
		<tr>
		<td class="bold">' . _l('address') . '</td>
		<td>' . $address . '</td>
		</tr>
		</tbody>
		</table>
		<div class="col-md-12">
		<table class="table table-bordered">
		<tbody>
		<tr>
		<th colspan="1" class="th_style_stk">'._l('_order').'</th>
		<th  colspan="1" class="th_stk10">' . _l('commodity_code') . '</th>
		<th  colspan="1" class="th_stk10">' . _l('commodity_name') . '</th>
		<th  colspan="1" class="th_stk7">' . _l('unit_name') . '</th>
		<th  colspan="2" class="th_stk17">' . _l('opening_stock') . '</th>
		<th  colspan="2" class="th_stk17">' . _l('receipt_in_period') . '</th>
		<th  colspan="2" class="th_stk17">' . _l('issue_in_period') . '</th>
		<th  colspan="2" class="th_r_stk17">' . _l('closing_stock') . '</th>
		</tr>
		<tr>
		<th class="td_w5"></th>
		<th class="td_w10"></th>
		<th class="td_w10"></th>
		<th class="td_stk_w7"></th>
		<th  class="td_stkw5">' . _l('quantity') . '</th>
		<th  class="td_stkw12">' . _l('Amount_') . '</th>
		<th  class="td_stkw5">' . _l('quantity') . '</th>
		<th  class="td_stkw12">' . _l('Amount_') . '</th>
		<th  class="td_stkw5">' . _l('quantity') . '</th>
		<th class="td_stkw12">' . _l('Amount_') . '</th>
		<th  class="td_stkw5">' . _l('quantity') . '</th>
		<th class="td_stkw12s">' . _l('Amount_') . '</th>
		</tr>';

		if($excel){
			$excel_data = [];
			if(isset($arr_import_openings)){
				$excel_data['arr_import_openings'] = $arr_import_openings;
			}
			if(isset($arr_export_openings)){
				$excel_data['arr_export_openings'] = $arr_export_openings;
			}
			if(isset($arr_import_openings_amount)){
				$excel_data['arr_import_openings_amount'] = $arr_import_openings_amount;
			}
			if(isset($arr_export_openings_amount)){
				$excel_data['arr_export_openings_amount'] = $arr_export_openings_amount;
			}
			if(isset($arr_import_periods)){
				$excel_data['arr_import_periods'] = $arr_import_periods;
			}
			if(isset($arr_import_periods_amount)){
				$excel_data['arr_import_periods_amount'] = $arr_import_periods_amount;
			}
			if(isset($arr_export_periods)){
				$excel_data['arr_export_periods'] = $arr_export_periods;
			}
			if(isset($arr_export_periods_amount)){
				$excel_data['arr_export_periods_amount'] = $arr_export_periods_amount;
			}
			$excel_data['commodity_lists'] = $commodity_lists;

			return $excel_data;
		}

		foreach ($commodity_lists as $commodity_list_key => $commodity_list) {
			//get purchase price of item, before version get sales price.
						
			$purchase_price = $this->get_purchase_price_from_commodity_id($commodity_list['commodity_id']);
			$commodity_list_key++;
			$html .= '<tr>
			<td class="border_td">' . $commodity_list_key . '</td>
			<td class="border_td">' . $commodity_list['commodity_code'] . '</td>
			<td class="border_td">' . $commodity_list['commodity_name'] . '</td>
			<td class="border_td">' . $commodity_list['unit_name'] . '</td>';
			//import opening
			$stock_opening_quatity = 0;
			$stock_opening_amount = 0;

			$import_opening_quantity = isset($arr_import_openings[$commodity_list['commodity_id']]) ? $arr_import_openings[$commodity_list['commodity_id']] : 0;

			$export_opening_quantity = isset($arr_export_openings[$commodity_list['commodity_id']]) ? $arr_export_openings[$commodity_list['commodity_id']] : 0;

			$import_opening_amount = isset($arr_import_openings_amount[$commodity_list['commodity_id']]) ? $arr_import_openings_amount[$commodity_list['commodity_id']] : 0;

			$export_opening_amount = isset($arr_export_openings_amount[$commodity_list['commodity_id']]) ? $arr_export_openings_amount[$commodity_list['commodity_id']] : 0;
			

			$stock_opening_quatity = (float)$import_opening_quantity - (float)$export_opening_quantity;
			$stock_opening_amount = (float)$import_opening_amount - (float)$export_opening_amount;
			$total_opening_quatity += $stock_opening_quatity;
			$total_opening_amount += $stock_opening_amount;

			//import period
			$import_period_quatity = 0;
			$import_period_amount = 0;

			$import_period_quantity = isset($arr_import_periods[$commodity_list['commodity_id']]) ? $arr_import_periods[$commodity_list['commodity_id']] : 0;

			$import_period_quatity = $import_period_quantity;
			$import_period_amount = isset($arr_import_periods_amount[$commodity_list['commodity_id']]) ? $arr_import_periods_amount[$commodity_list['commodity_id']] : 0;

			$total_import_period_quatity += $import_period_quatity;
			$total_import_period_amount += $import_period_amount;

			//export period
			$export_period_quatity = 0;
			$export_period_amount = 0;

			$export_period_quantity = isset($arr_export_periods[$commodity_list['commodity_id']]) ? $arr_export_periods[$commodity_list['commodity_id']] : 0;

			$export_period_quatity = $export_period_quantity;
			$export_period_amount = isset($arr_export_periods_amount[$commodity_list['commodity_id']]) ? $arr_export_periods_amount[$commodity_list['commodity_id']] : 0;

			$total_export_period_quatity += $export_period_quatity;
			$total_export_period_amount += $export_period_amount;

			//closing
			$closing_quatity = 0;
			$closing_amount = 0;
			$closing_quatity = $stock_opening_quatity + $import_period_quatity - $export_period_quatity;
			// before get from fomular: $closing_amount = ($stock_opening_amount + $import_period_amount - $export_period_amount) after change below
			$closing_amount = ($stock_opening_amount + $import_period_amount - $export_period_amount);

			$total_closing_quatity += $closing_quatity;
			$total_closing_amount += $closing_amount;

			$html .= '<td class="bor_alir">' . $stock_opening_quatity . '</td>
			<td class="bor_alir">' . app_format_money((float) $stock_opening_amount, '') . '</td>
			<td class="bor_alir">' . $import_period_quatity . '</td>
			<td class="bor_alir">' . app_format_money((float) $import_period_amount, '') . '</td>
			<td class="bor_alir">' . $export_period_quatity . '</td>
			<td class="bor_alir">' . app_format_money((float) $export_period_amount, '') . '</td>
			<td class="bor_alir">' . $closing_quatity . '</td>
			<td class="bor_r">' . app_format_money((float) $closing_amount, '') . '</td>
			</tr>';
		}
		$html .= '<tr>
		<th  colspan="4" class="th_stk_style">' . _l('total') . ' : </th>
		<th  colspan="1" class="th_stk_style">' . $total_opening_quatity . '</th>
		<th  colspan="1" class="th_stk_style">' . app_format_money((float) $total_opening_amount, '') . '</th>
		<th  colspan="1" class="th_stk_style">' . $total_import_period_quatity . '</th>
		<th  colspan="1" class="th_stk_style">' . app_format_money((float) $total_import_period_amount, '') . '</th>
		<th  colspan="1" class="th_stk_style">' . $total_export_period_quatity . '</th>
		<th  colspan="1" class="th_stk_style">' . app_format_money((float) $total_export_period_amount, '') . '</th>
		<th  colspan="1" class="th_stk_style">' . $total_closing_quatity . '</th>
		<th  colspan="1" class="th_st_spe">' . app_format_money((float) $total_closing_amount, '') . '</th>
		</tr>
		</tbody>
		</table>
		</div>';

		$html .= ' 
		<br>
		<br>';

		$html .= '
		<br>
		<br>
		<br>
		<br>';
		$html .= '<link href="' . FCPATH.'modules/warehouse/assets/css/pdf_style.css' . '"  rel="stylesheet" type="text/css" />';
		// old link
		// $html .= '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/pdf_style.css') . '"  rel="stylesheet" type="text/css" />';
		return $html;

	}

	/**
	 * stock summary report pdf
	 * @param  string $stock_report
	 * @return pdf view
	 */
	function stock_summary_report_pdf($stock_report) {
		return app_pdf('stock_summary_report', module_dir_path(WAREHOUSE_MODULE_NAME, 'libraries/pdf/Stock_summary_report_pdf.php'), $stock_report);
	}

	//get stock summary report for view
	/**
	 * get stock summary report view
	 * @param  array $data
	 * @return string
	 */
	public function get_stock_summary_report_view($data) {

		$from_date = $data['from_date'];
		$to_date = $data['to_date'];

		if(!$this->check_format_date($from_date)){
			$from_date = to_sql_date($from_date);
		}
		if(!$this->check_format_date($to_date)){
			$to_date = to_sql_date($to_date);
		}

		$where_warehouse_id = '';

		$where_warehouse_id_with_internal_i = '';
		$where_warehouse_id_with_internal_e = '';
		if (new_strlen($data['warehouse_id']) > 0) {
			$arr_warehouse_id =  new_explode(',', $data['warehouse_id']);

			foreach ($arr_warehouse_id as $warehouse_id) {
				if ($warehouse_id != '') {

					if ($where_warehouse_id == '') {
						$where_warehouse_id .= ' (find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.to_stock_name) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.from_stock_name)';

						$where_warehouse_id_with_internal_i .= ' (find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.to_stock_name)';

						$where_warehouse_id_with_internal_e .= ' (find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.from_stock_name)';
						


					} else {
						$where_warehouse_id .= ' or find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.to_stock_name) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.from_stock_name)';

						$where_warehouse_id_with_internal_i .= ' or find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.to_stock_name) ';

						$where_warehouse_id_with_internal_e .= ' or find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.from_stock_name) ';
						
					}

				}
			}

			if ($where_warehouse_id != '') {
				$where_warehouse_id .= ')';

				$where_warehouse_id_with_internal_i .= ')';
				$where_warehouse_id_with_internal_e .= ')';
			}
		}

		$where_commodity_id = '';
		if (new_strlen($data['commodity_id']) > 0) {
			$arr_commodity_id =  new_explode(',', $data['commodity_id']);

			foreach ($arr_commodity_id as $commodity_id) {
				if ($commodity_id != '') {

					if ($where_commodity_id == '') {
						$where_commodity_id .= ' (find_in_set('.$commodity_id.', '.db_prefix().'goods_transaction_detail.commodity_id) ';
					} else {
						$where_commodity_id .= ' or find_in_set('.$commodity_id.', '.db_prefix().'goods_transaction_detail.commodity_id) ';
					}

				}
			}

			if ($where_commodity_id != '') {
				$where_commodity_id .= ')';
			}
		}

		if($where_commodity_id != ''){
			if($where_warehouse_id != ''){
				$where_warehouse_id .= ' AND '.$where_commodity_id;

				$where_warehouse_id_with_internal_i .= ' AND '.$where_commodity_id;
				$where_warehouse_id_with_internal_e .= ' AND '.$where_commodity_id;
			}else{
				$where_warehouse_id .= $where_commodity_id;

				$where_warehouse_id_with_internal_i .= $where_commodity_id;
				$where_warehouse_id_with_internal_e .= $where_commodity_id;
			}

		}



		//get_commodity_list in warehouse
		if (new_strlen($where_warehouse_id) > 0) {
			$commodity_lists = $this->db->query('SELECT commodity_id, ' . db_prefix() . 'items.commodity_code, ' . db_prefix() . 'items.rate, ' . db_prefix() . 'items.description as commodity_name, ' . db_prefix() . 'ware_unit_type.unit_name FROM ' . db_prefix() . 'goods_transaction_detail
				LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'goods_transaction_detail.commodity_id = ' . db_prefix() . 'items.id
				LEFT JOIN ' . db_prefix() . 'ware_unit_type ON ' . db_prefix() . 'items.unit_id = ' . db_prefix() . 'ware_unit_type.unit_type_id where 1=1 AND  '.$where_warehouse_id. ' AND '.db_prefix().'items.active = 1 group by commodity_id')->result_array();

		}else{

			$commodity_lists = $this->db->query('SELECT commodity_id, ' . db_prefix() . 'items.commodity_code, ' . db_prefix() . 'items.rate, ' . db_prefix() . 'items.description as commodity_name, ' . db_prefix() . 'ware_unit_type.unit_name FROM ' . db_prefix() . 'goods_transaction_detail
				LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'goods_transaction_detail.commodity_id = ' . db_prefix() . 'items.id
				LEFT JOIN ' . db_prefix() . 'ware_unit_type ON ' . db_prefix() . 'items.unit_id = ' . db_prefix() . 'ware_unit_type.unit_type_id where '.db_prefix().'items.active = 1 group by commodity_id')->result_array();
		}

		//import_openings
		// status = 1 inventory receipt voucher, status = 4 internal delivery voucher
		if (new_strlen($where_warehouse_id) > 0) {
			$import_openings = $this->db->query('SELECT commodity_id, quantity as quantity , purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 1 OR status = 4 OR status = 3) AND date_format(date_add,"%Y-%m-%d") < "' . $from_date . '" AND '. $where_warehouse_id_with_internal_i)->result_array();

		}else{

			$import_openings = $this->db->query('SELECT commodity_id, quantity as quantity , purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 1 OR status = 4 OR status = 3) AND  date_format(date_add,"%Y-%m-%d") < "' . $from_date . '" ')->result_array();
		}



		$arr_import_openings = [];
		$arr_import_openings_amount = [];
		foreach ($import_openings as $import_opening_key => $import_opening_value) {
			if(isset($arr_import_openings[$import_opening_value['commodity_id']])){
				switch ($import_opening_value['status']) {
					case '1':
					$arr_import_openings_amount[$import_opening_value['commodity_id']] += (float)$import_opening_value['quantity']*(float)$import_opening_value['purchase_price'];
					$arr_import_openings[$import_opening_value['commodity_id']] 	   += (float)$import_opening_value['quantity'];
					break;
					case '3':
					if(((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity']) > 0){

						$arr_import_openings_amount[$import_opening_value['commodity_id']] += ((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity'])*(float)$import_opening_value['purchase_price'];
						$arr_import_openings[$import_opening_value['commodity_id']] 	   += ((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity']);
					}
					break;
					case '4':
					$arr_import_openings_amount[$import_opening_value['commodity_id']] += (float)$import_opening_value['quantity']*(float)$import_opening_value['purchase_price'];
					$arr_import_openings[$import_opening_value['commodity_id']] 	   += (float)$import_opening_value['quantity'];

					break;
					
				}

				
			}else{
				switch ($import_opening_value['status']) {
					case '1':
					$arr_import_openings_amount[$import_opening_value['commodity_id']] = (float)$import_opening_value['quantity']*(float)$import_opening_value['purchase_price'];
					$arr_import_openings[$import_opening_value['commodity_id']] 	   = (float)$import_opening_value['quantity'];
					break;
					case '3':
					if(((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity']) > 0){

						$arr_import_openings_amount[$import_opening_value['commodity_id']] = ((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity'])*(float)$import_opening_value['purchase_price'];
						$arr_import_openings[$import_opening_value['commodity_id']] 	   = ((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity']);
					}
					break;
					case '4':
					$arr_import_openings_amount[$import_opening_value['commodity_id']] = (float)$import_opening_value['quantity']*(float)$import_opening_value['purchase_price'];
					$arr_import_openings[$import_opening_value['commodity_id']] 	   = (float)$import_opening_value['quantity'];

					break;
					
				}

			}
		}

		//export_openings
		if (new_strlen($where_warehouse_id) > 0) {

			$export_openings = $this->db->query('SELECT commodity_id, quantity as quantity , purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 2 OR status = 4 OR status = 3 ) AND date_format(date_add,"%Y-%m-%d") < "' . $from_date . '" AND '. $where_warehouse_id_with_internal_e)->result_array();

		}else{

			$export_openings = $this->db->query('SELECT commodity_id, quantity as quantity , purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 2 OR status = 4 OR status = 3 ) AND date_format(date_add,"%Y-%m-%d") < "' . $from_date . '" ')->result_array();
		}


		$arr_export_openings = [];
		$arr_export_openings_amount = [];
		foreach ($export_openings as $export_opening_key => $export_opening_value) {
				//get purchase price of item, before version get sales price.
				// $purchase_price = $export_opening_value['price']
				if($export_opening_value['purchase_price'] != null){
					$purchase_price = $export_opening_value['purchase_price'];
				}else{
					$purchase_price = $this->get_purchase_price_from_commodity_id($export_opening_value['commodity_id']);
				}


			if(isset($arr_export_openings[$export_opening_value['commodity_id']])){
				switch ($export_opening_value['status']) {
					case '2':
					$arr_export_openings_amount[$export_opening_value['commodity_id']] += (float)$export_opening_value['quantity']*(float)$purchase_price;
					$arr_export_openings[$export_opening_value['commodity_id']] 	   += (float)$export_opening_value['quantity'];
					break;
					case '3':
					if(((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity']) < 0){

						$arr_export_openings_amount[$export_opening_value['commodity_id']] += abs((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity'])*(float)$purchase_price;
						$arr_export_openings[$export_opening_value['commodity_id']] 	   += abs((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity']);
					}
					break;
					case '4':
					$arr_export_openings_amount[$export_opening_value['commodity_id']] += (float)$export_opening_value['quantity']*(float)$purchase_price;
					$arr_export_openings[$export_opening_value['commodity_id']] 	   += (float)$export_opening_value['quantity'];

					break;
					
				}

				
			}else{
				switch ($export_opening_value['status']) {
					case '2':
					$arr_export_openings_amount[$export_opening_value['commodity_id']] = (float)$export_opening_value['quantity']*(float)$purchase_price;
					$arr_export_openings[$export_opening_value['commodity_id']] 	   = (float)$export_opening_value['quantity'];
					break;
					case '3':
					if(((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity']) < 0){

						$arr_export_openings_amount[$export_opening_value['commodity_id']] = abs((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity'])*(float)$purchase_price;
						$arr_export_openings[$export_opening_value['commodity_id']] 	   = abs((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity']);
					}
					break;
					case '4':
					$arr_export_openings_amount[$export_opening_value['commodity_id']] = (float)$export_opening_value['quantity']*(float)$purchase_price;
					$arr_export_openings[$export_opening_value['commodity_id']] 	   = (float)$export_opening_value['quantity'];

					break;
					
				}

			}
		}




		//import_periods
		if (new_strlen($where_warehouse_id) > 0) {
			$import_periods = $this->db->query('SELECT commodity_id, quantity as quantity, purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 1 OR status = 4 OR status = 3) AND "' . $from_date . '" <= date_format(date_add,"%Y-%m-%d") AND date_format(date_add,"%Y-%m-%d") <= "' . $to_date . '" AND '.$where_warehouse_id_with_internal_i)->result_array();

		}else{

			$import_periods = $this->db->query('SELECT commodity_id, quantity as quantity, purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 1 OR status = 4 OR status =3 ) AND "' . $from_date . '" <= date_format(date_add,"%Y-%m-%d") AND date_format(date_add,"%Y-%m-%d") <= "' . $to_date . '" ')->result_array();
		}


		$arr_import_periods = [];
		$arr_import_periods_amount = [];
		foreach ($import_periods as $import_period_key => $import_period_value) {
			if(isset($arr_import_periods[$import_period_value['commodity_id']])){

				switch ($import_period_value['status']) {
					case '1':
					$arr_import_periods_amount[$import_period_value['commodity_id']] += (float)$import_period_value['quantity']*(float)$import_period_value['purchase_price'];
					$arr_import_periods[$import_period_value['commodity_id']] 	   += (float)$import_period_value['quantity'];
					break;
					case '3':
					if(((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity']) > 0){

						$arr_import_periods_amount[$import_period_value['commodity_id']] += ((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity'])*(float)$import_period_value['purchase_price'];
						$arr_import_periods[$import_period_value['commodity_id']] 	   += ((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity']);
					}
					break;
					case '4':
					$arr_import_periods_amount[$import_period_value['commodity_id']] += (float)$import_period_value['quantity']*(float)$import_period_value['purchase_price'];
					$arr_import_periods[$import_period_value['commodity_id']] 	   += (float)$import_period_value['quantity'];

					break;
					
				}


			}else{
				switch ($import_period_value['status']) {
					case '1':
					$arr_import_periods_amount[$import_period_value['commodity_id']] = (float)$import_period_value['quantity']*(float)$import_period_value['purchase_price'];
					$arr_import_periods[$import_period_value['commodity_id']] 	   = (float)$import_period_value['quantity'];
					break;
					case '3':
					if(((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity']) > 0){

						$arr_import_periods_amount[$import_period_value['commodity_id']] = ((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity'])*(float)$import_period_value['purchase_price'];
						$arr_import_periods[$import_period_value['commodity_id']] 	   = ((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity']);
					}
					break;
					case '4':
					$arr_import_periods_amount[$import_period_value['commodity_id']] = (float)$import_period_value['quantity']*(float)$import_period_value['purchase_price'];
					$arr_import_periods[$import_period_value['commodity_id']] 	   = (float)$import_period_value['quantity'];

					break;
					
				}


			}
		}

		//export_periods
		//
		if (new_strlen($where_warehouse_id) > 0) {
			$export_periods = $this->db->query('SELECT commodity_id, quantity as quantity, purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 2 OR status = 4 OR status = 3) AND "' . $from_date . '" <= date_format(date_add,"%Y-%m-%d") AND date_format(date_add,"%Y-%m-%d") <= "' . $to_date . '" AND '.$where_warehouse_id_with_internal_e)->result_array();

		}else{

			$export_periods = $this->db->query('SELECT commodity_id, quantity as quantity, purchase_price, price, status, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 2 OR status = 4 OR status = 3 ) AND "' . $from_date . '" <= date_format(date_add,"%Y-%m-%d") AND date_format(date_add,"%Y-%m-%d") <= "' . $to_date . '" ')->result_array();
		}

		$arr_export_periods = [];
		$arr_export_periods_amount = [];
		foreach ($export_periods as $export_period_key => $export_period_value) {
				//get purchase price of item, before version get sales price.
				$purchase_price = $export_period_value['purchase_price'];
				
				// $purchase_price = $this->get_purchase_price_from_commodity_id($export_period_value['commodity_id']);

			if(isset($arr_export_periods[$export_period_value['commodity_id']])){

				switch ($export_period_value['status']) {
					case '2':
					$arr_export_periods_amount[$export_period_value['commodity_id']] += (float)$export_period_value['quantity']*(float)$purchase_price;
					$arr_export_periods[$export_period_value['commodity_id']] 	   += (float)$export_period_value['quantity'];
					break;
					case '3':
					if(((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity']) < 0){

						$arr_export_periods_amount[$export_period_value['commodity_id']] += abs((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity'])*(float)$purchase_price;
						$arr_export_periods[$export_period_value['commodity_id']] 	   += abs((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity']);
					}
					break;
					case '4':
					$arr_export_periods_amount[$export_period_value['commodity_id']] += (float)$export_period_value['quantity']*(float)$purchase_price;
					$arr_export_periods[$export_period_value['commodity_id']] 	   += (float)$export_period_value['quantity'];

					break;
					
				}


				
			}else{

				switch ($export_period_value['status']) {
					case '2':
					$arr_export_periods_amount[$export_period_value['commodity_id']] = (float)$export_period_value['quantity']*(float)$purchase_price;
					$arr_export_periods[$export_period_value['commodity_id']] 	   = (float)$export_period_value['quantity'];
					break;
					case '3':
					if(((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity']) < 0){

						$arr_export_periods_amount[$export_period_value['commodity_id']] = abs((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity'])*(float)$purchase_price;
						$arr_export_periods[$export_period_value['commodity_id']] 	   = abs((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity']);
					}
					break;
					case '4':
					$arr_export_periods_amount[$export_period_value['commodity_id']] = (float)$export_period_value['quantity']*(float)$purchase_price;
					$arr_export_periods[$export_period_value['commodity_id']] 	   = (float)$export_period_value['quantity'];

					break;
					
				}

				
			}
		}

		//html for page
		$staff_default_language = get_staff_default_language(get_staff_user_id());
		if(is_null($staff_default_language)){
			$staff_default_language = get_option('active_language');
		}

		$from_date_html =  _l('days') . '  ' . date('d', strtotime($from_date)) . '  ' . _l('month') . '  ' . date('m', strtotime($from_date)) . '  ' . _l('year') . '  ' . date('Y', strtotime($from_date)) . '  ';
		$to_date_html = _l('days') . '  ' . date('d', strtotime($to_date)) . '  ' . _l('month') . '  ' . date('m', strtotime($to_date)) . '  ' . _l('year') . '  ' . date('Y', strtotime($to_date)) . '  ';

		if($staff_default_language == 'english'){
			$from_date_html =  date('F', strtotime($from_date)).', '.date('d', strtotime($from_date)).' '. date('Y', strtotime($from_date)) ; 
			$to_date_html = date('F', strtotime($to_date)).', '.date('d', strtotime($to_date)).' '. date('Y', strtotime($to_date)) ; 
		}elseif($staff_default_language == 'french'){
			$from_date_html =  date('d', strtotime($from_date)).' '._l(date('F', strtotime($from_date))).' '. date('Y', strtotime($from_date)) ; 
			$to_date_html = date('d', strtotime($to_date)).' '._l(date('F', strtotime($to_date))).' '. date('Y', strtotime($to_date)) ; 
		}else{
			$from_date_html =  _d($from_date); 
			$to_date_html =  _d($to_date); 
		}

		$html = '';
		$html .= ' <p><h3 class="bold align_cen text-center">' . mb_strtoupper(_l('stock_summary_report')) . '</h3></p>
		<br>
		<div class="col-md-12 pull-right">
		<div class="row">
		<div class="col-md-12 align_cen text-center">
		<p>' . _l('from_date') . ' :  <span class="fstyle">' . $from_date_html. '</p>
		<p>' . _l('to_date') . ' :  <span class="fstyle">' . $to_date_html . '</p>
		</div>
		</div>
		</div>

		<table class="table">';
		$company_name = get_option('invoice_company_name');
		$address = get_option('invoice_company_address');
		$total_opening_quatity = 0;
		$total_opening_amount = 0;
		$total_import_period_quatity = 0;
		$total_import_period_amount = 0;
		$total_export_period_quatity = 0;
		$total_export_period_amount = 0;
		$total_closing_quatity = 0;
		$total_closing_amount = 0;

		$html .= '<tbody>
		<tr>
		<td class="bold width21">' . _l('company_name') . '</td>
		<td>' . $company_name . '</td>
		</tr>
		<tr>
		<td class="bold">' . _l('address') . '</td>
		<td>' . $address . '</td>
		</tr>
		</tbody>
		</table>
		<div class="col-md-12">
		<table class="table table-bordered">
		<tbody>
		<tr>
		<th colspan="1" class="th_style_stk">'._l('_order').'</th>
		<th  colspan="1" class="th_stk10">' . _l('commodity_code') . '</th>
		<th  colspan="1" class="th_stk10">' . _l('commodity_name') . '</th>
		<th  colspan="1" class="th_stk7">' . _l('wh_unit_name') . '</th>
		<th  colspan="2" class="th_stk17">' . _l('opening_stock') . '</th>
		<th  colspan="2" class="th_stk17">' . _l('receipt_in_period') . '</th>
		<th  colspan="2" class="th_stk17">' . _l('issue_in_period') . '</th>
		<th  colspan="2" class="th_r_stk17">' . _l('closing_stock') . '</th>
		</tr>
		<tr>
		<th class="td_w5"></th>
		<th class="td_w10"></th>
		<th class="td_w10"></th>
		<th class="td_stk_w7"></th>
		<th  class="td_stkw5">' . _l('quantity') . '</th>
		<th  class="td_stkw12">' . _l('Amount_') . '</th>
		<th  class="td_stkw5">' . _l('quantity') . '</th>
		<th  class="td_stkw12">' . _l('Amount_') . '</th>
		<th  class="td_stkw5">' . _l('quantity') . '</th>
		<th class="td_stkw12">' . _l('Amount_') . '</th>
		<th  class="td_stkw5">' . _l('quantity') . '</th>
		<th class="td_stkw12s">' . _l('Amount_') . '</th>
		</tr>';
		foreach ($commodity_lists as $commodity_list_key => $commodity_list) {
			//get purchase price of item, before version get sales price.
						
			$purchase_price = $this->get_purchase_price_from_commodity_id($commodity_list['commodity_id']);
			$commodity_list_key++;
			$html .= '<tr>
			<td class="border_td">' . $commodity_list_key . '</td>
			<td class="border_td">' . $commodity_list['commodity_code'] . '</td>
			<td class="border_td">' . $commodity_list['commodity_name'] . '</td>
			<td class="border_td">' . $commodity_list['unit_name'] . '</td>';
			//import opening
			$stock_opening_quatity = 0;
			$stock_opening_amount = 0;

			$import_opening_quantity = isset($arr_import_openings[$commodity_list['commodity_id']]) ? $arr_import_openings[$commodity_list['commodity_id']] : 0;

			$export_opening_quantity = isset($arr_export_openings[$commodity_list['commodity_id']]) ? $arr_export_openings[$commodity_list['commodity_id']] : 0;

			$import_opening_amount = isset($arr_import_openings_amount[$commodity_list['commodity_id']]) ? $arr_import_openings_amount[$commodity_list['commodity_id']] : 0;

			$export_opening_amount = isset($arr_export_openings_amount[$commodity_list['commodity_id']]) ? $arr_export_openings_amount[$commodity_list['commodity_id']] : 0;
			

			$stock_opening_quatity = (float)$import_opening_quantity - (float)$export_opening_quantity;
			// $stock_opening_amount = (float)$import_opening_amount - (float)$export_opening_amount;
			$stock_opening_amount = (float)$import_opening_amount - (float)$export_opening_amount;
			$total_opening_quatity += $stock_opening_quatity;
			$total_opening_amount += $stock_opening_amount;

			//import period
			$import_period_quatity = 0;
			$import_period_amount = 0;

			$import_period_quantity = isset($arr_import_periods[$commodity_list['commodity_id']]) ? $arr_import_periods[$commodity_list['commodity_id']] : 0;

			$import_period_quatity = $import_period_quantity;
			$import_period_amount = isset($arr_import_periods_amount[$commodity_list['commodity_id']]) ? $arr_import_periods_amount[$commodity_list['commodity_id']] : 0;

			$total_import_period_quatity += $import_period_quatity;
			$total_import_period_amount += $import_period_amount;

			//export period
			$export_period_quatity = 0;
			$export_period_amount = 0;

			$export_period_quantity = isset($arr_export_periods[$commodity_list['commodity_id']]) ? $arr_export_periods[$commodity_list['commodity_id']] : 0;

			$export_period_quatity = $export_period_quantity;
			$export_period_amount = isset($arr_export_periods_amount[$commodity_list['commodity_id']]) ? $arr_export_periods_amount[$commodity_list['commodity_id']] : 0;

			$total_export_period_quatity += $export_period_quatity;
			$total_export_period_amount += $export_period_amount;

			//closing
			$closing_quatity = 0;
			$closing_amount = 0;
			$closing_quatity = $stock_opening_quatity + $import_period_quatity - $export_period_quatity;
			// before get from fomular: $closing_amount = ($stock_opening_amount + $import_period_amount - $export_period_amount) after change below
			
			$closing_amount = ($stock_opening_amount + $import_period_amount - $export_period_amount);

			$total_closing_quatity += $closing_quatity;
			$total_closing_amount += $closing_amount;

			$html .= '<td class="bor_alir">' . $stock_opening_quatity . '</td>
			<td class="bor_alir">' . app_format_money((float) $stock_opening_amount, '') . '</td>
			<td class="bor_alir">' . $import_period_quatity . '</td>
			<td class="bor_alir">' . app_format_money((float) $import_period_amount, '') . '</td>
			<td class="bor_alir">' . $export_period_quatity . '</td>
			<td class="bor_alir">' . app_format_money((float) $export_period_amount, '') . '</td>
			<td class="bor_alir">' . $closing_quatity . '</td>
			<td class="bor_r">' . app_format_money((float) $closing_amount, '') . '</td>
			</tr>';
		}
		$html .= '<tr>
		<th  colspan="4" class="th_stk_style">' . _l('total') . ' : </th>
		<th  colspan="1" class="th_stk_style">' . $total_opening_quatity . '</th>
		<th  colspan="1" class="th_stk_style">' . app_format_money((float) $total_opening_amount, '') . '</th>
		<th  colspan="1" class="th_stk_style">' . $total_import_period_quatity . '</th>
		<th  colspan="1" class="th_stk_style">' . app_format_money((float) $total_import_period_amount, '') . '</th>
		<th  colspan="1" class="th_stk_style">' . $total_export_period_quatity . '</th>
		<th  colspan="1" class="th_stk_style">' . app_format_money((float) $total_export_period_amount, '') . '</th>
		<th  colspan="1" class="th_stk_style">' . $total_closing_quatity . '</th>
		<th  colspan="1" class="th_st_spe">' . app_format_money((float) $total_closing_amount, '') . '</th>
		</tr>
		</tbody>
		</table>
		</div>';

		$html .= ' 
		<br>
		<br>';

		$html .= '
		<br>
		<br>
		<br>
		<br>';

		$html .= '<link href="' . FCPATH.'modules/warehouse/assets/css/pdf_style.css' . '"  rel="stylesheet" type="text/css" />';
		// old link
		// $html .= '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/pdf_style.css') . '"  rel="stylesheet" type="text/css" />';

		return $html;

	}

	/**
	 * get quantity inventory
	 * @param  integer $warehouse_id
	 * @param  integer $commodity_id
	 * @return object
	 */
	public function get_quantity_inventory($warehouse_id, $commodity_id) {

		$sql = 'SELECT warehouse_id, commodity_id, sum(inventory_number) as inventory_number from ' . db_prefix() . 'inventory_manage where warehouse_id = ' . $warehouse_id . ' AND commodity_id = ' . $commodity_id .' group by warehouse_id, commodity_id';
		$result = $this->db->query($sql)->row();
		//if > 0 update, else insert
		return $result;

	}

	/**
	 * get warehourse attachments
	 * @param  integer $commodity_id
	 * @return array
	 */
	public function get_warehourse_attachments($commodity_id) {

		$this->db->order_by('dateadded', 'desc');
		$this->db->where('rel_id', $commodity_id);
		$this->db->where('rel_type', 'commodity_item_file');

		return $this->db->get(db_prefix() . 'files')->result_array();

	}

	/**
	 * add commodity one item
	 * @param array $data
	 * @return integer
	 */
	public function add_commodity_one_item($data) {
		$arr_insert_cf=[];
		$arr_variation=[];
		$arr_attributes=[];
		/*get custom fields*/
		if(isset($data['formdata'])){
			$arr_custom_fields=[];

			$arr_variation_temp=[];
			$variation_name_temp='';
			$variation_option_temp='';

			foreach ($data['formdata'] as $value_cf) {
				if(preg_match('/^custom_fields/', $value_cf['name'])){
					if(strpos($value_cf['name'], '[]')){
						$index =  new_str_replace('custom_fields[items][', '', $value_cf['name']);
						$index =  new_str_replace('][]', '', $index);

						$arr_custom_fields[$index][] = $value_cf['value'];

					}else{
						$index =  new_str_replace('custom_fields[items][', '', $value_cf['name']);
						$index =  new_str_replace(']', '', $index);

						$arr_custom_fields[$index] = $value_cf['value'];
					}
				}

				//get variation (parent attribute)
				
				if(preg_match('/^name/', $value_cf['name'])){
					$variation_name_temp = $value_cf['value'];
				}

				if(preg_match('/^options/', $value_cf['name'])){
					$variation_option_temp = $value_cf['value'];

					array_push($arr_variation, [
						'name' => $variation_name_temp,
						'options' => new_explode(',', $variation_option_temp),
					]);

					$variation_name_temp='';
					$variation_option_temp='';
				}

				//get attribute
				if(preg_match("/^variation_names_/", $value_cf['name'])){
					array_push($arr_attributes, [
						'name' => new_str_replace('variation_names_', '', $value_cf['name']),
						'option' => $value_cf['value'],
					]);
				}

			}

			$arr_insert_cf['items_pr'] = $arr_custom_fields;

			$formdata = $data['formdata'];
			unset($data['formdata']);
		}

		
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

		if (isset($data['custom_fields'])) {
			$custom_fields = $data['custom_fields'];
			unset($data['custom_fields']);
		}

		/*add data tblitem*/
		$data['rate'] = $data['rate'];

		if(isset($data['purchase_price']) && $data['purchase_price']){
			
			$data['purchase_price'] = $data['purchase_price'];
		}
		/*create sku code*/
		if($data['sku_code'] != ''){
			
			$data['sku_code'] = get_warehouse_option('item_sku_prefix').new_str_replace(' ', '', $data['sku_code']) ;
			$sku_code_none_prefix = new_str_replace(' ', '', $data['sku_code']) ;

		}else{
			//data sku_code = group_character.sub_code.commodity_str_betwen.next_commodity_id; // X_X_000.id auto increment
			$data['sku_code'] = get_warehouse_option('item_sku_prefix').$this->create_sku_code($data['group_id'], isset($data['sub_group']) ? $data['sub_group'] : '' );
			$sku_code_none_prefix = $this->create_sku_code($data['group_id'], isset($data['sub_group']) ? $data['sub_group'] : '' );
			/*create sku code*/
		}

		$data['sku_code'] = strtoupper(new_str_replace(" ", "", $data['sku_code']));

		// if(get_warehouse_option('barcode_with_sku_code') == 1){
		// 	$data['commodity_barcode'] = $sku_code_none_prefix;
		// }

		$tags = '';
		if (isset($data['tags'])) {
			$tags = $data['tags'];
			unset($data['tags']);
		}

		//update column unit name use sales/items
		$unit_type = get_unit_type($data['unit_id']);
		if(isset($unit_type->unit_name)){
			$data['unit'] = $unit_type->unit_name;
		}

		$this->db->insert(db_prefix() . 'items', $data);
		$insert_id = $this->db->insert_id();

		/*add data tblinventory*/
		if ($insert_id) {
			$data_inventory_min['commodity_id'] = $insert_id;
			$data_inventory_min['commodity_code'] = $data['commodity_code'];
			$data_inventory_min['commodity_name'] = $data['description'];
			$this->add_inventory_min($data_inventory_min);

			/*habdle add tags*/
			handle_tags_save($tags, $insert_id, 'item_tags');


			/*handle custom fields*/

			if(isset($formdata)){
				$data_insert_cf = [];

				handle_custom_fields_post($insert_id, $arr_insert_cf, true);
			}


			//create variant
			$add_variant=false;
			if(count($arr_variation) > 0 &&  new_strlen(json_encode($arr_variation)) > 28){
				$response_create_variant = $this->create_variant_product($insert_id, $data, $arr_variation);

				if($response_create_variant){
					$add_variant = true;
				}
			}

			hooks()->do_action('item_created', $insert_id);

			log_activity('New Warehouse Item Added [ID:' . $insert_id . ', ' . $data['description'] . ']');

			return ['insert_id' => $insert_id, 'add_variant' => $add_variant];

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
        	$barcode_gen = $this->generate_commodity_barcode();

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
	 * update commodity one item
	 * @param  array $data
	 * @param  integer $id
	 * @return boolean
	 */
	public function update_commodity_one_item($data, $id) {
		if (isset($data['custom_fields'])) {
			$custom_fields = $data['custom_fields'];
			unset($data['custom_fields']);
		}

		$arr_insert_cf=[];
		$arr_variation=[];
		$arr_attributes=[];
		/*get custom fields, get variation value*/
		if(isset($data['formdata'])){
			$arr_custom_fields=[];

			$arr_variation_temp=[];
			$variation_name_temp='';
			$variation_option_temp='';
			foreach ($data['formdata'] as $value_cf) {
				if(preg_match('/^custom_fields/', $value_cf['name'])){
					if(strpos($value_cf['name'], '[]')){
						$index =  new_str_replace('custom_fields[items][', '', $value_cf['name']);
						$index =  new_str_replace('][]', '', $index);

						$arr_custom_fields[$index][] = $value_cf['value'];

					}else{
						$index =  new_str_replace('custom_fields[items][', '', $value_cf['name']);
						$index =  new_str_replace(']', '', $index);

						$arr_custom_fields[$index] = $value_cf['value'];
					}
				}

				//get variation (parent attribute)
				if(preg_match('/^name/', $value_cf['name'])){
					$variation_name_temp = $value_cf['value'];
				}

				if(preg_match('/^options/', $value_cf['name'])){
					$variation_option_temp = $value_cf['value'];

					array_push($arr_variation, [
						'name' => $variation_name_temp,
						'options' => new_explode(',', $variation_option_temp),
					]);

					$variation_name_temp='';
					$variation_option_temp='';
				}

				//get attribute
				if(preg_match("/^variation_names_/", $value_cf['name'])){
					array_push($arr_attributes, [
						'name' => new_str_replace('variation_names_', '', $value_cf['name']),
						'option' => $value_cf['value'],
					]);
				}

			}

			$arr_insert_cf['items'] = $arr_custom_fields;

			$formdata = $data['formdata'];
			unset($data['formdata']);
		}
		

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

		if(isset($formdata)){
			$data_insert_cf = [];
			handle_custom_fields_post($id, $arr_insert_cf, true);
		}

		/*handle update item tag*/

		if(new_strlen($data['tags']) > 0){

			$this->db->where('rel_id', $id);
			$this->db->where('rel_type', 'item_tags');
			$arr_tag = $this->db->get(db_prefix() . 'taggables')->result_array();

			if(count($arr_tag) > 0){
	        	//update
				$arr_tag_insert =  new_explode(',', $data['tags']);

				$total_tag = count($arr_tag);
				$tag_order_last = $arr_tag[$total_tag-1]['tag_order']+1;

				foreach ($arr_tag_insert as $value) {
					$this->db->insert(db_prefix() . 'tags', ['name' => $value]);
					$insert_tag_id = $this->db->insert_id();

					if($insert_tag_id){
						$this->db->insert(db_prefix() . 'taggables', ['rel_id' => $id, 'rel_type'=>'item_tags', 'tag_id' => $insert_tag_id, 'tag_order' => $tag_order_last]);
						$this->db->insert_id();

						$tag_order_last++;
					}

				}

			}else{
	        	//insert
				handle_tags_save($data['tags'], $id, 'item_tags');

			}
		}

		if (isset($data['tags'])) {
			unset($data['tags']);
		}

		$data['sku_code'] = strtoupper(new_str_replace(" ", "", $data['sku_code']));

		// if(get_warehouse_option('barcode_with_sku_code') == 1){
		// 	$data['commodity_barcode'] = $data['sku_code'];
		// }


		$data['rate'] = $data['rate'];
		$data['purchase_price'] = $data['purchase_price'];

		//update column unit name use sales/items
		$unit_type = get_unit_type($data['unit_id']);
		if(isset($unit_type->unit_name)){
			$data['unit'] = $unit_type->unit_name;
		}

		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'items', $data);

		//update commodity min
		$this->db->where('commodity_id', $id);
		$data_inventory_min=[];
		$data_inventory_min['commodity_code'] = $data['commodity_code'];
		$data_inventory_min['commodity_name'] = $data['description'];
		$this->db->update(db_prefix() . 'inventory_commodity_min', $data_inventory_min);

		return true;
	}

	/**
	 * get sub group
	 * @param  boolean $id
	 * @return array  or object
	 */
	public function get_sub_group($id = false) {

		if (is_numeric($id)) {
			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'wh_sub_group')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblwh_sub_group')->result_array();
		}

	}

	/**
	 * add sub group
	 * @param array  $data
	 * @param boolean $id
	 * @return boolean
	 */
	public function add_sub_group($data, $id = false) {
		$affectedRows = 0;

		if (isset($data['hot_sub_group'])) {
			$hot_sub_group = $data['hot_sub_group'];
			unset($data['hot_sub_group']);
		}

		if(isset($hot_sub_group)){
			$sub_group_detail = json_decode($hot_sub_group);

			$es_detail = [];
			$row = [];
			$rq_val = [];
			$header = [];

			$header[] = 'id';
			$header[] = 'sub_group_code';
			$header[] = 'sub_group_name';
			$header[] = 'group_id';
			$header[] = 'order';
			$header[] = 'display';
			$header[] = 'note';


			foreach ($sub_group_detail as $key => $value) {
				//only get row "value" != 0
				if($value[1] != ''){
					$es_detail[] = array_combine($header, $value);

				}
			}
		}
		$row = [];
		$row['update'] = []; 
		$row['insert'] = []; 
		$total = [];


		foreach ($es_detail as $key => $value) {
			if($value['display'] == 'yes'){
				$value['display'] = 1;
			}else{
				$value['display'] = 0;
			}

			if($value['id'] != ''){
				$row['update'][] = $value;
			}else{
				unset($value['id']);
				$row['insert'][] = $value;
			}

		}


		if(count($row['insert']) != 0){
			$affected_rows = $this->db->insert_batch(db_prefix().'wh_sub_group', $row['insert']);
			if($affected_rows > 0){
				$affectedRows++;
			}
		}
		if(count($row['update']) != 0){
			$affected_rows = $this->db->update_batch(db_prefix().'wh_sub_group', $row['update'], 'id');
			if($affected_rows > 0){
				$affectedRows++;
			}
		}

		if ($affectedRows > 0) {
			return true;
		}

		return false;


	}

	/**
	 * delete_sub_group
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_sub_group($id) {
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'wh_sub_group');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * import xlsx commodity
	 * @param  array $data
	 * @return integer
	 */
	public function import_xlsx_commodity($data, $flag_insert_id) {
		//update column unit name use sales/items
		if(isset($data['unit_id'])){
			$unit_type = get_unit_type($data['unit_id']);
			if($unit_type){
				$data['unit'] = $unit_type->unit_name;
			}
		}

		if($data['commodity_barcode'] != ''){
			$data['commodity_barcode'] = $data['commodity_barcode'];
		}else{
			$data['commodity_barcode'] = $this->generate_commodity_barcode();
		}
		
		
		/*create sku code*/
		// if($data['sku_code'] != ''){
		// 	$data['sku_code'] = new_str_replace(' ', '', $data['sku_code']) ;
		// }else{
		// 	//data sku_code = group_character.sub_code.commodity_str_betwen.next_commodity_id; // X_X_000.id auto increment
		// 	$data['sku_code'] = $this->create_sku_code($data['group_id'], isset($data['sub_group']) ? $data['sub_group'] : '' ,$flag_insert_id );
		// 	/*create sku code*/
		// }

		// if(get_warehouse_option('barcode_with_sku_code') == 1){
		// 	$data['commodity_barcode'] = $data['sku_code'];
		// }


		/*caculator  pur, sale, profit*/
		if(isset($data['purchase_price']) && isset($data['rate']) && isset($data['profif_ratio'])){
			/*get profit*/

			$data['profif_ratio'] = $this->caculator_profit_rate_model($data['purchase_price'], $data['rate']);

		}elseif(isset($data['profif_ratio']) && isset($data['rate'])){
			/*get purchase*/
			$data['purchase_price'] = $this->caculator_purchase_price_model($data['profif_ratio'], $data['rate']);

		}elseif(isset($data['profif_ratio']) && isset($data['purchase_price'])){
			/*get rate*/
			$data['rate'] = $this->caculator_sale_price_model($data['purchase_price'], $data['profif_ratio']);

		}elseif(isset($data['purchase_price']) && isset($data['rate'])){
			/*get profit*/

			$data['profif_ratio'] = $this->caculator_profit_rate_model($data['purchase_price'], $data['rate']);
			
		}

		/*caculator  pur, sale, profit*/
		if(!isset($data['rate'])){
			return ['status' => false, 'message' => _l('rate') . _l('not_yet_entered')];
		}
		
		/*check update*/

		$item = $this->db->query('select * from tblitems where commodity_code = "'.$data['commodity_code'].'"')->row();

		if($item){
			$affected_rows=0;
			//check sku code dulicate
			if($data['sku_code'] != '' && strlen($data['sku_code']) > 0){
				if(!$this->check_sku_duplicate(['sku_code' => $data['sku_code'], 'item_id' => $item->id]) ){
					return ['status' => false, 'message' => _l('commodity_code').': '. $data['commodity_code'] ._l('wh_has').  _l('sku_code') ._l('exist')];
				}
			}

			if(isset($data['tags'])){
				$tags_value =  $data['tags'];
				unset($data['tags']);
			}else{
				$tags_value ='';
			}
			unset($data['tags']);
			foreach ($data as $key => $data_value) {
				if(!isset($data_value)){
					unset($data[$key]);
				}
			}

			$minimum_inventory = 0;
			if(isset($data['minimum_inventory'])){
				$minimum_inventory = $data['minimum_inventory'];
				unset($data['minimum_inventory']);
			}


			//update
			$this->db->where('commodity_code', $data['commodity_code']);
			$this->db->update(db_prefix() . 'items', $data);
			if ($this->db->affected_rows() > 0) {
				$affected_rows++;	
			}

			/*habdle add tags*/
			handle_tags_save($tags_value, $item->id, 'item_tags');
			

			/*check update or insert inventory min with commodity code*/
			$this->db->where('commodity_code', $data['commodity_code']);
			$check_inventory_min = $this->db->get(db_prefix().'inventory_commodity_min')->row();

			if($check_inventory_min){
				//update
				$this->db->where('commodity_code', $data['commodity_code']);
				$this->db->update(db_prefix() . 'inventory_commodity_min', ['inventory_number_min' => $minimum_inventory]);
				if ($this->db->affected_rows() > 0) {
					$affected_rows++;	
				}

			}else{
				//get commodity_id
				$this->db->where('commodity_code', $data['commodity_code']);
				$items = $this->db->get(db_prefix().'items')->row();

				$item_id=0;
				if($items){
					$item_id = $items->id;
				}

				//insert
				$data_inventory_min['commodity_id'] = $item_id;
				$data_inventory_min['commodity_code'] = $data['commodity_code'];
				$data_inventory_min['commodity_name'] = $data['description'];
				$data_inventory_min['inventory_number_min'] = $minimum_inventory;
				$add_inventory_min = $this->add_inventory_min($data_inventory_min);

				if ($add_inventory_min > 0) {
					$affected_rows++;	
				}

			}

			if($affected_rows > 0){
				return ['status' => true, 'message' => _l('updated_successfully')];
			}else{
				return ['status' => true, 'message' => _l('no_information_changed')];
			}

		}else{
			//check sku code dulicate
			/*create sku code*/
			if($data['sku_code'] != '' && strlen($data['sku_code']) > 0){
				$data['sku_code'] = new_str_replace(' ', '', $data['sku_code']) ;
			}else{
			//data sku_code = group_character.sub_code.commodity_str_betwen.next_commodity_id; // X_X_000.id auto increment
				$data['sku_code'] = $this->create_sku_code($data['group_id'], isset($data['sub_group']) ? $data['sub_group'] : '' , 0 );
				/*create sku code*/
			}

			if($data['sku_code'] != '' && strlen($data['sku_code']) > 0){
				if($this->check_sku_duplicate(['sku_code' => $data['sku_code'], 'item_id' => '']) == false){
					return ['status' => false, 'message' => _l('commodity_code').': '. $data['commodity_code'] ._l('wh_has').  _l('sku_code') ._l('exist')];
				}
			}


			$minimum_inventory = 0;
			if(isset($data['minimum_inventory'])){
				$minimum_inventory = $data['minimum_inventory'];
				unset($data['minimum_inventory']);
			}

			$data['sku_code'] = get_warehouse_option('item_sku_prefix').$data['sku_code'];

			if(isset($data['tags'])){
				$tags_value =  $data['tags'];
				unset($data['tags']);
			}else{
				$tags_value ='';
			}

			unset($data['tags']);

			//insert
			$this->db->insert(db_prefix() . 'items', $data);
			$insert_id = $this->db->insert_id();

			/*habdle add tags*/
			if($insert_id){
				handle_tags_save($tags_value, $insert_id, 'item_tags');
			}

			/*add data tblinventory*/
			if ($insert_id) {
				$data_inventory_min['commodity_id'] = $insert_id;
				$data_inventory_min['commodity_code'] = $data['commodity_code'];
				$data_inventory_min['commodity_name'] = $data['description'];
				$data_inventory_min['inventory_number_min'] = $minimum_inventory;
				$this->add_inventory_min($data_inventory_min);

				return ['status' => true, 'message' => '', 'insert_id' => $insert_id];
			}

			return ['status' => false, 'message' => 'Add item false'];
		}


	}

	/**
	 * get commodity attachments delete
	 * @param  integer $id
	 * @return object
	 */
	public function get_commodity_attachments_delete($id) {

		if (is_numeric($id)) {
			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'files')->row();
		}
	}

	//delete _commodity_file file for any
	/**
	 * delete commodity file
	 * @param  integer $attachment_id
	 * @return boolean
	 */
	public function delete_commodity_file($attachment_id) {
		$deleted = false;
		$attachment = $this->get_commodity_attachments_delete($attachment_id);

		if ($attachment) {
			if (empty($attachment->external)) {
				if (file_exists(WAREHOUSE_ITEM_UPLOAD . $attachment->rel_id . '/' . $attachment->file_name)) {
					unlink(WAREHOUSE_ITEM_UPLOAD . $attachment->rel_id . '/' . $attachment->file_name);
				} elseif(file_exists('modules/purchase/uploads/item_img/' . $attachment->rel_id . '/' . $attachment->file_name)) {
					unlink('modules/purchase/uploads/item_img/' . $attachment->rel_id . '/' . $attachment->file_name);
				}
			}
			$this->db->where('id', $attachment->id);
			$this->db->delete(db_prefix() . 'files');
			if ($this->db->affected_rows() > 0) {
				$deleted = true;
				log_activity('commodity Attachment Deleted [commodityID: ' . $attachment->rel_id . ']');
			}
			if (file_exists(WAREHOUSE_ITEM_UPLOAD . $attachment->rel_id . '/' . $attachment->file_name)) {
				if (is_dir(WAREHOUSE_ITEM_UPLOAD . $attachment->rel_id)) {

					// Check if no attachments left, so we can delete the folder also
					$other_attachments = list_files(WAREHOUSE_ITEM_UPLOAD . $attachment->rel_id);
					if (count($other_attachments) == 0) {
						// okey only index.html so we can delete the folder also
						delete_dir(WAREHOUSE_ITEM_UPLOAD . $attachment->rel_id);
					}
				}
			} else {
				if (is_dir('modules/purchase/uploads/item_img/' . $attachment->rel_id)) {

					// Check if no attachments left, so we can delete the folder also
					$other_attachments = list_files('modules/purchase/uploads/item_img/' . $attachment->rel_id);
					if (count($other_attachments) == 0) {
						// okey only index.html so we can delete the folder also
						delete_dir('modules/purchase/uploads/item_img/' . $attachment->rel_id);
					}
				}
			}

		}

		return $deleted;
	}

	/**
	 * get color
	 * @param  boolean $id
	 * @return array or object
	 */
	public function get_color($id = false) {

		if (is_numeric($id)) {
			$this->db->where('color_id', $id);

			return $this->db->get(db_prefix() . 'ware_color')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblware_color')->result_array();
		}

	}

	/**
	 * create sku code
	 * @param  int commodity_group
	 * @param  int sub_group
	 * @return string
	 */
	public function create_sku_code($commodity_group, $sub_group, $flag_insert_id = false) {
		// input  commodity group, sub group
		//get commodity group from id
		$group_character = '';
		if (isset($commodity_group) && $commodity_group != '') {

			$sql_group_where = 'SELECT * FROM ' . db_prefix() . 'items_groups where id = "' . $commodity_group . '"';
			$group_value = $this->db->query($sql_group_where)->row();
			if ($group_value) {

				if ($group_value->commodity_group_code != '') {
					$group_character = mb_substr($group_value->commodity_group_code, 0, 1, "UTF-8") . '-';

				}
			}

		}

		//get sku code from sku id
		$sub_code = '';
		if (isset($sub_group) && $sub_group != '') {

			$sql_sub_group_where = 'SELECT * FROM ' . db_prefix() . 'wh_sub_group where id = "' . $sub_group . '"';
			$sub_group_value = $this->db->query($sql_sub_group_where)->row();
			if ($sub_group_value) {
				$sub_code = $sub_group_value->sub_group_code . '-';
			}

		}

		if($flag_insert_id != 0 && $flag_insert_id != false){
			$last_commodity_id = $flag_insert_id;
		}else{

			$sql_where = 'SELECT * FROM ' . db_prefix() . 'items order by id desc limit 1';
			$res = $this->db->query($sql_where)->row();
			$last_commodity_id = 0;
			if (isset($res)) {
				$last_commodity_id = $this->db->query($sql_where)->row()->id;
			}
		}

		$next_commodity_id = (int) $last_commodity_id + 1;

		// data_sku_code = group_character.sub_code.commodity_str_betwen.next_commodity_id; // X_X_000.id auto increment
		return $group_character . $sub_code .str_pad($next_commodity_id,5,'0',STR_PAD_LEFT); // X_X_000.id auto increment
	}

	/**
	 * add color
	 * @param array $data
	 * @return integer
	 */
	public function add_color($data) {

		$option = 'off';
		if (isset($data['display'])) {
			$option = $data['display'];
			unset($data['display']);
		}

		if ($option == 'on') {
			$data['display'] = 1;
		} else {
			$data['display'] = 0;
		}

		$this->db->insert(db_prefix() . 'ware_color', $data);
		$insert_id = $this->db->insert_id();

		return $insert_id;
	}

	/**
	 * update color
	 * @param  array $data
	 * @param  integer $id
	 * @return boolean
	 */
	public function update_color($data, $id) {
		$option = 'off';
		if (isset($data['display'])) {
			$option = $data['display'];
			unset($data['display']);
		}

		if ($option == 'on') {
			$data['display'] = 1;
		} else {
			$data['display'] = 0;
		}

		$this->db->where('color_id', $id);
		$this->db->update(db_prefix() . 'ware_color', $data);

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return true;
	}

	/**
	 * delete color
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_color($id) {

		//delete job_p
		$this->db->where('color_id', $id);
		$this->db->delete(db_prefix() . 'ware_color');

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return false;
	}

	/**
	 * get color add commodity
	 * @return array
	 */
	public function get_color_add_commodity() {
		return $this->db->query('select * from tblware_color where display = 1 order by tblware_color.order asc ')->result_array();
	}

	/**
	 * Adds a loss adjustment.
	 *
	 * @param      <type>  $data   The data
	 *
	 * @return     <type>  (id loss addjustment) )
	 */
	public function add_loss_adjustment($data) {
		$loss_adjustments = [];
		if (isset($data['newitems'])) {
			$loss_adjustments = $data['newitems'];
			unset($data['newitems']);
		}

		unset($data['item_select']);
		unset($data['commodity_name']);
		unset($data['lot_number']);
		unset($data['expiry_date']);
		unset($data['current_number']);
		unset($data['updates_number']);
		unset($data['unit_name']);
		unset($data['commodity_code']);
		unset($data['unit_id']);
		unset($data['serial_number']);

		$check_appr = $this->get_approve_setting('3');
		$data_add['status'] = 0;
		if ($check_appr && $check_appr != false) {
			$data_add['status'] = 0;
		} else {
			$data_add['status'] = 1;
		}	

		if(!$this->check_format_date($data['time'])){
    		$data_add['time'] = to_sql_date($data['time'], true);
    	}else{
    		$data_add['time'] = $data['time'];
    	}

		$data_add['type'] = $data['type'];
		$data_add['reason'] = (isset($data['reason']) ? $data['reason'] : '');
		$data_add['addfrom'] = $data['addfrom'];
		$data_add['date_create'] = $data['date_create'];
		$data_add['warehouses'] = $data['warehouses'];

		$this->db->insert(db_prefix() . 'wh_loss_adjustment', $data_add);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {

			foreach ($loss_adjustments as $loss_adjustment) {
				$loss_adjustment['loss_adjustment'] = $insert_id;
				if(new_strlen($loss_adjustment['expiry_date']) > 0){
					$loss_adjustment['expiry_date'] = to_sql_date($loss_adjustment['expiry_date']);
				}else{
					$loss_adjustment['expiry_date'] = null;
				}
				unset($loss_adjustment['unit_name']);
				unset($loss_adjustment['order']);
				unset($loss_adjustment['id']);

				$this->db->insert(db_prefix() . 'wh_loss_adjustment_detail', $loss_adjustment);
			}

			//approval if not approval setting
			if (isset($insert_id)) {
				if ($data_add['status'] == 1) {

					$this->change_adjust($insert_id);
				}
			}

        	hooks()->do_action('after_wh_loss_adjustment_added', $insert_id);

			return $insert_id;
		}
		return false;

	}

	/**
	 * { update loss adjustment }
	 *
	 * @param      <type>   $data   The data
	 *
	 * @return     boolean
	 */
	public function update_loss_adjustment($data) {
		$affected_rows = 0;

		$loss_adjustments = [];
		$update_loss_adjustments = [];
		$remove_loss_adjustments = [];
		if(isset($data['isedit'])){
			unset($data['isedit']);
		}

		if (isset($data['newitems'])) {
			$loss_adjustments = $data['newitems'];
			unset($data['newitems']);
		}

		if (isset($data['items'])) {
			$update_loss_adjustments = $data['items'];
			unset($data['items']);
		}
		if (isset($data['removed_items'])) {
			$remove_loss_adjustments = $data['removed_items'];
			unset($data['removed_items']);
		}

		unset($data['item_select']);
		unset($data['commodity_name']);
		unset($data['lot_number']);
		unset($data['expiry_date']);
		unset($data['current_number']);
		unset($data['updates_number']);
		unset($data['unit_name']);
		unset($data['commodity_code']);
		unset($data['unit_id']);
		unset($data['serial_number']);


		$data_add['time'] = to_sql_date($data['time'], true);
		$data_add['type'] = $data['type'];
		$data_add['reason'] = (isset($data['reason']) ? $data['reason'] : '');
		$data_add['addfrom'] = $data['addfrom'];
		$data_add['date_create'] = $data['date_create'];
		$data_add['warehouses'] = $data['warehouses'];
		$this->db->where('id', $data['id']);
		$this->db->update(db_prefix() . 'wh_loss_adjustment', $data_add);


		// update loss adjustment detail
		foreach ($update_loss_adjustments as $loss_adjustment) {
			unset($loss_adjustment['order']);
			unset($loss_adjustment['unit_name']);

			if(new_strlen($loss_adjustment['expiry_date']) > 0){
				$loss_adjustment['expiry_date'] = to_sql_date($loss_adjustment['expiry_date']);
			}else{
				$loss_adjustment['expiry_date'] = null;
			}

			$this->db->where('id', $loss_adjustment['id']);
			if ($this->db->update(db_prefix() . 'wh_loss_adjustment_detail', $loss_adjustment)) {
				$affected_rows++;
			}
		}

		// delete loss adjustment detail
		foreach ($remove_loss_adjustments as $loss_adjustment_detail_id) {
			$this->db->where('id', $loss_adjustment_detail_id);
			if ($this->db->delete(db_prefix() . 'wh_loss_adjustment_detail')) {
				$affected_rows++;
			}
		}

		// Add loss adjustment detail
		foreach ($loss_adjustments as $loss_adjustment) {
			$loss_adjustment['loss_adjustment'] = $data['id'];

			if(new_strlen($loss_adjustment['expiry_date']) > 0){
				$loss_adjustment['expiry_date'] = to_sql_date($loss_adjustment['expiry_date']);
			}else{
				$loss_adjustment['expiry_date'] = null;
			}

			unset($loss_adjustment['order']);
			unset($loss_adjustment['id']);
			unset($loss_adjustment['unit_name']);

			$this->db->insert(db_prefix() . 'wh_loss_adjustment_detail', $loss_adjustment);
			if($this->db->insert_id()){
				$affected_rows++;
			}
		}

        hooks()->do_action('after_wh_loss_adjustment_updated', $data['id']);

		return true;

	}

	/**
	 * { delete loss adjustment }
	 *
	 * @param      <type>   $id     The identifier
	 *
	 * @return     boolean
	 */
	public function delete_loss_adjustment($id) {
		
		hooks()->do_action('before_loss_adjustment_deleted', $id);

		$affected_rows = 0;
		$this->db->where('loss_adjustment', $id);
		$this->db->delete(db_prefix() . 'wh_loss_adjustment_detail');
		if ($this->db->affected_rows() > 0) {

			$affected_rows++;
		}

		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'wh_loss_adjustment');
		if ($this->db->affected_rows() > 0) {

			$affected_rows++;
		}



		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * Gets the loss adjustment.
	 *
	 * @param      string  $id     The identifier
	 *
	 * @return     <type>  The loss adjustment.
	 */
	public function get_loss_adjustment($id = '') {
		if ($id == '') {
			return $this->db->get(db_prefix() . 'wh_loss_adjustment')->result_array();
		} else {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'wh_loss_adjustment')->row();
		}
	}

	/**
	 * Gets the loss adjustment detailt by masterid.
	 *
	 * @param      string  $id     The identifier
	 *
	 * @return     <type>  The loss adjustment detailt by masterid.
	 */
	public function get_loss_adjustment_detailt_by_masterid($id = '') {
		if ($id == '') {
			return $this->db->get(db_prefix() . 'wh_loss_adjustment_detail')->result_array();
		} else {
			$this->db->where('loss_adjustment', $id);
			return $this->db->get(db_prefix() . 'wh_loss_adjustment_detail')->result_array();
		}
	}

	/**
	 * { change adjust }
	 *
	 * @param      <type>  $id     The identifier
	 */
	public function change_adjust($id) {
		$loss_adjustment = $this->get_loss_adjustment($id);
		$detail = $this->get_loss_adjustment_detailt_by_masterid($id);

		$affected_rows = 0;
		foreach ($detail as $d) {
			$check = $this->check_commodity_exist_inventory($loss_adjustment->warehouses, $d['items'], $d['lot_number'], $d['expiry_date']);
			if ($check == false) {

				if(isset($d['lot_number']) && $d['lot_number'] != '0' && $d['lot_number'] != ''){
					/*have value*/
					$this->db->where('lot_number', $d['lot_number']);

				}else{

					/*lot number is 0 or ''*/
					$this->db->group_start();

					$this->db->where('lot_number', '0');
					$this->db->or_where('lot_number', '');
					$this->db->or_where('lot_number', null);

					$this->db->group_end();
				}

				$this->db->where('warehouse_id', $loss_adjustment->warehouses);
				$this->db->where('commodity_id', $d['items']);

				if($d['expiry_date'] == ''){
					$this->db->where('expiry_date', null);
				}else{
					$this->db->where('expiry_date', $d['expiry_date']);
				}


				$inventory_value = $this->db->get(db_prefix().'inventory_manage')->row();

				if($inventory_value){

					$this->db->where('id', $inventory_value->id);
				}else{
					return false;
				}

				$this->db->update(db_prefix() . 'inventory_manage', [
					'inventory_number' => $d['updates_number'],
				]);
				if ($this->db->affected_rows() > 0) {
					$affected_rows++;
				}

				//TODO loss
				$serial_number_list = '';
				if((float)$d['current_number'] < (float)$d['updates_number']){
					$this->add_serial_number($d['items'], $loss_adjustment->warehouses, $inventory_value->id, $d['serial_number']);
					$serial_number_list = $d['serial_number'];

				}else{

					if(new_strlen($d['serial_number']) > 0){
						$serial_number_data = [];
						$arr_serial_number = new_explode(',', $d['serial_number']);

						foreach ($arr_serial_number as $value) {
							if(new_strlen($value) > 0){	

								$serial_number_temporaty = $this->loss_adjustment_delete_serial_number($d['items'], $loss_adjustment->warehouses, $inventory_value->id, 1, $value);

								if(new_strlen($serial_number_list) > 0){
									$serial_number_list .= ','.$serial_number_temporaty;
								}else{
									$serial_number_list .= $serial_number_temporaty;
								}
							}
						}
					}else{
						/*Auto get random serial number*/
						$serial_number_quantity = (int)((float)$d['current_number'] - (float)$d['updates_number']);
						$list_temporaty_serial_numbers = $this->get_list_temporaty_serial_numbers($d['items'], $loss_adjustment->warehouses, $serial_number_quantity);

						foreach ($list_temporaty_serial_numbers as $value) {
							if(new_strlen($value['serial_number']) > 0){	

								$serial_number_temporaty = $this->loss_adjustment_delete_serial_number($d['items'], $loss_adjustment->warehouses, $inventory_value->id, 1, $value['serial_number']);

								if(new_strlen($serial_number_list) > 0){
									$serial_number_list .= ','.$serial_number_temporaty;
								}else{
									$serial_number_list .= $serial_number_temporaty;
								}
							}
						}


					}

				}

				// update serial number for loss adjustment detail
				$this->db->where('id', $d['id']);
				$this->db->update(db_prefix().'wh_loss_adjustment_detail', ['serial_number' => $serial_number_list]);

				$this->db->insert(db_prefix() . 'goods_transaction_detail', [
					'goods_receipt_id' => $id,
					'old_quantity' => $d['current_number'],
					'quantity' => $d['updates_number'],
					'date_add' => date('Y-m-d H:i:s'),
					'commodity_id' => $d['items'],
					'lot_number' => $d['lot_number'],
					'expiry_date' => $d['expiry_date'],
					'warehouse_id' => $loss_adjustment->warehouses,
					'status' => 3,
					'serial_number' => $serial_number_list,
				]);

			} else {
				return false;
			}

		}

		if ($affected_rows > 0) {
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'wh_loss_adjustment', [
				'status' => 1,
			]);

			return true;
		}
		return false;
	}

	/**
	 *@param array data
	 */
	public function get_inventory_valuation_report_view($data, $excel = false) {
		$from_date = $data['from_date'];
		$to_date = $data['to_date'];

		if(!$this->check_format_date($from_date)){
			$from_date = to_sql_date($from_date);
		}
		if(!$this->check_format_date($to_date)){
			$to_date = to_sql_date($to_date);
		}

		$where_warehouse_id = '';

		$where_warehouse_id_with_internal_i = '';
		$where_warehouse_id_with_internal_e = '';

		if (new_strlen($data['warehouse_id']) > 0) {
			$arr_warehouse_id =  new_explode(',', $data['warehouse_id']);

			foreach ($arr_warehouse_id as $warehouse_id) {
				if ($warehouse_id != '') {

					if ($where_warehouse_id == '') {
						$where_warehouse_id .= ' (find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) ';

						$where_warehouse_id_with_internal_i .= ' (find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.to_stock_name)';

						$where_warehouse_id_with_internal_e .= ' (find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.from_stock_name)';

					} else {
						$where_warehouse_id .= ' or find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) ';

						$where_warehouse_id_with_internal_i .= '  OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR  find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.to_stock_name) ';

						$where_warehouse_id_with_internal_e .= ' OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.warehouse_id) OR find_in_set('.$warehouse_id.', '.db_prefix().'goods_transaction_detail.from_stock_name) ';

					}

				}
			}

			if ($where_warehouse_id != '') {
				$where_warehouse_id .= ')';

				$where_warehouse_id_with_internal_i .= ')';
				$where_warehouse_id_with_internal_e .= ')';

			}
		}





		//get_commodity_list in warehouse
		if (new_strlen($data['warehouse_id']) > 0) {
			$commodity_lists = $this->db->query('SELECT commodity_id, ' . db_prefix() . 'items.commodity_code, ' . db_prefix() . 'items.rate, ' . db_prefix() . 'items.purchase_price, ' . db_prefix() . 'items.description as commodity_name, ' . db_prefix() . 'ware_unit_type.unit_name FROM ' . db_prefix() . 'goods_transaction_detail
				LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'goods_transaction_detail.commodity_id = ' . db_prefix() . 'items.id
				LEFT JOIN ' . db_prefix() . 'ware_unit_type ON ' . db_prefix() . 'items.unit_id = ' . db_prefix() . 'ware_unit_type.unit_type_id where '.$where_warehouse_id. ' AND '.db_prefix().'items.active = 1 group by commodity_id ')->result_array();

		}else{

			$commodity_lists = $this->db->query('SELECT commodity_id, ' . db_prefix() . 'items.commodity_code, ' . db_prefix() . 'items.rate, ' . db_prefix() . 'items.purchase_price, ' . db_prefix() . 'items.description as commodity_name, ' . db_prefix() . 'ware_unit_type.unit_name FROM ' . db_prefix() . 'goods_transaction_detail
				LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'goods_transaction_detail.commodity_id = ' . db_prefix() . 'items.id
				LEFT JOIN ' . db_prefix() . 'ware_unit_type ON ' . db_prefix() . 'items.unit_id = ' . db_prefix() . 'ware_unit_type.unit_type_id where '.db_prefix().'items.active = 1 group by commodity_id')->result_array();
		}

		//import_openings
		if (new_strlen($data['warehouse_id']) > 0) {
			$import_openings = $this->db->query('SELECT commodity_id, quantity, status, purchase_price, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 1 OR status = 4 OR status = 3) AND date_format(date_add,"%Y-%m-%d") < "' . $from_date . '" AND ' .$where_warehouse_id_with_internal_i)->result_array();

		}else{

			$import_openings = $this->db->query('SELECT commodity_id, quantity, status, purchase_price, old_quantity FROM ' . db_prefix() . 'goods_transaction_detail
				where ( status = 1 OR status = 4 OR status = 3) AND date_format(date_add,"%Y-%m-%d") < "' . $from_date . '"')->result_array();
		}

		$arr_import_openings = [];

		foreach ($import_openings as $import_opening_key => $import_opening_value) {
			if(isset($arr_import_openings[$import_opening_value['commodity_id']])){
				switch ($import_opening_value['status']) {
					case '1':
					$arr_import_openings_amount[$import_opening_value['commodity_id']] += (float)$import_opening_value['quantity']*(float)$import_opening_value['purchase_price'];
					$arr_import_openings[$import_opening_value['commodity_id']]      += (float)$import_opening_value['quantity'];
					break;
					case '3':
					if(((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity']) > 0){

						$arr_import_openings_amount[$import_opening_value['commodity_id']] += ((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity'])*(float)$import_opening_value['purchase_price'];

						$arr_import_openings[$import_opening_value['commodity_id']]      += ((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity']);
					}
					break;
					case '4':
					$arr_import_openings_amount[$import_opening_value['commodity_id']] += (float)$import_opening_value['quantity']*(float)$import_opening_value['purchase_price'];

					$arr_import_openings[$import_opening_value['commodity_id']]      += (float)$import_opening_value['quantity'];

					break;

				}


			}else{
				switch ($import_opening_value['status']) {
					case '1':
					$arr_import_openings_amount[$import_opening_value['commodity_id']] = (float)$import_opening_value['quantity']*(float)$import_opening_value['purchase_price'];

					$arr_import_openings[$import_opening_value['commodity_id']]      = (float)$import_opening_value['quantity'];
					break;
					case '3':
					if(((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity']) > 0){
						$arr_import_openings_amount[$import_opening_value['commodity_id']] = ((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity'])*(float)$import_opening_value['purchase_price'];

						$arr_import_openings[$import_opening_value['commodity_id']]      = ((float)$import_opening_value['quantity'] - (float)$import_opening_value['old_quantity']);
					}
					break;
					case '4':
					$arr_import_openings_amount[$import_opening_value['commodity_id']] = (float)$import_opening_value['quantity']*(float)$import_opening_value['purchase_price'];

					$arr_import_openings[$import_opening_value['commodity_id']]      = (float)$import_opening_value['quantity'];

					break;

				}

			}
		}

		//export_openings
		if (new_strlen($data['warehouse_id']) > 0) {
			$export_openings = $this->db->query('SELECT commodity_id, quantity, status, purchase_price, old_quantity FROM ' .db_prefix(). 'goods_transaction_detail
				where ( status = 2 OR status = 4 OR status = 3 ) AND date_format(date_add,"%Y-%m-%d") < "' . $from_date . '" AND '.$where_warehouse_id_with_internal_e )->result_array();

		}else{

			$export_openings = $this->db->query('SELECT commodity_id, quantity, status, purchase_price, old_quantity FROM ' .db_prefix(). 'goods_transaction_detail
				where ( status = 2 OR status = 4 OR status = 3 ) AND date_format(date_add,"%Y-%m-%d") < "' . $from_date . '"')->result_array();
		}

		$arr_export_openings = [];
		foreach ($export_openings as $export_opening_key => $export_opening_value) {
			$purchase_price = $export_opening_value['purchase_price'];

			if(isset($arr_export_openings[$export_opening_value['commodity_id']])){
				switch ($export_opening_value['status']) {
					case '2':
					$arr_export_openings_amount[$export_opening_value['commodity_id']] += (float)$export_opening_value['quantity']*(float)$purchase_price;

					$arr_export_openings[$export_opening_value['commodity_id']]      += (float)$export_opening_value['quantity'];
					break;
					case '3':
					if(((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity']) < 0){
						$arr_export_openings_amount[$export_opening_value['commodity_id']] += abs((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity'])*(float)$purchase_price;

						$arr_export_openings[$export_opening_value['commodity_id']]      += abs((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity']);
					}
					break;
					case '4':
					$arr_export_openings_amount[$export_opening_value['commodity_id']] += (float)$export_opening_value['quantity']*(float)$purchase_price;

					$arr_export_openings[$export_opening_value['commodity_id']]      += (float)$export_opening_value['quantity'];

					break;

				}


			}else{
				switch ($export_opening_value['status']) {
					case '2':
					$arr_export_openings_amount[$export_opening_value['commodity_id']] = (float)$export_opening_value['quantity']*(float)$purchase_price;

					$arr_export_openings[$export_opening_value['commodity_id']]      = (float)$export_opening_value['quantity'];
					break;
					case '3':
					if(((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity']) < 0){
						$arr_export_openings_amount[$export_opening_value['commodity_id']] = abs((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity'])*(float)$purchase_price;

						$arr_export_openings[$export_opening_value['commodity_id']]      = abs((float)$export_opening_value['quantity'] - (float)$export_opening_value['old_quantity']);
					}
					break;
					case '4':
					$arr_export_openings_amount[$export_opening_value['commodity_id']] = (float)$export_opening_value['quantity']*(float)$purchase_price;

					$arr_export_openings[$export_opening_value['commodity_id']]      = (float)$export_opening_value['quantity'];

					break;

				}

			}

		}

		//import_periods
		if (new_strlen($data['warehouse_id']) > 0) {
			$import_periods = $this->db->query('SELECT commodity_id, quantity, status, purchase_price, old_quantity FROM ' .db_prefix(). 'goods_transaction_detail
				where ( status = 1 OR status = 4 OR status = 3) AND "' . $from_date . '" <= date_format(date_add,"%Y-%m-%d") AND date_format(date_add,"%Y-%m-%d") <= "' . $to_date . '" AND '. $where_warehouse_id_with_internal_i)->result_array();

		}else{

			$import_periods = $this->db->query('SELECT commodity_id, quantity, status, purchase_price, old_quantity  FROM ' .db_prefix(). 'goods_transaction_detail
				where ( status = 1 OR status = 4 OR status = 3) AND "' . $from_date . '" <= date_format(date_add,"%Y-%m-%d") AND date_format(date_add,"%Y-%m-%d") <= "' . $to_date . '"')->result_array();
		}

		$arr_import_periods = [];
		foreach ($import_periods as $import_period_key => $import_period_value) {

			if(isset($arr_import_periods[$import_period_value['commodity_id']])){

				switch ($import_period_value['status']) {
					case '1':
					$arr_import_periods_amount[$import_period_value['commodity_id']] += (float)$import_period_value['quantity']*(float)$import_period_value['purchase_price'];

					$arr_import_periods[$import_period_value['commodity_id']]      += (float)$import_period_value['quantity'];
					break;
					case '3':
					if(((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity']) > 0){

						$arr_import_periods_amount[$import_period_value['commodity_id']] += ((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity'])*(float)$import_period_value['purchase_price'];

						$arr_import_periods[$import_period_value['commodity_id']]      += ((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity']);
					}
					break;
					case '4':
					$arr_import_periods_amount[$import_period_value['commodity_id']] += (float)$import_period_value['quantity']*(float)$import_period_value['purchase_price'];

					$arr_import_periods[$import_period_value['commodity_id']]      += (float)$import_period_value['quantity'];

					break;

				}


			}else{
				switch ($import_period_value['status']) {
					case '1':
					$arr_import_periods_amount[$import_period_value['commodity_id']] = (float)$import_period_value['quantity']*(float)$import_period_value['purchase_price'];

					$arr_import_periods[$import_period_value['commodity_id']]      = (float)$import_period_value['quantity'];
					break;
					case '3':
					if(((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity']) > 0){
						$arr_import_periods_amount[$import_period_value['commodity_id']] = ((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity'])*(float)$import_period_value['purchase_price'];

						$arr_import_periods[$import_period_value['commodity_id']]      = ((float)$import_period_value['quantity'] - (float)$import_period_value['old_quantity']);
					}
					break;
					case '4':
					$arr_import_periods_amount[$import_period_value['commodity_id']] = (float)$import_period_value['quantity']*(float)$import_period_value['purchase_price'];

					$arr_import_periods[$import_period_value['commodity_id']]      = (float)$import_period_value['quantity'];

					break;

				}


			}
		}

		//export_periods
		if (new_strlen($data['warehouse_id']) > 0) {
			$export_periods = $this->db->query('SELECT commodity_id, quantity, status, purchase_price, old_quantity FROM ' .db_prefix(). 'goods_transaction_detail
				where ( status = 2 OR status = 4 OR status = 3) AND "' . $from_date . '" <= date_format(date_add,"%Y-%m-%d") AND date_format(date_add,"%Y-%m-%d") <= "' . $to_date . '" AND '.$where_warehouse_id_with_internal_e)->result_array();

		}else{

			$export_periods = $this->db->query('SELECT commodity_id, quantity, status, purchase_price, old_quantity FROM ' .db_prefix(). 'goods_transaction_detail
				where ( status = 2 OR status = 4 OR status = 3) AND "' . $from_date . '" <= date_format(date_add,"%Y-%m-%d") AND date_format(date_add,"%Y-%m-%d") <= "' . $to_date . '"')->result_array();
		}

		$arr_export_periods = [];
		foreach ($export_periods as $export_period_key => $export_period_value) {
			$purchase_price = $export_period_value['purchase_price'];

			if(isset($arr_export_periods[$export_period_value['commodity_id']])){

				switch ($export_period_value['status']) {
					case '2':
					$arr_export_periods_amount[$export_period_value['commodity_id']] += (float)$export_period_value['quantity']*(float)$purchase_price;

					$arr_export_periods[$export_period_value['commodity_id']]      += (float)$export_period_value['quantity'];
					break;
					case '3':
					if(((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity']) < 0){
						$arr_export_periods_amount[$export_period_value['commodity_id']] += abs((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity'])*(float)$purchase_price;

						$arr_export_periods[$export_period_value['commodity_id']]      += abs((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity']);
					}
					break;
					case '4':
					$arr_export_periods_amount[$export_period_value['commodity_id']] += (float)$export_period_value['quantity']*(float)$purchase_price;

					$arr_export_periods[$export_period_value['commodity_id']]      += (float)$export_period_value['quantity'];

					break;

				}



			}else{

				switch ($export_period_value['status']) {
					case '2':
					$arr_export_periods_amount[$export_period_value['commodity_id']] = (float)$export_period_value['quantity']*(float)$purchase_price;

					$arr_export_periods[$export_period_value['commodity_id']]      = (float)$export_period_value['quantity'];
					break;
					case '3':
					if(((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity']) < 0){
						$arr_export_periods_amount[$export_period_value['commodity_id']] = abs((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity'])*(float)$purchase_price;

						$arr_export_periods[$export_period_value['commodity_id']]      = abs((float)$export_period_value['quantity'] - (float)$export_period_value['old_quantity']);
					}
					break;
					case '4':
					$arr_export_periods_amount[$export_period_value['commodity_id']] = (float)$export_period_value['quantity']*(float)$purchase_price;

					$arr_export_periods[$export_period_value['commodity_id']]      = (float)$export_period_value['quantity'];

					break;

				}


			}

		}

		//html for page
		$staff_default_language = get_staff_default_language(get_staff_user_id());
		if(is_null($staff_default_language)){
			$staff_default_language = get_option('active_language');
		}

		$from_date_html =  _l('days') . '  ' . date('d', strtotime($from_date)) . '  ' . _l('month') . '  ' . date('m', strtotime($from_date)) . '  ' . _l('year') . '  ' . date('Y', strtotime($from_date)) . '  ';
		$to_date_html = _l('days') . '  ' . date('d', strtotime($to_date)) . '  ' . _l('month') . '  ' . date('m', strtotime($to_date)) . '  ' . _l('year') . '  ' . date('Y', strtotime($to_date)) . '  ';

		if($staff_default_language == 'english'){
			$from_date_html =  date('F', strtotime($from_date)).', '.date('d', strtotime($from_date)).' '. date('Y', strtotime($from_date)) ; 
			$to_date_html = date('F', strtotime($to_date)).', '.date('d', strtotime($to_date)).' '. date('Y', strtotime($to_date)) ; 
		}elseif($staff_default_language == 'french'){
			$from_date_html =  date('d', strtotime($from_date)).' '._l(date('F', strtotime($from_date))).' '. date('Y', strtotime($from_date)) ; 
			$to_date_html = date('d', strtotime($to_date)).' '._l(date('F', strtotime($to_date))).' '. date('Y', strtotime($to_date)) ; 
		}else{
			$from_date_html = _d($from_date); 
			$to_date_html = _d($to_date); 
		}

		$html = '';
		$html .= ' <p><h3 class="bold align_cen text-center">' . mb_strtoupper(_l('inventory_valuation_report')) . '</h3></p>

		<div class="col-md-12 pull-right">
		<div class="row">
		<div class="col-md-12 align_cen text-center">
		<p>' . _l('from_date') . ' :  <span class="fstyle" >' . $from_date_html . '</p>
		<p>' . _l('to_date') . ' :  <span class="fstyle">' . $to_date_html . '</p>
		</div>
		</div>
		</div>

		<table class="table">';
		$company_name = get_option('invoice_company_name');
		$address = get_option('invoice_company_address');
		$total_opening_quatity = 0;
		$total_opening_amount = 0;
		$total_import_period_quatity = 0;
		$total_import_period_amount = 0;
		$total_export_period_quatity = 0;
		$total_export_period_amount = 0;
		$total_closing_quatity = 0;
		$total_closing_amount = 0;

		//rate
		$total_amount_sold = 0;
		$total_amount_purchased = 0;
		$total_expected_profit = 0;
		$total_sales_number = 0;
		//purchase

		$html .= '<tbody>
		<tr>
		<td class="bold width">' . _l('company_name') . '</td>
		<td>' . $company_name . '</td>
		</tr>
		<tr>
		<td class="bold">' . _l('address') . '</td>
		<td>' . $address . '</td>
		</tr>
		</tbody>
		</table>
		<div class="col-md-12">
		<table class="table table-bordered">
		<tbody>
		<tr>
		<th colspan="1" class="td_text">'. _l('_order').'</th>
		<th colspan="1" class="td_text">' . _l('commodity_code') . '</th>
		<th colspan="1" class="td_text">' . _l('commodity_name') . '</th>
		<th colspan="1" class="td_text">' . _l('wh_unit_name') . '</th>

		<th colspan="1" class="td_text">' . _l('inventory_number') . '</th>
		<th colspan="1" class="td_text">' . _l('rate') . '</th>
		<th colspan="1" class="td_text">' . _l('purchase_price') . '</th>
		<th colspan="1" class="td_text">' . _l('amount_sold') . '</th>
		<th colspan="1" class="td_text">' . _l('amount_purchased') . '</th>
		<th colspan="1" class="td_text">' . _l('expected_profit') . '</th>

		</tr>';

		if($excel){
			$excel_data = [];

			
			if(isset($arr_import_openings)){
				$excel_data['arr_import_openings'] = $arr_import_openings;
			}
			if(isset($arr_import_openings_amount)){
				$excel_data['arr_import_openings_amount'] = $arr_import_openings_amount;
			}

			if(isset($arr_export_openings)){
				$excel_data['arr_export_openings'] = $arr_export_openings;
			}

			if(isset($arr_export_openings_amount)){
				$excel_data['arr_export_openings_amount'] = $arr_export_openings_amount;
			}

			if(isset($arr_import_periods)){
				$excel_data['arr_import_periods'] = $arr_import_periods;
			}
			if(isset($arr_import_periods_amount)){
				$excel_data['arr_import_periods_amount'] = $arr_import_periods_amount;
			}
			
			if(isset($arr_export_periods)){
				$excel_data['arr_export_periods'] = $arr_export_periods;
			}
			if(isset($arr_export_periods_amount)){
				$excel_data['arr_export_periods_amount'] = $arr_export_periods_amount;
			}
			$excel_data['commodity_lists'] = $commodity_lists;

			return $excel_data;
		}

		foreach ($commodity_lists as $commodity_list_key => $commodity_list) {
			$commodity_list_key++;
			$html .= '<tr>
			<td class="border_1">' . $commodity_list_key . '</td>
			<td class="border_1">' . $commodity_list['commodity_code'] . '</td>
			<td class="border_1">' . $commodity_list['commodity_name'] . '</td>
			<td class="border_1">' . $commodity_list['unit_name'] . '</td>';

			//sales
			$sales_number = 0;
			$export_period_quantity = isset($arr_export_periods[$commodity_list['commodity_id']]) ? $arr_export_periods[$commodity_list['commodity_id']] : 0;
			$sales_number = $export_period_quantity;
			$total_sales_number += (float) $export_period_quantity;

			//opening
			$stock_opening_quatity = 0;
			$stock_opening_amount = 0;

			$import_opening_quantity = isset($arr_import_openings[$commodity_list['commodity_id']]) ? $arr_import_openings[$commodity_list['commodity_id']] : 0;

			$export_opening_quantity = isset($arr_export_openings[$commodity_list['commodity_id']]) ? $arr_export_openings[$commodity_list['commodity_id']] : 0;
			$import_opening_amount = isset($arr_import_openings_amount[$commodity_list['commodity_id']]) ? $arr_import_openings_amount[$commodity_list['commodity_id']] : 0;
			$export_opening_amount = isset($arr_export_openings_amount[$commodity_list['commodity_id']]) ? $arr_export_openings_amount[$commodity_list['commodity_id']] : 0;

			$stock_opening_quatity = $import_opening_quantity - $export_opening_quantity;
			$stock_opening_amount = (float)$import_opening_amount - (float)$export_opening_amount;

			//import_period
			$import_period_quatity = 0;
			$import_period_amount = 0;

			$import_period_quantity = isset($arr_import_periods[$commodity_list['commodity_id']]) ? $arr_import_periods[$commodity_list['commodity_id']] : 0;

			$import_period_quatity = $import_period_quantity;
			$import_period_amount = isset($arr_import_periods_amount[$commodity_list['commodity_id']]) ? $arr_import_periods_amount[$commodity_list['commodity_id']] : 0;

			//export_period
			$export_period_quatity = 0;
			$export_period_amount = 0;

			$export_period_quantity = isset($arr_export_periods[$commodity_list['commodity_id']]) ? $arr_export_periods[$commodity_list['commodity_id']] : 0;

			$export_period_quatity = $export_period_quantity;
			$export_period_amount = isset($arr_export_periods_amount[$commodity_list['commodity_id']]) ? $arr_export_periods_amount[$commodity_list['commodity_id']] : 0;

			//closing
			$closing_quatity = 0;
			$expected_profit = 0;
			//eventory number
			$closing_quatity = (float) $stock_opening_quatity + (float) $import_period_quatity - (float) $export_period_quatity;
			//sale
			//
			$total_amount_sold += ((float) $closing_quatity * $commodity_list['rate']);
			// $total_amount_purchased += ((float) $closing_quatity * $commodity_list['purchase_price']);
			// $total_expected_profit += (((float) $closing_quatity * $commodity_list['rate']) - ((float) $closing_quatity * $commodity_list['purchase_price']));


			$total_closing_quatity += $closing_quatity;
			$closing_amount = ($stock_opening_amount + $import_period_amount - $export_period_amount);
			$total_amount_purchased += (float)$closing_amount;
			$total_expected_profit += (((float) $closing_quatity * $commodity_list['rate']) - ((float)$closing_amount));



			// Sell number
			$text_danger = '';
			if(((float) $closing_quatity * $commodity_list['rate'] - (float)$closing_amount) < 0){
				$text_danger = ' text-danger';
			}
			$html .= '<td class="td_style_r">' . $closing_quatity . '</td>


			<td class="td_style_r">' . app_format_money((float)$commodity_list['rate'] , ''). '</td>
			<td class="td_style_r">' . app_format_money((float)$commodity_list['purchase_price'] , ''). '</td>
			<td class="td_style_r">' . app_format_money((float) ($closing_quatity * $commodity_list['rate']), '') . '</td>
			<td class="td_style_r">' . app_format_money((float) ($closing_amount), '') . '</td>
			<td class="td_style_r'.$text_danger.'">' . app_format_money((float) ((float) $closing_quatity * $commodity_list['rate'] - (float)$closing_amount), '') . '</td>
			</tr>';
		}
		$html .= '<tr>
		<th colspan="4" class="td_text_r">' . _l('total') . ' : </th>
		<th colspan="1" class="td_text_r">' . $total_closing_quatity . '</th>


		<th colspan="1" class="td_text_r"></th>
		<th colspan="1" class="td_text_r"></th>

		<th colspan="1" class="td_text_r">' . app_format_money((float) ($total_amount_sold), '') . '</th>
		<th colspan="1" class="td_text_r">' . app_format_money((float) ($total_amount_purchased), '') . '</th>
		<th colspan="1" class="td_text_r">' . app_format_money((float) ($total_expected_profit), '') . '</th>
		</tr>
		</tbody>
		</table>
		</div>



		<br>
		<br>
		<br>
		<br>';

		$html .= '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/pdf_style.css') . '"  rel="stylesheet" type="text/css" />';

		return $html;

	}

	/**
	 * generate commodity barcode
	 *
	 * @return     string
	 */
	public function generate_commodity_barcode() {
		$item = false;
		do {
			$length = 11;
			$chars = '0123456789';
			$count = new_strlen($chars);
			$password = '';
			for ($i = 0; $i < $length; $i++) {
				$index = rand(0, $count - 1);
				$password .= mb_substr($chars, $index, 1);
			}
			$this->db->where('commodity_barcode', $password);
			$item = $this->db->get(db_prefix() . 'items')->row();
		} while ($item);

		return $password;
	}

	/**
	 * delete goods receipt
	 * @param  [integer] $id
	 * @return [redirect]
	 */
	public function delete_goods_receipt($id) {

		hooks()->do_action('before_goods_receipt_deleted', $id);

		$affected_rows = 0;

		$this->db->where('goods_receipt_id', $id);
		$this->db->delete(db_prefix() . 'goods_receipt_detail');
		if ($this->db->affected_rows() > 0) {

			$affected_rows++;
		}

		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'goods_receipt');
		if ($this->db->affected_rows() > 0) {

			$affected_rows++;
		}

		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete goods delivery
	 * @param  [integer] $id
	 * @return [redirect]
	 */
	public function delete_goods_delivery($id) {

		hooks()->do_action('before_goods_delivery_deleted', $id);

		$affected_rows = 0;

		$this->db->where('goods_delivery_id', $id);
		$this->db->delete(db_prefix() . 'goods_delivery_detail');
		if ($this->db->affected_rows() > 0) {

			$affected_rows++;
		}

		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'goods_delivery');
		if ($this->db->affected_rows() > 0) {

			$affected_rows++;
		}

		$packing_list_ids = [];
		$packing_lists = $this->get_packing_list_by_deivery_note($id);
		foreach ($packing_lists as $value) {
		    $packing_list_ids[] = $value['id'];
		}

		if(count($packing_list_ids) > 0){
			$this->db->where('packing_list_id IN ('.implode(',', $packing_list_ids).')');
			$this->db->delete(db_prefix() . 'wh_packing_list_details');
			if ($this->db->affected_rows() > 0) {
				$affected_rows++;
			}
		}

		$this->db->where('delivery_note_id', $id);
		$this->db->delete(db_prefix() . 'wh_packing_lists');
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		// check if exist order => delete delivery note information in order
		if(get_status_modules_wh('omni_sales')){
			$this->db->where('stock_export_number', $id);
			$cart = $this->db->get(db_prefix().'cart')->row();
			if($cart){
				$this->db->where('id', $cart->id);
				$this->db->update(db_prefix() . 'cart', ['stock_export_number' => '', 'stock_export' => 'off']);
			}
		}

		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * check format date Y-m-d
	 *
	 * @param      String   $date   The date
	 *
	 * @return     boolean 
	 */
	public function check_format_date($date){
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
			return true;
		} else {
			return false;
		}
	}

	 /**
     * Gets the taxes.
     *
     * @return     <array>  The taxes.
     */
	 public function get_taxes()
	 {
	 	return $this->db->query('select id, name as label, taxrate from '.db_prefix().'taxes')->result_array();
	 }

    /**
     * get invoice by customer
     * @param  [type] $data 
     * @return array             
     */
    public function  get_invoices_by_customer($data)
    {

    	$this->db->where('clientid', $data);
    	$arr_invoice =  $this->db->get(db_prefix().'invoices')->result_array();
    	$options = '';
    	$options .= '<option value=""></option>';
    	foreach ($arr_invoice as $invoice) {

    		$options .= '<option value="' . $invoice['id'] . '">' . format_invoice_number($invoice['id']) . '</option>';
    	}
    	return $options;
    }

   	/**
     * Gets the taxes.
     *
     * @return     <array>  The taxes.
     */
   	public function get_taxe_value($id)
   	{
   		return $this->db->query('select id, name as label, taxrate from '.db_prefix().'taxes where id = '.$id)->row();
   	}

    /**
     * get goods delivery from invoice
     * @param  [integer] $invoice_id 
     * @return array             
     */
    public function  get_goods_delivery_from_invoice($invoice_id)
    {
    	$this->db->where('invoice_id', $invoice_id);
    	return $this->db->get(db_prefix() . 'goods_delivery')->result_array();
    }

    /**
     * get invoices
     * @param  boolean $id 
     * @return array      
     */
    public function  get_invoices($id = false)
    {

    	if (is_numeric($id)) {
    		$this->db->where('id', $id);

    		return $this->db->get(db_prefix() . 'invoices')->row();
    	}
    	if ($id == false) {
    		$arr_invoice = $this->get_invoices_goods_delivery('invoice');
    		if(count($arr_invoice) > 0){
    			return $this->db->query('select *, iv.id as id from '.db_prefix().'invoices as iv left join '.db_prefix().'projects as pj on pj.id = iv.project_id left join '.db_prefix().'clients as cl on cl.userid = iv.clientid  where iv.id NOT IN ('.implode(", ", $arr_invoice).') order by iv.id desc')->result_array();
    		}
    		return $this->db->query('select *, iv.id as id from '.db_prefix().'invoices as iv left join '.db_prefix().'projects as pj on pj.id = iv.project_id left join '.db_prefix().'clients as cl on cl.userid = iv.clientid  order by iv.id desc')->result_array();
    	}

    }


    /**
     * update goods delivery
     * @param [type]  $data 
     * @param boolean $id   
     */
    public function update_goods_delivery($data, $id = false) {
    	$results=0;

    	$goods_deliveries = [];
		$update_goods_deliveries = [];
		$remove_goods_deliveries = [];
		if(isset($data['isedit'])){
			unset($data['isedit']);
		}

		if (isset($data['newitems'])) {
			$goods_deliveries = $data['newitems'];
			unset($data['newitems']);
		}

		if (isset($data['items'])) {
			$update_goods_deliveries = $data['items'];
			unset($data['items']);
		}
		if (isset($data['removed_items'])) {
			$remove_goods_deliveries = $data['removed_items'];
			unset($data['removed_items']);
		}

		unset($data['item_select']);
		unset($data['commodity_name']);
		unset($data['warehouse_id']);
		unset($data['available_quantity']);
		unset($data['quantities']);
		unset($data['unit_price']);
		unset($data['note']);
		unset($data['unit_name']);
		unset($data['commodity_code']);
		unset($data['unit_id']);
		unset($data['discount']);
		unset($data['guarantee_period']);
		unset($data['tax_rate']);
		unset($data['tax_name']);
		unset($data['discount_money']);
		unset($data['total_after_discount']);
		unset($data['serial_number']);
		unset($data['without_checking_warehouse']);

    	$check_appr = $this->get_approve_setting('2');
    	$data['approval'] = 0;
    	if ($check_appr && $check_appr != false) {
    		$data['approval'] = 0;
    	} else {
    		$data['approval'] = 1;
    	}

    	if (isset($data['hot_purchase'])) {
    		$hot_purchase = $data['hot_purchase'];
    		unset($data['hot_purchase']);
    	}

    	if(isset($data['edit_approval'])){
    		unset($data['edit_approval']);
    	}

    	if(isset($data['save_and_send_request']) ){
	    		$save_and_send_request = $data['save_and_send_request'];
	    		unset($data['save_and_send_request']);
    	}

    	if(!$this->check_format_date($data['date_c'])){
    		$data['date_c'] = to_sql_date($data['date_c']);
    	}else{
    		$data['date_c'] = $data['date_c'];
    	}


    	if(!$this->check_format_date($data['date_add'])){
    		$data['date_add'] = to_sql_date($data['date_add']);
    	}else{
    		$data['date_add'] = $data['date_add'];
    	}

    	$data['total_money'] 	= reformat_currency_j($data['total_money']);
    	$data['total_discount'] = reformat_currency_j($data['total_discount']);
    	$data['after_discount'] = reformat_currency_j($data['after_discount']);

    	$data['addedfrom'] = get_staff_user_id();

    	$goods_delivery_id = $data['id'];
    	unset($data['id']);

    	$this->db->where('id', $goods_delivery_id);
    	$this->db->update(db_prefix() . 'goods_delivery', $data);
    	if ($this->db->affected_rows() > 0) {
			$results++;
		}

    	/*update googs delivery*/

    	foreach ($update_goods_deliveries as $goods_delivery) {
			$tax_money = 0;
			$tax_rate_value = 0;
			$tax_rate = null;
			$tax_id = null;
			$tax_name = null;
			if(isset($goods_delivery['tax_select'])){
				$tax_rate_data = $this->wh_get_tax_rate($goods_delivery['tax_select']);
				$tax_rate_value = $tax_rate_data['tax_rate'];
				$tax_rate = $tax_rate_data['tax_rate_str'];
				$tax_id = $tax_rate_data['tax_id_str'];
				$tax_name = $tax_rate_data['tax_name_str'];
			}

			if((float)$tax_rate_value != 0){
				$tax_money = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'] * (float)$tax_rate_value / 100;
				$total_money = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'] + (float)$tax_money;
				$amount = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'] + (float)$tax_money;
			}else{
				$total_money = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'];
				$amount = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'];
			}

			$sub_total = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'];

			$goods_delivery['tax_id'] = $tax_id;
			$goods_delivery['total_money'] = $total_money;
			$goods_delivery['tax_rate'] = $tax_rate;
			$goods_delivery['sub_total'] = $sub_total;
			$goods_delivery['tax_name'] = $tax_name;

			unset($goods_delivery['order']);
			unset($goods_delivery['tax_select']);
			unset($goods_delivery['unit_name']);
			if(isset($goods_delivery['without_checking_warehouse'])){
				unset($goods_delivery['without_checking_warehouse']);
			}


			$this->db->where('id', $goods_delivery['id']);
			if ($this->db->update(db_prefix() . 'goods_delivery_detail', $goods_delivery)) {
				$results++;
			}
		}

		// delete receipt note
		foreach ($remove_goods_deliveries as $goods_deliver_id) {
			$this->db->where('id', $goods_deliver_id);
			if ($this->db->delete(db_prefix() . 'goods_delivery_detail')) {
				$results++;
			}
		}

		// Add goods deliveries
		foreach ($goods_deliveries as $goods_delivery) {
			$goods_delivery['goods_delivery_id'] = $goods_delivery_id;
			$goods_delivery['expiry_date'] = null;
			$goods_delivery['lot_number'] = null;

			$tax_money = 0;
			$tax_rate_value = 0;
			$tax_rate = null;
			$tax_id = null;
			$tax_name = null;
			if(isset($goods_delivery['tax_select'])){
				$tax_rate_data = $this->wh_get_tax_rate($goods_delivery['tax_select']);
				$tax_rate_value = $tax_rate_data['tax_rate'];
				$tax_rate = $tax_rate_data['tax_rate_str'];
				$tax_id = $tax_rate_data['tax_id_str'];
				$tax_name = $tax_rate_data['tax_name_str'];
			}

			if((float)$tax_rate_value != 0){
				$tax_money = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'] * (float)$tax_rate_value / 100;
				$total_money = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'] + (float)$tax_money;
				$amount = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'] + (float)$tax_money;
			}else{
				$total_money = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'];
				$amount = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'];
			}

			$sub_total = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'];

			$goods_delivery['tax_id'] = $tax_id;
			$goods_delivery['total_money'] = $total_money;
			$goods_delivery['tax_rate'] = $tax_rate;
			$goods_delivery['sub_total'] = $sub_total;
			$goods_delivery['tax_name'] = $tax_name;

			unset($goods_delivery['order']);
			unset($goods_delivery['id']);
			unset($goods_delivery['tax_select']);
			unset($goods_delivery['unit_name']);
			if(isset($goods_delivery['without_checking_warehouse'])){
				unset($goods_delivery['without_checking_warehouse']);
			}

			$this->db->insert(db_prefix() . 'goods_delivery_detail', $goods_delivery);
			if($this->db->insert_id()){
				$results++;
			}
		}


			//send request approval
    	if($save_and_send_request == 'true'){
    		/*check send request with type =2 , inventory delivery voucher*/
    		$check_r = $this->check_inventory_delivery_voucher(['rel_id' => $goods_delivery_id, 'rel_type' => '2']);

    		if($check_r['flag_export_warehouse'] == 1){
    			$this->send_request_approve(['rel_id' => $goods_delivery_id, 'rel_type' => '2', 'addedfrom' => $data['addedfrom']]);

    		}
    	}


		//approval if not approval setting
    	if (isset($goods_delivery_id)) {
    		if ($data['approval'] == 1) {
    			$this->update_approve_request($goods_delivery_id, 2, 1);
    		}
    	}
        
        hooks()->do_action('after_wh_goods_delivery_updated', $goods_delivery_id);

    	return $results > 0 ? true : false;

    }


	/**
	 * update goods receipt
	 * @param  array  $data 
	 * @param  boolean $id   
	 * @return [type]        
	 */
	public function update_goods_receipt($data, $id = false) {

		$inventory_receipts = [];
		$update_inventory_receipts = [];
		$remove_inventory_receipts = [];
		if(isset($data['isedit'])){
			unset($data['isedit']);
		}

		if (isset($data['newitems'])) {
			$inventory_receipts = $data['newitems'];
			unset($data['newitems']);
		}

		if (isset($data['items'])) {
			$update_inventory_receipts = $data['items'];
			unset($data['items']);
		}
		if (isset($data['removed_items'])) {
			$remove_inventory_receipts = $data['removed_items'];
			unset($data['removed_items']);
		}
		unset($data['item_select']);
		unset($data['commodity_name']);
		unset($data['warehouse_id']);
		unset($data['quantities']);
		unset($data['unit_price']);
		unset($data['tax']);
		unset($data['lot_number']);
		unset($data['date_manufacture']);
		unset($data['expiry_date']);
		unset($data['note']);
		unset($data['unit_name']);
		unset($data['sub_total']);
		unset($data['commodity_code']);
		unset($data['unit_id']);
		unset($data['tax_rate']);
		unset($data['tax_name']);
		unset($data['tax_money']);
		unset($data['goods_money']);
		unset($data['serial_number']);

		if(isset($data['warehouse_id_m'])){
			$data['warehouse_id'] = $data['warehouse_id_m'];
			unset($data['warehouse_id_m']);
		}

		if(isset($data['expiry_date_m'])){
			$data['expiry_date'] = to_sql_date($data['expiry_date_m']);
			unset($data['expiry_date_m']);
		}

		$check_appr = $this->get_approve_setting('1');

		/*get suppier name from supplier code*/
		if (get_status_modules_wh('purchase')) {
			if($data['supplier_code'] != ''){
				$this->load->model('purchase/purchase_model');
				$client                = $this->purchase_model->get_vendor($id);
				if(count($client) > 0 ){
					$data['supplier_name'] = $client[0]['company'];
				}

			}
		}

		$data['approval'] = 0;
		if ($check_appr && $check_appr != false) {
			$data['approval'] = 0;
		} else {
			$data['approval'] = 1;
		}

		if(isset($data['save_and_send_request'])){
			$save_and_send_request = $data['save_and_send_request'];
			unset($data['save_and_send_request']);
		}



		if (isset($data['hot_purchase'])) {
			$hot_purchase = $data['hot_purchase'];
			unset($data['hot_purchase']);
		}

		
		if(!$this->check_format_date($data['date_c'])){
			$data['date_c'] = to_sql_date($data['date_c']);
		}else{
			$data['date_c'] = $data['date_c'];
		}
		
		if(!$this->check_format_date($data['date_add'])){
			$data['date_add'] = to_sql_date($data['date_add']);
		}else{
			$data['date_add'] = $data['date_add'];
		}

		if(isset($data['expiry_date'])){
			if(!$this->check_format_date($data['expiry_date'])){
				$data['expiry_date'] = to_sql_date($data['expiry_date']);
			}else{
				$data['expiry_date'] = $data['expiry_date'];
			}
		}


		$data['addedfrom'] = get_staff_user_id();

		$data['total_tax_money'] = reformat_currency_j($data['total_tax_money']);

		$data['total_goods_money'] = reformat_currency_j($data['total_goods_money']);
		$data['value_of_inventory'] = reformat_currency_j($data['value_of_inventory']);

		$data['total_money'] = reformat_currency_j($data['total_money']);

		$goods_receipt_id = $data['id'];
		unset($data['id']);

		$results = 0;

		$this->db->where('id', $goods_receipt_id);
		$this->db->update(db_prefix() . 'goods_receipt', $data);
		if ($this->db->affected_rows() > 0) {
			$results++;
		}

		/*update save note*/
		// update receipt note
		foreach ($update_inventory_receipts as $inventory_receipt) {
			if($inventory_receipt['date_manufacture'] != ''){
				$inventory_receipt['date_manufacture'] = to_sql_date($inventory_receipt['date_manufacture']);
			}else{
				$inventory_receipt['date_manufacture'] = null;
			}

			if($inventory_receipt['expiry_date'] != ''){
				$inventory_receipt['expiry_date'] = to_sql_date($inventory_receipt['expiry_date']);
			}else{
				$inventory_receipt['expiry_date'] = null;
			}

			$tax_money = 0;
			$tax_rate_value = 0;
			$tax_rate = null;
			$tax_id = null;
			$tax_name = null;
			if(isset($inventory_receipt['tax_select'])){
				$tax_rate_data = $this->wh_get_tax_rate($inventory_receipt['tax_select']);
				$tax_rate_value = $tax_rate_data['tax_rate'];
				$tax_rate = $tax_rate_data['tax_rate_str'];
				$tax_id = $tax_rate_data['tax_id_str'];
				$tax_name = $tax_rate_data['tax_name_str'];
			}

			if((float)$tax_rate_value != 0){
				$tax_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] * (float)$tax_rate_value / 100;
				$goods_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] + (float)$tax_money;
				$amount = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] + (float)$tax_money;
			}else{
				$goods_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];
				$amount = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];
			}

			$sub_total = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];

			$inventory_receipt['tax_money'] = $tax_money;
			$inventory_receipt['tax'] = $tax_id;
			$inventory_receipt['goods_money'] = $goods_money;
			$inventory_receipt['tax_rate'] = $tax_rate;
			$inventory_receipt['sub_total'] = $sub_total;
			$inventory_receipt['tax_name'] = $tax_name;
			unset($inventory_receipt['order']);
			unset($inventory_receipt['tax_select']);

			$this->db->where('id', $inventory_receipt['id']);
			if ($this->db->update(db_prefix() . 'goods_receipt_detail', $inventory_receipt)) {
				$results++;
			}
		}

		// delete receipt note
		foreach ($remove_inventory_receipts as $receipt_detail_id) {
			$this->db->where('id', $receipt_detail_id);
			if ($this->db->delete(db_prefix() . 'goods_receipt_detail')) {
				$results++;
			}
		}

		// Add receipt note
		foreach ($inventory_receipts as $inventory_receipt) {
			$inventory_receipt['goods_receipt_id'] = $goods_receipt_id;
			if($inventory_receipt['date_manufacture'] != ''){
				$inventory_receipt['date_manufacture'] = to_sql_date($inventory_receipt['date_manufacture']);
			}else{
				$inventory_receipt['date_manufacture'] = null;
			}

			if($inventory_receipt['expiry_date'] != ''){
				$inventory_receipt['expiry_date'] = to_sql_date($inventory_receipt['expiry_date']);
			}else{
				$inventory_receipt['expiry_date'] = null;
			}

			$tax_money = 0;
			$tax_rate_value = 0;
			$tax_rate = null;
			$tax_id = null;
			$tax_name = null;
			if(isset($inventory_receipt['tax_select'])){
				$tax_rate_data = $this->wh_get_tax_rate($inventory_receipt['tax_select']);
				$tax_rate_value = $tax_rate_data['tax_rate'];
				$tax_rate = $tax_rate_data['tax_rate_str'];
				$tax_id = $tax_rate_data['tax_id_str'];
				$tax_name = $tax_rate_data['tax_name_str'];
			}

			if((float)$tax_rate_value != 0){
				$tax_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] * (float)$tax_rate_value / 100;
				$goods_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] + (float)$tax_money;
				$amount = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] + (float)$tax_money;
			}else{
				$goods_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];
				$amount = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];
			}

			$sub_total = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];

			$inventory_receipt['tax_money'] = $tax_money;
			$inventory_receipt['tax'] = $tax_id;
			$inventory_receipt['goods_money'] = $goods_money;
			$inventory_receipt['tax_rate'] = $tax_rate;
			$inventory_receipt['sub_total'] = $sub_total;
			$inventory_receipt['tax_name'] = $tax_name;
			unset($inventory_receipt['order']);
			unset($inventory_receipt['id']);
			unset($inventory_receipt['tax_select']);

			$this->db->insert(db_prefix() . 'goods_receipt_detail', $inventory_receipt);
			if($this->db->insert_id()){
				$results++;
			}
		}
		
		if (isset($goods_receipt_id)) {
	            //send request approval
			if($save_and_send_request == 'true'){
				$this->send_request_approve(['rel_id' => $goods_receipt_id, 'rel_type' => '1', 'addedfrom' => $data['addedfrom']]);
			}
		}

		//approval if not approval setting
		if (isset($goods_receipt_id)) {
			if ($data['approval'] == 1) {
				$this->update_approve_request($goods_receipt_id, 1, 1);
			}
		}

        hooks()->do_action('after_wh_goods_receipt_updated', $goods_receipt_id);


		return $results > 0 ? $goods_receipt_id : false;

	}

	/**
	 * get commodity in_warehouse
	 * @param  array $warehouse 
	 * @return array            
	 */
	public function get_commodity_in_warehouse($warehouse){

		$array_commodity=[];
		$array_commodity[] = 0;
		$index=0;
		foreach ($warehouse as $warehouse_id) {
			$sql ='SELECT distinct commodity_id FROM '.db_prefix().'inventory_manage where warehouse_id = "'.$warehouse_id.'"';
			$array_data = $this->db->query($sql)->result_array();

			if(count($array_data)>0){
				foreach ($array_data as $c_key => $commodity_id) {
					if(!in_array($commodity_id['commodity_id'], $array_commodity)){
						$array_commodity[$index] = $commodity_id['commodity_id'];
						$index++;

					}
				}
			}

		}
		return $array_commodity;

	}

	/**
	 * get commodity alert
	 * @param  integer $status 
	 * @return array         
	 */
	public function get_commodity_alert($status){
		$array_commodity=[];
		$index=0;

		if($status == 1 ){
			/*1 : out of stock, 3: minmumstock, 4:maximum stock*/
			$sql ='SELECT commodity_id,  sum(inventory_number) as inventory_number FROM '.db_prefix().'inventory_manage group by commodity_id';
		}elseif($status == 2){
			/*2 : expired*/
			$sql ='SELECT commodity_id,  sum(inventory_number) as inventory_number, commodity_id, warehouse_id, expiry_date FROM '.db_prefix().'inventory_manage group by commodity_id, warehouse_id, expiry_date order by tblinventory_manage.commodity_id asc';
		}else{
			/*3: minmumstock, 4:maximum stock*/
			$sql ='SELECT commodity_id,  sum(inventory_number) as inventory_number, commodity_id, warehouse_id FROM '.db_prefix().'inventory_manage group by commodity_id, warehouse_id order by tblinventory_manage.commodity_id asc';
		}

		$array_data = $this->db->query($sql)->result_array();

		if(count($array_data)>0){
			foreach ($array_data as $c_key => $commodity_id) {
				if($status == 1){
					if($commodity_id['inventory_number'] == 0){
						if(!in_array($commodity_id['commodity_id'], $array_commodity)){
							$array_commodity[$index] = $commodity_id['commodity_id'];
							$index++;

						}
					}
				}elseif($status == 2){
					/*2 : expired*/
					if($commodity_id['expiry_date'] != null && $commodity_id['expiry_date'] != ''){
						if(!in_array($commodity_id['commodity_id'], $array_commodity)){

							$datediff  = strtotime($commodity_id['expiry_date']) - strtotime(date('Y-m-d'));
							$days_diff = floor($datediff / (60 * 60 * 24));

							if ($days_diff <= 30) {
								$array_commodity[$index] = $commodity_id['commodity_id'];
								$index++;

							}
						}

					}


				}elseif($status == 3){
					/*3: minmumstock*/
					$inventory_min = $this->get_inventory_min_by_commodity_id($commodity_id['commodity_id']);
					if($inventory_min){
						if($inventory_min->inventory_number_min >= $commodity_id['inventory_number']){
							if(!in_array($commodity_id['commodity_id'], $array_commodity)){
								$array_commodity[$index] = $commodity_id['commodity_id'];
								$index++;
							}
						}
					}

				}else{
					/*4: maximumstock*/
					$inventory_max = $this->get_inventory_min_by_commodity_id($commodity_id['commodity_id']);
					if($inventory_max){
						if($inventory_max->inventory_number_max <= $commodity_id['inventory_number']){
							if(!in_array($commodity_id['commodity_id'], $array_commodity)){
								$array_commodity[$index] = $commodity_id['commodity_id'];
								$index++;
							}
						}
					}

				}

			}
		}
		return $array_commodity;

	}


	/**
	 * get inventory by commodity
	 * @param  integer $commodity_id 
	 * @return object               
	 */
	public function get_inventory_by_commodity($commodity_id){

		$sql = 'SELECT sum(inventory_number) as inventory_number FROM ' . db_prefix() . 'inventory_manage
		where ' . db_prefix() . 'inventory_manage.commodity_id = ' . $commodity_id . ' group by ' . db_prefix() . 'inventory_manage.commodity_id';
		$data = $this->db->query($sql)->row(); 
		return $data;


	}

	/**
	 * check inventory min
	 * @param  integer $commodity_id 
	 * @return boolean               
	 */
	public function check_inventory_min($commodity_id)
	{	
		$status=false;
		$inventory_min=0;
		$this->db->where('commodity_id', $commodity_id);
		$result = $this->db->get(db_prefix() . 'inventory_commodity_min')->row();
		if($result){
			$inventory_min = $result->inventory_number_min;
		}

		$sql = 'SELECT sum(inventory_number) as inventory_number FROM ' . db_prefix() . 'inventory_manage
		where ' . db_prefix() . 'inventory_manage.commodity_id = ' . $commodity_id . ' group by ' . db_prefix() . 'inventory_manage.warehouse_id';

		$data = $this->db->query($sql)->result_array(); 
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				if((float)$value['inventory_number'] < (float)$inventory_min){
					return $status = false;
				}
			}

			$status=true;
		}else{
			if((float)$inventory_min > 0){
				return $status = false;
			}
			$status=true;
		}

		return $status;
	}

	/**
	 * get item group
	 * @return array 
	 */
	public function get_item_group() {
		return $this->db->query('select id as id, CONCAT(name,"_",commodity_group_code) as label from ' . db_prefix() . 'items_groups')->result_array();
	}

	/**
	 * list subgroup by group
	 * @param  integer $group 
	 * @return string        
	 */
	public function list_subgroup_by_group($group)
	{
		$this->db->where('group_id', $group);
		$arr_subgroup = $this->db->get(db_prefix().'wh_sub_group')->result_array();

		$options = '';
		if(count($arr_subgroup) > 0){
			foreach ($arr_subgroup as $value) {

				$options .= '<option value="' . $value['id'] . '">' . $value['sub_group_name'] . '</option>';
			}

		}
		return $options;

	}


	/**
	 * update warehouse selling price profif ratio
	 * @param  array $data 
	 * @return boolean       
	 */
	public function update_warehouse_selling_price_profif_ratio($data)
	{

		$this->db->where('name','warehouse_selling_price_rule_profif_ratio');
		$this->db->update(db_prefix() . 'options', [
			'value' => $data['warehouse_selling_price_rule_profif_ratio'],
		]);
		if ($this->db->affected_rows() > 0) {
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * update profit rate by purchase price sale
	 * @param  array $data 
	 * @return boolean       
	 */
	public function update_profit_rate_by_purchase_price_sale($data)
	{

		$this->db->where('name','profit_rate_by_purchase_price_sale');
		$this->db->update(db_prefix() . 'options', [
			'value' => $data['profit_rate_by_purchase_price_sale'],
		]);
		if ($this->db->affected_rows() > 0) {
			return true;
		}else{
			return false;
		}
	}

    /**
     * update rules for rounding prices
     * @param  array $data 
     * @return boolean       
     */
    public function update_rules_for_rounding_prices($data)
    {

    	if($data['type'] == 'warehouse_integer_part'){
    		$this->db->where('name','warehouse_the_fractional_part');
    		$this->db->update(db_prefix() . 'options', [
    			'value' => (int)0,
    		]);

    	}else{
    		$this->db->where('name','warehouse_integer_part');
    		$this->db->update(db_prefix() . 'options', [
    			'value' => (int)0,
    		]);
    	}

    	$this->db->where('name',$data['type']);
    	$this->db->update(db_prefix() . 'options', [
    		'value' => (int)$data['input_value'],
    	]);
    	if ($this->db->affected_rows() > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }

	/**
	 * get average price inventory
	 * @param  integer $commodity_id     
	 * @param  integer $sale_price       
	 * @param  integer $profif_ratio_old 
	 * @return array                   
	 */
	public function get_average_price_inventory($commodity_id, $sale_price, $profif_ratio_old, $warehouse_filter='')
	{	

		$average_price_of_inventory=0;	// purchase price
		$quantity=0;
		$total_money=0;
		$profit_rate_actual=0;
		$trade_discounts=0;

		$item = false;

		/*type : 0 purchase price, 1: sale price*/
		$profit_type = get_warehouse_option('profit_rate_by_purchase_price_sale');

		/*update filter by warehouse*/
		if(is_array($warehouse_filter)){
			$str_warehouse = implode(',', $warehouse_filter);

			$where_staff ='find_in_set(warehouse_id, "'.$str_warehouse.'")';
			$this->db->where($where_staff);

		}

		$this->db->where('commodity_id', $commodity_id);
		$this->db->where('inventory_number !=', '0');
		$arr_inventory = $this->db->get(db_prefix().'inventory_manage')->result_array();


		if(count($arr_inventory) > 0){
			foreach ($arr_inventory as $inventory_value) {
				$this->db->where('expiry_date', $inventory_value['expiry_date']);
				$this->db->where('lot_number', $inventory_value['lot_number']);
				$this->db->where('status', '1');
				$this->db->where('commodity_id', $commodity_id);
				$commodity_import = $this->db->get(db_prefix().'goods_transaction_detail')->row();

				if(isset($commodity_import)){

					$quantity 	 += (float)$inventory_value['inventory_number'];
					$total_money += (float)$inventory_value['purchase_price'] *(float)$inventory_value['inventory_number'];
				}
			}
			$item =  true;
		}

		if($quantity != 0){
			$average_price_of_inventory = (float)$total_money/(float)$quantity;
		}

		if($average_price_of_inventory != 0){
			/*caculator profit rate*/
			switch ($profit_type) {
				case '0':
	    			# Calculate the selling price based on the purchase price rate of profit
	    			# sale price = purchase price * ( 1 + profit rate)
				$profit_rate_actual = (((float)$sale_price/(float)$average_price_of_inventory)-(float)1)*100;

				break;

				case '1':

	    			# Calculate the selling price based on the selling price rate of profit
	    			# sale price = purchase price / ( 1 - profit rate)

				$profit_rate_actual = ((float)1 -((float)$average_price_of_inventory/(float)$sale_price))*100;

				break;

			}

		}

		if((float)$average_price_of_inventory > 0){

			if( ($profif_ratio_old !='') && ($profif_ratio_old != '0') && ($profif_ratio_old != 'null') ){
				$trade_discounts = (((float)$profit_rate_actual - (float)$profif_ratio_old)/(float)$profif_ratio_old)*100;
			}
		}

		$data=[];
		$data['average_price_of_inventory'] = $average_price_of_inventory;
		$data['profit_rate_actual'] = $profit_rate_actual;
		$data['trade_discounts'] = $trade_discounts;
		$data['item'] = $item;

		return $data;
	}


	 /**
     * { update purchase setting }
     *
     * @param      <type>   $data   The data
     *
     * @return     boolean 
     */
	 public function update_auto_create_received_delivery_setting($data)
	 {

	 	$val = $data['input_name_status'] == 'true' ? 1 : 0;

	 	$this->db->where('name',$data['input_name']);
	 	$this->db->update(db_prefix() . 'options', [
	 		'value' => $val,
	 	]);
	 	if ($this->db->affected_rows() > 0) {
	 		return true;
	 	}else{
	 		return false;
	 	}
	 }

    /**
     * auto create goods receipt with purchase order
     * @param  array $data 
     *      
     */
    public function auto_create_goods_receipt_with_purchase_order($data)
    {
    	$this->load->model('clients_model');

    	$arr_pur_resquest = [];
    	$total_goods_money = 0;
    	$total_money = 0;
    	$total_tax_money = 0;
    	$value_of_inventory = 0;

    	$sql = 'select item_code as commodity_code, ' . db_prefix() . 'items.description, ' . db_prefix() . 'items.unit_id, unit_price, quantity as quantities, ' . db_prefix() . 'pur_order_detail.tax as tax, into_money, (' . db_prefix() . 'pur_order_detail.total-' . db_prefix() . 'pur_order_detail.into_money) as tax_money, total as goods_money from ' . db_prefix() . 'pur_order_detail
    	left join ' . db_prefix() . 'items on ' . db_prefix() . 'pur_order_detail.item_code =  ' . db_prefix() . 'items.id
    	left join ' . db_prefix() . 'taxes on ' . db_prefix() . 'taxes.id = ' . db_prefix() . 'pur_order_detail.tax where ' . db_prefix() . 'pur_order_detail.pur_order = ' . $data['id'];
    	$results = $this->db->query($sql)->result_array();

    	$currency_rate = 1;
		$this->load->model('purchase/purchase_model');
		$get_pur_order = $this->purchase_model->get_pur_order($data['id']);
		if($get_pur_order){
			if($get_pur_order->currency_rate != null){
				$currency_rate = $get_pur_order->currency_rate;
			}
		}

    	foreach ($results as $key => $value) {
    		$value['unit_price'] = round((float)$value['unit_price']/$currency_rate, 5);

    		$value['into_money'] = $value['quantities'] * (float)$value['quantities'];

			//get tax value
    		$tax_rate = 0 ;
    		if($value['tax'] != null && $value['tax'] != '') {
    			$arr_tax = new_explode('|', $value['tax']);
    			foreach ($arr_tax as $tax_id) {

    				$tax = $this->get_taxe_value($tax_id);
    				if($tax){
    					$tax_rate += (float)$tax->taxrate;		    	
    				}
    			}
    		}

    		$value['tax_money'] = $value['into_money']*(float)$tax_rate/100;

    		$total_goods_money += $value['into_money'];
    		$total_tax_money += $value['tax_money'];

    		$sub_total = (float)$value['unit_price'] * (float)$value['quantities'];
    		$results[$key]['sub_total'] = $sub_total;
    		if(get_option('auto_generate_lotnumber') == 1){
    			$results[$key]['lot_number'] = $this->create_lot_number();
    		}
    	}

    	$total_money = $total_goods_money + $total_tax_money;
    	$value_of_inventory = $total_goods_money;

    	/*get purchase order*/
    	$this->db->where('id', $data['id']);
    	$purchase_order = $this->db->get(db_prefix().'pur_orders')->row();

    	$arr_pur_resquest['date_c']			= '';
    	$arr_pur_resquest['date_add']		= '';
    	$arr_pur_resquest['supplier_name']	= '';
    	$arr_pur_resquest['buyer_id']		= '';
    	$arr_pur_resquest['pr_order_id']	= $data['id'];
    	$arr_pur_resquest['description']	= '';
    	$arr_pur_resquest['addedfrom']	= '';

    	if($purchase_order){
    		$this->load->model('purchase/purchase_model');
    		$supplier_name = $this->purchase_model->get_vendor($purchase_order->vendor);

    		$arr_pur_resquest['date_c']			= $purchase_order->order_date;
    		$arr_pur_resquest['date_add']		= $purchase_order->delivery_date;
    		$arr_pur_resquest['supplier_name']	= isset($supplier_name) ? $supplier_name->company: '';
    		$arr_pur_resquest['buyer_id']		= $purchase_order->buyer;
    		$arr_pur_resquest['pr_order_id']	= $data['id'];
    		$arr_pur_resquest['description']	= $purchase_order->vendornote;
    		$arr_pur_resquest['addedfrom']	= $purchase_order->addedfrom;
    	}

    	$arr_pur_resquest['goods_receipt_detail'] = $results;
    	$arr_pur_resquest['total_tax_money'] = $total_tax_money;
    	$arr_pur_resquest['total_goods_money'] = $total_goods_money;
    	$arr_pur_resquest['value_of_inventory'] = $value_of_inventory;
    	$arr_pur_resquest['total_money'] = $total_money;
    	$arr_pur_resquest['total_results'] = count($results);

    	$status = $this->add_goods_receipt_from_purchase_order($arr_pur_resquest);

    	return $status;

    	
    }


    /**
     * update goods receipt warehouse
     * @param  array $data 
     * @return boolean       
     */
    public function update_goods_receipt_warehouse($data)
    {

    	$this->db->where('name',$data['input_name']);
    	$this->db->update(db_prefix() . 'options', [
    		'value' => $data['input_name_status'],
    	]);
    	if ($this->db->affected_rows() > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }

    public function add_goods_receipt_from_purchase_order($data_insert)
    {
    	
    	$warehouse_id =  get_warehouse_option('goods_receipt_warehouse');

    	$data['approval'] = 1;

    	if (isset($data['hot_purchase'])) {
    		$hot_purchase = $data['hot_purchase'];
    		unset($data['hot_purchase']);
    	}

    	$data['goods_receipt_code'] = $this->create_goods_code();

    	if(!is_null($data_insert['date_c'])){

    		if(!$this->check_format_date($data_insert['date_c'])){
    			$data['date_c'] = to_sql_date($data_insert['date_c']);
    		}else{
    			$data['date_c'] = $data_insert['date_c'];
    		}
    	}else{
    		$data['date_c'] = date("Y-m-d");
    	}

    	if(!is_null($data_insert['date_add']) && new_strlen($data_insert['date_add']) > 0 ){

    		if(!$this->check_format_date($data_insert['date_add'])){
    			$data['date_add'] = to_sql_date($data_insert['date_add']);
    		}else{
    			$data['date_add'] = $data_insert['date_add'];
    		}
    	}else{
    		$data['date_add'] = date("Y-m-d");
    	}

    	$data['addedfrom'] =  $data_insert['addedfrom'];

    	$data['total_tax_money'] = reformat_currency_j($data_insert['total_tax_money']);

    	$data['total_goods_money'] = reformat_currency_j($data_insert['total_goods_money']);
    	$data['value_of_inventory'] = reformat_currency_j($data_insert['value_of_inventory']);

    	$data['total_money'] = reformat_currency_j($data_insert['total_money']);
    	$data['supplier_name'] = $data_insert['supplier_name'];
    	$data['buyer_id'] = $data_insert['buyer_id'];
    	$data['pr_order_id'] = $data_insert['pr_order_id'];
    	$data['description'] = $data_insert['description'];


    	$this->db->insert(db_prefix() . 'goods_receipt', $data);
    	$insert_id = $this->db->insert_id();

    	$results=0;

    	if (isset($insert_id) && (count($data_insert['goods_receipt_detail']) > 0) ) {

    		foreach ($data_insert['goods_receipt_detail'] as $purchase_key => $purchase_value) {
    			if(isset($purchase_value['description'])){
    				unset($purchase_value['description']);
    			}
    			if(isset($purchase_value['into_money'])){
    				unset($purchase_value['into_money']);
    			}

    			$purchase_value['warehouse_id'] = $warehouse_id;
    			$purchase_value['goods_receipt_id'] = $insert_id;

    			$this->db->insert(db_prefix() . 'goods_receipt_detail', $purchase_value);
    			$insert_detail = $this->db->insert_id();

    			$results++;

    		}

    		$data_log = [];
    		$data_log['rel_id'] = $insert_id;
    		$data_log['rel_type'] = 'stock_import';
    		$data_log['staffid'] = get_staff_user_id();
    		$data_log['date'] = date('Y-m-d H:i:s');
    		$data_log['note'] = "stock_import";

    		$this->add_activity_log($data_log);

    	}

    	if(isset($insert_id)){
    		/*update next number setting*/
    		$this->update_inventory_setting(['next_inventory_received_mumber' =>  get_warehouse_option('next_inventory_received_mumber')+1]);
    	}

		//approval if not approval setting
    	if (isset($insert_id)) {
    		if ($data['approval'] == 1) {
    			$this->update_approve_request($insert_id, 1, 1);
    		}
    	}

    	return $results > 0 ? true : false;


    }


    /**
     * get itemid from name
     * @param  string $name 
     * @return integer       
     */
    public function get_itemid_from_name($name)
    {	
    	$item_id=0;

    	$this->db->where('description', $name);
    	$item_value = $this->db->get(db_prefix().'items')->row();

    	if($item_value){
    		$item_id = $item_value->id;
    	}

    	return $item_id;

    }


    /**
     * get tax id from taxname taxrate
     * @param  string $taxname 
     * @param  string $taxrate 
     * @return integer          
     */
    public function get_tax_id_from_taxname_taxrate($taxname, $taxrate)
    {	$tax_id = 0;
    	$this->db->where('name', $taxname);
    	$this->db->where('taxrate', $taxrate);

    	$tax_value = $this->db->get(db_prefix().'taxes')->row();

    	if($tax_value){
    		$tax_id = $tax_value->id;
    	}
    	return $tax_id;
    }



    /**
     * auto_create_goods_delivery_with_invoice
     * @param  integer $invoice_id 
     *              
     */
    public function auto_create_goods_delivery_with_invoice($invoice_id, $invoice_update='')
    {

    	$this->db->where('id', $invoice_id);
    	$invoice_value = $this->db->get(db_prefix().'invoices')->row();

    	if($invoice_value){

    		/*get value for goods delivery*/

    		$data['goods_delivery_code'] = $this->create_goods_delivery_code();

    		if(!$this->check_format_date($invoice_value->date)){
    			$data['date_c'] = to_sql_date($invoice_value->date);
    		}else{
    			$data['date_c'] = $invoice_value->date;
    		}


    		if(!$this->check_format_date($invoice_value->date)){
    			$data['date_add'] = to_sql_date($invoice_value->date);

    		}else{
    			$data['date_add'] = $invoice_value->date;
    		}

    		$data['customer_code'] 	= $invoice_value->clientid;
    		$data['invoice_id'] 	= $invoice_id;
    		$data['addedfrom'] 	= $invoice_value->addedfrom;
    		$data['description'] 	= $invoice_value->adminnote;
    		$data['address'] 	= $this->get_shipping_address_from_invoice($invoice_id);
    		$data['staff_id'] 	= $invoice_value->sale_agent;
    		$data['invoice_no'] 	= format_invoice_number($invoice_value->id);

    		$data['total_money'] 	= (float)$invoice_value->subtotal + (float)$invoice_value->total_tax;
    		$data['after_discount'] 	= (float)$invoice_value->subtotal + (float)$invoice_value->total_tax;

    		/*get data for goods delivery detail*/
    		/*get item in invoices*/
    		$this->db->where('rel_id', $invoice_id);
    		$this->db->where('rel_type', 'invoice');
    		$arr_itemable = $this->db->get(db_prefix().'itemable')->result_array();

    		$arr_item_insert=[];
    		$arr_new_item_insert=[];
    		$index=0;

    		if(count($arr_itemable) > 0){
    			foreach ($arr_itemable as $key => $value) {
    				$commodity_code = $this->get_itemid_from_name($value['description']);
					//get_unit_id
    				$unit_id = $this->get_unitid_from_commodity_name($value['description']);
					//get warranty
    				$warranty = $this->get_warranty_from_commodity_name($value['description']);

    				if($commodity_code != 0){

    					$tax_rate = '';
    					$tax_name = '';
    					$str_tax_id = '';
    					$total_tax_rate = 0;
    					$commodity_name = wh_get_item_variatiom($commodity_code);

    					/*get tax item*/
    					$this->db->where('itemid', $value['id']);
    					$this->db->where('rel_id', $invoice_id);
    					$this->db->where('rel_type', "invoice");

    					$item_tax = $this->db->get(db_prefix().'item_tax')->result_array();

    					if(count($item_tax) > 0){
    						foreach ($item_tax as $tax_value) {
    							$tax_id = $this->get_tax_id_from_taxname_taxrate($tax_value['taxname'], $tax_value['taxrate']);

    							if(new_strlen($tax_rate) > 0){
    								$tax_rate .= '|'.$tax_value['taxrate'];
    							}else{
    								$tax_rate .= $tax_value['taxrate'];

    							}
    							$total_tax_rate += (float)$tax_value['taxrate'];

    							if(new_strlen($tax_name) > 0){
    								$tax_name .= '|'.$tax_value['taxname'];
    							}else{
    								$tax_name .= $tax_value['taxname'];

    							}

    							if($tax_id != 0){
    								if(new_strlen($str_tax_id) > 0){
    									$str_tax_id .= '|'.$tax_id;
    								}else{
    									$str_tax_id .= $tax_id;

    								}
    							}
    						}
    					}

    					// TODO
    					if((float)$value['qty'] > 0){

							$temporaty_quantity = $value['qty'];
    						$inventory_warehouse_by_commodity = $this->get_inventory_warehouse_by_commodity($commodity_code);

    						if(count($inventory_warehouse_by_commodity) > 0){
    						//have serial number
	    						foreach ($inventory_warehouse_by_commodity as $key => $inventory_warehouse) {
	    							if($temporaty_quantity > 0){
	    								$available_quantity = (float)$inventory_warehouse['inventory_number'];
	    								$warehouse_id = $inventory_warehouse['warehouse_id'];

	    								$temporaty_available_quantity = $available_quantity;
	    								$list_temporaty_serial_numbers = $this->get_list_temporaty_serial_numbers($commodity_code, $inventory_warehouse['warehouse_id'], $value['qty']);
	    								foreach ($list_temporaty_serial_numbers as $serial_value) {

											if($temporaty_available_quantity > 0){
												$temporaty_commodity_name = $commodity_name.' SN: '.$serial_value['serial_number'];
												$quantities = 1;

												$arr_new_item_insert[$index]['commodity_name'] = $temporaty_commodity_name;
												$arr_new_item_insert[$index]['commodity_code'] = $commodity_code;
												$arr_new_item_insert[$index]['quantities'] = $quantities + 0;
												$arr_new_item_insert[$index]['unit_price'] = $value['rate'] + 0;
												$arr_new_item_insert[$index]['tax_rate'] = $tax_rate;
												$arr_new_item_insert[$index]['tax_name'] = $tax_name;
												$arr_new_item_insert[$index]['tax_id'] = $str_tax_id;
												$arr_new_item_insert[$index]['unit_id'] = $unit_id;
												$arr_new_item_insert[$index]['guarantee_period'] = $warranty;
												$arr_new_item_insert[$index]['serial_number'] = $serial_value['serial_number'];
												$arr_new_item_insert[$index]['warehouse_id'] = $warehouse_id;
												$arr_new_item_insert[$index]['available_quantity'] = $temporaty_available_quantity;

												$arr_new_item_insert[$index]['total_money'] = (float)$quantities*(float)$value['rate'] + ((float)$total_tax_rate/100 * (float)$quantities*(float)$value['rate']);
												$arr_new_item_insert[$index]['total_after_discount'] = (float)$quantities*(float)$value['rate'] + ((float)$total_tax_rate/100 * (float)$quantities*(float)$value['rate']);


												$temporaty_quantity--;
												$temporaty_available_quantity--;
												$index ++;
												$inventory_warehouse_by_commodity[$key]['inventory_number'] = $temporaty_available_quantity;
											}
	    								}
	    							}
	    						}
	    						
	    						
	    						// don't have serial number
	    						if($temporaty_quantity > 0){
	    							$quantities = $temporaty_quantity;
	    							$available_quantity = 0;

	    							foreach ($inventory_warehouse_by_commodity as $key => $inventory_warehouse) {
	    								if((float)$inventory_warehouse['inventory_number'] > 0 && $temporaty_quantity > 0){

	    									$available_quantity = (float)$inventory_warehouse['inventory_number'];
	    									$warehouse_id = $inventory_warehouse['warehouse_id'];
	    									
	    									if ($temporaty_quantity >= $inventory_warehouse['inventory_number']) {
	    										$temporaty_quantity = (float) $temporaty_quantity - (float) $inventory_warehouse['inventory_number'];
	    										$quantities = (float)$inventory_warehouse['inventory_number'];
	    									} else {
	    										$quantities = (float)$temporaty_quantity;
	    										$temporaty_quantity = 0;
	    									}

	    									$arr_new_item_insert[$index]['commodity_name'] = $commodity_name;
	    									$arr_new_item_insert[$index]['commodity_code'] = $commodity_code;
	    									$arr_new_item_insert[$index]['quantities'] = $quantities + 0;
	    									$arr_new_item_insert[$index]['unit_price'] = $value['rate'] + 0;
	    									$arr_new_item_insert[$index]['tax_rate'] = $tax_rate;
	    									$arr_new_item_insert[$index]['tax_name'] = $tax_name;
	    									$arr_new_item_insert[$index]['tax_id'] = $str_tax_id;
	    									$arr_new_item_insert[$index]['unit_id'] = $unit_id;
	    									$arr_new_item_insert[$index]['guarantee_period'] = $warranty;
	    									$arr_new_item_insert[$index]['serial_number'] = '';
	    									$arr_new_item_insert[$index]['warehouse_id'] = $warehouse_id;
	    									$arr_new_item_insert[$index]['available_quantity'] = $available_quantity;

	    									$arr_new_item_insert[$index]['total_money'] = (float)$quantities*(float)$value['rate'] + ((float)$total_tax_rate/100 * (float)$quantities*(float)$value['rate']);
	    									$arr_new_item_insert[$index]['total_after_discount'] = (float)$quantities*(float)$value['rate'] + ((float)$total_tax_rate/100 * (float)$quantities*(float)$value['rate']);

	    									$index ++;
	    								}
	    							}
	    						}

	    					}else{
	    						// check is product is service
	    						$get_commodity = $this->get_commodity($commodity_code);
	    						if($get_commodity && $get_commodity->without_checking_warehouse == 1){

	    							$quantities = $temporaty_quantity;
	    						// invoice with service item

	    							$available_quantity = 0;
	    							$warehouse_id = '';



	    							$arr_new_item_insert[$index]['commodity_name'] = $commodity_name;
	    							$arr_new_item_insert[$index]['commodity_code'] = $commodity_code;
	    							$arr_new_item_insert[$index]['quantities'] = $quantities + 0;
	    							$arr_new_item_insert[$index]['unit_price'] = $value['rate'] + 0;
	    							$arr_new_item_insert[$index]['tax_rate'] = $tax_rate;
	    							$arr_new_item_insert[$index]['tax_name'] = $tax_name;
	    							$arr_new_item_insert[$index]['tax_id'] = $str_tax_id;
	    							$arr_new_item_insert[$index]['unit_id'] = $unit_id;
	    							$arr_new_item_insert[$index]['guarantee_period'] = $warranty;
	    							$arr_new_item_insert[$index]['serial_number'] = '';
	    							$arr_new_item_insert[$index]['warehouse_id'] = $warehouse_id;
	    							$arr_new_item_insert[$index]['available_quantity'] = $available_quantity;

	    							$arr_new_item_insert[$index]['total_money'] = (float)$quantities*(float)$value['rate'] + ((float)$total_tax_rate/100 * (float)$quantities*(float)$value['rate']);
	    							$arr_new_item_insert[$index]['total_after_discount'] = (float)$quantities*(float)$value['rate'] + ((float)$total_tax_rate/100 * (float)$quantities*(float)$value['rate']);

	    							$index ++;	
	    						}

	    					}

    					}

    				}


    			}
    		}

    		$data_insert=[];

    		$data_insert['goods_delivery'] = $data;
    		$data_insert['goods_delivery_detail'] = $arr_new_item_insert;

  			if(count($arr_new_item_insert) == 0){
    			return false;
  			}
  			
    		if($invoice_update != ''){
				//case invoice update
    			$status = $this->add_goods_delivery_from_invoice_update($invoice_id, $data_insert);

    		}else{
				//case invoice add
    			$status = $this->add_goods_delivery_from_invoice($data_insert, $invoice_id);

    		}

    		if($status){
    			return true;
    		}else{
    			return false;
    		}

    	}

    	return false;

    }


    /**
     * add goods delivery from invoice
     * @param array $data_insert 
     */
    public function add_goods_delivery_from_invoice($data_insert, $invoice_id ='')
    {
    	$results=0;
    	$flag_export_warehouse = 1;


    	$this->db->insert(db_prefix() . 'goods_delivery', $data_insert['goods_delivery']);
    	$insert_id = $this->db->insert_id();


    	if (isset($insert_id)) {

    		foreach ($data_insert['goods_delivery_detail'] as $delivery_detail_key => $delivery_detail_value) {
    			/*check export warehouse*/

				//checking Do not save the quantity of inventory with item
    			if($this->check_item_without_checking_warehouse($delivery_detail_value['commodity_code']) == true){

    				$inventory = $this->get_inventory_by_commodity($delivery_detail_value['commodity_code']);

    				if($inventory){
    					$inventory_number =  $inventory->inventory_number;

    					if((float)$inventory_number < (float)$delivery_detail_value['quantities'] ){
    						$flag_export_warehouse = 0;
    					}

    				}else{
    					$flag_export_warehouse = 0;
    				}

    			}


    			$delivery_detail_value['goods_delivery_id'] = $insert_id;
    			$this->db->insert(db_prefix() . 'goods_delivery_detail', $delivery_detail_value);
    			$insert_detail = $this->db->insert_id();

    			$results++;

    		}
    		$data_log = [];
    		$data_log['rel_id'] = $insert_id;
    		$data_log['rel_type'] = 'stock_export';
    		$data_log['staffid'] = get_staff_user_id();
    		$data_log['date'] = date('Y-m-d H:i:s');
    		$data_log['note'] = "stock_export";

    		$this->add_activity_log($data_log);

    		/*update next number setting*/
    		$this->update_inventory_setting(['next_inventory_delivery_mumber' =>  get_warehouse_option('next_inventory_delivery_mumber')+1]);
    		hooks()->do_action('after_add_goods_delivery_from_invoice', $insert_id);
    	}


		//check inventory warehouse => export warehouse
    	if($flag_export_warehouse == 1){
			//update approval
    		$data_update['approval'] = 1;
    		$this->db->where('id', $insert_id);
    		$this->db->update(db_prefix() . 'goods_delivery', $data_update);

    		if(isset($data_insert['goods_delivery']['customer_code']) && is_numeric($data_insert['goods_delivery']['customer_code'])){
				// create_shipment_from_delivery_note
				$this->create_shipment_from_delivery_note($insert_id);
			}

			//update log for table goods_delivery_invoices_pr_orders
    		$this->db->insert(db_prefix() . 'goods_delivery_invoices_pr_orders', [
    			"rel_id" => $insert_id,
    			"rel_type" => $invoice_id,
    			"type" => 'invoice',
    		]);

    		if(is_numeric($data_insert['goods_delivery']['customer_code'])){
    			$this->warehouse_check_update_shipment_when_delivery_note_approval($insert_id);
    		}

			//update shipment when delivery note approval
    		$this->check_update_shipment_when_delivery_note_approval($insert_id);

			//update history stock, inventoty manage after staff approved
    		$goods_delivery_detail = $this->get_goods_delivery_detail($insert_id);

    		foreach ($goods_delivery_detail as $goods_delivery_detail_value) {
				// add goods transaction detail (log) after update invetory number
				// 
				// check Without checking warehouse

    			if($this->check_item_without_checking_warehouse($goods_delivery_detail_value['commodity_code']) == true){
					// $this->add_inventory_manage($goods_delivery_detail_value, 2);
    				$this->add_inventory_from_invoices($goods_delivery_detail_value);
    			}

    		}

        	hooks()->do_action('after_wh_goods_delivery_added', $insert_id);
    	}


    	return $results > 0 ? true : false;


    }


    /**
     * add inventory from invoices
     * @param array $data 
     */
    public function add_inventory_from_invoices($data)
    {		

    	$available_quantity_n =0;

    	$available_quantity = $this->get_inventory_by_commodity($data['commodity_code']);
    	if($available_quantity){
    		$available_quantity_n = $available_quantity->inventory_number;
    	}

    	if(!is_numeric($data['warehouse_id'])){
    		$data['warehouse_id']='';

    	}else{
    		$this->db->where('warehouse_id', $data['warehouse_id']);
    	}
    		//status == 2 export
			//update
    	$this->db->where('commodity_id', $data['commodity_code']);
    	$this->db->order_by('id', 'ASC');

    	$result = $this->db->get('tblinventory_manage')->result_array();

    	$temp_quantities = $data['quantities'];

    	$expiry_date = '';
    	$lot_number = '';
    	$str_serial_number = '';
    	$data_transaction_detail = [];

    	foreach ($result as $result_value) {
    		$temp_purchase_price = $result_value['purchase_price'];
    		// get old quantity
    		$inventory_value = $this->get_quantity_inventory($result_value['warehouse_id'], $result_value['commodity_id']);
			$old_quantity =  0;
			if($inventory_value){
				$old_quantity = $inventory_value->inventory_number;
			}

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

						//update inventory
    				$this->db->where('id', $result_value['id']);
    				$this->db->update(db_prefix() . 'inventory_manage', [
    					'inventory_number' => 0,
    				]);
    				$old_quantity -= (float)$result_value['inventory_number'];
						//add warehouse id get from inventory manage
    				// if(new_strlen($data['warehouse_id']) != 0){
    				// 	$data['warehouse_id'] .= ','.$result_value['warehouse_id'];
    				// }else{
    				// 	$data['warehouse_id'] .= $result_value['warehouse_id'];

    				// }
    				//get serial number for deivery note
    				$serial_number_for_delivery_note = $this->get_serial_number_for_delivery_note($result_value['commodity_id'], $result_value['warehouse_id'], $result_value['id'], $result_value['inventory_number'], $data['serial_number'], $data['id'], $data['commodity_name']);

    				$data_transaction_detail[] = [
    					'goods_delivery_id' => $data['goods_delivery_id'],
    					'purchase_price' => $temp_purchase_price,
    					'unit_price' => $data['unit_price'],
    					'expiry_date' => $data['expiry_date'],
    					'lot_number' => $data['lot_number'],
    					'serial_number' => $serial_number_for_delivery_note,
    					'warehouse_id' => $data['warehouse_id'],
    					'commodity_code' => $data['commodity_code'],
    					'id' => $data['id'],
    					'quantities' => $result_value['inventory_number'],
    					'old_quantity' => $old_quantity,
    					'note' => $data['note'],
    				];

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


						//update inventory
    				$this->db->where('id', $result_value['id']);
    				$this->db->update(db_prefix() . 'inventory_manage', [
    					'inventory_number' => (float) $result_value['inventory_number'] - (float) $temp_quantities,
    				]);

    				$old_quantity -= (float)$temp_quantities;

						//add warehouse id get from inventory manage
    				// if(new_strlen($data['warehouse_id']) != 0){
    				// 	$data['warehouse_id'] .= ','.$result_value['warehouse_id'];
    				// }else{
    				// 	$data['warehouse_id'] .= $result_value['warehouse_id'];

    				// }

    				//get serial number for deivery note
    				$serial_number_for_delivery_note = $this->get_serial_number_for_delivery_note($result_value['commodity_id'], $result_value['warehouse_id'], $result_value['id'], $temp_quantities, $data['serial_number'], $data['id'], $data['commodity_name']);

    				$data_transaction_detail[] = [
    					'goods_delivery_id' => $data['goods_delivery_id'],
    					'purchase_price' => $temp_purchase_price,
    					'unit_price' => $data['unit_price'],
    					'expiry_date' => $data['expiry_date'],
    					'lot_number' => $data['lot_number'],
    					'serial_number' => $serial_number_for_delivery_note,
    					'warehouse_id' => $data['warehouse_id'],
    					'commodity_code' => $data['commodity_code'],
    					'id' => $data['id'],
    					'quantities' => $temp_quantities,
    					'old_quantity' => $old_quantity,
    					'note' => $data['note'],
    				];

    				$temp_quantities = 0;

    			}

    			if(new_strlen($serial_number_for_delivery_note) > 0){
    				if(new_strlen($str_serial_number) > 0){
    					$str_serial_number .= ','.$serial_number_for_delivery_note;
    				}else{
    					$str_serial_number .= $serial_number_for_delivery_note;
    				}
    			}



    		}

    	}

			//update good delivery detail
    	$this->db->where('id', $data['id']);
    	$this->db->update(db_prefix() . 'goods_delivery_detail', [
    		'expiry_date' => $expiry_date,
    		'lot_number' => $lot_number,
    		// 'warehouse_id' => $data['warehouse_id'],
    		// 'available_quantity' => $available_quantity_n,
    		'serial_number' => $str_serial_number,
    	]);

			//goods transaction detail log
    	$data['expiry_date'] = $expiry_date;
    	$data['lot_number'] = $lot_number;
    	$data['serial_number'] = $str_serial_number;
    	if(count($data_transaction_detail) > 0){
    		foreach ($data_transaction_detail as $value) {
    			$this->add_goods_transaction_detail($value, 2);
    		}
    	}

    	return true;


    }


    /**
     * copyinvoice
     * @param  integer $invoice_id 
     * @return array             
     */
    public function copy_invoice($invoice_id)
    {
    	
    	$this->db->where('id', $invoice_id);
    	$invoice_value = $this->db->get(db_prefix().'invoices')->row();
    	$data_insert=[];
    	$status = false;
    	$goods_delivery_row_template = '';
    	$goods_delivery_row_template = $this->warehouse_model->create_goods_delivery_row_template();
    	$additional_discount = 0;

    	$data['goods_delivery_code'] = $this->create_goods_delivery_code();
    	$data['customer_code'] 	= '';
    	$data['invoice_id'] 	= '';
    	$data['addedfrom'] 	= '';
    	$data['description'] 	= '';
    	$data['address'] 	= '';

    	if($invoice_value){
    		$status = true;;

    		/*get value for goods delivery*/

    		$data['goods_delivery_code'] = $this->create_goods_delivery_code();

    		if(!$this->check_format_date($invoice_value->date)){
    			$data['date_c'] = to_sql_date($invoice_value->date);
    		}else{
    			$data['date_c'] = $invoice_value->date;
    		}


    		if(!$this->check_format_date($invoice_value->date)){
    			$data['date_add'] = to_sql_date($invoice_value->date);

    		}else{
    			$data['date_add'] = $invoice_value->date;
    		}


    		$data['customer_code'] 	= $invoice_value->clientid;
    		$data['invoice_id'] 	= $invoice_id;
    		$data['addedfrom'] 	= $invoice_value->addedfrom;
    		$data['description'] 	= $invoice_value->adminnote;
    		$data['address'] 	= $invoice_value->shipping_street.', '.$invoice_value->shipping_city.', '.$invoice_value->shipping_state.', '.get_country_name($invoice_value->shipping_country);
    		$additional_discount = $invoice_value->discount_total;

 
    		/*get data for goods delivery detail*/
    		/*get item in invoices*/
    		$this->db->where('rel_id', $invoice_id);
    		$this->db->where('rel_type', 'invoice');
    		$arr_itemable = $this->db->get(db_prefix().'itemable')->result_array();

    		$arr_item_insert=[];
    		$total_money_before_tax = 0;
    		$total_money = 0;
    		$total_discount = 0;
    		$after_discount = 0;
    		$index=0;
    		$item_index=0;

    		if(count($arr_itemable) > 0){
    			foreach ($arr_itemable as $key => $value) {
    				$commodity_code = $this->get_itemid_from_name($value['description']);

    				if($commodity_code != 0){
    					/*get item from name*/

    					$tax_rate = null;
    					$tax_name = null;
    					$tax_id = null;
    					$tax_rate_value = 0;

    					$quantities =  round(((float)$value['qty'] - (float)$value['wh_delivered_quantity']) + 0, 5);
    					$unit_price = $value['rate'] + 0;

    					/*update after : goods_delivery_id, warehouse_id*/

    					/*get tax item*/
    					$this->db->where('itemid', $value['id']);
    					$this->db->where('rel_id', $invoice_id);
    					$this->db->where('rel_type', "invoice");

    					$item_tax = $this->db->get(db_prefix().'item_tax')->result_array();

    					if(count($item_tax) > 0){
    						foreach ($item_tax as $tax_value) {
    							$taxid = $this->get_tax_id_from_taxname_taxrate($tax_value['taxname'], $tax_value['taxrate']);
    							$tax_rate_value += (float)$tax_value['taxrate'];

    							if(new_strlen($tax_rate) > 0){
    								$tax_rate .= '|'.$tax_value['taxrate'];
    							}else{
    								$tax_rate .= $tax_value['taxrate'];
    							}

    							if(new_strlen($tax_name) > 0){
    								$tax_name .= '|'.$tax_value['taxname'];
    							}else{
    								$tax_name .= $tax_value['taxname'];
    							}

    							if($taxid != 0){
    								if(new_strlen($tax_id) > 0){
    									$tax_id .= '|'.$taxid;
    								}else{
    									$tax_id .= $taxid;
    								}
    							}

    						}
    					}

    					$index++;
    					$unit_name = $value['unit'];
    					$unit_id = wh_get_unit_id($value['unit']);
    					$taxname = '';
    					$expiry_date = null;
    					$lot_number = null;
    					$note = null;
    					$commodity_name = wh_get_item_variatiom($commodity_code);
    					$total_money = 0;
    					$total_after_discount = 0;
    					$guarantee_period = $this->get_warranty_from_commodity_name($commodity_code);

    					if((float)$tax_rate_value != 0){
    						$tax_money = (float)$unit_price * (float)$quantities * (float)$tax_rate_value / 100;
    						$total_money = (float)$unit_price * (float)$quantities + (float)$tax_money;
    						$amount = (float)$unit_price * (float)$quantities + (float)$tax_money;
    						$total_after_discount = (float)$unit_price * (float)$quantities + (float)$tax_money;
    					}else{
    						$total_money = (float)$unit_price * (float)$quantities;
    						$amount = (float)$unit_price * (float)$quantities;
    						$total_after_discount = (float)$unit_price * (float)$quantities;
    					}

    					$sub_total = (float)$unit_price * (float)$quantities;

    					$without_checking_warehouse = 0;
    					$get_commodity = $this->get_commodity($commodity_code);
    					if($get_commodity){
    						$without_checking_warehouse = $get_commodity->without_checking_warehouse;
    					}

    					if((float)$quantities > 0){
							$temporaty_quantity = $quantities;
    						$inventory_warehouse_by_commodity = $this->get_inventory_warehouse_by_commodity($commodity_code);

    						if(count($inventory_warehouse_by_commodity) > 0){
	    						foreach ($inventory_warehouse_by_commodity as $key => $inventory_warehouse) {
	    							if($temporaty_quantity > 0){
	    								$available_quantity = (float)$inventory_warehouse['inventory_number'];
	    								$warehouse_id = $inventory_warehouse['warehouse_id'];

	    								$temporaty_available_quantity = $available_quantity;
	    								$list_temporaty_serial_numbers = $this->get_list_temporaty_serial_numbers($commodity_code, $inventory_warehouse['warehouse_id'], $quantities);
	    								foreach ($list_temporaty_serial_numbers as $value) {

											if($temporaty_available_quantity > 0){
												$temporaty_commodity_name = $commodity_name.' SN: '.$value['serial_number'];
												$quantities = 1;
												$name = 'newitems['.$item_index.']';

												$goods_delivery_row_template .= $this->create_goods_delivery_row_template([], $name, $temporaty_commodity_name, $warehouse_id, $temporaty_available_quantity, $quantities, $unit_name, $unit_price, $taxname, $commodity_code, $unit_id, $tax_rate, '', '', '', $total_after_discount, $guarantee_period, $expiry_date, $lot_number, $note, $sub_total, $tax_name, $tax_id, 'undefined', true, false, $value['serial_number'], $without_checking_warehouse );
												$temporaty_quantity--;
												$temporaty_available_quantity--;
												$item_index ++;
												$inventory_warehouse_by_commodity[$key]['inventory_number'] = $temporaty_available_quantity;
											}
	    								}
	    							}

	    						}

	    						if($temporaty_quantity > 0){
	    							$quantities = $temporaty_quantity;
	    							$available_quantity = 0;
	    							$name = 'newitems['.$item_index.']';

	    							foreach ($inventory_warehouse_by_commodity as $key => $inventory_warehouse) {
	    								if((float)$inventory_warehouse['inventory_number'] > 0 && $temporaty_quantity > 0){

	    									$available_quantity = (float)$inventory_warehouse['inventory_number'];
	    									$warehouse_id = $inventory_warehouse['warehouse_id'];
	    									
	    									if ($temporaty_quantity >= $inventory_warehouse['inventory_number']) {
	    										$temporaty_quantity = (float) $temporaty_quantity - (float) $inventory_warehouse['inventory_number'];
	    										$quantities = (float)$inventory_warehouse['inventory_number'];
	    									} else {
	    										$quantities = (float)$temporaty_quantity;
	    										$temporaty_quantity = 0;
	    									}

	    									$goods_delivery_row_template .= $this->create_goods_delivery_row_template([], $name, $commodity_name, $warehouse_id, $available_quantity, $quantities, $unit_name, $unit_price, $taxname, $commodity_code, $unit_id, $tax_rate, '', '', '', $total_after_discount, $guarantee_period, $expiry_date, $lot_number, $note, $sub_total, $tax_name, $tax_id, 'undefined', true, false, '', $without_checking_warehouse);
	    									$item_index ++;
	    								}
	    							}
	    						}
	    					}else{
	    						// check is product is service
	    						if($without_checking_warehouse == 1){

	    							$name = 'newitems['.$item_index.']';
	    							$available_quantity = 0;
	    							$warehouse_id = '';

	    							$goods_delivery_row_template .= $this->create_goods_delivery_row_template([], $name, $commodity_name, $warehouse_id, $available_quantity, $quantities, $unit_name, $unit_price, $taxname, $commodity_code, $unit_id, $tax_rate, '', '', '', $total_after_discount, $guarantee_period, $expiry_date, $lot_number, $note, $sub_total, $tax_name, $tax_id, 'undefined', true, false, '', $without_checking_warehouse);
	    							$item_index ++;
	    						}
	    					}

    					}

    				}

    			}
    		}

    	}
    	$data['additional_discount'] 	= $additional_discount;
    	$data_insert['goods_delivery'] = $data;
    	$data_insert['goods_delivery_detail'] = $goods_delivery_row_template;
    	$data_insert['status'] = $status;

    	return $data_insert ;

    }


    /**
     * get commodity active
     * @return array 
     */
    public function get_commodity_active()
    {	

    	return  $this->db->query('select * from tblitems where active = 1 order by id desc')->result_array();

    }

    /**
     * get job position training de
     * @param  integer $id 
     * @return object      
     */
    public function get_item_longdescriptions($id){

    	$this->db->where('id', $id);
    	return  $this->db->get(db_prefix() . 'items')->row();

    }

    /**
     * revert goods receipt
     * @param  string $value 
     * @return [type]        
     */
    public function revert_goods_receipt($goods_receipt)
    {
    	$count_result=0;
    	$arr_goods_receipt_detail = $this->get_goods_receipt_detail($goods_receipt);
    	if(count($arr_goods_receipt_detail) > 0){
    		foreach ($arr_goods_receipt_detail as $goods_receipt_detail_value) {
    			$re_revert_inventory_manage = $this->revert_inventory_manage($goods_receipt_detail_value, 1);
    			if($re_revert_inventory_manage){
    				$count_result++;
    			}

    			$re_revert_goods_transaction_detail = $this->revert_goods_transaction_detail($goods_receipt_detail_value, 1);
    			if($re_revert_goods_transaction_detail){
    				$count_result++;
    			}

    			$this->revert_serial_number($goods_receipt_detail_value['commodity_code'], $goods_receipt_detail_value['warehouse_id'], $goods_receipt_detail_value['quantities'], $goods_receipt_detail_value['serial_number']);
    		}


    	}
    		//delete goods receipt (goods receipt, goods receipt detail)
    		$re_delete_goods_receipt =  $this->delete_goods_receipt($goods_receipt);
    		if($re_delete_goods_receipt){
    			$count_result++;
    		}

    	if($count_result > 0){
    		return true;
    	}else{
    		return true;
    	}
    	
    }


    /**
     * revert goods delivery
     * @param  integer $goods_delivery 
     * @return                  
     */
    public function revert_goods_delivery($goods_delivery)
    {	
    	$count_result=0;

    	$goods_delivery_value = $this->get_goods_delivery($goods_delivery);
    	
    	$invoice = false;
    	$pur_order = false;
    	if($goods_delivery_value){
    		if( ($goods_delivery_value->invoice_id != '') && ($goods_delivery_value->invoice_id != 0) ){
    			$invoice = true;
    		}
    		if( !is_null($goods_delivery_value->pr_order_id) && is_numeric($goods_delivery_value->pr_order_id) && $goods_delivery_value->pr_order_id != 0 ){
    			$pur_order = true;
    			$pur_order_id = $goods_delivery_value->pr_order_id;
    		}
    	}


    	$arr_goods_delivery_detail = $this->get_goods_delivery_detail($goods_delivery);
    	if(count($arr_goods_delivery_detail) > 0){
    		foreach ($arr_goods_delivery_detail as $goods_delivery_detail_value) {

    			$re_revert_inventory_manage = $this->revert_inventory_manage($goods_delivery_detail_value, 2, $invoice);
    			if($re_revert_inventory_manage){
    				$count_result++;
    			}

    			$re_revert_goods_transaction_detail = $this->revert_goods_transaction_detail($goods_delivery_detail_value, 2);
    			if($re_revert_goods_transaction_detail){
    				$count_result++;
    			}

    			$this->delivery_note_rollback_serial_number($goods_delivery_detail_value['commodity_code'], $goods_delivery_detail_value['warehouse_id'], $goods_delivery_detail_value['quantities'], $goods_delivery_detail_value['serial_number']);

    		}


    	}
    		//delete goods delivery (goods delivery, goods delivery detail)
    		$re_delete_goods_delivery = $this->delete_goods_delivery($goods_delivery);
    		if($re_delete_goods_delivery){
    			$count_result++;
    		}

    		// delete log for create delivery from Purchase order
    		if($pur_order){
    			$this->db->where('type', 'purchase_orders');
    			$this->db->where('rel_id', $goods_delivery);
    			if(isset($pur_order_id)){
    				$this->db->where('rel_type', $pur_order_id);
    			}
    			$this->db->delete(db_prefix() . 'goods_delivery_invoices_pr_orders');
    		}

    	if($count_result > 0){
    		return true;
    	}else{
    		return true;
    	}

    }


	/**
	 * revert inventory manage
	 * @param  string $value 
	 * @return [type]        
	 */
	public function revert_inventory_manage($data, $status, $invoice = false)
	{


		if ($status == 1) {
    	// status '1:Goods receipt note 2:Goods delivery note',
    		//revert goods receipt

			$this->db->where('warehouse_id', $data['warehouse_id']);
			$this->db->where('commodity_id', $data['commodity_code']);
			$this->db->where('expiry_date', $data['expiry_date']);
			$this->db->where('lot_number', $data['lot_number']);
			$total_rows = $this->db->count_all_results('tblinventory_manage');

			if ($total_rows > 0) {
				$status_insert_update = false;
			} else {
				$status_insert_update = true;
			}

			if (!$status_insert_update) {
				//update
				$this->db->where('warehouse_id', $data['warehouse_id']);
				$this->db->where('commodity_id', $data['commodity_code']);
				$this->db->where('expiry_date', $data['expiry_date']);
				$this->db->where('lot_number', $data['lot_number']);

				$result = $this->db->get('tblinventory_manage')->row();
				$inventory_number = $result->inventory_number;
				$update_id = $result->id;

				
				//Goods receipt
				$data_update['inventory_number'] = (float) $inventory_number - (float) $data['quantities'];
				if((float)$data_update['inventory_number'] < 0){
					$data_update['inventory_number'] = 0;
				}

				//update
				$this->db->where('id', $update_id);
				$this->db->update(db_prefix() . 'inventory_manage', $data_update);
				if ($this->db->affected_rows() > 0) {
					return true;
				}else{
					return false;
				}

			} else {
				//insert
				$data_insert['warehouse_id'] = $data['warehouse_id'];
				$data_insert['commodity_id'] = $data['commodity_code'];
				$data_insert['inventory_number'] = $data['quantities'];
				$data_insert['date_manufacture'] = $data['date_manufacture'];
				$data_insert['expiry_date'] = $data['expiry_date'];
				$data_insert['lot_number'] = $data['lot_number'];

				$this->db->insert(db_prefix() . 'inventory_manage', $data_insert);

				$insert_id = $this->db->insert_id();
				if($insert_id){
					return true;
				}else{
					return false;
				}


			}
			
		}else{
			$result_with_invoice=0;
			//status == 2 export
			//revert goods delivery
			if($invoice == false){
				$total_quatity_revert = $data['quantities'];
				//goods delivery not with invoice
				//with key lot number
				if( ($data['lot_number'] != '') && (isset($data['lot_number'])) ){

					$arr_lot_quantity = new_explode(',', $data['lot_number']);

					foreach ($arr_lot_quantity as $key => $value) {

						if($key%2 == 0){
							$lot_number = '';

							$lot_number = $value;
						}else{
							$quantities = '';

							$quantities = $value;

							$this->db->where('warehouse_id', $data['warehouse_id']);
							$this->db->where('commodity_id', $data['commodity_code']);
							$this->db->where('lot_number', $lot_number);
							$this->db->order_by('id', 'ASC');
							$result = $this->db->get('tblinventory_manage')->row();

							if($result){
								$total_quatity_revert = (float)$total_quatity_revert-(float)$result->inventory_number;

								$new_inventory = (float)$result->inventory_number+(float)$quantities;
								$this->db->where('id', $result->id);
								$this->db->update(db_prefix().'inventory_manage', ['inventory_number'=>$new_inventory]);

								if ($this->db->affected_rows() > 0) {
									$result_with_invoice++;
								}

							}



						}

					}



				}elseif( ($data['expiry_date'] != '') && (isset($data['expiry_date'])) ){
				//with key expiry date
					$arr_expiry_date = new_explode(',', $data['expiry_date']);

					foreach ($arr_expiry_date as $key => $value) {

						if($key%2 == 0){
							$expiry_date = '';

							$expiry_date = $value;
						}else{
							$quantities = '';

							$quantities = $value;

							$this->db->where('warehouse_id', $data['warehouse_id']);
							$this->db->where('commodity_id', $data['commodity_code']);
							$this->db->where('expiry_date', $expiry_date);
							$this->db->order_by('id', 'ASC');
							$result = $this->db->get('tblinventory_manage')->row();

							if($result){
								$total_quatity_revert = (float)$total_quatity_revert-(float)$result->inventory_number;

								$new_inventory = (float)$result->inventory_number+(float)$quantities;
								$this->db->where('id', $result->id);
								$this->db->update(db_prefix().'inventory_manage', ['inventory_number'=>$new_inventory]);

								if ($this->db->affected_rows() > 0) {
									$result_with_invoice++;
								}
								
							}

						}

					}

				}else{
				//no expiry date, lot number, add the first
				//
					$this->db->where('warehouse_id', $data['warehouse_id']);
					$this->db->where('commodity_id', $data['commodity_code']);
					$this->db->order_by('id', 'ASC');
					$result = $this->db->get('tblinventory_manage')->row();

					if($result){
						$total_quatity_revert = 0;

						$new_inventory = (float)$result->inventory_number+(float)$data['quantities'];
						$this->db->where('id', $result->id);
						$this->db->update(db_prefix().'inventory_manage', ['inventory_number'=>$new_inventory]);

						if ($this->db->affected_rows() > 0) {
							$result_with_invoice++;
							$total_quatity_revert = 0;

						}

						
					}


				}

				//check last update
				if($total_quatity_revert > 0){
					$this->db->where('warehouse_id', $data['warehouse_id']);
					$this->db->where('commodity_id', $data['commodity_code']);
					$this->db->order_by('id', 'ASC');
					$result = $this->db->get('tblinventory_manage')->row();

					if($result){

						$total_quatity_revert = (float)$result->inventory_number+(float)$total_quatity_revert;
						$this->db->where('id', $result->id);
						$this->db->update(db_prefix().'inventory_manage', ['inventory_number'=>$total_quatity_revert]);

						if ($this->db->affected_rows() > 0) {
							$result_with_invoice++;
						}
						$total_quatity_revert = 0;

					}


				}


			}else{
				//with invoice

				$total_quatity_revert = $data['quantities'];
				//goods delivery with invoice
				

				$arr_warehouse = new_explode(',', $data['warehouse_id']);
					//with key lot number
				if( ($data['lot_number'] != '') && (isset($data['lot_number'])) ){
					$index_warehouse = 0;

					$arr_lot_quantity = new_explode(',', $data['lot_number']);


					foreach ($arr_lot_quantity as $key => $value) {

						if($key%2 == 0){

							$lot_number = '';

							$lot_number = $value;
						}else{
							$quantities = '';

							$quantities = $value;

							if(count($arr_lot_quantity)/2 == count($arr_warehouse)){
								if(isset($arr_warehouse[$index_warehouse])){
									$this->db->where('warehouse_id', $arr_warehouse[$index_warehouse]);
									$index_warehouse++;

								}else{
									$this->db->where('warehouse_id', $arr_warehouse[0]);

								}
							}

							$this->db->where('commodity_id', $data['commodity_code']);
							$this->db->where('lot_number', $lot_number);
							$this->db->order_by('id', 'ASC');
							$result = $this->db->get('tblinventory_manage')->row();

							if($result){
								$total_quatity_revert = (float)$total_quatity_revert-(float)$quantities;

								$new_inventory = (float)$result->inventory_number+(float)$quantities;
								$this->db->where('id', $result->id);
								$this->db->update(db_prefix().'inventory_manage', ['inventory_number'=>$new_inventory]);

								if ($this->db->affected_rows() > 0) {
									$result_with_invoice++;
								}


							}



						}

					}

				}elseif( ($data['expiry_date'] != '') && (isset($data['expiry_date'])) ){
					$index_warehouse = 0;
					//with key expiry date
					$arr_expiry_date = new_explode(',', $data['expiry_date']);

					foreach ($arr_expiry_date as $key => $value) {


						if($key%2 == 0){
							$expiry_date = '';

							$expiry_date = $value;
						}else{
							$quantities = '';

							$quantities = $value;



							if(count($arr_expiry_date)/2 == count($arr_warehouse)){
								if(isset($arr_warehouse[$index_warehouse])){
									$this->db->where('warehouse_id', $arr_warehouse[$index_warehouse]);
									$index_warehouse++;

								}else{
									$this->db->where('warehouse_id', $arr_warehouse[0]);

								}
							}


							$this->db->where('commodity_id', $data['commodity_code']);
							$this->db->where('expiry_date', $expiry_date);
							$this->db->order_by('id', 'ASC');
							$result = $this->db->get('tblinventory_manage')->row();



							if($result){
								$total_quatity_revert = (float)$total_quatity_revert-(float)$quantities;

								$new_inventory = (float)$result->inventory_number+(float)$quantities;

								$this->db->where('id', $result->id);
								$this->db->update(db_prefix().'inventory_manage', ['inventory_number'=>$new_inventory]);

								if ($this->db->affected_rows() > 0) {
									$result_with_invoice++;
								}

							}

						}

					}

				}else{


					//no expiry date, lot number, add the first
					//
					$this->db->where('warehouse_id', $arr_warehouse[0]);
					$this->db->where('commodity_id', $data['commodity_code']);
					$this->db->order_by('id', 'ASC');
					$result = $this->db->get('tblinventory_manage')->row();

					if($result){
						$total_quatity_revert = 0;

						$new_inventory = (float)$result->inventory_number+(float)$data['quantities'];
						$this->db->where('id', $result->id);
						$this->db->update(db_prefix().'inventory_manage', ['inventory_number'=>$new_inventory]);

						if ($this->db->affected_rows() > 0) {
							$result_with_invoice++;
						}

					}


				}
				

				//check last update
				if($total_quatity_revert > 0){

					$this->db->where('warehouse_id', $arr_warehouse[0]);
					$this->db->where('commodity_id', $data['commodity_code']);
					$this->db->order_by('id', 'ASC');
					$result = $this->db->get('tblinventory_manage')->row();

					if($result){

						$total_quatity_revert = (float)$result->inventory_number+(float)$total_quatity_revert;
						$this->db->where('id', $result->id);
						$this->db->update(db_prefix().'inventory_manage', ['inventory_number'=>$total_quatity_revert]);

						$total_quatity_revert = 0;
						if ($this->db->affected_rows() > 0) {
							$result_with_invoice++;
						}


					}

				}


			}

			if($result_with_invoice > 0){
				return true;
			}
			return false;

		}


	}


    /**
     * revert_goods_transaction_detail
     * @param  string $value 
     * @return [type]        
     */
    public function revert_goods_transaction_detail($data, $status)
    {

    	if($status == 1){
    		$this->db->where('goods_receipt_id', $data['goods_receipt_id']);

    	}else{

    		$this->db->where('goods_receipt_id', $data['goods_delivery_id']);
    	}

    	$this->db->where('status', $status);

    	$this->db->delete(db_prefix() . 'goods_transaction_detail');
    	if ($this->db->affected_rows() > 0) {
    		return true;
    	}
    	return false;

    }

    /**
     * update goods delivery approval
     * @param  array  $data 
     * @param  boolean $id   
     *  
     */
    public function update_goods_delivery_approval($data, $id = false)
    {
    	$results = 0;

		if(isset($data['isedit'])){
			unset($data['isedit']);
		}

		if (isset($data['newitems'])) {
			$goods_deliveries = $data['newitems'];
			unset($data['newitems']);
		}

		if (isset($data['items'])) {
			$update_goods_deliveries = $data['items'];
			unset($data['items']);
		}
		if (isset($data['removed_items'])) {
			$remove_goods_deliveries = $data['removed_items'];
			unset($data['removed_items']);
		}

		$arr_serial_numbers = [];
		$arr_data_update = [];
		$goods_delivery_details = $this->get_goods_delivery_detail($data['id']);

		foreach ($goods_delivery_details as $value) {
			$arr_serial_numbers[$value['id']] = $value;
		}

		if(isset($update_goods_deliveries)){
			foreach ($update_goods_deliveries as $update_goods_delivery) {
				if(isset($arr_serial_numbers[$update_goods_delivery['id']])){
					if($arr_serial_numbers[$update_goods_delivery['id']]['serial_number'] != $update_goods_delivery['serial_number']){
						$arr_data_update[] = [
							'id' => $update_goods_delivery['id'],
							'commodity_name' => $update_goods_delivery['commodity_name'],
							'warehouse_id' => $update_goods_delivery['warehouse_id'],
							'serial_number' => $update_goods_delivery['serial_number'],
						];

						$this->db->where('commodity_id', $update_goods_delivery['commodity_code']);
						$this->db->where('warehouse_id', $update_goods_delivery['warehouse_id']);
						$this->db->where('serial_number', $update_goods_delivery['serial_number']);
						$this->db->update(db_prefix() . 'wh_inventory_serial_numbers', ['is_used' => 'yes']);
						if ($this->db->affected_rows() > 0) {
							$results++;
						}

						$this->db->where('commodity_id', $arr_serial_numbers[$update_goods_delivery['id']]['commodity_code']);
						$this->db->where('warehouse_id', $arr_serial_numbers[$update_goods_delivery['id']]['warehouse_id']);
						$this->db->where('serial_number', $arr_serial_numbers[$update_goods_delivery['id']]['serial_number']);
						$this->db->update(db_prefix() . 'wh_inventory_serial_numbers', ['is_used' => 'no']);
						if ($this->db->affected_rows() > 0) {
							$results++;
						}
					}
				}
			}
		}
		

		if(count($arr_data_update) > 0){
			$affected_rows = $this->db->update_batch(db_prefix().'goods_delivery_detail', $arr_data_update, 'id');
			if($affected_rows > 0){
				$results++;
			}
		}

		$goods_delivery_id = $data['id'];
		unset($data['id']);

		$this->db->where('id', $goods_delivery_id);
		$this->db->update(db_prefix() . 'goods_delivery', ['description' => $data['description']]);
		if ($this->db->affected_rows() > 0) {
			$results++;
		}

		if($results > 0){
			return true;
		}
		return false;
    }

	/**
	 * get unitid from commodity name
	 * @param  string $name 
	 * @return integer       
	 */
	public function get_unitid_from_commodity_name($name)
	{	
		$unit_id=0;

		$this->db->where('description', $name);
		$item_value = $this->db->get(db_prefix().'items')->row();

		if($item_value){
			$unit_id = $item_value->unit_id;
		}

		return $unit_id;

	}


    /**
     * get warranty from commodity name
     * @param  string $name 
     * @return string       
     */
    public function get_warranty_from_commodity_name($name)
    {	
    	$guarantee_new = '';

    	if(is_numeric($name)){
    		$this->db->where('id', $name);

    	}else{
    		$this->db->where('description', $name);
    	}
    	$item_value = $this->db->get(db_prefix().'items')->row();

    	if($item_value){

    		if(($item_value->guarantee != '') && (($item_value->guarantee != null)))
    			$guarantee_new = date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$item_value->guarantee.' months'));

    	}

    	return $guarantee_new;

    }


    /**
     * get unitid from commodity id
     * @param  integer $id 
     * @return integer     
     */
    public function get_unitid_from_commodity_id($id)
    {	
    	$unit_id=0;

    	$this->db->where('id', $id);
    	$item_value = $this->db->get(db_prefix().'items')->row();

    	if($item_value){
    		$unit_id = $item_value->unit_id;
    	}

    	return $unit_id;

    }

    /**
     * get warranty from commodity id
     * @param  integer $id 
     * @return string     
     */
    public function get_warranty_from_commodity_id($id)
    {	
    	$guarantee_new = '';

    	$this->db->where('id', $id);
    	$item_value = $this->db->get(db_prefix().'items')->row();

    	if($item_value){

    		if(($item_value->guarantee != '') && (($item_value->guarantee != null)))
    			$guarantee_new = date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$item_value->guarantee.' months'));

    	}

    	return $guarantee_new;

    }

    /**
     * get shipping address from invoice
     * @param  integer $invoice_id 
     * @return string             
     */
    public function get_shipping_address_from_invoice($invoice_id)
    {	
    	$address='';

    	$this->db->where('id', $invoice_id);
    	$invoice_value = $this->db->get(db_prefix().'invoices')->row();
    	if($invoice_value){
    		$address = $invoice_value->shipping_street;
    	}

    	return $address;

    }

    /**
     * check item without checking warehouse
     * @param  integer $id 
     * @return boolean     
     */
    public function check_item_without_checking_warehouse($id)
    {	
    	$status =  true;
    	$this->db->where('id', $id);
    	$item_value = $this->db->get(db_prefix().'items')->row();
    	if($item_value){
    		$checking_warehouse = $item_value->without_checking_warehouse;
    		if($checking_warehouse == 1){
    			$status = false;
    		}
    	}

    	return $status;


    }


    /**
     * import xlsx opening stock
     * @param  array $data 
     * @return integer       
     */
    public function import_xlsx_opening_stock($data) {


    	/*check update*/

    	$item = $this->db->query('select * from tblitems where commodity_code = "'.$data['commodity_code'].'"')->row();

    	if($item){
    		foreach ($data as $key => $data_value) {
    			if(!isset($data_value)){
    				unset($data[$key]);
    			}
    		}

    		$minimum_inventory = 0;
    		if(isset($data['minimum_inventory'])){
    			$minimum_inventory = $data['minimum_inventory'];
    			unset($data['minimum_inventory']);
    		}

			//update
    		$this->db->where('commodity_code', $data['commodity_code']);
    		$this->db->update(db_prefix() . 'items', $data);

    		/*check update or insert inventory min with commodity code*/
    		$this->db->where('commodity_code', $data['commodity_code']);
    		$check_inventory_min = $this->db->get(db_prefix().'inventory_commodity_min')->row();

    		if($check_inventory_min){
				//update
    			$this->db->where('commodity_code', $data['commodity_code']);
    			$this->db->update(db_prefix() . 'inventory_commodity_min', ['inventory_number_min' => $minimum_inventory]);

    		}else{
				//get commodity_id
    			$this->db->where('commodity_code', $data['commodity_code']);
    			$items = $this->db->get(db_prefix().'items')->row();

    			$item_id=0;
    			if($items){
    				$item_id = $items->id;
    			}

				//insert
    			$data_inventory_min['commodity_id'] = $item_id;
    			$data_inventory_min['commodity_code'] = $data['commodity_code'];
    			$data_inventory_min['commodity_name'] = $data['description'];
    			$data_inventory_min['inventory_number_min'] = $minimum_inventory;
    			$this->add_inventory_min($data_inventory_min);

    		}


    		if ($this->db->affected_rows() > 0) {
    			return true;
    		}
    	}else{
			//insert
    		$this->db->insert(db_prefix() . 'items', $data);
    		$insert_id = $this->db->insert_id();

    		/*add data tblinventory*/
    		if ($insert_id) {
    			$data_inventory_min['commodity_id'] = $insert_id;
    			$data_inventory_min['commodity_code'] = $data['commodity_code'];
    			$data_inventory_min['commodity_name'] = $data['description'];
    			$data_inventory_min['inventory_number_min'] = $data['minimum_inventory'];
    			$this->add_inventory_min($data_inventory_min);
    		}

    		return $insert_id;
    	}


    }


	/**
	 * caculator purchase price
	 * @return json 
	 */
	public function caculator_profit_rate_model($purchase_price, $sale_price)
	{

		$profit_rate = 0;

		/*type : 0 purchase price, 1: sale price*/
		$profit_type = get_warehouse_option('profit_rate_by_purchase_price_sale');
		$the_fractional_part = get_warehouse_option('warehouse_the_fractional_part');
		$integer_part = get_warehouse_option('warehouse_integer_part');

		$purchase_price = reformat_currency_j($purchase_price);
		$sale_price = reformat_currency_j($sale_price);


		switch ($profit_type) {
			case '0':
    			# Calculate the selling price based on the purchase price rate of profit
    			# sale price = purchase price * ( 1 + profit rate)

			if( ($purchase_price =='') || ($purchase_price == '0')|| ($purchase_price == 'null') ){
				$profit_rate = 0;

			}else{
				$profit_rate = (((float)$sale_price/(float)$purchase_price)-1)*100;

			}
			break;

			case '1':
    			# Calculate the selling price based on the selling price rate of profit
    			# sale price = purchase price / ( 1 - profit rate)

			$profit_rate = (1-((float)$purchase_price/(float)$sale_price))*100;

			break;

		}
		return $profit_rate;

	}


     /**
     * caculator sale price
     * @return float 
     */
     public function caculator_sale_price_model($purchase_price, $profit_rate)
     {

     	$sale_price = 0;

     	/*type : 0 purchase price, 1: sale price*/
     	$profit_type = get_warehouse_option('profit_rate_by_purchase_price_sale');
     	$the_fractional_part = get_warehouse_option('warehouse_the_fractional_part');
     	$integer_part = get_warehouse_option('warehouse_integer_part');

     	$profit_rate = reformat_currency_j($profit_rate);
     	$purchase_price = reformat_currency_j($purchase_price);

     	switch ($profit_type) {
     		case '0':
    			# Calculate the selling price based on the purchase price rate of profit
    			# sale price = purchase price * ( 1 + profit rate)
     		if( ($profit_rate =='') || ($profit_rate == '0')|| ($profit_rate == 'null') ){

     			$sale_price = (float)$purchase_price;
     		}else{
     			$sale_price = (float)$purchase_price*(1+((float)$profit_rate/100));

     		}
     		break;

     		case '1':
    			# Calculate the selling price based on the selling price rate of profit
    			# sale price = purchase price / ( 1 - profit rate)
     		if( ($profit_rate =='') || ($profit_rate == '0')|| ($profit_rate == 'null') ){

     			$sale_price = (float)$purchase_price;
     		}else{
     			$sale_price = (float)$purchase_price/(1-((float)$profit_rate/100));

     		}
     		break;

     	}

    	//round sale_price
     	$sale_price = round($sale_price, (int)$the_fractional_part);

     	if($integer_part != '0'){
     		$integer_part = 0 - (int)($integer_part);
     		$sale_price = round($sale_price, $integer_part);
     	}

     	return $sale_price;

     }

    /**
     * caculator purchase price model
     * @return float 
     */
    public function caculator_purchase_price_model($profit_rate, $sale_price)
    {

    	$purchase_price = 0;

    	/*type : 0 purchase price, 1: sale price*/
    	$profit_type = get_warehouse_option('profit_rate_by_purchase_price_sale');
    	$the_fractional_part = get_warehouse_option('warehouse_the_fractional_part');
    	$integer_part = get_warehouse_option('warehouse_integer_part');

    	$profit_rate = reformat_currency_j($profit_rate);
    	$sale_price = reformat_currency_j($sale_price);


    	switch ($profit_type) {
    		case '0':
    			# Calculate the selling price based on the purchase price rate of profit
    			# sale price = purchase price * ( 1 + profit rate)
    		if( ($profit_rate =='') || ($profit_rate == '0')|| ($profit_rate == 'null') ){
    			$purchase_price = (float)$sale_price;

    		}else{
    			$purchase_price = (float)$sale_price/(1+((float)$profit_rate/100));

    		}
    		break;

    		case '1':
    			# Calculate the selling price based on the selling price rate of profit
    			# sale price = purchase price / ( 1 - profit rate)
    		if( ($profit_rate =='') || ($profit_rate == '0')|| ($profit_rate == 'null') ){
    			$purchase_price = (float)$sale_price;
    		}else{

    			$purchase_price = (float)$purchase_price*(1-((float)$profit_rate/100));

    		}
    		break;
    		
    	}

    	//round purchase_price
    	$purchase_price = round($purchase_price, (int)$the_fractional_part);

    	if($integer_part != '0'){
    		$integer_part = 0 - (int)($integer_part);
    		$purchase_price = round($purchase_price, $integer_part);
    	}

    	return $purchase_price;
    }

    /**
     * get list item tags
     * @param  integer $id 
     * @return [type]     
     */
    public function get_list_item_tags($id){
    	$data=[];

    	/* get list tinymce start*/
    	$this->db->from(db_prefix() . 'taggables');
    	$this->db->join(db_prefix() . 'tags', db_prefix() . 'tags.id = ' . db_prefix() . 'taggables.tag_id', 'left');

    	$this->db->where(db_prefix() . 'taggables.rel_id', $id);
    	$this->db->where(db_prefix() . 'taggables.rel_type', 'item_tags');
    	$this->db->order_by('tag_order', 'ASC');

    	$item_tags = $this->db->get()->result_array();

    	$html_tags='';
    	foreach ($item_tags as $tag_value) {
    		$html_tags .='<li class="tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable tag-id-'.$tag_value['id'].' true" value="'.$tag_value['id'].'">
    		<span class="tagit-label">'.$tag_value['name'].'</span>
    		<a class="tagit-close">
    		<span class="text-icon">×</span>
    		<span class="ui-icon ui-icon-close"></span>
    		</a>
    		</li>';
    	}


    	$data['htmltag']    = $html_tags;  

    	return $data;

    }


    /**
     * delete tag item
     * @param  integer $tag_id 
     * @return [type]         
     */
    public function delete_tag_item($tag_id){
    	$count_af = 0;
    	/* delete taggables*/
    	$this->db->where(db_prefix() . 'taggables.tag_id', $tag_id);
    	$this->db->delete(db_prefix() . 'taggables');
    	if ($this->db->affected_rows() > 0) {
    		$count_af++;
    	}

    	/*delete tag*/
    	$this->db->where(db_prefix() . 'tags.id', $tag_id);
    	$this->db->delete(db_prefix() . 'tags');
    	if ($this->db->affected_rows() > 0) {
    		$count_af++;
    	}

    	return $count_af > 0 ?  true :  false;
    }


    /**
     * inventory_cancel_invoice
     * @param  integer $invoice_id 
     *              
     */
    public function inventory_cancel_invoice($invoice_id)
    {
    	/*get inventory delivery by invoice_id with status approval*/ 
    	$this->db->where('invoice_id', $invoice_id);
    	$this->db->where('approval', 1);
    	$arr_goods_delivery = $this->db->get(db_prefix().'goods_delivery')->result_array();

    	if(count($arr_goods_delivery) > 0){
    		foreach ($arr_goods_delivery as $value) {

    			$this->revert_goods_delivery($value['id']);

    		}
    	}else{
    		$this->db->where('invoice_id', $invoice_id);
    		$this->db->where('approval != ', 1);
    		$arr_goods_delivery = $this->db->get(db_prefix().'goods_delivery')->result_array();

    		if(count($arr_goods_delivery) > 0){
    			foreach ($arr_goods_delivery as $value) {

    				$this->delete_goods_delivery($value['id']);

    			}
    		}

    	}
    	return true;

    }


    /**
     * items send notification inventory warning
     * @return boolean        
     */
    public function items_send_notification_inventory_warning()
    {
    	$string_commodity_active = $this->array_commodity_id_active();

    	$string_notification='';
    	$arr_item=[];

    	$now = time();
    	$inventorys_cronjob_active = get_option('inventorys_cronjob_active');

    	$inventory_auto_operations_hour = get_option('inventory_auto_operations_hour');
    	$automatically_send_items_expired_before = get_option('automatically_send_items_expired_before');
    	$inventory_cronjob_notification_recipients = get_option('inventory_cronjob_notification_recipients');

    	/*get inventory stock, expiry date*/
    	$this->db->select('commodity_id, warehouse_id, sum(inventory_number) as inventory_number');
    	if(new_strlen($string_commodity_active) > 0){
    		$str_where = 'commodity_id IN ('.$string_commodity_active.')';
    		$this->db->where($str_where);
    	}

    	$this->db->group_by(array("commodity_id", "warehouse_id"));
    	$arr_inventory_stock=  $this->db->get(db_prefix().'inventory_manage')->result_array();
    	foreach ($arr_inventory_stock as $value) {
    		if(!in_array($value['commodity_id'], $arr_item)){

    			$link = 'warehouse/view_commodity_detail/' . $value['commodity_id'];

	            //get_inventory_min
    			$inventory_min= $this->get_inventory_min_cron($value['commodity_id']);

    			$sku_code='';
    			$warehouse_code='';

    			$item_value = $this->get_commodity($value['commodity_id']);
    			$warehouse_value = $this->get_warehouse($value['warehouse_id']);

    			if($item_value){
    				$sku_code .= $item_value->sku_code;
    			}

    			if($warehouse_value){
    				$warehouse_code .= $warehouse_value->warehouse_code;
    			}

    			if($inventory_min){
    				if($value['inventory_number'] <= $inventory_min->inventory_number_min){


    					$string_notification .='<a href="'.admin_url($link).'">'. _l('sku_code').': '.$sku_code.' - '._l('warehouse_code').': '.$warehouse_code.' - '._l('inventory_minimum').': '.$inventory_min->inventory_number_min.' - '._l('inventory_number_').': '.$value['inventory_number'].'</a>'.'<br/>';

    				}
    			}
    			/*check expiry date*/
    			$this->db->select('commodity_id, warehouse_id,expiry_date, sum(inventory_number) as inventory_number');
    			$this->db->where('commodity_id', $value['commodity_id']);
    			$this->db->group_by(array("commodity_id","expiry_date", "warehouse_id"));
    			$arr_expiry_date=  $this->db->get(db_prefix().'inventory_manage')->result_array();

    			if( count($arr_expiry_date) >0){
    				foreach ($arr_expiry_date as $ex_value) {
    					if($ex_value['expiry_date'] != null && $ex_value['expiry_date'] != ''){

    						$datediff  = strtotime($ex_value['expiry_date']) - strtotime(date('Y-m-d'));
    						$days_diff = floor($datediff / (60 * 60 * 24));

    						if ($days_diff <= $automatically_send_items_expired_before) {



    							$string_notification .= '<a href="'.admin_url($link).'">'. _l('sku_code').': '.$sku_code.' - '._l('warehouse_code').': '.$warehouse_code.' - '._l('exriry_date').': '.$ex_value['expiry_date'].'</a>'.'<br/>';

    						}

    					}
    				}
    			}





    		}
    		$arr_item[] = $value['commodity_id'];

    	}


    	if(new_strlen($inventory_cronjob_notification_recipients) != 0){

	        //send notification
    		if($string_notification != ''){
    			$data_send_mail=[];
    			$arr_staff_id = new_explode(',', $inventory_cronjob_notification_recipients);

    			foreach ($arr_staff_id as $staffid) {

    				$notified = add_notification([
    					'description' => _l('inventory_warning').$string_notification,
    					'touserid' => $staffid,
    					'additional_data' => serialize([
    						$string_notification,
    					]),
    				]);
    				if ($notified) {
    					pusher_trigger_notification([$staffid]);
    				}

    				/*send mail*/
    				$staff = $this->staff_model->get($staffid);
    				$staff->id=1;
    				if($staff){

    					$data_send_mail['string_notification']=$string_notification;
    					$data_send_mail['email']=$staff->email;
    					$data_send_mail['staff_name']=$staff->firstname.' '.$staff->lastname;


    					$template = mail_template('inventory_warning_to_staff', 'warehouse', array_to_object($data_send_mail));

    					$template->send();


    				}

    			}


    		}


    	}

         //send mail

    	return true;


    }

    /**
     * get item tag filter
     * @return array 
     */
    public function get_item_tag_filter()
    {
    	return $this->db->query('select DISTINCT id, name  FROM '.db_prefix().'taggables left join '.db_prefix().'tags on '.db_prefix().'taggables.tag_id =' .db_prefix().'tags.id where '.db_prefix().'taggables.rel_type = "item_tags"')->result_array();
    }

    /**
     * check inventory delivery voucher
     * @param  array $data 
     * @return string       
     */
    public function check_inventory_delivery_voucher($data)
    {

    	$flag_export_warehouse = 1;

    	$str_error='';

    	/*get goods delivery detail*/
    	$this->db->where('goods_delivery_id', $data['rel_id']);
    	$goods_delivery_detail = $this->db->get(db_prefix().'goods_delivery_detail')->result_array();


    	if (count($goods_delivery_detail) > 0) {

    		foreach ($goods_delivery_detail as $delivery_detail_key => $delivery_detail_value) {

    			$sku_code='';
    			$commodity_code='';

    			$item_value = $this->get_commodity($delivery_detail_value['commodity_code']);
    			if($item_value){
    				$sku_code .= $item_value->sku_code;
    				$commodity_code .= $item_value->commodity_code;
    			}

    			/*check export warehouse*/

				//checking Do not save the quantity of inventory with item
    			if($this->check_item_without_checking_warehouse($delivery_detail_value['commodity_code']) == true){

    				$inventory = $this->get_quantity_inventory($delivery_detail_value['warehouse_id'],$delivery_detail_value['commodity_code']);

    				if($inventory){
    					$inventory_number =  $inventory->inventory_number;

    					if((float)$inventory_number < (float)$delivery_detail_value['quantities'] ){
    						$str_error .= _l('item_has_sku_code'). $sku_code. ','. _l('commodity_code').' '. $commodity_code.':  '._l('not_enough_inventory');
    						$flag_export_warehouse =  0;
    					}

    				}else{
    					$str_error .=_l('item_has_sku_code'). $sku_code. ','. _l('commodity_code').' '. $commodity_code.':  '._l('not_enough_inventory');
    					$flag_export_warehouse =  0;

    				}

    			}


    		}

    	}

    	$result=[];
    	$result['str_error'] = $str_error;
    	$result['flag_export_warehouse'] = $flag_export_warehouse;

    	return $result ;


    }


    /**
     * update po detail quantity
     * @param  integer $po_id                
     * @param  array $goods_receipt_detail 
     *                        
     */
   public function update_po_detail_quantity($po_id, $goods_receipt_detail)
    {
    	$flag_update_status = true;

    	// old case
    	if(1==2){

    		$this->db->where('pur_order', $po_id);
    		$this->db->where('item_code', $goods_receipt_detail['commodity_code']);
    		$pur_order_detail = $this->db->get(db_prefix().'pur_order_detail')->result_array();
    	}

    	$where = 'select * from '.db_prefix().'pur_order_detail where ( CAST(wh_quantity_received as DECIMAL(15,2)) < quantity OR wh_quantity_received is null) AND pur_order = '.$po_id.' AND item_code = '.$goods_receipt_detail['commodity_code'];
    	$pur_order_detail = $this->db->query($where)->result_array();

    	$temp_receipt_qty = (float)$goods_receipt_detail['quantities'];
    	foreach ($pur_order_detail as $value) {
    	    if($temp_receipt_qty > 0 && ((float)$value['wh_quantity_received'] < (float)$value['quantity'])){

    	    	if ($temp_receipt_qty >= ((float)$value['quantity'] - (float)$value['wh_quantity_received'])) {
    	    		$temp_receipt_qty = (float) $temp_receipt_qty - ((float)$value['quantity'] - (float)$value['wh_quantity_received']);

    	    		//check quantity in purchase order detail = wh_quantity_received
    	    		$wh_quantity_received = (float)$value['quantity'];

    	    		if((float)$value['quantity'] > $wh_quantity_received){
    	    			$flag_update_status = false;
    	    		}

    	    	}else{

    	    		$wh_quantity_received = (float)($value['wh_quantity_received']) + (float)$temp_receipt_qty;

    	    		if((float)$value['quantity'] > $wh_quantity_received){
    	    			$flag_update_status = false;
    	    		}
    	    		$temp_receipt_qty = 0;
    	    	}

    	    	$this->db->where('id', $value['id']);
    	    	$this->db->update(db_prefix() . 'pur_order_detail', ['wh_quantity_received' => $wh_quantity_received]);

    	    	if ($this->db->affected_rows() > 0) {
    	    		$results_update = true;
    	    	} else {
    	    		$results_update = false;
    	    		$flag_update_status =  false;
    	    	}

    	    }
    	}


    	$results=[];
    	$results['flag_update_status']=$flag_update_status;
    	return $results;

    }

    /**
     * array commodity id active
     * @return array 
     */
    public function array_commodity_id_active()
    {	
    	$data=[];
    	$this->db->select('id');
    	$this->db->where('active', 1);
    	$arr_item = $this->db->get(db_prefix().'items')->result_array();

    	if(count($arr_item) > 0){
    		foreach ($arr_item as $value) {
    			array_push($data, $value['id']);
    		}
    	}
    	return implode(',',$data );

    }

    /**
     * get inventory min cron
     * @param  integer $id 
     * @return [type]     
     */
    public function get_inventory_min_cron($id) {

    	$this->db->where('commodity_id', $id);

    	return $this->db->get(db_prefix() . 'inventory_commodity_min')->row();


    }


	/**
	 * check lost adjustment before save
	 * @param  array $data 
	 * @return boolean       
	 */
	public function check_lost_adjustment_before_save($data)
	{


		$flag_check = 0;
		$str_error = '';

		foreach ($data['hot_delivery'] as $d) {
			// items: d[0], lot_number: d2, expiry_date: d3
			if($d[0]){

				$check = $this->check_commodity_exist_inventory($data['warehouse_id'], $d[0], $d[2], $d[3]);
				if ($check == true) {
					$flag_check = 1;

					$commodity_code='';
					$commodity_name='';

					$item_value = $this->get_commodity($d[0]);
					if($item_value){
						$commodity_code .= $item_value->commodity_code;
						$commodity_name .= $item_value->description;
					}

					$str_error .= 'Item :'.$commodity_code.'-'.$commodity_name.' with '. _l('lot_number').'-' .$d[2].','._l('expiry_date').'-'. $d[3]._l('not_in_inventory').'<br/>';
				} 

			}

		}

		$data=[];
		$data['flag_check']=$flag_check;
		$data['str_error']=$str_error;

		return $data;



	}


	/**
	 * update inventory setting
	 * @param  array $data 
	 * @return boolean       
	 */
	public function update_inventory_setting($data)
	{
		$affected_rows=0;
		foreach ($data as $key => $value) {

			$this->db->where('name',$key);
			$this->db->update(db_prefix() . 'options', [
				'value' => $value,
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
	 * invoice update delete goods delivery detail
	 * @param  integer $invoice_id 
	 * @return              
	 */
	public function invoice_update_delete_goods_delivery_detail($invoice_id)
	{
		/*get inventory delivery by invoice_id with status approval*/ 
		$this->db->where('invoice_id', $invoice_id);
		$this->db->where('approval', 1);
		$arr_goods_delivery = $this->db->get(db_prefix().'goods_delivery')->result_array();

		if(count($arr_goods_delivery) > 0){
			foreach ($arr_goods_delivery as $value) {

				$this->revert_goods_delivery_from_invoice_update($value['id']);

			}
		}else{
			$this->db->where('invoice_id', $invoice_id);
			$this->db->where('approval ', 0);
			$arr_goods_delivery = $this->db->get(db_prefix().'goods_delivery')->result_array();

			if(count($arr_goods_delivery) > 0){
				foreach ($arr_goods_delivery as $key => $value) {

					$this->db->where('goods_delivery_id', $value['id']);
					$this->db->delete(db_prefix() . 'goods_delivery_detail');


				}
			}

		}
		return true;

	}


    /**
     * revert goods delivery from invoice update
     * @param  integer $goods_delivery 
     * @return [type]                 
     */
    public function revert_goods_delivery_from_invoice_update($goods_delivery)
    {	
    	$count_result=0;

    	$goods_delivery_value = $this->get_goods_delivery($goods_delivery);
    	
    	$invoice = false;
    	if($goods_delivery_value){
    		if( ($goods_delivery_value->invoice_id != '') && ($goods_delivery_value->invoice_id != 0) ){
    			$invoice = true;
    		}
    	}

    	$arr_goods_delivery_detail = $this->get_goods_delivery_detail($goods_delivery);
    	if(count($arr_goods_delivery_detail) > 0){
    		foreach ($arr_goods_delivery_detail as $goods_delivery_detail_value) {

    			$re_revert_inventory_manage = $this->revert_inventory_manage($goods_delivery_detail_value, 2, $invoice);
    			if($re_revert_inventory_manage){
    				$count_result++;
    			}

    			$re_revert_goods_transaction_detail = $this->revert_goods_transaction_detail($goods_delivery_detail_value, 2);
    			if($re_revert_goods_transaction_detail){
    				$count_result++;
    			}

    		}

    		//delete goods delivery  detail not delete goods delivery
    		
    		$this->db->where('goods_delivery_id', $goods_delivery);
    		$this->db->delete(db_prefix() . 'goods_delivery_detail');

    		if($re_revert_goods_transaction_detail){
    			$count_result++;
    		}

    	}

    	if($count_result > 0){
    		return true;
    	}else{
    		return true;
    	}

    }


    /**
     * add_goods delivery from invoice update
     * @param array $data_insert 
     */
    public function add_goods_delivery_from_invoice_update($invoice_id, $data_insert)
    {

    	$flag_insert = 0;

    	/*get goods delivery from invoice*/
    	$this->db->where('invoice_id', $invoice_id);
    	$goods_delivery_update = $this->db->get(db_prefix().'goods_delivery')->result_array();
    	
    	if(count($goods_delivery_update) > 0){
    		foreach ($goods_delivery_update as $value) {

    			$this->db->where('id',$value['id']);

    			$this->db->update(db_prefix() . 'goods_delivery', [
    				'customer_code' => $data_insert['goods_delivery']['customer_code'],
    				'description' => $data_insert['goods_delivery']['description'],
    				'address' => $data_insert['goods_delivery']['address'],
    				'total_money' => $data_insert['goods_delivery']['total_money'],
    				'total_discount' => $data_insert['goods_delivery']['total_discount'],
    				'after_discount' => $data_insert['goods_delivery']['after_discount'],
    				'approval' => 0,
    			]);

    			$insert_id = $value['id'];
    		}
    		//update
    		
    	}else{

    		//insert new
    		$this->db->insert(db_prefix() . 'goods_delivery', $data_insert['goods_delivery']);
    		$insert_id = $this->db->insert_id();

    		$flag_insert = 1;

    	}

    	$results=0;
    	$flag_export_warehouse = 1;


    	if (isset($insert_id)) {

    		foreach ($data_insert['goods_delivery_detail'] as $delivery_detail_key => $delivery_detail_value) {
    			/*check export warehouse*/

				//checking Do not save the quantity of inventory with item
    			if($this->check_item_without_checking_warehouse($delivery_detail_value['commodity_code']) == true){

    				$inventory = $this->get_inventory_by_commodity($delivery_detail_value['commodity_code']);

    				if($inventory){
    					$inventory_number =  $inventory->inventory_number;

    					if((float)$inventory_number < (float)$delivery_detail_value['quantities'] ){
    						$flag_export_warehouse = 0;
    					}

    				}else{
    					$flag_export_warehouse = 0;
    				}

    			}


    			$delivery_detail_value['goods_delivery_id'] = $insert_id;
    			$this->db->insert(db_prefix() . 'goods_delivery_detail', $delivery_detail_value);
    			$insert_detail = $this->db->insert_id();

    			$results++;

    		}

    		$data_log = [];
    		$data_log['rel_id'] = $insert_id;
    		$data_log['rel_type'] = 'stock_export';
    		$data_log['staffid'] = get_staff_user_id();
    		$data_log['date'] = date('Y-m-d H:i:s');
    		$data_log['note'] = "stock_export";

    		$this->add_activity_log($data_log);

    		if($flag_insert == 1){
    			/*update next number setting*/
    			$this->update_inventory_setting(['next_inventory_delivery_mumber' =>  get_warehouse_option('next_inventory_delivery_mumber')+1]);

    		}
    	}


		//check inventory warehouse => export warehouse
    	if($flag_export_warehouse == 1){
			//update approval
    		$data_update['approval'] = 1;
    		$this->db->where('id', $insert_id);
    		$this->db->update(db_prefix() . 'goods_delivery', $data_update);

			//update history stock, inventoty manage after staff approved
    		$goods_delivery_detail = $this->get_goods_delivery_detail($insert_id);

    		foreach ($goods_delivery_detail as $goods_delivery_detail_value) {
				// add goods transaction detail (log) after update invetory number
				// 
				// check Without checking warehouse

    			if($this->check_item_without_checking_warehouse($goods_delivery_detail_value['commodity_code']) == true){
					// $this->add_inventory_manage($goods_delivery_detail_value, 2);
    				$this->add_inventory_from_invoices($goods_delivery_detail_value);
    			}

    		}

        	hooks()->do_action('after_wh_goods_delivery_added', $insert_id);

    	}


    	return $results > 0 ? true : false;


    }


    /**
     * add internal delivery
     * @param array $data 
     */
    public function add_internal_delivery($data) {
    	$internal_deliveries = [];
		if (isset($data['newitems'])) {
			$internal_deliveries = $data['newitems'];
			unset($data['newitems']);
		}

		unset($data['item_select']);
		unset($data['commodity_name']);
		unset($data['from_stock_name']);
		unset($data['to_stock_name']);
		unset($data['available_quantity']);
		unset($data['quantities']);
		unset($data['unit_price']);
		unset($data['note']);
		unset($data['unit_name']);
		unset($data['commodity_code']);
		unset($data['unit_id']);
		unset($data['into_money']);
		unset($data['serial_number']);

    	$check_appr = $this->get_approve_setting('4');
    	$data['approval'] = 0;
    	if ($check_appr && $check_appr != false) {
    		$data['approval'] = 0;
    	} else {
    		$data['approval'] = 1;
    	}

    	if(isset($data['edit_approval'])){
    		unset($data['edit_approval']);
    	}

    	if (isset($data['hot_internal_delivery'])) {
    		$hot_internal_delivery = $data['hot_internal_delivery'];
    		unset($data['hot_internal_delivery']);
    	}

    	$data['internal_delivery_code'] = $this->create_internal_delivery_code();

    	if(!$this->check_format_date($data['date_c'])){
    		$data['date_c'] = to_sql_date($data['date_c']);
    	}else{
    		$data['date_c'] = $data['date_c'];
    	}


    	if(!$this->check_format_date($data['date_add'])){
    		$data['date_add'] = to_sql_date($data['date_add']);
    	}else{
    		$data['date_add'] = $data['date_add'];
    	}

    	$data['datecreated'] = date('Y-m-d H:i:s');

    	$data['total_amount'] 	= reformat_currency_j($data['total_amount']);
    	$data['addedfrom'] = get_staff_user_id();

    	$this->db->insert(db_prefix() . 'internal_delivery_note', $data);
    	$insert_id = $this->db->insert_id();

    	/*update save note*/

    	if (isset($insert_id)) {

    		foreach ($internal_deliveries as $internal_delivery) {
				$internal_delivery['internal_delivery_id'] = $insert_id;

				unset($internal_delivery['order']);
				unset($internal_delivery['id']);
				unset($internal_delivery['unit_name']);

				$this->db->insert(db_prefix() . 'internal_delivery_note_detail', $internal_delivery);
				if($this->db->insert_id()){
					// $results++;
				}
			}

    		/*write log*/
    		$data_log = [];
    		$data_log['rel_id'] = $insert_id;
    		$data_log['rel_type'] = 'internal_delivery';
    		$data_log['staffid'] = get_staff_user_id();
    		$data_log['date'] = date('Y-m-d H:i:s');
    		$data_log['note'] = "internal_delivery_note";

    		$this->add_activity_log($data_log);

    		/*update next number setting*/
    		$this->update_inventory_setting(['next_internal_delivery_mumber' =>  get_warehouse_option('next_internal_delivery_mumber')+1]);


    	}

		//approval if not approval setting
    	if (isset($insert_id)) {
    		if ($data['approval'] == 1) {
    			$this->update_approve_request($insert_id, 4, 1);
    		}
    	}

    	return $insert_id > 0 ? $insert_id : false;

    }


    public function create_internal_delivery_code() {

    	$internal_delivery_code = get_warehouse_option('internal_delivery_number_prefix') . (get_warehouse_option('next_internal_delivery_mumber'));

    	return $internal_delivery_code;
    }


	/**
	 * get internal delivery
	 * @param  integer $id 
	 * @return array     
	 */
	public function get_internal_delivery($id) {
		if (is_numeric($id)) {
			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'internal_delivery_note')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblinternal_delivery_note')->result_array();
		}
	}

	/**
	 * get internal delivery detail
	 * @param  integer $id
	 * @return array
	 */
	public function get_internal_delivery_detail($id) {
		if (is_numeric($id)) {
			$this->db->where('internal_delivery_id', $id);

			return $this->db->get(db_prefix() . 'internal_delivery_note_detail')->result_array();
		}
		if ($id == false) {
			return $this->db->query('select * from tblinternal_delivery_note_detail')->result_array();
		}
	}


	/**
	 * delete internal delivery
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_internal_delivery($id) {
		$affected_rows = 0;

		$this->db->where('internal_delivery_id', $id);
		$this->db->delete(db_prefix() . 'internal_delivery_note_detail');
		if ($this->db->affected_rows() > 0) {

			$affected_rows++;
		}

		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'internal_delivery_note');
		if ($this->db->affected_rows() > 0) {

			$affected_rows++;
		}

		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * update internal delivery
	 * @param  array $data 
	 * @param  integer $id   
	 * @return boolean       
	 */
	public function update_internal_delivery($data, $id)
	{
		$affectedRows = 0;

		$internal_deliveries = [];
		$update_internal_deliveries = [];
		$remove_internal_deliveries = [];
		if(isset($data['isedit'])){
			unset($data['isedit']);
		}

		if (isset($data['newitems'])) {
			$internal_deliveries = $data['newitems'];
			unset($data['newitems']);
		}

		if (isset($data['items'])) {
			$update_internal_deliveries = $data['items'];
			unset($data['items']);
		}
		if (isset($data['removed_items'])) {
			$remove_internal_deliveries = $data['removed_items'];
			unset($data['removed_items']);
		}

		unset($data['item_select']);
		unset($data['commodity_name']);
		unset($data['from_stock_name']);
		unset($data['to_stock_name']);
		unset($data['available_quantity']);
		unset($data['quantities']);
		unset($data['unit_price']);
		unset($data['note']);
		unset($data['unit_name']);
		unset($data['commodity_code']);
		unset($data['unit_id']);
		unset($data['into_money']);
		unset($data['serial_number']);

		if (isset($data['hot_internal_delivery'])) {
			$hot_internal_delivery = $data['hot_internal_delivery'];
			unset($data['hot_internal_delivery']);
		}

		if(!$this->check_format_date($data['date_c'])){
			$data['date_c'] = to_sql_date($data['date_c']);
		}else{
			$data['date_c'] = $data['date_c'];
		}


		if(!$this->check_format_date($data['date_add'])){
			$data['date_add'] = to_sql_date($data['date_add']);
		}else{
			$data['date_add'] = $data['date_add'];
		}

		$data['datecreated'] = date('Y-m-d H:i:s');

		$data['total_amount'] 	= reformat_currency_j($data['total_amount']);
		$data['addedfrom'] = get_staff_user_id();
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'internal_delivery_note', $data);
		if ($this->db->affected_rows() > 0) {
			$affectedRows++;
		}

		// update internal detail
		foreach ($update_internal_deliveries as $internal_delivery) {
			unset($internal_delivery['order']);
			unset($internal_delivery['unit_name']);

			$this->db->where('id', $internal_delivery['id']);
			if ($this->db->update(db_prefix() . 'internal_delivery_note_detail', $internal_delivery)) {
				$affectedRows++;
			}
		}

		// delete internal detail
		foreach ($remove_internal_deliveries as $internal_detail_id) {
			$this->db->where('id', $internal_detail_id);
			if ($this->db->delete(db_prefix() . 'internal_delivery_note_detail')) {
				$affectedRows++;
			}
		}

		// Add internal_delivery
		foreach ($internal_deliveries as $internal_delivery) {
			$internal_delivery['internal_delivery_id'] = $id;
			unset($internal_delivery['order']);
			unset($internal_delivery['id']);
			unset($internal_delivery['unit_name']);

			$this->db->insert(db_prefix() . 'internal_delivery_note_detail', $internal_delivery);
			if($this->db->insert_id()){
				$affectedRows++;
			}
		}

		if ($affectedRows > 0) {


			return true;
		}

		return false;
	}

    /**
     * approval internal delivery detail
     * @param  array $data 
     * @return [type]       
     */
    public function approval_internal_delivery_detail($data)
    {

    	/*step 1 inventory delivery note*/
    	$this->db->where('warehouse_id', $data['from_stock_name']);
    	$this->db->where('commodity_id', $data['commodity_code']);
    	$this->db->order_by('id', 'ASC');
    	$result = $this->db->get('tblinventory_manage')->result_array();

    	$temp_quantities = $data['quantities'];
    	$old_quantities = $data['available_quantity'];

    	$expiry_date = '';
    	$lot_number = '';

    	$data_log=[];

    	foreach ($result as $result_value) {
    		if (($result_value['inventory_number'] != 0) && ($temp_quantities != 0)) {

    			if ($temp_quantities >= $result_value['inventory_number']) {
    				$temp_quantities = (float) $temp_quantities - (float) $result_value['inventory_number'];


						//update inventory
    				$this->db->where('id', $result_value['id']);
    				$this->db->update(db_prefix() . 'inventory_manage', [
    					'inventory_number' => 0,
    				]);

    				// get serial number
    				$serial_number_for_internal_delivery_note = $this->get_serial_number_for_internal_delivery_note($data['commodity_code'], $data['from_stock_name'], $result_value['id'], $result_value['inventory_number'], $data['serial_number'], $data['id'], $data['commodity_name']);

						//import warehouse
    				$data_inventory_received=[];
    				$data_inventory_received['lot_number'] 		= $result_value['lot_number'];
    				$data_inventory_received['expiry_date']		= $result_value['expiry_date'];
    				$data_inventory_received['warehouse_id']	= $data['to_stock_name'];
    				$data_inventory_received['commodity_code']	= $data['commodity_code'];
    				$data_inventory_received['quantities']		= $result_value['inventory_number'];
    				$data_inventory_received['date_manufacture']		= $result_value['date_manufacture'];
    				$data_inventory_received['serial_number']		= $serial_number_for_internal_delivery_note;
    				$data_inventory_received['unit_price']		= $result_value['purchase_price'];

    				$this->add_inventory_manage($data_inventory_received, 1);

    					//log data
    				array_push($data_log, [
    					'goods_receipt_id' 	=> $data['internal_delivery_id'],
    					'goods_id' 			=> $data['id'],
    					'old_quantity' 			=> $old_quantities,
    					'quantity' 			=> $result_value['inventory_number'],
    					'date_add' 			=> date('Y-m-d H:i:s'),
    					'commodity_id' 		=> $data['commodity_code'],
    					'note' 				=> $data['note'],
    					'status' 			=> 4,
    					'purchase_price' 	=> $data['unit_price'],
    					'expiry_date' 		=> $result_value['expiry_date'],
    					'lot_number' 		=> $result_value['lot_number'],
    					'from_stock_name' 	=> $data['from_stock_name'],
    					'to_stock_name' 	=> $data['to_stock_name'],
    					'serial_number' 	=> $serial_number_for_internal_delivery_note,
    				]);

    				$old_quantities = (float)$old_quantities - (float)$result_value['inventory_number'];

    			} else {

						//update inventory
    				$this->db->where('id', $result_value['id']);
    				$this->db->update(db_prefix() . 'inventory_manage', [
    					'inventory_number' => (float) $result_value['inventory_number'] - (float) $temp_quantities,
    				]);

    				// get serial number
    				$serial_number_for_internal_delivery_note = $this->get_serial_number_for_internal_delivery_note($data['commodity_code'], $data['from_stock_name'], $result_value['id'], $temp_quantities, $data['serial_number'], $data['id'], $data['commodity_name']);

						//import warehouse
    				$data_inventory_received=[];
    				$data_inventory_received['lot_number'] 		= $result_value['lot_number'];
    				$data_inventory_received['expiry_date']		= $result_value['expiry_date'];
    				$data_inventory_received['warehouse_id']	= $data['to_stock_name'];
    				$data_inventory_received['commodity_code']	= $data['commodity_code'];
    				$data_inventory_received['quantities']		= $temp_quantities;
    				$data_inventory_received['date_manufacture']		= $result_value['date_manufacture'];
    				$data_inventory_received['serial_number']		= $serial_number_for_internal_delivery_note;
    				$data_inventory_received['unit_price']		= $result_value['purchase_price'];

    				$this->add_inventory_manage($data_inventory_received, 1);

    				//log data
    				array_push($data_log, [
    					'goods_receipt_id' 	=> $data['internal_delivery_id'],
    					'goods_id' 			=> $data['id'],
    					'old_quantity' 		=> $old_quantities,
    					'quantity' 			=> $temp_quantities,
    					'date_add' 			=> date('Y-m-d H:i:s'),
    					'commodity_id' 		=> $data['commodity_code'],
    					'note' 				=> $data['note'],
    					'status' 			=> 4,
    					'purchase_price' 	=> $data['unit_price'],
    					'expiry_date' 		=> $result_value['expiry_date'],
    					'lot_number' 		=> $result_value['lot_number'],
    					'from_stock_name' 	=> $data['from_stock_name'],
    					'to_stock_name' 	=> $data['to_stock_name'],
    					'serial_number' 	=> $serial_number_for_internal_delivery_note,
    				]);

    				$old_quantities = (float)$old_quantities - (float)$temp_quantities;

    				$temp_quantities = 0;

    			}

    		}

    	}

			//goods transaction detail log
    	$this->db->insert_batch(db_prefix(). 'goods_transaction_detail', $data_log);

    	return true;

    }



    public function check_internal_delivery_note_send_request($data)
    {


    	$flag_internal_delivery_warehouse = 1;

    	$str_error='';

    	/*get goods delivery detail*/
    	$this->db->where('internal_delivery_id', $data['rel_id']);
    	$internal_delivery_detail = $this->db->get(db_prefix().'internal_delivery_note_detail')->result_array();


    	if (count($internal_delivery_detail) > 0) {

    		foreach ($internal_delivery_detail as $delivery_detail_key => $delivery_detail_value) {

    			$sku_code='';
    			$commodity_code='';

    			$item_value = $this->get_commodity($delivery_detail_value['commodity_code']);
    			if($item_value){
    				$sku_code .= $item_value->sku_code;
    				$commodity_code .= $item_value->commodity_code;
    			}

    			/*check internal delivery note warehouse*/

    			$inventory = $this->get_quantity_inventory($delivery_detail_value['from_stock_name'], $delivery_detail_value['commodity_code']);

    			if($inventory){
    				$inventory_number =  $inventory->inventory_number;

    				if((float)$inventory_number < (float)$delivery_detail_value['quantities'] ){
    					$str_error .= _l('item_has_sku_code'). $sku_code. ','. _l('commodity_code').' '. $commodity_code.':  '._l('not_enough_inventory');
    					$flag_internal_delivery_warehouse =  0;
    				}

    			}else{
    				$str_error .=_l('item_has_sku_code'). $sku_code. ','. _l('commodity_code').' '. $commodity_code.':  '._l('not_enough_inventory');
    				$flag_internal_delivery_warehouse =  0;

    			}

    		}

    	}

    	$result=[];
    	$result['str_error'] = $str_error;
    	$result['flag_internal_delivery_warehouse'] = $flag_internal_delivery_warehouse;

    	return $result ;


    }


    /**
     * add one warehouse
     * @param [type] $data 
     */
    public function add_one_warehouse($data) {

    	$option = 'off';
    	if (isset($data['display'])) {
    		$option = $data['display'];
    		unset($data['display']);
    	}

    	if ($option == 'on') {
    		$data['display'] = 1;
    	} else {
    		$data['display'] = 0;
    	}

    	if(isset($data['assign_to_staffs'])){
    		$assign_to_staffs = $data['assign_to_staffs'];
    		unset($data['assign_to_staffs']);
    	}

    	$hide_warehouse_when_out_of_stock = 'off';
    	if (isset($data['hide_warehouse_when_out_of_stock'])) {
    		$hide_warehouse_when_out_of_stock = $data['hide_warehouse_when_out_of_stock'];
    		unset($data['hide_warehouse_when_out_of_stock']);
    	}

    	if ($hide_warehouse_when_out_of_stock == 'on') {
    		$data['hide_warehouse_when_out_of_stock'] = 1;
    	} else {
    		$data['hide_warehouse_when_out_of_stock'] = 0;
    	}

    	if (isset($data['custom_fields'])) {
    		$custom_fields = $data['custom_fields'];
    		unset($data['custom_fields']);
    	}

    	$this->db->insert(db_prefix() . 'warehouse', $data);
    	$insert_id = $this->db->insert_id();

    	if ($insert_id) {
    		if (isset($custom_fields)) {
    			handle_custom_fields_post($insert_id, $custom_fields);
    		}

    		// assigned warehouse to staffs
    		if(is_admin() && isset($assign_to_staffs)){
    			$this->assignWarehouseToStaffs($insert_id, $assign_to_staffs);
    		}
    		return $insert_id;
    	}


    	return false;
    }

	/**
	 * update color
	 * @param  array $data
	 * @param  integer $id
	 * @return boolean
	 */
	public function update_one_warehouse($data, $id) {
		$option = 'off';
		if (isset($data['display'])) {
			$option = $data['display'];
			unset($data['display']);
		}

		if ($option == 'on') {
			$data['display'] = 1;
		} else {
			$data['display'] = 0;
		}

		$assign_to_staffs = [];
		if(isset($data['assign_to_staffs'])){
			$assign_to_staffs = $data['assign_to_staffs'];
			unset($data['assign_to_staffs']);
		}

		$hide_warehouse_when_out_of_stock = 'off';
		if (isset($data['hide_warehouse_when_out_of_stock'])) {
			$hide_warehouse_when_out_of_stock = $data['hide_warehouse_when_out_of_stock'];
			unset($data['hide_warehouse_when_out_of_stock']);
		}

		if ($hide_warehouse_when_out_of_stock == 'on') {
			$data['hide_warehouse_when_out_of_stock'] = 1;
		} else {
			$data['hide_warehouse_when_out_of_stock'] = 0;
		}

		if (isset($data['custom_fields'])) {
			$custom_fields = $data['custom_fields'];
			unset($data['custom_fields']);
		}

		$affectedRows = 0;

		$this->db->where('warehouse_id', $id);
		$this->db->update(db_prefix() . 'warehouse', $data);

		if ($this->db->affected_rows() > 0) {
			$affectedRows++;
		}

		if (isset($custom_fields)) {
			if (handle_custom_fields_post($id, $custom_fields)) {
				$affectedRows++;
			}
		}

		// assigned warehouse to staffs
		if(is_admin() && isset($assign_to_staffs)){
			$this->assignWarehouseToStaffs($id, $assign_to_staffs);
		}

		if ($affectedRows > 0) {
			return true;
		}

		return true;
	}


	/**
	 * get inventory by warehouse
	 * @param  integer $warehouse_id 
	 * @return array               
	 */
	public function get_inventory_by_warehouse($warehouse_id) {
		
		$sql = 'SELECT sum(inventory_number) as inventory_number, commodity_id, warehouse_id FROM '.db_prefix().'inventory_manage
		where '.db_prefix().'inventory_manage.warehouse_id = '.$warehouse_id.' AND
		 commodity_id not in (
			SELECT distinct parent_id FROM '.db_prefix().'items
			where parent_id is not null and parent_id != 0 )
		group by commodity_id
		order by '.db_prefix().'inventory_manage.commodity_id asc';

		return $this->db->query($sql)->result_array();

	}


	/**
	 * get invoices goods delivery
	 * @return mixed 
	 */
	public function get_invoices_goods_delivery($type)
	{
		$this->db->where('type', $type);
		$goods_delivery_invoices_pr_orders = $this->db->get(db_prefix().'goods_delivery_invoices_pr_orders')->result_array();

		$array_id = [];
		foreach ($goods_delivery_invoices_pr_orders as $value) {
			array_push($array_id, $value['rel_type']);
		}

		return $array_id;

	}


    /**
	 * get purchase request
	 * @param  integer $pur_order
	 * @return array
	 */
    public function goods_delivery_get_pur_order($pur_order) {

    	$arr_pur_resquest = [];

    	$subtotal = 0;
    	$total_discount = 0;
    	$total_payment = 0;
    	$total_tax_money = 0;
    	$additional_discount = 0;
    	$pur_total_money = 0;
    	$goods_delivery_row_template = '';
    	$goods_delivery_row_template = $this->warehouse_model->create_goods_delivery_row_template();


    	$this->db->select('item_code as commodity_code, '.db_prefix().'items.description, ' .db_prefix().'items.unit_id , unit_price as rate, quantity as quantities, '.db_prefix().'pur_order_detail.tax as tax_id, '.db_prefix().'pur_order_detail.total as total_money, '.db_prefix().'pur_order_detail.total, '.db_prefix().'pur_order_detail.discount_% as discount, '.db_prefix().'pur_order_detail.discount_money, '.db_prefix().'pur_order_detail.total_money as total_after_discount, '.db_prefix().'items.guarantee, '.db_prefix().'pur_order_detail.tax_rate');
    	$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'pur_order_detail.item_code = ' . db_prefix() . 'items.id', 'left');
    	$this->db->where(db_prefix().'pur_order_detail.pur_order = '. $pur_order);
    	$arr_results = $this->db->get(db_prefix() . 'pur_order_detail')->result_array();

    	$this->db->where('id', $pur_order);
    	$get_pur_order = $this->db->get(db_prefix() . 'pur_orders')->row();


    	$index=0;
    	$status = false;
    	$item_index=0;

    	if(count($arr_results) > 0){
    		$status = false;

    		foreach ($arr_results as $key => $value) {
    			$tax_rate = null;
    			$tax_name = null;
    			$tax_id = null;
    			$tax_rate_value = 0;
    			$pur_total_money += (float)$value['total_after_discount'];

    			/*caculatoe guarantee*/
    			$guarantee_period = '';
    			if($value){
    				if(($value['guarantee'] != '') && (($value['guarantee'] != null)))
    					$guarantee_period = date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$value['guarantee'].' months'));
    			}


    			/*caculator subtotal*/
    			/*total discount*/
    			/*total payment*/

    			$total_goods_money = (float)$value['quantities']*(float)$value['rate'];

					//get tax value
    			if($value['tax_id'] != null && $value['tax_id'] != '') {
    				$tax_id = $value['tax_id'];
    				$arr_tax = new_explode('|', $value['tax_id']);
    				$arr_tax_rate = new_explode('|', $value['tax_rate']);

    				foreach ($arr_tax as $key => $tax_id) {
    					$get_tax_name = $this->get_tax_name($tax_id);

    					if(isset($arr_tax_rate[$key])){
    						$get_tax_rate = $arr_tax_rate[$key];
    					}else{
    						$tax = $this->get_taxe_value($tax_id);
    						$get_tax_rate = (float)$tax->taxrate;
    					}

    					$tax_rate_value += (float)$get_tax_rate;

    					if(new_strlen($tax_rate) > 0){
    						$tax_rate .= '|'.$get_tax_rate;
    					}else{
    						$tax_rate .= $get_tax_rate;
    					}

    					if(new_strlen($tax_name) > 0){
    						$tax_name .= '|'.$get_tax_name;
    					}else{
    						$tax_name .= $get_tax_name;
    					}


    				}
    			}

    			
    			$index++;
    			$unit_name = wh_get_unit_name($value['unit_id']);
    			$unit_id = $value['unit_id'];
    			$taxname = '';
    			$expiry_date = null;
    			$lot_number = null;
    			$note = null;
    			$commodity_name = wh_get_item_variatiom($value['commodity_code']);
    			$total_money = 0;
    			$total_after_discount = 0;
    			$quantities = (float)$value['quantities'];
    			$unit_price = (float)$value['rate'];
    			$commodity_code = $value['commodity_code'];
    			$discount_money = $value['discount_money'];

    			if((float)$tax_rate_value != 0){
    				$tax_money = (float)$unit_price * (float)$quantities * (float)$tax_rate_value / 100;
    				$total_money = (float)$unit_price * (float)$quantities + (float)$tax_money;
    				$amount = (float)$unit_price * (float)$quantities + (float)$tax_money;
    				$discount_money = (float)$amount*(float)$value['discount']/100;

    				$total_after_discount = (float)$unit_price * (float)$quantities + (float)$tax_money - (float)$discount_money;
    			}else{
    				$total_money = (float)$unit_price * (float)$quantities;
    				$amount = (float)$unit_price * (float)$quantities;
    				$discount_money = (float)$amount*(float)$value['discount']/100;

    				$total_after_discount = (float)$unit_price * (float)$quantities - (float)$discount_money;
    			}

    			$sub_total = (float)$unit_price * (float)$quantities;

    			if((float)$quantities > 0){
    				$temporaty_quantity = $quantities;
    				$inventory_warehouse_by_commodity = $this->get_inventory_warehouse_by_commodity($commodity_code);

    				foreach ($inventory_warehouse_by_commodity as $key => $inventory_warehouse) {
    					if($temporaty_quantity > 0){
    						$available_quantity = (float)$inventory_warehouse['inventory_number'];
    						$warehouse_id = $inventory_warehouse['warehouse_id'];

    						$temporaty_available_quantity = $available_quantity;
    						$list_temporaty_serial_numbers = $this->get_list_temporaty_serial_numbers($commodity_code, $inventory_warehouse['warehouse_id'], $quantities);
    						foreach ($list_temporaty_serial_numbers as $value) {

    							if($temporaty_available_quantity > 0){
    								$temporaty_commodity_name = $commodity_name.' SN: '.$value['serial_number'];
    								$quantities = 1;
    								$name = 'newitems['.$item_index.']';

    								$goods_delivery_row_template .= $this->create_goods_delivery_row_template([], $name, $temporaty_commodity_name, $warehouse_id, $temporaty_available_quantity, $quantities, $unit_name, $unit_price, $taxname, $commodity_code, $unit_id, $tax_rate, '', '', '', $total_after_discount, $guarantee_period, $expiry_date, $lot_number, $note, $sub_total, $tax_name, $tax_id, 'undefined', true, false, $value['serial_number'] );
    								$temporaty_quantity--;
    								$temporaty_available_quantity--;
    								$item_index ++;
    								$inventory_warehouse_by_commodity[$key]['inventory_number'] = $temporaty_available_quantity;
    							}
    						}
    					}

    				}

    				if($temporaty_quantity > 0){
    					$quantities = $temporaty_quantity;
    					$available_quantity = 0;
    					$name = 'newitems['.$item_index.']';

    					foreach ($inventory_warehouse_by_commodity as $key => $inventory_warehouse) {
    						if((float)$inventory_warehouse['inventory_number'] > 0 && $temporaty_quantity > 0){

    							$available_quantity = (float)$inventory_warehouse['inventory_number'];
    							$warehouse_id = $inventory_warehouse['warehouse_id'];
    							
    							if ($temporaty_quantity >= $inventory_warehouse['inventory_number']) {
    								$temporaty_quantity = (float) $temporaty_quantity - (float) $inventory_warehouse['inventory_number'];
    								$quantities = (float)$inventory_warehouse['inventory_number'];
    							} else {
    								$quantities = (float)$temporaty_quantity;
    								$temporaty_quantity = 0;
    							}

    							$goods_delivery_row_template .= $this->create_goods_delivery_row_template([], $name, $commodity_name, '', $available_quantity, $quantities, $unit_name, $unit_price, $taxname, $commodity_code, $unit_id, $tax_rate, '', '', '', $total_after_discount, $guarantee_period, $expiry_date, $lot_number, $note, $sub_total, $tax_name, $tax_id, 'undefined', true);
    							$item_index ++;
    						}
    					}
    				}
    			}

    		}

    		if($get_pur_order){
    			if((float)$get_pur_order->discount_percent > 0){
    				$additional_discount = (float)$get_pur_order->discount_percent * (float)$pur_total_money/100;
    			}
    		}
    	}


    	$arr_pur_resquest['result'] = $goods_delivery_row_template;
    	$arr_pur_resquest['additional_discount'] = $additional_discount;

    	return $arr_pur_resquest;
    }


	/**
	 * get pr order delivered
	 * @return [type] 
	 */
	public function  get_pr_order_delivered()
	{

		$arr_purchase_orders = $this->get_invoices_goods_delivery('purchase_orders');

		if(count($arr_purchase_orders) > 0){

			return $this->db->query('select * from tblpur_orders where approve_status = 2 AND delivery_status = 1 AND id NOT IN ('.implode(",", $arr_purchase_orders).') order by id desc')->result_array();
		}
		return $this->db->query('select * from tblpur_orders where approve_status = 2 AND delivery_status = 1 order by id desc')->result_array();

	}


    /**
     * get client lead
     * @param  string $id    
     * @param  array  $where 
     * @return array        
     */
    
    public function get_client_lead($q, $id = '')
    {	
    	//customer where
    	$where = '';
    	if ($q){
    		$where .= '(company LIKE "%' . $q . '%" OR CONCAT(firstname, " ", lastname) LIKE "%' . $q . '%" OR email LIKE "%' . $q . '%" OR vat LIKE "%'. $q .'%") AND '.db_prefix().'clients.active = 1';
    	}

    	$data_clients = $this->wh_get_client($where);

    	foreach ($data_clients as $key => $value) {
    		$data_clients[$key]['proposal_wh'] = 'customer';
    	}

        //lead where
    	$data_leads = $this->wh_search_leads($q, 0, [
    		'junk' => 0,
    	]);

    	foreach ($data_leads as $key => $value) {
    		$data_leads[$key]['proposal_wh'] = 'lead';
    	}

    	return array_merge($data_clients, $data_leads);
    }


    /**
     * wh search leads
     * @param  string  $q     
     * @param  integer $limit 
     * @param  array   $where 
     * @return array         
     */
    public function wh_search_leads($q, $limit = 0, $where = [])
    {

    	$has_permission_view = has_permission('leads', '', 'view');

    	if (is_staff_member()) {
            // Leads
    		$this->db->select();
    		$this->db->from(db_prefix() . 'leads');

    		if (!$has_permission_view) {
    			$this->db->where('(assigned = ' . get_staff_user_id() . ' OR addedfrom = ' . get_staff_user_id() . ' OR is_public=1)');
    		}

    		if (!startsWith($q, '#')) {
    			$this->db->where('(name LIKE "%' . $q . '%"
    				OR title LIKE "%' . $q . '%"
    				OR company LIKE "%' . $q . '%"
    				OR zip LIKE "%' . $q . '%"
    				OR city LIKE "%' . $q . '%"
    				OR state LIKE "%' . $q . '%"
    				OR address LIKE "%' . $q . '%"
    				OR email LIKE "%' . $q . '%"
    				OR phonenumber LIKE "%' . $q . '%"
    				OR vat LIKE "%' . $q . '%"
    			)');
    		} else {
    			$this->db->where('id IN
    				(SELECT rel_id FROM ' . db_prefix() . 'taggables WHERE tag_id IN
    				(SELECT id FROM ' . db_prefix() . 'tags WHERE name="' . strafter($q, '#') . '")
    				AND ' . db_prefix() . 'taggables.rel_type=\'lead\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
    				');
    		}


    		$this->db->where($where);

    		if ($limit != 0) {
    			$this->db->limit($limit);
    		}
    		$this->db->order_by('name', 'ASC');
    		return $this->db->get()->result_array();
    	}

    	return [];
    }


    /**
     * wh get client
     * @param  string $id    
     * @param  array  $where 
     * @return array        
     */
    public function wh_get_client($where = [])
    {
    	$this->db->select(implode(',', prefixed_table_fields_array(db_prefix() . 'clients')) . ',' . get_sql_select_client_company());

    	$this->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'clients.country', 'left');
    	$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.userid = ' . db_prefix() . 'clients.userid AND is_primary = 1', 'left');

    	if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
    		$this->db->where($where);
    	}


    	$this->db->order_by('company', 'asc');

    	return $this->db->get(db_prefix() . 'clients')->result_array();
    }

    /**
     * Gets the proposal attachments.
     *
     * @param      <type>  $id     The proposal
     *
     * @return     <type>  The proposal attachments.
     */
    public function get_proposal_attachments($id){

    	$this->db->where('rel_id',$id);
    	$this->db->where('rel_type','wh_proposal');
    	return $this->db->get(db_prefix().'files')->result_array();
    }

     /**
     * Gets the file.
     *
     * @param      <type>   $id      The file id
     * @param      boolean  $rel_id  The relative identifier
     *
     * @return     boolean  The file.
     */
     public function get_file($id, $rel_id = false)
     {
     	$this->db->where('id', $id);
     	$file = $this->db->get(db_prefix().'files')->row();

     	if ($file && $rel_id) {
     		if ($file->rel_id != $rel_id) {
     			return false;
     		}
     	}
     	return $file;
     }

     /**
     * Gets the part attachments.
     *
     * @param      <type>  $surope  The surope
     * @param      string  $id      The identifier
     *
     * @return     <type>  The part attachments.
     */
     public function get_wh_proposal_attachments($surope, $id = '')
     {
        // If is passed id get return only 1 attachment
     	if (is_numeric($id)) {
     		$this->db->where('id', $id);
     	} else {
     		$this->db->where('rel_id', $assets);
     	}
     	$this->db->where('rel_type', 'wh_proposal');
     	$result = $this->db->get(db_prefix().'files');
     	if (is_numeric($id)) {
     		return $result->row();
     	}

     	return $result->result_array();
     }


     /**
     * { delete purorder attachment }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean 
     */
     public function delete_wh_proposal_attachment($id)
     {
     	$attachment = $this->get_wh_proposal_attachments('', $id);

     	$deleted    = false;
     	if ($attachment) {
     		if (empty($attachment->external)) {
     			unlink(WAREHOUSE_MODULE_UPLOAD_FOLDER .'/proposal/'. $attachment->rel_id . '/' . $attachment->file_name);
     		}
     		$this->db->where('id', $attachment->id);
     		$this->db->delete('tblfiles');
     		if ($this->db->affected_rows() > 0) {
     			$deleted = true;
     		}

     		if (is_dir(WAREHOUSE_MODULE_UPLOAD_FOLDER .'/proposal/'. $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
     			$other_attachments = list_files(WAREHOUSE_MODULE_UPLOAD_FOLDER .'/proposal/'. $attachment->rel_id);
     			if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
     				delete_dir(WAREHOUSE_MODULE_UPLOAD_FOLDER .'/proposal/'. $attachment->rel_id);
     			}
     		}
     	}

     	return $deleted;
     }

    /**
	 * get brand
	 * @param  boolean $id
	 * @return array or object
	 */
    public function get_brand($id = false) {

    	if (is_numeric($id)) {
    		$this->db->where('id', $id);

    		return $this->db->get(db_prefix() . 'wh_brand')->row();
    	}
    	if ($id == false) {
    		return $this->db->query('select * from tblwh_brand')->result_array();
    	}

    }

    /**
	 * add brand
	 * @param array $data
	 * @return integer
	 */
    public function add_brand($data) {

    	$this->db->insert(db_prefix() . 'wh_brand', $data);
    	$insert_id = $this->db->insert_id();

    	return $insert_id;
    }

	/**
	 * update brand
	 * @param  array $data
	 * @param  integer $id
	 * @return boolean
	 */
	public function update_brand($data, $id) {

		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'wh_brand', $data);

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return true;
	}

	/**
	 * delete brand
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_brand($id) {
		//delete model
		$this->db->where('brand_id', $id);
		$arr_model = $this->db->get(db_prefix() . 'wh_model')->result_array();

		if(count($arr_model) > 0){
			foreach ($arr_model as $value) {
				$this->delete_model($value['id']);
			}
		}
		

		//delete brand
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'wh_brand');

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return false;
	}

	/**
	 * get model
	 * @param  boolean $id
	 * @return array or object
	 */
	public function get_model($id = false) {

		if (is_numeric($id)) {
			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'wh_model')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblwh_model')->result_array();
		}

	}

    /**
	 * add model
	 * @param array $data
	 * @return integer
	 */
    public function add_model($data) {

    	$this->db->insert(db_prefix() . 'wh_model', $data);
    	$insert_id = $this->db->insert_id();

    	return $insert_id;
    }

	/**
	 * update model
	 * @param  array $data
	 * @param  integer $id
	 * @return boolean
	 */
	public function update_model($data, $id) {

		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'wh_model', $data);

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return true;
	}

	/**
	 * delete model
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_model($id) {

		//delete series
		$this->db->where('model_id', $id);
		$arr_series = $this->db->get(db_prefix() . 'wh_series')->result_array();

		if(count($arr_series) > 0){
			foreach ($arr_series as $value) {
				$this->delete_series($value['id']);
			}
		}

		//delete model
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'wh_model');

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return false;
	}

	/**
	 * get series
	 * @param  boolean $id
	 * @return array or object
	 */
	public function get_series($id = false) {

		if (is_numeric($id)) {
			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'wh_series')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblwh_series')->result_array();
		}

	}

    /**
	 * add series
	 * @param array $data
	 * @return integer
	 */
    public function add_series($data) {

    	$this->db->insert(db_prefix() . 'wh_series', $data);
    	$insert_id = $this->db->insert_id();

    	return $insert_id;
    }

	/**
	 * update series
	 * @param  array $data
	 * @param  integer $id
	 * @return boolean
	 */
	public function update_series($data, $id) {

		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'wh_series', $data);

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return true;
	}

	/**
	 * delete series
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_series($id) {

		//delete series
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'wh_series');

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return false;
	}

	/**
	 * get item proposal value
	 * @param  array $data 
	 * @return [type]           
	 */
	public function get_item_proposal_value($data)
	{

	    //brand where

		$model_id = [];
		$model_options = '';

		$series_id = [];
		$series_options = '';

		$items_id = [];
		$item_options = '';


		$model_id_selected= $data['model_id'];
		$series_id_selected= $data['series_id'];

		if($data['status'] == 'brand_id'){
			$data['model_id'] ='';
		}

		if(isset($data['brand_id']) && $data['brand_id'] !=''){


			$items_id = [];

			$model_id = [];
			$model_options = '';

			$series_id = [];
			$series_options = '';


	    	//select model from brand
			$this->db->where('brand_id', $data['brand_id']);
			$arr_model = $this->db->get(db_prefix().'wh_model')->result_array();

			$model_options .=' <option value=""></option>';
			foreach ($arr_model as $model) {

				$select='';
				if($model['id'] == $model_id_selected){
					$select .= 'selected';
				}

				$model_options .= '<option value="' . $model['id'] . '" '.$select.'>' . $model['name'] . '</option>';
				array_push($model_id, $model['id']);
			}


		    	//select series from model
			if(count($model_id) > 0){
				$this->db->where_in('model_id', $model_id);
				$arr_series = $this->db->get(db_prefix().'wh_series')->result_array();

				foreach ($arr_series as $series) {

					array_push($series_id, $series['id']);
				}

				if(count($series_id) > 0){
					$this->db->where_in('series_id', $series_id);
					$arr_items = $this->db->get(db_prefix().'items')->result_array();

					foreach ($arr_items as $item) {

						array_push($items_id, $item['id']);
					}

				}

			}



		}

		if(isset($data['model_id']) && $data['model_id'] !=''){

			$items_id = [];

			$series_id = [];
			$series_options = '';

	    	//select model from brand
			$this->db->where('model_id', $data['model_id']);
			$arr_series = $this->db->get(db_prefix().'wh_series')->result_array();


			$series_options .=' <option value=""></option>';
			foreach ($arr_series as $series) {
				$select='';
				if($series['id'] == $series_id_selected){
					$select .= 'selected';
				}

				$series_options .= '<option value="' . $series['id'] . '" '.$select.'>' . $series['name'] . '</option>';
				array_push($series_id, $series['id']);
			}


		    	//select item from series
			if(count($series_id) > 0){
				$this->db->where_in('series_id', $series_id);
				$arr_items = $this->db->get(db_prefix().'items')->result_array();

				foreach ($arr_items as $item) {

					array_push($items_id, $item['id']);
				}

			}

		}

		if(isset($data['series_id']) && $data['series_id'] != ''){
			$items_id = [];
			$item_options = '';

	    	//select item from series
			$this->db->where('series_id', $data['series_id']);
			$arr_items = $this->db->get(db_prefix().'items')->result_array();
			foreach ($arr_items as $item) {
				array_push($items_id, $item['id']);
			}

		}

	    //get item from []
		$items = [];
		
		if(count($items_id) > 0){

			$this->db->order_by('name', 'asc');
			$groups = $this->db->get(db_prefix() . 'items_groups')->result_array();

			array_unshift($groups, [
				'id'   => 0,
				'name' => '',
			]);

			foreach ($groups as $group) {
				$this->db->select('*,' . db_prefix() . 'items_groups.name as group_name,' . db_prefix() . 'items.id as id');
				$this->db->where('group_id', $group['id']);
				$this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
				$this->db->order_by('description', 'asc');
				$this->db->where_in(db_prefix().'items.id', $items_id);

				if(isset($data['warehouse_id']) && $data['warehouse_id'] != ''){
					$this->db->where_in(db_prefix().'items.warehouse_id', $data['warehouse_id']);
				}

				$_items = $this->db->get(db_prefix() . 'items')->result_array();

				if (count($_items) > 0) {
					$item_options .=' <option value=""></option>';
					$item_options .= '<optgroup data-group-id="'.$_items[0]['group_id'].'" label="'.$_items[0]['group_name'].'">';
					foreach ($_items as $i) {

						$item_options .= '<option value="'.$i['id'].'" data-subtext="'. strip_tags(mb_substr($i['long_description'],0,200)).'...' .'">('.app_format_number($item['rate']) .') '.$i['description'].'</option>';

					}
					$item_options .= ' </optgroup>';
				}
			}

		}

		$data_return =[];
		$data_return['item_options'] = $item_options;
		$data_return['model_options'] = $model_options;
		$data_return['series_options'] = $series_options;

		return $data_return;

	}


	/**
	 * get custom fields warehouse
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function get_custom_fields_warehouse($id = false) {

		if (is_numeric($id)) {
			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'wh_custom_fields')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from tblwh_custom_fields')->result_array();
		}

	}

	/**
	 * add custom fields warehouse
	 * @param array $data 
	 */
	public function add_custom_fields_warehouse($data) {

		if(is_array($data['warehouse_id'])){
			$data['warehouse_id'] = implode(',', $data['warehouse_id']);
		}


		$this->db->insert(db_prefix() . 'wh_custom_fields', $data);
		$insert_id = $this->db->insert_id();

		return $insert_id;
	}


	/**
	 * update custom fields warehouse
	 * @param  array $data 
	 * @param  integer $id   
	 * @return [type]       
	 */
	public function update_custom_fields_warehouse($data, $id) {

		if(is_array($data['warehouse_id'])){
			$data['warehouse_id'] = implode(',', $data['warehouse_id']);
		}

		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'wh_custom_fields', $data);

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return true;
	}


	/**
	 * delete custom fields warehouse
	 * @param integer $id 
	 * @return [type]     
	 */
	public function delete_custom_fields_warehouse($id) {

		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'wh_custom_fields');

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return false;
	}


	/**
	 * check warehouse custom fields
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function check_warehouse_custom_fields($data)
	{	

		if(isset($data['id'])){
			$custom_fields_value = $this->get_custom_fields_warehouse($data['id']);
			if($custom_fields_value->custom_fields_id == $data['custom_fields_id']){
				return true;
			}else{
				$this->db->where('custom_fields_id', $data['custom_fields_id']);
				$this->db->where('id', $data['id']);

				$custom_fields = $this->db->get(db_prefix() . 'wh_custom_fields')->result_array();

				if(count($custom_fields) > 0){
					return false;
				}
				return true;
			}

		}else{
			return $this->check_warehouse_custom_fields_one($data['custom_fields_id']);
		}

	}


	/**
	 * check warehouse custom fields one
	 * @param  integer $custom_fields_id 
	 * @return [type]                   
	 */
	public function check_warehouse_custom_fields_one($custom_fields_id)
	{
		$this->db->where('custom_fields_id', $custom_fields_id);
		$custom_fields = $this->db->get(db_prefix() . 'wh_custom_fields')->row();
		if($custom_fields){
			return false;
		}
		return true;

	}

	/**
	 * get adjustment stock quantity
	 * @param  [type] $warehouse_id 
	 * @param  [type] $commodity_id 
	 * @param  [type] $lot_number   
	 * @param  [type] $expiry_date  
	 * @return [type]               
	 */
	public function get_adjustment_stock_quantity($warehouse_id, $commodity_id, $lot_number, $expiry_date) {

		if(isset($lot_number) && $lot_number != '0' && $lot_number != ''){
			/*have value*/
			$this->db->where('lot_number', $lot_number);
		}else{
			/*lot number is 0 or ''*/
			$this->db->group_start();

			$this->db->where('lot_number', '0');
			$this->db->or_where('lot_number', '');
			$this->db->or_where('lot_number', null);

			$this->db->group_end();
		}

		$this->db->where('warehouse_id', $warehouse_id);
		$this->db->where('commodity_id', $commodity_id);

		if(new_strlen($expiry_date) == 0){
			$this->db->where('expiry_date', NULL);
		}else{
			$this->db->where('expiry_date', $expiry_date);
		}
		$this->db->where('inventory_number > 0');

		$inventory_manage = $this->db->get(db_prefix() . 'inventory_manage')->row();
		return $inventory_manage;


	}


	/**
	 * delivery note get data send mail
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delivery_note_get_data_send_mail($id)
	{
		$options ='';
		$primary_email='';
		$goods_delivery_userid ='';
		$goods_delivery = $this->get_goods_delivery($id);

		if($goods_delivery){
			$goods_delivery_userid = $goods_delivery->customer_code;
		}

		$array_customer = $this->clients_model->get();

		foreach ($array_customer as $key => $value) {
			$select='';
			if($value['userid'] == $goods_delivery_userid){
				$select .=' selected';

				$contact_value = $this->clients_model->get_contacts($value['userid']);
				if(count($contact_value) > 0){
					$primary_email 	= $contact_value[0]['email'];
				}

			}
			$options .= '<option value="' . $value['userid'].'" '.$select.'>' . $value['company'] . '</option>';
		}

		$data=[];
		$data['options'] = $options;
		$data['primary_email'] = $primary_email;

		return $data;

	}


	/**
	 * get tags name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_tags_name($id){
		/* get list tinymce start*/
		$this->db->from(db_prefix() . 'taggables');
		$this->db->join(db_prefix() . 'tags', db_prefix() . 'tags.id = ' . db_prefix() . 'taggables.tag_id', 'left');

		$this->db->where(db_prefix() . 'taggables.rel_id', $id);
		$this->db->where(db_prefix() . 'taggables.rel_type', 'item_tags');
		$this->db->order_by('tag_order', 'ASC');

		$item_tags = $this->db->get()->result_array();

		$array_tags_name = [];
		foreach ($item_tags as $tag_value) {
			array_push($array_tags_name, $tag_value['name']);
		}

		return implode(",", $array_tags_name);

	}

    /**
     * send delivery note
     * @param  [type] $data 
     * @return [type]       
     */
    public function send_delivery_note($data){

    	$staff_id = get_staff_user_id();

    	$inbox = array();

    	$inbox['to'] = $data['email'];
    	$inbox['sender_name'] = get_staff_full_name($staff_id);
    	$inbox['subject'] = _strip_tags($data['subject']);
    	$inbox['body'] = _strip_tags($data['content']);        
    	$inbox['body'] = nl2br_save_html($inbox['body']);
    	$inbox['date_received']      = date('Y-m-d H:i:s');
    	$inbox['from_email'] = get_option('smtp_email');

    	$companyname =  get_option('companyname');

    	if(new_strlen(get_option('smtp_host')) > 0 && new_strlen(get_option('smtp_password')) > 0 && new_strlen(get_option('smtp_username')) > 0){

    		$ci = &get_instance();
    		$ci->email->initialize();
    		$ci->load->library('email');    
    		$ci->email->clear(true);
    		$ci->email->from($inbox['from_email'], $inbox['sender_name']);
    		$ci->email->to($inbox['to']);

    		$ci->email->subject($inbox['subject']);

    		$email_footer = get_option('email_footer');
    		$email_footer = new_str_replace('{companyname}', $companyname, $email_footer);
    		$ci->email->message(get_option('email_header') . $inbox['body'] . $email_footer);

    		$attachment_url = site_url(WAREHOUSE_PATH.'send_delivery_note/'.$data['goods_delivery'].'/'.$_FILES['attachment']['name']);
    		$ci->email->attach($attachment_url);
    		if($ci->email->send(true)){
            	//write activity if delivery created from invoice
    			if(isset($data['invoice_id'])){
    				$this->load->model('invoices_model');
    				$this->invoices_model->log_invoice_activity($data['invoice_id'], _l('delivery_slip_sent_to_email_address').' '.$data['email']);

    			}
    			return true;
    		}else{
    			return false;

    		}
    	}

    	return false;
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
    		$this->db->where('id != '.$data['item_id']);

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
     * stock internal delivery pdf
     * @param  [type] $internal 
     * @return [type]           
     */
	public function stock_internal_delivery_pdf($internal) {
		return app_pdf('internal', module_dir_path(WAREHOUSE_MODULE_NAME, 'libraries/pdf/Internal_pdf.php'), $internal);
	}


	/**
	 * get stock internal delivery pdf_html
	 * @param  [type] $internal_delivery_id 
	 * @return [type]                    
	 */
	public function get_stock_internal_delivery_pdf_html($internal_delivery_id) {
		$this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();
		// get_goods_receipt
		$internal_delivery = $this->get_internal_delivery($internal_delivery_id);
		// get_goods_receipt_detail
		$internal_delivery_detail = $this->get_internal_delivery_detail($internal_delivery_id);
		$company_name = get_option('invoice_company_name');
		$address = get_option('invoice_company_address');

		$day = date('d', strtotime($internal_delivery->date_add));
		$month = date('m', strtotime($internal_delivery->date_add));
		$year = date('Y', strtotime($internal_delivery->date_add));


		$html = '';

		$html .= '<table class="table">
		<tbody>
		<tr>
		<td rowspan="2" width="50%" class="text-left">'.pdf_logo_url().'</td>
		<td class="text_right_weight "><h3>' . mb_strtoupper(_l('delivery')) . '</h3></td>
		</tr>

		<tr>
		<td class="text_right">#'.$internal_delivery->internal_delivery_code.' - '.$internal_delivery->internal_delivery_name.'</td>
		</tr>
		</tbody>
		</table>
		<br>
		';

	     //organization_info
		$organization_info = '<div  class="bill_to_color">';
		$organization_info .= format_organization_info();
		$organization_info .= '</div>';


	    //invoice_data_date
		$invoice_date = '<br /><b>' . _l('invoice_data_date') . ' ' . _d($internal_delivery->date_add) . '</b><br />';

		$html .= '<table class="table">
		<tbody>
		<tr>
		<td rowspan="2" width="50%" class="text-left">'.$organization_info.'</td>
		<td rowspan="2" width="50%" class="text_right"></td>
		</tr>
		</tbody>
		</table>
		<br><br>
		<br><br>
		';

		
		$html .= '<table class="table">
		<tbody>
		<tr>
		<td rowspan="2" width="50%" class="text-left"></td>
		<td rowspan="2" width="50%" class="text_right">'.$invoice_date.'</td>
		</tr>
		</tbody>
		</table>
		<br><br><br>
		<br><br><br>
		';



		$html .= '<table class="table">
		<tbody>

		<tr>
		<th width="5%" class=" thead-dark-ip"><b>#</b></th>
		<th width="20%" class=" thead-dark-ip">' ._l('commodity_name').'</th>
		<th width="15%" class=" thead-dark-ip">' . _l('from_stock_name') . '</th>
		<th width="15%" class=" thead-dark-ip">' . _l('to_stock_name') . '</th>
		<th width="10%" class=" thead-dark-ip">' . _l('available_quantity') . '</th>
		<th width="10%" class=" thead-dark-ip">' . _l('quantity_export') . '</th>
		<th width="10%" class=" thead-dark-ip">' . _l('unit_price') . '</th>
		<th width="15%" class=" thead-dark-ip">' . _l('into_money') . '</th>

		</tr>';

		$warehouse_address 	= '';
		$array_warehouse	= [];
		$array_from_warehouse	= [];
		$array_to_warehouse	= [];
		foreach ($internal_delivery_detail as $delivery_key => $delivery_value) {
			$flag_from_warehouse = true;
			$flag_to_warehouse   = true;

			

			$item_order = $delivery_key +1;

			$commodity_name = get_commodity_name($delivery_value['commodity_code']) != null ? get_commodity_name($delivery_value['commodity_code'])->description : '';

			$available_quantity = (isset($delivery_value) ? $delivery_value['available_quantity'] : '');
			$quantities = (isset($delivery_value) ? $delivery_value['quantities'] : '');
			$unit_price = (isset($delivery_value) ? $delivery_value['unit_price'] : '');
			$into_money = (isset($delivery_value) ? $delivery_value['into_money'] : '');

			$commodity_code = get_commodity_name($delivery_value['commodity_code']) != null ? get_commodity_name($delivery_value['commodity_code'])->commodity_code : '';


			$from_stock_name ='';
			if(isset($delivery_value['from_stock_name']) && ($delivery_value['from_stock_name'] !='')){
				if(!in_array($delivery_value['from_stock_name'], $array_from_warehouse)){
					$array_from_warehouse[] = $delivery_value['from_stock_name'];
					$arr_warehouse = new_explode(',', $delivery_value['from_stock_name']);

					$str = '';
					if(count($arr_warehouse) > 0){

						foreach ($arr_warehouse as $wh_key => $warehouseid) {
							$str = '';
							if ($warehouseid != '' && $warehouseid != '0') {

								$team = get_warehouse_name($warehouseid);
								if($team){
									$value = $team != null ? get_object_vars($team)['warehouse_name'] : '';

									$str .= '<span class="label label-tag tag-id-1"><span class="tag">' . $value . '</span><span class="hide"> </span></span>';

									$from_stock_name .= $str;
									if($wh_key%3 ==0){
										$from_stock_name .='<br/>';
									}

								//get warehouse address
									if(!in_array($warehouseid, $array_warehouse)){
										$warehouse_address .= '<b>' .$team->warehouse_name .' : </b>'. wh_get_warehouse_address($warehouseid) .'.'.'<br/>';
									}
								}

							}
						}

					} else {
						$from_stock_name = '';
					}
				}
			}

			$to_stock_name ='';
			if(isset($delivery_value['to_stock_name']) && ($delivery_value['to_stock_name'] !='')){
				if(!in_array($delivery_value['to_stock_name'], $array_to_warehouse)){
					$array_to_warehouse[] = $delivery_value['to_stock_name'];
					$arr_warehouse = new_explode(',', $delivery_value['to_stock_name']);

					$str = '';
					if(count($arr_warehouse) > 0){

						foreach ($arr_warehouse as $wh_key => $warehouseid) {
							$str = '';
							if ($warehouseid != '' && $warehouseid != '0') {

								$team = get_warehouse_name($warehouseid);
								if($team){
									$value = $team != null ? get_object_vars($team)['warehouse_name'] : '';

									$str .= '<span class="label label-tag tag-id-1"><span class="tag">' . $value . '</span><span class="hide"> </span></span>';

									$to_stock_name .= $str;
									if($wh_key%3 ==0){
										$to_stock_name .='<br/>';
									}
								//get warehouse address
									if(!in_array($warehouseid, $array_warehouse)){
										$warehouse_address .= '<b>' .$team->warehouse_name .' : </b>'. wh_get_warehouse_address($warehouseid) .'.'.'<br/>';
									}
								}

							}
						}

					} else {
						$to_stock_name = '';
					}
				}
			}

			$get_from_stock_name = get_warehouse_name($delivery_value['from_stock_name']);
			$get_to_stock_name = get_warehouse_name($delivery_value['to_stock_name']);
			if($get_from_stock_name){
				$from_stock_name = $get_from_stock_name->warehouse_name;
			}
			if($get_to_stock_name){
				$to_stock_name = $get_to_stock_name->warehouse_name;
			}



			$unit_name = '';
			if(isset($delivery_value['unit_id']) && ($delivery_value['unit_id'] !='')){
				$unit_name = get_unit_type($delivery_value['unit_id']) != null ? get_unit_type($delivery_value['unit_id'])->unit_name : '';
			}

			$commodity_name = $delivery_value['commodity_name'];
			if(new_strlen($commodity_name) == 0){
				$commodity_name = wh_get_item_variatiom($delivery_value['commodity_code']);
			}

			$html .= '<tr>';
			$html .= '<td class=""><b>' . (float)$item_order . '</b></td>
			<td class=""><b>' . $commodity_name . '</b></td>
			<td class="td_style_r_ep_c">' . $from_stock_name. '</td>
			<td class="td_style_r_ep_c">' . $to_stock_name. '</td>
			<td class="td_style_r_ep_c">' . $available_quantity .' '.$unit_name. '</td>
			<td class="td_style_r_ep">' . $quantities .' '.$unit_name. '</td>';

			$html .= ' <td class="td_style_r_ep">' . app_format_money((float) $unit_price, '') . '</td>
			<td class="td_style_r_ep"><b>' . app_format_money((float) $into_money, '') . '</b></td>';
			
			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table>
		<br>
		<br>';



		$html .= '<table class="table">
		<tbody>
		<tr>
		<td ></td>
		<td ></td>
		<td ></td>
		<td ></td>
		<td class="text_left"><b>' . _l('total_amount') . '</b></td>
		<td class="text_right">' .$base_currency->symbol. app_format_money((float) $internal_delivery->total_amount, '') . '</td>
		</tr>
		</tbody>
		</table>
		<br>
		';


		$html .=  '<h4 class="note-align">' . _l('warehouse_address') . ':</h4>
		<p>' . $warehouse_address . '</p>';
		
		$html .=  '<h4 class="note-align">' . _l('note_') . ':</h4>
		<p>' . $internal_delivery->description . '</p>';


		$html .= '<table class="table">
		<tbody>
		<tr>
		<td class="fw_width35"><h4>' . _l('deliver_name') . '</h4></td>
		<td class="fw_width30"><h4>' . _l('stocker') . '</h4></td>
		<td class="fw_width30"><h4>' . _l('chief_accountant') . '</h4></td>

		</tr>
		<tr>


		<td class="fw_width35 fstyle">' . _l('sign_full_name') . '</td>
		<td class="fw_width30 fstyle ">' . _l('sign_full_name') . '</td>
		<td class="fw_width30 fstyle">' . _l('sign_full_name') . '</td>
		</tr>

		</tbody>
		</table>

		<br>
		<br>
		<br>
		<br>
		<table class="table">
		<tbody>
		<tr>';


		$html .= '<link href="' . FCPATH.'modules/warehouse/assets/css/pdf_style.css' . '"  rel="stylesheet" type="text/css" />';
		// old link
		// $html .= '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/pdf_style.css') . '"  rel="stylesheet" type="text/css" />';
		return $html;
	}



	public function print_barcode_pdf($print_barcode) {
		return app_pdf('print_barcode', module_dir_path(WAREHOUSE_MODULE_NAME, 'libraries/pdf/Print_barcode_pdf.php'), $print_barcode);
	}


	/**
	 * get stock internal delivery pdf_html
	 * @param  [type] $internal_delivery_id 
	 * @return [type]                    
	 */
	public function get_print_barcode_pdf_html($data) {

		$display_product_name = get_warehouse_option('display_product_name_when_print_barcode');
		$display_price = get_warehouse_option('wh_show_price_when_print_barcode');
		$print_qty = $data['print_qty'];

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
			$array_commodity = $this->get_commodity();
			$html_child='';
			$br_tem=1;
			$item_index=0;

			foreach ($array_commodity as $key => $value) {
				if($value['commodity_barcode'] != ''){

					if(!file_exists(WAREHOUSE_PRINT_ITEM. md5($value['commodity_barcode']).'.svg')){
						$this->getBarcode($value['commodity_barcode']);
					}
				}

				/*get frist 25 character */
				if(new_strlen($value['description']) > 30){
					$description = substr($value['description'],0,30); 
				}else{
					$description = $value['description'];
				}
				$description = json_encode(strip_tags($description ?? '', []));

			/*get frist 100 character */
				if(new_strlen($value['long_description']) > 30){
					$description_sub = substr($value['long_description'],0,30); 
				}else{
					$description_sub = $value['long_description'];
				}

				$description_sub = strip_tags($description_sub ?? '', []);
				//final price: price*Vat
				$tax_value=0;
				if($value['tax'] != 0 && $value['tax'] != ''){
					$tax_rate = get_tax_rate($value['tax']);
					if(!is_array($tax_rate)  && isset($tax_rate)){
						$tax_value = $tax_rate->taxrate;
					}
				}
				

				$rate_after_tax = (float)$value['rate'] + (float)$value['rate']*$tax_value/100;

				$price_html = '';
				if($display_price){
					$price_html = _l('print_barcode_sale_price').': '.app_format_money($rate_after_tax,$current_id);
				}

				$barcode_path  = FCPATH.'modules/warehouse/uploads/print_item/' . md5($value['commodity_barcode'] ?? '').'.svg';
				//old link
				// $barcode_path  = site_url('modules/warehouse/uploads/print_item/' . md5($value['commodity_barcode']).'.svg');

				for ($i=1; $i <= $print_qty; $i++) { 
					// code...
				if($value['commodity_barcode'] != ''){
					if($display_product_name == 1){
						$html_child .= '<td class="print-barcode-td-height"><span class="print-item-code print-item-name">'.$description.'</span><br><span class="print-item-code print-item-name">'.$description_sub.'</span><br><span class=" print-item-price">'.$price_html.'</span><span class="print-item"><img class="images_w_table" src="' . $barcode_path . '" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'.$value['commodity_barcode'].'</span></td>';
					}else{
						$html_child .= '<td class="print-barcode-td-height"><span class="print-item-code print-item-name"></span><br><span class="print-item-code print-item-name">'.$description.'</span><br><span class=" print-item-price">'.$price_html.'</span><span class="print-item"><img class="images_w_table" src="' . $barcode_path . '" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'.$value['commodity_barcode'].'</span></td>';

					}

					
				}else{
					if($display_product_name == 1){

						$html_child .= '<td class="print-barcode-td-height"><span class="print-item-code print-item-name">'.$description.'</span><br><span class="print-item-code print-item-name">'.$description_sub.'</span><br><span class=" print-item-price">'.$price_html.'</span><span class="print-item"><img class="images_w_table" src="" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'._l('the_product_has_no_barcode').'</span></td>';

					}else{
						$html_child .= '<td class="print-barcode-td-height"><span class="print-item-code print-item-name"></span><br><span class="print-item-code print-item-name">'.$description.'</span><br><span class=" print-item-price">'.$price_html.'</span><span class="print-item"><img class="images_w_table" src="" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'._l('the_product_has_no_barcode').'</span></td>';

					}
				
				}

				if(($item_index+1)%4 == 0 ){
					$html .= '<tr>'.$html_child.'</tr>';

					if($br_tem%36 == 0){
						$html .= '<br>';
					}

					$html_child='';
				}elseif(($item_index+1)%4 != 0 && ($item_index+1 == count($array_commodity)*$print_qty)){
					$html .= '<tr>'.$html_child.'</tr>';

					if($br_tem%36 == 0){
						$html .= '<br>';
					}

					$html_child='';
				}

				$br_tem++;
				$item_index++;

				}

				
			}



		}else{
			//select item check
			if(isset($data['item_select_print_barcode'])){

				$item_select_print_barcode_id = array_filter($data['item_select_print_barcode']);
				$sql_where ='select * from '.db_prefix().'items where id IN ('.implode(", ", $item_select_print_barcode_id).') order by id desc';
				$array_commodity =  $this->db->query($sql_where)->result_array();

				$html_child='';
				$br_tem=1;
				$item_index=0;
				foreach ($array_commodity as $key => $value) {
					if($value['commodity_barcode'] != ''){

						if(!file_exists(WAREHOUSE_PRINT_ITEM. md5($value['commodity_barcode']).'.svg')){
							$this->getBarcode($value['commodity_barcode']);
						}
					}

					/*get frist 100 character */
					if(new_strlen($value['description']) > 30){
						$description = substr($value['description'],0, 30); 
					}else{
						$description = $value['description'];
					}
					$description = json_encode(strip_tags($description ?? '', []));



					/*get frist 100 character */
					if(new_strlen($value['long_description']) > 30){
						$description_sub = substr($value['long_description'],0, 30 ); 
					}else{
						$description_sub = $value['long_description'];
					}
					$description_sub = strip_tags($description_sub ?? '', []);

					//final price: price*Vat
					$tax_value=0;
					if($value['tax'] != 0 && $value['tax'] != ''){
						$tax_rate = get_tax_rate($value['tax']);
						if(!is_array($tax_rate)  && isset($tax_rate)){
							$tax_value = $tax_rate->taxrate;
						}
					}

					$rate_after_tax = (float)$value['rate'] + (float)$value['rate']*$tax_value/100;
					$price_html = '';
					if($display_price){
						$price_html = _l('print_barcode_sale_price').': '.app_format_money($rate_after_tax,$current_id);
					}

					$barcode_path  = FCPATH.'modules/warehouse/uploads/print_item/' . md5($value['commodity_barcode'] ?? '').'.svg';
					//old link
					// $barcode_path  = site_url('modules/warehouse/uploads/print_item/' . md5($value['commodity_barcode']).'.svg');
					// 
					for ($i=1; $i <= $print_qty; $i++) { 

					if($value['commodity_barcode'] != ''){
						if($display_product_name == 1){

							$html_child .= '<td><span class="print-item-code print-item-name">'.$description.'</span><br><span class="print-item-code print-item-name ">'.$description_sub.'</span><br><span class=" print-item-price">'.$price_html.'</span><span class="print-item"><img class="images_w_table" src="' . $barcode_path . '" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'.$value['commodity_barcode'].'</span></td>';

						}else{

							$html_child .= '<td><span class="print-item-code print-item-name "></span><br><span class="print-item-code print-item-name">'.$description.'</span><br><span class=" print-item-price">'.$price_html.'</span><span class="print-item"><img class="images_w_table" src="' . $barcode_path . '" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'.$value['commodity_barcode'].'</span></td>';
						}
					}else{
						if($display_product_name == 1){
							$html_child .= '<td><span class="print-item-code print-item-name">'.$description.'</span><br><span class="print-item-code print-item-name ">'.$description_sub.'</span><br><span class=" print-item-price">'.$price_html.'</span><span class="print-item"><img class="images_w_table" src="" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'._l('the_product_has_no_barcode').'</span></td>';
						}else{
							$html_child .= '<td><span class="print-item-code print-item-name "></span><br><span class="print-item-code print-item-name">'.$description.'</span><br><span class=" print-item-price">'.$price_html.'</span><span class="print-item"><img class="images_w_table" src="" alt="' . $value['commodity_barcode'] . '" ></span><span class="print-item-code">'._l('the_product_has_no_barcode').'</span></td>';

						}
					}

					if(($item_index+1)%4 == 0 ){
						$html .= '<tr>'.$html_child.'</tr>';

						if($br_tem%36 == 0){
							$html .= '<br>';
						}

						$html_child='';
					}elseif(($item_index+1)%4 != 0 && ($item_index+1 == count($array_commodity)*$print_qty)){
						$html .= '<tr>'.$html_child.'</tr>';

						if($br_tem%36 == 0){
							$html .= '<br>';
						}

						$html_child='';
					}

					$br_tem++;
					$item_index++;
				}
				
				}
			}
		}
	
		$html .= '</tbody>
		</table>
		<br><br><br>
		';

		$html .= '<link href="' . FCPATH.'modules/warehouse/assets/css/pdf_style.css' . '"  rel="stylesheet" type="text/css" />';
		//old css link
		// $html .= '<link href="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/css/pdf_style.css') . '"  rel="stylesheet" type="text/css" />';
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
	        $barcodeobj = new TCPDFBarcode($sample ?? '', 'EAN13');
	        $code = $barcodeobj->getBarcodeSVGcode(4, 70, 'black');
			file_put_contents(WAREHOUSE_PRINT_ITEM.md5($sample).'.svg', $code);

			return true;
	    }
	}


	/**
	 * get purchase price from commodity id
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_purchase_price_from_commodity_id($id, $sale_price = false)
    {	
    	$purchase_price=0;

    	$this->db->where('id', $id);
    	$item_value = $this->db->get(db_prefix().'items')->row();

    	if($item_value){
    		if($sale_price == false){
	    		$purchase_price = $item_value->purchase_price;
	    	}else{
	    		$purchase_price = $item_value->rate;

	    	}
    	}

    	return $purchase_price;
    }

    /**
     * get list parent item
     * @return [type] 
     */

    public function get_list_parent_item($data)
	{
		 $item_options = '';
		 $flag_is_parent = false;

		if(isset($data['id']) && $data['id'] != ''){
			//count child item
			$this->db->where('parent_id', $data['id']);
			$array_child_value = $this->db->get(db_prefix().'items')->result_array();

			if(count($array_child_value) > 0){
				$flag_is_parent = true;
			}

			/*get main item for case update*/
			//get parent id checked
			$this->db->where('id', $data['id']);
			$item_value = $this->db->get(db_prefix().'items')->row();

			if($item_value){
				$parent_id = $item_value->parent_id;
			}else{
				$parent_id = '';
			}

			$sql_where = "id != ".$data['id']." AND ( parent_id is null OR parent_id = '') ";
			$this->db->where($sql_where);
			$this->db->order_by('id', 'desc');

			$list_item = $this->db->get(db_prefix().'items')->result_array();

    		$item_options .= '<option value=""></option>';

			foreach ($list_item as $item) {

	            	$select='';

	            	if($item['id'] == $parent_id){           
	            		$select .= 'selected';
	            	}
              		$item_options .= '<option value="' . $item['id'] . '" '.$select.'>' . $item['commodity_code'] . ' - '.$item['description']. '</option>';
	            }

		}else{
			/*get sub main item for case create new*/
        	$this->db->where('parent_id', null);
        	$this->db->or_where('parent_id', '');
			$this->db->order_by('id', 'desc');
    		$arr_item = $this->db->get(db_prefix().'items')->result_array();

    		$item_options .= '<option value=""></option>';
    		foreach ($arr_item as $item) {
    			$item_options .= '<option value="' . $item['id'] . '">' . $item['commodity_code'] . ' - '.$item['description']. '</option>';
            }

		}
	   
    	$data_return =[];
    	$data_return['item_options'] = $item_options;
    	$data_return['flag_is_parent'] = $flag_is_parent;

		return $data_return;

	}

    /**
     * get variation html
     * @param  [type] $id 
     * @return [type]     
     */
    public function get_variation_html($id)
    {
    	$index=0;
		$html = '';

    	if (is_numeric($id)) {

    			$item = $this->get_commodity($id);

    			if($item){
					$variation_value = json_decode($item->parent_attributes ?? '');
					if($variation_value){
						//current item is parent
						
						$get_parent_variation_html = $this->get_parent_variation_html($variation_value);
    					$html = $get_parent_variation_html['html'];
    					$index = $get_parent_variation_html['index'];
    					
					}elseif(isset($item->attributes)){
						//ex child value: [{"name":"Size","option":["M"]},{"name":"Color","option":["Red"]}]
						//ex parent value: [{"name":"Size","options":["S","M","L","XL","XXL"]},{"name":"Color","options":["Red","Black","White","Green"]}]

						//current item is child
						$parent_id = $item->parent_id;
						//get parent attributes
						$parent_item = $this->get_commodity($item->parent_id);

						//arrtribute decode
						$attributes_decode = json_decode($item->attributes);

						if(isset($parent_item->parent_attributes)){
							//check parent attribute != null
							$parent_attributes_decode = json_decode($parent_item->parent_attributes);

							$new_html = '';
							foreach ($parent_attributes_decode as  $value) {
								//get child option
								//
								$attribute_option ='';
								for ($x = 0; $x < count($attributes_decode); $x++) {

								    if($value->name == $attributes_decode[$x]->name){
								    	$attribute_option .= $attributes_decode[$x]->option;
								    	break;
								    }
								}

							    $new_html .= '<div class="col-md-6">
    									<div class ="form-group">
    									<label for="variation_names_'.$value->name.'" class="control-label"><small class="req text-danger">* </small>'.$value->name.'</label>
                      					<select name="variation_names_'.$value->name.'" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="" required>';
                      			$html_option_value = '';
                      			
                      			$html_option_value .= '<option value=""></option>';

                      			foreach ($value->options as $options_key => $options_value) {
                      				$selected='';
                      				if($attribute_option == $options_value){
                      					$selected .= 'selected';
                      				}

                      			    $html_option_value .= '<option value="'.$options_value.'" ' .$selected.'>'.$options_value.'</option>';
                      			}
                      			$new_html .= $html_option_value;

	    				    $new_html .= '</select>
	    				    		</div>
	    				    		</div>';


							}
    						$html .= '<div class="row">'.$new_html.'</div>';

						}
	    				

					}elseif($item->parent_id != null &&  $item->parent_id != '' && $item->parent_id != 0){

						$parent_value = $this->get_commodity($item->parent_id);
	    				if($parent_value->parent_attributes != null && $parent_value->parent_attributes != '' && new_strlen($parent_value->parent_attributes) > 28){

	    					$parent_variation = json_decode($parent_value->parent_attributes);
	    					
    						$html .= '<div class="row">'.$this->parent_attributes_sample_html($parent_variation).'</div>';

	    				}else{
	    					$html = '<div class="col-md-12">'._l('there_was_no_variation_in_the_parent_item').'</div>';
	    				}

					}else{
						$html = $this->parent_variation_sample_html();
						$index = 1;
					}
				}else{
					$html = $this->parent_variation_sample_html();
					$index = 1;

				}
			} else {

				$html = $this->parent_variation_sample_html();
				$index = 1;

			}

		return ['index' => $index, 'html' => $html];

    }


    /**
     * parent variation sample html
     * @return [type] 
     */
    public function parent_variation_sample_html()
    {	
    	$html ='';
    	$variation_attr =[];
        $variation_attr['rows'] = '1';

		$html .= '<div id="item_approve">
		<div class="col-md-11">
		<div class="col-md-4">
		' .  render_input('name[0]','variation_name', '', 'text') . '
		</div>
		<div class="col-md-8">
			<div class="options_wrapper">
			<span class="pull-left fa fa-question-circle" data-toggle="tooltip" title="" data-original-title="Populate the field by separating the options by coma. eq. apple,orange,banana"></span>
			' . render_textarea('options[0]', 'variation_options', '' , $variation_attr) . '
			</div>
			</div>
		</div>
		<div class="col-md-1 new_vendor_requests_button">
		<span class="pull-bot">
		<button name="add" class="btn new_wh_approval btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
		</span>
		</div>
		</div>';

		return $html;
    }


    public function parent_attributes_sample_html($parent_variation)
    {
    	$html ='';
    	foreach ($parent_variation as $key => $value) {

			$html .= '<div class="col-md-6">
						<div class ="form-group">
						<label for="variation_names_'.$value->name.'" class="control-label"><small class="req text-danger">* </small>'.$value->name.'</label>
      					<select name="variation_names_'.$value->name.'" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="" required>';
      			$html_option_value = '';
      			
      			$html_option_value .= '<option value=""></option>';

      			foreach ($value->options as $options_key => $options_value) {
      			    $html_option_value .= '<option value="'.$options_value.'">'.$options_value.'</option>';
      			}
      			$html .= $html_option_value;

		    $html .= '</select>
		    		</div>
		    		</div>';
		}

		return $html;
    }


    /**
     * get variation from parent item
     * @param  [type] $data 
     * @return [type]       
     */
    public function get_variation_from_parent_item($data)
    {
    	//parent_id, item_id
    	$index=0;
		$html = '';
		$check_is_parent = false;

    	if(isset($data['item_id']) && $data['item_id'] != 0 && $data['item_id'] != ''){
    	// update
    	//case has parent id, don't parent_id
    		if(isset($data['parent_id']) && $data['parent_id'] != ''){
    			//child item
    			$item_value = $this->get_commodity($data['item_id']);

    			if($item_value->parent_id == $data['parent_id']){
    				$parent_item = $this->get_commodity($data['parent_id']);

    				//check parent attribute != null
					$parent_attributes_decode = json_decode($parent_item->parent_attributes);

					if($parent_attributes_decode){

						//arrtribute decode
						$attributes_decode = json_decode($item_value->attributes);

						foreach ($parent_attributes_decode as  $value) {
							//get child option
							//
							$attribute_option ='';
							if($attributes_decode){
								for ($x = 0; $x <= count($attributes_decode); $x++) {
								    if($value->name == $attributes_decode[$x]->name){
								    	$attribute_option .= $attributes_decode[$x]->option;
								    	break;
								    }
								}
							}

						    $html .= '<div class="col-md-6">
									<div class ="form-group">
									<label for="variation_names_'.$value->name.'" class="control-label"><small class="req text-danger">* </small>'.$value->name.'</label>
	              					<select name="variation_names_'.$value->name.'" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="" required>';
	              			$html_option_value = '';
	              			
	              			$html_option_value .= '<option value=""></option>';

	              			foreach ($value->options as $options_key => $options_value) {
	              				$selected='';
	              				if($attribute_option == $options_value){
	              					$selected .= 'selected';
	              				}

	              			    $html_option_value .= '<option value="'.$options_value.'" ' .$selected.'>'.$options_value.'</option>';
	              			}
	              			$html .= $html_option_value;

					    $html .= '</select>
					    		</div>
					    		</div>';


						}

					}else{
						$html = '<div class="col-md-12">'._l('there_was_no_variation_in_the_parent_item').'</div>';
					}


    			}else{
    				$parent_value = $this->get_commodity($data['parent_id']);

    				if($parent_value->parent_attributes != null && $parent_value->parent_attributes != '' && new_strlen($parent_value->parent_attributes) > 28){

    					$parent_variation = json_decode($parent_value->parent_attributes);
	    				
    					$html .= $this->parent_attributes_sample_html($parent_variation);

    				}else{
    					$html = '<div class="col-md-12">'._l('there_was_no_variation_in_the_parent_item').'</div>';
    				}

    			}
    			
    		}else{
    			if(isset($data['item_id'])){
    				$parent_value = $this->get_commodity($data['item_id']);

    				if($parent_value->parent_attributes != null && $parent_value->parent_attributes != '' && new_strlen($parent_value->parent_attributes) > 28){

    					$variation_value = json_decode($parent_value->parent_attributes);
	    				
    					$get_parent_variation_html = $this->get_parent_variation_html($variation_value);
    					$html = $get_parent_variation_html['html'];
    					$index = $get_parent_variation_html['index'];

    				}else{
    					$check_is_parent = true;
		    			$html = $this->parent_variation_sample_html();
		    			$index = 1;
    				}
    			}else{
	    			$check_is_parent = true;
	    			$html = $this->parent_variation_sample_html();
	    			$index = 1;
    			}
    		}
    	
    	}else{
    	//insert
    	//case has parent_id, don't parent_id
    		if(isset($data['parent_id']) && $data['parent_id'] != ''){
    			//child item
    			
				$parent_value = $this->get_commodity($data['parent_id']);

				if($parent_value->parent_attributes != null && $parent_value->parent_attributes != '' && new_strlen($parent_value->parent_attributes) > 28){

					$parent_variation = json_decode($parent_value->parent_attributes);
    				
					$html .= $this->parent_attributes_sample_html($parent_variation);

				}else{
					$html = '<div class="col-md-12">'._l('there_was_no_variation_in_the_parent_item').'</div>';
				}
    			
    		}else{
    			//parent item
    			$check_is_parent = true;
    			$html = $this->parent_variation_sample_html();
    			$index = 1;
    		}
    	
    	}

    	$new_html = '';
    	$new_html .= '<div class="row">'.$html.'</div>';
    	return ['html' => $new_html, 'index' => $index, 'check_is_parent' => $check_is_parent];
    }


    /**
     * item to variation
     * @param  [type] $array_value 
     * @return [type]              
     */
    public function item_to_variation($array_value)
    {
    	$new_array=[];
    	foreach ($array_value as $key =>  $values) {

    	    $name = '';
    	    if($values['attributes'] != null && $values['attributes'] != ''){
    	    	$attributes_decode = json_decode($values['attributes']);

    	    	foreach ($attributes_decode as $n_value) {
    	    		if(is_array($n_value)){
    	    			foreach ($n_value as $n_n_value) {
    	    				if(new_strlen($name) > 0){
    	    					$name .= '#'.$n_n_value->name.' ( '.$n_n_value->option.' ) ';
    	    				}else{
    	    					$name .= ' #'.$n_n_value->name.' ( '.$n_n_value->option.' ) ';
    	    				}
    	    			}
    	    		}elseif(isset($n_value->name) && isset($n_value->option)){

    	    			if(new_strlen($name) > 0){
    	    				$name .= '#'.$n_value->name.' ( '.$n_value->option.' ) ';
    	    			}else{
    	    				$name .= ' #'.$n_value->name.' ( '.$n_value->option.' ) ';
    	    			}
    	    		}
    	    	}


    	    }
	    	array_push($new_array, [
	    		'id' => $values['id'],
	    		'label' => $values['commodity_code'].'_'.$values['description'],

	    	]);
    	}
    	return $new_array;
    }

    /**
     * row item to variation
     * @param  [type] $item_value 
     * @return [type]             
     */
    public function row_item_to_variation($item_value)
    {
    	if($item_value){
    		$item_value->new_description = $item_value->description;
	    }

    	return $item_value;
    }


    /**
     * get commodity id from barcode
     * @param  [type] $barcode 
     * @return [type]          
     */
    public function get_commodity_id_from_barcode($barcode)
    {
    	$this->db->where('commodity_barcode', $barcode);
    	$item_value = $this->db->get(db_prefix().'items')->row();
    	if($item_value){
    		return $item_value->id;
    	}else{
    		return 0;
    	}
    }	

    /**
     * get parent variation html
     * @param  [type] $variation_value 
     * @return [type]                  
     */
    public function get_parent_variation_html($variation_value)
    {
		$html = '';
		$index = 0;
		foreach ($variation_value as $key => $value) {
			$index++;
			$variation_attr =[];
            $variation_attr['rows'] = '1';

			if ($key == 0) {

				$html .= '<div id="item_approve">
				<div class="col-md-11">
				<div class="col-md-4">
				' . render_input('name['.$key.']','variation_name', $value->name, 'text') . '
				</div>
				<div class="col-md-8">
				<div class="options_wrapper">
				<span class="pull-left fa fa-question-circle" data-toggle="tooltip" title="" data-original-title="Populate the field by separating the options by coma. eq. apple,orange,banana"></span>
				' . render_textarea('options['.$key.']', 'variation_options', implode(",", $value->options) , $variation_attr) . '
				</div>
				</div>
				</div>
				<div class="col-md-1 new_vendor_requests_button" >
				<span class="pull-bot">
				<button name="add" class="btn new_wh_approval btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
				</span>
				</div>
				</div>';
			} else {
				$html .= '<div id="item_approve">
				<div class="col-md-11">
				<div class="col-md-4">
				' . render_input('name['.$key.']','variation_name', $value->name, 'text') . '
				</div>
				<div class="col-md-8">
				<div class="options_wrapper">
				<span class="pull-left fa fa-question-circle" data-toggle="tooltip" title="" data-original-title="Populate the field by separating the options by coma. eq. apple,orange,banana"></span>
				' . render_textarea('options['.$key.']', 'variation_options', implode(",", $value->options) , $variation_attr) . '
				</div>
				</div>
				</div>
				<div class="col-md-1 new_vendor_requests_button" >
				<span class="pull-bot">
				<button name="add" class="btn remove_wh_approval btn-danger" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
				</span>
				</div>
				</div>';
			}
		}

		return ['html' => $html, 'index' => $index];
    }

    /**
     * { update warehouse setting }
     *
     * @param         $data   The data
     *
     * @return     boolean 
     */
    public function update_pc_options_setting($data)
    {

            $val = $data['input_name_status'] == 'true' ? 1 : 0;
            $this->db->where('name',$data['input_name']);
            $this->db->update(db_prefix() . 'options', [
                    'value' => $val,
                ]);
            if ($this->db->affected_rows() > 0) {
                return true;
            }else{
                return false;
            }
    }

    /**
     * Gets the product by parent identifier.
     *
     * @param        $parent_id  The parent identifier
     *
     * @return       The product by parent identifier.
     */
    public function get_product_by_parent_id($parent_id)
    {
        $this->db->where('parent_id', $parent_id);
        $items =  $this->db->get(db_prefix() . 'items')->result_array();
        return $items;
    }

	/*NEED override this function when merger to master branches START*/

    /**
     * get inventory quantity by warehouse variant
     * @param  [type] $commodity_id 
     * @return [type]               
     */
    public function get_inventory_quantity_by_warehouse_variant($commodity_id)
    {
    	$result_array = [];

        $products = $this->get_product_by_parent_id($commodity_id);

    	if(count($products) > 0){
    		foreach ($products as $value) {
    		    $inventory_quantity = $this->get_inventory_quantity_by_warehouse($value['id']);
    		    $result_array = array_merge($result_array, $inventory_quantity);
    		}
    	}else{
    		$inventory_quantity = $this->get_inventory_quantity_by_warehouse($commodity_id);
    		$result_array = array_merge($result_array, $inventory_quantity);
    	}

    	return $result_array;

    }

    /**
     * get inventory quantity by warehouse
     * @param  [type] $commodity_id 
     * @return [type]               
     */
    public function get_inventory_quantity_by_warehouse($commodity_id)
    {

    	$result_array = [];

    	$arr_warehouse = get_warehouse_by_commodity($commodity_id);

    	$str = '';
    	if(count($arr_warehouse) > 0){
    		foreach ($arr_warehouse as $wh_key => $warehouseid) {
    			$str = '';
    			if ($warehouseid['warehouse_id'] != '' && $warehouseid['warehouse_id'] != '0') {
					//get inventory quantity
    				$inventory_quantity = $this->get_quantity_inventory_group_by($warehouseid['warehouse_id'], $commodity_id);

    				foreach ($inventory_quantity as $value) {
    					array_push($result_array, [
    						'id' => $value['id'],
    						'warehouse_id' => $value['warehouse_id'],
    						'commodity_id' => $value['commodity_id'],
    						'lot_number' => $value['lot_number'],
    						'expiry_date' => $value['expiry_date'],
    						'inventory_number' => $value['inventory_number'],
    					]);   
    				}

    			}
    		}

    	}else{
    		array_push($result_array, [
    			'commodity_id' => $commodity_id,
    		]);  
    	}

    	return $result_array;

    }
	/*NEED override this function when merger to master branches END*/


    /**
     * get quantity inventory group by
     * @param  [type] $warehouse_id 
     * @param  [type] $commodity_id 
     * @return [type]               
     */
    public function get_quantity_inventory_group_by($warehouse_id, $commodity_id)
    {
		$sql = 'SELECT * from ' . db_prefix() . 'inventory_manage where warehouse_id = ' . $warehouse_id . ' AND commodity_id = ' . $commodity_id;
		$result = $this->db->query($sql)->result_array();
		return $result;
	}

	/**
	 * add opening stock
	 * @param [type] $data 
	 */
	public function add_opening_stock($data)
	{
		$affectedRows=0;

		if (isset($data['item_add_opening_stock_hs'])) {
			$item_add_opening_stock_hs = $data['item_add_opening_stock_hs'];
			unset($data['item_add_opening_stock_hs']);
		}

		if(isset($item_add_opening_stock_hs)){
			$opening_stock_detail = json_decode($item_add_opening_stock_hs);

			$row = [];
			$row['update'] = []; 
			$row['insert'] = []; 

			$es_detail = [];
			$row = [];
			$header = [];

			$header[] = 'id';
			$header[] = 'commodity_id';
			$header[] = 'warehouse_id';
			$header[] = 'lot_number';
			$header[] = 'expiry_date';
			$header[] = 'inventory_number';

			
			foreach ($opening_stock_detail as $key => $value) {				

				if($value[1] != '' && $value[1] != null && $value[2] != '' && $value[2] != null && $value[5] !== '' && $value[5] !== null && $value[5] >= 0){
					$es_detail[] = array_combine($header, $value);
				}
			}

			foreach ($es_detail as $key => $value) {

				if($value['id'] != null){
					$row['update'][] = $value;
				}else{
					unset($value['id']);
					$row['insert'][] = $value;
				}

			}

			if(isset($row['insert']) && count($row['insert']) != 0){
				foreach ($row['insert'] as $in_value) {
					$purchase_price = $this->get_purchase_price_from_commodity_code($in_value['commodity_id']);

					$insert_temp=[];
					$insert_temp['warehouse_id']= $in_value['warehouse_id'];
					$insert_temp['commodity_code']= $in_value['commodity_id'];
					$insert_temp['quantities']= $in_value['inventory_number'];
					$insert_temp['date_manufacture']= null;
					$insert_temp['expiry_date']= $in_value['expiry_date'];
					$insert_temp['lot_number']= $in_value['lot_number'];
					$insert_temp['serial_number']= '';
					$insert_temp['unit_price']= $purchase_price;

					$affected_rows = $this->add_inventory_manage($insert_temp, 1);
					
					if ($affected_rows) {
						//add transaction log
						$transaction_data=[];

						$transaction_data['goods_receipt_id'] = 0;
						$transaction_data['purchase_price'] = $purchase_price;
						$transaction_data['expiry_date'] = $in_value['expiry_date'];
						$transaction_data['lot_number'] = $in_value['lot_number'];
						/*get old quantity by item, warehouse*/
						$inventory_value = $this->get_quantity_inventory($in_value['warehouse_id'], $in_value['commodity_id']);
						$old_quantity =  null;
						if($inventory_value){
							$old_quantity = $inventory_value->inventory_number;
						}

						$transaction_data['goods_id'] = 0;
						$transaction_data['old_quantity'] = (float)$old_quantity - (float)$in_value['inventory_number'];
						$transaction_data['commodity_id'] = $in_value['commodity_id'];
						$transaction_data['quantity'] = (float)$in_value['inventory_number'];
						$transaction_data['date_add'] = date('Y-m-d H:i:s');
						$transaction_data['warehouse_id'] = $in_value['warehouse_id'];
						$transaction_data['note'] = _l('import_opening_stock');
						$transaction_data['status'] = 1;

						$this->db->insert(db_prefix() . 'goods_transaction_detail', $transaction_data);

					}

					$affectedRows++;
				}
			}

			if(isset($row['update']) && count($row['update']) != 0){

				foreach ($row['update'] as $in_value) {

					$this->db->where('id', $in_value['id']);
					$inventory_manage  = $this->db->get(db_prefix().'inventory_manage')->row();

					if($inventory_manage){
						if( (float)$inventory_manage->inventory_number != (float)$in_value['inventory_number']){

						/*get old quantity by item, warehouse*/
						$inventory_value = $this->get_quantity_inventory($in_value['warehouse_id'], $in_value['commodity_id']);
						$_old_quantity =  null;
						if($inventory_value){
							$_old_quantity = $inventory_value->inventory_number;
						}


						if((float)$in_value['inventory_number'] > (float)$inventory_manage->inventory_number){
							//add
							$status = 1;
							$old_quantity  = (float)$_old_quantity;
							$quantity = (float)$in_value['inventory_number'] - (float)$inventory_manage->inventory_number;

						}else{
							//delivery
							$status = 2;

							$old_quantity = (float)$_old_quantity - ((float)$inventory_manage->inventory_number - (float)$in_value['inventory_number']);
							$quantity  = (float)$inventory_manage->inventory_number - (float)$in_value['inventory_number'];

							
						}

				   		//add transaction log
						$transaction_data=[];
						$purchase_price = $this->get_purchase_price_from_commodity_code($in_value['commodity_id']);

						$transaction_data['goods_receipt_id'] = 0;
						$transaction_data['purchase_price'] = $purchase_price;
						$transaction_data['expiry_date'] = $in_value['expiry_date'];
						$transaction_data['lot_number'] = $in_value['lot_number'];
						

						$transaction_data['goods_id'] = 0;
						$transaction_data['old_quantity'] = $old_quantity;
						$transaction_data['commodity_id'] = $in_value['commodity_id'];
						$transaction_data['quantity'] = $quantity;
						$transaction_data['date_add'] = date('Y-m-d H:i:s');
						$transaction_data['warehouse_id'] = $in_value['warehouse_id'];
						$transaction_data['note'] = _l('import_opening_stock');
						$transaction_data['status'] = $status;
						$this->db->insert(db_prefix() . 'goods_transaction_detail', $transaction_data);


						$inventory_manage_id = $in_value['id'];
						unset($in_value['id']);
						//update inventory quantity
						$this->db->where('id', $inventory_manage_id);
						$this->db->update(db_prefix() . 'inventory_manage', $in_value);
						

						}
					}
				}



			$affectedRows++;

			}

		}

		if ($affectedRows > 0) {
			return true;
		}
		return false;

	}

	/**
	 * wh get activity log
	 * @param  [type] $id   
	 * @param  [type] $type 
	 * @return [type]       
	 */
	public function wh_get_activity_log($id, $rel_type)
    {
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', $rel_type);
        $this->db->order_by('date', 'ASC');

        return $this->db->get(db_prefix() . 'wh_goods_delivery_activity_log')->result_array();
    }

    /**
     * log wh activity
     * @param  [type] $id              
     * @param  [type] $description     
     * @param  string $additional_data 
     * @return [type]                  
     */
    public function log_wh_activity($id, $rel_type, $description, $date = '')
    {
    	if(new_strlen($date) == 0){
    		$date = date('Y-m-d H:i:s');
    	}
        $log = [
            'date'            => $date,
            'description'     => $description,
            'rel_id'          => $id,
            'rel_type'          => $rel_type,
            'staffid'         => get_staff_user_id(),
            'full_name'       => get_staff_full_name(get_staff_user_id()),
        ];

        $this->db->insert(db_prefix() . 'wh_goods_delivery_activity_log', $log);
        $insert_id = $this->db->insert_id();
        if($insert_id){
        	if($rel_type == 'delivery' && get_option('notify_customer_when_change_delivery_status') == 1){
        		$this->notify_customer_shipment_status($id);
        	}
        	hooks()->do_action('affter_wh_logged', $insert_id);
        	return $insert_id;
        }
        return false;
    }

    /**
     * delete activitylog
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_activitylog($id)
    {
        $this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'wh_goods_delivery_activity_log');

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return false;
    }

    /**
     * get taxe value by ids
     * @param  [type] $id 
     * @return [type]     
     */
    public function get_taxe_value_by_ids($id)
   	{
   		return $this->db->query('select id, name as label, taxrate from '.db_prefix().'taxes where id IN ('.$id.')')->result_array();
   	}

   	/**
     * copy product image
     * @param  [type] $id 
     * @return [type]     
     */
    public function copy_product_image($id)
    {	
    	$arr_variant = $this->get_product_by_parent_id($id);
    	$attachments = $this->get_warehourse_attachments($id);

    	foreach ($arr_variant as $variant_id) {

    		if (is_dir(WAREHOUSE_ITEM_UPLOAD . $id)) {
    			xcopy(WAREHOUSE_ITEM_UPLOAD . $id, WAREHOUSE_ITEM_UPLOAD . $variant_id['id']);
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
     * delete attachment file
     * @param  [type] $attachment_id 
     * @param  [type] $folder_name   
     * @return [type]                
     */
    public function delete_attachment_file($attachment_id, $folder_name)
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
     * Gets the html tax receip.
     */
    public function get_html_tax_receip($id){
        $html = '';
        $preview_html = '';
        $html_currency = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];

        $this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();
        
        $this->db->where('goods_receipt_id', $id);
        $details = $this->db->get(db_prefix().'goods_receipt_detail')->result_array();

        foreach($details as $row){
            if($row['tax'] != ''){
                $tax_arr = new_explode('|', $row['tax']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = new_explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }
        }

        if(count($tax_name) > 0){
            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax'] ?? '', $taxes[$key]) === false)){
                        $tax_val[$key] += ($row_dt['quantities']*$row_dt['unit_price']*$t_rate[$key]/100);
                    }
                }
                $pdf_html .= '<tr id="subtotal"><td ></td><td></td><td></td><td class="text_left">'.$tn.'</td><td class="text_right">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], '').'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], '').'</td></tr>';
                $html_currency .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
            }
        }
        
        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        $rs['html_currency'] = $html_currency;
        return $rs;
    }

    /**
     * Gets the tax name.
     *
     * @param        $tax    The tax
     *
     * @return     string  The tax name.
     */
    public function get_tax_name($tax){
        $this->db->where('id', $tax);
        $tax_if = $this->db->get(db_prefix().'taxes')->row();
        if($tax_if){
            return $tax_if->name;
        }
        return '';
    }

    /**
     * { tax rate by id }
     *
     * @param        $tax_id  The tax identifier
     */
    public function tax_rate_by_id($tax_id){
        $this->db->where('id', $tax_id);
        $tax = $this->db->get(db_prefix().'taxes')->row();
        if($tax){
            return $tax->taxrate;
        }
        return 0;
    }

    /**
     * get purchase price from commodity code
     * @param  [type]  $commodity_code 
     * @param  boolean $sale_price     
     * @return [type]                  
     */
    public function get_purchase_price_from_commodity_code($commodity_code, $sale_price = false)
    {	
    	$purchase_price=0;

    	if(is_numeric($commodity_code)){
    		$this->db->where('id', $commodity_code);
    	}else{
    		$this->db->where('commodity_code', $commodity_code);
    	}
    	$item_value = $this->db->get(db_prefix().'items')->row();

    	if($item_value){
    		if($sale_price == false){
	    		$purchase_price = $item_value->purchase_price;
	    	}else{
	    		$purchase_price = $item_value->rate;

	    	}
    	}

    	return $purchase_price;
    }

    /**
     * commodity udpate profit rate
     * @param  [type] $id      
     * @param  [type] $percent 
     * @param  [type] $type    
     * @return [type]          
     */
    public function commodity_udpate_profit_rate($id, $percent, $type)
    {
    	$the_fractional_part = get_warehouse_option('warehouse_the_fractional_part');
    	$integer_part = get_warehouse_option('warehouse_integer_part');

    	$affected_rows=0;
    	$item = $this->get_commodity($id);
    	$profit_rate=0;

    	if($item){
    		$selling_price = (float)$item->rate;
    		$purchase_price = (float)$item->purchase_price;

    		if($type == 'selling_percent'){
    			//selling_percent
    			$new_selling_price = $selling_price + $selling_price*(float)$percent/100;
    	
    			if($integer_part != '0'){
    				$integer_part = 0 - (int)($integer_part);
    				$new_selling_price = round($new_selling_price, $integer_part);
    			}

    			$profit_rate = $this->caculator_profit_rate_model($purchase_price, $new_selling_price);

    			$this->db->where('id', $id);
    			$this->db->update(db_prefix().'items', ['rate' => $new_selling_price, 'profif_ratio' => $profit_rate]);
    			if ($this->db->affected_rows() > 0) {
    				$affected_rows++;
    			}

    		}else{
    			//purchase_percent
    			$new_purchase_price = $purchase_price + $purchase_price*(float)$percent/100;
    	
    			if($integer_part != '0'){
    				$integer_part = 0 - (int)($integer_part);
    				$new_purchase_price = round($new_purchase_price, $integer_part);
    			}

    			$profit_rate = $this->caculator_profit_rate_model($new_purchase_price, $selling_price);

    			$this->db->where('id', $id);
    			$this->db->update(db_prefix().'items', ['purchase_price' => $new_purchase_price, 'profif_ratio' => $profit_rate]);
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
     * get warehourse attachments
     * @param  integer $commodity_id 
     * @return array               
     */
    public function get_item_attachments($commodity_id){

        $this->db->order_by('dateadded', 'desc');
        $this->db->where('rel_id', $commodity_id);
        $this->db->where('rel_type', 'commodity_item_file');

        return $this->db->get(db_prefix() . 'files')->result_array();

    }

    /**
     * { clone_item }
     */
    public function clone_item($id){
        $current_items = $this->get_commodity($id);
        $item_attachments = $this->get_item_attachments($id);
        if($current_items){
            $item_data['description'] = $current_items->description;
            $item_data['purchase_price'] = $current_items->purchase_price;
            $item_data['unit_id'] = $current_items->unit_id;
            $item_data['rate'] = $current_items->rate;
            $item_data['sku_code'] = '';
            $item_data['commodity_barcode'] = $this->generate_commodity_barcode();
            $item_data['commodity_code'] = $this->generate_commodity_barcode();
            $item_data['group_id'] = $current_items->group_id;
            $item_data['sub_group'] = $current_items->sub_group;
            $item_data['tax'] = $current_items->tax;
            $item_data['commodity_type'] = $current_items->commodity_type;
            $item_data['warehouse_id'] = $current_items->warehouse_id;
            $item_data['profif_ratio'] = $current_items->profif_ratio;
            $item_data['origin'] = $current_items->origin;
            $item_data['style_id'] = $current_items->style_id;
            $item_data['model_id'] = $current_items->model_id;
            $item_data['size_id'] = $current_items->size_id;
            $item_data['color'] = $current_items->color;
            $item_data['guarantee'] = $current_items->guarantee;
            $item_data['without_checking_warehouse'] = $current_items->without_checking_warehouse;
            $item_data['long_description'] = $current_items->long_description;
            $item_id = $this->add_commodity_one_item_clone($item_data);
            if($item_id){
                if(count($item_attachments) > 0){
                	$source = WAREHOUSE_MODULE_UPLOAD_FOLDER.'/item_img/'.$id;
                    if(!is_dir($source)){
                    	if(get_status_modules_wh('purchase')){
                    		$source = PURCHASE_MODULE_UPLOAD_FOLDER.'/item_img/'.$id;
                    	}
                    }
                    $destination = WAREHOUSE_MODULE_UPLOAD_FOLDER.'/item_img/'.$item_id;
                    if(xcopy($source, $destination)){
                    	foreach($item_attachments as $attachment){
                        
                            $attachment_db   = [];
                            $attachment_db[] = [
                                'file_name' => $attachment['file_name'],
                                'filetype'  => $attachment['filetype'],
                                ];

                            $this->misc_model->add_attachment_to_database($item_id, 'commodity_item_file', $attachment_db);
                        }
                    }
                }

                $this->db->where('relid', $current_items->id);
                $this->db->where('fieldto', 'items_pr');
                $customfields = $this->db->get(db_prefix().'customfieldsvalues')->result_array();
                if(count($customfields) > 0){
                    foreach($customfields as $cf){
                        $this->db->insert(db_prefix().'customfieldsvalues', [
                            'relid' => $item_id,
                            'fieldid' => $cf['fieldid'],
                            'fieldto' => $cf['fieldto'],
                            'value' => $cf['value']
                        ]);
                    }
                }

                $this->db->where('rel_id', $current_items->id);
                $this->db->where('rel_type', 'item_tags');
                $tags = $this->db->get(db_prefix().'taggables')->result_array();
                if(count($tags) > 0){
                    foreach($tags as $tag){
                        $this->db->insert(db_prefix().'taggables', [
                            'rel_id' => $item_id,
                            'rel_type' => $tag['rel_type'],
                            'tag_id' => $tag['tag_id'],
                            'tag_order' => $tag['tag_order']
                        ]);

                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
	 * add commodity one item
	 * @param array $data
	 * @return integer
	 */
	public function add_commodity_one_item_clone($data) {
		
		$arr_insert_cf=[];
		$arr_variation=[];
		/*get custom fields*/
		if(isset($data['formdata'])){
			$arr_custom_fields=[];

			$arr_variation_temp=[];
			$variation_name_temp='';
			$variation_option_temp='';

			foreach ($data['formdata'] as $value_cf) {
				if(preg_match('/^custom_fields/', $value_cf['name'])){
					$index =  new_str_replace('custom_fields[items][', '', $value_cf['name']);
					$index =  new_str_replace(']', '', $index);

					$arr_custom_fields[$index] = $value_cf['value'];

				}

				//get variation 
				$variation_name_index=0;
				if(preg_match('/^name/', $value_cf['name'])){
					$variation_name_temp = $value_cf['value'];
				}

				if(preg_match('/^options/', $value_cf['name'])){
					$variation_option_temp = $value_cf['value'];

					array_push($arr_variation, [
						'name' => $variation_name_temp,
						'options' => new_explode(',', $variation_option_temp),
					]);

					$variation_name_temp='';
					$variation_option_temp='';
				}

			}

			$arr_insert_cf['items_pr'] = $arr_custom_fields;

			$formdata = $data['formdata'];
			unset($data['formdata']);
		}

		$data['parent_attributes'] = json_encode($arr_variation);

		if (isset($data['custom_fields'])) {
			$custom_fields = $data['custom_fields'];
			unset($data['custom_fields']);
		}

		/*add data tblitem*/
		$data['rate'] = $data['rate'];

		if(isset($data['purchase_price']) && $data['purchase_price']){
			
			$data['purchase_price'] = $data['purchase_price'];
		}

		/*create sku code*/
		if($data['sku_code'] != ''){
			$data['sku_code'] = get_warehouse_option('item_sku_prefix').new_str_replace(' ', '', $data['sku_code']) ;
			$sku_code_none_prefix = new_str_replace(' ', '', $data['sku_code']) ;

		}else{
			//data sku_code = group_character.sub_code.commodity_str_betwen.next_commodity_id; // X_X_000.id auto increment
			$data['sku_code'] = get_warehouse_option('item_sku_prefix').$this->create_sku_code($data['group_id'], isset($data['sub_group']) ? $data['sub_group'] : '' );
			$sku_code_none_prefix = $this->create_sku_code($data['group_id'], isset($data['sub_group']) ? $data['sub_group'] : '' );

			/*create sku code*/
		}

		// if(get_warehouse_option('barcode_with_sku_code') == 1){
		// 	$data['commodity_barcode'] = $sku_code_none_prefix;
		// }

		$tags = '';
		if (isset($data['tags'])) {
			$tags = $data['tags'];
			unset($data['tags']);
		}


		$this->db->insert(db_prefix() . 'items', $data);
		$insert_id = $this->db->insert_id();

		/*add data tblinventory*/
		if ($insert_id) {
			$data_inventory_min['commodity_id'] = $insert_id;
			$data_inventory_min['commodity_code'] = $data['commodity_code'];
			$data_inventory_min['commodity_name'] = $data['description'];
			$this->add_inventory_min($data_inventory_min);

			/*habdle add tags*/
			handle_tags_save($tags, $insert_id, 'item_tags');


			/*handle custom fields*/

			if(isset($formdata)){
				$data_insert_cf = [];

				handle_custom_fields_post($insert_id, $arr_insert_cf, true);
			}

			hooks()->do_action('item_created', $insert_id);

			log_activity('New Warehouse Item Added [ID:' . $insert_id . ', ' . $data['description'] . ']');


		}

		return $insert_id;

	}

	/**
     * item_attachments
     * @return [type] 
     */
    public function item_attachments() {
    	$arr_images = [];

		$this->db->order_by('dateadded', 'desc');
		$this->db->where('rel_type', 'commodity_item_file');
		$item_atts = $this->db->get(db_prefix() . 'files')->result_array();
		foreach ($item_atts as $key => $value) {
		    $arr_images[$value['rel_id']][] = $value;
		}

		return $arr_images;
	}

	/**
	 * arr inventory min
	 * @param  [type] $commodity_id 
	 * @return [type]               
	 */
	public function arr_inventory_min($inventory = false)
	{	
		$arr_inventory_min = [];
		$inventory_commodity_min = [];
		$inventory_number_arr = [];

		$inventory_min = $this->db->get(db_prefix() . 'inventory_commodity_min')->result_array();
		$inventory_numbers = $this->db->query('SELECT commodity_id, sum(inventory_number) as inventory_number FROM ' . db_prefix() . 'inventory_manage
		 group by ' . db_prefix() . 'inventory_manage.warehouse_id, '.db_prefix().'inventory_manage.commodity_id')->result_array(); 

		foreach ($inventory_min as $key => $value) {
		    $inventory_commodity_min[$value['commodity_id']] = $value;
		}

		if($inventory){
			return $inventory_commodity_min;
		}

		foreach ($inventory_numbers as $key => $value) {

			$inventory_min = false;
			if(isset($inventory_commodity_min[$value['commodity_id']])){
				if((float)$inventory_commodity_min[$value['commodity_id']]['inventory_number_min'] >= (float)$value['inventory_number'] ){
					$inventory_min = true;
				}
			}

		   $inventory_number_arr[$value['commodity_id']] = $inventory_min;
		}
		return $inventory_number_arr;
	}

	/**
	 * arr commodity group
	 * @return [type] 
	 */
	public function arr_commodity_group()
	{
		$arr_commodity_group = [];
	    $commodity_groups = $this->get_commodity_group_type();
	    foreach ($commodity_groups as $key => $value) {
	        $arr_commodity_group[$value['id']] = $value;
	    }

	    return $arr_commodity_group;
	}

	/**
	 * arr warehouse by item
	 * @return [type] 
	 */
	public function arr_warehouse_by_item()
	{
		$arr_warehouse = [];

		$inventory_numbers = $this->db->query('SELECT commodity_id, warehouse_id, sum(inventory_number) as inventory_number FROM ' . db_prefix() . 'inventory_manage
		 group by ' . db_prefix() . 'inventory_manage.warehouse_id, '.db_prefix().'inventory_manage.commodity_id')->result_array(); 
		foreach ($inventory_numbers as $key => $value) {
		    $arr_warehouse[$value['commodity_id']][] = $value;
		}

        return $arr_warehouse;
	}

	/**
	 * arr_warehouse_id
	 * @return [type] 
	 */
	public function arr_warehouse_id()
	{	
		$arr_warehouse = [];
		$warehouses = $this->get_warehouse();
		foreach ($warehouses as $key => $value) {
		    $arr_warehouse[$value['warehouse_id']] = $value;
		}
		return $arr_warehouse;
	}

	/**
	 * arr inventory number by item
	 * @return [type] 
	 */
	public function arr_inventory_number_by_item()
	{	
		$arr_inventory_number = [];
		$sql = 'SELECT commodity_id, sum(inventory_number) as inventory_number FROM ' . db_prefix() . 'inventory_manage
		 group by ' . db_prefix() . 'inventory_manage.commodity_id';
		$data = $this->db->query($sql)->result_array(); 

		foreach ($data as $key => $value) {
		    $arr_inventory_number[$value['commodity_id']] = $value;
		}
		return $arr_inventory_number;
	}

	/**
	 * ar item have variation
	 * @return [type] 
	 */
	public function arr_item_have_variation()
	{

		$arr_variation = [];
		$sql = 'SELECT parent_id, count(id) as total_child FROM ' . db_prefix() . 'items
		 group by ' . db_prefix() . 'items.parent_id';
		$data = $this->db->query($sql)->result_array(); 
		foreach ($data as $key => $value) {
		    $arr_variation[$value['parent_id']] = $value;
		}
		 return $arr_variation;
	}

	/**
	 * wh get grouped
	 * @return [type] 
	 */
	public function wh_get_grouped($can_be = '', $search_all = false)
	{
		$items = [];
		$this->db->order_by('name', 'asc');
		$groups = $this->db->get(db_prefix() . 'items_groups')->result_array();

		array_unshift($groups, [
			'id'   => 0,
			'name' => '',
		]);

		foreach ($groups as $group) {
			$this->db->select('*,' . db_prefix() . 'items_groups.name as group_name,' . db_prefix() . 'items.id as id, CONCAT(description, "(", IFNULL(( SELECT sum(inventory_number)  from '.db_prefix().'inventory_manage where '.db_prefix().'items.id = '.db_prefix().'inventory_manage.commodity_id group by commodity_id), 0),")") as description');
			if(new_strlen($can_be) > 0){
				$this->db->where($can_be, $can_be);
			}
			if(!$search_all){
				$this->db->where(db_prefix().'items.id not in ( SELECT distinct parent_id from '.db_prefix().'items WHERE parent_id is not null AND parent_id != "0" )');
			}
			$this->db->where('group_id', $group['id']);
			$this->db->where(db_prefix().'items.active', 1);
			$this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
			$this->db->order_by('description', 'asc');

			$_items = $this->db->get(db_prefix() . 'items')->result_array();

			if (count($_items) > 0) {
				$items[$group['id']] = [];
				foreach ($_items as $i) {
					array_push($items[$group['id']], $i);
				}
			}
		}

		return $items;
	}

    /**
     * get parent item
     * @return [type] 
     */
	public function get_parent_item_grouped($id = false)
	{

		$items = [];
        $this->db->order_by('name', 'asc');
        $groups = $this->db->get(db_prefix() . 'items_groups')->result_array();

        array_unshift($groups, [
            'id'   => 0,
            'name' => '',
        ]);


        foreach ($groups as $group) {
            $this->db->select('*,' . db_prefix() . 'items_groups.name as group_name,' . db_prefix() . 'items.id as id');
  
            $this->db->where('(parent_id is null OR parent_id = " ") AND group_id = '.$group['id'].' AND '.db_prefix().'items.active = 1');
            if(is_numeric($id) && $id != 0 ){
            	$this->db->where(db_prefix().'items.id', $id);
            }

            $this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
            $this->db->order_by('description', 'asc');
            $_items = $this->db->get(db_prefix() . 'items')->result_array();
            if (count($_items) > 0) {
                $items[$group['id']] = [];
                foreach ($_items as $i) {
                    array_push($items[$group['id']], $i);
                }
            }
        }
		return $items;
	}

	/**
	 * wh parent item search
	 * @param  [type] $q 
	 * @return [type]    
	 */
	public function wh_parent_item_search($q)
	{
		$this->db->select('rate, id, description as name, long_description as subtext, commodity_code');
		$this->db->group_start();
		$this->db->like('description', $q);
		$this->db->or_like('long_description', $q);
		$this->db->or_like('commodity_code', $q);
		$this->db->or_like('sku_code', $q);
		$this->db->group_end();
		$this->db->group_start();
		$this->db->where('parent_id', null);
		$this->db->or_where('parent_id', '');
		$this->db->or_where('parent_id', 0);
		$this->db->group_end();
		$this->db->order_by('id', 'desc');
		$this->db->limit(500);

		$items = $this->db->get(db_prefix() . 'items')->result_array();

		foreach ($items as $key => $item) {
			$items[$key]['subtext'] = strip_tags(mb_substr($item['subtext'], 0, 200)) . '...';
			$items[$key]['name']    = '(' . app_format_number($item['rate']) . ') ' .$item['commodity_code'].'-'. $item['name'];
		}

		return $items;

	}

	/**
	 * wh commodity code search
	 * @param  [type] $q 
	 * @return [type]    
	 */
	public function wh_commodity_code_search($q, $type, $can_be = '', $search_all = false)
	{

		$this->db->select('rate, id, description as name, long_description as subtext, commodity_code, purchase_price, CONCAT(description, "(", IFNULL(( SELECT sum(inventory_number)  from '.db_prefix().'inventory_manage where '.db_prefix().'items.id = '.db_prefix().'inventory_manage.commodity_id group by commodity_id), 0),")") as description_iv');
		
		$this->db->group_start();
		$this->db->like('description', $q);
		$this->db->or_like('long_description', $q);
		$this->db->or_like('commodity_code', $q);
		$this->db->or_like('sku_code', $q);
		$this->db->group_end();
		if(new_strlen($can_be) > 0){
			$this->db->where($can_be, $can_be);
		}
		$this->db->where('active', 1);
		if(!$search_all){
			$this->db->where('id not in ( SELECT distinct parent_id from '.db_prefix().'items WHERE parent_id is not null AND parent_id != "0" )');
		}
		$this->db->order_by('id', 'desc');
		$this->db->limit(500);

		$items = $this->db->get(db_prefix() . 'items')->result_array();

		foreach ($items as $key => $item) {
			$items[$key]['subtext'] = strip_tags(mb_substr($item['subtext'], 0, 200)) . '...';
			if($type == 'rate'){
				$items[$key]['name']    = '(' . app_format_number($item['rate']) . ') ' .$item['commodity_code'].'-'. $item['description_iv'];
			}else{
				$items[$key]['name']    = '(' . app_format_number($item['purchase_price']) . ') ' .$item['commodity_code'].'-'. $item['description_iv'];
			}

		}

		return $items;
	}

	public function get_item_v2($id = '')
    {
        $columns             = $this->db->list_fields(db_prefix() . 'items');
        $rateCurrencyColumns = '';
        foreach ($columns as $column) {
            if (strpos($column, 'rate_currency_') !== false) {
                $rateCurrencyColumns .= $column . ',';
            }
        }
        $this->db->select($rateCurrencyColumns . '' . db_prefix() . 'items.id as itemid,rate,
            t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,
            t2.taxrate as taxrate_2,t2.id as taxid_2,t2.name as taxname_2,
            CONCAT(commodity_code,"_",description) as code_description,long_description,group_id,' . db_prefix() . 'items_groups.name as group_name,unit,'.db_prefix().'ware_unit_type.unit_name as unit_name, purchase_price, unit_id, guarantee, without_checking_warehouse');
        $this->db->from(db_prefix() . 'items');
        $this->db->join('' . db_prefix() . 'taxes t1', 't1.id = ' . db_prefix() . 'items.tax', 'left');
        $this->db->join('' . db_prefix() . 'taxes t2', 't2.id = ' . db_prefix() . 'items.tax2', 'left');
        $this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->join(db_prefix() . 'ware_unit_type', '' . db_prefix() . 'ware_unit_type.unit_type_id = ' . db_prefix() . 'items.unit_id', 'left');
        $this->db->order_by('description', 'asc');
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'items.id', $id);

            return $this->db->get()->row();
        }

        return $this->db->get()->result_array();
    }

    /**
     * create goods receipt row template
     * @param  array   $warehouse_data   
     * @param  string  $name             
     * @param  string  $commodity_name   
     * @param  string  $warehouse_id     
     * @param  string  $quantities       
     * @param  string  $unit_name        
     * @param  string  $unit_price       
     * @param  string  $taxname          
     * @param  string  $lot_number       
     * @param  string  $date_manufacture 
     * @param  string  $expiry_date      
     * @param  string  $commodity_code   
     * @param  string  $unit_id          
     * @param  string  $tax_rate         
     * @param  string  $tax_money        
     * @param  string  $goods_money      
     * @param  string  $note             
     * @param  string  $item_key         
     * @param  string  $sub_total        
     * @param  string  $tax_name         
     * @param  string  $tax_id           
     * @param  boolean $is_edit          
     * @return [type]                    
     */
    public function create_goods_receipt_row_template($warehouse_data = [], $name = '', $commodity_name = '', $warehouse_id = '', $quantities = '', $unit_name = '', $unit_price = '', $taxname = '', $lot_number = '', $date_manufacture = '', $expiry_date = '', $commodity_code = '', $unit_id = '', $tax_rate = '', $tax_money = '', $goods_money = '', $note = '', $item_key = '', $sub_total = '', $tax_name = '', $tax_id = '', $is_edit = false, $serial_number = '') {
		
		$this->load->model('invoice_items_model');
		$row = '';

		$name_commodity_code = 'commodity_code';
		$name_commodity_name = 'commodity_name';
		$name_warehouse_id = 'warehouse_id';
		$name_unit_id = 'unit_id';
		$name_unit_name = 'unit_name';
		$name_quantities = 'quantities';
		$name_unit_price = 'unit_price';
		$name_tax_id_select = 'tax_select';
		$name_tax_id = 'tax';
		$name_tax_money = 'tax_money';
		$name_goods_money = 'goods_money';
		$name_date_manufacture = 'date_manufacture';
		$name_expiry_date = 'expiry_date';
		$name_note = 'note';
		$name_lot_number = 'lot_number';
		$name_tax_rate = 'tax_rate';
		$name_tax_name = 'tax_name';
		$array_attr = [];
		$array_attr_payment = ['data-payment' => 'invoice'];
		$name_sub_total = 'sub_total';
		$name_serial_number = 'serial_number';

		$array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
		$array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
		$str_rate_attr = 'min="0.0" step="any"';

		$lot_number_attr = ['placeholder' => _l('lot_number')];

		if(count($warehouse_data) == 0){
			$warehouse_data = $this->get_warehouse();
		}

		if ($name == '') {
			$row .= '<tr class="main">
                  <td></td>';
			$vehicles = [];
			$array_attr = ['placeholder' => _l('unit_price')];

			$manual             = true;
			$invoice_item_taxes = '';
			$amount = '';
			$sub_total = 0;

			if(get_option('auto_generate_lotnumber') && !$is_edit){
				$lot_number_attr = ['placeholder' => _l('lot_number'), 'data-toggle' => 'tooltip', 'data-original-title' => _l('wh_automatically_generate_batch_numbers')];
			}

		} else {
			$row .= '<tr class="sortable item">
					<td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
			$name_commodity_code = $name . '[commodity_code]';
			$name_commodity_name = $name . '[commodity_name]';
			$name_warehouse_id = $name . '[warehouse_id]';
			$name_unit_id = $name . '[unit_id]';
			$name_unit_name = '[unit_name]';
			$name_quantities = $name . '[quantities]';
			$name_unit_price = $name . '[unit_price]';
			$name_tax_id_select = $name . '[tax_select][]';
			$name_tax_id = $name . '[tax]';
			$name_tax_money = $name . '[tax_money]';
			$name_goods_money = $name . '[goods_money]';
			$name_date_manufacture = $name . '[date_manufacture]';
			$name_expiry_date = $name . '[expiry_date]';
			$name_note = $name . '[note]';
			$name_lot_number = $name . '[lot_number]';
			$name_tax_rate = $name . '[tax_rate]';
			$name_tax_name = $name .'[tax_name]';
			$name_sub_total = $name .'[sub_total]';
			$name_serial_number = $name .'[serial_number]';

			$array_rate_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('unit_price')];

			$array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities];

			//case for delivery note: only get warehouse available quantity

			$manual             = false;

			$tax_money = 0;
			$tax_rate_value = 0;

			if($is_edit){
				$invoice_item_taxes = wh_convert_item_taxes($tax_id, $tax_rate, $tax_name);
				$arr_tax_rate = new_explode('|', $tax_rate);
				foreach ($arr_tax_rate as $key => $value) {
					$tax_rate_value += (float)$value;
				}
			}else{
				$invoice_item_taxes = $taxname;
				$tax_rate_data = $this->wh_get_tax_rate($taxname);
				$tax_rate_value = $tax_rate_data['tax_rate'];
			}

			if((float)$tax_rate_value != 0){
				$tax_money = (float)$unit_price * (float)$quantities * (float)$tax_rate_value / 100;
				$goods_money = (float)$unit_price * (float)$quantities + (float)$tax_money;
				$amount = (float)$unit_price * (float)$quantities + (float)$tax_money;
			}else{
				$goods_money = (float)$unit_price * (float)$quantities;
				$amount = (float)$unit_price * (float)$quantities;
			}

			$sub_total = (float)$unit_price * (float)$quantities;
			$amount = app_format_number($amount);

		}
		$clients_attr = ["onchange" => "get_vehicle('" . $name_commodity_code . "','" . $name_unit_id . "','" . $name_warehouse_id . "');", "data-none-selected-text" => _l('customer_name'), 'data-customer_id' => 'invoice'];

		$row .= '<td class="">' . render_textarea($name_commodity_name, '', $commodity_name, ['rows' => 2, 'placeholder' => _l('item_description_placeholder'), 'readonly' => true] ) . '</td>';
		$row .= '<td class="warehouse_select">' .
		// render_select_with_input_group($name_warehouse_id, $warehouse_data,array('warehouse_id','warehouse_name'),'',$warehouse_id,'<a href="javascript:void(0)" onclick="new_vehicle_reg(this,\''.$name_commodity_code.'\', \''.$name_warehouse_id.'\');return false;"><i class="fa fa-plus"></i></a>', ["data-none-selected-text" => _l('warehouse_name')]).
		render_select($name_warehouse_id, $warehouse_data,array('warehouse_id','warehouse_name'),'',$warehouse_id,[], ["data-none-selected-text" => _l('warehouse_name')], 'no-margin').
		render_input($name_note, '', $note, 'text', ['placeholder' => _l('commodity_notes')], [], 'no-margin', 'input-transparent text-left').
		'</td>';
		$row .= '<td class="quantities">' . 
		render_input($name_quantities, '', $quantities, 'number', $array_qty_attr, [], 'no-margin') . 
		render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right wh_input_none').
		'</td>';

		$row .= '<td class="rate">' . render_input($name_unit_price, '', $unit_price, 'number', $array_rate_attr) . '</td>';
		$row .= '<td class="taxrate">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';
		$row .= '<td>' . render_input($name_lot_number, '', $lot_number, 'text', $lot_number_attr) . '</td>';

		$row .= '<td>' . render_date_input($name_date_manufacture, '', $date_manufacture, ['placeholder' => _l('date_manufacture')]) . '</td>';
		$row .= '<td>' . render_date_input($name_expiry_date, '', $expiry_date, ['placeholder' => _l('expiry_date')]) . '</td>';
		$row .= '<td class="amount" align="right">' . $amount . '</td>';

		$row .= '<td class="hide commodity_code">' . render_input($name_commodity_code, '', $commodity_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
		$row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';
		$row .= '<td class="hide serial_number">' . render_input($name_serial_number, '', $serial_number, 'text', ['placeholder' => _l('serial_number')]) . '</td>';

		if(new_strlen($serial_number) > 0){
			$name_serial_number_tooltip = _l('wh_serial_number').': '.$serial_number;
		}else{
			$name_serial_number_tooltip = _l('wh_view_serial_number');
		}

		if ($name == '') {
			$row .= '<td><button type="button" onclick="wh_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
		} else {
			$row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="wh_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;" data-toggle="tooltip" data-original-title="'._l('delete').'"><i class="fa fa-trash"></i></a></td>';

			if(get_option('wh_products_by_serial')){
				$row .= '<td><a href="javascript:void(0)" class="btn btn-success pull-right" onclick="wh_view_serial_number( \''. $name_quantities . '\', \''. $name_serial_number . '\',\''. $name . '\'); return false;" data-toggle="tooltip" data-original-title="'.$name_serial_number_tooltip.'"><i class="fa fa-eye"></i></a></td>';
			}

		}
		$row .= '</tr>';
		return $row;
	}

	/**
	 * get taxes dropdown template
	 * @param  [type]  $name     
	 * @param  [type]  $taxname  
	 * @param  string  $type     
	 * @param  string  $item_key 
	 * @param  boolean $is_edit  
	 * @param  boolean $manual   
	 * @return [type]            
	 */
	public function get_taxes_dropdown_template($name, $taxname, $type = '', $item_key = '', $is_edit = false, $manual = false)
	{
        // if passed manually - like in proposal convert items or project
		if($taxname != '' && !is_array($taxname)){
			$taxname = new_explode(',', $taxname);
		}

		if ($manual == true) {
            // + is no longer used and is here for backward compatibilities
			if (is_array($taxname) || strpos($taxname, '+') !== false) {
				if (!is_array($taxname)) {
					$__tax = new_explode('+', $taxname);
				} else {
					$__tax = $taxname;
				}
                // Multiple taxes found // possible option from default settings when invoicing project
				$taxname = [];
				foreach ($__tax as $t) {
					$tax_array = new_explode('|', $t);
					if (isset($tax_array[0]) && isset($tax_array[1])) {
						array_push($taxname, $tax_array[0] . '|' . $tax_array[1]);
					}
				}
			} else {
				$tax_array = new_explode('|', $taxname);
                // isset tax rate
				if (isset($tax_array[0]) && isset($tax_array[1])) {
					$tax = get_tax_by_name($tax_array[0]);
					if ($tax) {
						$taxname = $tax->name . '|' . $tax->taxrate;
					}
				}
			}
		}
        // First get all system taxes
		$this->load->model('taxes_model');
		$taxes = $this->taxes_model->get();
		$i     = 0;
		foreach ($taxes as $tax) {
			unset($taxes[$i]['id']);
			$taxes[$i]['name'] = $tax['name'] . '|' . $tax['taxrate'];
			$i++;
		}
		if ($is_edit == true) {

            // Lets check the items taxes in case of changes.
            // Separate functions exists to get item taxes for Invoice, Estimate, Proposal, Credit Note
			$func_taxes = 'get_' . $type . '_item_taxes';
			if (function_exists($func_taxes)) {
				$item_taxes = call_user_func($func_taxes, $item_key);
			}

			foreach ($item_taxes as $item_tax) {
				$new_tax            = [];
				$new_tax['name']    = $item_tax['taxname'];
				$new_tax['taxrate'] = $item_tax['taxrate'];
				$taxes[]            = $new_tax;
			}
		}

        // In case tax is changed and the old tax is still linked to estimate/proposal when converting
        // This will allow the tax that don't exists to be shown on the dropdowns too.
		if (is_array($taxname)) {
			foreach ($taxname as $tax) {
                // Check if tax empty
				if ((!is_array($tax) && $tax == '') || is_array($tax) && $tax['taxname'] == '') {
					continue;
				};
                // Check if really the taxname NAME|RATE don't exists in all taxes
				if (!value_exists_in_array_by_key($taxes, 'name', $tax)) {
					if (!is_array($tax)) {
						$tmp_taxname = $tax;
						$tax_array   = new_explode('|', $tax);
					} else {
						$tax_array   = new_explode('|', $tax['taxname']);
						$tmp_taxname = $tax['taxname'];
						if ($tmp_taxname == '') {
							continue;
						}
					}
					$taxes[] = ['name' => $tmp_taxname, 'taxrate' => $tax_array[1]];
				}
			}
		}

        // Clear the duplicates
		$taxes = $this->wh_uniqueByKey($taxes, 'name');

		$select = '<select class="selectpicker display-block taxes" data-width="100%" name="' . $name . '" multiple data-none-selected-text="' . _l('no_tax') . '">';

		foreach ($taxes as $tax) {
			$selected = '';
			if (is_array($taxname)) {
				foreach ($taxname as $_tax) {
					if (is_array($_tax)) {
						if ($_tax['taxname'] == $tax['name']) {
							$selected = 'selected';
						}
					} else {
						if ($_tax == $tax['name']) {
							$selected = 'selected';
						}
					}
				}
			} else {
				if ($taxname == $tax['name']) {
					$selected = 'selected';
				}
			}

			$select .= '<option value="' . $tax['name'] . '" ' . $selected . ' data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
		}
		$select .= '</select>';

		return $select;
	}

	/**
	 * [get taxes dropdown template v2
	 * @param  [type]  $name     
	 * @param  [type]  $taxname  
	 * @param  string  $type     
	 * @param  string  $item_key 
	 * @param  boolean $is_edit  
	 * @param  boolean $manual   
	 * @return [type]            
	 */
	public function get_taxes_dropdown_template_v2($name, $taxname, $type = '', $item_key = '', $is_edit = false, $manual = false)
	{

        // if passed manually - like in proposal convert items or project
		if($taxname != '' && !is_array($taxname)){
			$taxname = new_explode(',', $taxname);
		}

		if ($manual == true) {
            // + is no longer used and is here for backward compatibilities
			if (is_array($taxname) || strpos($taxname, '+') !== false) {
				if (!is_array($taxname)) {
					$__tax = new_explode('+', $taxname);
				} else {
					$__tax = $taxname;
				}
                // Multiple taxes found // possible option from default settings when invoicing project
				$taxname = [];
				foreach ($__tax as $t) {
					$tax_array = new_explode('|', $t);
					if (isset($tax_array[0]) && isset($tax_array[1])) {
						array_push($taxname, $tax_array[0] . '|' . $tax_array[1]);
					}
				}
			} else {
				$tax_array = new_explode('|', $taxname);
                // isset tax rate
				if (isset($tax_array[0]) && isset($tax_array[1])) {
					$tax = get_tax_by_name($tax_array[0]);
					if ($tax) {
						$taxname = $tax->name . '|' . $tax->taxrate;
					}
				}
			}
		}
        // First get all system taxes
		$this->load->model('taxes_model');
		$taxes = $this->taxes_model->get();
		$i     = 0;
		foreach ($taxes as $tax) {
			unset($taxes[$i]['id']);
			$taxes[$i]['name'] = $tax['name'] . '|' . $tax['taxrate'];
			$i++;
		}
		if ($is_edit == true) {

            // Lets check the items taxes in case of changes.
            // Separate functions exists to get item taxes for Invoice, Estimate, Proposal, Credit Note
			$func_taxes = 'get_' . $type . '_item_taxes';
			if (function_exists($func_taxes)) {
				$item_taxes = call_user_func($func_taxes, $item_key);
			}

			foreach ($item_taxes as $item_tax) {
				$new_tax            = [];
				$new_tax['name']    = $item_tax['taxname'];
				$new_tax['taxrate'] = $item_tax['taxrate'];
				$taxes[]            = $new_tax;
			}
		}

        // In case tax is changed and the old tax is still linked to estimate/proposal when converting
        // This will allow the tax that don't exists to be shown on the dropdowns too.
		if (is_array($taxname)) {
			foreach ($taxname as $tax) {
                // Check if tax empty
				if ((!is_array($tax) && $tax == '') || is_array($tax) && $tax['taxname'] == '') {
					continue;
				};
                // Check if really the taxname NAME|RATE don't exists in all taxes
				if (!value_exists_in_array_by_key($taxes, 'name', $tax)) {
					if (!is_array($tax)) {
						$tmp_taxname = $tax;
						$tax_array   = new_explode('|', $tax);
					} else {
						$tax_array   = new_explode('|', $tax['taxname']);
						$tmp_taxname = $tax['taxname'];
						if ($tmp_taxname == '') {
							continue;
						}
					}
					$taxes[] = ['name' => $tmp_taxname, 'taxrate' => $tax_array[1]];
				}
			}
		}

        // Clear the duplicates
		$taxes = $this->wh_uniqueByKey($taxes, 'name');

		$select = '<select class="selectpicker display-block taxes" data-width="100%" name="' . $name . '" multiple data-none-selected-text="' . _l('no_tax') . '">';
		foreach ($taxes as $key => $tax) {
			$selected = '';
			if (is_array($taxname)) {

				foreach ($taxname as $_tax) {
					if (is_array($_tax)) {

						if ($_tax['taxname'] == $tax['name']) {
							$selected = 'selected';
						}
					} else {
						if ($_tax == $tax['name']) {
							$selected = 'selected';
						}
					}
				}
			} else {
				if ($taxname == $tax['name']) {
					$selected = 'selected';
				}
			}

			if($selected == ''){
				$selected = 'disabled';
			}
			$select .= '<option value="' . $tax['name'] . '" ' . $selected . ' data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
		}
		$select .= '</select>';

		return $select;
	}

	/**
	 * wh get tax rate
	 * @param  [type] $taxname 
	 * @return [type]          
	 */
	public function wh_get_tax_rate($taxname)
	{	
		$tax_rate = 0;
		$tax_rate_str = '';
		$tax_id_str = '';
		$tax_name_str = '';
		if(is_array($taxname)){
			foreach ($taxname as $key => $value) {
				$_tax = new_explode("|", $value);
				if(isset($_tax[1])){
					$tax_rate += (float)$_tax[1];
					if(new_strlen($tax_rate_str) > 0){
						$tax_rate_str .= '|'.$_tax[1];
					}else{
						$tax_rate_str .= $_tax[1];
					}

					$this->db->where('name', $_tax[0]);
					$taxes = $this->db->get(db_prefix().'taxes')->row();
					if($taxes){
						if(new_strlen($tax_id_str) > 0){
							$tax_id_str .= '|'.$taxes->id;
						}else{
							$tax_id_str .= $taxes->id;
						}
					}

					if(new_strlen($tax_name_str) > 0){
						$tax_name_str .= '|'.$_tax[0];
					}else{
						$tax_name_str .= $_tax[0];
					}
				}
			}
		}
		return ['tax_rate' => $tax_rate, 'tax_rate_str' => $tax_rate_str, 'tax_id_str' => $tax_id_str, 'tax_name_str' => $tax_name_str];
	}

	/**
	 * create internal delivery row template
	 * @param  array   $warehouse_data     
	 * @param  string  $name               
	 * @param  string  $commodity_name     
	 * @param  string  $from_stock_name    
	 * @param  string  $to_stock_name      
	 * @param  string  $available_quantity 
	 * @param  string  $quantities         
	 * @param  string  $unit_name          
	 * @param  string  $unit_price         
	 * @param  string  $commodity_code     
	 * @param  string  $unit_id            
	 * @param  string  $into_money         
	 * @param  string  $note               
	 * @param  string  $item_key           
	 * @param  boolean $is_edit            
	 * @return [type]                      
	 */
	public function create_internal_delivery_row_template($warehouse_data = [], $name = '', $commodity_name = '', $from_stock_name = '', $to_stock_name = '', $available_quantity = '', $quantities = '', $unit_name = '', $unit_price = '', $commodity_code = '', $unit_id = '', $into_money = '', $note = '', $item_key = '', $is_edit = false, $serial_number = '') {
		
		$row = '';

		$name_commodity_code = 'commodity_code';
		$name_commodity_name = 'commodity_name';
		$name_from_stock_name = 'from_stock_name';
		$name_to_stock_name = 'to_stock_name';
		$name_unit_id = 'unit_id';
		$name_unit_name = 'unit_name';
		$name_available_quantity = 'available_quantity';
		$name_quantities = 'quantities';
		$name_unit_price = 'unit_price';
		$name_into_money = 'into_money';
		$name_note = 'note';
		$array_attr = [];
		$array_attr_payment = ['data-payment' => 'invoice'];
		$name_sub_total = 'sub_total';
		$name_serial_number = 'serial_number';

		$array_available_quantity_attr = [ 'min' => '0.0', 'step' => 'any', 'readonly' => true];
		$array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
		$array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
		$str_rate_attr = 'min="0.0" step="any"';

		if(count($warehouse_data) == 0){
			$warehouse_data = $this->get_warehouse();
		}

		if ($name == '') {
			$row .= '<tr class="main">
                  <td></td>';
			$vehicles = [];
			$array_attr = ['placeholder' => _l('unit_price')];
			$from_stock_name_attr = [];

			$manual             = true;
			$invoice_item_taxes = '';
			$amount = '';
			$sub_total = 0;
			$into_money = 0;

		} else {
			$row .= '<tr class="sortable item">
					<td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
			$name_commodity_code = $name . '[commodity_code]';
			$name_commodity_name = $name . '[commodity_name]';
			$name_from_stock_name = $name . '[from_stock_name]';
			$name_to_stock_name = $name . '[to_stock_name]';
			$name_unit_id = $name . '[unit_id]';
			$name_unit_name = $name .'[unit_name]';
			$name_available_quantity = $name . '[available_quantity]';
			$name_quantities = $name . '[quantities]';
			$name_unit_price = $name . '[unit_price]';
			$name_into_money = $name . '[into_money]';
			$name_note = $name . '[note]';
			$name_sub_total = $name .'[sub_total]';
			$name_serial_number = $name .'[serial_number]';

			$array_rate_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('unit_price')];

			$array_available_quantity_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-available_quantity' => (float)$available_quantity, 'readonly' => true];

			if(new_strlen($serial_number) > 0){
				$array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities, 'readonly' => true];
			}else{
				$array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities];
			}

			$from_stock_name_attr = ["onchange" => "get_available_quantity('" . $name_commodity_code . "','" . $name_from_stock_name . "','" . $name_available_quantity . "');", "data-none-selected-text" => _l('customer_name'), 'data-from_stock_id' => 'invoice'];

			//case for delivery note: only get warehouse available quantity
			// $vehicles = $this->omni_sales_model->get_vehicles_reg_by_client($customer);

			$manual             = false;

			$tax_money = 0;
			$tax_rate_value = 0;


			if((float)$tax_rate_value != 0){
				$tax_money = (float)$unit_price * (float)$quantities * (float)$tax_rate_value / 100;
				$goods_money = (float)$unit_price * (float)$quantities + (float)$tax_money;
				$amount = (float)$unit_price * (float)$quantities + (float)$tax_money;
			}else{
				$goods_money = (float)$unit_price * (float)$quantities;
				$amount = (float)$unit_price * (float)$quantities;
			}

			$sub_total = (float)$unit_price * (float)$quantities;
			$into_money = (float)$unit_price * (float)$quantities;
			$amount = app_format_number($amount);

		}
		

		$row .= '<td class="">' . render_textarea($name_commodity_name, '', $commodity_name, ['rows' => 2, 'placeholder' => _l('item_description_placeholder'), 'readonly' => true] ) . '</td>';
		$row .= '<td class="warehouse_select">' .
		// render_select_with_input_group($name_warehouse_id, $warehouse_data,array('warehouse_id','warehouse_name'),'',$warehouse_id,'<a href="javascript:void(0)" onclick="new_vehicle_reg(this,\''.$name_commodity_code.'\', \''.$name_warehouse_id.'\');return false;"><i class="fa fa-plus"></i></a>', ["data-none-selected-text" => _l('warehouse_name')]).
		render_select($name_from_stock_name, $warehouse_data,array('warehouse_id','warehouse_name'),'',$from_stock_name, $from_stock_name_attr, ["data-none-selected-text" => _l('from_stock_name')], 'no-margin').
		render_input($name_note, '', $note, 'text', ['placeholder' => _l('commodity_notes')], [], 'no-margin', 'input-transparent text-left').
		'</td>';
		$row .= '<td class="to_warehouse_select">' .
		render_select($name_to_stock_name, $warehouse_data,array('warehouse_id','warehouse_name'),'',$to_stock_name,[], ["data-none-selected-text" => _l('to_stock_name')]).
		'</td>';
		
		$row .= '<td class="available_quantity">' . 
		render_input($name_available_quantity, '', $available_quantity, 'number', $array_available_quantity_attr, [], 'no-margin') . 
		render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right wh_input_none').
		'</td>';
		$row .= '<td class="quantities">' . 
		render_input($name_quantities, '', $quantities, 'number', $array_qty_attr) . 
		'</td>';

		$row .= '<td class="rate">' . render_input($name_unit_price, '', $unit_price, 'number', $array_rate_attr) . '</td>';
		$row .= '<td class="amount" align="right">' . $amount . '</td>';

		$row .= '<td class="hide commodity_code">' . render_input($name_commodity_code, '', $commodity_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
		$row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';
		$row .= '<td class="hide into_money">' . render_input($name_into_money, '', $into_money, 'number') . '</td>';
		$row .= '<td class="hide serial_number">' . render_input($name_serial_number, '', $serial_number, 'text', []) . '</td>';

		if ($name == '') {
			$row .= '<td><button type="button" onclick="wh_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
		} else {
			$row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="wh_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
		}
		$row .= '</tr>';
		return $row;
	}

	public function create_loss_adjustment_row_template($name = '', $commodity_name = '', $available_quantity = '', $quantities = '', $unit_name = '', $expiry_date = '', $lot_number = '', $commodity_code = '', $unit_id = '', $item_key = '', $is_edit = false, $serial_number = '') {
		
		$row = '';

		$name_commodity_code = 'items';
		$name_commodity_name = 'commodity_name';
		$name_unit_id = 'unit';
		$name_unit_name = 'unit_name';
		$name_available_quantity = 'current_number';
		$name_quantities = 'updates_number';
		$name_expiry_date = 'expiry_date';
		$name_lot_number = 'lot_number';
		$array_attr = [];
		$array_attr_payment = ['data-payment' => 'invoice'];
		$name_serial_number = 'serial_number';

		$array_available_quantity_attr = [ 'min' => '0.0', 'step' => 'any', 'readonly' => true];
		$array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
		$array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
		$str_rate_attr = 'min="0.0" step="any"';

		$lot_number_name_attr = ['placeholder' => _l('lot_number') ];
		$expiry_date_name_attr = ['placeholder' => _l('expiry_date')];

		if ($name == '') {
			$row .= '<tr class="main">
                  <td></td>';
			$vehicles = [];
			$array_attr = ['placeholder' => _l('unit_price')];
			$from_stock_name_attr = [];

			$manual             = true;
			$invoice_item_taxes = '';
			$amount = '';
			$sub_total = 0;
			$into_money = 0;

		} else {
			$row .= '<tr class="sortable item">
					<td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
			$name_commodity_code = $name . '[items]';
			$name_commodity_name = $name . '[commodity_name]';
			$name_unit_id = $name . '[unit]';
			$name_unit_name = $name .'[unit_name]';
			$name_available_quantity = $name . '[current_number]';
			$name_quantities = $name . '[updates_number]';
			$name_expiry_date = $name .'[expiry_date]';
			$name_lot_number = $name .'[lot_number]';
			$name_serial_number = $name .'[serial_number]';

			$array_rate_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('unit_price')];

			$array_available_quantity_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-available_quantity' => (float)$available_quantity, 'readonly' => true];
			$array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities, 'readonly' => true];

			$manual             = false;

			$lot_number_name_attr = ["onchange" => "la_get_available_quantity('" . $name_commodity_code . "','" . $name_lot_number . "','" . $name_expiry_date . "','" . $name_available_quantity . "');", 'placeholder' => _l('lot_number') ];
			$expiry_date_name_attr = ["onchange" => "la_get_available_quantity('" . $name_commodity_code . "','" . $name_lot_number . "','" . $name_expiry_date . "','" . $name_available_quantity . "');" , 'placeholder' => _l('expiry_date')];

		}
		

		$row .= '<td class="">' . render_textarea($name_commodity_name, '', $commodity_name, ['rows' => 2, 'placeholder' => _l('item_description_placeholder'), 'readonly' => true] ) . '</td>';
		$row .= '<td>' . render_input($name_lot_number, '', $lot_number, 'text', $lot_number_name_attr) . '</td>';
		$row .= '<td>' . render_date_input($name_expiry_date, '', $expiry_date, $expiry_date_name_attr) . '</td>';
		
		$row .= '<td class="available_quantity">' . 
		render_input($name_available_quantity, '', $available_quantity, 'number', $array_available_quantity_attr, [], 'no-margin') . 
		render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right wh_input_none').
		'</td>';
		$row .= '<td class="quantities">' . 
		render_input($name_quantities, '', $quantities, 'number', $array_qty_attr) . 
		'</td>';
		$row .= '<td class="hide serial_number">' . render_input($name_serial_number, '', $serial_number, 'text', []) . '</td>';

		$row .= '<td class="hide commodity_code">' . render_input($name_commodity_code, '', $commodity_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
		$row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';
		
		if(new_strlen($serial_number) > 0){
			$name_serial_number_tooltip = _l('wh_serial_number').': '.$serial_number;
		}else{
			$name_serial_number_tooltip = _l('wh_view_serial_number');
		}

		if ($name == '') {
			$row .= '<td><button type="button" onclick="wh_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
		} else {
			$row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="wh_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
			if(get_option('wh_products_by_serial')){
				if($available_quantity > $quantities){
					$row .= '<td><a href="javascript:void(0)" class="btn btn-success pull-right" onclick="loss_wh_view_serial_number( \''. $name_available_quantity . '\',\''. $name_quantities . '\', \''. $name_serial_number . '\',\''. $name . '\'); return false;" data-toggle="tooltip" data-original-title="'.$name_serial_number_tooltip.'"><i class="fa fa-eye"></i></a></td>';
				}else{
					$row .= '<td><a href="javascript:void(0)" class="btn btn-success pull-right" onclick="adjustment_wh_view_serial_number(\''. $name_available_quantity . '\',\''. $name_quantities . '\', \''. $name_serial_number . '\',\''. $name . '\'); return false;" data-toggle="tooltip" data-original-title="'.$name_serial_number_tooltip.'"><i class="fa fa-eye"></i></a></td>';
				}
			}
		}
		$row .= '</tr>';
		return $row;
	}

	/**
	 * wh uniqueByKey
	 * @param  [type] $array 
	 * @param  [type] $key   
	 * @return [type]        
	 */
	public function wh_uniqueByKey($array, $key)
    {
        $temp_array = [];
        $i          = 0;
        $key_array  = [];

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i]  = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }

        return $temp_array;
    }

    /**
     * create goods delivery row template
     * @param  array   $warehouse_data       
     * @param  string  $name                 
     * @param  string  $commodity_name       
     * @param  string  $warehouse_id         
     * @param  string  $available_quantity   
     * @param  string  $quantities           
     * @param  string  $unit_name            
     * @param  string  $unit_price           
     * @param  string  $taxname              
     * @param  string  $commodity_code       
     * @param  string  $unit_id              
     * @param  string  $tax_rate             
     * @param  string  $total_money          
     * @param  string  $discount             
     * @param  string  $discount_money       
     * @param  string  $total_after_discount 
     * @param  string  $guarantee_period     
     * @param  string  $expiry_date          
     * @param  string  $lot_number           
     * @param  string  $note                 
     * @param  string  $sub_total            
     * @param  string  $tax_name             
     * @param  string  $tax_id               
     * @param  string  $item_key             
     * @param  boolean $is_edit              
     * @return [type]                        
     */
    public function create_goods_delivery_row_template($warehouse_data = [], $name = '', $commodity_name = '', $warehouse_id = '', $available_quantity = '', $quantities = '', $unit_name = '', $unit_price = '', $taxname = '',  $commodity_code = '', $unit_id = '', $tax_rate = '', $total_money = '', $discount = '', $discount_money = '', $total_after_discount = '', $guarantee_period = '', $expiry_date = '', $lot_number = '', $note = '',  $sub_total = '', $tax_name = '', $tax_id = '', $item_key = '',$is_edit = false, $is_purchase_order = false, $serial_number = '', $without_checking_warehouse = 0) {
		
		$this->load->model('invoice_items_model');
		$row = '';

		$name_commodity_code = 'commodity_code';
		$name_commodity_name = 'commodity_name';
		$name_warehouse_id = 'warehouse_id';
		$name_unit_id = 'unit_id';
		$name_unit_name = 'unit_name';
		$name_available_quantity = 'available_quantity';
		$name_quantities = 'quantities';
		$name_unit_price = 'unit_price';
		$name_tax_id_select = 'tax_select';
		$name_tax_id = 'tax_id';
		$name_total_money = 'total_money';
		$name_lot_number = 'lot_number';
		$name_expiry_date = 'expiry_date';
		$name_note = 'note';
		$name_tax_rate = 'tax_rate';
		$name_tax_name = 'tax_name';
		$array_attr = [];
		$array_attr_payment = ['data-payment' => 'invoice'];
		$name_sub_total = 'sub_total';
		$name_discount = 'discount';
		$name_discount_money = 'discount_money';
		$name_total_after_discount = 'total_after_discount';
		$name_guarantee_period = 'guarantee_period';
		$name_serial_number = 'serial_number';
		$name_without_checking_warehouse = 'without_checking_warehouse';

		$array_available_quantity_attr = [ 'min' => '0.0', 'step' => 'any', 'readonly' => true];
		$array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
		$array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
		$array_discount_attr = [ 'min' => '0.0', 'step' => 'any'];
		$str_rate_attr = 'min="0.0" step="any"';

		if(count($warehouse_data) == 0){
			$warehouse_data = $this->get_warehouse();
		}

		if ($name == '') {
			$row .= '<tr class="main">
                  <td></td>';
			$vehicles = [];
			$array_attr = ['placeholder' => _l('unit_price')];
			$warehouse_id_name_attr = [];
			$manual             = true;
			$invoice_item_taxes = '';
			$amount = '';
			$sub_total = 0;

		} else {
			$row .= '<tr class="sortable item">
					<td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
			$name_commodity_code = $name . '[commodity_code]';
			$name_commodity_name = $name . '[commodity_name]';
			$name_warehouse_id = $name . '[warehouse_id]';
			$name_unit_id = $name . '[unit_id]';
			$name_unit_name = '[unit_name]';
			$name_available_quantity = $name . '[available_quantity]';
			$name_quantities = $name . '[quantities]';
			$name_unit_price = $name . '[unit_price]';
			$name_tax_id_select = $name . '[tax_select][]';
			$name_tax_id = $name . '[tax_id]';
			$name_total_money = $name . '[total_money]';
			$name_lot_number = $name . '[lot_number]';
			$name_expiry_date = $name . '[expiry_date]';
			$name_note = $name . '[note]';
			$name_tax_rate = $name . '[tax_rate]';
			$name_tax_name = $name .'[tax_name]';
			$name_sub_total = $name .'[sub_total]';
			$name_discount = $name .'[discount]';
			$name_discount_money = $name .'[discount_money]';
			$name_total_after_discount = $name .'[total_after_discount]';
			$name_guarantee_period = $name .'[guarantee_period]';
			$name_serial_number = $name .'[serial_number]';
			$name_without_checking_warehouse = $name .'[without_checking_warehouse]';

			$warehouse_id_name_attr = ["onchange" => "get_available_quantity('" . $name_commodity_code . "','" . $name_warehouse_id . "','" . $name_available_quantity . "');", "data-none-selected-text" => _l('warehouse_name'), 'data-from_stock_id' => 'invoice'];
			$array_available_quantity_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-available_quantity' => (float)$available_quantity, 'readonly' => true];
			if($is_purchase_order){
				$array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities, 'readonly' => true];
			}elseif(new_strlen($serial_number) > 0){
				$array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities, 'readonly' => true];
			}else{
				$array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities];
			}

			$array_rate_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate')];
			$array_discount_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];


			$manual             = false;

			$tax_money = 0;
			$tax_rate_value = 0;

			if($is_edit){
				$invoice_item_taxes = wh_convert_item_taxes($tax_id, $tax_rate, $tax_name);
				$arr_tax_rate = new_explode('|', $tax_rate);
				foreach ($arr_tax_rate as $key => $value) {
					$tax_rate_value += (float)$value;
				}
			}else{
				$invoice_item_taxes = $taxname;
				$tax_rate_data = $this->wh_get_tax_rate($taxname);
				$tax_rate_value = $tax_rate_data['tax_rate'];
			}

			if((float)$tax_rate_value != 0){
				$tax_money = (float)$unit_price * (float)$quantities * (float)$tax_rate_value / 100;
				$goods_money = (float)$unit_price * (float)$quantities + (float)$tax_money;
				$amount = (float)$unit_price * (float)$quantities + (float)$tax_money;
			}else{
				$goods_money = (float)$unit_price * (float)$quantities;
				$amount = (float)$unit_price * (float)$quantities;
			}

			$sub_total = (float)$unit_price * (float)$quantities;
			$amount = app_format_number($amount);

		}
		$clients_attr = ["onchange" => "get_vehicle('" . $name_commodity_code . "','" . $name_unit_id . "','" . $name_warehouse_id . "');", "data-none-selected-text" => _l(''), 'data-customer_id' => 'invoice'];

		$row .= '<td class="">' . render_textarea($name_commodity_name, '', $commodity_name, ['rows' => 2, 'placeholder' => _l('item_description_placeholder'), 'readonly' => true] ) . '</td>';


		$row .= '<td class="warehouse_select">' .
		render_select($name_warehouse_id, $warehouse_data,array('warehouse_id','warehouse_name'),'',$warehouse_id, $warehouse_id_name_attr, ["data-none-selected-text" => _l('warehouse_name')], 'no-margin').
		render_input($name_note, '', $note, 'text', ['placeholder' => _l('commodity_notes')], [], 'no-margin', 'input-transparent text-left').
		'</td>';
		$row .= '<td class="available_quantity">' . 
		render_input($name_available_quantity, '', $available_quantity, 'number', $array_available_quantity_attr, [], 'no-margin') . 
		render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right wh_input_none').
		'</td>';
		$row .= '<td class="quantities">' . render_input($name_quantities, '', $quantities, 'number', $array_qty_attr, [], 'no-margin') .
		render_input($name_guarantee_period, '', $guarantee_period, 'text', ['placeholder' => _l('guarantee_period'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right wh_input_none').
		 '</td>';

		$row .= '<td class="rate">' . render_input($name_unit_price, '', $unit_price, 'number', $array_rate_attr) . '</td>';
		$row .= '<td class="taxrate">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';
		// $row .= '<td>' . render_input($name_lot_number, '', $lot_number, 'text', ['placeholder' => _l('lot_number')]) . '</td>';
		// $row .= '<td>' . render_date_input($name_expiry_date, '', $expiry_date, ['placeholder' => _l('expiry_date')]) . '</td>';
		$row .= '<td class="amount" align="right">' . $amount . '</td>';
		$row .= '<td class="discount">' . render_input($name_discount, '', $discount, 'number', $array_discount_attr) . '</td>';
		$row .= '<td class="label_discount_money" align="right">' . $amount . '</td>';
		$row .= '<td class="label_total_after_discount" align="right">' . $amount . '</td>';

		$row .= '<td class="hide commodity_code">' . render_input($name_commodity_code, '', $commodity_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
		$row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';
		$row .= '<td class="hide discount_money">' . render_input($name_discount_money, '', $discount_money, 'number', []) . '</td>';
		$row .= '<td class="hide total_after_discount">' . render_input($name_total_after_discount, '', $total_after_discount, 'number', []) . '</td>';
		$row .= '<td class="hide serial_number">' . render_input($name_serial_number, '', $serial_number, 'text', []) . '</td>';
		$row .= '<td class="hide without_checking_warehouse">' . render_input($name_without_checking_warehouse, '', $without_checking_warehouse, 'text', []) . '</td>';

		if ($name == '') {
			$row .= '<td></td>';
			$row .= '<td><button type="button" onclick="wh_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
		} else {
			if(is_numeric($item_key) && new_strlen($serial_number) > 0 && (is_admin() || has_permission('wh_stock_export_serial_number', '', 'edit')) && get_option('wh_products_by_serial')){
				$row .= '<td><a href="#" class="btn btn-success pull-right" data-toggle="tooltip" data-original-title="'._l('wh_change_serial_number').'" onclick="wh_change_serial_number(\''. $name_commodity_code .'\',\''.$name_warehouse_id .'\',\''. $name_serial_number .'\',\''. $name_commodity_name .'\'); return false;"><i class="fa fa-refresh"></i></a></td>';
			}else{
				$row .= '<td></td>';
			}
			if($is_purchase_order){
				$row .= '<td></td>';
			}else{
				$row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="wh_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
			}
		}
		$row .= '</tr>';
		return $row;
	}

	/**
	 * get html tax delivery
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_html_tax_delivery($id){
        $html = '';
        $html_currency = '';
        $preview_html = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];

        $this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();
        
        $this->db->where('goods_delivery_id', $id);
        $details = $this->db->get(db_prefix().'goods_delivery_detail')->result_array();

        foreach($details as $row){
            if($row['tax_id'] != ''){
                $tax_arr = new_explode('|', $row['tax_id']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = new_explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }
        }

        if(count($tax_name) > 0){
            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax_id'] ?? '', $taxes[$key]) === false)){
                        $tax_val[$key] += ((float)$row_dt['quantities']*(float)$row_dt['unit_price']*(float)$t_rate[$key]/100);
                    }
                }
                $pdf_html .= '<tr id="subtotal"><td ></td><td></td><td></td><td class="text_left">'.$tn.'</td><td class="text_right">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], '').'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], '').'</td></tr>';
                $html_currency .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
            }
        }
        
        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        $rs['html_currency'] = $html_currency;
        return $rs;
    }

    /**
     * packing list get goods delivery
     * @return [type] 
     */
    public function packing_list_get_goods_delivery()
	{
		$arr_goods_delivery = $this->get_invoices_goods_delivery('goods_delivery');

		$own_goods_delivery = '';
		if (!has_permission('wh_stock_export', '', 'view')) {
			$own_goods_delivery = 'AND (' . db_prefix() . 'goods_delivery.addedfrom=' . get_staff_user_id().' OR ' . db_prefix() . 'goods_delivery.staff_id=' . get_staff_user_id() .')';
		}

		if(count($arr_goods_delivery) > 0){
			return $this->db->query('select * from '.db_prefix().'goods_delivery where approval = 1 '.$own_goods_delivery.' AND id NOT IN ('.implode(",", $arr_goods_delivery).') order by id desc')->result_array();
		}
		return $this->db->query('select * from '.db_prefix().'goods_delivery where approval = 1 '.$own_goods_delivery.' order by id desc')->result_array();
	}


	public function create_packing_list_row_template( $delivery_detail_id = '', $name = '', $commodity_name = '', $quantities = '', $unit_name = '', $unit_price = '', $taxname = '',  $commodity_code = '', $unit_id = '', $tax_rate = '', $total_amount = '', $discount = '', $discount_total = '', $total_after_discount = '', $sub_total = '', $tax_name = '', $tax_id = '', $item_key = '',$is_edit = false, $max_qty = false, $serial_number = '') {
		
		$this->load->model('invoice_items_model');
		$row = '';

		$name_commodity_code = 'commodity_code';
		$name_commodity_name = 'commodity_name';
		$name_unit_id = 'unit_id';
		$name_unit_name = 'unit_name';
		$name_quantities = 'quantity';
		$name_unit_price = 'unit_price';
		$name_tax_id_select = 'tax_select';
		$name_tax_id = 'tax_id';
		$name_total_amount = 'total_amount';
		$name_note = 'note';
		$name_tax_rate = 'tax_rate';
		$name_tax_name = 'tax_name';
		$array_attr = [];
		$array_attr_payment = ['data-payment' => 'invoice'];
		$name_sub_total = 'sub_total';
		$name_discount = 'discount';
		$name_discount_total = 'discount_total';
		$name_total_after_discount = 'total_after_discount';
		$name_delivery_detail_id = 'delivery_detail_id';
		$name_serial_number = 'serial_number';

		$array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
		$array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
		$array_discount_attr = [ 'min' => '0.0', 'step' => 'any'];
		$str_rate_attr = 'min="0.0" step="any"';


		if ($name == '') {
			$row .= '<tr class="main hide">
                  <td></td>';
			$vehicles = [];
			$array_attr = ['placeholder' => _l('unit_price')];
			$warehouse_id_name_attr = [];
			$manual             = true;
			$invoice_item_taxes = '';
			$amount = '';
			$sub_total = 0;

		} else {
			$row .= '<tr class="sortable item">
					<td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
			$name_commodity_code = $name . '[commodity_code]';
			$name_commodity_name = $name . '[commodity_name]';
			$name_unit_id = $name . '[unit_id]';
			$name_unit_name = '[unit_name]';
			$name_quantities = $name . '[quantity]';
			$name_unit_price = $name . '[unit_price]';
			$name_tax_id_select = $name . '[tax_select][]';
			$name_tax_id = $name . '[tax_id]';
			$name_total_amount = $name . '[total_amount]';
			$name_note = $name . '[note]';
			$name_tax_rate = $name . '[tax_rate]';
			$name_tax_name = $name .'[tax_name]';
			$name_sub_total = $name .'[sub_total]';
			$name_discount = $name .'[discount]';
			$name_discount_total = $name .'[discount_total]';
			$name_total_after_discount = $name .'[total_after_discount]';
			$name_delivery_detail_id = $name .'[delivery_detail_id]';
			$name_serial_number = $name .'[serial_number]';

			if($max_qty){
				$array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'max' => (float)$max_qty, 'step' => 'any',  'data-quantity' => (float)$quantities];
			}else{
				$array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities];
			}

			$array_rate_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate')];
			$array_discount_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];


			$manual             = false;

			$tax_money = 0;
			$tax_rate_value = 0;

			if($is_edit){
				$invoice_item_taxes = wh_convert_item_taxes($tax_id, $tax_rate, $tax_name);
				$arr_tax_rate = new_explode('|', $tax_rate);
				foreach ($arr_tax_rate as $key => $value) {
					$tax_rate_value += (float)$value;
				}
			}else{
				$invoice_item_taxes = $taxname;
				$tax_rate_data = $this->wh_get_tax_rate($taxname);
				$tax_rate_value = $tax_rate_data['tax_rate'];
			}

			if((float)$tax_rate_value != 0){
				$tax_money = (float)$unit_price * (float)$quantities * (float)$tax_rate_value / 100;
				$goods_money = (float)$unit_price * (float)$quantities + (float)$tax_money;
				$amount = (float)$unit_price * (float)$quantities + (float)$tax_money;
			}else{
				$goods_money = (float)$unit_price * (float)$quantities;
				$amount = (float)$unit_price * (float)$quantities;
			}

			$sub_total = (float)$unit_price * (float)$quantities;
			$amount = app_format_number($amount);

		}

		$row .= '<td class="">' . render_textarea($name_commodity_name, '', $commodity_name, ['rows' => 2, 'placeholder' => _l('item_description_placeholder'), 'readonly' => true] ) . '</td>';

		$row .= '<td class="quantities">' . 
		render_input($name_quantities, '', $quantities, 'number', $array_qty_attr, [], 'no-margin').
		render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right wh_input_none').
		 '</td>';

		$row .= '<td class="rate">' . render_input($name_unit_price, '', $unit_price, 'number', $array_rate_attr) . '</td>';
		$row .= '<td class="taxrate">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';

		$row .= '<td class="amount" align="right">' . $amount . '</td>';
		$row .= '<td class="discount">' . render_input($name_discount, '', $discount, 'number', $array_discount_attr) . '</td>';
		$row .= '<td class="label_discount_money" align="right">' . $amount . '</td>';
		$row .= '<td class="label_total_after_discount" align="right">' . $amount . '</td>';

		$row .= '<td class="hide commodity_code">' . render_input($name_commodity_code, '', $commodity_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
		$row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';
		$row .= '<td class="hide discount_money">' . render_input($name_discount_total, '', $discount_total, 'number', []) . '</td>';
		$row .= '<td class="hide total_after_discount">' . render_input($name_total_after_discount, '', $total_after_discount, 'number', []) . '</td>';
		$row .= '<td class="hide">' . render_input($name_delivery_detail_id, '', $delivery_detail_id, 'number') . '</td>';
		$row .= '<td class="hide serial_number">' . render_input($name_serial_number, '', $serial_number, 'text', []) . '</td>';

		if ($name == '') {
			$row .= '<td><button type="button" onclick="wh_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
		} else {
			$row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="wh_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
		}
		$row .= '</tr>';
		return $row;
	}

	/**
	 * packing list get delivery note
	 * @param  [type] $delivery_id 
	 * @return [type]              
	 */
	public function packing_list_get_delivery_note($delivery_id) {

		$arr_pur_resquest = [];

		$subtotal = 0;
		$total_discount = 0;
		$total_payment = 0;
		$total_tax_money = 0;
		$additional_discount = 0;
		$shipping_fee = 0;
		$packing_list_total_shipping_fee = 0;
		$pur_total_money = 0;
		$packing_list_row_template = '';
		$packing_list_row_template = $this->warehouse_model->create_packing_list_row_template();
		$billing_shipping = [];
		$customer_id = '';

    	// get_goods_delivery
		$get_goods_delivery = $this->get_goods_delivery($delivery_id);
    	// get_goods_delivery_detail
		$goods_delivery_details = $this->get_goods_delivery_detail($delivery_id);
		// get total shipping fee by packing list
		$this->db->select('sum(shipping_fee) as total_shipping_fee');
		$this->db->where('delivery_note_id', $delivery_id);
		$this->db->where('approval', 1);
		$packing_lists = $this->db->get(db_prefix().'wh_packing_lists')->row();
		if($packing_lists){
			$packing_list_total_shipping_fee = (float)$packing_lists->total_shipping_fee;
		}

		$index=0;
		$status = false;
		if(count($goods_delivery_details) > 0){
			$status = false;

    		//get customer billing_shipping
			$this->load->model('clients_model');
			if(is_numeric($get_goods_delivery->customer_code)){
				$billing_shipping = $this->clients_model->get_customer_billing_and_shipping_details($get_goods_delivery->customer_code);
				$customer_id = $get_goods_delivery->customer_code;
			}

			foreach ($goods_delivery_details as $key => $value) {
				$quantities = (float)$value['quantities'] - (float)$value['packing_qty'];
				if($quantities > 0){
					$tax_rate = null;
					$tax_name = null;
					$tax_id = null;
					$tax_rate_value = 0;
					$pur_total_money += (float)$value['total_after_discount'];


					/*caculator subtotal*/
					/*total discount*/
					/*total payment*/

					$total_goods_money = (float)$quantities*(float)$value['unit_price'];

					//get tax value
					if($value['tax_id'] != null && $value['tax_id'] != '') {
						$tax_id = $value['tax_id'];
						$arr_tax = new_explode('|', $value['tax_id']);
						$arr_tax_rate = new_explode('|', $value['tax_rate']);
						$arr_tax_name = new_explode('|', $value['tax_name']);

						foreach ($arr_tax as $key => $tax_id) {
							if(isset($arr_tax_name[$key])){
								$get_tax_name = $arr_tax_name[$key];
							}else{
								$get_tax_name = $this->get_tax_name($tax_id);
							}

							if(isset($arr_tax_rate[$key])){
								$get_tax_rate = $arr_tax_rate[$key];
							}else{
								$tax = $this->get_taxe_value($tax_id);
								$get_tax_rate = (float)$tax->taxrate;
							}

							$tax_rate_value += (float)$get_tax_rate;

							if(new_strlen($tax_rate) > 0){
								$tax_rate .= '|'.$get_tax_rate;
							}else{
								$tax_rate .= $get_tax_rate;
							}

							if(new_strlen($tax_name) > 0){
								$tax_name .= '|'.$get_tax_name;
							}else{
								$tax_name .= $get_tax_name;
							}


						}
					}


					$index++;
					$unit_name = wh_get_unit_name($value['unit_id']);
					$unit_id = $value['unit_id'];
					$taxname = '';
					$expiry_date = null;
					$lot_number = null;
					$note = null;
					if(new_strlen($value['commodity_name']) == 0){
						$commodity_name = wh_get_item_variatiom($value['commodity_code']);
					}else{
						$commodity_name = $value['commodity_name'];
					}
					$total_money = 0;
					$total_after_discount = 0;
					$unit_price = (float)$value['unit_price'];
					$commodity_code = $value['commodity_code'];
					$discount_money = $value['discount_money'];

					if((float)$tax_rate_value != 0){
						$tax_money = (float)$unit_price * (float)$quantities * (float)$tax_rate_value / 100;
						$total_money = (float)$unit_price * (float)$quantities + (float)$tax_money;
						$amount = (float)$unit_price * (float)$quantities + (float)$tax_money;
						$discount_money = (float)$amount*(float)$value['discount']/100;

						$total_after_discount = (float)$unit_price * (float)$quantities + (float)$tax_money - (float)$discount_money;
					}else{
						$total_money = (float)$unit_price * (float)$quantities;
						$amount = (float)$unit_price * (float)$quantities;
						$discount_money = (float)$amount*(float)$value['discount']/100;

						$total_after_discount = (float)$unit_price * (float)$quantities - (float)$discount_money;
					}

					$sub_total = (float)$unit_price * (float)$quantities;
					$packing_list_row_template .= $this->warehouse_model->create_packing_list_row_template($value['id'], 'newitems[' . $index . ']', $commodity_name, $quantities, $unit_name, $unit_price, $taxname, $commodity_code, $unit_id , $tax_rate, $total_money, $value['discount'], $discount_money, $total_after_discount, $sub_total, $tax_name, $tax_id, 'undefined', true, (float)$quantities, $value['serial_number']);

				}
			}

			if($get_goods_delivery){
				if((float)$get_goods_delivery->additional_discount > 0){
					$additional_discount = (float)$get_goods_delivery->additional_discount;
				}
				if((float)$get_goods_delivery->shipping_fee > 0){
					$shipping_fee = round((float)$get_goods_delivery->shipping_fee - $packing_list_total_shipping_fee, 2);
				}
				
			}
		}


		$arr_pur_resquest['result'] = $packing_list_row_template;
		$arr_pur_resquest['additional_discount'] = $additional_discount;
		$arr_pur_resquest['billing_shipping'] = $billing_shipping;
		$arr_pur_resquest['customer_id'] = $customer_id;
		$arr_pur_resquest['shipping_fee'] = $shipping_fee;

		return $arr_pur_resquest;
	}

    /**
     * create packing list code
     * @return [type] 
     */
    public function create_packing_list_code()
    {
		$goods_code = get_warehouse_option('packing_list_number_prefix') . (get_warehouse_option('next_packing_list_number'));
		return $goods_code;
	}

	/**
	 * add packing list
	 * @param [type]  $data 
	 * @param boolean $id   
	 */
    public function add_packing_list($data, $id = false) {
		$packing_list_details = [];
		if (isset($data['newitems'])) {
			$packing_list_details = $data['newitems'];
			unset($data['newitems']);
		}

		unset($data['item_select']);
		unset($data['commodity_name']);
		unset($data['quantity']);
		unset($data['unit_price']);
		unset($data['unit_name']);
		unset($data['commodity_code']);
		unset($data['unit_id']);
		unset($data['discount']);
		unset($data['tax_rate']);
		unset($data['tax_name']);
		unset($data['delivery_detail_id']);
		unset($data['serial_number']);
		if(isset($data['main_additional_discount'])){
			unset($data['main_additional_discount']);
		}
		if(isset($data['include_shipping'])){
			unset($data['include_shipping']);
		}
		if(isset($data['main_shipping_fee'])){
			unset($data['main_shipping_fee']);
		}
		
		$check_appr = $this->get_approve_setting('5');
		$data['approval'] = 0;
		if ($check_appr && $check_appr != false) {
			$data['approval'] = 0;
		} else {
			$data['approval'] = 1;
		}

		if(isset($data['edit_approval'])){
			unset($data['edit_approval']);
		}

		if(isset($data['save_and_send_request'])){
			$save_and_send_request = $data['save_and_send_request'];
			unset($data['save_and_send_request']);
		}

		$data['packing_list_number'] = $this->create_packing_list_code();
		$data['total_amount'] 	= $data['total_amount'];
		$data['discount_total'] = $data['discount_total'];
		$data['total_after_discount'] = $data['total_after_discount'];
		$data['staff_id'] = get_staff_user_id();
		$data['datecreated'] = date('Y-m-d H:i:s');
		$data['delivery_status'] = null;

		$this->db->insert(db_prefix() . 'wh_packing_lists', $data);
		$insert_id = $this->db->insert_id();

		/*update save note*/

		if (isset($insert_id)) {
			foreach ($packing_list_details as $packing_list_detail) {
				$packing_list_detail['packing_list_id'] = $insert_id;

				$tax_money = 0;
				$tax_rate_value = 0;
				$tax_rate = null;
				$tax_id = null;
				$tax_name = null;
				if(isset($packing_list_detail['tax_select'])){
					$tax_rate_data = $this->wh_get_tax_rate($packing_list_detail['tax_select']);
					$tax_rate_value = $tax_rate_data['tax_rate'];
					$tax_rate = $tax_rate_data['tax_rate_str'];
					$tax_id = $tax_rate_data['tax_id_str'];
					$tax_name = $tax_rate_data['tax_name_str'];
				}

				if((float)$tax_rate_value != 0){
					$tax_money = (float)$packing_list_detail['unit_price'] * (float)$packing_list_detail['quantity'] * (float)$tax_rate_value / 100;
					$total_money = (float)$packing_list_detail['unit_price'] * (float)$packing_list_detail['quantity'] + (float)$tax_money;
					$amount = (float)$packing_list_detail['unit_price'] * (float)$packing_list_detail['quantity'] + (float)$tax_money;
				}else{
					$total_money = (float)$packing_list_detail['unit_price'] * (float)$packing_list_detail['quantity'];
					$amount = (float)$packing_list_detail['unit_price'] * (float)$packing_list_detail['quantity'];
				}

				$sub_total = (float)$packing_list_detail['unit_price'] * (float)$packing_list_detail['quantity'];

				$packing_list_detail['tax_id'] = $tax_id;
				$packing_list_detail['total_amount'] = $total_money;
				$packing_list_detail['tax_rate'] = $tax_rate;
				$packing_list_detail['sub_total'] = $sub_total;
				$packing_list_detail['tax_name'] = $tax_name;

				unset($packing_list_detail['order']);
				unset($packing_list_detail['id']);
				unset($packing_list_detail['tax_select']);
				unset($packing_list_detail['unit_name']);

				$this->db->insert(db_prefix() . 'wh_packing_list_details', $packing_list_detail);
			}

			/*write log*/
			$data_log = [];
			$data_log['rel_id'] = $insert_id;
			$data_log['rel_type'] = 'packing_lists';
			$data_log['staffid'] = get_staff_user_id();
			$data_log['date'] = date('Y-m-d H:i:s');
			$data_log['note'] = "packing_lists";
			$this->add_activity_log($data_log);

			/*update next number setting*/
			$this->update_inventory_setting(['next_packing_list_number' =>  (int)get_warehouse_option('next_packing_list_number')+1]);

			//send request approval
			if($save_and_send_request == 'true'){
				$this->send_request_approve(['rel_id' => $insert_id, 'rel_type' => '5', 'addedfrom' => $data['staff_id']]);
			}
		}

		//approval if not approval setting
		if (isset($insert_id)) {
			if ($data['approval'] == 1) {
				$this->update_approve_request($insert_id, 5, 1);
			}
		}

		return $insert_id > 0 ? $insert_id : false;
	}

	/**
	 * get packing list
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_packing_list($id)
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'wh_packing_lists')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from '.db_prefix().'wh_packing_lists')->result_array();
		}
	}

	/**
	 * get goods delivery detail
	 * @param  integer $id
	 * @return array
	 */
	public function get_packing_list_detail($id) {
		if (is_numeric($id)) {
			$this->db->where('packing_list_id', $id);

			return $this->db->get(db_prefix() . 'wh_packing_list_details')->result_array();
		}
		if ($id == false) {
			return $this->db->query('select * from '.db_prefix().'wh_packing_list_details')->result_array();
		}
	}

	/**
	 * update packing list
	 * @param  [type]  $data 
	 * @param  boolean $id   
	 * @return [type]        
	 */
	public function update_packing_list($data, $id = false) {
		$results=0;

		$packing_lists = [];
		$update_packing_lists = [];
		$remove_packing_lists = [];
		if(isset($data['isedit'])){
			unset($data['isedit']);
		}

		if (isset($data['newitems'])) {
			$packing_lists = $data['newitems'];
			unset($data['newitems']);
		}

		if (isset($data['items'])) {
			$update_packing_lists = $data['items'];
			unset($data['items']);
		}
		if (isset($data['removed_items'])) {
			$remove_packing_lists = $data['removed_items'];
			unset($data['removed_items']);
		}

		unset($data['item_select']);
		unset($data['commodity_name']);
		unset($data['quantity']);
		unset($data['unit_price']);
		unset($data['unit_name']);
		unset($data['commodity_code']);
		unset($data['unit_id']);
		unset($data['discount']);
		unset($data['tax_rate']);
		unset($data['tax_name']);
		unset($data['delivery_detail_id']);
		unset($data['serial_number']);
		if(isset($data['main_additional_discount'])){
			unset($data['main_additional_discount']);
		}
		if(isset($data['include_shipping'])){
			unset($data['include_shipping']);
		}
		if(isset($data['main_shipping_fee'])){
			unset($data['main_shipping_fee']);
		}

		$check_appr = $this->get_approve_setting('5');
		$data['approval'] = 0;
		if ($check_appr && $check_appr != false) {
			$data['approval'] = 0;
		} else {
			$data['approval'] = 1;
		}

		if(isset($data['edit_approval'])){
			unset($data['edit_approval']);
		}

		if(isset($data['save_and_send_request']) ){
			$save_and_send_request = $data['save_and_send_request'];
			unset($data['save_and_send_request']);
		}


		$data['total_amount'] 	= $data['total_amount'];
		$data['discount_total'] = $data['discount_total'];
		$data['total_after_discount'] = $data['total_after_discount'];
		$data['staff_id'] = get_staff_user_id();

		$packing_list_id = $data['id'];
		unset($data['id']);

		$this->db->where('id', $packing_list_id);
		$this->db->update(db_prefix() . 'wh_packing_lists', $data);
		if ($this->db->affected_rows() > 0) {
			$results++;
		}

		/*update googs delivery*/

		foreach ($update_packing_lists as $packing_list) {
			$tax_money = 0;
			$tax_rate_value = 0;
			$tax_rate = null;
			$tax_id = null;
			$tax_name = null;
			if(isset($packing_list['tax_select'])){
				$tax_rate_data = $this->wh_get_tax_rate($packing_list['tax_select']);
				$tax_rate_value = $tax_rate_data['tax_rate'];
				$tax_rate = $tax_rate_data['tax_rate_str'];
				$tax_id = $tax_rate_data['tax_id_str'];
				$tax_name = $tax_rate_data['tax_name_str'];
			}

			if((float)$tax_rate_value != 0){
				$tax_money = (float)$packing_list['unit_price'] * (float)$packing_list['quantity'] * (float)$tax_rate_value / 100;
				$total_money = (float)$packing_list['unit_price'] * (float)$packing_list['quantity'] + (float)$tax_money;
				$amount = (float)$packing_list['unit_price'] * (float)$packing_list['quantity'] + (float)$tax_money;
			}else{
				$total_money = (float)$packing_list['unit_price'] * (float)$packing_list['quantity'];
				$amount = (float)$packing_list['unit_price'] * (float)$packing_list['quantity'];
			}

			$sub_total = (float)$packing_list['unit_price'] * (float)$packing_list['quantity'];

			$packing_list['tax_id'] = $tax_id;
			$packing_list['total_amount'] = $total_money;
			$packing_list['tax_rate'] = $tax_rate;
			$packing_list['sub_total'] = $sub_total;
			$packing_list['tax_name'] = $tax_name;

			unset($packing_list['order']);
			unset($packing_list['tax_select']);
			unset($packing_list['unit_name']);


			$this->db->where('id', $packing_list['id']);
			if ($this->db->update(db_prefix() . 'wh_packing_list_details', $packing_list)) {
				$results++;
			}
		}

		// delete receipt note
		foreach ($remove_packing_lists as $packing_list_detail_id) {
			$this->db->where('id', $packing_list_detail_id);
			if ($this->db->delete(db_prefix() . 'wh_packing_list_details')) {
				$results++;
			}
		}

		// Add goods deliveries
		foreach ($packing_lists as $packing_list_detail) {
			$packing_list_detail['packing_list_id'] = $packing_list_id;

			$tax_money = 0;
			$tax_rate_value = 0;
			$tax_rate = null;
			$tax_id = null;
			$tax_name = null;
			if(isset($packing_list_detail['tax_select'])){
				$tax_rate_data = $this->wh_get_tax_rate($packing_list_detail['tax_select']);
				$tax_rate_value = $tax_rate_data['tax_rate'];
				$tax_rate = $tax_rate_data['tax_rate_str'];
				$tax_id = $tax_rate_data['tax_id_str'];
				$tax_name = $tax_rate_data['tax_name_str'];
			}

			if((float)$tax_rate_value != 0){
				$tax_money = (float)$packing_list_detail['unit_price'] * (float)$packing_list_detail['quantity'] * (float)$tax_rate_value / 100;
				$total_money = (float)$packing_list_detail['unit_price'] * (float)$packing_list_detail['quantity'] + (float)$tax_money;
				$amount = (float)$packing_list_detail['unit_price'] * (float)$packing_list_detail['quantity'] + (float)$tax_money;
			}else{
				$total_money = (float)$packing_list_detail['unit_price'] * (float)$packing_list_detail['quantity'];
				$amount = (float)$packing_list_detail['unit_price'] * (float)$packing_list_detail['quantity'];
			}

			$sub_total = (float)$packing_list_detail['unit_price'] * (float)$packing_list_detail['quantity'];

			$packing_list_detail['tax_id'] = $tax_id;
			$packing_list_detail['total_amount'] = $total_money;
			$packing_list_detail['tax_rate'] = $tax_rate;
			$packing_list_detail['sub_total'] = $sub_total;
			$packing_list_detail['tax_name'] = $tax_name;

			unset($packing_list_detail['order']);
			unset($packing_list_detail['id']);
			unset($packing_list_detail['tax_select']);
			unset($packing_list_detail['unit_name']);

			$this->db->insert(db_prefix() . 'wh_packing_list_details', $packing_list_detail);

			if($this->db->insert_id()){
				$results++;
			}
		}

		// TODO send request approval
		if($save_and_send_request == 'true'){
			$this->send_request_approve(['rel_id' => $packing_list_id, 'rel_type' => '5', 'addedfrom' => $data['staff_id']]);
		}

		//approval if not approval setting
		if (isset($packing_list_id)) {
			if ($data['approval'] == 1) {
				$this->update_approve_request($packing_list_id, 5, 1);
			}
		}

		return $results > 0 ? true : false;
	}

	/**
	 * delete packing list
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_packing_list($id) {
		$affected_rows = 0;

		$this->db->where('packing_list_id', $id);
		$this->db->delete(db_prefix() . 'wh_packing_list_details');
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'wh_packing_lists');
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * filter arr inventory min max
	 * @return [type] 
	 */
	public function filter_arr_inventory_min_max()
	{	
		$inventory_commodity_min = [];
		$arr_inventory_min = [];
		$arr_inventory_max = [];

		$inventory_min = $this->db->get(db_prefix() . 'inventory_commodity_min')->result_array();
		$inventory_numbers = $this->db->query('SELECT commodity_id, sum(inventory_number) as inventory_number FROM ' . db_prefix() . 'inventory_manage
		 group by ' . db_prefix() . 'inventory_manage.warehouse_id, '.db_prefix().'inventory_manage.commodity_id')->result_array(); 

		foreach ($inventory_min as $key => $value) {
		    $inventory_commodity_min[$value['commodity_id']] = $value;
		}


		foreach ($inventory_numbers as $key => $value) {

			$inventory_min_flag = false;
			$inventory_max_flag = false;
			if(isset($inventory_commodity_min[$value['commodity_id']])){
				if((float)$inventory_commodity_min[$value['commodity_id']]['inventory_number_min'] >= (float)$value['inventory_number'] ){
					$inventory_min_flag = true;
				}

				if((float)$inventory_commodity_min[$value['commodity_id']]['inventory_number_max'] < (float)$value['inventory_number'] ){
					$inventory_max_flag = true;
				}
			}
			if($inventory_min_flag){
				$arr_inventory_min[] = $value['commodity_id'];
			}
			if($inventory_max_flag){
				$arr_inventory_max[] = $value['commodity_id'];
			}
			
		}
		$results = [];
		$results['inventory_min'] = $arr_inventory_min;
		$results['inventory_max'] = $arr_inventory_max;

		return $results;
	}

	/**
	 * packing list partial or total
	 * @param  [type] $delivery_id          
	 * @param  [type] $packing_list_details 
	 * @return [type]                       
	 */
	public function packing_list_partial_or_total($delivery_id, $packing_list_details)
	{
		$type_of_packing_list = 'partial';
		$flag_update_status = true;

		/*get item in ddelivery_detail*/
		$this->db->where('goods_delivery_id', $delivery_id);
		$this->db->where('quantities != packing_qty');
		$arr_itemable = $this->db->get(db_prefix().'goods_delivery_detail')->result_array();


		//get item id TODO for Delivery note

		$new_packing_list_detail=[];
		$item_id=[];
		foreach ($packing_list_details as $value) {

			$item_id[$value['commodity_code']][] = $value;
			$new_packing_list_detail[$value['commodity_code']][] = $value;
		}

		foreach ($arr_itemable as $key => $itemable_value) {

			if(isset($item_id[$itemable_value['commodity_code']])){
				$first_key = array_key_first($item_id[$itemable_value['commodity_code']]);
				
				if(is_numeric($first_key)){
					$itemable_id = $item_id[$itemable_value['commodity_code']][$first_key]['commodity_code'];
					unset($item_id[$itemable_value['commodity_code']][$first_key]);
				}else{
					$itemable_id = 0;
				}
			}else{
				$itemable_id = 0;
			}

			if($itemable_id != 0){

				if(isset($new_packing_list_detail[$itemable_id])){
					$packing_list_first_key = array_key_first($new_packing_list_detail[$itemable_id]);
					if(is_numeric($packing_list_first_key)){
						$packing_list_qty = $new_packing_list_detail[$itemable_id][$packing_list_first_key]['quantity'];
					}else{
						$packing_list_qty = 0;
					}

				}else{
					$packing_list_qty = 0;
				}

		    	//check quantity in delivery note detail = packing_qty
				$wh_quantity_received = (float)($itemable_value['packing_qty']) + (float)$packing_list_qty;
			
				if((float)$itemable_value['quantities'] > (float)$wh_quantity_received){
					$flag_update_status = false;
				}else{
					if((float)$itemable_value['quantities'] == (float)$wh_quantity_received){
						// ==
						if(is_numeric($packing_list_first_key)){
							unset($new_packing_list_detail[$itemable_id][$packing_list_first_key]);
						}
					}
				}

				if($itemable_value['packing_qty'] == 0 && $itemable_value['quantities'] == $wh_quantity_received){
					$type_of_packing_list = 'total';
				}

				$arr_itemable[$key]['packing_qty'] = $wh_quantity_received;
			}else{
				$flag_update_status = false;
				$type_of_packing_list = 'partial';
			}
		}

		//update packing_qty
		if(count($arr_itemable) > 0){
			$this->db->update_batch(db_prefix().'goods_delivery_detail', $arr_itemable, 'id');
		}

		$result_array=[];
		$result_array['flag_update_status'] = $flag_update_status;
		$result_array['type_of_packing_list'] = $type_of_packing_list;
    	return $result_array;
	}

	/**
	 * get html tax packing list
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_html_tax_packing_list($id)
	{
		$html = '';
		$html_currency = '';
		$preview_html = '';
		$pdf_html = '';
		$taxes = [];
		$t_rate = [];
		$tax_val = [];
		$tax_val_rs = [];
		$tax_name = [];
		$rs = [];
		$pdf_html_currency = '';

		$this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();

		$details = $this->get_packing_list_detail($id);

		foreach($details as $row){
			if($row['tax_id'] != ''){
				$tax_arr = new_explode('|', $row['tax_id']);

				$tax_rate_arr = [];
				if($row['tax_rate'] != ''){
					$tax_rate_arr = new_explode('|', $row['tax_rate']);
				}

				foreach($tax_arr as $k => $tax_it){
					if(!isset($tax_rate_arr[$k]) ){
						$tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
					}

					if(!in_array($tax_it, $taxes)){
						$taxes[$tax_it] = $tax_it;
						$t_rate[$tax_it] = $tax_rate_arr[$k];
						$tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
					}
				}
			}
		}

		if(count($tax_name) > 0){
			foreach($tax_name as $key => $tn){
				$tax_val[$key] = 0;
				foreach($details as $row_dt){
					if(!(strpos($row_dt['tax_id'] ?? '', $taxes[$key]) === false)){
						$tax_val[$key] += ($row_dt['quantity']*$row_dt['unit_price']*$t_rate[$key]/100);
					}
				}
				$pdf_html .= '<tr id="subtotal"><td ></td><td></td><td></td><td class="text_left">'.$tn.'</td><td class="text_right">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
				$preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], '').'</td><tr>';
				$html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], '').'</td></tr>';
				$html_currency .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
				$tax_val_rs[] = $tax_val[$key];
				$pdf_html_currency .= '<tr ><td align="right" width="85%">'.$tn.'</td><td align="right" width="15%">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
			}
		}

		$rs['pdf_html'] = $pdf_html;
		$rs['preview_html'] = $preview_html;
		$rs['html'] = $html;
		$rs['taxes'] = $taxes;
		$rs['taxes_val'] = $tax_val_rs;
		$rs['html_currency'] = $html_currency;
		$rs['pdf_html_currency'] = $pdf_html_currency;
		return $rs;
	}

	/**
	 * check packing list send request
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function check_packing_list_send_request($data)
	{
		$packing_list = $this->get_packing_list($data['rel_id']);
		$packing_list_detail = $this->get_packing_list_detail($data['rel_id']);
		$str_error = '';
		$flag_update_status = true;

		if($packing_list){

			$arr_itemable = $this->get_goods_delivery_detail($packing_list->delivery_note_id);

			//get item id
			$new_packing_list_detail=[];
			$item_id=[];
			foreach ($packing_list_detail as $value) {
				$item_id[] = $value['commodity_code'];
				$new_packing_list_detail[$value['commodity_code']] = $value;
			}

			foreach ($arr_itemable as $key => $itemable_value) {

				$itemable_id = isset($item_id[$itemable_value['commodity_code']]) ?  $item_id[$itemable_value['commodity_code']] : 0;

				if($itemable_id != 0){
					$packing_list_qty = isset($new_packing_list_detail[$itemable_id]) ?  $new_packing_list_detail[$itemable_id]['quantity'] : 0;
					$delivery_note_remaining_quantity = (float)$itemable_value['quantities'] - (float)($itemable_value['packing_qty']) ;

					if($delivery_note_remaining_quantity < $packing_list_qty){
						$flag_update_status = false;
						$commodity_name = $itemable_value['commodity_name'];
						if(new_strlen($commodity_name) == 0){
							$commodity_name = wh_get_item_variatiom($itemable_value['commodity_code']);
						}
						$str_error .= $commodity_name .': '._l('the_current_packing_quantity_exceeds_the_actual_remaining_quantity_allowed_for_packing').': '._l('packing_quantity').': '.$packing_list_qty.' - '._l('remaining_quantity').': '.$delivery_note_remaining_quantity.'<br/>';
					}
				}
			}
		}

		$result=[];
		$result['str_error'] = $str_error;
		$result['flag_update_status'] = $flag_update_status;

		return $result;
	}

	/**
	 * packing list pdf
	 * @param  [type] $packing_list 
	 * @return [type]               
	 */
	public function packing_list_pdf($packing_list) {
		return app_pdf('packing_list', module_dir_path(WAREHOUSE_MODULE_NAME, 'libraries/pdf/Packing_pdf.php'), $packing_list);
	}

	/**
	 * get packing list by deivery note
	 * @param  [type] $delivery_id 
	 * @return [type]              
	 */
	public function get_packing_list_by_deivery_note($delivery_id)
	{
		$this->db->where('delivery_note_id', $delivery_id);
		$this->db->order_by('datecreated', 'asc');
		$packing_lists = $this->db->get(db_prefix().'wh_packing_lists')->result_array();
		return $packing_lists;
	}

	/**
	 * delivery status mark as
	 * @param  [type] $status 
	 * @param  [type] $id     
	 * @param  [type] $type   
	 * @return [type]         
	 */
	public function delivery_status_mark_as($status, $id, $type)
	{

		$status_f = false;
		if($type == 'delivery'){
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'goods_delivery', ['delivery_status' => $status]);
			if ($this->db->affected_rows() > 0) {
				$status_f = true;
				//write log
				$this->log_wh_activity($id, 'delivery', _l('wh_'.$status));

				$get_goods_delivery = $this->get_goods_delivery($id);
				if($get_goods_delivery && is_numeric($get_goods_delivery->customer_code)){
					$this->warehouse_check_update_shipment_when_delivery_note_approval($id, $status, 'delivery_status_mark');
				}
				
				$this->check_update_shipment_when_delivery_note_approval($id, $status, 'delivery_status_mark');

			}
		}elseif($type == 'packing_list'){
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'wh_packing_lists', ['delivery_status' => $status]);
			if ($this->db->affected_rows() > 0) {
				$status_f = true;
				//write log for packing list
				$this->log_wh_activity($id, 'packing_list', _l('wh_'.$status));


				//write log for delivery note
				$activity_log = '';
				$delivery_id = '';
				$get_packing_list = $this->get_packing_list($id);
				if($get_packing_list){
					$activity_log .= $get_packing_list->packing_list_number .' - '.$get_packing_list->packing_list_name;
					$delivery_id = $get_packing_list->delivery_note_id;
				}
				$activity_log .= ': '._l('wh_'.$status);
				if(is_numeric($delivery_id)){
					
					$get_goods_delivery = $this->get_goods_delivery($delivery_id);
					if($get_goods_delivery && is_numeric($get_goods_delivery->customer_code)){
						$this->warehouse_check_update_shipment_when_delivery_note_approval($id, $status, 'packing_list_status_mark', $delivery_id);
					}
					
					$this->check_update_shipment_when_delivery_note_approval($id, $status, 'packing_list_status_mark', $delivery_id);


					$delivery_note_log_des = ' <a href="'.admin_url('warehouse/manage_packing_list/' . $id).'">'.$activity_log.'</a> ';
					$this->log_wh_activity($delivery_id, 'delivery', $delivery_note_log_des);

				// check update delivery status of delivery note
					$delivery_list_status = delivery_list_status();
					$arr_delivery_list_status_name = [];
					$arr_delivery_list_status_order = [];
					foreach ($delivery_list_status as $value) {
					    $arr_delivery_list_status_name[$value['id']] = $value['order'];
					    $arr_delivery_list_status_order[$value['order']] = $value['id'];
					}

					$get_packing_list_by_deivery_note = $this->get_packing_list_by_deivery_note($delivery_id);
					if(count($get_packing_list_by_deivery_note) > 0){
						$goods_delivery_status = '';
						$goods_delivery_status_order = '';
						$packing_list_order = 0;

						$get_goods_delivery = $this->get_goods_delivery($delivery_id);
						if($get_goods_delivery){
							$goods_delivery_status = $get_goods_delivery->delivery_status;
						}

						if(isset($arr_delivery_list_status_name[$goods_delivery_status])){
							$goods_delivery_status_order = $arr_delivery_list_status_name[$goods_delivery_status];
						}
						
						foreach ($get_packing_list_by_deivery_note as $value) {
						    if(isset($arr_delivery_list_status_name[$value['delivery_status']])){
						    	if((int)$arr_delivery_list_status_name[$value['delivery_status']] >=  $packing_list_order){
						    		$packing_list_order = (int)$arr_delivery_list_status_name[$value['delivery_status']];
						    	}
						    }
						}

						if((int)$packing_list_order > (int)$goods_delivery_status_order){
							if(isset($arr_delivery_list_status_order[$packing_list_order])){
								$this->db->where('id', $delivery_id);
								$this->db->update(db_prefix() . 'goods_delivery', ['delivery_status' => $arr_delivery_list_status_order[$packing_list_order] ]);

								$get_goods_delivery = $this->get_goods_delivery($delivery_id);
								if($get_goods_delivery && is_numeric($get_goods_delivery->customer_code)){
									$this->warehouse_check_update_shipment_when_delivery_note_approval($delivery_id, $arr_delivery_list_status_order[$packing_list_order], 'delivery_status_mark');
								}
								
								$this->check_update_shipment_when_delivery_note_approval($delivery_id, $arr_delivery_list_status_order[$packing_list_order], 'delivery_status_mark');

							}
						}

					}
				}

				
			}
		}
	 return $status_f;
	}

	/**
	 * get shipment by order
	 * @param  [type] $order_id 
	 * @return [type]           
	 */
	public function get_shipment_by_order($order_id)
	{
		if (is_numeric($order_id)) {
			$this->db->where('cart_id', $order_id);
			return $this->db->get(db_prefix() . 'wh_omni_shipments')->row();
		}
		if ($order_id == false) {
			return $this->db->query('select * from '.db_prefix().'wh_omni_shipments')->result_array();
		}
	}

	/**
	 * wh get shipment activity log
	 * @param  [type] $shipment_id 
	 * @return [type]              
	 */
	public function wh_get_shipment_activity_log($shipment_id)
	{
		$cart_id = '';
		$order_id = '';
		$delivery_id = '';
		$packing_list_id = [];

		$arr_activity_log = [];

		// sales order activity_log
		// delivery note activity_log
		// packing list activity_log
		
		$this->db->where('id', $shipment_id);
		$shipment = $this->db->get(db_prefix() . 'wh_omni_shipments')->row();
		if($shipment){
			$cart_id = $shipment->cart_id;

			$order_id = $shipment->order_id;

			if(is_numeric($cart_id) && $cart_id != 0){

				$this->load->model('omni_sales/omni_sales_model');
				$get_cart = $this->omni_sales_model->get_cart($shipment->cart_id);
				if($get_cart && is_numeric($get_cart->stock_export_number)){
				// get order activity_log
					$delivery_id = $get_cart->stock_export_number;

					$packing_lists = $this->get_packing_list_by_deivery_note($get_cart->stock_export_number);

					if(count($packing_lists) > 0){
						foreach ($packing_lists as $value) {
							$packing_list_id[] = $value['id'];
						}
					}
				}
			}


			if(is_numeric($order_id) && $order_id != 0){
				$this->load->model('sales_agent/sales_agent_model');

				$get_order = $this->sales_agent_model->get_pur_order($order_id);
				if($get_order && is_numeric($get_order->stock_export_id)){
					$delivery_id = $get_order->stock_export_id;

					$packing_lists = $this->get_packing_list_by_deivery_note($get_order->stock_export_id);

					if(count($packing_lists) > 0){
						foreach ($packing_lists as $value) {
							$packing_list_id[] = $value['id'];
						}
					}
				}
			}
		}

		$this->db->or_group_start();
		$this->db->where('rel_id', $shipment_id);
		$this->db->where('rel_type', 'shipment');
		$this->db->group_end();
		if(new_strlen($cart_id) > 0){
			$this->db->or_group_start();
			$this->db->where('rel_id', $cart_id);
			$this->db->where('rel_type', 'omni_order');
			$this->db->group_end();
		}

		if(new_strlen($delivery_id) > 0){
			$this->db->or_group_start();
			$this->db->where('rel_id', $delivery_id);
			$this->db->where('rel_type', 'delivery');
			$this->db->group_end();
		}

		if(count($packing_list_id) > 0){
			$this->db->or_group_start();
			$this->db->where('rel_id IN ('.implode(',', $packing_list_id).')');
			$this->db->where('rel_type', 'packing_list');
			$this->db->group_end();
		}

		$this->db->order_by('date', 'desc');
		$shipment_activity_log = $this->db->get(db_prefix().'wh_goods_delivery_activity_log')->result_array();

		return $shipment_activity_log;
	}

	/**
	 * create shipment from order
	 * @param  [type] $order_id 
	 * @return [type]           
	 */
	public function create_shipment_from_order($order_id)
	{
		// create shipment
		if(get_status_modules_wh('omni_sales')){
			$this->load->model('omni_sales/omni_sales_model');
			$cart = $this->omni_sales_model->get_cart($order_id);
			if($cart){
				$shipment = [];
				$shipment['cart_id'] = $order_id;
				$shipment['shipment_number'] = 'SHIPMENT' . date('YmdHi');
				$shipment['planned_shipping_date'] = null;
				$shipment['shipment_status'] = 'confirmed_order';
				$shipment['datecreated'] = date('Y-m-d H:i:s');
				if(is_numeric($cart->stock_export_number)){
					$shipment['goods_delivery_id'] = $cart->stock_export_number;
				}
				$shipment['shipment_hash'] = app_generate_hash();

				$this->db->insert(db_prefix() . 'wh_omni_shipments', $shipment);
				$insert_id = $this->db->insert_id();
				if($insert_id){
					$shipment_log1 = _l('wh_order_has_been_confirmed');
					$this->log_wh_activity($insert_id, 'shipment', $shipment_log1);
					$shipment_log2 = _l('wh_shipment_have_been_created');
					$this->log_wh_activity($insert_id, 'shipment', $shipment_log2);

					return $insert_id;
				}
			}
		}

		return false;
	}

	/**
	 * update shipment status
	 * @param  [type] $id   
	 * @param  array  $data 
	 * @return [type]       
	 */
	public function update_shipment_status($id, $data = [])
	{
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'wh_omni_shipments', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * check update shipment when delivery note approval
	 * @param  [type] $delivery_id 
	 * @return [type]              
	 */
	public function check_update_shipment_when_delivery_note_approval($rel_id, $status = 'quality_check', $rel_type = 'delivery_approval', $delivery_id = 0)
	{
		if(get_status_modules_wh('omni_sales')){

			$delivery_list_status = delivery_list_status();
			$arr_delivery_list_status_name = [];
			$arr_delivery_list_status_order = [];
			foreach ($delivery_list_status as $value) {
				$arr_delivery_list_status_name[$value['id']] = $value['order'];
				$arr_delivery_list_status_order[$value['order']] = $value['id'];
			}

			if($status == 'quality_check' && $rel_type == 'delivery_approval'){

				$this->db->where('stock_export_number',$rel_id);
				$cart = $this->db->get(db_prefix().'cart')->row();
				if($cart){
					$shipment = $this->get_shipment_by_order($cart->id);
					if($shipment){
						$this->update_shipment_status($shipment->id, ['shipment_status' => 'quality_check']);
						return true;
					}
					return false;
				}
				return false;
			}elseif($rel_type == 'delivery_status_mark'){

				$this->db->where('stock_export_number',$rel_id);
				$cart = $this->db->get(db_prefix().'cart')->row();
				if($cart){
					$shipment = $this->get_shipment_by_order($cart->id);
					if($shipment){

						if(isset($arr_delivery_list_status_name[$status])){
							if((int)$arr_delivery_list_status_name[$status] >= 4){
							// delivered
								$this->update_shipment_status($shipment->id, ['shipment_status' => 'product_delivered']);
							}elseif((int)$arr_delivery_list_status_name[$status] >= 3){
							// delivery_in_progress
								$this->update_shipment_status($shipment->id, ['shipment_status' => 'product_dispatched']);
							}
						}
					}
				}

			}elseif($rel_type == 'packing_list_status_mark'){

				$this->db->where('stock_export_number',$delivery_id);
				$cart = $this->db->get(db_prefix().'cart')->row();
				if($cart){
					$shipment = $this->get_shipment_by_order($cart->id);
					if($shipment){
						if(isset($arr_delivery_list_status_name[$status])){
							if((int)$arr_delivery_list_status_name[$status] >= 3){
							// delivery_in_progress
								$this->update_shipment_status($shipment->id, ['shipment_status' => 'product_dispatched']);
							}
						}
					}
				}

			}
			return true;
		}
		return false;
	}

	/**
	 * wh get activity log by id
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function wh_get_activity_log_by_id($id)
	{
		$this->db->where('id', $id);
		return $this->db->get(db_prefix() . 'wh_goods_delivery_activity_log')->row();
	}

	/**
	 * update activity log
	 * @param  [type] $id   
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function update_activity_log($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update(db_prefix().'wh_goods_delivery_activity_log', $data);
		if($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * create order return code
	 * @return [type] 
	 */
	public function create_order_return_code()
	{
		$goods_code = get_warehouse_option('order_return_number_prefix') . (get_warehouse_option('next_order_return_number'));
		return $goods_code;
	}

	/**
	 * get order return
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_order_return($id)
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'wh_order_returns')->row();
		}
		if ($id == false) {
			return $this->db->query('select * from '.db_prefix().'wh_order_returns')->result_array();
		}
	}

	/**
	 * get order return detail
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_order_return_detail($id) {
		if (is_numeric($id)) {
			$this->db->where('order_return_id', $id);

			return $this->db->get(db_prefix() . 'wh_order_return_details')->result_array();
		}
		if ($id == false) {
			return $this->db->query('select * from '.db_prefix().'wh_order_return_details')->result_array();
		}
	}

	/**
	 * delete order return
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_order_return($id) {
		$affected_rows = 0;

		$this->db->where('order_return_id', $id);
		$this->db->delete(db_prefix() . 'wh_order_return_details');
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'wh_order_returns');
		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	public function create_order_return_row_template($rel_type, $rel_type_detail_id = '', $name = '', $commodity_name = '', $quantities = '', $unit_name = '', $unit_price = '', $taxname = '',  $commodity_code = '', $unit_id = '', $tax_rate = '', $total_amount = '', $discount = '', $discount_total = '', $total_after_discount = '', $reason_return = '', $sub_total = '', $tax_name = '', $tax_id = '', $item_key = '',$is_edit = false, $max_qty = false, $purchase_price = 0) {
		
		$this->load->model('invoice_items_model');
		$row = '';

		$name_commodity_code = 'commodity_code';
		$name_commodity_name = 'commodity_name';
		$name_unit_id = 'unit_id';
		$name_unit_name = 'unit_name';
		$name_quantities = 'quantity';
		$name_unit_price = 'unit_price';
		$name_purchase_price = 'purchase_price';
		$name_tax_id_select = 'tax_select';
		$name_tax_id = 'tax_id';
		$name_total_amount = 'total_amount';
		$name_note = 'note';
		$name_tax_rate = 'tax_rate';
		$name_tax_name = 'tax_name';
		$array_attr = [];
		$array_attr_payment = ['data-payment' => 'invoice'];
		$name_sub_total = 'sub_total';
		$name_discount = 'discount';
		$name_discount_total = 'discount_total';
		$name_total_after_discount = 'total_after_discount';
		$name_rel_type_detail_id = 'rel_type_detail_id';
		$name_reason_return = 'reason_return';

		$array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
		$array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
		$array_discount_attr = [ 'min' => '0.0', 'step' => 'any'];
		$str_rate_attr = 'min="0.0" step="any"';


		if ($name == '') {
			if($rel_type == 'manual'){
				$row .= '<tr class="main">
				<td></td>';
			}else{
				$row .= '<tr class="main hide">
				<td></td>';
			}

			$vehicles = [];
			$array_attr = ['placeholder' => _l('unit_price')];
			$warehouse_id_name_attr = [];
			$manual             = true;
			$invoice_item_taxes = '';
			$amount = '';
			$sub_total = 0;

		} else {
			$row .= '<tr class="sortable item">
					<td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
			$name_commodity_code = $name . '[commodity_code]';
			$name_commodity_name = $name . '[commodity_name]';
			$name_unit_id = $name . '[unit_id]';
			$name_unit_name = '[unit_name]';
			$name_quantities = $name . '[quantity]';
			$name_unit_price = $name . '[unit_price]';
			$name_purchase_price = $name . '[purchase_price]';
			$name_tax_id_select = $name . '[tax_select][]';
			$name_tax_id = $name . '[tax_id]';
			$name_total_amount = $name . '[total_amount]';
			$name_note = $name . '[note]';
			$name_tax_rate = $name . '[tax_rate]';
			$name_tax_name = $name .'[tax_name]';
			$name_sub_total = $name .'[sub_total]';
			$name_discount = $name .'[discount]';
			$name_discount_total = $name .'[discount_total]';
			$name_total_after_discount = $name .'[total_after_discount]';
			$name_rel_type_detail_id = $name .'[rel_type_detail_id]';
			$name_reason_return = $name .'[reason_return]';

			if($rel_type == 'i_sales_return_order' || $rel_type == 'i_purchasing_return_order'){
				if($max_qty){
					$array_qty_attr = ['onblur' => 'wh_sale_order_calculate_total();', 'onchange' => 'wh_sale_order_calculate_total();', 'min' => '0.0' , 'max' => (float)$max_qty, 'step' => 'any',  'data-quantity' => (float)$quantities, 'readonly' => true];
				}else{
					$array_qty_attr = ['onblur' => 'wh_sale_order_calculate_total();', 'onchange' => 'wh_sale_order_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities, 'readonly' => true];
				}

				$array_rate_attr = ['onblur' => 'wh_sale_order_calculate_total();', 'onchange' => 'wh_sale_order_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate'), 'readonly' => true];
				$array_discount_attr = ['onblur' => 'wh_sale_order_calculate_total();', 'onchange' => 'wh_sale_order_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount'), 'readonly' => true];

			}else{

				if($max_qty){
					$array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'max' => (float)$max_qty, 'step' => 'any',  'data-quantity' => (float)$quantities];
				}else{
					$array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities];
				}

				$array_rate_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate')];
				$array_discount_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];
			}


			$manual             = false;

			$tax_money = 0;
			$tax_rate_value = 0;

			if($is_edit){
				$invoice_item_taxes = wh_convert_item_taxes($tax_id, $tax_rate, $tax_name);
				$arr_tax_rate = new_explode('|', $tax_rate);
				foreach ($arr_tax_rate as $key => $value) {
					$tax_rate_value += (float)$value;
				}
			}else{
				$invoice_item_taxes = wh_convert_item_taxes($tax_id, $tax_rate, $tax_name);
				$arr_tax_rate = new_explode('|', $tax_rate);
				foreach ($arr_tax_rate as $key => $value) {
					$tax_rate_value += (float)$value;
				}

				// $invoice_item_taxes = $taxname;
				// $tax_rate_data = $this->wh_get_tax_rate($taxname);
				// $tax_rate_value = $tax_rate_data['tax_rate'];
			}

			if((float)$tax_rate_value != 0){
				$tax_money = (float)$unit_price * (float)$quantities * (float)$tax_rate_value / 100;
				$goods_money = (float)$unit_price * (float)$quantities + (float)$tax_money;
				$amount = (float)$unit_price * (float)$quantities + (float)$tax_money;
			}else{
				$goods_money = (float)$unit_price * (float)$quantities;
				$amount = (float)$unit_price * (float)$quantities;
			}

			$sub_total = (float)$unit_price * (float)$quantities;
			$amount = app_format_number($amount);

		}

		$row .= '<td class="">' . render_textarea($name_commodity_name, '', $commodity_name, ['rows' => 2, 'placeholder' => _l('item_description_placeholder'), 'readonly' => true] ) . '</td>';

		$row .= '<td class="quantities">' . 
		render_input($name_quantities, '', $quantities, 'number', $array_qty_attr, [], 'no-margin').
		render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right wh_input_none').
		 '</td>';

		$row .= '<td class="rate">' . render_input($name_unit_price, '', $unit_price, 'number', $array_rate_attr) . '</td>';
		$row .= '<td class="purchase_price hide">' . render_input($name_purchase_price, '', $purchase_price, 'number') . '</td>';
		$row .= '<td class="taxrate">' . $this->get_taxes_dropdown_template_v2($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';

		$row .= '<td class="amount" align="right">' . $amount . '</td>';
		$row .= '<td class="discount">' . render_input($name_discount, '', $discount, 'number', $array_discount_attr) . '</td>';
		$row .= '<td class="label_discount_money" align="right">' . $amount . '</td>';
		$row .= '<td class="label_total_after_discount" align="right">' . $amount . '</td>';

		$row .= '<td class="hide commodity_code">' . render_input($name_commodity_code, '', $commodity_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
		$row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';
		$row .= '<td class="hide discount_money">' . render_input($name_discount_total, '', $discount_total, 'number', []) . '</td>';
		$row .= '<td class="hide total_after_discount">' . render_input($name_total_after_discount, '', $total_after_discount, 'number', []) . '</td>';
		$row .= '<td class="hide">' . render_input($name_rel_type_detail_id, '', $rel_type_detail_id, 'number') . '</td>';
		$row .= '<td class=" hide">' . render_textarea($name_reason_return, '', $reason_return, ['rows' => 2, 'placeholder' => _l('reason_return')] ) . '</td>';


		if($rel_type == 'sales_return_order'){
			if ($name == '') {
				$row .= '<td><button type="button" onclick="wh_sales_order_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
			} else {
				$row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="wh_sales_order_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
			}
		}elseif($rel_type == 'i_sales_return_order' || $rel_type == 'i_purchasing_return_order'){
			$row .= '';
		}else{
			if ($name == '') {
				$row .= '<td><button type="button" onclick="wh_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
			} else {
				$row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="wh_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
			}
		}

		$row .= '</tr>';
		return $row;
	}
	/**
	 * get omni sale order list
	 * @return array 
	 */
	public function get_omni_sale_order_list(){
		$result = [];
		if(get_status_modules_wh('omni_sales')){
			$this->load->model('omni_sales/omni_sales_model');
			$data = $this->omni_sales_model->get_cart('','channel_id in (1,2,6,4) and status = 5');
			foreach ($data as $key => $row) {
				$result[] = ['id' => $row['id'], 'goods_delivery_code' => $row['order_number']];
			}
		}
		return $result;
	}
	/**
	 * omni sale detail order return
	 * @param  [type] $id 
	 * @return [type]              
	 */
	public function omni_sale_detail_order_return($id) {
		$this->load->model('omni_sales/omni_sales_model');
		$company_id = '';
		$email = '';
		$phonenumber = '';
		$order_number = '';
		$order_date = '';
		$number_of_item = '';
		$order_total = '';
		$datecreated = '';
		$main_additional_discount = 0;
		$additional_discount = 0;
		$total_item_qty = 0;
		$row_template = '';
		$cart_data = $this->omni_sales_model->get_cart($id);
		if($cart_data){
			$company_id = $cart_data->userid;
			$contacts = $this->clients_model->get_contacts($company_id);
			if(count($contacts) > 0){
				$email = $contacts[0]['email'];
			}
			$phonenumber = $cart_data->phonenumber;
			$order_number = $cart_data->order_number;
			$order_date = $cart_data->datecreator;
			$order_total = $cart_data->total;
			$datecreated = date('Y-m-d H-i-s');
			$main_additional_discount = $cart_data->discount_total;
			$additional_discount = $cart_data->discount_total;
			$row_template = '';
			$count_item = 0;
			$cart_detail_data = $this->omni_sales_model->get_cart_detailt_by_master($id);
			foreach ($cart_detail_data as $key => $row) {	
				$count_item++;
				$unit_name = '';
				$tax_id = '';
				$unit_id = '';
				$commodity_code = '';
				$item = $this->omni_sales_model->get_product($row['id']);
				if($item){
					$tax_name = '';
					$taxrate = '';
					$tax = $this->omni_sales_model->get_tax_info_by_product($id);
					if($tax){
						$tax_id = $tax->id;
					}
					$commodity_code = $item->commodity_code;
					if($item->unit_id){
						$unit_id = $item->unit_id;
						$data_unit = $this->omni_sales_model->get_unit($unit_id);
						if($data_unit){
							$unit_name = $data_unit->unit_name;
						}          
					}
				}
				$total_item_qty += $row['quantity'];
				$taxname = '';
				$tax_rate = '';
				$total_amount = $row['quantity'] * $row['prices'];
				$discount = $row['percent_discount'];
				$discount_total = $row['prices_discount'];
				$total_after_discount = '';
				$sub_total = '';
				$tax_name = '';
				$tax_id = '';
				$row_template .= $this->create_order_return_row_template('sales_return_order', $row['id'], 'newitems['.$row['id'].']', $row['product_name'], $row['quantity'], $unit_name, $row['prices'], $taxname,  $commodity_code, $unit_id, $tax_rate, $total_amount, $discount, $discount_total, $total_after_discount, '', $sub_total, $tax_name, $tax_id, $row['id'], false, false);
			}
			$number_of_item = $count_item;

		}
		$data['company_id'] = $company_id;
		$data['email'] = $email;
		$data['phonenumber'] = $phonenumber;
		$data['order_number'] = $order_number;
		$data['order_date'] = $order_date;
		$data['number_of_item'] = $number_of_item;
		$data['order_total'] = $order_total;
		$data['datecreated'] = $datecreated;
		$data['main_additional_discount'] = $main_additional_discount;
		$data['additional_discount'] = $additional_discount;
		$data['total_item_qty'] = $total_item_qty;
		$data['result'] = $row_template;
		return $data;
	}

	/**
	 * [add add order return
	 * @param [type] $data     
	 * @param [type] $rel_type 
	 */
	public function add_order_return($data, $rel_type)
	{
		$order_return_details = [];
		if (isset($data['newitems'])) {
			$order_return_details = $data['newitems'];
			unset($data['newitems']);
		}
		unset($data['item_select']);
		unset($data['commodity_name']);
		unset($data['quantity']);
		unset($data['unit_price']);
		if(isset($data['purchase_price'])){
			unset($data['purchase_price']);
		}
		unset($data['unit_name']);
		unset($data['commodity_code']);
		unset($data['unit_id']);
		unset($data['discount']);
		unset($data['tax_rate']);
		unset($data['tax_name']);
		unset($data['rel_type_detail_id']);
		unset($data['item_reason_return']);
		unset($data['reason_return']);

		if(isset($data['main_additional_discount'])){
			unset($data['main_additional_discount']);
		}
		
		$check_appr = $this->get_approve_setting('6');
		$data['approval'] = 0;
		if ($check_appr && $check_appr != false) {
			$data['approval'] = 0;
		} else {
			$data['approval'] = 1;
		}

		if(isset($data['edit_approval'])){
			unset($data['edit_approval']);
		}

		if(isset($data['save_and_send_request'])){
			$save_and_send_request = $data['save_and_send_request'];
			unset($data['save_and_send_request']);
		}

		if($data['receipt_delivery_type'] == 'inventory_receipt_voucher_returned_goods'){
			$data['order_return_number'] = $this->create_order_return_code();
		}else{
			$data['order_return_number'] = $this->create_delivery_order_return_code();
		}

		$data['total_amount'] 	= $data['total_amount'];
		$data['discount_total'] = $data['discount_total'];
		$data['total_after_discount'] = $data['total_after_discount'];
		$data['staff_id'] = get_staff_user_id();
		$data['datecreated'] = to_sql_date($data['datecreated'], true);
		if($data['order_date'] != null){
			$data['order_date'] = to_sql_date($data['order_date'], true);
		}
		$data['return_policies_information'] = get_option('wh_return_policies_information');
		$this->db->insert(db_prefix() . 'wh_order_returns', $data);
		$insert_id = $this->db->insert_id();

		/*update save note*/

		if (isset($insert_id)) {
			if($rel_type == 'manual' || $rel_type == 'i_sales_return_order' || $rel_type == 'i_purchasing_return_order'){
				//CASE: add i_sales_return_order
				foreach ($order_return_details as $order_return_detail) {
					$order_return_detail['order_return_id'] = $insert_id;

					$tax_money = 0;
					$tax_rate_value = 0;
					$tax_rate = null;
					$tax_id = null;
					$tax_name = null;
					if(isset($order_return_detail['tax_select'])){
						$tax_rate_data = $this->wh_get_tax_rate($order_return_detail['tax_select']);
						$tax_rate_value = $tax_rate_data['tax_rate'];
						$tax_rate = $tax_rate_data['tax_rate_str'];
						$tax_id = $tax_rate_data['tax_id_str'];
						$tax_name = $tax_rate_data['tax_name_str'];
					}

					if((float)$tax_rate_value != 0){
						$tax_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] * (float)$tax_rate_value / 100;
						$total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
						$amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
					}else{
						$total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
						$amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
					}

					$sub_total = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];

					$order_return_detail['tax_id'] = $tax_id;
					$order_return_detail['total_amount'] = $total_money;
					$order_return_detail['tax_rate'] = $tax_rate;
					$order_return_detail['sub_total'] = $sub_total;
					$order_return_detail['tax_name'] = $tax_name;

					unset($order_return_detail['order']);
					unset($order_return_detail['id']);
					unset($order_return_detail['tax_select']);
					unset($order_return_detail['unit_name']);

					$this->db->insert(db_prefix() . 'wh_order_return_details', $order_return_detail);
				}
			}elseif($rel_type == 'purchasing_return_order'){
				//CASE: add from Purchase order - Purchase
				
			}elseif($rel_type == 'sales_return_order'){
				//CASE: add from Sales order - Omni sale
				foreach ($order_return_details as $order_return_detail) {
					$order_return_detail['order_return_id'] = $insert_id;

					$tax_money = 0;
					$tax_rate_value = 0;
					$tax_rate = null;
					$tax_id = null;
					$tax_name = null;
					if(isset($order_return_detail['tax_select'])){
						$tax_rate_data = $this->wh_get_tax_rate($order_return_detail['tax_select']);
						$tax_rate_value = $tax_rate_data['tax_rate'];
						$tax_rate = $tax_rate_data['tax_rate_str'];
						$tax_id = $tax_rate_data['tax_id_str'];
						$tax_name = $tax_rate_data['tax_name_str'];
					}

					if((float)$tax_rate_value != 0){
						$tax_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] * (float)$tax_rate_value / 100;
						$total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
						$amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
					}else{
						$total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
						$amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
					}

					$sub_total = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];

					$order_return_detail['tax_id'] = $tax_id;
					$order_return_detail['total_amount'] = $total_money;
					$order_return_detail['tax_rate'] = $tax_rate;
					$order_return_detail['sub_total'] = $sub_total;
					$order_return_detail['tax_name'] = $tax_name;

					unset($order_return_detail['order']);
					unset($order_return_detail['id']);
					unset($order_return_detail['tax_select']);
					unset($order_return_detail['unit_name']);
					$this->db->insert(db_prefix() . 'wh_order_return_details', $order_return_detail);
				}
			}

			/*write log*/
			$data_log = [];
			$data_log['rel_id'] = $insert_id;
			$data_log['rel_type'] = 'order_returns';
			$data_log['staffid'] = get_staff_user_id();
			$data_log['date'] = date('Y-m-d H:i:s');
			$data_log['note'] = "order_returns";
			$this->add_activity_log($data_log);

			/*update next number setting*/
			if($data['receipt_delivery_type'] == 'inventory_receipt_voucher_returned_goods'){
				$this->update_inventory_setting(['next_order_return_number' =>  (int)get_warehouse_option('next_order_return_number')+1]);
			}else{
				$this->update_inventory_setting(['e_next_order_return_number' =>  (int)get_warehouse_option('e_next_order_return_number')+1]);
			}

			//send request approval
			if($save_and_send_request == 'true'){
				$this->send_request_approve(['rel_id' => $insert_id, 'rel_type' => '6', 'addedfrom' => $data['staff_id']]);
			}
		}

		//approval if not approval setting
		if (isset($insert_id)) {
			if ($data['approval'] == 1) {
				$this->update_approve_request($insert_id, 6, 1);
			}
		}

		return $insert_id > 0 ? $insert_id : false;
	}

	/**
	 * update order return
	 * @param  [type]  $data     
	 * @param  [type]  $rel_type 
	 * @param  boolean $id       
	 * @return [type]            
	 */
	public function update_order_return($data, $rel_type,  $id = false)
	{
		$results=0;

		$order_returns = [];
		$update_order_returns = [];
		$remove_order_returns = [];
		if(isset($data['isedit'])){
			unset($data['isedit']);
		}

		if (isset($data['newitems'])) {
			$order_returns = $data['newitems'];
			unset($data['newitems']);
		}

		if (isset($data['items'])) {
			$update_order_returns = $data['items'];
			unset($data['items']);
		}
		if (isset($data['removed_items'])) {
			$remove_order_returns = $data['removed_items'];
			unset($data['removed_items']);
		}

		unset($data['item_select']);
		unset($data['commodity_name']);
		unset($data['quantity']);
		unset($data['unit_price']);
		unset($data['unit_name']);
		unset($data['commodity_code']);
		unset($data['unit_id']);
		unset($data['discount']);
		unset($data['tax_rate']);
		unset($data['tax_name']);
		unset($data['rel_type_detail_id']);
		unset($data['item_reason_return']);
		unset($data['reason_return']);

		if(isset($data['main_additional_discount'])){
			unset($data['main_additional_discount']);
		}

		$check_appr = $this->get_approve_setting('5');
		$data['approval'] = 0;
		if ($check_appr && $check_appr != false) {
			$data['approval'] = 0;
		} else {
			$data['approval'] = 1;
		}

		if(isset($data['edit_approval'])){
			unset($data['edit_approval']);
		}

		if(isset($data['save_and_send_request']) ){
			$save_and_send_request = $data['save_and_send_request'];
			unset($data['save_and_send_request']);
		}

		$data['total_amount'] 	= $data['total_amount'];
		$data['discount_total'] = $data['discount_total'];
		$data['total_after_discount'] = $data['total_after_discount'];
		$data['staff_id'] = get_staff_user_id();
		$data['datecreated'] = to_sql_date($data['datecreated'], true);
		if($data['order_date'] != null){
			$data['order_date'] = to_sql_date($data['order_date'], true);
		}

		$order_return_id = $data['id'];
		unset($data['id']);

		$this->db->where('id', $order_return_id);
		$this->db->update(db_prefix() . 'wh_order_returns', $data);
		if ($this->db->affected_rows() > 0) {
			$results++;
		}

		/*update order return*/
		if($rel_type == 'manual' || $rel_type == 'i_sales_return_order' || $rel_type == 'i_purchasing_return_order'){
			//CASE: add manual
			foreach ($update_order_returns as $order_return) {
				$tax_money = 0;
				$tax_rate_value = 0;
				$tax_rate = null;
				$tax_id = null;
				$tax_name = null;
				if(isset($order_return['tax_select'])){
					$tax_rate_data = $this->wh_get_tax_rate($order_return['tax_select']);
					$tax_rate_value = $tax_rate_data['tax_rate'];
					$tax_rate = $tax_rate_data['tax_rate_str'];
					$tax_id = $tax_rate_data['tax_id_str'];
					$tax_name = $tax_rate_data['tax_name_str'];
				}

				if((float)$tax_rate_value != 0){
					$tax_money = (float)$order_return['unit_price'] * (float)$order_return['quantity'] * (float)$tax_rate_value / 100;
					$total_money = (float)$order_return['unit_price'] * (float)$order_return['quantity'] + (float)$tax_money;
					$amount = (float)$order_return['unit_price'] * (float)$order_return['quantity'] + (float)$tax_money;
				}else{
					$total_money = (float)$order_return['unit_price'] * (float)$order_return['quantity'];
					$amount = (float)$order_return['unit_price'] * (float)$order_return['quantity'];
				}

				$sub_total = (float)$order_return['unit_price'] * (float)$order_return['quantity'];

				$order_return['tax_id'] = $tax_id;
				$order_return['total_amount'] = $total_money;
				$order_return['tax_rate'] = $tax_rate;
				$order_return['sub_total'] = $sub_total;
				$order_return['tax_name'] = $tax_name;

				unset($order_return['order']);
				unset($order_return['tax_select']);
				unset($order_return['unit_name']);


				$this->db->where('id', $order_return['id']);
				if ($this->db->update(db_prefix() . 'wh_order_return_details', $order_return)) {
					$results++;
				}
			}
		}


		// delete order return handle for 3 case add manual, add from Purchase order - Purchase, add from Sales order - Omni sale
		foreach ($remove_order_returns as $order_return_detail_id) {
			$this->db->where('id', $order_return_detail_id);
			if ($this->db->delete(db_prefix() . 'wh_order_return_details')) {
				$results++;
			}
		}

		// Add order return
		if($rel_type == 'manual'){
			//CASE: add manual

			foreach ($order_returns as $order_return_detail) {
				$order_return_detail['order_return_id'] = $order_return_id;

				$tax_money = 0;
				$tax_rate_value = 0;
				$tax_rate = null;
				$tax_id = null;
				$tax_name = null;
				if(isset($order_return_detail['tax_select'])){
					$tax_rate_data = $this->wh_get_tax_rate($order_return_detail['tax_select']);
					$tax_rate_value = $tax_rate_data['tax_rate'];
					$tax_rate = $tax_rate_data['tax_rate_str'];
					$tax_id = $tax_rate_data['tax_id_str'];
					$tax_name = $tax_rate_data['tax_name_str'];
				}

				if((float)$tax_rate_value != 0){
					$tax_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] * (float)$tax_rate_value / 100;
					$total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
					$amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
				}else{
					$total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
					$amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
				}

				$sub_total = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];

				$order_return_detail['tax_id'] = $tax_id;
				$order_return_detail['total_amount'] = $total_money;
				$order_return_detail['tax_rate'] = $tax_rate;
				$order_return_detail['sub_total'] = $sub_total;
				$order_return_detail['tax_name'] = $tax_name;

				unset($order_return_detail['order']);
				unset($order_return_detail['id']);
				unset($order_return_detail['tax_select']);
				unset($order_return_detail['unit_name']);

				$this->db->insert(db_prefix() . 'wh_order_return_details', $order_return_detail);

				if($this->db->insert_id()){
					$results++;
				}
			}
		}
		

		// send request approval
		if($save_and_send_request == 'true'){
			$this->send_request_approve(['rel_id' => $order_return_id, 'rel_type' => '6', 'addedfrom' => $data['staff_id']]);
		}

		//approval if not approval setting
		if (isset($order_return_id)) {
			if ($data['approval'] == 1) {
				$this->update_approve_request($order_return_id, 6, 1);
			}
		}

		return $results > 0 ? true : false;
	}

	/**
	 * get html tax order return
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_html_tax_order_return($id)
	{
		$html = '';
		$html_currency = '';
		$preview_html = '';
		$pdf_html = '';
		$taxes = [];
		$t_rate = [];
		$tax_val = [];
		$tax_val_rs = [];
		$tax_name = [];
		$rs = [];
		$pdf_html_currency = '';

		$this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();

		$details = $this->get_order_return_detail($id);

		foreach($details as $row){
			if($row['tax_id'] != ''){
				$tax_arr = new_explode('|', $row['tax_id']);

				$tax_rate_arr = [];
				if($row['tax_rate'] != ''){
					$tax_rate_arr = new_explode('|', $row['tax_rate']);
				}

				foreach($tax_arr as $k => $tax_it){
					if(!isset($tax_rate_arr[$k]) ){
						$tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
					}

					if(!in_array($tax_it, $taxes)){
						$taxes[$tax_it] = $tax_it;
						$t_rate[$tax_it] = $tax_rate_arr[$k];
						$tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
					}
				}
			}
		}

		if(count($tax_name) > 0){
			foreach($tax_name as $key => $tn){
				$tax_val[$key] = 0;
				foreach($details as $row_dt){
					if(!(strpos($row_dt['tax_id'] ?? 0, $taxes[$key]) === false)){
						$tax_val[$key] += ($row_dt['quantity']*$row_dt['unit_price']*$t_rate[$key]/100);
					}
				}
				$pdf_html .= '<tr id="subtotal"><td ></td><td></td><td></td><td class="text_left">'.$tn.'</td><td class="text_right">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
				$preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], '').'</td><tr>';
				$html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], '').'</td></tr>';
				$html_currency .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
				$tax_val_rs[] = $tax_val[$key];
				$pdf_html_currency .= '<tr ><td align="right" width="85%">'.$tn.'</td><td align="right" width="15%">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
			}
		}

		$rs['pdf_html'] = $pdf_html;
		$rs['preview_html'] = $preview_html;
		$rs['html'] = $html;
		$rs['taxes'] = $taxes;
		$rs['taxes_val'] = $tax_val_rs;
		$rs['html_currency'] = $html_currency;
		$rs['pdf_html_currency'] = $pdf_html_currency;
		return $rs;
	}

	/**
	 * order return pdf
	 * @param  [type] $order_return 
	 * @return [type]               
	 */
	public function order_return_pdf($order_return) {
		return app_pdf('order_return', module_dir_path(WAREHOUSE_MODULE_NAME, 'libraries/pdf/Order_pdf.php'), $order_return);
	}

	/**
	 * order return create stock import
	 * @param  [type] $order_return_id 
	 * @return [type]                  
	 */
	public function order_return_create_stock_import($order_return_id)
	{
		$data = [];
		$newitems = [];
		$order_return =  $this->get_order_return($order_return_id);
		$order_return_details =  $this->get_order_return_detail($order_return_id);
		$total_tax_money = 0;
		$warehouse_id = get_option('warehouse_receive_return_order');

		//create data for stock import
		$data['save_and_send_request'] =  true;
		$data['date_c'] = _d(date('Y-m-d'));
		$data['date_add'] =  _d(date('Y-m-d'));
		$data['pr_order_id'] =  null;
		$data['supplier_code'] =  null;
		$data['supplier_name'] =  _l('create_from_oder_return').': '.$order_return->order_return_number;
		$data['buyer_id'] =  get_staff_user_id();
		$data['project'] =  '';
		$data['type'] =  '';
		$data['department'] =  '';
		$data['requester'] =  '';
		$data['deliver_name'] =  '';
		$data['warehouse_id_m'] =  '';
		$data['expiry_date_m'] =  '';
		$data['invoice_no'] =  '';
		$data['item_select'] =  '';
		$data['commodity_name'] =  '';
		$data['warehouse_id'] =  '';
		$data['note'] =  '';
		$data['quantities'] =  '';
		$data['unit_name'] =  '';
		$data['unit_price'] =  '';
		$data['lot_number'] =  '';
		$data['date_manufacture'] =  '';
		$data['expiry_date'] =  '';
		$data['commodity_code'] =  '';
		$data['unit_id'] =  '';

		$data['description'] =  '';

		foreach ($order_return_details as $key => $order_return_detail) {
			$tax_select = [];

			if($order_return_detail['tax_name'] != null && new_strlen($order_return_detail['tax_name']) > 0){
				$arr_tax_name = new_explode('|', $order_return_detail['tax_name']);
				$arr_tax_rate = new_explode('|', $order_return_detail['tax_rate']);
				foreach ($arr_tax_name as $tax_key => $tax_name_value) {
					$tax_select[] = $tax_name_value.'|'.$arr_tax_rate[$tax_key];
					$total_tax_money += ((float)$order_return_detail['quantity'] * (float)$order_return_detail['purchase_price']) * (float)$arr_tax_rate[$tax_key] / 100;
				}
			}

			$order = $key+1;
			$id = 'undefined';
			$commodity_name = $order_return_detail['commodity_name'];
			$note = '';
			$quantities = $order_return_detail['quantity'];
			$unit_price = $order_return_detail['purchase_price'];
			$lot_number = '';
			$date_manufacture = '';
			$expiry_date = '';
			$commodity_code = $order_return_detail['commodity_code'];
			$unit_id = $order_return_detail['unit_id'];
			$serial_number = '';
			$str_commodity_name = explode('SN: ', $order_return_detail['commodity_name']);
			if(count($str_commodity_name) > 1){
				$serial_number = $str_commodity_name[1];
			}
			
			$newitems[] = [
				'order' => $order,
				'id' => $id,
				'commodity_name' => $commodity_name,
				'warehouse_id' => $warehouse_id,
				'note' => $note,
				'quantities' => $quantities,
				'unit_price' => $unit_price,
				'lot_number' => $lot_number,
				'date_manufacture' => $date_manufacture,
				'expiry_date' => $expiry_date,
				'commodity_code' => $commodity_code,
				'unit_id' => $unit_id,
				'tax_select' => $tax_select,
				'serial_number' => $serial_number,
			];
		}

		$data['newitems'] = $newitems;
		$data['total_goods_money'] =  $order_return->subtotal;
		$data['value_of_inventory'] =  $order_return->subtotal;
		$data['total_tax_money'] =  $total_tax_money;
		$data['total_money'] =  $order_return->total_after_discount;


		//create stock import
		$result = $this->add_goods_receipt($data);
		if($result){
			//update order return
			$this->db->where('id', $order_return_id);
			$this->db->update(db_prefix().'wh_order_returns', ['receipt_delivery_id' => $result ]);
			return $result;
		}
		return false;
	}

	/**
	 * order return create stock export
	 * @param  [type] $order_return_id 
	 * @return [type]                  
	 */
	public function sales_return_order_create_stock_import($order_return_id)
	{
		$data = [];
		$newitems = [];
		$order_return =  $this->get_order_return($order_return_id);
		$order_return_details =  $this->get_order_return_detail($order_return_id);
		$total_tax_money = 0;
		$warehouse_id = get_option('warehouse_receive_return_order');

		//create data for stock import
		$data['save_and_send_request'] =  true;
		$data['date_c'] = _d(date('Y-m-d'));
		$data['date_add'] =  _d(date('Y-m-d'));
		$data['pr_order_id'] =  null;
		$data['supplier_code'] =  null;
		$data['supplier_name'] =  _l('create_from_oder_return').': '.$order_return->order_return_number;
		$data['buyer_id'] =  get_staff_user_id();
		$data['project'] =  '';
		$data['type'] =  '';
		$data['department'] =  '';
		$data['requester'] =  '';
		$data['deliver_name'] =  '';
		$data['warehouse_id_m'] =  '';
		$data['expiry_date_m'] =  '';
		$data['invoice_no'] =  '';
		$data['item_select'] =  '';
		$data['commodity_name'] =  '';
		$data['warehouse_id'] =  '';
		$data['note'] =  '';
		$data['quantities'] =  '';
		$data['unit_name'] =  '';
		$data['unit_price'] =  '';
		$data['lot_number'] =  '';
		$data['date_manufacture'] =  '';
		$data['expiry_date'] =  '';
		$data['commodity_code'] =  '';
		$data['unit_id'] =  '';

		$data['description'] =  '';

		foreach ($order_return_details as $key => $order_return_detail) {
			$tax_select = [];

			if($order_return_detail['tax_name'] != null && new_strlen($order_return_detail['tax_name']) > 0){
				$arr_tax_name = new_explode('|', $order_return_detail['tax_name']);
				$arr_tax_rate = new_explode('|', $order_return_detail['tax_rate']);
				foreach ($arr_tax_name as $tax_key => $tax_name_value) {
					$tax_select[] = $tax_name_value.'|'.$arr_tax_rate[$tax_key];
					$total_tax_money += ((float)$order_return_detail['quantity'] * (float)$order_return_detail['unit_price']) * (float)$arr_tax_rate[$tax_key] / 100;
				}
			}

			$order = $key+1;
			$id = 'undefined';
			$commodity_name = $order_return_detail['commodity_name'];
			$note = '';
			$quantities = $order_return_detail['quantity'];
			$unit_price = $order_return_detail['unit_price'];
			$lot_number = '';
			$date_manufacture = '';
			$expiry_date = '';
			$commodity_code = $order_return_detail['commodity_code'];
			$unit_id = $order_return_detail['unit_id'];
			
			$newitems[] = [
				'order' => $order,
				'id' => $id,
				'commodity_name' => $commodity_name,
				'warehouse_id' => $warehouse_id,
				'note' => $note,
				'quantities' => $quantities,
				'unit_price' => $unit_price,
				'lot_number' => $lot_number,
				'date_manufacture' => $date_manufacture,
				'expiry_date' => $expiry_date,
				'commodity_code' => $commodity_code,
				'unit_id' => $unit_id,
				'tax_select' => $tax_select,
			];
		}

		$data['newitems'] = $newitems;
		$data['total_goods_money'] =  $order_return->subtotal;
		$data['value_of_inventory'] =  $order_return->subtotal;
		$data['total_tax_money'] =  $total_tax_money;
		$data['total_money'] =  $order_return->total_after_discount;


		//create stock import
		$result = $this->add_goods_receipt($data);
		if($result){
			//update order return
			$this->db->where('id', $order_return_id);
			$this->db->update(db_prefix().'wh_order_returns', ['receipt_delivery_id' => $result ]);
			return $result;
		}
		return false;

	}

	/**
	 * purchasing return order create stock export
	 * @param  [type] $order_return_id 
	 * @return [type]                  
	 */
	public function purchasing_return_order_create_stock_export($order_return_id, $data_item_warehouse)
	{
		$data = [];
		$newitems = [];
		$order_return =  $this->get_order_return($order_return_id);
		$order_return_details =  $this->get_order_return_detail($order_return_id);
		$total_tax_money = 0;
		$arr_item_warehouse = [];
		foreach ($data_item_warehouse['newitems'] as $value) {
		    $arr_item_warehouse[$value['commodity_code']] = $value['warehouse_id'];
		}

		$vendor_address = '';  

		if(get_status_modules_wh('purchase')){
			$this->db->where('userid', $order_return->company_id);
			$vendor_info = $this->db->get(db_prefix().'pur_vendor')->row();
			if($vendor_info){
				if(new_strlen($vendor_info->shipping_street) > 0){
					$vendor_address .= $vendor_info->shipping_street;  
				}
				if(new_strlen($vendor_info->shipping_city) > 0){
					$vendor_address .= ', '.$vendor_info->shipping_city;  
				}
				if(new_strlen($vendor_info->shipping_state) > 0){
					$vendor_address .= ', '.$vendor_info->shipping_state;  
				}
				if(new_strlen($vendor_info->shipping_country) > 0){
					$vendor_address .= ', '.get_country_name($vendor_info->shipping_country);  
				}
			}
		}

		//create data for stock import
		$data['save_and_send_request'] =  false;
		$data['additional_discount'] =  false;
		$data['date_c'] = _d(date('y-m-d'));
		$data['date_add'] =  _d(date('y-m-d'));
		$data['pr_order_id'] =  null;
		$data['invoice_id'] =  null;
		$data['customer_code'] =  null;
		if(get_status_modules_wh('purchase')){
			$data['to_'] =  get_vendor_company_name($order_return->company_id);
		}else{
			$data['to_'] =  '';
		}
		$data['address'] =  $vendor_address;
		$data['project'] =  '';
		$data['type'] =  '';
		$data['department'] =  '';
		$data['requester'] =  '';
		$data['warehouse_id'] =  '';
		$data['staff_id'] =  get_staff_user_id();
		$data['invoice_no'] =  '';

		$data['item_select'] =  '';
		$data['commodity_name'] =  '';
		$data['note'] =  '';
		$data['available_quantity'] =  '';
		$data['unit_name'] =  '';
		$data['quantities'] =  '';
		$data['guarantee_period'] =  '';
		$data['unit_price'] =  '';
		$data['discount'] =  '';
		$data['commodity_code'] =  '';
		$data['unit_id'] =  '';
		$data['discount_money'] =  '';
		$data['total_after_discount'] =  '';
		$data['description'] =  '';

		foreach ($order_return_details as $key => $order_return_detail) {
			$tax_select = [];
			$available_quantity = 0;
			
			$warehouse_id = $arr_item_warehouse[$order_return_detail['commodity_code']];

			// check available_quantity
			$quantity_inventory = $this->get_quantity_inventory($warehouse_id, $order_return_detail['commodity_code']);
			if($quantity_inventory){
				$available_quantity = (float)$quantity_inventory->inventory_number; 				
			}

			if($order_return_detail['tax_name'] != null && new_strlen($order_return_detail['tax_name']) > 0){
				$arr_tax_name = new_explode('|', $order_return_detail['tax_name']);
				$arr_tax_rate = new_explode('|', $order_return_detail['tax_rate']);
				foreach ($arr_tax_name as $tax_key => $tax_name_value) {
					$tax_select[] = $tax_name_value.'|'.$arr_tax_rate[$tax_key];
					$total_tax_money += ((float)$order_return_detail['quantity'] * (float)$order_return_detail['unit_price']) * (float)$arr_tax_rate[$tax_key] / 100;
				}
			}

			$order = $key+1;
			$id = '';
			$commodity_name = $order_return_detail['commodity_name'];
			$note = '';
			$quantities = $order_return_detail['quantity'];
			$unit_price = $order_return_detail['unit_price'];
			$lot_number = '';
			$date_manufacture = '';
			$expiry_date = '';
			$commodity_code = $order_return_detail['commodity_code'];
			$unit_id = $order_return_detail['unit_id'];
			
			$newitems[] = [
				'order' => $order,
				'id' => $id,
				'commodity_name' => $commodity_name,
				'warehouse_id' => $warehouse_id,
				'note' => $note,
				'available_quantity' => $available_quantity,
				'quantities' =>  $order_return_detail['quantity'],
				'guarantee_period' => '',
				'unit_price' => $unit_price,
				'tax_select' => $tax_select,
				'commodity_code' => $commodity_code,
				'unit_id' => $unit_id,
				'discount_money' => $order_return_detail['discount_total'],
				'total_after_discount' => $order_return_detail['total_after_discount'],
			];
		}

		$data['newitems'] = $newitems;
		$data['sub_total'] =  $order_return->subtotal;
		$data['total_money'] =  $order_return->total_amount;
		$data['total_discount'] =  $order_return->discount_total;
		$data['after_discount'] =  $order_return->total_after_discount;
		$data['additional_discount'] =  $order_return->additional_discount;

		//create stock export
		$result = $this->add_goods_delivery($data);
		if($result){
			//update order return
			$this->db->where('id', $order_return_id);
			$this->db->update(db_prefix().'wh_order_returns', ['receipt_delivery_id' => $result ]);
			return $result;
		}
		return false;
	}
	
	/**
	 * order return get inventory receipt
	 * @return [type] 
	 */
	public function  order_return_get_inventory_receipt()
	{
		$arr_receipts = $this->get_invoices_goods_delivery('order_return_receipt');

		$own_goods_receipt = '';
		if (!has_permission('wh_stock_import', '', 'view')) {
			$own_goods_receipt = 'AND (' . db_prefix() . 'goods_receipt.addedfrom=' . get_staff_user_id().' OR ' . db_prefix() . 'goods_receipt.buyer_id=' . get_staff_user_id() .')';
		}

		if(count($arr_receipts) > 0){
			return $this->db->query('select *, goods_receipt_code as label from '.db_prefix().'goods_receipt where approval = 1 '.$own_goods_receipt.' AND id NOT IN ('.implode(", ", $arr_receipts).') order by id desc')->result_array();
		}
		return $this->db->query('select *, goods_receipt_code as label from '.db_prefix().'goods_receipt where approval = 1 '.$own_goods_receipt.'order by id desc')->result_array();
	}

	/**
	 * order return get purchasing order
	 * @return [type] 
	 */
	public function  order_return_get_purchasing_order()
	{
		$arr_purchase_orders = $this->get_invoices_goods_delivery('order_return_purchasing');
		if(count($arr_purchase_orders) > 0){
			return $this->db->query('select *, order_return_name as label from '.db_prefix().'wh_order_returns where approval = 1 AND rel_type = "purchasing_return_order" AND id NOT IN ("'.implode(", ", $arr_purchase_orders).'") order by id desc')->result_array();
		}
		return $this->db->query('select *, order_return_name as label from '.db_prefix().'wh_order_returns where approval = 1 AND rel_type = "purchasing_return_order" order by id desc')->result_array();
	}

	/**
	 * order return get inventory delivery
	 * @return [type] 
	 */
	public function  order_return_get_inventory_delivery()
	{
		$arr_deliveries = $this->get_invoices_goods_delivery('order_return_delivery');
		$own_goods_delivery = '';
		if (!has_permission('wh_stock_export', '', 'view')) {
			$own_goods_delivery = 'AND (' . db_prefix() . 'goods_delivery.addedfrom=' . get_staff_user_id().' OR ' . db_prefix() . 'goods_delivery.staff_id=' . get_staff_user_id() .')';
		}

		if(count($arr_deliveries) > 0){
			return $this->db->query('select *, goods_delivery_code as label from '.db_prefix().'goods_delivery where approval = 1 '.$own_goods_delivery.' AND id NOT IN ("'.implode(", ", $arr_deliveries).'") order by id desc')->result_array();
		}
		return $this->db->query('select *, goods_delivery_code as label from '.db_prefix().'goods_delivery where approval = 1 '.$own_goods_delivery.' order by id desc')->result_array();
	}

	/**
	 * order return get sale order
	 * @return [type] 
	 */
	public function  order_return_get_sale_order()
	{
		$arr_order_orders = $this->get_invoices_goods_delivery('order_return_sale');
		if(count($arr_order_orders) > 0){
			return $this->db->query('select *, order_return_name as label from '.db_prefix().'wh_order_returns where approval = 1 AND rel_type = "sales_return_order" AND id NOT IN ("'.implode(", ", $arr_order_orders).'") order by id desc')->result_array();
		}
		return $this->db->query('select *, order_return_name as label from '.db_prefix().'wh_order_returns where approval = 1 AND rel_type = "sales_return_order" order by id desc')->result_array();
	}

	/**
	 * order return get related data
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function order_return_get_related_data($data)
	{
		$related_data = '<option value=""></option>';

		if($data['receipt_delivery_type'] == 'inventory_receipt_voucher_returned_goods'){
			if($data['rel_type'] == 'manual'){
				$order_return_get_inventory_delivery = $this->order_return_get_inventory_delivery();
				foreach ($order_return_get_inventory_delivery as $value) {
					$related_data .= '<option value="' . $value['id'] . '">' . $value['goods_delivery_code'] . '</option>';
				}
			}else{
				$order_return_get_sale_order = $this->order_return_get_sale_order();
				foreach ($order_return_get_sale_order as $value) {
					$related_data .= '<option value="' . $value['id'] . '">' . $value['order_return_name'] . '</option>';
				}
			}

		}elseif($data['receipt_delivery_type'] == 'inventory_delivery_voucher_returned_purchasing_goods'){
			if($data['rel_type'] == 'manual'){
				$order_return_get_inventory_receipt = $this->order_return_get_inventory_receipt();
				foreach ($order_return_get_inventory_receipt as $value) {
					$related_data .= '<option value="' . $value['id'] . '">' . $value['goods_receipt_code'] . '</option>';
				}

			}else{
				$order_return_get_purchasing_order = $this->order_return_get_purchasing_order();
				foreach ($order_return_get_purchasing_order as $value) {
					$related_data .= '<option value="' . $value['id'] . '">' . $value['order_return_name'] . '</option>';
				}

			}
		}

		return $related_data;
	}

	/**
	 * order return get related data detail
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function order_return_get_related_data_detail($data)
	{

		$order_number = '';
		$company_id = 0;
		$company_name = null;
		$email = '';
		$phonenumber = '';
		$return_type = 'fully';
		$item_html = '';
		$order_number = '';
		$fee_return_order = 0;
		$refund_loyaty_point = 0;
		$subtotal = 0;
		$total_amount = 0;
		$discount_total = 0;
		$additional_discount = 0;
		$adjustment_amount = 0;
		$total_after_discount = 0;
		$currency = null;
		$return_reason = null;
		$vendors = '';

		if($data['rel_id'] != ''){

			if($data['receipt_delivery_type'] == 'inventory_receipt_voucher_returned_goods'){
				if($data['rel_type'] == 'manual'){
					//from inventory, Inventory delivey note
					//TODO
					$goods_delivery = $this->get_goods_delivery($data['rel_id']);
					$goods_delivery_details = $this->get_goods_delivery_detail($data['rel_id']);
					if($goods_delivery){

						$company_id = $goods_delivery->customer_code;
						if(($goods_delivery->customer_name == '' || $goods_delivery->customer_name == null ) && is_numeric($goods_delivery->customer_code)){
							$company_name = get_company_name($goods_delivery->customer_code);
						}else{
							$company_name = $goods_delivery->customer_name;
						}

						$return_type = 'fully';
						$order_number = $goods_delivery->goods_delivery_code;
						$subtotal = $goods_delivery->sub_total;
						$total_amount = $goods_delivery->total_money;
						$total_after_discount = $goods_delivery->after_discount;
						$return_reason = _l('wh_add_inventory_receipt_voucher_returned_goods');
					}

					if (count($goods_delivery_details) > 0) {
						$index_receipt = 0;
						foreach ($goods_delivery_details as $goods_delivery_detail) {
							$index_receipt++;
							$unit_name = wh_get_unit_name($goods_delivery_detail['unit_id']);
							$taxname = '';
							$expiry_date = null;
							$lot_number = null;
							$commodity_name = $goods_delivery_detail['commodity_name'];

							if(new_strlen($commodity_name) == 0){
								$commodity_name = wh_get_item_variatiom($goods_delivery_detail['commodity_code']);
							}
							$purchase_price = 0;
							$get_goods_transaction_detail = $this->get_goods_transaction_detail($goods_delivery_detail['goods_delivery_id'], $goods_delivery_detail['id']);
							if($get_goods_transaction_detail){
								$purchase_price = $get_goods_transaction_detail->purchase_price;
							}

							$item_html .= $this->warehouse_model->create_order_return_row_template('manual', 0, 'newitems[' . $index_receipt . ']', $commodity_name, $goods_delivery_detail['quantities'], $unit_name, $goods_delivery_detail['unit_price'], $taxname, $goods_delivery_detail['commodity_code'], $goods_delivery_detail['unit_id'] , $goods_delivery_detail['tax_rate'], $goods_delivery_detail['sub_total'], $goods_delivery_detail['discount'], $goods_delivery_detail['discount_money'], $goods_delivery_detail['total_money'], '', $goods_delivery_detail['sub_total'],$goods_delivery_detail['tax_name'],$goods_delivery_detail['tax_id'], $goods_delivery_detail['id'], true, false, $purchase_price);

						}
					}

				}else{
				//from omni_sales, Sales return order
					$order_return =  $this->get_order_return($data['rel_id']);
					$order_return_details =  $this->get_order_return_detail($data['rel_id']);
					if($order_return){
						$company_id = $order_return->company_id;
						$email = $order_return->email;
						$phonenumber = $order_return->phonenumber;
						$return_type = $order_return->return_type;
						$order_number = $order_return->order_number;
						$company_name = $order_return->company_name;
						$fee_return_order = $order_return->fee_return_order;
						$refund_loyaty_point = $order_return->refund_loyaty_point;
						$subtotal = $order_return->subtotal;
						$total_amount = $order_return->total_amount;
						$discount_total = $order_return->discount_total;
						$additional_discount = $order_return->additional_discount;
						$adjustment_amount = $order_return->adjustment_amount;
						$total_after_discount = $order_return->total_after_discount;
						$currency = $order_return->currency;
						$return_reason = _l('wh_add_inventory_receipt_voucher_returned_goods');
					}

					if (count($order_return_details) > 0) {
						$index_receipt = 0;
						foreach ($order_return_details as $order_return_detail) {
							$index_receipt++;
							$unit_name = wh_get_unit_name($order_return_detail['unit_id']);
							$taxname = '';
							$expiry_date = null;
							$lot_number = null;
							$commodity_name = $order_return_detail['commodity_name'];

							if(new_strlen($commodity_name) == 0){
								$commodity_name = wh_get_item_variatiom($order_return_detail['commodity_code']);
							}

							$item_html .= $this->warehouse_model->create_order_return_row_template('i_sales_return_order', $order_return_detail['rel_type_detail_id'], 'newitems[' . $index_receipt . ']', $commodity_name, $order_return_detail['quantity'], $unit_name, $order_return_detail['unit_price'], $taxname, $order_return_detail['commodity_code'], $order_return_detail['unit_id'] , $order_return_detail['tax_rate'], $order_return_detail['total_amount'], $order_return_detail['discount'], $order_return_detail['discount_total'], $order_return_detail['total_after_discount'], $order_return_detail['reason_return'], $order_return_detail['sub_total'],$order_return_detail['tax_name'],$order_return_detail['tax_id'], $order_return_detail['id'], true);

						}
					}
				}

			}elseif($data['receipt_delivery_type'] == 'inventory_delivery_voucher_returned_purchasing_goods'){
				if($data['rel_type'] == 'manual'){
				//from inventory, Inventory receipt note
					//TODO

					$goods_receipt = $this->get_goods_receipt($data['rel_id']);
					$goods_receipt_details = $this->get_goods_receipt_detail($data['rel_id']);
					if($goods_receipt){
						$company_id = $goods_receipt->supplier_code;
						$company_name = $goods_receipt->supplier_name;
						$return_type = 'fully';
						$order_number = $goods_receipt->goods_receipt_code;
						$subtotal = $goods_receipt->total_goods_money;
						$total_amount = $goods_receipt->total_money;
						$total_after_discount = $goods_receipt->total_money;
						$return_reason = _l('wh_add_inventory_receipt_voucher_returned_goods');
					}

					if (count($goods_receipt_details) > 0) {
						$index_receipt = 0;
						foreach ($goods_receipt_details as $goods_receipt_detail) {
							$index_receipt++;
							$unit_name = wh_get_unit_name($goods_receipt_detail['unit_id']);
							$taxname = '';
							$expiry_date = null;
							$lot_number = null;
							$commodity_name = $goods_receipt_detail['commodity_name'];

							if(new_strlen($commodity_name) == 0){
								$commodity_name = wh_get_item_variatiom($goods_receipt_detail['commodity_code']);
							}

							$item_html .= $this->warehouse_model->create_order_return_row_template('manual', 0, 'newitems[' . $index_receipt . ']', $commodity_name, $goods_receipt_detail['quantities'], $unit_name, $goods_receipt_detail['unit_price'], $taxname, $goods_receipt_detail['commodity_code'], $goods_receipt_detail['unit_id'] , $goods_receipt_detail['tax_rate'], $goods_receipt_detail['sub_total'], $goods_receipt_detail['discount'], $goods_receipt_detail['discount_money'], $goods_receipt_detail['goods_money'], '', $goods_receipt_detail['sub_total'],$goods_receipt_detail['tax_name'],$goods_receipt_detail['tax'], $goods_receipt_detail['id'], true);

						}
					}


				}else{
				//from Purchase, Purchasing return order

				//get list vendor 
					$get_vendor = $this->get_vendor();
					$vendors .= '<option value=""></option>';
					foreach ($get_vendor as $value) {
						$vendors .= '<option value="' . $value['userid'] . '">' . $value['company'] . '</option>';
					}

					$order_return =  $this->get_order_return($data['rel_id']);
					$order_return_details =  $this->get_order_return_detail($data['rel_id']);
					if($order_return){
						$company_id = $order_return->company_id;
						$email = $order_return->email;
						$phonenumber = $order_return->phonenumber;
						$return_type = $order_return->return_type;
						$order_number = $order_return->order_number;
						$company_name = $order_return->company_name;
						$fee_return_order = $order_return->fee_return_order;
						$refund_loyaty_point = $order_return->refund_loyaty_point;
						$subtotal = $order_return->subtotal;
						$total_amount = $order_return->total_amount;
						$discount_total = $order_return->discount_total;
						$additional_discount = $order_return->additional_discount;
						$adjustment_amount = $order_return->adjustment_amount;
						$total_after_discount = $order_return->total_after_discount;
						$currency = $order_return->currency;
						$return_reason = _l('wh_add_inventory_receipt_voucher_returned_goods');
					}

					if (count($order_return_details) > 0) {
						$index_receipt = 0;
						foreach ($order_return_details as $order_return_detail) {
							$index_receipt++;
							$unit_name = wh_get_unit_name($order_return_detail['unit_id']);
							$taxname = '';
							$expiry_date = null;
							$lot_number = null;
							$commodity_name = $order_return_detail['commodity_name'];

							if(new_strlen($commodity_name) == 0){
								$commodity_name = wh_get_item_variatiom($order_return_detail['commodity_code']);
							}

							$item_html .= $this->warehouse_model->create_order_return_row_template('i_purchasing_return_order', $order_return_detail['rel_type_detail_id'], 'newitems[' . $index_receipt . ']', $commodity_name, $order_return_detail['quantity'], $unit_name, $order_return_detail['unit_price'], $taxname, $order_return_detail['commodity_code'], $order_return_detail['unit_id'] , $order_return_detail['tax_rate'], $order_return_detail['total_amount'], $order_return_detail['discount'], $order_return_detail['discount_total'], $order_return_detail['total_after_discount'], $order_return_detail['reason_return'], $order_return_detail['sub_total'],$order_return_detail['tax_name'],$order_return_detail['tax_id'], $order_return_detail['id'], true);

						}
					}

				}
			}
		}

		$results = [];
		$results = [
			'order_number' => $order_number,
			'company_id' => $company_id,
			'company_name' => $company_name,
			'email' => $email,
			'phonenumber' => $phonenumber,
			'return_type' => $return_type,
			'item_html' => $item_html,
			'order_number' => $order_number,
			'fee_return_order' => $fee_return_order,
			'refund_loyaty_point' => $refund_loyaty_point,
			'subtotal' => $subtotal,
			'total_amount' => $total_amount,
			'discount_total' => $discount_total,
			'additional_discount' => $additional_discount,
			'adjustment_amount' => $adjustment_amount,
			'total_after_discount' => $total_after_discount,
			'currency' => $currency,
			'return_reason' => $return_reason,
			'vendors' => $vendors,
		];

		return $results;
	}

	/**
	 * create delivery order return code
	 * @return [type] 
	 */
	public function create_delivery_order_return_code()
	{
		$goods_code = get_warehouse_option('e_order_return_number_prefix') . (get_warehouse_option('e_next_order_return_number'));
		return $goods_code;
	}

	/**
	 * order return render warehouse modal
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function order_return_render_warehouse_modal($id)
	{
		$html = '';
		$get_order_return = $this->get_order_return($id);
		$get_order_return_detail = $this->get_order_return_detail($id);
		$arr_item_warehouse = [];
		$warehouse_data = $this->get_warehouse();
		$name_commodity_code = 'commodity_code';
		$name_commodity_name = 'commodity_name';
		$name_warehouse_id = 'warehouse_id';
		$name_quantity = 'quantity';
		$array_quantity_attr = ['readonly' => true];
		$get_goods_receipt_detail = [];

		if($get_order_return){
			if($get_order_return->rel_type == 'manual' && $get_order_return->receipt_delivery_type == 'inventory_delivery_voucher_returned_purchasing_goods'){


				$get_goods_receipt_detail = $this->get_goods_receipt_detail($get_order_return->rel_id);
				
			}elseif($get_order_return->rel_type == 'i_purchasing_return_order'){
				
				/*get po from return order*/				
				$this->db->where('id', $get_order_return->rel_id);
				$wh_order_returns = $this->db->get(db_prefix().'wh_order_returns')->row();
				if($wh_order_returns){
					//get receipt note from PO id
					$this->db->where('pr_order_id', $wh_order_returns->rel_id);
					$goods_receipt = $this->db->get(db_prefix().'goods_receipt')->row();
					if($goods_receipt){
						$get_goods_receipt_detail = $this->get_goods_receipt_detail($goods_receipt->id);
					}
				}
			}

			foreach ($get_goods_receipt_detail as $value) {
				$arr_item_warehouse[] = [
					'warehouse_id' => $value['warehouse_id'],
					'commodity_code' => $value['commodity_code'],
				];				    
			}

			$index = 0;
			foreach ($get_order_return_detail as $key => $order_return_detail) {
				$index++;
				$name = 'newitems['. $index .']';

				foreach ($arr_item_warehouse as $item_warehouse) {
					if($item_warehouse['commodity_code'] == $order_return_detail['commodity_code']){
						$html .= '<tr>';
						$html .= '<td class="">' . render_textarea($name.'['.$name_commodity_name.']', '', $order_return_detail['commodity_name'], ['rows' => 2, 'placeholder' => _l('item_description_placeholder'), 'readonly' => true] ) . '</td>';

						$html .= '<td class="hide">' .
						render_input($name.'['.$name_commodity_code.']', '', $order_return_detail['commodity_code'], 'text', ['placeholder' => _l('commodity_notes')], [], 'no-margin', 'input-transparent text-left').'</td>';

						$html .= '<td>' . 
						render_input($name.'['.$name_quantity.']', '', $order_return_detail['quantity'], 'number', $array_quantity_attr, [], 'no-margin') .'</td>';

						$html .= '<td>' .
						render_select($name.'['.$name_warehouse_id.']', $warehouse_data,array('warehouse_id','warehouse_name'),'',$item_warehouse['warehouse_id'], [], ["data-none-selected-text" => _l('warehouse_name')], 'no-margin', '', false).'</td>';
						$html .= '</tr>';

					}
				}
			}


		}

		return $html;
	}

	/**
	 * add serial number
	 * @param [type] $commodity_id        
	 * @param [type] $warehouse_id        
	 * @param [type] $inventory_manage_id 
	 * @param [type] $str_serial_number   
	 */
	public function add_serial_number($commodity_id, $warehouse_id, $inventory_manage_id, $str_serial_number)
	{
		if(new_strlen($str_serial_number) > 0){
			$serial_number_data = [];
			$arr_serial_number = new_explode(',', $str_serial_number);

			foreach ($arr_serial_number as $value) {
				if(new_strlen($value) > 0){		
					$serial_number_data[] = [
						'commodity_id' => $commodity_id,
						'warehouse_id' => $warehouse_id,
						'serial_number' => $value,
						'inventory_manage_id' => $inventory_manage_id,
					];
				}
			}

			if(count($serial_number_data) != 0){
				$affected_rows = $this->db->insert_batch(db_prefix().'wh_inventory_serial_numbers', $serial_number_data);
				if($affected_rows > 0){
					return true;
				}
				return false;
			}
		}
	}

	/**
	 * revert serial number
	 * @param  [type] $commodity_id        
	 * @param  [type] $warehouse_id        
	 * @param  [type] $inventory_manage_id 
	 * @param  [type] $str_serial_number   
	 * @return [type]                      
	 */
	public function revert_serial_number($commodity_id, $warehouse_id, $inventory_manage_id, $str_serial_number)
	{
		if(new_strlen($str_serial_number) > 0){
			$serial_number_data = [];
			$arr_serial_number = new_explode(',', $str_serial_number);
			$arr_serial_number_id = [];

			$this->db->where('commodity_id', $commodity_id);
			$this->db->where('warehouse_id', $warehouse_id);
			$this->db->where('serial_number IN ("' . implode('","', $arr_serial_number) . '") ');
			$this->db->limit($inventory_manage_id);
			$inventory_serial_numbers = $this->db->get(db_prefix().'wh_inventory_serial_numbers')->result_array();

			foreach ($inventory_serial_numbers as $value) {
				$arr_serial_number_id[] = $value['id'];
			}

			if(count($arr_serial_number_id) > 0){
				$this->db->where('id IN ('.implode(',', $arr_serial_number_id).')');
				$this->db->delete(db_prefix() . 'wh_inventory_serial_numbers');
				if ($this->db->affected_rows() > 0) {
					return true;
				}
			}
			return false;
		}
		return false;
	}

	/**
	 * get serial number for delivery note
	 * @param  [type] $commodity_id        
	 * @param  [type] $warehouse_id        
	 * @param  [type] $inventory_manage_id 
	 * @param  [type] $quantity            
	 * @return [type]                      
	 */
	public function get_serial_number_for_delivery_note($commodity_id, $warehouse_id, $inventory_manage_id, $quantity, $serial_number, $goods_delivery_detail_id, $commodity_name)
	{
		$str_serial_number = '';

		//check serial number is used
		$this->db->where('commodity_id', $commodity_id);
		$this->db->where('warehouse_id', $warehouse_id);
		$this->db->where('serial_number', $serial_number);
		$this->db->where('is_used', 'no');
		$this->db->order_by('id', 'asc');
		$this->db->limit((int)$quantity);
		$inventory_serial_numbers = $this->db->get(db_prefix().'wh_inventory_serial_numbers')->row();

		if($inventory_serial_numbers && new_strlen($serial_number) > 0){
			$this->db->where('id', $inventory_serial_numbers->id);
			$this->db->update(db_prefix().'wh_inventory_serial_numbers', ['is_used' => 'yes']);
			$str_serial_number = $serial_number;
		}else{

			$arr_serial_number_used = [];
			// get list serial number
			$this->db->where('commodity_id', $commodity_id);
			$this->db->where('warehouse_id', $warehouse_id);
			$this->db->where('inventory_manage_id', $inventory_manage_id);
			$this->db->order_by('id', 'asc');
			$this->db->limit((int)$quantity);
			$inventory_serial_numbers = $this->db->get(db_prefix().'wh_inventory_serial_numbers')->result_array();
			foreach ($inventory_serial_numbers as $value) {
				$arr_serial_number_used[] = $value['id'];
				if(new_strlen($str_serial_number) > 0){
					$str_serial_number .= ','.$value['serial_number'];
				}else{
					$str_serial_number .= $value['serial_number'];
				}
			}

			if(count($arr_serial_number_used) > 0){
				$this->db->where('id IN ('.implode(',', $arr_serial_number_used).')');
				$this->db->update(db_prefix().'wh_inventory_serial_numbers', ['is_used' => 'yes']);
			}

			if(new_strlen($serial_number) > 0){
				//serial number is used => need update serial number name in commodity name
				$commodity_name = new_str_replace($serial_number, $str_serial_number, $commodity_name);
				$this->db->where('id', $goods_delivery_detail_id);
				$this->db->update(db_prefix().'goods_delivery_detail', ['commodity_name' => $commodity_name]);
			}
		}

		return $str_serial_number;
	}

	/**
	 * get list temporaty serial numbers
	 * @param  [type] $commodity_id 
	 * @param  [type] $warehouse_id 
	 * @param  [type] $quantity     
	 * @return [type]               
	 */
	public function get_list_temporaty_serial_numbers($commodity_id, $warehouse_id, $quantity = '', $where = [] )
	{
		$this->db->where('commodity_id', $commodity_id);
		$this->db->where('warehouse_id', $warehouse_id);
		$this->db->where('is_used', 'no');
		if(count($where) > 0){
			$this->db->where('serial_number NOT IN ("' . implode('","', $where) . '") ');
		}
		$this->db->order_by('id', 'asc');
		if(is_numeric($quantity)){
			$this->db->limit((int)$quantity);
		}
		$inventory_serial_numbers = $this->db->get(db_prefix().'wh_inventory_serial_numbers')->result_array();
		return $inventory_serial_numbers;
	}

	/**
	 * get serial number for internal delivery note
	 * @param  [type] $commodity_id             
	 * @param  [type] $warehouse_id             
	 * @param  [type] $inventory_manage_id      
	 * @param  [type] $quantity                 
	 * @param  [type] $serial_number            
	 * @param  [type] $goods_delivery_detail_id 
	 * @param  [type] $commodity_name           
	 * @return [type]                           
	 */
	public function get_serial_number_for_internal_delivery_note($commodity_id, $warehouse_id, $inventory_manage_id, $quantity, $serial_number, $internal_delivery_detail_id, $commodity_name)
	{
		$str_serial_number = '';

		//check serial number is used
		$this->db->where('commodity_id', $commodity_id);
		$this->db->where('warehouse_id', $warehouse_id);
		$this->db->where('serial_number', $serial_number);
		$this->db->where('is_used', 'no');
		$this->db->order_by('id', 'asc');
		$this->db->limit((int)$quantity);
		$inventory_serial_numbers = $this->db->get(db_prefix().'wh_inventory_serial_numbers')->row();

		if($inventory_serial_numbers && new_strlen($serial_number) > 0){
			$this->db->where('id', $inventory_serial_numbers->id);
			$this->db->delete(db_prefix().'wh_inventory_serial_numbers');
			$str_serial_number = $serial_number;
		}else{

			$arr_serial_number_used = [];
			// get list serial number
			$this->db->where('commodity_id', $commodity_id);
			$this->db->where('warehouse_id', $warehouse_id);
			$this->db->where('inventory_manage_id', $inventory_manage_id);
			$this->db->order_by('id', 'asc');
			$this->db->limit((int)$quantity);
			$inventory_serial_numbers = $this->db->get(db_prefix().'wh_inventory_serial_numbers')->result_array();
			foreach ($inventory_serial_numbers as $value) {
				$arr_serial_number_used[] = $value['id'];
				if(new_strlen($str_serial_number) > 0){
					$str_serial_number .= ','.$value['serial_number'];
				}else{
					$str_serial_number .= $value['serial_number'];
				}
			}

			if(count($arr_serial_number_used) > 0){
				$this->db->where('id IN ('.implode(',', $arr_serial_number_used).')');
				$this->db->delete(db_prefix().'wh_inventory_serial_numbers');
			}

			if(new_strlen($serial_number) > 0){
				//serial number is used => need update serial number name in commodity name
				$commodity_name = new_str_replace($serial_number, $str_serial_number, $commodity_name);
				$this->db->where('id', $internal_delivery_detail_id);
				$this->db->update(db_prefix().'internal_delivery_note_detail', ['commodity_name' => $commodity_name]);
			}
		}

		return $str_serial_number;
	}

	/**
	 * loss adjustment delete serial number
	 * @param  [type] $commodity_id                
	 * @param  [type] $warehouse_id                
	 * @param  [type] $inventory_manage_id         
	 * @param  [type] $quantity                    
	 * @param  [type] $serial_number               
	 * @param  [type] $internal_delivery_detail_id 
	 * @param  [type] $commodity_name              
	 * @return [type]                              
	 */
	public function loss_adjustment_delete_serial_number($commodity_id, $warehouse_id, $inventory_manage_id, $quantity, $serial_number)
	{
		$str_serial_number = '';

		//check serial number is used
		$this->db->where('commodity_id', $commodity_id);
		$this->db->where('warehouse_id', $warehouse_id);
		$this->db->where('serial_number', $serial_number);
		$this->db->where('is_used', 'no');
		$this->db->order_by('id', 'asc');
		$this->db->limit((int)$quantity);
		$inventory_serial_numbers = $this->db->get(db_prefix().'wh_inventory_serial_numbers')->row();

		if($inventory_serial_numbers && new_strlen($serial_number) > 0){
			$this->db->where('id', $inventory_serial_numbers->id);
			$this->db->update(db_prefix().'wh_inventory_serial_numbers', ['is_used' => 'yes']);
			// $this->db->delete(db_prefix().'wh_inventory_serial_numbers');
			$str_serial_number = $serial_number;
		}else{

			$arr_serial_number_used = [];
			// get list serial number
			$this->db->where('commodity_id', $commodity_id);
			$this->db->where('warehouse_id', $warehouse_id);
			$this->db->where('inventory_manage_id', $inventory_manage_id);
			$this->db->order_by('id', 'asc');
			$this->db->limit((int)$quantity);
			$inventory_serial_numbers = $this->db->get(db_prefix().'wh_inventory_serial_numbers')->result_array();
			foreach ($inventory_serial_numbers as $value) {
				$arr_serial_number_used[] = $value['id'];
				if(new_strlen($str_serial_number) > 0){
					$str_serial_number .= ','.$value['serial_number'];
				}else{
					$str_serial_number .= $value['serial_number'];
				}
			}

			if(count($arr_serial_number_used) > 0){
				$this->db->where('id IN ('.implode(',', $arr_serial_number_used).')');
				$this->db->update(db_prefix().'wh_inventory_serial_numbers', ['is_used' => 'yes']);
				// $this->db->delete(db_prefix().'wh_inventory_serial_numbers');
			}
		}
		return $str_serial_number;
	}

	/**
	 * get inventory warehouse by commodity
	 * @param  boolean $commodity_id 
	 * @return [type]                
	 */
	public function get_inventory_warehouse_by_commodity($commodity_id = false)
	{
		$arr_inventory_number = [];
		$sql = 'SELECT ' . db_prefix() . 'warehouse.warehouse_name, '.db_prefix().'warehouse.warehouse_id, '.db_prefix().'inventory_manage.inventory_number FROM ' . db_prefix() . 'inventory_manage
		LEFT JOIN ' . db_prefix() . 'warehouse on ' . db_prefix() . 'inventory_manage.warehouse_id = ' . db_prefix() . 'warehouse.warehouse_id
		where ' . db_prefix() . 'inventory_manage.commodity_id = ' . $commodity_id.' order by '.db_prefix().'inventory_manage.id asc';
		$inventory_number = $this->db->query($sql)->result_array();

		foreach ($inventory_number as $value) {
		    if(isset($arr_inventory_number[$value['warehouse_id']])){
		    	$arr_inventory_number[$value['warehouse_id']]['inventory_number'] += $value['inventory_number'];
		    }else{
		    	$arr_inventory_number[$value['warehouse_id']] = $value;
		    }
		}
		return $arr_inventory_number;
	}

	/**
	 * create shipment from delivery note
	 * @param  [type] $delivery_id 
	 * @return [type]              
	 */
	public function create_shipment_from_delivery_note($delivery_id)
	{
		// create shipment
		$goods_delivery = $this->get_goods_delivery($delivery_id);
		if($goods_delivery){
			$shipment = [];
			$shipment['cart_id'] = null;
			$shipment['shipment_number'] = 'SHIPMENT' . date('YmdHi');
			$shipment['planned_shipping_date'] = null;
			$shipment['shipment_status'] = 'confirmed_order';
			$shipment['datecreated'] = date('Y-m-d H:i:s');
			$shipment['goods_delivery_id'] = $delivery_id;
			$shipment['shipment_hash'] = app_generate_hash();

			$this->db->insert(db_prefix() . 'wh_omni_shipments', $shipment);
			$insert_id = $this->db->insert_id();
			if($insert_id){
				$shipment_log1 = _l('wh_order_has_been_confirmed');
				$this->log_wh_activity($insert_id, 'shipment', $shipment_log1);
				$shipment_log2 = _l('wh_shipment_have_been_created');
				$this->log_wh_activity($insert_id, 'shipment', $shipment_log2);

				hooks()->do_action('after_create_shipment_from_delivery_note', $insert_id);
				return $insert_id;
			}
		}

		return false;
	}

	/**
	 * warehouse check update shipment when delivery note approval
	 * @param  [type]  $rel_id      
	 * @param  string  $status      
	 * @param  string  $rel_type    
	 * @param  integer $delivery_id 
	 * @return [type]               
	 */
	public function warehouse_check_update_shipment_when_delivery_note_approval($rel_id, $status = 'quality_check', $rel_type = 'delivery_approval', $delivery_id = 0)
	{

		$delivery_list_status = delivery_list_status();
		$arr_delivery_list_status_name = [];
		$arr_delivery_list_status_order = [];
		foreach ($delivery_list_status as $value) {
			$arr_delivery_list_status_name[$value['id']] = $value['order'];
			$arr_delivery_list_status_order[$value['order']] = $value['id'];
		}

		if($status == 'quality_check' && $rel_type == 'delivery_approval'){


			$shipment = $this->get_shipment_by_delivery($rel_id);
			if($shipment){
				$this->update_shipment_status($shipment->id, ['shipment_status' => 'quality_check']);
				return true;
			}
			return false;

		}elseif($rel_type == 'delivery_status_mark'){

			$shipment = $this->get_shipment_by_delivery($rel_id);
			if($shipment){

				if(isset($arr_delivery_list_status_name[$status])){
					if((int)$arr_delivery_list_status_name[$status] >= 4){
							// delivered
						$this->update_shipment_status($shipment->id, ['shipment_status' => 'product_delivered']);
					}elseif((int)$arr_delivery_list_status_name[$status] >= 3){
							// delivery_in_progress
						$this->update_shipment_status($shipment->id, ['shipment_status' => 'product_dispatched']);
					}
				}
			}

		}elseif($rel_type == 'packing_list_status_mark'){

			$shipment = $this->get_shipment_by_delivery($rel_id);
			if($shipment){
				if(isset($arr_delivery_list_status_name[$status])){
					if((int)$arr_delivery_list_status_name[$status] >= 3){
							// delivery_in_progress
						$this->update_shipment_status($shipment->id, ['shipment_status' => 'product_dispatched']);
					}
				}
			}

		}
		return true;
	}

	/**
	 * get shipment by delivery
	 * @param  [type] $delivery_id 
	 * @return [type]              
	 */
	public function get_shipment_by_delivery($delivery_id)
	{
		if (is_numeric($delivery_id)) {
			$this->db->where('goods_delivery_id', $delivery_id);
			$this->db->order_by('id', 'desc');
			return $this->db->get(db_prefix() . 'wh_omni_shipments')->row();
		}
		if ($delivery_id == false) {
			return $this->db->query('select * from '.db_prefix().'wh_omni_shipments')->result_array();
		}
	}

	/**
	 * get shipment by client
	 * @param  [type] $client_id 
	 * @return [type]            
	 */
	public function get_shipment_by_client($client_id)
	{
		// get list shipment by client
		$sql_where = "SELECT * from ".db_prefix()."wh_omni_shipments as snm
		WHERE  snm.goods_delivery_id IN ( select id from ".db_prefix()."goods_delivery as tem_gd where tem_gd.customer_code = ".$client_id." ) order by datecreated desc" ;
		$shipments = $this->db->query($sql_where)->result_array(); 
		return $shipments;
	}

	/**
	 * wh client get shipment activity log
	 * @param  [type] $shipment_id 
	 * @return [type]              
	 */
	public function wh_client_get_shipment_activity_log($shipment_id)
	{
		$cart_id = '';
		$delivery_id = '';
		$packing_list_id = [];

		$arr_activity_log = [];

		// sales order activity_log
		// delivery note activity_log
		// packing list activity_log
		
		$this->db->where('id', $shipment_id);
		$shipment = $this->db->get(db_prefix() . 'wh_omni_shipments')->row();
		if($shipment){
			$cart_id = $shipment->cart_id;

			

			if(new_strlen($shipment->goods_delivery_id) > 0){
				$delivery_id = $shipment->goods_delivery_id;
			}elseif(new_strlen($shipment->cart_id) > 0){
				$this->load->model('omni_sales/omni_sales_model');
				$get_cart = $this->omni_sales_model->get_cart($shipment->cart_id);
				if($get_cart && is_numeric($get_cart->stock_export_number)){
					$delivery_id = $get_cart->stock_export_number;
				}
			}

			if(new_strlen($delivery_id) > 0){
				$packing_lists = $this->get_packing_list_by_deivery_note($delivery_id);

				if(count($packing_lists) > 0){
					foreach ($packing_lists as $value) {
						$packing_list_id[] = $value['id'];
					}
				}
			}
		}

		$this->db->or_group_start();
		$this->db->where('rel_id', $shipment_id);
		$this->db->where('rel_type', 'shipment');
		$this->db->group_end();
		if(new_strlen($cart_id) > 0){
			$this->db->or_group_start();
			$this->db->where('rel_id', $cart_id);
			$this->db->where('rel_type', 'omni_order');
			$this->db->group_end();
		}

		if(new_strlen($delivery_id) > 0){
			$this->db->or_group_start();
			$this->db->where('rel_id', $delivery_id);
			$this->db->where('rel_type', 'delivery');
			$this->db->group_end();
		}

		if(count($packing_list_id) > 0){
			$this->db->or_group_start();
			$this->db->where('rel_id IN ('.implode(',', $packing_list_id).')');
			$this->db->where('rel_type', 'packing_list');
			$this->db->group_end();
		}

		$this->db->order_by('date', 'desc');
		$shipment_activity_log = $this->db->get(db_prefix().'wh_goods_delivery_activity_log')->result_array();

		return $shipment_activity_log;
	}

	/**
	 * warranty period pdf
	 * @param  [type] $warranty_period 
	 * @return [type]                  
	 */
	public function warranty_period_pdf($warranty_period) {
		return app_pdf('warranty_period', module_dir_path(WAREHOUSE_MODULE_NAME, 'libraries/pdf/Warranty_period_pdf.php'), $warranty_period);
	}

	/**
	 * get warranty period data
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function get_warranty_period_data($data)
	{

		$this->db->select('goods_delivery_id, commodity_code, quantities, unit_price, expiry_date, lot_number, serial_number,guarantee_period, unit_id, commodity_name, customer_code,'.db_prefix() . 'goods_delivery.customer_code as customer_code');
		$this->db->join(db_prefix() . 'goods_delivery_detail', '' . db_prefix() . 'goods_delivery_detail.goods_delivery_id = ' . db_prefix() . 'goods_delivery.id', 'left');

		// if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
		// 	$this->db->where($where);
		// }

		$where = [];
		$where[] = 'AND guarantee_period is not null AND guarantee_period != ""';
		if (isset($data['commodity_filter'])) {
			$where_commodity_ft = '';
			foreach ($data['commodity_filter'] as $commodity_id) {
				if ($commodity_id != '') {
					if ($where_commodity_ft == '') {
						$where_commodity_ft .= ' AND ('.db_prefix().'goods_delivery_detail.commodity_code = "' . $commodity_id . '"';
					} else {
						$where_commodity_ft .= ' or '.db_prefix().'goods_delivery_detail.commodity_code = "' . $commodity_id . '"';
					}
				}
			}
			if ($where_commodity_ft != '') {
				$where_commodity_ft .= ')';
				array_push($where, $where_commodity_ft);
			}
		}

		if (isset($data['to_date_filter']) && new_strlen($data['to_date_filter']) > 0) {
			array_push($where, "AND date_format(guarantee_period, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime(to_sql_date($this->ci->input->post('to_date_filter')))) . "'");
		}
		if (isset($data['customer_name_filter'])) {
			$where_customer_ft = '';
			foreach ($data['customer_name_filter'] as $client_id) {
				if ($client_id != '') {
					if ($where_customer_ft == '') {
						$where_customer_ft .= ' AND ('.db_prefix().'goods_delivery.customer_code = "' . $client_id . '"';
					} else {
						$where_customer_ft .= ' or '.db_prefix().'goods_delivery.customer_code = "' . $client_id . '"';
					}
				}
			}
			if ($where_customer_ft != '') {
				$where_customer_ft .= ')';
				array_push($where, $where_customer_ft);
			}
		}
		if (isset($data['status_filter']) && $data['status_filter'] != '' && count($data['status_filter']) > 0) {
			$status_ft = '';

			foreach ($data['status_filter'] as $value) {
				if($value == 1){
					if ($status_ft == '') {
						$status_ft .= " AND ( date_format(guarantee_period, '%Y-%m-%d') > '" . date('Y-m-d', strtotime(date('Y-m-d'))) . "'";
					}else{
						$status_ft .= " OR date_format(guarantee_period, '%Y-%m-%d') > '" . date('Y-m-d', strtotime(date('Y-m-d'))) . "'";
					}
				}elseif($value == 2){
					if ($status_ft == '') {
						$status_ft .= " AND ( date_format(guarantee_period, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime(date('Y-m-d'))) . "'";
					}else{
						$status_ft .= " OR date_format(guarantee_period, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime(date('Y-m-d'))) . "'";
					}
				}
			}
			if ($status_ft != '') {
				$status_ft .= ')';
				array_push($where, $status_ft);
			}
		}

		$where = implode(' ', $where);
		$where = trim($where);
		if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
			if (startsWith($where, 'OR')) {
				$where = substr($where, 2);
			} else {
				$where = substr($where, 3);
			}
		}

		$this->db->where($where);
		$this->db->order_by('guarantee_period', 'desc');
		$goods_delivery = $this->db->get(db_prefix() . 'goods_delivery')->result_array();


		return $goods_delivery;
	}

	/**
	 * notify customer shipment status
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function notify_customer_shipment_status($delivery_id)
	{	
		$delivery = $this->get_goods_delivery($delivery_id);
		if($delivery && is_numeric($delivery->customer_code) && ($delivery->pr_order_id == null || $delivery->pr_order_id == 0)){
			//get primary contact by client id
			$primary_contact_user_id = get_primary_contact_user_id($delivery->customer_code);
			$delivery_status = $delivery->delivery_status;
			$shipment_by_delivery = $this->get_shipment_by_delivery($delivery_id);
		}

		if(isset($primary_contact_user_id) && $primary_contact_user_id && isset($shipment_by_delivery)){
			$contact = $this->clients_model->get_contact($primary_contact_user_id);
			$companyname = get_company_name($delivery->customer_code);
			if($contact){
				$content_html = $this->email_content_from_shipment_status($delivery_status, $companyname, $shipment_by_delivery->shipment_number ,$shipment_by_delivery->shipment_hash);

				$inbox['body'] = _strip_tags($content_html);
				$inbox['body'] = nl2br_save_html($inbox['body']);
				$subject = _l('wh_delivery_status_notification').'['.$shipment_by_delivery->shipment_number.']';

				$this->load->model('emails_model');
				$result = $this->emails_model->send_simple_email($contact->email, $subject, $inbox['body'] );
				if ($result) {
					return true;
				}
				return false;

				$ci = &get_instance();
				$ci->email->initialize();
				$ci->load->library('email');
				$ci->email->clear(true);

				if (new_strlen(get_option('smtp_host_sms_email')) > 0 && new_strlen(get_option('smtp_password_sms_email')) > 0 && new_strlen(get_option('smtp_username_sms_email'))) {

					$ci->email->from(get_option('smtp_email_sms_email'), get_option('companyname'));
				} else {
					$ci->email->from(get_option('smtp_email'), get_option('companyname'));
				}

				$ci->email->to($data['email']);
				$ci->email->message(get_option('email_header') . $inbox['body'] . get_option('email_footer'));
				$ci->email->subject(_strip_tags($subject));

				if ($ci->email->send(true)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * email content from shipment status
	 * @param  [type] $status        
	 * @param  [type] $companyname   
	 * @param  [type] $shipment_code 
	 * @param  [type] $shipment_id   
	 * @return [type]                
	 */
	public function email_content_from_shipment_status($status, $companyname, $shipment_code, $shipment_id)
	{
		$content_html = '';
		$table_font_size = 'font-size:13px;';
		$status_font_size = 'font-size:20px;';

		switch ($status) {

			case 'ready_for_packing':
				$content_html = '';
				$content_html .='<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
				<tbody class="tbody-main" style="'.$table_font_size.'">';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_hello') .' '. $companyname .',</b></td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%">' . _l('wh_the_status_of_your_order') .' '.'<a href="' . site_url('warehouse/warehouse_client/shipment_detail_hash/' . $shipment_id ).'" >' . $shipment_code . '</a>'.' '. _l('wh_has_been_change'). '</td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$status_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_'.$status). '</b></td>';
				$content_html .= '</tr>';
				$content_html.= '</tbody>
				</table>';
			break;

			case 'ready_to_deliver':
				$content_html = '';
				$content_html .='<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
				<tbody class="tbody-main" style="'.$table_font_size.'">';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_hello') .' '. $companyname. ',</b></td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%">' . _l('wh_the_status_of_your_order') .' '.'<a href="' . site_url('warehouse/warehouse_client/shipment_detail_hash/' . $shipment_id ).'" >' . $shipment_code . '</a>'.' '. _l('wh_has_been_change'). '</td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$status_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_'.$status). '</b></td>';
				$content_html .= '</tr>';
				$content_html.= '</tbody>
				</table>';
			break;

			case 'delivery_in_progress':
				$content_html = '';
				$content_html .='<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
				<tbody class="tbody-main" style="'.$table_font_size.'">';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_hello') .' '. $companyname. ',</b></td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%">' . _l('wh_the_status_of_your_order') .' '.'<a href="' . site_url('warehouse/warehouse_client/shipment_detail_hash/' . $shipment_id ).'" >' . $shipment_code . '</a>'.' '. _l('wh_has_been_change'). '</td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$status_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_'.$status). '</b></td>';
				$content_html .= '</tr>';
				$content_html.= '</tbody>
				</table>';
			break;

			case 'delivered':
				$content_html = '';
				$content_html .='<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
				<tbody class="tbody-main" style="'.$table_font_size.'">';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_hello') .' '. $companyname. ',</b></td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%">' . _l('wh_the_status_of_your_order') .' '.'<a href="' . site_url('warehouse/warehouse_client/shipment_detail_hash/' . $shipment_id ).'" >' . $shipment_code . '</a>'.' '. _l('wh_has_been_change'). '</td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$status_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_'.$status). '</b></td>';
				$content_html .= '</tr>';
				$content_html.= '</tbody>
				</table>';
			break;

			case 'received':
				$content_html = '';
				$content_html .='<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
				<tbody class="tbody-main" style="'.$table_font_size.'">';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_hello') .' '. $companyname. ',</b></td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%">' . _l('wh_the_status_of_your_order') .' '.'<a href="' . site_url('warehouse/warehouse_client/shipment_detail_hash/' . $shipment_id ).'" >' . $shipment_code . '</a>'.' '. _l('wh_has_been_change'). '</td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$status_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_'.$status). '</b></td>';
				$content_html .= '</tr>';
				$content_html.= '</tbody>
				</table>';
			break;

			case 'returned':
				$content_html = '';
				$content_html .='<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
				<tbody class="tbody-main" style="'.$table_font_size.'">';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_hello') .' '. $companyname. ',</b></td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%">' . _l('wh_the_status_of_your_order') .' '.'<a href="' . site_url('warehouse/warehouse_client/shipment_detail_hash/' . $shipment_id ).'" >' . $shipment_code . '</a>'.' '. _l('wh_has_been_change'). '</td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$status_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_'.$status). '</b></td>';
				$content_html .= '</tr>';
				$content_html.= '</tbody>
				</table>';
			break;

			case 'not_delivered':
				$content_html = '';
				$content_html .='<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
				<tbody class="tbody-main" style="'.$table_font_size.'">';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_hello') .' '. $companyname. ',</b></td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%">' . _l('wh_the_status_of_your_order') .' '.'<a href="' . site_url('warehouse/warehouse_client/shipment_detail_hash/' . $shipment_id ).'" >' . $shipment_code . '</a>'.' '. _l('wh_has_been_change'). '</td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$status_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_'.$status). '</b></td>';
				$content_html .= '</tr>';
				$content_html.= '</tbody>
				</table>';
			break;

			default:
				$content_html = '';
				$content_html .='<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
				<tbody class="tbody-main" style="'.$table_font_size.'">';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_hello') .' '. $companyname. ',</b></td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$table_font_size.'">';
				$content_html .= '<td align="left" width="100%">' . _l('wh_the_status_of_your_order') .' '.'<a href="' . site_url('warehouse/warehouse_client/shipment_detail_hash/' . $shipment_id ).'" >' . $shipment_code . '</a>'.' '. _l('wh_has_been_change'). '</td>';
				$content_html .= '</tr>';

				$content_html .= '<tr style="'.$status_font_size.'">';
				$content_html .= '<td align="left" width="100%"><b>' . _l('wh_'.$status). '</b></td>';
				$content_html .= '</tr>';
				$content_html.= '</tbody>
				</table>';
			break;
		}
		return $content_html;
	}

	/**
	 * get shipment by hash
	 * @param  [type] $hash 
	 * @return [type]       
	 */
	public function get_shipment_by_hash($hash)
	{
		$this->db->where('shipment_hash', $hash);
		return $this->db->get(db_prefix() . 'wh_omni_shipments')->row();
	}

	/**
	 * delivery note rollback serial number
	 * @param  [type] $commodity_id        
	 * @param  [type] $warehouse_id        
	 * @param  [type] $inventory_manage_id 
	 * @param  [type] $str_serial_number   
	 * @return [type]                      
	 */
	public function delivery_note_rollback_serial_number($commodity_id, $warehouse_id, $quantity, $serial_number)
	{
		//TODO kieemr tra: revert số lượng tồn kho tăng 2
		if(new_strlen($serial_number) > 0){

			$this->db->where('commodity_id', $commodity_id);
			$this->db->where('warehouse_id', $warehouse_id);
			$this->db->where('serial_number', $serial_number);
			$this->db->where('is_used', 'yes');
			$this->db->limit($quantity);

			$this->db->update(db_prefix() . 'wh_inventory_serial_numbers', ['is_used' => 'no']);
			if ($this->db->affected_rows() > 0) {
				return true;
			}

			return false;
		}
		return false;
	}

	/**
	 * get quantity inventory array
	 * @param  [type] $warehouse_id 
	 * @param  [type] $commodity_id 
	 * @return [type]               
	 */
	public function get_quantity_inventory_array($warehouse_id, $commodity_id) {

		$sql = 'SELECT warehouse_id, commodity_id, sum(inventory_number) as inventory_number from ' . db_prefix() . 'inventory_manage where warehouse_id = ' . $warehouse_id . ' AND commodity_id = ' . $commodity_id .' group by warehouse_id, commodity_id';
		$result = $this->db->query($sql)->result_array();
		//if > 0 update, else insert
		return $result;

	}

	/**
	 * get serial number available
	 * @return [type] 
	 */
	public function get_serial_number_available()
	{
		$arr_serial_numbers = [];
		$this->db->select('serial_number');
		$this->db->where('is_used', 'no');
		$serial_numbers = $this->db->get(db_prefix().'wh_inventory_serial_numbers')->result_array();
		foreach ($serial_numbers as $value) {
		    $arr_serial_numbers[] = $value['serial_number'];
		}
		return $arr_serial_numbers;
	}

	/**
	 * update fee for return order
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function update_fee_for_return_order($data)
	{

		$this->db->where('name','wh_fee_for_return_order');
		$this->db->update(db_prefix() . 'options', [
			'value' => $data['wh_fee_for_return_order'],
		]);
		if ($this->db->affected_rows() > 0) {
			return true;
		}else{
			return false;
		}
	}

	/**
	 * update wh on total items
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function update_wh_on_total_items($data)
	{

		$this->db->where('name','wh_on_total_items');
		$this->db->update(db_prefix() . 'options', [
			'value' => $data['wh_on_total_items'],
		]);
		if ($this->db->affected_rows() > 0) {
			return true;
		}else{
			return false;
		}
	}

	/**
	 * get_shipment_log_attachments
	 * @param  [type] $log_id 
	 * @return [type]         
	 */
	public function get_shipment_log_attachments($log_id){

		$this->db->order_by('dateadded', 'desc');
		$this->db->where('rel_id', $log_id);
		$this->db->where('rel_type', 'shipment_image');

		return $this->db->get(db_prefix() . 'files')->result_array();

	}

	/**
	 * import commodity variations
	 * @param  [type] $data          
	 * @param  [type] $arr_parent_id 
	 * @return [type]                
	 */
	public function import_commodity_variations($data, $arr_parent_id)
	{

		$arr_parent_id = array_unique($arr_parent_id);
		$affectedRows = 0;
		$parent_data = [];
		$arr_variation_data = [];

		/*get list parent data*/
		$this->db->where('id IN ('.implode(',', $arr_parent_id).')');
		$list_parent = $this->db->get(db_prefix().'items')->result_array();
		foreach ($list_parent as $key => $parent) {
			$parent_data[$parent['id']] = $parent;
		}

		//get last product id
    	$sql_where = 'SELECT * FROM ' . db_prefix() . 'items order by id desc limit 1';
    	$res = $this->db->query($sql_where)->row();
    	$last_commodity_id = 0;
    	if (isset($res)) {
    		$last_commodity_id = $this->db->query($sql_where)->row()->id;
    	}
    	$next_commodity_id = (int) $last_commodity_id + 1;


		foreach ($data as $key => $value) {
		    if(isset($parent_data[$value['parent_id']])){
		    	$temp_data = $parent_data[$value['parent_id']];
		    	$description = $parent_data[$value['parent_id']]['description'];

		    	if(isset($temp_data['id'])){
		    		unset($temp_data['id']);
		    	}

		    	$str_variant='';
		    	$variant = json_decode($value['attributes']);

		    	if(count($variant) > 0){
		    		foreach ($variant as $value_option) {
		    			if(new_strlen($str_variant) == 0){
		    				$str_variant .= $value_option->option;
		    			}else{
		    				$str_variant .= '-'.$value_option->option;
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
		    	$barcode_gen = $this->generate_commodity_barcode();

		    	//create sku code
		    	$sku_code = str_pad($next_commodity_id,5,'0',STR_PAD_LEFT);

		    	$next_commodity_id++; 
		    	$temp_data['commodity_code'] = $sku_code;
		    	$temp_data['sku_code'] = $sku_code;

		    	$temp_data['commodity_barcode'] = $barcode_gen;
		    	$temp_data['commodity_code'] = $sku_code;
		    	$temp_data['sku_code'] = $sku_code;
		    	$temp_data['parent_id'] = $value['parent_id'];
		    	$temp_data['parent_attributes'] = null;
		    	$temp_data['attributes'] = $value['attributes'];

		    	$temp_data['description'] = $description.' '. $str_variant;

		    	$arr_variation_data[] = $temp_data;

		    }
		}

		if(count($arr_variation_data) > 0){
			/*insert batch*/
			$affected_rows = $this->db->insert_batch(db_prefix().'items', $arr_variation_data);
			if($affected_rows > 0){
				$affectedRows++;
			}

			/*copy attachement to variation product*/
			foreach ($arr_parent_id as $parent_id) {
				$this->copy_product_image($parent_id);
			}
		}

		return $affectedRows;
	}

	/**
	 * delete warehouse permission
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_warehouse_permission($id)
	{
		$str_permissions ='';
		foreach (list_warehouse_permisstion() as $per_key =>  $per_value) {
			if(new_strlen($str_permissions) > 0){
				$str_permissions .= ",'".$per_value."'";
			}else{
				$str_permissions .= "'".$per_value."'";
			}
		}

		$sql_where = " feature IN (".$str_permissions.") ";

		$this->db->where('staff_id', $id);
		$this->db->where($sql_where);
		$this->db->delete(db_prefix() . 'staff_permissions');

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return false;
	}

	/**
	 * get pur order created receipt
	 * @param  [type] $PARAMETERS 
	 * @return [type]             
	 */
	public function get_pur_order_created_receipt()
	{
		$pur_order_ids = [];
		$sql_where = 'SELECT pur_order, SUM(CAST(wh_quantity_received as DECIMAL(15,2))) as total_quantity_received, SUM(quantity) as total_quantity FROM '.db_prefix().'pur_order_detail GROUP BY pur_order';
		$pur_order = $this->db->query($sql_where)->result_array();

		foreach ($pur_order as $value) {
			if(!is_null($value['total_quantity_received']) && $value['total_quantity_received'] >= $value['total_quantity']){
				$pur_order_ids[] = $value['pur_order'];
			}
		}

		return $pur_order_ids;
	}

	/**
	 * wh assign warehouse to staffs
	 * @param  [type] $warehouseId 
	 * @param  [type] $staffIds    
	 * @return [type]              
	 */
	public function assignWarehouseToStaffs($warehouseId, $staffIds)
	{
		$affected_rows = 0;
		$this->db->where('warehouse_id', $warehouseId);
		$this->db->delete(db_prefix().'wh_staff_warehouses');

		if(count($staffIds) > 0){
			$arrStaffWarehouse = [];
			foreach ($staffIds as $staffId) {
			    $arrStaffWarehouse[] = [
			    	'staff_id' => $staffId,
			    	'warehouse_id' => $warehouseId,
			    ];
			}

			$affected_rows = $this->db->insert_batch(db_prefix().'wh_staff_warehouses', $arrStaffWarehouse);
			if($affected_rows > 0){
				$affectedRows++;
			}
		}
		if($affectedRows > 0){
			return true;
		}
		return false;
	}

	/**
	 * getStaffAssignedToWarehouse
	 * @param  string $warehouseId 
	 * @return [type]              
	 */
	public function getStaffAssignedToWarehouseHtml($warehouseId='0', $return_staff_ids = false)
	{
		$arrStaffIds = [];
		$staff_infors = [];
		$staff_ids = [];

		$this->db->where('warehouse_id', $warehouseId);
		$staffWarehouses = $this->db->get(db_prefix().'wh_staff_warehouses')->result_array();
		foreach ($staffWarehouses as $staffWarehouse) {
		    $arrStaffIds[] = $staffWarehouse['staff_id'];
		}

		$staffs = $this->get_staff();

		$model_options ='';
		foreach ($staffs as $staff) {

			$select='';
			if(in_array($staff['staffid'], $arrStaffIds)){
				$select .= 'selected';

				$staff_infors[] = [
					'staffid' => $staff['staffid'],
					'full_name' => $staff['full_name'],
				];
				$staff_ids[] = $staff['staffid'];
				
			}

			$model_options .= '<option value="' . $staff['staffid'] . '" '.$select.'>' . $staff['full_name'] . '</option>';
		}

		if($return_staff_ids){
			return ['staff_ids' => $staff_ids, 'staff_infors' => $staff_infors];
		}

		return $model_options;
	}

	/**
	 * getWarehouseByStaff
	 * @param  [type] $staffId 
	 * @return [type]          
	 */
	public function getWarehouseByStaff($staffId = '')
	{
		if(!is_numeric($staffId)){
			$staffId = get_staff_user_id();
		}

		$arrWarehouses = [];
		$arrWarehouses[] = 0;
		if(is_admin()){
			return true;
		}

		$this->db->where('staff_id', $staffId);
		$staffWarehouses = $this->db->get(db_prefix().'wh_staff_warehouses')->result_array();

		foreach ($staffWarehouses as $staffWarehouse) {
			$arrWarehouses[] = $staffWarehouse['warehouse_id'];
		}
		return $arrWarehouses;
	}

	/**
	 * get serial number detail data
	 * @return [type] 
	 */
	public function get_serial_number_detail_data($id)
	{
		$arr_serial_number_data = [];
		$item_id;
		$serial_number_str = '';
		$this->db->where('id', $id);
		$serial_number = $this->db->get(db_prefix().'wh_inventory_serial_numbers')->row();
		if($serial_number){
			$item_id = $serial_number->commodity_id;
			$serial_number_str = $serial_number->serial_number;
			// get receipt note
			$this->db->where('commodity_code', $serial_number->commodity_id);
			$this->db->where('(find_in_set("'.$serial_number->serial_number.'", '.db_prefix().'goods_receipt_detail.serial_number)) ');
			$this->db->join(db_prefix() . 'goods_receipt_detail', '' . db_prefix() . 'goods_receipt.id = ' . db_prefix() . 'goods_receipt_detail.goods_receipt_id', 'left');
			$goods_receipts = $this->db->get(db_prefix().'goods_receipt')->result_array();
			
			// get delivery note
			$this->db->where('commodity_code', $serial_number->commodity_id);
			$this->db->where('(find_in_set("'.$serial_number->serial_number.'", '.db_prefix().'goods_delivery_detail.serial_number)) ');
			$this->db->join(db_prefix() . 'goods_delivery_detail', '' . db_prefix() . 'goods_delivery.id = ' . db_prefix() . 'goods_delivery_detail.goods_delivery_id', 'left');
			$goods_deliveries = $this->db->get(db_prefix().'goods_delivery')->result_array();
			
			// packing list
			$this->db->where('commodity_code', $serial_number->commodity_id);
			$this->db->where('(find_in_set("'.$serial_number->serial_number.'", '.db_prefix().'wh_packing_list_details.serial_number)) ');
			$this->db->join(db_prefix() . 'wh_packing_list_details', '' . db_prefix() . 'wh_packing_lists.id = ' . db_prefix() . 'wh_packing_list_details.packing_list_id', 'left');
			$wh_packing_lists = $this->db->get(db_prefix().'wh_packing_lists')->result_array();

			// loss adjustment
			$this->db->where('items', $serial_number->commodity_id);
			$this->db->where('(find_in_set("'.$serial_number->serial_number.'", '.db_prefix().'wh_loss_adjustment_detail.serial_number)) ');
			$this->db->join(db_prefix() . 'wh_loss_adjustment_detail', '' . db_prefix() . 'wh_loss_adjustment.id = ' . db_prefix() . 'wh_loss_adjustment_detail.loss_adjustment', 'left');
			$wh_loss_adjustments = $this->db->get(db_prefix().'wh_loss_adjustment')->result_array();

			// internal delivery
			$this->db->where('commodity_code', $serial_number->commodity_id);
			$this->db->where('(find_in_set("'.$serial_number->serial_number.'", '.db_prefix().'internal_delivery_note_detail.serial_number)) ');
			$this->db->join(db_prefix() . 'internal_delivery_note_detail', '' . db_prefix() . 'internal_delivery_note.id = ' . db_prefix() . 'internal_delivery_note_detail.internal_delivery_id', 'left');
			$internal_delivery_notes = $this->db->get(db_prefix().'internal_delivery_note')->result_array();

			if(count($goods_receipts) > 0){
				foreach ($goods_receipts as $goods_receipt) {
				    $arr_serial_number_data[] = [
				    	'rel_id' => $goods_receipt['goods_receipt_id'],
				    	'rel_type' => 'receipt_note',
				    	'name' => $goods_receipt['goods_receipt_code'],
				    	'date_created' => $goods_receipt['date_c'],
				    ];
				}
			}
			if(count($wh_loss_adjustments) > 0){
				foreach ($wh_loss_adjustments as $wh_loss_adjustment) {
					$arr_serial_number_data[] = [
						'rel_id' => $wh_loss_adjustment['loss_adjustment'],
						'rel_type' => 'loss_adjustment',
						'name' => "LA#".$wh_loss_adjustment['loss_adjustment'],
						'date_created' => $wh_loss_adjustment['date_create'],
					];
				}
			}
			if(count($internal_delivery_notes) > 0){
				foreach ($internal_delivery_notes as $internal_delivery_note) {
					$arr_serial_number_data[] = [
						'rel_id' => $internal_delivery_note['internal_delivery_id'],
						'rel_type' => 'internal_delivery',
						'name' => $internal_delivery_note['internal_delivery_code'],
						'date_created' => $internal_delivery_note['datecreated'],
					];
				}
			}
			if(count($goods_deliveries) > 0){
				foreach ($goods_deliveries as $goods_delivery) {
				    $arr_serial_number_data[] = [
				    	'rel_id' => $goods_delivery['goods_delivery_id'],
				    	'rel_type' => 'delivery_note',
				    	'name' => $goods_delivery['goods_delivery_code'],
				    	'date_created' => $goods_delivery['date_c'],
				    ];
				}
			}
			if(count($wh_packing_lists) > 0){
				foreach ($wh_packing_lists as $wh_packing_list) {
				    $arr_serial_number_data[] = [
				    	'rel_id' => $wh_packing_list['packing_list_id'],
				    	'rel_type' => 'packing_list',
				    	'name' => $wh_packing_list['packing_list_number'],
				    	'date_created' => $wh_packing_list['datecreated'],
				    ];
				}
			}


		}
		return ['serial_number' => $serial_number_str, 'item_id' => $item_id, 'serial_number_data' => $arr_serial_number_data];
	}

	/**
	 * create lot number
	 * @return [type] 
	 */
	public function create_lot_number() {
		if(get_option('auto_generate_lotnumber') == 0){
			return null;
		}
		$str_result ='';

		$prefix_str ='';
		$prefix_str .= get_option('lot_number_prefix');
		$next_number = (int) get_option('next_lot_number');
		$str_result .= $prefix_str.'-'.date('m').date('y').'-'.str_pad($next_number,5,'0',STR_PAD_LEFT);

		/*update next number setting*/
		$this->update_inventory_setting(['next_lot_number' =>  get_option('next_lot_number')+1]);

		return $str_result;
	}

	/**
	 * get goods transaction detail
	 * @param  [type] $goods_receipt_id 
	 * @param  [type] $goods_id         
	 * @return [type]                   
	 */
	public function get_goods_transaction_detail($goods_receipt_id, $goods_id) {
		$this->db->where('goods_receipt_id', $goods_receipt_id);
		$this->db->where('goods_id', $goods_id);
		$goods_receipt_detail = $this->db->get(db_prefix() . 'goods_transaction_detail')->row();
		if($goods_receipt_detail){
			return $goods_receipt_detail;
		}
		return false;
	}

}
