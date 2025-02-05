<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_117 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        //Ver 1.1.7
       if (!$CI->db->table_exists(db_prefix() . 'pur_debit_notes')) {
		    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_debit_notes` (
		      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		      `vendorid` INT(11) NULL,
		      `deleted_vendor_name` VARCHAR(100) NULL,
		      `number` INT(11) NULL,
		      `prefix` varchar(50) NULL,
		      `number_format` INT(11) NULL,
		      `datecreated` datetime NULL,
		      `date` date NULL,
		      `adminnote` text NULL,
		      `terms` text NULL,
		      `vendornote` text NULL,
		      `currency` INT(11) NULL,
		      `subtotal` decimal(15,2) NULL,
		      `total_tax` decimal(15,2) NULL,
		      `total` decimal(15,2) NULL,
		      `adjustment` decimal(15,2) NULL,
		      `addedfrom` int(11) NULL,
		      `status` int(11) NULL,
		      `discount_percent` decimal(15,2) NULL,
		      `discount_total` decimal(15,2) NULL,
		      `discount_type` varchar(30) NULL,
		      `billing_street` varchar(200) NULL,
		      `billing_city` varchar(100) NULL,
		      `billing_state` varchar(100) NULL,
		      `billing_zip` varchar(100) NULL,
		      `billing_country` int(11) NULL,
		      `shipping_street` varchar(200) NULL,
		      `shipping_city` varchar(100) NULL,
		      `shipping_state` varchar(100) NULL,
		      `shipping_zip` varchar(100) NULL,
		      `shipping_country` int(11) NULL,
		      `include_shipping` tinyint(1) NULL,
		      `show_shipping_on_debit_note` tinyint(1) NULL,
		      `show_quantity_as` int(11) NULL,
		      `reference_no` varchar(100) NULL,
		      PRIMARY KEY (`id`)
		    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
		}

		if (!$CI->db->table_exists(db_prefix() . 'pur_debits')) {
		    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_debits` (
		      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		      `invoice_id` INT(11) NULL,
		      `debit_id` INT(11) NULL,
		      `staff_id` INT(11) NULL,
		      `date_applied` datetime NULL,
		      `date` date NULL,
		      `amount` decimal(15,2) NULL,
		      PRIMARY KEY (`id`)
		    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
		}

		if (!$CI->db->table_exists(db_prefix() . 'pur_debits_refunds')) {
		    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_debits_refunds` (
		      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		      `debit_note_id` INT(11) NULL,
		      `staff_id` INT(11) NULL,
		      `refunded_on` date NULL,
		      `payment_mode` varchar(40) NULL,
		      `note` text NULL,
		      `amount` decimal(15,2) NULL,
		      `created_at` datetime NULL,
		      PRIMARY KEY (`id`)
		    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
		}

		add_option('next_debit_note_number', 1);
		add_option('debit_note_number_format', 1);
		add_option('debit_note_prefix', 'DN-');

		create_email_template('Debit Note', '<span style=\"font-size: 12pt;\"> Hello !. </span><br /><br /><span style=\"font-size: 12pt;\"> We would like to share with you a link of Debit Note information with the number {dn_number} </span><br /><br /><span style=\"font-size: 12pt;\"><br />{additional_content}
		  </span><br /><br />', 'purchase_order', 'Debit Note (Sent to contact)', 'debit-note-to-contact');
	}
}