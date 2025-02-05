<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($part)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('assignment_history'); ?></h4>
<?php echo form_hidden('type', 'assignee'); ?>

<?php render_datatable(array(
  _l('assignee'),
  _l('start_time'),
  _l('end_time'),
  _l('started_by'),
  _l('ended_by'),
  ),'part-histories'); ?>

<?php } ?>