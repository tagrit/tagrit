<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div class="row">
	<div class="col-md-12">
		<div class="panel_s">
			<div class="panel-body">
				<h4><?php echo pur_html_entity_decode($title) ?></h4>
				<hr>

				<?php echo form_open_multipart($this->uri->uri_string() , array('autocomplete'=>'off')) ?>

				<div class="row">
                    <div class="col-md-6">
                      <label for="commodity_code"><span class="text-danger">* </span><?php  echo _l('commodity_code'); ?></label>
                        <?php $commodity_code = isset($item) ? $item->commodity_code : '';
                        echo render_input('commodity_code', '', $commodity_code,'text', ['required' => true]); ?>
                    </div>
                    <div class="col-md-6">
                      <label for="commodity_name"><span class="text-danger">* </span><?php  echo _l('commodity_name'); ?></label>
                      <?php $description = isset($item) ? $item->description : '';
                      echo render_input('description', '', $description,'text', ['required' => true]); ?>
                    </div>
                </div>

                <div class="row">
                   <div class="col-md-4">
                         <?php $commodity_barcode = isset($item) ? $item->commodity_barcode : '';
                         echo render_input('commodity_barcode', 'commodity_barcode', $commodity_barcode,'text'); ?>
                    </div>
                  <div class="col-md-4">
                    <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle skucode-tooltip"  data-toggle="tooltip" title="" data-original-title="<?php echo _l('commodity_sku_code_tooltip'); ?>"></i></a>
                    <?php $sku_code = isset($item) ? $item->sku_code : '';
                    echo render_input('sku_code', 'sku_code', $sku_code,''); ?>
                  </div>
                  <div class="col-md-4">
                    <?php $sku_name = isset($item) ? $item->sku_name : '';
                    echo render_input('sku_name', 'sku_name', $sku_name); ?>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                        <?php $long_description = isset($item) ? $item->long_description : '';
                        echo render_textarea('long_description', 'description', $long_description); ?>
                  </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                         <?php $commodity_group = isset($item) ? $item->group_id : '';
                         echo render_select('group_id',$commodity_groups,array('id','name'),'commodity_group', $commodity_group); ?>
                    </div>
                     <div class="col-md-6">
                         <?php $sub_group = isset($item) ? $item->sub_group : '';
                         echo render_select('sub_group',$sub_groups,array('id','sub_group_name'),'sub_group', $sub_group); ?>
                    </div>
                </div>

                <div class="row">
	                <div class="col-md-3">
	                    <?php $unit_id = isset($item) ? $item->unit_id : '';
	                    echo render_select('unit_id',$units,array('unit_type_id','unit_name'),'units', $unit_id); ?>
	                </div>

	                <div class="col-md-3">
                      <label for="rate"><span class="text-danger">* </span><?php echo _l('rate'); ?></label>
                        <?php $rate = isset($item) ? $item->rate : '';
                        $attr = array();
                        $attr = ['required' => true, 'step' => 'any'];
                         echo render_input('rate', '', $rate, 'number', $attr); ?>
                    </div>
	                    
	                <div class="col-md-3">
	                    <?php $tax = isset($item) ? $item->tax : '';
	                    echo render_select('tax',$taxes,array('id','label'),'tax_1', $tax); ?>
	                </div>

	                <div class="col-md-3">
	                    <?php $tax2 = isset($item) ? $item->tax2 : '';
	                    echo render_select('tax2',$taxes,array('id','label'),'tax_2', $tax2); ?>
	                </div>
                </div>

                <div class="row">
 					<div class="col-md-12">
		              <div class="attachments">
		                <div class="attachment">
		                  <div class="mbot15">
		                    <div class="form-group">
		                      <label for="attachment" class="control-label"><?php echo _l('ticket_add_attachments'); ?></label>
		                      <div class="input-group">
		                        <input type="file" extension="jpg,png" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="attachments[0]" accept="image/*">
		                        <span class="input-group-btn">
		                          <button class="btn btn-success add_more_attachments p8-half" data-max="10" type="button"><i class="fa fa-plus"></i></button>
		                        </span>
		                      </div>
		                    </div>
		                  </div>
		                </div>
		              </div>
		            </div>              	
                </div>

                <?php if(isset($item) && count($files) > 0){ ?>
                    <table class="table mtop15 table-files" data-order-col="1" data-order-type="desc">
                       <thead>
                        <tr>
                            <th class="th-files-file"><?php echo _l('customer_attachments_file'); ?></th>
                            <th class="th-files-date-uploaded"><?php echo _l('file_date_uploaded'); ?></th>
                           
                            <th class="th-files-option"><?php echo _l('options'); ?></th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($files as $file){ ?>
                            <tr>
                                <td>
                                  <?php
                                  $url = site_url() .'download/file/client/';
                                  $path = get_upload_path_by_type('customer') . $file['rel_id'] . '/' . $file['file_name'];
                                  $is_image = false;
                                  if(!isset($file['external'])) {
                                    $attachment_url = $url . $file['attachment_key'];
                                    $is_image = is_image($path);
                                    $img_url = site_url('download/preview_image?path='.protected_file_url_by_path($path,true).'&type='.$file['filetype']);
                                } else if(isset($file['external']) && !empty($file['external'])){
                                    if(!empty($file['thumbnail_link'])){
                                        $is_image = true;
                                        $img_url = optimize_dropbox_thumbnail($file['thumbnail_link']);
                                    }
                                    $attachment_url = $file['external_link'];
                                }

                                $href_url = site_url(PURCHASE_PATH.'vendor_items/'.$file['rel_id'].'/'.$file['file_name']).'" download';
                                                                  if(!empty($file['external'])){
                                                                    $href_url = $file['external_link'];
                                                                  }

                                if($is_image){
                                    echo '<div class="preview_image">';
                                }
                                ?>
                                <a href="<?php echo $href_url; ?>"<?php echo (isset($file['external']) && !empty($file['external']) ? ' target="_blank"' : ''); ?>
                                class="display-block mbot5">
                                <?php if($is_image){ ?>
                                    <div class="table-image">
                                      <div class="text-center"><i class="fa fa-spinner fa-spin mtop30"></i></div>
                                      <img src="#" class="img-table-loading" data-orig="<?php echo $href_url; ?>">
                                  </div>
                              <?php } else { ?>
                                <i class="<?php echo get_mime_class($file['filetype']); ?>"></i> <?php echo $file['file_name']; ?>
                            <?php } ?>
                        </a>
                        <?php if($is_image){ echo '</div>'; } ?>
                    </td>
                    <td data-order="<?php echo $file['dateadded']; ?>"><?php echo _dt($file['dateadded']); ?></td>
                    
                        <td>
                          
                                <a href="<?php echo site_url('purchase/vendors_portal/delete_vendor_item_file/'.$file['id'].'/'.$item->id); ?>"
                                    class="btn btn-danger btn-icon _delete file-delete"><i class="fa fa-remove"></i></a>
                                
                            </td>
                        
                    </tr>
                <?php } ?>
            </tbody>
            </table>
                <?php } ?>

                <div class="footer">
                	<hr>
                	<button type="submit" class="btn btn-info pull-right" ><?php echo _l('submit'); ?></button>
                </div>

				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php hooks()->do_action('app_admin_footer'); ?>