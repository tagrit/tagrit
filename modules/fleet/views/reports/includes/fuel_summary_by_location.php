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
          <div id="container_chart"></div>

          <table class="table table-booking scroll-responsive mtop25 dataTable ">
               <thead>
                  <tr>
                    <th><?php echo _l('fuel_vendor_location'); ?></th>
                    <th><?php echo _l('transactions'); ?></th>
                   <th><?php echo _l('gallons'); ?></th>
                   <th><?php echo _l('cost'); ?></th>
                  </tr>
               </thead>
               <tbody></tbody>
               <tfoot>
                  <?php 
                      foreach($fuel_summary_by_location as $key => $fuel_summary){ 
                          ?>
                         <tr>
                            <td><?php echo new_html_entity_decode($key); ?></td>
                            <td><?php echo number_format($fuel_summary['transactions']); ?></td>
                            <td><?php echo number_format($fuel_summary['gallons']); ?></td>
                            <td><?php echo app_format_money($fuel_summary['cost'], $currency->name); ?></td>
                         </tr>
                      <?php } ?>
               </tfoot>
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
