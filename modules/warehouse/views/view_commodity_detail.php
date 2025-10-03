<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">               
                        <div class="clearfix"></div>
                           <h4>
                              <?php echo new_html_entity_decode($commodity_item->description); ?>
                           </h4>


                        <hr class="hr-panel-heading" /> 
                        <div class="clearfix"></div> 
                        <div class="col-md-12">

                         <div class="row col-md-12">

                            <h4 class="h4-color"><?php echo _l('general_infor'); ?></h4>
                            <hr class="hr-color">

                            
                            
                            <div class="col-md-7 panel-padding">
                              <table class="table border table-striped table-margintop">
                                  <tbody>

                                      <tr class="project-overview">
                                        <td class="bold" width="30%"><?php echo _l('commodity_code'); ?></td>
                                        <td><?php echo new_html_entity_decode($commodity_item->commodity_code) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('commodity_name'); ?></td>
                                        <td><?php echo new_html_entity_decode($commodity_item->description) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('commodity_group'); ?></td>
                                        <td><?php echo get_wh_group_name(new_html_entity_decode($commodity_item->group_id)) != null ? get_wh_group_name(new_html_entity_decode($commodity_item->group_id))->name : '' ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('commodity_barcode'); ?></td>
                                        <td><?php echo new_html_entity_decode($commodity_item->commodity_barcode) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('sku_code'); ?></td>
                                        <td><?php echo new_html_entity_decode($commodity_item->sku_code) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('sku_name'); ?></td>
                                        <td><?php echo new_html_entity_decode($commodity_item->sku_name) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('tax_1'); ?></td>
                                        <td><?php echo new_html_entity_decode($commodity_item->tax) != '' && get_tax_rate($commodity_item->tax) != null ? get_tax_rate($commodity_item->tax)->name : '';  ?></td>
                                     </tr> 
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('tax_2'); ?></td>
                                        <td><?php echo new_html_entity_decode($commodity_item->tax2) != '' && get_tax_rate($commodity_item->tax2) != null ? get_tax_rate($commodity_item->tax2)->name : '';  ?></td>
                                     </tr> 
                                     
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('tags'); ?></td>
                                        <td>
                                          <div class="form-group">
                                            <div id="inputTagsWrapper">
                                               <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($commodity_item) ? prep_tags_input(get_tags_in($commodity_item->id,'item_tags')) : ''); ?>" data-role="tagsinput">
                                            </div>
                                          </div>

                                        </td>
                                     </tr>

                                    

                                    </tbody>
                              </table>
                          </div>

                            <div class="gallery">
                                <div class="wrapper-masonry">
                                  <div id="masonry" class="masonry-layout columns-3">
                                <?php if(isset($commodity_file) && count($commodity_file) > 0){ ?>
                                  <?php foreach ($commodity_file as $key => $value) { ?>

                                      <?php if(file_exists(WAREHOUSE_ITEM_UPLOAD .$value["rel_id"].'/'.$value["file_name"])){ ?>
                                          <a  class="images_w_table" href="<?php echo site_url('modules/warehouse/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/warehouse/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo new_html_entity_decode($value['file_name']) ?>"/></a>
                                           
                                        <?php }elseif(file_exists('modules/purchase/uploads/item_img/' . $value["rel_id"] . '/' . $value["file_name"])) { ?>
                                          <a  class="images_w_table" href="<?php echo site_url('modules/purchase/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/purchase/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo new_html_entity_decode($value['file_name']) ?>"/></a>
                                            

                                       <?php }elseif(file_exists('modules/manufacturing/uploads/products/' . $value["rel_id"] . '/' . $value["file_name"])){ ?>
                                          <a  class="images_w_table" href="<?php echo site_url('modules/manufacturing/uploads/products/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/manufacturing/uploads/products/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo new_html_entity_decode($value['file_name']) ?>"/></a>
                                          
                                        <?php }else{ ?>
                                          
                                           <a  href="<?php echo site_url('modules/warehouse/uploads/nul_image.jpg'); ?>"><img class="images_w_table" src="<?php echo site_url('modules/warehouse/uploads/nul_image.jpg'); ?>" alt="nul_image.jpg"/></a>
                                        <?php } ?>


                                <?php } ?>
                              <?php }else{ ?>
                                <?php  if(isset($vendor_image) && count($vendor_image) == 0){  ?>
                                    <a  href="<?php echo site_url('modules/warehouse/uploads/nul_image.jpg'); ?>"><img class="images_w_table" src="<?php echo site_url('modules/warehouse/uploads/nul_image.jpg'); ?>" alt="nul_image.jpg"/></a>
                                <?php } ?>

                              <?php } ?>

                              <?php 
                              $_img = ''; 
                              if(isset($vendor_image) && count($vendor_image) > 0){ 
                                foreach($vendor_image as $value){
                                  if(file_exists(PURCHASE_PATH.'vendor_items/' .$commodity_item->from_vendor_item .'/'.$value['file_name'])){
                                    $_img .= '<a  class="images_w_table" href="'.site_url('modules/purchase/uploads/vendor_items/'.$value["rel_id"].'/'.$value["file_name"]).'"><img class="images_w_table" src="'. site_url('modules/purchase/uploads/vendor_items/'.$value["rel_id"].'/'.$value["file_name"]).'" alt="'. pur_html_entity_decode($value['file_name']).'"/></a>';
                                }
                            }
                            echo $_img;
                        }

                        ?>
                                <div class="clear"></div>
                              </div>
                            </div>
                            </div>
                            <br>
                        </div>


                         <h4 class="h4-color"><?php echo _l('infor_detail'); ?></h4>
                          <hr class="hr-color">
                          <div class="col-md-6 panel-padding" >
                            <table class="table border table-striped table-margintop" >
                                <tbody>
                                   <tr class="project-overview">
                                      <td class="bold td-width"><?php echo _l('origin'); ?></td>
                                        <td><?php echo new_html_entity_decode($commodity_item->origin) ; ?></td>
                                   </tr>
                                   <tr class="project-overview">
                                      <td class="bold"><?php echo _l('colors'); ?></td>
                                        <?php
                                    $color_value ='';
                                    if($commodity_item->color){
                                      $color = get_color_type($commodity_item->color);
                                      if($color){
                                        $color_value .= $color->color_code.'_'.$color->color_name;
                                      }
                                    }
                                     ?>
                                      <td><?php echo new_html_entity_decode($color_value) ; ?></td>
                                   </tr>
                                   <tr class="project-overview">
                                      <td class="bold"><?php echo _l('styles'); ?></td>
                                    <td><?php  if($commodity_item->style_id != null){ echo get_style_name(new_html_entity_decode($commodity_item->style_id)) != null ? get_style_name(new_html_entity_decode($commodity_item->style_id))->style_name : '';}else{echo '';} ?></td>
                                   </tr>

                                    <tr class="project-overview">
                                      <td class="bold"><?php echo _l('rate'); ?></td>
                                      <td><?php echo app_format_money((float)$commodity_item->rate,'') ; ?></td>
                                   </tr>

                                   <tr class="project-overview">
                                      <td class="bold"><?php echo _l('_profit_rate_p'); ?></td>
                                      <td><?php echo new_html_entity_decode($commodity_item->profif_ratio) ; ?></td>
                                   </tr>
                                   

                                </tbody>
                            </table>
                          </div>
                           
                          <div class="col-md-6 panel-padding" >
                            <table class="table table-striped table-margintop">
                                <tbody>
                                   <tr class="project-overview">
                                      <td class="bold" width="40%"><?php echo _l('model_id'); ?></td>
                                       <td><?php if($commodity_item->style_id != null){ echo get_model_name(new_html_entity_decode($commodity_item->model_id)) != null ? get_model_name(new_html_entity_decode($commodity_item->model_id))->body_name : ''; }else{echo '';}?></td>
                                   </tr>
                                   <tr class="project-overview">
                                      <td class="bold"><?php echo _l('size_id'); ?></td>

                                      <td><?php if($commodity_item->style_id != null){ echo get_size_name(new_html_entity_decode($commodity_item->size_id)) != null ? get_size_name(new_html_entity_decode($commodity_item->size_id))->size_name : ''; }else{ echo '';}?></td>
                                   </tr>
                                   
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('unit_id'); ?></td>
                                        <td><?php echo  $commodity_item->unit_id != '' && get_unit_type($commodity_item->unit_id) != null ? get_unit_type($commodity_item->unit_id)->unit_name : ''; ?></td>
                                     </tr> 

                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('purchase_price'); ?></td>
                                        <td><?php echo app_format_money((float)$commodity_item->purchase_price,'') ; ?></td>
                                     </tr>

                                      <tr class="project-overview">
                                        <td class="bold"><?php echo _l('guarantee'); ?></td>
                                        <td><?php echo new_html_entity_decode($commodity_item->guarantee) ._l('month_label'); ?></td>
                                      </tr>
                                     
                                  
                                  
                                  </tbody>
                                </table>
                          </div>
                          <div class=" row ">
                            <div class="col-md-12">
                             <h4 class="h4-color"><?php echo _l('description'); ?></h4>
                            <hr class="hr-color">
                            <h5><?php echo new_html_entity_decode($commodity_item->long_description) ; ?></h5>
                              
                            </div>
                              
                          </div>
                          
                          <div class=" row ">
                            <div class="col-md-12">
                             <h4 class="h4-color"><?php echo _l('long_description'); ?></h4>
                            <hr class="hr-color">
                            <h5><?php echo new_html_entity_decode($commodity_item->long_descriptions) ; ?></h5>
                              
                            </div>
                              
                          </div>

                                        <div class="horizontal-scrollable-tabs preview-tabs-top">
                                          <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                                            <div class="horizontal-tabs">
                                              <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">

                                                  <li role="presentation" class="active">
                                                     <a href="#out_of_stock" aria-controls="out_of_stock" role="tab" id="tab_out_of_stock" data-toggle="tab">
                                                        <?php echo _l('inventory_stock') ?>
                                                     </a>
                                                  </li>

                                                  <li role="presentation" >
                                                     <a href="#expiry_date" aria-controls="expiry_date" role="tab" id="tab_expiry_date" data-toggle="tab">
                                                        <?php echo _l('expiry_date') ?>
                                                     </a>
                                                  </li>

                                                  <li role="presentation">
                                                     <a href="#history" aria-controls="history" role="tab" id="tab_history" data-toggle="tab">
                                                        <?php echo _l('transaction_history') ?>
                                                     </a>
                                                  </li> 

                                                  <li role="presentation">
                                                     <a href="#custom_fields" aria-controls="custom_fields" role="tab" id="tab_custom_fields" data-toggle="tab">
                                                        <?php echo _l('custom_fields') ?>
                                                     </a>
                                                  </li>

                                                  <li role="presentation">
                                                     <a href="#child_items" aria-controls="child_items" role="tab" id="tab_child_items" data-toggle="tab">
                                                        <?php echo _l('sub_items') ?>
                                                     </a>
                                                  </li>  
                                                                      
                                              </ul>
                                              </div>
                                          </div>

                                          <div class="tab-content col-md-12">

                                            <div role="tabpanel" class="tab-pane active row" id="out_of_stock">
                                                <?php render_datatable(array(
                                                 _l('id'),
                                                  _l('commodity_name'),
                                                  _l('expiry_date'),
                                                  _l('lot_number'),
                                                  _l('warehouse_name'),
                                              
                                                  _l('inventory_number'),
                                                  _l('unit_name'),
                                                  _l('rate'),
                                                  _l('purchase_price'),
                                                  _l('tax'),
                                                  _l('status_label'),
                                                 
                                                  ),'table_inventory_stock'); ?>
                                            </div>

                                            <div role="tabpanel" class="tab-pane  row" id="expiry_date">
                                                    <?php render_datatable(array(
                                                  _l('commodity_name'),
                                                  _l('expiry_date'),
                                                  _l('lot_number'),
                                                  _l('warehouse_name'),
                                              
                                                  _l('inventory_number'),
                                                  _l('unit_name'),
                                                  _l('rate'),
                                                  _l('purchase_price'),
                                                  _l('tax'),
                                                  _l('status_label'),
                                                 
                                                  ),'table_view_commodity_detail',['proposal_sm' => 'proposal_sm']); ?>
                                            </div>

                                            <div role="tabpanel" class="tab-pane row" id="history">
                                                <?php render_datatable(array(
                                              _l('id'),
                                              _l('form_code'),
                                              _l('commodity_code'),
                                              _l('warehouse_code'),
                                              _l('warehouse_name'),
                                              _l('day_vouchers'),
                                              _l('opening_stock'),
                                              _l('closing_stock'),
                                              _l('lot_number').'/'._l('quantity'),
                                              _l('expiry_date'),
                                              _l('wh_serial_number'),
                                              _l('note'),
                                              _l('status_label'),
                                              ),'table_warehouse_history'); ?>
                                            </div>  

                                            <div role="tabpanel" class="tab-pane row" id="custom_fields">
                                              <?php echo render_custom_fields('items',$commodity_item->id,[],['items_pr' => true]); ?>
                                            </div>


                                            <!-- child item -->
                                            <div role="tabpanel" class="tab-pane" id="child_items">
                                              <div class="row">
                                                <div class="col-md-12">
                                                  <div class="col-md-4 ">
                                                    <?php if (has_permission('warehouse_item', '', 'create') || is_admin() || has_permission('warehouse_item', '', 'edit') ) { ?>

                                                    <a href="#" id="dowload_items"  class="btn btn-warning pull-left  mr-4 button-margin-r-b hide"><?php echo _l('dowload_items'); ?></a>

                                                    <?php } ?>
                                                </div>

                                                </div>  
                                                <div class="col-md-12">

                                                   <!-- view/manage -->            
                      <div class="modal bulk_actions" id="table_commodity_list_bulk_actions" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              </div>
                              <div class="modal-body">
                                 <?php if(has_permission('warehouse_item','','delete') || is_admin()){ ?>
                                 <div class="checkbox checkbox-danger">
                                    <input type="checkbox" name="mass_delete" id="mass_delete">
                                    <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                 </div>
                                
                                 <?php } ?>
                              </div>
                              <div class="modal-footer">
                                 <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

                                 <?php if(has_permission('warehouse_item','','delete') || is_admin()){ ?>
                                 <a href="#" class="btn btn-info" onclick="warehouse_delete_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                  <?php } ?>
                              </div>
                           </div>
                          
                        </div>
                        
                     </div>

                     <!-- update multiple item -->

                     <div class="modal export_item" id="table_commodity_list_export_item" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <h4 class="modal-title"><?php echo _l('export_item'); ?></h4>
                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              </div>
                              <div class="modal-body">
                                 <?php if(has_permission('warehouse_item','','create') || is_admin()){ ?>
                                 <div class="checkbox checkbox-danger">
                                    <input type="checkbox" name="mass_delete" id="mass_delete">
                                    <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                 </div>
                                
                                 <?php } ?>
                              </div>
                              <div class="modal-footer">
                                 <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

                                 <?php if(has_permission('warehouse_item','','create') || is_admin()){ ?>
                                 <a href="#" class="btn btn-info" onclick="warehouse_delete_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                  <?php } ?>
                              </div>
                           </div>
                          
                        </div>
                        
                     </div>

                       <!-- print barcode -->      
                       <?php echo form_open_multipart(admin_url('warehouse/item_print_barcode'), array('id'=>'item_print_barcode')); ?>      
                      <div class="modal bulk_actions" id="table_commodity_list_print_barcode" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <h4 class="modal-title"><?php echo _l('print_barcode'); ?></h4>
                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              </div>
                              <div class="modal-body">
                                 <?php if(has_permission('warehouse_item','','create') || is_admin()){ ?>

                                 <div class="row">
                                   <div class="col-md-6">
                                      <div class="form-group">
                                          <div class="radio radio-primary radio-inline" >
                                              <input onchange="print_barcode_option(this); return false" type="radio" id="y_opt_1_" name="select_item" value="0" checked >
                                              <label for="y_opt_1_"><?php echo _l('select_all'); ?></label>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <div class="radio radio-primary radio-inline" >
                                              <input onchange="print_barcode_option(this); return false" type="radio" id="y_opt_2_" name="select_item" value="1" >
                                              <label for="y_opt_2_"><?php echo _l('select_item'); ?></label>
                                          </div>
                                    </div>
                                  </div>
                                 </div>     

                                 <div class="row display-select-item hide ">
                                  <div class=" col-md-12">
                                    <?php $this->load->view('warehouse/item_include/item_select', ['select_name' => 'item_select_print_barcode[]', 'id_name' => 'item_select_print_barcode', 'multiple' => true, 'data_none_selected_text' => 'select_item_print_barcode']); ?>
                                  </div>
                                  </div>
                                
                                 <?php } ?>
                              </div>
                              <div class="modal-footer">
                                 <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

                                 <?php if(has_permission('warehouse_item','','create') || is_admin()){ ?>

                                 <button type="submit" class="btn btn-info" ><?php echo _l('confirm'); ?></button>
                                  <?php } ?>
                              </div>
                           </div>
                        </div>
                     </div>
                      <?php echo form_close(); ?>

                      <?php if(has_permission('warehouse_item', '', 'edit') || has_permission('warehouse_item', '', 'delete') ){ ?>

                       <a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_commodity_list" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>
                   <?php } ?>

                   <?php if(has_permission('warehouse_item', '', 'edit') || has_permission('warehouse_item', '', 'create') ){ ?>

                       <a href="#"  onclick="staff_export_item(); return false;" data-toggle="modal" data-table=".table-table_commodity_list" data-target="#leads_export_item" class=" hide bulk-actions-btn table-btn"><?php echo _l('export_item'); ?></a>

                       <a href="#"  onclick="print_barcode_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_commodity_list" data-target="#print_barcode_item" class=" hide print_barcode-bulk-actions-btn table-btn"><?php echo _l('print_barcode'); ?></a>
                   <?php } ?>
                     

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
                                          _l('tax'),
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
        </div>
    </div>
</div>

  <!-- add one commodity list sibar start-->       

    <div class="modal" id="commodity_list-add-edit" tabindex="-1" role="dialog">
    <div class="modal-dialog ht-dialog-width">

        <?php echo form_open_multipart(admin_url('warehouse/commodity_list_add_edit'),array('class'=>'commodity_list-add-edit','autocomplete'=>'off')); ?>

      <div class="modal-content">

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">
                    <span class="edit-commodity-title"><?php echo _l('edit_item'); ?></span>
                    <span class="add-commodity-title"><?php echo _l('add_item'); ?></span>
                </h4>
            </div>

            <div class="modal-body">
                <div id="commodity_item_id"></div>


                <div class="horizontal-scrollable-tabs preview-tabs-top">
                  <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                  <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                  <div class="horizontal-tabs">
                  <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                   <li role="presentation" class="active">
                       <a href="#interview_infor" aria-controls="interview_infor" role="tab" data-toggle="tab" aria-controls="interview_infor">
                       <span class="glyphicon glyphicon-align-justify"></span>&nbsp;<?php echo _l('general_infor'); ?>
                       </a>
                    </li>
                    <li role="presentation">
                       <a href="#interview_evaluate" aria-controls="interview_evaluate" role="tab" data-toggle="tab" aria-controls="interview_evaluate">
                       <i class="fa fa-group"></i>&nbsp;<?php echo _l('properties'); ?>
                       </a>
                    </li>

                    <!-- TODO -->
                    <li role="presentation">
                       <a href="#variation" aria-controls="variation" role="tab" data-toggle="tab" aria-controls="variation">
                       <i class="fa fa-bars menu-icon"></i>&nbsp;<?php echo _l('variation'); ?>
                       </a>
                    </li>

                    <li role="presentation">
                       <a href="#custom_fields_sub" aria-controls="custom_fields_sub" role="tab" data-toggle="tab" aria-controls="custom_fields_sub">
                       <i class="fa fa-bars menu-icon"></i>&nbsp;<?php echo _l('custom_fields'); ?>
                       </a>
                    </li>
                    
                    
                   </ul>
                 </div>
               </div>

               <div class="tab-content">
              
                <!-- interview process start -->
                  <div role="tabpanel" class="tab-pane active" id="interview_infor">
                        <div class="row">
                          <div class=" col-md-12">
                            <div class="form-group">
                              <label for="parent_id" class="control-label"><?php echo _l('parent_item'); ?></label>
                              <select name="parent_id" id="parent_id" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="">
                                
                              </select>
                            </div>

                          </div>
                        </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <?php echo render_input('commodity_code', 'commodity_code'); ?>
                                </div>
                                <div class="col-md-6">
                                  <?php echo render_input('description', 'commodity_name'); ?>
                                </div>
                                
                            </div>

                            <div class="row">
                               <div class="col-md-4">
                                     <?php echo render_input('commodity_barcode', 'commodity_barcode','','text'); ?>
                                </div>
                              <div class="col-md-4">
                                <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle skucode-tooltip"  data-toggle="tooltip" title="" data-original-title="<?php echo _l('commodity_sku_code_tooltip'); ?>"></i></a>
                                <?php echo render_input('sku_code', 'sku_code','',''); ?>
                              </div>
                              <div class="col-md-4">
                                <?php echo render_input('sku_name', 'sku_name'); ?>
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-md-12">
                                  <div class="form-group" id="tags_value">
                                    <div id="inputTagsWrapper">
                                       <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
                                       <input type="text" class="tagsinput" id="tags" name="tags" value="" data-role="tagsinput">
                                    </div>
                                 </div>

                              </div>
                            </div>  

                            <div class="row">
                              <div class="col-md-12">
                                    <?php echo render_textarea('long_description', 'description'); ?>
                              </div>
                            </div>

                            <!--  add warehouse for item-->
                            <div class="row">
                              <div class="col-md-12">
                                  <?php echo render_select('warehouse_id',$warehouses,array('warehouse_id',array('warehouse_code','warehouse_name')),'warehouse_name'); ?>
                              </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                     <?php echo render_select('commodity_type',$commodity_types,array('commodity_type_id','commondity_name'),'commodity_type'); ?>

                                </div>
                                 <div class="col-md-6">
                                     <?php echo render_select('unit_id',$units,array('unit_type_id','unit_name'),'units'); ?>
                                </div>
                            </div>


                             <div class="row">
                              
                                <div class="col-md-6">
                                     <?php echo render_select('group_id',$commodity_groups,array('id','name'),'commodity_group'); ?>
                                </div>
                                 <div class="col-md-6">
                                     <?php echo render_select('sub_group',$sub_groups,array('id','sub_group_name'),'sub_group'); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                  <?php 
                                    $attr = array();
                                   
                                   ?>
                                     <?php echo render_input('profif_ratio','_profit_rate_p','','number',$attr); ?>
                                </div>
                                <div class="col-md-6">
                                     <?php echo render_select('tax',$taxes,array('id','name'),'taxes'); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">

                                    <?php 
                                    $attr = array();
                                    $attr = ['data-type' => 'currency'];
                                     echo render_input('purchase_price', 'purchase_price','', 'text', $attr); ?>
                                  
                                </div>
                                <div class="col-md-6">

                                     <?php $premium_rates = isset($premium_rates) ? $premium_rates : '' ?>
                                    <?php 
                                    $attr = array();
                                     $attr = ['data-type' => 'currency'];
                                     echo render_input('rate', 'rate','', 'text', $attr); ?>


                                </div>
                            </div>

                            <?php if(!isset($expense) || (isset($expense) && $expense->attachment == '')){ ?>
                            <div id="dropzoneDragArea" class="dz-default dz-message">
                               <span><?php echo _l('attach_images'); ?></span>
                            </div>
                            <div class="dropzone-previews"></div>
                            <?php } ?>

                            <div id="images_old_preview">
                              
                            </div>

                        
                  </div>
               
                  <div role="tabpanel" class="tab-pane" id="interview_evaluate">
                    <div class="row">
                    <div class="col-md-12">
                     <div id="additional_criteria"></div>   
                     <div class="form">

                        <div class="row">
                            <div class="col-md-6">
                                <?php echo render_input('origin', 'origin'); ?>
                            </div>
                            <div class="col-md-6">
                                 <?php echo render_select('style_id',$styles,array('style_type_id','style_name'),'styles'); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                 <?php echo render_select('model_id',$models,array('body_type_id','body_name'),'model_id'); ?>
                            </div>
                            <div class="col-md-6">
                                 <?php echo render_select('size_id',$sizes,array('size_type_id','size_name'),'sizes'); ?>
                            </div>
                        </div>

                        <div class="row">
                          <div class="col-md-6">
                            <?php echo render_select('color',$colors,array('color_id',array('color_hex','color_name')),'_color'); ?>
                          </div>
                          <div class="col-md-6">
                            <?php $attr = array();
                                  $attr = ['min' => 0, 'step' => 1]; ?>

                            <?php echo render_input('guarantee','guarantee_month','', 'number', $attr); ?>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                <div class="checkbox checkbox-primary">
                                  <input  type="checkbox" id="without_checking_warehouse" name="without_checking_warehouse" value="without_checking_warehouse">

                                  <label for="without_checking_warehouse"><?php echo _l('without_checking_warehouse'); ?><small ><?php echo _l('without_checking_warehouse_tooltip') ?> </small>
                                  </label>
                                </div>
                              </div>
                          </div>
                          <div class="col-md-3 col-sm-6">
                           <div class="form-group">
                            <div class="checkbox checkbox-primary">
                              <input  type="checkbox" id="can_be_sold" name="can_be_sold" value="can_be_sold" >
                              <label for="can_be_sold"><?php echo _l('can_be_sold'); ?></label>
                            </div>
                            <div class="checkbox checkbox-primary <?php if(!get_status_modules_wh('purchase')){echo ' hide';} ?>">
                              <input  type="checkbox" id="can_be_purchased" name="can_be_purchased" value="can_be_purchased" >
                              <label for="can_be_purchased"><?php echo _l('can_be_purchased'); ?></label>
                            </div>
                            
                          </div>
                        </div>  
                        <div class="col-md-3 col-sm-6">
                          <div class="form-group">
                            <div class="checkbox checkbox-primary">
                              <input  type="checkbox" id="can_be_inventory" name="can_be_inventory" value="can_be_inventory" >
                              <label for="can_be_inventory"><?php echo _l('can_be_inventory'); ?></label>
                            </div>
                            <div class="checkbox checkbox-primary <?php if(!get_status_modules_wh('manufacturing')){echo ' hide';} ?>">
                              <input  type="checkbox" id="can_be_manufacturing" name="can_be_manufacturing" value="can_be_manufacturing" >
                              <label for="can_be_manufacturing"><?php echo _l('can_be_manufacturing'); ?></label>
                            </div>
                          </div>
                        </div>    
                        </div>  

                        

                        <div class="row">
                          <div class="col-md-12 ">
                              <p class="bold"><?php echo _l('long_description'); ?></p>
                              <?php echo render_textarea('long_descriptions','','',array(),array(),'','tinymce'); ?>
                                  
                          </div>
                        </div>
                       
                        

                    </div>
                    </div>
                    </div>

                  </div>

                  <!-- TODO -->
                  <!-- variation -->
                  <div role="tabpanel" class="tab-pane " id="variation">
                      <div class="list_approve">
                        <div id="item_approve">
                          <div class="col-md-11">

                            <div class="col-md-4">
                              <?php echo render_input('name[0]','variation_name','', 'text'); ?>
                           </div>
                           <div class="col-md-8">
                            <div class="options_wrapper">
                            <?php 
                              $variation_attr =[];
                              $variation_attr['row'] = '1';
                             ?>
                            <span class="pull-left fa fa-question-circle" data-toggle="tooltip" title="" data-original-title="Populate the field by separating the options by coma. eq. apple,orange,banana"></span>
                            <?php echo render_textarea('options[0]', 'variation_options', '', $variation_attr); ?>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-1 new_vendor_requests_button">
                          <span class="pull-bot">
                            <button name="add" class="btn new_wh_approval btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- custome fields -->
                  <div role="tabpanel" class="tab-pane" id="custom_fields_sub">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form">

                          <div id="custom_fields_items">
                            <?php echo render_custom_fields('items'); ?>
                          </div>

                        </div>
                     </div>
                   </div>
                 </div>


              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close') ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('save'); ?></button>
            </div>
          </div>

          </div>
        </div>
            <?php echo form_close(); ?>

<!-- add one commodity list sibar end -->  
<div id="modal_wrapper"></div>


<?php echo form_hidden('commodity_id'); ?>
<?php echo form_hidden('parent_item_filter', 'false'); ?>


<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/view_commodity_detail_js.php';?>
<?php require 'modules/warehouse/assets/js/commodity_detail_js.php';?>
<?php require 'modules/warehouse/assets/js/sub_commodity_list_js.php';?>

</body>
</html>

