<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_131 extends App_module_migration
{
    public function up()
    {
       	$CI = &get_instance();
       	if (!$CI->db->field_exists('vendor_invoice_number' ,db_prefix() . 'pur_invoices')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
		      ADD COLUMN `vendor_invoice_number` TEXT NULL
		  ;");
		}
    }
}