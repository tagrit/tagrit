<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_128 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();
		
		if (!$CI->db->field_exists('shipment_hash' ,db_prefix() . 'wh_omni_shipments')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_omni_shipments`
				ADD COLUMN `shipment_hash` VARCHAR(32) NULL
				');
		}
		add_option('wh_display_shipment_on_client_portal', 1, 1);
		
	}
}
