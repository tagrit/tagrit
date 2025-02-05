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
          <div class="row">
             <div class="quick-stats-leads mbot10 col-xs-12 col-md-6 col-lg-4 col-sm-6 tw-mb-2 sm:tw-mb-0">
                <div class="top_stats_wrapper">
                    <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                        <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center">
                            <i class="fa-solid fa-gas-pump"> </i>&nbsp;<?php echo _l('total_cost'); ?>
                        </div>
                        <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0">
                            <?php echo number_format($operating_cost_summary['total_cost'] ?? '0'); ?>
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
                            <i class="fa-solid fa-truck-bolt"> </i>&nbsp;<?php echo _l('fuel_costs'); ?>
                        </div>
                        <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0">
                            <?php echo number_format($operating_cost_summary['fuel_costs'] ?? 0); ?>
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
                            <i class="fa-solid fa-temperature-full"> </i>&nbsp;<?php echo _l('work_order_costs'); ?>
                        </div>
                        <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0">
                            <?php echo number_format($operating_cost_summary['work_order_costs'] ?? '0'); ?>
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

          <div class="row mtop25">
            <div class="col-md-6">
              <div id="container_chart"></div>
            </div>
            <div class="col-md-6">
              <div id="container_task"></div>
            </div>
          </div>
          <hr>
          <table class="table table-booking scroll-responsive mtop25 dataTable ">
               <thead>
                  <tr>
                    <th><?php echo _l('vehicle'); ?></th>
                   <th><?php echo _l('fuel_costs'); ?></th>
                   <th><?php echo _l('work_order_costs'); ?></th>
                   <th><?php echo _l('total_cost'); ?></th>
                  </tr>
               </thead>
               <tbody></tbody>
               <tfoot>
                  <?php 
                      $this->load->model('fleet/fleet_model');
                      foreach($vehicles as $vehicle){ 
                          $operating_cost_summary = $this->fleet_model->vehicle_operating_cost_summary($vehicle['id']);
                          ?>
                         <tr>
                            <td><a href="<?php echo site_url('fleet/vehicle/' . $vehicle['id']); ?>" class="invoice-number"><?php echo new_html_entity_decode($vehicle['name']); ?></a></td>
                            <td><?php echo app_format_money($operating_cost_summary['fuel_costs'], $currency->name); ?></td>
                            <td><?php echo app_format_money($operating_cost_summary['work_order_costs'], $currency->name); ?></td>
                            <td><?php echo app_format_money($operating_cost_summary['total_cost'], $currency->name); ?></td>
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
