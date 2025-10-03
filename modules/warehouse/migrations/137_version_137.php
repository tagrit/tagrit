<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_137 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();
		add_option('wh_hide_shipping_fee', 0, 1);
	}
}
