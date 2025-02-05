<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_118 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        //Ver 1.1.8
        create_email_template('Purchase Statement', '<span style=\"font-size: 12pt;\"> Dear {contact_firstname} {contact_lastname} !. </span><br /><br /><span style=\"font-size: 12pt;\">Its been a great experience working with you. </span><br /><br /><span style=\"font-size: 12pt;\"><br />Attached with this email is a list of all transactions for the period between {statement_from} to {statement_to}<br/ ><br/ >For your information your account balance due is total: {statement_balance_due}<br /><br/ > Please contact us if you need more information.<br/ > <br />{additional_content}
  </span><br /><br />', 'purchase_order', 'Purchase Statement (Sent to contact)', 'purchase-statement-to-contact');

		add_option('show_purchase_tax_column', 1);
		add_option('po_only_prefix_and_number', 0);
    }
}