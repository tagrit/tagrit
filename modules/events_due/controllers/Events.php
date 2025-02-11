<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Events extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Event_model');

    }

    public function index()
    {
        $this->load->view('events/index');
    }

    public function create()
    {
        $this->load->view('events/create');

    }

    public function store()
    {

    }

    public function edit($event_id)
    {
        $this->load->view('events/edit');
    }

    public function update($event_id)
    {

    }
}