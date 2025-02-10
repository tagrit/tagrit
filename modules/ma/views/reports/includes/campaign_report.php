<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <a href="<?php echo admin_url('ma/reports'); ?>"><?php echo _l('back_to_report_list'); ?></a>
          <?php echo form_hidden('timezone', date_default_timezone_get()); ?>
          <hr />
          <div class="row">
            <div class="col-md-6">
              <div class="panel_s">
                <div class="panel-body">
                  <div id="container_email"></div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="panel_s">
                <div class="panel-body">
                  <div id="container_email_total"></div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="panel_s">
                <div class="panel-body">
                  <div id="container_text_message"></div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="panel_s">
                <div class="panel-body">
                  <div id="container_point_action"></div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
<!-- box loading -->
<div id="box-loading"></div>
<?php init_tail(); ?>
</body>
</html>
<?php require('modules/ma/assets/js/reports/campaign_report_js.php'); ?>
