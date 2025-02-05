<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Fund_reconciliations extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Fund_request_model');
        $this->load->model('emails_model');

    }

    public function send_email($fund_request_number, $slug, $staff_name, $staff_email, $status)
    {
        $template_slug = $slug;
        $email = $staff_email;
        $merge_fields = [
            'staff_name' => $staff_name,
            'status' => $status,
            'fund_request_number' => $fund_request_number
        ];

        $this->emails_model->send_email_template($template_slug, $email, $merge_fields);
    }

    public function statusEmailNotificationEnabled($status)
    {

        $notification_statuses = [];

        $query = $this->db->select('notifications')
            ->where('id', 1)
            ->get(db_prefix() . '_settings');

        if ($query->num_rows() > 0) {
            $result = $query->row();
            $serializedData = $result->notifications;
            $notification_statuses = unserialize($serializedData);
        }

        return in_array($status, $notification_statuses);

    }


    public function create()
    {
        $this->load->view('fund_reconciliations/create');
    }

    public function edit($fund_request_id)
    {
        $data = $this->Fund_request_model->get_fund_request_details($fund_request_id);
        $this->load->view('fund_reconciliations/edit', $data);
    }

    public function request($fund_request_id)
    {
        try {
            foreach ($_FILES as $fund_request_item_id => $file) {

                $upload_path = FCPATH . 'modules/imprest/assets/' . $fund_request_id . '/';

                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0755, true);
                }

                $file_name = time() . '_' . $file['name'];
                $file_path = $upload_path . $file_name;

                if (move_uploaded_file($file['tmp_name'], $file_path)) {

                    $receipt_url = 'modules/imprest/assets/' . $fund_request_id . '/' . $file_name;
                    $data = [
                        'receipt_url' => $receipt_url, // New value for receipt_url
                    ];

                    // Perform the update
                    $this->db->where('id', $fund_request_item_id);
                    $this->db->update(db_prefix() . '_fund_request_items', $data);

                    if ($this->input->post('flag') === 'solve-reconciliation') {
                        $this->db->where('cleared !=', 1)
                            ->update(db_prefix() . '_fund_request_items', ['cleared' => 3]);
                    }

                }
            }

            // Update the fund request status to approved
            if ($this->input->post('flag') === 'reconcile' ||
                $this->input->post('flag') === 'solve-reconciliation'
            ) {
                if (!$this->Fund_request_model->update($fund_request_id, [
                    'status' => 'pending_reconciliation'
                ])) {
                    throw new Exception('Error updating fund request status!');
                }

                if ($this->input->post('flag') === 'solve-reconciliation') {
                    set_alert('success', 'Reconciliation Marked as resolved successfully!');
                    redirect(admin_url('imprest/fund_requests/index'));
                }

                if ($this->input->post('flag') === 'reconcile') {
                    // Set success alert and redirect
                    set_alert('success', 'Fund Request submitted for reconciliation successfully!');
                    redirect(admin_url('imprest/fund_requests/index'));
                }

            }

            // Set success alert and redirect
            set_alert('success', 'Changes saved successfully!');
            redirect(admin_url('imprest/fund_requests/index'));

            // Commit the transaction if everything is successful
            $this->db->trans_commit();


        } catch (Exception $e) {

            // Rollback the transaction in case of error
            $this->db->trans_rollback();

            // Set error alert and redirect
            set_alert('danger', $e->getMessage());
            redirect(admin_url('imprest/fund_requests/index'));
        }

    }


    public function view($fund_request_id)
    {
        // Fetch the current fund request details
        $data = $this->Fund_request_model->get_fund_request_details($fund_request_id);

        if ($data['fund_request_details']['status'] === 'pending_reconciliation') {
            $this->Fund_request_model->update($fund_request_id, [
                'status' => 'reconciliation_ongoing'
            ]);
        }

        $this->load->view('fund_reconciliations/view', $data);
    }


    public function reject($fund_request_id)
    {

        // Start a transaction
        $this->db->trans_begin();

        try {

            // Update the fund request status to approved
            if (!$this->Fund_request_model->update($fund_request_id, [
                'status' => 'reconciliation_rejected',
                'rejection_reason' => $this->input->post('rejection_reason')
            ])) {
                throw new Exception('Error updating fund request status!');
            }

            if ($this->statusEmailNotificationEnabled('Reconciliation Rejected')) {
                $staff_name = $this->Fund_request_model->get_staff_name($this->get_requested_by($fund_request_id));
                $staff_email = $this->Fund_request_model->get_staff_email($this->get_requested_by($fund_request_id));
                $reference_no = $this->Fund_request_model->get_reference_no(($fund_request_id));
                $this->send_email($reference_no, 'fund-request-updated', $staff_name, $staff_email, 'Reconciliation Rejected');
            }

            // Commit the transaction if everything is successful
            $this->db->trans_commit();

            // Set success alert and redirect
            set_alert('success', 'Funds Reconciliation Rejected successfully!');
            redirect(admin_url('imprest/fund_requests/index'));

        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $this->db->trans_rollback();

            // Set error alert and redirect
            set_alert('danger', $e->getMessage());
            redirect(admin_url('imprest/fund_requests/index'));
        }
    }

    public function get_requested_by($fund_request_id)
    {
        $this->db->select('requested_by');
        $this->db->from(db_prefix() . '_fund_requests');
        $this->db->where('id', $fund_request_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->requested_by;
        } else {
            return null;
        }
    }


    public function add_account_history_entry($reference_no, $amount, $crediting_account, $debiting_account, $staff_id, $rel_id)
    {
        $entries = [
            //category account to debit
            [
                'account' => $debiting_account,
                'credit' => 0,
                'debit' => $amount,
                'rel_id' => $rel_id,
                'split' => $crediting_account, //account_id to credit
            ],

            //staff account to credit
            [
                'account' => $crediting_account,
                'credit' => $amount,
                'debit' => 0,
                'rel_id' => $rel_id,
                'split' => $debiting_account, //account_id to debit
            ]
        ];

        $common_data = [
            'datecreated' => date('Y-m-d H:i:s'),
            'addedfrom' => $staff_id,
            'date' => date('Y-m-d'),
            'rel_type' => 'expense',
            'customer' => 0,
            'tax' => 0,
            'cleared' => 0,
            'reference_no' => $reference_no,
        ];

        foreach ($entries as $entry) {
            $this->db->insert(db_prefix() . 'acc_account_history', array_merge($entry, $common_data));
        }
    }

    public function create_account($category_name)
    {

        // Insert the category into acc_account_type_details
        $this->db->insert(db_prefix() . 'acc_account_type_details', [
            'name' => $category_name,
            'account_type_id' => 14,
            'note' => $category_name,
        ]);

        // Get the inserted ID
        $account_detail_type_id = $this->db->insert_id();

        // Insert into acc_accounts using the retrieved account_detail_type_id
        $this->db->insert(db_prefix() . 'acc_accounts', [
            'name' => $category_name,
            'account_type_id' => 14,
            'account_detail_type_id' => $account_detail_type_id,
            'active' => 1,
        ]);

        return $this->db->insert_id();
    }


    public function clear($fund_request_id)
    {
        // Start a transaction
        $this->db->trans_begin();

        try {

            $unclearedItems = $this->db->where('fund_request_id', $fund_request_id)
                ->group_start() // Start grouping the WHERE conditions
                ->where('cleared', 0)
                ->or_where('cleared', 2)
                ->group_end() // End grouping
                ->get(db_prefix() . '_fund_request_items')
                ->result();

            if (!empty($unclearedItems)) {
                throw new Exception('Kindly make sure all Fund Request Items are cleared!');
            }

            // Update the fund request status to approved
            if (!$this->Fund_request_model->update($fund_request_id, [
                'status' => 'cleared',
            ])) {
                throw new Exception('Error updating fund request status!');
            }

            $query = $this->db->select('categories.name AS category_name, SUM(fund_request_items.amount_requested) AS total_amount')
                ->from(db_prefix() . '_fund_request_items AS fund_request_items')
                ->join(db_prefix() . '_expense_subcategories AS subcategories', 'fund_request_items.expense_subcategory_id = subcategories.id', 'left')
                ->join(db_prefix() . '_expense_categories AS categories', 'subcategories.category_id = categories.id', 'left')
                ->where('fund_request_items.fund_request_id', $fund_request_id)
                ->group_by('categories.id')
                ->get();

            $category_amounts = $query->result_array();

            // create journal for mapping staff amounts to respective accounts
            foreach ($category_amounts as $row) {

                // Fetch the ID from acc_accounts where name matches category_name --> Debiting Account
                $debiting_account = $this->db->select('id')
                    ->from(db_prefix().'acc_accounts')
                    ->where('name', $row['category_name'])
                    ->get()
                    ->row();

                //create account if it does not exist
                if (!$debiting_account) {
                    $debiting_account_id = $this->create_account($row['category_name']);
                } else {
                    $debiting_account_id = $debiting_account->id;
                }

                $reference_no = $this->Fund_request_model->get_reference_no(($fund_request_id));
                $staff = $this->Fund_request_model->get_staff_name($this->get_requested_by($fund_request_id));
                $staff_id = $this->get_requested_by($fund_request_id);

                // Fetch the ID from acc_accounts where name matches $staff --> Crediting Account
                $crediting_account = $this->db->select('id')
                    ->from(db_prefix().'acc_accounts')
                    ->where('name', $staff)
                    ->get()
                    ->row();
                $crediting_account_id = $crediting_account->id;

                //fetch relationship id
                $rel = $this->db->select('id')
                    ->from(db_prefix().'expenses_categories')
                    ->where('name', $row['category_name'])
                    ->get()
                    ->row();
                $rel_id = $rel->id;

                $this->add_account_history_entry($reference_no, $row['total_amount'], $crediting_account_id, $debiting_account_id, $staff_id, $rel_id);

            }

            if ($this->statusEmailNotificationEnabled('Cleared')) {
                $staff_name = $this->Fund_request_model->get_staff_name($this->get_requested_by($fund_request_id));
                $staff_email = $this->Fund_request_model->get_staff_email($this->get_requested_by($fund_request_id));
                $reference_no = $this->Fund_request_model->get_reference_no(($fund_request_id));
                $this->send_email($reference_no, 'fund-request-updated', $staff_name, $staff_email, 'Cleared');
            }

            // Commit the transaction if everything is successful
            $this->db->trans_commit();

            // Set success alert and redirect
            set_alert('success', 'Funds Reconciliation Cleared successfully!');
            redirect(admin_url('imprest/fund_requests/index'));

        } catch (Exception $e) {

            // Rollback the transaction in case of error
            $this->db->trans_rollback();

            // Set error alert and redirect
            set_alert('danger', $e->getMessage());
            redirect(admin_url('imprest/fund_reconciliations/view/' . $fund_request_id));
        }
    }


    public function cleared_view($fund_request_id)
    {
        $data = $this->Fund_request_model->get_fund_request_details($fund_request_id);
        $this->load->view('fund_reconciliations/cleared_view', $data);
    }

    public function clear_item()
    {
        // Get input from the AJAX request
        $fund_request_item_id = $this->input->post('fund_request_item_id');
        $action = $this->input->post('item_action');

        // Check if the required inputs are provided
        if (!$fund_request_item_id && !$action) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid input. Fund request ID and action are required.',
            ]);
            return;
        }

        $message = '';

        // Update the cleared status
        if ($action === 'clear') {
            $this->db->where('id', $fund_request_item_id);
            $this->db->update(db_prefix() . '_fund_request_items', ['cleared' => 1]);
            $message = 'Item cleared successfully.';
        }

        if ($action === 'reject') {
            $this->db->where('id', $fund_request_item_id);
            $this->db->update(db_prefix() . '_fund_request_items', [
                'cleared' => 2,
                'receipt_url' => 'resolve',
            ]);

            $message = 'Item Rejected successfully.';
        }

        // Check if the update was successful
        if ($this->db->affected_rows() > 0) {
            echo json_encode([
                'success' => true,
                'message' => $message,
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to clear the item. It may already be cleared or does not exist.',
            ]);
        }
    }

}
