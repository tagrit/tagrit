<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_320 extends CI_Migration
{
    public function up()
    {
        $dbPrefix    = db_prefix();
        $dbCharset   = $this->db->char_set;
        $dbCollation = $this->db->dbcollat;

        add_option('disable_ticket_public_url', '0');

        if (! $this->db->field_exists('formatted_number', $dbPrefix . 'invoices')) {
            $this->db->query('ALTER TABLE `' . $dbPrefix . 'invoices` ADD `formatted_number` VARCHAR(100) CHARACTER SET ' . $dbCharset . ' COLLATE ' . $dbCollation . ' NULL DEFAULT NULL AFTER `number_format`, ADD INDEX `formatted_number` (`formatted_number`);');
        }

        if (! $this->db->field_exists('formatted_number', $dbPrefix . 'estimates')) {
            $this->db->query('ALTER TABLE `' . $dbPrefix . 'estimates` ADD `formatted_number` VARCHAR(100) CHARACTER SET ' . $dbCharset . ' COLLATE ' . $dbCollation . ' NULL DEFAULT NULL AFTER `number_format`, ADD INDEX `formatted_number` (`formatted_number`);');
        }

        if (! $this->db->field_exists('formatted_number', $dbPrefix . 'creditnotes')) {
            $this->db->query('ALTER TABLE `' . $dbPrefix . 'creditnotes` ADD `formatted_number` VARCHAR(100) CHARACTER SET ' . $dbCharset . ' COLLATE ' . $dbCollation . ' NULL DEFAULT NULL AFTER `number_format`, ADD INDEX `formatted_number` (`formatted_number`);');
        }
    }
}
