<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div class="row">
	<div class="col-md-12">
		<div class="panel_s">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<h4>
	                      <?php echo pur_html_entity_decode($item->description); ?>
	                   	</h4>
	                   	<hr class="hr-panel-heading" /> 
					</div>
					
					<div class="col-md-7 panel-padding">
			          <table class="table border table-striped table-margintop">
			              <tbody>

			                  <tr class="project-overview">
			                    <td class="bold" width="30%"><?php echo _l('commodity_code'); ?></td>
			                    <td><?php echo pur_html_entity_decode($item->commodity_code) ; ?></td>
			                 </tr>
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('commodity_name'); ?></td>
			                    <td><?php echo pur_html_entity_decode($item->description) ; ?></td>
			                 </tr>
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('commodity_group'); ?></td>
			                    <td><?php echo get_group_name_pur(pur_html_entity_decode($item->group_id)) != null ? get_group_name_pur(pur_html_entity_decode($item->group_id))->name : '' ; ?></td>
			                 </tr>
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('commodity_barcode'); ?></td>
			                    <td><?php echo pur_html_entity_decode($item->commodity_barcode) ; ?></td>
			                 </tr>
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('sku_code'); ?></td>
			                    <td><?php echo pur_html_entity_decode($item->sku_code) ; ?></td>
			                 </tr>
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('sku_name'); ?></td>
			                    <td><?php echo pur_html_entity_decode($item->sku_name) ; ?></td>
			                 </tr>
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('tax_1'); ?></td>
			                    <td><?php echo pur_html_entity_decode($item->tax) != '' && pur_get_tax_rate($item->tax) != null ? pur_get_tax_rate($item->tax)->name : '';  ?></td>
			                 </tr> 
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('tax_2'); ?></td>
			                    <td><?php echo pur_html_entity_decode($item->tax2) != '' && pur_get_tax_rate($item->tax2) != null ? pur_get_tax_rate($item->tax2)->name : '';  ?></td>
			                 </tr> 
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('rate'); ?></td>
			                    <td><?php 
			                    	if($item_from == 'vendor'){
			                    		$rate = $item->rate;
			                    	}else{
			                    		$rate = $item->purchase_price;
			                    	}
			                    	echo app_format_money($rate, '')  
			                    ?></td>
			                 </tr> 
			                

			                </tbody>
			          </table>
			      	</div>
			      	<div class="gallery">
			            <div class="wrapper-masonry">
			              <div id="masonry" class="masonry-layout columns-2">
			              	<?php if($item_from == 'vendor'){ ?>
					            <?php if(isset($commodity_file) && count($commodity_file) > 0){ ?>
					              <?php foreach ($commodity_file as $key => $value) { ?>

					                  <?php if(file_exists(PURCHASE_MODULE_UPLOAD_FOLDER.'/vendor_items/' .$value["rel_id"].'/'.$value["file_name"])){ ?>
					                      <a  class="images_w_table" href="<?php echo site_url('modules/purchase/uploads/vendor_items/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/purchase/uploads/vendor_items/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo pur_html_entity_decode($value['file_name']) ?>"/></a>
					                       
					                    <?php } ?>

					            <?php } ?>
					          <?php }else{ ?>

					                <a class="images_w_table" href="<?php echo site_url('modules/purchase/uploads/nul_image.jpg'); ?>"><img class="images_w_table" src="<?php echo site_url('modules/purchase/uploads/nul_image.jpg'); ?>" alt="nul_image.jpg"/></a>

					          <?php } ?>
					      	<?php }else{ ?>
					      		<?php if(isset($item_file) && count($item_file) > 0){ ?>
					              <?php foreach ($item_file as $key => $value) { ?>
					                  <?php if(file_exists(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER .$value["rel_id"].'/'.$value["file_name"])){ ?>
					                        <a  class="images_w_table" href="<?php echo site_url('modules/purchase/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/purchase/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo pur_html_entity_decode($value['file_name']) ?>"/></a>
					                    <?php }elseif(file_exists('modules/warehouse/uploads/item_img/' .$value["rel_id"].'/'.$value["file_name"])){ ?>
					                       <a  class="images_w_table" href="<?php echo site_url('modules/warehouse/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/warehouse/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo pur_html_entity_decode($value['file_name']) ?>"/></a>
					                    <?php }else{ ?>

					                      <a  class="images_w_table" href="<?php echo site_url('modules/manufacturing/uploads/products/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/manufacturing/uploads/products/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo pur_html_entity_decode($value['file_name']) ?>"/></a>
					                    <?php } ?>
					            <?php } ?>
					          <?php }else{ ?>
					              <?php 
					              $_img = ''; 
					              if(isset($vendor_image) && count($vendor_image) > 0){ 
					                foreach($vendor_image as $value){
					                  if(file_exists(PURCHASE_PATH.'vendor_items/' .$item->from_vendor_item .'/'.$value['file_name'])){
					                    $_img .= '<a  class="images_w_table" href="'.site_url('modules/purchase/uploads/vendor_items/'.$value["rel_id"].'/'.$value["file_name"]).'"><img class="images_w_table" src="'. site_url('modules/purchase/uploads/vendor_items/'.$value["rel_id"].'/'.$value["file_name"]).'" alt="'. pur_html_entity_decode($value['file_name']).'"/></a>';
					                  }
					                }
					              }else{
					                $_img .= '<a href="'.site_url('modules/purchase/uploads/nul_image.jpg').'"><img class="images_w_table" src="'.site_url('modules/purchase/uploads/nul_image.jpg').'" alt="nul_image.jpg"/></a>';
					              }

					              echo $_img;
					              ?>
					          <?php } ?>
					        <?php } ?>
			            <div class="clear"></div>
			          </div>
			        </div>
			        </div>

				</div>
			</div>
		</div>
	</div>
</div>
<?php hooks()->do_action('app_admin_footer'); ?>