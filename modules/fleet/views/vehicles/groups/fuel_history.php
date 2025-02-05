<h4 class="customer-profile-group-heading"><?php echo _l('fuel_history'); ?></h4>
<div class="row">
   <div class="quick-stats-leads mbot10 col-xs-12 col-md-6 col-lg-4 col-sm-6 tw-mb-2 sm:tw-mb-0">
      <div class="top_stats_wrapper">
          <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
              <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center">
                  <i class="fa-solid fa-gas-pump"> </i>&nbsp;<?php echo _l('total_gallons'); ?>
              </div>
              <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0">
                  <?php echo number_format($fuel_consumption['total_gallons']); ?>
              </span>
          </div>

          <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">
              <div class="progress-bar progress-bar-mini no-percent-text not-dynamic" role="progressbar"
                  aria-valuenow="100" aria-valuemin="0"
                  aria-valuemax="100"
                  data-percent="100">
              </div>
          </div>
      </div>
   </div>
   <div class="quick-stats-leads mbot10 col-xs-12 col-md-6 col-lg-4 col-sm-6 tw-mb-2 sm:tw-mb-0">
      <div class="top_stats_wrapper">
          <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
              <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center">
                  <i class="fa-solid fa-truck-bolt"> </i>&nbsp;<?php echo _l('total_km'); ?>
              </div>
              <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0">
                  <?php echo number_format($fuel_consumption['total_km']); ?>
              </span>
          </div>

          <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">
              <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar"
                  aria-valuenow="100" aria-valuemin="0"
                  aria-valuemax="100"
                  data-percent="100">
              </div>
          </div>
      </div>
   </div>
   <div class="quick-stats-leads mbot10 col-xs-12 col-md-6 col-lg-4 col-sm-6 tw-mb-2 sm:tw-mb-0">
      <div class="top_stats_wrapper">
          <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
              <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center">
                  <i class="fa-solid fa-temperature-full"> </i>&nbsp;<?php echo _l('consumption').' ('._l('100_km').')'; ?>
              </div>
              <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0">
                  <?php echo number_format($fuel_consumption['consumption_100km'], 4); ?>
              </span>
          </div>

          <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">
              <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar"
                  aria-valuenow="100" aria-valuemin="0"
                  aria-valuemax="100"
                  data-percent="100">
              </div>
          </div>
      </div>
   </div>
</div>
<hr>
  <div>
    <a href="#" class="btn btn-info add-new-fuel mbot15"><?php echo _l('add'); ?></a>
  </div>
  <div class="row">
    <div class="col-md-3">
        <?php 
        $fuel_type = [
          ['id' => 'compressed_natural_gas', 'name' => _l('compressed_natural_gas')],
          ['id' => 'diesel', 'name' => _l('diesel')],
          ['id' => 'gasoline', 'name' => _l('gasoline')],
          ['id' => 'propane', 'name' => _l('propane')],
        ];
        echo render_select('_fuel_type', $fuel_type, array('id', 'name'), 'fuel_type');
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
         <th><?php echo _l('vehicle'); ?></th>
         <th><?php echo _l('date'); ?></th>
         <th><?php echo _l('vendor'); ?></th>
         <th><?php echo _l('odometer'); ?></th>
         <th><?php echo _l('gallons'); ?></th>
         <th><?php echo _l('price'); ?></th>
      </tr>
   </thead>
</table>

<?php $arrAtt = array();
      $arrAtt['data-type']='currency';
?>
<div class="modal fade" id="fuel-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('fuel')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('fleet/add_fuel'),array('id'=>'fuel-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
                <?php echo render_select('vehicle_id',$vehicles,array('id','name'),'vehicle'); ?>
                <?php echo render_datetime_input('fuel_time','fuel_time'); ?>
                <?php echo render_input('odometer', 'odometer', '', 'number') ?>
                <?php echo render_input('gallons', 'gallons') ?>
                <?php echo render_input('price', 'price', '', 'text', $arrAtt) ?>
                <?php echo render_select('fuel_type', $fuel_type, array('id', 'name'), 'fuel_type'); ?>
                <?php echo render_select('vendor_id', $vendors, array('userid', 'company'), 'vendor'); ?>
                <?php echo render_input('reference', 'reference') ?>
                <?php echo render_textarea('notes','notes') ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>
<div class="modal fade bulk_actions" id="fuel_bulk_actions" tabindex="-1" role="dialog" data-table=".table-fuel">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
         </div>
         <div class="modal-body">
            <?php if(has_permission('fleet_fuel_history','','detele')){ ?>
               <div class="checkbox checkbox-danger">
                  <input type="checkbox" name="mass_delete" id="mass_delete">
                  <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
               </div>
            <?php } ?>
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
         <a href="#" class="btn btn-info" onclick="bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
      </div>
   </div>
   <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal -->