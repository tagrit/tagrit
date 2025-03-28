<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Class Manufacturing
 */
class Manufacturing extends AdminController
{
	/**
	 * __construct
	 */
	public function __construct()
	{
	  parent::__construct();
	  $this->load->model('manufacturing_model');
	  hooks()->do_action('manufacturing_init');

	}

	/**
	 * setting
	 * @return [type] 
	 */
	public function setting()
	{
		if (!has_permission('manufacturing', '', 'edit') && !is_admin() && !has_permission('manufacturing', '', 'create')) {
			access_denied('manufacturing');
		}

		$data['group'] = $this->input->get('group');
		$data['title'] = _l('setting');

		$data['tab'][] = 'working_hour';
		$data['tab'][] = 'unit_of_measure_categories';
		$data['tab'][] = 'unit_of_measure';

		$data['tab'][] = 'prefix_number';

		if ($data['group'] == '') {
			$data['group'] = 'working_hour';
			$data['working_hours'] = $this->manufacturing_model->get_routings();
		}elseif ($data['group'] == 'working_hour') {
			$data['working_hours'] = $this->manufacturing_model->get_routings();
		}

		if($data['group'] == 'unit_of_measure_categories'){
			$data['tabs']['view'] = 'settings/unit_of_measure_categories/' . $data['group'];
			$data['categories']	= $this->manufacturing_model->get_unit_categories();
		}elseif($data['group'] == 'unit_of_measure'){
			$data['tabs']['view'] = 'settings/unit_of_measure/' . $data['group'];
		}else{

			$data['tabs']['view'] = 'settings/' . $data['group'];
		}

		$required_inventory_purchase = mrp_required_inventory_purchase_module();
		//required inventory purchase
		if($required_inventory_purchase['inventory'] == false || $required_inventory_purchase['purchase'] == false){
			$this->load->view('manufacturing/settings/required_inventory_module', $data);
		}else{
			$this->load->view('settings/manage_setting', $data);
		}
	}


	/**
	 * work center manage
	 * @return [type] 
	 */
	public function work_center_manage()
	{
	    if (!has_permission('manufacturing', '', 'view') ) {
			access_denied('work_center');
		}

		$data['title'] = _l('mrp_work_centers');
		
		$required_inventory_purchase = mrp_required_inventory_purchase_module();
		//required inventory purchase
		if($required_inventory_purchase['inventory'] == false || $required_inventory_purchase['purchase'] == false){
			$this->load->view('manufacturing/settings/required_inventory_module', $data);
		}else{
			$this->load->view('manufacturing/work_centers/work_center_manage', $data);
		}
	}

	/**
	 * work center table
	 * @return [type] 
	 */
	public function work_center_table()
	{
			$this->app->get_table_data(module_views_path('manufacturing', 'work_centers/work_center_table'));
	}

	/**
	 * work center modal
	 * @return [type] 
	 */
	public function work_center_modal()
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$this->load->model('staff_model');

		$data=[];
		if ($this->input->post('slug') === 'update') {
			$id = $this->input->post('id');
			$data['work_center'] = $this->manufacturing_model->get_work_centers($id);
		}
		$this->load->view('settings/work_center_modal', $data);
	}


	/**
	 * add edit work center
	 * @param string $id 
	 */
	public function add_edit_work_center($id = '')
	{
		if (!has_permission('manufacturing', '', 'view')  && !is_admin()) {
			access_denied('work_center');
		}
		
		if ($this->input->post()) {
			$data = $this->input->post();
			$data['description']     = $this->input->post('description', false);
			
			if ($id == '') {
				if (!has_permission('manufacturing', '', 'create') && !is_admin()) {
					access_denied('work_center');
				}

				$id = $this->manufacturing_model->add_work_center($data);
				if ($id) {
					$success = true;
					$message = _l('mrp_added_successfully', _l('work_center'));
				}

				if ($id) {
					set_alert('success', _l('mrp_added_successfully', _l('contract')));
					redirect(admin_url('manufacturing/work_center_manage'));
				}

			} else {
				if (!has_permission('manufacturing', '', 'edit') && !is_admin()) {
					access_denied('work_center');
				}

				$response = $this->manufacturing_model->update_work_center($data, $id);

				if (is_array($response)) {
					if (isset($response['cant_remove_main_admin'])) {
						set_alert('warning', _l('staff_cant_remove_main_admin'));
					} elseif (isset($response['cant_remove_yourself_from_admin'])) {
						set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
					}
				} elseif ($response == true) {
					set_alert('success', _l('mrp_updated_successfully', _l('contract')));
				}
				redirect(admin_url('manufacturing/work_center_manage'));
			}
		}
		
		$data=[];
		if ($id != ''){
			$data['work_center'] = $this->manufacturing_model->get_work_centers($id);
		}
		$data['working_hours'] = $this->manufacturing_model->get_working_hours();

		$this->load->view('manufacturing/work_centers/add_edit_work_center', $data);
	}


	/**
	 * view work center
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function view_work_center($id)
	{
		if (!has_permission('manufacturing', '', 'view')  && !is_admin()) {
			access_denied('work_center');
		}

	    $work_center = $this->manufacturing_model->get_work_centers($id);

		if (!$work_center) {
			blank_page('Work Center Not Found', 'danger');
		}

		$data['work_center'] = $work_center;
		$this->load->view('manufacturing/work_centers/view_work_center', $data);
	}


	/**
	 * delete work center
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_work_center($id)
	{
	    if (!has_permission('manufacturing', '', 'delete')  && !is_admin()) {
			access_denied('work_center');
		}

		$success = $this->manufacturing_model->delete_work_center($id);
		if ($success) {
			set_alert('success', _l('mrp_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('manufacturing/work_center_manage'));

	}

	/**
	 * working hour table
	 * @return [type] 
	 */
	public function working_hour_table()
	{
		$this->app->get_table_data(module_views_path('manufacturing', 'settings/working_hour_table'));
	}


	/**
	 * add edit working hour
	 * @param string $id 
	 */
	public function add_edit_working_hour($id = '')
	{
		if (!has_permission('manufacturing', '', 'view')  && !is_admin()) {
			access_denied('working_hour');
		}
		
		if ($this->input->post()) {
			$data = $this->input->post();

			if ($id == '') {
				if (!has_permission('manufacturing', '', 'create') && !is_admin()) {
					access_denied('working_hour');
				}

				$id = $this->manufacturing_model->add_working_hour($data);
				if ($id) {
					set_alert('success', _l('mrp_added_successfully', _l('working_hour')));
					redirect(admin_url('manufacturing/setting?group=working_hour'));
				}

			} else {
				if (!has_permission('manufacturing', '', 'edit') && !is_admin()) {
					access_denied('working_hour');
				}

				$response = $this->manufacturing_model->update_working_hour($data, $id);

				if (is_array($response)) {
					if (isset($response['cant_remove_main_admin'])) {
						set_alert('warning', _l('staff_cant_remove_main_admin'));
					} elseif (isset($response['cant_remove_yourself_from_admin'])) {
						set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
					}
				} elseif ($response == true) {
					set_alert('success', _l('mrp_updated_successfully', _l('working_hour')));
				}
				redirect(admin_url('manufacturing/setting?group=working_hour'));
			}
		}
		
		$data=[];
		if ($id != ''){
			$working_hour = $this->manufacturing_model->get_working_hour($id);
			$data['working_hour'] = $working_hour['working_hour'];
			$data['working_hour_details'] = $working_hour['working_hour_details'];
			$data['time_off'] = $working_hour['time_off'];
			
		}

		$day_period_type =[];
		$day_period_type[] = [
			'id' => 'morning',
			'label' => _l('morning'),
		];
		$day_period_type[] = [
			'id' => 'afternoon',
			'label' => _l('afternoon'),
		];

		$day_of_week_types=[];
		foreach (mrp_date_of_week() as $key => $value) {
		    array_push($day_of_week_types, [
		    	'id' => $key,
		    	'label' => _l($value),
		    ]);
		}

		$data['day_of_week_types'] = $day_of_week_types;
		$data['day_period_type'] = $day_period_type;
		$data['working_hour_sample_data'] = working_hour_sample_data();
		
		$this->load->view('manufacturing/settings/add_edit_working_hour', $data);
	}


	/**
	 * delete working hour
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_working_hour($id)
	{
	    if (!has_permission('manufacturing', '', 'delete')  && !is_admin()) {
			access_denied('work_center');
		}

		$success = $this->manufacturing_model->delete_working_hour($id);
		if ($success) {
			set_alert('success', _l('mrp_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('manufacturing/setting?group=working_hour'));
	}


	/*Routings*/

	/**
	 * routing manage
	 * @return [type] 
	 */
	public function routing_manage()
	{
	    if (!has_permission('manufacturing', '', 'view') ) {
			access_denied('work_center');
		}

		$data['title'] = _l('routing');
		
		$required_inventory_purchase = mrp_required_inventory_purchase_module();
		//required inventory purchase
		if($required_inventory_purchase['inventory'] == false || $required_inventory_purchase['purchase'] == false){
			$this->load->view('manufacturing/settings/required_inventory_module', $data);
		}else{
			$this->load->view('manufacturing/routings/routing_manage', $data);
		}
	}

	/**
	 * routing table
	 * @return [type] 
	 */
	public function routing_table()
	{
			$this->app->get_table_data(module_views_path('manufacturing', 'routings/routing_table'));
	}

	/**
	 * add routing modal
	 */
	public function routing_modal()
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$data=[];
		$data['routing_code'] = $this->manufacturing_model->create_code('routing_code');
		
		$this->load->view('routings/add_routing_modal', $data);
	}


	/**
	 * add routing modal
	 * @param string $id 
	 */
	public function add_routing_modal($id='')
	{

		if (!has_permission('manufacturing', '', 'view')  && !is_admin()) {
			access_denied('routing');
		}
		
		if ($this->input->post()) {
			$data = $this->input->post();
			$data['description']     = $this->input->post('description', false);

			if ($id == '') {
				if (!has_permission('manufacturing', '', 'create') && !is_admin()) {
					access_denied('routing');
				}

				$id = $this->manufacturing_model->add_routing($data);
				if ($id) {
					set_alert('success', _l('mrp_added_successfully', _l('routing')));
					redirect(admin_url('manufacturing/operation_manage/'.$id));
				}

			} else {
				if (!has_permission('manufacturing', '', 'edit') && !is_admin()) {
					access_denied('routing');
				}

				$response = $this->manufacturing_model->update_routing($data, $id);

				if (is_array($response)) {
					if (isset($response['cant_remove_main_admin'])) {
						set_alert('warning', _l('staff_cant_remove_main_admin'));
					} elseif (isset($response['cant_remove_yourself_from_admin'])) {
						set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
					}
				} elseif ($response == true) {
					set_alert('success', _l('mrp_updated_successfully', _l('routing')));
				}
				redirect(admin_url('manufacturing/operation_manage/'.$id));
			}
		}
		
		$data=[];
		if ($id != ''){
			$working_hour = $this->manufacturing_model->get_working_hour($id);
			$data['working_hour'] = $working_hour['working_hour'];
			$data['working_hour_details'] = $working_hour['working_hour_details'];
			$data['time_off'] = $working_hour['time_off'];
			
		}
		

		$day_period_type =[];
		$day_period_type[] = [
			'id' => 'morning',
			'label' => _l('morning'),
		];
		$day_period_type[] = [
			'id' => 'afternoon',
			'label' => _l('afternoon'),
		];

		$day_of_week_types=[];
		foreach (mrp_date_of_week() as $key => $value) {
			array_push($day_of_week_types, [
				'id' => $key,
				'label' => $value,
			]);
		}

		$data['day_of_week_types'] = $day_of_week_types;
		$data['day_period_type'] = $day_period_type;
		
		$this->load->view('manufacturing/settings/add_edit_working_hour', $data);
	}

	/**
	 * delete routing
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_routing($id)
	{
	    if (!has_permission('manufacturing', '', 'delete')  && !is_admin()) {
			access_denied('routing');
		}

		$success = $this->manufacturing_model->delete_routing($id);
		if ($success) {
			set_alert('success', _l('mrp_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('manufacturing/routing_manage'));
	}


	/**
	 * operation manage
	 * @return [type] 
	 */
	public function operation_manage($id='')
	{
	    if (!has_permission('manufacturing', '', 'view') ) {
			access_denied('work_center');
		}

		$data['title'] = _l('operation');
		if($id != ''){
			$data['routing'] = $this->manufacturing_model->get_routings($id);
		}
		
		$this->load->view('manufacturing/routings/routing_details/operation_manage', $data);
	}


	/**
	 * operation table
	 * @return [type] 
	 */
	public function operation_table()
	{
			$this->app->get_table_data(module_views_path('manufacturing', 'routings/routing_details/operation_table'));
	}


	/**
	 * operation_modal
	 * @return [type] 
	 */
	public function operation_modal()
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$data=[];
		$data = $this->input->post();
		if($data['operation_id'] != 0){
			$data['operation'] = $this->manufacturing_model->get_operation($data['operation_id']);
			$data['operation_attachment'] = $this->manufacturing_model->mrp_get_attachments_file($data['operation_id'], 'mrp_operation');
		}

		$data['work_centers'] = $this->manufacturing_model->get_work_centers();
		$this->load->view('routings/routing_details/add_edit_operation_modal', $data);
	}


	/**
	 * add edit operation
	 * @param [type] $operation_id 
	 */
	public function add_edit_operation($id='')
	{
	    if (!has_permission('manufacturing', '', 'view')  && !is_admin()) {
			access_denied('operation');
		}
		
		if ($this->input->post()) {
			$data = $this->input->post();
			$data['description']     = $this->input->post('description', false);
			$routing_id = $data['routing_id'];

			if ($id == '') {
				if (!has_permission('manufacturing', '', 'create') && !is_admin()) {
					access_denied('operation');
				}

				$id = $this->manufacturing_model->add_operation($data);
				if ($id) {
					$uploadedFiles = handle_mrp_operation_attachments_array($id,'file');

					set_alert('success', _l('mrp_added_successfully', _l('operation')));
					redirect(admin_url('manufacturing/operation_manage/'.$routing_id));
				}

			} else {
				if (!has_permission('manufacturing', '', 'edit') && !is_admin()) {
					access_denied('operation');
				}

				$response = $this->manufacturing_model->update_operation($data, $id);

				$uploadedFiles = handle_mrp_operation_attachments_array($id,'file');

				if (is_array($response)) {
					if (isset($response['cant_remove_main_admin'])) {
						set_alert('warning', _l('staff_cant_remove_main_admin'));
					} elseif (isset($response['cant_remove_yourself_from_admin'])) {
						set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
					}
				} elseif ($response == true) {
					set_alert('success', _l('mrp_updated_successfully', _l('operation')));
				}
				redirect(admin_url('manufacturing/operation_manage/'.$routing_id));
			
			}
		}

	}


	/**
	 * delete operation
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_operation($id, $routing_id)
	{
	    if (!has_permission('manufacturing', '', 'delete')  && !is_admin()) {
			access_denied('work_center');
		}

		$success = $this->manufacturing_model->delete_operation($id);
		if ($success) {
			set_alert('success', _l('mrp_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('manufacturing/operation_manage/'.$routing_id));


	}

	/**
	 * mrp view attachment file
	 * @param  [type] $id     
	 * @param  [type] $rel_id 
	 * @return [type]         
	 */
	public function mrp_view_attachment_file($id, $rel_id, $rel_type)
	{
		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
		$data['current_user_is_admin']             = is_admin();
		$data['file'] = $this->misc_model->get_file($id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}

		switch ($rel_type) {
			case 'operation':
				$folder_link = 'manufacturing/routings/routing_details/view_operation_file';
				break;
			
			default:
				# code...
				break;
		}

		$this->load->view($folder_link, $data);
	}


	/**
	 * delete operation attachment file
	 * @param  [type] $attachment_id 
	 * @return [type]                
	 */
	public function delete_operation_attachment_file($attachment_id)
	{
		if (!has_permission('manufacturing', '', 'delete') && !is_admin()) {
			access_denied('operation');
		}

		echo json_encode([
			'success' => $this->manufacturing_model->delete_mrp_attachment_file($attachment_id, MANUFACTURING_OPERATION_ATTACHMENTS_UPLOAD_FOLDER),
		]);
	}


	/**
	 * add edit category
	 * @param string $id 
	 */
	public function add_edit_category($id='')
	{

		if (!has_permission('manufacturing', '', 'view')  && !is_admin()) {
			access_denied('unit_of_measure_categories');
		}
		
		if ($this->input->post()) {
			$data = $this->input->post();
			if(isset($data['id'])){
				$id = $data['id'];
			}

			if ($id == '') {
				if (!has_permission('manufacturing', '', 'create') && !is_admin()) {
					access_denied('unit_of_measure_categories');
				}

				$id = $this->manufacturing_model->add_unit_categories($data);
				if ($id) {
					set_alert('success', _l('mrp_added_successfully', _l('unit_of_measure_categories')));
					redirect(admin_url('manufacturing/setting?group=unit_of_measure_categories'));
				}

			} else {
				if (!has_permission('manufacturing', '', 'edit') && !is_admin()) {
					access_denied('unit_of_measure_categories');
				}

				$response = $this->manufacturing_model->update_unit_categories($data, $id);

				if (is_array($response)) {
					if (isset($response['cant_remove_main_admin'])) {
						set_alert('warning', _l('staff_cant_remove_main_admin'));
					} elseif (isset($response['cant_remove_yourself_from_admin'])) {
						set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
					}
				} elseif ($response == true) {
					set_alert('success', _l('mrp_updated_successfully', _l('unit_of_measure_categories')));
				}
				redirect(admin_url('manufacturing/setting?group=unit_of_measure_categories'));
			}
		}

	}

	/**
	 * delete category
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_category($id)
	{
	    if (!has_permission('manufacturing', '', 'delete')  && !is_admin()) {
			access_denied('unit_of_measure_categories');
		}

		$success = $this->manufacturing_model->delete_unit_categories($id);
		if ($success) {
			set_alert('success', _l('mrp_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('manufacturing/setting?group=unit_of_measure_categories'));
	}

	/**
	 * unit of measure table
	 * @return [type] 
	 */
	public function unit_of_measure_table()
	{
		$this->app->get_table_data(module_views_path('manufacturing', 'settings/unit_of_measure/unit_of_measure_table'));
	}


	/**
	 * add edit unit of measure
	 * @param string $id 
	 */
	public function add_edit_unit_of_measure($id = '')
	{
		if (!has_permission('manufacturing', '', 'view')  && !is_admin()) {
			access_denied('unit_of_measure');
		}
		
		if ($this->input->post()) {
			$data = $this->input->post();

			if ($id == '') {
				if (!has_permission('manufacturing', '', 'create') && !is_admin()) {
					access_denied('unit_of_measure');
				}

				$id = $this->manufacturing_model->add_unit_of_measure($data);
				if ($id) {
					set_alert('success', _l('mrp_added_successfully', _l('unit_of_measure')));
					redirect(admin_url('manufacturing/setting?group=unit_of_measure'));
				}

			} else {
				if (!has_permission('manufacturing', '', 'edit') && !is_admin()) {
					access_denied('unit_of_measure');
				}

				$response = $this->manufacturing_model->update_unit_of_measure($data, $id);

				if (is_array($response)) {
					if (isset($response['cant_remove_main_admin'])) {
						set_alert('warning', _l('staff_cant_remove_main_admin'));
					} elseif (isset($response['cant_remove_yourself_from_admin'])) {
						set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
					}
				} elseif ($response == true) {
					set_alert('success', _l('mrp_updated_successfully', _l('unit_of_measure')));
				}
				redirect(admin_url('manufacturing/setting?group=unit_of_measure'));
			}
		}
		
		$data=[];
		if ($id != ''){
			$working_hour = $this->manufacturing_model->get_working_hour($id);
			$data['working_hour'] = $working_hour['working_hour'];
			$data['working_hour_details'] = $working_hour['working_hour_details'];
			$data['time_off'] = $working_hour['time_off'];
			
		}
		

		$day_period_type =[];
		$day_period_type[] = [
			'id' => 'morning',
			'label' => _l('morning'),
		];
		$day_period_type[] = [
			'id' => 'afternoon',
			'label' => _l('afternoon'),
		];

		$day_of_week_types=[];
		foreach (mrp_date_of_week() as $key => $value) {
		    array_push($day_of_week_types, [
		    	'id' => $key,
		    	'label' => $value,
		    ]);
		}

		$data['day_of_week_types'] = $day_of_week_types;
		$data['day_period_type'] = $day_period_type;
		
		$this->load->view('manufacturing/settings/add_edit_working_hour', $data);
	}

	/**
	 * unit of measure modal
	 * @return [type] 
	 */
	public function unit_of_measure_modal()
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$data=[];
		$data = $this->input->post();
		if($data['unit_id'] != 0){
			$data['unit_of_measure'] = $this->manufacturing_model->get_unit_of_measure($data['unit_id']);
		}

		$unit_types=[];
		$unit_types[] = [
			'id' => 'bigger',
			'value' => _l('bigger_than_the_reference_Unit_of_Measure'),
		];
		$unit_types[] = [
			'id' => 'reference',
			'value' => _l('reference_Unit_of_Measure_for_this_category'),
		];
		$unit_types[] = [
			'id' => 'smaller',
			'value' => _l('smaller_than_the_reference_Unit_of_Measure'),
		];
		$data['unit_types'] = $unit_types;

		$data['categories'] = $this->manufacturing_model->get_unit_categories();
		$this->load->view('settings/unit_of_measure/add_edit_unit_of_measure_modal', $data);
	}

	/**
	 * delete unit of measure
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_unit_of_measure($id)
	{
	    if (!has_permission('manufacturing', '', 'delete')  && !is_admin()) {
			access_denied('work_center');
		}

		$success = $this->manufacturing_model->delete_unit_of_measure($id);
		if ($success) {
			set_alert('success', _l('mrp_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('manufacturing/setting?group=unit_of_measure'));
	}


	/**
	 * product table
	 * @return [type] 
	 */
	public function product_table()
	{
		$this->app->get_table_data(module_views_path('manufacturing', 'products/products/product_table'));
	}


	/**
	 * product management
	 * @param  string $id 
	 * @return [type]     
	 */
	public function product_management($id = '')
	{

		$data['title'] = _l('product_management');
		$data['commodity_filter'] = $this->manufacturing_model->get_product();
		$data['product_id'] = $id;
		$data['parent_products'] = $this->manufacturing_model->get_parent_product();
		$data['product_types'] = mrp_product_type();
		$data['product_categories'] = $this->manufacturing_model->mrp_get_item_group();

		$required_inventory_purchase = mrp_required_inventory_purchase_module();
		//required inventory purchase
		if($required_inventory_purchase['inventory'] == false || $required_inventory_purchase['purchase'] == false){
			$this->load->view('manufacturing/settings/required_inventory_module', $data);
		}else{
			$this->load->view('products/products/product_manage', $data);
		}

	}


	/**
	 * add edit product
	 * @param [type] $type : product or product variant
	 * @param string $id   
	 */
	public function add_edit_product($type, $id = '')
	{
		if (!has_permission('manufacturing', '', 'view')  && !is_admin()) {
			access_denied('work_center');
		}
		
		if ($this->input->post()) {
			$data = $this->input->post();
			if ($id == '') {
				if (!has_permission('manufacturing', '', 'create') && !is_admin()) {
					access_denied('work_center');
				}

				$result = $this->manufacturing_model->add_product($data, $type);

				if($type == 'product_variant'){
					$url = admin_url('manufacturing/product_variant_management');
				}else{
					$url = admin_url('manufacturing/product_management');
				}

				if ($result) {

					set_alert('success', _l('mrp_added_successfully'));
					/*upload multifile*/
					echo json_encode([
						'url' => $url,
						'commodityid' => $result['insert_id'],
						'add_variant' => $result['add_variant'],
						'rel_type' => $type,
						'add_or_update' => 'add',

					]);
					die;
				}

				set_alert('warning', _l('mrp_added_failed'));

				if($type == 'product_variant'){
					$url = admin_url('manufacturing/product_variant_management');
				}else{
					$url = admin_url('manufacturing/product_management');
				}

				echo json_encode([
					'url' => $url,
					'rel_type' => $type,
					'add_or_update' => 'add',

				]);
				die;

			} else {
				if (!has_permission('manufacturing', '', 'edit') && !is_admin()) {
					access_denied('work_center');
				}
				$success = $this->manufacturing_model->update_product($data, $id, $type);
				/*update file*/
				set_alert('success', _l('mrp_updated_successfully'));

				if($type == 'product_variant'){
					$url = admin_url('manufacturing/product_variant_management');
				}else{
					$url = admin_url('manufacturing/product_management');
				}

				echo json_encode([
					'url' => $url,
					'commodityid' => $id,
					'rel_type' => $type,
					'add_or_update' => 'update',

				]);
				die;
			}
		}
		
		$data=[];
		$data['title'] = _l('add_product');
		if ($id != ''){
			$data['product'] = $this->manufacturing_model->get_product($id);
			$data['product_attachments'] = $this->manufacturing_model->mrp_get_attachments_file($id, 'commodity_item_file');
			$data['title'] = _l('update_product');
		}

		$data['array_product_type'] = mrp_product_type();
		$data['type'] = $type;
		$data['product_group'] = $this->manufacturing_model->mrp_get_item_group();
		$data['units'] = $this->manufacturing_model->mrp_get_unit();
		$data['taxes'] = mrp_get_taxes();

		$this->load->view('manufacturing/products/add_edit_product', $data);
	}


	/**
	 * check sku duplicate
	 * @return [type] 
	 */
	public function check_sku_duplicate()
    {
    	$data = $this->input->post();
    	$result = $this->manufacturing_model->check_sku_duplicate($data);

    	echo json_encode([
    		'message' => $result
    	]);
    	die;	
    }


    /**
     * add product attachment
     * @param [type] $id 
     */
    public function add_product_attachment($id, $rel_type, $add_variant='')
    {

    	mrp_handle_product_attachments($id);

    	if($rel_type == 'product_variant'){
    		$url = admin_url('manufacturing/product_variant_management');
    	}else{
    		$url = admin_url('manufacturing/product_management');
    	}

    	echo json_encode([
    		'url' => $url,
    		'id' => $id,
    		'rel_type' => $rel_type,
    		'add_variant' => $add_variant,
    	]);
    }


	/**
	 * delete product attachment
	 * @param  [type] $attachment_id 
	 * @param  [type] $rel_type      
	 * @return [type]                
	 */
	public function delete_product_attachment($attachment_id, $rel_type)
	{
	    if (!has_permission('manufacturing', '', 'delete') && !is_admin()) {
			access_denied('manufacturing');
		}

		$folder_name = '';

		switch ($rel_type) {
			case 'manufacturing':
				$folder_name = MANUFACTURING_PRODUCT_UPLOAD;
				break;
			case 'warehouse':
				$folder_name = module_dir_path('warehouse', 'uploads/item_img/');
				break;
			case 'purchase':
				$folder_name = module_dir_path('purchase', 'uploads/item_img/');
				break;
		}

		echo json_encode([
			'success' => $this->manufacturing_model->delete_mrp_attachment_file($attachment_id, $folder_name),
		]);
	}


	/**
	 * delete product
	 * @param  [type] $id       
	 * @param  [type] $rel_type 
	 * @return [type]           
	 */
	public function delete_product($id, $rel_type)
	{

		if (!$id) {
			redirect(admin_url('manufacturing/product_management'));
		}

		if(!has_permission('manufacturing', '', 'delete')  &&  !is_admin()) {
			access_denied('manufacturing');
		}

		$response = $this->manufacturing_model->delete_product($id, $rel_type);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('commodity')));
		} elseif ($response == true) {
			set_alert('success', _l('mrp_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		if($rel_type == 'product_variant'){
			redirect(admin_url('manufacturing/product_variant_management'));
		}else{
			redirect(admin_url('manufacturing/product_management'));
		}
	}


	/**
	 * product variant table
	 * @return [type] 
	 */
	public function product_variant_table()
	{
		$this->app->get_table_data(module_views_path('manufacturing', 'products/product_variants/product_variant_table'));
	}


	/**
	 * product variant management
	 * @param  string $id 
	 * @return [type]     
	 */
	public function product_variant_management($id = '')
	{

		$data['title'] = _l('product_variant_management');
		$data['commodity_filter'] = $this->manufacturing_model->get_product();
		$data['product_id'] = $id;
		$data['product_variants'] = $this->manufacturing_model->get_product_variant();
		$data['product_types'] = mrp_product_type();
		$data['product_categories'] = $this->manufacturing_model->mrp_get_item_group();
		
		$required_inventory_purchase = mrp_required_inventory_purchase_module();
		//required inventory purchase
		if($required_inventory_purchase['inventory'] == false || $required_inventory_purchase['purchase'] == false){
			$this->load->view('manufacturing/settings/required_inventory_module', $data);
		}else{
			$this->load->view('products/product_variants/product_variant_manage', $data);
		}
	}


	/**
	 * copy product image
	 * @param  [type] $id       
	 * @param  [type] $rel_type 
	 * @return [type]           
	 */
	public function copy_product_image($id, $rel_type)
    {

    	$this->manufacturing_model->copy_product_image($id);
    	if($rel_type == 'product_variant'){
    		$url = admin_url('manufacturing/product_variant_management');
    	}else{
    		$url = admin_url('manufacturing/product_management');
    	}

    	echo json_encode([
    		'url' => $url,
    	]);
    }


    /**
     * bill of material manage
     * @return [type] 
     */
    public function bill_of_material_manage()
	{
	    if (!has_permission('manufacturing', '', 'view') ) {
			access_denied('work_center');
		}

		$data['title'] = _l('bill_of_material');
		$data['products'] = $this->manufacturing_model->get_product();
		$data['routings'] = $this->manufacturing_model->get_routings();
		$bom_type=[];
		
		$bom_type[] = [
			'name' => 'kit',
			'label' => _l('kit'),
		];

		$bom_type[] = [
			'name' => 'manufacture_this_product',
			'label' => _l('manufacture_this_product'),
		];
		$data['bom_types'] = $bom_type;
		
		$required_inventory_purchase = mrp_required_inventory_purchase_module();
		//required inventory purchase
		if($required_inventory_purchase['inventory'] == false || $required_inventory_purchase['purchase'] == false){
			$this->load->view('manufacturing/settings/required_inventory_module', $data);
		}else{
			$this->load->view('manufacturing/bill_of_materials/bill_of_material_manage', $data);
		}
	}

	
	/**
	 * bill of material table
	 * @return [type] 
	 */
	public function bill_of_material_table()
	{
			$this->app->get_table_data(module_views_path('manufacturing', 'bill_of_materials/bill_of_material_table'));
	}


	/**
	 * bill of material modal
	 * @return [type] 
	 */
	public function bill_of_material_modal()
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$data=[];
		
		$ready_to_produce_type=[];
		$consumption_type=[];
		
		$ready_to_produce_type[] = [
			'name' => 'all_available',
			'label' => _l('when_all_components_are_available'),
		];

		$ready_to_produce_type[] = [
			'name' => 'components_for_1st',
			'label' => _l('when_components_for_1st_operation_are_available'),
		];

		$consumption_type[] = [
			'name' => 'strict',
			'label' => _l('strict'),
		];

		$consumption_type[] = [
			'name' => 'flexible',
			'label' => _l('flexible'),
		];

		

		$data['title'] = _l('bills_of_materials');
		$data['units'] = $this->manufacturing_model->mrp_get_unit();
		$data['ready_to_produce_type'] = $ready_to_produce_type;
		$data['consumption_type'] = $consumption_type;
		$data['routings'] = $this->manufacturing_model->get_routings();
		$data['parent_product'] = $this->manufacturing_model->get_parent_product();
		$data['bom_code'] = $this->manufacturing_model->create_code('bom_code');


		$this->load->view('bill_of_materials/add_edit_bill_of_material_modal', $data);
	}


	/**
	 * add bill of material modal
	 * @param string $id 
	 */
	public function add_bill_of_material_modal($id='')
	{

		if (!has_permission('manufacturing', '', 'view')  && !is_admin()) {
			access_denied('bill_of_material_label');
		}
		
		if ($this->input->post()) {
			$data = $this->input->post();

			if ($id == '') {
				if (!has_permission('manufacturing', '', 'create') && !is_admin()) {
					access_denied('bill_of_material_label');
				}

				$id = $this->manufacturing_model->add_bill_of_material($data);
				if ($id) {
					set_alert('success', _l('mrp_added_successfully', _l('bill_of_material_label')));
					redirect(admin_url('manufacturing/bill_of_material_detail_manage/'.$id));
				}

			} else {
				if (!has_permission('manufacturing', '', 'edit') && !is_admin()) {
					access_denied('bill_of_material_label');
				}

				$response = $this->manufacturing_model->update_bill_of_material($data, $id);

				if (is_array($response)) {
					if (isset($response['cant_remove_main_admin'])) {
						set_alert('warning', _l('staff_cant_remove_main_admin'));
					} elseif (isset($response['cant_remove_yourself_from_admin'])) {
						set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
					}
				} elseif ($response == true) {
					set_alert('success', _l('mrp_updated_successfully', _l('bill_of_material_label')));
				}
				redirect(admin_url('manufacturing/bill_of_material_detail_manage/'.$id));
			}
		}
		
		$data=[];
		if ($id != ''){
			$working_hour = $this->manufacturing_model->get_working_hour($id);
			$data['working_hour'] = $working_hour['working_hour'];
			$data['working_hour_details'] = $working_hour['working_hour_details'];
			$data['time_off'] = $working_hour['time_off'];
			
		}
		

		$ready_to_produce_type=[];
		$consumption_type=[];
		
		$ready_to_produce_type[] = [
			'name' => 'all_available',
			'label' => _l('when_all_components_are_available'),
		];

		$ready_to_produce_type[] = [
			'name' => 'components_for_1st',
			'label' => _l('when_components_for_1st_operation_are_available'),
		];

		$consumption_type[] = [
			'name' => 'strict',
			'label' => _l('strict'),
		];

		$consumption_type[] = [
			'name' => 'flexible',
			'label' => _l('flexible'),
		];
		$data['units'] = $this->manufacturing_model->mrp_get_unit();
		$data['ready_to_produce_type'] = $ready_to_produce_type;
		$data['consumption_type'] = $consumption_type;
		$data['routings'] = $this->manufacturing_model->get_routings();
		$data['parent_product'] = $this->manufacturing_model->get_parent_product();

		
		$this->load->view('manufacturing/settings/add_edit_working_hour', $data);
	}

	/**
	 * delete bill of material
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_bill_of_material($id)
	{
	    if (!has_permission('manufacturing', '', 'delete')  && !is_admin()) {
			access_denied('routing');
		}

		$success = $this->manufacturing_model->delete_bill_of_material($id);
		if ($success) {
			set_alert('success', _l('mrp_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('manufacturing/bill_of_material_manage'));
	}


	/**
	 * bill of material detail manage
	 * @param  string $id 
	 * @return [type]     
	 */
	public function bill_of_material_detail_manage($id='')
	{
	    if (!has_permission('manufacturing', '', 'view') ) {
			access_denied('bill_of_material_label');
		}

		$data['title'] = _l('bill_of_material_label');
		if($id != ''){
			$data['bill_of_material'] = $this->manufacturing_model->get_bill_of_materials($id);
		}
		
		$ready_to_produce_type=[];
		$consumption_type=[];
		
		$ready_to_produce_type[] = [
			'name' => 'all_available',
			'label' => _l('when_all_components_are_available'),
		];

		$ready_to_produce_type[] = [
			'name' => 'components_for_1st',
			'label' => _l('when_components_for_1st_operation_are_available'),
		];

		$consumption_type[] = [
			'name' => 'strict',
			'label' => _l('strict'),
		];

		$consumption_type[] = [
			'name' => 'flexible',
			'label' => _l('flexible'),
		];
		$data['units'] = $this->manufacturing_model->mrp_get_unit();
		$data['ready_to_produce_type'] = $ready_to_produce_type;
		$data['consumption_type'] = $consumption_type;
		$data['routings'] = $this->manufacturing_model->get_routings();
		$data['parent_product'] = $this->manufacturing_model->get_parent_product();
		$data['product_variant'] = $this->manufacturing_model->get_product_variant();

		$this->load->view('manufacturing/bill_of_materials/bill_of_material_details/bill_of_material_detail_manage', $data);
	}


	/**
	 * bill_of_material_detail table
	 * @return [type] 
	 */
	public function bill_of_material_detail_table()
	{
			$this->app->get_table_data(module_views_path('manufacturing', 'bill_of_materials/bill_of_material_details/bill_of_material_detail_table'));
	}


	/**
	 * bill of material detail modal
	 * @return [type] 
	 */
	public function bill_of_material_detail_modal()
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$data=[];
		$data = $this->input->post();


		if($data['component_id'] != 0){
			$data['bill_of_material_detail'] = $this->manufacturing_model->get_bill_of_material_details($data['component_id']);
		}
		// get product_variants
		$product_variant_id = 0;
		$bill_of_materials = $this->manufacturing_model->get_bill_of_materials($data['bill_of_material_id']);
		if($bill_of_materials){
			$product_variant_id = $bill_of_materials->product_variant_id;
		}
		//get variant of product
		$data['arr_variants'] = $this->manufacturing_model->get_variant_attribute($data['bill_of_material_product_id'], $product_variant_id);
		//get operation of routing
		$data['arr_operations'] = $this->manufacturing_model->get_operation(false, $data['routing_id']);

		$data['products'] = $this->manufacturing_model->get_product();
		$data['product_variants'] = $this->manufacturing_model->get_product_variant();
		$data['units'] = $this->manufacturing_model->mrp_get_unit();

		$this->load->view('bill_of_materials/bill_of_material_details/add_edit_bill_of_material_detail_modal', $data);
	}


	/**
	 * add edit bill of material detail
	 * @param string $id 
	 */
	public function add_edit_bill_of_material_detail($id='')
	{
	    if (!has_permission('manufacturing', '', 'view')  && !is_admin()) {
			access_denied('component');
		}
		
		if ($this->input->post()) {
			$data = $this->input->post();
			$bill_of_material_id = $data['bill_of_material_id'];

			if ($id == '') {
				if (!has_permission('manufacturing', '', 'create') && !is_admin()) {
					access_denied('component');
				}

				$id = $this->manufacturing_model->add_bill_of_material_detail($data);
				if ($id) {

					set_alert('success', _l('mrp_added_successfully', _l('component')));
					redirect(admin_url('manufacturing/bill_of_material_detail_manage/'.$bill_of_material_id));
				}

			} else {
				if (!has_permission('manufacturing', '', 'edit') && !is_admin()) {
					access_denied('component');
				}

				$response = $this->manufacturing_model->update_bill_of_material_detail($data, $id);

				if (is_array($response)) {
					if (isset($response['cant_remove_main_admin'])) {
						set_alert('warning', _l('staff_cant_remove_main_admin'));
					} elseif (isset($response['cant_remove_yourself_from_admin'])) {
						set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
					}
				} elseif ($response == true) {
					set_alert('success', _l('mrp_updated_successfully', _l('component')));
				}
				redirect(admin_url('manufacturing/bill_of_material_detail_manage/'.$bill_of_material_id));
			
			}
		}

	}


	/**
	 * delete bill of material detail
	 * @param  [type] $id         
	 * @param  [type] $routing_id 
	 * @return [type]             
	 */
	public function delete_bill_of_material_detail($id, $bill_of_material_id)
	{
	    if (!has_permission('manufacturing', '', 'delete')  && !is_admin()) {
			access_denied('work_center');
		}

		$success = $this->manufacturing_model->delete_bill_of_material_detail($id);
		if ($success) {
			set_alert('success', _l('mrp_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('manufacturing/bill_of_material_detail_manage/'.$bill_of_material_id));


	}

	/**
	 * get product variants
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_product_variants($id)
	{
		$product_variants = $this->manufacturing_model->get_product_variants($id);
		$product = $this->manufacturing_model->get_product($id);
		if($product){
			$unit_id = $product->unit_id;
		}else{
			$unit_id = '';
		}

		echo json_encode([
			'product_variants' => $product_variants,
			'unit_id' => $unit_id,
		]);
	    
	}


	/**
	 * manufacturing order manage
	 * @return [type] 
	 */
    public function manufacturing_order_manage()
	{
	    if (!has_permission('manufacturing', '', 'view') ) {
			access_denied('manufacturing_order');
		}

		
		$data['title'] = _l('manufacturing_order');
		$data['products'] = $this->manufacturing_model->get_product();
		$data['routings'] = $this->manufacturing_model->get_routings();
		$status_data=[];
		$status_data[]=[
			'name' => 'draft',
			'label' => _l('mrp_draft'),
		];
		$status_data[]=[
			'name' => 'planned',
			'label' => _l('mrp_planned'),
		];
		$status_data[]=[
			'name' => 'cancelled',
			'label' => _l('mrp_cancelled'),
		];
		$status_data[]=[
			'name' => 'confirmed',
			'label' => _l('mrp_confirmed'),
		];
		$status_data[]=[
			'name' => 'done',
			'label' => _l('mrp_done'),
		];
		$status_data[]=[
			'name' => 'in_progress',
			'label' => _l('mrp_in_progress'),
		];
		
		$data['status_data'] = $status_data;
		
		$required_inventory_purchase = mrp_required_inventory_purchase_module();
		//required inventory purchase
		if($required_inventory_purchase['inventory'] == false || $required_inventory_purchase['purchase'] == false){
			$this->load->view('manufacturing/settings/required_inventory_module', $data);
		}else{
			$this->load->view('manufacturing/manufacturing_orders/manufacturing_order_manage', $data);
		}
	}

	
	/**
	 * manufacturing order table
	 * @return [type] 
	 */
	public function manufacturing_order_table()
	{
			$this->app->get_table_data(module_views_path('manufacturing', 'manufacturing_orders/manufacturing_order_table'));
	}

	
	/**
	 * add edit manufacturing order
	 * @param string $id 
	 */
	public function add_edit_manufacturing_order($id = '')
	{
		if (!has_permission('manufacturing', '', 'view')  && !is_admin()) {
			access_denied('manufacturing_order');
		}
		
		$this->load->model('staff_model');
		$this->load->model('warehouse/warehouse_model');

		if ($this->input->post()) {
			$data = $this->input->post();

			if ($id == '') {
				if (!has_permission('manufacturing', '', 'create') && !is_admin()) {
					access_denied('manufacturing_order');
				}

				$id = $this->manufacturing_model->add_manufacturing_order($data);
				if ($id) {
					set_alert('success', _l('mrp_added_successfully', _l('manufacturing_order')));
					redirect(admin_url('manufacturing/view_manufacturing_order/'.$id));
				}

			} else {
				if (!has_permission('manufacturing', '', 'edit') && !is_admin()) {
					access_denied('manufacturing_order');
				}

				$response = $this->manufacturing_model->update_manufacturing_order($data, $id);

				if (is_array($response)) {
					if (isset($response['cant_remove_main_admin'])) {
						set_alert('warning', _l('staff_cant_remove_main_admin'));
					} elseif (isset($response['cant_remove_yourself_from_admin'])) {
						set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
					}
				} elseif ($response == true) {
					set_alert('success', _l('mrp_updated_successfully', _l('manufacturing_order')));
				}
				redirect(admin_url('manufacturing/view_manufacturing_order/'.$id));
			}
		}
		
		$data=[];
		if ($id != ''){
			$data['title'] = _l('update_manufacturing_order_lable');
			$manufacturing_order = $this->manufacturing_model->get_manufacturing_order($id);
			$data['manufacturing_order'] = $manufacturing_order['manufacturing_order'];
			$data['product_tab_details'] = $manufacturing_order['manufacturing_order_detail'];
			$data['bill_of_materials'] = $this->manufacturing_model->get_list_bill_of_material_by_product($data['manufacturing_order']->product_id);

		}else{
			$data['title'] = _l('add_manufacturing_order_lable');
			$data['bill_of_materials'] = $this->manufacturing_model->get_bill_of_material_detail_with_product_name();
		}

		$data['products'] = $this->manufacturing_model->get_product();
		$data['units'] = $this->manufacturing_model->mrp_get_unit();
		$data['product_for_hansometable'] = $this->manufacturing_model->get_product_for_hansometable();
		$data['unit_for_hansometable'] = $this->manufacturing_model->get_unit_for_hansometable();
		$data['staffs'] = $this->staff_model->get();
		$data['warehouses'] = $this->warehouse_model->get_warehouse();
		$data['mo_code'] = $this->manufacturing_model->create_code('mo_code');

		$this->load->view('manufacturing/manufacturing_orders/add_edit_manufacturing_order', $data);
	}


	/**
	 * delete manufacturing order
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_manufacturing_order($id)
	{
		if (!has_permission('manufacturing', '', 'delete')  && !is_admin()) {
			access_denied('manufacturing_order');
		}

		$success = $this->manufacturing_model->delete_manufacturing_order($id);
		if ($success) {
			set_alert('success', _l('mrp_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('manufacturing/manufacturing_order_manage'));
	}

	/**
	 * get data create manufacturing order
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_data_create_manufacturing_order($id)
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		$results = $this->manufacturing_model->get_data_create_manufacturing_order($id);

		echo json_encode([
			'bill_of_material_option' =>$results['bill_of_material_option'],
			'routing_id' => $results['routing_option'],
			'routing_name' => mrp_get_routing_name($results['routing_option']),
			'component_arr' => $results['component_arr'],
			'component_row' => $results['component_row'],
			'unit_id' => $results['unit_id'],
		]);
	}


	/**
	 * get bill of material detail
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_bill_of_material_detail($bill_of_material_id, $product_id, $product_qty='')
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$component_arr=[];
		$routing_id=0;

		$product = $this->manufacturing_model->get_product($product_id);
		if($product){
			$component_arr = $this->manufacturing_model->get_bill_of_material_details_by_product($bill_of_material_id, $product->attributes, $product_qty);
		}

		$bill_of_material = $this->manufacturing_model->get_bill_of_materials($bill_of_material_id);
		if($bill_of_material){
			$routing_id = $bill_of_material->routing_id;
		}

		echo json_encode([
			'component_arr' => $component_arr,
			'component_row' => count($component_arr),
			'routing_id' => $routing_id,
			'routing_name' => mrp_get_routing_name($routing_id),
		]);
	}


	/**
	 * view manufacturing order
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function view_manufacturing_order($id)
	{
		if (!has_permission('manufacturing', '', 'view')  && !is_admin()) {
			access_denied('manufacturing_order');
		}

		$manufacturing_order = $this->manufacturing_model->get_manufacturing_order($id);
		$data['manufacturing_order'] = $manufacturing_order['manufacturing_order'];
		$data['product_tab_details'] = $manufacturing_order['manufacturing_order_detail'];
		$data['product_for_hansometable'] = $this->manufacturing_model->get_product_for_hansometable();
		$data['unit_for_hansometable'] = $this->manufacturing_model->get_unit_for_hansometable();
		$data['manufacturing_order_costing'] = $this->manufacturing_model->get_manufacturing_order_costing($id);
		$check_manufacturing_order = $this->manufacturing_model->check_manufacturing_order_type($id);

		if($data['manufacturing_order']->status == 'confirmed'){
			$check_planned = $check_manufacturing_order['check_planned'];
		}else{
			$check_planned = false;
		}
		$data['check_planned'] = $check_planned;
		$data['check_mark_done'] = $check_manufacturing_order['check_mo_done'];
		$data['check_create_purchase_request'] = $check_manufacturing_order['check_create_purchase_request'];
		$data['check_availability'] = $check_manufacturing_order['check_availability'];
		$data['data_color'] = $check_manufacturing_order['data_color'];
		$data['title'] = _l('manufacturing_order_details');
		$data['currency'] = get_base_currency();

		//check pur order exist
		$pur_order_exist = false;
		if(is_numeric($data['manufacturing_order']->purchase_request_id)){
			$this->load->model('purchase/purchase_model');
			$get_purchase_request = $this->purchase_model->get_purchase_request($data['manufacturing_order']->purchase_request_id);
			if($get_purchase_request){
				$pur_order_exist = true;
			}
		}
		
		$data['pur_order_exist'] = $pur_order_exist;
		if (!$data['manufacturing_order']) {
			blank_page(_l('manufacturing_order'), 'danger');
		}

		$this->load->view('manufacturing/manufacturing_orders/view_manufacturing_order', $data);
	}

	/**
	 * mo mark as todo
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function mo_mark_as_todo($id, $type)
	{
		//Check inventory quantity => create purchase request on work order
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		if (!has_permission('manufacturing', '', 'create')  && !has_permission('manufacturing', '', 'edit')  && !is_admin()) {
			access_denied('manufacturing_order');
		}

		$mo_mark_as_todo = $this->manufacturing_model->mo_mark_as_todo($id, $type);

		if($mo_mark_as_todo['status']){
			$status='success';
			$message = _l('mrp_updated_successfully');
		}else{
			$status='warning';
			$message = $mo_mark_as_todo['message'];
		}

		echo json_encode([
			'status' => $status,
			'message' => $message,
		]);
	}

	/**
	 * mo mark as todo
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function mo_mark_as_planned($id)
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		if (!has_permission('manufacturing', '', 'create')  && !has_permission('manufacturing', '', 'edit')  && !is_admin()) {
			access_denied('manufacturing_order');
		}

		$mo_mark_as_planned = $this->manufacturing_model->mo_mark_as_planned($id);

		if($mo_mark_as_planned){
			$status='success';
			$message = _l('mrp_updated_successfully');
		}else{
			$status='warning';
			$message = _l('mrp_updated_failed');
		}

		echo json_encode([
			'status' => $status,
			'message' => $message,
		]);
	}

	/**
	 * work order manage
	 * @return [type] 
	 */
	public function work_order_manage()
	{
	    if (!has_permission('manufacturing', '', 'view') ) {
			access_denied('manufacturing_order');
		}

		
		$data['title'] = _l('manufacturing_order');
		$data['products'] = $this->manufacturing_model->get_product();
		$data['routings'] = $this->manufacturing_model->get_routings();
		$status_data=[];
		$status_data[]=[
			'name' => 'waiting_for_another_wo',
			'label' => _l('waiting_for_another_wo'),
		];
		$status_data[]=[
			'name' => 'ready',
			'label' => _l('ready'),
		];
		$status_data[]=[
			'name' => 'in_progress',
			'label' => _l('in_progress'),
		];
		$status_data[]=[
			'name' => 'finished',
			'label' => _l('finished'),
		];
		$status_data[]=[
			'name' => 'pause',
			'label' => _l('pause'),
		];
		
		$data['status_data'] = $status_data;
		$data['manufacturing_orders'] = $this->manufacturing_model->get_list_manufacturing_order();

		$required_inventory_purchase = mrp_required_inventory_purchase_module();
		//required inventory purchase
		if($required_inventory_purchase['inventory'] == false || $required_inventory_purchase['purchase'] == false){
			$this->load->view('manufacturing/settings/required_inventory_module', $data);
		}else{
			$this->load->view('manufacturing/work_orders/work_order_manage', $data);
		}
	}

	/**
	 * work order table
	 * @return [type] 
	 */
	public function work_order_table()
	{
		$this->app->get_table_data(module_views_path('manufacturing', 'work_orders/work_order_table'));
	}

	/**
	 * view work order
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function view_work_order($id, $manufacturing_order_id)
	{
		if (!has_permission('manufacturing', '', 'view') && !has_permission('manufacturing', '', 'create')  && !has_permission('manufacturing', '', 'edit')  && !is_admin()) {
			access_denied('work_order_label');
		}

		$data=[];
		$data['work_order'] = $this->manufacturing_model->get_work_order($id);

		if (!$data['work_order']) {
			blank_page(_l('work_order_label'), 'danger');
		}
		$data['work_order_file'] = $this->manufacturing_model->mrp_get_attachments_file($data['work_order']->routing_detail_id, 'mrp_operation');
		$work_order_prev_next = $this->manufacturing_model->get_work_order_previous_next($id, $manufacturing_order_id);
		$data['prev_id'] = $work_order_prev_next['prev_id'];
		$data['next_id'] = $work_order_prev_next['next_id'];
		$data['pager_value'] = $work_order_prev_next['pager_value'];
		$data['pager_limit'] = $work_order_prev_next['pager_limit'];
		$data['manufacturing_order_id'] = $manufacturing_order_id;
		$data['header'] = _l('work_order_label').' / '.mrp_get_manufacturing_code($manufacturing_order_id).' - '.mrp_get_product_name($data['work_order']->product_id).' - '.$data['work_order']->operation_name;
		$time_tracking_details = $this->manufacturing_model->get_time_tracking_details($id);

		$data['time_tracking_details'] = $time_tracking_details['time_trackings'];
		$data['rows'] = $time_tracking_details['rows'];
		$mo = $this->manufacturing_model->get_manufacturing_order($manufacturing_order_id);
		$check_mo_cancelled= false;
		if($mo['manufacturing_order']){
			if($mo['manufacturing_order']->status == 'cancelled'){
				$check_mo_cancelled= true;
			}
		}
		$data['check_mo_cancelled'] = $check_mo_cancelled;

		$this->load->view('manufacturing/work_orders/view_work_order', $data);
	}

	/**
	 * mo mark as start working
	 * @param  [type] $work_order_id 
	 * @return [type]                
	 */
	public function mo_mark_as_start_working($work_order_id, $manufacturing_order)
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		if (!has_permission('manufacturing', '', 'create')  && !has_permission('manufacturing', '', 'edit')  && !is_admin()) {
			access_denied('manufacturing_order');
		}

		$current_time=date('Y-m-d H:i:s');

		$mo_mark_as_start_working = $this->manufacturing_model->update_work_order_status($work_order_id, ['status' => 'in_progress', 'date_start' => to_sql_date($current_time, true)]);
		//update MO to in process
		$this->manufacturing_model->update_manufacturing_order_status($manufacturing_order, ['status' => 'in_progress']);
		
		//Add time tracking
		$data_tracking=[
			'work_order_id' => $work_order_id,
			'from_date' => $current_time,
			'staff_id' => get_staff_user_id(),
		];
		$this->manufacturing_model->add_time_tracking($data_tracking);


		if($mo_mark_as_start_working){
			$status='success';
			$message = _l('mrp_updated_successfully');
		}else{
			$status='warning';
			$message = _l('mrp_updated_failed');
		}

		echo json_encode([
			'status' => $status,
			'message' => $message,
		]);
	}

	/**
	 * mo mark as mark pause
	 * @param  [type] $work_order_id 
	 * @return [type]                
	 */
	public function mo_mark_as_mark_pause($work_order_id)
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		if (!has_permission('manufacturing', '', 'create')  && !has_permission('manufacturing', '', 'edit')  && !is_admin()) {
			access_denied('manufacturing_order');
		}

		$mo_mark_as_start_working = $this->manufacturing_model->update_work_order_status($work_order_id, ['status' => 'pause']);

		$current_time=date('Y-m-d H:i:s');

		//Update time tracking
		$data_update=[
			'work_order_id' => $work_order_id,
			'to_date' => $current_time,
			'staff_id' => get_staff_user_id(),
		];
		$update_time_tracking = $this->manufacturing_model->update_time_tracking($work_order_id, $data_update);

		if($update_time_tracking){
			$status='success';
			$message = _l('mrp_updated_successfully');
		}else{
			$status='warning';
			$message = _l('mrp_updated_failed');
		}

		echo json_encode([
			'status' => $status,
			'message' => $message,
		]);
	}

	/**
	 * mo mark as mark done
	 * @param  [type] $work_order_id 
	 * @return [type]                
	 */
	public function mo_mark_as_mark_done($work_order_id, $manufacturing_order_id)
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		if (!has_permission('manufacturing', '', 'create')  && !has_permission('manufacturing', '', 'edit')  && !is_admin()) {
			access_denied('manufacturing_order');
		}

		$wo_mark_as_done = $this->manufacturing_model->wo_mark_as_done($work_order_id, $manufacturing_order_id);

		if($wo_mark_as_done){
			$status='success';
			$message = _l('mrp_updated_successfully');
		}else{
			$status='warning';
			$message = _l('mrp_updated_failed');
		}

		echo json_encode([
			'status' => $status,
			'message' => $message,
		]);
	}
	
	/**
	 * mo work order manage
	 * @return [type] 
	 */
	public function mo_work_order_manage($mo_id='')
	{
	    if (!has_permission('manufacturing', '', 'view') ) {
			access_denied('manufacturing_order');
		}

		
		$data['title'] = _l('manufacturing_order');
		$data['products'] = $this->manufacturing_model->get_product();
		$data['routings'] = $this->manufacturing_model->get_routings();
		$status_data=[];
		$status_data[]=[
			'name' => 'draft',
			'label' => _l('mrp_draft'),
		];
		$status_data[]=[
			'name' => 'planned',
			'label' => _l('mrp_planned'),
		];
		$status_data[]=[
			'name' => 'cancelled',
			'label' => _l('mrp_cancelled'),
		];
		$status_data[]=[
			'name' => 'confirmed',
			'label' => _l('mrp_confirmed'),
		];
		$status_data[]=[
			'name' => 'done',
			'label' => _l('mrp_done'),
		];
		$status_data[]=[
			'name' => 'in_progress',
			'label' => _l('mrp_in_progress'),
		];
		
		$data['status_data'] = $status_data;
		$data['manufacturing_orders'] = $this->manufacturing_model->get_list_manufacturing_order();
		$data['mo_id'] = $mo_id;
		$data['data_timeline'] = $this->manufacturing_model->get_work_order_timeline($mo_id);

		$this->load->view('manufacturing/manufacturing_orders/mo_list_work_order', $data);
	}

	/**
	 * mo work order table
	 * @return [type] 
	 */
	public function mo_work_order_table()
	{
		$this->app->get_table_data(module_views_path('manufacturing', 'manufacturing_orders/mo_list_work_order_table'));
	}

	/**
	 * mo mark as done
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function mo_mark_as_done($id, $quantity)
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		if (!has_permission('manufacturing', '', 'create')  && !has_permission('manufacturing', '', 'edit')  && !is_admin()) {
			access_denied('manufacturing_order');
		}

		$mo_mark_as_done = $this->manufacturing_model->mo_mark_as_done($id, (float)$quantity);

		if($mo_mark_as_done){
			$status='success';
			$message = _l('mrp_updated_successfully');
		}else{
			$status='warning';
			$message = _l('mrp_updated_failed');
		}

		echo json_encode([
			'status' => $status,
			'message' => $message,
		]);
	}

	/**
	 * mo create purchase request
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function mo_create_purchase_request($id)
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		if (!has_permission('manufacturing', '', 'create')  && !has_permission('manufacturing', '', 'edit')  && !is_admin()) {
			access_denied('manufacturing_order');
		}

		$purchase_request_id = $this->manufacturing_model->mo_create_purchase_request($id);

		if($purchase_request_id){
			//update Purchase request id to Manufacturing order
			$this->manufacturing_model->update_manufacturing_order_status($id, ['purchase_request_id' => $purchase_request_id]);

			$status='success';
			$message = _l('mrp_added_successfully');
		}else{
			$status='warning';
			$message = _l('mrp_added_failed');
		}

		echo json_encode([
			'status' => $status,
			'message' => $message,
		]);
	}

	/**
	 * mo mark as unreserved
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function mo_mark_as_unreserved($id)
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		if (!has_permission('manufacturing', '', 'create')  && !has_permission('manufacturing', '', 'edit')  && !is_admin()) {
			access_denied('manufacturing_order');
		}

		$mo_mark_as_unreserved = $this->manufacturing_model->mo_mark_as_unreserved($id);

		if($mo_mark_as_unreserved){
			$status='success';
			$message = _l('mrp_updated_successfully');
		}else{
			$status='warning';
			$message = _l('mrp_updated_failed');
		}

		echo json_encode([
			'status' => $status,
			'message' => $message,
		]);
	}

	/**
	 * mo mark as cancel
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function mo_mark_as_cancel($id)
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		if (!has_permission('manufacturing', '', 'create')  && !has_permission('manufacturing', '', 'edit')  && !is_admin()) {
			access_denied('manufacturing_order');
		}

		$mo_mark_as_cancel = $this->manufacturing_model->mo_mark_as_cancel($id);

		if($mo_mark_as_cancel){
			$status='success';
			$message = _l('mrp_updated_successfully');
		}else{
			$status='warning';
			$message = _l('mrp_updated_failed');
		}

		echo json_encode([
			'status' => $status,
			'message' => $message,
		]);
	}
	
	/**
	 * mrp product delete bulk action
	 * @return [type] 
	 */
	public function mrp_product_delete_bulk_action()
	{
		if (!is_staff_member()) {
			ajax_access_denied();
		}

		$total_deleted = 0;

		if ($this->input->post()) {

			$ids                   = $this->input->post('ids');
			$rel_type                   = $this->input->post('rel_type');

			/*check permission*/
			switch ($rel_type) {
				case 'commodity_list':
				if (!has_permission('manufacturing', '', 'delete') && !is_admin()) {
					access_denied('product');
				}
				break;

				case 'bill_of_material':
				if (!has_permission('manufacturing', '', 'delete') && !is_admin()) {
					access_denied('product');
				}
				break;

				case 'manufacturing_order':
				if (!has_permission('manufacturing', '', 'delete') && !is_admin()) {
					access_denied('product');
				}
				break;

				case 'component_bill_of_material':
				if (!has_permission('manufacturing', '', 'delete') && !is_admin()) {
					access_denied('product');
				}
				break;
				

				default:
				break;
			}

			/*delete data*/
			if ($this->input->post('mass_delete')) {
				if (is_array($ids)) {
					switch ($rel_type) {
						case 'commodity_list':
							foreach ($ids as $id) {
								if ($this->manufacturing_model->delete_product($id, 'product')) {
									$total_deleted++;
								}
							}

							break;

						case 'bill_of_material':

							$this->db->where('bill_of_material_id IN ('.implode(",",$ids) .')');
							$this->db->delete(db_prefix() . 'mrp_bill_of_material_details');
							$delete_bom_detail = $this->db->affected_rows();
							
							//delete data
							$this->db->where('id IN ('.implode(",",$ids) .')');
							$this->db->delete(db_prefix() . 'mrp_bill_of_materials');
							$delete_bom = $this->db->affected_rows();
							if ($delete_bom > 0) {
								$total_deleted += $delete_bom;
							}

							break;

						case 'manufacturing_order':
							foreach ($ids as $id) {
								if ($this->manufacturing_model->delete_manufacturing_order($id)) {
									$total_deleted++;
								}
							}

							break;

						case 'component_bill_of_material':

							$this->db->where('id IN ('.implode(",",$ids) .')');
							$this->db->delete(db_prefix() . 'mrp_bill_of_material_details');
							$delete_bom_detail = $this->db->affected_rows();
							
							if ($delete_bom_detail > 0) {
								$total_deleted += $delete_bom_detail;
							}

							break;
						
						default:
							# code...
							break;
					}

				}

				/*return result*/
				switch ($rel_type) {
					case 'commodity_list':
					set_alert('success', _l('total_product'). ": " .$total_deleted);
					break;

					case 'bill_of_material':
					set_alert('success', _l('total_bill_of_material'). ": " .$total_deleted);
					break;
					
					case 'manufacturing_order':
					set_alert('success', _l('total_manufacturing_order'). ": " .$total_deleted);
					break;

					case 'component_bill_of_material':
					set_alert('success', _l('total_component_bill_of_material'). ": " .$total_deleted);
					break;
					

					default:
					break;

				}


			}

		}

	}

	/**
	 * item print barcode
	 * @return [type] 
	 */
	public function item_print_barcode()
	{
		$data = $this->input->post();

		$stock_export = $this->manufacturing_model->get_print_barcode_pdf_html($data);
		
		try {
			$pdf = $this->manufacturing_model->print_barcode_pdf($stock_export);

		} catch (Exception $e) {
			echo new_html_entity_decode($e->getMessage());
			die;
		}

		$type = 'I';

		if ($this->input->get('output_type')) {
			$type = $this->input->get('output_type');
		}

		if ($this->input->get('print')) {
			$type = 'I';
		}


		$pdf->Output('print_barcode_'.strtotime(date('Y-m-d H:i:s')).'.pdf', $type);

	}

	/**
	 * dashboard
	 * @return [type] 
	 */
	public function dashboard()
	{
	    if (!has_permission('manufacturing', '', 'view')  && !is_admin()) {
			access_denied('dashboard');
		}

		$data['title'] = _l('dashboard');
		$data['work_centers'] = $this->manufacturing_model->dasboard_get_work_center();

		$mo_measures_type=[];
		
		$mo_measures_type[]=[
			'name' => 'count',
			'label' => _l('count'),
		];
		$mo_measures_type[]=[
			'name' => 'total_qty',
			'label' => _l('total_qty'),
		];

		$wo_measures_type=[];
		
		$wo_measures_type[]=[
			'name' => 'count',
			'label' => _l('count'),
		];
		
		$wo_measures_type[]=[
			'name' => 'duration_per_unit',
			'label' => _l('duration_per_unit'),
		];
		$wo_measures_type[]=[
			'name' => 'expected_duration',
			'label' => _l('expected_duration'),
		];
		$wo_measures_type[]=[
			'name' => 'quantity',
			'label' => _l('quantity'),
		];
		$wo_measures_type[]=[
			'name' => 'real_duration',
			'label' => _l('real_duration'),
		];
		
		$data['mo_measures_type'] = $mo_measures_type;
		$data['wo_measures_type'] = $wo_measures_type;

		$required_inventory_purchase = mrp_required_inventory_purchase_module();
		//required inventory purchase
		if($required_inventory_purchase['inventory'] == false || $required_inventory_purchase['purchase'] == false){
			$this->load->view('manufacturing/settings/required_inventory_module', $data);
		}else{
			$this->load->view('manufacturing/dashboards/dashboard', $data);
		}

	}

	/**
	 * report by manufacturing order
	 * @param  [type] $sort_from     
	 * @param  string $months_report 
	 * @param  string $report_from   
	 * @param  string $report_to     
	 * @return [type]                
	 */
	public function report_by_manufacturing_order()
	{
		if ($this->input->is_ajax_request()) { 
			$data = $this->input->get();

			$mo_measures = $data['mo_measures'];
			$months_report = $data['months_report'];
			$report_from = $data['report_from'];
			$report_to = $data['report_to'];

			if($months_report == ''){

				$from_date = date('Y-m-d', strtotime('1997-01-01'));
				$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
			}

			if($months_report == 'this_month'){
				$from_date = date('Y-m-01');
				$to_date   = date('Y-m-t');
			}

			if($months_report == '1'){ 
				$from_date = date('Y-m-01', strtotime('first day of last month'));
				$to_date   = date('Y-m-t', strtotime('last day of last month'));
			}

			if($months_report == 'this_year'){
				$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
				$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
			}

			if($months_report == 'last_year'){

				$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
				$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));  


			}

			if($months_report == '3'){
				$months_report = 3;
				$months_report--;
				$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
				$to_date   = date('Y-m-t');

			}

			if($months_report == '6'){
				$months_report = 6;
				$months_report--;
				$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
				$to_date   = date('Y-m-t');
			}

			if($months_report == '12'){
				$months_report = 12;
				$months_report--;
				$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
				$to_date   = date('Y-m-t');
			}

			if($months_report == 'custom'){
				$from_date = to_sql_date($report_from);
				$to_date   = to_sql_date($report_to);
			}
	
			$mo_data = $this->manufacturing_model->get_mo_report_data($mo_measures, $from_date, $to_date);


			echo json_encode([
				'categories' => $mo_data['categories'],
				'draft' => $mo_data['draft'],
				'planned' => $mo_data['planned'],
				'cancelled' => $mo_data['cancelled'],
				'confirmed' => $mo_data['confirmed'],
				'done' => $mo_data['done'],
				'in_progress' => $mo_data['in_progress'],
			]); 
		}
	}

	/**
	 * report by work order
	 * @return [type] 
	 */
	public function report_by_work_order()
	{
		if ($this->input->is_ajax_request()) { 
			$data = $this->input->get();

			$mo_measures = $data['wo_measures'];
			$months_report = $data['months_report'];
			$report_from = $data['report_from'];
			$report_to = $data['report_to'];

			if($months_report == ''){

				$from_date = date('Y-m-d', strtotime('1997-01-01'));
				$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
			}

			if($months_report == 'this_month'){
				$from_date = date('Y-m-01');
				$to_date   = date('Y-m-t');
			}

			if($months_report == '1'){ 
				$from_date = date('Y-m-01', strtotime('first day of last month'));
				$to_date   = date('Y-m-t', strtotime('last day of last month'));
			}

			if($months_report == 'this_year'){
				$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
				$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
			}

			if($months_report == 'last_year'){

				$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
				$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));  


			}

			if($months_report == '3'){
				$months_report = 3;
				$months_report--;
				$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
				$to_date   = date('Y-m-t');

			}

			if($months_report == '6'){
				$months_report = 6;
				$months_report--;
				$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
				$to_date   = date('Y-m-t');
			}

			if($months_report == '12'){
				$months_report = 12;
				$months_report--;
				$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
				$to_date   = date('Y-m-t');
			}

			if($months_report == 'custom'){
				$from_date = to_sql_date($report_from);
				$to_date   = to_sql_date($report_to);
			}
	
			$mo_data = $this->manufacturing_model->get_wo_report_data($mo_measures, $from_date, $to_date);


			echo json_encode([
				'categories' => $mo_data['categories'],
				'mo_data' => $mo_data['mo_data'],

			]); 
		}
	}

	/**
	 * prefix number
	 * @return [type] 
	 */
	public function prefix_number()
	{
		if (!has_permission('manufacturing', '', 'edit') && !is_admin() && !has_permission('manufacturing', '', 'create')) {
			access_denied('manufacturing');
		}

		$data = $this->input->post();

		if ($data) {

			$success = $this->manufacturing_model->update_prefix_number($data);

			if ($success == true) {

				$message = _l('mrp_updated_successfully');
				set_alert('success', $message);
			}

			redirect(admin_url('manufacturing/setting?group=prefix_number'));
		}
	}

	public function view_product_detail($product_id) {
		$commodity_item = get_commodity_name($product_id);

		if (!$commodity_item) {
			blank_page('Product item Not Found', 'danger');
		}

		$this->load->model('warehouse/warehouse_model');

		//user for sub
		$data['units'] = $this->warehouse_model->get_unit_add_commodity();
		$data['commodity_types'] = $this->warehouse_model->get_commodity_type_add_commodity();
		$data['commodity_groups'] = $this->warehouse_model->get_commodity_group_add_commodity();
		$data['warehouses'] = $this->warehouse_model->get_warehouse_add_commodity();
		$data['taxes'] = get_taxes();
		$data['styles'] = $this->warehouse_model->get_style_add_commodity();
		$data['models'] = $this->warehouse_model->get_body_add_commodity();
		$data['sizes'] = $this->warehouse_model->get_size_add_commodity();
		$data['sub_groups'] = $this->warehouse_model->get_sub_group();
		$data['colors'] = $this->warehouse_model->get_color_add_commodity();
		$data['item_tags'] = $this->warehouse_model->get_item_tag_filter();
		$data['commodity_filter'] = $this->warehouse_model->get_commodity_active();
		$data['title'] = _l("item_detail");


		$data['commodity_item'] = $commodity_item;
		$data['commodity_file'] = $this->warehouse_model->get_warehourse_attachments($product_id);

		$this->load->view('products/view_product_detail', $data);

	}

	public function table_commodity_list() {
		$this->app->get_table_data(module_views_path('manufacturing', 'products/view_table_product_detail'));
	}

	/**
	 * bom change log table
	 * @return [type] 
	 */
	public function bom_change_log_table()
	{
		$this->app->get_table_data(module_views_path('manufacturing', 'manufacturing_orders/bom_change_logs/bom_change_log_table'));
	}

//end file
}

