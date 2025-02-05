<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_134 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();
		add_option('wh_show_price_when_print_barcode', 1, 1);
	}
}
