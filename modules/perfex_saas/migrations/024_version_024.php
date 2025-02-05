<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_024 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $q = "ALTER TABLE `" . perfex_saas_table('companies') . "` CHANGE `status` `status` ENUM('active','inactive','disabled','banned','pending','deploying','pending-delete') NOT NULL DEFAULT 'pending';";
        perfex_saas_raw_query($q, [], false, false);
        perfex_saas_install();
    }

    public function down()
    {
    }
}
