<?php

defined('BASEPATH') or exit('No direct script access allowed');

add_option('mpesa_gateway', 'enable');

//create tables
$table = db_prefix() . 'mpesa_gateway_transactions';
if (!$CI->db->table_exists($table)) {
    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `" . $table . "` (
            `id` int NOT NULL AUTO_INCREMENT,
            `invoice_id` varchar(255) NOT NULL,
            `ref_id` varchar(255) DEFAULT NULL,
            `status` varchar(255) DEFAULT NULL,
            `phone` varchar(255) DEFAULT NULL,
            `amount` decimal(10,2) DEFAULT '0.00',
            `timestamp` varchar(255) NOT NULL,
            `description` text,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";"
    );
}

if (!$CI->db->field_exists('receipt_number', $table)) {
    $CI->db->query("ALTER TABLE `$table` ADD `receipt_number` VARCHAR(255) NULL AFTER `description`;");
}