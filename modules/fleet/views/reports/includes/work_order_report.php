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
          <div class="col-md-6">
          <div id="container_chart"></div>
          </div>
          <div class="col-md-6">
            
          <div id="container_task"></div>
          </div>
          </div>

          <table class="table table-email-logs mtop25">
            <thead>
                <th><?php echo _l('work_order_number'); ?></th>
                 <th><?php echo _l('vehicle'); ?></th>
                 <th><?php echo _l('vendor'); ?></th>
                 <th><?php echo _l('issue_date'); ?></th>
                 <th><?php echo _l('start_date'); ?></th>
                 <th><?php echo _l('complete_date'); ?></th>
                 <th><?php echo _l('total'); ?></th>
                 <th><?php echo _l('status'); ?></th>
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
