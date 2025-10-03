<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s mtop15">
               <div class="panel-body">
                <h4 class="customer-profile-group-heading"><?php echo _l('asset'); ?></h4>
                  <h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800"><?php echo _l('general_info'); ?></h4>
                  <div class="row">
                    <div class="col-md-6">
                      <?php echo form_hidden('asset_id', $asset->id); ?>
                      <?php echo form_hidden('timezone', date_default_timezone_get()); ?>
                      
                      <table class="table table-striped no-margin">
                        <tbody>
                            <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('name'); ?></td>
                              <td><?php echo html_entity_decode($asset->name) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('category'); ?></td>
                              <td><?php echo ma_get_category_name($asset->category) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <?php $value = $asset->change_points; ?>
                              <?php $text_class = (($asset->change_points >= 0) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold" width="30%"><?php echo _l('change_point_when_downloading'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                          </tr>
                           <tr class="project-overview">
                              <?php $value = (($asset->published == 1) ? _l('yes') : _l('no')); ?>
                              <?php $text_class = (($asset->published == 1) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold"><?php echo _l('published'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('color'); ?></td>
                              <td>
                                <div class="calendar-cpicker cpicker cpicker-big br_customer" style="background-color: <?php echo html_entity_decode($asset->color) ; ?>;"></div>
                              </td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('download_url'); ?></td>
                              <td>
                                <div class="row">
                                  <div class="pull-right _buttons mright15">
                                    <a href="javascript:void(0)" onclick="copy_public_link(); return false;" class="btn btn-warning btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('copy_link'); ?>" data-placement="bottom"><i class="fa fa-clone "></i></a>
                                  </div>
                                  <div class="col-md-9">
                                    <?php echo render_input('link_register','', site_url('ma/ma_public/download_file/ma_asset/'.$asset->id), 'text', ['readonly' => true]); ?>
                                 </div>
                                </div>
                              </td>
                           </tr>
                          
                          </tbody>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <table class="table table-striped no-margin">
                        <tbody>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('description'); ?></td>
                              <td><?php echo html_entity_decode($asset->description) ; ?></td>
                           </tr>
                          </tbody>
                    </table>
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
                      </ul>
                      </div>
                  </div>
                  <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="chart_statistics">
                      <div id="container_download_chart"></div>
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
<?php require('modules/ma/assets/js/components/asset_detail_js.php'); ?>
