<?php init_head(); ?>


<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12" id="small-table">
            <div class="panel_s">
               <div class="panel-body">
                <?php echo form_hidden('proposal_id',$proposal_id); ?>
                  <div class="row">
                     <div class="col-md-12">
                      <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                      <br>

                    </div>
                  </div>

                  <div class="row row-margin-bottom">
                    <div class="col-md-12 ">
                        <?php if (has_permission('warehouse_item', '', 'create') || is_admin() || has_permission('warehouse_item', '', 'edit') ) { ?>

                          
                        <a href="#" onclick="new_commodity_item(); return false;" class="btn btn-info pull-left display-block mr-4 button-margin-r-b" data-toggle="sidebar-right" data-target=".commodity_list-add-edit-modal">
                            <?php echo _l('add'); ?>
                        </a>

                        <a href="<?php echo admin_url('warehouse/import_xlsx_commodity'); ?>" class="btn btn-success pull-left display-block  mr-4 button-margin-r-b" title="<?php echo _l('import_items') ?> ">
                            <?php echo _l('import_items'); ?>
                        </a>

                        <a href="#" id="dowload_items"  class="btn btn-warning pull-left  mr-4 button-margin-r-b hide"><?php echo _l('dowload_items'); ?></a>

                        <a href="<?php echo admin_url('warehouse/import_opening_stock'); ?>" class="btn btn-default pull-left display-block  mr-4 button-margin-r-b" title="<?php echo _l('import_opening_stock') ?> ">
                            <?php echo _l('import_opening_stock'); ?>
                        </a>
                        <?php if(get_option('wh_products_by_serial')){ ?>
                          <div class="btn-group">
                            <a href="#" class="btn btn-default pull-left mr-4 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('wh_serial_numbers').' '; ?><span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right">
                              <li class="hidden-xs"><a href="<?php echo admin_url('warehouse/import_serial_number'); ?>">
                                <?php echo _l('wh_import_serial_numbers'); ?></a>
                              </li>
                              <li class="hidden-xs"><a href="<?php echo admin_url('warehouse/manage_serial_number'); ?>">
                                <?php echo _l('wh_manage_serial_number'); ?></a>
                              </li>
                            </ul>
                          </div>

                        <?php } ?>

                        <?php } ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class=" col-md-3">
                      <div class="form-group">
                      <select name="warehouse_filter[]" id="warehouse_filter" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('warehouse_filter'); ?>">

                          <?php foreach($warehouse_filter as $warehouse) { ?>
                            <option value="<?php echo new_html_entity_decode($warehouse['warehouse_id']); ?>"><?php echo new_html_entity_decode($warehouse['warehouse_name']); ?></option>
                            <?php } ?>
                        </select>
                        </div>
                    </div>
                    <div class=" col-md-3">
                      <?php $this->load->view('warehouse/item_include/item_select', ['select_name' => 'commodity_filter[]', 'id_name' => 'commodity_filter', 'multiple' => true, 'data_none_selected_text' => 'commodity']); ?>
                    </div>
                    <div class=" col-md-2">
                      <div class="form-group">

                      <select name="item_filter[]" id="item_filter" class="selectpicker" multiple="true"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('tags'); ?>">

                           <?php foreach($item_tags as $item_f) { ?>
                            <option value="<?php echo new_html_entity_decode($item_f['id']); ?>"><?php echo new_html_entity_decode($item_f['name']); ?></option>
                            <?php } ?>

                        </select>
                      </div>
                    </div>

                    <div class=" col-md-2">
                      <div class="form-group">
                      <select name="alert_filter" id="alert_filter" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('alert_filter'); ?>">

                            <option value=""></option>
                            <option value="3"><?php echo _l('minimum_stock') ; ?></option>
                            <option value="4"><?php echo _l('maximum_stock') ; ?></option>
                            <option value="1"><?php echo _l('out_of_stock') ; ?></option>
                            <option value="2"><?php echo _l('1_month_before_expiration_date') ; ?></option>

                        </select>
                      </div>
                    </div>
                    <?php 
                    $can_be_type = [];
                    $can_be_type[] = [
                      'id' => 'can_be_sold',
                      'label' => _l('can_be_sold'),
                    ];
                    $can_be_type[] = [
                      'id' => 'can_be_purchased',
                      'label' => _l('can_be_purchased'),
                    ];
                    $can_be_type[] = [
                      'id' => 'can_be_manufacturing',
                      'label' => _l('can_be_manufacturing'),
                    ];
                    $can_be_type[] = [
                      'id' => 'can_be_inventory',
                      'label' => _l('can_be_inventory'),
                    ];
                    

                    ?>
                    <div class="col-md-2">
                      <?php echo render_select('can_be_value_filter[]', $can_be_type, array('id', array('label')), '', ['can_be_inventory'], ['multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker'], array(), '', '', false); ?>
                    </div>
                   
                    </div>

                    <div class="row">
                    <!-- view/manage -->            
                      <div class="modal bulk_actions" id="table_commodity_list_bulk_actions" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                 <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                              </div>
                              <div class="modal-body">
                                 <div class="checkbox checkbox-danger">
                                  <div class="row">

                                 <?php if(has_permission('warehouse_item','','delete') || is_admin()){ ?>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <input type="checkbox" name="mass_delete" id="mass_delete">
                                      <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                    </div>
                                  </div>
                                 <?php } ?>

                                   
                                  </div>

                                  <div class="row">
                                    <?php if(has_permission('warehouse_item','','create') || is_admin()){ ?>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <input type="checkbox" name="clone_items" id="clone_items">
                                      <label for="clone_items"><?php echo _l('clone_this_items'); ?></label>
                                    </div>
                                  </div>
                                 <?php } ?>
                                    
                                  </div>

                                 <?php if(has_permission('warehouse_item','','edit') || is_admin()){ ?>
                                  <div class="row">
                                    <div class="col-md-5">
                                      <div class="form-group">

                                        <input type="checkbox" name="change_item_selling_price" id="change_item_selling_price" >
                                        <label for="change_item_selling_price"><?php echo _l('change_item_selling_price'); ?></label>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">

                                        <div class="input-group" id="discount-total">
                                          <input type="number" class="form-control text-right" min="0" max="100" name="selling_price" value="">
                                          <div class="input-group-addon">
                                            <div class="dropdown">
                                             <span class="discount-type-selected">
                                              %
                                            </span>
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-md-5">
                                    <div class="form-group">

                                      <input type="checkbox" name="change_item_purchase_price" id="change_item_purchase_price">
                                      <label for="change_item_purchase_price"><?php echo _l('change_item_purchase_price'); ?></label>
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">

                                      <div class="input-group" id="discount-total">
                                        <input type="number" class="form-control text-right" min="0" max="100" name="b_purchase_price" value="">
                                        <div class="input-group-addon">
                                          <div class="dropdown">
                                           <span class="discount-type-selected">
                                            %
                                          </span>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                                 <?php } ?>
                                 
                                 </div>
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
                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                 <h4 class="modal-title"><?php echo _l('print_barcode'); ?></h4>
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

                                  <div class="row">
                                    <div class=" col-md-12">
                                      <?php echo render_input('print_qty', 'wh_print_qty', 1); ?>
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

                     <!-- search all simple product and variation product -->
                     <div class="row">
                       <div class="col-md-12">
                        <div class="col-md-3">
                          <?php echo render_select('group_filter[]', $commodity_groups, array('id', 'name'), '', '', ['data-none-selected-text' => _l('commodity_group'), 'multiple' => true, 'data-live-search' => true], [], '', '', false); ?>
                        </div>
                        <div class="col-md-3">
                          <?php echo render_select('sub_group_filter[]', $sub_groups, array('id', array('sub_group_code', 'sub_group_name') ), '', '', ['data-none-selected-text' => _l('sub_group'), 'multiple' => true, 'data-live-search' => true], [], '', '', false); ?>
                          
                        </div>
                        <div class="col-md-3 pull-right">
                          <?php echo render_input('barcode_filter', '', '', '', ['placeholder' => _l('wh_barcode_filter')]); ?>
                        </div>
                        <div class="form-group pull-right">
                          <div class="checkbox checkbox-primary">
                            <input  type="checkbox" id="filter_all_simple_variation" name="filter_all_simple_variation" >
                            <label for="filter_all_simple_variation"><?php echo _l('search_all_simple_variation_product'); ?> <i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('search_all_simple_variation_tooltip'); ?>"></i>
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>

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
         <div class="col-md-7 small-table-right-col">
            <div id="proposal_sm_view" class="hide">
            </div>
         </div>
      </div>
   </div>
   
</div>


    <div class="modal" id="warehouse_type" tabindex="-1" role="dialog">
    <div class="modal-dialog ht-dialog-width">

          <?php echo form_open_multipart(admin_url('warehouse/add_commodity_list'), array('id'=>'add_warehouse_type')); ?>
          <div class="modal-content" >
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>

                    <h4 class="modal-title">
                        <span class="add-title"><?php echo _l('add'); ?></span>
                    </h4>
                   
                </div>
                <div class="modal-body">
                  <div class="row">
                    <div class="col-md-12">
                         <div id="warehouse_type_id">
                         </div>   
                     <div class="form"> 
                        <div class="col-md-12" id="add_handsontable">
                        </div>
                          <?php echo form_hidden('hot_warehouse_type'); ?>
                    </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                     <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                     <button id="latch_assessor" type="button" class="btn btn-info intext-btn" onclick="add_warehouse_type(this); return false;" ><?php echo _l('submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
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
                       <a href="#custom_fields" aria-controls="custom_fields" role="tab" data-toggle="tab" aria-controls="custom_fields">
                       <i class="fa fa-bars menu-icon"></i>&nbsp;<?php echo _l('custom_fields'); ?>
                       </a>
                    </li>
                    
                    
                   </ul>
                 </div>
               </div>

               <div class="tab-content">
              
                <!-- interview process start -->
                  <div role="tabpanel" class="tab-pane active" id="interview_infor">
                        <div class="row parent_item_hide">
                          <div class=" col-md-12">
                            <div id="parent_item_html">
                              
                            </div>
                            <!-- <div class="form-group">
                              <label for="parent_id" class="control-label"><?php echo _l('parent_item'); ?></label>
                              <select name="parent_id" id="parent_id" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="">
                                
                              </select>
                            </div> -->

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
                               <div class="col-md-6">
                                <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle skucode-tooltip"  data-toggle="tooltip" title="" data-original-title="<?php echo _l('commodity_barcode_tooltip'); ?>"></i></a>
                                     <?php echo render_input('commodity_barcode', 'commodity_barcode','','text'); ?>
                                </div>
                              <div class="col-md-3">
                                <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle skucode-tooltip"  data-toggle="tooltip" title="" data-original-title="<?php echo _l('commodity_sku_code_tooltip'); ?>"></i></a>
                                <?php echo render_input('sku_code', 'sku_code','',''); ?>
                              </div>
                              <div class="col-md-3">
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
                                <div class="col-md-3">
                                     <?php echo render_select('tax',$taxes,array('id','name'),'tax_1'); ?>
                                </div>
                                <div class="col-md-3">
                                     <?php echo render_select('tax2',$taxes,array('id','name'),'tax_2'); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">

                                    <?php 
                                    $attr = array();
                                    //$attr = ['data-type' => 'currency'];
                                     echo render_input('purchase_price', 'purchase_price','', 'number', $attr); ?>
                                  
                                </div>
                                <div class="col-md-6">

                                     <?php $premium_rates = isset($premium_rates) ? $premium_rates : '' ?>
                                    <?php 
                                    $attr = array();
                                     //$attr = ['data-type' => 'currency'];
                                     echo render_input('rate', 'rate','', 'number', $attr); ?>


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
                  <div role="tabpanel" class="tab-pane" id="custom_fields">
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
                <button type="submit" class="btn btn-info submit_btn"><?php echo _l('save'); ?></button>
            </div>
          </div>

          </div>
        </div>
            <?php echo form_close(); ?>

<!-- add one commodity list sibar end -->  
<div class="modal fade" id="show_detail" tabindex="-1" role="dialog">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">
                      <span class="add-title"></span>
                  </h4>
              </div>
              <div class="modal-body">
                  <div class="row">
                     <div class="horizontal-scrollable-tabs preview-tabs-top col-md-12">
                  <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                  <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                  <div class="horizontal-tabs">
                    <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                      <li role="presentation" class="active">
                           <a href="#out_of_stock" aria-controls="out_of_stock" role="tab" id="tab_out_of_stock" data-toggle="tab">
                              <?php echo _l('out_of_stock') ?>
                           </a>
                        </li>
                        <li role="presentation">
                           <a href="#expired" aria-controls="expired" role="tab" id="tab_expired" data-toggle="tab">
                              <?php echo _l('expired') ?>
                           </a>
                        </li>                      
                    </ul>
                    </div>
                </div>

                <div class="tab-content col-md-12">
                  <div role="tabpanel" class="tab-pane active row" id="out_of_stock">
                    <div class="col-md-12">
                      <?php render_datatable(array(
                          _l('id'),
                          _l('commodity_name'),
                          _l('expiry_date'),
                          _l('lot_number'),
                          _l('quantity'),


                          ),'table_out_of_stock'); ?>
                    </div>
                  </div>

                  <div role="tabpanel" class="tab-pane row" id="expired">
                    <div class="col-md-12">
                      <?php render_datatable(array(
                          _l('id'),
                          _l('commodity_name'),
                          _l('expiry_date'),
                          _l('lot_number'),
                          _l('quantity'),

                          ),'table_expired'); ?>
                    </div>
                  </div>                  
                </div>
                  </div>
              </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                </div>
            </div>
          </div>
      </div>
       <?php echo form_hidden('warehouse_id'); ?>
       <?php echo form_hidden('commodity_id'); ?>
       <?php echo form_hidden('expiry_date'); ?>
       <?php echo form_hidden('parent_item_filter', 'true'); ?>
       <?php echo form_hidden('filter_all_simple_variation_value'); ?>


<div id="modal_wrapper"></div>

<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/commodity_list_js.php';?>
</body>
</html>
