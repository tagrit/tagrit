<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'expense-form','autocomplete'=>'off', 'class' => 'dropzone dropzone-manual dz-max-files-reached')); ?>
          <h4 class="no-margin font-bold"><?php echo new_html_entity_decode($title); ?></h4>
          <hr />
          <?php echo form_hidden('driver_id', $driver_id); ?>
          <?php echo form_hidden('vehicle_id', $vehicle_id); ?>
          <?php echo form_hidden('type', $vehicle_id > 0 ? 'vehicle' : 'driver'); ?>
          
          <div class="row">
            <div class="col-md-6">
          <?php $value = (isset($driver_document) ? $driver_document->subject : ''); ?>
          <?php echo render_input('subject','subject', $value); ?>
              <?php $value = (isset($driver_document) ? $driver_document->description : ''); ?>
              <?php echo render_textarea('description','description',$value); ?>
          </div>
          <div class="col-md-6">
                  <?php 
                  if(isset($driver_document) && $driver_document->files){ 
                    foreach($driver_document->files as $attachment){

                    ?>

                  <div class="row mtop10">
                     <div class="col-md-10">
                        <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i> <a href="<?php echo admin_url('fleet/download_file/fle_driver_document/'.$attachment['id']); ?>"><?php echo new_html_entity_decode($attachment['file_name']); ?></a>
                     </div>
                     <?php if($attachment['staffid'] == get_staff_user_id() || is_admin()){ ?>
                     <div class="col-md-2 text-right">
                        <a href="<?php echo admin_url('fleet/delete_driver_document_attachment/'.$attachment['id'].'/'.$driver_document->id); ?>" class="text-danger _delete"><i class="fa fa fa-times"></i></a>
                     </div>
                    <?php } ?>
                  </div>
                  
                  <?php }
                  } 
                  ?>
                  <?php if(!isset($driver_document) || (isset($driver_document) && count($driver_document->files) == 0)){ ?>
                  <div id="dropzoneDragArea" class="dz-default dz-message">
                     <span><?php echo _l('acc_attachment'); ?></span>
                  </div>
                  <div class="dropzone-previews"></div>
                  <?php  
                    }
                  ?>
          </div>
          </div>
          <div class="row">
            <div class="col-md-12">    
              <div class="modal-footer">
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
              </div>
            </div>
          </div>
          <?php echo form_close(); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/fleet/assets/js/driver_documents/driver_document_js.php';?>
