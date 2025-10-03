<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_102 extends App_module_migration

{
    public function up()
    {
    	$CI = &get_instance();

		add_option('fs_the_administrator_of_the_public_folder');
    }

}

