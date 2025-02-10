<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'pesapal_txn')) {
	$CI->db->query("CREATE TABLE `" . db_prefix() . "pesapal_txn` (
		`txn_id` bigint(40) NOT NULL,
		`invoiceid` bigint(40) NOT NULL,
		`amount` decimal(11,2) NOT NULL,
		`reference_code` varchar(50) NOT NULL,
		`tracking_id` varchar(5000) NOT NULL,
		`txn_date` datetime NOT NULL,
		`txn_ipn_date` datetime NOT NULL,
		`notification_type` varchar(50) DEFAULT NULL,
		`txn_status` varchar(20) NOT NULL,
		`flag` int(1) NOT NULL DEFAULT '0'
	) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

	$CI->db->query('ALTER TABLE `' . db_prefix() . 'pesapal_txn`
		ADD PRIMARY KEY (`txn_id`);');
	$CI->db->query('ALTER TABLE `' . db_prefix() . 'pesapal_txn`
		MODIFY `txn_id` bigint(40) NOT NULL AUTO_INCREMENT;');

}
