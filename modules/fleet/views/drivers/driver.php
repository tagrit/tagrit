<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'driver-form','autocomplete'=>'off')); ?>
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <hr />
          
          <?php $value = (isset($driver) ? $driver->firstname : ''); ?>
          <?php echo render_input('firstname','firstname', $value); ?>
          <?php $value = (isset($driver) ? $driver->lastname : ''); ?>
          <?php echo render_input('lastname','lastname', $value); ?>
          <?php $value = (isset($driver) ? $driver->email : ''); ?>
          <?php echo render_input('email','email', $value); ?>
          <?php $value = (isset($driver) ? $driver->phone : ''); ?>
          <?php echo render_input('phone','phone', $value); ?>

          <?php echo render_textarea('description','',$value,array(),array(),'','tinymce'); ?>
         
          <div class="row">
            <div class="col-md-12">
              <p class="bold"><?php echo _l('description'); ?></p>
              <?php $value = (isset($driver) ? $driver->description : ''); ?>
              <?php echo render_textarea('description','',$value,array(),array(),'','tinymce'); ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">    
              <div class="modal-footer">
                <button type="button" class="btn btn-info driver-form-submiter"><?php echo _l('submit'); ?></button>
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
<?php require 'modules/accounting/assets/js/drivers/driver_js.php';?>