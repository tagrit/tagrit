<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Contact_role extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('contact_role_model');
    }

    /**
     * Display the list of all affiliates.
     */
    function index()
    {
        // Check for permission awokumadivad
        if (!has_permission(CONTACT_ROLE_MODULE, '', 'view')) {
            return access_denied(CONTACT_ROLE_MODULE);
        }

        $data['title'] = _l(CONTACT_ROLE_MODULE);
        $data['roles'] = $this->contact_role_model->get();
        return $this->load->view('list', $data);
    }

    function form($id = '')
    {
        // Check for permission awokumadivad
        if (!has_permission(CONTACT_ROLE_MODULE, '', empty($id) ? 'create' : 'edit')) {
            return access_denied(CONTACT_ROLE_MODULE);
        }

        if ($this->input->post()) {
            // Get detail and update
            $data = $this->input->post(NULL, true);
            $data['email_notifications'] = json_encode($data['email_notifications']);
            $data['permissions'] = json_encode($data['permissions']);

            if (isset($data['id'])) unset($data['id']);

            if (empty($id)) {
                if ($this->contact_role_model->add($data))
                    set_alert('success', _l('added_successfully', _l('contact_role')));
            } else {
                if ($this->contact_role_model->update((int)$id, $data))
                    set_alert('success', _l('updated_successfully', _l('contact_role')));
            }
            return redirect(admin_url(CONTACT_ROLE_MODULE));
        }

        $data['title'] = _l(CONTACT_ROLE_MODULE);
        $data['contacts'] = $this->contact_role_model->get_contacts($id);
        $data['customer_permissions'] = get_contact_permissions();

        $data['title'] = _l('add_new', _l(CONTACT_ROLE_MODULE));
        if (!empty($id)) {
            $data['role'] = $this->contact_role_model->get($id);
            $data['role_permissions'] = (array)json_decode($data['role']->permissions);
            $data['email_notifications'] = (array)json_decode($data['role']->email_notifications);
            $data['title'] = _l('edit', strtolower(_l(CONTACT_ROLE_MODULE))) . ': ' . $data['role']->name;
        }

        return $this->load->view('form', $data);
    }

    public function delete($id)
    {
        if ($this->contact_role_model->delete((int)$id))
            set_alert('success', _l('deleted', _l('contact_role')));

        return redirect(admin_url(CONTACT_ROLE_MODULE));
    }
}
