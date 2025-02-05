<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Fleet Controller
 */
class Fleet extends AdminController
{
    public function __construct(){
        parent::__construct();
        
        $this->load->model('fleet_model');
        hooks()->do_action('fleet_init');
    }

    /**
     * vehicle
     * @return view
     */
    public function vehicles(){
        $this->required_module();

        if (!has_permission('fleet_vehicle', '', 'view')) {
            access_denied('fleet');
        }

        $data['title']         = _l('vehicle');
        $data['vehicle_types'] = $this->fleet_model->get_data_vehicle_types();
        $data['vehicle_groups'] = $this->fleet_model->get_data_vehicle_groups();

        $this->load->view('vehicles/manage', $data);
    }

    /**
     * setting
     * @return view
     */
    public function settings()
    {
        $this->required_module();
        if (!has_permission('fleet_setting', '', 'view')) {
            access_denied('setting');
        }
        
        $data          = [];
        $data['group'] = $this->input->get('group');

        $data['tab'][] = 'vehicle_groups';
        $data['tab'][] = 'vehicle_types';
        $data['tab'][] = 'inspection_forms';
        $data['tab'][] = 'criterias';
        $data['tab'][] = 'insurance_categories';
        $data['tab'][] = 'insurance_types';
        $data['tab'][] = 'insurance_company';
        $data['tab'][] = 'insurance_status';
        $data['tab'][] = 'part_types';
        $data['tab'][] = 'part_groups';
        
        $data['tab_2'] = $this->input->get('tab');
        if ($data['group'] == '') {
            $data['group'] = 'vehicle_groups';
        }


        $data['title']        = _l($data['group']);
        $data['tabs']['view'] = 'settings/' . $data['group'];
        $this->load->view('settings/manage', $data);
    }

    /**
     * vehicle groups table
     * @return json
     */
    public function vehicle_groups_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                'id',
                'name',
                'addedfrom',
                'datecreated',
            ];

            $where = [];
            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_vehicle_groups';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = $aRow['id'];

                $categoryOutput = $aRow['name'];

                $categoryOutput .= '<div class="row-options">';

                if (has_permission('fleet_setting', '', 'edit')) {
                    $categoryOutput .= '<a href="#" onclick="edit_vehicle_group(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                }

                if (has_permission('fleet_setting', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_vehicle_group/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = get_staff_full_name($aRow['addedfrom']);
                $row[] = _d($aRow['datecreated']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     *
     *  add or edit vehicle group
     *  @param  integer  $id     The identifier
     *  @return view
     */
    public function vehicle_group()
    {
        if (!has_permission('fleet_setting', '', 'edit') && !has_permission('fleet_setting', '', 'create')) {
            access_denied('fleet');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['description'] = $this->input->post('description', false);
            $message = '';
            if ($data['id'] == '') {
                if (!has_permission('fleet_setting', '', 'create')) {
                    access_denied('fleet');
                }
                $success = $this->fleet_model->add_vehicle_group($data);
                if ($success) {
                    $message = _l('added_successfully', _l('vehicle_group'));
                }else {
                    $message = _l('add_failure');
                }
            } else {
                if (!has_permission('fleet_setting', '', 'edit')) {
                    access_denied('fleet');
                }
                $id = $data['id'];
                unset($data['id']);
                $success = $this->fleet_model->update_vehicle_group($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('vehicle_group'));
                }else {
                    $message = _l('updated_fail');
                }
            }

            echo json_encode(['success' => $success, 'message' => $message]);
            die();
        }
    }

    /**
     * delete vehicle group
     * @param  integer $id
     * @return
     */
    public function delete_vehicle_group($id)
    {
        if (!has_permission('fleet_setting', '', 'delete')) {
            access_denied('fleet_setting');
        }
        $success = $this->fleet_model->delete_vehicle_group($id);
        $message = '';
        
        if ($success) {
            $message = _l('deleted', _l('vehicle_group'));
            set_alert('success', $message);
        } else {
            $message = _l('can_not_delete');
            set_alert('warning', $message);
        }
        redirect(admin_url('fleet/settings?group=vehicle_groups'));
    }

    /**
     * get data vehicle group
     * @param  integer $id 
     * @return json     
     */
    public function get_data_vehicle_group($id){
        $vehicle_group = $this->fleet_model->get_data_vehicle_groups($id);

        echo json_encode($vehicle_group);
    }

    /**
     * vehicle types table
     * @return json
     */
    public function vehicle_types_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                'id',
                'name',
                'addedfrom',
                'datecreated',
            ];

            $where = [];
            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_vehicle_types';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = $aRow['id'];

                $categoryOutput = $aRow['name'];

                $categoryOutput .= '<div class="row-options">';

                if (has_permission('fleet_setting', '', 'edit')) {
                    $categoryOutput .= '<a href="#" onclick="edit_vehicle_type(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                }

                if (has_permission('fleet_setting', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_vehicle_type/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = get_staff_full_name($aRow['addedfrom']);
                $row[] = _d($aRow['datecreated']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     *
     *  add or edit vehicle type
     *  @param  integer  $id     The identifier
     *  @return view
     */
    public function vehicle_type()
    {
        if (!has_permission('fleet_setting', '', 'edit') && !has_permission('fleet_setting', '', 'create')) {
            access_denied('fleet');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['description'] = $this->input->post('description', false);
            $message = '';
            if ($data['id'] == '') {
                if (!has_permission('fleet_setting', '', 'create')) {
                    access_denied('fleet');
                }
                $success = $this->fleet_model->add_vehicle_type($data);
                if ($success) {
                    $message = _l('added_successfully', _l('vehicle_type'));
                }else {
                    $message = _l('add_failure');
                }
            } else {
                if (!has_permission('fleet_setting', '', 'edit')) {
                    access_denied('fleet');
                }
                $id = $data['id'];
                unset($data['id']);
                $success = $this->fleet_model->update_vehicle_type($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('vehicle_type'));
                }else {
                    $message = _l('updated_fail');
                }
            }

            echo json_encode(['success' => $success, 'message' => $message]);
            die();
        }
    }

    /**
     * delete vehicle type
     * @param  integer $id
     * @return
     */
    public function delete_vehicle_type($id)
    {
        if (!has_permission('fleet_setting', '', 'delete')) {
            access_denied('fleet_setting');
        }
        $success = $this->fleet_model->delete_vehicle_type($id);
        $message = '';
        
        if ($success) {
            $message = _l('deleted', _l('vehicle_type'));
            set_alert('success', $message);
        } else {
            $message = _l('can_not_delete');
            set_alert('warning', $message);
        }
        redirect(admin_url('fleet/settings?group=vehicle_types'));
    }

    /**
     * get data vehicle type
     * @param  integer $id 
     * @return json     
     */
    public function get_data_vehicle_type($id){
        $vehicle_type = $this->fleet_model->get_data_vehicle_types($id);

        echo json_encode($vehicle_type);
    }

    /**
     * delete vehicle
     * @param  integer $id
     * @return
     */
    public function delete_vehicle($id)
    {
        if (!has_permission('fleet_vehicle', '', 'delete')) {
            access_denied('fleet_vehicle');
        }
        $success = $this->fleet_model->delete_vehicle($id);
        $message = '';
        if ($success) {
            $message = _l('deleted', _l('vehicle'));
            set_alert('success', $message);
        } else {
            $message = _l('can_not_delete');
            set_alert('warning', $message);
        }
        redirect(admin_url('fleet/vehicles'));
    }

    /**
     * vehicles table
     * @return json
     */
    public function vehicles_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                db_prefix() . 'fleet_vehicles.name as vehicle_name',
                db_prefix() . 'fleet_vehicle_types.name as type_name',
                db_prefix() . 'fleet_vehicle_groups.name as group_name',
                'year',
                'make',
                'model',
                'status',
            ];

            $where = [];

            $is_report = $this->input->post("is_report");
            if ($this->input->post('vehicle_type_id')) {
                $vehicle_type_id = $this->input->post('vehicle_type_id');
                array_push($where, 'AND vehicle_type_id = '. $vehicle_type_id);
            }

            if ($this->input->post('vehicle_group_id')) {
                $vehicle_group_id = $this->input->post('vehicle_group_id');
                array_push($where, 'AND vehicle_group_id = '. $vehicle_group_id);
            }

            if ($this->input->post('status')) {
                $status = $this->input->post('status');
                array_push($where, 'AND status = "'. $status.'"');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_vehicles';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'fleet_vehicle_types ON ' . db_prefix() . 'fleet_vehicle_types.id = ' . db_prefix() . 'fleet_vehicles.vehicle_type_id',
                'LEFT JOIN ' . db_prefix() . 'fleet_vehicle_groups ON ' . db_prefix() . 'fleet_vehicle_groups.id = ' . db_prefix() . 'fleet_vehicles.vehicle_group_id',];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'fleet_vehicles.id as id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $categoryOutput = '<a href="' . admin_url('fleet/vehicle/' . $aRow['id']) . '">' . $aRow['vehicle_name'] . '</a>';
                if($is_report == ''){
                    $categoryOutput .= '<div class="row-options">';
                    $categoryOutput .= '<a href="' . admin_url('fleet/vehicle/' . $aRow['id']) . '">' . _l('view') . '</a>';

                    if (has_permission('fleet_vehicle', '', 'edit')) {
                        $categoryOutput .= ' | <a href="' . admin_url('fleet/vehicle/' . $aRow['id']) . '">' . _l('edit') . '</a>';
                    }
                
                    if (has_permission('fleet_vehicle', '', 'delete')) {
                        $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_vehicle/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                    }

                    $categoryOutput .= '</div>';
                }
                $row[] = $categoryOutput;
                $row[] = $aRow['year'];
                $row[] = $aRow['make'];
                $row[] = $aRow['model'];
                $row[] = $aRow['type_name'];
                $row[] = $aRow['group_name'];
                $row[] = _l($aRow['status']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * add or edit driver
     * @return view
     */
    public function driver($id = ''){
        if ($this->input->post()) {
            $staffid                = $this->input->post('staff');

            if($staffid != ''){
                if (!has_permission('fleet_driver', '', 'create')) {
                    access_denied('fleet_driver');
                }

                $success = $this->fleet_model->add_driver($staffid);
                if ($success) {
                    set_alert('success', _l('added_successfully', _l('driver')));

                    redirect(admin_url('fleet/driver_detail/'.$staffid));
                }
            }
        }

        redirect(admin_url('fleet/drivers'));
    }

    /**
     * delete driver
     * @param  integer $id
     * @return
     */
    public function delete_driver($id)
    {
        if (!has_permission('fleet_driver', '', 'delete')) {
            access_denied('fleet_driver');
        }
        $success = $this->fleet_model->delete_driver($id);
        $message = '';
        if ($success) {
            $message = _l('deleted', _l('driver'));
            set_alert('success', $message);
        } else {
            $message = _l('can_not_delete');
            set_alert('warning', $message);
        }
        redirect(admin_url('fleet/drivers'));
    }

    /**
     * driver
     * @return view
     */
    public function drivers(){
        $this->required_module();
        if (!has_permission('fleet_driver', '', 'view')) {
            access_denied('fleet');
        }

        $data['title']         = _l('driver');
        $data['staffs']         = $this->staff_model->get();

        $this->load->view('drivers/manage', $data);
    }


    public function drivers_table(){
        $has_permission_delete = has_permission('staff', '', 'delete');

        $custom_fields = get_custom_fields('staff', [
            'show_on_table' => 1,
            ]);
        $aColumns = [
            'firstname',
            'email',
            db_prefix() . 'roles.name',
            'last_login',
            'active',
            ];
        $sIndexColumn = 'staffid';
        $sTable       = db_prefix() . 'staff';
        $join         = ['LEFT JOIN ' . db_prefix() . 'roles ON ' . db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role'];
        $i            = 0;
        foreach ($custom_fields as $field) {
            $select_as = 'cvalue_' . $i;
            if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
                $select_as = 'date_picker_cvalue_' . $i;
            }
            array_push($aColumns, 'ctable_' . $i . '.value as ' . $select_as);
            array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $i . ' ON ' . db_prefix() . 'staff.staffid = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
            $i++;
        }
                    // Fix for big queries. Some hosting have max_join_limit
        if (count($custom_fields) > 4) {
            @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
        }

        $role_id = $this->fleet_model->get_fleet_driver_role_id();

        $where =[];
        array_push($where, 'AND role = "'. $role_id.'"');

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'profile_image',
            'lastname',
            'staffid',
            ]);

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];
            for ($i = 0; $i < count($aColumns); $i++) {
                if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                    $_data = $aRow[strafter($aColumns[$i], 'as ')];
                } else {
                    $_data = $aRow[$aColumns[$i]];
                }
                if ($aColumns[$i] == 'last_login') {
                    if ($_data != null) {
                        $_data = '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . _dt($_data) . '">' . time_ago($_data) . '</span>';
                    } else {
                        $_data = 'Never';
                    }
                } elseif ($aColumns[$i] == 'active') {
                    $checked = '';
                    if ($aRow['active'] == 1) {
                        $checked = 'checked';
                    }

                    $_data = '<div class="onoffswitch">
                        <input type="checkbox" ' . (($aRow['staffid'] == get_staff_user_id() || (is_admin($aRow['staffid']) || !has_permission('staff', '', 'edit')) && !is_admin()) ? 'disabled' : '') . ' data-switch-url="' . admin_url() . 'staff/change_staff_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['staffid'] . '" data-id="' . $aRow['staffid'] . '" ' . $checked . '>
                        <label class="onoffswitch-label" for="c_' . $aRow['staffid'] . '"></label>
                    </div>';

                    // For exporting
                    $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
                } elseif ($aColumns[$i] == 'firstname') {
                    $_data = '<a href="' . admin_url('fleet/driver_detail/' . $aRow['staffid']) . '">' . staff_profile_image($aRow['staffid'], [
                        'staff-profile-image-small',
                        ]) . '</a>';
                    $_data .= ' <a href="' . admin_url('fleet/driver_detail/' . $aRow['staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';

                    $_data .= '<div class="row-options">';
                    $_data .= '<a href="' . admin_url('fleet/driver_detail/' . $aRow['staffid']) . '">' . _l('view') . '</a>';

                    if (has_permission('fleet_driver', '', 'delete')) {
                        $_data .= ' | <a href="' . admin_url('fleet/delete_driver/' . $aRow['staffid']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                    }

                    $_data .= '</div>';
                } elseif ($aColumns[$i] == 'email') {
                    $_data = '<a href="mailto:' . $_data . '">' . $_data . '</a>';
                } else {
                    if (strpos($aColumns[$i], 'date_picker_') !== false) {
                        $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
                    }
                }
                $row[] = $_data;
            }

            $row['DT_RowClass'] = 'has-row-options';

            $row = hooks()->apply_filters('staff_table_row', $row, $aRow);

            $output['aaData'][] = $row;
        }

        echo json_encode($output);
        die();
    }

    /**
     * view driver detail
     * @return view
     */
    public function driver_detail($id = ''){
        $this->load->model('departments_model');
        $data['staff_departments'] = $this->departments_model->get_staff_departments($id);

        $data['departments'] = $this->departments_model->get();

        if ($id == '') {
            $title = _l('add_new', _l('client_lowercase'));
        } else {
            $data['driver'] = $this->fleet_model->get_driver($id);

            $group         = !$this->input->get('group') ? 'general_information' : $this->input->get('group');
            $data['group'] = $group;

            $data['tab'][] = ['name' => 'general_information', 'icon' => '<i class="fa fa-user-circle menu-icon"></i>'];
            $data['tab'][] = ['name' => 'vehicle_assignments','icon' => '<i class="fa fa-truck menu-icon"></i>'];
            $data['tab'][] = ['name' => 'driver_documents', 'icon' => '<i class="fa fa-file-text menu-icon"></i>'];
            $data['tab'][] = ['name' => 'training_information', 'icon' => '<i class="fa fa-file-invoice menu-icon"></i>'];
            $data['tab'][] = ['name' => 'benefit_and_penalty', 'icon' => '<i class="fa fa-file-invoice-dollar menu-icon"></i>'];
            $data['tabs']['view'] = 'drivers/groups/'.$data['group'];

            if (!$data['driver']) {
                show_404();
            }

            $title          = $data['driver']->firstname .' '.$data['driver']->lastname;
            $data['vehicles'] = $this->fleet_model->get_vehicle();
            $data['drivers'] = $this->fleet_model->get_driver();

            if ($group == 'training_information') {
                $this->load->model('hr_profile/hr_profile_model');
                $member = $this->hr_profile_model->get_staff($id);
                if (!$member) {
                    blank_page('Staff Member Not Found', 'danger');
                }
                $data['member'] = $member;

                $training_data = [];
                //Onboarding training
                $training_allocation_staff = $this->hr_profile_model->get_training_allocation_staff($id);

                if ($training_allocation_staff != null) {

                    $training_data['list_training_allocation'] = get_object_vars($training_allocation_staff);
                }

                if (isset($training_allocation_staff) && $training_allocation_staff != null) {
                    $training_data['training_allocation_min_point'] = 0;

                    $job_position_training = $this->hr_profile_model->get_job_position_training_de($training_allocation_staff->jp_interview_training_id);

                    if ($job_position_training) {
                        $training_data['training_allocation_min_point'] = $job_position_training->mint_point;
                    }

                    if ($training_allocation_staff) {
                        $training_process_id = $training_allocation_staff->training_process_id;

                        $training_data['list_training'] = $this->hr_profile_model->get_list_position_training_by_id_training($training_process_id);

                        //Get the latest employee's training result.
                        $training_results = $this->get_mark_staff($id, $training_process_id);

                        $training_data['training_program_point'] = $training_results['training_program_point'];
                        $training_data['staff_training_result'] = $training_results['staff_training_result'];

                        //have not done the test data
                        $staff_training_result = [];
                        foreach ($training_data['list_training'] as $key => $value) {
                            $staff_training_result[$value['training_id']] = [
                                'training_name' => $value['subject'],
                                'total_point' => 0,
                                'training_id' => $value['training_id'],
                                'total_question' => 0,
                                'total_question_point' => 0,
                            ];
                        }

                        //did the test
                        if (count($training_results['staff_training_result']) > 0) {

                            foreach ($training_results['staff_training_result'] as $result_key => $result_value) {
                                if (isset($staff_training_result[$result_value['training_id']])) {
                                    unset($staff_training_result[$result_value['training_id']]);
                                }
                            }

                            $training_data['staff_training_result'] = array_merge($training_results['staff_training_result'], $staff_training_result);

                        } else {
                            $training_data['staff_training_result'] = $staff_training_result;
                        }

                        if ((float) $training_results['training_program_point'] >= (float) $training_data['training_allocation_min_point']) {
                            $training_data['complete'] = 0;
                        } else {
                            $training_data['complete'] = 1;
                        }

                    }
                }

                if (count($training_data) > 0) {
                    $data['training_data'][] = $training_data;
                }

                //Additional training
                $additional_trainings = $this->hr_profile_model->get_additional_training($id);

                foreach ($additional_trainings as $key => $value) {
                    $training_temp = [];

                    $training_temp['training_allocation_min_point'] = $value['mint_point'];
                    $training_temp['list_training_allocation'] = $value;
                    $training_temp['list_training'] = $this->hr_profile_model->get_list_position_training_by_id_training($value['position_training_id']);

                    //Get the latest employee's training result.
                    $training_results = $this->get_mark_staff($id, $value['position_training_id']);

                    $training_temp['training_program_point'] = $training_results['training_program_point'];
                    $training_temp['staff_training_result'] = $training_results['staff_training_result'];

                    //have not done the test data
                    $staff_training_result = [];
                    foreach ($training_temp['list_training'] as $key => $value) {
                        $staff_training_result[$value['training_id']] = [
                            'training_name' => $value['subject'],
                            'total_point' => 0,
                            'training_id' => $value['training_id'],
                            'total_question' => 0,
                            'total_question_point' => 0,
                        ];
                    }

                    //did the test
                    if (count($training_results['staff_training_result']) > 0) {

                        foreach ($training_results['staff_training_result'] as $result_key => $result_value) {
                            if (isset($staff_training_result[$result_value['training_id']])) {
                                unset($staff_training_result[$result_value['training_id']]);
                            }
                        }

                        $training_temp['staff_training_result'] = array_merge($training_results['staff_training_result'], $staff_training_result);

                    } else {
                        $training_temp['staff_training_result'] = $staff_training_result;
                    }

                    if ((float) $training_results['training_program_point'] >= (float) $training_temp['training_allocation_min_point']) {
                        $training_temp['complete'] = 0;
                    } else {
                        $training_temp['complete'] = 1;
                    }

                    if (count($training_temp) > 0) {
                        $data['training_data'][] = $training_temp;
                    }

                }

            }
        }


        $data['criterias'] = $this->fleet_model->get_criterias();


        $data['title'] = $title;
        
        $this->load->view('drivers/driver_detail', $data);
    }

    /* Edit client or add new client*/
    public function vehicle($id = '')
    {
        if (!has_permission('fleet_vehicle', '', 'view')) {
            access_denied('fleet');
        }

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('fleet_vehicle', '', 'create')) {
                    access_denied('fleet');
                }

                $data = $this->input->post();

                $id = $this->fleet_model->add_vehicle($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('vehicle')));
                    redirect(admin_url('fleet/vehicle/' . $id));
                }
            } else {
                if (!has_permission('fleet_vehicle', '', 'edit')) {
                    access_denied('fleet');
                }
                $success = $this->fleet_model->update_vehicle($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfully', _l('vehicle')));
                }
                redirect(admin_url('fleet/vehicle/' . $id));
            }
        }

        $group         = !$this->input->get('group') ? 'details' : $this->input->get('group');
        $data['group'] = $group;

        if ($id == '') {
            $title = _l('add_new', _l('vehicle'));
        } else {
            $vehicle                = $this->fleet_model->get_vehicle($id);
            $data['vehicle_tabs'] = [];
            $data['vehicle_tabs']['details'] = ['name' => 'details', 'icon' => '<i class="fa fa-user-circle menu-icon"></i>'];
            $data['vehicle_tabs']['maintenance'] = ['name' => 'maintenance','icon' => '<i class="fa fa-users menu-icon"></i>'];
            $data['vehicle_tabs']['lifecycle'] = ['name' => 'lifecycle','icon' => '<i class="fa fa-file-powerpoint menu-icon"></i>'];
            $data['vehicle_tabs']['financial'] = ['name' => 'financial', 'icon' => '<i class="fa fa-file-text menu-icon"></i>'];
            $data['vehicle_tabs']['specifications'] = ['name' => 'specifications', 'icon' => '<i class="fa fa-cart-plus menu-icon"></i>'];
            $data['vehicle_tabs']['assignment_history'] = ['name' => 'assignment_history', 'icon' => '<i class="fa fa-history menu-icon"></i>'];
            $data['vehicle_tabs']['fuel_history'] = ['name' => 'fuel_history', 'icon' => '<i class="fa fa-gas-pump menu-icon"></i>'];
            $data['vehicle_tabs']['insurances'] = ['name' => 'insurances', 'icon' => '<i class="fa fa-file-text menu-icon"></i>'];
            $data['vehicle_tabs']['inspections'] = ['name' => 'inspections', 'icon' => '<i class="fa fa-file-text menu-icon"></i>'];
            $data['vehicle_tabs']['vehicle_document'] = ['name' => 'vehicle_document', 'icon' => '<i class="fa fa-file-invoice menu-icon"></i>'];
            $data['vehicle_tabs']['parts'] = ['name' => 'parts', 'icon' => '<i class="fa fa-newspaper menu-icon"></i>'];
            $data['vehicle_tabs']['reminders'] = ['name' => 'reminders', 'icon' => '<i class="fa fa-clock menu-icon"></i>'];

            if (!$vehicle) {
                show_404();
            }

            $data['contacts'] = $this->clients_model->get_contacts($id);
            $data['tab']      = isset($data['vehicle_tabs'][$group]) ? $data['vehicle_tabs'][$group] : null;
            $data['tab']['view'] = 'vehicles/groups/'.$data['group'];

            if (!$data['tab']) {
                show_404();
            }

            // Fetch data based on groups
            if ($group == 'details') {
               
            } elseif ($group == 'financial') {
                $data['vendors'] = $this->fleet_model->get_vendor();
            } elseif ($group == 'vault') {
                $data['vault_entries'] = hooks()->apply_filters('check_vault_entries_visibility', $this->clients_model->get_vault_entries($id));

                if ($data['vault_entries'] === -1) {
                    $data['vault_entries'] = [];
                }
            } elseif ($group == 'fuel_history') {
                $data['vehicles'] = $this->fleet_model->get_vehicle();
                $data['vendors'] = $this->fleet_model->get_vendor();
                $data['fuel_consumption'] = $this->fleet_model->calculating_fuel_consumption($id);
            } elseif ($group == 'inspections') {
                $data['vehicles'] = $this->fleet_model->get_vehicle();
                $data['inspection_forms'] = $this->fleet_model->get_inspection_forms();
            } elseif ($group == 'assignment_history') {
                $data['vehicles'] = $this->fleet_model->get_vehicle();
                $data['drivers'] = $this->fleet_model->get_driver();
            } elseif ($group == 'maintenance') {
                $data['vehicles'] = $this->fleet_model->get_vehicle();
                $data['garages'] = $this->fleet_model->get_garages();
                $data['parts'] = $this->fleet_model->get_part();

                $this->load->model('currencies_model');
                $base_currency = $this->currencies_model->get_base_currency();
                $data['currency_name'] = '';
                if(isset($base_currency)){
                    $data['currency_name'] = $base_currency->name;
                } 
            } elseif ($group == 'insurances') {
                $data['insurance_status'] = $this->fleet_model->get_data_insurance_status();
                $data['insurance_company'] = $this->fleet_model->get_data_insurance_company();
                $data['insurance_categorys'] = $this->fleet_model->get_insurance_category();;
                $data['insurance_types'] = $this->fleet_model->get_insurance_type();;
                $data['vehicles'] = $this->fleet_model->get_vehicle();
            } elseif($group == 'parts'){
                $data['part_types'] = $this->fleet_model->get_data_part_types();
                $data['part_groups'] = $this->fleet_model->get_data_part_groups();
            } elseif ($group == 'map') {
                if (get_option('google_api_key') != '' && !empty($client->latitude) && !empty($client->longitude)) {
                    $this->app_scripts->add('map-js', base_url($this->app_scripts->core_file('assets/js', 'map.js')) . '?v=' . $this->app_css->core_version());

                    $this->app_scripts->add('google-maps-api-js', [
                        'path'       => 'https://maps.googleapis.com/maps/api/js?key=' . get_option('google_api_key') . '&callback=initMap',
                        'attributes' => [
                            'async',
                            'defer',
                            'latitude'       => "$client->latitude",
                            'longitude'      => "$client->longitude",
                            'mapMarkerTitle' => "$client->company",
                        ],
                        ]);
                }
            }

            $data['staff'] = $this->staff_model->get('', ['active' => 1]);

            $data['vehicle'] = $vehicle;
            $title          = $vehicle->name;

            // Get all active staff members (used to add reminder)
            $data['members'] = $data['staff'];

            if (!empty($data['client']->company)) {
                // Check if is realy empty client company so we can set this field to empty
                // The query where fetch the client auto populate firstname and lastname if company is empty
                if (is_empty_customer_company($data['client']->userid)) {
                    $data['client']->company = '';
                }
            }
        }

        $data['vehicle_types'] = $this->fleet_model->get_data_vehicle_types();
        $data['vehicle_groups'] = $this->fleet_model->get_data_vehicle_groups();
        $data['bodyclass'] = 'customer-profile dynamic-create-groups';
        $data['title']     = $title;

        $this->load->view('vehicles/vehicle', $data);
    }

    /**
     * { vehicle_detail }
     */
    public function vehicle_detail($id = ''){


        $group         = !$this->input->get('group') ? 'general_information' : $this->input->get('group');
        $data['group'] = $group;

        $data['tab'][] = ['name' => 'general_information', 'icon' => '<i class="fa fa-user-circle menu-icon"></i>'];
        $data['tab'][] = ['name' => 'maintenance','icon' => '<i class="fa fa-users menu-icon"></i>'];
        $data['tab'][] = ['name' => 'lifecycle','icon' => '<i class="fa fa-file-powerpoint menu-icon"></i>'];
        $data['tab'][] = ['name' => 'financial', 'icon' => '<i class="fa fa-file-text menu-icon"></i>'];
        $data['tab'][] = ['name' => 'specifications', 'icon' => '<i class="fa fa-cart-plus menu-icon"></i>'];
        $data['tab'][] = ['name' => 'assignment_history', 'icon' => '<i class="fa fa-history menu-icon"></i>'];
        $data['tab'][] = ['name' => 'fuel_history', 'icon' => '<i class="fa fa-gas-pump menu-icon"></i>'];
        $data['tab'][] = ['name' => 'inspections', 'icon' => '<i class="fa fa-file-text menu-icon"></i>'];
        $data['tab'][] = ['name' => 'settings', 'icon' => '<i class="fa fa-paperclip menu-icon"></i>'];

        if($data['group'] == ''){
            $data['group'] = 'general_information';
        }
        $data['tabs']['view'] = 'vehicles/groups/'.$data['group'];

        $data['vehicle'] = $this->fleet_model->get_vehicle($id);

        $data['title']     = $data['vehicle']->name;
        $data['vehicle_types'] = $this->fleet_model->get_data_vehicle_types();
        $data['vehicle_groups'] = $this->fleet_model->get_data_vehicle_groups();
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $data['drivers'] = $this->fleet_model->get_driver();
        $data['vendors'] = $this->fleet_model->get_vendor();
        $data['inspection_forms'] = $this->fleet_model->get_inspection_forms();

        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();
        $data['currency_name'] = '';
        if(isset($base_currency)){
            $data['currency_name'] = $base_currency->name;
        } 

        $this->load->view('vehicles/vehicle_detail', $data);
    }
    
    /* Edit driver document or add new driver document */
    public function driver_document($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['description'] = html_purify($this->input->post('description', false));
            if ($id == '') {

                $id = $this->fleet_model->add_driver_document($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('document')));
                    echo json_encode([
                        'url'       => admin_url('fleet/driver_document/' . $id),
                        'id' => $id,
                        'driver_id' => $data['driver_id'],
                        'vehicle_id' => $data['vehicle_id'],
                    ]);
                    die;
                }
            } else {

                $success = $this->fleet_model->update_driver_document($data, $id);

                if($data['driver_id'] != 0){
                    $url = admin_url('fleet/driver_detail/' . $data['driver_id'].'?group=driver_documents');
                }else{
                    $url = admin_url('fleet/vehicle/' . $data['vehicle_id'].'?group=vehicle_document');
                }

                echo json_encode([
                    'url'       => $url,
                    'id' => $id,
                    'driver_id' => $data['driver_id'],
                    'vehicle_id' => $data['vehicle_id'],
                ]);
                die;
            }
        }

        $data['driver_id'] = $this->input->get('driver_id');

        $data['vehicle_id'] = $this->input->get('vehicle_id');

        if ($id == '') {
            $title = _l('add_new', _l('document'));
        } else {
            $data['driver_document']                 = $this->fleet_model->get_driver_document($id, [], true);

            $title = $data['driver_document']->subject;
            $data['driver_id'] = $data['driver_document']->driver_id;
            $data['vehicle_id'] = $data['driver_document']->vehicle_id;

        }



        $data['title']         = $title;
        $this->load->view('driver_documents/driver_document', $data);
    }

    /**
     * Adds an driver document attachment.
     *
     * @param        $id     The identifier
     */
    public function add_driver_document_attachment($id)
    {
        $driver_id = $this->input->get('driver_id');
        $vehicle_id = $this->input->get('vehicle_id');
        handle_driver_document_attachments($id);

        if($driver_id != '' && $driver_id != 0){
            $url = admin_url('fleet/driver_detail/' . $driver_id.'?group=driver_documents');
        }else{
            $url = admin_url('fleet/vehicle/' . $vehicle_id.'?group=vehicle_document');
        }
        echo json_encode([
            'url' => $url
        ]);
        die;
    }

    /**
     * driver documents table
     * @return json
     */
    public function driver_documents_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                'id',
                'subject',
                'addedfrom',
                'datecreated',
            ];

            $where = [];
            $rel_id = '';
            $rel_type = '';

            $driverid = $this->input->post("driverid");
            if($driverid != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_driver_documents.driver_id ="'.$driverid.'"');
                $rel_id = $driverid;
                $rel_type = 'driver';
            }

            $vehicleid = $this->input->post("vehicleid");
            if($vehicleid != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_driver_documents.vehicle_id ="'.$vehicleid.'"');
                $rel_id = $vehicleid;
                $rel_type = 'vehicle';
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_driver_documents';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = $aRow['id'];

                $categoryOutput = $aRow['subject'];

                $categoryOutput .= '<div class="row-options">';
                $categoryOutput .= '<a href="' . admin_url('fleet/view_driver_documents/' . $aRow['id']) . '">' . _l('view') . '</a>';

                if (has_permission('fleet_vehicle', '', 'edit')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/driver_document/' . $aRow['id']) . '">' . _l('edit') . '</a>';
                }
            
                if (has_permission('fleet_vehicle', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_driver_document/' . $aRow['id'].'/'.$rel_id.'/'.$rel_type) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;

                $row[] = get_staff_full_name($aRow['addedfrom']);
                $row[] = _d($aRow['datecreated']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * add vehicle assignment
     * @return json
     */
    public function vehicle_assignment(){
        $data = $this->input->post();
        if($data['id'] == ''){
            if (!has_permission('fleet_vehicle_assignment', '', 'create')) {
                access_denied('fleet');
            }
            $success = $this->fleet_model->add_vehicle_assignment($data);
            if($success){
                $message = _l('added_successfully');

                $vehicle = $this->fleet_model->get_vehicle($data['vehicle_id']);

                if($data['driver_id'] != get_staff_user_id()){
                    add_notification([
                            'description'     => 'not_vehicle_assigned_to_you',
                            'touserid'        => $data['driver_id'],
                            'link'            => 'fleet/vehicle/' . $data['vehicle_id'],
                            'additional_data' => serialize([
                                $vehicle->name,
                            ]),
                        ]);
                }
            }else {
                $message = _l('vehicle_assignment_failed');
            }
        }else{
            if (!has_permission('fleet_vehicle_assignment', '', 'edit')) {
                access_denied('fleet');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->fleet_model->update_vehicle_assignment($data, $id);
            if ($success) {
                $message = _l('updated_successfully', _l('vehicle_assignment'));
            }
        }
        
        echo json_encode(['success' => $success, 'message' => $message]);
        die();
    }
    
    /**
     * vehicle assignments table
     * @return json
     */
    public function vehicle_assignments_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                db_prefix() . 'fleet_vehicles.name as vehicle_name',
                'driver_id',
                'start_time',
                'end_time',
                'starting_odometer',
                'ending_odometer',
                db_prefix() . 'fleet_vehicle_assignments.addedfrom as addedfrom',
            ];

            $where = [];
            $rel_id = '';
            $rel_type = '';

            $is_report = $this->input->post("is_report");
            $vehicle_id = $this->input->post("id");
            if($vehicle_id != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_vehicle_assignments.vehicle_id ="'.$vehicle_id.'"');
                $rel_id = $vehicle_id;
                $rel_type = 'vehicle';
            }

            $driverid = $this->input->post("driverid");
            if($driverid != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_vehicle_assignments.driver_id ="'.$driverid.'"');
                $rel_id = $driverid;
                $rel_type = 'driver';
            }


            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_vehicle_assignments';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'fleet_vehicles ON ' . db_prefix() . 'fleet_vehicles.id = ' . db_prefix() . 'fleet_vehicle_assignments.vehicle_id',
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'fleet_vehicle_assignments.id as id', 'vehicle_id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $categoryOutput = '<a href="' . admin_url('fleet/vehicle/' . $aRow['vehicle_id']) . '">'.$aRow['vehicle_name'].'</a>';

                if ($is_report == '') {
                    $categoryOutput .= '<div class="row-options">';
                    if (has_permission('fleet_vehicle', '', 'edit')) {
                        $categoryOutput .= '<a href="#" onclick="edit_vehicle_assignment(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                    }
                
                    if (has_permission('fleet_vehicle', '', 'delete')) {
                        $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_vehicle_assignment/' . $aRow['id'].'/'. $rel_id.'/'. $rel_type) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                    }

                    $categoryOutput .= '</div>';
                }

                $row[] = $categoryOutput;

                $row[] = get_staff_full_name($aRow['driver_id']);
                $row[] = _d($aRow['start_time']);
                $row[] = _d($aRow['end_time']);
                $row[] = $aRow['starting_odometer'];
                $row[] = $aRow['ending_odometer'];
                $row[] = get_staff_full_name($aRow['addedfrom']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
    * mantanances
    */
    public function maintenances(){
        $this->required_module();
        if ($this->input->post()) {
            $data  = $this->input->post();
            $insert_id = 0;
            if($data['id'] == ''){
                unset($data['id']);
                $insert_id = $this->fleet_model->add_maintenances($data);
                if($insert_id > 0){
                    set_alert('success', _l('added_successfully', _l('maintenances')));
                }
                else{
                    set_alert('danger', _l('added_fail', _l('maintenances')));                 
                }
            }
            else
            {
                $result = $this->fleet_model->update_maintenances($data);
                if($result == true){
                    set_alert('success', _l('updated_successfully', _l('maintenances')));
                }
                else{
                    set_alert('danger', _l('no_data_changes', _l('maintenances')));                    
                }
            }
            $redirect = $this->input->get('redirect');
            if($redirect != ''){
                $rel_type = $this->input->get('rel_type');
                $rel_id = $this->input->get('rel_id');
                if($rel_type != '' && is_numeric($rel_id)){
                    if($rel_type == 'audit'){
                        $this->fleet_model->update_audit_detail_item($data['asset_id'], $rel_id, ['maintenance_id' => $insert_id]);
                    }
                    if($rel_type == 'cart_detailt'){
                        $this->fleet_model->update_cart_detail($rel_id, ['maintenance_id' => $insert_id]);
                    }
                }
                redirect(admin_url($redirect));
            }
            else{
                redirect(admin_url('fleet/maintenances'));         
            }
        }

        $data['title']    = _l('maintenances');
        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();
        $data['currency_name'] = '';
        if(isset($base_currency)){
            $data['currency_name'] = $base_currency->name;
        }

        $data['garages'] = $this->fleet_model->get_garages();
        $data['vendors'] = $this->fleet_model->get_vendor();
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $data['parts'] = $this->fleet_model->get_part();
        $this->load->view('maintenances/manage', $data);
    }

    /**
    * maintenances table
    * @return json 
    */
    public function maintenances_table(){
        if ($this->input->is_ajax_request()) {
            if($this->input->post()){

                $this->load->model('currencies_model');
                $base_currency = $this->currencies_model->get_base_currency();
                $currency_name = '';
                if(isset($base_currency)){
                    $currency_name = $base_currency->name;
                }

                $select = [
                    db_prefix() . 'fleet_maintenances.id as id',
                    db_prefix() . 'fleet_vehicles.name as vehicle_name',
                    'maintenance_type',
                    'title',
                    'start_date',
                    'completion_date',
                    'cost',
                ];


                $where        = [];


                $aColumns     = $select;
                $sIndexColumn = 'id';
                $sTable       = db_prefix() . 'fleet_maintenances';
                $join         = [
                    'LEFT JOIN ' . db_prefix() . 'fleet_vehicles ON ' . db_prefix() . 'fleet_vehicles.id = ' . db_prefix() . 'fleet_maintenances.vehicle_id',
                ];

                $is_report = $this->input->post("is_report");
                $vehicle_id = $this->input->post("id");
                if($vehicle_id != ''){
                    array_push($where, ' AND '.db_prefix() . 'fleet_maintenances.vehicle_id ="'.$vehicle_id.'"');
                }

                $garage_id = $this->input->post("garage_id");
                if($garage_id != ''){
                    $is_report = 1;
                    array_push($where, ' AND '.db_prefix() . 'fleet_maintenances.garage_id ="'.$garage_id.'"');
                }

                $maintenance_type = $this->input->post("maintenance_type");
                $from_date = $this->input->post("from_date");
                $to_date = $this->input->post("to_date");

                if($maintenance_type != ''){
                    array_push($where, ' AND maintenance_type = "'.$maintenance_type.'"');
                }
                if($from_date != '' && $to_date == ''){
                    $from_date = fe_format_date($from_date);
                    array_push($where, ' AND date(start_date)="'.$from_date.'"');
                }
                if($from_date != '' && $to_date != ''){
                    $from_date = fe_format_date($from_date);
                    $to_date = fe_format_date($to_date);
                    array_push($where, ' AND date(start_date) between "'.$from_date.'" AND "'.$to_date.'"');
                }

                $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'fleet_maintenances.notes as notes', 'supplier_id'
                    
                ]);

                $output  = $result['output'];
                $rResult = $result['rResult'];  
                foreach ($rResult as $aRow) {
                    $row = [];
                    $row[] = $aRow['id'];

                    $categoryOutput = '<a href="' . admin_url('fleet/maintenance_detail/' . $aRow['id']) . '">'.$aRow['vehicle_name'].'</a>';

                    if ($is_report == '') {
                        $categoryOutput .= '<div class="row-options">';

                        $categoryOutput .= '<a href="'.admin_url('fleet/maintenance_detail/'.$aRow['id'].'').'">' . _l('view') . '</a>';

                        if (has_permission('fleet_maintenance', '', 'edit')) {
                            $categoryOutput .= ' | <a href="#" onclick="edit_maintenances(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                        }

                        if (has_permission('fleet_maintenance', '', 'delete')) {
                            $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_maintenances/' . $aRow['id'].'/'.$vehicle_id) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                        }

                        $categoryOutput .= '</div>';
                    }

                   
                    $row[] = $categoryOutput;  
                    $row[] = _l('fe_'.$aRow['maintenance_type']);  
                    $row[] = '<span class="text-nowrap">'.$aRow['title'].'</span>';  
                    $row[] = '<span class="text-nowrap">'._d($aRow['start_date']).'</span>';  
                    $row[] = '<span class="text-nowrap">'._d($aRow['completion_date']).'</span>';   
                    $row[] = $aRow['notes']; 
                    $row[] = app_format_money($aRow['cost'], $currency_name);  

                    $output['aaData'][] = $row;                                      
                }

                echo json_encode($output);
                die();
            }
        }
    }

    /**
    * get data maintenances
    * @param  integer $id 
    */
    public function get_data_maintenances($id){
        $data_assets = $this->fleet_model->get_maintenances($id);
        if($data_assets){
            $data_assets->completion_date = _d($data_assets->completion_date);
            $data_assets->start_date = _d($data_assets->start_date);
            $data_assets->cost = app_format_money($data_assets->cost,'');
        }
        echo json_encode($data_assets);
        die;
    }

    /**
    * delete maintenances
    * @param  integer $id 
    */
    public function delete_maintenances($id, $vehicle_id = ''){
        if($id != ''){
            $result =  $this->fleet_model->delete_maintenances($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('maintenances')));
            }
            else{
                set_alert('danger', _l('deleted_fail', _l('maintenances')));                 
            }
        }

        if ($vehicle_id != '') {
            redirect(admin_url('fleet/vehicle/'.$vehicle_id.'?group=maintenance'));
        }

        redirect(admin_url('fleet/maintenances'));
    }

    /**
    * garages
    */
    public function garages(){
        $this->required_module();
        if ($this->input->post()) {
            $data  = $this->input->post();
            $insert_id = 0;
            if($data['id'] == ''){
                unset($data['id']);
                $insert_id = $this->fleet_model->add_garages($data);
                if($insert_id > 0){
                    set_alert('success', _l('added_successfully', _l('garages')));
                }
                else{
                    set_alert('danger', _l('added_fail', _l('garages')));                 
                }
            }
            else
            {
                $result = $this->fleet_model->update_garages($data);
                if($result == true){
                    set_alert('success', _l('updated_successfully', _l('garages')));
                }
                else{
                    set_alert('danger', _l('no_data_changes', _l('garages')));                    
                }
            }
            $redirect = $this->input->get('redirect');
            if($redirect != ''){
                $rel_type = $this->input->get('rel_type');
                $rel_id = $this->input->get('rel_id');
                if($rel_type != '' && is_numeric($rel_id)){
                    if($rel_type == 'audit'){
                        $this->fleet_model->update_audit_detail_item($data['asset_id'], $rel_id, ['maintenance_id' => $insert_id]);
                    }
                    if($rel_type == 'cart_detailt'){
                        $this->fleet_model->update_cart_detail($rel_id, ['maintenance_id' => $insert_id]);
                    }
                }
                redirect(admin_url($redirect));
            }
            else{
                redirect(admin_url('fleet/garages'));         
            }
        }

        $data['title']    = _l('garages');
        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();
        $data['currency_name'] = '';
        if(isset($base_currency)){
            $data['currency_name'] = $base_currency->name;
        }
        $data['vendors'] = $this->fleet_model->get_vendor();
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $this->load->view('garages/manage', $data);
    }

    /**
    * garages table
    * @return json 
    */
    public function garages_table(){
        if ($this->input->is_ajax_request()) {
            if($this->input->post()){

                $this->load->model('currencies_model');
                $base_currency = $this->currencies_model->get_base_currency();
                $currency_name = '';
                if(isset($base_currency)){
                    $currency_name = $base_currency->name;
                }

                $select = [
                    'id',
                    'name',
                    'address',
                    'country',
                    'city',
                    'zip',
                    'state',
                    'notes'
                ];

                $where        = [];
                $aColumns     = $select;
                $sIndexColumn = 'id';
                $sTable       = db_prefix() . 'fleet_garages';
                $join         = [
                ];

                $maintenance_type = $this->input->post("maintenance_type");

                if($maintenance_type != ''){
                    array_push($where, ' AND maintenance_type = "'.$maintenance_type.'"');
                }

                $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

                $output  = $result['output'];
                $rResult = $result['rResult'];  
                foreach ($rResult as $aRow) {
                    $row = [];
                    $row[] = $aRow['id'];

                    $_data = '';
                    $_data .= '<div class="row-options">';
                    $_data .= '<a href="'.admin_url('fleet/garage_detail/'.$aRow['id'].'').'">' . _l('view') . '</a>';

                    if(is_admin() || has_permission('fleet_garage', '', 'edit')){
                        $_data .= ' | <a href="javascript:void(0)" onclick="edit('.$aRow['id'].'); return false;">' . _l('edit') . '</a>';
                    }
                    if(is_admin() || has_permission('fleet_garage', '', 'delete')){
                        $_data .= ' | <a href="'.admin_url('fleet/delete_garages/'.$aRow['id'].'').'" class="text-danger _delete">' . _l('fe_delete') . '</a>';
                    }

                    $_data .= '</div>'; 
                    $row[] = '<span class="text-nowrap">'.$aRow['name'].'</span>'.$_data;  
                    $row[] = $aRow['address']; 
                    $country_name = '';
                    if ($aRow['country'] != '') {
                        $country = get_country($aRow['country']);
                        if ($country->short_name) {
                            $country_name = $country->short_name;
                        }
                    }
                    $row[] = $country_name;
                
                    $row[] = $aRow['city']; 
                    $row[] = $aRow['zip']; 
                    $row[] = $aRow['state']; 
                    $row[] = $aRow['notes']; 

                    $output['aaData'][] = $row;                                      
                }

                echo json_encode($output);
                die();
            }
        }
    }

    /**
    * get data garages
    * @param  integer $id 
    */
    public function get_data_garages($id){
        $data_garages = $this->fleet_model->get_garages($id);
       
        echo json_encode($data_garages);
        die;
    }

    /**
    * delete garages
    * @param  integer $id 
    */
    public function delete_garages($id){
        if($id != ''){
            $result =  $this->fleet_model->delete_garages($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('garages')));
            }
            else{
                set_alert('danger', _l('deleted_fail', _l('garages')));                 
            }
        }
        redirect(admin_url('fleet/garages'));
    }

    /**
     * fuels
     * @return view
     */
    public function fuels(){

        $this->required_module();
        if (!has_permission('fleet_fuel', '', 'view')) {
            access_denied('fleet');
        }

        $data['title']         = _l('fuels');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $data['vendors'] = $this->fleet_model->get_vendor();

        $this->load->view('fuels/manage', $data);
    }

    /**
     * fuel history table
     * @return json
     */
    public function fuel_history_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $select = [
                db_prefix() . 'fleet_vehicles.name as vehicle_name',
                'fuel_time',
                'vendor_id',
                db_prefix() . 'fleet_fuel_history.odometer as odometer',
                'gallons',
                'price',
            ];

            $where = [];

            $is_report = $this->input->post("is_report");

            $vehicle_id = $this->input->post("id");
            if($vehicle_id != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_fuel_history.vehicle_id ="'.$vehicle_id.'"');
            }



            $fuel_type = $this->input->post("fuel_type");
            if($fuel_type != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_fuel_history.fuel_type ="'.$fuel_type.'"');
            }

            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                if (!$this->fleet_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                if (!$this->fleet_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (fuel_time >= "' . $from_date . '" and fuel_time <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (fuel_time >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (fuel_time <= "' . $to_date . '")');
            }
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_fuel_history';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'fleet_vehicles ON ' . db_prefix() . 'fleet_vehicles.id = ' . db_prefix() . 'fleet_fuel_history.vehicle_id',
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['vehicle_id', db_prefix() . 'fleet_fuel_history.id as id',]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = '<a href="' . admin_url('fleet/vehicle/' . $aRow['vehicle_id']) . '">'.$aRow['vehicle_name'].'</a>';

                if ($is_report == '') {
                    $categoryOutput .= '<div class="row-options">';

                    if (has_permission('fleet_fuel', '', 'edit')) {
                        $categoryOutput .= '<a href="#" onclick="edit_fuel(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                    }

                    if (has_permission('fleet_fuel', '', 'delete')) {
                        $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_fuel/' . $aRow['id'].'/'.$vehicle_id) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                    }

                    $categoryOutput .= '</div>';
                }
                $row[] = $categoryOutput;
                $row[] = _dt($aRow['fuel_time']);
                $row[] = get_vendor_company_name($aRow['vendor_id']);
                $row[] = $aRow['odometer'] != null ? number_format($aRow['odometer']) : '';
                $row[] = $aRow['gallons'] != null ? number_format($aRow['gallons']) : '';
                $row[] = app_format_money($aRow['price'], $currency->name);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * add fuel
     * @return json
     */
    public function add_fuel(){
        $data = $this->input->post();
        if($data['id'] == ''){
            if (!has_permission('fleet_fuel', '', 'create')) {
                access_denied('fleet');
            }
            $success = $this->fleet_model->add_fuel_history($data);
            if($success){
                $message = _l('added_successfully');
            }else {
                $message = _l('fuel_failed');
            }
        }else{
            if (!has_permission('fleet_fuel', '', 'edit')) {
                access_denied('fleet');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->fleet_model->update_fuel_history($data, $id);
            if ($success) {
                $message = _l('updated_successfully', _l('fuel'));
            }
        }
        echo json_encode(['success' => $success, 'message' => $message]);
        die();
    }

    /**
    * get data fuel
    * @param  integer $id 
    */
    public function get_data_fuel($id){
        $data_garages = $this->fleet_model->get_fuel_history($id);
       
        echo json_encode($data_garages);
        die;
    }

    /**
    * delete fuel
    * @param  integer $id 
    */
    public function delete_fuel($id){
        if($id != ''){
            $result =  $this->fleet_model->delete_fuel_history($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('fuels')));
            }
            else{
                set_alert('danger', _l('deleted_fail', _l('fuels')));                 
            }
        }
        redirect(admin_url('fleet/fuels'));
    }

    /**
     * inspections
     * @return view
     */
    public function inspections(){
        $this->required_module();
        if (!has_permission('fleet_inspection', '', 'view')) {
            access_denied('fleet');
        }

        $data['title']         = _l('inspections');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $data['inspection_forms'] = $this->fleet_model->get_inspection_forms();

        $this->load->view('inspections/manage', $data);
    }
    
    /**
     * Add new inspection form or update existing
     * @param integer id
     */
    public function inspection_form($id = '') {
        if (!has_permission('fleet_setting', '', 'view')) {
            access_denied('inspection_form');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['description'] = $this->input->post('description', false);

            if ($id == '') {
                if (!has_permission('fleet_setting', '', 'create')) {
                    access_denied('inspection_form');
                }
                $id = $this->fleet_model->add_inspection_form($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('inspection_form')));
                    redirect(admin_url('fleet/inspection_form/' . $id));
                }
            } else {
                if (!has_permission('fleet_setting', '', 'edit')) {
                    access_denied('inspection_form');
                }
                $success = $this->fleet_model->update_inspection_form($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('inspection_form')));
                }
                redirect(admin_url('fleet/inspection_form/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('inspection_form'));
        } else {
            $inspection_form = $this->fleet_model->get_inspection_form($id);
            $data['inspection_form'] = $inspection_form;
            $title = $inspection_form->name;
        }
        if (is_gdpr() && (get_option('gdpr_enable_consent_for_contacts') == '1' || get_option('gdpr_enable_consent_for_leads') == '1')) {
            $this->load->model('gdpr_model');
            $data['purposes'] = $this->gdpr_model->get_consent_purposes();
        }
        $data['title'] = $title;
        $this->app_scripts->add('surveys-js', module_dir_url('surveys', 'assets/js/surveys.js'), 'admin', ['app-js']);

        $this->load->view('settings/inspection_form', $data);
    }

    /* New inspection form question */
    public function add_inspection_question_form() {
        if (!has_permission('staffmanage_training', '', 'edit') && !has_permission('staffmanage_training', '', 'create')) {
            echo json_encode([
                'success' => false,
                'message' => _l('access_denied'),
            ]);
            die();
        }
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                echo json_encode([
                    'data' => $this->fleet_model->add_inspection_question_form($this->input->post()),
                    'survey_question_only_for_preview' => _l('hr_survey_question_only_for_preview'),
                    'survey_question_required' => _l('required'),
                    'survey_question_string' => _l('question'),
                ]);
                die();
            }
        }
    }

    /* Update question */
    public function update_inspection_question_form() {
        if (!has_permission('staffmanage_training', '', 'edit') && !has_permission('staffmanage_training', '', 'create')) {
            echo json_encode([
                'success' => false,
                'message' => _l('access_denied'),
            ]);
            die();
        }
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $this->fleet_model->update_inspection_question_form($this->input->post());
            }
        }
    }

    /* Remove survey question */
    public function remove_question($questionid)
    {
        if (!has_permission('surveys', '', 'edit')) {
            echo json_encode([
                'success' => false,
                'message' => _l('access_denied'),
            ]);
            die();
        }
        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'success' => $this->fleet_model->remove_question($questionid),
            ]);
        }
    }

    /* Removes survey checkbox/radio description*/
    public function remove_box_description($questionboxdescriptionid)
    {
        if (!has_permission('surveys', '', 'edit')) {
            echo json_encode([
                'success' => false,
                'message' => _l('access_denied'),
            ]);
            die();
        }
        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'success' => $this->fleet_model->remove_box_description($questionboxdescriptionid),
            ]);
        }
    }

    /* Add box description */
    public function add_box_description($questionid, $boxid)
    {
        if (!has_permission('surveys', '', 'edit')) {
            echo json_encode([
                'success' => false,
                'message' => _l('access_denied'),
            ]);
            die();
        }
        if ($this->input->is_ajax_request()) {
            $boxdescriptionid = $this->fleet_model->add_box_description($questionid, $boxid);
            echo json_encode([
                'boxdescriptionid' => $boxdescriptionid,
            ]);
        }
    }

    /* Reorder surveys */
    public function update_inspection_questions_orders()
    {
        if (has_permission('fleet_setting', '', 'edit')) {
            if ($this->input->is_ajax_request()) {
                if ($this->input->post()) {
                    $this->fleet_model->update_inspection_questions_orders($this->input->post());
                }
            }
        }
    }

    /**
     * inspection forms table
     * @return json
     */
    public function inspection_forms_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                'id',
                'name',
                'addedfrom',
                'datecreated',
            ];

            $where = [];
            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_inspection_forms';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = $aRow['id'];

                $categoryOutput = $aRow['name'];

                $categoryOutput .= '<div class="row-options">';

                if (has_permission('fleet_setting', '', 'edit')) {
                    $categoryOutput .= '<a href="' . admin_url('fleet/inspection_form/' . $aRow['id']) . '">' . _l('edit') . '</a>';
                }

                if (has_permission('fleet_setting', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_inspection_form/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = get_staff_full_name($aRow['addedfrom']);
                $row[] = _d($aRow['datecreated']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * delete inspection form
     * @param  [type] $id
     * @return [type]
     */
    public function delete_inspection_form($id) {
        if (!has_permission('staffmanage_job_position', '', 'delete')) {
            access_denied('job_position');
        }
        if (!$id) {
            redirect(admin_url('fleet/settings?group=inspection_forms'));
        }
        $success = $this->fleet_model->delete_inspection_form($id);
        if ($success) {
            set_alert('success', _l('hr_deleted'));
        } else {
            set_alert('warning', _l('problem_deleting'));
        }
        redirect(admin_url('fleet/settings?group=inspection_forms'));
    }

    /**
    * inspections table
    * @return json 
    */
    public function inspections_table(){
        if ($this->input->is_ajax_request()) {
            if($this->input->post()){

                $this->load->model('currencies_model');
                $base_currency = $this->currencies_model->get_base_currency();
                $currency_name = '';
                if(isset($base_currency)){
                    $currency_name = $base_currency->name;
                }

                $select = [
                    db_prefix() . 'fleet_vehicles.name as vehicle_name',
                    db_prefix() . 'fleet_inspection_forms.name as inspection_name',
                    db_prefix() . 'fleet_inspections.addedfrom as addedfrom',
                    db_prefix() . 'fleet_inspections.datecreated as datecreated',
                ];


                $where        = [];
                $aColumns     = $select;
                $sIndexColumn = 'id';
                $sTable       = db_prefix() . 'fleet_inspections';
                $join         = [
                    'LEFT JOIN ' . db_prefix() . 'fleet_vehicles ON ' . db_prefix() . 'fleet_vehicles.id = ' . db_prefix() . 'fleet_inspections.vehicle_id',
                    'LEFT JOIN ' . db_prefix() . 'fleet_inspection_forms ON ' . db_prefix() . 'fleet_inspection_forms.id = ' . db_prefix() . 'fleet_inspections.inspection_form_id',
                ];

                $is_report = $this->input->post("is_report");
                $vehicle_id = $this->input->post("id");
                if($vehicle_id != ''){
                    array_push($where, ' AND '.db_prefix() . 'fleet_inspections.vehicle_id ="'.$vehicle_id.'"');
                }

                $from_date = $this->input->post("from_date");
                $to_date = $this->input->post("to_date");

                if($from_date != '' && $to_date == ''){
                    $from_date = fe_format_date($from_date);
                    array_push($where, ' AND date('.db_prefix() . 'fleet_inspections.datecreated)="'.$from_date.'"');
                }
                if($from_date != '' && $to_date != ''){
                    $from_date = fe_format_date($from_date);
                    $to_date = fe_format_date($to_date);
                    array_push($where, ' AND date('.db_prefix() . 'fleet_inspections.datecreated) between "'.$from_date.'" AND "'.$to_date.'"');
                }

                $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() .'fleet_inspections.id as id'
                    
                ]);

                $output  = $result['output'];
                $rResult = $result['rResult'];  
                foreach ($rResult as $aRow) {
                    $row = [];

                    $_data = '';
                    if($is_report == ''){
                        $_data .= '<div class="row-options">';
                        $_data .= '<a href="' . admin_url('fleet/inspection_detail/' . $aRow['id']) . '">' . _l('view') . '</a>';

                        if(is_admin() || has_permission('fleet_inspection', '', 'edit')){
                            $_data .= ' | <a href="javascript:void(0)" onclick="edit_inspections('.$aRow['id'].'); return false;">' . _l('edit') . '</a>';
                        }
                        if(is_admin() || has_permission('fleet_inspection', '', 'delete')){
                            $_data .= ' | <a href="'.admin_url('fleet/delete_inspections/'.$aRow['id'].'/'.$vehicle_id).'" class="text-danger _delete">' . _l('fe_delete') . '</a>';
                        }
                        $_data .= '</div>'; 
                    }
                    $row[] = '<span class="text-nowrap">'.$aRow['vehicle_name'].'</span>'.$_data;  
                    $row[] = '<span class="text-nowrap">'.$aRow['inspection_name'].'</span>';  
                    $row[] = get_staff_full_name($aRow['addedfrom']);
                    $row[] = _d($aRow['datecreated']);  

                    $output['aaData'][] = $row;                                      
                }

                echo json_encode($output);
                die();
            }
        }
    }

    /**
     * add inspection
     * @return json
     */
    public function add_inspection($vehicle_id = ''){
        $data = $this->input->post();
        if($data['id'] == ''){
            if (!has_permission('fleet_inspection', '', 'create')) {
                access_denied('fleet');
            }
            $success = $this->fleet_model->add_inspection($data);
            if($success){
                $message = _l('added_successfully', _l('inspection'));
                set_alert('success', $message);
            }else {
                $message = _l('inspection_failed');
                set_alert('warning', $message);
            }
        }else{
            if (!has_permission('fleet_inspection', '', 'edit')) {
                access_denied('fleet');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->fleet_model->update_inspection($data, $id);
            if ($success) {
                $message = _l('updated_successfully', _l('inspection'));
                set_alert('success', $message);
            }
        }

        if ($vehicle_id != '') {
            redirect(admin_url('fleet/vehicle/'.$vehicle_id.'?group=inspections'));
        }
        redirect(admin_url('fleet/inspections'));
    }

    /**
     * add inspection form content
     * @return json
     */
    public function get_inspection_form_content($inspection_form_id = '', $inspection_id = ''){
        if($inspection_form_id != ''){
            $data['inspection_form'] = $this->fleet_model->get_inspection_form($inspection_form_id);
            $data['inspection_results'] = $this->fleet_model->get_inspection_results($inspection_id);
            
            $this->load->view('inspections/inspection_form_content', $data);
        }
    }

    /**
    * get data inspections
    * @param  integer $id 
    */
    public function get_data_inspections($id){
        $data_assets = $this->fleet_model->get_inspections($id);
        
        echo json_encode($data_assets);
        die;
    }

    /**
    * delete inspections
    * @param  integer $id 
    */
    public function delete_inspections($id, $vehicle_id = ''){
        if($id != ''){
            $result =  $this->fleet_model->delete_inspection($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('inspections')));
            }
            else{
                set_alert('danger', _l('deleted_fail', _l('inspections')));                 
            }
        }

        if ($vehicle_id != '') {
            redirect(admin_url('fleet/vehicle/'.$vehicle_id.'?group=inspections'));
        }
        redirect(admin_url('fleet/inspections'));
    }

    /**
     * parts list
     * @param  integer $id
     * @return load view
     */
    public function parts($id = '') {
        $this->required_module();
        if(!has_permission('fleet_part', '', 'view')) {
            access_denied('fleet');
        }

        $data['part_types'] = $this->fleet_model->get_data_part_types();
        $data['part_groups'] = $this->fleet_model->get_data_part_groups();

        $data['title']         = _l('parts');
        $this->load->view('parts/manage', $data);
    }

    /**
     * add maintenance
     * @return json
     */
    public function add_maintenance(){
        $data = $this->input->post();
        $message = '';
        if($data['id'] == ''){
            if (!has_permission('fleet_maintenance', '', 'create')) {
                access_denied('fleet');
            }
            $success = $this->fleet_model->add_maintenances($data);
            if($success){
                $message = _l('added_successfully');
            }else {
                $message = _l('maintenance_failed');
            }
        }else{
            if (!has_permission('fleet_maintenance', '', 'edit')) {
                access_denied('fleet');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->fleet_model->update_maintenances($data, $id);
            if ($success) {
                $message = _l('updated_successfully', _l('maintenance'));
            }else{
                $message = _l('update_failed', _l('maintenance'));
            }
        }
        echo json_encode(['success' => $success, 'message' => $message]);
        die();
    }

    /**
     * fuels
     * @return view
     */
    public function benefit_and_penalty(){
        $this->required_module();
        if (!has_permission('fleet_benefit_and_penalty', '', 'view')) {
            access_denied('fleet');
        }

        $data['title']         = _l('benefit_and_penalty');
        $data['drivers'] = $this->fleet_model->get_driver();
        $data['criterias'] = $this->fleet_model->get_criterias();

        $this->load->view('benefit_and_penalty/manage', $data);
    }

    /**
     * benefit and penalty table
     * @return json
     */
    public function benefit_and_penalty_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $select = [
                db_prefix() . 'fleet_benefit_and_penalty.id as id', // bulk actions
                'subject',
                'driver_id',
                'type',
                'date',
            ];

            $where = [];

            $id = $this->input->post("driverid");
            if($id != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_benefit_and_penalty.driver_id ="'.$id.'"');
            }

            $fuel_type = $this->input->post("type");
            if($fuel_type != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_benefit_and_penalty.type ="'.$fuel_type.'"');
            }

            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                if (!$this->fleet_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                if (!$this->fleet_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (date >= "' . $from_date . '" and date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (date <= "' . $to_date . '")');
            }
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_benefit_and_penalty';
            $join         = [
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $row[] = $aRow['id'];
                $categoryOutput = $aRow['subject'];

                $categoryOutput .= '<div class="row-options">';

                if (has_permission('fleet_benefit_and_penalty', '', 'edit')) {
                    $categoryOutput .= '<a href="#" onclick="edit_benefit_and_penalty(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                }

                if (has_permission('fleet_benefit_and_penalty', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_benefit_and_penalty/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = get_staff_full_name($aRow['driver_id']);
                $row[] = _l($aRow['type']);
                $row[] = _d($aRow['date']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }
    
    /**
     * add benefit_and_penalty
     * @return json
     */
    public function add_benefit_and_penalty(){
        $data = $this->input->post();
        if($data['id'] == ''){
            if (!has_permission('fleet_benefit_and_penalty', '', 'create')) {
                access_denied('fleet');
            }
            $success = $this->fleet_model->add_benefit_and_penalty($data);
            if($success){
                $message = _l('added_successfully');
            }else {
                $message = _l('benefit_and_penalty_failed');
            }
        }else{
            if (!has_permission('fleet_benefit_and_penalty', '', 'edit')) {
                access_denied('fleet');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->fleet_model->update_benefit_and_penalty($data, $id);
            if ($success) {
                $message = _l('updated_successfully', _l('benefit_and_penalty'));
            }
        }
        echo json_encode(['success' => $success, 'message' => $message]);
        die();
    }

    /**
    * delete benefit_and_penalty
    * @param  integer $id 
    */
    public function delete_benefit_and_penalty($id){
        if($id != ''){
            $result =  $this->fleet_model->delete_benefit_and_penalty($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('benefit_and_penalty')));
            }
            else{
                set_alert('danger', _l('deleted_fail', _l('benefit_and_penalty')));                 
            }
        }
        redirect(admin_url('fleet/benefit_and_penalty'));
    }

    /**
    * get data benefit_and_penalty
    * @param  integer $id 
    */
    public function get_data_benefit_and_penalty($id){
        $data_benefit_and_penalty = $this->fleet_model->get_benefit_and_penalty($id);
       
        echo json_encode($data_benefit_and_penalty);
        die;
    }

    /**
     * criterias table
     * @return json
     */
    public function criterias_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                'id',
                'name',
                'addedfrom',
                'datecreated',
            ];

            $where = [];
            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_criterias';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = $aRow['id'];

                $categoryOutput = $aRow['name'];

                $categoryOutput .= '<div class="row-options">';

                if (has_permission('fleet_setting', '', 'edit')) {
                    $categoryOutput .= '<a href="#" onclick="edit_criteria(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                }

                if (has_permission('fleet_setting', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_criteria/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = get_staff_full_name($aRow['addedfrom']);
                $row[] = _d($aRow['datecreated']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * delete criteria
     * @param  integer $id
     * @return
     */
    public function delete_criteria($id)
    {
        if (!has_permission('fleet_setting', '', 'delete')) {
            access_denied('fleet_setting');
        }
        $success = $this->fleet_model->delete_criteria($id);
        $message = '';
        
        if ($success) {
            $message = _l('deleted', _l('criteria'));
            set_alert('success', $message);
        } else {
            $message = _l('can_not_delete');
            set_alert('warning', $message);
        }
        redirect(admin_url('fleet/settings?group=criterias'));
    }

    /**
     * get data criteria
     * @param  integer $id 
     * @return json     
     */
    public function get_data_criteria($id){
        $criteria = $this->fleet_model->get_criterias($id);

        echo json_encode($criteria);
    }

    /**
     *
     *  add or edit criteria
     *  @param  integer  $id     The identifier
     *  @return view
     */
    public function criteria()
    {
        if (!has_permission('fleet_setting', '', 'edit') && !has_permission('fleet_setting', '', 'create')) {
            access_denied('fleet');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['description'] = $this->input->post('description', false);
            $message = '';
            if ($data['id'] == '') {
                if (!has_permission('fleet_setting', '', 'create')) {
                    access_denied('fleet');
                }
                $success = $this->fleet_model->add_criteria($data);
                if ($success) {
                    $message = _l('added_successfully', _l('criteria'));
                }else {
                    $message = _l('add_failure');
                }
            } else {
                if (!has_permission('fleet_setting', '', 'edit')) {
                    access_denied('fleet');
                }
                $id = $data['id'];
                unset($data['id']);
                $success = $this->fleet_model->update_criteria($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('criteria'));
                }else {
                    $message = _l('updated_fail');
                }
            }

            echo json_encode(['success' => $success, 'message' => $message]);
            die();
        }
    }

    /**
    * delete vehicle_assignment
    * @param  integer $id 
    */
    public function delete_vehicle_assignment($id, $rel_id = '', $rel_type = ''){
        if($id != ''){
            $result =  $this->fleet_model->delete_vehicle_assignment($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('vehicle_assignment')));
            }
            else{
                set_alert('danger', _l('deleted_fail', _l('vehicle_assignment')));                 
            }
        }
        if($rel_type == 'vehicle'){
            redirect(admin_url('fleet/vehicle/'.$rel_id.'?group=assignment_history'));
        }elseif($rel_type == 'driver'){
            redirect(admin_url('fleet/driver_detail/'.$rel_id.'?group=vehicle_assignments'));
        }
    }

    /**
    * get data vehicle_assignment
    * @param  integer $id 
    */
    public function get_data_vehicle_assignment($id){
        $data_vehicle_assignment = $this->fleet_model->get_vehicle_assignment($id);
       
        echo json_encode($data_vehicle_assignment);
        die;
    }

    /**
     * bookings
     * @return view
     */
    public function bookings(){
        $this->required_module();
        if (!has_permission('fleet_bookings', '', 'view')) {
            access_denied('fleet');
        }

        $data['title']         = _l('bookings');
        $data['booking_status'] = fleet_booking_status();
        $data['clients'] = $this->clients_model->get();

        $this->load->view('bookings/manage', $data);
    }

    /**
     * bookings table
     * @return json
     */
    public function bookings_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $select = [
                db_prefix() . 'fleet_bookings.id as id',
                db_prefix() . 'fleet_bookings.subject as subject',
                db_prefix() . 'fleet_bookings.delivery_date as delivery_date',
                db_prefix() . 'clients.company as company',
                db_prefix() . 'fleet_bookings.status as status',
                db_prefix() . 'fleet_bookings.amount as amount',
                'invoice_id',
            ];

            $where = [];

            $status = $this->input->post("status");
            if($status != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_bookings.status ="'.$status.'"');
            }

            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                if (!$this->fleet_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                if (!$this->fleet_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (delivery_date >= "' . $from_date . '" and delivery_date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (delivery_date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (delivery_date <= "' . $to_date . '")');
            }
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_bookings';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'fleet_bookings.userid',
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['number']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = '<a href="' . admin_url('fleet/booking_detail/' . $aRow['id']) . '">' . $aRow['number'] . '</a>';

                $categoryOutput .= '<div class="row-options">';

                $categoryOutput .= '<a href="' . admin_url('fleet/booking_detail/' . $aRow['id']) . '">' . _l('view') . '</a>';

                if (has_permission('fleet_bookings', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_booking/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = $aRow['company'];
                $row[] = _d($aRow['delivery_date']);
                $row[] = $aRow['subject'];
                $row[] = app_format_money($aRow['amount'], $currency->name);
                $row[] = fleet_render_status_html($aRow['id'], 'booking', $aRow['status'], false);
                $row[] = format_invoice_number($aRow['invoice_id']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * view booking detail
     * @return view
     */
    public function booking_detail($id = ''){

        $data['booking'] = $this->fleet_model->get_booking($id);
        $data['bookings'] = $this->fleet_model->get_booking();
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $data['drivers'] = $this->fleet_model->get_driver();

        $data['title'] = _l('booking');
        
        $this->load->view('bookings/booking_detail', $data);
    }

    /**
     * { booking change status }
     *
     * @param  $order_number  The order number
     * @return json
     */
    public function booking_status_mark_as($status, $booking_id){
        $message = '';
        $success = $this->fleet_model->booking_change_status($status, $booking_id);
        if ($success) {
            $message = _l('updated_successfully');
        }               

        echo json_encode([
                    'message' => $message,
                    'success' => $success
                ]);
        die;
    }

    public function booking_update_info(){
        if (!has_permission('fleet_bookings', '', 'edit')) {
            access_denied('fleet');
        }

        $data = $this->input->post();
        $id = '';
        if(isset($data['id'])){
            $id = $data['id'];
            unset($data['id']);
        }

        if(isset($data['amount'])){
            $data['amount']      = str_replace(',', '', $data['amount']);
        }

        $success = $this->fleet_model->update_booking($data, $id);

        if ($success == true) {
            set_alert('success', _l('updated_successfully', _l('booking')));
        }

        redirect(admin_url('fleet/booking_detail/' . $id));
    }

    /**
     * create invoice by booking
     * @param  integer $id the booking id
     * @return json
     */
    public function create_invoice_by_booking($id)
    {
        if (!has_permission('fleet_bookings', '', 'create')) {
            access_denied('fleet_bookings');
        }
        $invoice_id = $this->fleet_model->create_invoice_by_booking($id);
        $message    = $invoice_id ? _l('create_invoice_successfully') : '';

        $invoice_number = '';
        if ($invoice_id > 0) {
            $invoice_number = format_invoice_number($invoice_id);
        }
        echo json_encode([
            'invoice_number' => $invoice_number,
            'message'        => $message,
        ]);
        die();
    }

    /**
    * delete booking
    * @param  integer $id 
    */
    public function delete_booking($id){
        if($id != ''){
            $result =  $this->fleet_model->delete_booking($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('bookings')));
            }
            else{
                set_alert('danger', _l('deleted_fail', _l('bookings')));                 
            }
        }
        redirect(admin_url('fleet/bookings'));
    }

    /**
     * add booking
     * @return json
     */
    public function booking(){
        $data = $this->input->post();
        if($data['id'] == ''){
            if (!has_permission('fleet_bookings', '', 'create')) {
                access_denied('fleet');
            }
            $success = $this->fleet_model->add_booking($data);
            if($success){
                $message = _l('added_successfully');
            }else {
                $message = _l('bookings_failed');
            }
        }else{
            if (!has_permission('fleet_bookings', '', 'edit')) {
                access_denied('fleet');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->fleet_model->update_booking($data, $id);
            if ($success) {
                $message = _l('updated_successfully', _l('bookings'));
            }
        }
        echo json_encode(['success' => $success, 'message' => $message]);
        die();
    }

    /**
     * garage detail
     * @param  integer $garage_id 
     * @return view               
     */
    public function garage_detail($garage_id) {

        $data['garage'] = $this->fleet_model->get_garages($garage_id);
        $data['staffs']         = $this->staff_model->get();

        $this->load->view('garages/garage_detail', $data);

    }

    public function maintenance_team_table(){
        $has_permission_delete = has_permission('staff', '', 'delete');

        $custom_fields = get_custom_fields('staff', [
            'show_on_table' => 1,
            ]);
        $aColumns = [
            'firstname',
            'email',
            db_prefix() . 'roles.name',
            'last_login',
            'active',
            ];
            
        $sIndexColumn = 'id';
        $sTable       = db_prefix() . 'fleet_maintenance_teams';
        $join         = [
            'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'fleet_maintenance_teams.staffid',
            'LEFT JOIN ' . db_prefix() . 'roles ON ' . db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role'
        ];
        $i            = 0;

        $where = [];
        $garage_id = $this->input->post("garage_id");
        if($garage_id != ''){
            array_push($where, ' AND '.db_prefix() . 'fleet_maintenance_teams.garage_id ="'.$garage_id.'"');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            db_prefix() . 'fleet_maintenance_teams.staffid as staffid',
            'profile_image',
            'lastname',
            ]);

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];
            for ($i = 0; $i < count($aColumns); $i++) {
                if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                    $_data = $aRow[strafter($aColumns[$i], 'as ')];
                } else {
                    $_data = $aRow[$aColumns[$i]];
                }
                if ($aColumns[$i] == 'last_login') {
                    if ($_data != null) {
                        $_data = '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . _dt($_data) . '">' . time_ago($_data) . '</span>';
                    } else {
                        $_data = 'Never';
                    }
                } elseif ($aColumns[$i] == 'active') {
                    $checked = '';
                    if ($aRow['active'] == 1) {
                        $checked = 'checked';
                    }

                    $_data = '<div class="onoffswitch">
                        <input type="checkbox" ' . (($aRow['staffid'] == get_staff_user_id() || (is_admin($aRow['staffid']) || !has_permission('staff', '', 'edit')) && !is_admin()) ? 'disabled' : '') . ' data-switch-url="' . admin_url() . 'staff/change_staff_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['staffid'] . '" data-id="' . $aRow['staffid'] . '" ' . $checked . '>
                        <label class="onoffswitch-label" for="c_' . $aRow['staffid'] . '"></label>
                    </div>';

                    // For exporting
                    $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
                } elseif ($aColumns[$i] == 'firstname') {
                    $_data = '<a href="' . admin_url('fleet/driver_detail/' . $aRow['staffid']) . '">' . staff_profile_image($aRow['staffid'], [
                        'staff-profile-image-small',
                        ]) . '</a>';
                    $_data .= ' <a href="' . admin_url('fleet/driver_detail/' . $aRow['staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';

                    $_data .= '<div class="row-options">';
                    $_data .= '<a href="' . admin_url('fleet/driver_detail/' . $aRow['staffid']) . '">' . _l('view') . '</a>';

                    if (($has_permission_delete && ($has_permission_delete && !is_admin($aRow['staffid']))) || is_admin()) {
                        $_data .= ' | <a href="' . admin_url('fleet/delete_driver/' . $aRow['staffid']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                    }

                    $_data .= '</div>';
                } elseif ($aColumns[$i] == 'email') {
                    $_data = '<a href="mailto:' . $_data . '">' . $_data . '</a>';
                } else {
                    if (strpos($aColumns[$i], 'date_picker_') !== false) {
                        $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
                    }
                }
                $row[] = $_data;
            }

            $row['DT_RowClass'] = 'has-row-options';

            $output['aaData'][] = $row;
        }

        echo json_encode($output);
        die();
    }

    /**
     * add or edit driver
     * @return view
     */
    public function add_maintenance_team($id = ''){
        if ($this->input->post()) {

            if (!has_permission('fleet_garage', '', 'create')) {
                access_denied('fleet_garages');
            }

            $data                = $this->input->post();
            $success = $this->fleet_model->add_maintenance_team($data);
            if ($success) {
                set_alert('success', _l('added_successfully', _l('maintenance_team')));

                redirect(admin_url('fleet/garage_detail/'.$data['garage_id']));
            }
        }
    }

    /**
    * insurances
    */
    public function insurances(){
        $this->required_module();
        if ($this->input->post()) {
            $data  = $this->input->post();
            $insert_id = 0;
            if($data['id'] == ''){
                unset($data['id']);
                $insert_id = $this->fleet_model->add_insurances($data);
                if($insert_id > 0){
                    set_alert('success', _l('added_successfully', _l('insurances')));
                }
                else{
                    set_alert('danger', _l('added_fail', _l('insurances')));                 
                }
            }
            else
            {
                $result = $this->fleet_model->update_insurances($data);
                if($result == true){
                    set_alert('success', _l('updated_successfully', _l('insurances')));
                }
                else{
                    set_alert('danger', _l('no_data_changes', _l('insurances')));                    
                }
            }
            $redirect = $this->input->get('redirect');
            if($redirect != ''){
                $rel_type = $this->input->get('rel_type');
                $rel_id = $this->input->get('rel_id');
                if($rel_type != '' && is_numeric($rel_id)){
                    if($rel_type == 'audit'){
                        $this->fleet_model->update_audit_detail_item($data['asset_id'], $rel_id, ['insurance_id' => $insert_id]);
                    }
                    if($rel_type == 'cart_detailt'){
                        $this->fleet_model->update_cart_detail($rel_id, ['insurance_id' => $insert_id]);
                    }
                }
                redirect(admin_url($redirect));
            }
            else{
                redirect(admin_url('fleet/insurances'));         
            }
        }

        $data['title']    = _l('insurances');
        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();
        $data['currency_name'] = '';
        if(isset($base_currency)){
            $data['currency_name'] = $base_currency->name;
        }
        
        $data['insurance_categorys'] = $this->fleet_model->get_insurance_category();
        $data['insurance_types'] = $this->fleet_model->get_insurance_type();
        $data['insurance_status'] = $this->fleet_model->get_data_insurance_status();
        $data['insurance_company'] = $this->fleet_model->get_data_insurance_company();
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $this->load->view('insurances/manage', $data);
    }

    /**
     * insurances table
     * @return json
     */
    public function insurances_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $select = [
                db_prefix() . 'fleet_insurances.name as name',
                db_prefix() . 'fleet_vehicles.name as vehicle_name',
                db_prefix() . 'fleet_insurance_company.name as company_name',
                db_prefix() . 'fleet_insurance_status.name as status_name',
                db_prefix() . 'fleet_insurances.start_date as start_date',
                db_prefix() . 'fleet_insurances.end_date as end_date',
                db_prefix() . 'fleet_insurances.amount as amount',
            ];

            $where = [];

            $vehicleid = $this->input->post("vehicleid");
            if($vehicleid != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_insurances.vehicle_id ="'.$vehicleid.'"');
            }

            $status = $this->input->post("status");
            if($status != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_insurances.status ="'.$status.'"');
            }

            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                if (!$this->fleet_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                if (!$this->fleet_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (start_date >= "' . $from_date . '" and start_date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (start_date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (start_date <= "' . $to_date . '")');
            }
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_insurances';
            $join         = [
            'LEFT JOIN ' . db_prefix() . 'fleet_insurance_company ON ' . db_prefix() . 'fleet_insurance_company.id = ' . db_prefix() . 'fleet_insurances.insurance_company_id',
            'LEFT JOIN ' . db_prefix() . 'fleet_insurance_status ON ' . db_prefix() . 'fleet_insurance_status.id = ' . db_prefix() . 'fleet_insurances.insurance_status_id',
            'LEFT JOIN ' . db_prefix() . 'fleet_vehicles ON ' . db_prefix() . 'fleet_vehicles.id = ' . db_prefix() . 'fleet_insurances.vehicle_id'
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'fleet_insurances.id as id', 'vehicle_id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = $aRow['name'];

                $categoryOutput .= '<div class="row-options">';

                if (has_permission('fleet_insurance', '', 'edit')) {
                    $categoryOutput .= '<a href="#" onclick="edit_insurance(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                }

                if (has_permission('fleet_insurance', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_insurance/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = '<a href="' . admin_url('fleet/vehicle/' . $aRow['vehicle_id']) . '">' . $aRow['vehicle_name'] . '</a>';
                $row[] = $aRow['company_name'];
                $row[] = $aRow['status_name'];
                $row[] = _d($aRow['start_date']);
                $row[] = _d($aRow['end_date']);
                $row[] = app_format_money($aRow['amount'], $currency->name);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * work_performances
     * @return view
     */
    public function work_performances(){
        $this->required_module();
        if (!has_permission('fleet_work_performance', '', 'view')) {
            access_denied('fleet');
        }

        $data['title']         = _l('logbooks');
        $data['logbook_status'] = fleet_logbook_status();
        $data['bookings'] = $this->fleet_model->get_booking();
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $data['drivers'] = $this->fleet_model->get_driver();

        $this->load->view('work_performances/manage', $data);
    }

    /**
     * logbook table
     * @return json
     */
    public function logbook_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $select = [
                db_prefix() . 'fleet_logbooks.id as id',
                db_prefix() . 'fleet_logbooks.name as name',
                db_prefix() . 'fleet_logbooks.date as date',
                db_prefix() . 'fleet_bookings.number as number',
                db_prefix() . 'fleet_vehicles.name as vehicle_name',
                db_prefix() . 'fleet_logbooks.status as status',
            ];

            $where = [];
                $is_report = $this->input->post("is_report");
            $booking_id = $this->input->post("booking_id");
            if($booking_id != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_logbooks.booking_id ="'.$booking_id.'"');
            }

            $status = $this->input->post("status");
            if($status != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_logbooks.status ="'.$status.'"');
            }

            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                if (!$this->fleet_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                if (!$this->fleet_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (date >= "' . $from_date . '" and date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (date <= "' . $to_date . '")');
            }
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_logbooks';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'fleet_bookings ON ' . db_prefix() . 'fleet_bookings.id = ' . db_prefix() . 'fleet_logbooks.booking_id',
                'LEFT JOIN ' . db_prefix() . 'fleet_vehicles ON ' . db_prefix() . 'fleet_vehicles.id = ' . db_prefix() . 'fleet_logbooks.vehicle_id',
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['booking_id', 'driver_id', 'vehicle_id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = '<a href="' . admin_url('fleet/logbook_detail/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';

                if($is_report == ''){
                    $categoryOutput .= '<div class="row-options">';

                    $categoryOutput .= '<a href="' . admin_url('fleet/logbook_detail/' . $aRow['id']) . '">' . _l('view') . '</a>';

                   if (has_permission('fleet_setting', '', 'edit')) {
                        $categoryOutput .= ' | <a href="#" onclick="edit_logbook(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                    }

                    if (has_permission('fleet_work_performance', '', 'delete')) {
                        $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_logbook/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                    }

                    $categoryOutput .= '</div>';
                }
                $row[] = $categoryOutput;
                $row[] = '<a href="' . admin_url('fleet/booking_detail/' . $aRow['booking_id']) . '">' . $aRow['number'] . '</a>';

                $row[] = '<a href="' . admin_url('fleet/vehicle/' . $aRow['vehicle_id']) . '">' . $aRow['vehicle_name'] . '</a>';
                $row[] = get_staff_full_name($aRow['driver_id']);
                $row[] = _d($aRow['date']);
                $row[] = fleet_render_status_html($aRow['id'], 'logbook', $aRow['status'], false);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * add logbook
     * @return json
     */
    public function logbook(){
        $data = $this->input->post();
        if($data['id'] == ''){
            if (!has_permission('fleet_work_performance', '', 'create')) {
                access_denied('fleet');
            }
            $success = $this->fleet_model->add_logbook($data);
            if($success){
                add_notification([
                    'description'     => 'not_logbook_assigned_to_you',
                    'touserid'        => $data['driver_id'],
                    'link'            => 'fleet/logbook_detail/' . $success,
                    'additional_data' => serialize([
                        $data['name'],
                    ]),
                ]);
                $message = _l('added_successfully');
            }else {
                $message = _l('logbooks_failed');
            }
        }else{
            if (!has_permission('fleet_work_performance', '', 'edit')) {
                access_denied('fleet');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->fleet_model->update_logbook($data, $id);
            if ($success) {
                $message = _l('updated_successfully', _l('logbooks'));
            }
        }
        echo json_encode(['success' => $success, 'message' => $message]);
        die();
    }

    /**
    * delete logbook
    * @param  integer $id 
    */
    public function delete_logbook($id){
        if($id != ''){
            $result =  $this->fleet_model->delete_logbook($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('logbooks')));
            }
            else{
                set_alert('danger', _l('deleted_fail', _l('logbooks')));                 
            }
        }
        redirect(admin_url('fleet/work_performances'));
    }

    /**
    * get data logbook
    * @param  integer $id 
    */
    public function get_data_logbook($id){
        $data_garages = $this->fleet_model->get_logbook($id);
       
        echo json_encode($data_garages);
        die;
    }

    /**
     * view booking detail
     * @return view
     */
    public function logbook_detail($id = ''){

        $data['logbook'] = $this->fleet_model->get_logbook($id);

        $data['title'] = _l('logbook');
        
        $this->load->view('work_performances/logbook_detail', $data);
    }

    /**
     * { logbook change status }
     *
     * @param  $order_number  The order number
     * @return json
     */
    public function logbook_status_mark_as($status, $logbook_id){
        $message = '';
        $success = $this->fleet_model->logbook_change_status($status, $logbook_id);
        if ($success) {
            $message = _l('updated_successfully');
        }               

        echo json_encode([
                    'message' => $message,
                    'success' => $success
                ]);
        die;
    }

    /**
     * tiem card table
     * @return json
     */
    public function time_card_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $select = [
                db_prefix() . 'fleet_time_cards.driver_id as driver_id',
                db_prefix() . 'fleet_time_cards.start_time as start_time',
                db_prefix() . 'fleet_time_cards.end_time as end_time',
                db_prefix() . 'fleet_time_cards.notes as notes',
            ];

            $where = [];
            $logbook_id = $this->input->post("logbook_id");
            if($logbook_id != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_time_cards.logbook_id ="'.$logbook_id.'"');
            }
          
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_time_cards';
            $join         = [
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['logbook_id', 'id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = '<a href="' . admin_url('fleet/driver_detail/' . $aRow['driver_id']) . '">' . get_staff_full_name($aRow['driver_id']) . '</a>';

                $categoryOutput .= '<div class="row-options">';

                if (has_permission('fleet_work_performance', '', 'edit')) {
                    $categoryOutput .= '<a href="#" onclick="edit_time_card(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                }

                if (has_permission('fleet_work_performance', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_time_card/' . $aRow['id'].'/'.$aRow['logbook_id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';

                $row[] = $categoryOutput;
                $row[] = _dt($aRow['start_time'], true);
                $row[] = _dt($aRow['end_time'], true);
                $row[] = $aRow['notes'];

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
    * delete time_card
    * @param  integer $id 
    */
    public function delete_time_card($id, $logbook_id = ''){
        if($id != ''){
            $result =  $this->fleet_model->delete_time_card($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('time_cards')));
            }
            else{
                set_alert('danger', _l('deleted_fail', _l('time_cards')));                 
            }
        }
        redirect(admin_url('fleet/logbook_detail/'.$logbook_id));
    }

    /**
     * add time_card
     * @return json
     */
    public function time_card(){
        $data = $this->input->post();
        if($data['id'] == ''){
            if (!has_permission('fleet_work_performance', '', 'create')) {
                access_denied('fleet');
            }
            $success = $this->fleet_model->add_time_card($data);
            if($success){
                $message = _l('added_successfully');
            }else {
                $message = _l('time_cards_failed');
            }
        }else{
            if (!has_permission('fleet_work_performance', '', 'edit')) {
                access_denied('fleet');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->fleet_model->update_time_card($data, $id);
            if ($success) {
                $message = _l('updated_successfully', _l('time_cards'));
            }
        }
        echo json_encode(['success' => $success, 'message' => $message]);
        die();
    }

    /**
    * get data time_card
    * @param  integer $id 
    */
    public function get_data_time_card($id){
        $data_time_card = $this->fleet_model->get_time_card($id);
       
        echo json_encode($data_time_card);
        die;
    }

    /**
     * add insurance
     * @return json
     */
    public function insurance(){
        $data = $this->input->post();
        if($data['id'] == ''){
            if (!has_permission('fleet_insurance', '', 'create')) {
                access_denied('fleet');
            }
            $success = $this->fleet_model->add_insurance($data);
            if($success){
                $message = _l('added_successfully');
            }else {
                $message = _l('insurances_failed');
            }
        }else{
            if (!has_permission('fleet_insurance', '', 'edit')) {
                access_denied('fleet');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->fleet_model->update_insurance($data, $id);
            if ($success) {
                $message = _l('updated_successfully', _l('insurances'));
            }
        }
        echo json_encode(['success' => $success, 'message' => $message]);
        die();
    }

    /**
    * delete insurance
    * @param  integer $id 
    */
    public function delete_insurance($id){
        if($id != ''){
            $result =  $this->fleet_model->delete_insurance($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('insurances')));
            }
            else{
                set_alert('danger', _l('deleted_fail', _l('insurances')));                 
            }
        }
        redirect(admin_url('fleet/insurances'));
    }


    /**
    * get data insurance
    * @param  integer $id 
    */
    public function get_data_insurance($id){
        $data_insurance = $this->fleet_model->get_insurance($id);
       
        echo json_encode($data_insurance);
        die;
    }

    /**
     *
     *  add or edit insurance_category
     *  @param  integer  $id     The identifier
     *  @return view
     */
    public function insurance_category()
    {
        if (!has_permission('fleet_setting', '', 'edit') && !has_permission('fleet_setting', '', 'create')) {
            access_denied('fleet');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['description'] = $this->input->post('description', false);
            $message = '';
            if ($data['id'] == '') {
                if (!has_permission('fleet_setting', '', 'create')) {
                    access_denied('fleet');
                }
                $success = $this->fleet_model->add_insurance_category($data);
                if ($success) {
                    $message = _l('added_successfully', _l('insurance_category'));
                }else {
                    $message = _l('add_failure');
                }
            } else {
                if (!has_permission('fleet_setting', '', 'edit')) {
                    access_denied('fleet');
                }
                $id = $data['id'];
                unset($data['id']);
                $success = $this->fleet_model->update_insurance_category($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('insurance_category'));
                }else {
                    $message = _l('updated_fail');
                }
            }

            echo json_encode(['success' => $success, 'message' => $message]);
            die();
        }
    }

    /**
     *
     *  add or edit insurance_type
     *  @param  integer  $id     The identifier
     *  @return view
     */
    public function insurance_type()
    {
        if (!has_permission('fleet_setting', '', 'edit') && !has_permission('fleet_setting', '', 'create')) {
            access_denied('fleet');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['description'] = $this->input->post('description', false);
            $message = '';
            if ($data['id'] == '') {
                if (!has_permission('fleet_setting', '', 'create')) {
                    access_denied('fleet');
                }
                $success = $this->fleet_model->add_insurance_type($data);
                if ($success) {
                    $message = _l('added_successfully', _l('insurance_type'));
                }else {
                    $message = _l('add_failure');
                }
            } else {
                if (!has_permission('fleet_setting', '', 'edit')) {
                    access_denied('fleet');
                }
                $id = $data['id'];
                unset($data['id']);
                $success = $this->fleet_model->update_insurance_type($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('insurance_type'));
                }else {
                    $message = _l('updated_fail');
                }
            }

            echo json_encode(['success' => $success, 'message' => $message]);
            die();
        }
    }

    /**
     * insurance_categories table
     * @return json
     */
    public function insurance_categories_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                'id',
                'name',
                'addedfrom',
                'datecreated',
            ];

            $where = [];
            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_insurance_categories';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = $aRow['id'];

                $categoryOutput = $aRow['name'];

                $categoryOutput .= '<div class="row-options">';

                if (has_permission('fleet_setting', '', 'edit')) {
                    $categoryOutput .= '<a href="#" onclick="edit_insurance_category(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                }

                if (has_permission('fleet_setting', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_insurance_category/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = get_staff_full_name($aRow['addedfrom']);
                $row[] = _d($aRow['datecreated']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * delete insurance_category
     * @param  integer $id
     * @return
     */
    public function delete_insurance_category($id)
    {
        if (!has_permission('fleet_setting', '', 'delete')) {
            access_denied('fleet_setting');
        }
        $success = $this->fleet_model->delete_insurance_category($id);
        $message = '';
        
        if ($success) {
            $message = _l('deleted', _l('insurance_category'));
            set_alert('success', $message);
        } else {
            $message = _l('can_not_delete');
            set_alert('warning', $message);
        }
        redirect(admin_url('fleet/settings?group=insurance_categories'));
    }

    /**
     * get data insurance_category
     * @param  integer $id 
     * @return json     
     */
    public function get_data_insurance_category($id){
        $insurance_category = $this->fleet_model->get_insurance_category($id);

        echo json_encode($insurance_category);
    }

    /**
     * insurance_types table
     * @return json
     */
    public function insurance_types_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                'id',
                'name',
                'addedfrom',
                'datecreated',
            ];

            $where = [];
            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_insurance_types';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = $aRow['id'];

                $categoryOutput = $aRow['name'];

                $categoryOutput .= '<div class="row-options">';

                if (has_permission('fleet_setting', '', 'edit')) {
                    $categoryOutput .= '<a href="#" onclick="edit_insurance_type(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                }

                if (has_permission('fleet_setting', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_insurance_type/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = get_staff_full_name($aRow['addedfrom']);
                $row[] = _d($aRow['datecreated']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * delete insurance_type
     * @param  integer $id
     * @return
     */
    public function delete_insurance_type($id)
    {
        if (!has_permission('fleet_setting', '', 'delete')) {
            access_denied('fleet_setting');
        }
        $success = $this->fleet_model->delete_insurance_type($id);
        $message = '';
        
        if ($success) {
            $message = _l('deleted', _l('insurance_type'));
            set_alert('success', $message);
        } else {
            $message = _l('can_not_delete');
            set_alert('warning', $message);
        }
        redirect(admin_url('fleet/settings?group=insurance_types'));
    }

    /**
     * get data insurance_type
     * @param  integer $id 
     * @return json     
     */
    public function get_data_insurance_type($id){
        $insurance_type = $this->fleet_model->get_insurance_type($id);

        echo json_encode($insurance_type);
    }

    /**
     * events
     * @return view
     */
    public function events(){
        $this->required_module();
        if (!has_permission('fleet_event', '', 'view')) {
            access_denied('fleet');
        }

        $data['title']         = _l('events');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $data['drivers'] = $this->fleet_model->get_driver();

        $this->load->view('events/manage', $data);
    }

    /**
     * add event
     * @return json
     */
    public function event(){
        $data = $this->input->post();
        if($data['id'] == ''){
            if (!has_permission('fleet_event', '', 'create')) {
                access_denied('fleet');
            }
            $success = $this->fleet_model->add_event($data);
            if($success){
                $message = _l('added_successfully');
            }else {
                $message = _l('events_failed');
            }
        }else{
            if (!has_permission('fleet_event', '', 'edit')) {
                access_denied('fleet');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->fleet_model->update_event($data, $id);
            if ($success) {
                $message = _l('updated_successfully', _l('events'));
            }
        }
        echo json_encode(['success' => $success, 'message' => $message]);
        die();
    }

    /**
     * events table
     * @return json
     */
    public function events_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $select = [
                db_prefix() . 'fleet_events.id as id',
                db_prefix() . 'fleet_events.subject as subject',
                db_prefix() . 'fleet_events.driver_id as driver_id',
                db_prefix() . 'fleet_vehicles.name as vehicle_name',
                db_prefix() . 'fleet_events.event_type as event_type',
                db_prefix() . 'fleet_events.event_time as event_time',
            ];

            $where = [];

            $is_report = $this->input->post("is_report");
            $event_type = $this->input->post("event_type");
            if($event_type != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_events.event_type ="'.$event_type.'"');
            }

            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                if (!$this->fleet_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                if (!$this->fleet_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (event_time >= "' . $from_date . '" and event_time <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (event_time >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (event_time <= "' . $to_date . '")');
            }
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_events';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'fleet_vehicles ON ' . db_prefix() . 'fleet_vehicles.id = ' . db_prefix() . 'fleet_events.vehicle_id',
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'fleet_events.description as description', 'vehicle_id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = $aRow['subject'];

                if($is_report == ''){
                    $categoryOutput .= '<div class="row-options">';

                    if (has_permission('fleet_event', '', 'edit')) {
                        $categoryOutput .= '<a href="#" onclick="edit_event(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                    }

                    if (has_permission('fleet_event', '', 'delete')) {
                        $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_event/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                    }
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = '<a href="' . admin_url('fleet/vehicle/' . $aRow['vehicle_id']) . '">' . $aRow['vehicle_name'] . '</a>';
                $row[] = get_staff_full_name($aRow['driver_id']);
                $row[] = _dt($aRow['event_time']);
                $row[] = _l($aRow['event_type']);
                $row[] = $aRow['description'];

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
    * get data event
    * @param  integer $id 
    */
    public function get_data_event($id){
        $data_event = $this->fleet_model->get_event($id);
       
        echo json_encode($data_event);
        die;
    }

    /**
    * delete event
    * @param  integer $id 
    */
    public function delete_event($id){
        if($id != ''){
            $result =  $this->fleet_model->delete_event($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('event')));
            }
            else{
                set_alert('danger', _l('deleted_fail', _l('event')));                 
            }
        }
        redirect(admin_url('fleet/events'));
    }

    /**
     * work_orders
     * @return view
     */
    public function work_orders(){
        $this->required_module();
        if (!has_permission('fleet_work_orders', '', 'view')) {
            access_denied('fleet');
        }

        $data['title']         = _l('work_orders');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $data['drivers'] = $this->fleet_model->get_driver();

        $this->load->view('work_orders/manage', $data);
    }

    /**
     * work_orders table
     * @return json
     */
    public function work_orders_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $select = [
                db_prefix() . 'fleet_work_orders.id as id',
                db_prefix() . 'fleet_work_orders.number as number',
                db_prefix() . 'fleet_vehicles.name as vehicle_name',
                db_prefix() . 'fleet_work_orders.vendor_id as vendor_id',
                db_prefix() . 'fleet_work_orders.issue_date as issue_date',
                db_prefix() . 'fleet_work_orders.start_date as start_date',
                db_prefix() . 'fleet_work_orders.complete_date as complete_date',
                db_prefix() . 'fleet_work_orders.total as total',
            ];

            $where = [];

                $is_report = $this->input->post("is_report");
            $status = $this->input->post("status");
            if($status != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_work_orders.status ="'.$status.'"');
            }

            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                if (!$this->fleet_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                if (!$this->fleet_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (issue_date >= "' . $from_date . '" and issue_date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (issue_date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (issue_date <= "' . $to_date . '")');
            }
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_work_orders';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'fleet_vehicles ON ' . db_prefix() . 'fleet_vehicles.id = ' . db_prefix() . 'fleet_work_orders.vehicle_id',
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'fleet_work_orders.status as status', 'vehicle_id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = '<a href="' . admin_url('fleet/work_order_detail/' . $aRow['id']) . '">'. $aRow['number']. '</a>';

                if($is_report == ''){
                    $categoryOutput .= '<div class="row-options">';

                    $categoryOutput .= '<a href="' . admin_url('fleet/work_order_detail/' . $aRow['id']) . '">' . _l('view') . '</a>';

                    if (has_permission('fleet_work_orders', '', 'edit')) {
                        $categoryOutput .= ' | <a href="' . admin_url('fleet/work_order/' . $aRow['id']) . '">' . _l('edit') . '</a>';
                    }

                    if (has_permission('fleet_work_orders', '', 'delete')) {
                        $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_work_order/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                    }
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = '<a href="' . admin_url('fleet/vehicle/' . $aRow['vehicle_id']) . '">' . $aRow['vehicle_name'] . '</a>';
                $row[] = get_vendor_company_name($aRow['vendor_id']);
                $row[] = _d($aRow['issue_date']);
                $row[] = _d($aRow['start_date']);
                $row[] = _d($aRow['complete_date']);
                $row[] = app_format_money($aRow['total'], $currency->name);
                $row[] = fleet_render_status_html($aRow['id'], 'work_order', $aRow['status'], false);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
    * get data work_order
    * @param  integer $id 
    */
    public function get_data_work_order($id){
        $data_work_order = $this->fleet_model->get_work_order($id);
       
        echo json_encode($data_work_order);
        die;
    }

    /**
    * delete work_order
    * @param  integer $id 
    */
    public function delete_work_order($id){
        if($id != ''){
            $result =  $this->fleet_model->delete_work_order($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('work_order')));
            }
            else{
                set_alert('danger', _l('deleted_fail', _l('work_order')));                 
            }
        }
        redirect(admin_url('fleet/work_orders'));
    }
        
    /**
     * add or update work_order
     * @return view
     */
    public function work_order($id = ''){
        if ($this->input->post()) {
            $data                = $this->input->post();
            $data['work_requested'] = html_purify($this->input->post('work_requested', false));

            if($id == ''){
                if (!has_permission('fleet_work_orders', '', 'create')) {
                    access_denied('fleet_work_orders');
                }
                $success = $this->fleet_model->add_work_order($data);
                if ($success) {
                    set_alert('success', _l('added_successfully', _l('work_order')));
                }

                redirect(admin_url('fleet/work_order_detail/' . $success));
            }else{
                if (!has_permission('fleet_work_orders', '', 'edit')) {
                    access_denied('fleet_work_orders');
                }
                $success = $this->fleet_model->update_work_order($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('work_order')));
                }

                redirect(admin_url('fleet/work_order_detail/' . $id));
            }
        }

        if($id != ''){
            $data['work_order'] = $this->fleet_model->get_work_order($id);
        }

        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $data['title'] = _l('work_order');
        $data['vendors'] = $this->fleet_model->get_vendor();
        $data['parts'] = $this->fleet_model->get_part();

        $this->load->view('work_orders/work_order', $data);
    }

    /**
     * maintenance detail
     * @param  integer $maintenance_id 
     * @return view               
     */
    public function maintenance_detail($maintenance_id) {

        $data['maintenance'] = $this->fleet_model->get_maintenances($maintenance_id);

        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();

        $this->load->view('maintenances/maintenance_detail', $data);

    }

    /**
     * view work order detail
     * @return view
     */
    public function work_order_detail($id = ''){

        $data['work_order'] = $this->fleet_model->get_work_order($id);
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();

        $data['title'] = _l('work_order');
        
        $this->load->view('work_orders/work_order_detail', $data);
    }

    /**
     * { work_order change status }
     *
     * @param  $order_number  The order number
     * @return json
     */
    public function work_order_status_mark_as($status, $work_order_id){
        $message = '';
        $success = $this->fleet_model->work_order_change_status($status, $work_order_id);
        if ($success) {
            $message = _l('updated_successfully');
        }               

        echo json_encode([
                    'message' => $message,
                    'success' => $success
                ]);
        die;
    }

    /**
     * create expense by work_order
     * @param  integer $id the work_order id
     * @return json
     */
    public function create_expense_by_work_order($id)
    {
        if (!has_permission('fleet_work_orders', '', 'create')) {
            access_denied('fleet_work_orders');
        }

        $expense_id = $this->fleet_model->create_expense_by_work_order($id);
        $message    = $expense_id ? _l('create_expense_successfully') : '';


        echo json_encode([
            'message'        => $message,
        ]);
        die();
    }

    /**
     * transactions
     * @return view
     */
    public function transactions()
    {
        $this->required_module();

        if (!has_permission('fleet_transaction', '', 'view')) {
            access_denied('setting');
        }
        
        $data          = [];
        $data['group'] = $this->input->get('group');

        $data['tab'][] = 'invoices';
        $data['tab'][] = 'expenses';
        
        $data['tab_2'] = $this->input->get('tab');
        if ($data['group'] == '') {
            $data['group'] = 'invoices';
        }


        $data['title']        = _l($data['group']);
        $data['tabs']['view'] = 'transactions/' . $data['group'];
        $this->load->view('transactions/manage', $data);
    }

    /**
     * invoices table
     * @return json
     */
    public function invoices_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $acc_closing_date = '';
            if(get_option('acc_close_the_books') == 1){
                $acc_closing_date = get_option('acc_closing_date');
            }
            $select = [
                db_prefix() . 'invoices.id as id',
                'clientid',
                db_prefix(). 'currencies.name as currency_name',
                db_prefix() .'invoices.date as date',
                db_prefix() .'invoices.status as status',
            ];
            $where = [];
            array_push($where, 'AND from_fleet = 1');
            
            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                if (!$this->fleet_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                if (!$this->fleet_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'invoices.date >= "' . $from_date . '" and ' . db_prefix() . 'invoices.date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'invoices.date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'invoices.date <= "' . $to_date . '")');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'invoices';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'invoices.currency',
                        ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['total']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $categoryOutput = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a>';

                $row[] = $categoryOutput;

                $row[] = _d($aRow['date']);
                $row[] = app_format_money($aRow['total'], $aRow['currency_name']);

                $row[] = get_company_name($aRow['clientid']);
                $row[] = format_invoice_status($aRow['status']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * expenses table
     * @return json
     */
    public function expenses_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
           
            $select = [
                db_prefix() . 'expenses.id as id',
                db_prefix() . 'expenses_categories.name as category_name',
                'expense_name',
                db_prefix() . 'expenses.date as date',
            ];
            $where = [];
            array_push($where, 'AND from_fleet = 1');

            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                if (!$this->fleet_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                if (!$this->fleet_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'expenses.date >= "' . $from_date . '" and ' . db_prefix() . 'expenses.date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'expenses.date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'expenses.date <= "' . $to_date . '")');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'expenses';
            $join         = [
                'JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category',
                'LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'expenses.paymentmode',
                'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'expenses.currency'
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix(). 'currencies.name as currency_name', 'amount']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = '<a href="' . admin_url('expenses#' . $aRow['id']) . '" target="_blank">' . $aRow['expense_name'] . '</a>';
               
                $row[] = $categoryOutput;
                $row[] = _d($aRow['date']);

                $row[] = app_format_money($aRow['amount'], $aRow['currency_name']);

                $row[] = $aRow['category_name'];

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
    * delete driver_document
    * @param  integer $id 
    */
    public function delete_driver_document($id, $rel_id = '', $rel_type = 'driver'){
        if($id != ''){
            $result =  $this->fleet_model->delete_driver_document($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('driver_document')));
            }
            else{
                set_alert('danger', _l('deleted_fail', _l('driver_document')));                 
            }
        }
        if($rel_type == 'driver'){
            redirect(admin_url('fleet/driver_detail/'.$rel_id.'?group=driver_documents'));
        }else{
            redirect(admin_url('fleet/vehicle/'.$rel_id.'?group=vehicle_document'));
        }

        redirect(admin_url('fleet/drivers'));
    }

    /**
     * view driver_documents
     * @return view
     */
    public function view_driver_documents($id = ''){

        $data['driver_document'] = $this->fleet_model->get_driver_document($id);

        $data['title'] = _l('driver_document');
        
        $this->load->view('driver_documents/driver_document_detail', $data);
    }

    /**
     * { delete bill attachment }
     *
     * @param      <type>  $id       The identifier
     * @param      string  $preview  The preview
     */
    public function delete_driver_document_attachment($id, $document_id, $preview = '')
    {
        $this->db->where('id', $id);
        $file = $this->db->get(db_prefix() . 'files')->row();

        if ($file->staffid == get_staff_user_id() || is_admin()) {
            $success = $this->fleet_model->delete_driver_document_attachment($file, $document_id);
            if ($success) {
                set_alert('success', _l('deleted', _l('file')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('file')));
            }

            if ($preview == '') {
                redirect(admin_url('fleet/driver_document/' . $document_id));
            } else {
                redirect(admin_url('fleet/view_driver_documents/' . $document_id));
            }
        } else {
            access_denied('fleet');
        }
    }

    /**
     * Dashboard
     * @return view
     */
    public function dashboard(){
        $this->required_module();
    
        $data['title'] = _l('dashboard');
        $data['driver_role_id'] = $this->fleet_model->get_fleet_driver_role_id();
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();
        $data['google_ids_calendars']  = $this->misc_model->get_google_calendar_ids();
        $data['fuel_consumption_ranking'] = $this->fleet_model->fuel_consumption_ranking();
        $data['calculating_driver_point'] = $this->fleet_model->driver_ranking();
        
        add_calendar_assets();

        $this->load->view('dashboard/manage', $data);
    }

    /**
     * get data dashboard
     * @return json
     */
    public function get_data_dashboard(){
        $data_filter = $this->input->get();

        $data['profit_and_loss_chart'] = $this->fleet_model->get_data_profit_and_loss_chart($data_filter);
        $data['sales_chart'] = $this->fleet_model->get_data_sales_chart($data_filter);

        echo json_encode($data);
        die;
    }

    /**
     * Reports
     * @return 
     */
    public function reports(){
        $this->required_module();
        $data['title'] = _l('reports');
        
        $this->load->view('reports/manage', $data);
    }
    
    /**
     * report fuel
     * @return view
     */
    public function fuel_report(){
        $data['title'] = _l('fuel_report');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $this->load->view('reports/includes/fuel_report', $data);
    }

    /**
     * Gets the data fuel chart.
     * @return json data chart
     */
    public function get_data_fuel_chart() {
        $data_fuel = $this->fleet_model->get_data_fuel_chart();

        echo json_encode([
            'data_fuel' => $data_fuel,
        ]);
        die();
    }

    /**
     * report maintenance
     * @return view
     */
    public function maintenance_report(){
        $data['title'] = _l('maintenance_report');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $this->load->view('reports/includes/maintenance_report', $data);
    }

    /**
     * Gets the data maintenance chart.
     * @return json data chart
     */
    public function get_data_maintenance_chart() {
        $data_maintenance = $this->fleet_model->get_data_maintenance_chart();

        echo json_encode([
            'data_maintenance' => $data_maintenance,
        ]);
        die();
    }

    /**
     * report event
     * @return view
     */
    public function event_report(){
        $data['title'] = _l('event_report');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $this->load->view('reports/includes/event_report', $data);
    }

    /**
     * Gets the data event chart.
     * @return json data chart
     */
    public function get_data_event_chart() {
        $data_event = $this->fleet_model->get_data_event_chart();
        $data_event_stats = $this->fleet_model->event_stats();


        echo json_encode([
            'data_event' => $data_event,
            'data_event_stats' => $data_event_stats,
        ]);
        die();
    }

    /**
     * report work_order
     * @return view
     */
    public function work_order_report(){
        $data['title'] = _l('work_order_report');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $this->load->view('reports/includes/work_order_report', $data);
    }

    /**
     * Gets the data work_order chart.
     * @return json data chart
     */
    public function get_data_work_order_chart() {
        $data_work_order = $this->fleet_model->get_data_work_order_chart();
        $data_work_order_stats = $this->fleet_model->work_order_stats();


        echo json_encode([
            'data_work_order' => $data_work_order,
            'data_work_order_stats' => $data_work_order_stats,
        ]);
        die();
    }

    /**
     * report income_and_expense
     * @return view
     */
    public function income_and_expense_report(){
        $data['title'] = _l('income_and_expense_report');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();
        $this->load->view('reports/includes/income_and_expense_report', $data);
    }

    /**
     * Gets the data income_and_expense chart.
     * @return json data chart
     */
    public function get_data_income_and_expense_chart() {
        $data = [];
        $data['profit_and_loss_chart'] = $this->fleet_model->get_data_profit_and_loss_chart();
        $data['sales_chart'] = $this->fleet_model->get_data_sales_chart();

        echo json_encode($data);
        die();
    }

    /**
     * report work_performance
     * @return view
     */
    public function work_performance_report(){
        $data['title'] = _l('work_performance_report');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $this->load->view('reports/includes/work_performance_report', $data);
    }

    /**
     * Gets the data work_performance chart.
     * @return json data chart
     */
    public function get_data_work_performance_chart() {
        $data_work_performance = $this->fleet_model->get_data_work_performance_chart();
        $data_work_performance_stats = $this->fleet_model->work_performance_stats();


        echo json_encode([
            'data_work_performance' => $data_work_performance,
            'data_work_performance_stats' => $data_work_performance_stats,
        ]);
        die();
    }

    /**
     * [get_data_work_performance_chart description]
     * @return [type] [description]
     */
    public function required_module() {
        $data = [];

        $data['required'] = [];

        if(!fleet_get_status_modules('hr_profile')){
            $data['required'][] = _l('hr_profile');
        }

        if(!fleet_get_status_modules('purchase')){
            $data['required'][] = _l('purchase_module');
        }

        if(count($data['required']) > 0){
            redirect('fleet/required_module_detail');
        }
    }

    public function required_module_detail() {
        $data = [];

        $data['required'] = [];
        $data['required']['hr_profile'] = 0;
        $data['required']['purchase'] = 0;

        if(fleet_get_status_modules('hr_profile')){
            $data['required']['hr_profile'] = 1;
        }

        if(fleet_get_status_modules('purchase')){
            $data['required']['purchase'] = 1;
        }

        $this->load->view('fleet/required_module', $data);
    }


    /* Edit legal document or add new legal document */
    public function vehicle_document($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['description'] = html_purify($this->input->post('description', false));
            $data['type'] = 'vehicle';
            if ($id == '') {

                if (!has_permission('contracts', '', 'create')) {
                    access_denied('contracts');
                }
                $id = $this->fleet_model->add_driver_document($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('acc_bill')));
                    echo json_encode([
                        'url'       => admin_url('fleet/vehicle_document/' . $data['vehicle_id']),
                        'id' => $id,
                        'vehicle_id' => $data['vehicle_id'],
                    ]);
                    die;
                }
            } else {
                if (!has_permission('contracts', '', 'edit')) {
                    access_denied('contracts');
                }
                $success = $this->fleet_model->update_driver_document($data, $id);

                echo json_encode([
                    'url'       => admin_url('fleet/vehicle/' . $data['vehicle_id'].'?group=vehicle_document'),
                    'id' => $id,
                    'vehicle_id' => $data['vehicle_id'],
                ]);
                die;
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('vehicle_document'));
        } else {
            $data['vehicle_document']                 = $this->fleet_model->get_driver_document($id, [], true);

            $title = $data['vehicle_document']->subject;
            $data['vehicle_id'] = $data['vehicle_document']->vehicle_id;

        }

        if ($this->input->get('vehicle_id')) {
            $data['vehicle_id'] = $this->input->get('vehicle_id');
        }

        $data['title']         = $title;
        $this->load->view('vehicle_documents/vehicle_document', $data);
    }

    /**
     * get mark staff
     * @param  integer $id_staff
     * @return array
     */
    public function get_mark_staff($id_staff, $training_process_id) {
        $array_training_point = [];
        $training_program_point = 0;

        //Get the latest employee's training result.
        $trainig_resultset = $this->hr_profile_model->get_resultset_training($id_staff, $training_process_id);

        $array_training_resultset = [];
        $array_resultsetid = [];
        $list_resultset_id = '';

        foreach ($trainig_resultset as $item) {
            if (count($array_training_resultset) == 0) {
                array_push($array_training_resultset, $item['trainingid']);
                array_push($array_resultsetid, $item['resultsetid']);

                $list_resultset_id .= '' . $item['resultsetid'] . ',';
            }
            if (!in_array($item['trainingid'], $array_training_resultset)) {
                array_push($array_training_resultset, $item['trainingid']);
                array_push($array_resultsetid, $item['resultsetid']);

                $list_resultset_id .= '' . $item['resultsetid'] . ',';
            }
        }

        $list_resultset_id = rtrim($list_resultset_id, ",");
        $count_out = 0;
        if ($list_resultset_id == "") {
            $list_resultset_id = '0';
        } else {
            $count_out = count($array_training_resultset);
        }

        $array_result = [];
        foreach ($array_training_resultset as $key => $training_id) {
            $total_question = 0;
            $total_question_point = 0;

            $total_point = 0;
            $training_library_name = '';
            $training_question_forms = $this->hr_profile_model->hr_get_training_question_form_by_relid($training_id);
            $hr_position_training = $this->hr_profile_model->get_board_mark_form($training_id);
            $total_question = count($training_question_forms);
            if ($hr_position_training) {
                $training_library_name .= $hr_position_training->subject;
            }

            foreach ($training_question_forms as $question) {
                $flag_check_correct = true;

                $get_id_correct = $this->hr_profile_model->get_id_result_correct($question['questionid']);
                $form_results = $this->hr_profile_model->hr_get_form_results_by_resultsetid($array_resultsetid[$key], $question['questionid']);

                if (count($get_id_correct) == count($form_results)) {
                    foreach ($get_id_correct as $correct_key => $correct_value) {
                        if (!in_array($correct_value, $form_results)) {
                            $flag_check_correct = false;
                        }
                    }
                } else {
                    $flag_check_correct = false;
                }

                $result_point = $this->hr_profile_model->get_point_training_question_form($question['questionid']);
                $total_question_point += $result_point->point;

                if ($flag_check_correct == true) {
                    $total_point += $result_point->point;
                    $training_program_point += $result_point->point;
                }

            }

            array_push($array_training_point, [
                'training_name' => $training_library_name,
                'total_point' => $total_point,
                'training_id' => $training_id,
                'total_question' => $total_question,
                'total_question_point' => $total_question_point,
            ]);
        }

        $response = [];
        $response['training_program_point'] = $training_program_point;
        $response['staff_training_result'] = $array_training_point;

        return $response;
    }

    public function download_file($folder_indicator, $attachmentid = '')
    {   
        $this->load->helper('download');

        $path = '';
        if ($folder_indicator == 'fle_driver_document') {
            $this->db->where('id', $attachmentid);
            $file = $this->db->get(db_prefix() . 'files')->row();
            $path = FLEET_MODULE_UPLOAD_FOLDER . '/driver_documents/' . $file->rel_id . '/' . $file->file_name;
        }else {
            die('folder not specified');
        }

        force_download($path, null);
    }

    /**
     * [get_calendar_data description]
     * @return [type] [description]
     */
    public function get_calendar_data()
    {
        echo json_encode($this->fleet_model->get_calendar_data(
                date('Y-m-d', strtotime($this->input->get('start'))),
                date('Y-m-d', strtotime($this->input->get('end'))),
                '',
                '',
                $this->input->get()
            ));
        die();
    }

    /* Edit part or add new part*/
    public function part($id = '')
    {
        if (!has_permission('fleet_part', '', 'view')) {
            access_denied('fleet');
        }

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('fleet_part', '', 'create')) {
                    access_denied('fleet');
                }

                $data = $this->input->post();

                $id = $this->fleet_model->add_part($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('part')));
                    redirect(admin_url('fleet/part/' . $id));
                }
            } else {
                if (!has_permission('fleet_part', '', 'edit')) {
                    access_denied('fleet');
                }
                $success = $this->fleet_model->update_part($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfully', _l('part')));
                }
                redirect(admin_url('fleet/part/' . $id));
            }
        }

        $group         = !$this->input->get('group') ? 'details' : $this->input->get('group');
        $data['group'] = $group;

        if ($id == '') {
            $title = _l('add_new', _l('part'));
        } else {
            $part                = $this->fleet_model->get_part($id);
            $data['part_tabs'] = [];
            $data['part_tabs']['details'] = ['name' => 'details', 'icon' => '<i class="fa fa-user-circle menu-icon"></i>'];
            $data['part_tabs']['assignment_history'] = ['name' => 'assignment_history', 'icon' => '<i class="fa fa-history menu-icon"></i>'];
            $data['part_tabs']['linked_vehicle_history'] = ['name' => 'linked_vehicle_history', 'icon' => '<i class="fa fa-link menu-icon"></i>'];

            if (!$part) {
                show_404();
            }

            $data['tab']      = isset($data['part_tabs'][$group]) ? $data['part_tabs'][$group] : null;
            $data['tab']['view'] = 'parts/groups/'.$data['group'];

            if (!$data['tab']) {
                show_404();
            }

            // Fetch data based on groups
            if ($group == 'details') {
               
            } 

            $data['part'] = $part;
            $title          = $part->name;

            if (!empty($data['client']->company)) {
                // Check if is realy empty client company so we can set this field to empty
                // The query where fetch the client auto populate firstname and lastname if company is empty
                if (is_empty_customer_company($data['client']->userid)) {
                    $data['client']->company = '';
                }
            }
        }

        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $data['drivers'] = $this->fleet_model->get_driver();
        $data['vendors'] = $this->fleet_model->get_vendor();
        $data['part_types'] = $this->fleet_model->get_data_part_types();
        $data['part_groups'] = $this->fleet_model->get_data_part_groups();
        $data['bodyclass'] = 'customer-profile dynamic-create-groups';
        $data['title']     = $title;

        $this->load->view('parts/part', $data);
    }

    /**
     * part groups table
     * @return json
     */
    public function part_groups_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                'id',
                'name',
                'addedfrom',
                'datecreated',
            ];

            $where = [];
            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_part_groups';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = $aRow['id'];

                $categoryOutput = $aRow['name'];

                $categoryOutput .= '<div class="row-options">';

                if (has_permission('fleet_setting', '', 'edit')) {
                    $categoryOutput .= '<a href="#" onclick="edit_part_group(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                }

                if (has_permission('fleet_setting', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_part_group/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = get_staff_full_name($aRow['addedfrom']);
                $row[] = _d($aRow['datecreated']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     *
     *  add or edit part group
     *  @param  integer  $id     The identifier
     *  @return view
     */
    public function part_group()
    {
        if (!has_permission('fleet_setting', '', 'edit') && !has_permission('fleet_setting', '', 'create')) {
            access_denied('fleet');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['description'] = $this->input->post('description', false);
            $message = '';
            if ($data['id'] == '') {
                if (!has_permission('fleet_setting', '', 'create')) {
                    access_denied('fleet');
                }
                $success = $this->fleet_model->add_part_group($data);
                if ($success) {
                    $message = _l('added_successfully', _l('part_group'));
                }else {
                    $message = _l('add_failure');
                }
            } else {
                if (!has_permission('fleet_setting', '', 'edit')) {
                    access_denied('fleet');
                }
                $id = $data['id'];
                unset($data['id']);
                $success = $this->fleet_model->update_part_group($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('part_group'));
                }else {
                    $message = _l('updated_fail');
                }
            }

            echo json_encode(['success' => $success, 'message' => $message]);
            die();
        }
    }

    /**
     * delete part group
     * @param  integer $id
     * @return
     */
    public function delete_part_group($id)
    {
        if (!has_permission('fleet_setting', '', 'delete')) {
            access_denied('fleet_setting');
        }
        $success = $this->fleet_model->delete_part_group($id);
        $message = '';
        
        if ($success) {
            $message = _l('deleted', _l('part_group'));
            set_alert('success', $message);
        } else {
            $message = _l('can_not_delete');
            set_alert('warning', $message);
        }
        redirect(admin_url('fleet/settings?group=part_groups'));
    }

    /**
     * get data part group
     * @param  integer $id 
     * @return json     
     */
    public function get_data_part_group($id){
        $part_group = $this->fleet_model->get_data_part_groups($id);

        echo json_encode($part_group);
    }

    /**
     * part types table
     * @return json
     */
    public function part_types_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                'id',
                'name',
                'addedfrom',
                'datecreated',
            ];

            $where = [];
            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_part_types';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = $aRow['id'];

                $categoryOutput = $aRow['name'];

                $categoryOutput .= '<div class="row-options">';

                if (has_permission('fleet_setting', '', 'edit')) {
                    $categoryOutput .= '<a href="#" onclick="edit_part_type(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                }

                if (has_permission('fleet_setting', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_part_type/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = get_staff_full_name($aRow['addedfrom']);
                $row[] = _d($aRow['datecreated']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     *
     *  add or edit part type
     *  @param  integer  $id     The identifier
     *  @return view
     */
    public function part_type()
    {
        if (!has_permission('fleet_setting', '', 'edit') && !has_permission('fleet_setting', '', 'create')) {
            access_denied('fleet');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['description'] = $this->input->post('description', false);
            $message = '';
            if ($data['id'] == '') {
                if (!has_permission('fleet_setting', '', 'create')) {
                    access_denied('fleet');
                }
                $success = $this->fleet_model->add_part_type($data);
                if ($success) {
                    $message = _l('added_successfully', _l('part_type'));
                }else {
                    $message = _l('add_failure');
                }
            } else {
                if (!has_permission('fleet_setting', '', 'edit')) {
                    access_denied('fleet');
                }
                $id = $data['id'];
                unset($data['id']);
                $success = $this->fleet_model->update_part_type($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('part_type'));
                }else {
                    $message = _l('updated_fail');
                }
            }

            echo json_encode(['success' => $success, 'message' => $message]);
            die();
        }
    }

    /**
     * delete part type
     * @param  integer $id
     * @return
     */
    public function delete_part_type($id)
    {
        if (!has_permission('fleet_setting', '', 'delete')) {
            access_denied('fleet_setting');
        }
        $success = $this->fleet_model->delete_part_type($id);
        $message = '';
        
        if ($success) {
            $message = _l('deleted', _l('part_type'));
            set_alert('success', $message);
        } else {
            $message = _l('can_not_delete');
            set_alert('warning', $message);
        }
        redirect(admin_url('fleet/settings?group=part_types'));
    }

    /**
     * get data part type
     * @param  integer $id 
     * @return json     
     */
    public function get_data_part_type($id){
        $part_type = $this->fleet_model->get_data_part_types($id);

        echo json_encode($part_type);
    }

    /**
     * part histories table
     * @return json
     */
    public function part_histories_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                'driver_id',
                'start_time',
                'end_time',
                'start_by',
                'end_by',
            ];

            $where = [];
            $rel_id = '';
            $rel_type = '';

            $part_id = $this->input->post("id");
            if($part_id != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_part_histories.part_id ="'.$part_id.'"');
                $rel_id = $part_id;
                $rel_type = 'part';
            }

            $driverid = $this->input->post("driverid");
            if($driverid != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_part_histories.driver_id ="'.$driverid.'"');
                $rel_id = $driverid;
                $rel_type = 'driver';
            }

            $type = $this->input->post("type");
            if($type != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_part_histories.type ="'.$type.'"');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_part_histories';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'fleet_vehicles ON ' . db_prefix() . 'fleet_vehicles.id = ' . db_prefix() . 'fleet_part_histories.vehicle_id',
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'fleet_part_histories.id as id', db_prefix() . 'fleet_vehicles.name as vehicle_name', 'type', 'vehicle_id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                if($aRow['type'] == 'assignee'){
                    $row[] = '<a href="' . admin_url('fleet/driver_detail/' . $aRow['driver_id']) . '">' . get_staff_full_name($aRow['driver_id']) . '</a>';
                }elseif($aRow['type'] == 'linked_vehicle'){
                    $row[] = '<a href="' . admin_url('fleet/vehicle/' . $aRow['vehicle_id']) . '">' . $aRow['vehicle_name'] . '</a>';
                }
                $row[] = _d($aRow['start_time']);
                $row[] = _d($aRow['end_time']);
                $row[] = get_staff_full_name($aRow['start_by']);
                $row[] = $aRow['end_by'] != '' ? get_staff_full_name($aRow['end_by']) : '';

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * parts table
     * @return json
     */
    public function parts_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $select = [
                db_prefix() . 'fleet_parts.name as name',
                db_prefix() . 'fleet_parts.brand as brand',
                db_prefix() . 'fleet_parts.model as model',
                db_prefix() . 'fleet_parts.serial_number as serial_number',
                db_prefix() . 'fleet_parts.status as status',
                db_prefix() . 'fleet_part_groups.name as group_name',
                db_prefix() . 'fleet_part_types.name as type_name',
                db_prefix() . 'fleet_vehicles.name as vehicle_name',
                db_prefix() . 'fleet_parts.driver_id as driver_id',
            ];

            $where = [];

            $vehicleid = $this->input->post("vehicleid");
            if($vehicleid != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_parts.vehicle_id ="'.$vehicleid.'"');
            }

            $driverid = $this->input->post("driverid");
            if($driverid != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_parts.driver_id ="'.$driverid.'"');
            }

            $status = $this->input->post("status");
            if($status != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_parts.status ="'.$status.'"');
            }

            $type = $this->input->post("type");
            if($type != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_parts.part_type_id ="'.$type.'"');
            }

            $group = $this->input->post("group");
            if($group != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_parts.part_group_id ="'.$group.'"');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_parts';
            $join         = [
            'LEFT JOIN ' . db_prefix() . 'fleet_vehicles ON ' . db_prefix() . 'fleet_vehicles.id = ' . db_prefix() . 'fleet_parts.vehicle_id',
            'LEFT JOIN ' . db_prefix() . 'fleet_part_types ON ' . db_prefix() . 'fleet_part_types.id = ' . db_prefix() . 'fleet_parts.part_type_id',
            'LEFT JOIN ' . db_prefix() . 'fleet_part_groups ON ' . db_prefix() . 'fleet_part_groups.id = ' . db_prefix() . 'fleet_parts.part_group_id'
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'fleet_parts.id as id', db_prefix() . 'fleet_parts.vehicle_id as vehicle_id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = '<a href="'.admin_url('fleet/part/'.$aRow['id'].'').'">' . $aRow['name'] . '</a>';

                if($vehicleid == ''){
                    $categoryOutput .= '<div class="row-options">';

                    $categoryOutput .= '<a href="'.admin_url('fleet/part/'.$aRow['id'].'').'">' . _l('view') . '</a>';

                    if (has_permission('fleet_part', '', 'delete')) {
                        $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_part/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                    }

                    $categoryOutput .= '</div>';
                }
                $row[] = $categoryOutput;

                $row[] = $aRow['type_name'];
                $row[] = $aRow['brand'];
                $row[] = $aRow['model'];
                $row[] = $aRow['serial_number'];
                $row[] = $aRow['group_name'];
                $row[] = _l($aRow['status']);
                $row[] = '<a href="'.admin_url('fleet/driver_detail/'.$aRow['driver_id'].'').'">' . get_staff_full_name($aRow['driver_id']) . '</a>';
                $row[] = '<a href="'.admin_url('fleet/vehicle/'.$aRow['vehicle_id'].'').'">' . $aRow['vehicle_name'] . '</a>';

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * delete part
     * @param  integer $id
     * @return
     */
    public function delete_part($id)
    {
        if (!has_permission('fleet_part', '', 'delete')) {
            access_denied('fleet_part');
        }
        $success = $this->fleet_model->delete_part($id);
        $message = '';
        if ($success) {
            $message = _l('deleted', _l('part'));
            set_alert('success', $message);
        } else {
            $message = _l('can_not_delete');
            set_alert('warning', $message);
        }
        redirect(admin_url('fleet/parts'));
    }

    /**
     * insurance company table
     * @return json
     */
    public function insurance_company_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                'id',
                'name',
                'addedfrom',
                'datecreated',
            ];

            $where = [];
            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_insurance_company';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = $aRow['id'];

                $categoryOutput = $aRow['name'];

                $categoryOutput .= '<div class="row-options">';

                if (has_permission('fleet_setting', '', 'edit')) {
                    $categoryOutput .= '<a href="#" onclick="edit_insurance_company(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                }

                if (has_permission('fleet_setting', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_insurance_company/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = get_staff_full_name($aRow['addedfrom']);
                $row[] = _d($aRow['datecreated']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     *
     *  add or edit insurance company
     *  @param  integer  $id     The identifier
     *  @return view
     */
    public function insurance_company()
    {
        if (!has_permission('fleet_setting', '', 'edit') && !has_permission('fleet_setting', '', 'create')) {
            access_denied('fleet');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['description'] = $this->input->post('description', false);
            $message = '';
            if ($data['id'] == '') {
                if (!has_permission('fleet_setting', '', 'create')) {
                    access_denied('fleet');
                }
                $success = $this->fleet_model->add_insurance_company($data);
                if ($success) {
                    $message = _l('added_successfully', _l('insurance_company'));
                }else {
                    $message = _l('add_failure');
                }
            } else {
                if (!has_permission('fleet_setting', '', 'edit')) {
                    access_denied('fleet');
                }
                $id = $data['id'];
                unset($data['id']);
                $success = $this->fleet_model->update_insurance_company($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('insurance_company'));
                }else {
                    $message = _l('updated_fail');
                }
            }

            echo json_encode(['success' => $success, 'message' => $message]);
            die();
        }
    }

    /**
     * delete insurance company
     * @param  integer $id
     * @return
     */
    public function delete_insurance_company($id)
    {
        if (!has_permission('fleet_setting', '', 'delete')) {
            access_denied('fleet_setting');
        }
        $success = $this->fleet_model->delete_insurance_company($id);
        $message = '';
        
        if ($success) {
            $message = _l('deleted', _l('insurance_company'));
            set_alert('success', $message);
        } else {
            $message = _l('can_not_delete');
            set_alert('warning', $message);
        }
        redirect(admin_url('fleet/settings?group=insurance_company'));
    }

    /**
     * get data insurance company
     * @param  integer $id 
     * @return json     
     */
    public function get_data_insurance_company($id){
        $insurance_company = $this->fleet_model->get_data_insurance_company($id);

        echo json_encode($insurance_company);
    }

    /**
     * insurance status table
     * @return json
     */
    public function insurance_status_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                'id',
                'name',
                'addedfrom',
                'datecreated',
            ];

            $where = [];
            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_insurance_status';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = $aRow['id'];

                $categoryOutput = $aRow['name'];

                $categoryOutput .= '<div class="row-options">';

                if (has_permission('fleet_setting', '', 'edit')) {
                    $categoryOutput .= '<a href="#" onclick="edit_insurance_status(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                }

                if (has_permission('fleet_setting', '', 'delete')) {
                    $categoryOutput .= ' | <a href="' . admin_url('fleet/delete_insurance_status/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = get_staff_full_name($aRow['addedfrom']);
                $row[] = _d($aRow['datecreated']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     *
     *  add or edit insurance status
     *  @param  integer  $id     The identifier
     *  @return view
     */
    public function insurance_status()
    {
        if (!has_permission('fleet_setting', '', 'edit') && !has_permission('fleet_setting', '', 'create')) {
            access_denied('fleet');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['description'] = $this->input->post('description', false);
            $message = '';
            if ($data['id'] == '') {
                if (!has_permission('fleet_setting', '', 'create')) {
                    access_denied('fleet');
                }
                $success = $this->fleet_model->add_insurance_status($data);
                if ($success) {
                    $message = _l('added_successfully', _l('insurance_status'));
                }else {
                    $message = _l('add_failure');
                }
            } else {
                if (!has_permission('fleet_setting', '', 'edit')) {
                    access_denied('fleet');
                }
                $id = $data['id'];
                unset($data['id']);
                $success = $this->fleet_model->update_insurance_status($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('insurance_status'));
                }else {
                    $message = _l('updated_fail');
                }
            }

            echo json_encode(['success' => $success, 'message' => $message]);
            die();
        }
    }

    /**
     * delete insurance status
     * @param  integer $id
     * @return
     */
    public function delete_insurance_status($id)
    {
        if (!has_permission('fleet_setting', '', 'delete')) {
            access_denied('fleet_setting');
        }
        $success = $this->fleet_model->delete_insurance_status($id);
        $message = '';
        
        if ($success) {
            $message = _l('deleted', _l('insurance_status'));
            set_alert('success', $message);
        } else {
            $message = _l('can_not_delete');
            set_alert('warning', $message);
        }
        redirect(admin_url('fleet/settings?group=insurance_status'));
    }

    /**
     * get data insurance status
     * @param  integer $id 
     * @return json     
     */
    public function get_data_insurance_status($id){
        $insurance_status = $this->fleet_model->get_data_insurance_status($id);

        echo json_encode($insurance_status);
    }

    /**
     * report fuel
     * @return view
     */
    public function rp_operating_cost_summary(){
        $data['title'] = _l('operating_cost_summary');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['operating_cost_summary'] = $this->fleet_model->vehicle_operating_cost_summary();
        $data['vehicles'] = $this->fleet_model->get_vehicle();

        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();

        $this->load->view('reports/includes/operating_cost_summary', $data);
    }

    /**
     * Gets the data event chart.
     * @return json data chart
     */
    public function get_data_operating_cost_chart() {
        $data_operating_cost = $this->fleet_model->get_data_operating_cost_chart();
        $data_operating_cost_stats = $this->fleet_model->operating_cost_stats();


        echo json_encode([
            'data_operating_cost' => $data_operating_cost,
            'data_operating_cost_stats' => $data_operating_cost_stats,
        ]);
        die();
    }

    /**
     * report fuel
     * @return view
     */
    public function rp_total_cost_trend(){
        $data['title'] = _l('total_cost_trend');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();

        $this->load->view('reports/includes/total_cost_trend', $data);
    }

    /**
     * Gets the data event chart.
     * @return json data chart
     */
    public function get_data_total_cost_trend_chart() {
        $data_total_cost_trend = $this->fleet_model->get_data_total_cost_trend_chart();


        echo json_encode([
            'data_total_cost_trend' => $data_total_cost_trend,
        ]);
        die();
    }

    /**
     * report expense summary
     * @return view
     */
    public function rp_expense_summary(){
        $data['title'] = _l('expense_summary');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();
        $data['vehicle_groups'] = $this->fleet_model->get_data_vehicle_groups();

        $this->load->view('reports/includes/expense_summary', $data);
    }

    /**
     * report expenses_by_vehicle
     * @return view
     */
    public function rp_expenses_by_vehicle(){
        $data['title'] = _l('expenses_by_vehicle');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();

        $this->load->view('reports/includes/expenses_by_vehicle', $data);
    }

    /**
     * report status_changes
     * @return view
     */
    public function rp_status_changes(){
        $data['title'] = _l('status_changes');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $this->load->view('reports/includes/status_changes', $data);
    }

    /**
     * report group_changes
     * @return view
     */
    public function rp_group_changes(){
        $data['title'] = _l('group_changes');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $this->load->view('reports/includes/group_changes', $data);
    }
    
    /**
     * vehicle_histories table
     * @return json
     */
    public function vehicle_histories_table()
    {
        if ($this->input->is_ajax_request()) {
          
            $select = [
                db_prefix() . 'fleet_vehicles.name as vehicle_name',
                'from_value',
                'to_value',
                db_prefix() . 'fleet_vehicle_histories.addedfrom as addedfrom',
                db_prefix() . 'fleet_vehicle_histories.datecreated as datecreated',
            ];

            $where = [];

            $is_report = $this->input->post("is_report");

            $vehicle_id = $this->input->post("id");
            if($vehicle_id != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_vehicle_histories.vehicle_id ="'.$vehicle_id.'"');
            }



            $type = $this->input->post("type");
            if($type != ''){
                array_push($where, ' AND '.db_prefix() . 'fleet_vehicle_histories.type ="'.$type.'"');
            }

            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                if (!$this->fleet_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                if (!$this->fleet_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (datecreated >= "' . $from_date . '" and datecreated <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (datecreated >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (datecreated <= "' . $to_date . '")');
            }
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_vehicle_histories';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'fleet_vehicles ON ' . db_prefix() . 'fleet_vehicles.id = ' . db_prefix() . 'fleet_vehicle_histories.vehicle_id',
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['vehicle_id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = '<a href="' . admin_url('fleet/vehicle/' . $aRow['vehicle_id']) . '">'.$aRow['vehicle_name'].'</a>';

                $row[] = $categoryOutput;
                $row[] = $aRow['from_value'] != null ? $aRow['from_value'] : '';
                $row[] = $aRow['to_value'] != null ? $aRow['to_value'] : '';
                $row[] = get_staff_full_name($aRow['addedfrom']);
                $row[] = _dt($aRow['datecreated']);

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * report status_summary
     * @return view
     */
    public function rp_status_summary(){
        $data['title'] = _l('status_summary');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['vehicles'] = $this->fleet_model->get_vehicle();

        $this->load->view('reports/includes/status_summary', $data);
    }

    /**
     * report vehicle_assignment_log
     * @return view
     */
    public function rp_vehicle_assignment_log(){
        $data['title'] = _l('vehicle_assignment_log');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $this->load->view('reports/includes/vehicle_assignment_log', $data);
    }

    /**
     * report vehicle_assignment_summary
     * @return view
     */
    public function rp_vehicle_assignment_summary(){
        $data['title'] = _l('vehicle_assignment_summary');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $this->load->view('reports/includes/vehicle_assignment_summary', $data);
    }

    /**
     * view report
     * @return view
     */
    public function view_report(){
        $data_filter = $this->input->post();
        if(isset($data_filter['from_date'])){
            $data_filter['from_date'] = to_sql_date($data_filter['from_date']);
        }

        if(isset($data_filter['to_date'])){
            $data_filter['to_date'] = to_sql_date($data_filter['to_date']);
        }

        $this->load->model('currencies_model');
        $data = $data_filter;
        $data['title'] = _l($data_filter['type']);
        $data['currency'] = $this->currencies_model->get_base_currency();

        switch ($data_filter['type']) {
            case 'vehicle_assignment_summary':
                $data['vehicles'] = $this->fleet_model->get_vehicle();
                break;
            case 'cost_meter_trend':
                $data['vehicles'] = $this->fleet_model->get_vehicle();
                break;

            default:
                break;
        }

        $this->load->view('reports/details/'.$data_filter['type'], $data);
    }

    /**
     * Gets the data event chart.
     * @return json data chart
     */
    public function get_data_vehicle_assignment_summary_chart() {
        $data_filter = $this->input->get();
        $data_vehicle_assignment_summary = $this->fleet_model->get_data_vehicle_assignment_summary_chart($data_filter);


        echo json_encode([
            'data_vehicle_assignment_summary' => $data_vehicle_assignment_summary,
        ]);
        die();
    }

    /**
     * [rp_inspection_submissions_list description]
     * @return [type] [description]
     */
    public function rp_inspection_submissions_list(){
        $data['title'] = _l('inspection_submissions_list');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $this->load->view('reports/includes/inspection_submissions_list', $data);
    }

    /**
     * Gets the data event chart.
     * @return json data chart
     */
    public function get_data_inspection_submissions_list_chart() {
        $data_inspection_submissions_list = $this->fleet_model->get_data_inspection_submissions_list_chart();


        echo json_encode([
            'data_inspection_submissions_list' => $data_inspection_submissions_list,
        ]);
        die();
    }

    /**
     * [rp_inspection_submissions_summary description]
     * @return [type] [description]
     */
    public function rp_inspection_submissions_summary(){
        $data['title'] = _l('inspection_submissions_summary');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $this->load->view('reports/includes/inspection_submissions_summary', $data);
    }

    /**
     * [rp_fuel_summary description]
     * @return [type] [description]
     */
    public function rp_fuel_summary(){
        $data['title'] = _l('fuel_summary');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();
        $data['fuel_summary'] = $this->fleet_model->get_fuel_summary();


        $this->load->view('reports/includes/fuel_summary', $data);
    }

    /**
     * [rp_fuel_entries_by_vehicle description]
     * @return [type] [description]
     */
    public function rp_fuel_entries_by_vehicle(){
        $data['title'] = _l('fuel_entries_by_vehicle');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();
        $this->load->view('reports/includes/fuel_entries_by_vehicle', $data);
    }

    /**
     * Gets the data event chart.
     * @return json data chart
     */
    public function get_data_inspection_submissions_summary_chart() {
        $data_inspection_submissions_summary = $this->fleet_model->get_data_inspection_submissions_summary_chart();


        echo json_encode([
            'data_inspection_submissions_summary' => $data_inspection_submissions_summary,
        ]);
        die();
    }

    /**
     * [rp_vehicle_list description]
     * @return [type] [description]
     */
    public function rp_vehicle_list(){
        $data['title'] = _l('vehicle_list');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();
        $this->load->view('reports/includes/vehicle_list', $data);
    }


    /**
     * [rp_utilization_summary description]
     * @return [type] [description]
     */
    public function rp_utilization_summary(){
        $data['title'] = _l('utilization_summary');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();
        $this->load->view('reports/includes/utilization_summary', $data);
    }

    /**
     * [rp_vehicle_renewal_reminders description]
     * @return [type] [description]
     */
    public function rp_vehicle_renewal_reminders(){
        $data['title'] = _l('vehicle_renewal_reminders');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['vehicles'] = $this->fleet_model->get_vehicle();
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();
        $this->load->view('reports/includes/vehicle_renewal_reminders', $data);
    }

    /**
     * vehicle reminders table
     * @return json
     */
    public function vehicle_reminders_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                db_prefix() . 'fleet_vehicles.name as vehicle_name',
                db_prefix() . 'reminders.description as description',
                db_prefix() . 'reminders.date as date',
                'staff',
                'isnotified',
            ];

            $where        = [
                'AND rel_type="vehicle"',
                ];
            $from_date = '';
            $to_date   = '';
                

                

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'reminders';
            $join         = ['LEFT JOIN ' . db_prefix() . 'fleet_vehicles ON ' . db_prefix() . 'fleet_vehicles.id = ' . db_prefix() . 'reminders.rel_id',];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['rel_id'
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $categoryOutput = '<a href="' . admin_url('fleet/vehicle/' . $aRow['rel_id']) . '">' . $aRow['vehicle_name'] . '</a>';
                
                $row[] = $categoryOutput;
                $row[] = $aRow['description'];
                $row[] = _dt($aRow['date']);
                $row[] = '<a href="' . admin_url('staff/profile/' . $aRow['staff']) . '">' . staff_profile_image($aRow['staff'], [
                'staff-profile-image-small',
                ]) . ' ' . get_staff_full_name($aRow['staff']) . '</a>';
                if ($aRow['isnotified'] == 1) {
                    $row[] = _l('reminder_is_notified_boolean_yes');
                } else {
                    $row[] = _l('reminder_is_notified_boolean_no');
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * [rp_fuel_summary_by_location description]
     * @return [type] [description]
     */
    public function rp_fuel_summary_by_location(){
        $data['title'] = _l('fuel_summary_by_location');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['fuel_summary_by_location'] = $this->fleet_model->fuel_summary_by_location();
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();
        $this->load->view('reports/includes/fuel_summary_by_location', $data);
    }

    /**
     * inspection detail
     * @param  integer $inspection_id 
     * @return view               
     */
    public function inspection_detail($inspection_id) {

        $data['inspection'] = $this->fleet_model->get_inspections($inspection_id);
        $data['staffs']         = $this->staff_model->get();

        $this->load->view('inspections/inspection_detail', $data);
    }

    /**
     * [rp_inspection_failures_list description]
     * @return [type] [description]
     */
    public function rp_inspection_failures_list(){
        $data['title'] = _l('inspection_failures_list');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();
        $this->load->view('reports/includes/inspection_failures_list', $data);
    }

    /**
     * inspection failures table
     * @return json
     */
    public function inspection_failures_table(){
        if ($this->input->is_ajax_request()) {
           
            $select = [
                db_prefix(). 'fleet_inspection_question_forms.question as question',
                db_prefix(). 'fleet_vehicles.name as vehicle_name',
                db_prefix(). 'fleet_inspection_forms.name as inspection_form_name',
                db_prefix(). 'fleet_inspections.id as inspection_id',
                db_prefix(). 'fleet_inspections.datecreated as date',
            ];

            $where        = ['AND '.db_prefix().'fleet_inspection_question_box_description.is_fail = 1'];

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fleet_inspection_results';
            $join         = ['LEFT JOIN ' . db_prefix() . 'fleet_inspection_question_box_description ON ' . db_prefix() . 'fleet_inspection_question_box_description.questionboxdescriptionid = ' . db_prefix() . 'fleet_inspection_results.boxdescriptionid',
                'LEFT JOIN ' . db_prefix() . 'fleet_inspections ON ' . db_prefix() . 'fleet_inspections.id = ' . db_prefix() . 'fleet_inspection_results.inspection_id',
                'LEFT JOIN ' . db_prefix() . 'fleet_inspection_forms ON ' . db_prefix() . 'fleet_inspection_forms.id = ' . db_prefix() . 'fleet_inspections.inspection_form_id',
                'LEFT JOIN ' . db_prefix() . 'fleet_vehicles ON ' . db_prefix() . 'fleet_vehicles.id = ' . db_prefix() . 'fleet_inspections.vehicle_id',
                'LEFT JOIN ' . db_prefix() . 'fleet_inspection_question_forms ON ' . db_prefix() . 'fleet_inspection_question_forms.questionid = ' . db_prefix() . 'fleet_inspection_question_box_description.questionid AND rel_type = "inspection_form"',
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['vehicle_id', 'is_fail'
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = _dt($aRow['date']);
                $row[] = '<a href="' . admin_url('fleet/inspection_detail/' . $aRow['inspection_id']) . '">' . _l('submission').' #'.$aRow['inspection_id'] . '</a>';
                $row[] = $aRow['inspection_form_name'];
                $row[] = $aRow['question'];
               
                $row[] = '<a href="' . admin_url('fleet/vehicle/' . $aRow['vehicle_id']) . '">' . $aRow['vehicle_name'] . '</a>';
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * [inspection schedules]
     * @return [type] [description]
     */
    public function rp_inspection_schedules(){
        $data['title'] = _l('inspection_schedules');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();
        $this->load->view('reports/includes/inspection_schedules', $data);
    }

    /**
    * inspections schedules table
    * @return json 
    */
    public function inspection_schedules_table(){
        if ($this->input->is_ajax_request()) {
            if($this->input->post()){

                $this->load->model('currencies_model');
                $base_currency = $this->currencies_model->get_base_currency();
                $currency_name = '';
                if(isset($base_currency)){
                    $currency_name = $base_currency->name;
                }

                $select = [
                    db_prefix() .'fleet_inspections.id as id',
                    db_prefix() . 'fleet_vehicles.name as vehicle_name',
                    db_prefix() . 'fleet_inspection_forms.name as inspection_name',
                    db_prefix() . 'fleet_inspections.recurring as recurring',
                    db_prefix() . 'fleet_inspections.is_recurring_from as is_recurring_from',
                    db_prefix() . 'fleet_inspections.last_recurring_date as last_recurring_date',
                    db_prefix() . 'fleet_inspections.addedfrom as addedfrom',
                    db_prefix() . 'fleet_inspections.datecreated as datecreated',
                ];


                $where        = [];
                $aColumns     = $select;
                $sIndexColumn = 'id';
                $sTable       = db_prefix() . 'fleet_inspections';
                $join         = [
                    'LEFT JOIN ' . db_prefix() . 'fleet_vehicles ON ' . db_prefix() . 'fleet_vehicles.id = ' . db_prefix() . 'fleet_inspections.vehicle_id',
                    'LEFT JOIN ' . db_prefix() . 'fleet_inspection_forms ON ' . db_prefix() . 'fleet_inspection_forms.id = ' . db_prefix() . 'fleet_inspections.inspection_form_id',
                ];

                $is_report = $this->input->post("is_report");
                $vehicle_id = $this->input->post("id");
                if($vehicle_id != ''){
                    array_push($where, ' AND '.db_prefix() . 'fleet_inspections.vehicle_id ="'.$vehicle_id.'"');
                }

                $from_date = $this->input->post("from_date");
                $to_date = $this->input->post("to_date");

                if($from_date != '' && $to_date == ''){
                    $from_date = fe_format_date($from_date);
                    array_push($where, ' AND date('.db_prefix() . 'fleet_inspections.datecreated)="'.$from_date.'"');
                }
                if($from_date != '' && $to_date != ''){
                    $from_date = fe_format_date($from_date);
                    $to_date = fe_format_date($to_date);
                    array_push($where, ' AND date('.db_prefix() . 'fleet_inspections.datecreated) between "'.$from_date.'" AND "'.$to_date.'"');
                }

                $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() .'fleet_inspections.vehicle_id as vehicle_id'
                    
                ]);

                $output  = $result['output'];
                $rResult = $result['rResult'];  
                foreach ($rResult as $aRow) {
                    $row = [];

                    $row[] = '<a href="' . admin_url('fleet/vehicle/' . $aRow['vehicle_id']) . '">' . $aRow['vehicle_name'] . '</a>';
                    $row[] = get_staff_full_name($aRow['addedfrom']);
                    $row[] = _d($aRow['datecreated']);  
                    $row[] = '<a href="' . admin_url('fleet/inspection_detail/' . $aRow['id']) . '">' . _l('submission').' #'.$aRow['id'] . '</a>';
                    
                    $row[] = '<span class="text-nowrap">'.$aRow['inspection_name'].'</span>';  
                    if ($aRow['recurring'] != 0) {
                        $row[] = _l('reminder_is_notified_boolean_yes');
                    } else {
                        $row[] = _l('reminder_is_notified_boolean_no');
                    }

                    if($aRow['is_recurring_from'] != 0){
                        $row[] = '<a href="' . admin_url('fleet/inspection_detail/' . $aRow['is_recurring_from']) . '">' . _l('submission').' #'.$aRow['is_recurring_from'] . '</a>';
                    }else{
                        $row[] = '';
                    }

                    $row[] = _d($aRow['last_recurring_date']);

                    $output['aaData'][] = $row;             
                }

                echo json_encode($output);
                die();
            }
        }
    }

    /**
     * [cost meter trend]
     * @return [type] [description]
     */
    public function rp_cost_meter_trend(){
        $data['title'] = _l('cost_meter_trend');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();

        $this->load->view('reports/includes/cost_meter_trend', $data);
    }

    /**
     * Gets the data cost meter trend chart.
     * @return json data chart
     */
    public function get_data_cost_meter_trend_chart() {
        $data_filter = $this->input->get();
        $data_cost_meter_trend = $this->fleet_model->get_data_cost_meter_trend_chart($data_filter);

        echo json_encode([
            'data_cost_meter_trend' => $data_cost_meter_trend,
        ]);
        die();
    }

    /**
     * [ vehicle details]
     * @return [type] [description]
     */
    public function rp_vehicle_details(){
        $data['title'] = _l('vehicle_details');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $this->load->model('currencies_model');
        $data['currency'] = $this->currencies_model->get_base_currency();
        $data['vehicles'] = $this->fleet_model->get_vehicle();

        $this->load->view('reports/includes/vehicle_details', $data);
    }
}  