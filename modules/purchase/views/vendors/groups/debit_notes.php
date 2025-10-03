<div class="col-md-12" id="small-table">
	<div class="row">
      <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('debit_notes'); ?></h4>
      <hr />
  	</div> 
    <?php if (has_permission('purchase_debit_notes', '', 'create') || is_admin()) { ?>
      <a href="<?php echo admin_url('purchase/debit_note'); ?>"class="btn btn-info pull-left mright10 display-block">
        <i class="fa fa-plus"></i>&nbsp;<?php echo _l('new_pur_order'); ?>
      </a>
    <?php } ?> 	
    <br><br><br>
        <?php $table_data = array(
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


render_datatable($table_data,'debit-notes'); ?>
</div>