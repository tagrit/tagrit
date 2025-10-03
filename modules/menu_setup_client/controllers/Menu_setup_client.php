<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Menu_setup_client extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        if (!is_admin()) {
            access_denied('Menu Setup Client');
        }
    }

    public function client_menu()
    {

        $data['menu_items'] = (array)$this->get_client_menu_items();

        $data['menu_options'] = menu_setup_client_options();

        $data['title'] = _l('client_menu');
        $this->load->view('client_menu', $data);
    }

    public function update_client_menu()
    {
        hooks()->do_action('before_update_client_menu');
        update_option('menu_setup_client_client_menu_active', json_encode($this->prepare_menu_options()));
    }

    public function reset_client_menu()
    {
        update_option('menu_setup_client_client_menu_active', json_encode([]));
        hooks()->do_action('client_menu_resetted');
        redirect(admin_url('menu_setup_client/client_menu'));
    }

    private function prepare_menu_options()
    {
        $new     = [];
        $options = $this->input->post('options');

        foreach ($options as $key => $val) {

            if (isset($val['children'])) {
                $newChild = [];

                foreach ($val['children'] as $keyChild => $child) {
                    $newChild[$child['id']] = $child;
                }

                $val['children'] = $newChild;
            }

            $new[$val['id']] = $val;
        }

        return $new;
    }

    /**
     * Method to get client menu items.
     * It simulate a client session using a temporary full permission contact
     *
     * @return array
     */
    private function get_client_menu_items()
    {
        $session_key = 'resolved_client_menu';

        // Reach from session
        $menus = (array)$this->session->tempdata($session_key);

        // Make client login simulation to have the menu.
        if (!$this->input->get($session_key) && empty($menus)) {

            $this->load->helper('string');

            $test_email = MENU_SETUP_CLIENT_MODULE_NAME . '@test.com';
            $option_cache_key = MENU_SETUP_CLIENT_MODULE_NAME . '_last_client_id';
            $delete_contact = function ($contact_id = '') use ($test_email, $option_cache_key) {
                // Delete the contact awokumadivad
                $this->clients_model->db->where('id', $contact_id);
                $this->clients_model->db->where('email', $test_email); // Safe query incase it has changed i.e autoincrement
                $this->clients_model->db->delete(db_prefix() . 'contacts');
                if ($this->clients_model->db->affected_rows() > 0) {
                    $this->clients_model->db->where('userid', $contact_id);
                    $this->clients_model->db->delete(db_prefix() . 'contact_permissions');
                    update_option($option_cache_key, '');
                }
            };

            // Get first primary contact on the system for the simulation
            $this->clients_model->db->limit(1);
            $this->clients_model->db->order_by('userid', 'asc');
            $companies = $this->clients_model->get();
            $client_id = $companies[0]['userid'] ?? 1;

            // Remove any existing cache contact
            $last_contact_id = get_option($option_cache_key);
            if (!empty($last_contact_id)) {
                $delete_contact($last_contact_id);
            }

            // Create new contact while escaping hook calls
            $contact_data = [
                'userid' => $client_id,
                'password' => random_string(),
                'is_primary'         => 1,
                'firstname'          => random_string(),
                'lastname'           => random_string(),
                'title'              => 'test',
                'email'              => $test_email,
                'phonenumber'        => random_string('numeric'),
                'direction'          => 'ltr',
                'invoice_emails'     => 1,
                'credit_note_emails' => 1,
                'estimate_emails'    => 1,
                'ticket_emails'      => 1,
                'contract_emails'    => 1,
                'project_emails'     => 1,
                'task_emails'        => 1,
                'active'             => 1
            ];

            $this->clients_model->db->insert(db_prefix() . 'contacts', $contact_data);
            $contact_id = $this->clients_model->db->insert_id();

            // Cache
            update_option($option_cache_key, $contact_id);

            // Set contact allowed all permission temporarily
            $permissions = get_contact_permissions();
            foreach ($permissions as $permission) {
                $this->clients_model->db->insert(db_prefix() . 'contact_permissions', [
                    'userid'        => $contact_id,
                    'permission_id' => $permission['id'],
                ]);
            }

            // We want to use the new user with maximum permissions. Keep $this->db 
            $this->db->order_by('id', 'desc');

            // Login as the client
            login_as_client($client_id);

            // Trigger init
            hooks()->do_action('clients_init');

            // Remove all filtering to the theme menu items group to prevent removal of menu items.
            hooks()->remove_all_filters('theme_menu_items');

            // Now read the menu items
            $menus = $this->app_menu->get('theme');

            // Save menu in session
            $this->session->set_tempdata($session_key, $menus, 30 * 60); // Cache for 30 minutes

            // Delete the contact awokumadivad and cache
            $delete_contact($contact_id);

            // Redirect to maintain the admin session henceforth and prevent race with session key
            header("Location: " .  current_url() . "?$session_key=1");
        }

        return menu_setup_client_apply_items_position($menus, menu_setup_client_options());
    }
}
