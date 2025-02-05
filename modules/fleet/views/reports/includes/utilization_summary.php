<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <a href="<?php echo admin_url('fleet/reports'); ?>"><?php echo _l('back_to_report_list'); ?></a>
          <?php echo form_hidden('timezone', date_default_timezone_get()); ?>
          <?php echo form_hidden('is_report', 1); ?>
          <?php echo form_hidden('type', 'status_change'); ?>
          <hr />
          <div id="container_chart"></div>

          <table class="table table-email-logs mtop25">
            <thead>
                <th><?php echo _l('vehicle'); ?></th>
                <th><?php echo _l('group'); ?></th>
               <th><?php echo _l('min_value'); ?></th>
               <th><?php echo _l('min_date'); ?></th>
               <th><?php echo _l('max_value'); ?></th>
               <th><?php echo _l('max_date'); ?></th>
               <th><?php echo _l('usage'); ?></th>
               <th><?php echo _l('avg_day'); ?></th>
            </thead>
            <tbody>
              <?php 
                      $this->load->model('fleet/fleet_model');
                      foreach($vehicles as $vehicle){ 
                          $utilization_summary = $this->fleet_model->utilization_summary_by_vehicle($vehicle['id']);
                          ?>
                         <tr>
                            <td><a href="<?php echo site_url('fleet/vehicle/' . $vehicle['id']); ?>" class="invoice-number"><?php echo new_html_entity_decode($vehicle['name']); ?></a></td>
                            <td><?php echo fleet_get_vehicle_group_name_by_id($vehicle['vehicle_group_id']); ?></td>
                            <td><?php echo number_format($utilization_summary['min_value']); ?></td>
                            <td><?php echo _d($utilization_summary['min_date']); ?></td>
                            <td><?php echo number_format($utilization_summary['max_value'] ?? ''); ?></td>
                            <td><?php echo _d($utilization_summary['max_date']); ?></td>
                            <td><?php echo number_format($utilization_summary['usage'] ?? ''); ?></td>
                            <td><?php echo number_format($utilization_summary['avg_day'], 2); ?></td>
                         </tr>
                      <?php } ?>
            </tbody>
          </table>
      </div>
    </div>
  </div>
</div>
</div>
<!-- box loading -->
<div id="box-loading"></div>
<?php init_tail(); ?>
</body>
</html>
