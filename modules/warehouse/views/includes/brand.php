<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
<div class="_buttons">
    
    <?php if (has_permission('wh_setting', '', 'create') || is_admin()) { ?>

    <a href="#" onclick="new_brand(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('add_brand'); ?>
    </a>
<?php } ?>

</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table border table-striped">
 <thead>
    <th><?php echo _l('_order'); ?></th>
    <th><?php echo _l('brand_name'); ?></th>
    
    <th><?php echo _l('options'); ?></th>
 </thead>
  <tbody>
    <?php foreach($brands as $brand){ ?>

    <tr>
        <td><?php echo new_html_entity_decode($brand['id']); ?></td>
        <td><?php echo new_html_entity_decode($brand['name']); ?></td>
        
        <td>
            <?php if (has_permission('wh_setting', '', 'edit') || is_admin()) { ?>
              <a href="#" onclick="edit_brand(this,<?php echo new_html_entity_decode($brand['id']); ?>); return false;" data-name="<?php echo new_html_entity_decode($brand['name']); ?>" class="btn btn-default btn-icon"><i class="fa-regular fa-pen-to-square"></i>
            </a>
            <?php } ?>

            <?php if (has_permission('wh_setting', '', 'delete') || is_admin()) { ?> 
            <a href="<?php echo admin_url('warehouse/delete_brand/'.$brand['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
             <?php } ?>
        </td>
    </tr>
    <?php } ?>
 </tbody>
</table>   

<div class="modal1 fade" id="brand" tabindex="-1" role="dialog">
        <div class="modal-dialog w-25">
          <?php echo form_open_multipart(admin_url('warehouse/brands_setting'), array('id'=>'brands_setting')); ?>

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="add-title"><?php echo _l('add_brand'); ?></span>
                        <span class="edit-title"><?php echo _l('edit_brand'); ?></span>
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                             <div id="brand_id_t"></div>   
                          <div class="form"> 
                            <div class="col-md-12">
                              <?php echo render_input('name', 'brand_name'); ?>
                            </div>

                          </div>
                        </div>
                    </div>
                </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                        
                         <button type="submit" class="btn btn-info intext-btn"><?php echo _l('submit'); ?></button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div> 
</div>

</body>
</html>
