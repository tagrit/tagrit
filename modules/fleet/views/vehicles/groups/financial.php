<?php if (isset($vehicle)) { ?>
<h4 class="customer-profile-group-heading"><?php echo _l('financial'); ?></h4>
<?php } ?>

<?php echo form_open($this->uri->uri_string(), ['class' => 'vehicle-form', 'autocomplete' => 'off']); ?>
<h4><?php echo _l('purchase_details'); ?></h4>
<?php $value = (isset($vehicle) ? $vehicle->purchase_vendor : ''); ?>
<?php echo render_select('purchase_vendor', $vendors, array('userid', 'company'), 'purchase_vendor', $value); ?>

<div class="row">
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->purchase_date : ''); ?>
    <?php echo render_date_input('purchase_date','purchase_date', $value); ?>
  </div>
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->purchase_price : ''); ?>
    <?php echo render_input('purchase_price','purchase_price', $value); ?>
  </div>
</div>
<?php $value = (isset($vehicle) ? $vehicle->odometer : ''); ?>
<?php echo render_input('odometer','odometer', $value); ?>

<?php $value = (isset($vehicle) ? $vehicle->notes : ''); ?>
<?php echo render_textarea('notes','notes', $value); ?>
<hr>

<h4><?php echo _l('warranty'); ?></h4>
<div class="row">
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->expiration_date : ''); ?>
    <?php echo render_date_input('expiration_date','expiration_date', $value); ?>
  </div>
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->max_meter_value : ''); ?>
    <?php echo render_input('max_meter_value','max_meter_value', $value); ?>
  </div>
</div>
<hr>
<?php echo form_close(); ?>
