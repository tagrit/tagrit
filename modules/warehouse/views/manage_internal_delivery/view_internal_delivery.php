<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="col-md-12 no-padding">
   <div class="panel_s">
      <div class="panel-body">

         <?php if($internal_delivery->approval == 0){ ?>
           <div class="ribbon info"><span><?php echo _l('not_yet_approve'); ?></span></div>
       <?php }elseif($internal_delivery->approval == 1){ ?>
         <div class="ribbon success"><span><?php echo _l('approved'); ?></span></div>
       <?php }elseif($internal_delivery->approval == -1){ ?>  
         <div class="ribbon danger"><span><?php echo _l('reject'); ?></span></div>
       <?php } ?>
         <div class="horizontal-scrollable-tabs preview-tabs-top">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="active">
                     <a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
                     <?php echo _l('internal_delivery_note'); ?>
                     </a>
                  </li>  

                  <li role="presentation" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>" class="tab-separator toggle_view">
                     <a href="#" onclick="small_table_full_view(); return false;">
                     <i class="fa fa-expand"></i></a>
                  </li>
               </ul>
            </div>
         </div>

         <div class="clearfix"></div>
         <div class="tab-content">
            <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
              <?php if($internal_delivery->approval == 0){ ?>
                  <div class="row">
                    <div class="col-md-4">

                    </div>
                    <div class="col-md-8">
                       <div class="pull-right _buttons">
                          <?php if(has_permission('wh_internal_delivery_note','','edit')){ ?>
                          <a href="<?php echo admin_url('warehouse/add_update_internal_delivery/'.$internal_delivery->id); ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('edit'); ?>" data-placement="bottom"><i class="fa fa-edit"></i></a>
                          <?php } ?>

                       </div>

                    </div>
                 </div>
               <?php } ?>
                 
               <div id="estimate-preview">

          <div class="col-md-12 panel-padding">
            <table class="table border table-striped table-margintop" >
              <tbody>

                 <tr class="project-overview">
                    <td class="bold" width="30%"><?php echo _l('internal_delivery_note'); ?></td>
                    <td><?php echo new_html_entity_decode($internal_delivery->internal_delivery_code .' - '.$internal_delivery->internal_delivery_name) ; ?></td>
                 </tr>
                  <tr class="project-overview">
                    <td class="bold" width="30%"><?php echo _l('deliver_name'); ?></td>
                    <td>

                      <?php 
                        $_data='';
                         $_data .= '<a href="' . admin_url('staff/profile/' . $internal_delivery->staff_id) . '">' . staff_profile_image($internal_delivery->staff_id, [
                'staff-profile-image-small',
                ]) . '</a>';
                      $_data .= ' <a href="' . admin_url('staff/profile/' . $internal_delivery->staff_id) . '">' . get_staff_full_name($internal_delivery->staff_id) . '</a>';

                       ?>

                      <?php echo new_html_entity_decode($_data) ; ?>
                        
                      </td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('addedfrom'); ?></td>

                      <?php 
                        $_data='';
                         $_data .= '<a href="' . admin_url('staff/profile/' . $internal_delivery->addedfrom) . '">' . staff_profile_image($internal_delivery->addedfrom, [
                'staff-profile-image-small',
                ]) . '</a>';
                      $_data .= ' <a href="' . admin_url('staff/profile/' . $internal_delivery->addedfrom) . '">' . get_staff_full_name($internal_delivery->addedfrom) . '</a>';

                       ?>


                    <td><?php echo new_html_entity_decode($_data) ; ?></td>
                 </tr>

                <tr class="project-overview">
                    <td class="bold"><?php echo _l('datecreated'); ?></td>
                    <td><?php echo _d($internal_delivery->datecreated) ; ?></td>
                 </tr>
                <tr class="project-overview">
                    <td class="bold"><?php echo _l('note_'); ?></td>
                    <td><?php echo new_html_entity_decode($internal_delivery->description) ; ?></td>
                 </tr>

                <tr>
                  <td class="bold"><?php echo _l('print'); ?></td>
                  <td>
                    <div class="btn-group">
                      <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
                      <ul class="dropdown-menu dropdown-menu-right">
                       <li class="hidden-xs"><a href="<?php echo admin_url('warehouse/stock_internal_delivery_pdf/'.$internal_delivery->id.'?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                       <li class="hidden-xs"><a href="<?php echo admin_url('warehouse/stock_internal_delivery_pdf/'.$internal_delivery->id.'?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                       <li><a href="<?php echo admin_url('warehouse/stock_internal_delivery_pdf/'.$internal_delivery->id); ?>"><?php echo _l('download'); ?></a></li>
                       <li>
                        <a href="<?php echo admin_url('warehouse/stock_internal_delivery_pdf/'.$internal_delivery->id.'?print=true'); ?>" target="_blank">
                          <?php echo _l('print'); ?>
                        </a>
                      </li>
                    </ul>
                  </div>

                </td>
              </tr>

                
                  </tbody>
          </table>
        </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="table-responsive">
                           <table class="table items items-preview estimate-items-preview" data-type="estimate">
                              <thead>
                                 <tr>
                                    <th align="center">#</th>
                                    <th  colspan="1"><?php echo _l('commodity_code') ?></th>
                                     <th colspan="1"><?php echo _l('from_stock_name') ?></th>
                                     <th colspan="1"><?php echo _l('to_stock_name') ?></th>
                                     <th  colspan="1"><?php echo _l('unit_name') ?></th>
                                     <th  colspan="1" class="text-center"><?php echo _l('available_quantity') ?></th>
                                     <th  colspan="1" class="text-center"><?php echo _l('quantity_export') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('unit_price') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('into_money') ?></th>
                                     
                                 </tr>
                              </thead>
                              <tbody class="ui-sortable">
                                
                              <?php 
                              foreach ($internal_delivery_detail as $internal_delivery_key => $internal_delivery_value) {

                              $internal_delivery_key++;
                             $availale_quantity = (isset($internal_delivery_value) ? $internal_delivery_value['available_quantity'] : '');
                             $quantities = (isset($internal_delivery_value) ? $internal_delivery_value['quantities'] : '');

                             $unit_price = (isset($internal_delivery_value) ? $internal_delivery_value['unit_price'] : '');
                             $into_money = (isset($internal_delivery_value) ? $internal_delivery_value['into_money'] : '');

                             $commodity_code = get_commodity_name($internal_delivery_value['commodity_code']) != null ? get_commodity_name($internal_delivery_value['commodity_code'])->commodity_code : '';
                             $commodity_name = get_commodity_name($internal_delivery_value['commodity_code']) != null ? get_commodity_name($internal_delivery_value['commodity_code'])->description : '';

                            $unit_name ='';
                             if(is_numeric($internal_delivery_value['unit_id'])){
                               $unit_name = get_unit_type($internal_delivery_value['unit_id']) != null ? get_unit_type($internal_delivery_value['unit_id'])->unit_name : '';

                             }


                              $from_stock_name = get_warehouse_name($internal_delivery_value['from_stock_name']) != null ? get_warehouse_name($internal_delivery_value['from_stock_name'])->warehouse_name : '';

                              $to_stock_name = get_warehouse_name($internal_delivery_value['to_stock_name']) != null ? get_warehouse_name($internal_delivery_value['to_stock_name'])->warehouse_name : '';

                              $commodity_name = $internal_delivery_value['commodity_name'];
                              if(new_strlen($commodity_name ?? '') == 0){
                                $commodity_name = wh_get_item_variatiom($internal_delivery_value['commodity_code']);
                              }
                            ?>
          
                              <tr>
                              <td ><?php echo new_html_entity_decode($internal_delivery_key) ?></td>
                                  <td ><?php echo new_html_entity_decode($commodity_name) ?></td>
                                  <td ><?php echo new_html_entity_decode($from_stock_name) ?></td>
                                  <td ><?php echo new_html_entity_decode($to_stock_name) ?></td>
                                  <td ><?php echo new_html_entity_decode($unit_name) ?></td>

                                  <td class="text-right" ><?php echo new_html_entity_decode($availale_quantity) ?></td>
                                  <td class="text-right" ><?php echo new_html_entity_decode($quantities) ?></td>

                                  <td class="text-right"><?php echo app_format_money((float)$unit_price,'') ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$into_money,'') ?></td>
                                  
                                </tr>
                             <?php  } ?>
                              </tbody>
                           </table>
                        </div>
                     </div>

                     <div class="col-md-3 pull-right panel-padding">
                      <table class="table text-right table-margintop">
                        <tbody>
                          <tr class="project-overview">
                            <?php $total_amount = isset($internal_delivery) ?  $internal_delivery->total_amount : 0 ;?>
                            <td class="td_style"><span class="bold"><?php echo _l('total_amount'); ?></span></td>
                            <td><?php echo app_format_money((float)$total_amount, $base_currency); ?></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                        


                    <div class="col-md-12">
                      <div class="project-overview-right">
    <?php if(count($list_approve_status) > 0){ ?>
      
     <div class="row">
       <div class="col-md-12 project-overview-expenses-finance">
        <div class="col-md-4 text-center">
        </div>
        <?php 
          $this->load->model('staff_model');
          $enter_charge_code = 0;
        foreach ($list_approve_status as $value) {
          $value['staffid'] = new_explode(', ',$value['staffid']);
          if($value['action'] == 'sign'){
         ?>
         <div class="col-md-3 text-center">
             <p class="text-uppercase text-muted no-mtop bold">
              <?php
              $staff_name = '';
              $st = _l('status_0');
              $color = 'warning';
              foreach ($value['staffid'] as $key => $val) {
                if($staff_name != '')
                {
                  $staff_name .= ' or ';
                }

                $staff_approval = $this->staff_model->get($val);
                if($staff_approval){
                  $staff_name .= $staff_approval->firstname.' '.$staff_approval->lastname;
                }

              }
              echo new_html_entity_decode($staff_name); 
              ?></p>
             <?php if($value['approve'] == 1){ 
              ?>
              <?php if (file_exists(WAREHOUSE_INTERNAL_DELIVERY_MODULE_UPLOAD_FOLDER . $internal_delivery->id . '/signature_'.$value['id'].'.png') ){ ?>

                <img src="<?php echo site_url('modules/warehouse/uploads/internal_delivery/'.$internal_delivery->id.'/signature_'.$value['id'].'.png'); ?>" class="img-width-height">
              <?php }else{ ?>
                <img src="<?php echo site_url('modules/warehouse/uploads/image_not_available.jpg'); ?>" class="img-width-height">
              <?php } ?>

             <?php }
              ?>    
        </div>
        <?php }else{ ?>
        <div class="col-md-3 text-center">
             <p class="text-uppercase text-muted no-mtop bold">
              <?php
              $staff_name = '';
              foreach ($value['staffid'] as $key => $val) {
                if($staff_name != '')
                {
                  $staff_name .= ' or ';
                }

                $staff_approval = $this->staff_model->get($val);
                if($staff_approval){
                  $staff_name .= $staff_approval->firstname.' '.$staff_approval->lastname;
                }
                
              }
              echo new_html_entity_decode($staff_name); 
              ?></p>
             <?php if($value['approve'] == 1){ 
              ?>
              <img src="<?php echo site_url('modules/warehouse/uploads/approval/approved.png') ; ?>" class="img-width-height">
             <?php }elseif($value['approve'] == -1){ ?>
                <img src="<?php echo site_url('modules/warehouse/uploads/approval/rejected.png') ; ?>" class="img-width-height">
            <?php }
              ?>  

            <p class="text-muted no-mtop bold">  
              <?php echo new_html_entity_decode($value['note']) ?>
            </p>  
        </div>
        <?php }
        } ?>
       </div>
    </div>
    
    <?php } ?>
    </div>

                       <div class="pull-right">
                   
                  <?php 
                  if($internal_delivery->approval != 1 && ($check_approve_status == false ))

                    { ?>
            <?php if($check_appr && $check_appr != false){ ?>

              <a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo new_html_entity_decode($internal_delivery->id); ?>); return false;"><?php echo _l('send_request_approve'); ?></a>
            <?php } ?>
            
            <?php }
              if(isset($check_approve_status['staffid'])){
                  ?>
                  <?php 
              if(in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign)){ ?>
                  <div class="btn-group" >
                         <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>
                         <ul class="dropdown-menu dropdown-menu-right menu-width-height">
                          <li>
                            <div class="col-md-12">
                              <?php echo render_textarea('reason', 'reason'); ?>
                            </div>
                          </li>
                            <li>
                              <div class="row text-right col-md-12">
                                <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_request(<?php echo new_html_entity_decode($internal_delivery->id); ?>); return false;" class="btn btn-success button-margin"><?php echo _l('approve'); ?></a>
                               <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_request(<?php echo new_html_entity_decode($internal_delivery->id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a></div>
                            </li>
                         </ul>
                      </div>
                <?php }
                  ?>
                  
                <?php
                 if(in_array(get_staff_user_id(), $check_approve_status['staffid']) && in_array(get_staff_user_id(), $get_staff_sign)){ ?>
                  <button onclick="accept_action();" class="btn btn-success pull-right action-button"><?php echo _l('e_signature_sign'); ?></button>
                <?php }
                  ?>
                  <?php 
                   }
                  ?>
                </div>

                     </div>                                    
                    
                  </div>
               </div>
            </div>

         </div>

         <div class="modal fade" id="add_action" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         
        <div class="modal-body">
         <p class="bold" id="signatureLabel"><?php echo _l('signature'); ?></p>
            <div class="signature-pad--body">
              <canvas id="signature" height="130" width="550"></canvas>
            </div>
            <input type="text" class="sig-input-style" tabindex="-1" name="signature" id="signatureInput">
            <div class="dispay-block">
              <button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
           <button onclick="sign_request(<?php echo new_html_entity_decode($internal_delivery->id); ?>);" autocomplete="off" class="btn btn-success sign_request_class"><?php echo _l('e_signature_sign'); ?></button>
          </div>


      </div>
   </div>
</div>

      </div>
   </div>
</div>

<?php require 'modules/warehouse/assets/js/view_internal_delivery_js.php';?>
</body>
</html>