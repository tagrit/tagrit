<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(has_permission('wh_setting', '', 'create')){ ?>
<a href="#" onclick="warehouse_permissions_update(0,0,' hide'); return false;" class="btn btn-info mbot10"><?php echo _l('add'); ?></a>
<?php } ?>
<table class="table table-warehouse-permission">
  <thead>
    <th><?php echo _l('staff_name'); ?></th>
    <th><?php echo _l('role'); ?></th>
    <th><?php echo _l('staff_dt_email'); ?></th>
    <th><?php echo _l('phone'); ?></th>
    <th><?php echo _l('options'); ?></th>
  </thead>
  <tbody>
  </tbody>
</table>
<div id="modal_wrapper"></div>

