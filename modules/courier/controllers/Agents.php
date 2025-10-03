<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Agents extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('courier/courier'); // Load the helper specific to the courier module
        $this->load->model('Agent_model');
        $this->load->model('Shipment_model');
        $this->load->library('form_validation');

    }


    public function main()
    {

        $group = $this->input->get('group', true) ?? 'dashboard';

        switch ($group) {
            case 'create_agent':
                $data['title'] = _l('Create Agent');
                $data['countries'] = $this->Shipment_model->get_countries();
                $data['group_content'] = $this->load->view('agents/create', $data, true);
                break;

            default:
                $data['agents'] = $this->Agent_model->get();
                $data['title'] = _l('List agents');
                $data['group_content'] = $this->load->view('agents/index', $data, true);
                break;
        }

        if ($this->router->fetch_method() == 'main' && !$this->input->is_ajax_request()) {
            $this->load->view('agents/main', $data);
        }

    }

    public function dashboard()
    {
        $this->load->view('agents/dashboard');
    }


    public function create()
    {
        $this->load->view('agents/create');
    }

    private function set_validation_rules()
    {
        if ($this->input->post('type') === 'individual') {
            $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'required|numeric|min_length[10]|max_length[15]');

            if (empty($_FILES['id_file']['name'])) {
                $this->session->set_flashdata('id_file_error', 'The Attachment is required.');
            }

            if (empty($_FILES['kra_file']['name'])) {
                $this->session->set_flashdata('kra_file_error', 'The Attachment is required.');
            }

            if (empty($_FILES['location_pin_file']['name'])) {
                $this->session->set_flashdata('location_pin_file_error', 'The Attachment is required.');
            }


            $this->form_validation->set_rules('address', 'Address', 'required|trim');
            $this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric|min_length[5]|max_length[50]');
            $this->form_validation->set_rules('country_id', 'Country', 'required');
            $this->form_validation->set_rules('state_id', 'State', 'required');
            $this->form_validation->set_rules('unique_number', 'Agent Number', 'required');
        }


        if ($this->input->post('type') === 'company') {
            $this->form_validation->set_rules('company_name', 'Company Name', 'required');
            $this->form_validation->set_rules('contact_name', 'Name', 'required');
            $this->form_validation->set_rules('contact_email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('company_password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('contact_phone_number', 'Phone Number', 'required|numeric|min_length[10]|max_length[15]');

            if (empty($_FILES['company_id_file']['name'])) {
                $this->session->set_flashdata('company_id_file_error', 'The Attachment is required.');
            }

            if (empty($_FILES['company_kra_file']['name'])) {
                $this->session->set_flashdata('company_kra_file_error', 'The Attachment is required.');
            }

            if (empty($_FILES['corporation_certificate_file']['name'])) {
                $this->session->set_flashdata('corporation_certificate_file_error', 'The Attachment is required.');
            }

            $this->form_validation->set_rules('company_address', 'Address', 'required|trim');
            $this->form_validation->set_rules('company_username', 'Username', 'required|alpha_numeric|min_length[5]|max_length[50]');
            $this->form_validation->set_rules('company_country_id', 'Country', 'required');
            $this->form_validation->set_rules('company_state_id', 'State', 'required');
            $this->form_validation->set_rules('company_unique_number', 'Agent Number', 'required');
        }

    }


    public function upload_file($folder, $name)
    {
        $upload_path = FCPATH . 'modules/courier/assets/' . $folder . '/';

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $file_name = time() . '_' . $_FILES[$name]['name'];
        $file_path = $upload_path . $file_name;

        if (move_uploaded_file($_FILES[$name]['tmp_name'], $file_path)) {
            return 'modules/courier/assets/' . $folder . '/' . $file_name;
        } else {
            set_alert('danger', 'File upload failed.');
        }

    }


    public function store()
    {

        $this->set_validation_rules();

        if ($this->form_validation->run() == FALSE) {

            $show_company_section = $this->input->post('type') === 'company';

            $this->session->set_userdata('show_company_section', $show_company_section);

            // Validation failed; set flashdata for both errors and input values
            foreach ($this->input->post() as $key => $value) {
                $this->session->set_flashdata($key . '_error', form_error($key));
                $this->session->set_flashdata($key, $value); // Preserve form input values
            }

            redirect('admin/courier/agents/main?group=create_agent');
        } else {

            $data = [];

            // Insert into staff table
            if ($this->input->post('type') === 'individual') {
                $data = [
                    'firstname' => $this->input->post('first_name'),
                    'lastname' => $this->input->post('last_name'),
                    'email' => $this->input->post('email'),
                    'password' => app_hash_password($this->input->post('password')),
                ];
            }

            if ($this->input->post('type') === 'company') {
                $data = [
                    'firstname' => $this->input->post('contact_name'),
                    'lastname' => $this->input->post('contact_name'),
                    'email' => $this->input->post('contact_email'),
                    'password' => app_hash_password($this->input->post('company_password')),
                ];
            }


            $this->db->insert(db_prefix() . 'staff', $data);
            $staff_id = $this->db->insert_id();

            // Get the role_id for "Courier: Agent"
            $this->db->where('name', 'Courier: Agent');
            $courier_agent_role = $this->db->get(db_prefix() . 'roles')->row();

            if ($courier_agent_role) {
                $role_id = $courier_agent_role->roleid;
                $this->db->where('staffid', $staff_id);
                $this->db->update(db_prefix() . 'staff', ['role' => $role_id]);

                $this->db->where('roleid', $role_id);
                $role = $this->db->get(db_prefix() . 'roles')->row();

                $permissions = isset($role->permissions) ? unserialize($role->permissions) : null;
                foreach ($permissions as $feature => $capabilities) {
                    foreach ($capabilities as $capability) {
                        $this->db->insert(db_prefix() . 'staff_permissions', [
                            'staff_id' => $staff_id,
                            'feature' => $feature,
                            'capability' => $capability
                        ]);
                    }
                }
            }

            $unique_number = '';

            if ($this->input->post('type') === 'individual') {
                $unique_number = $this->input->post('unique_number');
            } else {
                $unique_number = $this->input->post('company_unique_number');
            }

            $parts = explode('/', $unique_number);
            $agent_number = $parts[2];
            $agent_data = [];

            if ($this->input->post('type') === 'individual') {

                $id_file_url = $this->upload_file('agent_ids', 'id_file');
                $kra_file_url = $this->upload_file('agent_kras', 'kra_file');
                $location_file_url = $this->upload_file('agent_location_files', 'location_pin_file');

                $agent_data = [
                    'staff_id' => $staff_id,
                    'phone_number' => $this->input->post('phone_number'),
                    'address' => $this->input->post('address'),
                    'unique_number' => $unique_number,
                    'agent_number' => $agent_number,
                    'id_file_url' => $id_file_url,
                    'location_file_url' => $location_file_url,
                    'kra_file_url' => $kra_file_url,
                    'country_id' => $this->input->post('country_id'),
                    'state_id' => $this->input->post('state_id'),
                    'agent_type' => $this->input->post('type')
                ];
            }


            if ($this->input->post('type') === 'company') {

                $id_file_url = $this->upload_file('agent_ids', 'company_id_file');
                $kra_file_url = $this->upload_file('agent_kras', 'company_kra_file');
                $cert_of_corp_file = $this->upload_file('agent_corporation_certificates', 'corporation_certificate_file');

                $agent_data = [
                    'staff_id' => $staff_id,
                    'phone_number' => $this->input->post('contact_phone_number'),
                    'address' => $this->input->post('company_address'),
                    'company_name' => $this->input->post('company_name'),
                    'unique_number' => $unique_number,
                    'agent_number' => $agent_number,
                    'id_file_url' => $id_file_url,
                    'kra_file_url' => $kra_file_url,
                    'cert_of_corp_url' => $cert_of_corp_file,
                    'country_id' => $this->input->post('company_country_id'),
                    'state_id' => $this->input->post('company_state_id'),
                    'agent_type' => $this->input->post('type')

                ];
            }

            $this->db->insert(db_prefix() . '_agents', $agent_data);
            set_alert('success', 'Agent added successfully.');
            redirect('admin/courier/agents/main?group=list_agents');
        }
    }


    public function sync_role_permissions()
    {
        // Syncing logic
        $this->db->where('name', 'Courier: Agent');
        $courier_agent_role = $this->db->get(db_prefix() . 'roles')->row();

        if ($courier_agent_role) {
            $role_id = $courier_agent_role->roleid;

            $this->db->where('roleid', $role_id);
            $role = $this->db->get(db_prefix() . 'roles')->row();

            if ($role) {
                $permissions = unserialize($role->permissions);

                $this->db->where('role', $role_id);
                $staff_members = $this->db->get(db_prefix() . 'staff')->result();

                foreach ($staff_members as $staff) {
                    // Clear old permissions
                    $this->db->where('staff_id', $staff->staffid);
                    $this->db->delete(db_prefix() . 'staff_permissions');

                    // Insert new permissions
                    if ($permissions) {
                        foreach ($permissions as $feature => $capabilities) {
                            foreach ($capabilities as $capability) {
                                $this->db->insert(db_prefix() . 'staff_permissions', [
                                    'staff_id' => $staff->staffid,
                                    'feature' => $feature,
                                    'capability' => $capability
                                ]);
                            }
                        }
                    }
                }

                // Return success response
                echo json_encode(['message' => 'Permissions synced successfully.']);
            } else {
                // Role not found, return error
                http_response_code(404);
                echo json_encode(['message' => 'Role not found.']);
            }
        } else {
            // Return error if the role is not found
            http_response_code(404);
            echo json_encode(['message' => 'Courier: Agent role not found.']);
        }
    }


    public function agent_number()
    {
        if ($this->input->is_ajax_request()) {

            $country_id = $this->input->post('country_id');
            $state_id = $this->input->post('state_id');

            $country_code = $this->Shipment_model->get_countries($country_id)[0]->iso2;
            $this->db->select_max('agent_number');
            $this->db->where('country_id', $country_id);
            $agent_number = $this->db->get(db_prefix() . '_agents')->row()->agent_number;

            $new_agent_number = 0;

            if ($agent_number) {
                $new_agent_number = $agent_number + 1;
            } else {
                $new_agent_number = 1;
            }


            $new_unique_number = $country_code . '/' . $state_id . '/' . $new_agent_number;

            echo json_encode([
                'success' => true,
                'new_agent_number' => $new_unique_number
            ]);
        } else {
            show_404();
        }

    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $agent = $this->db->get(db_prefix() . '_agents')->row();

        if ($agent) {
            $staff_id = $agent->staff_id;

            // Begin a transaction to ensure atomic operations
            $this->db->trans_start();

            // Delete the staff record
            $this->db->where('staffid', $staff_id);
            $this->db->delete(db_prefix() . 'staff');

            // Delete all permissions for the staff
            $this->db->where('staff_id', $staff_id);
            $this->db->delete(db_prefix() . 'staff_permissions');

            // Delete the agent from the agents table
            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . '_agents');

            // Complete the transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                set_alert('danger', 'Failed to delete agent and related records.');
                redirect('admin/courier/agents/main?group=list_agents');

            } else {
                set_alert('success', 'Agent and related records deleted successfully.');
                redirect('admin/courier/agents/main?group=list_agents');
            }
        } else {
            set_alert('danger', 'Agent not found.');
            redirect('admin/courier/agents/main?group=list_agents');
        }
    }

    public function update_status()
    {
        $agent_id = $this->input->post('agent_id');
        $status = $this->input->post('status');

        $this->db->where('id', $agent_id);
        $success = $this->db->update(db_prefix().'_agents', ['status' => $status]);

        // Return JSON response
        echo json_encode(['success' => $success]);

    }

}