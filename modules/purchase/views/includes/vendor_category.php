<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div>
<div class="_buttons">
    <a href="#" onclick="new_vendor_cate(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('new'); ?>
    </a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
 <thead>
    <th><?php echo _l('id'); ?></th>
    <th><?php echo _l('name'); ?></th>
    <th><?php echo _l('description'); ?></th>
    <th><?php echo _l('options'); ?></th>
 </thead>
 <tbody>
  <?php foreach($vendor_categories as $vc){ ?>
    <tr>
      <td><?php echo pur_html_entity_decode($vc['id']); ?></td>
      <td><?php echo pur_html_entity_decode($vc['category_name']); ?></td>
      <td><?php echo pur_html_entity_decode($vc['description']); ?></td>
      <td>
        <a href="#" onclick="edit_vendor_cate(this,<?php echo pur_html_entity_decode($vc['id']); ?>); return false" data-name="<?php echo pur_html_entity_decode($vc['category_name']); ?>" data-description="<?php echo pur_html_entity_decode($vc['description']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square"></i></a>

          <a href="<?php echo admin_url('purchase/delete_vendor_category/' . $vc['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
      </td>
    </tr>
  <?php } ?>
 </tbody>
</table>
<div class="modal fade" id="vendor_cate" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('purchase/vendor_cate')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_vendor_cate'); ?></span>
                    <span class="add-title"><?php echo _l('new_vendor_cate'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional_vendor_cate"></div>
                     <div class="form">
                        <?php echo render_input('category_name', 'name'); ?>

                        <?php echo render_textarea('description', 'description', '') ?>
                    </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
</body>
</html>
