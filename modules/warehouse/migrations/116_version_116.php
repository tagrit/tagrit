<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_116 extends App_module_migration
{
     public function up()
     {   
        $CI = &get_instance();
            
        add_option('goods_delivery_pdf_display', 0, 1);
            
        if (warehouse_row_options_exist('"goods_delivery_pdf_display"') == 0){
            $CI->db->query('INSERT INTO `tbloptions` (`name`,`value`, `autoload`) VALUES ("goods_delivery_pdf_display", "0", "1");
          ');
        }

     }
}
