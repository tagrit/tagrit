<?php defined('BASEPATH') or exit('No direct script access allowed');

class Api extends AdminController
{
    const ENTITY_NAME = 'api_users';

    /**
     * Display the list of all api tokens.
     */
    function index()
    {
        // Check for permission
        if (!staff_can('view', 'perfex_saas_api_user')) {
            return access_denied('perfex_saas_api_user');
        }

        // Show list of api users
        $data['api_users'] = perfex_saas_api_users();
        $data['title'] = _l('perfex_saas_api_user');
        $this->load->view('api/manage', $data);
    }

    /**
     * Create a new company.
     */
    function create_user()
    {
        // Check permission to create api
        if (!staff_can('create', 'perfex_saas_api_user')) {
            return access_denied('perfex_saas_api_user');
        }

        $this->create_or_update_user();

        // Show form to create a new company
        $data['title'] = _l('perfex_saas_api_user');
        $this->load->view('api/users/form', $data);
    }

    /**
     * Edit an existing api user.
     *
     * @param string $id The ID of the api user
     */
    function edit_user($id)
    {
        if (!staff_can('edit', 'perfex_saas_api_user')) {
            return access_denied('perfex_saas_api_user');
        }

        $this->create_or_update_user((int)$id);

        // Show form to edit the new company
        $data['api_user'] = perfex_saas_api_users($id);
        $data['title'] = _l('perfex_saas_api_user');
        $this->load->view('api/users/form', $data);
    }

    /**
     * Delete an api user.
     *
     * @param string $id The ID of the api user to delete
     * @return void
     */
    function delete_user($id)
    {
        if (!staff_can('delete', 'perfex_saas_api_user')) {
            return access_denied('perfex_saas_api_user');
        }

        $id = (int)$id;
        $removed = $this->perfex_saas_model->delete(self::ENTITY_NAME, $id);
        if ($removed)
            set_alert('success', _l('deleted', _l('perfex_saas_api_user')));
        else
            set_alert('danger', _l('perfex_saas_error_completing_action') . (is_string($removed) ? $removed : ''));

        return redirect(admin_url(PERFEX_SAAS_ROUTE_NAME . '/api'));
    }

    private function create_or_update_user($id = '')
    {
        if ($this->input->post()) {

            // Make some validation
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', _l('perfex_saas_name'), 'required');
            $this->form_validation->set_rules('token', _l('perfex_saas_api_token'), 'required');

            if ($this->form_validation->run() !== false) {

                $form_data = $this->input->post(NULL, true);

                try {

                    $data = [
                        'name' => trim($form_data['name']),
                        'token' => trim($form_data['token']),
                        'permissions' => json_encode($form_data['permissions'] ?? [])
                    ];

                    $token_length = strlen($data['token'] ?? '');
                    if ($token_length > 150 || $token_length < 32) {
                        throw new \Exception(_l('perfex_saas_api_key_invalid'), 1);
                    }

                    if ($id && $id == $form_data['id'])
                        $data['id'] = $id;


                    $_id = $this->perfex_saas_model->add_or_update(self::ENTITY_NAME, $data);
                    if ($_id) {

                        set_alert('success', _l('updated_successfully', _l('perfex_saas_api_user')));
                        return redirect(admin_url(PERFEX_SAAS_ROUTE_NAME . '/api'));
                    }

                    // Log error
                    log_message('error', _l('perfex_saas_error_completing_action') . ':' . ($this->db->error() ?? ''));

                    throw new \Exception(_l('perfex_saas_error_completing_action'), 1);
                } catch (\Throwable $th) {

                    set_alert('danger', $th->getMessage());
                    return perfex_saas_redirect_back();
                }
            }
        }
    }
}