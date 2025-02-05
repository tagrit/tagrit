<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'pur_unit')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_unit` (
  `unit_id` INT(11) NOT NULL AUTO_INCREMENT,
  `unit_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`unit_id`));');
}
if (!$CI->db->table_exists(db_prefix() . 'pur_vendor')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_vendor` (
      `userid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `company` varchar(200) NULL,
      `vat` varchar(200) NULL,
      `phonenumber` varchar(30) NULL,
      `country` int(11) NOT NULL DEFAULT '0',
      `city` varchar(100) NULL,
      `zip` varchar(15) NULL,
      `state` varchar(50) NULL,
      `address` varchar(100) NULL,
      `website` varchar(150) NULL,
      `datecreated` DATETIME NOT NULL,
      `active` INT(11) NOT NULL DEFAULT '1',
      `leadid` INT(11) NULL,
      `billing_street` varchar(200) NULL,
      `billing_city` varchar(100) NULL,
      `billing_state` varchar(100) NULL,
      `billing_zip` varchar(100) NULL,
      `billing_country` int(11) NULL DEFAULT '0',
      `shipping_street` varchar(200) NULL,
      `shipping_city` varchar(100) NULL,
      `shipping_state` varchar(100) NULL,
      `shipping_zip` varchar(100) NULL,
      `shipping_country` int(11) NULL DEFAULT '0',
      `longitude` varchar(191) NULL,
      `latitude` varchar(191) NULL,
      `default_language` varchar(40) NULL,
      `default_currency` INT(11) NOT NULL DEFAULT '0',
      `show_primary_contact` INT(11) NOT NULL DEFAULT '0',
      `stripe_id` varchar(40) NULL,
      `registration_confirmed` INT(11) NOT NULL DEFAULT '1',
      `addedfrom` INT(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`userid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_contacts')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_contacts` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `userid` int(11) NOT NULL,
      `is_primary` int(11) NOT NULL DEFAULT '1',
      `firstname` varchar(191) NOT NULL,
      `lastname` VARCHAR(191) NOT NULL,
      `email` varchar(100) NOT NULL,
      `phonenumber` varchar(100) NOT NULL,
      `title` varchar(100) NULL,
      `datecreated` datetime NOT NULL,
      `password` varchar(255) NULL,
      `new_pass_key` varchar(32) NULL,
      `new_pass_key_requested` datetime NULL,
      `email_verified_at` datetime NULL,
      `email_verification_key` varchar(32) NULL,
      `email_verification_sent_at` DATETIME NULL,
      `last_ip` varchar(40) NULL,
      `last_login` DATETIME NULL,
      `last_password_change` DATETIME NULL,
      `active` TINYINT(1) NOT NULL DEFAULT '1',
      `profile_image` varchar(191) NULL,
      `direction` varchar(3) NULL,
      `invoice_emails` TINYINT(1) NOT NULL DEFAULT '1',
      `estimate_emails` TINYINT(1) NOT NULL DEFAULT '1',
      `credit_note_emails` TINYINT(1) NOT NULL DEFAULT '1',
      `contract_emails` TINYINT(1) NOT NULL DEFAULT '1',
      `task_emails` TINYINT(1) NOT NULL DEFAULT '1',
      `project_emails` TINYINT(1) NOT NULL DEFAULT '1',
      `ticket_emails` TINYINT(1) NOT NULL DEFAULT '1',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_vendor_admin')) {
    $CI->db->query('CREATE TABLE `'.db_prefix()."pur_vendor_admin` (
  `staff_id` INT(11) NOT NULL,
  `vendor_id` INT(11) NOT NULL,
  `date_assigned` DATETIME NOT NULL);");
}

if (!$CI->db->table_exists(db_prefix() . 'pur_approval_setting')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_approval_setting` (
    `id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`related` VARCHAR(255) NOT NULL,
	`setting` LONGTEXT NOT NULL,
	PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_approval_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_approval_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `rel_id` INT(11) NOT NULL,
  `rel_type` VARCHAR(45) NOT NULL,
  `staffid` VARCHAR(45) NULL,
  `approve` VARCHAR(45) NULL,
  `note` TEXT NULL,
  `date` DATETIME NULL,
  `approve_action` VARCHAR(255) NULL,
  `reject_action` VARCHAR(255) NULL,
  `approve_value` VARCHAR(255) NULL,
  `reject_value` VARCHAR(255) NULL,
  `staff_approve` INT(11) NULL,
  `action` VARCHAR(45) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_request')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_request` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_rq_code` VARCHAR(45) NOT NULL,
  `pur_rq_name` VARCHAR(100) NOT NULL,
  `rq_description` TEXT NULL,
  `requester` INT(11) NOT NULL,
  `department` INT(11) NOT NULL,
  `request_date` DATETIME NOT NULL,
  `status` INT(11) NULL,
  `status_goods` INT(11) NOT NULL DEFAULT "0", 
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_request_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_request_detail` (
  `prd_id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_request` INT(11) NOT NULL,
  `item_code` VARCHAR(100) NOT NULL,
  `unit_id` INT(11) NULL,
  `unit_price` DECIMAL(15,0) NULL,
  `quantity` int(11) NOT NULL,
  `into_money` DECIMAL(15,0) NULL,
  `inventory_quantity` int(11) NOT NULL DEFAULT "0",
  PRIMARY KEY (`prd_id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_estimates')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_estimates` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `sent` TINYINT(1) NOT NULL DEFAULT '0',
      `datesend` DATETIME NULL,
      `vendor` INT(11) NOT NULL,
      `deleted_vendor_name` VARCHAR(100) NULL,
      `pur_request` INT(11) NOT NULL,
      `number` INT(11) NOT NULL,
      `prefix` varchar(50) NULL,
      `number_format` INT(11) NOT NULL DEFAULT '0',
      `hash` VARCHAR(32) NULL,
      `datecreated` DATETIME NOT NULL,
      `date` DATE NOT NULL,
      `expirydate` DATE NULL,
      `currency` INT(11) NOT NULL,
      `subtotal` DECIMAL(15,2) NOT NULL,
      `total_tax` DECIMAL(15,2) NOT NULL,
      `total` DECIMAL(15,2) NOT NULL,
      `adjustment` DECIMAL(15,2) NULL,
      `addedfrom` INT(11) NOT NULL,
      `status` INT(11) NOT NULL DEFAULT '1',
      `vendornote` TEXT NULL,
      `adminnote` TEXT NULL,
      `discount_percent` DECIMAL(15,2) NULL DEFAULT '0.00',
      `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
      `discount_type` VARCHAR(30) NULL,
      `invoiceid` INT(11) NULL,
      `invoiced_date` DATETIME NULL,
      `terms` TEXT NULL,
      `reference_no` VARCHAR(100) NULL,
      `buyer` INT(11) NOT NULL DEFAULT '0',
      `billing_street` VARCHAR(200) NULL,
      `billing_city` VARCHAR(100) NULL,
      `billing_state` VARCHAR(100) NULL,
      `billing_zip` VARCHAR(100) NULL,
      `billing_country` INT(11) NULL,
      `shipping_street` VARCHAR(200) NULL,
      `shipping_city` VARCHAR(100) NULL,
      `shipping_state` VARCHAR(100) NULL,
      `shipping_zip` VARCHAR(100) NULL,
      `shipping_country` INT(11) NULL,
      `include_shipping` TINYINT(1) NOT NULL,
      `show_shipping_on_estimate` TINYINT(1) NOT NULL DEFAULT '1',
      `show_quantity_as` INT(11) NOT NULL DEFAULT '1',
      `pipeline_order` INT(11) NOT NULL DEFAULT '0',
      `is_expiry_notified` INT(11) NOT NULL DEFAULT '0',
      `acceptance_firstname` VARCHAR(50) NULL,
      `acceptance_lastname` VARCHAR(50) NULL,
      `acceptance_email` VARCHAR(100) NULL,
      `acceptance_date` DATETIME NULL,
      `acceptance_ip` VARCHAR(40) NULL,
      `signature` VARCHAR(40) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_estimate_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_estimate_detail` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_estimate` INT(11) NOT NULL,
  `item_code` VARCHAR(100) NOT NULL,
  `unit_id` INT(11) NULL,
  `unit_price` DECIMAL(15,0) NULL,
  `quantity` int(11) NOT NULL,
  `into_money` DECIMAL(15,0) NULL,
  `tax` text NULL,
  `total` DECIMAL(15,0) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->field_exists('discount_%' ,db_prefix() . 'pur_estimate_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
    ADD COLUMN `discount_%` DECIMAL(15,0) NULL AFTER `total`
  ;");
}

if (!$CI->db->field_exists('discount_money' ,db_prefix() . 'pur_estimate_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
    ADD COLUMN `discount_money` DECIMAL(15,0) NULL AFTER `total`
  ;");
}

if (!$CI->db->field_exists('total_money' ,db_prefix() . 'pur_estimate_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
    ADD COLUMN `total_money` DECIMAL(15,0) NULL AFTER `total`
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'pur_orders')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_orders` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `pur_order_name` varchar(100) NOT NULL,
      `vendor` INT(11) NOT NULL,
      `estimate` INT(11) NOT NULL,
      `pur_order_number` VARCHAR(30) NOT NULL,
      `order_date` date NOT NULL,
      `status` INT(32) NOT NULL DEFAULT '1',
      `approve_status` INT(32) NOT NULL DEFAULT '1',
      `datecreated` DATETIME NOT NULL,
      `days_owed` INT(11) NOT NULL,
      `delivery_date` DATE NULL,
      `subtotal` DECIMAL(15,2) NOT NULL,
      `total_tax` DECIMAL(15,2) NOT NULL,
      `total` DECIMAL(15,2) NOT NULL,
      `addedfrom` INT(11) NOT NULL,
      `vendornote` TEXT NULL,
      `terms` TEXT NULL,
      `discount_percent` DECIMAL(15,2) NULL DEFAULT '0.00',
      `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
      `discount_type` VARCHAR(30) NULL,
      `buyer` INT(11) NOT NULL DEFAULT '0',
      `status_goods` INT(11) NOT NULL DEFAULT '0', 
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_order_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_order_detail` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_order` INT(11) NOT NULL,
  `item_code` VARCHAR(100) NOT NULL,
  `unit_id` INT(11) NULL,
  `unit_price` DECIMAL(15,0) NULL,
  `quantity` int(11) NOT NULL,
  `into_money` DECIMAL(15,0) NULL,
  `tax` text NULL,
  `total` DECIMAL(15,0) NULL,
  `discount_%` DECIMAL(15,0) NULL,
  `discount_money` DECIMAL(15,0) NULL,
  `total_money` DECIMAL(15,0) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_contracts')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_contracts` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `contract_number` varchar(200) NOT NULL,
      `contract_name` varchar(200) NOT NULL,
      `content` LONGTEXT NULL,
      `vendor` INT(11) NOT NULL,
      `pur_order` INT(11) NOT NULL,
      `contract_value` DECIMAL(15,0) NOT NULL,
      `start_date` date NOT NULL,
      `end_date` date NULL,
      `buyer` INT(11) NULL,
      `time_payment`date NULL,
      `add_from` INT(11) NOT NULL,
      `signed` INT(32) NOT NULL DEFAULT '0',
      `note` LONGTEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('contract_value' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
    ADD COLUMN `contract_value` DECIMAL(15,0) NULL AFTER `pur_order`
  ;");
}

if (!$CI->db->field_exists('signed_status' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
    ADD COLUMN `signed_status` varchar(50) NULL AFTER `note`
  ;");
}

if (!$CI->db->field_exists('signed_date' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
    ADD COLUMN `signed_date` DATE NULL AFTER `note`
  ;");
}

if (!$CI->db->field_exists('signer' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
    ADD COLUMN `signer` INT(11) NULL AFTER `note`
  ;");
}


if (!$CI->db->field_exists('sender', db_prefix() .'pur_approval_details')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'pur_approval_details` 
ADD COLUMN `sender` INT(11) NULL AFTER `action`,
ADD COLUMN `date_send` DATETIME NULL AFTER `sender`;');            
}

if (!$CI->db->table_exists(db_prefix() . 'purchase_option')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "purchase_option` (
      `option_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `option_name` varchar(200) NOT NULL,
      `option_val` longtext NULL,
      `auto` tinyint(1) NULL,
      PRIMARY KEY (`option_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (row_purchase_options_exist('"purchase_order_setting"') == 0){
  $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("purchase_order_setting", "1", "1");
');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_order_payment')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_order_payment` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `pur_order` int(11) NOT NULL,
      `amount` DECIMAL(15,2) NOT NULL,
      `paymentmode` LONGTEXT NULL,
      `date` DATE NOT NULL,
      `daterecorded` DATETIME NOT NULL,
      `note` TEXT NOT NULL,
      `transactionid` MEDIUMTEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('commodity_code' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `commodity_code` varchar(100) NOT NULL,
  ADD COLUMN `commodity_barcode` text NULL,
  ADD COLUMN `unit_id` int(11) NULL,
  ADD COLUMN `sku_code` varchar(200)  NULL,
  ADD COLUMN `sku_name` varchar(200)  NULL,
  ADD COLUMN `purchase_price` decimal(15,2)  NULL
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'ware_unit_type')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "ware_unit_type` (
      `unit_type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `unit_code` varchar(100) NULL,
      `unit_name` text NULL,
      `unit_symbol` text NULL,
      `order` int(10) NULL,
      `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
      `note` text NULL,
      PRIMARY KEY (`unit_type_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('commodity_group_code' ,db_prefix() . 'items_groups')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items_groups`
  ADD COLUMN `commodity_group_code` varchar(100) NULL AFTER `name`,
  ADD COLUMN `order` int(10) NULL AFTER `commodity_group_code`,
  ADD COLUMN `display` int(1)  NULL AFTER `order` ,
  ADD COLUMN `note` text NULL AFTER `display`
  ;");
}

// Version 1.0.1
if (row_purchase_options_exist('"pur_order_prefix"') == 0){
    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("pur_order_prefix", "#PO", "1");
  ');
  }

  if (!$CI->db->field_exists('number', db_prefix() .'pur_orders')) {
      $CI->db->query('ALTER TABLE `'.db_prefix() . 'pur_orders` 
    ADD COLUMN `number` INT(11) NULL;');            
  }

  if (!$CI->db->field_exists('expense_convert', db_prefix() .'pur_orders')) {
      $CI->db->query('ALTER TABLE `'.db_prefix() . 'pur_orders` 
    ADD COLUMN `expense_convert` INT(11) NULL DEFAULT "0";');            
  }

// Version 1.0.2
if (!$CI->db->field_exists('vendor', db_prefix() .'expenses')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'expenses` 
  ADD COLUMN `vendor` INT(11) NULL;');            
}

// Version 1.0.3
if (!$CI->db->table_exists(db_prefix() . 'pur_vendor_items')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_vendor_items` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `vendor` int(11) NOT NULL,
    `group_items` int(11) NULL,
    `items` int(11) NOT NULL,
    `add_from` int(11) NULL,
    `datecreate` DATE NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// Version 1.0.4
if (!$CI->db->field_exists('description' ,db_prefix() . 'pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
    ADD COLUMN `description` TEXT NULL AFTER `item_code`
  ;");
}

if (!$CI->db->field_exists('commodity_group_code' ,db_prefix() . 'items_groups')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items_groups`
  ADD COLUMN `commodity_group_code` varchar(100) NULL AFTER `name`,
  ADD COLUMN `order` int(10) NULL AFTER `commodity_group_code`,
  ADD COLUMN `display` int(1)  NULL AFTER `order` ,
  ADD COLUMN `note` text NULL AFTER `display`
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'wh_sub_group')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_sub_group` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `sub_group_code` varchar(100) NULL,
      `sub_group_name` text NULL,
      `order` int(10) NULL,
      `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
      `note` text NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('group_id' ,db_prefix() . 'wh_sub_group')) { 
    $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_sub_group`
        ADD COLUMN `group_id` int(11)  NULL
    ;");
  } 

if (!$CI->db->field_exists('sub_group' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `sub_group` varchar(200)  NULL
  ;");
}

//version 1.0.5, update decimal 15,0 to 15,2

        //purchase request detail
        if ($CI->db->field_exists('unit_price' ,db_prefix() . 'pur_request_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
        CHANGE COLUMN `unit_price` `unit_price` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('into_money' ,db_prefix() . 'pur_request_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
        CHANGE COLUMN `into_money` `into_money` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    //purchase order detail
    if ($CI->db->field_exists('unit_price' ,db_prefix() . 'pur_order_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
        CHANGE COLUMN `unit_price` `unit_price` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('into_money' ,db_prefix() . 'pur_order_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
        CHANGE COLUMN `into_money` `into_money` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('total' ,db_prefix() . 'pur_order_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
        CHANGE COLUMN `total` `total` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('discount_%' ,db_prefix() . 'pur_order_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
        CHANGE COLUMN `discount_%` `discount_%` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('discount_money' ,db_prefix() . 'pur_order_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
        CHANGE COLUMN `discount_money` `discount_money` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('total_money' ,db_prefix() . 'pur_order_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
        CHANGE COLUMN `total_money` `total_money` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    //pur estimate detail
    if ($CI->db->field_exists('unit_price' ,db_prefix() . 'pur_contracts')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
        CHANGE COLUMN `unit_price` `unit_price` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('into_money' ,db_prefix() . 'pur_estimate_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
        CHANGE COLUMN `into_money` `into_money` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('total' ,db_prefix() . 'pur_estimate_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
        CHANGE COLUMN `total` `total` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('total_money' ,db_prefix() . 'pur_estimate_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
        CHANGE COLUMN `total_money` `total_money` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('discount_money' ,db_prefix() . 'pur_estimate_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
        CHANGE COLUMN `discount_money` `discount_money` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('discount_%' ,db_prefix() . 'pur_estimate_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
        CHANGE COLUMN `discount_%` `discount_%` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    //pur contract
    if ($CI->db->field_exists('contract_value' ,db_prefix() . 'pur_contracts')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
        CHANGE COLUMN `contract_value` `contract_value` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

// purchase request hash
if (!$CI->db->field_exists('hash' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
    ADD COLUMN `hash` VARCHAR(32) NULL
  ;");
}

// purchase order hash
if (!$CI->db->field_exists('hash' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
    ADD COLUMN `hash` VARCHAR(32) NULL
  ;");
}

// version 1.0.6  purchase order client
if (!$CI->db->field_exists('clients' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
    ADD COLUMN `clients` TEXT NULL
  ;");
}

// version 1.0.6  purchase request fix
if ($CI->db->field_exists('inventory_quantity' ,db_prefix() . 'pur_request_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
    CHANGE COLUMN `inventory_quantity` `inventory_quantity` INT(11) NULL DEFAULT '0'
  ;");
}

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
      ADD COLUMN `project` INT(11)  NULL
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

//version 1.0.7 Purchase option

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

//version 1.0.7 Purchase request detail
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
      `contract` int(11) NULL,
      `vendor` int(11) NULL,
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

if (row_purchase_options_exist('"item_by_vendor"') == 0){
  $CI->db->query('INSERT INTO `'.db_prefix().'purchase_option` (`option_name`, `option_val`, `auto`) VALUES ("item_by_vendor", "0", "1");
');
}

// version 1.0.9 term condition & vendor note
if (row_purchase_options_exist('"terms_and_conditions"') == 0){
  $CI->db->query('INSERT INTO `'.db_prefix().'purchase_option` (`option_name`, `option_val`, `auto`) VALUES ("terms_and_conditions", "", "1");
');
}

if (row_purchase_options_exist('"vendor_note"') == 0){
  $CI->db->query('INSERT INTO `'.db_prefix().'purchase_option` (`option_name`, `option_val`, `auto`) VALUES ("vendor_note", "", "1");
');
}

// 1.1.0
if (!$CI->db->table_exists(db_prefix() . 'pur_comments')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_comments` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `content` MEDIUMTEXT NULL,
      `rel_type` VARCHAR(50) NOT NULL,
      `rel_id` INT(11) NULL,
      `staffid` INT(11) NOT NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

//version 1.10 Purchase request detail
if (!$CI->db->field_exists('tax' ,db_prefix() . 'pur_request_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
      ADD COLUMN `tax` TEXT  NULL
  ;");
}

//version 1.10 Purchase request detail
if (!$CI->db->field_exists('tax_rate' ,db_prefix() . 'pur_request_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
      ADD COLUMN `tax_rate` TEXT  NULL
  ;");
}

//version 1.10 Purchase request detail
if (!$CI->db->field_exists('tax_value' ,db_prefix() . 'pur_request_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
      ADD COLUMN `tax_value` DECIMAL(15,2)  NULL
  ;");
}

//version 1.10 Purchase request detail
if (!$CI->db->field_exists('total' ,db_prefix() . 'pur_request_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
      ADD COLUMN `total` DECIMAL(15,2)  NULL
  ;");
}

if (!$CI->db->field_exists('subtotal' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
      ADD COLUMN `subtotal` DECIMAL(15,2) NULL
  ;");
}

if (!$CI->db->field_exists('total_tax' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
      ADD COLUMN `total_tax` DECIMAL(15,2) NULL
  ;");
}

if (!$CI->db->field_exists('total' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
      ADD COLUMN `total` DECIMAL(15,2) NULL
  ;");
}

if (!$CI->db->field_exists('sale_invoice' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
      ADD COLUMN `sale_invoice` int(11) NULL
  ;");
}

if (!$CI->db->field_exists('sale_invoice' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `sale_invoice` int(11) NULL
  ;");
}

if (!$CI->db->field_exists('tax_value' ,db_prefix() . 'pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
      ADD COLUMN `tax_value` DECIMAL(15,2) NULL
  ;");
}

if (!$CI->db->field_exists('tax_rate' ,db_prefix() . 'pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
      ADD COLUMN `tax_rate` TEXT NULL
  ;");
}

//Ver 1.1.1
if (!$CI->db->field_exists('tax_value' ,db_prefix() . 'pur_estimate_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
      ADD COLUMN `tax_value` DECIMAL(15,2) NULL
  ;");
}

if (!$CI->db->field_exists('tax_rate' ,db_prefix() . 'pur_estimate_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
      ADD COLUMN `tax_rate` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('active' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `active` INT(11) NULL DEFAULT 1
  ;");
}

if (!$CI->db->field_exists('parent_id' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `parent_id` int(11)  NULL  DEFAULT NULL
  ;");
}

if (!$CI->db->field_exists('attributes' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `attributes` LONGTEXT  NULL
  ;");
}

if (!$CI->db->field_exists('parent_attributes' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `parent_attributes` LONGTEXT  NULL
  ;");
}


//Ver 1.1.2
if ($CI->db->field_exists('quantity' ,db_prefix() . 'pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
      CHANGE COLUMN `quantity` `quantity` DECIMAL(15,2) NOT NULL 
  ;");
}

if ($CI->db->field_exists('quantity' ,db_prefix() . 'pur_estimate_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
       CHANGE COLUMN `quantity` `quantity` DECIMAL(15,2) NOT NULL 
  ;");
}

if ($CI->db->field_exists('quantity' ,db_prefix() . 'pur_request_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
       CHANGE COLUMN `quantity` `quantity` DECIMAL(15,2) NOT NULL 
  ;");
}

//Ver 1.1.3
create_email_template('Purchase Request', '<span style=\"font-size: 12pt;\"> Hello !. </span><br /><br /><span style=\"font-size: 12pt;\"> We would like to share with you a link of Purchase Request information with the number {pr_number} </span><br /><br /><span style=\"font-size: 12pt;\"><br />Please click on the link to view information: {public_link}<br/ >{additional_content}
  </span><br /><br />', 'purchase_order', 'Purchase Request (Sent to contact)', 'purchase-request-to-contact');

create_email_template('Purchase Quotation', '<span style=\"font-size: 12pt;\"> Hello !. </span><br /><br /><span style=\"font-size: 12pt;\"> We would like to share with you a link of Purchase Quotation information with the number {pq_number} </span><br /><br /><span style=\"font-size: 12pt;\"><br />Please click on the link to view information: {quotation_link}<br/ >{additional_content}
  </span><br /><br />', 'purchase_order', 'Purchase Quotation (Sent to contact)', 'purchase-quotation-to-contact');

//Ver 1.1.5

if (!$CI->db->field_exists('recurring' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `recurring` INT(11) NULL
  ;");
}

if (!$CI->db->field_exists('recurring_type' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `recurring_type` VARCHAR(10) NULL
  ;");
}

if (!$CI->db->field_exists('cycles' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `cycles` INT(11) NULL DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('total_cycles' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `total_cycles` INT(11) NULL DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('last_recurring_date' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `last_recurring_date` DATE NULL
  ;");
}

if (!$CI->db->field_exists('is_recurring_from' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `is_recurring_from` INT(11) NULL
  ;");
}

if (!$CI->db->field_exists('duedate' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `duedate` DATE NULL
  ;");
}

add_option('pur_invoice_auto_operations_hour', 21);

if (!$CI->db->field_exists('compare_note' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
      ADD COLUMN `compare_note` text NULL
  ;");
}

if (!$CI->db->field_exists('make_a_contract' ,db_prefix() . 'pur_estimates')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimates`
      ADD COLUMN `make_a_contract` text NULL
  ;");
}

// Debit notes function
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

create_email_template('Purchase Statement', '<span style=\"font-size: 12pt;\"> Dear {contact_firstname} {contact_lastname} !. </span><br /><br /><span style=\"font-size: 12pt;\">Its been a great experience working with you. </span><br /><br /><span style=\"font-size: 12pt;\"><br />Attached with this email is a list of all transactions for the period between {statement_from} to {statement_to}<br/ ><br/ >For your information your account balance due is total: {statement_balance_due}<br /><br/ > Please contact us if you need more information.<br/ > <br />{additional_content}
  </span><br /><br />', 'purchase_order', 'Purchase Statement (Sent to contact)', 'purchase-statement-to-contact');

add_option('show_purchase_tax_column', 1);
add_option('po_only_prefix_and_number', 0);

if ($CI->db->field_exists('address' ,db_prefix() . 'pur_vendor')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
      CHANGE COLUMN `address` `address` TEXT NULL DEFAULT NULL
  ;");
}

if ($CI->db->field_exists('unit_price' ,db_prefix() . 'pur_estimate_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
    CHANGE COLUMN `unit_price` `unit_price` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}


// ---------------- version 1.2.1
// purchase, => can_be_purchased
// inventory => can_be_inventory
// loyalty => can_be_sold
// omni_sale => can_be_sold
// sale_invoice => can_be_sold
// manufacturing order => can_be_manufacturing
// affiliate => can_be_sold

if (!$CI->db->field_exists('can_be_sold' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `can_be_sold` VARCHAR(100) NULL DEFAULT 'can_be_sold'
  ;");
}
if (!$CI->db->field_exists('can_be_purchased' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `can_be_purchased` VARCHAR(100) NULL DEFAULT 'can_be_purchased' 
  ;");
}
if (!$CI->db->field_exists('can_be_manufacturing' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `can_be_manufacturing` VARCHAR(100) NULL DEFAULT 'can_be_manufacturing' 
  ;");
}

if (!$CI->db->field_exists('can_be_inventory' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `can_be_inventory` VARCHAR(100) NULL DEFAULT 'can_be_inventory' 
  ;");
}

if (!$CI->db->field_exists('tax_name' ,db_prefix() . 'pur_request_detail')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
  ADD COLUMN `tax_name` TEXT NULL 
  ;");
}

if (!$CI->db->field_exists('tax_name' ,db_prefix() . 'pur_estimate_detail')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
  ADD COLUMN `tax_name` TEXT NULL 
  ;");
}

if (!$CI->db->field_exists('item_name' ,db_prefix() . 'pur_estimate_detail')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
  ADD COLUMN `item_name` TEXT NULL 
  ;");
}

if (!$CI->db->field_exists('tax_name' ,db_prefix() . 'pur_order_detail')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
  ADD COLUMN `tax_name` TEXT NULL 
  ;");
}

if (!$CI->db->field_exists('item_name' ,db_prefix() . 'pur_order_detail')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
  ADD COLUMN `item_name` TEXT NULL 
  ;");
}

if (!$CI->db->field_exists('send_to_vendors' ,db_prefix() . 'pur_request')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
  ADD COLUMN `send_to_vendors` TEXT NULL 
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'items_of_vendor')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "items_of_vendor` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `vendor_id` INT(11) NOT NULL,
      `description` TEXT NOT NULL,
      `long_description` TEXT NULL,
      `rate` DECIMAL(15,2) NULL,
      `tax` int(11) NULL,
      `tax2` int(11) NULL,
      `unit` varchar(40) NULL,
      `group_id` int(11) NOT NULL,
      `commodity_code` varchar(100) NOT NULL,
      `commodity_barcode` TEXT NULL,
      `unit_id` int(11) NULL,
      `sku_code` VARCHAR(200) NULL,
      `sku_name` VARCHAR(200) NULL,
      `sub_group` VARCHAR(200) NULL,
      `active` INT(11) NULL,
      `parent` INT(11) NULL,
      `attributes` LONGTEXT NULL,
      `parent_attributes` LONGTEXT NULL,
      `commodity_type` INT(11) NULL,
      `origin` VARCHAR(100) NULL,
      `commodity_name` VARCHAR(200) NOT NULL,
      `series_id` TEXT NULL,
      `long_descriptions` LONGTEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('share_status' ,db_prefix() . 'items_of_vendor')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "items_of_vendor`
  ADD COLUMN `share_status` int(1) NULL DEFAULT 0
  ;");
}

if (!$CI->db->field_exists('from_vendor_item' ,db_prefix() . 'items')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `from_vendor_item` int(11) NULL
  ;");
}


if (!$CI->db->field_exists('add_from_type' ,db_prefix() . 'pur_invoices')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
  ADD COLUMN `add_from_type` varchar(20) NULL
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'currency_rates')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "currency_rates` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `from_currency_id` int(11) NULL,
    `from_currency_name` VARCHAR(100) NULL,
    `from_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
    `to_currency_id` int(11) NULL,
    `to_currency_name` VARCHAR(100) NULL,
    `to_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
    `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'currency_rate_logs')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "currency_rate_logs` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `from_currency_id` int(11) NULL,
    `from_currency_name` VARCHAR(100) NULL,
    `from_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
    `to_currency_id` int(11) NULL,
    `to_currency_name` VARCHAR(100) NULL,
    `to_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
    `date` DATE NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


add_option('cr_date_cronjob_currency_rates', '');
add_option('cr_automatically_get_currency_rate', 1);
add_option('cr_global_amount_expiration', 0);

if (!$CI->db->field_exists('currency' ,db_prefix() . 'pur_request')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
  ADD COLUMN `currency` INT(11) NULL DEFAULT 0
  ;");
}

if (!$CI->db->field_exists('currency' ,db_prefix() . 'pur_orders')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
  ADD COLUMN `currency` INT(11) NULL DEFAULT 0
  ;");
}

if (!$CI->db->field_exists('currency' ,db_prefix() . 'pur_invoices')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
  ADD COLUMN `currency` INT(11) NULL DEFAULT 0
  ;");
}

// --- Version 1.2.2

if (!$CI->db->field_exists('order_status' ,db_prefix() . 'pur_orders')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
  ADD COLUMN `order_status` VARCHAR(30) NULL
  ;");
}

if (!$CI->db->field_exists('shipping_note' ,db_prefix() . 'pur_orders')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
  ADD COLUMN `shipping_note` TEXT NULL
  ;");
}


// Order returns
// return request must be placed within X days after the delivery date
  add_option('pur_return_request_within_x_day', 30, 1);
  add_option('pur_fee_for_return_order', 0, 1);
  add_option('pur_return_policies_information', '', 1);

  add_option('pur_order_return_number_prefix', 'OReturn', 1);
  add_option('next_pur_order_return_number', 1, 1);


if (!$CI->db->table_exists(db_prefix() . 'wh_order_returns')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_order_returns` (

    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `rel_id` INT(11) NULL,
    `rel_type` VARCHAR(50) NOT NULL COMMENT'manual, sales_return_order, purchasing_return_order',
    `return_type` VARCHAR(50) NULL COMMENT'manual, partially, fully',
    `company_id` INT(11) NULL,
    `company_name` VARCHAR(500) NULL,
    `email` VARCHAR(100) NULL,
    `phonenumber` VARCHAR(20) NULL,
    `order_number` VARCHAR(500) NULL,
    `order_date` DATETIME NULL,
    `number_of_item` DECIMAL(15,2) NULL DEFAULT '0.00',
    `order_total` DECIMAL(15,2) NULL DEFAULT '0.00',
    `order_return_number` VARCHAR(200) NULL,
    `order_return_name` VARCHAR(500) NULL,
    `fee_return_order` DECIMAL(15,2) NULL DEFAULT '0.00',
    `refund_loyaty_point` INT(11) NULL DEFAULT '0',
    `subtotal` DECIMAL(15,2) NULL DEFAULT '0.00',
    `total_amount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
    `additional_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `adjustment_amount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `return_policies_information` TEXT NULL,
    `admin_note` TEXT NULL,
    `approval` INT(11) NULL DEFAULT 0,
    `datecreated` DATETIME NULL,
    `staff_id` INT(11) NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('company_id' ,db_prefix() . 'wh_order_returns')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
  ADD COLUMN `company_id` INT(11) NULL 
  ;");
}

if (!$CI->db->field_exists('currency' ,db_prefix() . 'wh_order_returns')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
  ADD COLUMN `currency` INT(11) NULL 
  ;");
}
if ($CI->db->field_exists('discount_total' ,db_prefix() . 'wh_order_returns')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
  CHANGE COLUMN `discount_total` `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00' 
   ;");
}
if ($CI->db->field_exists('additional_discount' ,db_prefix() . 'wh_order_returns')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
  CHANGE COLUMN `additional_discount` `additional_discount` DECIMAL(15,2) NULL DEFAULT '0.00' ;");
}
if ($CI->db->field_exists('adjustment_amount' ,db_prefix() . 'wh_order_returns')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
  CHANGE COLUMN `adjustment_amount` `adjustment_amount` DECIMAL(15,2) NULL DEFAULT '0.00' ;");
}
if ($CI->db->field_exists('total_after_discount' ,db_prefix() . 'wh_order_returns')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
  CHANGE COLUMN `total_after_discount` `total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00' ;");
}




if (!$CI->db->table_exists(db_prefix() . 'wh_order_return_details')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_order_return_details` (

    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `order_return_id` INT(11) NOT NULL,
    `rel_type_detail_id` INT(11) NULL,
    `commodity_code` INT(11) NULL,
    `commodity_name` TEXT NULL,
    `quantity` DECIMAL(15,2) NULL DEFAULT '0.00',
    `unit_id` INT(11) NULL,
    `unit_price` DECIMAL(15,2) NULL DEFAULT '0.00',
    `sub_total` DECIMAL(15,2) NULL DEFAULT '0.00',
    `tax_id`  TEXT NULL,
    `tax_rate`  TEXT NULL,
    `tax_name`  TEXT NULL,
    `total_amount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `discount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
    `total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `reason_return` VARCHAR(200) NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if ($CI->db->field_exists('discount' ,db_prefix() . 'wh_order_return_details')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_return_details`
  CHANGE COLUMN `discount` `discount` DECIMAL(15,2) NULL DEFAULT '0.00' 
   ;");
}
if ($CI->db->field_exists('discount_total' ,db_prefix() . 'wh_order_return_details')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_return_details`
  CHANGE COLUMN `discount_total` `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00' ;");
}
if ($CI->db->field_exists('total_after_discount' ,db_prefix() . 'wh_order_return_details')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_return_details`
  CHANGE COLUMN `total_after_discount` `total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00' ;");
}

if (!$CI->db->field_exists('receipt_delivery_id' ,db_prefix() . 'wh_order_returns')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
  ADD COLUMN `receipt_delivery_id` INT(1) NULL  DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('return_reason' ,db_prefix() . 'wh_order_returns')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_order_returns`
  ADD COLUMN `return_reason` longtext NULL
  ');
}


// Draft, Processing, Pending payment, Confirm, Shipping, Finish, Failed, Canceled, On Hold.
if (!$CI->db->field_exists('status' ,db_prefix() . 'wh_order_returns')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_order_returns`
  ADD COLUMN `status` varchar(30) NULL DEFAULT "draft"                                             
  ');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_activity_log')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_activity_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `rel_id` INT(11) NOT NULL,
  `rel_type` VARCHAR(45) NOT NULL,
  `staffid` INT(11) NULL,
  `date` DATETIME NULL,
  `note` TEXT NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'wh_goods_delivery_activity_log')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_goods_delivery_activity_log` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `rel_id` int NULL ,
    `rel_type` varchar(100) NULL ,
    `description` mediumtext NULL,
    `additional_data` text NULL,
    `date` datetime NULL,
    `staffid` int(11) NULL,
    `full_name` varchar(100) NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_invoice_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_invoice_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_invoice` INT(11) NOT NULL,
  `item_code` VARCHAR(100) NULL,
  `description` TEXT NULL,
  `unit_id` INT(11) NULL,
  `unit_price` DECIMAL(15,2) NULL,
  `quantity` DECIMAL(15,2) NULL,
  `into_money` DECIMAL(15,2) NULL,
  `tax` TEXT NULL,
  `total` DECIMAL(15,2) NULL,
  `discount_percent` DECIMAL(15,2) NULL,
  `discount_money` DECIMAL(15,2) NULL,
  `total_money` DECIMAL(15,2) NULL,
  `tax_value` DECIMAL(15,2) NULL,
  `tax_rate` TEXT NULL,
  `tax_name` TEXT NULL,
  `item_name` TEXT NULL,
  PRIMARY KEY (`id`));');
}


if (!$CI->db->field_exists('discount_total' ,db_prefix() . 'pur_invoices')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_invoices`
  ADD COLUMN `discount_total` DECIMAL(15,2) NULL
  ');
}

if (!$CI->db->field_exists('discount_percent' ,db_prefix() . 'pur_invoices')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_invoices`
  ADD COLUMN `discount_percent` DECIMAL(15,2) NULL
  ');
}

if (!$CI->db->field_exists('return_within_day' ,db_prefix() . 'pur_vendor')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_vendor`
  ADD COLUMN `return_within_day` INT(11) NULL
  ');
}

if (!$CI->db->field_exists('return_order_fee' ,db_prefix() . 'pur_vendor')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_vendor`
  ADD COLUMN `return_order_fee` DECIMAL(15,2) NULL
  ');
}

if (!$CI->db->field_exists('return_policies' ,db_prefix() . 'pur_vendor')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_vendor`
  ADD COLUMN `return_policies` TEXT NULL
  ');
}

if (!$CI->db->table_exists(db_prefix() . 'wh_order_returns_refunds')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_order_returns_refunds` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `order_return_id` INT(11) NULL,
      `staff_id` INT(11) NULL,
      `refunded_on` date NULL,
      `payment_mode` varchar(40) NULL,
      `note` text NULL,
      `amount` decimal(15,2) NULL,
      `created_at` datetime NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('currency_rate' ,db_prefix() . 'pur_request')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_request`
  ADD COLUMN `currency_rate` DECIMAL(15,6) NULL
  ');
}

if (!$CI->db->field_exists('from_currency' ,db_prefix() . 'pur_request')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_request`
  ADD COLUMN `from_currency` VARCHAR(20) NULL
  ');
}

if (!$CI->db->field_exists('to_currency' ,db_prefix() . 'pur_request')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_request`
  ADD COLUMN `to_currency` VARCHAR(20) NULL
  ');
}

if (!$CI->db->field_exists('currency_rate' ,db_prefix() . 'pur_estimates')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_estimates`
  ADD COLUMN `currency_rate` DECIMAL(15,6) NULL
  ');
}

if (!$CI->db->field_exists('from_currency' ,db_prefix() . 'pur_estimates')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_estimates`
  ADD COLUMN `from_currency` VARCHAR(20) NULL
  ');
}

if (!$CI->db->field_exists('to_currency' ,db_prefix() . 'pur_estimates')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_estimates`
  ADD COLUMN `to_currency` VARCHAR(20) NULL
  ');
}

if (!$CI->db->field_exists('currency_rate' ,db_prefix() . 'pur_orders')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_orders`
  ADD COLUMN `currency_rate` DECIMAL(15,6) NULL
  ');
}

if (!$CI->db->field_exists('from_currency' ,db_prefix() . 'pur_orders')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_orders`
  ADD COLUMN `from_currency` VARCHAR(20) NULL
  ');
}

if (!$CI->db->field_exists('to_currency' ,db_prefix() . 'pur_orders')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_orders`
  ADD COLUMN `to_currency` VARCHAR(20) NULL
  ');
}

if (!$CI->db->field_exists('currency_rate' ,db_prefix() . 'pur_invoices')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_invoices`
  ADD COLUMN `currency_rate` DECIMAL(15,6) NULL
  ');
}

if (!$CI->db->field_exists('from_currency' ,db_prefix() . 'pur_invoices')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_invoices`
  ADD COLUMN `from_currency` VARCHAR(20) NULL
  ');
}

if (!$CI->db->field_exists('to_currency' ,db_prefix() . 'pur_invoices')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'pur_invoices`
  ADD COLUMN `to_currency` VARCHAR(20) NULL
  ');
}
// 1.2.6 create purchase request from  sale estimate
if (!$CI->db->field_exists('sale_estimate' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
    ADD COLUMN `sale_estimate` INT(11) NULL
  ;");
}

if (!$CI->db->field_exists('guarantee', 'items')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'items` 
    ADD COLUMN `guarantee` text  NULL 
    
    ;');            
}

if (!$CI->db->field_exists('profif_ratio' ,db_prefix() . 'items')) { 
    $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
        ADD COLUMN `profif_ratio` text  NULL
    ;");
}

if (!$CI->db->field_exists('long_descriptions' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `long_descriptions` LONGTEXT NULL
  ;");
}

if (!$CI->db->field_exists('without_checking_warehouse' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `without_checking_warehouse` int(11) NULL default 0
  ;");
}

if (!$CI->db->field_exists('series_id' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `series_id` TEXT  NULL
  ;");
}

if (!$CI->db->field_exists('warehouse_id' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
    ADD COLUMN `warehouse_id` int(11) NULL;
    ");
}

if (!$CI->db->field_exists('origin' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
    ADD COLUMN `origin` varchar(100) NULL;
    ");
}
if (!$CI->db->field_exists('color_id' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
    ADD COLUMN `color_id` int(11) NULL;
    ");
}
if (!$CI->db->field_exists('style_id' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
    ADD COLUMN `style_id` int(11) NULL;
    ");
}
if (!$CI->db->field_exists('model_id' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
    ADD COLUMN `model_id` int(11) NULL;
    ");
}
if (!$CI->db->field_exists('size_id' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
    ADD COLUMN `size_id` int(11) NULL;
    ");
}

if (!$CI->db->field_exists('unit_id' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `unit_id` int(11) NULL
  ;");
}

// 1.2.7 shipping fee
if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'pur_estimates')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimates`
      ADD COLUMN `shipping_fee` decimal(15,2) NULL
  ;");
}

if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `shipping_fee` decimal(15,2) NULL
  ;");
}

if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `shipping_fee` decimal(15,2) NULL
  ;");
}

// 1.2.9 option enable/disable send mail welcome contact & reset purchase order number every month.
add_option('send_email_welcome_for_new_contact', 1, 1);
add_option('reset_purchase_order_number_every_month', 1, 1);

if (!$CI->db->field_exists('vendor_invoice_number' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `vendor_invoice_number` TEXT NULL
  ;");
}

// 1.3.3 update shipping information for PO.

add_option('pur_company_address', '', 1);
add_option('pur_company_city', '', 1);
add_option('pur_company_state', '', 1);
add_option('pur_company_zipcode', '', 1);
add_option('pur_company_country_code', '', 1);


if (!$CI->db->field_exists('shipping_address' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `shipping_address` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('shipping_city' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `shipping_city` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('shipping_state' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `shipping_state` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('shipping_zip' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `shipping_zip` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('shipping_country' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `shipping_country` INT(11) NULL
  ;");
}

add_option('allow_vendors_to_register', 1);

create_email_template('Your registration is confirmed', '<p>Dear {contact_firstname} {contact_lastname}<br /><br />We just wanted to let you know that your registration at&nbsp;{companyname} is successfully confirmed and your account is now active.<br /><br />You can login at&nbsp;<a href="{vendor_portal_url}">{vendor_portal_url}</a> with the email and password you provided during registration.<br /><br />Please contact us if you need any help.<br /><br />Kind Regards, <br />{email_signature}</p>
 <p><br />(This is an automated email, so please dont reply to this email address)</p>', 'purchase_order', 'Vendor Registration Confirmed', 'vendor-registration-confirmed');

if (!$CI->db->field_exists('signature' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
      ADD COLUMN `signature` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('marked_as_signed' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
      ADD COLUMN `marked_as_signed` TINYINT(1) NULL DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('acceptance_firstname' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
      ADD COLUMN `acceptance_firstname` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('acceptance_lastname' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
      ADD COLUMN `acceptance_lastname` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('acceptance_email' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
      ADD COLUMN `acceptance_email` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('acceptance_date' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
      ADD COLUMN `acceptance_date` DATETIME NULL
  ;");
}

if (!$CI->db->field_exists('acceptance_ip' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
      ADD COLUMN `acceptance_ip` TEXT NULL
  ;");
}

create_email_template('Purchase Contract', '<span style=\"font-size: 12pt;\"> Hello !. </span><br /><br /><span style=\"font-size: 12pt;\"> We would like to share with you a link of Purchase Contract information with the number {contract_number} </span><br /><br /><span style=\"font-size: 12pt;\"><br />Please click on the link to view information: {contract_link}
  </span><br /><br />', 'purchase_order', 'Purchase Contract (Sent to contact)', 'purchase-contract-to-contact');


create_email_template('New Contact Added/Registered (Welcome Email)', '<span style=\"font-size: 12pt;\"> Dear {contact_firstname} {contact_lastname}! </span><br /><br /><span style=\"font-size: 12pt;\"> Welcome to our system </span><br /><br /><span style=\"font-size: 12pt;\"><br />Click here to login: {vendor_portal_link}</span><br /></span><br />', 'purchase_order', 'New Contact Added/Registered (Welcome Email)', 'new-contact-created');


create_email_template('Request approval', '<span style=\"font-size: 12pt;\"> Hello {staff_name}! </span><br /><br /><span style=\"font-size: 12pt;\"> You receive an approval request: {link} from {from_staff_name}</span>', 'purchase_order', 'Request approval', 'purchase-request-approval');

create_email_template('Email send approved', '<span style=\"font-size: 12pt;\"> Hello {staff_name}! </span><br /><br /><span style=\"font-size: 12pt;\">{type} has been approved by {by_staff_name} </span><br /><span style=\"font-size: 12pt;\"><br />Click here to view detail: {link} </span><br /></span><br />', 'purchase_order', 'Email send approved', 'purchase-send-approved');

create_email_template('Email send rejected', '<span style=\"font-size: 12pt;\"> Hello {staff_name}! </span><br /><br /><span style=\"font-size: 12pt;\"> {type} has been declined by {by_staff_name} </span><br /><span style=\"font-size: 12pt;\"><br />Click here to view detail: {link}  </span><br /></span><br />', 'purchase_order', 'Email send rejected', 'purchase-send-rejected');

if (!$CI->db->field_exists('discount_type' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `discount_type` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('discount_type' ,db_prefix() . 'wh_order_returns')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
  ADD COLUMN `discount_type` TEXT NULL 
  ;");
}

add_option('pur_company_country_text', '');

if (!$CI->db->field_exists('shipping_country_text' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `shipping_country_text` TEXT NULL
  ;");
}

if ($CI->db->field_exists('department' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
  CHANGE COLUMN `department` `department` INT(11) NULL DEFAULT NULL ;");
}

if (!$CI->db->field_exists('wh_quantity_received' ,db_prefix() . 'pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
      ADD COLUMN `wh_quantity_received` varchar(200)  NULL
  ;");
}