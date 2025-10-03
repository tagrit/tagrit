<table class="table table-email-logs mtop25">
    <thead>
        <th><?php echo _l('vehicle'); ?></th>
       <th><?php echo _l('Assignments'); ?></th>
       <th><?php echo _l('Operators'); ?></th>
       <th><?php echo _l('Assigned'); ?></th>
    </thead>
    <tbody>
      <?php 
        $this->load->model('fleet/fleet_model');
        foreach($vehicles as $vehicle){ 
            $assignment_summary = $this->fleet_model->assignment_summary_by_vehicle($vehicle['id'], $from_date, $to_date);
            ?>
           <tr>
              <td><a href="<?php echo site_url('fleet/vehicle/' . $vehicle['id']); ?>" class="invoice-number"><?php echo new_html_entity_decode($vehicle['name']); ?></a></td>
              <td><?php echo new_html_entity_decode($assignment_summary['assignments']); ?></td>
              <td><?php echo new_html_entity_decode($assignment_summary['operators']); ?></td>
              <td><?php echo new_html_entity_decode($assignment_summary['assigned']); ?>%</td>
           </tr>
        <?php } ?>
    </tbody>
</table>