<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_134 extends App_module_migration
{
    public function up()
    {
    	
		add_option('allow_vendors_to_register', 1);

		create_email_template('Your registration is confirmed', '<p>Dear {contact_firstname} {contact_lastname}<br /><br />We just wanted to let you know that your registration at&nbsp;{companyname} is successfully confirmed and your account is now active.<br /><br />You can login at&nbsp;<a href="{vendor_portal_url}">{vendor_portal_url}</a> with the email and password you provided during registration.<br /><br />Please contact us if you need any help.<br /><br />Kind Regards, <br />{email_signature}</p>
		 <p><br />(This is an automated email, so please dont reply to this email address)</p>', 'purchase_order', 'Vendor Registration Confirmed', 'vendor-registration-confirmed');
    }
}