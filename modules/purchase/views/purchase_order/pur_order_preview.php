<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_hidden('_attachment_sale_id',$estimate->id); ?>
<?php echo form_hidden('_attachment_sale_type','estimate'); ?>
<?php
   $base_currency = get_base_currency_pur();
    if($estimate->currency != 0){
      $base_currency = pur_get_currency_by_id($estimate->currency);
    }
 ?>
<div class="col-md-12 no-padding">
   <div class="panel_s">
      <div class="panel-body">
         <?php if($estimate->approve_status == 1){ ?>
           <div class="ribbon info span_style"><span><?php echo _l('purchase_draft'); ?></span></div>
       <?php }elseif($estimate->approve_status == 2){ ?>
         <div class="ribbon success"><span><?php echo _l('purchase_approved'); ?></span></div>
       <?php }elseif($estimate->approve_status == 3){ ?>  
         <div class="ribbon warning"><span><?php echo _l('pur_rejected'); ?></span></div>
       <?php }elseif ($estimate->approve_status == 4) { ?>
         <div class="ribbon danger"><span><?php echo _l('cancelled'); ?></span></div>
      <?php  } ?>
         <div class="horizontal-scrollable-tabs preview-tabs-top">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="active">
                     <a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
                     <?php echo _l('pur_order'); ?>
                     </a>
                  </li>
                  <li role="presentation">
                     <a href="#payment_record" aria-controls="payment_record" role="tab" data-toggle="tab">
                     <?php echo _l('payment_record'); ?>
                     </a>
                  </li>   
                  <li role="presentation">
                     <a href="#tab_reminders" onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo pur_html_entity_decode($estimate->id) ;?> + '/' + 'purchase_order', undefined, undefined, undefined,[1,'asc']); return false;" aria-controls="tab_reminders" role="tab" data-toggle="tab">
                     <?php echo _l('estimate_reminders'); ?>
                     <?php
                        $total_reminders = total_rows(db_prefix().'reminders',
                          array(
                           'isnotified'=>0,
                           'staff'=>get_staff_user_id(),
                           'rel_type'=>'purchase_order',
                           'rel_id'=>$estimate->id
                           )
                          );
                        if($total_reminders > 0){
                          echo '<span class="badge">'.$total_reminders.'</span>';
                        }
                        ?>
                     </a>
                  </li>
                     <?php
                     $customer_custom_fields = false;
                     if(total_rows(db_prefix().'customfields',array('fieldto'=>'pur_order','active'=>1)) > 0 ){
                          $customer_custom_fields = true;
                      ?>
                  <li role="presentation" >
                     <a href="#custom_fields" aria-controls="custom_fields" role="tab" data-toggle="tab">
                     <?php echo _l( 'custom_fields'); ?>
                     </a>
                  </li>
                  <?php } ?>
                  <li role="presentation">
                     <a href="#tab_tasks" onclick="init_rel_tasks_table(<?php echo pur_html_entity_decode($estimate->id); ?>,'pur_order'); return false;" aria-controls="tab_tasks" role="tab" data-toggle="tab">
                     <?php echo _l('tasks'); ?>
                     </a>
                  </li>
                  <li role="presentation" class="tab-separator">
                     <a href="#tab_notes" onclick="get_sales_notes(<?php echo pur_html_entity_decode($estimate->id); ?>,'purchase'); return false" aria-controls="tab_notes" role="tab" data-toggle="tab">
                     <?php echo _l('estimate_notes'); ?>
                     <span class="notes-total">
                        <?php $totalNotes       = total_rows(db_prefix().'notes', ['rel_id' => $estimate->id, 'rel_type' => 'purchase_order']);
                        if($totalNotes > 0){ ?>
                           <span class="badge"><?php echo ($totalNotes); ?></span>
                        <?php } ?>
                     </span>
                     </a>
                  </li>

                  <li role="presentation" class="tab-separator">
                    <?php
                              $totalComments = total_rows(db_prefix().'pur_comments',['rel_id' => $estimate->id, 'rel_type' => 'pur_order']);
                              ?>
                     <a href="#discuss" aria-controls="discuss" role="tab" data-toggle="tab">
                     <?php echo _l('pur_discuss'); ?>
                      <span class="badge comments-indicator<?php echo $totalComments == 0 ? ' hide' : ''; ?>"><?php echo $totalComments; ?></span>
                     </a>
                  </li> 
                  
                  <li role="presentation" class="tab-separator">
                     <a href="#attachment" aria-controls="attachment" role="tab" data-toggle="tab">
                     <?php echo _l('attachment'); ?>
                     </a>
                  </li>  
                  
                  <li role="presentation" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>" class="tab-separator toggle_view">
                     <a href="#" onclick="small_table_full_view(); return false;">
                     <i class="fa fa-expand"></i></a>
                  </li>
               </ul>
            </div>
         </div>
         <div class="row">
            <div class="col-md-4">
              <p class="bold p_mar"><?php echo _l('vendor').': '?><a href="<?php echo admin_url('purchase/vendor/'.$estimate->vendor); ?>"><?php echo get_vendor_company_name($estimate->vendor); ?></a></p>
              <?php 
                $order_status_class = '';
                $order_status_text = '';
                if($estimate->order_status == 'new'){
                  $order_status_class = 'label-info';
                  $order_status_text = _l('new_order');
                }else if($estimate->order_status == 'delivered'){
                  $order_status_class = 'label-success';
                  $order_status_text = _l('delivered');
                }else if($estimate->order_status == 'confirmed'){
                  $order_status_class = 'label-warning';
                  $order_status_text = _l('confirmed');
                }else if($estimate->order_status == 'cancelled'){
                  $order_status_class = 'label-danger';
                  $order_status_text = _l('cancelled');
                }else if($estimate->order_status == 'return'){
                   $order_status_class = 'label-warning';
                   $order_status_text = _l('pur_return');
                }
               ?>

               <?php if($estimate->order_status != null){ ?>
               <p class="bold p_mar"><?php echo _l('order_status').': '; ?><span class="label <?php echo pur_html_entity_decode($order_status_class); ?>"><?php echo pur_html_entity_decode($order_status_text); ?></span></p>
               <?php } ?>

               <?php $clients_ids = explode(',', $estimate->clients ?? ''); ?>
               <?php if(count($clients_ids) > 0){ ?>
              <p class="bold p_mar"><?php echo _l('clients').': '?></p>
                <?php  foreach ($clients_ids as $ids) {
                ?>
                  <a href="<?php echo admin_url('clients/client/'.$ids); ?>"><span class="label label-tag"><?php echo get_company_name($ids); ?></span></a>
               <?php } ?>
             <?php } ?>
              
            </div>
            <div class="col-md-8">
               <div class="btn-group pull-right">
                  <a href="javascript:void(0)" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
                  <ul class="dropdown-menu dropdown-menu-right">
                     <li class="hidden-xs"><a href="<?php echo admin_url('purchase/purorder_pdf/'.$estimate->id.'?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                     <li class="hidden-xs"><a href="<?php echo admin_url('purchase/purorder_pdf/'.$estimate->id.'?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                     <li><a href="<?php echo admin_url('purchase/purorder_pdf/'.$estimate->id); ?>"><?php echo _l('download'); ?></a></li>
                     <li>
                        <a href="<?php echo admin_url('purchase/purorder_pdf/'.$estimate->id.'?print=true'); ?>" target="_blank">
                        <?php echo _l('print'); ?>
                        </a>
                     </li>
                  </ul>

                  <a href="javascript:void(0)" onclick="send_po('<?php echo pur_html_entity_decode($estimate->id); ?>'); return false;" class="btn btn-success mleft10" ><i class="fa fa-envelope" data-toggle="tooltip" title="<?php echo _l('send_to_vendor') ?>"></i></a>
               </div>

               <?php if(is_admin()){ ?>
                 <div class="btn-group pull-right mright5">
                     <button type="button" class="btn btn-default  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <?php echo _l('more'); ?> <span class="caret"></span>
                     </button>
                     <ul class="dropdown-menu dropdown-menu-right">
                      <?php if(has_permission('purchase_order_return','','edit') &&  $estimate->status == 'confirm'){ ?>
                        <li>
                             <a href="#" onclick="refund_order_return(); return false;" id="order_return_refund">
                             <?php echo _l('refund'); ?>
                             </a>
                          </li>
                      <?php } ?>

                        <?php if(is_admin()){ ?>
                        <?php 
                          $statuses = [
                            'new',
                            'delivered',
                            'confirmed',
                            'cancelled',
                            'return',
                          ]; 
                        ?>

                        <?php foreach($statuses as $status){ ?>
                          <?php if($status != $estimate->order_status){ ?>
                            <li>
                               <a href="<?php echo admin_url('purchase/mark_pur_order_as/'.$status.'/'.$estimate->id); ?>"><?php echo _l('invoice_mark_as',_l($status)); ?></a>
                            </li>
                        <?php } ?>
                      <?php } ?>    
                        <?php } ?>

                        <?php if(has_permission('purchase_order_return','','edit') && $estimate->approve_status != 2){ ?>
                        <li>
                           <a href="<?php echo admin_url('purchase/pur_order/'.$estimate->id); ?>"><?php echo _l('edit'); ?></a>
                        </li>
                        <?php } ?>

                        
                        <?php if(has_permission('purchase_order_return','','delete')){ ?>
                        <li>
                           <a href="<?php echo admin_url('purchase/delete_pur_order/'.$estimate->id); ?>" class="text-danger delete-text _delete"><?php echo _l('delete_invoice'); ?></a>
                        </li>
                        <?php } ?>
           
                    
                     </ul>
                  </div>
                <?php } ?>

               <?php if($estimate->approve_status != 2){ ?>
                  <div class="pull-right _buttons mright10">
                     <?php if(has_permission('purchase_orders','','edit')){ ?>
                     <a href="<?php echo admin_url('purchase/pur_order/'.$estimate->id); ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('edit'); ?>" data-placement="bottom"><i class="fa fa-pencil-square"></i></a>
                     <?php } ?>

                  </div>
               <?php } ?>

               <?php if(has_permission('purchase_order_change_approve_status', '', 'edit')){ ?>     
               <select name="status" id="status" class="selectpicker pull-right mright10" onchange="change_status_pur_order(this,<?php echo ($estimate->id); ?>); return false;" data-live-search="true" data-width="35%" data-none-selected-text="<?php echo _l('pur_change_status_to'); ?>">
                 <option value=""></option>
                 <option value="1" class="<?php if($estimate->approve_status == 1) { echo 'hide';}?>"><?php echo _l('purchase_draft'); ?></option>
                 <option value="2" class="<?php if($estimate->approve_status == 2) { echo 'hide';}?>"><?php echo _l('purchase_approved'); ?></option>
                 <option value="3" class="<?php if($estimate->approve_status == 3) { echo 'hide';}?>"><?php echo _l('pur_rejected'); ?></option>
                 <option value="4" class="<?php if($estimate->approve_status == 4) { echo 'hide';}?>"><?php echo _l('pur_canceled'); ?></option>
               </select>
              <?php } ?>
               
               <div class="col-md-12 padr_div_0">
                  <br>
                  <div class="pull-right _buttons  ">
                     <a href="javascript:void(0)" onclick="copy_public_link(<?php echo pur_html_entity_decode($estimate->id); ?>); return false;" class="btn btn-warning btn-with-tooltip mleft10" data-toggle="tooltip" title="<?php if($estimate->hash == ''){ echo _l('create_public_link'); }else{ echo _l('copy_public_link'); } ?>" data-placement="bottom"><i class="fa fa-clone "></i></a>
                  </div>
                  <div class="pull-right col-md-6">
                     <?php if($estimate->hash != '' && $estimate->hash != null){
                      echo render_input('link_public','', site_url('purchase/vendors_portal/pur_order/'.$estimate->id.'/'.$estimate->hash)); 
                     }else{
                         echo render_input('link_public','', ''); 
                     } ?>
                  </div>
               </div>
            </div>
         </div>

         <div class="clearfix"></div>
         <hr class="hr-panel-heading" />
         <div class="tab-content">
            <?php if($customer_custom_fields) { ?>
              <div role="tabpanel" class="tab-pane" id="custom_fields">
                <?php echo form_open(admin_url('purchase/update_customfield_po/'.$estimate->id)); ?>
                 <?php $rel_id=( isset($estimate) ? $estimate->id : false); ?>
                 <?php echo render_custom_fields( 'pur_order',$rel_id); ?>

                <div class="bor_top_0" >
                   <button id="obgy_btn2" type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
               </div>
                <?php echo form_close(); ?>
              </div>
             <?php } ?>
            <div role="tabpanel" class="tab-pane" id="tab_tasks">
               <?php init_relation_tasks_table(array('data-new-rel-id'=>$estimate->id,'data-new-rel-type'=>'pur_order')); ?>
            </div>
            <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
               <div id="estimate-preview">
                  <div class="row">

                    <div class="<?php if(!is_mobile()){ echo 'pull-right'; } ?> mleft5 mright5">
                          <?php if($check_appr && $check_appr != false){
                          if($estimate->approve_status == 1 && ($check_approve_status == false || $check_approve_status == 'reject')){ ?>
                      <a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo pur_html_entity_decode($estimate->id); ?>); return false;"><?php echo _l('send_request_approve_pur'); ?></a>
                    <?php } }
                      if(isset($check_approve_status['staffid'])){
                          ?>
                          <?php 
                      if(in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign) && $estimate->status == 1){ ?>
                          <div class="btn-group" >
                                 <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>
                                 <ul class="dropdown-menu dropdown-menu-<?php if(is_mobile()){ echo 'left';}else{ echo 'right';} ?> ul_style" >
                                  <li>
                                    <div class="col-md-12">
                                      <?php echo render_textarea('reason', 'reason'); ?>
                                    </div>
                                  </li>
                                    <li>
                                      <div class="row text-right col-md-12">
                                        <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_request(<?php echo pur_html_entity_decode($estimate->id); ?>); return false;" class="btn btn-success mright15" ><?php echo _l('approve'); ?></a>
                                       <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_request(<?php echo pur_html_entity_decode($estimate->id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a></div>
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
                     
                     <div class="project-overview-right">
                        <?php if(count($list_approve_status) > 0 ){ ?>
                          
                         <div class="row">
                           <div class="col-md-12 project-overview-expenses-finance">
                            <?php 
                              $this->load->model('staff_model');
                              $enter_charge_code = 0;
                            foreach ($list_approve_status as $value) {
                              $value['staffid'] = explode(', ',$value['staffid'] ?? '');
                              if($value['action'] == 'sign'){
                             ?>
                             <div class="col-md-4 apr_div">
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
                                  echo pur_html_entity_decode($staff_name); 
                                  ?></p>
                                 <?php if($value['approve'] == 2){ 
                                  ?>
                                  <img src="<?php echo site_url(PURCHASE_PATH.'pur_order/signature/'.$estimate->id.'/signature_'.$value['id'].'.png'); ?>" class="img_style">
                                   <br><br>
                                 <p class="bold text-center text-success"><?php echo _l('signed').' '._dt($value['date']); ?></p> 
                                 <?php } ?> 
                                   
                            </div>
                            <?php }else{ ?>
                            <div class="col-md-4 apr_div" >
                                 <p class="text-uppercase text-muted no-mtop bold">
                                  <?php
                                  $staff_name = '';
                                  foreach ($value['staffid'] as $key => $val) {
                                    if($staff_name != '')
                                    {
                                      $staff_name .= ' or ';
                                    }
                                    $staff_name .= $this->staff_model->get($val)->firstname;
                                  }
                                  echo pur_html_entity_decode($staff_name); 
                                  ?></p>
                                 <?php if($value['approve'] == 2){ 
                                  ?>
                                  <img src="<?php echo site_url(PURCHASE_PATH.'approval/approved.png'); ?>" class="img_style">
                                 <?php }elseif($value['approve'] == 3){ ?>
                                    <img src="<?php echo site_url(PURCHASE_PATH.'approval/rejected.png'); ?>" class="img_style">
                                <?php } ?> 
                                <br><br>  
                                 <p><?php echo pur_html_entity_decode($value['note']) ?></p>  
                                <p class="bold text-center text-<?php if($value['approve'] == 2){ echo 'success'; }elseif($value['approve'] == 3){ echo 'danger'; } ?>"><?php echo _dt($value['date']); ?></p> 
                            </div>
                            <?php }
                            } ?>
                           </div>
                        </div>
                        
                        <?php } ?>
                        </div>
                    
                     <?php if($estimate->estimate != 0){ ?>
                     <div class="col-md-12">
                        <h4 class="font-medium mbot15"><?php echo _l('',array(
                          '',
                           '',
                           '<a href="'.admin_url('purchase/quotations/'.$estimate->estimate).'" target="_blank">' . format_pur_estimate_number($estimate->id) . '</a>',
                           )); ?></h4>
                     </div>
                     <?php } ?>
                     <div class="col-md-6 col-sm-6">
                        <h4 class="bold mbot5">
                         
                           <a href="<?php echo admin_url('purchase/purchase_order/'.$estimate->id); ?>">
                           <span id="estimate-number">
                           <?php echo pur_html_entity_decode($estimate->pur_order_number.' - '.$estimate->pur_order_name); ?>
                           </span>
                           </a>
                        </h4>

                        <address class="mbot5">
                           <?php echo format_organization_info(); ?>
                        </address>

                        <?php $custom_fields = get_custom_fields('pur_order');
                         foreach($custom_fields as $field){ ?>
                          <?php $value = get_custom_field_value($estimate->id,$field['id'],'pur_order');
                              if($value == ''){continue;}?>
                          <div class="task-info">
                          <h5 class="task-info-custom-field task-info-custom-field-<?php echo $field['id']; ?>">
                            <i class="fa task-info-icon fa-fw fa-lg fa-pencil-square"></i>
                            <?php echo $field['name']; ?>: <?php echo $value; ?>
                          </h5>
                           </div>
                          <?php } ?>
                     </div>
                     
                     
                  </div>
                  <div class="row">
                     <div class="col-md-12">

                        <?php if($estimate->approve_status != 2){ ?>
                          <a href="javascript:void(0)" onclick="refresh_order_value(<?php echo pur_html_entity_decode($estimate->id); ?>); return false;" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title="<?php echo _l('refresh_value_note'); ?>"><i class="fa fa-refresh"></i> <?php echo ' '._l('refresh_order_value'); ?></a>
                        <?php } ?>
                       

                        <div class="table-responsive">
                           <table class="table items items-preview estimate-items-preview" data-type="estimate">
                              <thead>
                                 <tr>
                                    <th align="center">#</th>
                                    <th class="description" width="50%" align="left"><?php echo _l('items'); ?></th>
                                    <th align="right"><?php echo _l('purchase_quantity'); ?></th>
                                    <th align="right"><?php echo _l('purchase_unit_price'); ?></th>
                                    <th align="right"><?php echo _l('into_money'); ?></th>
                                    <?php if(get_option('show_purchase_tax_column') == 1){ ?>
                                    <th align="right"><?php echo _l('tax'); ?></th>
                                    <?php } ?>
                                    <th align="right"><?php echo _l('sub_total'); ?></th>
                                    <th align="right"><?php echo _l('discount(%)'); ?></th>
                                    <th align="right"><?php echo _l('discount(money)'); ?></th>
                                    <th align="right"><?php echo _l('total'); ?></th>
                                 </tr>
                              </thead>
                              <tbody class="ui-sortable">

                                 <?php if(count($estimate_detail) > 0){
                                    $count = 1;
                                    $t_mn = 0;
                                    $item_discount = 0;
                                 foreach($estimate_detail as $es) { ?>
                                 <tr nobr="true" class="sortable">
                                    <td class="dragger item_no ui-sortable-handle" align="center"><?php echo pur_html_entity_decode($count); ?></td>
                                    <td class="description" align="left;"><span><strong><?php 
                                    $item = get_item_hp($es['item_code']); 
                                    if(isset($item) && isset($item->commodity_code) && isset($item->description)){
                                       echo pur_html_entity_decode($item->commodity_code.' - '.$item->description);
                                    }else{
                                       echo pur_html_entity_decode($es['item_name']);
                                    }
                                    ?></strong><?php if($es['description'] != ''){ ?><br><span><?php echo pur_html_entity_decode($es['description']); ?></span><?php } ?></td>
                                    <td align="right"  width="12%"><?php echo pur_html_entity_decode($es['quantity']); ?></td>
                                    <td align="right"><?php echo app_format_money($es['unit_price'],$base_currency->symbol); ?></td>
                                    <td align="right"><?php echo app_format_money($es['into_money'],$base_currency->symbol); ?></td>
                                    <?php if(get_option('show_purchase_tax_column') == 1){ ?>
                                    <td align="right"><?php echo app_format_money(($es['total'] - $es['into_money']),$base_currency->symbol); ?></td>
                                    <?php } ?>
                                    <td class="amount" align="right"><?php echo app_format_money($es['total'],$base_currency->symbol); ?></td>
                                    <td class="amount" width="12%" align="right"><?php echo ($es['discount_%'].'%'); ?></td>
                                    <td class="amount" align="right"><?php echo app_format_money($es['discount_money'],$base_currency->symbol); ?></td>
                                    <td class="amount" align="right"><?php echo app_format_money($es['total_money'],$base_currency->symbol); ?></td>
                                 </tr>
                              <?php 
                              $t_mn += $es['total_money'];
                              $item_discount += $es['discount_money'];
                              $count++; } } ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <div class="col-md-5 col-md-offset-7">
                        <table class="table text-right">
                           <tbody>
                              <tr id="subtotal">
                                 <td><span class="bold"><?php echo _l('subtotal'); ?></span>
                                 </td>
                                 <td class="subtotal">
                                    <?php echo app_format_money($estimate->subtotal,$base_currency->symbol); ?>
                                 </td>
                              </tr>

                              <?php if($tax_data['preview_html'] != ''){
                                echo pur_html_entity_decode($tax_data['preview_html']);
                              } ?>


                              <?php if(($estimate->discount_total + $item_discount) > 0){ ?>
                              
                              <tr id="subtotal">
                                 <td><span class="bold"><?php echo _l('discount_total(money)'); ?></span>
                                 </td>
                                 <td class="subtotal">
                                    <?php echo '-'.app_format_money(($estimate->discount_total + $item_discount), $base_currency->symbol); ?>
                                 </td>
                              </tr>
                              <?php } ?>

                              <?php if($estimate->shipping_fee > 0){ ?>
                                <tr id="subtotal">
                                  <td><span class="bold"><?php echo _l('pur_shipping_fee'); ?></span></td>
                                  <td class="subtotal">
                                    <?php echo app_format_money($estimate->shipping_fee, $base_currency->symbol); ?>
                                  </td>
                                </tr>
                              <?php } ?>


                              <tr id="subtotal">
                                 <td><span class="bold"><?php echo _l('total'); ?></span>
                                 </td>
                                 <td class="subtotal bold">
                                    <?php echo app_format_money($estimate->total, $base_currency->symbol); ?>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>   

                     <?php if($estimate->vendornote != ''){ ?>
                     <div class="col-md-12 mtop15">
                        <p class="bold text-muted"><?php echo _l('estimate_note'); ?></p>
                        <p><?php echo nl2br($estimate->vendornote); ?></p>
                     </div>
                     <?php } ?>
                                                            
                     <?php if($estimate->terms != ''){ ?>
                     <div class="col-md-12 mtop15">
                        <p class="bold text-muted"><?php echo _l('terms_and_conditions'); ?></p>
                        <p><?php echo pur_html_entity_decode($estimate->terms); ?></p>
                     </div>
                     <?php } ?>
                  </div>
               </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_reminders">
               <a href="#" data-toggle="modal" class="btn btn-info" data-target=".reminder-modal-purchase_order-<?php echo pur_html_entity_decode($estimate->id); ?>"><i class="fa fa-bell-o"></i> <?php echo _l('estimate_set_reminder_title'); ?></a>
               <hr />
               <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified')), 'reminders'); ?>
               <?php $this->load->view('admin/includes/modals/reminder',array('id'=>$estimate->id,'name'=>'purchase_order','members'=>$members,'reminder_title'=>_l('estimate_set_reminder_title'))); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_notes">
               <?php echo form_open(admin_url('purchase/add_note/'.$estimate->id),array('id'=>'sales-notes','class'=>'estimate-notes-form')); ?>
               <?php echo render_textarea('description'); ?>
               <div class="text-right">
                  <button type="submit" class="btn btn-info mtop15 mbot15"><?php echo _l('estimate_add_note'); ?></button>
               </div>
               <?php echo form_close(); ?>
               <hr />
               <div class="panel_s mtop20 no-shadow" id="sales_notes_area">
               </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="discuss">
              <div class="row contract-comments mtop15">
                 <div class="col-md-12">
                    <div id="contract-comments"></div>
                    <div class="clearfix"></div>
                    <textarea name="content" id="comment" rows="4" class="form-control mtop15 contract-comment"></textarea>
                    <button type="button" class="btn btn-info mtop10 pull-right" onclick="add_contract_comment();"><?php echo _l('proposal_add_comment'); ?></button>
                 </div>
              </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="attachment">
               <?php echo form_open_multipart(admin_url('purchase/purchase_order_attachment/'.$estimate->id),array('id'=>'partograph-attachments-upload')); ?>
                <?php echo render_input('file','file','','file'); ?>

                <div class="col-md-12 pad_div_0">

               </div>
               <div class="modal-footer bor_top_0" >
                   <button id="obgy_btn2" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
               </div>
                <?php echo form_close(); ?>
               
               <div class="col-md-12" id="purorder_pv_file">
                                    <?php
                                        $file_html = '';
                                        if(count($pur_order_attachments) > 0){
                                            $file_html .= '<hr />';
                                            foreach ($pur_order_attachments as $f) {
                                                $href_url = site_url(PURCHASE_PATH.'pur_order/'.$f['rel_id'].'/'.$f['file_name']).'" download';
                                                                if(!empty($f['external'])){
                                                                  $href_url = $f['external_link'];
                                                                }
                                               $file_html .= '<div class="mbot15 row inline-block full-width" data-attachment-id="'. $f['id'].'">
                                              <div class="col-md-8">
                                                 <a name="preview-purorder-btn" onclick="preview_purorder_btn(this); return false;" rel_id = "'. $f['rel_id']. '" id = "'.$f['id'].'" href="Javascript:void(0);" class="mbot10 mright5 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'. _l('preview_file').'"><i class="fa fa-eye"></i></a>
                                                 <div class="pull-left"><i class="'. get_mime_class($f['filetype']).'"></i></div>
                                                 <a href=" '. $href_url.'" target="_blank" download>'.$f['file_name'].'</a>
                                                 <br />
                                                 <small class="text-muted">'.$f['filetype'].'</small>
                                              </div>
                                              <div class="col-md-4 text-right">';
                                                if($f['staffid'] == get_staff_user_id() || is_admin()){
                                                $file_html .= '<a href="#" class="text-danger" onclick="delete_purorder_attachment('. $f['id'].'); return false;"><i class="fa fa-times"></i></a>';
                                                } 
                                               $file_html .= '</div></div>';
                                            }
                                            echo pur_html_entity_decode($file_html);
                                        }
                                     ?>
                                  </div>

               <div id="purorder_file_data"></div>
            </div>

            <div role="tabpanel" class="tab-pane" id="payment_record">
               <div class="col-md-6 pad_div_0" >
               <h4 class="font-medium mbot15 bold text-success"><?php echo _l('payment_for_pur_order').' '.$estimate->pur_order_number; ?></h4>
               </div>
               <div class="col-md-6 padr_div_0">
                
               <!-- <?php if(purorder_left_to_pay($estimate->id) > 0){ ?>
               <a href="#" onclick="add_payment(<?php echo pur_html_entity_decode($estimate->id); ?>); return false;" class="btn btn-success pull-right"><i class="fa fa-plus"></i><?php echo ' '._l('payment'); ?></a>
               <?php } ?> -->

               <?php if(purorder_left_to_pay($estimate->id) < $estimate->total){ ?>
               <a href="#" onclick="convert_to_purchase_inv(<?php echo pur_html_entity_decode($estimate->id); ?> ); return false;" class="btn btn-info pull-right mright5" data-toggle="tooltip" data-placement="top" title="<?php echo _l('convert_to_payment_of_purchase_inv'); ?>" ><i class="fa fa-refresh"></i></a>
                <?php } ?>

                <?php if(purorder_inv_left_to_pay($estimate->id) > 0){ ?>
                  <?php if(total_inv_value_by_pur_order($estimate->id) > 0){ ?>
                    
                    <a href="#" onclick="add_payment_with_inv(<?php echo pur_html_entity_decode($estimate->id); ?>); return false;" class="btn btn-success pull-right"><i class="fa fa-plus"></i><?php echo ' '._l('payment'); ?></a>

                  <?php }else{ ?>

                     <a href="#" onclick="add_payment(<?php echo pur_html_entity_decode($estimate->id); ?>); return false;" class="btn btn-success pull-right"><i class="fa fa-plus"></i><?php echo ' '._l('payment'); ?></a>

                  <?php } ?>
                <?php } ?>
               </div>
               <div class="clearfix"></div>
               <table class="table dt-table">
                   <thead>
                     <th><?php echo _l('payments_table_amount_heading'); ?></th>
                      <th><?php echo _l('payments_table_mode_heading'); ?></th>
                      <th><?php echo _l('payment_transaction_id'); ?></th>
                      
                      <th><?php echo _l('payments_table_date_heading'); ?></th>
                      <th><?php echo _l('options'); ?></th>
                   </thead>
                  <tbody>
                     <?php foreach($payment as $pay) { ?>
                      <?php
                        $base_currency = $base_currency;
                        $invoice_currency_id = get_invoice_currency_id($pay['pur_invoice']);
                        if($invoice_currency_id != 0){
                          $base_currency = pur_get_currency_by_id($invoice_currency_id);
                        }
                       ?>
                        <tr>
                           <td><?php echo app_format_money($pay['amount'],$base_currency->symbol); ?></td>
                           <td><?php echo get_payment_mode_by_id($pay['paymentmode']); ?></td>
                           <td><?php echo pur_html_entity_decode($pay['transactionid']); ?></td>
                           <td><?php echo _d($pay['date']); ?></td>
                           <td> 
                            <?php if(has_permission('purchase_invoices','','edit') || is_admin()){ ?>
                              <a href="<?php echo admin_url('purchase/payment_invoice/'.$pay['id']); ?>" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="<?php echo _l('view'); ?>" ><i class="fa fa-eye "></i></a>
                            <?php } ?>
                            <?php if(has_permission('purchase_invoices','','delete') || is_admin()){ ?>
                            <a href="<?php echo admin_url('purchase/delete_payment/'.$pay['id'].'/'.$estimate->id); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                            <?php } ?>
                           </td>
                        </tr>
                     <?php } ?>
                  </tbody>
               </table>
            </div>

         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="payment_record_pur" tabindex="-1" role="dialog">
    <div class="modal-dialog dialog_30" >
        <?php echo form_open(admin_url('purchase/add_payment_on_po/'.$estimate->id),array('id'=>'purorder-add_payment-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_payment'); ?></span>
                    <span class="add-title"><?php echo _l('new_payment'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional"></div>
                     <?php echo render_input('amount','amount',purorder_inv_left_to_pay($estimate->id),'number', array('max' => purorder_inv_left_to_pay($estimate->id)) ); ?>
                        <?php echo render_date_input('date','payment_edit_date'); ?>
                        <?php echo render_select('paymentmode',$payment_modes,array('id','name'),'payment_mode'); ?>
                        
                        <?php echo render_input('transactionid','payment_transaction_id'); ?>
                        <?php echo render_textarea('note','note','',array('rows'=>7)); ?>

                    </div>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<div class="modal fade" id="payment_record_pur_with_inv" tabindex="-1" role="dialog">
    <div class="modal-dialog dialog_30" >
        <?php echo form_open(admin_url('purchase/add_payment_on_po_with_inv/'.$estimate->id),array('id'=>'purorder-add_payment_with_inv-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="add-title"><?php echo _l('new_payment'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="inv_additional"></div>

                     <?php $selected = $invoices[0]['id'];
                     echo render_select('pur_invoice',$invoices,array('id','invoice_number', 'total'),'pur_invoice', $selected, array('onchange' => 'pur_inv_payment_change(this); return false;')); ?>

                     <?php echo render_input('amount','amount',purinvoice_left_to_pay($invoices[0]['id']),'number', array('max' => purinvoice_left_to_pay($invoices[0]['id']))); ?>
                        <?php echo render_date_input('date','payment_edit_date'); ?>
                        <?php echo render_select('paymentmode',$payment_modes,array('id','name'),'payment_mode'); ?>
                        
                        <?php echo render_input('transactionid','payment_transaction_id'); ?>
                        <?php echo render_textarea('note','note','',array('rows'=>7)); ?>

                    </div>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    
<div class="modal fade" id="add_action" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         
        <div class="modal-body">
         <p class="bold" id="signatureLabel"><?php echo _l('signature'); ?></p>
            <div class="signature-pad--body">
              <canvas id="signature" height="130" width="550"></canvas>
            </div>
            <input type="text" class="ip_style" tabindex="-1" name="signature" id="signatureInput">
            <div class="dispay-block">
              <button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>
            
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
           <button onclick="sign_request(<?php echo pur_html_entity_decode($estimate->id); ?>);" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" class="btn btn-success"><?php echo _l('e_signature_sign'); ?></button>
          </div>

      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="send_po" tabindex="-1" role="dialog">
  <div class="modal-dialog">
      <?php echo form_open_multipart(admin_url('purchase/send_po'),array('id'=>'send_po-form')); ?>
      <div class="modal-content modal_withd">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">
                  <span><?php echo _l('send_a_po'); ?></span>
              </h4>
          </div>
          <div class="modal-body">
              <div id="additional_po"></div>
              <div class="row">
                <div class="col-md-12 form-group">
                  <label for="send_to"><span class="text-danger">* </span><?php echo _l('send_to'); ?></label>
                    <select name="send_to[]" id="send_to" class="selectpicker" required multiple="true"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                        <?php foreach($vendor_contacts as $s) { ?>
                        <option value="<?php echo pur_html_entity_decode($s['email']); ?>" data-subtext="<?php echo pur_html_entity_decode($s['firstname'].' '.$s['lastname']); ?>" selected><?php echo pur_html_entity_decode($s['email']); ?></option>
                          <?php } ?>
                    </select>
                    <br>
                </div>     
                <div class="col-md-12">
                  <div class="checkbox checkbox-primary">
                      <input type="checkbox" name="attach_pdf" id="attach_pdf" checked>
                      <label for="attach_pdf"><?php echo _l('attach_purchase_order_pdf'); ?></label>
                  </div>
                </div>

                <div class="col-md-12">
                 <?php echo render_textarea('content','additional_content','',array('rows'=>6,'data-task-ae-editor'=>true, !is_mobile() ? 'onclick' : 'onfocus'=>(!isset($routing) || isset($routing) && $routing->description == '' ? 'routing_init_editor(\'.tinymce-task\', {height:200, auto_focus: true});' : '')),array(),'no-mbot','tinymce-task'); ?> 
                </div>     
                <div id="type_care">
                  
                </div>        
              </div>
          </div>
          <div class="modal-footer">
              <button type=""class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
              <button id="sm_btn" type="submit" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-info"><?php echo _l('pur_send'); ?></button>
          </div>
      </div><!-- /.modal-content -->
          <?php echo form_close(); ?>
      </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
    <?php require 'modules/purchase/assets/js/pur_order_preview_js.php';?>
