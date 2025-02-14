<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Registrations extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Registration_model');

    }

    public function index()
    {
        $this->load->view('registrations/index');
    }

    public function view()
    {
        $this->load->view('registrations/view');

    }

}