<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="panel_s">
         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'text-messages-form')) ;?>
         <div class="panel-body col-md-6">
            <div class="clearfix"></div>
            <h4 class="no-margin"><?php echo html_entity_decode($title); ?></h4>
            <hr class="hr-panel-heading" />
            
            <div class="col-md-12">
                <?php $value=( isset($text_message) ? $text_message->description : ''); ?>
               <?php echo render_textarea( 'description', 'description',$value); ?>
            </div>
            <hr class="hr-panel-heading" />
            <div class="col-md-12 text-right">
               <a href="<?php echo admin_url('ma/settings?group=text_messages'); ?>" class="btn btn-default"><?php echo _l('back'); ?></a>
               <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
         </div>
         <div class="col-md-6">
            <div class="panel_s">
               <div class="panel-body">
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
                                 if($key != 'leads' && $key != 'other'){
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
         <?php echo form_close(); ?>
      </div>
   </div>
</div>
<?php init_tail(); ?>

</body>
</html>
