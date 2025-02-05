<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="panel_s">
        <?php $arrAtt = array();
      $arrAtt['data-type']='currency';
?>
         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'work-order-form')) ;?>
         <div class="panel-body">
            <div class="clearfix"></div>
            <h4 class="no-margin"><?php echo new_html_entity_decode($title); ?></h4>
            <hr class="hr-panel-heading" />
            <div class="btn-bottom-toolbar text-right">
               <a href="<?php echo admin_url('fleet/work_orders'); ?>" class="btn btn-default"><?php echo _l('back'); ?></a>
               <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <div class="row">
            <div class="col-md-6">
               <?php $value = (isset($work_order) ? $work_order->vehicle_id : ''); ?>
                <?php echo render_select('vehicle_id', $vehicles, array('id', 'name'), 'vehicle', $value); ?>

               <?php $value = (isset($work_order) ? $work_order->issue_date : ''); ?>
               <?php echo render_date_input('issue_date', 'issue_date', $value); ?>

               <?php $value = (isset($work_order) ? $work_order->start_date : ''); ?>
               <?php echo render_date_input('start_date', 'start_date', $value); ?>

               <?php $value = (isset($work_order) ? $work_order->complete_date : ''); ?>
               <?php echo render_date_input('complete_date', 'complete_date', $value); ?>

               <?php 
                  $statuss = [
                     ['id' => 'open', 'name' => _l('open')],
                     ['id' => 'in_progress', 'name' => _l('in_progress')],
                     ['id' => 'parts_ordered', 'name' => _l('parts_ordered')],
                     ['id' => 'complete', 'name' => _l('complete')],
                  ];
               ?>

               <?php $value = (isset($work_order) ? $work_order->status : ''); ?>
               <?php echo render_select('status',$statuss, array('id', 'name'),'status',$value); ?>

               <?php $value = (isset($work_order) ? explode(',',$work_order->parts) : ''); ?>
               <?php echo render_select('parts[]', $parts, array('id', 'name'), 'parts',$value, array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
            </div>
            <div class="col-md-6">
              <?php $value = (isset($work_order) ? $work_order->vendor_id : ''); ?>
                <?php echo render_select('vendor_id', $vendors, array('userid', 'company'), 'vendor', $value); ?>

                <?php $value = (isset($work_order) ? $work_order->total : ''); ?>
                <?php echo render_input('total', 'work_order_price', $value, 'text', $arrAtt); ?>
                <div class="row">
                  <div class="col-md-6">
                    <?php $value = (isset($work_order) ? $work_order->odometer_in : ''); ?>
                    <?php echo render_input('odometer_in', 'odometer_in', $value, 'text', $arrAtt); ?>
                  </div>
                  <div class="col-md-6">
                    <?php $value = (isset($work_order) ? $work_order->odometer_out : ''); ?>
                    <?php echo render_input('odometer_out', 'odometer_out', $value, 'text', $arrAtt); ?>
                  </div>
                </div>
                <?php $work_requested = (isset($work_order) ? $work_order->work_requested : ''); ?>
               <?php echo render_textarea('work_requested', 'work_requested', $work_requested); ?>
            </div>
         </div>
         </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>
<?php init_tail(); ?>

</body>
</html>
