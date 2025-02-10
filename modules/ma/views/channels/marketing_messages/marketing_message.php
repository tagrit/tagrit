<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="panel_s">
         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'marketing-message-form')) ;?>
         <div class="panel-body">
            <div class="clearfix"></div>
            <h4 class="no-margin"><?php echo html_entity_decode($title); ?></h4>
            <hr class="hr-panel-heading" />
            <div class="btn-bottom-toolbar text-right">
               <a href="<?php echo admin_url('ma/channels?group=marketing_messages'); ?>" class="btn btn-default"><?php echo _l('back'); ?></a>
               <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
            <div class="row">
               <div class="col-md-6">
                  <?php $value = (isset($marketing_message) ? $marketing_message->name : ''); ?>
                  <?php echo render_input('name','name',$value); ?>
                  <?php $value = (isset($marketing_message) ? $marketing_message->category : ''); ?>
                  <?php echo render_select('category',$category, array('id', 'name'),'category',$value); ?>
                  <div class="form-group">
                    <?php
                      $selected = (isset($marketing_message) ? $marketing_message->published : ''); 
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
                   $description = (isset($marketing_message) ? $marketing_message->description : ''); 
                   ?>
                  <p class="bold"><?php echo _l('dt_expense_description'); ?></p>
                  <?php echo render_textarea('description','',$description,array(),array(),'','tinymce'); ?>
               </div>
               <div class="col-md-6">
                  <?php $types = [
                     ['id' => 'email', 'name' => _l('email')],
                     ['id' => 'web_notification', 'name' => _l('web_notification')],
                  ]; ?>
                  <?php $type = (isset($marketing_message) ? $marketing_message->type : ''); ?>
                  <?php echo render_select('type',$types, array('id', 'name'),'type',$type); ?>
                  <div id="div_email" class="<?php echo ($type == 'email') ? '' : 'hide'; ?>">
                     <?php $value = (isset($marketing_message) ? $marketing_message->email_template : ''); ?>
                     <?php echo render_select('email_template',$email_templates, array('id', 'name'),'email_template',$value); ?>
                  </div>
                  <div id="div_web_notification" class="<?php echo ($type == 'web_notification') ? '' : 'hide'; ?>">
                     <?php $value=( isset($marketing_message) ? $marketing_message->web_notification_description : ''); ?>
                     <?php echo render_textarea( 'web_notification_description', 'web_notification_description',$value); ?>
                     <?php $value = (isset($marketing_message) ? $marketing_message->web_notification_link : ''); ?>
                     <?php echo render_input('web_notification_link','web_notification_link',$value); ?>
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
