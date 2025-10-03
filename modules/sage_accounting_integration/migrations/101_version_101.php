<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_101 extends App_module_migration
{
     public function up()
     {
          add_option('acc_integration_sage_accounting_connected', 0);
          add_option('acc_integration_sage_accounting_last_cron_run', '');
     }
}