<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($driver)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('driver_license'); ?></h4>
<?php if(has_permission('fleet_driver','','create')){ ?>
<a href="#" onclick="add_vehicle_assignment(); return false;" class="btn btn-info mbot25<?php if($driver->active == 0){echo ' disabled';} ?>"><?php echo _l('new'); ?></a>
<div class="clearfix"></div>
<?php } ?>
<?php render_datatable(array(
  _l('subject'),
  _l('driver'),
  _l('start_date'),
  _l('end_date'),
  ),'driver-license'); ?>
<?php } ?>