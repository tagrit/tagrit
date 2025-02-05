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
                  <?php 
                      $this->load->model('fleet/fleet_model');
                      foreach($vehicles as $vehicle){ 
                          ?>
                          <div class="row">
                            <div class="col-md-6">
                              <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
                                  <?php echo _l('vehicle'); ?>:
                                  <span class="tw-font-medium tw-text-neutral-700"><a href="<?php echo site_url('fleet/vehicle/' . $vehicle['id']); ?>" class="invoice-number">
                                      <?php echo new_html_entity_decode($vehicle['name']); ?>
                                    </a>
                                  </span>
                              </p>
                            </div>
                            <div class="col-md-6">
                              <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
                                  <?php echo _l('group'); ?>:
                                  <span class="tw-font-medium tw-text-neutral-700">
                                      <?php echo fleet_get_vehicle_group_name_by_id($vehicle['vehicle_group_id']); ?>
                                  </span>
                              </p>
                            </div>
                            <div class="col-md-6">
                              <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
                                  <?php echo _l('status'); ?>:
                                  <span class="tw-font-medium tw-text-neutral-700">
                                      <?php echo _l($vehicle['status']); ?>
                                  </span>
                              </p>
                            </div>
                            <div class="col-md-6">
                              <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
                                  <?php echo _l('fleet_body_type'); ?>:
                                  <span class="tw-font-medium tw-text-neutral-700">
                                      <?php echo _l($vehicle['body_type'] ?? ''); ?>
                                  </span>
                              </p>
                            </div>
                          </div>
                         <table class="table table-booking scroll-responsive mtop25 dataTable mtop15">
                           <thead>
                              <tr>
                                <th><?php echo _l('fuel_time'); ?></th>
                                <th><?php echo _l('odometer'); ?></th>
                                <th><?php echo _l('gallons'); ?></th>
                                <th><?php echo _l('price'); ?></th>
                              </tr>
                           </thead>
                           <tbody>
                        
                        <?php 
                          $fuel_history = $this->fleet_model->get_fuel_history('', 'vehicle_id = '.$vehicle['id']);
                        foreach($fuel_history as $history){ ?>
                          <tr>
                            <td><?php echo _d($history['fuel_time']); ?></td>
                            <td><?php echo new_html_entity_decode($history['odometer']); ?></td>
                            <td><?php echo new_html_entity_decode($history['gallons']); ?></td>
                            <td><?php echo app_format_money($history['price'], $currency->name); ?></td>
                         </tr>
                        <?php } ?>
                        </tbody>
                      </table>
                          <hr>

                      <?php } ?>
      </div>
    </div>
  </div>
</div>
<!-- box loading -->
<div id="box-loading"></div>
<?php init_tail(); ?>
</body>
</html>
