<div class="col-md-12" id="small-table">
	<div class="row">
      <h4 class="no-margin font-bold"><i class="fa fa-file-text-o" aria-hidden="true"></i> <?php echo _l('quotations'); ?></h4>
      <hr />
  	</div>  
    <?php if (has_permission('purchase_quotations', '', 'create') || is_admin()) { ?>
      <a href="<?php echo admin_url('purchase/estimate?vendor='.$client->userid); ?>"class="btn btn-info pull-left mright10 display-block">
        <i class="fa fa-plus"></i>&nbsp;<?php echo _l('new_pur_order'); ?>
      </a>
    <?php } ?> 	
    <br><br><br>
        
        <?php $table_data = array(
   _l('estimate_dt_table_heading_number'),
   _l('estimate_dt_table_heading_amount'),
   _l('estimates_total_tax'),
   array(
      'name'=>_l('invoice_estimate_year'),
      'th_attrs'=>array('class'=>'not_visible')
   ),
   _l('vendor'),
   _l('pur_request'),
   _l('estimate_dt_table_heading_date'),
   _l('estimate_dt_table_heading_expirydate'),
   _l('estimate_dt_table_heading_status'));


render_datatable($table_data, 'pur_estimates'); ?>	
</div>
