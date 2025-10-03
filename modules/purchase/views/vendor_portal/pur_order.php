<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<?php if($pur_order->currency != 0){
  $base_currency = pur_get_currency_by_id($pur_order->currency);
}
 ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      
      <div class="col-md-12">
        <div class="panel_s accounting-template estimate">
        <div class="panel-body">

          <?php if($pur_order->order_status ==  'new'){ ?>
               <div class="ribbon info span_style"><span><?php echo _l('new_order'); ?></span></div>
           <?php }elseif($pur_order->order_status == 'delivered'){ ?>
             <div class="ribbon success"><span><?php echo _l('delivered'); ?></span></div>
           <?php }elseif($pur_order->order_status == 'confirmed'){ ?>  
             <div class="ribbon warning"><span><?php echo _l('confirmed'); ?></span></div>
           <?php }elseif ($pur_order->order_status == 'cancelled') { ?>
             <div class="ribbon danger"><span><?php echo _l('cancelled'); ?></span></div>
          <?php  }elseif ($pur_order->order_status == 'return'){ ?>
            <div class="ribbon warning"><span><?php echo _l('pur_return'); ?></span></div>
          <?php } ?>
          
       
            <div class="horizontal-scrollable-tabs preview-tabs-top">
            
            <div class="horizontal-tabs">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="<?php if($this->input->get('tab') != 'discussion' && $this->input->get('tab') != 'attachment'){echo 'active';} ?>">
                     <a href="#general_infor" aria-controls="general_infor" role="tab" data-toggle="tab">
                     <?php echo _l('general_infor'); ?>
                     </a>
                  </li>

                  <li role="presentation" class="<?php if($this->input->get('tab') === 'attachment'){echo 'active';} ?>">
                     <a href="#attachment" aria-controls="attachment" role="tab" data-toggle="tab">
                     <?php echo _l('pur_attachment'); ?>
                     </a>
                  </li>
                  
                  <li role="presentation" class="tab-separator <?php if($this->input->get('tab') === 'discussion'){echo 'active';} ?>">
                    <?php
                              $totalComments = total_rows(db_prefix().'pur_comments',['rel_id' => $pur_order->id, 'rel_type' => 'pur_order']);
                              ?>
                     <a href="#discuss" aria-controls="discuss" role="tab" data-toggle="tab">
                     <?php echo _l('pur_discuss'); ?>
                      <span class="badge comments-indicator<?php echo $totalComments == 0 ? ' hide' : ''; ?>"><?php echo $totalComments; ?></span>
                     </a>
                  </li> 
                  
               </ul>
            </div>
         </div>
          <div class="tab-content">
             <div role="tabpanel" class="tab-pane ptop10 <?php if($this->input->get('tab') != 'discussion' && $this->input->get('tab') != 'attachment'){echo 'active';} ?>" id="general_infor">
              <div class="row">
                <?php if($pur_order->order_status == 'new' && is_vendor_logged_in()){ ?>
                  <div class="col-md-12">
                    <a href="javascript:void(0)" onclick="confirm_order(this); return false;" class="btn btn-info pull-right" data-order_id="<?php echo pur_html_entity_decode($pur_order->id); ?>" data-toggle="tooltip" data-placement="top" title="<?php echo _l('pur_confirm_order_note'); ?>"><?php echo _l('pur_confirm'); ?></a>
                    </div>
                  <?php } ?>

                  <?php if($pur_order->order_status == 'confirmed' ){ ?>
                    <div class="col-md-4 col-md-offset-8">
                      <select name="delivery_status" onchange="update_delivery_status(this); return false;" id="delivery_status" class="selectpicker"  data-live-search="true" data-width="100%" >
                           <option value="0" <?php if($pur_order->delivery_status == 0){ echo 'selected'; } ?> ><?php echo _l('undelivered'); ?></option>
                           <option value="1" <?php if($pur_order->delivery_status == 1){ echo 'selected'; } ?> ><?php echo _l('completely_delivered'); ?></option>
                           <option value="2" <?php if($pur_order->delivery_status == 2){ echo 'selected'; } ?> ><?php echo _l('pending_delivered'); ?></option>
                           <option value="3" <?php if($pur_order->delivery_status == 3){ echo 'selected'; } ?> ><?php echo _l('partially_delivered'); ?></option>
                        </select>
                    </div>
                  <?php } ?>
               <div class="col-md-6">
                  
                  <table class="table table-striped table-bordered">
                    <tr>
                      <td width="30%"><?php echo _l('pur_order_number'); ?></td>
                      <td><?php echo pur_html_entity_decode($pur_order->pur_order_number) ?></td>
                    </tr>
                    <tr>
                      <td><?php echo _l('pur_order_name'); ?></td>
                      <td><?php echo pur_html_entity_decode($pur_order->pur_order_name) ?></td>
                    </tr>
                    <tr>
                      <td><?php echo _l('pur_approve_status'); ?></td>
                      <td><?php echo get_status_approve($pur_order->approve_status) ?></td>
                    </tr>
                  </table>
               </div>
               <div class="col-md-6">
                  <table class="table table-striped table-bordered">
                    <tr>
                      <td width="30%"><?php echo _l('order_date'); ?></td>
                      <td><?php echo _d($pur_order->order_date) ?></td>
                    </tr>
                    <tr>
                      <td><?php echo _l('delivery_date'); ?></td>
                      <td><?php echo pur_render_date_input('delivery_date', '', _d($pur_order->delivery_date), array('onchange' => 'update_delivery_date(this); return false;')); ?></td>
                    </tr>
                    <tr>
                      <td><?php echo _l('total'); ?></td>
                      <td><?php echo app_format_money($pur_order->total,'') ?></td>
                    </tr>
                  </table>
               </div>
               </div>
            </div>

            <div role="tabpanel" class="tab-pane <?php if($this->input->get('tab') === 'discussion'){echo ' active';} ?>" id="discuss">
              <?php echo form_open($this->uri->uri_string()) ;?>
               <div class="contract-comment">
                  <textarea name="content" rows="4" class="form-control"></textarea>
                  <button type="submit" class="btn btn-info mtop10 pull-right" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('proposal_add_comment'); ?></button>
                  <?php echo form_hidden('action','po_comment'); ?>
               </div>
               <?php echo form_close(); ?>
               <div class="clearfix"></div>
               <?php
                  $comment_html = '';
                  foreach ($comments as $comment) {
                   $comment_html .= '<div class="contract_comment mtop10 mbot20" data-commentid="' . $comment['id'] . '">';
                   if($comment['staffid'] != 0){
                    $comment_html .= staff_profile_image($comment['staffid'], array(
                     'staff-profile-image-small',
                     'media-object img-circle pull-left mright10'
                  ));
                  }
                  $comment_html .= '<div class="media-body valign-middle">';
                  $comment_html .= '<div class="mtop5">';
                  $comment_html .= '<b>';
                  if($comment['staffid'] != 0){
                    $comment_html .= get_staff_full_name($comment['staffid']);
                  } else {
                    $comment_html .= get_vendor_company_name(get_vendor_user_id());
                  }
                  $comment_html .= '</b>';
                  $comment_html .= ' - <small class="mtop10 text-muted">' . time_ago($comment['dateadded']) . '</small>';
                  $comment_html .= '</div>';
              
                  $comment_html .= check_for_links($comment['content']) . '<br />';
                  $comment_html .= '</div>';
                  $comment_html .= '</div>';
                  $comment_html .= '<hr />';
                  }
                  echo $comment_html; ?>
            </div>

            <div role="tabpanel" class="tab-pane <?php if($this->input->get('tab') === 'attachment'){echo ' active';} ?>" id="attachment">
              <?php echo form_open_multipart(site_url('purchase/vendors_portal/upload_files/'.$pur_order->id),array('class'=>'dropzone','id'=>'files-upload')); ?>
                 <input type="file" name="file" multiple class="hide"/>
                 <?php echo form_close(); ?>

                 <div class="mtop15 mbot15 text-right">
                  <button class="gpicker" data-on-pick="customerFileGoogleDriveSave">
                      <i class="fa fa-google" aria-hidden="true"></i>
                      <?php echo _l('choose_from_google_drive'); ?>
                  </button>
                  <?php if(get_option('dropbox_app_key') != ''){ ?>
                      <div id="dropbox-chooser-files"></div>
                  <?php } ?>
              </div>

              <?php if(count($files) == 0){ ?>
                  <hr class="hr-panel-heading" />
                  <div class="text-center">
                      <h4 class="no-margin"><?php echo _l('no_files_found'); ?></h4>
                  </div>
              <?php } else { ?>
                  <table class="table dt-table mtop15 table-files" data-order-col="1" data-order-type="desc">
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

                              $href_url = site_url(PURCHASE_PATH.'pur_order/'.$file['rel_id'].'/'.$file['file_name']).'" download';
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
                          <?php if($file['contact_id'] == get_vendor_contact_user_id()){ ?>
                              <a href="<?php echo site_url('purchase/vendors_portal/delete_po_file/'.$file['id'].'/'.$pur_order->id); ?>"
                                  class="btn btn-danger btn-icon _delete file-delete"><i class="fa fa-remove"></i></a>
                              <?php } ?>
                          </td>
                      
                  </tr>
              <?php } ?>
          </tbody>
          </table>
          <?php } ?>

            </div>

          </div>
          
        </div>
        <div class="panel-body mtop10">
        <div class="row">
        <div class="col-md-12">
          <div class="table-responsive">
             <table class="table items items-preview estimate-items-preview" data-type="estimate">
                <thead>
                   <tr>
                      <th align="center">#</th>
                      <th class="description" width="25%" align="left"><?php echo _l('items'); ?></th>
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

                   <?php if(count($pur_order_detail) > 0){
                      $count = 1;
                      $t_mn = 0;
                      $item_discount = 0;
                   foreach($pur_order_detail as $es) { ?>
                   <tr nobr="true" class="sortable">
                      <td class="dragger item_no ui-sortable-handle" align="center"><?php echo pur_html_entity_decode($count); ?></td>
                      <td class="description" align="left;"><span><strong><?php 
                      $item = get_item_hp2($es['item_code']); 
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
         <div class="col-md-6 col-md-offset-6">
           <table class="table text-right">
               <tbody>
                  <tr id="subtotal">
                     <td><span class="bold"><?php echo _l('subtotal'); ?></span>
                     </td>
                     <td class="subtotal">
                        <?php echo app_format_money($pur_order->subtotal,$base_currency->symbol); ?>
                     </td>
                  </tr>

                  <?php if($tax_data['preview_html'] != ''){
                    echo pur_html_entity_decode($tax_data['preview_html']);
                  } ?>


                  <?php if(($pur_order->discount_total + $item_discount) > 0){ ?>
                  
                  <tr id="subtotal">
                     <td><span class="bold"><?php echo _l('discount_total(money)'); ?></span>
                     </td>
                     <td class="subtotal">
                        <?php echo '-'.app_format_money(($pur_order->discount_total + $item_discount), $base_currency->symbol); ?>
                     </td>
                  </tr>
                  <?php } ?>

                  <?php if($pur_order->shipping_fee > 0){ ?>
                      <tr id="subtotal">
                        <td><span class="bold"><?php echo _l('pur_shipping_fee'); ?></span></td>
                        <td class="subtotal">
                          <?php echo app_format_money($pur_order->shipping_fee, $base_currency->symbol); ?>
                        </td>
                      </tr>
                    <?php } ?>

                  <tr id="subtotal">
                     <td><span class="bold"><?php echo _l('total'); ?></span>
                     </td>
                     <td class="subtotal bold">
                        <?php echo app_format_money($pur_order->total, $base_currency->symbol); ?>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div> 

         <?php if($pur_order->vendornote != ''){ ?>
           <div class="col-md-12 mtop15">
              <p class="bold text-muted"><?php echo _l('estimate_note'); ?></p>
              <p><?php echo nl2br($pur_order->vendornote); ?></p>
           </div>
           <?php } ?>
                                                  
           <?php if($pur_order->terms != ''){ ?>
           <div class="col-md-12 mtop15">
              <p class="bold text-muted"><?php echo _l('terms_and_conditions'); ?></p>
              <p><?php echo pur_html_entity_decode($pur_order->terms); ?></p>
           </div>
           <?php } ?>

        </div>
      </div>
        </div>
      
        </div>

      </div>
    
      
    </div>
  </div>
</div>
</div>
<?php hooks()->do_action('app_admin_footer'); ?>
</body>
</html>
<?php require 'modules/purchase/assets/js/pur_order_vendor_js.php';?>
