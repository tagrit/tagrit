<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">               
                      <div class="col-md-12 no-padding">


         <?php if($loss_adjustment->status == 0){ ?>
           <div class="ribbon info"><span><?php echo _l('not_yet_approve'); ?></span></div>
       <?php }elseif($loss_adjustment->status == 1){ ?>
         <div class="ribbon success"><span><?php echo _l('approved'); ?></span></div>
       <?php }elseif($loss_adjustment->status == -1){ ?>  
         <div class="ribbon danger"><span><?php echo _l('reject'); ?></span></div>
       <?php } ?>

         <div class="clearfix"></div>
         <div class="tab-content">
            <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
                 
               <div id="estimate-preview">

            <h4 class="h4-color"><?php echo _l('general_infor'); ?></h4>
            <hr class="hr-color">

          <div class="col-md-6 panel-padding">
            <table class="table border table-striped table-margintop" >
              <tbody>

                 <tr class="project-overview">
                    <td class="bold" width="30%"><?php echo _l('type'); ?></td>
                    <td><?php echo _l($loss_adjustment->type) ; ?></td>
                 </tr>
                  <tr class="project-overview">
                    <td class="bold" width="30%"><?php echo _l('add_from'); ?></td>
                    <td><?php echo get_staff_full_name($loss_adjustment->addfrom) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('time'); ?></td>
                    <td><?php echo new_html_entity_decode(_d($loss_adjustment->time)) ; ?></td>
                 </tr>
                <tr class="project-overview">
                    <td class="bold"><?php echo _l('reason'); ?></td>
                    <td><?php echo new_html_entity_decode($loss_adjustment->reason) ; ?></td>
                 </tr>

                <?php 
                  $warehouse_code = get_warehouse_name($loss_adjustment->warehouses) != null ? get_warehouse_name($loss_adjustment->warehouses)->warehouse_name : '';
                 ?>
                <tr class="project-overview">
                    <td class="bold"><?php echo _l('warehouse_name'); ?></td>
                    <td><?php echo new_html_entity_decode($warehouse_code) ; ?></td>
                 </tr>

                
                  </tbody>
          </table>
        </div>

        <!-- approval infor -->
        <div class="col-md-6 panel-padding">
                               <div class="col-md-12">
                      <div class="project-overview-right">
    <?php if(count($list_approve_status) > 0){ ?>

      <h4 class="h4-color"><?php echo _l('approval_infor'); ?></h4>

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
                if($this->staff_model->get($val)){
                  $staff_name .= $this->staff_model->get($val)->full_name;
                }
              }
              echo new_html_entity_decode($staff_name); 
              ?></p>
             <?php if($value['approve'] == 1){ 
              ?>
              
              <?php if (file_exists(WAREHOUSE_LOST_ADJUSTMENT_MODULE_UPLOAD_FOLDER . $loss_adjustment->id . '/signature_'.$value['id'].'.png') ){ ?>

                <img src="<?php echo site_url('modules/warehouse/uploads/lost_adjustment/'.$loss_adjustment->id.'/signature_'.$value['id'].'.png'); ?>" class="img-width-height">
                
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
                if($val != ''){
                if($staff_name != '')
                {
                  $staff_name .= ' or ';
                }
                $staff = $this->staff_model->get($val);

                if($staff){
                  $staff_name .= $staff->full_name;
                }
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
                  if($loss_adjustment->status != 1 && ($check_approve_status == false ))

                    { ?>
            <?php if($check_appr && $check_appr != false){ ?>
              
              <a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo new_html_entity_decode($loss_adjustment->id); ?>); return false;"><?php echo _l('send_request_approve'); ?></a>
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
                                <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_request(<?php echo new_html_entity_decode($loss_adjustment->id); ?>); return false;" class="btn btn-success button-margin"><?php echo _l('approve'); ?></a>
                               <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_request(<?php echo new_html_entity_decode($loss_adjustment->id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a></div>
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
               <br>
               <div class="row">
                     <div class="col-md-12">
                        <div class="table-responsive">
                           <table class="table items items-preview estimate-items-preview" data-type="estimate">
                              <thead>
                                 <tr>
                                    <th align="center">#</th>
                                    <th  colspan="1"><?php echo _l('commodity_code') ?></th>
                                    <th  colspan="1"><?php echo _l('unit_name') ?></th>
                                    <th align="right" colspan="1"><?php echo _l('lot_number') ?></th>
                                    <th align="right" colspan="1"><?php echo _l('expiry_date') ?></th>
                                    <th  colspan="1" class="text-center"><?php echo _l('available_quantity') ?></th>
                                    <th align="right" colspan="1"><?php echo _l('stock_quantity') ?></th>
                                    <th align="right" colspan="1"><?php echo _l('wh_serial_number_list') ?></th>
                                  
                                 </tr>
                              </thead>
                              <tbody class="ui-sortable">
                                
                              <?php 
                              foreach ($loss_adjustment_detail as $detail_key => $detail_value) {
                                $detail_key++;
                             $available_quantity = (isset($detail_value) ? $detail_value['current_number'] : '');
                             $stock_quantity = (isset($detail_value) ? $detail_value['updates_number'] : '');

                             $commodity_code = get_commodity_name($detail_value['items']) != null ? get_commodity_name($detail_value['items'])->commodity_code : '';
                             $commodity_name = get_commodity_name($detail_value['items']) != null ? get_commodity_name($detail_value['items'])->description : '';

                             $unit_name ='';
                             if(is_numeric($detail_value['unit'])){
                               $unit_name = get_unit_type($detail_value['unit']) != null ? get_unit_type($detail_value['unit'])->unit_name : '';

                             }
                             
                             
                              $expiry_date =(isset($detail_value) ? $detail_value['expiry_date'] : '');
                              $lot_number =(isset($detail_value) ? $detail_value['lot_number'] : '');
                              $commodity_name = $detail_value['commodity_name'];
                              if(new_strlen($commodity_name ?? '') == 0){
                                $commodity_name = wh_get_item_variatiom($detail_value['items']);
                              }

                              if(new_strlen($detail_value['serial_number'] ?? '') > 0){
                                $name_serial_number_tooltip = _l('wh_serial_number').': '.$detail_value['serial_number'];
                              }else{
                                $name_serial_number_tooltip = '';
                              }
                            ?>

                              <tr data-toggle="tooltip" data-original-title="<?php echo new_html_entity_decode($name_serial_number_tooltip); ?>">
                              <td ><?php echo new_html_entity_decode($detail_key) ?></td>
                                  <td ><?php echo new_html_entity_decode($commodity_name) ?></td>
                                  <td ><?php echo new_html_entity_decode($unit_name) ?></td>
                                  <td class="text-right"><?php echo new_html_entity_decode($lot_number) ?></td>
                                  <td class="text-right"><?php echo _d($expiry_date) ?></td>
                                  <td class="text-right" ><?php echo new_html_entity_decode($available_quantity) ?></td>
                                  <td class="text-right"><?php echo new_html_entity_decode($stock_quantity) ?></td>
                                  <td class="text-right"><?php echo new_html_entity_decode($detail_value['serial_number']) ?></td>
                                  
                                </tr>
                             <?php  } ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <br/>
                     <br/>




                                     
                    
                  </div>

            </div>

            
         </div>

        <div class="modal-footer">

            <a href="<?php echo admin_url('warehouse/loss_adjustment'); ?>"class="btn btn-default pull-right mright10 display-block close_button"><?php echo _l('close'); ?></a>
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
           <button onclick="sign_request(<?php echo new_html_entity_decode($loss_adjustment->id); ?>);" autocomplete="off" class="btn btn-success sign_request_class"><?php echo _l('e_signature_sign'); ?></button>
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

<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/view_lost_adjustment_js.php';?>
</body>
</html>

