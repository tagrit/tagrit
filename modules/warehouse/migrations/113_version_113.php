<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_113 extends App_module_migration
{
     public function up()
     {   
        $CI = &get_instance();

        add_option('inventory_received_number_prefix', 'NK', 1);
        add_option('next_inventory_received_mumber', 1, 1);
        add_option('inventory_delivery_number_prefix', 'XK', 1);
        add_option('next_inventory_delivery_mumber', 1, 1);

     }
}
