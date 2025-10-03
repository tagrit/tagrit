<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_111 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

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

    }
}