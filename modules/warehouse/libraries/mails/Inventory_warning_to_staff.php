<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Inventory_warning_to_staff extends App_mail_template
{

    protected $notification_info;



    public $slug = 'inventory-warning-to-staff';

    public function __construct($notification_info)
    {
        parent::__construct();

        $this->notification_info = $notification_info;


        // For SMS and merge fields for email
        $this->set_merge_fields('inventory_warning_merge_fields', $this->notification_info);
    }
    public function build()
    {

        $this->to($this->notification_info->email);
        
    }
}
