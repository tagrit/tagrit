<?php if (isset($vehicle)) { ?>
<h4 class="customer-profile-group-heading"><?php echo _l('lifecycle'); ?></h4>
<?php } ?>

<?php echo form_open($this->uri->uri_string(), ['class' => 'vehicle-form', 'autocomplete' => 'off']); ?>
<h4><?php echo _l('in_service'); ?></h4>
<div class="row">
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->in_service_date : ''); ?>
    <?php echo render_date_input('in_service_date','in_service_date', $value); ?>
  </div>
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->in_service_odometer : ''); ?>
    <?php echo render_input('in_service_odometer','in_service_odometer', $value); ?>
  </div>
</div>
<hr>

<h4><?php echo _l('vehicle_life_estimates'); ?></h4>
<div class="row">
  <div class="col-md-12">
    <?php $value = (isset($vehicle) ? $vehicle->estimated_service_life_in_months : ''); ?>
    <?php echo render_input('estimated_service_life_in_months','estimated_service_life_in_months', $value); ?>
  </div>
  <div class="col-md-12">
    <?php $value = (isset($vehicle) ? $vehicle->estimated_service_life_in_meter : ''); ?>
    <?php echo render_input('estimated_service_life_in_meter','estimated_service_life_in_meter', $value); ?>
  </div>
  <div class="col-md-12">
    <?php $value = (isset($vehicle) ? $vehicle->estimated_resale_value : ''); ?>
    <?php echo render_input('estimated_resale_value','estimated_resale_value', $value); ?>
  </div>
</div>
<hr>

<h4><?php echo _l('out_of_service'); ?></h4>
<div class="row">
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->out_of_service_date : ''); ?>
    <?php echo render_date_input('out_of_service_date','out_of_service_date', $value); ?>
  </div>
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->out_of_service_odometer : ''); ?>
    <?php echo render_input('out_of_service_odometer','out_of_service_odometer', $value); ?>
  </div>
</div>
<?php echo form_close(); ?>
