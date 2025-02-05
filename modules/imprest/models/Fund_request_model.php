<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fund_request_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . '_fund_requests';

    }

    public function get()
    {
        $this->db->select('
        fr.id as fund_request_id,
        e.name as event_name,
        e.id as event_id,
        ed.venue,
        ed.organization,
        ed.start_date,
        ed.end_date,
        ed.facilitator,
        fr.status,
        fr.reference_no,
        fr.rejection_reason,
        fr.requested_by,
        fr.additional_fund_request,
        CONCAT(s.firstname, " ", s.lastname) as requested_by,
        SUM(fri.amount_requested) as total_requested,
        COUNT(DISTINCT fr.id) as fund_request_count
       ');

        $this->db->from(db_prefix() . '_fund_requests as fr');
        $this->db->join(db_prefix() . '_events as e', 'fr.event_id = e.id', 'left');
        $this->db->join(db_prefix() . '_events_details as ed', 'fr.event_detail_id = ed.id', 'left');
        $this->db->join(db_prefix() . '_fund_request_items as fri', 'fr.id = fri.fund_request_id', 'left');
        $this->db->join(db_prefix() . 'staff as s', 'fr.requested_by = s.staffid', 'left');
        $this->db->group_by('fr.id');

        // Check if the user has permission to view all fund requests
        if (!staff_can('view_all_fund_requests', 'impress-fund-requests')) {
            $this->db->where('fr.requested_by', get_staff_user_id());
        }

        return $this->db->get()->result();
    }


    public function get_staff_name($staff_id)
    {
        $query = $this->db->query("SELECT firstname, lastname FROM " . db_prefix() . "staff WHERE staffid = ?", array($staff_id));
        $result = $query->row();

        if ($result) {
            return $result->firstname . ' ' . $result->lastname;
        } else {
            return 'Staff not found';
        }
    }

    public function get_reference_no($fund_request_id)
    {
        $query = $this->db->query("SELECT reference_no FROM " . db_prefix() . "_fund_requests WHERE id = ?", array($fund_request_id));
        $result = $query->row();

        if ($result) {
            return $result->reference_no;
        } else {
            return 'Fund Request not found';
        }
    }


    public function get_staff_email($staff_id)
    {
        $query = $this->db->query("SELECT email FROM " . db_prefix() . "staff WHERE staffid = ?", array($staff_id));
        $result = $query->row();

        if ($result) {
            return $result->email;
        } else {
            return 'Staff not found';
        }
    }


    public function get_fund_request_details($id = null)
    {
        $this->db->select('
    fr.id as fund_request_id,
    fr.requested_by,
    fr.status as fund_request_status,
    e.id as event_id,
    e.name as event_name,
    ed.venue,
    ed.organization,
    ed.start_date,
    ed.end_date,
    ed.no_of_delegates,
    ed.facilitator,
    ed.trainers,
    ed.revenue,
    fri.amount_requested,
    fri.receipt_url,
    fri.cleared,
    fri.id as item_id,
    esc.name as expense_subcategory_name,
    esc.id as expense_subcategory_id,
    ec.name as expense_category_name,
    hcd.hotel_name as conferencing_hotel_name,
    hcd.amount_per_person as conferencing_amount_per_person,
    hcd.number_of_days as conferencing_number_of_days,
    hcd.number_of_persons as conferencing_number_of_persons,
    hcd.total as conferencing_total, 
    spd.speaker_name as speaker_name,
    spd.rate_per_day as speaker_rate_per_day,
    spd.number_of_days as speaker_number_of_days,
    spd.total as speaker_total,
    acd.hotel_name as accommodation_hotel_name,
    acd.amount_per_person as accommodation_amount_per_person,
    acd.dinner as accommodation_dinner,
    acd.number_of_nights as accommodation_number_of_nights,
    acd.number_of_persons as accommodation_number_of_persons,
    acd.total as accommodation_total,
    afd.reason as reason,
   ');
        $this->db->from(db_prefix() . '_fund_requests fr');
        $this->db->join(db_prefix() . '_events e', 'fr.event_id = e.id', 'left');
        $this->db->join(db_prefix() . '_events_details ed', 'fr.event_detail_id = ed.id', 'left');
        $this->db->join(db_prefix() . '_fund_request_items fri', 'fri.fund_request_id = fr.id', 'left');
        $this->db->join(db_prefix() . '_expense_subcategories esc', 'fri.expense_subcategory_id = esc.id', 'left');
        $this->db->join(db_prefix() . '_expense_categories ec', 'esc.category_id = ec.id', 'left');
        $this->db->join(db_prefix() . '_hotel_conferencing_details hcd', 'hcd.fund_request_id = fr.id', 'left');
        $this->db->join(db_prefix() . '_speaker_details spd', 'spd.fund_request_id = fr.id', 'left');
        $this->db->join(db_prefix() . '_hotel_accommodation_details acd', 'acd.fund_request_id = fr.id', 'left');
        $this->db->join(db_prefix() . '_additional_funds_details afd', 'afd.fund_request_id = fr.id', 'left');

        if ($id !== null) {
            $this->db->where('fr.id', $id);
        }

        // Check if the user has permission to view all fund requests
        if (!staff_can('view_all_fund_requests', 'impress-fund-requests')) {
            $this->db->where('fr.requested_by', get_staff_user_id());
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $data = $query->result_array();

            // Group data by categories and calculate the total amount requested
            $groupedData = [];
            $totalAmountRequested = 0;
            $totalAmountCleared = 0;

            foreach ($data as $row) {

                $category = $row['expense_category_name'];
                $subcategory = $row['expense_subcategory_name'];
                $amount = (float)$row['amount_requested'];
                $subcategory_id = $row['expense_subcategory_id'];
                $receipt_url = $row['receipt_url'];
                $tem_id = $row['item_id'];
                $cleared = $row['cleared'];
                $totalAmountRequested += $amount;

                // If cleared is true (1), add to the total cleared amount
                if ($cleared == 1) {
                    $totalAmountCleared += $amount;
                }

                if (!isset($groupedData[$category])) {
                    $groupedData[$category] = [];
                }

                $groupedData[$category][] = [
                    'subcategory_name' => $subcategory,
                    'item_id' => $tem_id,
                    'amount_requested' => $amount,
                    'subcategory_id' => $subcategory_id,
                    'receipt_url' => $receipt_url,
                    'cleared' => $cleared
                ];
            }

            // Event details
            $eventDetails = [
                'event_name' => $data[0]['event_name'],
                'venue' => $data[0]['venue'],
                'organization' => $data[0]['organization'],
                'start_date' => $data[0]['start_date'],
                'end_date' => $data[0]['end_date'],
                'delegates' => $data[0]['no_of_delegates'],
                'facilitator' => $data[0]['facilitator'],
                'trainers' => $data[0]['trainers'],
                'status' => $data[0]['fund_request_status'],
                'revenue' => $data[0]['revenue']
            ];

            $conferencing_details = [
                'hotel_name' => $data[0]['conferencing_hotel_name'],
                'amount_per_person' => $data[0]['conferencing_amount_per_person'],
                'number_of_days' => $data[0]['conferencing_number_of_days'],
                'number_of_persons' => $data[0]['conferencing_number_of_persons'],
                'total' => $data[0]['conferencing_total']
            ];

            $speaker_details = [
                'speaker_name' => $data[0]['speaker_name'],
                'rate_per_day' => $data[0]['speaker_rate_per_day'],
                'number_of_days' => $data[0]['speaker_number_of_days'],
                'total' => $data[0]['speaker_total']
            ];

            $accommodation_details = [
                'hotel_name' => $data[0]['accommodation_hotel_name'],
                'amount_per_person' => $data[0]['accommodation_amount_per_person'],
                'dinner' => $data[0]['accommodation_dinner'],
                'number_of_nights' => $data[0]['accommodation_number_of_nights'],
                'number_of_persons' => $data[0]['accommodation_number_of_persons'],
                'total' => $data[0]['accommodation_total']
            ];

            $additional_funds = [
                'reason' => $data[0]['reason'],
            ];

            // Fund request details
            $fundRequestDetails = [
                'id' => $data[0]['fund_request_id'],
                'status' => $data[0]['fund_request_status'],
                'requested_by' => $this->get_staff_name($data[0]['requested_by']),
                'requested_by_id' => $data[0]['requested_by']
            ];

            return [
                'event_details' => $eventDetails,
                'categories' => $groupedData,
                'fund_request_details' => $fundRequestDetails,
                'total_amount_requested' => $totalAmountRequested,
                'total_amount_cleared' => $totalAmountCleared,
                'conferencing_details' => $conferencing_details,
                'speaker_details' => $speaker_details,
                'accommodation_details' => $accommodation_details,
                'additional_funds' => $additional_funds
            ];
        }

        return [];
    }

    public function get_pending_approval_amount($id = null)
    {
        // Select the amount_requested where the status is 'pending_approval'
        $this->db->select('SUM(fri.amount_requested) as total_pending_amount');
        $this->db->from(db_prefix() . '_fund_requests fr');
        $this->db->join(db_prefix() . '_fund_request_items fri', 'fri.fund_request_id = fr.id', 'left');
        $this->db->where('fr.status', 'pending_approval');

        if ($id !== null) {
            $this->db->where('fr.id', $id);
        }

        // Check if the user has permission to view all fund requests
        if (!staff_can('view_all_fund_requests', 'impress-fund-requests')) {
            $this->db->where('fr.requested_by', get_staff_user_id());
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return (float)$query->row()->total_pending_amount;
        }

        return 0.0; // Return 0 if no pending approvals
    }


    public function add($event_id, $subcategories, $amounts)
    {
        $this->db->trans_start();

        $data_fund_request = array(
            'event_id' => $event_id,
            'requested_by' => get_staff_user_id(),
            'status' => 'pending_approval',
            'fund_request_date' => date('Y-m-d')
        );

        $this->db->insert($this->table, $data_fund_request);
        $fund_request_id = $this->db->insert_id();

        foreach ($subcategories as $index => $subcategory_name) {
            $data_fund_request_item = array(
                'fund_request_id' => $fund_request_id,
                'expense_subcategory_id' => $this->get_subcategory_id_by_name($subcategory_name),
                'amount_requested' => $amounts[$index],
                'receipt_url' => null,
            );

            // Insert each fund_request_item
            $this->db->insert(db_prefix() . '_fund_request_items', $data_fund_request_item);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return $fund_request_id;
    }

    public function get_subcategory_id_by_name($subcategory_name)
    {
        $this->db->select('id');
        $this->db->from(db_prefix() . '_expense_subcategories');
        $this->db->where('name', $subcategory_name);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->id;
        }
        return null;
    }

    public function update($id, $data): bool
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function update_fund_request_items($id, $data): bool
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    public function get_admin_staff()
    {
        $this->db->select('firstname,lastname, email');
        $this->db->from('staff');
        $this->db->where('admin', 1);
        $query = $this->db->get();

        return $query->result_array(); // Returns an array of admin names & emails
    }

}
