<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_columns_to_events_details  {
    public function up() {
        $CI =& get_instance();
        $CI->load->database();
        $table = db_prefix() . '_events_details';
        $fundRequestsTable = db_prefix() . '_fund_requests';

        // Define columns to be added
        $columns = [
            'setup' => 'VARCHAR(255) NULL',
            'type' => 'VARCHAR(255) NULL',
            'event_id' => 'INT NOT NULL',
            'location' => 'TEXT NULL'
        ];

        // Add missing columns
        foreach ($columns as $column => $definition) {
            if (!$CI->db->field_exists($column, $table)) {
                $CI->db->query("ALTER TABLE `$table` ADD `$column` $definition");
            }
        }

        // Update setup and type to default values
        $CI->db->query("UPDATE `$table` SET `setup` = 'Physical', `type` = 'Local' WHERE `setup` IS NULL OR `type` IS NULL");

        // Update event_id using the event_detail_id from fund_requests
        $CI->db->query("
        UPDATE `$table` ed
        JOIN `$fundRequestsTable` fr ON ed.id = fr.event_detail_id
        SET ed.event_id = fr.event_id
    ");
    }

}
