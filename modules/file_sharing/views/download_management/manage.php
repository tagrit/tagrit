<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <hr />
          <div class="row">
              <?php if(is_admin()){ ?>
                <div class="col-md-3">
                <?php  echo render_select('member_filter', $staffs, array('staffid', 'firstname', 'lastname'), 'staff', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
                </div>
              <?php } ?>
              <div class="col-md-3">
              <?php echo render_select('hash_share', $hash_share, array('id', 'hash_share'), 'hash_share', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
              </div>
            <div class="col-md-3">
              <?php echo render_date_input('from_date','from_date'); ?>
            </div>
            <div class="col-md-3">
              <?php echo render_date_input('to_date','to_date'); ?>
            </div>
          </div>
          <table class="table table-download-management">
            <thead>
              <th><?php echo _l('hash_share'); ?></th>
              <th><?php echo _l('name'); ?></th>
              <th><?php echo _l('size'); ?></th>
              <th><?php echo _l('fs_share_expiration_date'); ?></th>
              <th><?php echo _l('download_limits'); ?></th>
              <th><?php echo _l('ip'); ?></th>
              <th><?php echo _l('browser'); ?></th>
              <th><?php echo _l('time'); ?></th>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
</body>
</html>
