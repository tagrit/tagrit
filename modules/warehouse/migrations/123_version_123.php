<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_123 extends App_module_migration
{
	public function up()
	{   
		$CI = &get_instance();
		add_option('goods_delivery_pdf_display_outstanding', 0, 1);
		add_option('goods_delivery_pdf_display_warehouse_lotnumber_bottom_infor', 0, 1);
	}
}
