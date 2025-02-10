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
               <?php $value = (isset($text_message) ? $text_message->name : ''); ?>
               <?php echo render_input('name','name',$value); ?>
               <?php $value = (isset($text_message) ? $text_message->category : ''); ?>
               <?php echo render_select('category',$category, array('id', 'name'),'category',$value); ?>
               
               <div class="form-group">
                 <?php
                   $selected = (isset($text_message) ? $text_message->published : ''); 
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
                <?php $value=( isset($text_message) ? $text_message->description : ''); ?>
               <?php echo render_textarea( 'description', 'description',$value); ?>
            </div>
            <hr class="hr-panel-heading" />
            <div class="col-md-12 text-right">
               <a href="<?php echo admin_url('ma/settings?group=text_messages'); ?>" class="btn btn-default"><?php echo _l('back'); ?></a>
               <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
         </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>
<?php init_tail(); ?>

</body>
</html>
