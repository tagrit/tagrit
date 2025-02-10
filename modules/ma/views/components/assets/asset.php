<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="panel_s">
         <?php
            $form_class = '';
            if(isset($asset)){
             echo form_hidden('is_edit','true');
            }else{
               $form_class = 'dropzone dropzone-manual';
            }
            ?>
         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'expense-form','class'=>$form_class)) ;?>
         <div class="panel-body">
            <div class="clearfix"></div>
            <h4 class="no-margin"><?php echo html_entity_decode($title); ?></h4>
            <hr class="hr-panel-heading" />
            <div class="btn-bottom-toolbar text-right">
               <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
            <div class="row">
            <div class="col-md-6">
               <?php $value = (isset($asset) ? $asset->name : ''); ?>
               <?php echo render_input('name','name',$value); ?>
               <?php $value = (isset($asset) ? $asset->category : ''); ?>
               <?php echo render_select('category',$category, array('id', 'name'),'category',$value); ?>
               <?php $value = (isset($asset) ? $asset->change_points : ''); ?>
               <?php echo render_input('change_points','change_point_when_downloading',$value, 'number'); ?>
               <?php $value = (isset($asset) ? $asset->color : ''); ?>
               <?php echo render_color_picker('color',_l('color'),$value); ?>
               <div class="form-group">
                 <?php
                   $selected = (isset($asset) ? $asset->published : ''); 
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
                $description = (isset($asset) ? $asset->description : ''); 
                ?>
               <p class="bold"><?php echo _l('dt_expense_description'); ?></p>
               <?php echo render_textarea('description','',$description); ?>
            </div>
            <div class="col-md-6">
               <?php if(isset($asset) && $asset->attachment !== ''){ ?>
               <div class="row">
                  <div class="col-md-12">
                     <i class="<?php echo get_mime_class($asset->filetype); ?>"></i> <a href="<?php echo admin_url('ma/download_file/ma_asset/'.$asset->id); ?>"><?php echo html_entity_decode($asset->attachment); ?></a>
                  </div>
               </div>
               <?php } ?>
               <?php if(!isset($asset)){ ?>
               <div id="dropzoneDragArea" class="dz-default dz-message">
                  <span><?php echo _l('attachment'); ?></span>
               </div>
               <div class="dropzone-previews"></div>
               <?php } ?>
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
