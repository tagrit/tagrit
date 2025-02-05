<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s">

					<div class="panel-body mtop10">
						<div class="row">
							<div class="col-md-6">
								<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l('wh_manage_serial_number'); ?></h4>
							</div>
							<div class="col-md-6">
								<div class="form-group pull-right">
									<a href="<?php echo admin_url('warehouse/commodity_list'); ?>"class="btn btn-default text-right mright5"><?php echo _l('close'); ?></a>
									<a href="<?php echo admin_url('warehouse/import_serial_number'); ?>"class="btn btn-danger text-right mright5"><?php echo _l('wh_import_serial_numbers'); ?></a>
								</div>
							</div>
						</div>

						
						<div class="row">
							<div class=" col-md-4">
								<div class="form-group">
									<select name="warehouse_filter[]" id="warehouse_filter" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('warehouse_filter'); ?>">

										<?php foreach($warehouse_filter as $warehouse) { ?>
											<option value="<?php echo new_html_entity_decode($warehouse['warehouse_id']); ?>"><?php echo new_html_entity_decode($warehouse['warehouse_name']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<?php $this->load->view('warehouse/item_include/item_select', ['select_name' => 'commodity_filter[]', 'id_name' => 'commodity_filter', 'multiple' => true, 'data_none_selected_text' => 'commodity']); ?>
							</div>
							
							<?php 
							$serial_number_status = [];
							$serial_number_status[] = [
								'name' => 1,
								'label' => _l('wh_in_stock'),
							];
							$serial_number_status[] = [
								'name' => 2,
								'label' => _l('wh_out_stock'),
							];
							
							?>
							<div class="col-md-4">
								<?php echo render_select('show_serial_number_status_filter[]', $serial_number_status, array('name', array('label')), '', [1], ['multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker'], array(), '', '', false); ?>
							</div>
						</div>

						<div class="row">

							<!-- update multiple item -->

							<div class="col-md-12">
								<?php 
								$table_data = array(

									_l('id'),
									_l('wh_serial_number'),
									_l('commodity_name'),
									_l('purchase_price'),
									_l('lot_number'),
									_l('date_manufacture'),
									_l('expiry_date'),
									_l('wh_detail'),
									_l('warehouse_name'),
									_l('quantity'),
									_l('status_label'),

								);


								render_datatable($table_data,'table_serial_number',
									array('customizable-table'),
									array(
										'proposal_sm' => 'proposal_sm',
										'id'=>'table-table_serial_number',
										'data-last-order-identifier'=>'table_serial_number',
										'data-default-order'=>get_table_last_order('table_serial_number'),
									)); ?>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

	<?php echo form_hidden('warehouse_id'); ?>
	<?php echo form_hidden('commodity_id'); ?>
	<?php echo form_hidden('filter_all_simple_variation_value'); ?>

	<div id="modal_wrapper"></div>
	<!-- box loading -->
	<div id="box-loading">

	</div>

	<?php init_tail(); ?>
	<?php require 'modules/warehouse/assets/js/serial_numbers/manage_serial_number/manage_js.php';?>
</body>
</html>
