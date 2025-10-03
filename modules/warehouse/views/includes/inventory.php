<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div>
<div class="row">
     <div class="col-md-12">
      <h4 class="no-margin font-bold h4-color" ><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l('inventory_config_min'); ?></h4>
      <hr class="hr-color" >
    </div>
</div>

<div class="row mb-4">   
  <div class="col-md-12">
    <!-- filter -->
    <div class="row filter_by">

      <div class="col-md-3 pull-right">
        <?php echo render_input('inventory_filter','','', 'text', ['placeholder' => 'Search...']); ?>   
      </div>

    </div>
    <!-- filter -->
  </div>

</div>

 <?php echo form_open_multipart(admin_url('warehouse/update_inventory_min'), array('id'=>'update_inventory')); ?>
    <div class="form"> 
      <div id="inventory_min" class="hot handsontable htColumnHeaders">
          
      </div>
    <?php echo form_hidden('inventory_min'); ?>
  </div>

    <hr class="hr-panel-heading" />

    <?php if (has_permission('wh_setting', '', 'create') || is_admin() || has_permission('wh_setting', '', 'edit')  ) { ?>

     <a href="#"class="btn btn-info pull-right mright10 display-block" onclick="add_goods_receipt(this); return false;"><?php echo _l('submit'); ?></a>
   <?php } ?>

 <?php echo form_close(); ?>

</div>


</body>
</html>
