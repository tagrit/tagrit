<?php if (isset($vehicle)) { ?>
<h4 class="customer-profile-group-heading"><?php echo _l('specifications'); ?></h4>
<?php } ?>

<?php echo form_open($this->uri->uri_string(), ['class' => 'vehicle-form', 'autocomplete' => 'off']); ?>
<h4><?php echo _l('dimensions'); ?></h4>
<div class="row">
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->width : ''); ?>
    <?php echo render_input('width','width', $value); ?>
  </div>
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->height : ''); ?>
    <?php echo render_input('height','fle_height', $value); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->length : ''); ?>
    <?php echo render_input('length','length', $value); ?>
  </div>
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->interior_volume : ''); ?>
    <?php echo render_input('interior_volume','interior_volume', $value); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->passenger_volume : ''); ?>
    <?php echo render_input('passenger_volume','passenger_volume', $value); ?>
  </div>
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->cargo_volume : ''); ?>
    <?php echo render_input('cargo_volume','cargo_volume', $value); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->ground_clearance : ''); ?>
    <?php echo render_input('ground_clearance','ground_clearance', $value); ?>
  </div>
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->bed_length : ''); ?>
    <?php echo render_input('bed_length','bed_length', $value); ?>
  </div>
</div>
<hr>
<h4><?php echo _l('fle_weight'); ?></h4>
<div class="row">
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->curb_weight : ''); ?>
    <?php echo render_input('curb_weight','curb_weight', $value); ?>
  </div>
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->gross_vehicle_weight_rating : ''); ?>
    <?php echo render_input('gross_vehicle_weight_rating','gross_vehicle_weight_rating', $value); ?>
  </div>
</div>
<hr>

<h4><?php echo _l('performance'); ?></h4>
<div class="row">
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->towing_capacity : ''); ?>
    <?php echo render_input('towing_capacity','towing_capacity', $value); ?>
  </div>
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->max_payload : ''); ?>
    <?php echo render_input('max_payload','max_payload', $value); ?>
  </div>
</div>
<hr>

<h4><?php echo _l('fuel_economy'); ?></h4>
<div class="row">
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->epa_city : ''); ?>
    <?php echo render_input('epa_city','epa_city', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->epa_highway : ''); ?>
    <?php echo render_input('epa_highway','epa_highway', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->epa_combined : ''); ?>
    <?php echo render_input('epa_combined','epa_combined', $value); ?>
  </div>
</div>
<hr>

<h4><?php echo _l('engine'); ?></h4>
<div class="row">
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->engine_summary : ''); ?>
    <?php echo render_input('engine_summary','engine_summary', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->engine_brand : ''); ?>
    <?php echo render_input('engine_brand','engine_brand', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->aspiration : ''); ?>
    <?php echo render_input('aspiration','aspiration', $value); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->block_type : ''); ?>
    <?php echo render_input('block_type','block_type', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->bore : ''); ?>
    <?php echo render_input('bore','bore', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->cam_type : ''); ?>
    <?php echo render_input('cam_type','cam_type', $value); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->compression : ''); ?>
    <?php echo render_input('compression','compression', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->cylinders : ''); ?>
    <?php echo render_input('cylinders','cylinders', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->displacement : ''); ?>
    <?php echo render_input('displacement','displacement', $value); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->fuel_induction : ''); ?>
    <?php echo render_input('fuel_induction','fuel_induction', $value); ?>
  </div>
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->max_hp : ''); ?>
    <?php echo render_input('max_hp','max_hp', $value); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->max_torque : ''); ?>
    <?php echo render_input('max_torque','max_torque', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->redline_rpm : ''); ?>
    <?php echo render_input('redline_rpm','redline_rpm', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->stroke : ''); ?>
    <?php echo render_input('stroke','stroke', $value); ?>
  </div>
</div>
<?php $value = (isset($vehicle) ? $vehicle->valves : ''); ?>
<?php echo render_input('valves','valves', $value); ?>
<hr>

<h4><?php echo _l('transmission'); ?></h4>
<div class="row">
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->transmission_summary : ''); ?>
    <?php echo render_input('transmission_summary','transmission_summary', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->transmission_brand : ''); ?>
    <?php echo render_input('transmission_brand','transmission_brand', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->transmission_type : ''); ?>
    <?php echo render_input('transmission_type','transmission_type', $value); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <?php $value = (isset($vehicle) ? $vehicle->transmission_gears : ''); ?>
    <?php echo render_input('transmission_gears','transmission_gears', $value); ?>
  </div>
</div>
<hr>

<h4><?php echo _l('wheels_tires'); ?></h4>
<div class="row">
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->drive_type : ''); ?>
    <?php echo render_input('drive_type','drive_type', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->brake_system : ''); ?>
    <?php echo render_input('brake_system','brake_system', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->front_track_width : ''); ?>
    <?php echo render_input('front_track_width','front_track_width', $value); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->rear_track_width : ''); ?>
    <?php echo render_input('rear_track_width','rear_track_width', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->wheelbase : ''); ?>
    <?php echo render_input('wheelbase','wheelbase', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->front_wheel_diameter : ''); ?>
    <?php echo render_input('front_wheel_diameter','front_wheel_diameter', $value); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->rear_wheel_diameter : ''); ?>
    <?php echo render_input('rear_wheel_diameter','rear_wheel_diameter', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->rear_axle : ''); ?>
    <?php echo render_input('rear_axle','rear_axle', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->front_tire_type : ''); ?>
    <?php echo render_input('front_tire_type','front_tire_type', $value); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->front_tire_psi : ''); ?>
    <?php echo render_input('front_tire_psi','front_tire_psi', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->rear_tire_type : ''); ?>
    <?php echo render_input('rear_tire_type','rear_tire_type', $value); ?>
  </div>
  <div class="col-md-4">
    <?php $value = (isset($vehicle) ? $vehicle->rear_tire_psi : ''); ?>
    <?php echo render_input('rear_tire_psi','rear_tire_psi', $value); ?>
  </div>
</div>
<hr>

<h4><?php echo _l('fuel'); ?></h4>
<div class="row">
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->fuel_type : ''); ?>
    <?php echo render_input('fuel_type','fuel_type', $value); ?>
  </div>
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->fuel_quality : ''); ?>
    <?php echo render_input('fuel_quality','fuel_quality', $value); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->fuel_tank_1_capacity : ''); ?>
    <?php echo render_input('fuel_tank_1_capacity','fuel_tank_1_capacity', $value); ?>
  </div>
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->fuel_tank_2_capacity : ''); ?>
    <?php echo render_input('fuel_tank_2_capacity','fuel_tank_2_capacity', $value); ?>
  </div>
</div>
<hr>

<h4><?php echo _l('oil'); ?></h4>
<div class="row">
  <div class="col-md-12">
    <?php $value = (isset($vehicle) ? $vehicle->oil_capacity : ''); ?>
    <?php echo render_input('oil_capacity','oil_capacity', $value); ?>
  </div>
</div>
<?php echo form_close(); ?>
