<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_150 extends App_module_migration
{
    public function up()
    {
    	$CI = &get_instance();

    	if (!$CI->db->field_exists('wh_quantity_received' ,db_prefix() . 'pur_order_detail')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
		      ADD COLUMN `wh_quantity_received` varchar(200)  NULL
		  ;");
		}
    }

}