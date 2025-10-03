<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <a href="<?php echo admin_url('ma/reports'); ?>"><?php echo _l('back_to_report_list'); ?></a>
          <hr />
          <div id="container_chart"></div>
          <?php echo form_hidden('timezone', date_default_timezone_get()); ?>

          <table class="table table-point-action mtop25">
            <thead>
              <th><?php echo _l('point_action'); ?></th>
              <th><?php echo _l('contact_type'); ?></th>
              <th><?php echo _l('contact_name'); ?></th>
              <th><?php echo _l('email'); ?></th>
              <th><?php echo _l('change_points'); ?></th>
              <th><?php echo _l('time'); ?></th>
            </thead>
            <tbody>
            </tbody>
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
<?php require('modules/ma/assets/js/reports/lead_and_point_report_js.php'); ?>
