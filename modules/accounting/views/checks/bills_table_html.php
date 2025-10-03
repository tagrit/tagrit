<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = array(
  _l('acc_vendor_name'),
  _l('acc_due_date'),
  _l('account_from_chart_of_accounts'),
  _l('expense_dt_table_heading_amount'),
  _l('acc_status'),
 );

render_datatable($table_data, (isset($class) ? $class : 'bills'));
?>
