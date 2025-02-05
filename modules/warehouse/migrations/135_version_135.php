<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_135 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();
		if (!$CI->db->table_exists(db_prefix() . 'wh_staff_warehouses')) {
			$CI->db->query('CREATE TABLE `' . db_prefix() . "wh_staff_warehouses` (
				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`staff_id` INT(11) NULL,
				`warehouse_id` INT(11) NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
		}
	}
}
