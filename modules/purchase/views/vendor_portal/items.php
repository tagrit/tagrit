<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div class="row">
	<div class="col-md-12">
		<div class="panel_s">
			<div class="panel-body">
				<?php 
				$base_currency = get_base_currency_pur(); 
				$admin_currency = $base_currency;
				$vendor_currency = get_vendor_currency(get_vendor_user_id());
				if($vendor_currency != 0){
					$base_currency = pur_get_currency_by_id($vendor_currency);
				}
				?>
				<h4><?php echo pur_html_entity_decode($title) ?></h4>
				
				<div class="horizontal-scrollable-tabs preview-tabs-top">
            
		            <div class="horizontal-tabs">
		               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
		                  <li role="presentation" class="active">
		                     <a href="#internal_items" aria-controls="internal_items" role="tab" data-toggle="tab">
		                     <?php echo _l('private_items'); ?>
		                     </a>
		                  </li>

		                  <li role="presentation">
		                     <a href="#external_items" aria-controls="external_items" role="tab" data-toggle="tab">
		                     <?php echo _l('public_items'); ?>
		                     </a>
		                  </li>
		                  
		                  
		                </ul>
		            </div>
		        </div>

		        <div class="tab-content">
             		<div role="tabpanel" class="tab-pane active" id="internal_items">

						<a href="<?php echo site_url('purchase/vendors_portal/add_update_items'); ?>" class="btn btn-info"><?php echo _l('add_new'); ?></a>
						<br><br>
						<table class="table dt-table" >
				            <thead>
				               <tr>
				               	  <th ><?php echo _l('pur_image'); ?></th>
				                  <th ><?php echo _l('pur_item'); ?></th>
				                  <th ><?php echo _l('unit'); ?></th>
				                  <th ><?php echo _l('pur_group'); ?></th>
				                  <th ><?php echo _l('pur_rate'); ?></th>
				                  <th ><?php echo _l('pur_tax'); ?></th>
				                  <th ><?php echo _l('options') ?></th>
				               </tr>
				            </thead>
				            <tbody>
				            	<?php foreach($items as $p){ ?>
				            	
				            		<tr>
				            			<td>
				            				<?php 
				            				$arr_images = vendor_item_images($p['id']);

				            				if(count($arr_images) > 0){

					            				if(file_exists(PURCHASE_MODULE_UPLOAD_FOLDER .'/vendor_items/' .$arr_images[0]['rel_id'] .'/'.$arr_images[0]['file_name'])){
								                    $_data = '<img class="images_w_table" src="' . site_url('modules/purchase/uploads/vendor_items/' . $arr_images[0]['rel_id'] .'/'.$arr_images[0]['file_name']).'" alt="'.$arr_images[0]['file_name'] .'" >';
								                }else{
								                	$_data = '<img class="images_w_table" src="' . site_url('modules/purchase/uploads/nul_image.jpg' ).'" alt="nul_image.jpg">';
								                }
								            }else{
				            				 	$_data = '<img class="images_w_table" src="' . site_url('modules/purchase/uploads/nul_image.jpg' ).'" alt="nul_image.jpg">';
								            }

								            echo pur_html_entity_decode($_data);

								            ?>
				            			</td>
				            			<td><a href="<?php echo site_url('purchase/vendors_portal/detail_item/'.$p['id']); ?>"><?php echo pur_html_entity_decode($p['commodity_code'].' - '.$p['description']); ?></a></td>
				            			<td><?php echo pur_get_unit_name($p['unit_id']); ?></td>
				            			<td>
				            			<?php 
				            				$group_name = '';
				            				$group = get_group_name_pur($p['group_id']);

				            				if($group){
				            					$group_name = $group->name;
				            				}

				            				echo pur_html_entity_decode($group_name);
				            			 ?>
				            			</td>
				            			<td>
				            				<?php echo app_format_money($p['rate'], $base_currency->symbol); ?>
				            			</td>
				            			<td>
				            				<?php
				            					if($p['tax'] != '' && $p['tax'] != null && $p['tax'] != 0){
				            						$tax_name = $this->purchase_model->get_tax_name($p['tax']);
				            						echo _l('tax_1').': '.$tax_name;
				            					}

				            					if($p['tax2'] != '' && $p['tax2'] != null && $p['tax2'] != 0){
				            						$tax_name2 = $this->purchase_model->get_tax_name($p['tax2']);
				            						echo ' | '._l('tax_2').': '.$tax_name2;
				            					}
				            				 ?>
				            			</td>
				            			<td>
				            				<a href="<?php echo site_url('purchase/vendors_portal/detail_item/'.$p['id']); ?>" class="btn btn-icon btn-info"><i class="fa fa-eye"></i></a>
				            				<a href="<?php echo site_url('purchase/vendors_portal/add_update_items/'.$p['id']); ?>" class="btn btn-icon btn-warning" ><i class="fa fa-pencil"></i></a>
				            				<?php if($p['share_status'] == 0){ ?>
				            				<a href="<?php echo site_url('purchase/vendors_portal/share_item/'.$p['id']) ?>" class="btn btn-icon btn-success" data-toggle="tooltip" data-placement="top" title="<?php echo _l('share_to_client'); ?>"><i class="fa fa-share-alt "></i></a>
				            				<?php } ?>

				            				<a href="<?php echo site_url('purchase/vendors_portal/delete_vendor_item/'.$p['id']); ?>" class="btn btn-icon btn-danger _delete"><i class="fa fa-remove"></i></a>
				            			</td>
				            		</tr>
				            		
				            	<?php   } ?>
				            </tbody>
				         </table>
				     </div>

				    <div role="tabpanel" class="tab-pane" id="external_items">
				     	<table class="table dt-table" >
				            <thead>
				               <tr>
								  <th ><?php echo _l('pur_image'); ?></th>
				                  <th ><?php echo _l('pur_item'); ?></th>
				                  <th ><?php echo _l('unit'); ?></th>
				                  <th ><?php echo _l('pur_group'); ?></th>
				                  <th ><?php echo _l('pur_rate'); ?></th>
				                  <th ><?php echo _l('pur_tax'); ?></th>
				               </tr>
				            </thead>
				            <tbody>
				            	<?php $this->load->model('purchase/purchase_model');
				            	foreach($external_items as $p){ ?>
				            		<?php $_item = get_item_hp($p['items']); ?>
				            		<?php if($_item){ ?>

					            		<tr>
					            			<td>
					            				<?php
					            				$arr_images = $this->purchase_model->get_item_attachments($_item->id);
					            				$_data_img = '';
									            if(count($arr_images) > 0){
									                if(file_exists(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER .$arr_images[0]['rel_id'] .'/'.$arr_images[0]['file_name'])){
									                    $_data_img = '<img class="images_w_table" src="' . site_url('modules/purchase/uploads/item_img/' . $arr_images[0]['rel_id'] .'/'.$arr_images[0]['file_name']).'" alt="'.$arr_images[0]['file_name'] .'" >';
									                }else if(file_exists('modules/warehouse/uploads/item_img/' . $arr_images[0]['rel_id'] . '/' . $arr_images[0]['file_name'])){
									                    $_data_img = '<img class="images_w_table" src="' . site_url('modules/warehouse/uploads/item_img/' . $arr_images[0]['rel_id'] .'/'.$arr_images[0]['file_name']).'" alt="'.$arr_images[0]['file_name'] .'" >';
									                }else if(file_exists('modules/manufacturing/uploads/products/' . $arr_images[0]['rel_id'] . '/' . $arr_images[0]['file_name'])) {
									                    $_data_img = '<img class="images_w_table" src="' . site_url('modules/manufacturing/uploads/products/' . $arr_images[0]['rel_id'] . '/' . $arr_images[0]['file_name']) . '" alt="' . $arr_images[0]['file_name'] . '" >';
									                }else{
									                    
									                    $_data_img = '<img class="images_w_table" src="' . site_url('modules/purchase/uploads/nul_image.jpg' ).'" alt="nul_image.jpg">'; 
									                   
									                }

									            }else{
									                $_data_img = '<img class="images_w_table" src="' . site_url('modules/purchase/uploads/nul_image.jpg' ).'" alt="nul_image.jpg">';
									                if(is_numeric($_item->from_vendor_item)){
									                    $vendor_image = $this->purchase_model->get_vendor_item_file($_item->from_vendor_item);
									                    if(count($vendor_image) > 0){
									                        if(file_exists(PURCHASE_PATH.'vendor_items/' .$_item->from_vendor_item .'/'.$vendor_image[0]['file_name'])){
									                            $_data_img = '<img class="images_w_table" src="' . site_url('modules/purchase/uploads/vendor_items/' . $vendor_image[0]['rel_id'] .'/'.$vendor_image[0]['file_name']).'" alt="'.$vendor_image[0]['file_name'] .'" >';
									                        }
									                    }
									                }
									            }

									            echo html_entity_decode($_data_img);
					            				 ?>
					            			</td>
					            			<td><a href="<?php echo site_url('purchase/vendors_portal/detail_item/'.$p['items']); ?>"><?php echo pur_html_entity_decode($_item->commodity_code.' - '.$_item->description); ?></a></td>
					            			<td><?php echo pur_get_unit_name($_item->unit_id); ?></td>
					            			<td>
					            			<?php 
					            				$group_name = '';
					            				$group = get_group_name_pur($_item->group_id);

					            				if($group){
					            					$group_name = $group->name;
					            				}

					            				echo pur_html_entity_decode($group_name);
					            			 ?>
					            			</td>
					            			<td>
					            				<?php echo app_format_money($_item->purchase_price, $admin_currency->symbol); ?>
					            			</td>
					            			<td>
					            				<?php
					            					if($_item->tax != '' && $_item->tax != null && $_item->tax != 0){
					            						$tax_name = $this->purchase_model->get_tax_name($_item->tax);
					            						echo _l('tax_1').': '.$tax_name;
					            					}

					            					if($_item->tax2 != '' && $_item->tax2 != null && $_item->tax2 != 0){
					            						$tax_name2 = $this->purchase_model->get_tax_name($_item->tax2);
					            						echo ' | '._l('tax_2').': '.$tax_name2;
					            					}
					            				 ?>
					            			</td>
					            			
					            		</tr>
					            	<?php } ?>
				            		
				            	<?php   } ?>
				            </tbody>
				         </table>
				    </div>
				 </div>
			</div>
		</div>
	</div>
</div>
<?php hooks()->do_action('app_admin_footer'); ?>