<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Expense_category_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . '_expense_categories';

    }

    public function get()
    {
        $this->db->select('categories.id, categories.name, subcategories.name as subcategory_name, subcategories.id as subcategory_id');
        $this->db->from($this->table . ' as categories');
        $this->db->join(db_prefix() . '_expense_subcategories as subcategories', 'subcategories.category_id = categories.id', 'left');
        $this->db->order_by('categories.id', 'ASC');

        return $this->db->get()->result_array();
    }


    public function get_categories($id = null, $additional_funds = false)
    {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->get($this->table)->row_array();
        }

        if (!$additional_funds) {
            $this->db->where('name !=', 'Additional Funds');
        }

        return $this->db->get($this->table)->result();
    }


    public function add_subcategory($category_id, $name)
    {
        $this->db->insert(db_prefix() . '_expense_subcategories', ['category_id' => $category_id, 'name' => $name]);
    }

    public function delete_subcategory($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . '_expense_subcategories');
    }

    public function add($data): bool|int
    {
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        } else {
            log_message('error', 'Insert failed for shipment stop: ' . $this->db->last_query());
            return false;
        }
    }


    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }
}
