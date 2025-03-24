<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_columns_to_events_details  {
    public function up() {
        $CI =& get_instance();
        $CI->load->database();
        $table = db_prefix() . '_events_details';

        $columns = [
            'setup' => 'VARCHAR(255) NULL',
            'type' => 'VARCHAR(255) NULL',
            'event_id' => 'INT NOT NULL',
            'location' => 'TEXT NULL'
        ];

        foreach ($columns as $column => $definition) {
            if (!$CI->db->field_exists($column, $table)) {
                $CI->db->query("ALTER TABLE `$table` ADD `$column` $definition");
            }
        }
    }
}
