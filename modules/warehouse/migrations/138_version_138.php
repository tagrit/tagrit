<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_138 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();
		add_option('lot_number_prefix', 'LOT', 1);
		add_option('next_lot_number', 1, 1);
		add_option('auto_generate_lotnumber', 0, 1);

		add_option('custom_name_for_meter', 'm', 1);
		add_option('custom_name_for_kg', 'kg', 1);
		add_option('custom_name_for_m3', 'm3', 1);

		add_option('packing_list_pdf_display_rate', 1, 1);
		add_option('packing_list_pdf_display_tax', 1, 1);
		add_option('packing_list_pdf_display_subtotal', 1, 1);
		add_option('packing_list_pdf_display_discount_percent', 1, 1);
		add_option('packing_list_pdf_display_discount_amount', 1, 1);
		add_option('packing_list_pdf_display_totalpayment', 1, 1);
		add_option('packing_list_pdf_display_summary', 1, 1);

	}
}
