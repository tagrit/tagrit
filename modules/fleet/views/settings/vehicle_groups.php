<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
	<a href="#" class="btn btn-info add-new-vehicle-group mbot15"><?php echo _l('add'); ?></a>
</div>
<div class="row">
	<div class="col-md-12">
		<?php 
			$table_data = array(
        _l('id'),
				_l('name'),
        _l('addedfrom'),
				_l('datecreated'),
				);
			render_datatable($table_data,'vehicle-groups');
		?>
	</div>
</div>
<div class="clearfix"></div>
<div class="modal fade" id="vehicle-group-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l('vehicle_group')?></h4>
      </div>
      <?php echo form_open_multipart(admin_url('fleet/vehicle_group'),array('id'=>'vehicle-group-form'));?>
      <?php echo form_hidden('id'); ?>
      <div class="modal-body">
        <?php echo render_input('name','name'); ?>
        <div class="row">
          <div class="col-md-12">
            <p class="bold"><?php echo _l('description'); ?></p>
            <?php echo render_textarea('description','','',array(),array(),'','tinymce'); ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
      </div>
      <?php echo form_close(); ?>  
    </div>
  </div>
</div>