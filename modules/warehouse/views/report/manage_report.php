<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="panel_s">
      <div class="panel-body">
        <div class="horizontal-scrollable-tabs">
          <nav>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <?php
              $i = 0;
              foreach($tab as $val){
                ?>
                <li<?php if($i == 0){echo " class='active'"; } ?>>
                <a href="<?php echo admin_url('warehouse/manage_report?group='.$val); ?>" data-group="<?php echo new_html_entity_decode($val); ?>">
                  <?php echo _l($val); ?></a>
                </li>
                <?php $i++; } ?>
            </ul>
          </nav>
        </div>
        </div>
      </div>
      <div class="panel_s">
        <div class="panel-body">
          <?php $this->load->view($tabs['view']); ?>
        </div>
      </div>
  </div>
</div>
 <?php echo form_hidden('check_csrf_protection', check_csrf_protection()); ?>

<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/inventory_inside_js.php';?>
<?php require 'modules/warehouse/assets/js/warranty_period_report_js.php';?>
</body>
</html>
