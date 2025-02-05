<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="_buttons">
   <?php if (has_permission('wh_setting', '', 'create') || is_admin() ) { ?>

	<a href="#" class="btn btn-info pull-left" onclick="new_approval_setting(); return false;"><?php echo _l('new_approval_setting'); ?></a>
<?php } ?>

</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
	<thead>
		<th><?php echo _l('_order'); ?></th>
		<th><?php echo _l('approval_name'); ?></th>
		<th><?php echo _l('related'); ?></th>
		<th><?php echo _l('options'); ?></th>
	</thead>
	<tbody>
	<?php foreach($approval_setting as $value){ ?>
		<tr>
			<td><?php echo new_html_entity_decode($value['id']); ?></td>
			<td><?php echo new_html_entity_decode($value['name']); ?></td>
			<?php 
			$related ='';
			if($value['related'] == 1){
				$related = _l('stock_import');
			}elseif($value['related'] == 2){
				$related = _l('stock_export');

			}elseif($value['related'] == 3){
				$related = _l('loss_adjustment');
			}elseif($value['related'] == 4){
				$related = _l('internal_delivery_note');
			}elseif($value['related'] == 5){
				$related = _l('wh_packing_list');
			}elseif($value['related'] == 6){
				$related = _l('inventory_receipt_inventory_delivery_returns_goods');
			}

		    ?>
		   <td><?php echo new_html_entity_decode($related); ?></td>
		   <td>

            <?php if (is_admin() || has_permission('wh_setting', '', 'edit') ) { ?>

		     <a href="#" onclick="edit_approval_setting(this,<?php echo new_html_entity_decode($value['id']); ?>); return false" data-name="<?php echo new_html_entity_decode($value['name']); ?>" data-related="<?php echo new_html_entity_decode($value['related']); ?>" data-setting='<?php echo new_html_entity_decode($value['setting']); ?>' class="btn btn-default btn-icon"><i class="fa-regular fa-pen-to-square"></i></a>
		 	<?php } ?>

            <?php if (is_admin() || has_permission('wh_setting', '', 'delete') ) { ?>

		      <a href="<?php echo admin_url('warehouse/delete_approval_setting/'.$value['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
		  <?php } ?>

		   </td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<div class="modal fade" id="approval_setting_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog approval_model" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">
					<span class="edit-title"><?php echo _l('edit_approval_setting'); ?></span>
					<span class="add-title"><?php echo _l('new_approval_setting'); ?></span>
				</h4>
			</div>
			<?php echo form_open('warehouse/approval_setting',array('id'=>'approval-setting-form')); ?>
			<div id="approval_id">
			<?php echo form_hidden('approval_setting_id'); ?>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<?php echo render_input('name','_subject','','text'); ?>
						<?php $related = [ 
								0 => ['id' => '1', 'name' => _l('stock_import')],
								1 => ['id' => '2', 'name' => _l('stock_export')],
								2 => ['id' => '3', 'name' => _l('loss_adjustment')],
								3 => ['id' => '4', 'name' => _l('internal_delivery_note')],
								4 => ['id' => '5', 'name' => _l('wh_packing_list')],
								5 => ['id' => '6', 'name' => _l('wh_order_return')],
								
							]; ?>

						
						<?php echo render_select('related',$related,array('id','name'),'task_single_related'); ?>
						<div class="list_approve">
							<div id="item_approve">
                                <div class="col-md-11">
                                <div class="col-md-4 hide">
                                	<div class="select-placeholder form-group">
		                                <label for="approver[0]"><?php echo _l('approver'); ?></label>
		                            <select name="approver[0]" id="approver[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" required>
		                                <option value=""></option>
		                                <option value="direct_manager"><?php echo _l('direct_manager'); ?></option>
		                                <option value="department_manager"><?php echo _l('department_manager'); ?></option>
		                                <option value="staff" selected><?php echo _l('staff'); ?></option>
		                            </select>
		                           </div> 
                          		</div>
                          		<div class="col-md-8">
                                	<div class="select-placeholder form-group">
		                                <label for="staff[0]"><?php echo _l('staff'); ?></label>
		                            <select name="staff[0]" id="staff[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true">
		                                <option value=""></option>
		                                <?php foreach($staffs as $val){
		                                 $selected = '';
		                                  ?>
		                              <option value="<?php echo new_html_entity_decode($val['staffid']); ?>">
		                                 <?php echo get_staff_full_name(new_html_entity_decode($val['staffid'])); ?>
		                              </option>
		                              <?php } ?>
		                            </select>
		                           </div> 
                          		</div>
                          		<div class="col-md-4">
                                	<div class="select-placeholder form-group">
		                                <label for="action[0]"><?php echo _l('action_label'); ?></label>
		                            <select name="action[0]" id="action[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true">
		                                <option value="approve" selected><?php echo _l('approve'); ?></option>
		                                <option value="sign"><?php echo _l('sign'); ?></option>
		                            </select>
		                           </div> 
                          		</div>
                          		</div>
                                <div class="col-md-1 new_vendor_requests_button">
                                <span class="pull-bot">
                                    <button name="add" class="btn new_wh_approval btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                    </span>
                              </div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
</body>
</html>

