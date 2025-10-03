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
                 <?php $regions = [ 
                  1 => ['id' => 'central_european', 'name' => _l('central_european')],
                  2 => ['id' => 'south_african', 'name' => _l('south_african')],
                ]; 
                    $region = get_option('acc_integration_sage_accounting_region');
                ?>
                <?php echo render_select('settings[acc_integration_sage_accounting_region]',$regions,array('id','name'),'region', $region, array(), array(), '', '', false); ?>
                  
                <div class="border-right">
                  <h4 class="no-margin font-bold"><?php echo _l('automatic_sync_config'); ?></h4>
                  <hr />
                </div>
                    <div class="row">
                      <div class="col-md-6">
                        <?php render_yes_no_option('acc_integration_sage_accounting_sync_from_system', 'acc_sync_from_system_to_sage_accounting'); ?>
                      </div>
                      <div class="col-md-6 south_african_region <?php if($region == 'central_european') { echo 'hide'; } ?>">
                  <?php if($organizations){ ?>
                      <?php 
                      $sync_from_system_organizations = explode(',', get_option('acc_integration_sage_accounting_sync_from_system_organizations')); ?>
                      <?php echo render_select('acc_integration_sage_accounting_sync_from_system_organizations[]',$organizations,array('ID', 'Name'),'organizations',$sync_from_system_organizations,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                      ?>
                <?php } ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <?php render_yes_no_option('acc_integration_sage_accounting_sync_to_system', 'acc_sync_from_sage_accounting_to_system'); ?>
                      </div>
                      <div class="col-md-6 south_african_region <?php if($region == 'central_european') { echo 'hide'; } ?>">
                  <?php if($organizations){ ?>
                        <?php $sync_to_system_organizations = explode(',', get_option('acc_integration_sage_accounting_sync_to_system_organizations')); ?>
                      <?php echo render_select('acc_integration_sage_accounting_sync_to_system_organizations[]',$organizations,array('ID', 'Name'),'organizations',$sync_to_system_organizations,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
                  <?php } ?>

                      </div>
                    </div>

                <div class="south_african_region <?php if($region == 'central_european') { echo 'hide'; } ?>">
                  <?php 
                    $value = get_option('acc_integration_sage_accounting_api_key');
                    echo render_input('settings[acc_integration_sage_accounting_api_key]', 'acc_api_key', $value);

                    $value = get_option('acc_integration_sage_accounting_username');
                    echo render_input('settings[acc_integration_sage_accounting_username]', 'acc_username', $value);

                    $value = get_option('acc_integration_sage_accounting_password');
                    $password = $this->encryption->decrypt($value);
                    echo render_input('settings[acc_integration_sage_accounting_password]', 'acc_password', $password, 'password');
                  ?>
                  <a href="#" class="btn btn-primary mbot10" onclick="test_connect();"><?php echo _l('test_connection'); ?></a>
                </div>
                <div class="central_european_region <?php if($region == 'south_african') { echo 'hide'; } ?>">
                  <?php 
                    $value = get_option('acc_integration_sage_accounting_client_id');
                    $client_id = $this->encryption->decrypt($value);
                    echo render_input('settings[acc_integration_sage_accounting_client_id]', 'acc_client_id', $client_id);

                    $value = get_option('acc_integration_sage_accounting_client_secret');
                    $client_secret = $this->encryption->decrypt($value);
                    echo render_input('settings[acc_integration_sage_accounting_client_secret]', 'acc_client_secret', $client_secret, 'password');
                  ?>
                  <?php if(get_option('acc_integration_sage_accounting_connected') == 1){ ?>
                    <span class="label label-success mbot10"><?php echo _l('connected'); ?></span><br>
                  <?php }else{ ?>
                    <span class="label label-warning mbot10"><?php echo _l('not_connected_yet'); ?></span><br>
                  <?php } ?>
                  <a href="<?php echo admin_url('sage_accounting_integration/connect'); ?>" class="btn btn-primary mbot10"><?php echo _l('connect'); ?></a>
                </div>
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
