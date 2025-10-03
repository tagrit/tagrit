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
          <table class="table table-booking scroll-responsive mtop25 dataTable ">
               <thead>
                  <tr>
                    <th><?php echo _l('group'); ?></th>
                   <th><?php echo _l('transactions'); ?></th>
                   <th><?php echo _l('vehicles'); ?></th>
                   <th><?php echo _l('total_cost'); ?></th>
                  </tr>
               </thead>
               <tbody></tbody>
               <tfoot>
                  <?php 
                      $this->load->model('fleet/fleet_model');
                      foreach($vehicle_groups as $group){ 
                          $expense_summary = $this->fleet_model->expense_summary_by_vehicle_group($group['id']);
                          ?>
                         <tr>
                            <td><?php echo new_html_entity_decode($group['name']); ?></td>
                            <td><?php echo number_format($expense_summary['total_transaction']); ?></td>
                            <td><?php echo number_format($expense_summary['total_vehicle']); ?></td>
                            <td><?php echo app_format_money($expense_summary['total_cost'], $currency->name); ?></td>
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
