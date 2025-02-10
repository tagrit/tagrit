<div role="tabpanel" class="tab-pane" id="lead_campaigns">
    <div class="col-lg-6 col-xs-12 col-md-12 total-column">
      <div class="panel_s">
         <div class="panel-body">
            <h3 class="text-muted _total">
               <?php echo ma_lead_total_point($lead->id); ?>
            </h3>
            <span class="text-success"><?php echo _l('point'); ?></span>
         </div>
      </div>
   </div>
   <div class="col-lg-6 col-xs-12 col-md-12 total-column">
      <div class="panel_s">
         <div class="panel-body">
            <h3 class="text-muted _total">
               <?php echo count($campaigns); ?>
            </h3>
            <span class="text-warning"><?php echo _l('total_number_of_campaigns'); ?></span>
         </div>
      </div>
   </div>

   <table class="table items">
        <thead>
          <tr class="project-overview">
              <th class="text-center bold"><?php echo _l('campaign'); ?></th>
              <th class="text-center bold"><?php echo _l('change_points'); ?></th>
           </tr>
        </thead>
        <tbody>
        <?php foreach ($campaigns as $value) { 
            $campaign_point = ma_lead_total_point_by_campaign($lead->id, $value['campaign_id']);
            ?>
            <tr class="project-overview">
              <td class="" width="30%">
                <a href="<?php echo admin_url('ma/campaign_detail/'.$value['campaign_id']); ?>">
                        <?php echo ma_get_campaign_name($value['campaign_id']); ?>
                     </a>
                </td>
              <?php $point = $campaign_point; ?>
              <?php $text_class = (($campaign_point >= 0) ? 'text-success' : 'text-danger'); ?>
              <td class="text-center <?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($point) ; ?></td>
           </tr>
        <?php } ?>
    </table>
</div>