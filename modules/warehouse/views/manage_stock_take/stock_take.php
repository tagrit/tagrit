 <?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
  
  <div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12" id="small-table">
            <div class="panel_s">
               <div class="panel-body">

                  <div class="row">
                     <div class="col-md-12">
                      <h4 class="no-margin font-bold h4-color"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                      <hr class="hr-color">
                    </div>
                  </div>

                  <?php echo form_open_multipart(admin_url('warehouse/manage_goods_receipt'), array('id'=>'add_goods_receipt')); ?>
               
                <div class="row row-margin">
                  <div class="col-md-8">
                     <div class="panel panel-primary">
                      <div class="panel-heading"><?php echo _l('general_infor') ?></div>
                      <div class="panel-body">

                        <div class="col-md-6">
                          <div class="form-group">
                             <label for="pr_order_id"><?php echo _l('reference_purchase_request'); ?></label>
                            <select onchange="pr_order_change(this); return false;" name="pr_order_id" id="pr_order_id" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                              <option value=""></option>
                              <?php foreach($pr_orders as $pr_order) { ?>
                                <option value="<?php echo new_html_entity_decode($pr_order['id']); ?>"><?php echo new_html_entity_decode($pr_order['pur_order_number'].' - '.$pr_order['pur_order_name']); ?></option>
                                <?php } ?>
                            </select>
                          </div>

                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="supplier_code"><?php echo _l('supplier_name'); ?></label>
                            <select name="supplier_code" id="supplier_code" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                              <option value=""></option>
                              <?php foreach($vendors as $vendor) { ?>
                                <option value="<?php echo new_html_entity_decode($vendor['userid']); ?>"><?php echo new_html_entity_decode($vendor['company']); ?></option>
                                <?php } ?>
                            </select>
                          </div>

                        </div>

                        <br>

                      <div class=" col-md-6">
                          <?php $deliver_name = (isset($candidate) ? $candidate->deliver_name : '');
                          echo render_input('deliver_name','deliver_name',$deliver_name) ?>
                      </div>
                      <div class=" col-md-6">
                          <label for="buyer_id" class="control-label"><?php echo _l('Buyer'); ?></label>
                            <select name="buyer_id" class="selectpicker" id="buyer_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                              <option value=""></option> 
                              <?php foreach($staff as $s){ ?>
                            <option value="<?php echo new_html_entity_decode($s['staffid']); ?>"> <?php echo new_html_entity_decode($s['firstname'].''.$s['lastname']); ?></option>                  
                            <?php }?>
                            </select>
                      </div>

                      <div class="col-md-12">
                          <?php $description = (isset($candidate) ? $candidate->description : '');
                          echo render_textarea('description','note', $description) ?>
                      </div>

                      </div>
                    </div>
                  </div>

                  <div class="col-md-4">
                     <div class="panel panel-primary">
                      <div class="panel-heading"><?php echo _l('license_number') ?></div>
                        <div class="panel-body">
                          <div class="col-md-12">
                            <?php $stock_take_code = (isset($goods_code) ? $goods_code : '');?>
                            <?php echo render_input('stock_take_code', 'Số phiếu kiểm kê',$stock_take_code,'',array('disabled' => 'true')) ?></td>
                          </div>
                          <div class="col-md-12">
                            <?php echo render_date_input('date_add','Ngày kiểm kê') ?>
                          </div>
                          <div class="col-md-12">
                            <?php echo render_date_input('date_add','Giờ') ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                    
                   
                    <div class="col-md-12 ">
                 
                        <h5 class="no-margin font-bold h4-color"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l('stock_received_docket_detail'); ?></h5>
                        <hr class="hr-color">

                          <div class="panel-body ">
                            <div class="horizontal-scrollable-tabs preview-tabs-top">
                             <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                             <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                             <div class="horizontal-tabs">
                             <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                               <li role="presentation" class="active">
                                   <a href="#commodity" aria-controls="commodity" role="tab" data-toggle="tab" aria-controls="commodity" id="ac_commodity">
                                   <span class="glyphicon glyphicon-align-justify"></span>&nbsp;<?php echo _l('commodity'); ?>
                                   </a>
                                </li>
                                
                              </ul>
                             </div>
                            </div>

                            <div class="tab-content">
                              <div role="tabpanel" class="tab-pane active" id="commodity">
                                <div class="form"> 
                                    <div id="hot_purchase" class="hot handsontable htColumnHeaders">
                                        
                                    </div>
                                  <?php echo form_hidden('hot_purchase'); ?>
                                </div>

                              </div>
                              <div role="tabpanel" class="tab-pane" id="tax">  

                              </div>
                            </div>

                           
                             <div class="col-md-3 pull-right panel-padding">
                                <table class="table border table-striped table-margintop" >
                                  <tbody>
                                      <tr class="project-overview">
                                        <td ><?php echo render_input('total_goods_money','total_goods_money','','',array('disabled' => 'true')) ?>
                                          <?php echo form_hidden('total_goods_money'); ?>
                                        </td>
                                     </tr>

                                     <tr class="project-overview">
                                        <td ><?php echo render_input('total_money','total_money','','',array('disabled' => 'true')) ?>
                                          <?php echo form_hidden('total_money'); ?>
                                          
                                        </td>

                                     </tr>
                                      </tbody>
                              </table>
                          </div>

                          <div class="col-md-3 pull-right panel-padding" >
                                <table class="table border table-striped table-margintop">
                                  <tbody>
                                     <tr class="project-overview">
                                        <td ><?php echo render_input('total_tax_money','total_tax_money','','',array('disabled' => 'true')) ?>
                                          <?php echo form_hidden('total_tax_money'); ?>
                                          
                                        </td>

                                     </tr>
                                     <tr class="project-overview">
                                        <td ><?php echo render_input('value_of_inventory','value_of_inventory','','',array('disabled' => 'true')) ?>
                                          <?php echo form_hidden('value_of_inventory'); ?>
                                          
                                        </td>
                                     </tr>
                                     
                                      </tbody>
                              </table>
                          </div>
                          </div>
                        </div>

                  <hr>
                 <div class="modal-footer">                
                    <a href="#"class="btn btn-info pull-right mright10 display-block" onclick="add_goods_receipt(this); return false;"><?php echo _l('submit'); ?></a>
                    <a href="<?php echo admin_url('warehouse/manage_purchase'); ?>"class="btn btn-default pull-right mright10 display-block"><?php echo _l('close'); ?></a>
                </div>

                      </div>

                    </div>
                   

                  </div>
              


                  <?php echo form_close(); ?>

               </div>
            </div>
          </div>
      </div>
    </div>
  </div>


<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/stock_take_js.php';?>
</body>
</html>


