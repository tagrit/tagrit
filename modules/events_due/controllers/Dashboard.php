<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_model');

    }

    public function index()
    {
        $data['latest_events'] = $this->Dashboard_model->latest_events();
        $data['events_count'] = $this->Dashboard_model->events_count();
        $data['delegates_count'] = $this->Dashboard_model->delegates_count();
        $data['clients_per_month'] = $this->Dashboard_model->clients_per_month();
        $data['events_per_division'] = json_decode($this->Dashboard_model->events_per_division(), true);
        $data['revenue_per_division'] = $this->Dashboard_model->get_revenue_per_division();
        $this->load->view('dashboard', $data);
    }


}