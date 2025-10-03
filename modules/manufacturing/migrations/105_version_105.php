<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_105 extends App_module_migration
{
     public function up()
     {
            $CI = &get_instance();
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
     }
}
