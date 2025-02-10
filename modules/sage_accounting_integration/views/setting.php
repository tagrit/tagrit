<?php defined('BASEPATH') or exit('No direct script access allowed');?>

<?php init_head();?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
              <div class="panel-body">
                <div class="border-right">
                  <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
                  <hr />
                </div>
                <?php echo form_open(admin_url('sage_accounting_integration/update_setting')); ?>
                <?php
                    render_yes_no_option('acc_integration_sage_accounting_active', 'acc_active');
                    render_yes_no_option('acc_integration_sage_accounting_sync_from_system', 'acc_sync_from_system_to_sage_accounting');
                    render_yes_no_option('acc_integration_sage_accounting_sync_to_system', 'acc_sync_from_sage_accounting_to_system');

                    $value = get_option('acc_integration_sage_accounting_client_id');
                    $client_id = $this->encryption->decrypt($value);
                    echo render_input('settings[acc_integration_sage_accounting_client_id]', 'acc_client_id', $client_id);

                    $value = get_option('acc_integration_sage_accounting_client_secret');
                    $client_secret = $this->encryption->decrypt($value);
                    echo render_input('settings[acc_integration_sage_accounting_client_secret]', 'acc_client_secret', $client_secret);
                ?>
                <?php if(get_option('acc_integration_sage_accounting_active') == 1){ ?>
                  <?php if(get_option('acc_integration_sage_accounting_connected') == 1){ ?>
                    <span class="label label-success mbot10"><?php echo _l('connected'); ?></span><br>
                  <?php }else{ ?>
                    <span class="label label-warning mbot10"><?php echo _l('not_connected_yet'); ?></span><br>
                  <?php } ?>
                  <a href="<?php echo admin_url('sage_accounting_integration/connect'); ?>" class="btn btn-primary"><?php echo _l('connect'); ?></a>
                <?php } ?>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
              </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail();?>
</body>
</html>
