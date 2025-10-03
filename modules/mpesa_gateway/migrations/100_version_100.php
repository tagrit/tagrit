<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_100 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $table = db_prefix() . 'mpesa_gateway_transactions';

        $CI = &get_instance();
        if (!$CI->db->field_exists('receipt_number', $table)) {
            $CI->db->query("ALTER TABLE `$table` ADD `receipt_number` VARCHAR(255) NULL AFTER `description`;");
        }
    }

    public function down()
    {
    }
}