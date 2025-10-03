<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_111 extends App_module_migration
{
     public function up()
     {   
        $CI = &get_instance();
     	  
          if (!$CI->db->field_exists('without_checking_warehouse' ,db_prefix() . 'items')) { 
            $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
                ADD COLUMN `without_checking_warehouse` int(11) NULL default 0
            ;");
          }

     }
}
