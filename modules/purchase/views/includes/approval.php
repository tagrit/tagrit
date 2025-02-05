<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="_buttons">
	<a href="#" class="btn btn-info pull-left" onclick="new_approval_setting(); return false;"><?php echo _l('new_approval_setting'); ?></a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
	<thead>
		<th><?php echo _l('id'); ?></th>
		<th><?php echo _l('name'); ?></th>
		<th><?php echo _l('related'); ?></th>
		<th><?php echo _l('options'); ?></th>
	</thead>
	<tbody>
	<?php foreach($approval_setting as $value){ ?>
		<tr>
		   <td><?php echo pur_html_entity_decode($value['id']); ?></td>
		   <td><?php echo pur_html_entity_decode($value['name']); ?></td>
		   <td><?php echo _l($value['related']); ?></td>
		   <td>
		     <a href="#" onclick="edit_approval_setting(this,<?php echo pur_html_entity_decode($value['id']); ?>); return false" data-name="<?php echo pur_html_entity_decode($value['name']); ?>" data-related="<?php echo pur_html_entity_decode($value['related']); ?>" data-setting='<?php echo pur_html_entity_decode($value['setting']); ?>' class="btn btn-default btn-icon"><i class="fa fa-pencil-square"></i></a>
		      <a href="<?php echo admin_url('purchase/delete_approval_setting/'.$value['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
		   </td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<?php
$hr_record_status = 0; 
if(get_status_modules_pur('hr_profile') == true){
	$hr_record_status = 1;
} ?>

<div class="modal fade" id="approval_setting_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog withd_1k" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">
					<span class="edit-title"><?php echo _l('edit_approval_setting'); ?></span>
					<span class="add-title"><?php echo _l('new_approval_setting'); ?></span>
				</h4>
			</div>
			<?php echo form_open('purchase/approval_setting',array('id'=>'approval-setting-form')); ?>
			<?php echo form_hidden('approval_setting_id'); ?>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<?php echo render_input('name','subject','','text'); ?>
						<?php $related = [ 
								0 => ['id' => 'pur_request', 'name' => _l('pur_request')],
								1 => ['id' => 'pur_quotation', 'name' => _l('pur_quotation')],
								2 => ['id' => 'pur_order', 'name' => _l('pur_order')],
								3 => ['id' => 'payment_request', 'name' => _l('payment_request')],
							]; ?>
						<?php echo render_select('related',$related,array('id','name'),'task_single_related'); ?>
						<div class="list_approve">
							<div id="item_approve">
                                <div class="col-md-11">
                                <div class="col-md-<?php if($hr_record_status == 1){ echo '4'; }else{ echo '1'; } ?> <?php if($hr_record_status == 0){ echo 'hide'; } ?>">
                                	<div class="select-placeholder form-group">
		                                <label for="approver[0]"><?php echo _l('approver'); ?></label>
		                            <select name="approver[0]" id="approver[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" required>
		                                <option value=""></option>
		                                <option value="direct_manager"><?php echo _l('direct_manager'); ?></option>
		                                <option value="head_of_department"><?php echo _l('department_manager'); ?></option>
		                                <option value="staff" <?php if($hr_record_status == 0){ echo 'selected'; } ?> ><?php echo _l('staff'); ?></option>
		                            </select>
		                           </div> 
                          		</div>
                          		<div class="col-md-<?php if($hr_record_status == 1){ echo '4'; }else{ echo '8'; } ?>">
                                	<div class="select-placeholder form-group">
		                                <label for="staff[0]"><?php echo _l('staff'); ?></label>
		                            <select name="staff[0]" id="staff[0]" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true">
		                                <option value=""></option>
		                                <?php foreach($staffs as $val){
		                                 $selected = '';
		                                  ?>
		                              <option value="<?php echo pur_html_entity_decode($val['staffid']); ?>">
		                                 <?php echo get_staff_full_name($val['staffid']); ?>
		                              </option>
		                              <?php } ?>
		                            </select>
		                           </div> 
                          		</div>
                          		<div class="col-md-4" id="is_staff_0">
                                	<div class="select-placeholder form-group">
		                                <label for="action[0]"><?php echo _l('action'); ?></label>
		                            <select name="action[0]" id="action[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true">
		                                <option value="approve" selected><?php echo _l('approve'); ?></option>
		                                <option value="sign"><?php echo _l('sign'); ?></option>
		                            </select>
		                           </div> 
                          		</div>
                          		</div>
                                <div class="col-md-1 bot_div">
                                <span class="pull-bot">
                                    <button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
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

