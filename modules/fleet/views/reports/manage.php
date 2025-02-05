<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <hr />
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse44"><h4><?php echo _l('vehicle'); ?></h4></a>
                </h4>
              </div>
              <div id="collapse44" class="panel-collapse collapse in">
                <ul class="list-group">
                  <li class="list-group-item">
                    <div class="row">
                     <div class="col-md-6">
                      <a href="<?php echo admin_url('fleet/rp_vehicle_list'); ?>"><h4 class="no-margin"><?php echo _l('vehicle_list'); ?></h4></a>
                      <p><?php echo _l('vehicle_list_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_vehicle_details'); ?>"><h4 class="no-margin"><?php echo _l('vehicle_details'); ?></h4></a>
                      <p><?php echo _l('vehicle_details_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_total_cost_trend'); ?>"><h4 class="no-margin"><?php echo _l('total_cost_trend'); ?></h4></a>
                      <p><?php echo _l('total_cost_trend_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_cost_meter_trend'); ?>"><h4 class="no-margin"><?php echo _l('cost_meter_trend'); ?></h4></a>
                      <p><?php echo _l('cost_meter_trend_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_expenses_by_vehicle'); ?>"><h4 class="no-margin"><?php echo _l('expenses_by_vehicle'); ?></h4></a>
                      <p><?php echo _l('expenses_by_vehicle_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_expense_summary'); ?>"><h4 class="no-margin"><?php echo _l('expense_summary'); ?></h4></a>
                      <p><?php echo _l('expense_summary_note'); ?></p>
                    </div>
                     <div class="col-md-6">
                       <a href="<?php echo admin_url('fleet/rp_operating_cost_summary'); ?>"><h4 class="no-margin"><?php echo _l('operating_cost_summary'); ?></h4></a>
                      <p><?php echo _l('operating_cost_summary_note'); ?></p>
                      
                      <a href="<?php echo admin_url('fleet/rp_utilization_summary'); ?>"><h4 class="no-margin"><?php echo _l('utilization_summary'); ?></h4></a>
                      <p><?php echo _l('utilization_summary_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_vehicle_renewal_reminders'); ?>"><h4 class="no-margin"><?php echo _l('vehicle_renewal_reminders'); ?></h4></a>
                      <p><?php echo _l('vehicle_renewal_reminders_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_status_changes'); ?>"><h4 class="no-margin"><?php echo _l('status_changes'); ?></h4></a>
                      <p><?php echo _l('status_changes_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_group_changes'); ?>"><h4 class="no-margin"><?php echo _l('group_changes'); ?></h4></a>
                      <p><?php echo _l('group_changes_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_status_summary'); ?>"><h4 class="no-margin"><?php echo _l('status_summary'); ?></h4></a>
                      <p><?php echo _l('status_summary_note'); ?></p>
                    </div>

                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse45"><h4><?php echo _l('vehicle_assignments'); ?></h4></a>
                </h4>
              </div>
              <div id="collapse45" class="panel-collapse collapse in">
                <ul class="list-group">
                  <li class="list-group-item">
                    <div class="row">
                     <div class="col-md-6">
                       <a href="<?php echo admin_url('fleet/rp_vehicle_assignment_log'); ?>"><h4 class="no-margin"><?php echo _l('vehicle_assignment_log'); ?></h4></a>
                      <p><?php echo _l('vehicle_assignment_log_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_vehicle_assignment_summary'); ?>"><h4 class="no-margin"><?php echo _l('vehicle_assignment_summary'); ?></h4></a>
                      <p><?php echo _l('vehicle_assignment_summary_note'); ?></p>
                    </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse45"><h4><?php echo _l('inspections'); ?></h4></a>
                </h4>
              </div>
              <div id="collapse45" class="panel-collapse collapse in">
                <ul class="list-group">
                  <li class="list-group-item">
                    <div class="row">
                     <div class="col-md-6">
                       <a href="<?php echo admin_url('fleet/rp_inspection_submissions_list'); ?>"><h4 class="no-margin"><?php echo _l('inspection_submissions_list'); ?></h4></a>
                      <p><?php echo _l('inspection_submissions_list_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_inspection_submissions_summary'); ?>"><h4 class="no-margin"><?php echo _l('inspection_submissions_summary'); ?></h4></a>
                      <p><?php echo _l('inspection_submissions_summary_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_inspection_failures_list'); ?>"><h4 class="no-margin"><?php echo _l('inspection_failures_list'); ?></h4></a>
                      <p><?php echo _l('inspection_failures_list_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_inspection_schedules'); ?>"><h4 class="no-margin"><?php echo _l('inspection_schedules'); ?></h4></a>
                      <p><?php echo _l('inspection_schedules_note'); ?></p>
                    </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse45"><h4><?php echo _l('fuel'); ?></h4></a>
                </h4>
              </div>
              <div id="collapse45" class="panel-collapse collapse in">
                <ul class="list-group">
                  <li class="list-group-item">
                    <div class="row">
                     <div class="col-md-6">
                       <a href="<?php echo admin_url('fleet/rp_fuel_summary'); ?>"><h4 class="no-margin"><?php echo _l('fuel_summary'); ?></h4></a>
                      <p><?php echo _l('fuel_summary_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_fuel_entries_by_vehicle'); ?>"><h4 class="no-margin"><?php echo _l('fuel_entries_by_vehicle'); ?></h4></a>
                      <p><?php echo _l('fuel_entries_by_vehicle_note'); ?></p>
                      <a href="<?php echo admin_url('fleet/rp_fuel_summary_by_location'); ?>"><h4 class="no-margin"><?php echo _l('fuel_summary_by_location'); ?></h4></a>
                      <p><?php echo _l('fuel_summary_by_location_note'); ?></p>
                    </div>


                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <ul class="list-group hide">
            <li class="list-group-item no-border">
              <div class="row">
               <div class="col-md-6">
                  <a href="<?php echo admin_url('fleet/fuel_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('fuel_report'); ?></h4></a>
                  <a href="<?php echo admin_url('fleet/maintenance_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('maintenance_report'); ?></h4></a>
                  <a href="<?php echo admin_url('fleet/event_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('event_report'); ?></h4></a>
              </div>
              <div class="col-md-6">
                  <a href="<?php echo admin_url('fleet/work_order_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('work_order_report'); ?></h4></a>
                  <a href="<?php echo admin_url('fleet/income_and_expense_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('income_and_expense_report'); ?></h4></a>
                  <a href="<?php echo admin_url('fleet/work_performance_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('work_performance_report'); ?></h4></a>
              </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
</body>
</html>
