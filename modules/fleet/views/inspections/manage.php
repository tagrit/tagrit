<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row panel">
      <div class="col-md-12">
        <h4>
          <br>
          <?php echo new_html_entity_decode($title); ?>
          <hr>          
        </h4>
        <?php 
        if(is_admin() || has_permission('fleet_inspection', '', 'create')){
         ?>
         <button class="btn btn-primary mbot20" onclick="add_inspections();"><?php echo _l('add'); ?></button>          
         <div class="clearfix"></div>
       <?php } ?>

       <div class="row">
        <div class="col-md-3">
          <?php echo render_date_input('from_date_filter', 'fe_from_date'); ?>
        </div>

        <div class="col-md-3">
          <?php echo render_date_input('to_date_filter', 'fe_to_date'); ?>
        </div>
        <div class="col-md-3"></div>
      </div>

      <div class="clearfix"></div>
      <br>
      <div class="clearfix"></div>
      <table class="table table-inspections scroll-responsive">
       <thead>
         <tr>
          <th><?php echo  _l('vehicle_name'); ?></th>
          <th><?php echo  _l('inspection_form'); ?></th>
          <th><?php echo  _l('addedfrom'); ?></th>
          <th><?php echo  _l('datecreated'); ?></th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

  </div>
</div>
</div>
</div>

<div class="modal fade" id="add_new_inspections" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
         <span class="add-title hide"><?php echo _l('inspections'); ?></span>
         <span class="edit-title"><?php echo _l('inspections'); ?></span>
       </h4>
     </div>
     <?php echo form_open(admin_url('fleet/add_inspection'),array('id'=>'inspections-form')); ?>
     <div class="modal-body">
      <?php 
        echo form_hidden('id');
        echo render_select('vehicle_id', $vehicles, array('id', 'name'), 'vehicle');
        echo render_select('inspection_form_id', $inspection_forms, array('id', 'name'), 'inspection_form');
      ?>
      <div class="inspection-form-content">
        
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
    </div>
    <?php echo form_close(); ?>                 
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
</body>
</html>
