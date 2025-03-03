<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Fund_request_model');

    }

    public function index()
    {
        $data = [];

        $query = $this->db->select('categories.name AS category_name, SUM(items.amount_requested) AS total_amount')
            ->from(db_prefix() . '_expense_categories categories')
            ->join(db_prefix() . '_expense_subcategories subcategories', 'subcategories.category_id = categories.id')
            ->join(db_prefix() . '_fund_request_items items', 'items.expense_subcategory_id = subcategories.id')
            ->group_by('categories.id')
            ->get();

        $labels = [];
        $values = [];

        foreach ($query->result() as $row) {
            $labels[] = $row->category_name;
            $values[] = $row->total_amount;
        }

        $data['fund_requests'] = $this->Fund_request_model->get();
        $data['labels'] = $labels;
        $data['values'] = $values;
        $fund_request_details = $this->Fund_request_model->get_fund_request_details(null,true);

        if (!empty($fund_request_details)) {
            $data['totalAmountRequested'] = $fund_request_details['total_amount_requested'] - $this->Fund_request_model->get_pending_approval_amount();
            $data['totalAmountCleared'] = $fund_request_details['total_amount_cleared'];
        }

        $this->load->view('dashboard', $data);
    }


}
