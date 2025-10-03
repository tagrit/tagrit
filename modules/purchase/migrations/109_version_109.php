<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_109 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        if (row_purchase_options_exist('"terms_and_conditions"') == 0){
		  $CI->db->query('INSERT INTO `'.db_prefix().'purchase_option` (`option_name`, `option_val`, `auto`) VALUES ("terms_and_conditions", "", "1");
		');
		}

		if (row_purchase_options_exist('"vendor_note"') == 0){
		  $CI->db->query('INSERT INTO `'.db_prefix().'purchase_option` (`option_name`, `option_val`, `auto`) VALUES ("vendor_note", "", "1");
		');
		}
    }
}