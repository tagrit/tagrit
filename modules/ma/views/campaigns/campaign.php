<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
   <div class="content">
            <?php echo form_open($this->uri->uri_string(),array('class'=>'campaign-form','autocomplete'=>'off')); ?>
         
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
               <button class="btn btn-primary" type="submit">
               <?php echo _l( 'submit'); ?>
               </button>
            </div>
            <div class="panel_s mtop15">
               <div class="panel-body">
            <h4 class="customer-profile-group-heading"><?php echo _l('campaign'); ?></h4>
               <?php echo form_hidden('id',( isset($campaign) ? $campaign->id : '') ); ?>
                     <div class="row">
                        <div class="col-md-6">
                          <?php $name = ( isset($campaign) ? $campaign->name : '');
                              echo render_input('name','name',$name,'text'); ?>
                          <?php $value = ( isset($campaign) ? $campaign->category : ''); ?>
                          <?php echo render_select('category',$categories,array('id','name'), 'category', $value); ?>
                           <?php $value = (isset($campaign) ? $campaign->color : ''); ?>
                           <?php echo render_color_picker('color',_l('color'),$value); ?>
                           <div class="form-group">
                             <?php $selected = (isset($campaign) ? $campaign->published : ''); ?>
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
                           <div class="row">
                              <div class="col-md-6">
                                 <?php $value = (isset($campaign) ? _d($campaign->start_date) : _d(date('Y-m-d'))); ?>
                                 <?php echo render_date_input('start_date','start_date',$value);?>
                              </div>
                              <div class="col-md-6">
                                 <?php $due_date = (isset($campaign) ? _d($campaign->end_date) : _d(date('Y-m-d')));
                                 echo render_date_input('end_date','end_date',$due_date); ?>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6">
                          <?php
                            $description = (isset($campaign) ? $campaign->description : ''); 
                            ?>
                          <p class="bold"><?php echo _l('dt_expense_description'); ?></p>
                          <?php echo render_textarea('description','',$description,array(),array(),'','tinymce'); ?>
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
