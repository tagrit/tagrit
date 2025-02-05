<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($driver)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('vehicle_assignments'); ?></h4>
<?php if(has_permission('fleet_driver','','create')){ ?>
<a href="#" onclick="add_vehicle_assignment(); return false;" class="btn btn-info mbot25<?php if($driver->active == 0){echo ' disabled';} ?>"><?php echo _l('new'); ?></a>
<div class="clearfix"></div>
<?php } ?>
<?php render_datatable(array(
_l('vehicle'),
_l('driver'),
  _l('start_time'),
  _l('end_time'),
  _l('starting_odometer'),
  _l('ending_odometer'),
_l('addedfrom'),
  ),'vehicle-assignments'); ?>


<?php $arrAtt = array();
      $arrAtt['data-type']='currency';
?>

<div class="modal fade" id="vehicle-assignment-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l('vehicle_assignment')?></h4>
      </div>
      <?php echo form_open_multipart(admin_url('fleet/vehicle_assignment'),array('id'=>'vehicle-assignment-form'));?>
         <?php echo form_hidden('id'); ?>
      <div class="modal-body">
        <?php echo render_select('vehicle_id',$vehicles, array('id', 'name'),'vehicle'); ?>
        <?php echo render_select('driver_id',$drivers, array('staffid', array('firstname', 'lastname')),'driver', $driver->staffid); ?>
        <div class="row">
          <div class="col-md-6">
            <?php echo render_datetime_input('start_time','start_time'); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_input('starting_odometer','starting_odometer', '', 'text', $arrAtt); ?>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <?php echo render_datetime_input('end_time','end_time'); ?>
          </div>
          <div class="col-md-6">
            <?php echo render_input('ending_odometer','ending_odometer', '', 'text', $arrAtt); ?>
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
<?php } ?>