<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<div class="row row-margin-bottom">
									<div class="col-md-12 ">
										<?php if (has_permission('warehouse_item', '', 'create') || is_admin() || has_permission('warehouse_item', '', 'edit') ) { ?>
											<a href="#" id="dowload_items"  class="btn btn-warning pull-left  mr-4 button-margin-r-b hide"><?php echo _l('dowload_items'); ?></a>

										<?php } ?>
									</div>
								</div>
								<ul>
									<li class="text-danger">1. <?php echo _l('Create_file_import_Serial_number_Check_the_item_to_import_Serial_number_then_click_Export_the_seleted_item'); ?></li>
									<li class="text-danger">2. <?php echo _l('It_is_necessary_to_use_the_files_generated_from_the_system_to_enter_data_into_the_system'); ?></li>
									<li class="text-danger">3. <?php echo _l('Do_not_add_any_columns_or_rows_to_the_file_downloaded_from_the_system_Only_the_value_of_the_Serial_Number_column'); ?></li>
								</ul>

								<div class="row">
									<div class="col-md-4">
										<?php echo form_open_multipart(admin_url('warehouse/import_file_xlsx_opening_stock'),array('id'=>'import_form')) ;?>
										<?php echo form_hidden('leads_import','true'); ?>
										<?php echo render_input('file_csv','choose_excel_file','','file'); ?> 

										<div class="form-group">
											<a href="<?php echo admin_url('warehouse/commodity_list'); ?>"class="btn btn-default text-right mright5"><?php echo _l('close'); ?></a>
											<button id="uploadfile" type="button" class="btn btn-info import" onclick="return uploadfilecsv(this);" ><?php echo _l('import'); ?></button>
										</div>
										<?php echo form_close(); ?>
									</div>
									<div class="col-md-8">
										<div class="form-group" id="file_upload_response">

										</div>

									</div>
								</div>

							</div>
						</div>
					</div>

					<div class="panel-body mtop10">
						<div class="row">
							<div class="col-md-12">
								<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l('items'); ?></h4>
								<br>

							</div>
						</div>

						
						<div class="row">
							<?php 
							$serial_number_type = [];
							$serial_number_type[] = [
								'name' => 1,
								'label' => _l('wh_add_serial_numbers_for_items'),
							];
							$serial_number_type[] = [
								'name' => 2,
								'label' => _l('wh_update_serial_numbers_for_items'),
							];
							
							 ?>
							<div class="col-md-4">
								<?php echo render_select('show_items_filter[]', $serial_number_type, array('name', array('label')), '', [2], ['multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker'], array(), '', '', false); ?>
							</div>
							<div class=" col-md-4">
								<?php $this->load->view('warehouse/item_include/item_select', ['select_name' => 'commodity_filter[]', 'id_name' => 'commodity_filter', 'multiple' => true, 'data_none_selected_text' => 'commodity']); ?>
							</div>
							<div class=" col-md-4">
								<div class="form-group">
									<select name="warehouse_filter[]" id="warehouse_filter" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('warehouse_filter'); ?>">

										<?php foreach($warehouse_filter as $warehouse) { ?>
											<option value="<?php echo new_html_entity_decode($warehouse['warehouse_id']); ?>"><?php echo new_html_entity_decode($warehouse['warehouse_name']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>

						<div class="row">

							<!-- update multiple item -->

							<a href="#"  onclick="export_item(); return false;" data-toggle="modal" data-table=".table-table_commodity_list" data-target="#leads_export_item" class=" hide bulk-actions-btn table-btn"><?php echo _l('export_item'); ?></a>

							<div class="col-md-12">
								<?php 
								$table_data = array(
									'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_commodity_list"><label></label></div>',
									_l('_images'),
									_l('commodity_code'),
									_l('commodity_name'),
									_l('sku_code'),
									_l('group_name'),
									_l('warehouse_name'),
									_l('tags'),
									_l('inventory_number'),
									_l('unit_name'),
									_l('rate'),
									_l('purchase_price'),
									_l('tax_1'),
									_l('tax_2'),
									_l('status'),                         
									_l('minimum_stock'),                         
									_l('maximum_stock'),
									_l('final_price'),                         
								);

								$cf = get_custom_fields('items',array('show_on_table'=>1));
								foreach($cf as $custom_field) {
									array_push($table_data,$custom_field['name']);
								}

								render_datatable($table_data,'table_commodity_list',
									array('customizable-table'),
									array(
										'proposal_sm' => 'proposal_sm',
										'id'=>'table-table_commodity_list',
										'data-last-order-identifier'=>'table_commodity_list',
										'data-default-order'=>get_table_last_order('table_commodity_list'),
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
	<?php require 'modules/warehouse/assets/js/serial_numbers/manage_commodity_js.php';?>
</body>
</html>
