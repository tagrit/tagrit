<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_133 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();
		add_option('wh_shortened_form_pdf', 0, 1);
	}
}
