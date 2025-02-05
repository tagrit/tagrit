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
         <?php if($estimate->status == 1){ ?>
           <div class="ribbon info"><span class="fontz9"><?php echo _l('purchase_draft'); ?></span></div>
       <?php }elseif($estimate->status == 2){ ?>
         <div class="ribbon success"><span><?php echo _l('purchase_approved'); ?></span></div>
       <?php }elseif($estimate->status == 3){ ?>  
         <div class="ribbon danger"><span><?php echo _l('pur_rejected'); ?></span></div>
       <?php } ?>
         <div class="horizontal-scrollable-tabs preview-tabs-top">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="active">
                     <a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
                     <?php echo _l('estimate'); ?>
                     </a>
                  </li>
                  <li role="presentation">
                     <a href="#tab_tasks" onclick="init_rel_tasks_table(<?php echo pur_html_entity_decode($estimate->id); ?>,'pur_quotation'); return false;" aria-controls="tab_tasks" role="tab" data-toggle="tab">
                     <?php echo _l('tasks'); ?>
                     </a>
                  </li> 

                  <li role="presentation" class="tab-separator">
                    <?php
                              $totalComments = total_rows(db_prefix().'pur_comments',['rel_id' => $estimate->id, 'rel_type' => 'pur_quotation']);
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
              <p class="bold mtop15" ><?php echo _l('vendor').': '?><a href="<?php echo admin_url('purchase/vendor/'.$estimate->vendor->userid); ?>"><?php echo pur_html_entity_decode($estimate->vendor->company); ?></a></p>
            </div>
            <div class="col-md-8">
               <div class="pull-right _buttons">
              <?php if($estimate->status != 2){ ?>
              
                  <?php if(has_permission('purchase_quotations','','edit')){ ?>
                  <a href="<?php echo admin_url('purchase/estimate/'.$estimate->id); ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('edit_estimate_tooltip'); ?>" data-placement="bottom"><i class="fa fa-pencil-square"></i></a>
                  <?php } ?>
                <?php } ?>
                  <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
                  <ul class="dropdown-menu dropdown-menu-right">
                     <li class="hidden-xs"><a href="<?php echo admin_url('purchase/purestimate_pdf/'.$estimate->id.'?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                     <li class="hidden-xs"><a href="<?php echo admin_url('purchase/purestimate_pdf/'.$estimate->id.'?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                     <li><a href="<?php echo admin_url('purchase/purestimate_pdf/'.$estimate->id); ?>"><?php echo _l('download'); ?></a></li>
                     <li>
                        <a href="<?php echo admin_url('purchase/purestimate_pdf/'.$estimate->id.'?print=true'); ?>" target="_blank">
                        <?php echo _l('print'); ?>
                        </a>
                     </li>
                  </ul>
               </div>
             
              <a href="#" onclick="send_quotation('<?php echo pur_html_entity_decode($estimate->id); ?>'); return false;" class="btn btn-success pull-right mleft5 mright5" ><i class="fa fa-envelope" data-toggle="tooltip" title="<?php echo _l('send_a_quote') ?>"></i></a>

              <?php if(has_permission('purchase_estimate_change_approve_status', '', 'edit')){ ?>
                 <select name="status" id="status" class="selectpicker pull-right mright10" onchange="change_status_pur_estimate(this,<?php echo pur_html_entity_decode($estimate->id); ?>); return false;" data-live-search="true" data-width="35%" data-none-selected-text="<?php echo _l('pur_change_status_to'); ?>">
                   <option value=""></option>
                   <option value="1" class="<?php if($estimate->status == 1) { echo 'hide';}?>"><?php echo _l('purchase_draft'); ?></option>
                   <option value="2" class="<?php if($estimate->status == 2) { echo 'hide';}?>"><?php echo _l('purchase_approved'); ?></option>
                   <option value="3" class="<?php if($estimate->status == 3) { echo 'hide';}?>"><?php echo _l('pur_rejected'); ?></option>
                 </select>
               <?php } ?>
               
               <div class="pull-right mright5">
                            <?php if($check_appr && $check_appr != false){
                            if($estimate->status == 1 && ($check_approve_status == false || $check_approve_status == 'reject')){ ?>
                        <a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo pur_html_entity_decode($estimate->id); ?>); return false;"><?php echo _l('send_request_approve_pur'); ?></a>
                      <?php } }
                        if(isset($check_approve_status['staffid'])){
                            ?>
                            <?php 
                        if(in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign) && $estimate->status == 1){ ?>
                            <div class="btn-group" >
                                   <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>
                                   <ul class="dropdown-menu dropdown-menu-right ul_style" >
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
            </div>
         </div>
         <div class="clearfix"></div>
         <hr class="hr-panel-heading" />
         <div class="tab-content">
           <div role="tabpanel" class="tab-pane" id="tab_tasks">
               <?php init_relation_tasks_table(array('data-new-rel-id'=>$estimate->id,'data-new-rel-type'=>'pur_quotation')); ?>
            </div>

             <div role="tabpanel" class="tab-pane" id="attachment">
               <?php echo form_open_multipart(admin_url('purchase/purchase_estimate_attachment/'.$estimate->id),array('id'=>'partograph-attachments-upload')); ?>
                <?php echo render_input('file','file','','file'); ?>

                <div class="col-md-12 pad_div_0">

               </div>
               <div class="modal-footer bor_top_0" >
                   <button id="obgy_btn2" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
               </div>
                <?php echo form_close(); ?>
               
               <div class="col-md-12" id="estimate_pv_file">
                                    <?php
                                        $file_html = '';
                                        if(count($pur_estimate_attachments) > 0){
                                            $file_html .= '<hr />';
                                            foreach ($pur_estimate_attachments as $f) {
                                                $href_url = site_url(PURCHASE_PATH.'pur_estimate/'.$f['rel_id'].'/'.$f['file_name']).'" download';
                                                                if(!empty($f['external'])){
                                                                  $href_url = $f['external_link'];
                                                                }
                                               $file_html .= '<div class="mbot15 row inline-block full-width" data-attachment-id="'. $f['id'].'">
                                              <div class="col-md-8">
                                                 <a name="preview-estimate-btn" onclick="preview_estimate_btn(this); return false;" rel_id = "'. $f['rel_id']. '" id = "'.$f['id'].'" href="Javascript:void(0);" class="mbot10 mright5 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'. _l('preview_file').'"><i class="fa fa-eye"></i></a>
                                                 <div class="pull-left"><i class="'. get_mime_class($f['filetype']).'"></i></div>
                                                 <a href=" '. $href_url.'" target="_blank" download>'.$f['file_name'].'</a>
                                                 <br />
                                                 <small class="text-muted">'.$f['filetype'].'</small>
                                              </div>
                                              <div class="col-md-4 text-right">';
                                                if($f['staffid'] == get_staff_user_id() || is_admin()){
                                                $file_html .= '<a href="#" class="text-danger" onclick="delete_estimate_attachment('. $f['id'].'); return false;"><i class="fa fa-times"></i></a>';
                                                } 
                                               $file_html .= '</div></div>';
                                            }

                                            echo pur_html_entity_decode($file_html);
                                        }
                                     ?>
                                  </div>

               <div id="estimate_file_data"></div>
            </div>

            <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
               <div id="estimate-preview">
                  <div class="row">
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
                                  <img src="<?php echo site_url(PURCHASE_PATH.'pur_estimate/signature/'.$estimate->id.'/signature_'.$value['id'].'.png'); ?>" class="img_style">
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

                    <?php if($estimate->pur_request){ ?>    
                     <?php if($estimate->pur_request->id != 0){ ?>
                     <div class="col-md-12">
                        <h4 class="font-medium mbot15"><?php echo _l('related_to_pur_request',array(
                           _l('estimate_lowercase'),
                           _l('pur_request'),
                           '<a href="'.admin_url('purchase/view_pur_request/'.$estimate->pur_request->id).'" target="_blank">' . $estimate->pur_request->pur_rq_name . '</a>',
                           )); ?></h4>
                     </div>
                     <?php } ?>
                   <?php } ?>
                     <div class="col-md-6 col-sm-6">
                        <h4 class="bold">
                           <?php
                              $tags = get_tags_in($estimate->id,'estimate');
                              if(count($tags) > 0){
                                echo '<i class="fa fa-tag" aria-hidden="true" data-toggle="tooltip" data-title="'.html_escape(implode(', ',$tags)).'"></i>';
                              }
                              ?>
                           <a href="<?php echo admin_url('purchase/estimate/'.$estimate->id); ?>">
                           <span id="estimate-number">
                           <?php echo format_pur_estimate_number($estimate->id); ?>
                           </span>
                           </a>
                        </h4>
                        <address>
                           <?php echo format_organization_info(); ?>
                        </address>
                     </div>
                     
                  </div>
                  <div class="row">
                     <div class="col-md-12">
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
                                    <th align="right"><?php echo _l('subtotal'); ?></th>
                                    <th align="right"><?php echo _l('discount(%)'); ?></th>
                                    <th align="right"><?php echo _l('discount(money)'); ?></th>
                                    <th align="right"><?php echo _l('total'); ?></th>
                                 </tr>
                              </thead>
                              <tbody class="ui-sortable">

                                 <?php if(count($estimate_detail) > 0){
                                    $count = 1;
                                    $t_mn = 0;
                                 foreach($estimate_detail as $es) { ?>
                                 <tr nobr="true" class="sortable">
                                    <td class="dragger item_no ui-sortable-handle" align="center"><?php echo pur_html_entity_decode($count); ?></td>
                                    <td class="description" align="left;"><span ><strong><?php 
                                    $item = get_item_hp($es['item_code']); 
                                    if(isset($item) && !is_array($item)){
                                       echo pur_html_entity_decode($item->commodity_code.' - '.$item->description);
                                    }else{
                                       echo pur_html_entity_decode($es['item_name']);
                                    }
                                    ?></strong></td>
                                    <td align="right"  width="12%"><?php echo pur_html_entity_decode($es['quantity']); ?></td>
                                    <td align="right"><?php echo app_format_money($es['unit_price'],$base_currency->symbol); ?></td>
                                    <td align="right"><?php echo app_format_money($es['into_money'],$base_currency->symbol); ?></td>
                                    <?php if(get_option('show_purchase_tax_column') == 1){ ?>
                                    <td align="right"><?php echo app_format_money(($es['total'] - $es['into_money']),$base_currency->symbol); ?></td>
                                    <?php } ?>
                                    <td class="amount" align="right"><?php echo app_format_money($es['total'],$base_currency->symbol); ?></td>
                                    <td class="amount" width="12%" align="right"><?php echo pur_html_entity_decode($es['discount_%'].'%'); ?></td>
                                    <td class="amount" align="right"><?php echo app_format_money($es['discount_money'],$base_currency->symbol); ?></td>
                                    <td class="amount" align="right"><?php echo app_format_money($es['total_money'],$base_currency->symbol); ?></td>
                                 </tr>
                              <?php $t_mn += $es['total_money'];
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

                              <?php if($estimate->discount_total > 0){ ?>
                              
                              <tr id="subtotal">
                                 <td><span class="bold"><?php echo _l('discount(money)'); ?></span>
                                 </td>
                                 <td class="subtotal">
                                    <?php echo '-'.app_format_money($estimate->discount_total, $base_currency->symbol); ?>
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
                     <?php if($estimate->terms != ''){ ?>
                     <div class="col-md-12 mtop15">
                        <p class="bold text-muted"><?php echo _l('terms_and_conditions'); ?></p>
                        <p><?php echo pur_html_entity_decode($estimate->terms); ?></p>
                     </div>
                     <?php } ?>
                  </div>
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
<div class="modal fade" id="send_quotation" tabindex="-1" role="dialog">
  <div class="modal-dialog">
      <?php echo form_open_multipart(admin_url('purchase/send_quotation'),array('id'=>'send_quotation-form')); ?>
      <div class="modal-content modal_withd">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">
                  <span><?php echo _l('send_a_quote'); ?></span>
              </h4>
          </div>
          <div class="modal-body">
              <div id="additional_quo"></div>
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
                      <label for="attach_pdf"><?php echo _l('attach_purchase_quotation_pdf'); ?></label>
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

<?php require 'modules/purchase/assets/js/estimate_preview_template_js.php';?>


