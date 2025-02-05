<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_115 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

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
    }
}