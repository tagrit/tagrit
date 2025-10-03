<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_126 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        // 1.2.6 create purchase request from  sale estimate
		if (!$CI->db->field_exists('sale_estimate' ,db_prefix() . 'pur_request')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
		    ADD COLUMN `sale_estimate` INT(11) NULL
		  ;");
		}

		if (!$CI->db->field_exists('guarantee', 'items')) {
		    $CI->db->query('ALTER TABLE `'.db_prefix() . 'items` 
		    ADD COLUMN `guarantee` text  NULL 
		    
		    ;');            
		}

		if (!$CI->db->field_exists('profif_ratio' ,db_prefix() . 'items')) { 
		    $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
		        ADD COLUMN `profif_ratio` text  NULL
		    ;");
		}

		if (!$CI->db->field_exists('long_descriptions' ,db_prefix() . 'items')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
		      ADD COLUMN `long_descriptions` LONGTEXT NULL
		  ;");
		}

		if (!$CI->db->field_exists('without_checking_warehouse' ,db_prefix() . 'items')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
		      ADD COLUMN `without_checking_warehouse` int(11) NULL default 0
		  ;");
		}

		if (!$CI->db->field_exists('series_id' ,db_prefix() . 'items')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
		      ADD COLUMN `series_id` TEXT  NULL
		  ;");
		}

		if (!$CI->db->field_exists('warehouse_id' ,db_prefix() . 'items')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
		    ADD COLUMN `warehouse_id` int(11) NULL;
		    ");
		}

		if (!$CI->db->field_exists('origin' ,db_prefix() . 'items')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
		    ADD COLUMN `origin` varchar(100) NULL;
		    ");
		}
		if (!$CI->db->field_exists('color_id' ,db_prefix() . 'items')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
		    ADD COLUMN `color_id` int(11) NULL;
		    ");
		}
		if (!$CI->db->field_exists('style_id' ,db_prefix() . 'items')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
		    ADD COLUMN `style_id` int(11) NULL;
		    ");
		}
		if (!$CI->db->field_exists('model_id' ,db_prefix() . 'items')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
		    ADD COLUMN `model_id` int(11) NULL;
		    ");
		}
		if (!$CI->db->field_exists('size_id' ,db_prefix() . 'items')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
		    ADD COLUMN `size_id` int(11) NULL;
		    ");
		}

		if (!$CI->db->field_exists('unit_id' ,db_prefix() . 'items')) { 
		  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
		      ADD COLUMN `unit_id` int(11) NULL
		  ;");
		}
    }
}