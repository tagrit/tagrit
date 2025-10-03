<?php echo form_open_multipart(admin_url('warehouse/warranty_period_pdf'), array('id'=>'print_report')); ?>
<div class="row row-margin-bottom">

	<div class=" col-md-3">
		<?php $this->load->view('warehouse/item_include/item_select', ['select_name' => 'commodity_filter[]', 'id_name' => 'commodity_filter', 'multiple' => true, 'label_name' => 'commodity']); ?>
	</div>
	<div class=" col-md-3 hide">
		<?php echo render_select('staff_id[]', [], array('staffid', array('full_name')), 'stock_export', '', ['multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', false); ?>
	</div>

	<div class=" col-md-3">
		<?php echo render_select('customer_name_filter[]', $clients, array('userid', array('company')), 'customer_name', '', ['multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', false); ?>
	</div>
	
	<div class="col-md-2">
		<?php echo render_date_input('to_date_filter', 'Warranty_Expiry_date', $period_to_date); ?>
	</div>

	<?php 
	$packing_list_status = [];
	$packing_list_status[] = [
		'id' => 1,
		'label' => _l('within_the_warranty_period'),
	];
	$packing_list_status[] = [
		'id' => 2,
		'label' => _l('expiry_of_warranty'),
	];

	?>
	<div class="col-md-3">
		<?php echo render_select('status_filter[]', $packing_list_status, array('id', array('label')), 'status', $period_status_id, ['multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker'], array(), '', '', false); ?>
	</div>
	<div class="col-md-1">
		<label><?php echo _l('print'); ?></label>
		<div class="form-group">
			<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
			<ul class="dropdown-menu dropdown-menu-right">
				<li class="hidden-xs"><a href="?output_type=I" target="_blank" onclick="stock_submit(this); return false;"><?php echo _l('download_pdf'); ?></a></li>
			</ul>
		</div>
	</div>
	
</div>
<?php echo form_close(); ?>

<br/>

<div class="row">
	<div class="col-md-12">
		<?php 
		$table_data = array(
			_l('goods_delivery'),
			_l('customer_name'),
			_l('commodity_name'),
			_l('quantity'),
			_l('rate'),
			_l('expiry_date'),
			_l('lot_number'),
			_l('wh_serial_number'),
			_l('guarantee_period'),

		);
		render_datatable($table_data,'table_warranty_period',
			array('customizable-table')
		); ?>

	</div>
</div>


</body>
</html>
