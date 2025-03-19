<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function main()
    {
        $this->load->view('reports/main');
    }


}