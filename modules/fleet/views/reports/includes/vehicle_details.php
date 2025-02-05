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
          <?php foreach($vehicles as $key => $vehicle){ ?>
            <hr>
              <h4 class="bold"><a href="<?php echo site_url('fleet/vehicle/' . $vehicle['id']); ?>" class="invoice-number"><?php echo new_html_entity_decode($vehicle['name']); ?></a></h4>
               <table class="table table-striped  no-margin">
                    <tbody>
                        <tr class="project-overview">
                          <td class="bold" width="20%"><?php echo _l('type'); ?></td>
                          <td width="30%"><?php echo fleet_get_vehicle_type_name_by_id($vehicle['vehicle_type_id']) ; ?></td>
                          <td class="bold" width="20%"><?php echo _l('status'); ?></td>
                          <td width="30%"><?php echo _l($vehicle['status']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('year'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['year']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('fuel_type'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['fuel_type']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('current_meter'); ?></td>
                            <td><?php echo fleet_get_vehicle_current_meter($vehicle['id']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('current_meter_date'); ?></td>
                            <td><?php echo fleet_get_vehicle_current_meter_date($vehicle['id']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('make'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['make']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('model'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['model']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('color'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['color']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('license_plate'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['license_plate']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('vehicle_group'); ?></td>
                            <td><?php echo fleet_get_vehicle_group_name_by_id($vehicle['vehicle_group_id']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('registration_state'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['registration_state']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('current_operator'); ?></td>
                            <td><?php echo fleet_get_vehicle_current_operator($vehicle['id']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('ownership'); ?></td>
                            <td><?php echo _l($vehicle['ownership'] ?? '') ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('purchase'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('purchase_date'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['purchase_date']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('purchase_meter'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['odometer']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('purchase_price'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['purchase_price']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('purchase_comments'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['notes']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('warranty_expiration_date'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['expiration_date']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('warranty_expiration_meter'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['max_meter_value']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('lifecycle'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('in_service_date'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['in_service_date']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('in_service_odometer'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['in_service_odometer']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('estimated_service_life_in_months'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['estimated_service_life_in_months']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('estimated_service_life_in_meter'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['estimated_service_life_in_meter']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('estimated_resale_value'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['estimated_resale_value']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('out_of_service_date'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['out_of_service_date']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('out_of_service_odometer'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['out_of_service_odometer']) ; ?></td>
                            <td class="bold" width="20%"></td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('dimensions'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('width'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['width']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('height'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['height']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('length'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['length']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('interior_volume'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['interior_volume']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('passenger_volume'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['passenger_volume']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('cargo_volume'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['cargo_volume']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('ground_clearance'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['ground_clearance']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('bed_length'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['bed_length']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('weight'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('curb_weight'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['curb_weight']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('gross_vehicle_weight_rating'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['gross_vehicle_weight_rating']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('make'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['make']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('model'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['model']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('performance'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('towing_capacity'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['towing_capacity']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('max_payload'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['max_payload']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('make'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['make']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('model'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['model']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('fuel_economy'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('epa_city'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['epa_city']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('epa_highway'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['epa_highway']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('epa_combined'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['epa_combined']) ; ?></td>
                            <td></td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('engine'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('engine_summary'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['engine_summary']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('engine_brand'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['engine_brand']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('aspiration'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['aspiration']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('block_type'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['block_type']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('bore'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['bore']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('cam_type'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['cam_type']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('compression'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['compression']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('cylinders'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['cylinders']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('displacement'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['displacement']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('fuel_induction'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['fuel_induction']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('max_hp'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['max_hp']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('max_torque'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['max_torque']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('redline_rpm'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['redline_rpm']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('stroke'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['stroke']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('valves'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['valves']) ; ?></td>
                            <td></td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('transmission'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('transmission_summary'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['transmission_summary']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('transmission_brand'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['transmission_brand']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('transmission_type'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['transmission_type']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('transmission_gears'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['transmission_gears']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('wheels_tires'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('drive_type'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['drive_type']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('brake_system'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['brake_system']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('front_track_width'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['front_track_width']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('rear_track_width'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['rear_track_width']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('wheelbase'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['wheelbase']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('front_wheel_diameter'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['front_wheel_diameter']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('rear_wheel_diameter'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['rear_wheel_diameter']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('rear_axle'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['rear_axle']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('front_tire_type'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['front_tire_type']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('front_tire_psi'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['front_tire_psi']) ; ?></td>
                         </tr>
                          <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('rear_tire_type'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['rear_tire_type']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('rear_tire_psi'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['rear_tire_psi']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('fuel'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('fuel_type'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['fuel_type']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('fuel_quality'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['fuel_quality']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('fuel_tank_1_capacity'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['fuel_tank_1_capacity']) ; ?></td>
                            <td class="bold" width="20%"><?php echo _l('fuel_tank_2_capacity'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['fuel_tank_2_capacity']) ; ?></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('oil'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="20%"><?php echo _l('oil_capacity'); ?></td>
                            <td><?php echo new_html_entity_decode($vehicle['oil_capacity']) ; ?></td>
                            <td></td>
                            <td></td>
                         </tr>
                      </tbody>
                </table>
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
