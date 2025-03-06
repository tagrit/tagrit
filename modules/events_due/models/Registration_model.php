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
        tblevents_due_events.id,
        tblevents_due_events.event_name_id,
        tblevents_due_events.start_date,
        tblevents_due_events.end_date,
        tblevents_due_events.setup,
        tblevents_due_events.duration,
        tblevents_due_events.division,
        tblevents_due_events.type,
        tblevents_due_events.cost_net,
        tblevents_due_name.event_name AS event_name,
        tblevents_due_locations.name AS location,
        tblevents_due_venues.name AS venue,
        tblclients.full_name,
        tblclients.organization_name
    ');

        $this->db->from(db_prefix() . 'events_due_events AS tblevents_due_events');
        $this->db->join(db_prefix() . 'events_due_name AS tblevents_due_name', 'tblevents_due_events.event_name_id = tblevents_due_name.id', 'left');
        $this->db->join(db_prefix() . 'events_due_locations AS tblevents_due_locations', 'tblevents_due_events.location_id = tblevents_due_locations.id', 'left');
        $this->db->join(db_prefix() . 'events_due_venues AS tblevents_due_venues', 'tblevents_due_events.venue_id = tblevents_due_venues.id', 'left');

        // **INNER JOIN** to ensure only events that exist in registrations are retrieved
        $this->db->join(db_prefix() . 'events_due_registrations AS tblevents_due_registrations', 'tblevents_due_events.id = tblevents_due_registrations.event_id', 'inner');

        // Join clients table to get client details
        $this->db->join(db_prefix() . 'events_due_clients AS tblclients', 'tblevents_due_registrations.client_id = tblclients.id', 'left');

        if ($event_id) {
            $this->db->where('tblevents_due_events.id', $event_id);
        }

        return $this->db->get()->result();
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