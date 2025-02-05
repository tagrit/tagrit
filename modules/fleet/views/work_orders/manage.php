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
            <?php if(is_admin() || has_permission('fleet_work_orders', '', 'create')){ ?>
            <a href="<?php echo admin_url('fleet/work_order'); ?>" class="btn btn-info mbot10"><?php echo _l('add'); ?></a>
            <?php } ?>
          </div>
          <div class="row">
            <div class="col-md-3">
              <?php 
                $statuss = [
                     ['id' => 'open', 'name' => _l('open')],
                     ['id' => 'in_progress', 'name' => _l('in_progress')],
                     ['id' => 'parts_ordered', 'name' => _l('parts_ordered')],
                     ['id' => 'complete', 'name' => _l('complete')],
                  ];
                echo render_select('_status', $statuss, array('id', 'name'), 'status');
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
          <table class="table table-work-order scroll-responsive">
           <thead>
              <tr>
                 <th><?php echo _l('work_order_number'); ?></th>
                 <th><?php echo _l('vehicle'); ?></th>
                 <th><?php echo _l('vendor'); ?></th>
                 <th><?php echo _l('issue_date'); ?></th>
                 <th><?php echo _l('start_date'); ?></th>
                 <th><?php echo _l('complete_date'); ?></th>
                 <th><?php echo _l('total'); ?></th>
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
<div class="modal fade" id="work-order-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('work_order')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('fleet/work_order'),array('id'=>'work-order-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
            <?php echo render_input('subject', 'subject'); ?>
            <?php echo render_select('vehicle_id', $vehicles, array('id', 'name'), 'vehicle'); ?>
            <?php echo render_select('driver_id', $drivers, array('staffid', array('firstname', 'lastname')),'driver'); ?>
            <?php echo render_datetime_input('work_order_time', 'work_order_time'); ?>
            <?php echo render_select('work_order_type', $work_order_type, array('id', 'name'), 'work_order_type'); ?>
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
<?php require 'modules/fleet/assets/js/work_orders/manage_js.php'; ?>
