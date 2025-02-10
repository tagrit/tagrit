<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s mtop15">
               <div class="panel-body">
                <h4 class="customer-profile-group-heading"><?php echo _l('point_action'); ?></h4>
                  <h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800"><?php echo _l('general_info'); ?></h4>
                  <div class="row">
                    <div class="col-md-6">
                      <?php echo form_hidden('timezone', date_default_timezone_get()); ?>
                      <?php echo form_hidden('point_action_id', $point_action->id); ?>
                      <table class="table table-striped no-margin">
                        <tbody>
                            <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('name'); ?></td>
                              <td><?php echo html_entity_decode($point_action->name) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('category'); ?></td>
                              <td><?php echo ma_get_category_name($point_action->category) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <?php $value = (($point_action->published == 1) ? _l('yes') : _l('no')); ?>
                              <?php $text_class = (($point_action->published == 1) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold"><?php echo _l('published'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('when_a_contact'); ?></td>
                              <td><?php echo _l($point_action->action) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('description'); ?></td>
                              <td><?php echo html_entity_decode($point_action->description) ; ?></td>
                           </tr>
                          </tbody>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <table class="table table-striped no-margin">
                        <tbody>
                            
                           <tr class="project-overview">
                              <?php $value = $point_action->change_points; ?>
                              <?php $text_class = (($point_action->change_points >= 0) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold" width="30%"><?php echo _l('change_points'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                          </tr>
                          <tr class="project-overview">
                              <?php $value = (($point_action->add_points_by_country == 1) ? _l('yes') : _l('no')); ?>
                              <?php $text_class = (($point_action->add_points_by_country == 1) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold"><?php echo _l('add_points_by_country'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                           </tr>
                    </table>
                    <?php if ($point_action->add_points_by_country == 1) { ?>
                        <table class="table items">
                            <thead>
                              <tr class="project-overview">
                                  <th class="text-center bold"><?php echo _l('country'); ?></th>
                                  <th class="text-center bold"><?php echo _l('change_points'); ?></th>
                               </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($point_action->change_point_details as $key => $value) { 
                                ?>
                                <tr class="project-overview">
                                  <td class="" width="30%"><?php echo get_country($value['country'])->short_name; ?></td>
                                  <?php $point = $value['change_points']; ?>
                                  <?php $text_class = (($value['change_points'] >= 0) ? 'text-success' : 'text-danger'); ?>
                                  <td class="text-center <?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($point) ; ?></td>
                               </tr>
                            <?php } ?>
                        </table>
                    <?php } ?>
                  </div>
                </div>
                <div class="horizontal-scrollable-tabs preview-tabs-top">
                  <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                    <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                    <div class="horizontal-tabs">
                      <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                          <li role="presentation" class="active">
                             <a href="#chart_statistics" aria-controls="chart_statistics" role="tab" id="tab_expiry_date" data-toggle="tab">
                                <?php echo _l('chart_statistics'); ?>
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
                  <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="chart_statistics">
                      <div id="container_chart"></div>
                      <div id="container_campaign_chart" class="mtop25"></div>
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
                              render_datatable($table_data,'leads-point-action',
                              array('customizable-table'),
                              array(
                               'id'=>'table-leads-point-action',
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
<?php init_tail(); ?>

</body>
</html>
<?php require('modules/ma/assets/js/points/point_action_detail_js.php'); ?>
