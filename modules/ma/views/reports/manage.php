<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <hr />
          <ul class="list-group">
            <li class="list-group-item no-border">
              <div class="row">
               <div class="col-md-6">
                  <a href="<?php echo admin_url('ma/campaign_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('campaign_report'); ?></h4></a>
                  <a href="<?php echo admin_url('ma/email_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('email_report'); ?></h4></a>
                  <a href="<?php echo admin_url('ma/sms_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('sms_report'); ?></h4></a>
              </div>
              <div class="col-md-6">
                  <a href="<?php echo admin_url('ma/asset_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('asset_report'); ?></h4></a>
                  <a href="<?php echo admin_url('ma/lead_and_point_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('contact_and_point_report'); ?></h4></a>
                  <a href="<?php echo admin_url('ma/form_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('form_report'); ?></h4></a>
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
