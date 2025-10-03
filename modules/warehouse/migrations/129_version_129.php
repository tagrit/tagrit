<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_129 extends App_module_migration
{
	public function up()
	{
				
		add_option('wh_on_total_items', 200, 1);
	}
}
