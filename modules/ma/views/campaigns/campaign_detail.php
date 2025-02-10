<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s mtop15">
               <div class="panel-body">
                  <h4 class="customer-profile-group-heading"><?php echo _l('campaign'); ?></h4>
                  <h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800"><?php echo _l('general_info'); ?></h4>
                  <div class="row">
                    <div class="col-md-6">
                      <?php echo form_hidden('timezone', date_default_timezone_get()); ?>
                      <?php echo form_hidden('campaign_id',$campaign->id); ?>
                      <table class="table table-striped  no-margin">
                        <tbody>
                            <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('name'); ?></td>
                              <td><span style="color: <?php echo html_entity_decode($campaign->color); ?>"><?php echo html_entity_decode($campaign->name); ?></span></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('category'); ?></td>
                              <td><?php echo ma_get_category_name($campaign->category); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <?php $value = (($campaign->published == 1) ? _l('yes') : _l('no')); ?>
                              <?php $text_class = (($campaign->published == 1) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold"><?php echo _l('published'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('start_date'); ?></td>
                              <td><?php echo _d($campaign->start_date) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('end_date'); ?></td>
                              <td><?php echo _d($campaign->end_date) ; ?></td>
                           </tr>
                          </tbody>
                    </table>
                  </div>
                  <div class="col-md-6">
                      <table class="table table-striped  no-margin">
                        <tbody>
                          <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('datecreator'); ?></td>
                              <td><?php echo _dt($campaign->dateadded) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('addedfrom'); ?></td>
                              <td><?php echo get_staff_full_name($campaign->addedfrom) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('description'); ?></td>
                              <td><?php echo html_entity_decode($campaign->description) ; ?></td>
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
                                <?php echo _l('workflow') ?>
                             </a>
                          </li>
                          <li role="presentation">
                             <a href="#actions" aria-controls="actions" role="tab" id="tab_out_of_stock" data-toggle="tab">
                                <?php echo _l('ma_actions') ?>
                             </a>
                          </li>
                          <li role="presentation" >
                             <a href="#statistics" aria-controls="statistics" role="tab" id="tab_expiry_date" data-toggle="tab">
                                <?php echo _l('statistics'); ?>
                             </a>
                          </li>
                          <li role="presentation" >
                             <a href="#leads" aria-controls="leads" role="tab" id="tab_expiry_date" data-toggle="tab">
                                <?php echo _l('leads'); ?>
                             </a>
                          </li>
                          <li role="presentation" >
                             <a href="#clients" aria-controls="clients" role="tab" id="tab_expiry_date" data-toggle="tab">
                                <?php echo _l('clients'); ?>
                             </a>
                          </li>
                          <li role="presentation" >
                             <a href="#test_campaign" aria-controls="test_campaign" role="tab" id="tab_expiry_date" data-toggle="tab">
                                <?php echo _l('test_campaign'); ?>
                             </a>
                          </li>
                      </ul>
                      </div>
                  </div>
                  <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="workflow">
                      <div class="wrapper">
                        <div class="col-md-12">
                          <div id="drawflow" ondrop="drop(event)" ondragover="allowDrop(event)">
                            <div class="btn-export" onclick="builder(); return false;"><?php echo _l('builder'); ?></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="actions">
                      <div class="row mtop15">
                        <div class="col-md-4">
                          <div class="panel_s">
                            <div class="panel-heading">
                              <h4><?php echo _l('point_actions'); ?></h4>
                            </div>
                            <div class="panel-body">
                              <table class="table table-striped  no-margin">
                                <tbody>
                                  <?php foreach($point_actions as $action){ ?>
                                    <tr class="project-overview">
                                      <td width="30%"><span><?php echo html_entity_decode($action->name); ?></span></td>
                                      <td class="text-right"><?php echo html_entity_decode($action->total); ?></td>
                                   </tr>
                                  <?php } ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="panel_s">
                            <div class="panel-heading">
                              <h4><?php echo _l('emails'); ?></h4>
                            </div>
                            <div class="panel-body">
                              <table class="table table-striped no-margin">
                                <?php  ?>
                                <thead>
                                  <tr>
                                    <th></th>
                                    <th class="text-right"><?php echo _l('total'); ?></th>
                                    <th class="text-right"><?php echo _l('open'); ?></th>
                                    <th class="text-right"><?php echo _l('click'); ?></th>
                                    <th class="text-right"><?php echo _l('average_time_to_open'); ?></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php 
                                  $total = 0;
                                  $open = 0;
                                  $click = 0;

                                  foreach($emails as $email){ 
                                    $total += $email->total;
                                    $open += $email->open;
                                    $click += $email->click;
                                    ?>
                                    <tr class="project-overview">
                                      <td width="30%"><span style="color: <?php echo html_entity_decode($email->color); ?>"><?php echo html_entity_decode($email->name); ?></span></td>
                                      <td class="text-right"><?php echo html_entity_decode($email->total); ?></td>
                                      <td class="text-right"><?php echo html_entity_decode($email->open); ?><small><?php echo html_entity_decode('('.$email->open_percent.'%)'); ?></small></td>
                                      <td class="text-right"><?php echo html_entity_decode($email->click); ?><small><?php echo html_entity_decode('('.$email->click_percent.'%)'); ?></small></td>
                                      <td class="text-right"><small><?php echo html_entity_decode($email->average_time_to_open); ?></small></td>
                                   </tr>
                                  <?php } ?>
                                  <tr class="project-overview bold">
                                      <td width="30%"><?php echo _l('total'); ?></td>
                                      <td class="text-right"><?php echo html_entity_decode($total); ?></td>
                                      <td class="text-right"><?php echo html_entity_decode($open); ?><small><?php echo html_entity_decode('('.($total != 0 ? round(($open/$total) * 100, 2) : '0').'%)'); ?></small></td>
                                      <td class="text-right"><?php echo html_entity_decode($click); ?><small><?php echo html_entity_decode('('.($total != 0 ? round(($click/$total) * 100, 2) : '0').'%)'); ?></small></td>
                                      <td class="text-right"><small><?php echo html_entity_decode($average_time_to_open); ?></small></td>
                                   </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="panel_s">
                            <div class="panel-heading">
                              <h4><?php echo _l('segments'); ?></h4>
                            </div>
                            <div class="panel-body">
                              <table class="table table-striped  no-margin">
                                <tbody>
                                  <?php foreach($segments as $segment){ ?>
                                    <tr class="project-overview">
                                      <td width="30%"><span style="color: <?php echo html_entity_decode($segment->color); ?>"><?php echo html_entity_decode($segment->name); ?></span></td>
                                      <td class="text-right"><?php echo html_entity_decode($segment->total); ?></td>
                                   </tr>
                                  <?php } ?>
                                  <?php foreach($customer_segments as $segment){ ?>
                                    <tr class="project-overview">
                                      <td width="30%"><span style="color: <?php echo html_entity_decode($segment->color); ?>"><?php echo html_entity_decode($segment->name); ?></span></td>
                                      <td class="text-right"><?php echo html_entity_decode($segment->total); ?></td>
                                   </tr>
                                  <?php } ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <div class="panel_s">
                            <div class="panel-heading">
                              <h4><?php echo _l('sms'); ?></h4>
                            </div>
                            <div class="panel-body">
                              <table class="table table-striped  no-margin">
                                <tbody>
                                  <?php foreach($sms as $_sms){ ?>
                                    <tr class="project-overview">
                                      <td width="30%"><span><?php echo html_entity_decode($_sms->name); ?></span></td>
                                      <td class="text-right"><?php echo html_entity_decode($_sms->total); ?></td>
                                   </tr>
                                  <?php } ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="panel_s">
                            <div class="panel-heading">
                              <h4><?php echo _l('stages'); ?></h4>
                            </div>
                            <div class="panel-body">
                              <table class="table table-striped  no-margin">
                                <tbody>
                                  <?php foreach($stages as $stage){ ?>
                                    <tr class="project-overview">
                                      <td width="30%"><span style="color: <?php echo html_entity_decode($stage->color); ?>"><?php echo html_entity_decode($stage->name); ?></span></td>
                                      <td class="text-right"><?php echo html_entity_decode($stage->total); ?><small><?php echo html_entity_decode('('.$stage->percent.'%)'); ?></small></td>
                                   </tr>
                                  <?php } ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="statistics">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_email"></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_email_total"></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_text_message"></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_point_action"></div>
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
                      render_datatable($table_data,'leads-campaign',
                      array('customizable-table'),
                      array(
                       'id'=>'table-leads-campaign',
                       'data-last-order-identifier'=>'leads',
                       )); ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="clients">
                     <?php
                     $table_data = array();
                     $_table_data = array(
                      '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="clients"><label></label></div>',
                       array(
                         'name'=>_l('the_number_sign'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-number')
                        ),
                         array(
                         'name'=>_l('clients_list_company'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
                        ),
                         array(
                         'name'=>_l('contact_primary'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-primary-contact')
                        ),
                         array(
                         'name'=>_l('company_primary_email'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-primary-contact-email')
                        ),
                        array(
                         'name'=>_l('clients_list_phone'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-phone')
                        ),
                         array(
                         'name'=>_l('customer_active'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-active')
                        ),
                        array(
                         'name'=>_l('customer_groups'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-groups')
                        ),
                        array(
                         'name'=>_l('point'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-point')
                        ),
                      );
                     foreach($_table_data as $_t){
                      array_push($table_data,$_t);
                     }

                     $table_data = hooks()->apply_filters('customers_table_columns', $table_data);

                     render_datatable($table_data,'clients-campaign',[],[
                           'data-last-order-identifier' => 'customers',
                           'data-default-order'         => get_table_last_order('customers'),
                     ]);
                     ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="test_campaign">
                      <p class="text-danger"><?php echo _l('test_campaign_note'); ?></p>
                      <?php if($campaign_test){ ?>
                        <div class="row">
                          <div class="col-md-6">
                        <a href="#" onclick="run_now_test_campaign(<?php echo html_entity_decode($campaign->id); ?>); return false;" class="btn btn-primary mbot15"><?php echo _l('run_now'); ?></a>
                        <a href="#" onclick="refresh_test_campaign(<?php echo html_entity_decode($campaign->id); ?>); return false;" class="btn btn-success mbot15"><?php echo _l('refresh'); ?></a>
                        <a href="#" onclick="delete_test_campaign(<?php echo html_entity_decode($campaign->id); ?>); return false;" class="btn btn-danger mbot15"><?php echo _l('ma_delete'); ?></a>

                            <table class="table table-striped  no-margin">
                              <tbody>
                                <tr class="project-overview">
                                  <td class="bold" width="30%"><?php echo _l('name'); ?></td>
                                  <td><?php echo html_entity_decode($campaign_test->name); ?></td>
                               </tr>
                               <tr class="project-overview">
                                  <td class="bold"><?php echo _l('email'); ?></td>
                                  <td><?php echo html_entity_decode($campaign_test->email); ?></td>
                               </tr>
                                <tr class="project-overview">
                                  <td class="bold"><?php echo _l('point'); ?></td>
                                  <?php
                                    $text_class = 'text-success';
                                    if($campaign_test->point < 0){
                                      $text_class = 'text-danger';
                                    }
                                  ?>
                                  <td><span class="<?php echo html_entity_decode($text_class); ?>"><?php echo html_entity_decode($campaign_test->point); ?></span></td>
                               </tr>
                               <?php if ($campaign_test->segment_id != 0){ ?>
                                <tr class="project-overview">
                                  <td class="bold"><?php echo _l('segment'); ?></td>
                                  <td><span class="text-info"><?php echo ma_get_segment_name($campaign_test->segment_id); ?></span></td>
                               </tr>
                               <?php } ?>
                               <?php if ($campaign_test->stage_id != 0){ ?>
                                <tr class="project-overview">
                                  <td class="bold"><?php echo _l('stage'); ?></td>
                                  <td><span class="text-warning"><?php echo ma_get_stage_name($campaign_test->stage_id); ?></span></td>
                               </tr>
                               <?php } ?>
                               <?php if ($campaign_test->status != 0){ ?>
                                <tr class="project-overview">
                                  <td class="bold"><?php echo _l('lead_status'); ?></td>
                                  <td><span class="text-secondary"><?php echo ma_get_leads_status_name($campaign_test->status); ?></span></td>
                               </tr>

                               <?php } ?>
                               <?php if ($campaign_test->tags != ''){ ?>
                                <tr class="project-overview">
                                  <td class="bold"><?php echo _l('tags'); ?></td>
                                  <td><?php echo render_tags($campaign_test->tags); ?></td>
                               </tr>
                               <?php } ?>
                               <?php if ($campaign_test->delete_lead != 0){ ?>
                                <tr class="project-overview">
                                  <td class="bold"><?php echo _l('delete_lead'); ?></td>
                                  <td><span class="text-success"><?php echo _l('settings_yes'); ?></span></td>
                               </tr>
                               <?php } ?>
                               <?php if ($campaign_test->remove_from_campaign != 0){ ?>
                                <tr class="project-overview">
                                  <td class="bold"><?php echo _l('remove_from_campaign'); ?></td>
                                  <td><span class="text-success"><?php echo _l('settings_yes'); ?></span></td>
                               </tr>
                               <?php } ?>
                               <?php if ($campaign_test->convert_to_customer != 0){ ?>
                                <tr class="project-overview">
                                  <td class="bold"><?php echo _l('convert_to_customer'); ?></td>
                                  <td><span class="text-success"><?php echo _l('settings_yes'); ?></span></td>
                               </tr>
                               <?php } ?>
                              </tbody>
                            </table>
                          </div>
                          <div class="col-md-6">
                            <div class="activity-feed">
                            <?php foreach ($campaign_test->data_logs as $log) { ?>
                                  <?php if ($log['result'] != '') { ?>
                            <div class="feed-item">
                                <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip"
                                        data-title="<?php echo _dt($log['dateadded']); ?>">
                                        <?php echo _dt($log['dateadded']); ?>
                                    </span>
                                </div>
                                <div class="text">
                                  <?php echo html_entity_decode($log['result']); ?>
                                </div>
                            </div>
                                  <?php } ?>
                            <?php } ?>
                        </div>
                          </div>
                        </div>
                      <?php }else{ ?>
                        <a href="#" onclick="test_campaign(); return false;" class="btn btn-primary mbot15"><?php echo _l('start_test'); ?></a>
                      <?php } ?>
                    </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
     
   </div>
</div>


<div class="modal fade" id="test-campaign-modal">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('test_campaign')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('ma/add_test_campaign'),array('id'=>'test-campaign-form'));?>
         <?php echo form_hidden('campaign_id', $campaign->id); ?>
         
         <div class="modal-body">
              <?php echo render_input('name', 'name') ?>
              <?php echo render_input('email', 'email', '', 'email') ?>
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
<?php require 'modules/ma/assets/js/campaigns/workflow_builder_js.php';?>
<?php require 'modules/ma/assets/js/campaigns/campaign_detail_js.php';?>
</body>
</html>
