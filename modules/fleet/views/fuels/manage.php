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
            <?php if(is_admin() || has_permission('fleet_fuel', '', 'create')){ ?>
            <a href="#" class="btn btn-info add-new-fuel mbot15"><?php echo _l('add'); ?></a>

            <?php } ?>

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
        </div>
      </div>
    </div>
  </div>
</div>
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
         <?php echo form_open(admin_url('fleet/add_fuel'),array('id'=>'fuel-form'));?>
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
<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/fleet/assets/js/fuels/manage_js.php'; ?>
