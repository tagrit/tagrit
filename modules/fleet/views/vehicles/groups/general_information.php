<?php $value = (isset($vehicle) ? $vehicle->name : ''); ?>
<?php echo render_input('name','name', $value); ?>
<?php $value = (isset($vehicle) ? $vehicle->vehicle_type_id : ''); ?>
<?php echo render_select('vehicle_type_id',$vehicle_types, array('id', 'name'),'vehicle_type',$value); ?>
<div class="row">
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->model : ''); ?>
    <?php echo render_input('model','model', $value); ?>
  </div>
  <div class="col-md-6">
    <?php $value = (isset($vehicle) ? $vehicle->year : ''); ?>
    <?php echo render_input('year','year', $value); ?>
  </div>
</div>
<?php $value = (isset($vehicle) ? $vehicle->vehicle_group_id : ''); ?>
<?php echo render_select('vehicle_group_id',$vehicle_groups, array('id', 'name'),'vehicle_group',$value); ?>
<?php 
    $status = [
       ['id' => 'active', 'name' => _l('active')],
       ['id' => 'inactive', 'name' => _l('inactive')],
       ['id' => 'in_shop', 'name' => _l('in_shop')],
       ['id' => 'out_of_service', 'name' => _l('out_of_service')],
       ['id' => 'sold', 'name' => _l('sold')],
    ];
 ?>

<?php $value = (isset($vehicle) ? $vehicle->status : ''); ?>
<?php echo render_select('status',$status, array('id', 'name'),'status',$value); ?>
