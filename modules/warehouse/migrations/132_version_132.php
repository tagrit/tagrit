<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_132 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();
		if (!$CI->db->field_exists('hide_warehouse_when_out_of_stock' ,db_prefix() . 'warehouse')){
			$CI->db->query('ALTER TABLE `' . db_prefix() . "warehouse`
				ADD COLUMN `hide_warehouse_when_out_of_stock` INT(11) NULL DEFAULT '0' COMMENT  ' 1: yes  0: no'
				;");
		}
	}
}
