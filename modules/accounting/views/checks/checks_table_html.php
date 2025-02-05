<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = array(
  _l('acc_vendor_name'),
  _l('acc_date'),
  _l('acc_bank_account'),
  _l('expense_dt_table_heading_amount'),
  _l('check_number'),
  _l('status'),
 );

render_datatable($table_data, (isset($class) ? $class : 'checks'));
?>
