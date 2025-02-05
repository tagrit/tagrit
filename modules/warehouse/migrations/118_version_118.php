<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_118 extends App_module_migration
{
     public function up()
     {   
        $CI = &get_instance();
        
        //Inventory Module V1.1.8
        /*
        *- "stock summary report" -> Export In Period get "purchase price", Amount = quantity*purchase price ( don't sub)"
        *- Loss & adjustment" -> fix time format error (done)
        *- Remove "Deliver name", "Stocker", "Cheif accountant".
        *- Item remove required "Commodity type"
        *- commodity code = skucode, commodity name = sku name when sysc from woocommer
        *- add space PDF file inventory receiving voucher (done)
        *- Item: add parent id( default NULL)
        *- Add variation for parent item ( add , edit, delete)
        *1. Item: add parent id( default NULL)
        *2. Detail item screen: add new tab ( load child item)
        *3. Add variation for parent item ( add , edit, delete)
        *4. Load variation from parent: (when create item, if choose parent item ).
        *5. Update related to  item ( get item)
        *6. Create Goods Delivery: fill item via scanner barcode
        *7. Create Goods Receiving: fill item via scanner barcode
         */
        //new variation
        if (!$CI->db->field_exists('parent_id' ,db_prefix() . 'items')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
              ADD COLUMN `parent_id` int(11)  NULL  DEFAULT NULL
          ;");
        }

        if (!$CI->db->field_exists('attributes' ,db_prefix() . 'items')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
              ADD COLUMN `attributes` LONGTEXT  NULL
          ;");
        }

        if (!$CI->db->field_exists('parent_attributes' ,db_prefix() . 'items')) { 
          $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
              ADD COLUMN `parent_attributes` LONGTEXT  NULL
          ;");
        }
        add_option('display_product_name_when_print_barcode', 0, 1);


     }
}
