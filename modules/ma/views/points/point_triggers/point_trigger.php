<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="panel_s mtop15">
         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'point-trigger-form')) ;?>
         <div class="panel-body">
            <h4 class="customer-profile-group-heading"><?php echo html_entity_decode($title); ?></h4>
            <div class="btn-bottom-toolbar text-right">
               <a href="<?php echo admin_url('ma/points?group=point_triggers'); ?>" class="btn btn-default"><?php echo _l('back'); ?></a>
               <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
            <?php if (isset($point_trigger)) { ?>
               <ul class="nav nav-tabs profile-tabs row customer-profile-tabs nav-tabs-horizontal" role="tablist">
                  <li role="presentation" class="active">
                     <a href="#tab_details" aria-controls="tab_details" role="tab" data-toggle="tab">
                     <?php echo _l('details'); ?>
                     </a>
                  </li>
                  <li role="presentation">
                     <a href="#tab_events" aria-controls="tab_events" role="tab" data-toggle="tab">
                     <?php echo _l('events'); ?>
                     </a>
                  </li>
               </ul>
               <?php } ?>
            <div class="tab-content mtop15">
               <div role="tabpanel" class="tab-pane active" id="tab_details">
                  <div class="row">
                  <div class="col-md-6">
                     <?php $value = (isset($point_trigger) ? $point_trigger->name : ''); ?>
                     <?php echo render_input('name','name',$value); ?>
                     <?php $value = (isset($point_trigger) ? $point_trigger->category : ''); ?>
                     <?php echo render_select('category',$category, array('id', 'name'),'category',$value); ?>
                     <div class="form-group">
                       <?php
                         $selected = (isset($point_trigger) ? $point_trigger->published : ''); 
                         ?>
                       <label for="published"><?php echo _l('published'); ?></label><br />
                       <div class="radio radio-inline radio-primary">
                         <input type="radio" name="published" id="published_yes" value="1" <?php if($selected == '1'|| $selected == ''){echo 'checked';} ?>>
                         <label for="published_yes"><?php echo _l("settings_yes"); ?></label>
                       </div>
                       <div class="radio radio-inline radio-primary">
                         <input type="radio" name="published" id="published_no" value="0" <?php if($selected == '0'){echo 'checked';} ?>>
                         <label for="published_no"><?php echo _l("settings_no"); ?></label>
                       </div>
                     </div>
                     <?php
                      $description = (isset($point_trigger) ? $point_trigger->description : ''); 
                      ?>
                     <p class="bold"><?php echo _l('dt_expense_description'); ?></p>
                     <?php echo render_textarea('description','',$description,array(),array(),'','tinymce'); ?>
                  </div>
                  <div class="col-md-6">
                     <?php $value = (isset($point_trigger) ? $point_trigger->minimum_number_of_points : ''); ?>
                     <?php echo render_input('minimum_number_of_points','minimum_number_of_points',$value, 'number'); ?>
                     <?php $value = (isset($point_trigger) ? $point_trigger->contact_color : ''); ?>
                     <?php echo render_color_picker('contact_color',_l('contact_color'),$value); ?>
                     
                  </div>
               </div>
               </div>
               <div role="tabpanel" class="tab-pane" id="tab_events">
                  <div class="btn-group btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                     <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="btn_filter">
                       <i class="fa fa-plus-circle" aria-hidden="true"></i> <?php echo _l('add_an_event'); ?>
                     </button>
                     <ul class="dropdown-menu width300">
                        <li class="filter-group disabled padding-10 btn-default" data-filter-group="group-date">
                               <?php echo _l('campaign_triggers'); ?>
                       </li>
                       <li class="filter-group" data-filter-group="group-date">
                           <a href="#" data-cview="modify_contact_is_campaigns" onclick="add_an_event('modify_contact_is_campaigns'); return false;">
                               <?php echo _l('modify_contact_is_campaigns'); ?>
                           </a>
                       </li>
                       <li class="filter-group disabled padding-10 btn-default" data-filter-group="group-date">
                               <?php echo _l('contact_triggers'); ?>
                       </li>
                       <li class="filter-group" data-filter-group="group-date">
                           <a href="#" data-cview="modify_contact_is_segments" onclick="add_an_event('modify_contact_is_segments'); return false;">
                               <?php echo _l('modify_contact_is_segments'); ?>
                           </a>
                       </li>
                       <li class="filter-group" data-filter-group="group-date">
                           <a href="#" data-cview="modify_contact_is_tags" onclick="add_an_event('modify_contact_is_tags'); return false;">
                               <?php echo _l('modify_contact_is_tags'); ?>
                           </a>
                       </li>
                       <li class="filter-group disabled padding-10 btn-default" data-filter-group="group-date">
                               <?php echo _l('addon_triggers'); ?>
                       </li>
                       <li class="filter-group" data-filter-group="group-date">
                           <a href="#" data-cview="push_contact_to_integration" onclick="add_an_event('push_contact_to_integration'); return false;">
                               <?php echo _l('push_contact_to_integration'); ?>
                           </a>
                       </li>
                       <li class="filter-group disabled padding-10 btn-default" data-filter-group="group-date">
                               <?php echo _l('email_triggers'); ?>
                       </li>
                       <li class="filter-group" data-filter-group="group-date">
                           <a href="#" data-cview="send_an_email" onclick="add_an_event('send_an_email'); return false;">
                               <?php echo _l('send_an_email'); ?>
                           </a>
                       </li>
                       <li class="filter-group" data-filter-group="group-date">
                           <a href="#" data-cview="send_an_email_to_user" onclick="add_an_event('send_an_email_to_user'); return false;">
                               <?php echo _l('send_an_email_to_user'); ?>
                           </a>
                       </li>
                     </ul>
                   </div>
               </div>
            </div>
         </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>
<?php init_tail(); ?>

</body>
</html>
