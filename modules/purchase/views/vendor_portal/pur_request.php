<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      
      <div class="col-md-12">
        <div class="panel_s accounting-template estimate">
        <div class="panel-body">

           <div class="horizontal-scrollable-tabs preview-tabs-top">
            
            <div class="horizontal-tabs">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="<?php if($this->input->get('tab') != 'attachment'){echo 'active';} ?>">
                     <a href="#general_infor" aria-controls="general_infor" role="tab" data-toggle="tab">
                     <?php echo _l('pur_general_infor'); ?>
                     </a>
                  </li>

                  <li role="presentation" class="<?php if($this->input->get('tab') === 'attachment'){echo 'active';} ?>">
                     <a href="#attachment" aria-controls="attachment" role="tab" data-toggle="tab">
                     <?php echo _l('pur_attachment'); ?>
                     </a>
                  </li>
                  
               </ul>
            </div>
         </div>
        
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane ptop10 <?php if($this->input->get('tab') != 'discussion' && $this->input->get('tab') != 'attachment'){echo 'active';} ?>" id="general_infor">  

          <div class="row">
            <div class="col-md-12">
              <a href="<?php echo site_url('purchase/vendors_portal/add_update_quotation?purchase_request='.$pur_request->id); ?>" class="btn btn-info mbot10 pull-right"><?php echo _l('convert_to_quotation'); ?></a>
            </div>

             <div class="col-md-6">
                <table class="table table-striped table-bordered">
                  <tr>
                    <td width="30%"><?php echo _l('pur_rq_code'); ?></td>
                    <td><?php echo pur_html_entity_decode($pur_request->pur_rq_code); ?></td>
                  </tr>
                  <tr>
                    <td><?php echo _l('pur_rq_name'); ?></td>
                    <td><?php echo pur_html_entity_decode($pur_request->pur_rq_name); ?></td>
                  </tr>
                  <tr>
                    <td><?php echo _l('description'); ?></td>
                    <td><?php echo pur_html_entity_decode($pur_request->rq_description); ?></td>
                  </tr>
                </table>
             </div>
             <div class="col-md-6">
                <table class="table table-striped table-bordered">
                  <tr>
                    <td width="30%"><?php echo _l('request_date'); ?></td>
                    <td><?php echo _dt($pur_request->request_date); ?></td>
                  </tr>
                  <tr>
                    <td><?php echo _l('requester'); ?></td>
                    <td><?php echo get_staff_full_name($pur_request->requester); ?></td>
                  </tr>
                  <tr>
                    <td><?php echo _l('status'); ?></td>
                    <td><?php echo get_status_approve($pur_request->status); ?></td>
                  </tr>
                </table>
             </div>  
               
          </div>
        </div>

        <div role="tabpanel" class="tab-pane <?php if($this->input->get('tab') === 'attachment'){echo ' active';} ?>" id="attachment">
              <?php echo form_open_multipart(site_url('purchase/vendors_portal/upload_pr_files/'.$pur_request->id.'/'.$pur_request->hash),array('class'=>'dropzone','id'=>'files-upload')); ?>
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

                              $href_url = site_url(PURCHASE_PATH.'pur_request/'.$file['rel_id'].'/'.$file['file_name']).'" download';
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
                              <a href="<?php echo site_url('purchase/vendors_portal/delete_pr_file/'.$file['id'].'/'.$pur_request->id.'/'.$pur_request->hash); ?>"
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
        <p class="bold p_style"><?php echo _l('pur_detail'); ?></p>
        <hr class="hr_style"/>
          <div class="table-responsive">
             <table class="table items items-preview estimate-items-preview" data-type="estimate">
                <thead>
                   <tr>

                    <th width="25%" align="left"><?php echo _l('debit_note_table_item_heading'); ?></th>
                    <th width="10%" align="right" class="qty"><?php echo _l('purchase_quantity'); ?></th>
                    <th width="10%" align="right"><?php echo _l('unit_price'); ?></th>
                    <th width="15%" align="right"><?php echo _l('subtotal_before_tax'); ?></th>
                    <th width="15%" align="right"><?php echo _l('debit_note_table_tax_heading'); ?></th>
                    <th width="10%" align="right"><?php echo _l('tax_value'); ?></th>
                    <th width="10%" align="right"><?php echo _l('debit_note_total'); ?></th>
                   </tr>
                </thead>
                <tbody class="ui-sortable">

                   <?php $_subtotal = 0;
                   $_total = 0;
                   if(count($pur_request_detail) > 0){
                      $count = 1;
                      $t_mn = 0;
                   foreach($pur_request_detail as $es) { 
                    $_subtotal += $es['into_money'];
                    $_total += $es['total'];
                    ?>
                   <tr nobr="true" class="sortable">
     
                      <td class="description" align="left;"><span><strong><?php 
                      $item = get_item_hp($es['item_code']); 
                      if(isset($item) && isset($item->commodity_code) && isset($item->description)){
                         echo pur_html_entity_decode($item->commodity_code.' - '.$item->description);
                      }else{
                         echo pur_html_entity_decode($es['item_text']);
                      }
                      ?></strong></td>
                      <?php
                           $unit_name = pur_get_unit_name($es['unit_id']); 
                        ?>
                      <td align="right"  width="12%"><?php echo pur_html_entity_decode($es['quantity']). ' '.$unit_name; ?></td>
                      <td align="right"><?php echo app_format_money($es['unit_price'],$base_currency->symbol); ?></td>
                      <td align="right"><?php echo app_format_money($es['into_money'],$base_currency->symbol); ?></td>
                      <td align="right"><?php 
                      if($es['tax_name'] != ''){
                        echo pur_html_entity_decode($es['tax_name']); 
                      }else{
                        $this->load->model('purchase/purchase_model');
                        if($es['tax'] != ''){
                          $tax_arr =  $es['tax'] != '' ? explode('|', $es['tax']) : [];
                          $tax_str = '';
                          if(count($tax_arr) > 0){
                            foreach($tax_arr as $key => $tax_id){
                              if(($key + 1) < count($tax_arr) ){
                                $tax_str .= $this->purchase_model->get_tax_name($tax_id).'|';
                              }else{
                                $tax_str .= $this->purchase_model->get_tax_name($tax_id);
                              }
                            }
                          }

                          echo pur_html_entity_decode($tax_str); 
                        }
                      }
                      ?></td>
                      <td align="right"><?php echo app_format_money($es['tax_value'], $base_currency->symbol); ?></td>
                  
                      <td class="amount" align="right"><?php echo app_format_money($es['total'],$base_currency->symbol); ?></td>
                   </tr>
                <?php 
                
                } } ?>
                </tbody>
             </table>
          </div>
        </div>

        <div class="col-md-6 col-md-offset-6">
         <table class="table text-right mbot0">
           <tbody>
              <tr id="subtotal">
                 <td class="td_style"><span class="bold"><?php echo _l('subtotal'); ?></span>
                 </td>
                 <td width="65%" id="total_td">
                  
                   <?php echo app_format_money($_subtotal, $base_currency->symbol); ?>
                 </td>
              </tr>
              <?php if(isset($pur_request)){ 
                echo $taxes_data['html'];
                ?>
              <?php } ?>

              <tr id="total">
                 <td class="td_style"><span class="bold"><?php echo _l('total'); ?></span>
                 </td>
                 <td width="65%" id="total_td">
                   <?php echo app_format_money($_total, $base_currency->symbol); ?>
                 </td>
              </tr>
            </tbody>
          </table>

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
<?php require 'modules/purchase/assets/js/pur_request_vendor_js.php';?>
