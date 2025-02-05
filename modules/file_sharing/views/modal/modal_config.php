<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="config-modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title add-title"><?php echo _l('fs_new_configuration')?></h4>
				<h4 class="modal-title update-title hide"><?php echo _l('fs_edit_configuration')?></h4>
			</div>
			<?php echo form_open_multipart(admin_url('file_sharing/add_new_config'),array('id'=>'fs-config-form'));?>
			<?php echo form_hidden('id'); ?>
			<div class="modal-body">
				<div class="row mtop15 mbot15 fs-gr-radio">
					<div class="col-md-12">
						<label class="radio-inline">
							<input type="radio" name="type" value="fs_staff" checked>
							<span class="inline-mbot"><?php echo _l('fs_staff'); ?></span>
						</label>
						<label class="radio-inline">
							<input type="radio" name="type" value="fs_client">
							<span class="inline-mbot"><?php echo _l('fs_client'); ?></span>
						</label>
					</div>
				</div>
				<div id="div_staff">
					<?php 
					$selected_role = array();
					echo render_select('role[]',$roles,array('roleid',array('name')),'role',$selected_role,array('multiple'=>true, 'data-actions-box' => true),array(),'','',false); ?>
					<?php 
					$selected = array();
					echo render_select('staff[]',$staffs,array('staffid',array('firstname','lastname')),'staff',$selected,array('multiple'=>true, 'data-actions-box' => true),array(),'','',false); ?>
				</div>
				<div id="div_client" class="hide">
					<?php 
					$selected_role = array();
					echo render_select('customer_group[]',$client_groups,array('id','name'),'customer_group',$selected_role,array('multiple'=>true, 'data-actions-box' => true),array(),'','',false); ?>
					<?php 
					$selected = array();
					echo render_select('customer[]',$clients,array('userid','company'),'customer',$selected,array('multiple'=>true, 'data-actions-box' => true),array(),'','',false); ?>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
					       	<label for="min_size"><?php echo _l('fs_min_size'); ?></label>
					       	<div class="input-group">
					          <input type="number" name="min_size" class="form-control" value="" data-isedit="false" data-original-number="false" aria-invalid="false">
					            <span class="input-group-addon">
					             <?php echo _l('fs_mb'); ?>
					          	</span>
					        </div>
					    </div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
					       	<label for="max_size"><?php echo _l('fs_max_size'); ?></label>
					       	<div class="input-group">
					          <input type="number" name="max_size" class="form-control" value="" data-isedit="false" data-original-number="false" aria-invalid="false">
					            <span class="input-group-addon">
					             <?php echo _l('fs_mb'); ?>
					          	</span>
					        </div>
					    </div>
					</div>
				</div>
				<div class="row">
				<div class="col-md-6">
				<h5 class="title mbot5"><?php echo _l('fs_permisstion') ?></h5>
			    <div class="row">
			        <div class="col-md-6 mtop10 border-right">
			            <span><?php echo _l('view'); ?></span>
			        </div>
			        <div class="col-md-6 mtop10">
			            <div class="onoffswitch">
			                <input type="checkbox" id="is_read" data-perm-id="5" class="onoffswitch-checkbox" value="1" name="is_read">
			                <label class="onoffswitch-label" for="is_read"></label>
			            </div>
			        </div>
			        <div class="col-md-6 mtop10 border-right">
			            <span><?php echo _l('upload_and_override'); ?></span>
			        </div>
			        <div class="col-md-6 mtop10">
			            <div class="onoffswitch">
			                <input type="checkbox" id="is_write" data-perm-id="6" class="onoffswitch-checkbox"  value="1" name="is_write">
			                <label class="onoffswitch-label" for="is_write"></label>
			            </div>
			        </div>
			        <div class="col-md-6 mtop10 border-right">
			            <span><?php echo _l('delete'); ?></span>
			        </div>
			        <div class="col-md-6 mtop10">
			            <div class="onoffswitch">
			                <input type="checkbox" id="is_delete" data-perm-id="5" class="onoffswitch-checkbox"  value="1" name="is_delete">
			                <label class="onoffswitch-label" for="is_delete"></label>
			            </div>
			        </div>
			        <div class="col-md-6 mtop10 border-right">
			            <span><?php echo _l('project_setting_upload_files'); ?></span>
			        </div>
			        <div class="col-md-6 mtop10">
			            <div class="onoffswitch">
			                <input type="checkbox" id="is_upload" data-perm-id="6" class="onoffswitch-checkbox"  value="1" name="is_upload">
			                <label class="onoffswitch-label" for="is_upload"></label>
			            </div>
			        </div>
			        <div class="col-md-6 mtop10 border-right">
			            <span><?php echo _l('download'); ?></span>
			        </div>
			        <div class="col-md-6 mtop10">
			            <div class="onoffswitch">
			                <input type="checkbox" id="is_download" data-perm-id="5" class="onoffswitch-checkbox"  value="1" name="is_download">
			                <label class="onoffswitch-label" for="is_download"></label>
			            </div>
			        </div>
			    </div>
			</div>
				</div>

			</div>


			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
			</div>
			<?php echo form_close(); ?>  
		</div>
	</div>
</div>