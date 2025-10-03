<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'ware_commodity_type')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "ware_commodity_type` (
      `commodity_type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `commondity_code` varchar(100) NULL,
      `commondity_name` text NULL,
      `order` int(10) NULL,
      `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
      `note` text NULL,
      PRIMARY KEY (`commodity_type_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
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
if (!$CI->db->table_exists(db_prefix() . 'ware_size_type')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "ware_size_type` (
      `size_type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `size_code` varchar(100) NULL,
      `size_name` text NULL,
      `size_symbol` text NULL,
      `order` int(10) NULL,
      `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
      `note` text NULL,
      PRIMARY KEY (`size_type_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'ware_style_type')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "ware_style_type` (
      `style_type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `style_code` varchar(100) NULL,
      `style_barcode` text NULL,
      `style_name` text NULL,
      `order` int(10) NULL,
      `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
      `note` text NULL,
      PRIMARY KEY (`style_type_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'ware_body_type')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "ware_body_type` (
      `body_type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `body_code` varchar(100) NULL,
      `body_name` text NULL,
      `order` int(10) NULL,
      `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
      `note` text NULL,
      PRIMARY KEY (`body_type_id`)
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
if (!$CI->db->table_exists(db_prefix() . 'warehouse')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "warehouse` (
      `warehouse_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `warehouse_code` varchar(100) NULL,
      `warehouse_name` text NULL,
      `warehouse_address` text NULL,
      `order` int(10) NULL,
      `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
      `note` text NULL,
      PRIMARY KEY (`warehouse_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->field_exists('commodity_code' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
    ADD COLUMN `commodity_code` varchar(100) NOT NULL;
    ");
}
if (!$CI->db->field_exists('commodity_barcode' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
    ADD COLUMN `commodity_barcode` text NULL;
    ");
}
if (!$CI->db->field_exists('commodity_type' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
    ADD COLUMN `commodity_type` int(11) NULL;
    ");
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

if (!$CI->db->table_exists(db_prefix() . 'goods_receipt')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "goods_receipt` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `supplier_code` varchar(100) NULL,
      `supplier_name` text NULL,
      `deliver_name` text NULL,
      `buyer_id` int(11) NULL,
      `description` text NULL,
      `pr_order_id` int(11) NULL COMMENT 'code puchase request agree',
      `date_c` date NULL ,
      `date_add` date NULL,
      `goods_receipt_code` varchar(100) NULL,
      `total_tax_money` varchar(100) NULL,
      `total_goods_money` varchar(100) NULL,
      `value_of_inventory` varchar(100) NULL,
      `total_money` varchar(100) NULL COMMENT 'total_money = total_tax_money +total_goods_money ',

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('approval', 'goods_receipt')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_receipt` 
ADD COLUMN `approval` INT(11) NULL DEFAULT 0 AFTER `total_money`;');            
}

if (!$CI->db->field_exists('addedfrom', 'goods_receipt')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_receipt` 
ADD COLUMN `addedfrom` INT(11) NULL AFTER `total_money`;');            
}

if (!$CI->db->table_exists(db_prefix() . 'goods_receipt_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "goods_receipt_detail` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `goods_receipt_id` int(11) NOT NULL,
      `commodity_code` varchar(100) NULL,
      `commodity_name` text NULL,
      `warehouse_id` text NULL,
      `unit_id` text NULL,
      `quantities` text NULL,
      `unit_price` varchar(100) NULL,
      `tax` varchar(100) NULL,
      `tax_money` varchar(100) NULL,
      `goods_money` varchar(100) NULL ,
      `note` text NULL ,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'goods_transaction_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "goods_transaction_detail` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `goods_receipt_id` int(11)  NULL COMMENT 'id_goods_receipt_id or goods_delivery_id',
      `goods_id` int(11) NOT NULL COMMENT ' is id commodity',
      `quantity` varchar(100) NULL,
      `date_add` DATETIME NULL,
      `commodity_id` int(11) NOT NULL,
      `warehouse_id` int(11) NOT NULL,
      `note`  text null,
      `status` int(2) NULL COMMENT '1:Goods receipt note 2:Goods delivery note',

      PRIMARY KEY (`id`,`goods_id`, `commodity_id`, `warehouse_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'inventory_manage')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "inventory_manage` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `warehouse_id` int(11) NOT NULL ,
      `commodity_id` int(11) NOT NULL,
      `inventory_number` varchar(100) NULL,

      PRIMARY KEY (`id`, `commodity_id`, `warehouse_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'inventory_commodity_min')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "inventory_commodity_min` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `commodity_id` int(11) NOT NULL,
      `commodity_code` varchar(100) NULL,
      `commodity_name` varchar(100) NULL,
      `inventory_number_min` varchar(100) NULL,

      PRIMARY KEY (`id`, `commodity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'wh_approval_setting')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'wh_approval_setting` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `related` VARCHAR(255) NOT NULL,
    `setting` LONGTEXT NOT NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'wh_approval_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'wh_approval_details` (
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

if (!$CI->db->field_exists('sender', 'wh_approval_details')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'wh_approval_details` 
ADD COLUMN `sender` INT(11) NULL AFTER `action`,
ADD COLUMN `date_send` DATETIME NULL AFTER `sender`;');            
}

if (!$CI->db->table_exists(db_prefix() . 'wh_activity_log')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'wh_activity_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `rel_id` INT(11) NOT NULL,
  `rel_type` VARCHAR(45) NOT NULL,
  `staffid` INT(11) NULL,
  `date` DATETIME NULL,
  `note` TEXT NULL,
  PRIMARY KEY (`id`));');
}

//
if (!$CI->db->table_exists(db_prefix() . 'goods_delivery')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "goods_delivery` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `rel_type` int(11) NULL COMMENT 'type goods delivery',
      `rel_document` int(11) NULL COMMENT 'document id of goods delivery',
      `customer_code` text NULL,
      `customer_name` varchar(100) NULL,
      `to_` varchar(100) NULL,
      `address` varchar(100) NULL,
      `description` text NULL COMMENT 'the reason delivery',
      `staff_id` int(11) NULL COMMENT 'salesman',
      `date_c` date NULL ,
      `date_add` date NULL,
      `goods_delivery_code` varchar(100) NULL COMMENT 'số chứng từ xuất kho',
      `approval` INT(11) NULL DEFAULT 0 COMMENT 'status approval ',
      `addedfrom` INT(11) ,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'goods_delivery_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "goods_delivery_detail` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `goods_delivery_id` int(11) NOT NULL,
      `commodity_code` varchar(100) NULL,
      `commodity_name` text NULL,
      `warehouse_id` text NULL,
      `unit_id` text NULL,
      `quantities` text NULL,
      `unit_price` varchar(100) NULL,
      `note` text NULL ,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->table_exists(db_prefix() . 'stock_take')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "stock_take` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `description` text NULL COMMENT 'the reason stock take',
      `warehouse_id` int(11) NULL ,
      `date_stock_take` date NULL ,
      `stock_take_code` varchar(100) NULL COMMENT 'số kiểm kê kho',
      `date_add` date NULL,
      `hour_add` date NULL,
      `staff_id` varchar(100) NULL,
      `approval` INT(11) NULL DEFAULT 0 COMMENT 'status approval ',
      `addedfrom` INT(11) ,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'stock_take_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "stock_take_detail` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `stock_take_id` int(11) NOT NULL,
      `commodity_code` varchar(100) NULL,
      `commodity_name` text NULL,
      `unit_id` text NULL,
      `unit_price` varchar(100) NULL,
      `quantity_stock_take` varchar(100) NULL,
      `quantity_accounting_book` varchar(100) NULL,
      `quantity_change` varchar(100) NULL,
      `handling` text NULL ,
      `reason` text NULL ,
      `approval` INT(11) NULL DEFAULT 0 COMMENT 'status approval ',

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

/*add column to table tblitem*/
if (!$CI->db->field_exists('sku_code' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `sku_code` varchar(200)  NULL
  ;");
}
if (!$CI->db->field_exists('sku_name' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `sku_name` varchar(200)  NULL
  ;");
}
if (!$CI->db->field_exists('purchase_price' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `purchase_price` decimal(15,2)  NULL
  ;");
}
if (!$CI->db->field_exists('sub_group' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `sub_group` varchar(200)  NULL
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

if (!$CI->db->table_exists(db_prefix() . 'ware_color')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "ware_color` (
      `color_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `color_code` varchar(100) NULL,
      `color_name` varchar(100) NULL,
      `color_hex` text NULL,
      `order` int(10) NULL,
      `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
      `note` text NULL,
      PRIMARY KEY (`color_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('commodity_name' ,db_prefix() . 'items')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `commodity_name` varchar(200) NOT NULL
  ;");
}
if (!$CI->db->field_exists('color' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `color` text NULL
  ;");
}
if (!$CI->db->field_exists('date_manufacture', 'inventory_manage')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'inventory_manage` 
    ADD COLUMN `date_manufacture` date NULL AFTER `inventory_number`,
    ADD COLUMN `expiry_date` date NULL AFTER `date_manufacture`;');            
}

if (!$CI->db->field_exists('warehouse_id', 'goods_receipt')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_receipt` 
    ADD COLUMN `warehouse_id` int(11) NULL AFTER `goods_receipt_code`
    ;');            
}

if (!$CI->db->field_exists('date_manufacture', 'goods_receipt_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_receipt_detail` 
    ADD COLUMN `date_manufacture` date NULL AFTER `goods_money`,
    ADD COLUMN `expiry_date` date NULL AFTER `date_manufacture`;');            
}


if (!$CI->db->table_exists(db_prefix() . 'wh_loss_adjustment')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_loss_adjustment` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,     
      `type` varchar(15) NULL,     
      `addfrom` int(11) NULL,    
      `reason` LONGTEXT NULL,   
      `time` datetime NULL,
      `date_create` date NOT NULL,
      `status` int NOT NULL,  
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('warehouses' ,db_prefix() . 'wh_loss_adjustment')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_loss_adjustment`
  ADD COLUMN `warehouses` int(11) NOT NULL AFTER `status`
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'wh_loss_adjustment_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_loss_adjustment_detail` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `items` int(11) NULL, 
      `unit` int(11) NULL,
      `current_number` int(15) NULL,     
      `updates_number` int(15) NULL, 
      `loss_adjustment` INT(11) NULL,       
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->field_exists('total_money', 'goods_delivery')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery` 
    ADD COLUMN `total_money` varchar(200) NULL AFTER `goods_delivery_code`
    ;');            
}

if (!$CI->db->field_exists('total_money', 'goods_delivery_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
    ADD COLUMN `total_money` varchar(200) NULL AFTER `unit_price`
    ;');            
}

if (!$CI->db->field_exists('warehouse_id', 'goods_delivery')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery` 
    ADD COLUMN `warehouse_id` int(11) NULL AFTER `goods_delivery_code`
    ;');            
}

if ($CI->db->field_exists('goods_id', 'goods_transaction_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_transaction_detail` 
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`id`, `commodity_id`);');            
}

if (!$CI->db->field_exists('old_quantity', 'goods_transaction_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_transaction_detail` 
    ADD COLUMN `old_quantity` varchar(100) NULL AFTER `goods_id`
    ;');            
}

//version v1.0.1
if (!$CI->db->field_exists('discount', 'goods_receipt_detail')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_receipt_detail` 
  ADD COLUMN `discount` varchar(100)
  ;');            
}

if (!$CI->db->field_exists('discount_money', 'goods_receipt_detail')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_receipt_detail` 
  ADD COLUMN `discount_money` varchar(100)
  ;');            
}

if (!$CI->db->field_exists('discount', 'goods_delivery_detail')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
  ADD COLUMN `discount` varchar(100)
  ;');            
}

if (!$CI->db->field_exists('discount_money', 'goods_delivery_detail')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
  ADD COLUMN `discount_money` varchar(100)
  ;');            
}
if (!$CI->db->field_exists('available_quantity', 'goods_delivery_detail')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
  ADD COLUMN `available_quantity` varchar(100)
  ;');            
}

if (!$CI->db->field_exists('purchase_price', 'goods_transaction_detail')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_transaction_detail` 
  ADD COLUMN `purchase_price` varchar(100)
  ;');            
}
if (!$CI->db->field_exists('price', 'goods_transaction_detail')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_transaction_detail` 
  ADD COLUMN `price` varchar(100)
  ;');            
}

if (!$CI->db->field_exists('total_discount', 'goods_delivery')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery` 
  ADD COLUMN `total_discount` varchar(100)
  ;');            
}
if (!$CI->db->field_exists('after_discount', 'goods_delivery')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery` 
  ADD COLUMN `after_discount` varchar(100)
  ;');            
}

// Version 1.0.2
if (!$CI->db->field_exists('tax_id', 'goods_delivery_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
    ADD COLUMN `tax_id` varchar(100)
    ;');            
}
if (!$CI->db->field_exists('total_after_discount', 'goods_delivery_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
    ADD COLUMN `total_after_discount` varchar(100)
    ;');            
}

// Version 1.0.3
if (!$CI->db->field_exists('invoice_id', 'goods_delivery')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery` 
    ADD COLUMN `invoice_id` varchar(100)
    ;');            
}

if (!$CI->db->field_exists('lot_number', 'goods_receipt_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_receipt_detail` 
    ADD COLUMN `lot_number` varchar(100)
    ;');            
}

if (!$CI->db->field_exists('lot_number', 'inventory_manage')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'inventory_manage` 
    ADD COLUMN `lot_number` varchar(100)
    ;');            
}

// Version 1.0.4
if (!$CI->db->field_exists('expiry_date', 'goods_delivery_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
    ADD COLUMN `expiry_date` text  NULL ,
    ADD COLUMN `lot_number` text NULL
    ;');            
}

if (!$CI->db->field_exists('expiry_date', 'goods_transaction_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_transaction_detail` 
    ADD COLUMN `expiry_date` text NULL ,
    ADD COLUMN `lot_number` text NULL
    ;');            
}

// Version 1.0.5
if (!$CI->db->field_exists('guarantee', 'items')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'items` 
    ADD COLUMN `guarantee` text  NULL 
    
    ;');            
}

if (!$CI->db->field_exists('guarantee_period', 'goods_delivery_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'goods_delivery_detail` 
    ADD COLUMN `guarantee_period` text  NULL 
    
    ;');            
}

if (!$CI->db->field_exists('expiry_date', 'wh_loss_adjustment_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'wh_loss_adjustment_detail` 
    ADD COLUMN `expiry_date` text NULL ,
    ADD COLUMN `lot_number` text NULL
    ;');            
}
//version 1.0.7
if (!$CI->db->field_exists('group_id' ,db_prefix() . 'wh_sub_group')) { 
    $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_sub_group`
        ADD COLUMN `group_id` int(11)  NULL
    ;");
  } 

//versio 1.0.8 
//update Set the selling price rule according to profit ratio
  
  add_option('warehouse_selling_price_rule_profif_ratio', 0, 1);


  if (!$CI->db->field_exists('profif_ratio' ,db_prefix() . 'items')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
          ADD COLUMN `profif_ratio` text  NULL
      ;");
  } 

  /*value 0 purchase price, 1 selling price*/
  
  add_option('profit_rate_by_purchase_price_sale', 0, 1);
  add_option('warehouse_the_fractional_part', 0, 1);
  add_option('warehouse_integer_part', 0, 1);

  //version 1.0.9
  //update auto create goods received note when create purchase order ( approval)
  //update auto create goods delivery note when create invoices ( paid)

  add_option('auto_create_goods_received', 0, 1);
  add_option('auto_create_goods_delivery', 0, 1);
  add_option('goods_receipt_warehouse', 0, 1);


  if ($CI->db->field_exists('warehouse_id' ,db_prefix() . 'goods_transaction_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_transaction_detail`
          CHANGE COLUMN `warehouse_id` `warehouse_id` TEXT NOT NULL ,
          DROP PRIMARY KEY,
          ADD PRIMARY KEY (`id`, `commodity_id`)
      ;");
  }

  if (!$CI->db->field_exists('active' ,db_prefix() . 'items')) { 
    $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
        ADD COLUMN `active` INT(11) NULL DEFAULT 1
    ;");
  }

  add_option('barcode_with_sku_code', 0, 1);
    
  if (!$CI->db->field_exists('long_descriptions' ,db_prefix() . 'items')) { 
    $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
        ADD COLUMN `long_descriptions` LONGTEXT NULL
    ;");
  }

  add_option('revert_goods_receipt_goods_delivery', 0, 1);

    if (!$CI->db->field_exists('without_checking_warehouse' ,db_prefix() . 'items')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
          ADD COLUMN `without_checking_warehouse` int(11) NULL default 0
      ;");
    }  

    //version 1.1.2
  add_option('cancelled_invoice_reverse_inventory_delivery_voucher', 0, 1);
  add_option('uncancelled_invoice_create_inventory_delivery_voucher', 0, 1);
  add_option('inventory_auto_operations_hour', 0, 1);
  add_option('automatically_send_items_expired_before', 0, 1);
  add_option('inventorys_cronjob_active', 0, 1);
  add_option('inventory_cronjob_notification_recipients', '', 1);

  create_email_template('Inventory warning', 'Hi {staff_name}! <br /><br />This is a inventory warning<br />{<span 12pt="">notification_content</span>}. <br /><br />Regards.', 'inventory_warning', 'Inventory warning (Sent to staff)', 'inventory-warning-to-staff');

  if (get_status_modules_wh('purchase')) {
    if (!$CI->db->field_exists('wh_quantity_received' ,db_prefix() . 'pur_order_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
          ADD COLUMN `wh_quantity_received` varchar(200)  NULL
      ;");
    }
  }

  //update inventory setting prefix

  add_option('inventory_received_number_prefix', 'NK', 1);
  add_option('next_inventory_received_mumber', 1, 1);
  add_option('inventory_delivery_number_prefix', 'XK', 1);
  add_option('next_inventory_delivery_mumber', 1, 1);

    //add internal delivery note, function
    if (!$CI->db->table_exists(db_prefix() . 'internal_delivery_note')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . "internal_delivery_note` (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

          `internal_delivery_name` text NULL ,
          `description` text NULL ,
          `staff_id` int(11) NULL ,
          `date_c` date NULL ,
          `date_add` date NULL,
          `internal_delivery_code` varchar(100) NULL ,
          `approval` INT(11) NULL DEFAULT 0 COMMENT 'status approval ',
          `addedfrom` INT(11) null,
          `total_amount` decimal(15,2) null ,
          `datecreated` datetime null ,

          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
    }

    if (!$CI->db->table_exists(db_prefix() . 'internal_delivery_note_detail')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . "internal_delivery_note_detail` (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `internal_delivery_id` int(11) NOT NULL,
          `commodity_code` varchar(100) NULL,
          `from_stock_name` text NULL,
          `to_stock_name` text NULL,
          `unit_id` text NULL,
          `available_quantity` text NULL,
          `quantities` text NULL,
          `unit_price` varchar(100) NULL,
          `into_money` varchar(100) NULL,
          `note` text NULL ,

          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
    }

    add_option('internal_delivery_number_prefix', 'ID', 1);
    add_option('next_internal_delivery_mumber', 1, 1);


    if (!$CI->db->field_exists('from_stock_name' ,db_prefix() . 'goods_transaction_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_transaction_detail`
          ADD COLUMN `from_stock_name` int(11),
          ADD COLUMN `to_stock_name` int(11)
      ;");
    }

    add_option('item_sku_prefix', '', 1);

          //current version on eoffice 1.1.3
      //maximum stock
      if (!$CI->db->field_exists('inventory_number_max' ,db_prefix() . 'inventory_commodity_min')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "inventory_commodity_min`
              ADD COLUMN `inventory_number_max` varchar(100) NULL default 0
          ;");
        }

      //Goods receipt

      if (!$CI->db->field_exists('project' ,db_prefix() . 'goods_receipt')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt`
            ADD COLUMN `project` TEXT  NULL
        ;");
      }
      if (!$CI->db->field_exists('type' ,db_prefix() . 'goods_receipt')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt`
            ADD COLUMN `type` TEXT  NULL
        ;");
      }

      if (!$CI->db->field_exists('department' ,db_prefix() . 'goods_receipt')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt`
            ADD COLUMN `department` int(11)  NULL
        ;");
      }

      if (!$CI->db->field_exists('requester' ,db_prefix() . 'goods_receipt')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt`
            ADD COLUMN `requester` int(11)  NULL
        ;");
      }

      if (!$CI->db->field_exists('expiry_date' ,db_prefix() . 'goods_receipt')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt`
            ADD COLUMN `expiry_date` DATE NULL
        ;");
      }
      if (!$CI->db->field_exists('invoice_no' ,db_prefix() . 'goods_receipt')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt`
            ADD COLUMN `invoice_no` text NULL
        ;");
      }

      /*Required PO selected  when create goods received voucher*/

    add_option('goods_receipt_required_po', 0, 1);
    add_option('goods_delivery_required_po', 0, 1);

      //Goods delivery
      if (!$CI->db->field_exists('project' ,db_prefix() . 'goods_delivery')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
            ADD COLUMN `project` TEXT  NULL
        ;");
      }
      if (!$CI->db->field_exists('type' ,db_prefix() . 'goods_delivery')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
            ADD COLUMN `type` TEXT  NULL
        ;");
      }

      if (!$CI->db->field_exists('department' ,db_prefix() . 'goods_delivery')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
            ADD COLUMN `department` int(11)  NULL
        ;");
      }

      if (!$CI->db->field_exists('requester' ,db_prefix() . 'goods_delivery')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
            ADD COLUMN `requester` int(11)  NULL
        ;");
      }

      if (!$CI->db->field_exists('invoice_no' ,db_prefix() . 'goods_delivery')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
            ADD COLUMN `invoice_no` text NULL
        ;");
      }

      //goods delivery invoice
      if (!$CI->db->table_exists(db_prefix() . 'goods_delivery_invoices_pr_orders')) {
          $CI->db->query('CREATE TABLE `' . db_prefix() . "goods_delivery_invoices_pr_orders` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `rel_id` int(11) NULL COMMENT  'goods_delivery_id',
            `rel_type` int(11) NULL COMMENT 'invoice_id or purchase order id',

            `type` varchar(100) NULL COMMENT'invoice,  purchase_orders',

            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
      }

      if (!$CI->db->field_exists('pr_order_id' ,db_prefix() . 'goods_delivery')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
            ADD COLUMN `pr_order_id` int(11) NULL
        ;");
      }

      //add vat column in lead
      if (!$CI->db->field_exists('vat' ,db_prefix() . 'leads')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "leads`
            ADD COLUMN `vat` varchar(50) NULL
        ;");
      }

      //table brand
      if (!$CI->db->table_exists(db_prefix() . 'wh_brand')) {
          $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_brand` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` text NULL ,

            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
      }

      //table model
      if (!$CI->db->table_exists(db_prefix() . 'wh_model')) {
          $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_model` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` text NULL ,
            `brand_id` int(11) NOT NULL,

            PRIMARY KEY (`id`,`brand_id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
      }


      //table series
      if (!$CI->db->table_exists(db_prefix() . 'wh_series')) {
          $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_series` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` text NULL ,
            `model_id` int(11) NOT NULL,

            PRIMARY KEY (`id`,`model_id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
      }

      if (!$CI->db->field_exists('series_id' ,db_prefix() . 'items')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
            ADD COLUMN `series_id` TEXT  NULL
        ;");
      }
      
      if (!$CI->db->field_exists('processing' ,db_prefix() . 'proposals')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "proposals`
            ADD COLUMN `processing` TEXT  NULL
        ;");
      }

      //warehouse custom fields
      if (!$CI->db->table_exists(db_prefix() . 'wh_custom_fields')) {
          $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_custom_fields` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `custom_fields_id` int NULL ,
            `warehouse_id` text NULL,

            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
      }
      
      //version_116
    add_option('goods_delivery_pdf_display', 0, 1);


      if (!$CI->db->field_exists('city' ,db_prefix() . 'warehouse')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "warehouse`
            ADD COLUMN `city` TEXT  NULL
        ;");
      }

      if (!$CI->db->field_exists('state' ,db_prefix() . 'warehouse')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "warehouse`
            ADD COLUMN `state` TEXT  NULL
        ;");
      }

      if (!$CI->db->field_exists('zip_code' ,db_prefix() . 'warehouse')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "warehouse`
            ADD COLUMN `zip_code` TEXT  NULL
        ;");
      }

      if (!$CI->db->field_exists('country' ,db_prefix() . 'warehouse')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "warehouse`
            ADD COLUMN `country` TEXT  NULL
        ;");
      }
      

      //new variation
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
      
          //version_118x

    add_option('display_product_name_when_print_barcode', 0, 1);
    add_option('show_item_cf_on_pdf', 0, 1);


      //version_118x add activity log for Delivery note
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
      //version_118x add create inventory delivery was partial from invoice, add column
      if (!$CI->db->field_exists('wh_delivered_quantity' ,db_prefix() . 'itemable')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "itemable`
          ADD COLUMN `wh_delivered_quantity` DECIMAL(15,2)  DEFAULT '0'
          ;");
      }

      if (!$CI->db->field_exists('type_of_delivery' ,db_prefix() . 'goods_delivery')) { 
        $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
          ADD COLUMN `type_of_delivery` VARCHAR(100)  NULL DEFAULT 'total'
          ;");
      }


// Version 122 update tax
if (!$CI->db->field_exists('tax_rate' ,db_prefix() . 'goods_receipt_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt_detail`
      ADD COLUMN `tax_rate` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('sub_total' ,db_prefix() . 'goods_receipt_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt_detail`
      ADD COLUMN `sub_total` DECIMAL(15,2) NULL DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('tax_name' ,db_prefix() . 'goods_receipt_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_receipt_detail`
      ADD COLUMN `tax_name` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('commodity_name' ,db_prefix() . 'internal_delivery_note_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "internal_delivery_note_detail`
      ADD COLUMN `commodity_name` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('commodity_name' ,db_prefix() . 'wh_loss_adjustment_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_loss_adjustment_detail`
      ADD COLUMN `commodity_name` TEXT NULL
  ;");
}
if (!$CI->db->field_exists('tax_rate' ,db_prefix() . 'goods_delivery_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery_detail`
      ADD COLUMN `tax_rate` TEXT NULL,
      ADD COLUMN `tax_name` TEXT NULL,
      ADD COLUMN `sub_total` DECIMAL(15,2) NULL DEFAULT '0'
  ;");
}
if (!$CI->db->field_exists('additional_discount' ,db_prefix() . 'goods_delivery')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
      ADD COLUMN `additional_discount` DECIMAL(15,2) NULL DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('sub_total' ,db_prefix() . 'goods_delivery')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
      ADD COLUMN `sub_total` DECIMAL(15,2) NULL DEFAULT '0'
  ;");
}

// Version 123
add_option('goods_delivery_pdf_display_outstanding', 0, 1);
add_option('goods_delivery_pdf_display_warehouse_lotnumber_bottom_infor', 0, 1);

//Version 124
  add_option('packing_list_number_prefix', 'PL', 1);
  add_option('next_packing_list_number', 1, 1);

if (!$CI->db->table_exists(db_prefix() . 'wh_packing_lists')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_packing_lists` (

    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `delivery_note_id` INT(11) NULL,
    `packing_list_number` VARCHAR(100) NULL,
    `packing_list_name` VARCHAR(200) NULL,
    `width` DECIMAL(15,2) NULL DEFAULT '0.00',
    `height` DECIMAL(15,2) NULL DEFAULT '0.00',
    `lenght` DECIMAL(15,2) NULL DEFAULT '0.00',
    `weight` DECIMAL(15,2) NULL DEFAULT '0.00',
    `volume` DECIMAL(15,2) NULL DEFAULT '0.00',
    `clientid` INT(11) NULL,
    `subtotal` DECIMAL(15,2) NULL DEFAULT '0.00',
    `total_amount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
    `additional_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `billing_street` varchar(200) DEFAULT NULL,
    `billing_city` varchar(100) DEFAULT NULL,
    `billing_state` varchar(100) DEFAULT NULL,
    `billing_zip` varchar(100) DEFAULT NULL,
    `billing_country` int(11) DEFAULT NULL,
    `shipping_street` varchar(200) DEFAULT NULL,
    `shipping_city` varchar(100) DEFAULT NULL,
    `shipping_state` varchar(100) DEFAULT NULL,
    `shipping_zip` varchar(100) DEFAULT NULL,
    `shipping_country` int(11) DEFAULT NULL,
    `client_note` TEXT NULL,
    `admin_note` TEXT NULL,
    `approval` INT(11) NULL DEFAULT 0,
    `datecreated` DATETIME NULL,
    `staff_id` INT(11) NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'wh_packing_list_details')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_packing_list_details` (

    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `packing_list_id` INT(11) NOT NULL,
    `delivery_detail_id` INT(11) NULL,
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

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('packing_qty' ,db_prefix() . 'goods_delivery_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery_detail`
      ADD COLUMN `packing_qty` DECIMAL(15,2) NULL DEFAULT '0.00'
  ;");
}

if (!$CI->db->field_exists('type_of_packing_list' ,db_prefix() . 'wh_packing_lists')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_packing_lists`
      ADD COLUMN `type_of_packing_list` VARCHAR(100)  NULL DEFAULT 'total'
  ;");
}

if (!$CI->db->field_exists('delivery_status' ,db_prefix() . 'wh_packing_lists')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_packing_lists`
      ADD COLUMN `delivery_status` VARCHAR(100)  NULL DEFAULT 'wh_ready_to_deliver'
  ;");
}

if (!$CI->db->field_exists('delivery_status' ,db_prefix() . 'goods_delivery')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "goods_delivery`
      ADD COLUMN `delivery_status` VARCHAR(100)  NULL DEFAULT 'ready_for_packing'
  ;");
}

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

//add shipment on Omnisales module
if (!$CI->db->table_exists(db_prefix() . 'wh_omni_shipments')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_omni_shipments` (

    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `cart_id` INT(11) NULL,
    `shipment_number` VARCHAR(100) NULL,
    `planned_shipping_date` DATETIME NULL,
    `shipment_status` VARCHAR(50) NULL,
    `datecreated` DATETIME NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// Order returns
// return request must be placed within X days after the delivery date
  add_option('wh_return_request_within_x_day', 30, 1);
  add_option('wh_fee_for_return_order', 0, 1);
  add_option('wh_return_policies_information', '', 1);
  add_option('wh_refund_loyaty_point', '1', 1);
  add_option('order_return_number_prefix', 'ReReturn', 1);
  add_option('next_order_return_number', 1, 1);
  add_option('e_order_return_number_prefix', 'DEReturn', 1);
  add_option('e_next_order_return_number', 1, 1);

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
    `receipt_delivery_id` INT(1) NULL DEFAULT 0,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('order_return_name' ,db_prefix() . 'wh_order_returns')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
  ADD COLUMN `order_return_name` VARCHAR(500) NULL
  ;");
}

if (!$CI->db->field_exists('company_id' ,db_prefix() . 'wh_order_returns')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
  ADD COLUMN `company_id` INT(11) NULL 
  ;");
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

if (!$CI->db->field_exists('currency' ,db_prefix() . 'wh_order_returns')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
  ADD COLUMN `currency` INT(11) NULL 
  ;");
}

if (!$CI->db->field_exists('receipt_delivery_id' ,db_prefix() . 'wh_order_returns')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_returns`
  ADD COLUMN `receipt_delivery_id` INT(1) NULL  DEFAULT '0'
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

add_option('warehouse_receive_return_order ', 0, 1);
if (!$CI->db->field_exists('return_reason' ,db_prefix() . 'wh_order_returns')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_order_returns`
  ADD COLUMN `return_reason` longtext NULL
  ');
}

// inventory_receipt_voucher_returned_goods
// inventory_delivery_voucher_returned_purchasing_goods
if (!$CI->db->field_exists('receipt_delivery_type' ,db_prefix() . 'wh_order_returns')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_order_returns`
  ADD COLUMN `receipt_delivery_type` VARCHAR(100) NULL
  ');
}

if ($CI->db->field_exists('discount_total' ,db_prefix() . 'wh_packing_lists')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_packing_lists`
  CHANGE COLUMN `discount_total` `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
  CHANGE COLUMN `additional_discount` `additional_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
  CHANGE COLUMN `total_after_discount` `total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00'
   ;");
}
if ($CI->db->field_exists('discount' ,db_prefix() . 'wh_packing_list_details')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_packing_list_details`
  CHANGE COLUMN `discount` `discount` DECIMAL(15,2) NULL DEFAULT '0.00',
  CHANGE COLUMN `discount_total` `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
  CHANGE COLUMN `total_after_discount` `total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00'
   ;");
}

//serial numbers
if (!$CI->db->table_exists(db_prefix() . 'wh_inventory_serial_numbers')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_inventory_serial_numbers` (

    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `commodity_id` INT(11) NOT NULL,
    `warehouse_id` INT(11) NULL,
    `inventory_manage_id` INT(11) NULL,
    `serial_number` VARCHAR(255) NULL,
    `is_used` VARCHAR(20) NULL DEFAULT 'no',

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'goods_receipt_detail')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'goods_receipt_detail`
  ADD COLUMN `serial_number` VARCHAR(255) NULL
  ');
}
if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'goods_delivery_detail')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'goods_delivery_detail`
  ADD COLUMN `serial_number` VARCHAR(255) NULL
  ');
}

if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'internal_delivery_note_detail')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'internal_delivery_note_detail`
  ADD COLUMN `serial_number` VARCHAR(255) NULL
  ');
}
if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'wh_loss_adjustment_detail')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_loss_adjustment_detail`
  ADD COLUMN `serial_number` VARCHAR(255) NULL
  ');
}
if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'wh_packing_list_details')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_packing_list_details`
  ADD COLUMN `serial_number` VARCHAR(255) NULL
  ');
}
if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'goods_transaction_detail')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'goods_transaction_detail`
  ADD COLUMN `serial_number` VARCHAR(255) NULL
  ');
}

if (!$CI->db->field_exists('purchase_price' ,db_prefix() . 'inventory_manage')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'inventory_manage`
  ADD COLUMN `purchase_price` DECIMAL(15,2) NULL DEFAULT "0.00"
  ');
}

//Omni_sale add shipping fee on sales order => delivery note -  add shipping_fee
if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'goods_delivery')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'goods_delivery`
  ADD COLUMN `shipping_fee` DECIMAL(15,2) NULL DEFAULT "0.00"
  ');
}
if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'wh_packing_lists')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_packing_lists`
  ADD COLUMN `shipping_fee` DECIMAL(15,2) NULL DEFAULT "0.00"
  ');
}
if (!$CI->db->field_exists('goods_delivery_id' ,db_prefix() . 'wh_omni_shipments')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_omni_shipments`
  ADD COLUMN `goods_delivery_id` INT(11) NULL
  ');
}

if (!$CI->db->field_exists('shipment_hash' ,db_prefix() . 'wh_omni_shipments')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_omni_shipments`
  ADD COLUMN `shipment_hash` VARCHAR(32) NULL
  ');
}
add_option('wh_display_shipment_on_client_portal', 1, 1);
add_option('wh_on_total_items', 200, 1);
add_option('wh_products_by_serial', 1, 1);

/*for sales agent module*/
if (!$CI->db->field_exists('order_id' ,db_prefix() . 'wh_omni_shipments')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_omni_shipments`
  ADD COLUMN `order_id` INT(11) NULL DEFAULT '0'
  ;");
}


if (!$CI->db->field_exists('hide_warehouse_when_out_of_stock' ,db_prefix() . 'warehouse')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "warehouse`
  ADD COLUMN `hide_warehouse_when_out_of_stock` INT(11) NULL DEFAULT '0' COMMENT  ' 1: yes  0: no'
  ;");
}

  add_option('wh_shortened_form_pdf', 0, 1);
  add_option('wh_show_price_when_print_barcode', 1, 1);

if (!$CI->db->table_exists(db_prefix() . 'wh_staff_warehouses')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_staff_warehouses` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `staff_id` INT(11) NULL,
      `warehouse_id` INT(11) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

add_option('notify_customer_when_change_delivery_status', 1, 1);
add_option('wh_hide_shipping_fee', 0, 1);

add_option('lot_number_prefix', 'LOT', 1);
add_option('next_lot_number', 1, 1);
add_option('auto_generate_lotnumber', 0, 1);
add_option('custom_name_for_meter', 'm', 1);
add_option('custom_name_for_kg', 'kg', 1);
add_option('custom_name_for_m3', 'm3', 1);

add_option('packing_list_pdf_display_rate', 1, 1);
add_option('packing_list_pdf_display_tax', 1, 1);
add_option('packing_list_pdf_display_subtotal', 1, 1);
add_option('packing_list_pdf_display_discount_percent', 1, 1);
add_option('packing_list_pdf_display_discount_amount', 1, 1);
add_option('packing_list_pdf_display_totalpayment', 1, 1);
add_option('packing_list_pdf_display_summary', 1, 1);

if (!$CI->db->field_exists('purchase_price' ,db_prefix() . 'wh_order_return_details')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_order_return_details`
      ADD COLUMN `purchase_price` decimal(15,2)  DEFAULT '0.00'
  ;");
}
