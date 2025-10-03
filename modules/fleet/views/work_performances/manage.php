<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <hr />
          <div>
            <?php if(is_admin() || has_permission('fleet_work_performance', '', 'create')){ ?>
            <a href="#" class="btn btn-info add-new-logbook mbot15"><?php echo _l('add'); ?></a>
            <?php } ?>
          </div>
          <div class="row">
            <div class="col-md-3">
                <?php 
                echo render_select('status', $logbook_status, array('id', 'name'), 'status');
                ?>
              </div>
            <div class="col-md-3">
              <?php echo render_date_input('from_date','from_date'); ?>
            </div>
            <div class="col-md-3">
              <?php echo render_date_input('to_date','to_date'); ?>
            </div>
          </div>
          <hr>
          <table class="table table-logbook scroll-responsive">
           <thead>
              <tr>
                 <th><?php echo _l('name'); ?></th>
                 <th><?php echo _l('booking_number'); ?></th>
                 <th><?php echo _l('vehicle'); ?></th>
                 <th><?php echo _l('driver'); ?></th>
                 <th><?php echo _l('date'); ?></th>
                 <th><?php echo _l('status'); ?></th>
              </tr>
           </thead>
        </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $arrAtt = array();
      $arrAtt['data-type']='currency';
?>
<div class="modal fade" id="logbook-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('logbook')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('fleet/logbook'),array('id'=>'logbook-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
            <?php echo render_select('booking_id', $bookings, array('id', 'number'), 'booking'); ?>
            <?php echo render_select('vehicle_id', $vehicles, array('id', 'name'),'vehicle'); ?>
            <?php echo render_select('driver_id', $drivers, array('staffid', array('firstname', 'lastname')),'driver'); ?>
            <?php echo render_input('name', 'name'); ?>
            <?php echo render_input('odometer', 'odometer'); ?>
            <?php echo render_date_input('date', 'date'); ?>
            <?php echo render_textarea('description','description'); ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>
<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/fleet/assets/js/work_performances/logbook_manage_js.php'; ?>
