<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
  <a href="<?php echo admin_url('fleet/inspection_form') ?>" class="btn btn-info mbot15"><?php echo _l('add'); ?></a>
</div>
<div class="row">
  <div class="col-md-12">
    <?php 
      $table_data = array(
        _l('id'),
        _l('name'),
        _l('addedfrom'),
        _l('datecreated'),
        );
      render_datatable($table_data,'inspection-forms');
    ?>
  </div>
</div>