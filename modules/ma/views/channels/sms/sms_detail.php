<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s mtop15">
               <div class="panel-body">
                  <h4 class="customer-profile-group-heading"><?php echo _l('sms'); ?></h4>
                  <h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800"><?php echo _l('general_info'); ?></h4>
                  <div class="row">
                    <div class="col-md-6">
                      <?php echo form_hidden('sms_id', $sms->id); ?>
                      <table class="table table-striped  no-margin">
                        <tbody>
                            <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('name'); ?></td>
                              <td><span><?php echo html_entity_decode($sms->name); ?></span></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('category'); ?></td>
                              <td><?php echo ma_get_category_name($sms->category); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('sms_template'); ?></td>
                              <td><?php echo ma_get_text_message_name($sms->sms_template); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <?php $value = (($sms->published == 1) ? _l('yes') : _l('no')); ?>
                              <?php $text_class = (($sms->published == 1) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold"><?php echo _l('published'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                           </tr>
                          </tbody>
                    </table>
                  </div>
                  <div class="col-md-6">
                     <table class="table table-striped  no-margin">
                        <tbody>
                           <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('datecreator'); ?></td>
                              <td><?php echo _dt($sms->dateadded) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('addedfrom'); ?></td>
                              <td><?php echo get_staff_full_name($sms->addedfrom) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('description'); ?></td>
                              <td><?php echo html_entity_decode($sms->description) ; ?></td>
                           </tr>
                          </tbody>
                    </table>
                  </div>
                </div>
                <div class="horizontal-scrollable-tabs preview-tabs-top mtop25">
                  <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                    <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                    <div class="horizontal-tabs">
                      <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                           <li role="presentation" class="active">
                             <a href="#workflow" aria-controls="workflow" role="tab" id="tab_out_of_stock" data-toggle="tab">
                                <?php echo _l('preview') ?>
                             </a>
                          </li>
                          <li role="presentation">
                             <a href="#statistics" aria-controls="statistics" role="tab" id="tab_out_of_stock" data-toggle="tab">
                                <?php echo _l('statistics') ?>
                             </a>
                          </li>
                          <li role="presentation" >
                             <a href="#leads" aria-controls="leads" role="tab" id="tab_expiry_date" data-toggle="tab">
                                <?php echo _l('leads'); ?>
                             </a>
                          </li>
                      </ul>
                      </div>
                  </div>
                  <div class="tab-content mtop15">
                     <div role="tabpanel" class="tab-pane active" id="workflow">
                      <a class="btn btn-primary add_language" href="javascript:void(0);"><?php echo _l('add_language'); ?></a>
                      <a class="btn btn-success clone_language" href="javascript:void(0);"><?php echo _l('clone_design'); ?></a>
                        <div class="horizontal-scrollable-tabs preview-tabs-top mtop25">
                          <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                              <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                                <?php foreach($sms->data_design as $key => $design){ ?>
                                  <li role="presentation" class="<?php echo ($key == 0 ? 'active' : '') ?>">
                                     <a href="#<?php echo html_entity_decode($design['language']) ?>" aria-controls="<?php echo html_entity_decode($design['language']) ?>" role="tab" id="tab_out_of_stock" data-toggle="tab">
                                        <?php echo ucfirst($design['language']) ?>
                                     </a>
                                  </li>
                                <?php } ?>
                              </ul>
                              </div>
                          </div>
                          <div class="tab-content mtop15">
                              <?php foreach($sms->data_design as $key => $design){ ?>
                                <div role="tabpanel" class="tab-pane tab-language <?php echo ($key == 0 ? 'active' : '') ?>" id="<?php echo html_entity_decode($design['language']); ?>">
                                    <div class="wrapper">
                                      <div class="col-md-12">
                                        <a class="btn btn-sm btn-primary tw-font-semibold tw-tracking-tight tw-bg-white tw-text-primary-700 mbot20 tw-rounded-full tw-px-3 edit_design" href="javascript:void(0);"><?php echo _l('design'); ?></a>
                                        <a class="btn btn-sm btn-danger tw-font-semibold tw-tracking-tight tw-bg-white tw-text-danger-600 mbot20 _delete" href="<?php echo admin_url('ma/delete_sms_design/'.$design['id'].'/'.$sms->id); ?>"><?php echo _l('delete'); ?></a>
                                        <?php echo form_open_multipart(admin_url('ma/sms_design_save'),array('id'=>'sms-design-form'));?>
                                        <?php echo form_hidden('id', $design['id']); ?>
                                        <?php echo form_hidden('sms_id', $sms->id); ?>
                                        <?php echo render_textarea('content', 'content', html_entity_decode(($design['content'] != null) ? $design['content'] : ''), array('disabled' => true)); ?>
                                        <div class="modal-footer hide">
                                          <a class="btn btn-default close_design" href="javascript:void(0);"><?php echo _l('close'); ?></a>
                                          <button type="submit" class="btn btn-primary btn-submit"><?php echo _l('submit'); ?></button>
                                        </div>
                                       <?php echo form_close(); ?> 
                                        <div class="available_merge_fields hide">
                                          <h4 class="no-margin">
                                             <?php echo _l('available_merge_fields'); ?>
                                          </h4>
                                          <hr class="hr-panel-heading" />
                                          <div class="row">
                                             <div class="col-md-12">
                                                <div class="row available_merge_fields_container">
                                                   <?php
                                                      $mergeLooped = array();

                                                      foreach($available_merge_fields as $field){
                                                       foreach($field as $key => $val){
                                                         if($key != 'leads' && $key != 'other' && $key != 'client'){
                                                            continue;
                                                         }
                                                        echo '<div class="col-md-6 merge_fields_col">';
                                                        echo '<h5 class="bold">'.ucfirst($key).'</h5>';

                                                         if($key == 'leads'){
                                                            echo '<p>'._l('lead_first_name');
                                                            echo '<span class="pull-right"><a href="#" class="textarea-merge-field" data-to="description">';
                                                            echo '{lead_first_name}';
                                                            echo '</a>';
                                                            echo '</span>';
                                                            echo '</p>';
                                                            echo '<p>'._l('lead_last_name');
                                                            echo '<span class="pull-right"><a href="#" class="textarea-merge-field" data-to="description">';
                                                            echo '{lead_last_name}';
                                                            echo '</a>';
                                                            echo '</span>';
                                                            echo '</p>';
                                                         }

                                                        foreach($val as $_field){
                                                          if(count($_field['available']) == 0) {
                                                              // Fake data to simulate foreach loop and check the templates key for the available slugs
                                                            $_field['available'][] = '1';
                                                        }
                                                        foreach($_field['available'] as $_available){
                                                            if($_field['key'] == '{dark_logo_image_with_url}' || $_field['key'] == '{logo_image_with_url}' || $_field['key'] == '{logo_url}'){
                                                               continue;
                                                            }

                                                            if(!in_array($_field['name'], $mergeLooped)){
                                                               $mergeLooped[] = $_field['name'];
                                                               echo '<p>'.$_field['name'];
                                                               echo '<span class="pull-right"><a href="#" class="textarea-merge-field" data-to="description">';
                                                               echo html_entity_decode($_field['key']);
                                                               echo '</a>';
                                                               echo '</span>';
                                                               echo '</p>';
                                                            }
                                                        }
                                                      }
                                                      echo '</div>';
                                                      }
                                                      }
                                                      ?>
                                                </div>
                                             </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                </div>
                              <?php } ?>
                          </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="statistics">
                     <div class="row">
                      <div class="col-lg-4 col-xs-12 col-md-12 total-column">
                        <div class="panel_s">
                           <div class="panel-body">
                              <h3 class="text-muted _total">
                                 <?php echo count($lead_by_sms); ?>
                              </h3>
                              <span class="text-warning"><?php echo _l('total_number_of_lead'); ?></span>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4 col-xs-12 col-md-12 total-column">
                        <div class="panel_s">
                           <div class="panel-body">
                              <h3 class="text-muted _total">
                                 <?php echo html_entity_decode($campaign_by_sms['campaigns']); ?>
                              </h3>
                              <span class="text-success"><?php echo _l('number_of_active_campaigns'); ?></span>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4 col-xs-12 col-md-12 total-column">
                        <div class="panel_s">
                           <div class="panel-body">
                              <h3 class="text-muted _total">
                                 <?php echo html_entity_decode($campaign_by_sms['old_campaigns']); ?>
                              </h3>
                              <span class="text-default"><?php echo _l('number_of_campaigns_participated'); ?></span>
                           </div>
                        </div>
                     </div>
                   </div>
                     <div class="row">
                        <div class="col-md-12">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_chart"></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_campaign_chart" class="container_campaign"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="leads">
                      <?php

                              $table_data = array();
                              $_table_data = array(
                                '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="leads"><label></label></div>',
                                array(
                                 'name'=>_l('the_number_sign'),
                                 'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-number')
                               ),
                                array(
                                 'name'=>_l('leads_dt_name'),
                                 'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-name')
                               ),
                              );
                              if(is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
                                $_table_data[] = array(
                                    'name'=>_l('gdpr_consent') .' ('._l('gdpr_short').')',
                                    'th_attrs'=>array('id'=>'th-consent', 'class'=>'not-export')
                                 );
                              }
                              $_table_data[] = array(
                               'name'=>_l('lead_company'),
                               'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
                              );
                              $_table_data[] =   array(
                               'name'=>_l('leads_dt_email'),
                               'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-email')
                              );
                              $_table_data[] =  array(
                               'name'=>_l('leads_dt_phonenumber'),
                               'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-phone')
                              );
                              $_table_data[] =  array(
                                 'name'=>_l('leads_dt_lead_value'),
                                 'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-lead-value')
                                );
                              $_table_data[] =  array(
                               'name'=>_l('tags'),
                               'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-tags')
                              );
                              $_table_data[] = array(
                               'name'=>_l('leads_dt_assigned'),
                               'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-assigned')
                              );
                              $_table_data[] = array(
                               'name'=>_l('leads_dt_status'),
                               'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-status')
                              );
                              $_table_data[] = array(
                               'name'=>_l('leads_source'),
                               'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-source')
                              );
                              $_table_data[] = array(
                                 'name'=>_l('point'),
                                 'th_attrs'=>array('class'=>'toggleable','id'=>'th-point')
                               );
                              foreach($_table_data as $_t){
                               array_push($table_data,$_t);
                              }
                             
                              $table_data = hooks()->apply_filters('leads_table_columns', $table_data);
                              render_datatable($table_data,'leads-email-template',
                              array('customizable-table'),
                              array(
                               'id'=>'table-leads-email-template',
                               'data-last-order-identifier'=>'leads',
                               )); ?>
                    </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
     
   </div>
</div>


<div class="modal fade" id="language-modal">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('language')?></h4>
         </div>

         <?php echo form_open_multipart(admin_url('ma/add_sms_language'),array('id'=>'language-form'));?>
         <?php echo form_hidden('sms_id', $sms->id); ?>
         <div class="modal-body">
            <div class="form-group select-placeholder">
              <label for="language" class="control-label"><?php echo _l('form_lang_validation'); ?></label>
              <select name="language" id="language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                 <option value=""></option>
                 <?php foreach ($languages as $availableLanguage) {
                  ?>
                 <option value="<?php echo html_entity_decode($availableLanguage); ?>"><?php echo ucfirst($availableLanguage); ?></option>
                 <?php } ?>
              </select>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-primary btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>

<div class="modal fade" id="clone-design-modal">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('clone_design')?></h4>
         </div>

         <?php echo form_open_multipart(admin_url('ma/clone_sms_design'),array('id'=>'clone-design-form'));?>
         <?php echo form_hidden('sms_id', $sms->id); ?>
         <div class="modal-body">
            <div class="form-group select-placeholder">
              <label for="from_language" class="control-label"><?php echo _l('from_language'); ?></label>
              <select name="from_language" id="from_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                 <option value=""></option>
                  <?php foreach($sms->data_design as $key => $design){ ?>
                    <option value="<?php echo html_entity_decode($design['id']); ?>"><?php echo ucfirst($design['language']); ?></option>
                 <?php } ?>
              </select>
            </div>
            <div class="form-group select-placeholder">
              <label for="to_language" class="control-label"><?php echo _l('to_language'); ?></label>
              <select name="to_language" id="to_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                 <option value=""></option>
                 <?php foreach ($languages as $availableLanguage) {
                  ?>
                 <option value="<?php echo html_entity_decode($availableLanguage); ?>"><?php echo ucfirst($availableLanguage); ?></option>
                 <?php } ?>
              </select>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-primary btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>
<?php init_tail(); ?>
</body>
</html>
<?php require('modules/ma/assets/js/channels/sms_detail_js.php'); ?>
