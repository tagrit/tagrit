<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-6">
      <div class="panel_s mtop15">
         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'email-form')) ;?>
         <div class="panel-body">
            <h4 class="customer-profile-group-heading"><?php echo html_entity_decode($title); ?></h4>

            <div class="btn-bottom-toolbar text-right">
               <a href="<?php echo admin_url('ma/channels?group=emails'); ?>" class="btn btn-default"><?php echo _l('back'); ?></a>
               <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
            <div class="horizontal-scrollable-tabs">
               <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
               <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
               <div class="horizontal-tabs">
                  <ul class="nav nav-tabs nav-tabs-horizontal mbot15 customer-profile-tabs" role="tablist">
                       <li role="presentation" class="active">
                          <a href="#information" aria-controls="information" role="tab" id="tab_expiry_date" data-toggle="tab">
                             <?php echo _l('information'); ?>
                          </a>
                       </li>
                       <li role="presentation" >
                          <a href="#advanced" aria-controls="advanced" role="tab" id="tab_expiry_date" data-toggle="tab">
                             <?php echo _l('advanced'); ?>
                          </a>
                       </li>
                   </ul>
               </div>
            </div>
            <div class="tab-content mtop15">
               <div role="tabpanel" class="tab-pane active" id="information">
                     <?php $value = (isset($email) ? $email->subject : ''); ?>
                     <?php echo render_input('subject','subject',$value); ?>
                     <?php $value = (isset($email) ? $email->name : ''); ?>
                     <?php echo render_input('name','internal_name',$value); ?>
                     <?php $value = (isset($email) ? $email->category : ''); ?>
                     <?php echo render_select('category',$category, array('id', 'name'),'category',$value); ?>
                     <?php $email_template = (isset($email) ? $email->email_template : ''); ?>
                        <?php echo render_select('email_template',$email_templates, array('id', 'name'),'email_template',$email_template); ?>
                     <div class="form-group">
                       <?php
                         $selected = (isset($email) ? $email->published : ''); 
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
                     
                     <?php $value = (isset($email) ? $email->color : ''); ?>
                     <?php echo render_color_picker('color',_l('color'),$value); ?>
                        <?php
                      $description = (isset($email) ? $email->description : ''); 
                      ?>
                     <p class="bold"><?php echo _l('dt_expense_description'); ?></p>
                     <?php echo render_textarea('description','',$description,array(),array(),'','tinymce'); ?>
               </div>
               <div role="tabpanel" class="tab-pane" id="advanced">
                  <?php $value = (isset($email) ? $email->from_name : ''); ?>
                  <?php echo render_input('from_name','from_name',$value); ?>
                  <?php $value = (isset($email) ? $email->from_address : ''); ?>
                  <?php echo render_input('from_address','from_address',$value, 'email'); ?>
                  <?php $value = (isset($email) ? $email->reply_to_address : ''); ?>
                  <?php echo render_input('reply_to_address','reply_to_address',$value, 'email'); ?>
               
                  <?php $value = (isset($email) ? $email->bcc_address : ''); ?>
                  <?php echo render_input('bcc_address','bcc_address',$value, 'email'); ?>
                  <?php $value = (isset($email) ? $email->attachment : ''); ?>
                  <?php echo render_select('attachment',$assets, array('id', 'name'),'attachment',$value); ?>
               </div>
            </div>
         </div>
         <?php echo form_close(); ?>
      </div>
      </div>
      <div id="preview_area" class="col-md-6 no-padding build-section">
      </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>

</body>
</html>
