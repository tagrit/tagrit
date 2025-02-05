<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_144 extends App_module_migration
{
    public function up()
    {
    	create_email_template('New Contact Added/Registered (Welcome Email)', '<span style=\"font-size: 12pt;\"> Dear {contact_firstname} {contact_lastname}! </span><br /><br /><span style=\"font-size: 12pt;\"> Welcome to our system </span><br /><br /><span style=\"font-size: 12pt;\"><br />Click here to login: {vendor_portal_link}</span><br /></span><br />', 'purchase_order', 'New Contact Added/Registered (Welcome Email)', 'new-contact-created');


        create_email_template('Request approval', '<span style=\"font-size: 12pt;\"> Hello {staff_name}! </span><br /><br /><span style=\"font-size: 12pt;\"> You receive an approval request: {link} from {from_staff_name}</span>', 'purchase_order', 'Request approval', 'purchase-request-approval');

        create_email_template('Email send approved', '<span style=\"font-size: 12pt;\"> Hello {staff_name}! </span><br /><br /><span style=\"font-size: 12pt;\">{type} has been approved by {by_staff_name} </span><br /><span style=\"font-size: 12pt;\"><br />Click here to view detail: {link} </span><br /></span><br />', 'purchase_order', 'Email send approved', 'purchase-send-approved');

        create_email_template('Email send rejected', '<span style=\"font-size: 12pt;\"> Hello {staff_name}! </span><br /><br /><span style=\"font-size: 12pt;\"> {type} has been declined by {by_staff_name} </span><br /><span style=\"font-size: 12pt;\"><br />Click here to view detail: {link}  </span><br /></span><br />', 'purchase_order', 'Email send rejected', 'purchase-send-rejected');
    }

}