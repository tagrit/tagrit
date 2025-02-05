<div class="col-md-12" id="small-table">
	<div class="row">
      <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('purchase_invoice'); ?></h4>
      <hr />
  	</div> 
    <?php if (has_permission('purchase_invoices', '', 'create') || is_admin()) { ?>
      <a href="<?php echo admin_url('purchase/pur_invoice'); ?>"class="btn btn-info pull-left mright10 display-block">
        <i class="fa fa-plus"></i>&nbsp;<?php echo _l('new_pur_order'); ?>
      </a>
    <?php } ?> 	
    <br><br><br>
        <?php $table_data = array(
                        _l('invoice_code'),
                        _l('invoice_no'),
                        _l('vendor'),
                        _l('contract'),
                        _l('pur_order'),
                        _l('invoice_date'),
                        _l('recurring_from'),
                        _l('invoice_amount'),
                        _l('tax_value'),
                        _l('total_included_tax'),
                        _l('payment_request_status'),
                        _l('payment_status'),
                        _l('transaction_id'),
                        _l('tag'),
                        );

                    $custom_fields = get_custom_fields('pur_invoice',array('show_on_table'=>1));
                    foreach($custom_fields as $field){
                     array_push($table_data,$field['name']);
                    }
                    
                    render_datatable($table_data,'table_pur_invoices'); ?>
</div>