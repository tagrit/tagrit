<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_110 extends App_module_migration
{
     public function up()
     {   
        $CI = &get_instance();
     	  
          if (!$CI->db->field_exists('long_descriptions' ,db_prefix() . 'items')) { 
            $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
                ADD COLUMN `long_descriptions` LONGTEXT NULL
            ;");
          }

          if (warehouse_row_options_exist('"revert_goods_receipt_goods_delivery"') == 0){
              $CI->db->query('INSERT INTO `tbloptions` (`name`,`value`, `autoload`) VALUES ("revert_goods_receipt_goods_delivery", "0", "1");
            ');
          }
          
        
     }
}
