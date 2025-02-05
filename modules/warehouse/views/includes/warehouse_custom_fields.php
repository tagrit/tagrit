<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
<div class="_buttons">
<?php if (has_permission('wh_setting', '', 'create') || is_admin()) { ?>

    <a href="#" onclick="new_custom_fields_warehouse(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('add'); ?>
    </a>
<?php } ?>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table border table-striped">
 <thead>
    <th><?php echo _l('_order'); ?></th>
    <th><?php echo _l('custom_fields_name'); ?></th>
    <th><?php echo _l('warehouse_name'); ?></th>

    <th><?php echo _l('options'); ?></th>
 </thead>
  <tbody>
    <?php foreach($custom_fields_warehouse as $custom_fields){ ?>

    <tr>
        <td><?php echo new_html_entity_decode($custom_fields['id']); ?></td>
        <?php
            $custom_fields_name='' ;
            $custom_fields_value = wh_get_custom_fields($custom_fields['custom_fields_id']);

            if($custom_fields_value){
                $custom_fields_name .= $custom_fields_value->name.' ( '.$custom_fields_value->type.' )';
            }
         ?>
        <td><?php echo new_html_entity_decode($custom_fields_name); ?></td>

        <?php 

            $warehouse_id = '';

            $warehouse_name       = new_explode(',', $custom_fields['warehouse_id']);
            $list_warehouse_name = '';
            $exportwarehouse_name = '';

            foreach ($warehouse_name as $key => $warehouse) {
                    $get_warehouse   = get_warehouse_name($warehouse);
                    if($get_warehouse){
                        $warehouse_name_value = $get_warehouse->warehouse_name;
                    }else{

                        $warehouse_name_value = '';
                    }

                    $list_warehouse_name .= '<li class="text-success mbot10 mtop"><a href="#"  style="text-align: left;">'.$warehouse_name_value. '</a></li>';

                    
            }
            
            $warehouse_id .= '<span class="avatar bg-secondary brround avatar-none">+'. (count($warehouse_name) ) .'</span>';


            $warehouse_id1 = '<div class="task-info task-watched task-info-watched">
                                   <h5>
                                      <div class="btn-group">
                                         <span class="task-single-menu task-menu-watched">
                                            <div class="avatar-list avatar-list-stacked" data-toggle="dropdown">'.$warehouse_id.'</div>
                                            <ul class="dropdown-menu list-staff" role="menu">
                                               <li class="dropdown-plus-title">
                                                  '. _l('warehouse_name') .'
                                               </li>
                                               '.$list_warehouse_name.'
                                            </ul>
                                         </span>
                                      </div>
                                   </h5>
                                </div>';

            $_data = $warehouse_id1;


         ?>
        <td><?php echo new_html_entity_decode($_data); ?></td>

        <td>
            <?php if (has_permission('wh_setting', '', 'edit') || is_admin()) { ?>
              <a href="#" onclick="edit_custom_fields_warehouse(this,<?php echo new_html_entity_decode($custom_fields['id']); ?>); return false;" data-custom_fields_id="<?php echo new_html_entity_decode($custom_fields['custom_fields_id']); ?>" data-warehouse_id="<?php echo new_html_entity_decode($custom_fields['warehouse_id']); ?>" class="btn btn-default btn-icon"><i class="fa-regular fa-pen-to-square"></i>
            </a>
            <?php } ?>

            <?php if (has_permission('wh_setting', '', 'delete') || is_admin()) { ?> 
            <a href="<?php echo admin_url('warehouse/delete_custom_fields_warehouse/'.$custom_fields['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
             <?php } ?>
        </td>
    </tr>
    <?php } ?>
 </tbody>
</table>   

<div class="modal1 fade" id="custom_fields" tabindex="-1" role="dialog">
        <div class="modal-dialog ">
          <?php echo form_open_multipart(admin_url('warehouse/custom_fields_setting'), array('id'=>'custom_fields_setting')); ?>

            <div class="modal-content w-25">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="add-title"><?php echo _l('add_custom_fields_for_warehouse'); ?></span>
                        <span class="edit-title"><?php echo _l('update_custom_fields_for_warehouse'); ?></span>
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                             <div id="custom_fields_id_t"></div>   
                          <div class="form"> 
                            <div class="col-md-12">
                                  <div class="form-group">
                                     <label for="custom_fields_id"><?php echo _l('select_custom_fields'); ?></label>
                                      <select name="custom_fields_id" id="custom_fields_id" class="selectpicker" data-live-search="true"data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                                        <option  value=""></option>
                                        <?php foreach($wh_custom_fields as $custome_value) { ?>
                                          <option value="<?php echo new_html_entity_decode($custome_value['id']); ?>"><?php echo new_html_entity_decode($custome_value['name'].' ( '.$custome_value['type']).' )'; ?></option>
                                          <?php } ?>

                                      </select>
                                  </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                     <label for="warehouse_id[]"><?php echo _l('warehouse_name'); ?></label>
                                      <select name="warehouse_id[]" id="warehouse_id" class="selectpicker" data-live-search="true" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                                        <?php foreach($warehouses as $warehouse) { ?>
                                          <option value="<?php echo new_html_entity_decode($warehouse['warehouse_id']); ?>"><?php echo new_html_entity_decode($warehouse['warehouse_name']); ?></option>
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
                        
                         <button type="button" class="btn btn-info warehouse_custom_fields_submit"><?php echo _l('submit'); ?></button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div> 
</div>

</body>
</html>
