<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">               
                            <h4 class="h4-color"><?php echo _l('general_infor'); ?></h4>
                            <hr class="hr-color">
                            <div class="panel-padding">
                              <table class="table border table-striped table-margintop">
                                  <tbody>
                                     <tr class="project-overview">
                                        <td class="bold"  width="30%"><?php echo _l('maintenance_service_name'); ?></td>
                                        <td><?php echo new_html_entity_decode($maintenance->title) ; ?></td>
                                     </tr>
                                    <tr class="project-overview">
                                        <td class="bold"><?php echo _l('vehicle'); ?></td>
                                        <td><?php echo new_html_entity_decode($maintenance->vehicle->name) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('garage'); ?></td>
                                        <td><?php echo new_html_entity_decode($maintenance->garage->name) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('maintenance_type'); ?></td>
                                        <td><?php echo _l($maintenance->maintenance_type) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('start_date'); ?></td>
                                        <td><?php echo _d($maintenance->start_date) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('completion_date'); ?></td>
                                        <td><?php echo _d($maintenance->completion_date) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('cost'); ?></td>
                                        <td><?php echo app_format_money($maintenance->cost, $currency->name) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('note'); ?></td>
                                        <td><?php echo new_html_entity_decode($maintenance->notes) ; ?></td>
                                     </tr>
                                    </tbody>
                              </table>
                            </div>
                            <h4 class="h4-color mtop25"><?php echo _l('parts'); ?></h4>
                        <hr class="hr-color">
                        <table class="table table-booking scroll-responsive">
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
                             <tbody></tbody>
                             <tfoot>
                                <?php 
                                if($maintenance->parts != '' && $maintenance->parts != null){
                                    $parts = explode(',', $maintenance->parts);
                                    $this->load->model('fleet/fleet_model');
                                    foreach($parts as $part){ 
                                        $part = $this->fleet_model->get_part($part);
                                        if(!$part){
                                            continue;
                                        }
                                        ?>
                                       <tr>
                                          <td><a href="<?php echo site_url('fleet/part/' . $part->id); ?>" class="invoice-number"><?php echo new_html_entity_decode($part->name); ?></a></td>
                                          <td><?php echo fleet_get_part_type_name_by_id($part->part_type_id); ?></td>
                                          <td><?php echo new_html_entity_decode($part->brand); ?></td>
                                          <td><?php echo new_html_entity_decode($part->model); ?></td>
                                          <td><?php echo new_html_entity_decode($part->serial_number); ?></td>
                                          <td><?php echo fleet_get_part_group_name_by_id($part->part_group_id); ?></td>
                                          <td><?php echo _l($part->status); ?></td>
                                          <td><?php echo get_staff_full_name($part->driver_id); ?></td>
                                          <td><?php echo fleet_get_vehicle_name_by_id($part->vehicle_id); ?></td>
                                       </tr>
                                    <?php } ?>
                                <?php } ?>
                             </tfoot>
                          </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
</body>
</html>

