<?php init_head();?>
<div id="wrapper">
  <div class="content">
      <div class="panel_s">
        <div class="panel-body backdrop">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <hr />
          <div class="row">
            <div class="col-md-3">
              <?php echo render_select('ft_account',$accounts,array('id','name', 'account_type_name'),'acc_account', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
            </div>
            <div class="col-md-3">
              <?php echo render_select('ft_parent_account',$accounts,array('id','name', 'account_type_name'),'sub_account', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
            </div>
            <div class="col-md-3 ">
              <?php echo render_select('ft_type',$account_types,array('id','name'),'type', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
            </div>
            <div class="col-md-3 ">
              <?php echo render_select('ft_detail_type',$detail_types,array('id','name'),'detail_type', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
            </div>
            <div class="col-md-3 ">
              <?php echo render_date_input('from_date',_l('from_date'),''); ?>
            </div>
            <div class="col-md-3 ">
              <?php echo render_date_input('to_date',_l('to_date'),''); ?>
            </div>
            <div class="col-md-3 hide">
              <?php $active = [ 
                    1 => ['id' => 'all', 'name' => _l('all')],
                    2 => ['id' => 'yes', 'name' => _l('is_active_export')],
                    3 => ['id' => 'no', 'name' => _l('is_not_active_export')],
                  ]; 
                  ?>
                  <?php echo render_select('ft_active',$active,array('id','name'),'staff_dt_active', 'yes', array(), array(), '', '', false); ?>
            </div>
          </div>
          <hr>
          <table class="table table-registers">
            <thead>
              <th><?php echo _l('type'); ?></th>
              <th><?php echo _l('sub_type'); ?></th>
              <th><?php echo _l('account_code'); ?></th>
              <th><?php echo _l('account_name'); ?></th>
              <th><?php echo _l('sub_account'); ?></th>
              <th><?php echo _l('balance'); ?></th>
              <th><?php echo _l('bank_balance'); ?></th>
              <th><?php echo _l('staff_dt_active'); ?></th>
              <th><?php echo _l('options'); ?></th>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
  </div>
</div>
<?php $arrAtt = array();
      $arrAtt['data-type']='currency';
?>

<div class="modal fade" id="account-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l('acc_user_register')?></h4>
      </div>
      <?php echo form_open_multipart(admin_url('accounting/user_register_view'),array('id'=>'user_register_view'));?>

      <div class="modal-body">
        <div class='account_id hide'>
          
        </div>
          <?php echo render_select('account',$accounts,array('id','name'),'acc_account','',['disabled' => true], array(),'','',false); ?>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info btn-submit"><?php echo _l('acc_submit'); ?></button>
      </div>
      <?php echo form_close(); ?>  
    </div>
  </div>
</div>



<!-- /.modal -->
<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/accounting/assets/js/registers/manage_js.php'; ?>
