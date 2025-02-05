<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_149 extends App_module_migration
{
    public function up()
    {
    	$CI = &get_instance();

    	if ($CI->db->field_exists('department' ,db_prefix() . 'pur_request')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
		  CHANGE COLUMN `department` `department` INT(11) NULL DEFAULT NULL;");
		}
    }

}