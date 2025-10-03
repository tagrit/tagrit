<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Event_code_model extends App_Model
{

    private $table = 'event_unique_codes';

    public function __construct()
    {
        parent::__construct();
    }

    public function create($data)
    {
        $this->db->where('event_id', $data['event_id']);
        $this->db->where('location', $data['location']);
        $this->db->where('venue', $data['venue']);
        $this->db->where('start_date', $data['start_date']);
        $this->db->where('end_date', $data['end_date']);

        $query = $this->db->get(db_prefix() . $this->table);

        if ($query->num_rows() > 0) {
            $this->db->where('event_id', $data['event_id']);
            $this->db->where('location', $data['location']);
            $this->db->where('venue', $data['venue']);
            $this->db->where('start_date', $data['start_date']);
            $this->db->where('end_date', $data['end_date']);
            return $this->db->update(db_prefix() . $this->table, $data);
        } else {
            $data['event_unique_code'] = $this->generateEventUniqueCode($data['event_id'], $data['venue'], $data['location'], $data['start_date']);
            return $this->db->insert(db_prefix() . $this->table, $data);
        }
    }


    private function generateEventUniqueCode($event_id, $venue, $location, $start_date)
    {

        $event = $this->db->get_where(db_prefix() . '_events', ['id' => $event_id])->row();

        if (!$event) {
            return null;
        }

        // Clean and format inputs
        $eventPart = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $event->name), 0, 4));
        $venuePart = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $venue), 0, 3));
        $locationPart = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $location), 0, 3));
        $startDatePart = date('dmy', strtotime($start_date));

        // Combine to create the code
        return "{$eventPart}-{$venuePart}-{$locationPart}-{$startDatePart}";
    }


}