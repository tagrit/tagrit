<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_112 extends App_module_migration
{
     public function up()
     {   
        $CI = &get_instance();

        //add approval setting for lost adjustment
        //split purchase order  with multiple inventory receipts voucher
     	  
        add_option('cancelled_invoice_reverse_inventory_delivery_voucher', 0, 1);
        add_option('uncancelled_invoice_create_inventory_delivery_voucher', 0, 1);
        add_option('inventory_auto_operations_hour', 0, 1);
        add_option('automatically_send_items_expired_before', 0, 1);
        add_option('inventorys_cronjob_active', 0, 1);
        add_option('inventory_cronjob_notification_recipients', '', 1);

        if (warehouse_row_options_exist('"cancelled_invoice_reverse_inventory_delivery_voucher"') == 0){
            $CI->db->query('INSERT INTO `tbloptions` (`name`,`value`, `autoload`) VALUES ("cancelled_invoice_reverse_inventory_delivery_voucher", "0", "1");
          ');
        }

        if (warehouse_row_options_exist('"uncancelled_invoice_create_inventory_delivery_voucher"') == 0){
            $CI->db->query('INSERT INTO `tbloptions` (`name`,`value`, `autoload`) VALUES ("uncancelled_invoice_create_inventory_delivery_voucher", "0", "1");
          ');
        }


        if (warehouse_row_options_exist('"inventory_auto_operations_hour"') == 0){
            $CI->db->query('INSERT INTO `tbloptions` (`name`,`value`, `autoload`) VALUES ("inventory_auto_operations_hour", "0", "1");
          ');
        }
        if (warehouse_row_options_exist('"automatically_send_items_expired_before"') == 0){
            $CI->db->query('INSERT INTO `tbloptions` (`name`,`value`, `autoload`) VALUES ("automatically_send_items_expired_before", "0", "1");
          ');
        }

        if (warehouse_row_options_exist('"inventorys_cronjob_active"') == 0){
            $CI->db->query('INSERT INTO `tbloptions` (`name`,`value`, `autoload`) VALUES ("inventorys_cronjob_active", "0", "1");
          ');
        }
        if (warehouse_row_options_exist('"inventory_cronjob_notification_recipients"') == 0){
            $CI->db->query('INSERT INTO `tbloptions` (`name`,`value`, `autoload`) VALUES ("inventory_cronjob_notification_recipients", "", "1");
          ');
        }

          create_email_template('Inventory warning', 'Hi {staff_name}! <br /><br />This is a inventory warning<br />{<span 12pt="">notification_content</span>}. <br /><br />Regards.', 'inventory_warning', 'Inventory warning (Sent to staff)', 'inventory-warning-to-staff');

        if (get_status_modules_wh('purchase')) {
          if (!$CI->db->field_exists('wh_quantity_received' ,db_prefix() . 'pur_order_detail')) { 
            $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
                ADD COLUMN `wh_quantity_received` varchar(200)  NULL
            ;");
          }
        }
          

     }
}
