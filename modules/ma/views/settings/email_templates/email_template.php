<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="panel_s">
         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'email-template-form')) ;?>
         <div class="panel-body">
            <div class="clearfix"></div>
            <h4 class="no-margin"><?php echo html_entity_decode($title); ?></h4>
            <hr class="hr-panel-heading" />
            <div class="btn-bottom-toolbar text-right">
               <a href="<?php echo admin_url('ma/settings?group=ma_email_templates'); ?>" class="btn btn-default"><?php echo _l('back'); ?></a>
               <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
            <div class="row">
            <div class="col-md-6">
               <?php $value = (isset($email_template) ? $email_template->name : ''); ?>
               <?php echo render_input('name','name',$value); ?>
               <?php $value = (isset($email_template) ? $email_template->category : ''); ?>
               <?php echo render_select('category',$category, array('id', 'name'),'category',$value); ?>
               <?php $value = (isset($email_template) ? $email_template->color : ''); ?>
               <?php echo render_color_picker('color',_l('color'),$value); ?>
               <div class="form-group">
                 <?php
                   $selected = (isset($email_template) ? $email_template->published : ''); 
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
            </div>
            <div class="col-md-6">
               <?php
                $description = (isset($email_template) ? $email_template->description : ''); 
                ?>
               <p class="bold"><?php echo _l('dt_expense_description'); ?></p>
               <?php echo render_textarea('description','',$description,array(),array(),'','tinymce'); ?>
               <?php $types = [
                  ['id' => 'template_email_template', 'name' => _l('template_email_template')],
                  ['id' => 'segment_email_template', 'name' => _l('segment_email_template')],
               ]; ?>
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
