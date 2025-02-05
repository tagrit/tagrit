<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Expense_Categories extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Expense_category_model');

    }

    public function store()
    {
        $this->Expense_category_model->add([
            'name' => $this->input->post('name')
        ]);

        $this->db->insert(db_prefix() . 'expenses_categories', [
            'name' => $this->input->post('name'),
            'description' => $this->input->post('name'),
        ]);

        set_alert('success', 'Category added successfully.');
        redirect(admin_url('imprest/expense_categories'));
    }

    public function add_subcategory()
    {

        $subcategory_name = $this->input->post('subcategory_name');
        $category_id = $this->input->post('updated_category_id');
        if ($subcategory_name && $category_id) {
            $this->Expense_category_model->add_subcategory($category_id, $subcategory_name);
            set_alert('success', 'Subcategory added successfully.');
        }
        redirect(admin_url('imprest/expense_categories'));
    }

    public function delete_subcategory($subcategory_id)
    {
        $this->Expense_category_model->delete_subcategory($subcategory_id);
        set_alert('success', 'Subcategory deleted successfully.');
        redirect(admin_url('imprest/expense_categories'));
    }

    public function index()
    {
        $data['categories'] = $this->Expense_category_model->get();

        // Group subcategories by category ID
        $groupedCategories = [];
        foreach ($data['categories'] as $item) {
            // Initialize the category data
            $groupedCategories[$item['id']]['name'] = $item['name'];

            // Only add subcategory if it exists and is not empty
            if (!empty($item['subcategory_name']) && !empty($item['subcategory_id'])) {
                $groupedCategories[$item['id']]['subcategories'][] = [
                    'name' => $item['subcategory_name'],
                    'id' => $item['subcategory_id']
                ];
            }
        }

        $data['groupedCategories'] = $groupedCategories;
        $this->load->view('expense_categories/index', $data);
    }


}
