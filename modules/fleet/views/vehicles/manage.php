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
            <?php if(is_admin() || has_permission('fleet_vehicle', '', 'create')){ ?>
              <a href="<?php echo admin_url('fleet/vehicle'); ?>" class="btn btn-info mbot15"><?php echo _l('add'); ?></a>
            <?php } ?>

          </div>
          <div class="row">
            <div class="col-md-3">
              <?php echo render_select('vehicle_type_id',$vehicle_types, array('id', 'name'),'vehicle_type'); ?>
            </div>
            <div class="col-md-3">
              <?php echo render_select('vehicle_group_id',$vehicle_groups, array('id', 'name'),'vehicle_group'); ?>
            </div>
            <div class="col-md-3">
              <?php 
                  $status = [
                     ['id' => 'active', 'name' => _l('active')],
                     ['id' => 'inactive', 'name' => _l('inactive')],
                     ['id' => 'in_shop', 'name' => _l('in_shop')],
                     ['id' => 'out_of_service', 'name' => _l('out_of_service')],
                     ['id' => 'sold', 'name' => _l('sold')],
                  ];
               ?>

              <?php echo render_select('status',$status, array('id', 'name'),'status'); ?>
            </div>
          </div>

          <table class="table table-vehicles scroll-responsive">
           <thead>
              <tr>
                 <th><?php echo _l('name'); ?></th>
                 <th><?php echo _l('year'); ?></th>
                  <th><?php echo _l('make'); ?></th>
                  <th><?php echo _l('model'); ?></th>
                 <th><?php echo _l('type'); ?></th>
                 <th><?php echo _l('group'); ?></th>
                 <th><?php echo _l('status'); ?></th>
              </tr>
           </thead>
        </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
</body>
</html>
