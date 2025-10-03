<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_107 extends App_module_migration
{
     public function up()
     {   
     	$CI = &get_instance();
        if (!$CI->db->field_exists('group_id' ,db_prefix() . 'wh_sub_group')) { 
			  $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_sub_group`
			      ADD COLUMN `group_id` int(11)  NULL
			  ;");
			} 
     }
}
