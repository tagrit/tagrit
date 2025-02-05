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
          <hr />
          <div id="container_chart"></div>

          <table class="table table-booking scroll-responsive mtop25 dataTable ">
               <thead>
                  <tr>
                    <th><?php echo _l('group'); ?></th>
                   <th><?php echo _l('submissions_count'); ?></th>
                   <th><?php echo _l('forms_count'); ?></th>
                  </tr>
               </thead>
               <tbody></tbody>
               <tfoot>
                  <?php 
                      $this->load->model('fleet/fleet_model');
                      foreach($vehicles as $vehicle){ 
                          $inspections_summary = $this->fleet_model->inspections_summary_by_vehicle($vehicle['id']);
                          ?>
                         <tr>
                            <td><?php echo new_html_entity_decode($vehicle['name']); ?></td>
                            <td><?php echo number_format($inspections_summary['submission_count']); ?></td>
                            <td><?php echo number_format($inspections_summary['forms_count']); ?></td>
                         </tr>
                      <?php } ?>
               </tfoot>
            </table>
      </div>
    </div>
  </div>
</div>
<!-- box loading -->
<div id="box-loading"></div>
<?php init_tail(); ?>
</body>
</html>
