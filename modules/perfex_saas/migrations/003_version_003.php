<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_003 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
        $CI = &get_instance();
        $this->db = $CI->db;
    }

    public function up()
    {
        // This migration was in v0.0.2, however not included in create statement in v0.0.2. Thus fresh install could give an error.
        // So we include this in v0.0.3
        if (!$this->db->field_exists('custom_domain', perfex_saas_table('companies'))) {

            $this->db->query("ALTER TABLE `" . perfex_saas_table('companies') . "` ADD `custom_domain` VARCHAR(255) NULL AFTER `dsn`;");
        }
    }

    public function down()
    {
    }
}
