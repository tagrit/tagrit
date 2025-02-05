<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_137 extends App_module_migration
{
    public function up()
    {
    	$CI = &get_instance();

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
    }

}