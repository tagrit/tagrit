<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends AdminController
{

    public function main()
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

        $query = $this->db->select('custom_fields')
            ->where('id', 1)
            ->get(db_prefix() . '_settings');

        if ($query->num_rows() > 0) {
            $result = $query->row();
            $serializedData = $result->custom_fields;
            $unSerializedData = unserialize($serializedData);
            $data['staff_custom_id'] = $unSerializedData['staff_custom_id'];
            $data['event_custom_id'] = $unSerializedData['event_custom_id'];
        }

        $query = $this->db->select('notifications')
            ->where('id', 1)
            ->get(db_prefix() . '_settings');

        if ($query->num_rows() > 0) {
            $result = $query->row();
            $serializedData = $result->notifications;
            $data['notification_statuses'] = unserialize($serializedData);
        }

        $query = $this->db->select('events')
            ->where('id', 1)
            ->get(db_prefix() . '_settings');

        if ($query->num_rows() > 0) {
            $result = $query->row();
            $serializedData = $result->events;
            $data['mandatory_fields'] = unserialize($serializedData);
        }

        $group = $this->input->get('group', true) ?? 'reconciliation';
        $data['group'] = $group;

        switch ($group) {
            case 'reconciliation':
                $data['group_content'] = $this->load->view('settings/fund_reconciliation', $data, true);
                break;

            case 'custom_fields':
                $data['group_content'] = $this->load->view('settings/custom_fields', $data, true);
                break;
            case 'email_notifications':
                $data['group_content'] = $this->load->view('settings/email_notifications', $data, true);
                break;
            case 'events':
                $data['group_content'] = $this->load->view('settings/events', $data, true);
                break;
            default:
                $data['group_content'] = $this->load->view('settings/fund_reconciliation', [], true);
                break;
        }

        if ($this->router->fetch_method() == 'main' && !$this->input->is_ajax_request()) {
            $this->load->view('settings/main', $data);
        }
    }

    public function set_max_unreconciled_amount()
    {
        try {

            // Validate required inputs
            if (empty($this->input->post('max_unreconciled_amount'))) {
                throw new Exception('Max unreconciled Amount is required.');
            }

            $serializedData = serialize(['max_unreconciled_amount' => $this->input->post('max_unreconciled_amount')]);

            $data = [
                'fund_reconciliation' => $serializedData,
            ];

            $this->db->where('id', 1); // Adjust the condition as needed
            if (!$this->db->update(db_prefix() . '_settings', $data)) {
                throw new Exception('Error Updating Fund Reconciliation Settings.');
            }

            set_alert('success', 'Fund Reconciliation Settings Updated successfully!');
            redirect(admin_url('imprest/settings/main'));

        } catch (Exception $e) {
            log_message('error', 'Error during fund request: ' . $e->getMessage());
            set_alert('danger', 'Fund request failed: ' . $e->getMessage());
            redirect(admin_url('imprest/settings/main'));
        }
    }


    public function set_custom_ids()
    {
        try {

            // Validate required inputs
            if (empty($this->input->post('staff_custom_id')) || empty($this->input->post('event_custom_id'))) {
                throw new Exception('Please fill all the required fields.');
            }

            $serializedData = serialize([
                'staff_custom_id' => $this->input->post('staff_custom_id'),
                'event_custom_id' => $this->input->post('event_custom_id')
            ]);

            $data = [
                'custom_fields' => $serializedData,
            ];

            $this->db->where('id', 1); // Adjust the condition as needed
            if (!$this->db->update(db_prefix() . '_settings', $data)) {
                throw new Exception('Error Updating Custom Fields Settings.');
            }

            set_alert('success', 'Custom Field Settings Updated successfully!');
            redirect(admin_url('imprest/settings/main?group=custom_fields'));


        } catch (Exception $e) {
            log_message('error', 'Error during fund request: ' . $e->getMessage());
            set_alert('danger', 'Fund request failed: ' . $e->getMessage());
            redirect(admin_url('imprest/settings/main?group=custom_fields'));
        }
    }


    public function set_email_notification_statuses()
    {
        try {

            $serializedData = serialize($this->input->post('notification_statuses'));

            $data = [
                'notifications' => $serializedData,
            ];

            $this->db->where('id', 1); // Adjust the condition as needed

            if (!$this->db->update(db_prefix() . '_settings', $data)) {
                throw new Exception('Error Updating Email Notification Statuses Settings.');
            }

            set_alert('success', 'Email Notification Statuses Settings Updated successfully!');
            redirect(admin_url('imprest/settings/main?group=email_notifications'));


        } catch (Exception $e) {

            log_message('error', 'Error during fund request: ' . $e->getMessage());
            set_alert('danger', 'Fund request failed: ' . $e->getMessage());
            redirect(admin_url('imprest/settings/main?group=email_notifications'));
        }
    }


    public function set_event_mandatory_fields()
    {
        try {

            $serializedData = serialize($this->input->post('mandatory_fields'));

            $data = [
                'events' => $serializedData,
            ];

            $this->db->where('id', 1); // Adjust the condition as needed

            if (!$this->db->update(db_prefix() . '_settings', $data)) {
                throw new Exception('Error Updating Events Mandatory Fields.');
            }

            set_alert('success', 'Events Mandatory Fields Updated successfully!');
            redirect(admin_url('imprest/settings/main?group=events'));


        } catch (Exception $e) {

            log_message('error', 'Error during setting: ' . $e->getMessage());
            set_alert('danger', 'Error Updating Events Mandatory Fields: ' . $e->getMessage());
            redirect(admin_url('imprest/settings/main?group=events'));
        }
    }


}