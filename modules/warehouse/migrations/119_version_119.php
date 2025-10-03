<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_119 extends App_module_migration
{
	public function up()
	{   
		$CI = &get_instance();

		        //version_118x
        add_option('display_product_name_when_print_barcode', 0, 1);
        add_option('show_item_cf_on_pdf', 0, 1);

        if (!$CI->db->table_exists(db_prefix() . 'wh_goods_delivery_activity_log')) {
			$CI->db->query('CREATE TABLE `' . db_prefix() . "wh_goods_delivery_activity_log` (
				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`rel_id` int NULL ,
				`rel_type` varchar(100) NULL ,
				`description` mediumtext NULL,
				`additional_data` text NULL,
				`date` datetime NULL,
				`staffid` int(11) NULL,
				`full_name` varchar(100) NULL,

				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
		}

	}
}
