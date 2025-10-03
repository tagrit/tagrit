<?php defined('BASEPATH') or exit('No direct script access allowed'); 
?>
<?php 
$file_header = array();
$file_header[] = _l('commodity_code');
$file_header[] = _l('commodity_name');
$file_header[] = _l('commodity_barcode');
$file_header[] = _l('sku_code');
$file_header[] = _l('sku_name');
$file_header[] = _l('Tags');
$file_header[] = _l('description');
$file_header[] = _l('commodity_type');
$file_header[] = _l('unit_id');

$file_header[] = _l('commodity_group');
$file_header[] = _l('sub_group');
$file_header[] = _l('_profit_rate');
$file_header[] = _l('purchase_price');
$file_header[] = _l('rate');
$file_header[] = _l('tax_1');
$file_header[] = _l('tax_2');
$file_header[] = _l('origin');
$file_header[] = _l('style_id');
$file_header[] = _l('model_id');
$file_header[] = _l('size_id');
$file_header[] = _l('_color');
$file_header[] = _l('guarantee_month');
$file_header[] = _l('minimum_inventory');



?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<h4><?php echo _l('Import_product') ?></h4>
						<div id ="dowload_file_sample">


						</div>

						<?php if(!isset($simulate)) { ?>
							<ul>
								<li class="text-danger">1. <?php echo _l('file_xlsx_commodity'); ?></li>
								<li class="text-danger">2. <?php echo _l('file_xlsx_tax'); ?></li>
								<li class="text-danger">3. <?php echo _l('file_xlsx_format'); ?></li>
								<li class="text-danger">4. <?php echo _l('tags_format'); ?></li>
							</ul>
							<div class="table-responsive no-dt">
								<table class="table table-hover table-bordered">
									<thead>
										<tr>
											<?php
											$total_fields = 0;
											
											for($i=0;$i<count($file_header);$i++){
												if($i == 0  ||$i == 1||$i == 9){
													?>
													<th class="bold"><span class="text-danger">*</span> <?php echo new_html_entity_decode($file_header[$i]) ?> </th>
													<?php 
												} else {
													?>
													<th class="bold"><?php echo new_html_entity_decode($file_header[$i]) ?> </th>
													
													<?php

												} 
												$total_fields++;
											}

											?>

										</tr>
									</thead>
									<tbody>
										<?php for($i = 0; $i<1;$i++){
											echo '<tr>';
											for($x = 0; $x<count($file_header);$x++){
												echo '<td>- </td>';
											}
											echo '</tr>';
										}
										?>
									</tbody>
								</table>
							</div>
							<hr>

						<?php } ?>
						
						<div class="row">
							<div class="col-md-4">
								<?php echo form_open_multipart(admin_url('hrm/import_job_p_excel'),array('id'=>'import_form')) ;?>
								<?php echo form_hidden('leads_import','true'); ?>
								<?php echo render_input('file_csv','choose_excel_file','','file'); ?> 

								<div class="form-group">
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

			<!-- box loading -->
			<div id="box-loading">

			</div>

		</div>


	<?php 
	$file_header = array();
	$file_header[] = _l('parent_id');
	$file_header[] = _l('attributes');
	?>

		<!-- import variation product -->
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<h4><?php echo _l('import_variation_product') ?></h4>

						<div id ="dowload_variation_file">
						</div>

						<?php if(!isset($simulate)) { ?>
							<ul>
								<li class="text-danger">1. <?php echo _l('import_variation_product_description'); ?></li>
								<li class="text-danger">2. <?php echo _l('import_variation_product_description1'); ?></li>
								<li class="text-danger">3. <?php echo _l('import_variation_product_description2'); ?></li>
								<p>
									product 1: width:10; height:30; length:40<br/>
									product 2: width:10; height:30; length:50<br/>
									product 3: width:10; height:40; length:40<br/>
									product 4: width:10; height:40; length:50<br/>
									product 5: width:20; height:30; length:40<br/>
									product 6: width:20; height:30; length:50<br/>
									product 7: width:20; height:40; length:40<br/>
									product 8: width:20; height:40; length:50<br/>
								</p>
								<a href="https://exceloffthegrid.com/list-of-all-possible-combinations-using-power-query/" target="_blank"><?php echo _l('import_variation_product_description7'); ?></a>
								<li class="text-danger">4. <?php echo _l('import_variation_product_description3'); ?></li>
								<li class="text-danger">5. <a href="<?php echo site_url('modules/warehouse/uploads/get_parent_id.PNG') ?>" target="_blank"><?php echo _l('import_variation_product_description4'); ?></a></li>
								<li class="text-danger">6. <a href="<?php echo site_url('modules/warehouse/uploads/attributes.PNG') ?>" target="_blank"><?php echo _l('import_variation_product_description5'); ?></a></li>
								<li class="text-danger">7. <?php echo _l('import_variation_product_description6'); ?></li>
								<li class="text-danger">8. <?php echo _l('import_variation_product_description8'); ?></li>


							</ul>
							<div class="table-responsive no-dt">
								<table class="table table-hover table-bordered">
									<thead>
										<tr>
											<?php
											$total_fields = 0;
											
											for($i=0;$i<count($file_header);$i++){
												if($i == 0  ||$i == 1||$i == 9){
													?>
													<th class="bold"><span class="text-danger">*</span> <?php echo new_html_entity_decode($file_header[$i]) ?> </th>
													<?php 
												} else {
													?>
													<th class="bold"><?php echo new_html_entity_decode($file_header[$i]) ?> </th>
													
													<?php

												} 
												$total_fields++;
											}

											?>

										</tr>
									</thead>
									<tbody>
										<?php for($i = 0; $i<1;$i++){
											echo '<tr>';
											for($x = 0; $x<count($file_header);$x++){
												echo '<td>- </td>';
											}
											echo '</tr>';
										}
										?>
									</tbody>
								</table>
							</div>
							<hr>

						<?php } ?>
						
						<div class="row">
							<div class="col-md-4">
								<?php echo form_open_multipart(admin_url('hrm/import_job_p_excel1'),array('id'=>'import_form1')) ;?>
								<?php echo form_hidden('leads_import1','true'); ?>
								<?php echo render_input('file_csv1','choose_excel_file','','file'); ?> 

								<div class="form-group">
									<button id="uploadfile1" type="button" class="btn btn-info import1" onclick="return uploadfilecsv1(this);" ><?php echo _l('import'); ?></button>
								</div>
								<?php echo form_close(); ?>
							</div>
							<div class="col-md-8">
								<div class="form-group" id="file_variation_upload_response">
									
								</div>
								
							</div>
						</div>
						
					</div>
				</div>
			</div>

			<!-- box loading -->
			<div id="box-loading">

			</div>

		</div>

	</div>
</div>
<?php init_tail(); ?>

<?php require 'modules/warehouse/assets/js/import_excel_js.php';?>
</body>
</html>
