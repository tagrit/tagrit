<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_default_data_to_settings
{

    public function up()
    {
        $CI =& get_instance();
        $CI->load->database();

        // Table where data is stored
        $table = db_prefix() . '_settings';

        // Fetch the existing serialized events field
        $CI->db->where('id', 1); // Adjust the condition as needed
        $query = $CI->db->get($table);

        if ($query->num_rows() > 0) {
            $row = $query->row();
            $eventsData = unserialize($row->events);

            // Ensure it's an array before modifying
            if (is_array($eventsData)) {
                // Add only the new values if they are not already in the array
                if (!in_array('setup', $eventsData)) {
                    $eventsData[] = 'setup';
                }
                if (!in_array('type', $eventsData)) {
                    $eventsData[] = 'type';
                }

                // Update the database with the new serialized value
                $CI->db->update($table, ['events' => serialize($eventsData)]);
            }
        }
    }
}
