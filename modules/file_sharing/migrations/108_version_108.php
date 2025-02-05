<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_108 extends App_module_migration

{
    public function up()
    {
    	add_option('fs_permisstion_staff_share_to_client', 1);
    }

}

