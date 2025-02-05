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
            <?php echo form_open(admin_url('fleet/view_report'),array('id'=>'filter-form')); ?>
              <div class="col-md-5">
                <?php echo render_date_input('from_date','from_date', _d($from_date)); ?>
              </div>
              <div class="col-md-5">
                <?php echo render_date_input('to_date','to_date', _d($to_date)); ?>
              </div>
              <div class="col-md-2">
                <?php echo form_hidden('type', 'cost_meter_trend'); ?>
                <button type="submit" class="btn btn-info btn-submit mtop25"><?php echo _l('filter'); ?></button>
              </div>
            <?php echo form_close(); ?>
          </div>

          <div id="container_chart"></div>

          <div id="DivIdToPrint"></div>
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
