<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Fund_requests extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Event_model');
        $this->load->model('Expense_category_model');
        $this->load->model('Fund_request_model');
        $this->load->model('Event_details_model');
        $this->load->model('emails_model');
        $this->show_notification();
    }

    public function index()
    {

        $data['fund_requests'] = $this->Fund_request_model->get();
        $this->load->view('fund_requests/index', $data);
    }


    public function show_notification()
    {

        // Check if the user has permission to view all fund requests
        if (!staff_can('view_all_fund_requests', 'impress-fund-requests')) {

            $data = [];

            $query = $this->db->select('fund_reconciliation')
                ->where('id', 1)
                ->get(db_prefix() . '_settings');

            if ($query->num_rows() > 0) {
                $result = $query->row();
                $serializedData = $result->fund_reconciliation;
                $unSerializedData = unserialize($serializedData);
                $data['max_unreconciled_amount'] = $unSerializedData['max_unreconciled_amount'];
            }

            $fund_request_details = $this->Fund_request_model->get_fund_request_details(null,true);

            if (!empty($fund_request_details)) {
                $data['totalAmountRequested'] = $fund_request_details['total_amount_requested'] - $this->Fund_request_model->get_pending_approval_amount();
                $data['totalAmountCleared'] = $fund_request_details['total_amount_cleared'];
            }

            if (!empty($data['totalAmountRequested']) && ($data['totalAmountRequested'] - $data['totalAmountCleared']) > intval($data['max_unreconciled_amount'])) {

                $description = 'The unreconciled amount is <strong>' . number_format($data['totalAmountRequested'] - $data['totalAmountCleared']) .
                    '</strong>, which exceeds the threshold of <strong>' . number_format($data['max_unreconciled_amount']) .
                    '</strong>. Please reconcile the unreconciled funds before requesting more funds.</p>';

                $this->clean_up_notifications();

                add_notification([
                    'description' => $description,
                    'touserid' => get_staff_user_id(),
                    'link' => 'imprest/fund_requests',
                    'fromuserid' => get_staff_user_id(),
                    'fromcompany' => false,
                    'additional_data' => serialize([
                        'imprest'
                    ])
                ]);

                pusher_trigger_notification([get_staff_user_id()]);
            }
        }
    }


    public function clean_up_notifications()
    {
        $query = $this->db->select('id, additional_data')
            ->from(db_prefix() . 'notifications')
            ->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $notification) {
                $additional_data = @unserialize($notification->additional_data);

                // Check if unserialization is successful and contains 'imprest'
                if (is_array($additional_data) && in_array('imprest', $additional_data)) {
                    // Delete the notification
                    $this->db->where('id', $notification->id);
                    $this->db->delete(db_prefix() . 'notifications');
                }
            }
        }
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

    public function create()
    {
        $data = [];

        $query = $this->db->select('fund_reconciliation')
            ->where('id', 1)
            ->get(db_prefix() . '_settings');

        if ($query->num_rows() > 0) {
            $result = $query->row();
            $serializedData = $result->fund_reconciliation;
            $unSerializedData = unserialize($serializedData);
            $data['max_unreconciled_amount'] = $unSerializedData['max_unreconciled_amount'];
        }

        $query = $this->db->select('events')
            ->where('id', 1)
            ->get(db_prefix() . '_settings');

        if ($query->num_rows() > 0) {
            $result = $query->row();
            $serializedData = $result->events;
            $data['mandatory_fields'] = unserialize($serializedData);
        }

        $fund_request_details = $this->Fund_request_model->get_fund_request_details(null,true);

        if (!empty($fund_request_details)) {
            $data['totalAmountRequested'] = $fund_request_details['total_amount_requested'] - $this->Fund_request_model->get_pending_approval_amount();
            $data['totalAmountCleared'] = $fund_request_details['total_amount_cleared'];
        }

        $data['facilitator'] = $this->Fund_request_model->get_staff_name(get_staff_user_id());
        $data['events'] = $this->Event_model->get();
        $data['categories'] = $this->Expense_category_model->get_categories(null, false);
        $data['subcategories'] = $this->get_categories_with_subcategories(false);
        $this->load->view('fund_requests/create', $data);
    }


    // Get categories along with their subcategories
    public function get_categories_with_subcategories($additional_funds = false)
    {
        $this->db->select('c.id as category_id, c.name as category_name, s.name as subcategory_name');
        $this->db->from(db_prefix() . '_expense_categories c');
        $this->db->join(db_prefix() . '_expense_subcategories s', 's.category_id = c.id', 'left'); // Assuming you have a subcategories table with a category_id

        if (!$additional_funds) {
            $this->db->where('c.name !=', 'Additional Funds');
        }

        $query = $this->db->get();
        $categories = [];

        foreach ($query->result() as $row) {
            if (!isset($categories[$row->category_id])) {
                $categories[$row->category_id] = [];
            }
            if ($row->subcategory_name) {
                $categories[$row->category_id][] = $row->subcategory_name;
            }
        }

        return $categories;
    }

    public function view($fund_request_id)
    {
        $data = $this->Fund_request_model->get_fund_request_details($fund_request_id);
        $this->load->view('fund_requests/view', $data);

    }

    public function request_additional_funds()
    {

        // Begin transaction
        $this->db->trans_start();

        try {

            // Retrieve event_id and amount from input
            $event_id = $this->input->post('event_id');
            $fund_request_id = $this->input->post('fund_request_id');
            $amount = $this->input->post('amount');

            // Validate required inputs
            if (empty($fund_request_id) || empty($amount)) {
                throw new Exception('Fund Request ID and amount are required.');
            }

            // Prepare fund request item data
            $data_fund_request_item = array(
                'fund_request_id' => $fund_request_id,
                'expense_subcategory_id' => $this->Fund_request_model->get_subcategory_id_by_name('Additional Funds'),
                'amount_requested' => $amount,
                'receipt_url' => '',
            );

            // Insert fund request item
            $this->db->insert(db_prefix() . '_fund_request_items', $data_fund_request_item);

            $additional_funds_details = [
                'fund_request_id' => $fund_request_id,
                'reason' => $this->input->post('reason'),
                'amount' => $amount,
            ];

            $this->db->insert(db_prefix() . '_additional_funds_details', $additional_funds_details);

            //update fund request status
            $this->Fund_request_model->update($fund_request_id, [
                'status' => 'pending_approval'
            ]);

            $reference_no = $this->Fund_request_model->get_reference_no(($fund_request_id));

            //update expense table
            $this->db->where('reference_no', $reference_no);
            $this->db->delete(db_prefix() . 'expenses');

            //update journals
            $this->db->where('reference_no', $reference_no);
            $this->db->delete(db_prefix() . 'acc_account_history');

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed.');
            }

            set_alert('success', 'Funds Requested successfully!');
            redirect(admin_url('imprest/fund_requests/index'));

        } catch (Exception $exception) {
            // Rollback transaction
            $this->db->trans_rollback();

            log_message('error', 'Error during fund request: ' . $exception->getMessage());
            set_alert('danger', 'Fund request failed: ' . $exception->getMessage());
            redirect(admin_url('imprest/fund_requests/index'));
        }
    }

    function generateFundRequestReference($id)
    {
        $datePart = date('Ymd');
        return "FR-{$datePart}-{$id}";
    }

    public function store()
    {

        $this->db->trans_begin(); // Begin the transaction

        try {

            if (empty($this->input->post('subcategories'))) {
                throw new Exception('Kindly Choose at least one Expense category');
            }

            $subcategories = $this->input->post('subcategories');
            $amounts = $this->input->post('amounts');

            $fund_request_id = $this->Fund_request_model->add($this->input->post('event_id'), $subcategories, $amounts);

            $this->Fund_request_model->update($fund_request_id, [
                'reference_no' => $this->generateFundRequestReference($fund_request_id)
            ]);

            $conferencing_hotel_name = $this->input->post('conferencing_hotel_name');
            if (!empty($conferencing_hotel_name)) {
                $conferencing_num_persons = $this->input->post('conferencing_num_persons');
                $conferencing_charges_per_person = $this->input->post('conferencing_charges_per_person');
                $conferencing_num_days = $this->input->post('conferencing_num_days');

                $total = $conferencing_num_persons * $conferencing_charges_per_person * $conferencing_num_days;

                $data = [
                    'fund_request_id' => $fund_request_id,
                    'hotel_name' => $conferencing_hotel_name,
                    'amount_per_person' => $conferencing_charges_per_person,
                    'number_of_days' => $conferencing_num_days,
                    'number_of_persons' => $conferencing_num_persons,
                    'total' => $total,
                ];

                $this->db->insert(db_prefix() . '_hotel_conferencing_details', $data);
            }

            $accommodation_hotel_name = $this->input->post('accommodation_hotel_name');
            if (!empty($accommodation_hotel_name)) {
                $accommodation_num_persons = $this->input->post('accommodation_num_persons');
                $accommodation_charges_per_person = $this->input->post('accommodation_charges_per_person');
                $accommodation_dinner_per_person = $this->input->post('accommodation_dinner_per_person');
                $accommodation_num_nights = $this->input->post('accommodation_num_days');

                $accommodation_total = ($accommodation_charges_per_person + $accommodation_dinner_per_person) * $accommodation_num_persons * $accommodation_num_nights;

                $accommodation_data = [
                    'fund_request_id' => $fund_request_id,
                    'hotel_name' => $accommodation_hotel_name,
                    'amount_per_person' => $accommodation_charges_per_person,
                    'dinner' => $accommodation_dinner_per_person,
                    'number_of_nights' => $accommodation_num_nights,
                    'number_of_persons' => $accommodation_num_persons,
                    'total' => $accommodation_total,
                ];

                $this->db->insert(db_prefix() . '_hotel_accommodation_details', $accommodation_data);
            }


            $speaker_name = $this->input->post('speaker_name');
            if (!empty($speaker_name)) {

                $speaker_rate_per_day = $this->input->post('speaker_rate_per_day');
                $speaker_num_days = $this->input->post('speaker_num_days');
                $speaker_total = $speaker_rate_per_day * $speaker_num_days;

                $speaker_data = [
                    'fund_request_id' => $fund_request_id,
                    'speaker_name' => $speaker_name,
                    'rate_per_day' => $speaker_rate_per_day,
                    'number_of_days' => $speaker_num_days,
                    'total' => $speaker_total,
                ];

                $this->db->insert(db_prefix() . '_speaker_details', $speaker_data);
            }


            $data = [
                'venue' => $this->input->post('venue') ?? '',
                'organization' => $this->input->post('organization') ?? '',
                'start_date' => $this->input->post('start_date') ?? '',
                'end_date' => $this->input->post('end_date') ?? '',
                'no_of_delegates' => $this->input->post('no_of_delegates') ?? '',
                'charges_per_delegate' => $this->input->post('charges_per_delegate') ?? '',
                'division' => $this->input->post('division') ?? '',
                'trainers' => serialize($this->input->post('trainers')) ?? '',
                'facilitator' => $this->input->post('facilitator') ?? '',
                'revenue' => $this->input->post('revenue') ?? '',
            ];

            $event_detail_id = $this->Event_details_model->add($data);

            $this->Fund_request_model->update($fund_request_id, [
                'event_detail_id' => $event_detail_id
            ]);

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed.');
            }

            //send email to admin
            if ($this->statusEmailNotificationEnabled('Funds Requested')) {

                $reference_no = $this->Fund_request_model->get_reference_no($fund_request_id);

                // Fetch all admin staff
                $admin_staff = $this->Fund_request_model->get_admin_staff();

                // Loop through each admin and send an email
                foreach ($admin_staff as $admin) {
                    $this->send_email($reference_no, 'funds-requested', $admin['firstname'] . ' ' . $admin['lastname'], $admin['email'], 'Funds Requested');
                }
            }

            $this->db->trans_commit(); // Commit the transaction
            set_alert('success', 'Funds Requested successfully!');
            redirect(admin_url('imprest/fund_requests/index'));

        } catch (Exception $e) {
            $this->db->trans_rollback(); // Rollback the transaction on error
            log_message('error', 'Error during fund request: ' . $e->getMessage());
            set_alert('danger', 'Fund request failed: ' . $e->getMessage());
            redirect(admin_url('imprest/fund_requests/create'));
        }
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


    public function approve($fund_request_id)
    {

        // Start a transaction
        $this->db->trans_begin();

        try {

            // Get event
            $event = $this->Event_model->get_event_by_fund_request_id($fund_request_id);

            // Step 1: Delete existing fund request items
            $this->db->where('fund_request_id', $fund_request_id);
            if (!$this->db->delete(db_prefix() . '_fund_request_items')) { // Corrected table name
                throw new Exception('Error deleting fund request items!');
            }

            $categoryAmounts = [];

            // Step 2: Insert new fund request items
            foreach ($this->input->post() as $subcategoryId => $amountRequested) {


                $subcategoryId = explode("-", $subcategoryId)[1];

                if (is_numeric($amountRequested)) {
                    // Fetch the category_id for the subcategory
                    $subcategory = $this->db->get_where(db_prefix() . '_expense_subcategories', ['id' => $subcategoryId])->row();
                    if (!$subcategory) {
                        throw new Exception('Subcategory not found!');
                    }

                    $categoryId = $subcategory->category_id;
                    $category = $this->db->get_where(db_prefix() . '_expense_categories', ['id' => $categoryId])->row();
                    if (!$category) {
                        throw new Exception('Category not found for category ID: ' . $categoryId);
                    }

                    $categoryName = $category->name;

                    // Accumulate the amount requested by category
                    if (!isset($categoryAmounts[$categoryName])) {
                        $categoryAmounts[$categoryName] = 0;
                    }
                    $categoryAmounts[$categoryName] += $amountRequested;

                    // Insert the fund request item
                    $data = [
                        'fund_request_id' => $fund_request_id,
                        'expense_subcategory_id' => $subcategoryId,
                        'amount_requested' => $amountRequested,
                    ];

                    if (!$this->db->insert(db_prefix() . '_fund_request_items', $data)) { // Corrected table name
                        throw new Exception('Error inserting fund request item!');
                    }
                }
            }

            // Step 4: Update the fund request status to approved
            if (!$this->db->update(db_prefix() . '_fund_requests', ['status' => 'pending_approval'], ['id' => $fund_request_id])) {
                throw new Exception('Error updating fund request status!');
            }

            if (staff_can('approve_fund_requests', 'impress-fund-requests')) {

                // Step 3: Create expenses for each category
                foreach ($categoryAmounts as $categoryName => $totalAmount) {

                    // Fetch the category ID based on the category name
                    $category = $this->db->get_where(db_prefix() . 'expenses_categories', ['name' => $categoryName])->row();

                    if (!$category) {
                        throw new Exception('Category not found for name: ' . $categoryName);
                    }

                    $reference_no = $this->Fund_request_model->get_reference_no(($fund_request_id));

                    $expenseData = [
                        'category' => $category->id,
                        'expense_name' => $category->name,
                        'amount' => $totalAmount,
                        'date' => date('Y-m-d'),
                        'currency' => 1,
                        'reference_no' => $reference_no,
                        'tax' => 0,
                        'addedfrom' => $this->get_requested_by($fund_request_id),
                        'dateadded' => date('Y-m-d H:i:s'),
                    ];

                    // Insert the expense data into the expenses table
                    if (!$this->db->insert(db_prefix() . 'expenses', $expenseData)) {
                        throw new Exception('Error creating expense for category: ' . $category->name);
                    }


                    $expenseId = $this->db->insert_id();
                    $staff = $this->Fund_request_model->get_staff_name($this->get_requested_by($fund_request_id));
                    $staff_id = $this->get_requested_by($fund_request_id);

                    $customFields = [
                        ['fieldid' => intval($this->getCustomFieldIds()['event_custom_id']), 'value' => $event->event_name],
                        ['fieldid' => intval($this->getCustomFieldIds()['staff_custom_id']), 'value' => $staff]
                    ];

                    foreach ($customFields as $field) {
                        $customFieldData = [
                            'fieldid' => $field['fieldid'],
                            'relid' => $expenseId,
                            'fieldto' => 'expenses',
                            'value' => $field['value']
                        ];

                        if (!$this->db->insert(db_prefix() . 'customfieldsvalues', $customFieldData)) {
                            throw new Exception('Error inserting custom field data for expense ID: ' . $expenseId);
                        }

                    }


                    //create user account if does not exists
                    $account_id = $this->create_account($staff, 15, 139);

                    //create expense journal
                    $this->add_account_history_entry($reference_no, $totalAmount, $account_id, $staff_id, $expenseId);

                }

                //  Step 4: Update the fund request status to approved
                if (!$this->db->update(db_prefix() . '_fund_requests', ['status' => 'approved'], ['id' => $fund_request_id])) {
                    throw new Exception('Error updating fund request status!');
                }

                if ($this->statusEmailNotificationEnabled('Approved')) {
                    $staff_name = $this->Fund_request_model->get_staff_name($this->get_requested_by($fund_request_id));
                    $staff_email = $this->Fund_request_model->get_staff_email($this->get_requested_by($fund_request_id));
                    $reference_no = $this->Fund_request_model->get_reference_no(($fund_request_id));
                    $this->send_email($reference_no, 'fund-request-updated', $staff_name, $staff_email, 'Approved');
                }

            }

            // Commit the transaction if everything is successful
            $this->db->trans_commit();

            if (staff_can('approve_fund_requests', 'impress-fund-requests')) {

                // Set success alert and redirect
                set_alert('success', 'Funds Request Approved successfully!');
                redirect(admin_url('imprest/fund_requests/index'));
            }

            // Set success alert and redirect
            set_alert('success', 'Changes Saved successfully!');
            redirect(admin_url('imprest/fund_requests/index'));


        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $this->db->trans_rollback();

            // Set error alert and redirect
            set_alert('danger', $e->getMessage());
            redirect(admin_url('imprest/fund_requests/index'));
        }
    }

    public function getCustomFieldIds()
    {

        $customFieldIDs = [];

        $query = $this->db->select('custom_fields')
            ->where('id', 1)
            ->get(db_prefix() . '_settings');

        if ($query->num_rows() > 0) {
            $result = $query->row();
            $serializedData = $result->custom_fields;
            $customFieldIDs = unserialize($serializedData);
        }

        return $customFieldIDs;
    }


    public function create_account($name, $account_type_id, $account_detail_type_id): int
    {
        $this->db->where('name', $name);
        $account = $this->db->get(db_prefix() . 'acc_accounts')->row();

        if ($account) {
            return $account->id;
        } else {
            $data = [
                'name' => $name,
                'active' => 1,
                'account_type_id' => $account_type_id,
                'account_detail_type_id' => $account_detail_type_id,
            ];
            $this->db->insert(db_prefix() . 'acc_accounts', $data);
            return $this->db->insert_id();
        }
    }

    public function add_account_history_entry($reference_no, $amount, $account, $staff_id, $expense_id)
    {
        $entries = [
            [
                'account' => $account,
                'credit' => 0,
                'debit' => $amount,
                'rel_id' => $expense_id,
                'split' => 13,
            ],
            [
                'account' => 13,
                'credit' => $amount,
                'debit' => 0,
                'rel_id' => $expense_id,
                'split' => $account,
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


    public function reject($fund_request_id)
    {
        // Start a transaction
        $this->db->trans_begin();

        try {
            // Update the fund request status to approved
            if (!$this->Fund_request_model->update($fund_request_id, [
                'status' => 'rejected',
                'rejection_reason' => $this->input->post('rejection_reason')
            ])) {
                throw new Exception('Error updating fund request status!');
            }

            if ($this->statusEmailNotificationEnabled('Rejected')) {
                $staff_name = $this->Fund_request_model->get_staff_name($this->get_requested_by($fund_request_id));
                $staff_email = $this->Fund_request_model->get_staff_email($this->get_requested_by($fund_request_id));
                $reference_no = $this->Fund_request_model->get_reference_no(($fund_request_id));
                $this->send_email($reference_no, 'fund-request-updated', $staff_name, $staff_email, 'Rejected');
            }

            // Commit the transaction if everything is successful
            $this->db->trans_commit();

            // Set success alert and redirect
            set_alert('success', 'Funds Request Rejected successfully!');
            redirect(admin_url('imprest/fund_requests/index'));

        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $this->db->trans_rollback();

            // Set error alert and redirect
            set_alert('danger', $e->getMessage());
            redirect(admin_url('imprest/fund_requests/index'));
        }
    }


}
