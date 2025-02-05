<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = array(
 _l('debit_note_number'),
 _l('debit_note_date'),
 (!isset($client) ? _l('vendor') : array(
   'name'=>_l('vendor'),
   'th_attrs'=>array('class'=>'not_visible')
 )),
 _l('debit_note_status'),

 _l('reference_no'),
 _l('debit_note_amount'),
 _l('debit_note_remaining_debits'),
);


render_datatable($table_data,'debit-notes');
?>
