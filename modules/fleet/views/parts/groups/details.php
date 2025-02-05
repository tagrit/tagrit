<?php if (isset($part)) { ?>
<h4 class="customer-profile-group-heading"><?php echo _l('details'); ?></h4>
<?php } ?>
<?php $arrAtt = array();
      $arrAtt['data-type']='currency';
?>
<?php echo form_open($this->uri->uri_string(), ['class' => 'part-form', 'autocomplete' => 'off']); ?>

<h4><?php echo _l('identification'); ?></h4>
<?php $value = (isset($part) ? $part->name : ''); ?>
<?php echo render_input('name','name', $value); ?>

<?php $value = (isset($part) ? $part->part_type_id : ''); ?>
<?php echo render_select('part_type_id',$part_types, array('id', 'name'),'part_type',$value); ?>

<?php $value = (isset($part) ? $part->brand : ''); ?>
<?php echo render_input('brand','brand', $value); ?>

<?php $value = (isset($part) ? $part->model : ''); ?>
<?php echo render_input('model','model', $value); ?>

<?php $value = (isset($part) ? $part->serial_number : ''); ?>
<?php echo render_input('serial_number','serial_number', $value); ?>

<hr>
<h4><?php echo _l('custody'); ?></h4>

<?php $value = (isset($part) ? $part->vehicle_id : ''); ?>
<?php echo render_select('vehicle_id',$vehicles, array('id', 'name'),'linked_vehicle',$value); ?>

<?php $value = (isset($part) ? $part->driver_id : ''); ?>
<?php echo render_select('driver_id',$drivers, array('staffid', array('firstname', 'lastname')),'current_assignee',$value); ?>

<hr>
<h4><?php echo _l('classification'); ?></h4>

<?php $value = (isset($part) ? $part->part_group_id : ''); ?>
<?php echo render_select('part_group_id',$part_groups, array('id', 'name'),'part_group',$value); ?>

<?php 
    $status = [
       ['id' => 'in_service', 'name' => _l('in_service')],
       ['id' => 'out_of_service', 'name' => _l('out_of_service')],
       ['id' => 'disposed', 'name' => _l('disposed')],
       ['id' => 'missing', 'name' => _l('missing')],
    ];
 ?>

<?php $value = (isset($part) ? $part->status : ''); ?>
<?php echo render_select('status', $status, array('id', 'name'), 'status',$value); ?>

<hr>
<h4><?php echo _l('purchase_information'); ?></h4>
<?php $value = (isset($part) ? $part->purchase_vendor : ''); ?>
<?php echo render_select('purchase_vendor', $vendors, array('userid', 'company'), 'purchase_vendor', $value); ?>

<?php $value = (isset($part) ? $part->purchase_date : ''); ?>
<?php echo render_date_input('purchase_date','purchase_date', $value); ?>

<?php $value = (isset($part) ? $part->purchase_price : ''); ?>
<?php echo render_input('purchase_price','purchase_price', $value, 'text', $arrAtt); ?>

<?php $value = (isset($part) ? $part->warranty_expiration_date : ''); ?>
<?php echo render_date_input('warranty_expiration_date','warranty_expiration_date', $value); ?>

<?php $value = (isset($part) ? $part->purchase_comments : ''); ?>
<?php echo render_textarea('purchase_comments','purchase_comments', $value); ?>

<hr>
<h4><?php echo _l('lifecycle'); ?></h4>
<?php $value = (isset($part) ? $part->in_service_date : ''); ?>
<?php echo render_date_input('in_service_date','in_service_date', $value); ?>

<?php $value = (isset($part) ? $part->estimated_service_life_in_months : ''); ?>
<?php echo render_input('estimated_service_life_in_months','estimated_service_life_in_months', $value, 'number'); ?>

<?php $value = (isset($part) ? $part->estimated_resale_value : ''); ?>
<?php echo render_input('estimated_resale_value','estimated_resale_value', $value, 'text', $arrAtt); ?>

<?php $value = (isset($part) ? $part->out_of_service_date : ''); ?>
<?php echo render_date_input('out_of_service_date','out_of_service_date', $value); ?>

<?php echo form_close(); ?>