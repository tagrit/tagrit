<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <hr />
          <div>
            <?php if(is_admin() || has_permission('fleet_driver', '', 'create')){ ?>

            <a href="#" class="btn btn-info add-new-drivers mbot15"><?php echo _l('add'); ?></a>
            <?php } ?>

          </div>

          <?php
            $table_data = array(
              _l('staff_dt_name'),
              _l('staff_dt_email'),
              _l('role'),
              _l('staff_dt_last_Login'),
              _l('staff_dt_active'),
              );
            $custom_fields = get_custom_fields('staff',array('show_on_table'=>1));
            foreach($custom_fields as $field){
              array_push($table_data,$field['name']);
            }
            render_datatable($table_data,'drivers');
            ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
</body>
</html>

<div class="modal fade" id="driver-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l('driver')?></h4>
      </div>
      <?php echo form_open_multipart(admin_url('fleet/driver'),array('id'=>'driver-form'));?>
      <div class="modal-body">
        <?php echo render_select('staff',$staffs, array('staffid', array('firstname', 'lastname')),'staff'); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
      </div>
      <?php echo form_close(); ?>  
    </div>
  </div>
</div>