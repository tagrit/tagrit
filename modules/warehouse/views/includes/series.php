<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
<div class="_buttons">
    
    <?php if (has_permission('wh_setting', '', 'create') || is_admin()) { ?>

    <a href="#" onclick="new_series(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('add_series'); ?>
    </a>
<?php } ?>

</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table border table-striped">
 <thead>
    <th><?php echo _l('_order'); ?></th>
    <th><?php echo _l('series_name'); ?></th>
    <th><?php echo _l('model_name'); ?></th>
    
    <th><?php echo _l('options'); ?></th>
 </thead>
  <tbody>
    <?php foreach($series_l as $series){ ?>

    <tr>
        <td><?php echo new_html_entity_decode($series['id']); ?></td>
        <td><?php echo new_html_entity_decode($series['name']); ?></td>
        <?php 
            $model_name='';
            if($series['model_id'] != 0 && $series['model_id'] != ''){
                $model_value = get_models_name($series['model_id']);
                if($model_value){
                    $model_name .= $model_value->name;
                }
            }
         ?>
        <td><?php echo new_html_entity_decode($model_name); ?></td>
        
        <td>
            <?php if (has_permission('wh_setting', '', 'edit') || is_admin()) { ?>
              <a href="#" onclick="edit_series(this,<?php echo new_html_entity_decode($series['id']); ?>); return false;" data-name="<?php echo new_html_entity_decode($series['name']); ?>" data-model_id="<?php echo new_html_entity_decode($series['model_id']); ?>" class="btn btn-default btn-icon"><i class="fa-regular fa-pen-to-square"></i>
            </a>
            <?php } ?>

            <?php if (has_permission('wh_setting', '', 'delete') || is_admin()) { ?> 
            <a href="<?php echo admin_url('warehouse/delete_series/'.$series['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
             <?php } ?>
        </td>
    </tr>
    <?php } ?>
 </tbody>
</table>   

<div class="modal1 fade" id="series" tabindex="-1" role="dialog">
        <div class="modal-dialog w-25">
          <?php echo form_open_multipart(admin_url('warehouse/series_setting'), array('id'=>'series_setting')); ?>

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="add-title"><?php echo _l('add_series'); ?></span>
                        <span class="edit-title"><?php echo _l('edit_series'); ?></span>
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                             <div id="series_id_t"></div>   
                          <div class="form"> 
                            <div class="col-md-12">
                              <?php echo render_input('name', 'series_name'); ?>
                            </div>

                            <div class="col-md-12" >
                              <div class="form-group">
                                 <label for="model_id"><?php echo _l('model_name'); ?></label>
                                    <select name="model_id" id="model_id" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                                      <option value=""></option>
                                      <?php foreach($list_models as $model) { ?>
                                        <option value="<?php echo new_html_entity_decode($model['id']); ?>"><?php echo new_html_entity_decode($model['name']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
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
