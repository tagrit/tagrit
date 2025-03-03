<?php
defined('BASEPATH') or exit('No direct script access allowed');


if (!$CI->db->table_exists(db_prefix() . 'mrp_work_centers')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_work_centers` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

    `work_center_name` varchar(200) NULL,
    `work_center_code` varchar(200) NULL,
    `working_hours` varchar(200) NULL,
    `time_efficiency` DECIMAL(15,2)  DEFAULT '0',
    `capacity` DECIMAL(15,2)  DEFAULT '0',
    `oee_target` DECIMAL(15,2)  DEFAULT '0',
    `time_start` DECIMAL(15,2)  DEFAULT '0',
    `time_stop` DECIMAL(15,2)  DEFAULT '0',
    `costs_hour` DECIMAL(15,2)  DEFAULT '0',
    `description` TEXT DEFAULT NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'mrp_routings')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_routings` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

    `routing_code` varchar(200) NULL,
    `routing_name` varchar(200) NULL,
    `description` TEXT DEFAULT NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


//get work sheet file with rel type mrp_work_sheet
if (!$CI->db->table_exists(db_prefix() . 'mrp_routing_details')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_routing_details` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `routing_id` int(11) NOT NULL ,
    `operation` VARCHAR(200) NULL ,
    `work_center_id` INT(11) NULL ,
    `duration_computation` VARCHAR(200) NULL ,
    `based_on` DECIMAL(15,2) DEFAULT '0',
    `default_duration` DECIMAL(15,2) DEFAULT '0',

    `start_next_operation` VARCHAR(200) NULL ,
    `quantity_process` DECIMAL(15,2) DEFAULT '0',

    `description` TEXT DEFAULT NULL,
    `display_order` DECIMAL(15,2) DEFAULT '0',

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->table_exists(db_prefix() . 'mrp_working_hours')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_working_hours` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `working_hour_name` VARCHAR(200) NULL ,
    `hours_per_day` DECIMAL(15,2) DEFAULT '0',

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'mrp_working_hour_times')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_working_hour_times` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `working_hour_id` int(11) NOT NULL ,
    `working_hour_name` VARCHAR(200) NULL ,
    `day_of_week` VARCHAR(100) NULL,
    `day_period` VARCHAR(100) NULL,
    `work_from` TIME NULL,
    `work_to` TIME NULL,
    `starting_date` DATE NULL,
    `end_date` DATE NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'mrp_working_hour_time_off')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_working_hour_time_off` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `working_hour_id` int(11) NOT NULL ,
    `reason` VARCHAR(200) NULL ,
    `starting_date` DATE NULL,
    `end_date` DATE NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


/**
 * create Unit category
 * Note: this module use table ware_unit_type with Inventory module, and new column
 */

if (!$CI->db->table_exists(db_prefix() . 'mrp_unit_measure_categories')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_unit_measure_categories` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

    `category_name` VARCHAR(200) NOT NULL ,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

/**
 * unit name => Unit of measure
 * display => Active 
 *
 * when create unit type: take "unit code" and "unit symbol" = unit name remove "space", order = 1
 */
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

//add this script if itegration with Inventory module
if (!$CI->db->field_exists('category_id' ,db_prefix() . 'ware_unit_type')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "ware_unit_type`
  ADD COLUMN `category_id` int(11) NULL ,
  ADD COLUMN `unit_measure_type` VARCHAR(100) DEFAULT 'reference' ,
  ADD COLUMN `bigger_ratio` DECIMAL(15,5) DEFAULT '0' ,
  ADD COLUMN `smaller_ratio` DECIMAL(15,5) DEFAULT '0' ,
  ADD COLUMN `rounding` DECIMAL(15,5) DEFAULT '0'

  ;");
}

if (!$CI->db->field_exists('product_type' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `product_type` VARCHAR(100) NULL

  ;");
}

if (!$CI->db->field_exists('description_internal_transfers' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `description_internal_transfers` TEXT NULL 
 ;");
}
if (!$CI->db->field_exists('description_receipts' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `description_receipts` TEXT NULL
 ;");
}

if (!$CI->db->field_exists('description_delivery_orders' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `description_delivery_orders` TEXT NULL
 ;");
}

if (!$CI->db->field_exists('customer_lead_time' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `customer_lead_time` DECIMAL(15,2) NULL DEFAULT '0'
 ;");
}

if (!$CI->db->field_exists('replenish_on_order' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `replenish_on_order` VARCHAR(100) NULL
 ;");
}

if (!$CI->db->field_exists('supplier_taxes_id' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `supplier_taxes_id` TEXT NULL
 ;");
}

if (!$CI->db->field_exists('description_sale' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `description_sale` TEXT NULL
 ;");
}

if (!$CI->db->field_exists('invoice_policy' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `invoice_policy` VARCHAR(100) NULL DEFAULT 'ordered_quantities'
 ;");
}
if (!$CI->db->field_exists('purchase_unit_measure' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `purchase_unit_measure` INT(11) NULL 
 ;");
}

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
if (!$CI->db->field_exists('manufacture' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `manufacture` VARCHAR(100) NULL
 ;");
}
if (!$CI->db->field_exists('manufacturing_lead_time' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `manufacturing_lead_time` DECIMAL(15,2) NULL DEFAULT '0' 
 ;");
}
if (!$CI->db->field_exists('weight' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `weight` DECIMAL(15,2) NULL DEFAULT '0' 
 ;");
}
if (!$CI->db->field_exists('volume' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `volume` DECIMAL(15,2) NULL DEFAULT '0'
 ;");
}
if (!$CI->db->field_exists('hs_code' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `hs_code` VARCHAR(200) NULL
 ;");
}


//TODO
//Handle add some table or column use with Inventory module


//BOM
if (!$CI->db->table_exists(db_prefix() . 'mrp_bill_of_materials')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_bill_of_materials` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

    `bom_code` VARCHAR(100) NULL,
    `product_id` int(11) NULL,
    `product_variant_id` int(11) NULL,
    `product_qty` DECIMAL(15,2) DEFAULT '0',
    `unit_id` INT(11) NULL,
    `routing_id` INT(11) NULL,
    `bom_type` VARCHAR(100) NULL,
    `ready_to_produce` VARCHAR(200) NULL,
    `consumption` VARCHAR(200) NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->table_exists(db_prefix() . 'mrp_bill_of_material_details')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_bill_of_material_details` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `bill_of_material_id` int(11) NOT NULL ,

    `product_id` int(11) NULL COMMENT  'Only Product variant do not get parent Product',
    `product_qty` DECIMAL(15,2) DEFAULT '0',
    `unit_id` INT(11) NULL,
    `apply_on_variants` TEXT NULL,
    `operation_id` INT(11) NULL,
    `display_order` DECIMAL(15,2) DEFAULT '0',
    
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

//manufacturing order
//status: draft,confirmed,planned,cancelled,in_progress,done
if (!$CI->db->table_exists(db_prefix() . 'mrp_manufacturing_orders')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_manufacturing_orders` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

    `manufacturing_order_code` VARCHAR(100) NULL,
    `product_id` int(11) NULL COMMENT  'Only Product variant do not get parent Product',
    `product_qty` DECIMAL(15,2) DEFAULT '0',
    `unit_id` INT(11) NULL,
    `bom_id` INT(11) NULL,
    `routing_id` INT(11) NULL,
    `date_deadline` DATETIME NULL,
    `date_plan_from` DATETIME NULL,
    `date_planned_start` DATETIME NULL ,
    `date_planned_finished` DATETIME NULL ,

    `status` VARCHAR(100) NULL DEFAULT 'draft',
    `material_availability_status` VARCHAR(100) NULL,
    `staff_id` INT(11) NULL,
    `components_warehouse_id` TEXT NULL,
    `finished_products_warehouse_id` TEXT NULL,
    `purchase_request_id` INT(11) NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->table_exists(db_prefix() . 'mrp_manufacturing_order_details')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_manufacturing_order_details` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `manufacturing_order_id` int(11) NOT NULL ,

    `product_id` int(11) NULL,
    `unit_id` INT(11) NULL,
    `qty_to_consume` DECIMAL(15,2) DEFAULT '0',
    `qty_reserved` DECIMAL(15,2) DEFAULT '0',
    `qty_done` DECIMAL(15,2) DEFAULT '0',
    `check_inventory_qty` VARCHAR(10) NULL,
    `warehouse_id` TEXT NULL,
    `lot_number` TEXT NULL,
    `expiry_date` TEXT NULL,
    `available_quantity` DECIMAL(15,2) DEFAULT '0',

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

//status: waiting for another WO, Ready, in Progress, Finished
if (!$CI->db->table_exists(db_prefix() . 'mrp_work_orders')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_work_orders` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `manufacturing_order_id` int(11) NOT NULL ,

    `product_id` int(11) NULL ,
    `qty_produced` DECIMAL(15,2) DEFAULT '0',
    `qty_production` DECIMAL(15,2) DEFAULT '0',
    `qty_producing` DECIMAL(15,2) DEFAULT '0',
    `unit_id` INT(11) NULL,
    `routing_detail_id` INT(11) NULL,
    `operation_name` TEXT NULL,
    `work_center_id` INT(11) NULL,

    `date_planned_start` DATETIME NULL ,
    `date_planned_finished` DATETIME NULL ,
    `date_start` DATETIME NULL ,
    `date_finished` DATETIME NULL ,
    `duration_expected` DECIMAL(15,2) DEFAULT '0',
    `real_duration` DECIMAL(15,2) DEFAULT '0',
    `status` VARCHAR(100) NULL ,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'mrp_work_order_details')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_work_order_details` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `work_order_id` int(11) NOT NULL ,

    `product_id` int(11) NULL ,
    `to_consume` DECIMAL(15,2) DEFAULT '0',
    `reserved` DECIMAL(15,2) DEFAULT '0',

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->table_exists(db_prefix() . 'mrp_work_order_time_trackings')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_work_order_time_trackings` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `work_order_id` int(11) NOT NULL ,

    `from_date` DATETIME NULL ,
    `to_date` DATETIME NULL ,
    `duration` DECIMAL(15,2) DEFAULT '0',
    `staff_id` INT(11) NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'mrp_option')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_option` (
    `option_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `option_name` varchar(200) NOT NULL,
    `option_val` longtext NULL,
    `auto` tinyint(1) NULL,
    PRIMARY KEY (`option_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

//general settings
  if (mrp_row_options_exists('"bom_prefix"') == 0){
      $CI->db->query('INSERT INTO `'.db_prefix().'mrp_option` (`option_name`,`option_val`, `auto`) VALUES ("bom_prefix", "#BOM_", "1");
    ');
  }

  if (mrp_row_options_exists('"bom_number"') == 0){
      $CI->db->query('INSERT INTO `'.db_prefix().'mrp_option` (`option_name`,`option_val`, `auto`) VALUES ("bom_number", "1", "1");
    ');
  }

  if (mrp_row_options_exists('"routing_prefix"') == 0){
      $CI->db->query('INSERT INTO `'.db_prefix().'mrp_option` (`option_name`,`option_val`, `auto`) VALUES ("routing_prefix", "#RO_", "1");
    ');
  }

  if (mrp_row_options_exists('"routing_number"') == 0){
      $CI->db->query('INSERT INTO `'.db_prefix().'mrp_option` (`option_name`,`option_val`, `auto`) VALUES ("routing_number", "1", "1");
    ');
  }

  if (mrp_row_options_exists('"mo_prefix"') == 0){
      $CI->db->query('INSERT INTO `'.db_prefix().'mrp_option` (`option_name`,`option_val`, `auto`) VALUES ("mo_prefix", "#MO_", "1");
    ');
  }

  if (mrp_row_options_exists('"mo_number"') == 0){
      $CI->db->query('INSERT INTO `'.db_prefix().'mrp_option` (`option_name`,`option_val`, `auto`) VALUES ("mo_number", "1", "1");
    ');
  }

  if (mrp_row_options_exists('"cost_hour"') == 0){
      $CI->db->query('INSERT INTO `'.db_prefix().'mrp_option` (`option_name`,`option_val`, `auto`) VALUES ("cost_hour", "0", "1");
    ');
  }

  // Version 1.0.5
  if (!$CI->db->table_exists(db_prefix() . 'mrp_bom_changes_logs')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "mrp_bom_changes_logs` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `manufacturing_order_id` INT(11) NULL,
      `parent_product_id` INT(11) NULL,
      `product_id` INT(11) NULL,
      `unit_id` INT(11) NULL DEFAULT '0',
      `change_type` TEXT NULL,
      `change_quantity` DECIMAL(15,2) DEFAULT '0.00',
      `created_at` DATETIME,
      `staff_id` INT(11) NULL DEFAULT '0',
      `description` TEXT NULL,
      `rel_id` INT(11) NULL,
      `rel_type` TEXT NULL COMMENT 'receipt_note, delivery_note',

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
  }

  if (!$CI->db->field_exists('check_availability' ,db_prefix() . 'mrp_bom_changes_logs')) { 
    $CI->db->query('ALTER TABLE `' . db_prefix() . "mrp_bom_changes_logs`
      ADD COLUMN `check_availability` INT(11) NULL DEFAULT '0'

      ;");
  }