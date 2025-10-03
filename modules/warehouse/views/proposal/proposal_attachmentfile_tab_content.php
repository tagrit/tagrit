  <div role="tabpanel" class="tab-pane" id="pro_attachment">
     <?php echo form_open_multipart(admin_url('warehouse/wh_proposal_attachment/'.$proposal_id),array('id'=>'partograph-attachments-upload')); ?>
      <?php echo render_input('file','file','','file'); ?>

      <div class="col-md-12 pad_div_0">

     </div>
     <div class="modal-footer bor_top_0" >
         <button id="obgy_btn2" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
     </div>
      <?php echo form_close(); ?>
     
     <div class="col-md-12" id="proposal_pv_file">
          <?php
              $file_html = '';
              if(count($proposal_attachments) > 0){
                  $file_html .= '<hr />
                          <p class="bold text-muted">'._l('customer_attachments').'</p>';
                  foreach ($proposal_attachments as $f) {
                      $href_url = site_url(WAREHOUSE_PATH.'proposal/'.$f['rel_id'].'/'.$f['file_name']).'" download';
                                      if(!empty($f['external'])){
                                        $href_url = $f['external_link'];
                                      }
                     $file_html .= '<div class="mbot15 row inline-block full-width" data-attachment-id="'. $f['id'].'">
                    <div class="col-md-8">
                       <a name="preview-purorder-btn" onclick="preview_proposal_btn(this); return false;" rel_id = "'. $f['rel_id']. '" id = "'.$f['id'].'" href="Javascript:void(0);" class="mbot10 mright5 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'. _l('preview_file').'"><i class="fa fa-eye"></i></a>
                       <div class="pull-left"><i class="'. get_mime_class($f['filetype']).'"></i></div>
                       <a href=" '. $href_url.'" target="_blank" download>'.$f['file_name'].'</a>
                       <br />
                       <small class="text-muted">'.$f['filetype'].'</small>
                    </div>
                    <div class="col-md-4 text-right">';
                      if($f['staffid'] == get_staff_user_id() || is_admin()){
                      $file_html .= '<a href="#" class="text-danger" onclick="delete_wh_proposal_attachment('. $f['id'].'); return false;"><i class="fa fa-times"></i></a>';
                      } 
                     $file_html .= '</div></div>';
                  }
                  $file_html .= '<hr />';
                  echo new_html_entity_decode($file_html);
              }
           ?>
      </div>

     <div id="proposal_file_data"></div>
  </div>