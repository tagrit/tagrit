<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_116 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        //Ver 1.1.6
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
    }
}