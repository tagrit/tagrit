<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($vehicle)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('vehicle_document'); ?></h4>
<?php if(has_permission('fleet_vehicle','','create')){ ?>
<a href="<?php echo admin_url('fleet/driver_document?vehicle_id='.$vehicle->id); ?>" class="btn btn-info mbot25"><?php echo _l('new'); ?></a>
<div class="clearfix"></div>
<?php } ?>
<?php $this->load->view('vehicle_documents/table_html'); ?>
<?php } ?>
