<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="mtop15 preview-top-wrapper">
   <div class="row">
      <div class="col-md-3">
         <div class="mbot30">
            <div class="contract-html-logo">
               <?php echo get_dark_company_logo(); ?>
            </div>
         </div>
      </div>
      <div class="clearfix"></div>
   </div>
   <div class="top" data-sticky data-sticky-class="preview-sticky-header">
      <div class="container preview-sticky-container">
         <div class="row">
            <div class="col-md-12">
               <h4 class="pull-left no-mtop contract-html-subject"><?php echo $contract->contract_number; ?><br />
                  <small><?php echo $contract->contract_name; ?></small>
               </h4>
               <div class="visible-xs">
                  <div class="clearfix"></div>
               </div>
               <?php if($contract->signed == 0 ) { ?>
               <button type="submit" id="accept_action" class="btn btn-success pull-right action-button"><?php echo _l('e_signature_sign'); ?></button>
               <?php } else { ?>
               <span class="success-bg content-view-status contract-html-is-signed"><?php echo _l('is_signed'); ?></span>
               <?php } ?>
              
               <?php if(is_vendor_logged_in()){ ?>
               <a href="<?php echo site_url('purchase/vendors_portal'); ?>" class="btn btn-default mright5 pull-right action-button go-to-portal">
               <?php echo _l('client_go_to_dashboard'); ?>
               </a>
               <?php } ?>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-md-8 contract-left">
      <div class="panel_s mtop20">
         <div class="panel-body tc-content padding-30 contract-html-content">
            <?php echo $contract->content; ?>
         </div>
      </div>
   </div>
   <div class="col-md-4 contract-right">
      <div class="inner mtop20 contract-html-tabs">
         <ul class="nav nav-tabs nav-tabs-flat mbot15" role="tablist">
            <li role="presentation" class="<?php if(!$this->input->get('tab') || $this->input->get('tab') === 'summary'){echo 'active';} ?>">
               <a href="#summary" aria-controls="summary" role="tab" data-toggle="tab">
               <i class="fa fa-file-text-o" aria-hidden="true"></i> <?php echo _l('summary'); ?></a>
            </li>
            <li role="presentation" class="<?php if($this->input->get('tab') === 'discussion'){echo 'active';} ?>">
               <a href="#discussion" aria-controls="discussion" role="tab" data-toggle="tab">
               <i class="fa fa-commenting-o" aria-hidden="true"></i> <?php echo _l('discussion'); ?>
               </a>
            </li>
         </ul>
         <div class="tab-content">
            <div role="tabpanel" class="tab-pane<?php if(!$this->input->get('tab') || $this->input->get('tab') === 'summary'){echo ' active';} ?>" id="summary">
               <address class="contract-html-company-info">
                  <?php echo format_organization_info(); ?>
               </address>
               <div class="row mtop20">
                  <?php if($contract->contract_value != 0){ ?>
                  <div class="col-md-12 contract-value">
                     <h4 class="bold mbot30">
                        <?php echo _l('contract_value'); ?>:
                        <?php echo app_format_money($contract->contract_value, get_base_currency()); ?>
                     </h4>
                  </div>
                  <?php } ?>
                  <div class="col-md-5 text-muted contract-number">
                     # <?php echo _l('contract_number'); ?>
                  </div>
                  <div class="col-md-7 contract-number">
                     <?php echo $contract->id; ?>
                  </div>
                  <div class="col-md-5 text-muted contract-start-date">
                     <?php echo _l('contract_start_date'); ?>
                  </div>
                  <div class="col-md-7 contract-start-date">
                     <?php echo _d($contract->start_date); ?>
                  </div>
                  <?php if(!empty($contract->end_date)){ ?>
                  <div class="col-md-5 text-muted contract-end-date">
                     <?php echo _l('contract_end_date'); ?>
                  </div>
                  <div class="col-md-7 contract-end-date">
                     <?php echo _d($contract->end_date); ?>
                  </div>
                  <?php } ?>
                  <?php if(!empty($contract->contract_name)){ ?>
                  <div class="col-md-5 text-muted contract-type">
                     <?php echo _l('contract_type'); ?>
                  </div>
                  <div class="col-md-7 contract-type">
                     <?php echo $contract->contract_name; ?>
                  </div>
                  <?php } ?>
               </div>
               <?php if($contract->signed == 1){ ?>
                  <div class="row mtop20">
                     <div class="col-md-12 contract-value">
                        <h4 class="bold mbot30">
                           <?php echo _l('signature'); ?>
                        </h4>
                     </div>
                     <div class="col-md-5 text-muted contract-signed-by">
                        <?php echo _l('contract_signed_by'); ?>
                     </div>
                     <div class="col-md-7 contract-contract-signed-by">
                        <?php echo "{$contract->acceptance_firstname} {$contract->acceptance_lastname}"; ?>
                     </div>
                     
                     <div class="col-md-5 text-muted contract-signed-by">
                        <?php echo _l('contract_signed_date'); ?>
                     </div>
                     <div class="col-md-7 contract-contract-signed-by">
                        <?php echo _d(explode(' ', $contract->acceptance_date)[0]); ?>
                     </div>
                     
                     <div class="col-md-5 text-muted contract-signed-by">
                        <?php echo _l('contract_signed_ip'); ?>
                     </div>
                     <div class="col-md-7 contract-contract-signed-by">
                        <?php echo $contract->acceptance_ip; ?>
                     </div>
                  </div>
               <?php } ?>

                 <div class="col-md-12" id="ic_pv_file">
                   <?php
                      
                       $file_html = '';
                      
                       if(count($attachments) > 0){
                           $file_html .= '<hr />
                                   <p class="bold text-muted">'._l('attachments').'</p>';
                           foreach ($attachments as $f) {
                               $href_url = site_url(PURCHASE_PATH.'pur_contract/'.$f['rel_id'].'/'.$f['file_name']).'" download';
                                               if(!empty($f['external'])){
                                                 $href_url = $f['external_link'];
                                               }
                              $file_html .= '<div class="mbot15 row inline-block full-width" data-attachment-id="'. $f['id'].'">
                             <div class="col-md-12">
                                
                                <div class="pull-left"><i class="'. get_mime_class($f['filetype']).'"></i></div>
                                <a href=" '. $href_url.'" target="_blank" download>'.$f['file_name'].'</a>
                                <br />
                                <small class="text-muted">'.$f['filetype'].'</small>
                             </div>';
                            
                              $file_html .= '</div>';
                           }
                           $file_html .= '<hr />';
                           echo pur_html_entity_decode($file_html);
                       }
                    ?>
                 </div>
               
                 <div id="ic_file_data"></div>

             
            </div>
            <div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') === 'discussion'){echo ' active';} ?>" id="discussion">
               <?php echo form_open($this->uri->uri_string()) ;?>
               <div class="contract-comment">
                  <textarea name="content" rows="4" class="form-control"></textarea>
                  <button type="submit" class="btn btn-info mtop10 pull-right" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('proposal_add_comment'); ?></button>
                  <?php echo form_hidden('action','contract_comment'); ?>
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
                   $comment_html .= '<hr/>';
                  }
                  echo $comment_html; ?>
            </div>
         </div>
      </div>
   </div>
</div>
<?php
   get_template_part('identity_confirmation_form', array('formData' => form_hidden('action', 'sign_contract')));
   ?>
<script>
   $(function(){
      new Sticky('[data-sticky]');
      $(".contract-left table").wrap("<div class='table-responsive'></div>");
         // Create lightbox for contract content images
         $('.contract-html-content img').wrap( function(){ return '<a href="' + $(this).attr('src') + '" data-lightbox="contract"></a>'; });
      })
</script>
