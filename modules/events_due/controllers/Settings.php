<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function main()
    {
        $group = $this->input->get('group', true) ?? 'import_event_registrations';
        $data['group'] = $group;

        switch ($group) {
            case 'import_event_registration':
                $data['group_content'] = $this->load->view('settings/import_event_registrations', $data, true);
                break;
            default:
                $data['group_content'] = $this->load->view('settings/import_events_registrations', [], true);
                break;
        }

        if ($this->router->fetch_method() == 'main' && !$this->input->is_ajax_request()) {
            $this->load->view('settings/main', $data);
        }
    }


}