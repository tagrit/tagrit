<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_129 extends App_module_migration
{
    public function up()
    {
       	add_option('send_email_welcome_for_new_contact', 1, 1);
		add_option('reset_purchase_order_number_every_month', 1, 1);
    }
}