<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_hidden('_attachment_sale_id',$goods_delivery->id); ?>
<?php echo form_hidden('_attachment_sale_type','estimate'); ?>
<div class="col-md-12 no-padding">
   <div class="panel_s">
      <div class="panel-body">
         <?php if($goods_delivery->approval == 0){ ?>
           <div class="ribbon info"><span><?php echo _l('not_yet_approve'); ?></span></div>
       <?php }elseif($goods_delivery->approval == 1){ ?>
         <div class="ribbon success"><span><?php echo _l('approved'); ?></span></div>
       <?php }elseif($goods_delivery->approval == -1){ ?>  
         <div class="ribbon danger"><span><?php echo _l('reject'); ?></span></div>
       <?php } ?>
         <div class="horizontal-scrollable-tabs preview-tabs-top">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="active">
                     <a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
                     <?php echo _l('export_output_slip'); ?>
                     </a>
                  </li>  

                  <li role="presentation">
                     <a href="#tab_tasks" onclick="init_rel_tasks_table(<?php echo new_html_entity_decode($goods_delivery->id); ?>,'stock_export'); return false;" aria-controls="tab_tasks" role="tab" data-toggle="tab">
                     <?php echo _l('tasks'); ?>
                     </a>
                  </li>
                  <?php if(count($packing_lists) > 0){ ?>
                    <li role="presentation" >
                     <a href="#tab_packing_list" class="tab_packing_list" aria-controls="tab_packing_list" role="tab" data-toggle="tab">
                       <?php echo _l('wh_packing_list'); ?>
                     </a>
                   </li>
                 <?php } ?>

                  <li role="presentation" >
                     <a href="#tab_activilog" class="tab_activilog" aria-controls="tab_activilog" role="tab" data-toggle="tab">
                     <?php echo _l('wh_activilog'); ?>
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
                
                <div class="row">
                  <div class="col-md-4">

                  </div>
                  <div class="col-md-8">
                     <div class="pull-right _buttons">
                        <?php if(has_permission('wh_stock_export','','view') || is_admin()){ ?>
                        <a href="<?php echo admin_url('warehouse/edit_delivery/'.$goods_delivery->id); ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('view'); ?>" data-placement="bottom"><i class="fa fa-eye"></i></a>
                        <?php } ?>

                        <!-- send mail -->
                        <a href="#" onclick="get_goods_delivery_ajax('<?php echo new_html_entity_decode($goods_delivery->id); ?>', '<?php echo new_html_entity_decode($goods_delivery->invoice_id); ?>'); return false;" class="btn btn-success mleft5" ><i class="fa fa-envelope" data-toggle="tooltip" title="<?php echo _l('send_mail') ?>"></i></a>

                     </div>

                  </div>
               </div>

               <div id="estimate-preview">

          <div class="col-md-12 row-margin">
            <table class="table border table-striped table-margintop ">
              <tbody>
                <?php 
                $customer_name='';
                if($goods_delivery){
                  
                    if(is_numeric($goods_delivery->customer_code)){
                      $customer_value = $this->clients_model->get($goods_delivery->customer_code);
                      if($customer_value){
                        $customer_name .= $customer_value->company;
                      }
                  }

                }
                 ?>

                  <tr class="project-overview">
                    <td class="bold" width="30%"><?php echo _l('customer_name'); ?></td>
                    <td><?php echo new_html_entity_decode($customer_name) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('to'); ?></td>
                    <td><?php echo new_html_entity_decode($goods_delivery->to_) ; ?></td>
                 </tr>
                <tr class="project-overview">
                    <td class="bold"><?php echo _l('address'); ?></td>
                    <td><?php echo new_html_entity_decode($goods_delivery->address) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('note_'); ?></td>
                    <td><?php echo new_html_entity_decode($goods_delivery->description) ; ?></td>
                 </tr>

                 <?php 
                      if( ($goods_delivery->invoice_id != '') && ($goods_delivery->invoice_id != 0) ){ ?>

                        <tr class="project-overview">
                          <td class="bold"><?php echo _l('invoices'); ?></td>
                          <td>
                              <a href="<?php echo admin_url('invoices#'.$goods_delivery->invoice_id) ?>" ><?php echo format_invoice_number($goods_delivery->invoice_id).get_invoice_company_projecy($goods_delivery->invoice_id) ?></a>

                            </td>
                       </tr>

                    <?php   }
                  ?>

                  <?php 
                  if (get_status_modules_wh('purchase')) {
                      if( ($goods_delivery->pr_order_id != '') && ($goods_delivery->pr_order_id != 0) ){ ?>

                        <tr class="project-overview">
                          <td class="bold"><?php echo _l('reference_purchase_order'); ?></td>
                          <td>
                              <a href="<?php echo admin_url('purchase/purchase_order/'.$goods_delivery->pr_order_id) ?>" ><?php echo get_pur_order_name($goods_delivery->pr_order_id) ?></a>

                            </td>
                       </tr>

                    <?php   }
                  }
                  ?>

                  <tr>
                    <td class="bold"><?php echo _l('print'); ?></td>
                    <td>
                      <div class="btn-group">
                          <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
                          <ul class="dropdown-menu dropdown-menu-right">
                             <li class="hidden-xs"><a href="<?php echo admin_url('warehouse/stock_export_pdf/'.$goods_delivery->id.'?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                             <li class="hidden-xs"><a href="<?php echo admin_url('warehouse/stock_export_pdf/'.$goods_delivery->id.'?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                             <li><a href="<?php echo admin_url('warehouse/stock_export_pdf/'.$goods_delivery->id); ?>"><?php echo _l('download'); ?></a></li>
                             <li>
                                <a href="<?php echo admin_url('warehouse/stock_export_pdf/'.$goods_delivery->id.'?print=true'); ?>" target="_blank">
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
                                     <th colspan="1"><?php echo _l('warehouse_name') ?></th>
                                     <th colspan="1"><?php echo _l('available_quantity') ?></th>
                                     <th  colspan="1"><?php echo _l('unit_name') ?></th>
                                     <th  colspan="1" class="text-center"><?php echo _l('quantity') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('rate') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('subtotal') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('subtotal_after_tax') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('discount').'(%)' ?></th>
                                     <th align="right" colspan="1"><?php echo _l('discount(money)') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('lot_number').'/'._l('quantity') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('total_money') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('guarantee_period') ?></th>
            
                                 </tr>
                              </thead>
                              <tbody class="ui-sortable">
                              <?php 
                              $subtotal = 0 ;
                              foreach ($goods_delivery_detail as $delivery => $delivery_value) {
                             $delivery++;
                             $available_quantity = (isset($delivery_value) ? $delivery_value['available_quantity'] : '');
                             $total_money = (isset($delivery_value) ? $delivery_value['total_money'] : '');
                             $discount = (isset($delivery_value) ? $delivery_value['discount'] : '');
                             $discount_money = (isset($delivery_value) ? $delivery_value['discount_money'] : '');
                             $guarantee_period = (isset($delivery_value) ? _d($delivery_value['guarantee_period']) : '');

                             $quantities = (isset($delivery_value) ? $delivery_value['quantities'] : '');
                             $unit_price = (isset($delivery_value) ? $delivery_value['unit_price'] : '');
                             $total_after_discount = (isset($delivery_value) ? $delivery_value['total_after_discount'] : '');

                             $commodity_code = get_commodity_name($delivery_value['commodity_code']) != null ? get_commodity_name($delivery_value['commodity_code'])->commodity_code : '';
                             $commodity_name = get_commodity_name($delivery_value['commodity_code']) != null ? get_commodity_name($delivery_value['commodity_code'])->description : '';
                             $subtotal += (float)$delivery_value['quantities'] * (float)$delivery_value['unit_price'];
                             $item_subtotal = (float)$delivery_value['quantities'] * (float)$delivery_value['unit_price'];
                             


                             $warehouse_name ='';

                            if(isset($delivery_value['warehouse_id']) && ($delivery_value['warehouse_id'] !='')){
                              $arr_warehouse = new_explode(',', $delivery_value['warehouse_id']);

                              $str = '';
                              if(count($arr_warehouse) > 0){

                                foreach ($arr_warehouse as $wh_key => $warehouseid) {
                                  $str = '';
                                  if ($warehouseid != '' && $warehouseid != '0') {

                                    $team = get_warehouse_name($warehouseid);
                                    if($team){
                                      $value = $team != null ? get_object_vars($team)['warehouse_name'] : '';

                                      $str .= '<span class="label label-tag tag-id-1"><span class="tag">' . $value . '</span><span class="hide">, </span></span>&nbsp';

                                      $warehouse_name .= $str;
                                      if($wh_key%3 ==0){
                                        $warehouse_name .='<br/>';
                                      }
                                    }

                                  }
                                }

                              } else {
                                $warehouse_name = '';
                              }
                            }



                             $unit_name = '';
                             if(is_numeric($delivery_value['unit_id'])){
                                $unit_name = get_unit_type($delivery_value['unit_id']) != null ? get_unit_type($delivery_value['unit_id'])->unit_name : '';
                              }

                             $lot_number ='';
                             if(($delivery_value['lot_number'] != null) && ( $delivery_value['lot_number'] != '') ){
                                $array_lot_number = new_explode(',', $delivery_value['lot_number']);
                                foreach ($array_lot_number as $key => $lot_value) {
                                    
                                    if($key%2 ==0){
                                      $lot_number .= $lot_value;
                                    }else{
                                      $lot_number .= ' : '.$lot_value.' ';
                                    }

                                }
                             }

                             $commodity_name = $delivery_value['commodity_name'];
                             if(new_strlen($commodity_name ?? '') == 0){
                              $commodity_name = wh_get_item_variatiom($delivery_value['commodity_code']);
                            }

                            ?>
          
                              <tr>
                              <td ><?php echo new_html_entity_decode($delivery) ?></td>
                                  <td ><?php echo new_html_entity_decode($commodity_name) ?></td>
                                  <td ><?php echo new_html_entity_decode($warehouse_name) ?></td>
                                  <td ><?php echo new_html_entity_decode($available_quantity) ?></td>
                                  <td ><?php echo new_html_entity_decode($unit_name) ?></td>
                                  <td class="text-right"><?php echo new_html_entity_decode($quantities) ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$unit_price,'') ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$item_subtotal,'') ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$total_money,'') ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$discount,'') ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$discount_money,'') ?></td>
                                  <td class="text-right"><?php echo new_html_entity_decode($lot_number) ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$total_after_discount,'') ?></td>
                                  <td class="text-right"><?php echo new_html_entity_decode($guarantee_period) ?></td>
                                </tr>
                             <?php  } ?>
                              </tbody>
                           </table>

                           <div class="col-md-8 col-md-offset-4">
                             <table class="table text-right">
                              <tbody>
                                <tr id="subtotal">
                                  <td class="bold"><?php echo _l('subtotal'); ?></td>
                                  <td><?php echo app_format_money((float)$subtotal, $base_currency); ?></td>
                                </tr>
                                <?php if(isset($goods_delivery) && $tax_data['html_currency'] != ''){
                                  echo new_html_entity_decode($tax_data['html_currency']);
                                } ?>
                                <tr id="total_discount">
                                  <?php
                                  $total_discount = 0 ;
                                  if(isset($goods_delivery)){
                                    $total_discount += (float)$goods_delivery->total_discount  + (float)$goods_delivery->additional_discount;
                                  }
                                  ?>
                                  <td class="bold"><?php echo _l('total_discount'); ?></td>
                                  <td><?php echo app_format_money((float)$total_discount, $base_currency); ?></td>
                                </tr>
                                <?php if(get_option('wh_hide_shipping_fee') == 0){ ?>
                                <tr id="shipping_fee">
                                  <?php
                                  $shipping_fee = 0 ;
                                  if(isset($goods_delivery)){
                                    $shipping_fee = (float)$goods_delivery->shipping_fee;
                                  }
                                  ?>
                                  <td class="bold"><?php echo _l('wh_shipping_fee'); ?></td>
                                  <td><?php echo app_format_money((float)$shipping_fee, $base_currency); ?></td>
                                </tr>
                              <?php } ?>

                                <tr id="totalmoney">
                                  <?php
                                  $after_discount = isset($goods_delivery) ?  $goods_delivery->after_discount : 0 ;
                                  if($goods_delivery->after_discount == null){
                                    $after_discount = $goods_delivery->total_money;
                                  }
                                  ?>
                                 <td class="bold"><?php echo _l('total_money'); ?></td>
                                  <td><?php echo app_format_money((float)$after_discount, $base_currency); ?></td>
                                </tr>
                              </tbody>
                             </table>
                           </div>

                        </div>
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
                $staff_name .= $this->staff_model->get($val)->firstname;
              }
              echo new_html_entity_decode($staff_name); 
              ?></p>
             <?php if($value['approve'] == 1){ 
              ?>

              <?php if (file_exists(WAREHOUSE_STOCK_EXPORT_MODULE_UPLOAD_FOLDER . $goods_delivery->id . '/signature_'.$value['id'].'.png') ){ ?>

                <img src="<?php echo site_url('modules/warehouse/uploads/stock_export/'.$goods_delivery->id.'/signature_'.$value['id'].'.png'); ?>" class="img-width-height">
                
              <?php }else{ ?>
                <img src="<?php echo site_url('modules/warehouse/uploads/image_not_available.jpg'); ?>" class="img-width-height">
              <?php } ?>
              
              
             <?php }
              ?> 
              <p class="text-muted no-mtop bold">  
              <?php echo new_html_entity_decode($value['note']) ?>
            </p>   
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
                $get_name = $this->staff_model->get($val);
                if($get_name){
                  $staff_name .= $get_name->firstname;
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
                  if($goods_delivery->approval != 1 && ($check_approve_status == false ))

                    { ?>
                  <?php if($check_appr && $check_appr != false){ ?>
              <a data-toggle="tooltip"  class="btn btn-success lead-top-btn lead-view send_request_approve_class" data-placement="top" href="#" onclick="send_request_approve(<?php echo new_html_entity_decode($goods_delivery->id); ?>); return false;"><?php echo _l('send_request_approve'); ?></a>
            <?php } ?>
            
            <?php }
              if(isset($check_approve_status['staffid'])){
                  ?>
                  <?php 
              if(in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign)){ ?>
                  <div class="btn-group" >
                         <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>
                         <ul class="dropdown-menu dropdown-menu-right menu-width-height" >
                          <li>
                            <div class="col-md-12">
                              <?php echo render_textarea('reason', 'reason'); ?>
                            </div>
                          </li>
                            <li>
                              <div class="row text-right col-md-12">
                                <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_request(<?php echo new_html_entity_decode($goods_delivery->id); ?>); return false;" class="btn btn-success button-margin" ><?php echo _l('approve'); ?></a>
                               <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_request(<?php echo new_html_entity_decode($goods_delivery->id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a></div>
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

            <div role="tabpanel" class="tab-pane" id="tab_tasks">
               <?php init_relation_tasks_table(array('data-new-rel-id'=>$goods_delivery->id,'data-new-rel-type'=>'stock_export')); ?>
            </div>

            <?php if(count($packing_lists) > 0){ ?>
            <div role="tabpanel" class="tab-pane" id="tab_packing_list">
             <div class="panel_s no-shadow">

              <div class="row">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table class="table items items-preview estimate-items-preview" data-type="estimate">
                      <thead>
                        <tr>
                          <th  colspan="1"><?php echo _l('packing_list_number') ?></th>
                          <th  colspan="1"><?php echo _l('customer_name') ?></th>
                          <th align="right" colspan="1"><?php echo _l('wh_dimension') ?></th>
                          <th align="right" colspan="1"><?php echo _l('volume_m3_label') ?></th>
                          <th align="right" colspan="1"><?php echo _l('total_amount') ?></th>
                          <th align="right" colspan="1"><?php echo _l('discount_total') ?></th>
                          <th align="right" colspan="1"><?php echo _l('total_after_discount') ?></th>
                          <th align="right" colspan="1"><?php echo _l('datecreated') ?></th>
                          <th align="right" colspan="1"><?php echo _l('status_label') ?></th>
                        </tr>
                      </thead>
                      <tbody class="ui-sortable">
                        <?php 
                        $subtotal = 0 ;
                        foreach ($packing_lists as $key => $packing_list) {
                          $delivery++;
                          ?>

                          <tr>
                            <td ><a href="<?php echo admin_url('warehouse/manage_packing_list/' . $packing_list['id'] ) ?>" ><?php echo new_html_entity_decode($packing_list['packing_list_number'] .' - '.$packing_list['packing_list_name']) ?></a></td>
                            <td ><?php echo get_company_name($packing_list['clientid']) ?></td>
                            <td class="text-right"><?php echo new_html_entity_decode($packing_list['width'].' x '.$packing_list['height'].' x '.$packing_list['lenght']) ?></td>
                            <td class="text-right"><?php echo app_format_money($packing_list['volume'], '') ?></td>
                            <td class="text-right"><?php echo app_format_money($packing_list['total_amount'], '') ?></td>
                            <td class="text-right"><?php echo app_format_money($packing_list['discount_total']+$packing_list['additional_discount'], '') ?></td>
                            <td class="text-right"><?php echo app_format_money($packing_list['total_after_discount'], '') ?></td>
                            <td class="text-right"><?php echo _dt($packing_list['datecreated']) ?></td>
                            <?php 
                            $approve_data = '';
                            if($packing_list['approval'] == 1){
                              $approve_data = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'._l('approved').'</span><span class="hide">, </span></span>&nbsp';
                            }elseif($packing_list['approval'] == 0){
                              $approve_data = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'._l('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
                            }elseif($packing_list['approval'] == -1){
                              $approve_data = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'._l('reject').'</span><span class="hide">, </span></span>&nbsp';
                            }
                            ?>
                            <td class="text-right"><?php echo new_html_entity_decode($approve_data); ?></td>
                          </tr>
                        <?php  } ?>
                      </tbody>
                    </table>

                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
            <div role="tabpanel" class="tab-pane" id="tab_activilog">
               <div class="panel_s no-shadow">
                <div class="activity-feed">
                 <?php foreach($activity_log as $log){ ?>
                   <div class="feed-item">
                    <div class="date">
                      <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($log['date']); ?>">
                        <?php echo time_ago($log['date']); ?>
                      </span>
                      <?php if($log['staffid'] == get_staff_user_id() || is_admin() || has_permission('wh_stock_export','','delete')){ ?>
                        <a href="#" class="pull-right text-danger" onclick="delete_wh_activitylog(this,<?php echo new_html_entity_decode($log['id']); ?>);return false;"><i class="fa fa fa-times"></i></a>
                      <?php } ?>
                    </div>
                    <div class="text">
                     <?php if($log['staffid'] != 0){ ?>
                       <a href="<?php echo admin_url('profile/'.$log["staffid"]); ?>">
                         <?php echo staff_profile_image($log['staffid'],array('staff-profile-xs-image pull-left mright5'));
                         ?>
                       </a>
                       <?php
                     }
                     $additional_data = '';
                     if(!empty($log['additional_data'])){
                       $additional_data = unserialize($log['additional_data']);
                       echo ($log['staffid'] == 0) ? _l($log['description'],$additional_data) : $log['full_name'] .' - '._l($log['description'],$additional_data);
                     } else {
                      echo new_html_entity_decode($log['full_name']) . ' - ';
                        echo _l($log['description']);
                    }
                    ?>
                  </div>

                </div>
              <?php } ?>
            </div>
            <div class="col-md-12">
             <?php echo render_textarea('wh_activity_textarea','','',array('placeholder'=>_l('wh_activilog')),array(),'mtop15'); ?>
             <div class="text-right">
              <button id="wh_enter_activity" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
          </div>
          <div class="clearfix"></div>
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
                     <button onclick="sign_request(<?php echo new_html_entity_decode($goods_delivery->id); ?>);" autocomplete="off" class="btn btn-success sign_request_class"><?php echo _l('e_signature_sign'); ?></button>
                    </div>
        

                </div>
             </div>
          </div>

      </div>
   </div>
</div>

<?php require 'modules/warehouse/assets/js/view_delivery_js.php';?>
</body>
</html>