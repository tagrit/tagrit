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
               <th><?php echo _l('change'); ?></th>
               <th><?php echo _l('active'); ?></th>
               <th><?php echo _l('inactive'); ?></th>
               <th><?php echo _l('in_shop'); ?></th>
               <th><?php echo _l('out_of_service'); ?></th>
               <th><?php echo _l('sold'); ?></th>
            </thead>
            <tbody>
              <?php 
                      $this->load->model('fleet/fleet_model');
                      foreach($vehicles as $vehicle){ 
                          $status_summary = $this->fleet_model->status_summary_by_vehicle($vehicle['id']);
                          ?>
                         <tr>
                            <td><?php echo new_html_entity_decode($vehicle['name']); ?></td>
                            <td><?php echo new_html_entity_decode($status_summary['total_change']); ?></td>
                            <td><?php echo new_html_entity_decode($status_summary['active']); ?></td>
                            <td><?php echo new_html_entity_decode($status_summary['inactive']); ?></td>
                            <td><?php echo new_html_entity_decode($status_summary['in_shop']); ?></td>
                            <td><?php echo new_html_entity_decode($status_summary['out_of_service']); ?></td>
                            <td><?php echo new_html_entity_decode($status_summary['sold']); ?></td>
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
