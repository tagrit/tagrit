<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($vehicle)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('maintenances'); ?></h4>
<?php 
if(is_admin() || has_permission('fleet_maintenance', '', 'create')){
 ?>
 <button class="btn btn-primary mbot20" onclick="add_maintenances();"><?php echo _l('add'); ?></button>          
 <div class="clearfix"></div>
<?php } ?>
<div class="row">
  <div class="col-md-3">
    <?php 
    $maintenances = [
      ['id' => 'maintenance', 'maintenance_name' => _l('maintenance')],
      ['id' => 'repair', 'maintenance_name' => _l('repair')],
    ];
    echo render_select('maintenance_type_filter', $maintenances, array('id', 'maintenance_name'), 'maintenance_type');
    ?>
  </div>

  <div class="col-md-3">
    <?php echo render_date_input('from_date_filter', 'from_date'); ?>
  </div>

  <div class="col-md-3">
    <?php echo render_date_input('to_date_filter', 'to_date'); ?>
  </div>
  <div class="col-md-3"></div>
</div>

<div class="clearfix"></div>
<br>
  <table class="table table-maintenances scroll-responsive">
   <thead>
     <tr>
      <th>ID</th>
      <th><?php echo  _l('asset_name'); ?></th>
      <th><?php echo  _l('maintenance_type'); ?></th>
      <th><?php echo  _l('title'); ?></th>
      <th><?php echo  _l('start_date'); ?></th>
      <th><?php echo  _l('completion_date'); ?></th>
      <th><?php echo  _l('notes'); ?></th>
      <th><?php echo  _l('cost'); ?></th>
    </tr>
  </thead>
  <tbody></tbody>
</table>

<div class="modal fade" id="add_new_maintenances" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
         <span class="add-title hide"><?php echo _l('maintenance'); ?></span>
         <span class="edit-title"><?php echo _l('maintenance'); ?></span>
       </h4>
     </div>
     <?php echo form_open(admin_url('fleet/add_maintenance'),array('id'=>'maintenances-form')); ?>
     <div class="modal-body">
      <?php 
      $this->load->view('maintenances/maintenance_modal_content.php');
      ?>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
    </div>
    <?php echo form_close(); ?>                 
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<input type="hidden" name="are_you_sure_you_want_to_delete_these_items" value="<?php echo _l('are_you_sure_you_want_to_delete_these_items') ?>">
<input type="hidden" name="please_select_at_least_one_item_from_the_list" value="<?php echo _l('please_select_at_least_one_item_from_the_list') ?>">

<input type="hidden" name="check">
<?php } ?>