<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="panel_s">
         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'point-action-form')) ;?>
         <div class="panel-body">
            <div class="clearfix"></div>
            <h4 class="no-margin"><?php echo html_entity_decode($title); ?></h4>
            <hr class="hr-panel-heading" />
            <div class="btn-bottom-toolbar text-right">
               <a href="<?php echo admin_url('ma/points?group=point_actions'); ?>" class="btn btn-default"><?php echo _l('back'); ?></a>
               <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
            <div class="row">
            <div class="col-md-6">
               <?php $value = (isset($point_action) ? $point_action->name : ''); ?>
               <?php echo render_input('name','name',$value); ?>
               <?php $value = (isset($point_action) ? $point_action->category : ''); ?>
               <?php echo render_select('category',$category, array('id', 'name'),'category',$value); ?>
               <div class="form-group">
                 <?php
                   $selected = (isset($point_action) ? $point_action->published : ''); 
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
                $description = (isset($point_action) ? $point_action->description : ''); 
                ?>
               <p class="bold"><?php echo _l('dt_expense_description'); ?></p>
               <?php echo render_textarea('description','',$description,array(),array(),'','tinymce'); ?>
            </div>
            <div class="col-md-6">
               <?php 
                  $actions = [
                     ['id' => 'downloads_an_asset', 'name' => _l('downloads_an_asset')],
                     ['id' => 'is_sent_an_email', 'name' => _l('is_sent_an_email')],
                     ['id' => 'opens_an_email', 'name' => _l('opens_an_email')],
                     ['id' => 'submit_a_form', 'name' => _l('submit_a_form')],
                  ];
               ?>

               <?php $value = (isset($point_action) ? $point_action->action : ''); ?>
               <?php echo render_select('action',$actions, array('id', 'name'),'when_a_contact',$value); ?>
               <?php $value = (isset($point_action) ? $point_action->change_points : ''); ?>
               <?php echo render_input('change_points','change_points',$value, 'number'); ?>

               <div class="checkbox checkbox-primary">
                  <input type="checkbox" name="add_points_by_country" id="add_points_by_country" <?php if (isset($point_action) && $point_action->add_points_by_country == 1) {
                      echo 'checked';
                  } ?> value="1">
                  <label for="add_points_by_country"><?php echo _l('add_points_by_country'); ?></label>
               </div>
               <div id="div_add_points_by_country" class="<?php if (isset($point_action) && $point_action->add_points_by_country != 1 || !isset($point_action)) { echo ' hide'; } ?>">
                  <div class="col-md-12">
                    <div class="row list_ladder_setting">
                      <?php if(isset($point_action) && count($point_action->change_point_details) > 0) { 
                        $setting = $point_action->change_point_details;
                        ?>
                        <?php foreach ($setting as $key => $value) { ?>
                        <div id="item_ladder_setting">
                          <div class="row">
                            <div class="col-md-10">
                              <div class="row">
                                 <div class="col-md-6">
                                 <?php echo render_select('country['.$key.']', $countries,array('country_id',array( 'short_name')),'country', $value['country']); ?>
                               </div>
                               <div class="col-md-6">
                                 <?php echo render_input('list_change_points['.$key.']','change_points',$value['change_points'],'number'); ?>
                               </div>
                            </div>
                            </div>
                            <div class="col-md-2">
                            <span class="pull-bot">
                                <?php if($key != 0){ ?>
                                  <button name="add" class="btn remove_item_ladder btn-danger mtop25" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
                                <?php }else{ ?>
                                  <button name="add" class="btn new_item_ladder btn-success mtop25" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                <?php } ?>
                                  </span>
                            </div>
                          </div>
                        </div>
                        <?php } ?>
                      <?php }else{ ?>
                      <div id="item_ladder_setting">
                        <div class="row">
                          <div class="col-md-10">
                           <div class="row">
                               <div class="col-md-6">
                                 <?php echo render_select('country[0]', $countries,array('country_id',array( 'short_name')),'country'); ?>
                               </div>
                               <div class="col-md-6">
                                 <?php echo render_input('list_change_points[0]','change_points','','number'); ?>
                               </div>
                           </div>
                          </div>
                          <div class="col-md-2 no-padding">
                          <span class="pull-bot">
                              <button name="add" class="btn new_item_ladder btn-success mtop25" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                              </span>
                          </div>
                        </div>
                      </div>
                        <?php } ?>
                    </div>
                  </div>
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
