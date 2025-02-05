<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = array(
   _l('estimate_dt_table_heading_number'),
   _l('estimate_dt_table_heading_amount'),
   _l('estimates_total_tax'),
   array(
      'name'=>_l('invoice_estimate_year'),
      'th_attrs'=>array('class'=>'not_visible')
   ),
   array(
      'name'=>_l('vendor'),
      'th_attrs'=>array('class'=> (isset($client) ? 'not_visible' : ''))
   ),
   _l('pur_request'),
   _l('estimate_dt_table_heading_date'),
   _l('estimate_dt_table_heading_expirydate'),
   _l('approval_status'));


render_datatable($table_data, 'pur_estimates');
