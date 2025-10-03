<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_107 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        //version 1.0.7 vendor category table
        if (!$CI->db->table_exists(db_prefix() . 'pur_vendor_cate')) {
		    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_vendor_cate` (
		      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		      `category_name` VARCHAR(255) NULL,
		      `description` text NULL,
		      PRIMARY KEY (`id`)
		    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
		}

		//version 1.0.7 additional field vendor
		if (!$CI->db->field_exists('category' ,db_prefix() . 'pur_vendor')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
		      ADD COLUMN `category` TEXT  NULL
		  ;");
		}

		if (!$CI->db->field_exists('bank_detail' ,db_prefix() . 'pur_vendor')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
		      ADD COLUMN `bank_detail` TEXT  NULL
		  ;");
		}

		if (!$CI->db->field_exists('payment_terms' ,db_prefix() . 'pur_vendor')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
		      ADD COLUMN `payment_terms` TEXT  NULL
		  ;");
		}

		if (!$CI->db->field_exists('vendor_code' ,db_prefix() . 'pur_vendor')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
		      ADD COLUMN `vendor_code` VARCHAR(100)  NULL
		  ;");
		}

		//version 1.0.7 additional field purchase request
		if (!$CI->db->field_exists('type' ,db_prefix() . 'pur_request')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
		      ADD COLUMN `type` TEXT  NULL
		  ;");
		}

		if (!$CI->db->field_exists('project' ,db_prefix() . 'pur_request')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
		      ADD COLUMN `project` TEXT  NULL
		  ;");
		}

		if (!$CI->db->field_exists('number' ,db_prefix() . 'pur_request')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
		      ADD COLUMN `number` INT(11)  NULL
		  ;");
		}

		if (!$CI->db->field_exists('from_items' ,db_prefix() . 'pur_request')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
		      ADD COLUMN `from_items` INT(2)  NULL DEFAULT '1'
		  ;");
		}


		//version 1.0.7 additional field purchase order

		if (!$CI->db->field_exists('delivery_status' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		      ADD COLUMN `delivery_status` int(2)  NULL DEFAULT '0'
		  ;");
		}

		if (!$CI->db->field_exists('type' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		      ADD COLUMN `type` TEXT  NULL
		  ;");
		}

		if (!$CI->db->field_exists('project' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		      ADD COLUMN `project` INT(11)  NULL
		  ;");
		}

		if (!$CI->db->field_exists('pur_request' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		      ADD COLUMN `pur_request` INT(11)  NULL
		  ;");
		}

		if (!$CI->db->field_exists('department' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		      ADD COLUMN `department` INT(11)  NULL
		  ;");
		}

		if (!$CI->db->field_exists('tax_order_rate' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		      ADD COLUMN `tax_order_rate` DECIMAL(15,2)  NULL
		  ;");
		}

		if (!$CI->db->field_exists('tax_order_amount' ,db_prefix() . 'pur_orders')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
		      ADD COLUMN `tax_order_amount` DECIMAL(15,2)  NULL
		  ;");
		}

		//Purchase Order next Number option
		if (row_purchase_options_exist('"next_po_number"') == 0){
		    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("next_po_number", "1", "1");
		  ');
		}

		if (row_purchase_options_exist('"date_reset_number"') == 0){
		    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("date_reset_number", "", "1");
		  ');
		}

		if (row_purchase_options_exist('"pur_request_prefix"') == 0){
		    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("pur_request_prefix", "#PR", "1");
		  ');
		}

		if (row_purchase_options_exist('"next_pr_number"') == 0){
		    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("next_pr_number", "1", "1");
		  ');
		}

		if (row_purchase_options_exist('"date_reset_pr_number"') == 0){
		    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("date_reset_pr_number", "", "1");
		  ');
		}

		// Purchase request detail
		if (!$CI->db->field_exists('item_text' ,db_prefix() . 'pur_request_detail')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
		      ADD COLUMN `item_text` TEXT  NULL
		  ;");
		}

		// version 1.0.7 Contract
		if (!$CI->db->field_exists('service_category' ,db_prefix() . 'pur_contracts')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
		    ADD COLUMN `service_category` TEXT NULL
		  ;");
		}

		if (!$CI->db->field_exists('project' ,db_prefix() . 'pur_contracts')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
		    ADD COLUMN `project` INT(11) NULL
		  ;");
		}

		if (!$CI->db->field_exists('payment_terms' ,db_prefix() . 'pur_contracts')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
		    ADD COLUMN `payment_terms` TEXT NULL
		  ;");
		}

		if (!$CI->db->field_exists('payment_amount' ,db_prefix() . 'pur_contracts')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
		    ADD COLUMN `payment_amount` DECIMAL(15,2) NULL
		  ;");
		}

		if (!$CI->db->field_exists('payment_cycle' ,db_prefix() . 'pur_contracts')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
		    ADD COLUMN `payment_cycle` VARCHAR(50) NULL
		  ;");
		}
		if (!$CI->db->field_exists('department' ,db_prefix() . 'pur_contracts')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
		    ADD COLUMN `department` INT(11) NULL
		  ;");
		}

		// version 1.0.7 Create table pur_invoices
		if (!$CI->db->table_exists(db_prefix() . 'pur_invoices')) {
		    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_invoices` (
		      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		      `number` int(11) NOT NULL,
		      `invoice_number` TEXT NULL,
		      `invoice_date` DATE NULL,
		      `subtotal` DECIMAL(15,2) NULL,
		      `tax_rate` INT(11) NULL,
		      `tax` DECIMAL(15,2) NULL,
		      `total` DECIMAL(15,2) NULL,
		      `contract` int(11) NOT NULL,
		      `vendor` int(11) NOT NULL,
		      `transactionid` MEDIUMTEXT NULL,
		      `transaction_date` DATE NULL,
		      `payment_request_status` VARCHAR(30) NULL,
		      `payment_status` VARCHAR(30) NULL,
		      `vendor_note` TEXT NULL, 
		      `adminnote` TEXT NULL, 
		      `terms` TEXT NULL,
		      `add_from` INT(11) NULL,
      		  `date_add` DATE NULL,
		      PRIMARY KEY (`id`)
		    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
		}

		if (row_purchase_options_exist('"pur_inv_prefix"') == 0){
		    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("pur_inv_prefix", "#INV", "1");
		  ');
		}

		if (row_purchase_options_exist('"next_inv_number"') == 0){
		    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("next_inv_number", "1", "1");
		  ');
		}

		if ($CI->db->field_exists('contract' ,db_prefix() . 'pur_invoices')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
		    CHANGE COLUMN `contract` `contract` INT(11) NULL
		  ;");
		}

		if ($CI->db->field_exists('vendor' ,db_prefix() . 'pur_invoices')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
		    CHANGE COLUMN `vendor` `vendor` INT(11) NULL
		  ;");
		}

		if (!$CI->db->field_exists('pur_order' ,db_prefix() . 'pur_invoices')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
		    ADD COLUMN `pur_order` INT(11) NULL
		  ;");
		}

		if (row_purchase_options_exist('"create_invoice_by"') == 0){
		    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("create_invoice_by", "contract", "1");
		  ');
		}

		// version 1.0.7 Create table invoices payment
		if (!$CI->db->table_exists(db_prefix() . 'pur_invoice_payment')) {
		    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_invoice_payment` (
		      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		      `pur_invoice` int(11) NOT NULL,
		      `amount` DECIMAL(15,2) NOT NULL,
		      `paymentmode` LONGTEXT NULL,
		      `date` DATE NOT NULL,
		      `daterecorded` DATETIME NOT NULL,
		      `note` TEXT NOT NULL,
		      `transactionid` MEDIUMTEXT NULL,
		      `approval_status` INT(2) NULL,
		      PRIMARY KEY (`id`)
		    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
		}

		if (!$CI->db->field_exists('requester' ,db_prefix() . 'pur_invoice_payment')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoice_payment`
		    ADD COLUMN `requester` INT(11) NULL
		  ;");
		}

		//version 1.0.7 remove required condition for purchase request field in purchase estimate
		if ($CI->db->field_exists('pur_request' ,db_prefix() . 'pur_estimates')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimates`
		    CHANGE COLUMN `pur_request` `pur_request` INT(11) NULL
		  ;");
		}

		//version 1.0.7 email template
		create_email_template('Purchase Order', '<span style=\"font-size: 12pt;\"> Hello !. </span><br /><br /><span style=\"font-size: 12pt;\"> We would like to share with you a link of Purchase Order information with the number {po_number} </span><br /><br /><span style=\"font-size: 12pt;\"><br />Please click on the link to view information: {public_link}
		  </span><br /><br />', 'purchase_order', 'Purchase Order (Sent to contact)', 'purchase-order-to-contact');
    }
}
