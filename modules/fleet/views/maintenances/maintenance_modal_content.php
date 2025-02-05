       <input type="hidden" name="id">
       <div class="row">
        <div class="col-md-12">
          <?php if(isset($vehicle)){
            echo render_select('vehicle_id', $vehicles, array('id', 'name'), 'vehicle', $vehicle->id);
          }else{
            echo render_select('vehicle_id', $vehicles, array('id', 'name'), 'vehicle');
          } ?>
        </div>
      </div>
      
       <?php echo render_select('garage_id', $garages, array('id', 'name'), 'garage'); ?>

      <div class="row">
        <div class="col-md-12">
          <?php
          $maintenances = [
            ['id' => 'maintenance', 'maintenance_name' => _l('fe_maintenance')],
            ['id' => 'repair', 'maintenance_name' => _l('fe_repair')],
          ];
          echo render_select('maintenance_type', $maintenances, array('id', 'maintenance_name'), 'maintenance_type'); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php echo render_input('title', 'maintenance_service_name'); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <?php echo render_date_input('start_date', 'start_date'); ?>
        </div>
        <div class="col-md-6">
          <?php echo render_date_input('completion_date', 'completion_date'); ?>
        </div>
      </div>
       <?php echo render_select('parts[]', $parts, array('id', 'name'), 'parts','', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
     
      <div class="row">
        <div class="col-md-12">
         <div class="form-group">
          <label for="gst"><?php echo _l('fe_cost'); ?></label>            
          <div class="input-group">
            <span class="input-group-addon"><?php echo new_html_entity_decode($currency_name); ?></span>
            <input data-type="currency" class="form-control" name="cost" value="">
          </div>
        </div>
      </div>
    </div>
  <div class="row">
    <div class="col-md-12">
      <?php echo render_textarea('notes','fe_notes') ?>
    </div>
  </div>