<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_130 extends App_module_migration
{
	public function up()
	{
				
		add_option('wh_products_by_serial', 1, 1);
	}
}
