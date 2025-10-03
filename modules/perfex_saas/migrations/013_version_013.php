<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_013 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
        $CI = &get_instance();
        $this->db = $CI->db;
    }

    public function up()
    {
        $sql = "ALTER TABLE `" . perfex_saas_table('companies') . "` CHANGE `status` `status` ENUM('active','inactive','disabled','banned','pending','deploying') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'pending';";
        $this->db->query($sql);

        $sql = "ALTER TABLE `" . perfex_saas_table('packages') . "` CHANGE `description` `description` TEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL;";
        $this->db->query($sql);

        perfex_saas_install();
    }
    public function down()
    {
    }
}
