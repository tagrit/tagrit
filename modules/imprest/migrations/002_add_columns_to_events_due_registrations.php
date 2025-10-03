<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_columns_to_events_due_registrations
{
    public function up()
    {
        $CI =& get_instance();
        $CI->load->database();
        $table = db_prefix() . 'events_due_registrations';

        $columns = [
            'event_detail_id' => 'INT NOT NULL',
            'clients' => 'TEXT NOT NULL',
        ];

        foreach ($columns as $column => $definition) {
            if (!$CI->db->field_exists($column, $table)) {
                $CI->db->query("ALTER TABLE `$table` ADD `$column` $definition");
            }
        }
    }
}
