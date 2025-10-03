<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($driver)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('driver_documents'); ?></h4>
<?php if(has_permission('fleet_driver','','create')){ ?>
<a href="<?php echo admin_url('fleet/driver_document?driver_id='.$driver->staffid); ?>" class="btn btn-info mbot25<?php if($driver->active == 0){echo ' disabled';} ?>"><?php echo _l('new'); ?></a>
<div class="clearfix"></div>
<?php } ?>
<?php $this->load->view('driver_documents/table_html'); ?>
<?php } ?>
