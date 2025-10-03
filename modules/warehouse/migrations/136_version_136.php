<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_136 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();
		add_option('notify_customer_when_change_delivery_status', 1, 1);
	}
}
