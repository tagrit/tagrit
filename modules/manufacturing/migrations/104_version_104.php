<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_104 extends App_module_migration
{
     public function up()
     {
            $CI = &get_instance();
            if (mrp_row_options_exists('"cost_hour"') == 0){
                  $CI->db->query('INSERT INTO `'.db_prefix().'mrp_option` (`option_name`,`option_val`, `auto`) VALUES ("cost_hour", "0", "1");
                ');
            }
     }
}
