<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Registration_model');

    }

    public function main()
    {
        $data['registrations'] = $this->Registration_model->get();
        $this->load->view('reports/main',$data);
    }


}