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
            <?php if(is_admin() || has_permission('fleet_booking', '', 'create')){ ?>

            <a href="#" class="btn btn-info add-new-booking mbot15"><?php echo _l('add'); ?></a>
            <?php } ?>

          </div>
          <div class="row">
            <div class="col-md-3">
                <?php 
                echo render_select('status', $booking_status, array('id', 'name'), 'status');
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
          <table class="table table-fuel scroll-responsive">
           <thead>
              <tr>
                 <th><?php echo _l('booking_number'); ?></th>
                 <th><?php echo _l('subject'); ?></th>
                 <th><?php echo _l('delivery_date'); ?></th>
                 <th><?php echo _l('customer'); ?></th>
                 <th><?php echo _l('amount'); ?></th>
                 <th><?php echo _l('status'); ?></th>
                 <th><?php echo _l('invoice'); ?></th>
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
<div class="modal fade" id="booking-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('booking')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('fleet/booking'),array('id'=>'booking-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
                <?php echo render_select('userid', $clients, array('userid', 'company'), 'customer'); ?>
                <?php echo render_input('subject', 'subject'); ?>
                <div class="row">
                   <div class="col-md-6">
                     <?php echo render_date_input('delivery_date', 'delivery_date'); ?>
                   </div>
                   <div class="col-md-6">
                     <?php echo render_input('phone', 'phone') ?>
                   </div>
                </div>
                <div class="row">
                   <div class="col-md-6">
                     <?php echo render_textarea('receipt_address','receipt_address'); ?>
                   </div>
                   <div class="col-md-6">
                     <?php echo render_textarea('delivery_address','delivery_address'); ?>
                   </div>
                </div>
                <?php echo render_textarea('note','note'); ?>
                <?php echo render_input('amount', 'amount', '', 'text', $arrAtt); ?>
                <?php echo render_textarea('admin_note','admin_note'); ?>
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
<?php require 'modules/fleet/assets/js/bookings/manage_js.php'; ?>
