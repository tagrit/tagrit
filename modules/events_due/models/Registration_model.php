<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Registration_model extends App_Model
{

    private $table = 'events_due_registrations';

    public function __construct()
    {
        parent::__construct();
    }


    public function get($event_id = null)
    {
        $this->db->select('
        tblevents_due_events.event_id,
        tblevents_due_events.start_date,
        tblevents_due_events.end_date,
        tblevents_due_events.setup,
        tblevents_due_events.division,
        tblevents_due_events.type,
        tblevents_due_events.revenue,
        tblevents_due_name.name AS event_name,
        tblevents_due_events.location,
        tblevents_due_events.venue,
        tblevents_due_events.organization,
        tblevents_due_registrations.clients AS serialized_clients
    ');

        $this->db->from(db_prefix() . '_events_details AS tblevents_due_events');
        $this->db->join(db_prefix() . '_events AS tblevents_due_name', 'tblevents_due_events.event_id = tblevents_due_name.id', 'left');
        $this->db->join(db_prefix() . 'events_due_registrations AS tblevents_due_registrations', 'tblevents_due_events.id = tblevents_due_registrations.event_detail_id', 'inner');

        if ($event_id) {
            $this->db->where('tblevents_due_events.id', $event_id);
        }

        $results = $this->db->get()->result_array();

        // Process the serialized clients
        $final_results = [];

        foreach ($results as $row) {
            $clients = unserialize($row['serialized_clients']);

            if (is_array($clients)) {
                foreach ($clients as $client) {
                    $final_results[] = array_merge($row, [
                        'client_first_name' => $client['first_name'] ?? '',
                        'client_last_name' => $client['last_name'] ?? '',
                        'client_email' => $client['email'] ?? '',
                        'client_phone' => $client['phone'] ?? ''
                    ]);
                }
            } else {
                // If no clients, just return the event details
                $final_results[] = array_merge($row, [
                    'client_first_name' => '',
                    'client_last_name' => '',
                    'client_email' => '',
                    'client_phone' => ''
                ]);
            }
        }

        return json_decode(json_encode($final_results));
    }


    public function get_filtered_data($status = null, $start_date = null, $end_date = null, $organization = null, $query = null)
    {
        $this->db->select('
        tblevents_due_events.event_id,
        tblevents_due_events.start_date,
        tblevents_due_events.end_date,
        tblevents_due_events.setup,
        tblevents_due_events.division,
        tblevents_due_events.type,
        tblevents_due_events.revenue,
        tblevents_due_name.name AS event_name,
        tblevents_due_events.location,
        tblevents_due_events.venue,
        tblevents_due_events.organization,
        tblevents_due_registrations.clients AS serialized_clients,
        tblevents_due_registrations.status,
        tblevents_due_registrations.payment_status
    ');

        $this->db->from(db_prefix() . '_events_details AS tblevents_due_events');
        $this->db->join(db_prefix() . '_events AS tblevents_due_name', 'tblevents_due_events.event_id = tblevents_due_name.id', 'left');
        $this->db->join(db_prefix() . 'events_due_registrations AS tblevents_due_registrations', 'tblevents_due_events.id = tblevents_due_registrations.event_detail_id', 'inner');

        // Apply filters for status, date, and organization
        if (!empty($status)) {
            $this->db->where('tblevents_due_registrations.status', $status);
        }
        if (!empty($start_date)) {
            $this->db->where('tblevents_due_events.start_date >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->db->where('tblevents_due_events.end_date <=', $end_date);
        }

        if (!empty($organization)) {
            $this->db->where('tblevents_due_events.organization', $organization);
        }


        $results = $this->db->get()->result_array();
        $final_results = [];
        $query_lower = strtolower($query ?? '');

        foreach ($results as $row) {
            $clients = unserialize($row['serialized_clients']);
            $event_match = false;
            $client_match = false;

            // ✅ Check if event matches the query
            $event_name = strtolower($row['event_name'] ?? '');
            $location = strtolower($row['location'] ?? '');
            $venue = strtolower($row['venue'] ?? '');

            if (empty($query_lower) ||
                strpos($event_name, $query_lower) !== false ||
                strpos($location, $query_lower) !== false ||
                strpos($venue, $query_lower) !== false) {
                $event_match = true;
            }

            // ✅ Check if any client matches the query
            $matched_clients = [];
            foreach ($clients as $client) {
                $first_name = strtolower($client['first_name'] ?? '');
                $last_name = strtolower($client['last_name'] ?? '');
                $email = strtolower($client['email'] ?? '');
                $phone = strtolower($client['phone'] ?? '');

                if (empty($query_lower) ||
                    strpos($first_name, $query_lower) !== false ||
                    strpos($last_name, $query_lower) !== false ||
                    strpos($email, $query_lower) !== false ||
                    strpos($phone, $query_lower) !== false) {
                    $client_match = true;
                    $matched_clients[] = [
                        'client_first_name' => $client['first_name'] ?? '',
                        'client_last_name' => $client['last_name'] ?? '',
                        'client_email' => $client['email'] ?? '',
                        'client_phone' => $client['phone'] ?? ''
                    ];
                }
            }

            // ✅ Include the event if it matches OR if any client matches
            if ($event_match || $client_match) {
                if (!empty($matched_clients)) {
                    foreach ($matched_clients as $client_data) {
                        $final_results[] = array_merge($row, $client_data);
                    }
                } else {
                    // If no specific client matched but the event matched, return all clients
                    foreach ($clients as $client) {
                        $final_results[] = array_merge($row, [
                            'client_first_name' => $client['first_name'] ?? '',
                            'client_last_name' => $client['last_name'] ?? '',
                            'client_email' => $client['email'] ?? '',
                            'client_phone' => $client['phone'] ?? ''
                        ]);
                    }
                }
            }
        }

        return json_decode(json_encode($final_results));

    }


    public function create($data)
    {
        return $this->db->insert(db_prefix() . $this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update(db_prefix() . $this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete(db_prefix() . $this->table);
    }
}