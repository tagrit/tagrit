<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$hasPermission = staff_can('edit', 'accounting_bills') || staff_can('edit', 'accounting_bills');
 ?>

<?php
$table_data = [
   '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="bills"><label for="mass_select_all"></label></div>',
  _l('the_number_sign'),
  _l('acc_vendor_name'),
  _l('bill_date'),
  _l('acc_due_date'),
  _l('date_paid'),
  _l('expense_dt_table_heading_amount'),
  _l('check_number'),
  _l('acc_status'),

];


/*$table_data = array_merge($table_data, [
  _l('expense_dt_table_heading_reference_no'),
]);*/


render_datatable($table_data, (isset($class) ? $class : 'bills'), [], [
  'data-last-order-identifier' => 'bills',
  'data-default-order'         => get_table_last_order('bills'),
]);


