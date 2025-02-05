<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_107 extends App_module_migration

{
    public function up()
    {
    	add_option('fs_allow_file_editing', 1);
		add_option('fs_hidden_files', '.tmb,index.html');
    }

}

