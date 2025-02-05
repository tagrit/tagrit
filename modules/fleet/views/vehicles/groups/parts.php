<h4 class="customer-profile-group-heading"><?php echo _l('parts'); ?></h4>
    <div class="row">
      <div class="col-md-12">

      <div class="row">
        <div class="col-md-3">
          <?php echo render_select('type',$part_types, array('id', 'name'),'part_type'); ?>
        </div>
        <div class="col-md-3">
          <?php echo render_select('group',$part_groups, array('id', 'name'),'part_group'); ?>
        </div>
        <div class="col-md-3">
          <?php 
              $status = [
                 ['id' => 'in_service', 'name' => _l('in_service')],
                 ['id' => 'out_of_service', 'name' => _l('out_of_service')],
                 ['id' => 'disposed', 'name' => _l('disposed')],
                 ['id' => 'missing', 'name' => _l('missing')],
              ];
           ?>
          <?php echo render_select('status', $status, array('id', 'name'), 'status'); ?>
        </div>
      </div>
      <hr>
      <table class="table table-parts scroll-responsive">
           <thead>
              <tr>
                 <th><?php echo _l('part_name'); ?></th>
                 <th><?php echo _l('type'); ?></th>
                 <th><?php echo _l('brand'); ?></th>
                 <th><?php echo _l('model'); ?></th>
                 <th><?php echo _l('serial_number'); ?></th>
                 <th><?php echo _l('group'); ?></th>
                 <th><?php echo _l('status'); ?></th>
                 <th><?php echo _l('current_assignee'); ?></th>
                 <th><?php echo _l('linked_vehicle'); ?></th>
              </tr>
           </thead>
        </table>

  </div>
</div>
