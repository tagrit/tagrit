<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
<div class="_buttons">
    
    <?php if (has_permission('wh_setting', '', 'create') || is_admin()) { ?>

    <a href="#" onclick="new_color(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('add_color'); ?>
    </a>
<?php } ?>

</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table border table-striped">
 <thead>
    <th><?php echo _l('_order'); ?></th>
    <th><?php echo _l('color_code'); ?></th>
    <th><?php echo _l('color_name'); ?></th>
    <th><?php echo _l('color_hex'); ?></th>
    <th><?php echo _l('order'); ?></th>
    <th><?php echo _l('display'); ?></th>
    <th><?php echo _l('note'); ?></th>
    <th><?php echo _l('options'); ?></th>
 </thead>
  <tbody>
    <?php foreach($colors as $color){ ?>

    <tr>
        <td><?php echo _l($color['color_id']); ?></td>
        <td><?php echo _l($color['color_code']); ?></td>
        <td><?php echo _l($color['color_name']); ?></td>
        <td><?php echo _l($color['color_hex']); ?></td>
        <td><?php echo _l($color['order']); ?></td>
        <td><?php if($color['display'] == 0){ echo _l('not_display'); }else{echo _l('display');} ?></td>
        <td><?php echo _l($color['note']); ?></td>

        <td>
            <?php if (has_permission('wh_setting', '', 'edit') || is_admin()) { ?>
              <a href="#" onclick="edit_color(this,<?php echo new_html_entity_decode($color['color_id']); ?>); return false;" data-color_code="<?php echo new_html_entity_decode($color['color_code']); ?>" data-color_name="<?php echo new_html_entity_decode($color['color_name']); ?>" data-color_hex="<?php echo new_html_entity_decode($color['color_hex']); ?>" data-order="<?php echo new_html_entity_decode($color['order']); ?>" data-display="<?php echo new_html_entity_decode($color['display']); ?>" data-note="<?php echo new_html_entity_decode($color['note']); ?>" class="btn btn-default btn-icon"><i class="fa-regular fa-pen-to-square"></i>
            </a>
            <?php } ?>

            <?php if (has_permission('wh_setting', '', 'delete') || is_admin()) { ?> 
            <a href="<?php echo admin_url('warehouse/delete_color/'.$color['color_id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
             <?php } ?>
        </td>
    </tr>
    <?php } ?>
 </tbody>
</table>   

<div class="modal1 fade" id="color" tabindex="-1" role="dialog">
        <div class="modal-dialog setting-handsome-table">
          <?php echo form_open_multipart(admin_url('warehouse/colors_setting'), array('id'=>'colors_setting')); ?>

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="add-title"><?php echo _l('add_color'); ?></span>
                        <span class="edit-title"><?php echo _l('edit_color'); ?></span>
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                             <div id="color_id_t"></div>   
                          <div class="form"> 
                            <div class="col-md-6">
                              <?php echo render_input('color_code', 'color_code'); ?>
                            </div>

                            <div class="col-md-6">
                              <?php echo render_input('color_name', 'color_name'); ?>
                            </div>

                            <div class="col-md-6">
                              <?php echo render_color_picker('color_hex',  _l('color_hex')); ?>
                            </div>
                            <div class="col-md-6">
                              <?php $mint_point_f="1";
                                        $min_p =[];
                                        $min_p['min']='0';
                                        $min_p['required']='true';

                                     ?>
                                <?php echo render_input('order','order',new_html_entity_decode($mint_point_f),'number', $min_p) ?>
                            </div>

                            <div class="col-md-12">
                              <?php echo render_textarea('note', 'note'); ?>

                            </div>

                            <div class="col-md-12">
                              <input data-can-view="" type="checkbox" class="capability" name="display" checked>
                              <label for="contracts_view" class="pt-2">
                                      <?php echo _l('display'); ?>               
                                    </label>
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
