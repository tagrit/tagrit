<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class for managing the payment logs through mpesa
 */
class Logs extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoices_model');
    }

    public function index()
    {
        $data = [];

        if ($this->input->is_ajax_request()) {

            $this->app->get_table_data(module_views_path(MPESA_GATEWAY_MODULE_NAME, 'logs/table'));
        }

        return $this->load->view('logs/manage', $data);
    }
}